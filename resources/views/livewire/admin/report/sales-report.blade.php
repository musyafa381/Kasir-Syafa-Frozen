<div class="space-y-3" x-data="salesReportCharts()">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Laporan Penjualan & Performa Kasir</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                Analisis transaksi kasir, total omset kotor & bersih, HPP modal produk, dan laba kotor penjualan dagang.
            </p>
        </div>
        
        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.print.sales.report', ['start_date' => $startDate, 'end_date' => $endDate, 'payment_method' => $paymentMethod, 'cashier_id' => $cashierId]) }}" 
                target="_blank" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak PDF</span>
            </a>

            <a 
                href="{{ route('admin.print.sales.excel', ['start_date' => $startDate, 'end_date' => $endDate, 'payment_method' => $paymentMethod, 'cashier_id' => $cashierId]) }}" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Ekspor Excel</span>
            </a>

            <a 
                href="{{ route('admin.reports.profit-loss') }}" 
                wire:navigate 
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#163144] hover:bg-[#244262] text-white font-extrabold text-xs shadow-xs transition cursor-pointer"
            >
                <span>Lihat Laba Rugi</span>
            </a>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="glass-card-plek rounded-[24px] p-4 space-y-3">
        
        <!-- Period Quick Presets -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0 sm:flex-wrap">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1 shrink-0">Periode:</span>
            
            @php
                $presets = [
                    'today' => 'Hari Ini',
                    'yesterday' => 'Kemarin',
                    'last_7_days' => '7 Hari Terakhir',
                    'this_month' => 'Bulan Ini',
                    'last_month' => 'Bulan Lalu',
                    'this_year' => 'Tahun Ini',
                    'custom' => 'Kustom',
                ];
            @endphp

            @foreach($presets as $key => $label)
                <button 
                    type="button" 
                    wire:click="setPeriod('{{ $key }}')" 
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === $key ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Custom Date Range & Select Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3 pt-2 border-t border-white/40">
            
            <!-- Start Date -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input 
                    wire:model.live="startDate" 
                    type="date" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition cursor-pointer"
                >
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input 
                    wire:model.live="endDate" 
                    type="date" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition cursor-pointer"
                >
            </div>

            <!-- Cabang Toko -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cabang Toko</label>
                <select 
                    wire:model.live="outletId" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer transition"
                >
                    <option value="">Semua Cabang Toko</option>
                    @foreach($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name }} {{ $o->is_main ? '(Pusat)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kasir / User (Searchable Dropdown) -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Filter Kasir</label>
                <x-searchable-select 
                    wire:model.live="cashierId" 
                    :options="$cashiers" 
                    placeholder="Semua Kasir" 
                    empty-option="Semua Kasir" 
                />
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Metode Bayar</label>
                <select 
                    wire:model.live="paymentMethod" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer transition"
                >
                    <option value="">Semua Metode Bayar</option>
                    <option value="cash">Tunai (Cash)</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>

        </div>
    </div>

    <!-- 4 EXECUTIVE FINANCIAL KPI CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- Total Omset Penjualan Bersih -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Omset</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Penjualan</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $totalTransactionsCount }} Trx
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($netSales, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Omset Kotor:</span>
                    <span class="font-bold text-[#163144]">Rp {{ number_format($grossSales, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        <!-- Total HPP (Modal Barang Terjual) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Beban Pokok</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">HPP Modal</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $netSales > 0 ? round(($totalHpp / $netSales) * 100, 1) : 0 }}%
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalHpp, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Fisik Terjual:</span>
                    <span class="font-bold text-[#163144]">{{ number_format($totalUnitsSold, 0, ',', '.') }} unit</span>
                </p>
            </div>
        </div>

        <!-- Laba Bersih Penjualan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Margin Profit</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Laba Kotor Dagang</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $profitMargin }}% Margin
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Keuntungan:</span>
                    <span class="font-bold text-[#163144]">Omset - HPP</span>
                </p>
            </div>
        </div>

        <!-- Rata-rata Nilai Belanja (AOV) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Keranjang</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Rata-rata Belanja</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    AOV
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($averageOrderValue, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Rata-rata Struk:</span>
                    <span class="font-bold text-[#163144]">Per Transaksi</span>
                </p>
            </div>
        </div>

    </div>

    <!-- Charts Section (Tren Penjualan & Komposisi Metode Bayar) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        
        <!-- Chart 1: Tren Omset & Laba Bersih (2 Cols) -->
        <div class="lg:col-span-2 glass-card-plek rounded-[24px] p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 mb-2 border-b border-white/40">
                <div>
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">Grafik Tren Omset & Laba Bersih</h3>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Pergerakan arus kas harian selama periode terpilih</p>
                </div>
            </div>

            <div class="relative h-64 sm:h-72 w-full mt-2" wire:ignore>
                <canvas 
                    x-ref="trendCanvas"
                    data-labels="{{ json_encode($trendLabels) }}"
                    data-omset="{{ json_encode($trendOmset) }}"
                    data-profit="{{ json_encode($trendProfit) }}"
                ></canvas>
            </div>
        </div>

        <!-- Chart 2: Komposisi Metode Pembayaran (1 Col) -->
        <div class="glass-card-plek rounded-[24px] p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 mb-2 border-b border-white/40">
                <div>
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">Metode Pembayaran</h3>
                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Distribusi kas masuk per channel</p>
                </div>
            </div>

            <div class="relative h-44 w-full flex items-center justify-center my-auto" wire:ignore>
                <canvas 
                    x-ref="paymentCanvas"
                    data-labels="{{ json_encode($paymentChartData['labels']) }}"
                    data-values="{{ json_encode($paymentChartData['data']) }}"
                ></canvas>
            </div>

            <!-- Breakdown Table -->
            <div class="pt-3 border-t border-white/40 space-y-1.5 text-xs font-semibold">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Tunai (Cash):</span>
                    <span class="font-black text-[#163144]">Rp {{ number_format($cashTotal, 0, ',', '.') }} ({{ $cashCount }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">QRIS:</span>
                    <span class="font-black text-[#163144]">Rp {{ number_format($qrisTotal, 0, ',', '.') }} ({{ $qrisCount }})</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Transfer Bank:</span>
                    <span class="font-black text-[#163144]">Rp {{ number_format($transferTotal, 0, ',', '.') }} ({{ $transferCount }})</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Row 2: Top 5 Best Selling Products (Chart & Table) -->
    <div class="glass-card-plek rounded-[24px] p-4 sm:p-5">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-white/40">
            <div>
                <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">Top 5 Produk Terlaris (Best Seller)</h3>
                <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Varian frozen food dengan volume penjualan tertinggi di periode ini</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            
            <!-- Horizontal Bar Chart -->
            <div class="relative h-56 w-full" wire:ignore>
                <canvas 
                    x-ref="topProductCanvas"
                    data-labels="{{ json_encode($topProductLabels) }}"
                    data-qty="{{ json_encode($topProductQty) }}"
                ></canvas>
            </div>

            <!-- List Details -->
            <div class="divide-y divide-white/30">
                @forelse($topProducts as $idx => $tp)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-full bg-[#163144] text-white font-black text-[10px] flex items-center justify-center shrink-0">
                                #{{ $idx + 1 }}
                            </span>
                            <div class="truncate">
                                <span class="font-black text-[#163144] block truncate">{{ $tp->product_name }}</span>
                                <span class="text-[10px] text-slate-500 font-bold">Terjual: {{ number_format($tp->total_qty, 0, ',', '.') }} unit</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-black text-[#163144] block">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold">HPP: Rp {{ number_format($tp->total_cogs, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400 font-semibold">
                        Belum ada data penjualan pada periode tanggal ini.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <!-- Rincian Riwayat Transaksi Table -->
    <div class="space-y-3">
        
        <!-- Search & Table Header -->
        <div class="glass-card-plek rounded-[24px] p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari no invoice atau nama pelanggan..." 
                    class="w-full px-4 py-2 rounded-2xl glass-pill-plek text-xs font-semibold text-[#163144] placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
                @endif
            </div>

            <span class="text-xs text-slate-500 font-bold">
                Menemukan <strong class="text-[#163144]">{{ $transactions->total() }}</strong> transaksi
            </span>
        </div>

        <!-- Transactions Table & Mobile Cards -->
        <div class="glass-card-plek rounded-[24px] overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[820px]">
                    <thead class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                        <tr>
                            <th class="p-3.5 pl-5 whitespace-nowrap">Invoice & Waktu</th>
                            <th class="p-3.5 whitespace-nowrap">Kasir</th>
                            <th class="p-3.5 whitespace-nowrap">Pelanggan</th>
                            <th class="p-3.5 whitespace-nowrap">Metode Bayar</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Omset</th>
                            <th class="p-3.5 text-right whitespace-nowrap">HPP Modal</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Laba Bersih</th>
                            <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/30 font-semibold">
                        @forelse($transactions as $trx)
                            @php
                                $trxHpp = 0;
                                foreach($trx->items as $it) {
                                    $trxHpp += ($it->cost_price * $it->quantity);
                                }
                                $trxProfit = $trx->grand_total - $trxHpp;
                            @endphp
                            <tr class="hover:bg-white/40 transition">
                                
                                <!-- Invoice & Waktu -->
                                <td class="p-3.5 pl-5">
                                    <span class="font-black text-[#163144] block">{{ $trx->invoice_number }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">{{ $trx->created_at->format('d/m/Y H:i') }}</span>
                                </td>

                                <!-- Kasir -->
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="font-bold text-[#163144]">{{ $trx->user->name ?? 'Kasir' }}</span>
                                </td>

                                <!-- Pelanggan -->
                                <td class="p-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-[#163144]">{{ $trx->customer_name }}</span>
                                        @if($trx->is_loyalty)
                                            <span class="text-[9px] font-extrabold glass-pill-plek text-[#163144] px-1.5 py-0.2 rounded-full">Member</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Metode Bayar -->
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[10px]">
                                        {{ $trx->payment_method === 'cash' ? 'Tunai' : ($trx->payment_method === 'qris' ? 'QRIS' : 'Transfer') }}
                                    </span>
                                </td>

                                <!-- Total Omset -->
                                <td class="p-3.5 text-right font-black text-[#163144] whitespace-nowrap">
                                    Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                </td>

                                <!-- HPP Modal -->
                                <td class="p-3.5 text-right font-bold text-slate-500 whitespace-nowrap">
                                    Rp {{ number_format($trxHpp, 0, ',', '.') }}
                                </td>

                                <!-- Laba Bersih -->
                                <td class="p-3.5 text-right font-black text-[#163144] whitespace-nowrap">
                                    +Rp {{ number_format($trxProfit, 0, ',', '.') }}
                                </td>

                                <!-- Aksi -->
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    <button 
                                        wire:click="openDetail({{ $trx->id }})" 
                                        class="btn-glass-action inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Rincian</span>
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-slate-400 font-semibold">
                                    Tidak ada transaksi yang cocok dengan filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card List View -->
            <div class="sm:hidden divide-y divide-white/30">
                @forelse($transactions as $trx)
                    @php
                        $trxHpp = 0;
                        foreach($trx->items as $it) {
                            $trxHpp += ($it->cost_price * $it->quantity);
                        }
                        $trxProfit = $trx->grand_total - $trxHpp;
                    @endphp
                    <div class="p-3.5 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-[#163144] text-[10px] glass-pill-plek px-2 py-0.5 rounded-full inline-block mb-1">
                                    {{ $trx->invoice_number }}
                                </span>
                                <h4 class="font-black text-[#163144] text-sm leading-snug">{{ $trx->customer_name }}</h4>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1 text-[11px] text-slate-500 font-semibold">
                                    <span>{{ $trx->user->name ?? 'Kasir' }}</span>
                                    <span>•</span>
                                    <span>{{ $trx->created_at->format('d/m/Y H:i') }}</span>
                                    <span>•</span>
                                    <span class="font-extrabold uppercase text-[9px] px-2 py-0.5 rounded-full glass-pill-plek text-[#163144]">{{ $trx->payment_method }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Total Omset</span>
                                <span class="font-black text-[#163144] text-sm block">
                                    Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-[#163144] font-black block mt-0.5">
                                    Laba: +Rp {{ number_format($trxProfit, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-1 border-t border-white/40">
                            <button 
                                wire:click="openDetail({{ $trx->id }})" 
                                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-extrabold"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Lihat Rincian & Nota</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 font-semibold">
                        <p class="text-xs font-bold text-[#163144]">Tidak ada transaksi yang cocok</p>
                    </div>
                @endforelse
            </div>

            @if($transactions->hasPages())
                <div class="p-3 border-t border-white/40">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Detail Invoice Transaksi -->
    @if($showDetailModal && $selectedTransaction)
        @php
            $receiptSetting = \App\Models\ReceiptSetting::getSettings();
            $receiptPaperSize = $receiptSetting->default_paper_size ?? '58mm';
            $paperWidthClass = $receiptPaperSize === '80mm' ? 'w-[320px]' : 'w-[260px]';
            $mHpp = 0;
            foreach($selectedTransaction->items as $it) {
                $mHpp += ($it->cost_price * $it->quantity);
            }
            $mProfit = $selectedTransaction->grand_total - $mHpp;
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm sm:max-w-md w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[92vh] relative my-auto modal-content-animate">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3 min-w-0 pr-2">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-sm sm:text-base text-[#163144] truncate">Nota Digital</h3>
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black shrink-0">{{ $selectedTransaction->invoice_number }}</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium truncate">{{ $selectedTransaction->created_at->translatedFormat('d F Y – H:i') }} WIB</p>
                        </div>
                    </div>
                    <button wire:click="closeDetail" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black shrink-0">✕</button>
                </div>

                <!-- Laba & HPP Info Badge (Khusus Sales Report / Admin) -->
                <div class="px-6 py-3 bg-[#a0bddb]/15 border-b border-slate-100/80 flex items-center justify-between text-xs text-[#163144] shrink-0">
                    <div>
                        <span class="text-slate-500 text-[10px] uppercase font-black tracking-wider block">Modal HPP</span>
                        <span class="font-mono font-black text-[#163144] text-xs">Rp {{ number_format($mHpp, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-emerald-700 text-[10px] uppercase font-black tracking-wider block">Laba Bersih</span>
                        <span class="font-mono font-black text-emerald-800 text-xs">+Rp {{ number_format($mProfit, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Printable Receipt Content Scroll Area -->
                <div class="p-4 overflow-y-auto bg-slate-50/70 flex-1 flex justify-center">
                    
                    <div 
                        id="sales-receipt-printable" 
                        class="bg-white text-black text-xs font-mono shadow-md rounded-2xl p-4 transition-all {{ $paperWidthClass }}"
                        style="line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Courier New', Courier;"
                    >
                        <!-- Header Toko -->
                        <div class="text-center mb-2">
                            <h3 class="text-sm sm:text-base font-normal tracking-tight text-black mb-1 leading-tight">
                                {{ $receiptSetting->store_name ?: 'Syafa frozen food' }}
                            </h3>
                            @if($receiptSetting->store_address)
                                <p class="text-[11px] leading-tight text-neutral-800 px-1">
                                    {{ $receiptSetting->store_address }}
                                </p>
                            @endif
                            @if($receiptSetting->store_phone)
                                <p class="text-[11px] text-neutral-800 mt-0.5">
                                    No.Tlp {{ $receiptSetting->store_phone }}
                                </p>
                            @endif
                            @if($receiptSetting->header_notes)
                                <p class="text-[11px] text-neutral-800 mt-0.5">
                                    {{ $receiptSetting->header_notes }}
                                </p>
                            @endif
                        </div>

                        <!-- Divider 1 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Info Row: Tanggal, Kasir & No. Invoice -->
                        <div class="text-[11px] text-neutral-900 flex justify-between items-start leading-tight">
                            <div>
                                <div>{{ $selectedTransaction->created_at->format('Y-m-d') }}</div>
                                <div>{{ $selectedTransaction->created_at->format('H:i:s') }}</div>
                            </div>
                            <div class="text-right">
                                @if($receiptSetting->show_cashier_name)
                                    <div>Kasir : {{ $selectedTransaction->user->name ?? 'Kasir' }}</div>
                                @endif
                                <div>{{ $selectedTransaction->invoice_number }}</div>
                            </div>
                        </div>

                        @if($receiptSetting->show_customer_name && $selectedTransaction->customer_name)
                            <div class="text-[11px] text-neutral-900 mt-1">
                                Pelanggan : {{ $selectedTransaction->customer_name }} {{ $selectedTransaction->is_loyalty ? '(Member)' : '' }}
                            </div>
                        @endif

                        <!-- Divider 2 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Item List -->
                        <div class="space-y-2 text-[11px] text-neutral-900">
                            @foreach($selectedTransaction->items as $item)
                                <div>
                                    <div class="font-bold uppercase">{{ $item->product_name }}</div>
                                    <div class="flex justify-between items-center text-neutral-800">
                                        <span>{{ $item->quantity }} &nbsp;x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Divider 3 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Totals Calculation -->
                        <div class="space-y-1 text-[11px] text-neutral-900">
                            <div class="flex justify-between">
                                <span>Total QTY : {{ $selectedTransaction->items->sum('quantity') }}</span>
                                <span></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sub Total</span>
                                <span>Rp {{ number_format($selectedTransaction->subtotal, 0, ',', '.') }}</span>
                            </div>

                            @if($selectedTransaction->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span>Diskon{{ $selectedTransaction->discount ? ' (' . $selectedTransaction->discount->name . ')' : '' }}</span>
                                    <span>- Rp {{ number_format($selectedTransaction->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            @if($selectedTransaction->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span>Pajak{{ $selectedTransaction->tax ? ' (' . $selectedTransaction->tax->name . ')' : ($selectedTransaction->tax_rate > 0 ? ' (' . number_format($selectedTransaction->tax_rate, 0) . '%)' : '') }}</span>
                                    <span>+ Rp {{ number_format($selectedTransaction->tax_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between font-bold text-xs pt-1">
                                <span>Total</span>
                                <span>Rp {{ number_format($selectedTransaction->grand_total, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between pt-0.5">
                                <span>Bayar ({{ ucfirst($selectedTransaction->payment_method) }})</span>
                                <span>Rp {{ number_format($selectedTransaction->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kembalian</span>
                                <span>Rp {{ number_format($selectedTransaction->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Divider 4 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Footer Struk -->
                        <div class="text-center text-[10px] text-neutral-800 space-y-1 pt-0.5">
                            @if($receiptSetting->footer_link)
                                <div>Link Kritik dan Saran:</div>
                                <div class="font-normal break-all">{{ $receiptSetting->footer_link }}</div>
                            @endif

                            @if($receiptSetting->footer_notes)
                                <div class="pt-0.5 leading-tight">{{ $receiptSetting->footer_notes }}</div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- WhatsApp & Print Toolbar Footer -->
                <div class="p-4 px-6 bg-white/80 border-t border-slate-100 space-y-3 shrink-0 rounded-b-[32px]">
                    
                    <!-- WhatsApp Image Share Box -->
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <input 
                                wire:model="waPhoneNumber" 
                                type="text" 
                                placeholder="No. WA (081234...)" 
                                class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-xs font-semibold outline-none focus:ring-2 focus:ring-[#a0bddb]"
                            >
                            <button 
                                type="button" 
                                onclick="sendReceiptToWhatsAppImage('sales-receipt-printable', '{{ $waPhoneNumber }}', '{{ $selectedTransaction->invoice_number }}')"
                                class="px-5 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-950/20 transition active:scale-95 hover:scale-105 inline-flex items-center gap-1 cursor-pointer shrink-0"
                            >
                                <span>Kirim WA</span>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-200/60 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button"
                                onclick="downloadReceiptImage('sales-receipt-printable', '{{ $selectedTransaction->invoice_number }}')"
                                class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                            >
                                Unduh PNG
                            </button>
                            <button 
                                type="button"
                                onclick="navigator.clipboard.writeText({{ json_encode($this->generateWhatsAppReceiptText($selectedTransaction)) }}); alert('Teks nota berhasil disalin!');"
                                class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                            >
                                Salin Teks
                            </button>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button" 
                                @click="printThermalReceipt('sales-receipt-printable', '{{ $receiptPaperSize }}')" 
                                class="px-5 py-2 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer flex items-center gap-1"
                            >
                                <span>Cetak Struk</span>
                            </button>
                            <button 
                                type="button" 
                                wire:click="closeDetail" 
                                class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
