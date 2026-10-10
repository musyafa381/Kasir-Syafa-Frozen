<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Katalog Produk & Stok</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Kelola harga modal (HPP), harga jual reguler, harga khusus member/loyalty, dan stok.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.stock.index') }}" wire:navigate class="btn-glass-action font-extrabold text-xs px-3.5 py-2 rounded-2xl">
                Penyesuaian Stok
            </a>
            
            <button 
                type="button"
                wire:click="openImportModal" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span>Impor Excel</span>
            </button>

            <a 
                href="{{ route('admin.products.create') }}" 
                wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Tambah Produk Baru</span>
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        <!-- Search -->
        <div class="relative">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari nama produk atau barcode..." 
                class="w-full pl-3.5 pr-10 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
            >
            @if($search)
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
            @endif
        </div>

        <!-- Category Filter (Searchable Dropdown) -->
        <div>
            <x-searchable-select 
                wire:model.live="filterCategory" 
                :options="$categories" 
                placeholder="Semua Kategori" 
                empty-option="Semua Kategori" 
            />
        </div>

        <!-- Supplier Filter (Searchable Dropdown) -->
        <div>
            <x-searchable-select 
                wire:model.live="filterSupplier" 
                :options="$suppliers" 
                placeholder="Semua Supplier" 
                empty-option="Semua Supplier" 
            />
        </div>

        <!-- Stock Status Filter -->
        <div>
            <select 
                wire:model.live="filterStock" 
                class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
            >
                <option value="">Semua Status Stok</option>
                <option value="low">Stok Rendah / Menipis</option>
                <option value="out">Stok Habis (0)</option>
            </select>
        </div>
    </div>

    <!-- Table & Mobile Card View of Products -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[900px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Produk</th>
                        <th class="p-3.5 whitespace-nowrap">Supplier Pemasok</th>
                        <th class="p-3.5 whitespace-nowrap">Barcode / SKU</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Modal (HPP)</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Harga Reguler</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Harga Loyalty</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Sisa Stok</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($products as $product)
                        @php
                            $profitReg = $product->selling_price - $product->cost_price;
                            $profitLoyalty = ($product->loyalty_price ?: $product->selling_price) - $product->cost_price;
                            $isLow = $product->isLowStock();
                        @endphp
                        <tr class="hover:bg-white/40 transition">
                            <!-- Nama Produk -->
                            <td class="p-3.5 pl-5">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider block whitespace-nowrap text-[#386797]">
                                    {{ $product->category?->name ?? 'Tanpa Kategori' }}
                                </span>
                                <span class="font-extrabold text-[#163144] text-sm block">
                                    {{ $product->name }}
                                </span>
                                @if($product->hasBoxOption())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded-full glass-pill-plek text-amber-900 font-bold text-[10px]">
                                        <span>Dus: {{ $product->pieces_per_box }} {{ $product->unit }} @ Rp {{ number_format($product->box_selling_price, 0, ',', '.') }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Supplier Pemasok -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if($product->supplier)
                                    <span class="font-bold text-[#163144] text-xs block">{{ $product->supplier->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $product->supplier->code ?: 'Supplier' }}</span>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">- Belum Diatur -</span>
                                @endif
                            </td>

                            <!-- Barcode -->
                            <td class="p-3.5 font-mono text-slate-600 text-[11px] whitespace-nowrap">
                                <div><span class="text-[9px] text-slate-400 uppercase font-sans">Ecer:</span> {{ $product->barcode ?: '-' }}</div>
                                @if($product->box_barcode)
                                    <div class="text-amber-800 font-bold text-[10px] mt-0.5"><span class="text-[9px] text-amber-600 uppercase font-sans">Dus:</span> {{ $product->box_barcode }}</div>
                                @endif
                            </td>

                            <!-- Modal (HPP) -->
                            <td class="p-3.5 text-right font-bold text-[#163144] whitespace-nowrap">
                                Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                            </td>

                            <!-- Harga Jual Reguler -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-black text-[#163144] text-sm block">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-emerald-700 font-bold">
                                    +Rp {{ number_format($profitReg, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Harga Loyalty (Member) -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                @if($product->loyalty_price && $product->loyalty_price > 0)
                                    <span class="font-black text-[#163144] text-sm block">
                                        Rp {{ number_format($product->loyalty_price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono font-semibold">
                                        Hemat Rp {{ number_format($product->selling_price - $product->loyalty_price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">- Sama dgn Reguler -</span>
                                @endif
                            </td>

                            <!-- Stok -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="inline-block px-3 py-1 rounded-full font-black text-xs glass-pill-plek 
                                    {{ $product->stock <= 0 ? 'text-red-700 ring-1 ring-red-300' : ($isLow ? 'text-amber-800 ring-1 ring-amber-300' : 'text-[#163144]') }}">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                            </td>

                            <!-- Status Switch -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <button 
                                    wire:click="toggleStatus({{ $product->id }})" 
                                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase glass-pill-plek transition cursor-pointer 
                                    {{ $product->is_active ? 'text-[#163144]' : 'text-slate-400 opacity-60' }}"
                                >
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            <!-- Aksi Side by Side -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        type="button"
                                        wire:click="openBranchPricingModal({{ $product->id }})" 
                                        class="btn-glass-action text-[11px]"
                                        title="Atur harga jual, harga member, dan stok di tiap cabang toko"
                                    >
                                        Cabang
                                    </button>
                                    <a 
                                        href="{{ route('admin.products.edit', $product->id) }}" 
                                        wire:navigate
                                        class="btn-glass-action p-2"
                                        title="Edit Produk"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button 
                                        type="button"
                                        wire:click="confirmDelete({{ $product->id }})" 
                                        class="btn-glass-action p-2"
                                        title="Hapus Produk"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-slate-400 font-semibold">
                                Belum ada data produk sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($products as $product)
                @php
                    $profitReg = $product->selling_price - $product->cost_price;
                    $isLow = $product->isLowStock();
                @endphp
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider block text-[#386797]">
                                {{ $product->category?->name ?? 'Tanpa Kategori' }}
                            </span>
                            <h3 class="font-extrabold text-[#163144] text-sm leading-snug">{{ $product->name }}</h3>
                            @if($product->barcode)
                                <span class="font-mono text-[10px] text-slate-400 font-semibold">SKU: {{ $product->barcode }}</span>
                            @endif
                        </div>
                        <button 
                            wire:click="toggleStatus({{ $product->id }})" 
                            class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase glass-pill-plek transition cursor-pointer 
                            {{ $product->is_active ? 'text-[#163144]' : 'text-slate-400 opacity-60' }}"
                        >
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    <!-- Price & Stock Grid -->
                    <div class="grid grid-cols-3 gap-2 glass-pill-plek p-2.5 rounded-2xl border border-white/60 text-center">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-extrabold block">Harga Jual</span>
                            <span class="font-mono font-black text-[#163144] text-xs">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-extrabold block">Harga Member</span>
                            <span class="font-mono font-black text-[#163144] text-xs">
                                {{ $product->loyalty_price ? 'Rp '.number_format($product->loyalty_price, 0, ',', '.') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-extrabold block">Sisa Stok</span>
                            <span class="font-mono font-black text-xs {{ $product->stock <= 0 ? 'text-red-600' : ($isLow ? 'text-amber-700' : 'text-[#163144]') }}">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions Bar -->
                    <div class="flex items-center justify-between pt-1">
                        <button 
                            type="button"
                            wire:click="openBranchPricingModal({{ $product->id }})" 
                            class="btn-glass-action text-[11px]"
                        >
                            Harga Cabang
                        </button>
                        <div class="flex items-center gap-1.5">
                            <a 
                                href="{{ route('admin.products.edit', $product->id) }}" 
                                wire:navigate
                                class="btn-glass-action"
                            >
                                Edit
                            </a>
                            <button 
                                type="button"
                                wire:click="confirmDelete({{ $product->id }})" 
                                class="btn-glass-action"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                    Belum ada data produk sesuai filter.
                </div>
            @endforelse
        </div>

        {{ $products->links() }}
    </div>

    <!-- Modal Konfirmasi Hapus Produk -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4 my-auto confirm-card">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl {{ $deletingProductHasHistory ? 'bg-amber-500/15 text-amber-700 border-amber-300/60' : 'bg-rose-500/15 text-[#e1144b] border-rose-300/40' }} flex items-center justify-center shrink-0 border shadow-2xs">
                        @if($deletingProductHasHistory)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">
                            {{ $deletingProductHasHistory ? 'Produk Memiliki Riwayat' : 'Konfirmasi Hapus Produk' }}
                        </h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            @if($deletingProductHasHistory)
                                Produk <strong class="text-[#163144]">"{{ $deletingProductName }}"</strong> memiliki <strong class="text-amber-700 font-bold">{{ $deletingProductHistoryCount }} data riwayat</strong> transaksi/pembelian sehingga tidak dapat dihapus permanen. Anda dapat menonaktifkannya dari POS/Kasir.
                            @else
                                Apakah Anda yakin ingin menghapus produk <strong class="text-[#163144]">"{{ $deletingProductName }}"</strong> dari katalog?
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Buttons (Divider + Right-Aligned Rounded Pills) -->
                <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-slate-300/40">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteModal', false)" 
                        class="px-5 py-2 rounded-full bg-white/50 hover:bg-white/75 border border-white/40 text-slate-700 font-black text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    @if($deletingProductHasHistory)
                        <button 
                            type="button" 
                            wire:click="deactivateProduct" 
                            class="px-5 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-md shadow-amber-950/20 transition active:scale-95 cursor-pointer"
                        >
                            Nonaktifkan
                        </button>
                    @else
                        <button 
                            type="button" 
                            wire:click="deleteProduct" 
                            class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                        >
                            Ya, Hapus
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Impor Data Produk (Excel / CSV) -->
    @if($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-4xl w-full border border-white/90 overflow-hidden shadow-2xl my-8 flex flex-col max-h-[90vh] modal-content-animate">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">Impor Master Produk & Stok Awal</h2>
                            <p class="text-xs text-slate-500 font-semibold">Impor massal produk baru, update harga reguler/member, dan stok awal via Excel CSV</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="closeImportModal" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 space-y-5 overflow-y-auto grow text-slate-700">

                    <!-- Template Download Section -->
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50 border border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[10px] font-extrabold uppercase tracking-wide">Template Resmi Excel</span>
                                <span class="text-xs font-bold text-slate-900">Kolom Terpisah Otomatis di Excel (Tanpa Peringatan Error)</span>
                            </div>
                            <p class="text-xs text-slate-600">
                                Unduh template untuk mendapatkan format kolom standar yang otomatis terbagi ke Kolom A s/d I di Excel, lengkap dengan kolom <strong>Harga Jual Member</strong> dan <strong>Daftar Kategori Aktif</strong>.
                            </p>
                        </div>
                        <a 
                            href="{{ route('admin.print.products.import-template') }}" 
                            target="_blank"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition whitespace-nowrap cursor-pointer shrink-0"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh Template Excel</span>
                        </a>
                    </div>

                    <!-- File Upload Input Dropzone -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih File Excel / CSV Hasil Pengisian
                        </label>
                        <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-6 text-center bg-slate-50/50 hover:bg-emerald-50/30 transition">
                            <input 
                                type="file" 
                                wire:model="importFile" 
                                accept=".csv, .xlsx, .xls, .txt, text/csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            >
                            <div class="space-y-2 flex flex-col items-center">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="text-xs">
                                    <span class="font-bold text-emerald-600">Klik untuk memilih file</span> atau drag & drop file ke sini
                                </div>
                                <p class="text-[11px] text-slate-400">Format didukung: Excel Workbook (.xlsx), CSV (.csv), atau .xls (Maks 10MB)</p>
                            </div>
                        </div>

                        <!-- Upload Loading State -->
                        <div wire:loading wire:target="importFile" class="text-xs font-semibold text-[#163144] flex items-center gap-2 pt-1">
                            <svg class="animate-spin w-4 h-4 text-[#163144]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Sedang mengunggah dan menganalisis file...</span>
                        </div>

                        @error('importFile')
                            <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Error Alert -->
                    @if($importErrorMessage)
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                            <div class="w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0 mt-0.5">✕</div>
                            <div>
                                <h4 class="font-bold">Terjadi Kesalahan Analisis File</h4>
                                <p class="mt-0.5">{{ $importErrorMessage }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Preview & Validation Section -->
                    @if(!empty($previewRows))
                        <div class="space-y-3 pt-2">
                            
                            <!-- Stats & Options Bar -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
                                <div class="flex items-center gap-2 flex-wrap text-xs">
                                    <span class="font-bold text-slate-700">Ringkasan File:</span>
                                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-semibold text-slate-700">
                                        Total: <strong class="text-slate-900">{{ $importStats['total'] }}</strong>
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 font-semibold text-emerald-700">
                                        🟢 Produk Baru: <strong>{{ $importStats['valid'] }}</strong>
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 font-semibold text-amber-700">
                                        🟡 Update Terdaftar: <strong>{{ $importStats['update'] }}</strong>
                                    </span>
                                    @if($importStats['invalid'] > 0)
                                        <span class="px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-200 font-semibold text-rose-700">
                                            🔴 Error / Dilewati: <strong>{{ $importStats['invalid'] }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer select-none">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="updateExisting" 
                                        class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span>Perbarui jika barcode sudah ada</span>
                                </label>
                            </div>

                            <!-- Preview Table -->
                            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
                                <div class="max-h-64 overflow-y-auto">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead class="bg-slate-100 text-slate-700 font-bold sticky top-0 z-10 border-b border-slate-200">
                                            <tr>
                                                <th class="py-2.5 px-3">Baris</th>
                                                <th class="py-2.5 px-3">Status</th>
                                                <th class="py-2.5 px-3">Barcode</th>
                                                <th class="py-2.5 px-3">Nama Produk</th>
                                                <th class="py-2.5 px-3">Kategori</th>
                                                <th class="py-2.5 px-3 text-right">HPP</th>
                                                <th class="py-2.5 px-3 text-right">Harga Reguler</th>
                                                <th class="py-2.5 px-3 text-right">Harga Member</th>
                                                <th class="py-2.5 px-3 text-center">Stok Awal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @foreach($previewRows as $row)
                                                <tr class="hover:bg-slate-50 transition {{ !$row['is_valid'] ? 'bg-rose-50/50' : ($row['is_update'] ? 'bg-amber-50/30' : '') }}">
                                                    <td class="py-2 px-3 font-mono text-slate-500 text-[11px]">{{ $row['row_number'] }}</td>
                                                    <td class="py-2 px-3 whitespace-nowrap">
                                                        @if(!$row['is_valid'])
                                                            <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 font-bold text-[10px]" title="{{ implode(', ', $row['errors']) }}">
                                                                🔴 Error
                                                            </span>
                                                        @elseif($row['is_update'])
                                                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 font-bold text-[10px]">
                                                                🟡 Update
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 font-bold text-[10px]">
                                                                🟢 Baru
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 px-3 font-mono text-slate-600 text-[11px]">{{ $row['barcode'] ?: '-' }}</td>
                                                    <td class="py-2 px-3 font-semibold text-slate-900">
                                                        {{ $row['name'] }}
                                                        @if(!$row['is_valid'] && !empty($row['errors']))
                                                            <div class="text-[10px] text-rose-600 font-normal mt-0.5">
                                                                {{ implode(', ', $row['errors']) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="py-2 px-3 text-slate-600">{{ $row['category_name'] ?: 'Umum' }}</td>
                                                    <td class="py-2 px-3 text-right font-mono text-slate-700">Rp {{ number_format($row['cost_price'], 0, ',', '.') }}</td>
                                                    <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($row['selling_price'], 0, ',', '.') }}</td>
                                                    <td class="py-2 px-3 text-right font-mono text-emerald-700 font-bold">
                                                        {{ $row['loyalty_price'] > 0 ? 'Rp ' . number_format($row['loyalty_price'], 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="py-2 px-3 text-center font-bold text-blue-600">{{ $row['stock'] }} {{ $row['unit'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Modal Footer Actions -->
                <div class="p-4 px-6 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between gap-3 shrink-0 rounded-b-[32px]">
                    <button 
                        type="button" 
                        wire:click="closeImportModal" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>
                    
                    @if(!empty($previewRows))
                        <button 
                            type="button" 
                            wire:click="executeImport" 
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="executeImport">
                                Eksekusi Impor ({{ ($importStats['valid'] + ($updateExisting ? $importStats['update'] : 0)) }} Data Siap)
                            </span>
                            <span wire:loading wire:target="executeImport" class="inline-flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Sedang Menyimpan Data...</span>
                            </span>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    @endif

    <!-- Modal Pengaturan Harga & Stok Cabang -->
    @if($showBranchPricingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-2xl w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[90vh] my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">Harga & Stok per Cabang Toko</h3>
                            <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] text-[11px] font-black">
                                Produk: {{ $branchProductName }}
                            </span>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="$set('showBranchPricingModal', false)" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveBranchPricing" class="flex flex-col flex-1 min-h-0">
                    <div class="p-6 space-y-4 overflow-y-auto flex-1 max-h-[60vh]">
                        <p class="text-xs text-slate-500 font-semibold">
                            Atur harga jual reguler, harga member loyalty, dan jumlah stok terpisah untuk tiap cabang toko:
                        </p>

                        @foreach($branchPrices as $outletId => $bData)
                            <div wire:key="branch-outlet-modal-{{ $outletId }}" class="p-4 rounded-2xl border {{ $bData['is_main'] ? 'border-[#a0bddb]/60 bg-[#a0bddb]/10' : 'border-slate-200/80 bg-slate-50/80' }} space-y-3 shadow-2xs">
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-[#163144] text-sm">{{ $bData['outlet_name'] }}</span>
                                        <span class="font-mono text-[10px] text-slate-400 font-semibold">({{ $bData['outlet_code'] }})</span>
                                        @if($bData['is_main'])
                                            <span class="px-2.5 py-0.5 rounded-full bg-[#163144] text-white font-extrabold text-[10px] uppercase">Pusat</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[10px] uppercase">Cabang</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <!-- Harga Jual Reguler -->
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Harga Jual</label>
                                        <input 
                                            wire:model="branchPrices.{{ $outletId }}.selling_price" 
                                            type="number" 
                                            min="0" 
                                            required
                                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-black text-[#163144] focus:border-[#163144] outline-none shadow-2xs"
                                        >
                                    </div>

                                    <!-- Harga Loyalty Member -->
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Harga Member</label>
                                        <input 
                                            wire:model="branchPrices.{{ $outletId }}.loyalty_price" 
                                            type="number" 
                                            min="0" 
                                            placeholder="Opsional"
                                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-black text-[#163144] focus:border-[#163144] outline-none shadow-2xs"
                                        >
                                    </div>

                                    <!-- Stok di Cabang Ini -->
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Stok Cabang</label>
                                        <input 
                                            wire:model="branchPrices.{{ $outletId }}.stock" 
                                            type="number" 
                                            min="0" 
                                            required
                                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-black text-emerald-700 focus:border-[#163144] outline-none shadow-2xs"
                                        >
                                    </div>

                                    <!-- Minimum Alert Stok -->
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-[#163144] uppercase mb-1">Min. Alert</label>
                                        <input 
                                            wire:model="branchPrices.{{ $outletId }}.min_stock_alert" 
                                            type="number" 
                                            min="0" 
                                            required
                                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:border-[#163144] outline-none shadow-2xs"
                                        >
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 px-6 bg-slate-50/60 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0 rounded-b-[32px]">
                        <button 
                            type="button" 
                            wire:click="$set('showBranchPricingModal', false)" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove>Simpan Pengaturan Cabang</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>
