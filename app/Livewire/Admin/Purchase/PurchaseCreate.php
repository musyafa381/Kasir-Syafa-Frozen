<?php

namespace App\Livewire\Admin\Purchase;

use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMutation;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Buat Faktur Pembelian & Restock - Syafa Frozen Food')]
class PurchaseCreate extends Component
{
    // Invoice & Supplier State
    public string $invoice_number = '';
    public string $supplier_invoice_number = '';
    public ?int $supplier_id = null;
    public ?int $outlet_id = null;
    public string $purchase_date = '';
    public string $payment_type = 'cash'; // cash, transfer, tempo
    public ?string $due_date = null;
    public float|int|string $paid_amount = 0;
    public string $notes = '';

    // Restock Plan Linking State
    public ?int $restock_plan_id = null;
    public ?string $restock_plan_number = null;
    public bool $showRestockModal = false;

    // Add Item Form State
    public ?int $selectedProductId = null;
    public string $purchaseMode = 'unit'; // 'unit' (eceran/pack/pcs) or 'box' (kardus/dus)
    public int $boxCount = 1; // jumlah dus
    public int $boxPieces = 24; // isi per dus
    public float|int|string $boxPrice = 0; // harga beli 1 dus
    public int $inputQuantity = 1;
    public float|int|string $inputUnitCost = 0;
    public bool $inputUpdateCostPrice = true;
    public ?Product $selectedProductModel = null;

    public array $items = [];

    public function mount(?int $restock_id = null): void
    {
        $this->invoice_number = Purchase::generateInvoiceNumber();
        $this->outlet_id = Outlet::where('is_main', true)->first()?->id ?? Outlet::first()?->id;
        $this->purchase_date = Carbon::now()->format('Y-m-d');
        $this->payment_type = 'cash';
        $this->due_date = Carbon::now()->addDays(14)->format('Y-m-d');
        $this->paid_amount = 0;
        $this->items = [];

        if ($restock_id) {
            $this->loadFromRestockPlan($restock_id);
        }
    }

    public function loadFromRestockPlan(int $planId): void
    {
        $plan = \App\Models\RestockPlan::with(['supplier', 'items.product'])->find($planId);
        if (!$plan) {
            session()->flash('error', 'Rencana kulakan tidak ditemukan.');
            return;
        }

        $this->restock_plan_id = $plan->id;
        $this->restock_plan_number = $plan->plan_number;
        $this->supplier_id = $plan->supplier_id;
        $this->updatedSupplierId();

        if ($plan->notes && empty($this->notes)) {
            $this->notes = "Dari Kulakan: {$plan->plan_number} - {$plan->notes}";
        }

        $this->items = [];
        foreach ($plan->items as $item) {
            $product = $item->product ?: Product::find($item->product_id);
            $currentStock = $product ? $product->stock : $item->current_stock;
            $qty = max(1, (int) $item->quantity_ordered);
            $unitCost = (float) $item->unit_cost;

            $this->items[] = [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'current_stock' => $currentStock,
                'quantity' => $qty,
                'unit_cost' => $unitCost,
                'update_cost_price' => true,
                'subtotal' => $qty * $unitCost,
            ];
        }

        $this->showRestockModal = false;
        session()->flash('success', "Berhasil menarik data pesanan {$plan->plan_number}! Data supplier & {$plan->items->count()} item telah otomatis terisi.");
    }

    public function detachRestockPlan(): void
    {
        $this->restock_plan_id = null;
        $this->restock_plan_number = null;
        session()->flash('info', 'Tautan rencana kulakan dilepas.');
    }

