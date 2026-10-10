<?php

namespace App\Livewire\Pos;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\HoldTransaction;
use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\ReceiptSetting;
use App\Models\StockMutation;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.pos')]
#[Title('Terminal Kasir - Syafa Frozen Food')]
class PosTerminal extends Component
{
    // Step Navigation: 'catalog', 'customer', 'payment', 'receipt'
    public string $currentStep = 'catalog';

    // Active Branch / Outlet
    public ?int $activeOutletId = null;
    public bool $showOutletModal = false;

    // Search & Category
    public string $search = '';
    public ?int $selectedCategoryId = null;

    // Cart State: [productId => ['product_id' => ..., 'name' => ..., 'price' => ..., 'regular_price' => ..., 'loyalty_price' => ..., 'cost_price' => ..., 'qty' => ..., 'stock' => ..., 'unit' => ...]]
    public array $cart = [];

    // Customer & Loyalty State
    public ?int $selectedCustomerId = null;
    public string $customerName = 'Pelanggan Umum';
    public bool $isLoyalty = false;
    public string $customerSearch = '';
    public bool $showCustomerModal = false;
    public bool $showQuickMemberModal = false;

    // Quick Member Form Fields
    public string $quickMemberName = '';
    public string $quickMemberPhone = '';
    public string $quickMemberAddress = '';

    // Discount & Tax
    public ?int $selectedDiscountId = null;
    public bool $showDiscountModal = false;
    public $discountAmount = 0;

    // Item-Level Discount & Pricing Modal
    public bool $showItemDiscountModal = false;
    public ?string $editingCartKey = null;
    public string $itemDiscountType = 'percent'; // 'percent' or 'fixed'
    public $itemDiscountRate = '';
    
    public bool $enableTax = false;
    public ?int $selectedTaxId = null;
    public bool $showTaxModal = false;
    public $taxAmount = 0;

    // Payment Modal State
    public bool $showPaymentModal = false;
    public string $paymentMethod = 'cash'; // cash, qris, transfer, tempo, split
    public $paidAmount = 0;
    public string $paymentNotes = '';
    public string $tempoDueDate = '';
    public string $tempoNotes = '';

    // Split Payment State
    public $splitCashAmount = 0;
    public $splitNonCashAmount = 0;
    public string $splitNonCashMethod = 'qris'; // 'qris' or 'transfer'

    // Cashier Shift State
    public ?int $activeShiftId = null;
    public bool $showOpenShiftModal = false;
    public bool $showCloseShiftModal = false;
    public bool $showShiftReceiptModal = false;
    public $shiftStartingCash = 200000;
    public string $shiftOpeningNotes = '';
    public $shiftActualEndingCash = '';
    public string $shiftClosingNotes = '';
    public ?int $lastClosedShiftId = null;

    // Hold / Suspend Transaction State
    public bool $showHoldModal = false;
    public bool $showHoldListModal = false;
    public string $holdCustomerNote = '';

    // Receipt Modal State
    public bool $showReceiptModal = false;
    public ?int $lastTransactionId = null;
    public string $whatsappPhone = '';

    // Mobile Cart Drawer
    public bool $mobileCartOpen = false;

    // Error & Flash messages in POS
    public ?string $posAlert = null;
    public ?string $posAlertType = 'info';

    // Quick Add Product State (POS Fast Action)
    public bool $showQuickProductModal = false;
    public string $quickProductName = '';
    public string $quickProductBarcode = '';
    public ?int $quickProductCategoryId = null;
    public string $quickProductUnit = 'pack';
    public $quickProductCostPrice = '';
    public $quickProductSellingPrice = '';
    public $quickProductLoyaltyPrice = '';
    public $quickProductStock = '';
    public int $quickProductMinStock = 5;
    public bool $quickProductAutoAddToCart = true;

    // Quick Add Stock State (POS Fast Action)
    public bool $showQuickStockModal = false;
    public ?int $quickStockProductId = null;
    public string $quickStockProductName = '';
    public string $quickStockProductUnit = 'pcs';
    public int $quickStockCurrentStock = 0;
    public $quickStockQuantity = '';
    public string $quickStockNotes = 'Restock cepat dari kasir';

    public function mount(): void
    {
        $user = Auth::user();
        $activeOutlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        $savedOutletId = session('pos_active_outlet_id');
        $savedOutletExists = $savedOutletId ? Outlet::where('id', $savedOutletId)->where('is_active', true)->exists() : false;

        if ($savedOutletExists) {
            // Prioritaskan cabang yang aktif dipilih pada sesi POS ini
            $this->activeOutletId = (int) $savedOutletId;
            $this->showOutletModal = false;
        } elseif ($user && $user->outlet_id && Outlet::where('id', $user->outlet_id)->where('is_active', true)->exists()) {
            // Fallback ke cabang yang di-assign pada profil user saat pertama buka POS
            $this->activeOutletId = (int) $user->outlet_id;
            session(['pos_active_outlet_id' => $this->activeOutletId]);
            $this->showOutletModal = false;
        } elseif ($activeOutlets->count() > 1) {
            // Jika ada lebih dari 1 cabang dan belum ada yang dipilih, buka modal pemilihan
            $this->activeOutletId = $activeOutlets->where('is_main', true)->value('id') ?? $activeOutlets->first()->id;
            session(['pos_active_outlet_id' => $this->activeOutletId]);
            $this->showOutletModal = true;
        } else {
            // Default ke cabang tunggal yang tersedia
            $this->activeOutletId = $activeOutlets->first()?->id ?? Outlet::where('is_main', true)->value('id');
            if ($this->activeOutletId) {
                session(['pos_active_outlet_id' => $this->activeOutletId]);
            }
            $this->showOutletModal = false;
        }

        $this->checkActiveShift();
        $this->dispatch('focus-search');
    }

    public function selectOutlet(int $outletId): void
    {
        $outlet = Outlet::where('is_active', true)->find($outletId);
        if ($outlet) {
            $this->activeOutletId = $outlet->id;
            session(['pos_active_outlet_id' => $outlet->id]);
            $this->showOutletModal = false;
            $this->recalculateCartPrices();
            $this->showToast("Cabang aktif: {$outlet->name}", 'success');
            $this->dispatch('focus-search');
        }
    }

    public function openOutletModal(): void
    {
        $this->showOutletModal = true;
    }

    public function closeOutletModal(): void
    {
        if ($this->activeOutletId) {
            $this->showOutletModal = false;
            $this->dispatch('focus-search');
        }
    }

    public function switchOutlet(int $outletId): void
    {
        $this->selectOutlet($outletId);
    }

    // ================= STEP NAVIGATION =================
    public function setStep(string $step): void
    {
        if ($step === 'payment' && empty($this->cart)) {
            $this->showToast('Keranjang belanja masih kosong! Silakan pilih produk terlebih dahulu.', 'warning');
            $this->currentStep = 'catalog';
            $this->showPaymentModal = false;
            $this->dispatch('focus-search');
            return;
        }

        $this->currentStep = $step;
        $this->showPaymentModal = ($step === 'payment');
        $this->showReceiptModal = ($step === 'receipt');

        if ($step === 'catalog') {
            $this->dispatch('focus-search');
        }

        if ($step === 'payment') {
            if (empty($this->tempoDueDate)) {
                $this->tempoDueDate = \Carbon\Carbon::now()->addDays(14)->format('Y-m-d');
            }
            if ($this->paymentMethod === 'tempo') {
                $this->paidAmount = 0;
            } else {
                $this->paidAmount = (int) $this->grandTotal;
            }
        }
    }

    public function goToPayment(): void
    {
        $this->mobileCartOpen = false;
        $this->setStep('payment');
    }

    public function openMobileCartModal(): void
    {
        $this->mobileCartOpen = true;
    }

