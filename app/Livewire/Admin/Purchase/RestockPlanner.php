<?php

namespace App\Livewire\Admin\Purchase;

use App\Models\Category;
use App\Models\Product;
use App\Models\RestockPlan;
use App\Models\RestockPlanItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Perencanaan Kulakan & Stok Rendah - Syafa Frozen Food')]
class RestockPlanner extends Component
{
    use WithPagination;

    // Navigation Tab: 'analysis' | 'orders'
    public string $activeTab = 'analysis';

    // Filters for Analysis Tab
    public string $search = '';
    public ?int $selectedSupplierFilter = null;
    public ?int $selectedCategoryFilter = null;
    public string $stockUrgency = 'all'; // all, out_of_stock, low_stock

    // State for order generation in Analysis Tab: [product_id => ['selected' => bool, 'qty' => int, 'cost' => float]]
    public array $orderInputs = [];

    // Filters for Orders Tab
    public string $orderStatusFilter = 'all'; // all, draft, ordered, received, cancelled
    public string $orderSearch = '';

    // Modals
    public bool $showDetailModal = false;
    public ?RestockPlan $selectedPlan = null;
    public bool $showAddCustomProductModal = false;
    public ?int $customSupplierId = null;
    public ?int $customProductId = null;
    public int $customQty = 1;

    // WhatsApp Preview Modal
    public bool $showWhatsAppModal = false;
    public ?RestockPlan $whatsAppPlan = null;

    // Delete Modal
    public bool $showDeleteModal = false;
    public ?int $deletingPlanId = null;
    public ?RestockPlan $deletingPlan = null;

    protected $queryString = [
        'activeTab' => ['except' => 'analysis'],
        'selectedSupplierFilter' => ['except' => null],
    ];

    public function mount(): void
    {
        $this->initOrderInputs();
    }

