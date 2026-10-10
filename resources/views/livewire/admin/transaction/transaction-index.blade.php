<div class="space-y-3">

    <!-- Page Header & Action Bar with Breadcrumb -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-2 sm:pb-3">
        <div class="min-w-0">
            <!-- Quick Back Button if in drill-down level -->
            @if($viewLevel !== 'yearly')
                <div class="mb-1.5">
                    <button 
                        type="button" 
                        @if($viewLevel === 'transactions') wire:click="goToDaily({{ $selectedYear }}, {{ $selectedMonth }})"
                        @elseif($viewLevel === 'daily') wire:click="goToMonthly({{ $selectedYear }})"
                        @elseif($viewLevel === 'monthly') wire:click="goToYearly"
                        @endif
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full glass-pill-plek text-[11px] font-bold text-[#163144] hover:bg-white transition cursor-pointer shadow-2xs"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>
                            @if($viewLevel === 'transactions') Kembali ke Laporan Harian
                            @elseif($viewLevel === 'daily') Kembali ke Laporan Bulanan
                            @elseif($viewLevel === 'monthly') Kembali ke Laporan Tahunan
                            @endif
                        </span>
                    </button>
                </div>
            @endif

            <!-- Breadcrumb Navigation (Scrollable on Mobile, No Awkward Wrap) -->
            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 mb-1 overflow-x-auto no-scrollbar whitespace-nowrap py-0.5">
                <button 
                    type="button" 
                    wire:click="goToYearly" 
                    class="hover:text-[#163144] transition cursor-pointer shrink-0 {{ $viewLevel === 'yearly' ? 'text-[#163144] font-bold' : 'text-slate-500' }}"
                >
                    <span>Laporan Transaksi</span>
                </button>

                @if($viewLevel !== 'yearly')
                    <span class="text-slate-400 shrink-0">/</span>
                    <button 
                        type="button" 
                        wire:click="goToMonthly({{ $selectedYear }})" 
                        class="hover:text-[#163144] transition cursor-pointer shrink-0 {{ $viewLevel === 'monthly' ? 'text-[#163144] font-bold' : 'text-slate-500' }}"
                    >
                        Tahun {{ $selectedYear }}
                    </button>
                @endif

                @if($viewLevel === 'daily' || $viewLevel === 'transactions')
                    <span class="text-slate-400 shrink-0">/</span>
                    <button 
                        type="button" 
                        wire:click="goToDaily({{ $selectedYear }}, {{ $selectedMonth }})" 
                        class="hover:text-[#163144] transition cursor-pointer shrink-0 {{ $viewLevel === 'daily' ? 'text-[#163144] font-bold' : 'text-slate-500' }}"
                    >
                        Bulan {{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F') }}
                    </button>
                @endif

                @if($viewLevel === 'transactions')
                    <span class="text-slate-400 shrink-0">/</span>
                    <span class="text-[#163144] font-bold shrink-0">
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') }}
                    </span>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-lg sm:text-xl font-black text-[#163144] tracking-tight leading-tight">
                    @if($viewLevel === 'yearly')
                        Laporan Transaksi Tahunan
                    @elseif($viewLevel === 'monthly')
                        Laporan Transaksi Bulanan ({{ $selectedYear }})
                    @elseif($viewLevel === 'daily')
                        Laporan Transaksi Harian ({{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }})
                    @else
                        Riwayat Struk Transaksi
                    @endif
                </h1>

                @if($viewLevel === 'transactions')
                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek font-black text-[#163144] font-mono text-[11px] shadow-2xs">
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                    </span>
                @endif
            </div>

            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-1 leading-normal line-clamp-2 sm:line-clamp-none">
                Pantau grafik performa penjualan, keuntungan kotor, rincian nota, dan kelola retur pengembalian barang.
            </p>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
            <!-- Global Struk Search Input -->
            <div class="relative w-full sm:w-64">
                <input 
                    wire:model.live.debounce.400ms="searchInvoice" 
                    type="text" 
                    placeholder="Cari ID Struk (INV-...)" 
                    class="w-full pl-9 pr-8 py-2 rounded-2xl glass-pill-plek text-xs font-semibold text-[#163144] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#a0bddb] transition shadow-2xs"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                @if($searchInvoice)
                    <button wire:click="resetSearch" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs cursor-pointer">✕</button>
                @endif
            </div>
        </div>
    </div>

    <!-- TOP EXECUTIVE KPI STATS CARDS (All Views) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- 1. Volume Transaksi (Nota) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Volume</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Transaksi</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $viewLevel === 'yearly' ? 'Tahunan' : ($viewLevel === 'monthly' ? 'Tahun ' . $selectedYear : ($viewLevel === 'daily' ? 'Bulan Ini' : 'Hari Ini')) }}
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($stats->total_transactions) }} <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase">Nota</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Aktif:</span>
                    <span class="font-bold text-[#163144]">{{ $stats->active_periods }} Periode</span>
                </p>
            </div>
        </div>

        <!-- 2. Total Omset Penjualan (Kotor) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#386797] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Arus Kas</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Omset Kotor</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Gross
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($stats->total_revenue, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>AOV / Nota:</span>
                    <span class="font-bold text-[#163144]">Rp {{ number_format($stats->aov, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        <!-- 3. Estimasi Laba Bersih & Margin -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Keuntungan</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Laba Kotor</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $stats->profit_margin }}%
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($stats->total_profit, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>HPP:</span>
                    <span class="font-bold text-[#163144]">Rp {{ number_format($stats->total_cost, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        <!-- 4. Puncak Performa & Efisiensi -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Performa</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Puncak</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Puncak
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-xs sm:text-base font-black text-[#163144] truncate">
                    {{ $stats->best_label }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Omset:</span>
                    <span class="font-bold text-[#163144]">Rp {{ number_format($stats->best_revenue, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

    </div>

    <!-- COMPACT CIRCLE GRID PERFORMANCE WIDGETS (Takes minimal vertical space) -->
    @if($viewLevel === 'yearly' && $yearlyData->isNotEmpty())
        <div class="glass-card-plek rounded-[24px] p-4">
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-white/40">
                <div class="flex items-center gap-2">
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">Performa Antar Tahun</h3>
                </div>
                <span class="text-xs font-bold text-[#163144] glass-pill-plek px-2.5 py-0.5 rounded-full">
                    {{ $yearlyData->count() }} Tahun Tercatat
                </span>
            </div>

            <!-- Tahun Berjejer Horizontal Grid (Matching Gambar Ke 2) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                @foreach($yearlyData as $yItem)
                    <div 
                        wire:click="goToMonthly({{ $yItem->year }})"
                        class="group flex items-center gap-3.5 p-3.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 hover:shadow-xs transition cursor-pointer"
                    >
                        <div class="w-11 h-11 rounded-full bg-[#163144] text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-xs">
                            {{ substr((string)$yItem->year, -2) }}'
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="block font-extrabold text-xs text-[#163144]">
                                Tahun {{ $yItem->year }}
                            </span>
                            <span class="block text-[11px] font-bold text-[#386797] mt-0.5 truncate">
                                {{ number_format($yItem->total_transactions) }} Nota · Rp {{ number_format($yItem->total_revenue, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($viewLevel === 'monthly' && $monthlyData->isNotEmpty())
        <div class="glass-card-plek rounded-[24px] p-4">
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-white/40">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#386797]"></span>
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">Performa 12 Bulan (Tahun {{ $selectedYear }})</h3>
                </div>
                <button 
                    type="button" 
                    wire:click="goToYearly" 
                    class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali ke Tahunan</span>
                </button>
            </div>

            <!-- 12 Bulan Berjejer Horizontal Grid (All 12 Months Solid Dark & Consistent) -->
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-2">
                @foreach($monthlyData->sortBy('month_num') as $mItem)
                    <div 
                        wire:click="goToDaily({{ $selectedYear }}, {{ $mItem->month_num }})"
                        class="group relative flex flex-col items-center justify-between p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/90 hover:shadow-xs transition cursor-pointer"
                    >
                        <div class="w-8 h-8 rounded-full bg-[#163144] text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs">
                            {{ substr($mItem->month_name, 0, 3) }}
                        </div>

                        <div class="text-center mt-1.5 w-full">
                            <span class="block font-black text-[#163144] text-[10px] leading-tight">
                                {{ $mItem->total_transactions }} Nota
                            </span>
                            <span class="block text-[9px] font-bold text-[#386797] mt-0.5 truncate">
                                Rp {{ $mItem->total_revenue >= 1000000 ? number_format($mItem->total_revenue / 1000000, 1) . 'M' : number_format($mItem->total_revenue / 1000, 0) . 'K' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($viewLevel === 'daily' && $dailyData->isNotEmpty())
        @php
            $activeDays = $dailyData->where('total_transactions', '>', 0);
        @endphp
        <div class="glass-card-plek rounded-[24px] p-4">
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-white/40">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">Performa Hari Aktif ({{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }})</h3>
                </div>
                <button 
                    type="button" 
                    wire:click="goToMonthly({{ $selectedYear }})" 
                    class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali ke Bulanan</span>
                </button>
            </div>

            @if($activeDays->isNotEmpty())
                <!-- Hari Aktif Berjejer Horizontal Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-2">
                    @foreach($activeDays as $dItem)
                        <div 
                            wire:click="goToTransactions('{{ $dItem->date }}')"
                            class="group flex flex-col items-center justify-between p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/90 hover:shadow-xs transition cursor-pointer"
                        >
                            <div class="w-8 h-8 rounded-full bg-[#163144] text-white font-extrabold text-xs flex items-center justify-center shadow-xs shrink-0">
                                {{ sprintf('%02d', $dItem->day_num) }}
                            </div>
                            <div class="text-center mt-1.5 w-full">
                                <span class="block font-black text-[#163144] text-[10px]">
                                    {{ $dItem->total_transactions }} Nota
                                </span>
                                <span class="block text-[9px] font-bold text-[#386797] mt-0.5 truncate">
                                    Rp {{ $dItem->total_revenue >= 1000000 ? number_format($dItem->total_revenue / 1000000, 1) . 'M' : number_format($dItem->total_revenue / 1000, 0) . 'K' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-3 text-center text-xs text-slate-500 font-medium">
                    Belum ada transaksi pada bulan ini.
                </div>
            @endif
        </div>
    @endif

    <!-- ================= LEVEL 1: TABEL LAPORAN TAHUNAN ================= -->
    @if($viewLevel === 'yearly')
        <div class="glass-card-plek rounded-[24px] overflow-hidden">
            
            <div class="p-4 border-b border-white/40 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="font-black text-[#163144] text-sm">Laporan Transaksi Tahunan</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Rekapitulasi total volume transaksi, omset penjualan, dan estimasi keuntungan bersih per tahun.</p>
                </div>
            </div>

            <div class="hidden sm:block w-full overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[600px]">
                    <thead class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                        <tr>
                            <th class="p-3.5 pl-5 whitespace-nowrap">Tahun</th>
                            <th class="p-3.5 whitespace-nowrap">Jumlah Transaksi</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Pendapatan</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Keuntungan</th>
                            <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/30 font-semibold">
                        @forelse($yearlyData as $row)
                            <tr class="hover:bg-white/40 transition">
                                <!-- Tahun -->
                                <td class="p-3.5 pl-5 font-black text-[#163144] text-sm whitespace-nowrap">
                                    {{ $row->year }}
                                </td>

                                <!-- Jumlah Transaksi -->
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-bold text-xs inline-block">
                                        {{ number_format($row->total_transactions) }} transaksi
                                    </span>
                                </td>

                                <!-- Pendapatan -->
                                <td class="p-3.5 text-right font-black text-[#163144] text-xs whitespace-nowrap">
                                    Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                                </td>

                                <!-- Keuntungan -->
                                <td class="p-3.5 text-right font-black text-emerald-700 text-xs whitespace-nowrap">
                                    Rp {{ number_format($row->total_profit, 0, ',', '.') }}
                                </td>

                                <!-- Aksi: Masuk ke Bulanan -->
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    <button 
                                        type="button" 
                                        wire:click="goToMonthly({{ $row->year }})"
                                        class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs"
                                        title="Lihat Rincian Bulan Tahun {{ $row->year }}"
                                    >
                                        <span>Bulan</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-400">
                                    Belum ada data transaksi penjualan tahunan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($yearlyData->count() > 0)
                        <tfoot class="bg-white/20 border-t border-white/40 font-black text-xs">
                            <tr>
                                <td class="p-3.5 pl-5 text-[#163144] uppercase tracking-wider text-[11px] whitespace-nowrap">Total Keseluruhan</td>
                                <td class="p-3.5 text-[#163144] whitespace-nowrap">
                                    {{ number_format($yearlyData->sum('total_transactions')) }} Transaksi
                                </td>
                                <td class="p-3.5 text-right text-[#163144] whitespace-nowrap">
                                    Rp {{ number_format($yearlyData->sum('total_revenue'), 0, ',', '.') }}
                                </td>
                                <td class="p-3.5 text-right text-emerald-700 whitespace-nowrap">
                                    Rp {{ number_format($yearlyData->sum('total_profit'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-white/40">
                @forelse($yearlyData as $row)
                    <div class="p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-[#163144] text-base">Tahun {{ $row->year }}</span>
                            <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-xs">
                                {{ number_format($row->total_transactions) }} trx
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 glass-pill-plek p-2.5 rounded-xl text-xs">
                            <div>
                                <span class="text-[9px] text-slate-400 uppercase font-bold block">Pendapatan</span>
                                <span class="font-mono font-black text-[#163144]">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 uppercase font-bold block">Keuntungan</span>
                                <span class="font-mono font-black text-emerald-700">Rp {{ number_format($row->total_profit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pt-1">
                            <button 
                                type="button" 
                                wire:click="goToMonthly({{ $row->year }})"
                                class="btn-glass-action"
                            >
                                Lihat Bulan
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada data transaksi tahunan.
                    </div>
                @endforelse
            </div>

        </div>
    @endif

    <!-- ================= LEVEL 2: TABEL LAPORAN BULANAN ================= -->
    @if($viewLevel === 'monthly')
        <div class="glass-card-plek rounded-[24px] overflow-hidden">
            
            <div class="p-4 border-b border-white/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-[#163144] text-sm">Laporan Transaksi Bulanan (Tahun {{ $selectedYear }})</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Rincian pendapatan dan laba penjualan tiap bulan pada tahun {{ $selectedYear }}.</p>
                </div>

                <button 
                    type="button" 
                    wire:click="goToYearly"
                    class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali ke Tahunan</span>
                </button>
            </div>

            <div class="hidden sm:block w-full overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[600px]">
                    <thead class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                        <tr>
                            <th class="p-3.5 pl-5 whitespace-nowrap">Bulan</th>
                            <th class="p-3.5 whitespace-nowrap">Jumlah Transaksi</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Pendapatan</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Keuntungan</th>
                            <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/30 font-semibold">
                        @forelse($monthlyData as $row)
                            <tr class="hover:bg-white/40 transition">
                                <!-- Bulan -->
                                <td class="p-3.5 pl-5 whitespace-nowrap">
                                    <span class="font-black text-[#163144] text-sm block">{{ $row->month_name }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $row->month_label }}</span>
                                </td>

                                <!-- Jumlah Transaksi -->
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-bold text-xs inline-block">
                                        {{ number_format($row->total_transactions) }} transaksi
                                    </span>
                                </td>

                                <!-- Pendapatan -->
                                <td class="p-3.5 text-right font-black text-[#163144] text-xs whitespace-nowrap">
                                    Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                                </td>

                                <!-- Keuntungan -->
                                <td class="p-3.5 text-right font-black text-emerald-700 text-xs whitespace-nowrap">
                                    Rp {{ number_format($row->total_profit, 0, ',', '.') }}
                                </td>

                                <!-- Aksi: Masuk ke Harian -->
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    <button 
                                        type="button" 
                                        wire:click="goToDaily({{ $row->year }}, {{ $row->month_num }})"
                                        class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs"
                                        title="Lihat Laporan Harian Bulan {{ $row->month_name }}"
                                    >
                                        <span>Hari</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-400">
                                    Belum ada data transaksi penjualan pada tahun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($monthlyData->count() > 0)
                        <tfoot class="bg-white/20 border-t border-white/40 font-black text-xs">
                            <tr>
                                <td class="p-3.5 pl-5 text-[#163144] uppercase tracking-wider text-[11px] whitespace-nowrap">Total Tahun {{ $selectedYear }}</td>
                                <td class="p-3.5 text-[#163144] whitespace-nowrap">
                                    {{ number_format($monthlyData->sum('total_transactions')) }} Transaksi
                                </td>
                                <td class="p-3.5 text-right text-[#163144] whitespace-nowrap">
                                    Rp {{ number_format($monthlyData->sum('total_revenue'), 0, ',', '.') }}
                                </td>
                                <td class="p-3.5 text-right text-emerald-700 whitespace-nowrap">
                                    Rp {{ number_format($monthlyData->sum('total_profit'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-white/40">
                @forelse($monthlyData as $row)
                    <div class="p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-black text-[#163144] text-sm block">{{ $row->month_name }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $row->month_label }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-xs">
                                {{ number_format($row->total_transactions) }} trx
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 glass-pill-plek p-2.5 rounded-xl text-xs">
                            <div>
                                <span class="text-[9px] text-slate-400 uppercase font-bold block">Pendapatan</span>
                                <span class="font-mono font-black text-[#163144]">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 uppercase font-bold block">Keuntungan</span>
                                <span class="font-mono font-black text-emerald-700">Rp {{ number_format($row->total_profit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pt-1">
                            <button 
                                type="button" 
                                wire:click="goToDaily({{ $row->year }}, {{ $row->month_num }})"
                                class="btn-glass-action"
                            >
                                Lihat Harian
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada data transaksi bulanan.
                    </div>
                @endforelse
            </div>

        </div>
    @endif

    <!-- ================= LEVEL 3: TABEL LAPORAN HARIAN ================= -->
    @if($viewLevel === 'daily')
        <div class="glass-card-plek rounded-[24px] overflow-hidden">
            
            <div class="p-4 border-b border-white/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-[#163144] text-sm">Laporan Transaksi Harian ({{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }})</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Daftar penjualan harian. Klik tombol struk untuk melihat daftar seluruh struk/nota di tanggal tersebut.</p>
                </div>

                <button 
                    type="button" 
                    wire:click="goToMonthly({{ $selectedYear }})"
                    class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali ke Bulanan</span>
                </button>
            </div>

            <div class="hidden sm:block w-full overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[600px]">
                    <thead class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                        <tr>
                            <th class="p-3.5 pl-5 whitespace-nowrap">Tanggal</th>
                            <th class="p-3.5 whitespace-nowrap">Jumlah Transaksi</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Pendapatan</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Keuntungan</th>
                            <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/30 font-semibold">
                        @forelse($dailyData as $row)
                            <tr class="hover:bg-white/40 transition">
                                <!-- Tanggal -->
                                <td class="p-3.5 pl-5 whitespace-nowrap">
                                    <span class="font-black text-[#163144] text-sm block">{{ $row->date_formatted }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $row->day_name }}</span>
                                </td>

                                <!-- Jumlah Transaksi -->
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-bold text-xs inline-block">
                                        {{ number_format($row->total_transactions) }} transaksi
                                    </span>
                                </td>

                                <!-- Pendapatan -->
                                <td class="p-3.5 text-right font-black text-[#163144] text-xs whitespace-nowrap">
                                    Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                                </td>

                                <!-- Keuntungan -->
                                <td class="p-3.5 text-right font-black text-emerald-700 text-xs whitespace-nowrap">
                                    Rp {{ number_format($row->total_profit, 0, ',', '.') }}
                                </td>

                                <!-- Aksi: Masuk ke Detail Struk Hari Tersebut -->
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    <button 
                                        type="button" 
                                        wire:click="goToTransactions('{{ $row->date }}')"
                                        class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs"
                                        title="Buka Daftar Struk Tanggal {{ $row->date_formatted }}"
                                    >
                                        <span>Struk</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-400">
                                    Belum ada data transaksi pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($dailyData->count() > 0)
                        <tfoot class="bg-white/20 border-t border-white/40 font-black text-xs">
                            <tr>
                                <td class="p-3.5 pl-5 text-[#163144] uppercase tracking-wider text-[11px] whitespace-nowrap">Total Bulan Ini</td>
                                <td class="p-3.5 text-[#163144] whitespace-nowrap">
                                    {{ number_format($dailyData->sum('total_transactions')) }} Transaksi
                                </td>
                                <td class="p-3.5 text-right text-[#163144] whitespace-nowrap">
                                    Rp {{ number_format($dailyData->sum('total_revenue'), 0, ',', '.') }}
                                </td>
                                <td class="p-3.5 text-right text-emerald-700 whitespace-nowrap">
                                    Rp {{ number_format($dailyData->sum('total_profit'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-white/40">
                @forelse($dailyData as $row)
                    <div class="p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-black text-[#163144] text-sm block">{{ $row->date_formatted }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $row->day_name }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-xs">
                                {{ number_format($row->total_transactions) }} trx
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 glass-pill-plek p-2.5 rounded-xl text-xs">
                            <div>
                                <span class="text-[9px] text-slate-400 uppercase font-bold block">Pendapatan</span>
                                <span class="font-mono font-black text-[#163144]">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 uppercase font-bold block">Keuntungan</span>
                                <span class="font-mono font-black text-emerald-700">Rp {{ number_format($row->total_profit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pt-1">
                            <button 
                                type="button" 
                                wire:click="goToTransactions('{{ $row->date }}')"
                                class="btn-glass-action"
                            >
                                Buka Struk
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Tidak ada transaksi ditemukan pada bulan ini.
                    </div>
                @endforelse
            </div>

        </div>
    @endif

    <!-- ================= LEVEL 4: DAFTAR STRUK TRANSAKSI DI HARI TERSEBUT ================= -->
    @if($viewLevel === 'transactions')
        <div class="glass-card-plek rounded-[24px] overflow-hidden space-y-0">
            
            <div class="p-4 border-b border-white/40 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-[#163144] text-sm">
                        @if($searchInvoice)
                            Hasil Pencarian Struk: "{{ $searchInvoice }}"
                        @else
                            Daftar Riwayat Struk Transaksi | {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                        @endif
                    </h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Daftar transaksi kasir, cetak ulang nota, bagikan nota via WhatsApp, dan retur barang.</p>
                </div>

                <!-- Navigation & Filter Bar -->
                <div class="flex items-center gap-2 flex-wrap">
                    <button 
                        type="button" 
                        wire:click="goToDaily({{ $selectedYear }}, {{ $selectedMonth }})"
                        class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs shrink-0"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali ke Harian</span>
                    </button>

                    <select 
                        wire:model.live="filterPaymentMethod" 
                        class="px-3.5 py-1.5 rounded-full glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer"
                    >
                        <option value="">Semua Metode Bayar</option>
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="tempo">Tempo (Piutang)</option>
                    </select>

                    <select 
                        wire:model.live="filterCashierId" 
                        class="px-3.5 py-1.5 rounded-full glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer"
                    >
                        <option value="">Semua Kasir</option>
                        @foreach($cashiers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="hidden sm:block w-full overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[760px]">
                    <thead class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                        <tr>
                            <th class="p-3.5 pl-5 whitespace-nowrap">ID Struk</th>
                            <th class="p-3.5 whitespace-nowrap">Kasir & Pelanggan</th>
                            <th class="p-3.5 whitespace-nowrap">Tipe & Metode</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Total Pendapatan</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Keuntungan</th>
                            <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/30 font-semibold">
                        @forelse($transactionsData as $trx)
                            @php
                                $trxCost = $trx->items->sum(function($it) {
                                    return (float) $it->cost_price * (int) $it->quantity;
                                });
                                $trxProfit = max(0, (float) $trx->grand_total - $trxCost);
                            @endphp
                            <tr class="hover:bg-white/40 transition">
                                
                                <!-- ID Struk & Waktu -->
                                <td class="p-3.5 pl-5">
                                    <span class="font-black text-[#163144] block text-xs truncate" title="{{ $trx->invoice_number }}">
                                        {{ $trx->invoice_number }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">
                                        {{ $trx->created_at->format('d/m/Y H:i') }} WIB
                                    </span>
                                </td>

                                <!-- Kasir & Pelanggan -->
                                <td class="p-3.5">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-bold text-[#163144] block truncate text-xs">{{ $trx->customer_name }}</span>
                                        @if($trx->is_loyalty)
                                            <span class="text-[9px] font-black bg-[#a0bddb]/30 text-[#163144] px-1.5 py-0.5 rounded-full">Member</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400 block">Kasir: {{ $trx->user?->name ?? 'Kasir' }}</span>
                                </td>

                                <!-- Tipe & Metode Pembayaran -->
                                <td class="p-3.5 whitespace-nowrap">
                                    @if($trx->payment_method === 'cash')
                                        <span class="px-2.5 py-1 rounded-full glass-pill-plek text-emerald-800 font-bold text-[10px]">Tunai (Cash)</span>
                                    @elseif($trx->payment_method === 'qris')
                                        <span class="px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-bold text-[10px]">QRIS</span>
                                    @elseif($trx->payment_method === 'tempo')
                                        <span class="px-2.5 py-1 rounded-full glass-pill-plek text-amber-800 font-bold text-[10px]">Tempo (Hutang)</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full glass-pill-plek text-purple-800 font-bold text-[10px]">Transfer</span>
                                    @endif
                                </td>

                                <!-- Total Pendapatan -->
                                <td class="p-3.5 text-right font-black text-[#163144] text-xs whitespace-nowrap">
                                    Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                </td>

                                <!-- Keuntungan -->
                                <td class="p-3.5 text-right font-black text-emerald-700 text-xs whitespace-nowrap">
                                    Rp {{ number_format($trxProfit, 0, ',', '.') }}
                                </td>

                                <!-- Tombol Aksi (Nota, Retur, Hapus) -->
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                        
                                        <!-- Tombol Nota Digital -->
                                        <button 
                                            type="button" 
                                            wire:click="openDetail({{ $trx->id }})" 
                                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs"
                                            title="Lihat Nota & Kirim WhatsApp"
                                        >
                                            <span>Nota</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>

                                        <!-- Tombol Retur Barang -->
                                        <button 
                                            type="button" 
                                            wire:click="openReturnModal({{ $trx->id }})" 
                                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs"
                                            title="Retur / Kembalikan Barang"
                                        >
                                            <span>Retur</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <button 
                                            type="button" 
                                            wire:click="confirmDelete({{ $trx->id }})" 
                                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs"
                                            title="Batalkan & Hapus Transaksi"
                                        >
                                            <span>Hapus</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400">
                                    Tidak ada struk transaksi ditemukan pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-white/40">
                @forelse($transactionsData as $trx)
                    @php
                        $trxCost = $trx->items->sum(function($it) {
                            return (float) $it->cost_price * (int) $it->quantity;
                        });
                        $trxProfit = max(0, (float) $trx->grand_total - $trxCost);
                    @endphp
                    <div class="p-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono font-black text-[#163144] text-xs block truncate">{{ $trx->invoice_number }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $trx->created_at->format('d/m/Y H:i') }} WIB</span>
                            </div>
                            @if($trx->payment_method === 'cash')
                                <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-bold text-[10px]">Tunai</span>
                            @elseif($trx->payment_method === 'qris')
                                <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-[10px]">QRIS</span>
                            @elseif($trx->payment_method === 'tempo')
                                <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-800 font-bold text-[10px]">Tempo</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-purple-800 font-bold text-[10px]">Transfer</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-xs pt-0.5">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-[#163144] block truncate">{{ $trx->customer_name }}</span>
                                    @if($trx->is_loyalty)
                                        <span class="text-[8px] font-black bg-[#a0bddb]/30 text-[#163144] px-1.5 py-0.2 rounded-full">Member</span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-400 block">Kasir: {{ $trx->user?->name ?? 'Kasir' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-black text-[#163144] font-mono text-sm block">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-emerald-700 font-bold block">Laba: Rp {{ number_format($trxProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-white/40">
                            <button 
                                type="button" 
                                wire:click="openDetail({{ $trx->id }})" 
                                class="btn-glass-action"
                            >
                                Nota
                            </button>
                            <button 
                                type="button" 
                                wire:click="openReturnModal({{ $trx->id }})" 
                                class="btn-glass-action"
                            >
                                Retur
                            </button>
                            <button 
                                type="button" 
                                wire:click="confirmDelete({{ $trx->id }})" 
                                class="btn-glass-action"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Tidak ada struk transaksi ditemukan pada tanggal ini.
                    </div>
                @endforelse
            </div>

            @if($transactionsData)
                {{ $transactionsData->links() }}
            @endif

        </div>
    @endif

    <!-- ================= MODAL RETUR BARANG ================= -->
    @if($showReturnModal && $returningTransactionId)
        @php
            $trxReturn = \App\Models\Transaction::with(['items.product'])->find($returningTransactionId);
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 shadow-2xl overflow-hidden flex flex-col max-h-[90vh] modal-content-animate my-auto">
                
                <!-- Header Modal Retur -->
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">Retur / Pengembalian Barang</h3>
                            <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[11px] font-black tracking-wide">
                                {{ $trxReturn?->invoice_number }}
                            </span>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="closeReturnModal" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 sm:p-6 space-y-4 overflow-y-auto">
                    
                    <!-- Alert Notice Ketentuan Retur -->
                    <div class="p-3.5 bg-amber-50/70 border border-amber-200/70 rounded-2xl text-xs text-amber-900 shadow-2xs flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-amber-950">Ketentuan Retur Transaksi:</p>
                            <p class="text-[11px] text-amber-800 leading-relaxed font-semibold mt-0.5">
                                Pilih produk yang ingin dikembalikan dan sesuaikan jumlahnya. Stok barang akan otomatis ditambahkan kembali ke inventori toko.
                            </p>
                        </div>
                    </div>

                    <!-- Daftar Item yang Dibeli -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-[#163144] uppercase tracking-wider">Pilih Produk yang Diretur:</label>
                        
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($returnItems as $itemId => $item)
                                <div class="p-3 rounded-2xl border transition flex items-center justify-between gap-3 {{ $item['selected'] ? 'bg-[#a0bddb]/15 border-[#a0bddb]/70 ring-1 ring-[#a0bddb]/40' : 'bg-slate-50/80 border-slate-200/80 hover:bg-slate-100/80' }}">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <input 
                                            type="checkbox" 
                                            wire:model.live="returnItems.{{ $itemId }}.selected"
                                            class="w-4 h-4 text-[#163144] rounded border-slate-300 focus:ring-[#163144] cursor-pointer"
                                        >
                                        <div class="min-w-0">
                                            <span class="font-extrabold text-xs text-[#163144] block truncate">{{ $item['product_name'] }}</span>
                                            <span class="text-[11px] text-slate-500 font-bold block mt-0.5">
                                                Dibeli: {{ $item['max_qty'] }} item · @ Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Stepper Quantity Retur -->
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <label class="text-[10px] text-slate-400 font-extrabold">Qty:</label>
                                        <input 
                                            type="number" 
                                            wire:model.live="returnItems.{{ $itemId }}.qty"
                                            min="1" 
                                            max="{{ $item['max_qty'] }}"
                                            disabled="{{ !$item['selected'] }}"
                                            class="w-16 px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-black text-center text-[#163144] outline-none focus:ring-2 focus:ring-[#163144] disabled:opacity-40 disabled:bg-slate-100 shadow-2xs"
                                        >
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Alasan Retur -->
                    <div>
                        <label class="block text-[10px] font-black text-[#163144] uppercase tracking-wider mb-1.5">Alasan Retur / Pengembalian:</label>
                        <select 
                            wire:model="returnReason" 
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/90 border border-slate-200/80 focus:border-[#163144] focus:bg-white text-xs font-bold text-[#163144] outline-none transition cursor-pointer shadow-2xs"
                        >
                            <option value="Salah Beli / Tukar Barang">Salah Beli / Tukar Barang</option>
                            <option value="Kemasan Rusak / Bocor">Kemasan Rusak / Bocor</option>
                            <option value="Kadaluarsa / Mutu Berubah">Kadaluarsa / Mutu Berubah</option>
                            <option value="Batal Beli / Refund Pembeli">Batal Beli / Refund Pembeli</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label class="block text-[10px] font-black text-[#163144] uppercase tracking-wider mb-1.5">Catatan Tambahan (Opsional):</label>
                        <input 
                            type="text" 
                            wire:model="returnNotes" 
                            placeholder="Contoh: Ditukar kemasan baru / Uang tunai dikembalikan..."
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/90 border border-slate-200/80 focus:border-[#163144] focus:bg-white text-xs font-bold text-[#163144] placeholder-slate-400 outline-none transition shadow-2xs"
                        >
                    </div>

                    <!-- Checkbox Restock -->
                    <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 cursor-pointer shadow-2xs">
                        <input 
                            type="checkbox" 
                            wire:model="returnRestock" 
                            class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer"
                        >
                        <span class="text-xs font-bold text-emerald-950">
                            Kembalikan Stok ke Gudang (Restock Otomatis)
                        </span>
                    </label>

                    <!-- Total Estimasi Refund -->
                    @php
                        $estRefund = 0;
                        foreach($returnItems as $it) {
                            if (!empty($it['selected'])) {
                                $estRefund += ((int) $it['qty']) * ((float) $it['unit_price']);
                            }
                        }
                    @endphp
                    <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-200/80 flex items-center justify-between shadow-2xs">
                        <span class="text-xs font-black text-[#163144]">Total Pengembalian Dana:</span>
                        <span class="text-base font-black text-[#163144] font-mono">
                            Rp {{ number_format($estRefund, 0, ',', '.') }}
                        </span>
                    </div>

                </div>

                <!-- Footer Modal Retur -->
                <div class="p-4 px-6 border-t border-slate-100 bg-slate-50/60 flex items-center justify-end gap-2.5 shrink-0 rounded-b-[32px]">
                    <button 
                        type="button" 
                        wire:click="closeReturnModal" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="processReturn" 
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer disabled:opacity-50 inline-flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Konfirmasi & Proses Retur</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ================= MODAL DETAIL NOTA DIGITAL & KIRIM WHATSAPP ================= -->
    @if($showDetailModal && $selectedTransaction)
        @php
            $receiptSetting = \App\Models\ReceiptSetting::getSettings();
            $receiptPaperSize = $receiptSetting->default_paper_size ?? '58mm';
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="bg-gradient-to-b from-white via-white to-[#eef5f3] backdrop-blur-2xl rounded-[32px] max-w-md w-full border border-white shadow-[0_30px_70px_-20px_rgba(14,30,43,0.5)] overflow-hidden flex flex-col max-h-[92vh] modal-content-animate my-auto">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">Nota Transaksi Digital</h3>
                            <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[11px] font-black tracking-wide">
                                {{ $selectedTransaction->invoice_number }}
                            </span>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="closeDetail" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Receipt Body Preview -->
                <div class="p-4 sm:p-6 overflow-y-auto min-h-0 flex-1 bg-gradient-to-b from-[#e6f0ee]/70 to-[#dbe9e6]/60 flex justify-center items-start">
                    
                    <div 
                        id="sales-receipt-printable" 
                        class="bg-white p-5 rounded-2xl shadow-[0_10px_30px_-12px_rgba(22,49,68,0.30)] border border-white w-full max-w-[340px] shrink-0 h-auto text-xs font-mono text-slate-800 space-y-3 select-text"
                    >
                        <!-- Header Toko -->
                        <div class="text-center space-y-1">
                            @if($receiptSetting->show_logo && $receiptSetting->logo_path)
                                <img src="{{ asset('storage/' . $receiptSetting->logo_path) }}" alt="Logo" class="h-10 mx-auto object-contain mb-1">
                            @endif
                            <h2 class="font-bold text-base text-slate-900 uppercase tracking-tight">{{ $receiptSetting->store_name }}</h2>
                            <p class="text-[10px] text-slate-500 leading-tight">{{ $receiptSetting->store_address }}</p>
                            @if($receiptSetting->store_phone)
                                <p class="text-[10px] text-slate-500">Telp/WA: {{ $receiptSetting->store_phone }}</p>
                            @endif
                        </div>

                        <!-- Divider 1 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Info Nota -->
                        <div class="space-y-1 text-[11px] text-slate-600">
                            <div class="flex justify-between">
                                <span>No: {{ $selectedTransaction->invoice_number }}</span>
                                <span>{{ $selectedTransaction->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kasir: {{ $selectedTransaction->user->name ?? 'Kasir' }}</span>
                                <span>{{ $selectedTransaction->customer_name }}</span>
                            </div>
                        </div>

                        <!-- Divider 2 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Daftar Item Belanja -->
                        <div class="space-y-1.5 text-[11px]">
                            @foreach($selectedTransaction->items as $item)
                                <div>
                                    <div class="font-bold text-slate-900 flex items-center justify-between">
                                        <span>{{ $item->product_name }}</span>
                                        @if($item->price_type === 'loyalty')
                                            <span class="text-[8px] font-bold bg-[#a0bddb]/30 text-[#163144] px-1 rounded">SALES</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between text-slate-600 text-[10px]">
                                        <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                        <span class="font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    @if($item->discount_amount > 0)
                                        <div class="flex justify-between text-[9px] text-rose-600 italic">
                                            <span>↳ Potongan: -Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Divider 3 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Totals Calculation -->
                        <div class="space-y-1 text-[11px] text-slate-900 font-semibold">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($selectedTransaction->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($selectedTransaction->discount_amount > 0)
                                <div class="flex justify-between text-rose-600 font-bold">
                                    <span>Diskon</span>
                                    <span>-Rp {{ number_format($selectedTransaction->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-xs font-black text-slate-900 pt-1 border-t border-slate-200">
                                <span>Total Akhir</span>
                                <span>Rp {{ number_format($selectedTransaction->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600 pt-1">
                                <span>Bayar ({{ strtoupper($selectedTransaction->payment_method) }})</span>
                                <span>Rp {{ number_format($selectedTransaction->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Kembalian</span>
                                <span>Rp {{ number_format($selectedTransaction->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Divider 4 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Footer Struk -->
                        <div class="text-center text-[10px] text-slate-500 space-y-0.5">
                            @if($receiptSetting->footer_notes)
                                <p>{{ $receiptSetting->footer_notes }}</p>
                            @else
                                <p>Terima kasih telah berbelanja di Syafa Frozen Food!</p>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- WhatsApp & Print Toolbar Footer -->
                <div class="p-4 px-5 sm:px-6 border-t border-slate-200/70 bg-white/80 space-y-3 shrink-0 font-sans rounded-b-[32px]">
                    
                    <!-- WhatsApp Image Share Box -->
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <input 
                                wire:model="waPhoneNumber" 
                                type="text" 
                                placeholder="No. WhatsApp (0812...)" 
                                class="flex-1 px-4 py-2.5 rounded-full bg-white border border-slate-200 text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#163144] shadow-2xs"
                            >
                            <button 
                                type="button" 
                                onclick="sendReceiptToWhatsAppImage('sales-receipt-printable', '{{ $waPhoneNumber }}', '{{ $selectedTransaction->invoice_number }}')"
                                class="px-5 py-2.5 rounded-full bg-emerald-600/90 hover:bg-emerald-700 text-white font-black text-xs shadow-sm shadow-emerald-700/20 transition active:scale-95 hover:scale-105 inline-flex items-center gap-1.5 cursor-pointer shrink-0"
                            >
                                <span>Kirim WA</span>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-200/60 flex-wrap">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a 
                                href="{{ route('admin.print.invoice.formal', $selectedTransaction->id) }}" 
                                target="_blank"
                                class="px-4 py-2 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs cursor-pointer inline-flex items-center gap-1.5 shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>Faktur A4 / PDF</span>
                            </a>
                            <button 
                                type="button"
                                onclick="downloadReceiptImage('sales-receipt-printable', '{{ $selectedTransaction->invoice_number }}')"
                                class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                            >
                                Unduh PNG
                            </button>
                            <button 
                                type="button" 
                                @click="printThermalReceipt('sales-receipt-printable', '{{ $receiptPaperSize }}')" 
                                class="px-4 py-2 rounded-full bg-[#386797] hover:bg-[#2c527a] text-white font-black text-xs transition active:scale-95 hover:scale-105 cursor-pointer flex items-center gap-1.5 shadow-md shadow-[#386797]/20"
                            >
                                <span>Cetak Struk</span>
                            </button>
                        </div>

                        <button 
                            type="button" 
                            wire:click="closeDetail" 
                            class="px-5 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    <!-- ================= MODAL DELETE / BATAL TRANSAKSI ================= -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="bg-gradient-to-b from-white to-[#eef5f3] backdrop-blur-2xl rounded-[32px] max-w-sm w-full border border-white p-6 sm:p-7 space-y-4 shadow-[0_30px_70px_-20px_rgba(14,30,43,0.5)] modal-content-animate my-auto text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-500/15 text-rose-700 mx-auto flex items-center justify-center border border-rose-200/60 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-black text-[#163144] text-lg tracking-tight">Batalkan Transaksi?</h3>
                    <p class="text-xs text-slate-600 font-semibold leading-relaxed mt-1">
                        Invoice <strong class="text-[#163144] font-mono px-1.5 py-0.5 rounded-md bg-slate-100">{{ $deletingInvoiceNumber }}</strong> akan dihapus dan stok barang akan otomatis dikembalikan ke inventori toko.
                    </p>
                </div>
                <div class="flex items-center gap-2.5 pt-2">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteModal', false)" 
                        class="flex-1 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="deleteTransaction" 
                        class="flex-1 py-2.5 rounded-full bg-rose-600/90 hover:bg-rose-700 text-white font-black text-xs shadow-md shadow-rose-950/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

