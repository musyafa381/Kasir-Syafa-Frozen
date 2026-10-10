<div class="space-y-3">

    <!-- Page Header & Back Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-1">
        <div class="flex items-center gap-3">
            <a 
                href="{{ route('admin.categories.index') }}" 
                wire:navigate 
                class="w-10 h-10 rounded-2xl glass-card-plek text-[#163144] hover:bg-white flex items-center justify-center shadow-2xs transition active:scale-95 shrink-0"
                title="Kembali ke Kategori"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-black text-[#163144] tracking-tight">Kategori : {{ $category->name }}</h1>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                    {{ $category->description ?: 'Daftar produk frozen food yang terdaftar di dalam kategori ini.' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a 
                href="{{ route('admin.products.index') }}" 
                wire:navigate 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Buka Katalog Produk</span>
            </a>
        </div>
    </div>

    <!-- Summary Metric KPI Cards (Arctic Frosted Glass) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- 1. Total Jenis Produk / Varian -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Katalog</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Jumlah Varian</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-mono font-bold text-[9px] sm:text-[10px] shrink-0">
                    SKU
                </span>
            </div>
            <div class="mt-2 sm:mt-3 flex items-baseline justify-between">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block truncate">
                    {{ $totalProductsCount }}
                </span>
                <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold truncate">Varian produk</span>
            </div>
        </div>

        <!-- 2. Total Unit Stok Fisik -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Fisik</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Unit Stok</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-bold text-[9px] sm:text-[10px] shrink-0">
                    Tersedia
                </span>
            </div>
            <div class="mt-2 sm:mt-3 flex items-baseline justify-between">
                <span class="text-base sm:text-xl font-black text-emerald-700 font-mono tracking-tight block truncate">
                    {{ number_format($totalPhysicalStock, 0, ',', '.') }}
                </span>
                <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold truncate">Unit tersimpan</span>
            </div>
        </div>

        <!-- 3. Total Nilai Aset Modal HPP -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Aset</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Nilai Modal (HPP)</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-mono font-bold text-[9px] sm:text-[10px] shrink-0">
                    Modal
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block truncate">
                    Rp {{ number_format($totalAssetValue, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Total modal stok aktif</p>
            </div>
        </div>

        <!-- 4. Total Potensi Omset -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#2c527a] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Proyeksi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Potensi Omset</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-[#a0bddb]/25 text-[#163144] border border-[#a0bddb]/40 font-bold text-[9px] sm:text-[10px] shrink-0">
                    Omset
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block truncate">
                    Rp {{ number_format($totalPotentialRevenue, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Nilai jual jika habis</p>
            </div>
        </div>

    </div>

    <!-- Search & Filter Bar (Glass Card Plek) -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari nama produk atau barcode di kategori ini..." 
                    class="w-full pl-3.5 pr-10 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs transition"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-600">Filter Stok:</span>
                <select 
                    wire:model.live="filterStock" 
                    class="px-3 py-1.5 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
                    <option value="">Semua Stok</option>
                    <option value="low">Menipis (Low Stock)</option>
                    <option value="out">Habis (Out of Stock)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table & Mobile Card View of Category Products -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Produk</th>
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
                            $isLow = $product->isLowStock();
                        @endphp
                        <tr class="hover:bg-white/40 transition">
                            <!-- Nama Produk -->
                            <td class="p-3.5 pl-5">
                                <span class="font-black text-[#163144] text-sm block">
                                    {{ $product->name }}
                                </span>
                            </td>

                            <!-- Barcode -->
                            <td class="p-3.5 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $product->barcode ?: '-' }}
                            </td>

                            <!-- Modal HPP -->
                            <td class="p-3.5 text-right font-medium text-slate-600 font-mono whitespace-nowrap">
                                Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                            </td>

                            <!-- Harga Jual Reguler -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-black text-[#163144] text-sm block font-mono">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Harga Loyalty -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                @if($product->loyalty_price && $product->loyalty_price < $product->selling_price)
                                    <span class="font-black text-[#163144] text-sm block font-mono">
                                        Rp {{ number_format($product->loyalty_price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-mono">
                                        Hemat Rp {{ number_format($product->selling_price - $product->loyalty_price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">- Sama dgn Reguler -</span>
                                @endif
                            </td>

                            <!-- Sisa Stok -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="inline-block px-3 py-1 rounded-full font-black text-xs font-mono
                                    {{ $product->stock <= 0 ? 'bg-rose-50 text-rose-700 border border-rose-200/60' : ($isLow ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'glass-pill-plek text-[#163144]') }}">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase 
                                    {{ $product->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-slate-100/80 text-slate-500 border border-slate-200/60' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a 
                                        href="{{ route('admin.products.edit', $product->id) }}" 
                                        wire:navigate 
                                        class="btn-glass-action"
                                        title="Edit Produk"
                                    >
                                        Edit
                                    </a>
                                    <button 
                                        wire:click="confirmDetach({{ $product->id }})" 
                                        class="btn-glass-action"
                                        title="Lepaskan dari kategori ini"
                                    >
                                        Lepas Kategori
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Belum ada produk di dalam kategori ini.
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
                    $isLow = $product->isLowStock();
                @endphp
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-black text-[#163144] text-sm leading-snug">{{ $product->name }}</h3>
                            @if($product->barcode)
                                <span class="font-mono text-[10px] text-slate-400">SKU: {{ $product->barcode }}</span>
                            @endif
                        </div>
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $product->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-slate-100/80 text-slate-500 border border-slate-200/60' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 glass-pill-plek p-2.5 rounded-xl text-xs">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Harga Reguler</span>
                            <span class="font-mono font-black text-[#163144]">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Sisa Stok</span>
                            <span class="font-mono font-black {{ $product->stock <= 0 ? 'text-rose-600' : ($isLow ? 'text-amber-700' : 'text-[#163144]') }}">
                                {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-white/40">
                        <a 
                            href="{{ route('admin.products.edit', $product->id) }}" 
                            wire:navigate 
                            class="btn-glass-action"
                        >
                            Edit
                        </a>
                        <button 
                            wire:click="confirmDetach({{ $product->id }})" 
                            class="btn-glass-action"
                        >
                            Lepas Kategori
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada produk di dalam kategori ini.
                </div>
            @endforelse
        </div>

        {{ $products->links() }}
    </div>

    <!-- Modal Konfirmasi Lepas Kategori -->
    @if($showDetachModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4 my-auto confirm-card">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-700 border border-amber-300/60 shadow-2xs flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Lepas Kategori</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin melepaskan produk <strong class="text-[#163144]">"{{ $detachingProductName }}"</strong> dari kategori <strong class="text-[#163144]">"{{ $category->name }}"</strong>?
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1 font-semibold">
                            (Produk tetap tersimpan di katalog dan berstatus <em>Tanpa Kategori</em>)
                        </p>
                    </div>
                </div>

                <!-- Action Buttons (Divider + Right-Aligned Rounded Pills) -->
                <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-slate-300/40">
                    <button 
                        type="button" 
                        wire:click="$set('showDetachModal', false)" 
                        class="px-5 py-2 rounded-full bg-white/50 hover:bg-white/75 border border-white/40 text-slate-700 font-black text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="detachProduct" 
                        class="px-5 py-2 rounded-full bg-amber-500 hover:bg-amber-600 text-white font-black text-xs shadow-md shadow-amber-950/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Lepaskan
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
