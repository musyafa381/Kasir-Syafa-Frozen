<div class="space-y-4">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">
                {{ $isEditing ? 'Edit Produk : ' . $name : 'Tambah Produk Baru' }}
            </h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                {{ $isEditing ? 'Perbarui harga modal (HPP), harga jual eceran/member, opsi dus grosir, dan stok per cabang.' : 'Input data produk baru, tentukan HPP modal, harga jual reguler/member, opsi dus grosir, dan stok per cabang.' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.products.index') }}" 
                wire:navigate 
                class="btn-glass-action font-extrabold text-xs px-4 py-2.5 rounded-2xl"
            >
                Batal
            </a>
            <button 
                type="button" 
                wire:click="save" 
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Produk' }}</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menyimpan...</span>
                </span>
            </button>
        </div>
    </div>

    <!-- Form Section -->
    <form wire:submit="save" class="space-y-4">

        <!-- CARD 1: INFORMASI UTAMA & IDENTITAS PRODUK -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="border-b border-white/40 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">1. Informasi Dasar & Identitas Produk</h2>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Nama produk, satuan unit, kategori, supplier utama, dan kode barcode/SKU</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                <!-- Nama Produk -->
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Nama Produk <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        wire:model="name" 
                        type="text" 
                        placeholder="Contoh: Fiesta Crispy Nugget 500g / Cedea Crab Stick 250g" 
                        required
                        class="w-full px-3.5 py-2.5 bg-white/70 border {{ $errors->has('name') ? 'border-rose-500' : 'border-white/80 focus:border-[#163144]' }} rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white outline-none shadow-2xs"
                    >
                    @error('name') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Satuan Unit -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Satuan Unit <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        wire:model="unit" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                    >
                        <option value="pack">Pack</option>
                        <option value="pcs">Pcs</option>
                        <option value="kg">Kg</option>
                        <option value="box">Box / Dus</option>
                        <option value="butir">Butir</option>
                        <option value="gram">Gram</option>
                    </select>
                    @error('unit') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kategori Produk -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Kategori Produk</label>
                    <x-searchable-select 
                        wire:model="category_id" 
                        :options="$categories" 
                        placeholder="— Pilih Kategori Produk —" 
                        empty-option="— Tanpa Kategori —" 
                    />
                    @error('category_id') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Supplier Rekanan -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Supplier Rekanan Utama</label>
                    <x-searchable-select 
                        wire:model="supplier_id" 
                        :options="$suppliers" 
                        placeholder="— Pilih Supplier Pemasok —" 
                        empty-option="— Tanpa Supplier —" 
                    />
                    @error('supplier_id') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Barcode / SKU -->
                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold text-[#163144] uppercase tracking-wider">
                            Barcode / SKU Satuan Eceran
                        </label>
                        <button 
                            type="button" 
                            wire:click="generateBarcode" 
                            class="btn-glass-action text-[11px] font-bold px-2.5 py-0.5 rounded-full"
                        >
                            Generate Otomatis
                        </button>
                    </div>
                    <input 
                        wire:model="barcode" 
                        type="text" 
                        placeholder="Scan barcode fisik produk atau gunakan generate otomatis..." 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-mono font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    @error('barcode') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Deskripsi / Catatan -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Deskripsi / Catatan Tambahan</label>
                    <input 
                        wire:model="description" 
                        type="text" 
                        placeholder="Catatan rasa, berat kemasan, expired, atau varian..." 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    @error('description') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- CARD 2: PENETAPAN HARGA SATUAN ECERAN -->
        <div 
            class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4"
            x-data="{
                costDisplay: '',
                sellingDisplay: '',
                loyaltyDisplay: '',
                init() {
                    this.costDisplay = this.formatNum($wire.cost_price);
                    this.sellingDisplay = this.formatNum($wire.selling_price);
                    this.loyaltyDisplay = this.formatNum($wire.loyalty_price);
                    $watch('$wire.cost_price', val => this.costDisplay = this.formatNum(val));
                    $watch('$wire.selling_price', val => this.sellingDisplay = this.formatNum(val));
                    $watch('$wire.loyalty_price', val => this.loyaltyDisplay = this.formatNum(val));
                },
                formatNum(val) {
                    let num = parseInt(val) || 0;
                    return num > 0 ? num.toLocaleString('id-ID') : '0';
                },
                handleCost(e) {
                    let num = parseInt(e.target.value.replace(/[^0-9]/g, '')) || 0;
                    this.costDisplay = num > 0 ? num.toLocaleString('id-ID') : '0';
                    $wire.set('cost_price', num);
                },
                handleSelling(e) {
                    let num = parseInt(e.target.value.replace(/[^0-9]/g, '')) || 0;
                    this.sellingDisplay = num > 0 ? num.toLocaleString('id-ID') : '0';
                    $wire.set('selling_price', num);
                },
                handleLoyalty(e) {
                    let num = parseInt(e.target.value.replace(/[^0-9]/g, '')) || 0;
                    this.loyaltyDisplay = num > 0 ? num.toLocaleString('id-ID') : '0';
                    $wire.set('loyalty_price', num);
                }
            }"
        >
            <div class="border-b border-white/40 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">2. Penetapan Harga Satuan Eceran (Standar Pusat)</h2>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Harga modal dasar (HPP), harga jual standar konsumen, dan harga khusus loyalty member</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
                <!-- Modal (HPP Eceran) -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Modal HPP Eceran <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-xs">Rp</span>
                        <input 
                            type="text" 
                            x-model="costDisplay"
                            @input="handleCost($event)"
                            @focus="$event.target.select()"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-black text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        >
                    </div>
                    <p class="text-[11px] text-slate-400 font-semibold mt-1">Biaya modal beli per unit ecer</p>
                    @error('cost_price') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jual Reguler -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold text-[#163144] uppercase tracking-wider">
                            Harga Jual Reguler <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-xs">Rp</span>
                        <input 
                            type="text" 
                            x-model="sellingDisplay"
                            @input="handleSelling($event)"
                            @focus="$event.target.select()"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-black text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        >
                    </div>
                    <p class="text-[11px] text-emerald-700 font-bold mt-1">
                        Margin Laba: +Rp {{ number_format(max(0, (int)($selling_price ?: 0) - (int)($cost_price ?: 0)), 0, ',', '.') }}
                    </p>
                    @error('selling_price') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jual Loyalty Member -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold text-[#163144] uppercase tracking-wider">
                            Harga Loyalty Member
                        </label>
                        <span class="text-[10px] text-slate-400 font-semibold">(Opsional)</span>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-xs">Rp</span>
                        <input 
                            type="text" 
                            x-model="loyaltyDisplay"
                            @input="handleLoyalty($event)"
                            @focus="$event.target.select()"
                            placeholder="0"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-black text-[#386797] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        >
                    </div>
                    <p class="text-[11px] text-slate-500 font-semibold mt-1">
                        @if((int)($loyalty_price ?: 0) > 0 && (int)($selling_price ?: 0) > (int)$loyalty_price)
                            <span class="text-emerald-700 font-bold">Hemat Member: Rp {{ number_format((int)$selling_price - (int)$loyalty_price, 0, ',', '.') }}</span>
                        @else
                            Kosongkan jika sama dengan reguler
                        @endif
                    </p>
                    @error('loyalty_price') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- CARD 3: PENGATURAN GROSIR / KARDUS (DUS) -->
        <div 
            class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4"
            x-data="{
                boxSellingDisplay: '',
                boxLoyaltyDisplay: '',
                init() {
                    this.boxSellingDisplay = this.formatNum($wire.box_selling_price);
                    this.boxLoyaltyDisplay = this.formatNum($wire.box_loyalty_price);
                    $watch('$wire.box_selling_price', val => this.boxSellingDisplay = this.formatNum(val));
                    $watch('$wire.box_loyalty_price', val => this.boxLoyaltyDisplay = this.formatNum(val));
                },
                formatNum(val) {
                    let num = parseInt(val) || 0;
                    return num > 0 ? num.toLocaleString('id-ID') : '';
                },
                handleBoxSelling(e) {
                    let num = parseInt(e.target.value.replace(/[^0-9]/g, '')) || 0;
                    this.boxSellingDisplay = num > 0 ? num.toLocaleString('id-ID') : '';
                    $wire.set('box_selling_price', num);
                },
                handleBoxLoyalty(e) {
                    let num = parseInt(e.target.value.replace(/[^0-9]/g, '')) || 0;
                    this.boxLoyaltyDisplay = num > 0 ? num.toLocaleString('id-ID') : '';
                    $wire.set('box_loyalty_price', num);
                }
            }"
        >
            <div class="border-b border-white/40 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">3. Penjualan Grosir / Kardus (Dus)</h2>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Isi jika produk ini dapat dibeli langsung dalam 1 kardus/karton utuh (opsional)</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-900 font-extrabold text-[10px]">
                    Opsi Grosir
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
                <!-- Isi per Dus -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Isi per Dus (Pack/Pcs)
                    </label>
                    <input 
                        wire:model="pieces_per_box" 
                        type="number" 
                        min="2" 
                        placeholder="Contoh: 12 / 24 / 40" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    <p class="text-[11px] text-slate-400 font-semibold mt-1">Jumlah unit ecer di dalam 1 dus</p>
                    @error('pieces_per_box') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Harga 1 Dus Reguler -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Harga 1 Dus Reguler
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-xs">Rp</span>
                        <input 
                            type="text" 
                            x-model="boxSellingDisplay"
                            @input="handleBoxSelling($event)"
                            @focus="$event.target.select()"
                            placeholder="0"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-black text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        >
                    </div>
                    @error('box_selling_price') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Harga 1 Dus Member -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Harga 1 Dus Member
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-xs">Rp</span>
                        <input 
                            type="text" 
                            x-model="boxLoyaltyDisplay"
                            @input="handleBoxLoyalty($event)"
                            @focus="$event.target.select()"
                            placeholder="0"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-black text-[#386797] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        >
                    </div>
                    @error('box_loyalty_price') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Barcode Dus Luar -->
                <div class="sm:col-span-3">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold text-[#163144] uppercase tracking-wider">
                            Barcode Luar Kardus (Box Barcode)
                        </label>
                        <button 
                            type="button" 
                            wire:click="generateBoxBarcode" 
                            class="btn-glass-action text-[11px] font-bold px-2.5 py-0.5 rounded-full"
                        >
                            Generate Barcode Dus
                        </button>
                    </div>
                    <input 
                        wire:model="box_barcode" 
                        type="text" 
                        placeholder="Scan barcode yang tercetak di luar karton/dus..." 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-mono font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    @error('box_barcode') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- CARD 4: PERSEDIAAN STOK & STATUS TOKO -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="border-b border-white/40 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">4. Persediaan Stok & Status Toko</h2>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Jumlah stok fisik utama dan status ketersediaan di layar kasir</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                <!-- Stok Saat Ini -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Jumlah Stok Master / Pusat <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        wire:model="stock" 
                        type="number" 
                        min="0" 
                        required
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-black text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    <p class="text-[11px] text-slate-400 font-semibold mt-1">Stok dihitung dalam satuan {{ $unit ?: 'pack' }}</p>
                    @error('stock') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Alert Stok -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">
                        Batas Peringatan Stok Menipis <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        wire:model="min_stock_alert" 
                        type="number" 
                        min="0" 
                        required
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-black text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    <p class="text-[11px] text-slate-400 font-semibold mt-1">Peringatan otomatis jika stok di bawah angka ini</p>
                    @error('min_stock_alert') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Status Aktif Switch -->
            <div class="pt-2 border-t border-white/40">
                <label class="inline-flex items-center gap-2.5 text-xs font-bold text-[#163144] cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        wire:model="is_active" 
                        class="w-4 h-4 rounded border-slate-300 text-[#163144] focus:ring-[#163144]"
                    >
                    <span>Tampilkan produk ini di katalog dan layar kasir POS (Status Aktif)</span>
                </label>
            </div>
        </div>

        <!-- CARD 5: PENGATURAN HARGA & STOK PER CABANG TOKO (MULTI-CABANG) -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="border-b border-white/40 pb-3 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">5. Pengaturan Khusus Tiap Cabang Toko</h2>
                        <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[10px]">Multi-Cabang</span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Tentukan harga jual reguler, harga member, dan alokasi stok terpisah untuk masing-masing cabang</p>
                </div>
            </div>

            <div class="space-y-3.5">
                @foreach($outlets as $outlet)
                    @php
                        $outletId = $outlet->id;
                    @endphp
                    <div wire:key="outlet-pricing-{{ $outletId }}" class="p-4 rounded-2xl border border-white/80 {{ $outlet->is_main ? 'bg-blue-50/40' : 'bg-white/40' }} space-y-3">
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-[#163144] text-sm">{{ $outlet->name }}</span>
                                <span class="font-mono text-[10px] text-slate-400 font-semibold">({{ $outlet->code }})</span>
                                @if($outlet->is_main)
                                    <span class="px-2.5 py-0.5 rounded-full bg-[#163144] text-white font-extrabold text-[10px] uppercase">Pusat</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[10px] uppercase">Cabang</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <!-- Harga Jual Reguler Cabang -->
                            <div>
                                <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Harga Jual Cabang</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center font-black text-slate-400 text-xs">Rp</span>
                                    <input 
                                        type="number" 
                                        wire:model="branchPrices.{{ $outletId }}.selling_price" 
                                        min="0"
                                        placeholder="{{ (int)($selling_price ?: 0) }}"
                                        class="w-full pl-8 pr-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-[#163144] focus:border-[#163144] outline-none"
                                    >
                                </div>
                            </div>

                            <!-- Harga Member Cabang -->
                            <div>
                                <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Harga Member</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center font-black text-slate-400 text-xs">Rp</span>
                                    <input 
                                        type="number" 
                                        wire:model="branchPrices.{{ $outletId }}.loyalty_price" 
                                        min="0"
                                        placeholder="0 (opsional)"
                                        class="w-full pl-8 pr-2.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-[#386797] focus:border-[#163144] outline-none"
                                    >
                                </div>
                            </div>

                            <!-- Stok Cabang -->
                            <div>
                                <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Stok di Cabang Ini</label>
                                <input 
                                    type="number" 
                                    wire:model="branchPrices.{{ $outletId }}.stock" 
                                    min="0"
                                    placeholder="0"
                                    class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-emerald-700 focus:border-[#163144] outline-none"
                                >
                            </div>

                            <!-- Minimum Alert Cabang -->
                            <div>
                                <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Min. Alert Stok</label>
                                <input 
                                    type="number" 
                                    wire:model="branchPrices.{{ $outletId }}.min_stock_alert" 
                                    min="0"
                                    placeholder="5"
                                    class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:border-[#163144] outline-none"
                                >
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bottom Action Bar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a 
                href="{{ route('admin.products.index') }}" 
                wire:navigate 
                class="btn-glass-action font-extrabold text-xs px-5 py-2.5 rounded-2xl"
            >
                Batal
            </a>
            <button 
                type="submit" 
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Produk' }}</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menyimpan...</span>
                </span>
            </button>
        </div>

    </form>
</div>