    public function updatedSelectedProductId(): void
    {
        if ($this->selectedProductId) {
            $this->selectedProductModel = Product::find($this->selectedProductId);
            if ($this->selectedProductModel) {
                $this->inputUnitCost = (float) $this->selectedProductModel->cost_price;
                if ($this->inputQuantity < 1) {
                    $this->inputQuantity = 1;
                }
                if ($this->purchaseMode === 'box') {
                    $this->boxPrice = $this->inputUnitCost * max(1, $this->boxPieces);
                    $this->recalculateBoxValues();
                }
            }
        } else {
            $this->selectedProductModel = null;
            $this->inputUnitCost = 0;
            $this->boxPrice = 0;
        }
    }

    public function updatedPurchaseMode(): void
    {
        $this->recalculateBoxValues();
    }

    public function updatedBoxCount(): void
    {
        $this->recalculateBoxValues();
    }

    public function updatedBoxPieces(): void
    {
        $this->recalculateBoxValues();
    }

    public function updatedBoxPrice(): void
    {
        $this->recalculateBoxValues();
    }

    private function recalculateBoxValues(): void
    {
        if ($this->purchaseMode === 'box') {
            $boxes = max(1, (int) $this->boxCount);
            $pieces = max(1, (int) $this->boxPieces);
            $this->inputQuantity = $boxes * $pieces;

            $totalBoxPrice = (float) $this->boxPrice;
            if ($totalBoxPrice > 0 && $pieces > 0) {
                $this->inputUnitCost = round($totalBoxPrice / $pieces, 2);
            }
        }
    }

    public function updatedSupplierId(): void
    {
        if ($this->supplier_id) {
            $supplier = Supplier::find($this->supplier_id);
            if ($supplier) {
                if (str_starts_with($supplier->payment_term, 'tempo_')) {
                    $this->payment_type = 'tempo';
                    $days = (int) str_replace('tempo_', '', $supplier->payment_term);
                    $this->due_date = Carbon::now()->addDays($days)->format('Y-m-d');
                }
            }
        }
    }