    public function closeMobileCartModal(): void
    {
        $this->mobileCartOpen = false;
        $this->dispatch('focus-search');
    }

    public function backToCatalog(): void
    {
        $this->setStep('catalog');
    }

    public function goToCustomer(): void
    {
        $this->openCustomerModal();
    }

    public function startNewTransaction(): void
    {
        $this->cart = [];
        $this->selectedDiscountId = null;
        $this->showDiscountModal = false;
        $this->discountAmount = 0;
        $this->enableTax = false;
        $this->selectedTaxId = null;
        $this->showTaxModal = false;
        $this->taxAmount = 0;
        $this->selectedCustomerId = null;
        $this->customerName = 'Pelanggan Umum';
        $this->isLoyalty = false;
        $this->paymentNotes = '';
        $this->tempoDueDate = '';
        $this->tempoNotes = '';
        $this->lastTransactionId = null;
        $this->showCustomerModal = false;
        $this->showQuickMemberModal = false;
        $this->setStep('catalog');
    }

    public function updatedSearch(): void
    {
        $query = trim($this->search);
        if ($query === '') {
            return;
        }

        // 1. Cek jika barcode kardus/dus cocok persis
        $matchedBoxProduct = Product::where('box_barcode', $query)
            ->where('is_active', true)
            ->first();

        if ($matchedBoxProduct) {
            $this->addToCart($matchedBoxProduct->id, 'box');
            $this->search = '';
            $this->dispatch('focus-search');
            return;
        }

        // 2. Cek jika barcode ecer cocok persis
        $matchedProduct = Product::where('barcode', $query)
            ->where('is_active', true)
            ->first();

        if ($matchedProduct) {
            $this->addToCart($matchedProduct->id, 'unit');
            $this->search = '';
            $this->dispatch('focus-search');
        }
    }

