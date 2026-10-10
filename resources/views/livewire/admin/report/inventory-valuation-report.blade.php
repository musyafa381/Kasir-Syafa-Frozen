<div class="space-y-4">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Laporan Valuasi Aset Stok & Persediaan</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Analisis total modal persediaan yang tersimpan di toko, estimasi potensi omzet, dan proyeksi laba kotor stok.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.print.inventory-valuation.report', ['category_id' => $categoryId, 'outlet_id' => $outletId]) }}" 
                target="_blank" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-4 h-4 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak PDF</span>
            </a>

            <a 
                href="{{ route('admin.print.inventory-valuation.excel', ['category_id' => $categoryId, 'outlet_id' => $outletId]) }}" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-4 h-4 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Ekspor Excel</span>
            </a>
        </div>
    </div>

    <!-- 4 Executive KPI Valuation Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        
        <!-- 1. Total Modal Persediaan (Asset Cost Value) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Persediaan</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Modal Stok (HPP)</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    HPP
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($totalAssetCostValue, 0, ',', '.') }}
                </span>
                <p class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ number_format($totalStockUnits, 0, ',', '.') }} unit stok fisik</p>
            </div>
        </div>

        <!-- 2. Potensi Omzet Penjualan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Proyeksi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Potensi Omzet</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Omzet
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($totalPotentialRevenue, 0, ',', '.') }}
                </span>
                <p class="text-[10px] text-slate-500 font-semibold mt-0.5">Jika seluruh stok habis terjual</p>
            </div>
        </div>

        <!-- 3. Potensi Laba Kotor Stok -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Estimasi Margin</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Potensi Laba</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $potentialMarginPercent }}%
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-emerald-800 font-mono tracking-tight block">
                    Rp {{ number_format($totalPotentialProfit, 0, ',', '.') }}
                </span>
                <p class="text-[10px] text-slate-500 font-semibold mt-0.5">Estimasi margin laba kotor</p>
            </div>
        </div>

        <!-- 4. Kondisi Fisik Produk -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kondisi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Status Stok</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $totalProductsCount }} Produk
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-xs font-black text-[#163144] font-mono">
                        {{ $totalProductsCount - $totalLowStockCount - $totalOutOfStockCount }} Aman
                    </span>
                    @if($totalLowStockCount > 0)
                        <span class="text-[10px] font-bold text-amber-800 glass-pill-plek px-2 py-0.2 rounded-full font-mono">
                            {{ $totalLowStockCount }} Tipis
                        </span>
                    @endif
                    @if($totalOutOfStockCount > 0)
                        <span class="text-[10px] font-bold text-rose-700 glass-pill-plek px-2 py-0.2 rounded-full font-mono">
                            {{ $totalOutOfStockCount }} Habis
                        </span>
                    @endif
                </div>
                <p class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ number_format($totalStockUnits, 0, ',', '.') }} unit beku</p>
            </div>
        </div>

    </div>

    <!-- Filter Toolbar -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4 space-y-3">
        
        <!-- Stock Status Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0 sm:flex-wrap">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mr-1.5 shrink-0">Status Stok:</span>
            
            <button 
                type="button" 
                wire:click="$set('stockStatus', 'all')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $stockStatus === 'all' ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
            >
                Semua Produk ({{ $totalProductsCount }})
            </button>
            <button 
                type="button" 
                wire:click="$set('stockStatus', 'safe')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $stockStatus === 'safe' ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
            >
                Stok Aman
            </button>
            <button 
                type="button" 
                wire:click="$set('stockStatus', 'low')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $stockStatus === 'low' ? 'bg-amber-600 text-white shadow-md' : 'btn-glass-action' }}"
            >
                Stok Menipis ({{ $totalLowStockCount }})
            </button>
            <button 
                type="button" 
                wire:click="$set('stockStatus', 'out')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $stockStatus === 'out' ? 'bg-rose-600 text-white shadow-md' : 'btn-glass-action' }}"
            >
                Stok Habis ({{ $totalOutOfStockCount }})
            </button>
        </div>

        <!-- Filters & Sorting Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 sm:gap-3 pt-2 border-t border-white/40 items-end">
            
            <!-- Outlet Filter -->
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Filter Cabang Toko</label>
                <select 
                    wire:model.live="outletId" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
                    <option value="">Semua Cabang Toko</option>
                    @foreach($outlets as $ot)
                        <option value="{{ $ot->id }}">{{ $ot->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search Box -->
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Cari Produk / SKU / Barcode</label>
                <div class="relative">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="Ketik nama frozen food, barcode, SKU..." 
                        class="w-full pl-3.5 pr-8 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
                    @endif
                </div>
            </div>

            <!-- Category Filter (Searchable) -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Filter Kategori</label>
                <x-searchable-select 
                    wire:model.live="categoryId" 
                    :options="$categories" 
                    placeholder="Semua Kategori" 
                    empty-option="Semua Kategori" 
                    :threshold="0" 
                />
            </div>

            <!-- Sort By -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Urutkan Berdasarkan</label>
                <select 
                    wire:model.live="sortBy" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
                    <option value="asset_value_desc">Valuasi Modal Tertinggi (Rp)</option>
                    <option value="stock_desc">Jumlah Stok Terbanyak (Unit)</option>
                    <option value="profit_desc">Potensi Laba Tertinggi (Rp)</option>
                    <option value="name_asc">Nama Produk (A - Z)</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Inventory Valuation Table -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[880px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 w-10 text-center">No</th>
                        <th class="p-3.5">Produk Frozen Food</th>
                        <th class="p-3.5 text-center">Stok Fisik</th>
                        <th class="p-3.5 text-right">Modal (HPP)</th>
                        <th class="p-3.5 text-right">Harga Jual</th>
                        <th class="p-3.5 text-right font-black text-[#163144]">Total Modal (HPP)</th>
                        <th class="p-3.5 text-right">Potensi Omzet</th>
                        <th class="p-3.5 pr-5 text-right">Potensi Laba</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($products as $p)
                        @php
                            if ($outletId) {
                                $op = $p->getOutletProduct($outletId);
                                $rowStock = max(0, (int) ($op?->stock ?? 0));
                                $rowCost = max(0, (float) ($op?->cost_price ?: $p->cost_price));
                                $rowSell = max(0, (float) ($op?->selling_price ?: $p->selling_price));
                                $rowAlert = (int) ($op?->min_stock_alert ?: $p->min_stock_alert);
                            } else {
                                $hasOps = $p->outletProducts->isNotEmpty();
                                $rowStock = $hasOps ? max(0, (int) $p->outletProducts->sum('stock')) : max(0, (int) $p->stock);
                                $firstOpCost = $p->outletProducts->firstWhere('cost_price', '>', 0)?->cost_price;
                                $rowCost = max(0, (float) ($p->cost_price ?: ($firstOpCost ?? 0)));
                                $firstOpSell = $p->outletProducts->firstWhere('selling_price', '>', 0)?->selling_price;
                                $rowSell = max(0, (float) ($p->selling_price ?: ($firstOpSell ?? 0)));
                                $rowAlert = (int) $p->min_stock_alert;
                            }
                            $rowAssetCost = $rowStock * $rowCost;
                            $rowRevenue = $rowStock * $rowSell;
                            $rowProfit = max(0, $rowRevenue - $rowAssetCost);
                        @endphp
                        <tr class="hover:bg-white/40 transition">
                            
                            <!-- No -->
                            <td class="p-3.5 pl-5 text-center font-mono text-slate-400 font-bold">
                                {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}
                            </td>

                            <!-- Product Info -->
                            <td class="p-3.5">
                                <span class="font-extrabold text-[#163144] block">{{ $p->name }}</span>
                                <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-semibold mt-0.5">
                                    <span class="text-[#386797]">{{ $p->category->name ?? 'Tanpa Kategori' }}</span>
                                    @if($p->supplier)
                                        <span class="text-slate-300">•</span>
                                        <span class="text-slate-500 font-mono">{{ $p->supplier->name }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Physical Stock & Status -->
                            <td class="p-3.5 text-center">
                                <span class="font-black font-mono text-xs {{ $rowStock === 0 ? 'text-rose-600' : ($rowStock <= $rowAlert ? 'text-amber-800' : 'text-[#163144]') }}">
                                    {{ $rowStock }} {{ $p->unit }}
                                </span>
                                <div class="mt-0.5">
                                    @if($rowStock === 0)
                                        <span class="px-2 py-0.2 rounded-full glass-pill-plek text-rose-700 text-[9px] font-black">Habis</span>
                                    @elseif($rowStock <= $rowAlert)
                                        <span class="px-2 py-0.2 rounded-full glass-pill-plek text-amber-800 text-[9px] font-black">Menipis</span>
                                    @else
                                        <span class="px-2 py-0.2 rounded-full glass-pill-plek text-emerald-800 text-[9px] font-black">Aman</span>
                                    @endif
                                </div>
                            </td>

                            <!-- HPP Modal Satuan -->
                            <td class="p-3.5 text-right font-mono text-slate-600 font-bold">
                                Rp {{ number_format($rowCost, 0, ',', '.') }}
                            </td>

                            <!-- Selling Price Satuan -->
                            <td class="p-3.5 text-right font-mono text-slate-700 font-bold">
                                Rp {{ number_format($rowSell, 0, ',', '.') }}
                            </td>

                            <!-- Valuasi Total Modal (HPP * Stok) -->
                            <td class="p-3.5 text-right font-mono font-black text-[#163144]">
                                Rp {{ number_format($rowAssetCost, 0, ',', '.') }}
                            </td>

                            <!-- Potensi Omzet -->
                            <td class="p-3.5 text-right font-mono text-[#386797] font-black">
                                Rp {{ number_format($rowRevenue, 0, ',', '.') }}
                            </td>

                            <!-- Potensi Laba Kotor -->
                            <td class="p-3.5 pr-5 text-right font-mono text-emerald-800 font-black">
                                +Rp {{ number_format($rowProfit, 0, ',', '.') }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400 font-semibold">
                                Tidak ada data produk yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($products as $p)
                @php
                    if ($outletId) {
                        $op = $p->getOutletProduct($outletId);
                        $rowStock = max(0, (int) ($op?->stock ?? 0));
                        $rowCost = max(0, (float) ($op?->cost_price ?: $p->cost_price));
                        $rowSell = max(0, (float) ($op?->selling_price ?: $p->selling_price));
                        $rowAlert = (int) ($op?->min_stock_alert ?: $p->min_stock_alert);
                    } else {
                        $hasOps = $p->outletProducts->isNotEmpty();
                        $rowStock = $hasOps ? max(0, (int) $p->outletProducts->sum('stock')) : max(0, (int) $p->stock);
                        $firstOpCost = $p->outletProducts->firstWhere('cost_price', '>', 0)?->cost_price;
                        $rowCost = max(0, (float) ($p->cost_price ?: ($firstOpCost ?? 0)));
                        $firstOpSell = $p->outletProducts->firstWhere('selling_price', '>', 0)?->selling_price;
                        $rowSell = max(0, (float) ($p->selling_price ?: ($firstOpSell ?? 0)));
                        $rowAlert = (int) $p->min_stock_alert;
                    }
                    $rowAssetCost = $rowStock * $rowCost;
                    $rowRevenue = $rowStock * $rowSell;
                    $rowProfit = max(0, $rowRevenue - $rowAssetCost);
                @endphp
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <h4 class="font-extrabold text-[#163144] text-sm leading-snug">{{ $p->name }}</h4>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-semibold mt-0.5">
                                <span class="text-[#386797]">{{ $p->category->name ?? 'Tanpa Kategori' }}</span>
                                @if($p->supplier)
                                    <span>•</span>
                                    <span class="font-mono text-slate-400">{{ $p->supplier->name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-black font-mono text-xs block {{ $rowStock === 0 ? 'text-rose-600' : ($rowStock <= $rowAlert ? 'text-amber-800' : 'text-[#163144]') }}">
                                {{ $rowStock }} {{ $p->unit }}
                            </span>
                            @if($rowStock === 0)
                                <span class="px-2 py-0.2 rounded-full glass-pill-plek text-rose-700 text-[9px] font-black inline-block mt-0.5">Habis</span>
                            @elseif($rowStock <= $rowAlert)
                                <span class="px-2 py-0.2 rounded-full glass-pill-plek text-amber-800 text-[9px] font-black inline-block mt-0.5">Menipis</span>
                            @else
                                <span class="px-2 py-0.2 rounded-full glass-pill-plek text-emerald-800 text-[9px] font-black inline-block mt-0.5">Aman</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-1.5 text-[11px] glass-pill-plek p-2.5 rounded-2xl border border-white/60 font-mono text-center">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase font-extrabold font-sans">Valuasi HPP</span>
                            <span class="text-[#163144] font-black">Rp {{ number_format($rowAssetCost, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase font-extrabold font-sans">Potensi Omzet</span>
                            <span class="text-[#386797] font-black">Rp {{ number_format($rowRevenue, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase font-extrabold font-sans">Potensi Laba</span>
                            <span class="text-emerald-800 font-black">+Rp {{ number_format($rowProfit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-semibold">
                    <p class="text-xs text-slate-500">Tidak ada data produk yang sesuai dengan filter.</p>
                </div>
            @endforelse
        </div>

        {{ $products->links() }}
    </div>

</div>