    public function initOrderInputs(): void
    {
        // Fetch all low stock products and initialize order quantities
        $lowStockProducts = Product::active()
            ->lowStock()
            ->get();

        foreach ($lowStockProducts as $product) {
            $alert = max(1, $product->min_stock_alert);
            $current = max(0, $product->stock);
            // Suggest order quantity to reach double the alert threshold
            $suggested = max(1, ($alert * 2) - $current);

            $this->orderInputs[$product->id] = [
                'selected' => true,
                'quantity' => $suggested,
                'unit_cost' => (float) $product->cost_price,
            ];
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedSupplierFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedOrderSearch(): void
    {
        $this->resetPage();
    }

    public function updatedOrderStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Create a Restock Plan for a specific supplier from the checked items
     */
    public function createPlanForSupplier(int $supplierId): void
    {
        $supplier = Supplier::find($supplierId);
        if (!$supplier) {
            session()->flash('error', 'Supplier tidak ditemukan.');
            return;
        }

        // Find products belonging to this supplier that are checked
        $supplierProducts = Product::where('supplier_id', $supplierId)
            ->whereIn('id', array_keys($this->orderInputs))
            ->get();

        $itemsToOrder = [];
        $totalCost = 0;

        foreach ($supplierProducts as $product) {
            $input = $this->orderInputs[$product->id] ?? null;
            if ($input && !empty($input['selected']) && (int) $input['quantity'] > 0) {
                $qty = (int) $input['quantity'];
                $cost = (float) ($input['unit_cost'] ?: $product->cost_price);
                $subtotal = $qty * $cost;
                $totalCost += $subtotal;

                $itemsToOrder[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $product->stock,
                    'min_stock_alert' => $product->min_stock_alert,
                    'quantity_ordered' => $qty,
                    'unit' => $product->unit ?: 'pcs',
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if (empty($itemsToOrder)) {
            session()->flash('error', "Pilih minimal 1 produk dengan jumlah pesan > 0 untuk supplier {$supplier->name}.");
            return;
        }

        DB::transaction(function () use ($supplier, $itemsToOrder, $totalCost) {
            $plan = RestockPlan::create([
                'plan_number' => RestockPlan::generatePlanNumber(),
                'supplier_id' => $supplier->id,
                'user_id' => Auth::id() ?? 1,
                'status' => 'draft',
                'total_estimated_cost' => $totalCost,
                'notes' => 'Pesanan restock dibuat dari analisis stok rendah.',
            ]);

            foreach ($itemsToOrder as $item) {
                $item['restock_plan_id'] = $plan->id;
                RestockPlanItem::create($item);
            }
        });

        session()->flash('success', "Rencana pesanan untuk {$supplier->name} berhasil dibuat! Silakan tinjau dan kirim via WhatsApp.");
        $this->activeTab = 'orders';
        $this->orderStatusFilter = 'all';
    }

    /**
     * Create a single plan from all checked products across all suppliers
     */
    public function createPlanForAllSelected(): void
    {
        // Group checked products by supplier
        $checkedProductIds = [];
        foreach ($this->orderInputs as $prodId => $data) {
            if (!empty($data['selected']) && (int) $data['quantity'] > 0) {
                $checkedProductIds[] = $prodId;
            }
        }

        if (empty($checkedProductIds)) {
            session()->flash('error', 'Pilih minimal 1 produk untuk membuat rencana kulakan.');
            return;
        }

        $products = Product::whereIn('id', $checkedProductIds)->get();
        $grouped = $products->groupBy('supplier_id');

        $createdCount = 0;

        DB::transaction(function () use ($grouped, &$createdCount) {
            foreach ($grouped as $supplierId => $prods) {
                if (!$supplierId) {
                    continue; // Skip products without supplier or handle separately
                }

                $supplier = Supplier::find($supplierId);
                if (!$supplier) continue;

                $totalCost = 0;
                $items = [];

                foreach ($prods as $prod) {
                    $input = $this->orderInputs[$prod->id] ?? null;
                    if ($input) {
                        $qty = (int) $input['quantity'];
                        $cost = (float) ($input['unit_cost'] ?: $prod->cost_price);
                        $subtotal = $qty * $cost;
                        $totalCost += $subtotal;

                        $items[] = [
                            'product_id' => $prod->id,
                            'product_name' => $prod->name,
                            'current_stock' => $prod->stock,
                            'min_stock_alert' => $prod->min_stock_alert,
                            'quantity_ordered' => $qty,
                            'unit' => $prod->unit ?: 'pcs',
                            'unit_cost' => $cost,
                            'subtotal' => $subtotal,
                        ];
                    }
                }

                if (!empty($items)) {
                    $plan = RestockPlan::create([
                        'plan_number' => RestockPlan::generatePlanNumber(),
                        'supplier_id' => $supplierId,
                        'user_id' => Auth::id() ?? 1,
                        'status' => 'draft',
                        'total_estimated_cost' => $totalCost,
                        'notes' => 'Pesanan restock otomatis dibuat dari analisis stok rendah.',
                    ]);

                    foreach ($items as $item) {
                        $item['restock_plan_id'] = $plan->id;
                        RestockPlanItem::create($item);
                    }

                    $createdCount++;
                }
            }
        });

        if ($createdCount > 0) {
            session()->flash('success', "Berhasil membuat {$createdCount} rencana order kulakan terpisah per supplier!");
            $this->activeTab = 'orders';
        } else {
            session()->flash('error', 'Produk yang dipilih belum memiliki supplier pemasok. Harap tentukan supplier produk terlebih dahulu.');
        }
    }

    /**
     * Open WhatsApp preview modal
     */
    public function openWhatsAppModal(int $planId): void
    {
        $this->whatsAppPlan = RestockPlan::with(['supplier', 'items'])->find($planId);
        if ($this->whatsAppPlan) {
            $this->showWhatsAppModal = true;
        }
    }

    /**
     * Mark plan as Ordered (sent via WhatsApp)
     */
    public function markAsOrdered(int $planId): void
    {
        $plan = RestockPlan::find($planId);
        if ($plan) {
            $plan->update([
                'status' => 'ordered',
                'ordered_at' => Carbon::now(),
            ]);
            session()->flash('success', "Status pesanan {$plan->plan_number} diubah menjadi 'Sudah Dipesan' (Ordered).");
            $this->showWhatsAppModal = false;
        }
    }

    /**
     * Cancel a plan
     */
    public function cancelPlan(int $planId): void
    {
        $plan = RestockPlan::find($planId);
        if ($plan && $plan->status !== 'received') {
            $plan->update(['status' => 'cancelled']);
            session()->flash('success', "Rencana pesanan {$plan->plan_number} telah dibatalkan.");
        }
    }

    /**
     * Delete confirmation
     */
    public function confirmDelete(int $planId): void
    {
        $this->deletingPlanId = $planId;
        $this->deletingPlan = RestockPlan::with(['supplier', 'items'])->find($planId);
        if ($this->deletingPlan) {
            $this->showDeleteModal = true;
        }
    }

    /**
     * Delete a restock plan
     */
    public function deletePlan(): void
    {
        $id = $this->deletingPlanId;
        if (!$id) {
            return;
        }

        $plan = RestockPlan::find($id);
        if ($plan) {
            if ($plan->purchase_id || $plan->status === 'received') {
                session()->flash('error', 'Rencana ini sudah memiliki faktur penerimaan / pembelian barang terkait, tidak dapat dihapus.');
                $this->showDeleteModal = false;
                return;
            }

            $planNum = $plan->plan_number;
            DB::transaction(function () use ($plan) {
                $plan->items()->delete();
                $plan->delete();
            });

            session()->flash('success', "Rencana pesanan {$planNum} berhasil dihapus.");
            if ($this->selectedPlan && $this->selectedPlan->id === $id) {
                $this->showDetailModal = false;
                $this->selectedPlan = null;
            }
        }

        $this->showDeleteModal = false;
        $this->deletingPlanId = null;
        $this->deletingPlan = null;
    }

    /**
     * Open detail modal
     */
    public function openDetailModal(int $planId): void
    {
        $this->selectedPlan = RestockPlan::with(['supplier', 'items.product', 'user', 'purchase'])->find($planId);
        if ($this->selectedPlan) {
            $this->showDetailModal = true;
        }
    }

    /**
     * Quick assign supplier to product if empty
     */
    public function assignSupplierToProduct(int $productId, int $supplierId): void
    {
        $product = Product::find($productId);
        $supplier = Supplier::find($supplierId);

        if ($product && $supplier) {
            $product->update(['supplier_id' => $supplierId]);
            session()->flash('success', "Supplier {$supplier->name} berhasil ditautkan ke {$product->name}.");
        }
    }

    public function render()
    {
        // 1. Overall Stats
        $allLowStockCount = Product::active()->lowStock()->count();
        $outOfStockCount = Product::active()->where('stock', '<=', 0)->count();
        $pendingPlansCount = RestockPlan::whereIn('status', ['draft', 'ordered'])->count();

        // 2. Fetch Low Stock Data grouped by Supplier
        $lowStockQuery = Product::active()
            ->lowStock()
            ->with(['supplier', 'category'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('barcode', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedSupplierFilter, function ($q) {
                $q->where('supplier_id', $this->selectedSupplierFilter);
            })
            ->when($this->selectedCategoryFilter, function ($q) {
                $q->where('category_id', $this->selectedCategoryFilter);
            })
            ->when($this->stockUrgency === 'out_of_stock', function ($q) {
                $q->where('stock', '<=', 0);
            })
            ->orderBy('stock', 'asc')
            ->orderBy('name', 'asc');

        $lowStockProducts = $lowStockQuery->get();

        // Calculate total estimated capital needed
        $totalEstCapital = $lowStockProducts->sum(function ($p) {
            $input = $this->orderInputs[$p->id] ?? null;
            $qty = $input ? (int) $input['quantity'] : max(1, ($p->min_stock_alert * 2) - $p->stock);
            $cost = $input ? (float) $input['unit_cost'] : (float) $p->cost_price;
            return $qty * $cost;
        });

        // Group low stock products by supplier
        $productsBySupplier = $lowStockProducts->groupBy(function ($item) {
            return $item->supplier_id ? (int) $item->supplier_id : 0;
        });

        // 3. Fetch Orders List for Tab 2
        $ordersQuery = RestockPlan::with(['supplier', 'items', 'user'])
            ->when($this->orderStatusFilter !== 'all', function ($q) {
                $q->where('status', $this->orderStatusFilter);
            })
            ->when($this->orderSearch, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('plan_number', 'like', "%{$this->orderSearch}%")
                        ->orWhereHas('supplier', function ($sq) {
                            $sq->where('name', 'like', "%{$this->orderSearch}%");
                        });
                });
            })
            ->latest();

        $orders = $ordersQuery->paginate(10);

        $suppliers = Supplier::active()->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.purchase.restock-planner', [
            'allLowStockCount' => $allLowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'pendingPlansCount' => $pendingPlansCount,
            'totalEstCapital' => $totalEstCapital,
            'productsBySupplier' => $productsBySupplier,
            'lowStockProducts' => $lowStockProducts,
            'orders' => $orders,
            'suppliers' => $suppliers,
            'categories' => $categories,
        ]);
    }
}