    public function addToCart(): void
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'inputQuantity' => 'required|integer|min:1',
            'inputUnitCost' => 'required|numeric|min:0',
            'inputUpdateCostPrice' => 'boolean',
        ], [
            'selectedProductId.required' => 'Pilih produk frozen food terlebih dahulu.',
            'selectedProductId.exists' => 'Produk tidak ditemukan.',
            'inputQuantity.min' => 'Jumlah barang minimal 1 unit.',
            'inputUnitCost.min' => 'Harga beli modal tidak boleh negatif.',
        ]);

        $product = Product::findOrFail($this->selectedProductId);
        $qty = (int) $this->inputQuantity;
        $unitCost = (float) $this->inputUnitCost;

        // Check if product is already in cart
        $existingIndex = null;
        foreach ($this->items as $idx => $item) {
            if ((int) $item['product_id'] === (int) $this->selectedProductId) {
                $existingIndex = $idx;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Update existing cart row
            $this->items[$existingIndex]['quantity'] += $qty;
            $this->items[$existingIndex]['unit_cost'] = $unitCost;
            $this->items[$existingIndex]['update_cost_price'] = $this->inputUpdateCostPrice;
            $this->items[$existingIndex]['subtotal'] = $this->items[$existingIndex]['quantity'] * $unitCost;
        } else {
            // Add new cart row
            $currentStock = $this->outlet_id ? $product->getStockForOutlet($this->outlet_id) : $product->stock;
            $this->items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $currentStock,
                'quantity' => $qty,
                'unit_cost' => $unitCost,
                'update_cost_price' => $this->inputUpdateCostPrice,
                'subtotal' => $qty * $unitCost,
            ];
        }

        // Reset Add Item Form
        $this->selectedProductId = null;
        $this->selectedProductModel = null;
        $this->purchaseMode = 'unit';
        $this->boxCount = 1;
        $this->boxPieces = 24;
        $this->boxPrice = 0;
        $this->inputQuantity = 1;
        $this->inputUnitCost = 0;
        $this->inputUpdateCostPrice = true;
        $this->resetValidation(['selectedProductId', 'inputQuantity', 'inputUnitCost']);
    }

    public function getSelectedProductStockProperty(): int
    {
        if (!$this->selectedProductModel) return 0;
        return $this->outlet_id ? $this->selectedProductModel->getStockForOutlet($this->outlet_id) : (int) $this->selectedProductModel->stock;
    }

    public function removeFromCart(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function incrementQuantity(int $index): void
    {
        if (isset($this->items[$index])) {
            $this->items[$index]['quantity']++;
            $this->items[$index]['subtotal'] = $this->items[$index]['quantity'] * (float) $this->items[$index]['unit_cost'];
        }
    }

    public function decrementQuantity(int $index): void
    {
        if (isset($this->items[$index])) {
            if ($this->items[$index]['quantity'] > 1) {
                $this->items[$index]['quantity']--;
                $this->items[$index]['subtotal'] = $this->items[$index]['quantity'] * (float) $this->items[$index]['unit_cost'];
            } else {
                $this->removeFromCart($index);
            }
        }
    }

    public function updatedItems(mixed $value, ?string $key = null): void
    {
        // When quantity or unit_cost is edited directly in cart table
        if ($key) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];
            if (isset($this->items[$index])) {
                $qty = max(1, (int) ($this->items[$index]['quantity'] ?? 1));
                $cost = max(0, (float) ($this->items[$index]['unit_cost'] ?? 0));
                $this->items[$index]['quantity'] = $qty;
                $this->items[$index]['unit_cost'] = $cost;
                $this->items[$index]['subtotal'] = $qty * $cost;
            }
        }
    }

    public function getCalculatedTotalProperty(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            $qty = max(0, (int) ($item['quantity'] ?? 0));
            $cost = max(0, (float) ($item['unit_cost'] ?? 0));
            $total += ($qty * $cost);
        }
        return $total;
    }

    public function savePurchase()
    {
        $hasOutlets = Outlet::exists();
        $this->validate([
            'invoice_number' => 'required|string|unique:purchases,invoice_number',
            'supplier_invoice_number' => 'nullable|string|max:100',
            'supplier_id' => 'required|exists:suppliers,id',
            'outlet_id' => $hasOutlets ? 'required|exists:outlets,id' : 'nullable',
            'purchase_date' => 'required|date',
            'payment_type' => 'required|in:cash,transfer,tempo',
            'due_date' => 'nullable|date|required_if:payment_type,tempo',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.update_cost_price' => 'boolean',
        ], [
            'supplier_id.required' => 'Silakan pilih supplier pemasok.',
            'outlet_id.required' => 'Silakan pilih cabang / gudang penerima barang.',
            'items.required' => 'Keranjang restock masih kosong. Silakan masukkan minimal 1 produk.',
            'items.min' => 'Keranjang restock masih kosong. Silakan masukkan minimal 1 produk.',
            'items.*.product_id.required' => 'Pilih produk pada setiap baris item.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1 unit.',
            'items.*.unit_cost.min' => 'Harga beli tidak boleh negatif.',
        ]);

        $totalAmount = $this->calculatedTotal;
        $paidAmount = (float) $this->paid_amount;

        // Auto-handle full paid amount for cash/transfer
        if ($this->payment_type !== 'tempo' && $paidAmount === 0.0 && $totalAmount > 0) {
            $paidAmount = $totalAmount;
        }

        $paymentStatus = 'paid';
        if ($paidAmount < $totalAmount) {
            $paymentStatus = $paidAmount > 0 ? 'partial' : 'unpaid';
        }

        DB::transaction(function () use ($totalAmount, $paidAmount, $paymentStatus) {
            $supplier = Supplier::findOrFail($this->supplier_id);
            $targetOutlet = $this->outlet_id ? Outlet::find($this->outlet_id) : null;
            $isMainOutlet = $targetOutlet ? (bool) $targetOutlet->is_main : true;

            // 1. Create Purchase Record
            $purchase = Purchase::create([
                'invoice_number' => $this->invoice_number,
                'supplier_invoice_number' => $this->supplier_invoice_number ?: null,
                'supplier_id' => $this->supplier_id,
                'outlet_id' => $this->outlet_id,
                'user_id' => Auth::id() ?? 1,
                'purchase_date' => $this->purchase_date,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'payment_type' => $this->payment_type,
                'payment_status' => $paymentStatus,
                'due_date' => $this->payment_type === 'tempo' ? $this->due_date : null,
                'notes' => $this->notes ?: null,
            ]);

            // 2. Process Items, Increment Stocks & Record Mutations
            foreach ($this->items as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                $qty = (int) $itemData['quantity'];
                $unitCost = (float) $itemData['unit_cost'];
                $subtotal = $qty * $unitCost;
                $updateCost = (bool) ($itemData['update_cost_price'] ?? true);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                    'update_cost_price' => $updateCost,
                ]);

                $oldStock = (int) $product->stock;
                $newStock = $oldStock + $qty;

                // Update stock in target outlet if specified
                if ($this->outlet_id) {
                    $op = OutletProduct::firstOrNew([
                        'outlet_id' => $this->outlet_id,
                        'product_id' => $product->id,
                    ]);
                    $oldOutletStock = $op->exists ? (int) $op->stock : ($isMainOutlet ? $oldStock : 0);
                    $newOutletStock = $oldOutletStock + $qty;
                    $op->stock = $newOutletStock;
                    if ($updateCost) {
                        $op->cost_price = $unitCost;
                    }
                    $op->save();
                }

                // Update master product stock
                if ($isMainOutlet || !$this->outlet_id) {
                    $productUpdateData = ['stock' => $newStock];
                    if ($updateCost) {
                        $productUpdateData['cost_price'] = $unitCost;
                    }
                    if (!$product->supplier_id) {
                        $productUpdateData['supplier_id'] = $this->supplier_id;
                    }
                    $product->update($productUpdateData);
                } elseif ($updateCost || !$product->supplier_id) {
                    $productUpdateData = [];
                    if ($updateCost) {
                        $productUpdateData['cost_price'] = $unitCost;
                    }
                    if (!$product->supplier_id) {
                        $productUpdateData['supplier_id'] = $this->supplier_id;
                    }
                    $product->update($productUpdateData);
                }

                $outletName = $targetOutlet ? $targetOutlet->name : 'Pusat';
                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id() ?? 1,
                    'outlet_id' => $this->outlet_id,
                    'supplier_id' => $this->supplier_id,
                    'purchase_id' => $purchase->id,
                    'type' => 'in',
                    'quantity' => $qty,
                    'stock_before' => $oldStock,
                    'stock_after' => $newStock,
                    'reference' => $purchase->invoice_number,
                    'description' => "Restock masuk ke {$outletName} dari {$supplier->name} (PO: {$purchase->invoice_number})",
                ]);
            }

            // 3. If linked to a RestockPlan, mark it as received
            if ($this->restock_plan_id) {
                \App\Models\RestockPlan::where('id', $this->restock_plan_id)->update([
                    'status' => 'received',
                    'purchase_id' => $purchase->id,
                    'received_at' => Carbon::now(),
                ]);
            }
        });

        session()->flash('success', "Faktur pembelian {$this->invoice_number} berhasil disimpan dan stok cabang otomatis bertambah.");
        return $this->redirect(route('admin.purchases.index'), navigate: true);
    }

    public function render()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        $allProducts = Product::orderBy('name')->get();
        $availableRestockPlans = \App\Models\RestockPlan::with(['supplier', 'items'])
            ->whereIn('status', ['draft', 'ordered'])
            ->latest()
            ->get();

        return view('livewire.admin.purchase.purchase-create', [
            'suppliers' => $suppliers,
            'outlets' => $outlets,
            'allProducts' => $allProducts,
            'availableRestockPlans' => $availableRestockPlans,
        ]);
    }
}
