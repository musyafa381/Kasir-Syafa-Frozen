<div class="space-y-3" x-data="dashboardAnalytics()">

    <!-- Header Toolbar / Sub-Header (Minimalist & Sleek) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-[#163144] tracking-tight">
                {{ auth()->user()->isAdmin() ? 'Ringkasan Eksekutif Toko' : 'Dashboard Operasional Kasir' }}
            </h2>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                {{ auth()->user()->isAdmin() ? 'Statistik penjualan real-time, margin keuntungan, arus kas, dan kontrol inventori.' : 'Aktivitas transaksi penjualan hari ini dan pemantauan batas aman stok barang.' }}
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if(count($outlets) > 0)
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 hidden sm:inline">Outlet:</span>
                    <select 
                        wire:model.live="outletId" 
                        class="px-3 py-1.5 rounded-xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer"
                    >
                        <option value="">Semua Cabang Toko</option>
                        @foreach($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- ========================================================================= -->
    <!-- ROW 1: TOP 4 PREVIEW FLOATING CARDS (Tight Gaps & Matching Curvature) === -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2.5 sm:gap-3.5">
        
        <!-- Card 1: Plus Action Card / Quick POS Launcher (Matches Left '+' Card in Reference) -->
        <a href="{{ route('pos.index') }}" wire:navigate class="glass-card-plek p-3 sm:p-5 rounded-[22px] flex flex-col justify-between group cursor-pointer relative overflow-hidden min-h-[120px] sm:min-h-[135px]">
            <div class="flex items-start justify-between">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-2xl bg-white/60 border border-white/80 flex items-center justify-center text-[#163144] shadow-2xs group-hover:scale-110 group-hover:bg-white/90 transition-all">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </div>
            </div>
            <div>
                <span class="block text-xs sm:text-sm font-black text-[#163144] group-hover:text-[#386797] transition-colors truncate">Buka Kasir POS</span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium truncate">Lakukan transaksi cepat</p>
            </div>
        </a>

        <!-- Card 2: Traffic Overview / Omset Penjualan (Matches 'Traffic Overview' in Reference) -->
        <div class="glass-card-plek ambient-glow p-3 sm:p-5 rounded-[22px] flex flex-col justify-between relative group min-h-[120px] sm:min-h-[135px]">
            <div class="flex items-start justify-between relative z-0">
                <div class="min-w-0">
                    <span class="text-xs sm:text-sm font-black text-[#163144] block tracking-tight truncate">Omset Penjualan</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold truncate block">{{ $periodLabel }}</span>
                </div>
            </div>

            <div class="mt-1 sm:mt-2">
                <p class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($sales, 0, ',', '.') }}
                </p>
                <div class="flex items-center gap-1.5 sm:gap-2 mt-0.5 flex-wrap">
                    <span class="text-[9px] sm:text-[10px] font-bold text-[#386797]">{{ $ordersCount }} Nota</span>
                    @if($salesGrowth !== null)
                        <span class="text-[8px] sm:text-[9px] font-black {{ $salesGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $salesGrowth >= 0 ? '+' : '' }}{{ $salesGrowth }}% vs lalu
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 3: Conversion Rate / Laba Bersih (Matches 'Conversion Rate' in Reference) -->
        <div class="glass-card-plek ambient-glow p-3 sm:p-5 rounded-[22px] flex flex-col justify-between relative group min-h-[120px] sm:min-h-[135px]">

            <div class="flex items-start justify-between relative z-0">
                <div class="min-w-0">
                    <span class="text-xs sm:text-sm font-black text-[#163144] block tracking-tight truncate">Laba Bersih Riil</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold truncate block">Margin {{ $grossMargin }}%</span>
                </div>
            </div>

            <div class="mt-1 sm:mt-2">
                <p class="text-base sm:text-2xl font-black {{ $netProfit >= 0 ? 'text-[#163144]' : 'text-rose-600' }} tracking-tight truncate">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
                <p class="text-[9px] sm:text-[10px] font-semibold text-slate-500 mt-0.5 truncate">
                    OPEX: Rp {{ number_format($expenses, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Card 4: Valuasi Aset Stok (Minimalist Theme SVG Icon) -->
        <div class="glass-card-plek ambient-glow p-3 sm:p-5 rounded-[22px] border border-dashed border-slate-300/80 flex flex-col justify-between relative group min-h-[120px] sm:min-h-[135px]">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <span class="text-xs sm:text-sm font-black text-[#163144] block tracking-tight truncate">Valuasi Stok Toko</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold truncate block">{{ $totalProductsCount }} SKU</span>
                </div>
            </div>

            <div class="mt-1 sm:mt-2">
                <p class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($inventoryValuation, 0, ',', '.') }}
                </p>
                <div class="flex items-center justify-between mt-0.5 flex-wrap gap-1">
                    <span class="text-[9px] sm:text-[10px] font-semibold text-slate-500 truncate">{{ number_format($totalStockUnits, 0, ',', '.') }} Unit</span>
                    @if($totalLowStockCount > 0)
                        <span class="text-[8px] sm:text-[9px] font-black text-rose-600 bg-rose-50 px-1.5 py-0.2 rounded-full">{{ $totalLowStockCount }} Kritis</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- ROW 2: HERO ANALYTICS METRIC CARD WITH DOTTED LINE CHART (Plek-ketiplek) = -->
    <!-- ========================================================================= -->
    <div class="glass-card-plek p-5 sm:p-6 rounded-[24px] space-y-5">
        
        <!-- Metric Header & Filter Pills Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-black text-[#163144] tracking-tight">Tren Penjualan & Performa Toko</h3>
                <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Periode Terpantau: {{ $periodLabel }}</p>
            </div>

            <!-- Filter Pills -->
            <div class="flex items-center gap-1.5 flex-wrap sm:flex-nowrap">
                <!-- Period Switcher Pills -->
                <button 
                    type="button" 
                    wire:click="setPeriod('today')" 
                    class="px-3 py-1.5 rounded-full text-xs font-black transition {{ $period === 'today' ? 'glass-pill-dark' : 'glass-pill-plek text-slate-600' }}"
                >
                    Hari Ini
                </button>
                <button 
                    type="button" 
                    wire:click="setPeriod('last_7_days')" 
                    class="px-3 py-1.5 rounded-full text-xs font-black transition {{ $period === 'last_7_days' ? 'glass-pill-dark' : 'glass-pill-plek text-slate-600' }}"
                >
                    7 Hari
                </button>
                <button 
                    type="button" 
                    wire:click="setPeriod('this_month')" 
                    class="px-3 py-1.5 rounded-full text-xs font-black transition {{ $period === 'this_month' ? 'glass-pill-dark' : 'glass-pill-plek text-slate-600' }}"
                >
                    Bulan Ini
                </button>
                <button 
                    type="button" 
                    wire:click="setPeriod('this_year')" 
                    class="px-3 py-1.5 rounded-full text-xs font-black transition {{ $period === 'this_year' ? 'glass-pill-dark' : 'glass-pill-plek text-slate-600' }}"
                >
                    Tahun Ini
                </button>
            </div>
        </div>

        <!-- Big Metric Number & Chart Split Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            
            <!-- Left: Hero Big Number & Sub-Metrics (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pendapatan (Omset)</span>
                    <p class="text-3xl sm:text-4xl lg:text-[40px] font-black text-[#163144] tracking-tight leading-none mt-1">
                        Rp {{ number_format($sales, 0, ',', '.') }}
                    </p>
                </div>

                <!-- 4 Sub-Metrics Grid -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200/60">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Trx Selesai</span>
                        <p class="text-base font-black text-[#163144] mt-0.5">{{ $ordersCount }} Struk</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Margin Kotor</span>
                        <p class="text-base font-black text-[#386797] mt-0.5">{{ $grossMargin }}%</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Laba Bersih</span>
                        <p class="text-base font-black text-emerald-700 mt-0.5">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Member Trx</span>
                        <p class="text-base font-black text-[#163144] mt-0.5">{{ $memberOrdersCount }} Trx</p>
                    </div>
                </div>
            </div>

            <!-- Right: Minimalist Dotted Line Chart with Floating Tool-Pill (8 Cols) -->
            <div class="lg:col-span-8 relative hatch-chart-container p-3 sm:p-4 rounded-3xl bg-white/40 border border-white/60">

                <div class="h-56 sm:h-64 w-full relative">
                    <canvas 
                        x-ref="trendCanvas"
                        data-labels='@json($chartLabels)'
                        data-omset='@json($chartOmset)'
                        data-profit='@json($chartProfit)'
                    ></canvas>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- ROW 3: GAUGES, TOP SELLERS, AND OPERATIONAL INTELLIGENCE ================ -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-2.5 sm:gap-3">
        
        <!-- Widget 1: Metode Pembayaran Donut Gauge with Bottom Dock Icons -->
        <div class="glass-card-plek p-4 sm:p-5 rounded-[24px] flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-[#163144]">Metode Pembayaran</h4>
                    <p class="text-[10px] text-slate-400 font-semibold">Distribusi penerimaan kas</p>
                </div>
                <span class="text-[10px] font-bold text-slate-400">{{ $periodLabel }}</span>
            </div>

            <div class="h-36 w-full relative flex items-center justify-center my-1">
                <canvas 
                    x-ref="paymentCanvas"
                    data-labels='["Cash", "QRIS", "Transfer", "Tempo"]'
                    data-values='[{{ $paymentDistribution["cash"] }}, {{ $paymentDistribution["qris"] }}, {{ $paymentDistribution["transfer"] }}, {{ $paymentDistribution["tempo"] }}]'
                ></canvas>
            </div>

            <!-- Mini Dock Pill Bar under Gauge (Plek-ketiplek) -->
            <div class="flex items-center justify-center gap-2 pt-2 border-t border-slate-200/60">
                <div class="px-3 py-1 rounded-full glass-pill-plek flex items-center gap-3 text-xs text-slate-600">
                    <span class="inline-flex items-center gap-1 font-bold text-[10px]"><span class="w-2 h-2 rounded-full bg-[#163144]"></span> Cash</span>
                    <span class="inline-flex items-center gap-1 font-bold text-[10px]"><span class="w-2 h-2 rounded-full bg-[#386797]"></span> QRIS</span>
                    <span class="inline-flex items-center gap-1 font-bold text-[10px]"><span class="w-2 h-2 rounded-full bg-[#a0bddb]"></span> Transfer</span>
                    <span class="inline-flex items-center gap-1 font-bold text-[10px]"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Tempo</span>
                </div>
            </div>
        </div>

        <!-- Widget 2: Produk Terlaris (Top 5) with Floating Badge -->
        <div class="glass-card-plek p-4 sm:p-5 rounded-[24px] flex flex-col justify-between relative space-y-3">
            
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-[#163144]">Produk Terlaris</h4>
                    <p class="text-[10px] text-slate-400 font-semibold">Berdasarkan volume terjual</p>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-slate-200/60 text-[#163144] font-bold text-[10px]">Top 5</span>
            </div>

            <div class="flex-1 space-y-2">
                @if($topProducts->isEmpty())
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Belum ada penjualan di periode ini.
                    </div>
                @else
                    @php $maxQty = $topProducts->first()?->total_qty ?: 1; @endphp
                    @foreach($topProducts as $idx => $tp)
                        <div class="p-2 rounded-xl bg-white/60 border border-white/80 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-5 h-5 rounded-full {{ $idx === 0 ? 'bg-[#163144] text-white' : 'bg-[#a0bddb]/30 text-[#163144]' }} font-black text-[10px] flex items-center justify-center shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h5 class="font-extrabold text-xs text-[#163144] truncate">{{ $tp->product_name }}</h5>
                                    <p class="text-[9px] text-slate-400 truncate">{{ $tp->product?->category?->name ?? 'Produk' }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-xs text-[#163144] block">{{ $tp->total_qty }} {{ $tp->product?->unit ?? 'pcs' }}</span>
                                <span class="text-[9px] text-slate-400">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-bold text-slate-500">
                <span>Total SKU Aktif: {{ $totalProductsCount }}</span>
                <a href="{{ route('admin.products.index') }}" wire:navigate class="text-[#386797] hover:underline">Katalog</a>
            </div>
        </div>

        <!-- Widget 3: Peringatan Stok Rendah & Piutang Jatuh Tempo -->
        <div class="glass-card-plek p-4 sm:p-5 rounded-[24px] flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-black text-[#163144]">Peringatan Operasional</h4>
                    <p class="text-[10px] text-slate-400 font-semibold">Batas restock & piutang tempo</p>
                </div>
                <a href="{{ route('admin.purchases.restock') }}" wire:navigate class="text-xs font-black text-[#386797] hover:underline">Kulakan</a>
            </div>

            <div class="space-y-2 flex-1">
                <!-- Low Stock Items -->
                @if($lowStockProducts->isEmpty())
                    <div class="p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-200/60 flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <div class="text-xs">
                            <span class="font-bold text-emerald-900 block">Stok Aman</span>
                            <span class="text-[10px] text-emerald-700">Tidak ada produk di bawah batas minimum</span>
                        </div>
                    </div>
                @else
                    @foreach($lowStockProducts->take(2) as $lowItem)
                        <div class="p-2 rounded-xl bg-rose-50/70 border border-rose-200/60 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <h5 class="font-bold text-xs text-[#163144] truncate">{{ $lowItem->name }}</h5>
                                <span class="text-[9px] text-rose-600 font-bold">Min: {{ $lowItem->min_stock_alert }} {{ $lowItem->unit }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-rose-200 text-rose-900 font-black text-[10px]">
                                Sisa {{ $lowItem->stock }}
                            </span>
                        </div>
                    @endforeach
                @endif

                <!-- Overdue Receivables -->
                @if($overdueReceivables->isEmpty())
                    <div class="p-2.5 rounded-xl bg-white/60 border border-white flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <div class="text-xs">
                            <span class="font-bold text-[#163144] block">Piutang Lancar</span>
                            <span class="text-[10px] text-slate-500">Semua tempo berjalan tertib</span>
                        </div>
                    </div>
                @else
                    @foreach($overdueReceivables->take(2) as $overdueTrx)
                        <div class="p-2 rounded-xl bg-amber-50/70 border border-amber-200/60 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <h5 class="font-bold text-xs text-[#163144] truncate">{{ $overdueTrx->customer_name }}</h5>
                                <span class="text-[9px] text-amber-800 font-bold">Tempo Lewat</span>
                            </div>
                            <span class="font-black text-xs text-rose-700">
                                Rp {{ number_format($overdueTrx->remaining_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-bold text-slate-500">
                <span>Piutang Total: Rp {{ number_format($totalReceivables, 0, ',', '.') }}</span>
                <a href="{{ route('admin.receivables.index') }}" wire:navigate class="text-[#386797] hover:underline">Semua</a>
            </div>
        </div>

    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ROW 4: TRANSAKSI TERKINI TABLE (Glassmorphic Table) ===================== -->
    <!-- ========================================================================= -->
    <div class="glass-card-plek p-4 sm:p-6 rounded-[24px] space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-[#163144]">Aktivitas Transaksi Terkini</h3>
                <p class="text-[11px] text-slate-400 font-semibold">Struk dan nota penjualan terbaru yang selesai diproses</p>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.transactions.index') }}" wire:navigate class="text-xs font-black text-[#386797] hover:underline flex items-center gap-1">
                <span>Lihat Semua Riwayat</span>
            </a>
            @endif
        </div>

        @if($recentTransactions->isEmpty())
            <div class="text-center py-10 text-slate-400 text-xs">
                Belum ada transaksi pada cabang atau periode ini.
            </div>
        @else
            <!-- Desktop Frosted Table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[700px]">
                    <thead>
                        <tr class="border-b border-slate-200/80 text-[10px] uppercase font-black tracking-wider text-slate-400">
                            <th class="py-3 px-4">No. Invoice</th>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Kasir</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Total Belanja</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold">
                        @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-white/80 transition group">
                                <td class="py-3.5 px-4 font-mono font-black text-[#163144]">
                                    {{ $tx->invoice_number }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                    {{ $tx->created_at->translatedFormat('d M H:i') }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    {{ $tx->user->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-[#163144]">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $tx->customer_name }}</span>
                                        @if($tx->is_loyalty)
                                            <span class="px-2 py-0.2 rounded-full text-[9px] font-black bg-[#a0bddb] text-[#163144]">Member</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] uppercase font-black glass-pill-plek text-[#163144] font-mono">
                                        {{ $tx->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($tx->payment_status === 'paid')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100/80 text-emerald-800">
                                            Lunas
                                        </span>
                                    @elseif($tx->payment_status === 'partial')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100/80 text-amber-800">
                                            DP Sebagian
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100/80 text-rose-800">
                                            Tempo
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-[#163144] font-mono text-sm">
                                    Rp {{ number_format($tx->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button 
                                        type="button"
                                        wire:click="openTransactionDetail({{ $tx->id }})"
                                        class="btn-glass-action"
                                    >
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-white/40">
                @foreach($recentTransactions as $tx)
                    <div class="p-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono font-black text-[#163144] text-xs block">{{ $tx->invoice_number }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">{{ $tx->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            @if($tx->payment_status === 'paid')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100/80 text-emerald-800">Lunas</span>
                            @elseif($tx->payment_status === 'partial')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100/80 text-amber-800">DP Sebagian</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100/80 text-rose-800">Tempo</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <div class="min-w-0">
                                <span class="text-slate-800 font-bold block truncate">{{ $tx->customer_name }}</span>
                                <span class="text-[10px] text-slate-400">Kasir: {{ $tx->user->name ?? '-' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-[#163144] font-mono text-sm block">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</span>
                                <span class="text-[9px] uppercase font-bold glass-pill-plek px-2 py-0.5 rounded-full text-[#163144]">{{ $tx->payment_method }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pt-1">
                            <button 
                                type="button"
                                wire:click="openTransactionDetail({{ $tx->id }})"
                                class="btn-glass-action"
                            >
                                Lihat Struk
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Detail Transaksi Modal (Luxury Frosted Glass Modal) -->
    @if($showDetailModal && $selectedTransaction)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-white/90 relative my-auto modal-content-animate space-y-4">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">Detail Nota Transaksi</h3>
                            <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[11px] font-black tracking-wide">
                                {{ $selectedTransaction->invoice_number }}
                            </span>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="closeTransactionDetail" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Transaction Info Grid -->
                <div class="grid grid-cols-2 gap-2.5 p-3.5 bg-slate-50/90 rounded-2xl border border-slate-100/80 shadow-2xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] font-extrabold uppercase tracking-wider">WAKTU</span>
                        <span class="font-bold text-[#163144] text-xs">{{ $selectedTransaction->created_at->translatedFormat('d F Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-extrabold uppercase tracking-wider">KASIR</span>
                        <span class="font-bold text-[#163144] text-xs">{{ $selectedTransaction->user->name ?? 'Kasir 1' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-extrabold uppercase tracking-wider">PELANGGAN</span>
                        <span class="font-bold text-[#163144] text-xs">{{ $selectedTransaction->customer_name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-extrabold uppercase tracking-wider">METODE / STATUS</span>
                        <span class="inline-flex items-center gap-1.5 font-black uppercase text-xs {{ $selectedTransaction->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-700' }}">
                            <span>{{ $selectedTransaction->payment_method }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $selectedTransaction->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ strtoupper($selectedTransaction->payment_status) }}
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Items Table Card -->
                <div class="border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs bg-white">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/80 text-slate-600 text-[10px] uppercase font-black border-b border-slate-200/60">
                            <tr>
                                <th class="py-2.5 px-3">Produk</th>
                                <th class="py-2.5 px-3 text-center">Qty</th>
                                <th class="py-2.5 px-3 text-right">Harga</th>
                                <th class="py-2.5 px-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                            @foreach($selectedTransaction->items as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-2.5 px-3 font-bold text-[#163144]">{{ $item->product_name }}</td>
                                    <td class="py-2.5 px-3 text-center font-mono">{{ $item->formatted_quantity }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-slate-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold font-mono text-[#163144]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50/90 font-black border-t border-slate-200">
                            <tr>
                                <td colspan="3" class="py-2 px-3 text-right text-slate-600 font-extrabold text-xs">Grand Total</td>
                                <td class="py-2 px-3 text-right font-mono text-[#163144] text-sm">Rp {{ number_format($selectedTransaction->grand_total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-1.5 px-3 text-right text-slate-500 font-medium text-[11px]">Dibayar</td>
                                <td class="py-1.5 px-3 text-right font-mono text-slate-700 text-xs">Rp {{ number_format($selectedTransaction->paid_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-1.5 px-3 text-right text-slate-500 font-medium text-[11px]">Kembalian</td>
                                <td class="py-1.5 px-3 text-right font-mono text-slate-700 text-xs">Rp {{ number_format($selectedTransaction->change_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Modal Footer -->
                <div class="pt-2 flex items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        wire:click="closeTransactionDetail"
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>
                    @if(auth()->user()->isAdmin())
                    <a 
                        href="{{ route('admin.transactions.index') }}" 
                        wire:navigate
                        class="px-5 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs transition active:scale-95 hover:scale-105 inline-flex items-center gap-1.5 shadow-md shadow-[#163144]/20"
                    >
                        <span>Buka di Riwayat</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
