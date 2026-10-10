<?php

namespace App\Livewire\Admin\Stock;

use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Penyesuaian Stok - Syafa Frozen Food')]
class StockAdjustment extends Component
{
    use WithPagination;

    // Filters
    public string $filterProduct = '';
    public string $filterType = '';

    // Modal Form State
    public bool $showFormModal = false;

    // Form fields
    public ?int $product_id = null;
    public string $type = 'in'; // 'in', 'out', 'adjustment'
    public ?int $quantity = 1;
    public string $reference = '';
    public string $description = '';

    protected function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|max:100',
            'description' => 'nullable|max:255',
        ];
    }

    public function updatingFilterProduct(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function openAdjustmentModal(?int $productId = null): void
    {
        $this->reset(['quantity', 'reference', 'description']);
        $this->type = 'in';
        $this->quantity = 1;
        $this->product_id = $productId ?: Product::active()->first()?->id;
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function saveAdjustment(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $product = Product::lockForUpdate()->findOrFail($this->product_id);
                $stockBefore = $product->stock;

                if ($this->type === 'in') {
                    // Stok Masuk (+)
                    $delta = $this->quantity;
                    $stockAfter = $stockBefore + $delta;
                    $qtyRecord = $delta;
                } elseif ($this->type === 'out') {
                    // Stok Keluar / Rusak / Expired (-)
                    if ($this->quantity > $stockBefore) {
                        throw new \Exception("Jumlah stok keluar ({$this->quantity}) melebihi stok yang tersedia ({$stockBefore})!");
                    }
                    $delta = -$this->quantity;
                    $stockAfter = $stockBefore + $delta;
                    $qtyRecord = $delta;
                } else {
                    // Penyesuaian Koreksi (Set ke nilai $quantity)
                    $stockAfter = $this->quantity;
                    $qtyRecord = $stockAfter - $stockBefore;
                }

                $product->update(['stock' => $stockAfter]);

                $mainOutlet = \App\Models\Outlet::where('is_main', true)->first();
                if ($mainOutlet) {
                    \App\Models\OutletProduct::updateOrCreate(
                        [
                            'outlet_id' => $mainOutlet->id,
                            'product_id' => $product->id,
                        ],
                        [
                            'stock' => $stockAfter,
                        ]
                    );
                }

                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => $this->type,
                    'quantity' => $qtyRecord,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => $this->reference ?: 'Penyesuaian Manual',
                    'description' => $this->description ?: ($this->type === 'in' ? 'Kulakan / Restock Stok Masuk' : ($this->type === 'out' ? 'Barang Rusak / Expired' : 'Koreksi Fisik Opname')),
                ]);
            });

            $msg = 'Penyesuaian stok berhasil disimpan dan mutasi tercatat.';
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
            $this->showFormModal = false;
            $this->reset(['product_id', 'quantity', 'reference', 'description']);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->dispatch('toast', ['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        $products = Product::orderBy('name')->get();

        $mutations = StockMutation::query()
            ->with(['product', 'user'])
            ->when($this->filterProduct, function ($q) {
                $q->where('product_id', $this->filterProduct);
            })
            ->when($this->filterType, function ($q) {
                $q->where('type', $this->filterType);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.stock.stock-adjustment', [
            'mutations' => $mutations,
            'products' => $products,
        ]);
    }
}
