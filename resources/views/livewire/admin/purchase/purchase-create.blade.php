<div class="space-y-4">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Input Faktur Masuk (Restock Barang)</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Input penerimaan stok dari supplier, update stok otomatis, dan perbarui harga modal (HPP).</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button 
                type="button"
                wire:click="$set('showRestockModal', true)"
                class="btn-glass-action"
            >
                <span>Tarik dari Rencana Kulakan</span>
                @if(isset($availableRestockPlans) && $availableRestockPlans->count() > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-[#163144] text-white font-black ml-1">
                        {{ $availableRestockPlans->count() }}
                    </span>
                @endif
            </button>

            <a 
                href="{{ route('admin.purchases.index') }}" 
                wire:navigate 
                class="btn-glass-action"
            >
                <span>Batal</span>
            </a>
            <button 
                type="button" 
                wire:click="savePurchase"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Simpan & Tambah Stok</span>
            </button>
        </div>
    </div>

    <!-- Active Restock Plan Alert Banner -->
    @if($restock_plan_id)
        <div class="glass-card-plek p-3.5 sm:p-4 rounded-[22px] border border-[#a0bddb]/50 text-[#163144] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-[#163144]">Faktur ini terhubung dengan Rencana Kulakan:</span>
                        <span class="text-xs font-mono font-black px-2.5 py-0.5 rounded-full bg-[#163144] text-white">{{ $restock_plan_number }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Saat faktur disimpan, status rencana kulakan akan otomatis berubah menjadi 'Selesai Masuk Faktur' (Received).</p>
                </div>
            </div>

            <button 
                type="button" 
                wire:click="detachRestockPlan" 
                class="btn-glass-action text-rose-600 hover:text-white shrink-0 self-end sm:self-auto"
            >
                Lepas Tautan
            </button>
        </div>
    @endif

    <form wire:submit.prevent="savePurchase" class="space-y-4">

        <!-- CARD 1: INFORMASI FAKTUR & SUPPLIER -->
        <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 p-5 shadow-xs space-y-4">
            <div class="border-b border-white/60 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-[#163144] text-white flex items-center justify-center text-[10px] font-black">1</span>
                        <span>Informasi Faktur & Pemasok</span>
                    </h3>
                </div>
                <span class="text-[10px] font-mono font-bold text-[#163144] bg-[#a0bddb]/30 px-3 py-1 rounded-full border border-[#a0bddb]/40">
                    {{ $invoice_number }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Cabang / Gudang Penerima -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#163144] mb-1">
                        <span>Cabang Penerima *</span>
                    </label>
                    <select 
                        wire:model.live="outlet_id" 
                        class="w-full px-3.5 py-2.5 bg-white/80 border border-white rounded-xl text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs cursor-pointer transition"
                    >
                        @foreach($outlets as $ot)
                            <option value="{{ $ot->id }}">{{ $ot->name }}</option>
                        @endforeach
                    </select>
                    @error('outlet_id') <span class="text-rose-500 text-[10px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <!-- Supplier Pemasok (Searchable) -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#163144] mb-1">Supplier *</label>
                    <x-searchable-select 
                        wire:model.live="supplier_id" 
                        :options="$suppliers" 
                        placeholder="Pilih Supplier Pemasok..." 
                        :threshold="0"
                    />
                    @error('supplier_id') <span class="text-rose-500 text-[10px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                <!-- No Faktur Asli Supplier -->
                <div>
                    <label class="block text-xs font-bold text-[#163144] mb-1">No. Faktur</label>
                    <input 
                        wire:model="supplier_invoice_number" 
                        type="text" 
                        placeholder="Contoh: INV/2026/089" 
                        class="w-full px-3.5 py-2.5 bg-white/80 border border-white rounded-xl text-xs font-mono font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs transition"
                    >
                    @error('supplier_invoice_number') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Pembelian / Masuk -->
                <div>
                    <label class="block text-xs font-bold text-[#163144] mb-1">Tanggal Masuk *</label>
                    <input 
                        wire:model="purchase_date" 
                        type="date" 
                        class="w-full px-3.5 py-2.5 bg-white/80 border border-white rounded-xl text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs transition"
                    >
                    @error('purchase_date') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-bold text-[#163144] mb-1">Metode Pembayaran *</label>
                    <select 
                        wire:model.live="payment_type" 
                        class="w-full px-3.5 py-2.5 bg-white/80 border border-white rounded-xl text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs transition"
                    >
                        <option value="cash">Tunai (Cash Langsung)</option>
                        <option value="transfer">Transfer Bank Langsung</option>
                        <option value="tempo">Tempo / Hutang Dagang</option>
                    </select>
                </div>

                <!-- Tanggal Jatuh Tempo (If Tempo) -->
                @if($payment_type === 'tempo')
                    <div>
                        <label class="block text-xs font-bold text-amber-900 mb-1">Tanggal Jatuh Tempo *</label>
                        <input 
                            wire:model="due_date" 
                            type="date" 
                            class="w-full px-3.5 py-2.5 bg-amber-50/80 border border-amber-300 rounded-xl text-xs font-bold text-amber-900 outline-none focus:ring-2 focus:ring-amber-400 shadow-2xs transition"
                        >
                        @error('due_date') <span class="text-rose-500 text-[10px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Nominal Terbayar -->
                <div>
                    <label class="block text-xs font-bold text-[#163144] mb-1">
                        {{ $payment_type === 'tempo' ? 'Uang Muka (DP) / Bayar Sekarang' : 'Nominal Terbayar (Rp)' }}
                    </label>
                    <input 
                        wire:model="paid_amount" 
                        type="number" 
                        min="0" 
                        step="1000"
                        placeholder="{{ $payment_type === 'tempo' ? '0 jika belum bayar' : 'Total Belanja' }}" 
                        class="w-full px-3.5 py-2.5 bg-white/80 border border-white rounded-xl text-xs font-mono font-black text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs transition"
                    >
                    @error('paid_amount') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Catatan Tambahan -->
                <div class="{{ $payment_type === 'tempo' ? 'sm:col-span-2' : 'sm:col-span-2' }}">
                    <label class="block text-xs font-bold text-[#163144] mb-1">Catatan Pengadaan</label>
                    <input 
                        wire:model="notes" 
                        type="text" 
                        placeholder="Keterangan pengiriman, armada kendaraan, atau kondisi barang beku..." 
                        class="w-full px-3.5 py-2.5 bg-white/80 border border-white rounded-xl text-xs text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs transition"
                    >
                </div>

            </div>
        </div>

        <!-- CARD 2: FORM TAMBAH BARANG KE KERANJANG -->
        <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 p-5 shadow-xs space-y-4">
            <div class="border-b border-white/60 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-[#163144] text-white flex items-center justify-center text-[10px] font-black">2</span>
                        <span>Tambah Barang Masuk ke Keranjang</span>
                    </h3>
                </div>

                @if($selectedProductModel)
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#a0bddb]/20 border border-[#a0bddb]/40 rounded-full text-[11px] text-[#163144] font-bold self-start sm:self-auto">
                        <span>Stok di Cabang: <strong class="font-mono">{{ $this->selectedProductStock }}</strong> {{ $selectedProductModel->unit }}</span>
                        <span class="text-slate-300">•</span>
                        <span>HPP: <strong class="font-mono">Rp {{ number_format($selectedProductModel->cost_price, 0, ',', '.') }}</strong></span>
                    </div>
                @endif
            </div>

            <!-- Mode Switch: Satuan Eceran vs Beli Per Dus/Kardus -->
            <div class="flex items-center gap-2.5 flex-wrap pb-1">
                <span class="text-[11px] font-black text-[#163144]">Satuan Pembelian:</span>
                <div class="inline-flex p-1 rounded-full glass-pill-plek text-xs font-bold">
                    <button 
                        type="button" 
                        wire:click="$set('purchaseMode', 'unit')" 
                        class="px-3.5 py-1 rounded-full transition cursor-pointer flex items-center gap-1.5 {{ $purchaseMode === 'unit' ? 'bg-[#163144] text-white shadow-xs font-black' : 'text-slate-600 hover:text-[#163144]' }}"
                    >
                        <span>Satuan Eceran</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="$set('purchaseMode', 'box')" 
                        class="px-3.5 py-1 rounded-full transition cursor-pointer flex items-center gap-1.5 {{ $purchaseMode === 'box' ? 'bg-[#163144] text-white shadow-xs font-black' : 'text-slate-600 hover:text-[#163144]' }}"
                    >
                        <span>Beli Per Kardus</span>
                    </button>
                </div>
            </div>

            @if($purchaseMode === 'unit')
                <!-- Add Item Form Grid (Mode Eceran) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3.5 items-end">
                    
                    <!-- 1. Searchable Product Selector (4 Cols) -->
                    <div class="sm:col-span-2 md:col-span-4">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Pilih Produk Frozen Food *</label>
                        <x-searchable-select 
                            wire:model.live="selectedProductId" 
                            :options="$allProducts" 
                            placeholder="-- Cari dan Pilih Produk Frozen Food --" 
                            :threshold="0"
                            key="product_picker_input_unit"
                        />
                        @error('selectedProductId') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Jumlah (Qty) (2 Cols) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Jumlah (Qty) *</label>
                        <div class="flex items-center h-[38px] rounded-xl overflow-hidden border border-white bg-white/80">
                            <button 
                                type="button" 
                                wire:click="$set('inputQuantity', {{ max(1, $inputQuantity - 1) }})"
                                class="h-full px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-r border-slate-200 cursor-pointer flex items-center justify-center transition"
                            >-</button>
                            <input 
                                wire:model.live="inputQuantity" 
                                type="number" 
                                min="1" 
                                class="w-full h-full text-center px-2 bg-transparent text-xs font-mono font-black text-[#163144] outline-none"
                            >
                            <button 
                                type="button" 
                                wire:click="$set('inputQuantity', {{ $inputQuantity + 1 }})"
                                class="h-full px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-l border-slate-200 cursor-pointer flex items-center justify-center transition"
                            >+</button>
                        </div>
                        @error('inputQuantity') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 3. Harga Beli Satuan (Rp) (2 Cols) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Harga Beli Satuan *</label>
                        <div class="relative h-[38px]">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input 
                                wire:model.live="inputUnitCost" 
                                type="number" 
                                min="0" 
                                step="500" 
                                placeholder="0" 
                                class="w-full h-full pl-9 pr-2.5 bg-white/80 border border-white rounded-xl text-xs font-mono font-black text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs"
                            >
                        </div>
                        @error('inputUnitCost') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 4. Update HPP Master Checklist (2 Cols) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Update HPP Master</label>
                        <label 
                            for="input_sync_hpp_unit" 
                            class="h-[38px] flex items-center justify-between px-2.5 rounded-xl border cursor-pointer select-none transition-all shadow-2xs {{ $inputUpdateCostPrice ? 'border-[#163144] bg-[#a0bddb]/20 text-[#163144] ring-1 ring-[#163144]/20' : 'border-white bg-white/80 text-slate-500 hover:bg-white' }}"
                            title="Perbarui harga modal master produk secara otomatis"
                        >
                            <div class="flex items-center gap-1.5 min-w-0">
                                <div class="w-4 h-4 rounded-md flex items-center justify-center transition-all shrink-0 {{ $inputUpdateCostPrice ? 'bg-[#163144] text-white' : 'bg-white border border-slate-300' }}">
                                    @if($inputUpdateCostPrice)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-xs font-black truncate text-[#163144]">Update HPP</span>
                            </div>
                            
                            <input 
                                wire:model.live="inputUpdateCostPrice" 
                                type="checkbox" 
                                id="input_sync_hpp_unit" 
                                class="sr-only"
                            >
                            
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full shrink-0 {{ $inputUpdateCostPrice ? 'bg-[#163144] text-white' : 'bg-slate-200 text-slate-500' }}">
                                {{ $inputUpdateCostPrice ? 'Ya' : 'Tidak' }}
                            </span>
                        </label>
                    </div>

                    <!-- 5. Tombol Tambah ke Keranjang (2 Cols) -->
                    <div class="md:col-span-2">
                        <button 
                            type="button" 
                            wire:click="addToCart" 
                            class="w-full h-[38px] px-3 rounded-full bg-[#163144] hover:bg-[#1f425c] active:bg-[#163144] text-white font-black text-xs shadow-sm shadow-[#163144]/20 transition cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            <span>Masukkan</span>
                        </button>
                    </div>

                </div>

                <!-- Subtotal Live Indicator for Input Eceran -->
                @if($selectedProductId && $inputQuantity > 0 && $inputUnitCost > 0)
                    <div class="p-3 bg-white/60 border border-white/80 rounded-2xl flex items-center justify-between text-xs font-bold text-[#163144]">
                        <span class="text-slate-500">Estimasi Subtotal Barang Masuk:</span>
                        <span class="font-mono font-black text-[#163144] text-sm">
                            {{ $inputQuantity }} {{ $selectedProductModel ? $selectedProductModel->unit : 'unit' }} × Rp {{ number_format($inputUnitCost, 0, ',', '.') }} = Rp {{ number_format($inputQuantity * $inputUnitCost, 0, ',', '.') }}
                        </span>
                    </div>
                @endif
            @else
                <!-- Add Item Form Grid (Mode Kardus / Dus / Grosir) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3.5 items-end">
                    
                    <!-- 1. Searchable Product Selector (3 Cols) -->
                    <div class="sm:col-span-2 md:col-span-3">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Pilih Produk Frozen Food *</label>
                        <x-searchable-select 
                            wire:model.live="selectedProductId" 
                            :options="$allProducts" 
                            placeholder="-- Pilih Produk --" 
                            :threshold="0"
                            key="product_picker_input_box"
                        />
                        @error('selectedProductId') <span class="text-rose-500 text-[10px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Jumlah Dus (2 Cols) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Jumlah Dus / Kardus *</label>
                        <div class="flex items-center h-[38px] rounded-xl overflow-hidden border border-white bg-white/80">
                            <button 
                                type="button" 
                                wire:click="$set('boxCount', {{ max(1, $boxCount - 1) }})"
                                class="h-full px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-r border-slate-200 cursor-pointer flex items-center justify-center transition"
                            >-</button>
                            <input 
                                wire:model.live="boxCount" 
                                type="number" 
                                min="1" 
                                class="w-full h-full text-center px-1 bg-transparent text-xs font-mono font-black text-[#163144] outline-none"
                            >
                            <button 
                                type="button" 
                                wire:click="$set('boxCount', {{ $boxCount + 1 }})"
                                class="h-full px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-l border-slate-200 cursor-pointer flex items-center justify-center transition"
                            >+</button>
                        </div>
                    </div>

                    <!-- 3. Isi per Dus (Pack/Pcs) (2 Cols) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#163144] mb-1">
                            Isi per Dus ({{ $selectedProductModel ? $selectedProductModel->unit : 'Pack' }}) *
                        </label>
                        <input 
                            wire:model.live="boxPieces" 
                            type="number" 
                            min="1" 
                            placeholder="Contoh: 24" 
                            class="w-full h-[38px] px-3 bg-white/80 border border-white rounded-xl text-xs font-mono font-black text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs"
                        >
                    </div>

                    <!-- 4. Harga 1 Dus (Rp) (2.5 Cols) -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Harga 1 Dus (Rp) *</label>
                        <div class="relative h-[38px]">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input 
                                wire:model.live="boxPrice" 
                                type="number" 
                                min="0" 
                                step="1000" 
                                placeholder="0" 
                                class="w-full h-full pl-9 pr-2.5 bg-white/80 border border-white rounded-xl text-xs font-mono font-black text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] shadow-2xs"
                            >
                        </div>
                    </div>

                    <!-- 5. Update HPP Master (1.5 Cols) -->
                    <div class="md:col-span-1.5 sm:col-span-1">
                        <label class="block text-xs font-bold text-[#163144] mb-1">Update HPP</label>
                        <label 
                            for="input_sync_hpp_box" 
                            class="h-[38px] flex items-center justify-center px-2 rounded-xl border cursor-pointer select-none transition-all shadow-2xs {{ $inputUpdateCostPrice ? 'border-[#163144] bg-[#a0bddb]/20 text-[#163144]' : 'border-white bg-white/80 text-slate-500 hover:bg-white' }}"
                            title="Perbarui harga modal master produk secara otomatis"
                        >
                            <input 
                                wire:model.live="inputUpdateCostPrice" 
                                type="checkbox" 
                                id="input_sync_hpp_box" 
                                class="sr-only"
                            >
                            <span class="text-xs font-black {{ $inputUpdateCostPrice ? 'text-[#163144]' : 'text-slate-500' }}">
                                {{ $inputUpdateCostPrice ? 'Ya' : 'Tidak' }}
                            </span>
                        </label>
                    </div>

                    <!-- 6. Tombol Masukkan (1.5 Cols) -->
                    <div class="md:col-span-1.5 sm:col-span-1">
                        <button 
                            type="button" 
                            wire:click="addToCart" 
                            class="w-full h-[38px] px-3 rounded-full bg-[#163144] hover:bg-[#1f425c] active:bg-[#163144] text-white font-black text-xs shadow-sm shadow-[#163144]/20 transition cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            <span>Masukkan</span>
                        </button>
                    </div>

                </div>

                <!-- Box Calculation Live Breakdown Banner -->
                @if($selectedProductId && $boxCount > 0 && $boxPieces > 0)
                    <div class="p-3.5 bg-white/60 border border-white/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full bg-[#163144] text-white font-mono font-black text-[10px] uppercase">Hasil Konversi Dus</span>
                            <span class="text-slate-700">
                                Stok Masuk: <strong class="font-mono text-[#163144] font-black text-sm">{{ $boxCount }} Dus × {{ $boxPieces }} {{ $selectedProductModel ? $selectedProductModel->unit : 'pack' }} = {{ $inputQuantity }} {{ $selectedProductModel ? $selectedProductModel->unit : 'pack' }}</strong>
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-700">
                                Modal (HPP): <strong class="font-mono text-emerald-700 font-bold">Rp {{ number_format($inputUnitCost, 0, ',', '.') }} / {{ $selectedProductModel ? $selectedProductModel->unit : 'pack' }}</strong>
                            </span>
                        </div>
                        <div class="font-mono font-black text-[#163144] text-sm">
                            Subtotal Faktur: <span class="text-[#163144]">Rp {{ number_format((float)$boxPrice * (int)$boxCount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            @endif

        </div>

        <!-- CARD 3: TABEL KERANJANG BARANG MASUK (RESTOCK CART) -->
        <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 p-5 shadow-xs space-y-4">
            <div class="border-b border-white/60 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-[#163144] text-white flex items-center justify-center text-[10px] font-black">3</span>
                        <span>Keranjang Barang Masuk (Restock Cart)</span>
                    </h3>
                </div>

                <span class="text-xs font-black text-[#163144] bg-[#a0bddb]/30 px-3 py-1 rounded-full font-mono">
                    {{ count($items) }} Jenis Produk
                </span>
            </div>

            @error('items') <p class="text-rose-500 text-xs font-bold">{{ $message }}</p> @enderror

            <!-- Cart Items (Desktop Table & Mobile Cards) -->
            <div class="border border-white/80 rounded-2xl overflow-hidden shadow-2xs">
                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[700px]">
                        <thead class="bg-[#163144]/5 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                            <tr>
                                <th class="p-3.5 pl-5 w-12 text-center">No</th>
                                <th class="p-3.5">Produk Frozen Food</th>
                                <th class="p-3.5 w-36 text-center">Jumlah (Qty)</th>
                                <th class="p-3.5 w-36 text-right">Harga Beli Satuan</th>
                                <th class="p-3.5 w-36 text-right">Subtotal</th>
                                <th class="p-3.5 pr-5 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/40 font-medium">
                            @forelse($items as $index => $item)
                                <tr class="hover:bg-white/60 transition">
                                    
                                    <!-- No -->
                                    <td class="p-3.5 pl-5 text-center font-mono text-slate-400 font-bold">
                                        {{ $loop->iteration }}
                                    </td>

                                    <!-- Product Info -->
                                    <td class="p-3.5">
                                        <span class="font-black text-[#163144] block text-xs">{{ $item['product_name'] ?? 'Produk' }}</span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] text-slate-400">Stok Awal: <strong class="font-mono text-slate-600">{{ $item['current_stock'] ?? 0 }}</strong></span>
                                            @if($item['update_cost_price'] ?? false)
                                                <span class="text-[9px] font-mono text-[#163144] bg-[#a0bddb]/30 border border-[#a0bddb]/50 px-2 py-0.2 rounded-full font-bold">Update HPP Master</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Qty Stepper -->
                                    <td class="p-3.5 text-center">
                                        <div class="inline-flex items-center rounded-xl overflow-hidden border border-white bg-white/80">
                                            <button 
                                                type="button" 
                                                wire:click="decrementQuantity({{ $index }})" 
                                                class="w-7 h-7 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-r border-slate-200 cursor-pointer"
                                            >-</button>
                                            <input 
                                                wire:model.live.debounce.300ms="items.{{ $index }}.quantity" 
                                                type="number" 
                                                min="1" 
                                                class="w-14 h-7 text-center font-mono font-black bg-transparent text-xs outline-none text-[#163144]"
                                            >
                                            <button 
                                                type="button" 
                                                wire:click="incrementQuantity({{ $index }})" 
                                                class="w-7 h-7 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-l border-slate-200 cursor-pointer"
                                            >+</button>
                                        </div>
                                    </td>

                                    <!-- Unit Cost (Editable) -->
                                    <td class="p-3.5 text-right font-mono">
                                        <div class="relative w-28 ml-auto">
                                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[11px] font-bold text-slate-400">Rp</span>
                                            <input 
                                                wire:model.live.debounce.300ms="items.{{ $index }}.unit_cost" 
                                                type="number" 
                                                min="0" 
                                                step="500" 
                                                class="w-full pl-7 pr-2 py-1 text-right font-mono font-black bg-white/80 border border-white rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#a0bddb] text-[#163144]"
                                            >
                                        </div>
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="p-3.5 text-right font-mono font-black text-[#163144] whitespace-nowrap">
                                        Rp {{ number_format(($item['quantity'] ?? 0) * ($item['unit_cost'] ?? 0), 0, ',', '.') }}
                                    </td>

                                    <!-- Remove Action -->
                                    <td class="p-3.5 pr-5 text-center">
                                        <button 
                                            type="button" 
                                            wire:click="removeFromCart({{ $index }})" 
                                            class="btn-glass-action inline-flex items-center justify-center w-7 h-7 p-0"
                                            title="Hapus dari Keranjang"
                                        >
                                            ✕
                                        </button>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-slate-400">
                                        <div class="space-y-1">
                                            <p class="font-bold text-[#163144] text-xs">Keranjang restock masih kosong</p>
                                            <p class="text-[11px] text-slate-400">Silakan pilih produk pada <strong>Card 2 (Tambah Barang Masuk)</strong> di atas, lalu klik <strong>'Masukkan'</strong>.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                        @if(count($items) > 0)
                            <tfoot class="bg-[#163144]/5 border-t border-white/60 font-black text-xs">
                                <tr>
                                    <td colspan="2" class="p-3 pl-5 text-[#163144]">Total Akumulasi Barang Masuk:</td>
                                    <td class="p-3 text-center font-mono text-[#163144]">
                                        {{ array_sum(array_column($items, 'quantity')) }} Unit
                                    </td>
                                    <td class="p-3 text-right text-[#163144]">Total Belanja:</td>
                                    <td class="p-3 text-right font-mono text-[#163144] font-black">
                                        Rp {{ number_format($this->calculatedTotal, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden divide-y divide-white/40">
                    @forelse($items as $index => $item)
                        <div class="p-3.5 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="font-black text-[#163144] text-xs block leading-tight">{{ $item['product_name'] ?? 'Produk' }}</span>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[10px] text-slate-400">Stok: <strong class="font-mono text-slate-600">{{ $item['current_stock'] ?? 0 }}</strong></span>
                                        @if($item['update_cost_price'] ?? false)
                                            <span class="text-[9px] font-mono text-[#163144] bg-[#a0bddb]/30 border border-[#a0bddb]/50 px-1.5 py-0.2 rounded-full font-bold">Update HPP</span>
                                        @endif
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    wire:click="removeFromCart({{ $index }})" 
                                    class="w-7 h-7 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center text-xs font-black shrink-0 transition"
                                    title="Hapus"
                                >
                                    ✕
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-1 items-center">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 block mb-1">Jumlah (Qty)</span>
                                    <div class="inline-flex items-center rounded-xl overflow-hidden border border-white bg-white/80 shadow-2xs">
                                        <button 
                                            type="button" 
                                            wire:click="decrementQuantity({{ $index }})" 
                                            class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-black text-xs border-r border-slate-200"
                                        >-</button>
                                        <input 
                                            wire:model.live.debounce.300ms="items.{{ $index }}.quantity" 
                                            type="number" 
                                            min="1" 
                                            class="w-11 h-7 text-center font-mono font-black bg-transparent text-xs outline-none text-[#163144]"
                                        >
                                        <button 
                                            type="button" 
                                            wire:click="incrementQuantity({{ $index }})" 
                                            class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-black text-xs border-l border-slate-200"
                                        >+</button>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 block mb-1">Harga Beli (Satuan)</span>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-[10px] font-bold text-slate-400">Rp</span>
                                        <input 
                                            wire:model.live.debounce.300ms="items.{{ $index }}.unit_cost" 
                                            type="number" 
                                            min="0" 
                                            step="500" 
                                            class="w-full pl-6 pr-2 py-1 text-right font-mono font-black bg-white/80 border border-white rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#a0bddb] text-[#163144] shadow-2xs"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1 border-t border-white/50 text-xs">
                                <span class="text-[11px] text-slate-500 font-semibold">Subtotal Item:</span>
                                <span class="font-mono font-black text-[#163144]">
                                    Rp {{ number_format(($item['quantity'] ?? 0) * ($item['unit_cost'] ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 px-4 text-slate-400">
                            <p class="font-bold text-[#163144] text-xs">Keranjang restock masih kosong</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Silakan pilih produk pada formulir di atas lalu klik 'Masukkan'.</p>
                        </div>
                    @endforelse

                    @if(count($items) > 0)
                        <div class="p-3.5 bg-[#163144]/5 flex items-center justify-between text-xs font-black text-[#163144]">
                            <span>Total ({{ array_sum(array_column($items, 'quantity')) }} unit)</span>
                            <span class="font-mono text-sm">Rp {{ number_format($this->calculatedTotal, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- CARD 4: RINGKASAN TOTAL & TOMBOL SIMPAN -->
        <div class="glass-card-plek p-6 rounded-3xl border border-white/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div>
                <span class="text-xs font-black text-[#163144] uppercase tracking-wider block">Total Faktur Pengadaan</span>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if(count($items) > 0)
                        Akumulasi total biaya pengadaan dari {{ count($items) }} jenis produk ({{ array_sum(array_column($items, 'quantity')) }} unit)
                    @else
                        Belum ada produk di dalam keranjang restock
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                <div class="text-right">
                    <span class="text-2xl sm:text-3xl font-black text-[#163144] font-mono">
                        Rp {{ number_format($this->calculatedTotal, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <a 
                        href="{{ route('admin.purchases.index') }}" 
                        wire:navigate 
                        class="btn-glass-action px-4 py-2.5"
                    >
                        Batal
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#1f425c] active:bg-[#163144] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        @if(count($items) === 0) disabled @endif
                    >
                        Simpan & Tambah Stok
                    </button>
                </div>
            </div>
        </div>

    </form>

    <!-- ========================================================================= -->
    <!-- MODAL: PILIH RENCANA KULAKAN UNTUK DITARIK KE FAKTUR -->
    <!-- ========================================================================= -->
    <!-- ========================================================================= -->
    <!-- MODAL: PILIH RENCANA KULAKAN UNTUK DITARIK KE FAKTUR -->
    <!-- ========================================================================= -->
    @if($showRestockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0" wire:click="$set('showRestockModal', false)"></div>

            <!-- Modal Panel -->
            <div class="glass-card-plek relative w-full max-w-2xl p-6 sm:p-7 my-auto overflow-hidden text-left bg-white/95 shadow-2xl rounded-[32px] border border-white/90 modal-content-animate">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-[#163144] leading-tight">Tarik Data dari Rencana Kulakan</h3>
                                <p class="text-xs text-slate-500 font-medium">Pilih pesanan kulakan yang sudah dibuat / dipesan via WhatsApp untuk diinput ke faktur</p>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showRestockModal', false)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                    </div>

                    @if(!isset($availableRestockPlans) || $availableRestockPlans->isEmpty())
                        <div class="p-8 text-center space-y-3">
                            <div class="w-14 h-14 rounded-2xl bg-[#a0bddb]/30 text-[#163144] border border-[#a0bddb]/40 shadow-2xs flex items-center justify-center mx-auto text-xl">
                                📋
                            </div>
                            <h4 class="text-sm font-black text-[#163144]">Tidak ada Rencana Kulakan Aktif</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">Semua rencana kulakan sudah diproses atau belum pernah dibuat dari menu stok rendah.</p>
                            <a 
                                href="{{ route('admin.purchases.restock') }}" 
                                wire:navigate 
                                class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-[#163144] font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs inline-flex items-center gap-1.5 mt-2"
                            >
                                <span>Buka Menu Perencanaan Kulakan &raquo;</span>
                            </a>
                        </div>
                    @else
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                            @foreach($availableRestockPlans as $rPlan)
                                <div class="p-4 rounded-2xl border border-slate-100 bg-white hover:bg-[#a0bddb]/10 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs font-mono font-black text-[#163144]">{{ $rPlan->plan_number }}</span>
                                            @if($rPlan->status === 'ordered')
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Sudah Di-WA ({{ $rPlan->ordered_at ? $rPlan->ordered_at->format('d/m H:i') : 'Ordered' }})
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    Draft
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-xs font-bold text-slate-800 mt-1">
                                            Supplier: <span class="text-[#163144]">{{ $rPlan->supplier ? $rPlan->supplier->name : '-' }}</span>
                                        </p>

                                        <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
                                            <span>{{ $rPlan->created_at->format('d M Y, H:i') }}</span>
                                            <span>•</span>
                                            <span>{{ $rPlan->items->count() }} Jenis Produk ({{ $rPlan->items->sum('quantity_ordered') }} Unit)</span>
                                            <span>•</span>
                                            <span class="font-mono font-black text-[#163144]">Est: Rp {{ number_format($rPlan->total_estimated_cost, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <button 
                                        type="button" 
                                        wire:click="loadFromRestockPlan({{ $rPlan->id }})" 
                                        class="px-5 py-2 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer flex items-center justify-center gap-1.5 shrink-0"
                                    >
                                        <span>Ambil Data Ini &raquo;</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-2 mt-5 pt-3 border-t border-slate-100">
                        <a 
                            href="{{ route('admin.purchases.restock') }}" 
                            wire:navigate 
                            class="text-xs font-bold text-[#163144] hover:underline flex items-center gap-1"
                        >
                            <span>Kelola Menu Perencanaan Kulakan &raquo;</span>
                        </a>

                        <button 
                            type="button" 
                            wire:click="$set('showRestockModal', false)" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