    public function submitBarcodeOrSearch(): void
    {
        $query = trim($this->search);
        if ($query === '') {
            $this->dispatch('focus-search');
            return;
        }

        // 1. Coba cari barcode kardus/dus yang cocok persis
        $boxProduct = Product::where('box_barcode', $query)
            ->where('is_active', true)
            ->first();

        if ($boxProduct) {
            $this->addToCart($boxProduct->id, 'box');
            $this->search = '';
            $this->dispatch('focus-search');
            return;
        }

        // 2. Coba cari barcode eceran yang cocok persis
        $product = Product::where('barcode', $query)
            ->where('is_active', true)
            ->first();

        if ($product) {
            $this->addToCart($product->id, 'unit');
            $this->search = '';
            $this->dispatch('focus-search');
            return;
        }

        // 3. Jika barcode tidak cocok, cari nama atau barcode
        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('barcode', 'like', '%' . $query . '%')
                  ->orWhere('box_barcode', 'like', '%' . $query . '%');
            })
            ->get();

        if ($products->count() === 1) {
            $this->addToCart($products->first()->id, 'unit');
            $this->search = '';
        }

        $this->dispatch('focus-search');
    }

    public function setCategory(?int $categoryId = null): void
    {
        $this->selectedCategoryId = $categoryId;
        $this->dispatch('focus-search');
    }

    // ================= CUSTOMER & LOYALTY MANAGEMENT =================
    public function openCustomerModal(): void
    {
        $this->customerSearch = '';
        $this->showCustomerModal = true;
    }

    public function closeCustomerModal(): void
    {
        $this->showCustomerModal = false;
        $this->dispatch('focus-search');
    }

    public function selectCustomer(?int $customerId = null): void
    {
        if (!$customerId) {
            $this->selectedCustomerId = null;
            $this->customerName = 'Pelanggan Umum';
            $this->isLoyalty = false;
        } else {
            $customer = Customer::find($customerId);
            if ($customer) {
                $this->selectedCustomerId = $customer->id;
                $this->customerName = $customer->name;
                $this->isLoyalty = (bool) $customer->is_active;
            }
        }

        $this->recalculateCartPrices();
        $this->showCustomerModal = false;
        $this->dispatch('focus-search');
    }

    public function toggleLoyalty(): void
    {
        $this->isLoyalty = !$this->isLoyalty;
        $this->recalculateCartPrices();
        $this->dispatch('focus-search');
    }

    public function openQuickMemberModal(): void
    {
        $this->reset(['quickMemberName', 'quickMemberPhone', 'quickMemberAddress']);
        $this->resetValidation();
        $this->showQuickMemberModal = true;
    }

    public function closeQuickMemberModal(): void
    {
        $this->showQuickMemberModal = false;
    }

    public function saveQuickMember(): void
    {
        $this->validate([
            'quickMemberName' => 'required|min:2|max:100',
            'quickMemberPhone' => 'nullable|max:25',
            'quickMemberAddress' => 'nullable|max:255',
        ]);

        $customer = Customer::create([
            'name' => $this->quickMemberName,
            'phone' => $this->quickMemberPhone ?: null,
            'member_code' => Customer::generateMemberCode(),
            'address' => $this->quickMemberAddress ?: null,
            'is_active' => true,
        ]);

        $this->showQuickMemberModal = false;
        $this->selectCustomer($customer->id);
        $this->showToast("Member baru '{$customer->name}' berhasil didaftarkan & dipilih.", 'success');
        $this->dispatch('focus-search');
    }

    // ================= QUICK PRODUCT & STOCK METHODS =================
    public function openQuickProductModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'quickProductName',
            'quickProductBarcode',
            'quickProductCategoryId',
            'quickProductCostPrice',
            'quickProductSellingPrice',
            'quickProductLoyaltyPrice',
            'quickProductStock',
        ]);
        $this->quickProductUnit = 'pack';
        $this->quickProductMinStock = 5;
        $this->quickProductAutoAddToCart = true;

        $firstCat = Category::first();
        if ($firstCat) {
            $this->quickProductCategoryId = $firstCat->id;
        }

        $this->showQuickProductModal = true;
    }

    public function closeQuickProductModal(): void
    {
        $this->showQuickProductModal = false;
        $this->dispatch('focus-search');
    }

    public function saveQuickProduct(): void
    {
        $this->validate([
            'quickProductName' => 'required|min:2|max:255',
            'quickProductBarcode' => 'nullable|max:100|unique:products,barcode',
            'quickProductCategoryId' => 'nullable|exists:categories,id',
            'quickProductUnit' => 'required|string|max:50',
            'quickProductCostPrice' => 'nullable|numeric|min:0',
            'quickProductSellingPrice' => 'required|numeric|min:1',
            'quickProductLoyaltyPrice' => 'nullable|numeric|min:0',
            'quickProductStock' => 'nullable|integer|min:0',
            'quickProductMinStock' => 'nullable|integer|min:0',
        ], [
            'quickProductName.required' => 'Nama produk wajib diisi.',
            'quickProductSellingPrice.required' => 'Harga jual wajib diisi.',
            'quickProductSellingPrice.min' => 'Harga jual minimal Rp 1.',
            'quickProductBarcode.unique' => 'Barcode ini sudah digunakan oleh produk lain.',
        ]);

        $costPrice = (float) ($this->quickProductCostPrice ?: 0);
        $sellingPrice = (float) $this->quickProductSellingPrice;
        $loyaltyPrice = ($this->quickProductLoyaltyPrice !== '' && $this->quickProductLoyaltyPrice !== null) ? (float) $this->quickProductLoyaltyPrice : $sellingPrice;
        $initialStock = (int) ($this->quickProductStock ?: 0);
        $minStock = (int) ($this->quickProductMinStock ?: 5);

        DB::transaction(function () use ($costPrice, $sellingPrice, $loyaltyPrice, $initialStock, $minStock) {
            $product = Product::create([
                'category_id' => $this->quickProductCategoryId ?: Category::first()?->id,
                'name' => trim($this->quickProductName),
                'barcode' => $this->quickProductBarcode ? trim($this->quickProductBarcode) : null,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'loyalty_price' => $loyaltyPrice,
                'stock' => $initialStock,
                'min_stock_alert' => $minStock,
                'unit' => $this->quickProductUnit ?: 'pack',
                'is_active' => true,
            ]);

            if ($this->activeOutletId) {
                OutletProduct::updateOrCreate(
                    [
                        'outlet_id' => $this->activeOutletId,
                        'product_id' => $product->id,
                    ],
                    [
                        'stock' => $initialStock,
                        'min_stock_alert' => $minStock,
                        'cost_price' => $costPrice,
                        'selling_price' => $sellingPrice,
                        'loyalty_price' => $loyaltyPrice,
                    ]
                );

                if ($initialStock > 0) {
                    StockMutation::create([
                        'product_id' => $product->id,
                        'outlet_id' => $this->activeOutletId,
                        'user_id' => Auth::id(),
                        'type' => 'in',
                        'quantity' => $initialStock,
                        'stock_before' => 0,
                        'stock_after' => $initialStock,
                        'description' => 'Stok awal produk baru (Input cepat kasir POS)',
                    ]);
                }
            }

            $this->showQuickProductModal = false;

            if ($this->quickProductAutoAddToCart) {
                $this->addToCart($product->id, 'unit');
            }

            $this->showToast("Produk '{$product->name}' berhasil ditambahkan & siap dijual!", 'success');
        });

        $this->dispatch('focus-search');
    }

    public function openQuickStockModal(int $productId): void
    {
        $this->resetValidation();
        $product = Product::find($productId);
        if (!$product) {
            $this->showToast('Produk tidak ditemukan.', 'error');
            return;
        }

        $this->quickStockProductId = $product->id;
        $this->quickStockProductName = $product->name;
        $this->quickStockProductUnit = $product->unit ?: 'pcs';
        $this->quickStockCurrentStock = (int) $product->getStockForOutlet($this->activeOutletId);
        $this->quickStockQuantity = '';
        $this->quickStockNotes = 'Restock cepat dari kasir';
        $this->showQuickStockModal = true;
    }

    public function closeQuickStockModal(): void
    {
        $this->showQuickStockModal = false;
        $this->reset(['quickStockProductId', 'quickStockProductName', 'quickStockQuantity']);
        $this->dispatch('focus-search');
    }

    public function setQuickStockPreset(int $qty): void
    {
        $this->quickStockQuantity = $qty;
    }

    public function saveQuickStock(): void
    {
        $this->validate([
            'quickStockQuantity' => 'required|integer|min:1',
            'quickStockNotes' => 'nullable|string|max:255',
        ], [
            'quickStockQuantity.required' => 'Jumlah penambahan stok wajib diisi.',
            'quickStockQuantity.min' => 'Jumlah penambahan stok minimal 1.',
        ]);

        $product = Product::find($this->quickStockProductId);
        if (!$product) {
            $this->showToast('Produk tidak ditemukan.', 'error');
            $this->closeQuickStockModal();
            return;
        }

        $addQty = (int) $this->quickStockQuantity;
        $currentStock = (int) $product->getStockForOutlet($this->activeOutletId);
        $newStock = $currentStock + $addQty;

        DB::transaction(function () use ($product, $addQty, $currentStock, $newStock) {
            $outlet = Outlet::find($this->activeOutletId);
            $isMain = $outlet ? $outlet->is_main : false;

            $op = OutletProduct::firstOrNew([
                'outlet_id' => $this->activeOutletId,
                'product_id' => $product->id,
            ]);

            $op->stock = $newStock;
            if (!$op->exists) {
                $op->cost_price = $product->cost_price;
                $op->selling_price = $product->selling_price;
                $op->loyalty_price = $product->loyalty_price;
                $op->min_stock_alert = $product->min_stock_alert;
            }
            $op->save();

            if ($isMain) {
                $product->update(['stock' => $newStock]);
            }

            StockMutation::create([
                'product_id' => $product->id,
                'outlet_id' => $this->activeOutletId,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => $addQty,
                'stock_before' => $currentStock,
                'stock_after' => $newStock,
                'description' => $this->quickStockNotes ?: 'Restock cepat kasir di POS',
            ]);

            // Update info stok di keranjang jika ada
            $unitKey = "{$product->id}:unit";
            $boxKey = "{$product->id}:box";
            if (isset($this->cart[$unitKey])) {
                $this->cart[$unitKey]['stock'] = $newStock;
            }
            if (isset($this->cart[$boxKey])) {
                $this->cart[$boxKey]['stock'] = $newStock;
            }
        });

        $this->showQuickStockModal = false;
        $this->showToast("Stok '{$product->name}' bertambah +{$addQty} {$product->unit}. Sisa stok: {$newStock} {$product->unit}.", 'success');
        $this->dispatch('focus-search');
    }

    public function calculateItemSubtotal(array &$item): void
    {
        $qty = (int) ($item['qty'] ?? 1);
        $price = (float) ($item['price'] ?? 0);
        $gross = $price * $qty;

        $discType = $item['discount_type'] ?? null;
        $discRate = (float) ($item['discount_rate'] ?? 0);
        $discAmount = 0;

        if ($discType === 'percent' && $discRate > 0) {
            $discAmount = ($gross * $discRate) / 100;
        } elseif ($discType === 'fixed' && $discRate > 0) {
            // Potongan nominal per item x kuantitas
            $discAmount = min($gross, $discRate * $qty);
        }

        $item['discount_amount'] = max(0.0, round($discAmount, 2));
        $item['subtotal'] = max(0.0, round($gross - $item['discount_amount'], 2));
    }

    public function recalculateCartPrices(): void
    {
        foreach ($this->cart as $key => $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $unitType = $item['unit_type'] ?? 'unit';
                $piecesPerUnit = (int) ($item['pieces_per_unit'] ?? 1);
                $op = $product->getOutletProduct($this->activeOutletId);

                $regularPrice = ($op && $op->selling_price) ? (float)$op->selling_price : (float)$product->selling_price;
                $loyaltyPrice = ($op && $op->loyalty_price) ? (float)$op->loyalty_price : (float)($product->loyalty_price ?? $product->selling_price);

                if ($unitType === 'box') {
                    $regularPrice = ($op && $op->box_selling_price) ? (float)$op->box_selling_price : (float)($product->box_selling_price ?: ($product->selling_price * $piecesPerUnit));
                    $loyaltyPrice = ($op && $op->box_loyalty_price) ? (float)$op->box_loyalty_price : (float)($product->box_loyalty_price ?: ($product->loyalty_price ? $product->loyalty_price * $piecesPerUnit : $regularPrice));
                }

                $this->cart[$key]['regular_price'] = $regularPrice;
                $this->cart[$key]['loyalty_price'] = $loyaltyPrice;

                // Sync item price type default according to global isLoyalty if not explicitly modified
                $priceType = $this->isLoyalty ? 'loyalty' : 'regular';
                $this->cart[$key]['price_type'] = $priceType;
                $this->cart[$key]['price'] = ($priceType === 'loyalty') ? $loyaltyPrice : $regularPrice;
                $this->cart[$key]['stock'] = $product->getStockForOutlet($this->activeOutletId);

                $this->calculateItemSubtotal($this->cart[$key]);
            }
        }
        $this->recalculateDiscount();
    }

    // ================= CART OPERATIONS =================
    public function addToCart(int $productId, string $unitType = 'unit'): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_active) {
            $this->showToast('Produk tidak ditemukan atau tidak aktif.', 'error');
            return;
        }

        $isBox = ($unitType === 'box');
        $piecesPerUnit = $isBox ? (int) ($product->pieces_per_box ?: 1) : 1;
        $cartKey = "{$productId}:{$unitType}";

        $branchStock = $product->getStockForOutlet($this->activeOutletId);

        // Hitung total pcs yang saat ini sudah masuk ke keranjang untuk produk ini
        $currentCartPieces = 0;
        foreach ($this->cart as $item) {
            if ($item['product_id'] === $productId) {
                $currentCartPieces += ($item['qty'] * ($item['pieces_per_unit'] ?? 1));
            }
        }

        if ($currentCartPieces + $piecesPerUnit > $branchStock) {
            $unitName = $isBox ? "1 Dus ({$piecesPerUnit} {$product->unit})" : "1 {$product->unit}";
            $this->showToast("Stok cabang tidak mencukupi untuk menambah {$unitName}! Sisa stok cabang: {$branchStock} {$product->unit}.", 'warning');
            return;
        }

        $op = $product->getOutletProduct($this->activeOutletId);
        $costPerUnit = ($op && $op->cost_price) ? (float)$op->cost_price : (float)$product->cost_price;
        $regularPrice = ($op && $op->selling_price) ? (float)$op->selling_price : (float)$product->selling_price;
        $loyaltyPrice = ($op && $op->loyalty_price) ? (float)$op->loyalty_price : (float)($product->loyalty_price ?? $product->selling_price);

        if ($isBox) {
            $regularPrice = ($op && $op->box_selling_price) ? (float)$op->box_selling_price : (float)($product->box_selling_price ?: ($product->selling_price * $piecesPerUnit));
            $loyaltyPrice = ($op && $op->box_loyalty_price) ? (float)$op->box_loyalty_price : (float)($product->box_loyalty_price ?: ($product->loyalty_price ? $product->loyalty_price * $piecesPerUnit : $regularPrice));
            $costPrice = (float) ($costPerUnit * $piecesPerUnit);
        } else {
            $costPrice = (float) $costPerUnit;
        }

        $priceType = $this->isLoyalty ? 'loyalty' : 'regular';
        $chosenPrice = ($priceType === 'loyalty') ? $loyaltyPrice : $regularPrice;

        if ($chosenPrice <= 0) {
            $unitName = $isBox ? "Dus" : "Eceran ({$product->unit})";
            $this->showToast("Harga {$unitName} untuk '{$product->name}' belum ditentukan (Rp 0). Produk tidak dapat dijual.", 'warning');
            return;
        }

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['qty'] += 1;
            $this->calculateItemSubtotal($this->cart[$cartKey]);
        } else {
            $this->cart[$cartKey] = [
                'key' => $cartKey,
                'product_id' => $product->id,
                'name' => $product->name,
                'barcode' => ($isBox && $product->box_barcode) ? $product->box_barcode : $product->barcode,
                'unit_type' => $unitType,
                'unit_label' => $isBox ? ("Dus (@{$piecesPerUnit} {$product->unit})") : $product->unit,
                'pieces_per_unit' => $piecesPerUnit,
                'regular_price' => $regularPrice,
                'loyalty_price' => $loyaltyPrice,
                'price_type' => $priceType,
                'price' => $chosenPrice,
                'cost_price' => $costPrice,
                'qty' => 1,
                'stock' => $branchStock,
                'unit' => $product->unit,
                'discount_type' => null,
                'discount_rate' => 0,
                'discount_amount' => 0,
                'subtotal' => $chosenPrice,
            ];
            $this->calculateItemSubtotal($this->cart[$cartKey]);
        }

        $this->search = '';
        $this->recalculateDiscount();
        $this->dispatch('focus-search');
    }

    public function setItemPriceType(string $cartKey, string $priceType): void
    {
        if (!isset($this->cart[$cartKey])) {
            return;
        }

        $this->cart[$cartKey]['price_type'] = $priceType;
        if ($priceType === 'loyalty') {
            $this->cart[$cartKey]['price'] = (float) $this->cart[$cartKey]['loyalty_price'];
        } else {
            $this->cart[$cartKey]['price'] = (float) $this->cart[$cartKey]['regular_price'];
        }

        $this->calculateItemSubtotal($this->cart[$cartKey]);
        $this->recalculateDiscount();
        $this->showToast("Harga '{$this->cart[$cartKey]['name']}' diset ke " . ($priceType === 'loyalty' ? 'Sales / Member' : 'Reguler') . ".", 'info');
        $this->dispatch('focus-search');
    }

    public function toggleItemPriceType(string $cartKey): void
    {
        if (!isset($this->cart[$cartKey])) {
            return;
        }

        $current = $this->cart[$cartKey]['price_type'] ?? 'regular';
        $newType = ($current === 'loyalty') ? 'regular' : 'loyalty';
        $this->setItemPriceType($cartKey, $newType);
    }

    // ================= ITEM DISCOUNT MODAL METHODS =================
    public function openItemDiscountModal(string $cartKey): void
    {
        if (!isset($this->cart[$cartKey])) {
            return;
        }

        $item = $this->cart[$cartKey];
        $this->editingCartKey = $cartKey;
        $this->itemDiscountType = $item['discount_type'] ?: 'percent';
        $this->itemDiscountRate = $item['discount_rate'] > 0 ? $item['discount_rate'] : '';
        $this->showItemDiscountModal = true;
    }

    public function closeItemDiscountModal(): void
    {
        $this->showItemDiscountModal = false;
        $this->editingCartKey = null;
        $this->dispatch('focus-search');
    }

    public function applyItemDiscount(): void
    {
        if (!$this->editingCartKey || !isset($this->cart[$this->editingCartKey])) {
            $this->closeItemDiscountModal();
            return;
        }

        $rawRate = is_numeric($this->itemDiscountRate) ? (float)$this->itemDiscountRate : (float)preg_replace('/[^0-9.]/', '', (string)$this->itemDiscountRate);

        if ($rawRate < 0) {
            $rawRate = 0;
        }

        if ($this->itemDiscountType === 'percent' && $rawRate > 100) {
            $rawRate = 100;
        }

        if ($rawRate > 0) {
            $this->cart[$this->editingCartKey]['discount_type'] = $this->itemDiscountType;
            $this->cart[$this->editingCartKey]['discount_rate'] = $rawRate;
        } else {
            $this->cart[$this->editingCartKey]['discount_type'] = null;
            $this->cart[$this->editingCartKey]['discount_rate'] = 0;
            $this->cart[$this->editingCartKey]['discount_amount'] = 0;
        }

        $this->calculateItemSubtotal($this->cart[$this->editingCartKey]);
        $this->recalculateDiscount();
        $this->closeItemDiscountModal();
        $this->showToast("Diskon per item berhasil diterapkan.", 'success');
    }

    public function removeItemDiscount(string $cartKey): void
    {
        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['discount_type'] = null;
            $this->cart[$cartKey]['discount_rate'] = 0;
            $this->cart[$cartKey]['discount_amount'] = 0;
            $this->calculateItemSubtotal($this->cart[$cartKey]);
            $this->recalculateDiscount();
            $this->showToast("Diskon item dihapus.", 'info');
        }
        $this->dispatch('focus-search');
    }

    public function incrementQty(string|int $cartKey): void
    {
        $key = (string) $cartKey;
        // Fallback jika dikirim ID produk numerik saja
        if (!isset($this->cart[$key])) {
            $key = "{$cartKey}:unit";
        }

        if (!isset($this->cart[$key])) {
            return;
        }

        $item = $this->cart[$key];
        $product = Product::find($item['product_id']);

        if (!$product) {
            return;
        }

        $branchStock = $product->getStockForOutlet($this->activeOutletId);

        // Hitung total pcs di keranjang saat ini
        $currentCartPieces = 0;
        foreach ($this->cart as $k => $cItem) {
            if ($cItem['product_id'] === $product->id) {
                $currentCartPieces += ($cItem['qty'] * ($cItem['pieces_per_unit'] ?? 1));
            }
        }

        $piecesToAdd = (int) ($item['pieces_per_unit'] ?? 1);

        if ($currentCartPieces + $piecesToAdd > $branchStock) {
            $this->showToast("Maksimal stok cabang tercapai untuk '{$product->name}' (Tersedia: {$branchStock} {$product->unit}).", 'warning');
            $this->dispatch('focus-search');
            return;
        }

        $this->cart[$key]['qty'] += 1;
        $this->calculateItemSubtotal($this->cart[$key]);
        $this->recalculateDiscount();
        $this->dispatch('focus-search');
    }

    public function decrementQty(string|int $cartKey): void
    {
        $key = (string) $cartKey;
        if (!isset($this->cart[$key])) {
            $key = "{$cartKey}:unit";
        }

        if (!isset($this->cart[$key])) {
            return;
        }

        if ($this->cart[$key]['qty'] > 1) {
            $this->cart[$key]['qty'] -= 1;
            $this->calculateItemSubtotal($this->cart[$key]);
            $this->recalculateDiscount();
        } else {
            $this->removeFromCart($key);
        }
        $this->dispatch('focus-search');
    }

    public function removeFromCart(string|int $cartKey): void
    {
        $key = (string) $cartKey;
        if (!isset($this->cart[$key])) {
            $key = "{$cartKey}:unit";
        }

        if (isset($this->cart[$key])) {
            unset($this->cart[$key]);
        }
        $this->recalculateDiscount();
        $this->dispatch('focus-search');
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->selectedDiscountId = null;
        $this->discountAmount = 0;
        $this->enableTax = false;
        $this->selectedTaxId = null;
        $this->taxAmount = 0;
        $this->showToast('Keranjang belanja berhasil dikosongkan.', 'info');
        $this->dispatch('focus-search');
    }

    // ================= DISCOUNT / PROMO METHODS =================
    public function openDiscountModal(): void
    {
        $this->showDiscountModal = true;
    }

    public function closeDiscountModal(): void
    {
        $this->showDiscountModal = false;
        $this->dispatch('focus-search');
    }

    public function selectDiscount(?int $discountId): void
    {
        $this->selectedDiscountId = $discountId;
        $this->recalculateDiscount();
        $this->showDiscountModal = false;

        if ($discountId) {
            $disc = Discount::find($discountId);
            if ($disc) {
                $this->showToast("Promo '{$disc->name}' berhasil diterapkan.", 'success');
            }
        } else {
            $this->showToast('Diskon promo dinonaktifkan.', 'info');
        }

        $this->dispatch('focus-search');
    }

    public function removeDiscount(): void
    {
        $this->selectedDiscountId = null;
        $this->discountAmount = 0;
        $this->showToast('Diskon promo dihapus.', 'info');
        $this->dispatch('focus-search');
    }

    public function recalculateDiscount(): void
    {
        if (!$this->selectedDiscountId) {
            $this->discountAmount = 0;
            $this->recalculateTax();
            return;
        }

        $discount = Discount::find($this->selectedDiscountId);
        if (!$discount || !$discount->is_active) {
            $this->selectedDiscountId = null;
            $this->discountAmount = 0;
            $this->recalculateTax();
            return;
        }

        $this->discountAmount = $discount->calculateDiscount($this->subtotal);
        $this->recalculateTax();
    }

    public function getSelectedDiscountProperty(): ?Discount
    {
        if (!$this->selectedDiscountId) {
            return null;
        }
        return Discount::find($this->selectedDiscountId);
    }

    // ================= TAX METHODS =================
    public function toggleTax(): void
    {
        $this->enableTax = !$this->enableTax;
        if ($this->enableTax && !$this->selectedTaxId) {
            $defaultTax = Tax::where('is_default', true)->where('is_active', true)->first() ?: Tax::active()->first();
            if ($defaultTax) {
                $this->selectedTaxId = $defaultTax->id;
            }
        }
        $this->recalculateTax();
        $this->dispatch('focus-search');
    }

    public function openTaxModal(): void
    {
        $this->showTaxModal = true;
    }

    public function closeTaxModal(): void
    {
        $this->showTaxModal = false;
        $this->dispatch('focus-search');
    }

    public function selectTax(?int $taxId): void
    {
        $this->selectedTaxId = $taxId;
        $this->enableTax = ($taxId !== null);
        $this->recalculateTax();
        $this->showTaxModal = false;

        if ($taxId) {
            $tax = Tax::find($taxId);
            if ($tax) {
                $this->showToast("Pajak '{$tax->name}' ({$tax->formatted_rate}) diterapkan.", 'success');
            }
        } else {
            $this->showToast('Pajak dinonaktifkan.', 'info');
        }

        $this->dispatch('focus-search');
    }

    public function removeTax(): void
    {
        $this->enableTax = false;
        $this->selectedTaxId = null;
        $this->taxAmount = 0;
        $this->showToast('Pajak dinonaktifkan.', 'info');
        $this->dispatch('focus-search');
    }

    public function recalculateTax(): void
    {
        if (!$this->enableTax || !$this->selectedTaxId) {
            $this->taxAmount = 0;
            $this->syncPaymentStepAfterCartChange();
            return;
        }

        $tax = Tax::find($this->selectedTaxId);
        if (!$tax || !$tax->is_active) {
            $this->enableTax = false;
            $this->selectedTaxId = null;
            $this->taxAmount = 0;
            $this->syncPaymentStepAfterCartChange();
            return;
        }

        $taxable = max(0, $this->subtotal - $this->getNumericDiscountAmount());
        $this->taxAmount = $tax->calculateTax($taxable);
        $this->syncPaymentStepAfterCartChange();
    }

    protected function syncPaymentStepAfterCartChange(): void
    {
        if ($this->currentStep === 'payment') {
            if (empty($this->cart)) {
                $this->setStep('catalog');
                return;
            }
            if ($this->paymentMethod !== 'tempo') {
                $this->paidAmount = (int) $this->grandTotal;
            }
        }
    }

    public function getSelectedTaxProperty(): ?Tax
    {
        if (!$this->selectedTaxId) {
            return null;
        }
        return Tax::find($this->selectedTaxId);
    }

    public function getNumericTaxAmount(): float
    {
        if ($this->enableTax && $this->selectedTaxId) {
            $tax = Tax::find($this->selectedTaxId);
            if ($tax && $tax->is_active) {
                $taxable = max(0, $this->subtotal - $this->getNumericDiscountAmount());
                return (float) $tax->calculateTax($taxable);
            }
        }
        if (is_numeric($this->taxAmount)) {
            return (float) $this->taxAmount;
        }
        $cleaned = preg_replace('/[^0-9.]/', '', (string)$this->taxAmount);
        return (float) ($cleaned ?: 0);
    }

    public function getNumericPaidAmount(): float
    {
        if (is_numeric($this->paidAmount)) {
            return (float) $this->paidAmount;
        }
        $cleaned = preg_replace('/[^0-9]/', '', (string)$this->paidAmount);
        return (float) ($cleaned ?: 0);
    }

    public function getNumericDiscountAmount(): float
    {
        if ($this->selectedDiscountId) {
            $discount = Discount::find($this->selectedDiscountId);
            if ($discount && $discount->is_active) {
                return (float) $discount->calculateDiscount($this->subtotal);
            }
        }
        if (is_numeric($this->discountAmount)) {
            return (float) $this->discountAmount;
        }
        $cleaned = preg_replace('/[^0-9]/', '', (string)$this->discountAmount);
        return (float) ($cleaned ?: 0);
    }

    public function getSubtotalProperty(): float
    {
        return (float) array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getGrandTotalProperty(): float
    {
        $taxable = max(0, $this->subtotal - $this->getNumericDiscountAmount());
        $total = $taxable + $this->getNumericTaxAmount();
        return max(0, $total);
    }

    public function getChangeAmountProperty(): float
    {
        return max(0, $this->getNumericPaidAmount() - $this->grandTotal);
    }

    public function setPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
        if ($method === 'tempo') {
            $this->paidAmount = 0;
            if (empty($this->tempoDueDate)) {
                $this->tempoDueDate = \Carbon\Carbon::now()->addDays(14)->format('Y-m-d');
            }
        } elseif ($method === 'split') {
            $this->splitCashAmount = 0;
            $this->splitNonCashAmount = (int) $this->grandTotal;
            $this->paidAmount = (int) $this->grandTotal;
        } elseif ($method === 'cash') {
            $this->paidAmount = (int) $this->grandTotal;
        } else {
            $this->paidAmount = (int) $this->grandTotal;
        }
    }

    public function updatedSplitCashAmount($value): void
    {
        $cleanCash = is_numeric($value) ? (float)$value : (float)preg_replace('/[^0-9]/', '', (string)$value);
        $remaining = max(0, $this->grandTotal - $cleanCash);
        $this->splitNonCashAmount = (int) $remaining;
        $this->paidAmount = (int) ($cleanCash + $this->splitNonCashAmount);
    }

    public function setSplitCashPreset($amount): void
    {
        $cleanAmount = (float) $amount;
        $this->splitCashAmount = $cleanAmount;
        $remaining = max(0, $this->grandTotal - $cleanAmount);
        $this->splitNonCashAmount = (int) $remaining;
        $this->paidAmount = (int) ($cleanAmount + $this->splitNonCashAmount);
    }

    public function fillSplitNonCashRemainder(): void
    {
        $cleanCash = is_numeric($this->splitCashAmount) ? (float)$this->splitCashAmount : (float)preg_replace('/[^0-9]/', '', (string)$this->splitCashAmount);
        $remaining = max(0, $this->grandTotal - $cleanCash);
        $this->splitNonCashAmount = (int) $remaining;
        $this->paidAmount = (int) ($cleanCash + $this->splitNonCashAmount);
    }

    public function setShiftStartingCashPreset($amount): void
    {
        $this->shiftStartingCash = (float) $amount;
    }

    public function openPaymentModal(): void
    {
        $this->setStep('payment');
    }

    public function closePaymentModal(): void
    {
        $this->setStep('catalog');
    }

    public function setQuickPaid($amount): void
    {
        $this->paidAmount = (float) $amount;
    }

    public function setTempoDueDays(int $days): void
    {
        $this->tempoDueDate = \Carbon\Carbon::now()->addDays($days)->format('Y-m-d');
    }

    public function processPayment(): void
    {
        if (empty($this->cart)) {
            $this->showToast('Keranjang kosong!', 'error');
            return;
        }

        $actualPaid = $this->getNumericPaidAmount();
        $actualDiscount = $this->getNumericDiscountAmount();
        $actualTax = $this->getNumericTaxAmount();

        if ($this->paymentMethod === 'tempo') {
            if (!$this->selectedCustomerId) {
                $this->showToast('Penjualan Tempo (Hutang) WAJIB memilih Pelanggan / Member terdaftar!', 'error');
                return;
            }

            if ($actualPaid > $this->grandTotal) {
                $actualPaid = $this->grandTotal;
            }

            $paymentStatus = ($actualPaid >= $this->grandTotal ? 'paid' : ($actualPaid > 0 ? 'partial' : 'unpaid'));
            $dueDate = $this->tempoDueDate ?: \Carbon\Carbon::now()->addDays(14)->format('Y-m-d');
            $change = 0;
        } elseif ($this->paymentMethod === 'split') {
            $cleanCash = is_numeric($this->splitCashAmount) ? (float)$this->splitCashAmount : (float)preg_replace('/[^0-9]/', '', (string)$this->splitCashAmount);
            $cleanNonCash = is_numeric($this->splitNonCashAmount) ? (float)$this->splitNonCashAmount : (float)preg_replace('/[^0-9]/', '', (string)$this->splitNonCashAmount);
            $totalSplit = $cleanCash + $cleanNonCash;

            if ($totalSplit < $this->grandTotal) {
                $this->showToast('Total pembayaran kombinasi (Tunai + Non-Tunai) kurang dari total tagihan!', 'error');
                return;
            }
            $actualPaid = $totalSplit;
            $paymentStatus = 'paid';
            $dueDate = null;
            $change = max(0, $totalSplit - $this->grandTotal);
        } elseif ($this->paymentMethod === 'cash') {
            if ($actualPaid < $this->grandTotal) {
                $this->showToast('Jumlah uang tunai yang dibayar kurang dari total tagihan!', 'error');
                return;
            }
            $paymentStatus = 'paid';
            $dueDate = null;
            $change = max(0, $actualPaid - $this->grandTotal);
        } else {
            // QRIS & Transfer
            $actualPaid = $this->grandTotal;
            $this->paidAmount = (int) $this->grandTotal;
            $paymentStatus = 'paid';
            $dueDate = null;
            $change = 0;
        }

        try {
            DB::transaction(function () use ($actualPaid, $actualDiscount, $actualTax, $paymentStatus, $dueDate, $change) {
                // 1. Validasi ulang stok produk (hitung akumulasi total pieces yang dibutuhkan)
                $neededPiecesPerProduct = [];
                foreach ($this->cart as $item) {
                    $pId = (int) $item['product_id'];
                    $needed = ((int)$item['qty']) * ((int)($item['pieces_per_unit'] ?? 1));
                    $neededPiecesPerProduct[$pId] = ($neededPiecesPerProduct[$pId] ?? 0) + $needed;
                }

                foreach ($neededPiecesPerProduct as $pId => $totalNeeded) {
                    $prod = Product::lockForUpdate()->find($pId);
                    $currBranchStock = $prod ? $prod->getStockForOutlet($this->activeOutletId) : 0;
                    if (!$prod || $currBranchStock < $totalNeeded) {
                        $prodName = $prod ? $prod->name : "ID #{$pId}";
                        $unit = $prod ? $prod->unit : 'pcs';
                        throw new \Exception("Stok di cabang untuk '{$prodName}' tidak mencukupi saat proses checkout (Dibutuhkan: {$totalNeeded} {$unit}, Sisa stok cabang: {$currBranchStock} {$unit}).");
                    }
                }

                // 2. Buat Transaksi
                $selectedTaxObj = ($this->enableTax && $this->selectedTaxId) ? Tax::find($this->selectedTaxId) : null;
                $taxRate = ($selectedTaxObj && $actualTax > 0) ? (float)$selectedTaxObj->rate : 0.00;
                $taxId = ($selectedTaxObj && $actualTax > 0) ? $selectedTaxObj->id : null;

                $cleanCash = ($this->paymentMethod === 'split') ? (is_numeric($this->splitCashAmount) ? (float)$this->splitCashAmount : (float)preg_replace('/[^0-9]/', '', (string)$this->splitCashAmount)) : 0;
                $cleanNonCash = ($this->paymentMethod === 'split') ? (is_numeric($this->splitNonCashAmount) ? (float)$this->splitNonCashAmount : (float)preg_replace('/[^0-9]/', '', (string)$this->splitNonCashAmount)) : 0;

                $transaction = Transaction::create([
                    'invoice_number' => Transaction::generateInvoiceNumber(),
                    'outlet_id' => $this->activeOutletId,
                    'user_id' => Auth::id(),
                    'cashier_shift_id' => $this->activeShiftId,
                    'customer_id' => $this->selectedCustomerId,
                    'customer_name' => $this->customerName ?: 'Pelanggan Umum',
                    'discount_id' => $this->selectedDiscountId,
                    'tax_id' => $taxId,
                    'tax_rate' => $taxRate,
                    'is_loyalty' => $this->isLoyalty,
                    'subtotal' => $this->subtotal,
                    'discount_amount' => $actualDiscount,
                    'tax_amount' => $actualTax,
                    'grand_total' => $this->grandTotal,
                    'paid_amount' => $actualPaid,
                    'change_amount' => $change,
                    'payment_method' => $this->paymentMethod,
                    'split_cash_amount' => $cleanCash,
                    'split_non_cash_amount' => $cleanNonCash,
                    'split_non_cash_method' => ($this->paymentMethod === 'split') ? $this->splitNonCashMethod : null,
                    'payment_status' => $paymentStatus,
                    'due_date' => $dueDate,
                    'status' => 'completed',
                    'notes' => $this->paymentNotes,
                ]);

                if ($this->activeShiftId) {
                    $activeShiftObj = CashierShift::find($this->activeShiftId);
                    if ($activeShiftObj) {
                        $activeShiftObj->recalculateTotals();
                    }
                }

                // 3. Simpan Detail Item & Kurangi Stok Cabang & Catat Mutasi
                $activeOutlet = Outlet::find($this->activeOutletId);

                foreach ($this->cart as $item) {
                    $unitType = $item['unit_type'] ?? 'unit';
                    $piecesPerUnit = (int) ($item['pieces_per_unit'] ?? 1);
                    $totalDeduction = ((int) $item['qty']) * $piecesPerUnit;

                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['name'],
                        'unit_type' => $unitType,
                        'pieces_per_unit' => $piecesPerUnit,
                        'cost_price' => $item['cost_price'],
                        'unit_price' => $item['price'],
                        'price_type' => $item['price_type'] ?? 'regular',
                        'discount_type' => $item['discount_type'] ?? null,
                        'discount_rate' => $item['discount_rate'] ?? 0,
                        'discount_amount' => $item['discount_amount'] ?? 0,
                        'quantity' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $prod = Product::find($item['product_id']);
                    
                    // Deduct from branch stock
                    $op = OutletProduct::where('outlet_id', $this->activeOutletId)->where('product_id', $prod->id)->first();
                    $stockBefore = $op ? $op->stock : $prod->stock;
                    $stockAfter = $stockBefore - $totalDeduction;

                    if ($op) {
                        $op->update(['stock' => $stockAfter]);
                    } else {
                        OutletProduct::create([
                            'outlet_id' => $this->activeOutletId,
                            'product_id' => $prod->id,
                            'selling_price' => $prod->selling_price,
                            'cost_price' => $prod->cost_price,
                            'stock' => $stockAfter,
                            'min_stock_alert' => $prod->min_stock_alert,
                        ]);
                    }

                    if ($activeOutlet && $activeOutlet->is_main) {
                        $prod->update(['stock' => $stockAfter]);
                    }

                    $unitDesc = ($unitType === 'box') 
                        ? " ({$item['qty']} Dus @ {$piecesPerUnit} {$prod->unit} = {$totalDeduction} {$prod->unit})" 
                        : " ({$item['qty']} {$prod->unit})";

                    $branchLabel = $activeOutlet ? " [Cabang: {$activeOutlet->name}]" : '';

                    StockMutation::create([
                        'outlet_id' => $this->activeOutletId,
                        'product_id' => $prod->id,
                        'user_id' => Auth::id(),
                        'type' => 'out',
                        'quantity' => -$totalDeduction,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reference' => $transaction->invoice_number,
                        'description' => "Penjualan POS Invoice {$transaction->invoice_number}{$unitDesc}" . ($this->isLoyalty ? ' (Member)' : '') . ($this->paymentMethod === 'tempo' ? ' [Tempo]' : '') . $branchLabel,
                    ]);
                }

                $this->lastTransactionId = $transaction->id;
            });

            // Reset cart & modal state
            $this->cart = [];
            $this->selectedDiscountId = null;
            $this->discountAmount = 0;
            $this->taxAmount = 0;
            $this->selectedCustomerId = null;
            $this->customerName = 'Pelanggan Umum';
            $this->isLoyalty = false;
            $this->paymentNotes = '';
            $this->tempoDueDate = '';
            $this->tempoNotes = '';
            $this->mobileCartOpen = false;

            // Transition to Receipt Step
            $this->setStep('receipt');
            $this->whatsappPhone = $this->selectedCustomerId ? (Customer::find($this->selectedCustomerId)?->phone ?? '') : '';
            $this->showToast('Transaksi berhasil disimpan!', 'success');

        } catch (\Exception $e) {
            $this->showToast('Gagal memproses transaksi: ' . $e->getMessage(), 'error');
        }
    }

    public function closeReceiptModal(): void
    {
        $this->startNewTransaction();
    }

    // ================= CASHIER SHIFT METHODS =================
    public function checkActiveShift(): void
    {
        $shift = CashierShift::getActiveShift(Auth::id(), $this->activeOutletId);
        if ($shift) {
            $this->activeShiftId = $shift->id;
            $shift->recalculateTotals();
        } else {
            $this->activeShiftId = null;
        }
    }

    public function openOpenShiftModal(): void
    {
        $this->shiftStartingCash = 200000;
        $this->shiftOpeningNotes = '';
        $this->showOpenShiftModal = true;
    }

    public function closeOpenShiftModal(): void
    {
        $this->showOpenShiftModal = false;
        $this->dispatch('focus-search');
    }

    public function startShift(): void
    {
        $rawStartingCash = is_numeric($this->shiftStartingCash) ? (float)$this->shiftStartingCash : (float)preg_replace('/[^0-9]/', '', (string)$this->shiftStartingCash);
        
        $shift = CashierShift::create([
            'user_id' => Auth::id(),
            'outlet_id' => $this->activeOutletId,
            'opened_at' => Carbon::now(),
            'starting_cash' => $rawStartingCash,
            'expected_ending_cash' => $rawStartingCash,
            'status' => 'open',
            'opening_notes' => $this->shiftOpeningNotes,
        ]);

        $this->activeShiftId = $shift->id;
        $this->showOpenShiftModal = false;
        $this->showToast("Shift kasir berhasil dibuka dengan kas awal Rp " . number_format($rawStartingCash, 0, ',', '.') . "!", 'success');
        $this->dispatch('focus-search');
    }

    public function openCloseShiftModal(): void
    {
        if (!$this->activeShiftId) {
            $this->showToast('Tidak ada sesi shift yang sedang aktif.', 'warning');
            return;
        }

        $shift = CashierShift::find($this->activeShiftId);
        if ($shift) {
            $shift->recalculateTotals();
            $this->shiftActualEndingCash = $shift->expected_ending_cash;
            $this->shiftClosingNotes = '';
            $this->showCloseShiftModal = true;
        }
    }

    public function closeCloseShiftModal(): void
    {
        $this->showCloseShiftModal = false;
        $this->dispatch('focus-search');
    }

    public function submitCloseShift(): void
    {
        if (!$this->activeShiftId) {
            return;
        }

        $shift = CashierShift::find($this->activeShiftId);
        if (!$shift) {
            return;
        }

        $shift->recalculateTotals();
        $rawActualEndingCash = is_numeric($this->shiftActualEndingCash) ? (float)$this->shiftActualEndingCash : (float)preg_replace('/[^0-9]/', '', (string)$this->shiftActualEndingCash);
        
        $diff = $rawActualEndingCash - (float)$shift->expected_ending_cash;

        $shift->update([
            'closed_at' => Carbon::now(),
            'actual_ending_cash' => $rawActualEndingCash,
            'cash_difference' => $diff,
            'status' => 'closed',
            'closing_notes' => $this->shiftClosingNotes,
        ]);

        $this->lastClosedShiftId = $shift->id;
        $this->activeShiftId = null;
        $this->showCloseShiftModal = false;
        $this->showShiftReceiptModal = true;
        $this->showToast("Sesi shift berhasil ditutup!", 'success');
    }

    public function closeShiftReceiptModal(): void
    {
        $this->showShiftReceiptModal = false;
        $this->lastClosedShiftId = null;
        $this->dispatch('focus-search');
    }

    public function getActiveShiftProperty(): ?CashierShift
    {
        if (!$this->activeShiftId) {
            return null;
        }
        return CashierShift::with(['user', 'outlet'])->find($this->activeShiftId);
    }

    // ================= HOLD / SUSPEND TRANSACTION METHODS =================
    public function openHoldModal(): void
    {
        if (empty($this->cart)) {
            $this->showToast('Keranjang belanja masih kosong! Tidak ada transaksi untuk ditahan.', 'warning');
            return;
        }
        $this->holdCustomerNote = ($this->customerName !== 'Pelanggan Umum') ? $this->customerName : '';
        $this->showHoldModal = true;
    }

    public function closeHoldModal(): void
    {
        $this->showHoldModal = false;
        $this->dispatch('focus-search');
    }

    public function confirmHoldCart(): void
    {
        if (empty($this->cart)) {
            $this->showToast('Keranjang belanja kosong!', 'error');
            return;
        }

        $holdName = trim($this->holdCustomerNote) ?: ($this->customerName ?: 'Antrean #' . (HoldTransaction::where('outlet_id', $this->activeOutletId)->count() + 1));

        HoldTransaction::create([
            'hold_code' => HoldTransaction::generateHoldCode(),
            'user_id' => Auth::id(),
            'outlet_id' => $this->activeOutletId,
            'customer_id' => $this->selectedCustomerId,
            'customer_name' => $holdName,
            'cart_data' => $this->cart,
            'discount_id' => $this->selectedDiscountId,
            'tax_id' => $this->selectedTaxId,
            'enable_tax' => $this->enableTax,
            'is_loyalty' => $this->isLoyalty,
            'notes' => $this->paymentNotes,
            'subtotal' => $this->subtotal,
            'grand_total' => $this->grandTotal,
        ]);

        // Reset keranjang untuk melayani pelanggan berikutnya
        $this->cart = [];
        $this->selectedDiscountId = null;
        $this->discountAmount = 0;
        $this->enableTax = false;
        $this->selectedTaxId = null;
        $this->taxAmount = 0;
        $this->selectedCustomerId = null;
        $this->customerName = 'Pelanggan Umum';
        $this->isLoyalty = false;
        $this->paymentNotes = '';
        $this->showHoldModal = false;
        $this->mobileCartOpen = false;

        $this->showToast("Transaksi '{$holdName}' berhasil ditahan! Kasir siap melayani antrean berikutnya.", 'success');
        $this->dispatch('focus-search');
    }

    public function openHoldListModal(): void
    {
        $this->showHoldListModal = true;
    }

    public function closeHoldListModal(): void
    {
        $this->showHoldListModal = false;
        $this->dispatch('focus-search');
    }

    public function resumeHoldTransaction(int $holdId): void
    {
        $hold = HoldTransaction::find($holdId);
        if (!$hold) {
            $this->showToast('Data antrean tertahan tidak ditemukan.', 'error');
            return;
        }

        if (!empty($this->cart)) {
            $this->showToast('Keranjang saat ini berisi item. Silakan kosongkan atau tahan keranjang aktif terlebih dahulu.', 'warning');
            return;
        }

        $this->cart = $hold->cart_data ?: [];
        $this->selectedCustomerId = $hold->customer_id;
        $this->customerName = $hold->customer_name ?: 'Pelanggan Umum';
        $this->selectedDiscountId = $hold->discount_id;
        $this->enableTax = (bool) $hold->enable_tax;
        $this->selectedTaxId = $hold->tax_id;
        $this->isLoyalty = (bool) $hold->is_loyalty;
        $this->paymentNotes = $hold->notes ?: '';

        $this->recalculateCartPrices();
        $this->recalculateDiscount();
        $this->recalculateTax();

        $hold->delete();
        $this->showHoldListModal = false;
        $this->showToast("Antrean '{$this->customerName}' berhasil dimuat kembali ke kasir!", 'success');
        $this->dispatch('focus-search');
    }

    public function deleteHoldTransaction(int $holdId): void
    {
        $hold = HoldTransaction::find($holdId);
        if ($hold) {
            $name = $hold->customer_name;
            $hold->delete();
            $this->showToast("Antrean '{$name}' dibatalkan/dihapus.", 'info');
        }
    }

    public function getHoldCountProperty(): int
    {
        return HoldTransaction::where('outlet_id', $this->activeOutletId)->count();
    }

    private function showToast(string $message, string $type = 'info'): void
    {
        $this->posAlert = $message;
        $this->posAlertType = $type;
    }

    public function render()
    {
        $categories = Category::withCount('products')->get();

        $productsQuery = Product::query()
            ->active()
            ->with(['category', 'outletProducts']);

        if ($this->selectedCategoryId) {
            $productsQuery->where('category_id', $this->selectedCategoryId);
        }

        if (trim($this->search) !== '') {
            $productsQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%')
                  ->orWhere('box_barcode', 'like', '%' . $this->search . '%');
            });
        }

        $products = $productsQuery->orderBy('name')->get();

        // Hanya query jika modal pelanggan sedang terbuka (hemat waktu render tiap klik)
        $customers = $this->showCustomerModal
            ? Customer::query()
                ->where('is_active', true)
                ->when($this->customerSearch, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('name', 'like', '%' . $this->customerSearch . '%')
                            ->orWhere('phone', 'like', '%' . $this->customerSearch . '%')
                            ->orWhere('member_code', 'like', '%' . $this->customerSearch . '%');
                    });
                })
                ->latest()
                ->take(15)
                ->get()
            : collect();

        $lastTransaction = null;
        if ($this->lastTransactionId && $this->currentStep === 'receipt') {
            $lastTransaction = Transaction::with(['items', 'user', 'customer', 'discount', 'tax', 'outlet'])->find($this->lastTransactionId);
        }

        $lastClosedShift = null;
        if ($this->lastClosedShiftId && $this->showShiftReceiptModal) {
            $lastClosedShift = CashierShift::with(['user', 'outlet', 'transactions'])->find($this->lastClosedShiftId);
        }

        $holdTransactions = $this->showHoldListModal
            ? HoldTransaction::where('outlet_id', $this->activeOutletId)->latest()->get()
            : collect();

        $activeDiscounts = Discount::active()->orderBy('type', 'desc')->orderBy('value', 'desc')->get();
        $activeTaxes = Tax::active()->orderByDesc('is_default')->get();
        $receiptSetting = ReceiptSetting::getSettings();
        $activeOutlet = Outlet::find($this->activeOutletId);
        $allOutlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        return view('livewire.pos.pos-terminal', [
            'categories' => $categories,
            'products' => $products,
            'customers' => $customers,
            'activeDiscounts' => $activeDiscounts,
            'activeTaxes' => $activeTaxes,
            'lastTransaction' => $lastTransaction,
            'lastClosedShift' => $lastClosedShift,
            'holdTransactions' => $holdTransactions,
            'receiptSetting' => $receiptSetting,
            'activeOutlet' => $activeOutlet,
            'allOutlets' => $allOutlets,
        ]);
    }
}
