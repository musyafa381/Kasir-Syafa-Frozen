<div class="space-y-4 text-slate-800">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Laporan Arus Keuangan & Laba Rugi</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Analisis arus kas (cashflow), beban operasional riil (OPEX), laba bersih murni, dan neraca kesehatan aset toko.</p>
        </div>
        
        <div class="flex items-center gap-2 flex-wrap print:hidden">
            <a 
                href="{{ route('admin.print.profit-loss.report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                target="_blank" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-3.5 h-3.5 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak PDF Laba Rugi</span>
            </a>

            <a 
                href="{{ route('admin.print.profit-loss.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-3.5 h-3.5 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Ekspor Excel</span>
            </a>

            <a 
                href="{{ route('admin.expenses.index') }}" 
                wire:navigate 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <span>Kelola Beban Toko</span>
            </a>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4 space-y-3 print:hidden">
        
        <!-- Period Quick Presets -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0 sm:flex-wrap">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mr-2 shrink-0">Periode:</span>
            
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
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === $key ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Custom Date Range & Cashier & Outlet Filter -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 pt-2 border-t border-white/40">
            
            <!-- Start Date -->
            <div>
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input 
                    wire:model.live="startDate" 
                    type="date" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input 
                    wire:model.live="endDate" 
                    type="date" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
            </div>

            <!-- Cabang Toko -->
            <div>
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Cabang Toko</label>
                <select 
                    wire:model.live="outletId" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
                    <option value="">Semua Cabang Toko</option>
                    @foreach($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kasir Filter -->
            <div>
                <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider mb-1">Filter Kasir</label>
                <x-searchable-select 
                    wire:model.live="cashierId" 
                    :options="$cashiers" 
                    placeholder="Semua Kasir Bertugas" 
                    empty-option="Semua Kasir Bertugas" 
                />
            </div>

        </div>
    </div>

    <!-- 4 Financial Scorecard KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        
        <!-- 1. Laba Bersih Riil (True Net Profit) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Hasil Bersih</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Laba Bersih</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek font-black text-[9px] sm:text-[10px] shrink-0 {{ $trueNetProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }}">
                    {{ $netProfitMargin }}% Net
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black font-mono tracking-tight block {{ $trueNetProfit >= 0 ? 'text-emerald-800' : 'text-rose-600' }}">
                    Rp {{ number_format($trueNetProfit, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">
                    Laba Kotor Rp {{ number_format($grossProfit, 0, ',', '.') }} − OPEX
                </p>
            </div>
        </div>

        <!-- 2. Total Beban Operasional (OPEX) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-rose-600 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Pengeluaran</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Beban OPEX</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-rose-700 font-black text-[9px] sm:text-[10px] shrink-0">
                    {{ $netSales > 0 ? round(($totalExpenses / $netSales) * 100, 1) : 0 }}%
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-rose-600 font-mono tracking-tight block">
                    Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">
                    Listrik, Gaji, Packing, dll.
                </p>
            </div>
        </div>

        <!-- 3. Arus Kas Bersih (Net Cash Flow) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Likuiditas</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Arus Kas Bersih</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek font-black text-[9px] sm:text-[10px] shrink-0 {{ $netCashFlow >= 0 ? 'text-emerald-800' : 'text-rose-700' }}">
                    {{ $netCashFlow >= 0 ? 'Surplus' : 'Defisit' }}
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black font-mono tracking-tight block {{ $netCashFlow >= 0 ? 'text-[#163144]' : 'text-rose-600' }}">
                    Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">
                    In: {{ number_format($totalCashInflow, 0, ',', '.') }} • Out: {{ number_format($totalCashOutflow, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- 4. Total Valuasi Kekayaan Toko (Net Worth) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kekayaan</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Kekayaan</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-black text-[9px] sm:text-[10px] shrink-0">
                    Ekuitas
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($totalNetWorth, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">
                    Stok + Piutang + Aset − Hutang
                </p>
            </div>
        </div>

    </div>

    <!-- Main 2-Column Financial Report Structure -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 printable-report">
        
        <!-- Left Column: Revenue, COGS, and Operational Expenses (OPEX) -->
        <div class="space-y-4">
            
            <!-- 1. Pendapatan Penjualan (Revenue Stream) -->
            <div class="glass-card-plek rounded-[24px] overflow-hidden">
                <div class="p-4 bg-white/40 border-b border-white/60 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">1. Pendapatan Penjualan (Revenue)</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Penjualan produk dari kasir POS</p>
                    </div>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144]">
                        Pendapatan
                    </span>
                </div>

                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-slate-600 font-semibold">Penjualan Pelanggan Reguler</span>
                        <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($regularSales, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-slate-600 font-semibold">Penjualan Pelanggan Member Loyalty</span>
                        <span class="font-mono font-bold text-[#386797]">Rp {{ number_format($memberSales, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 glass-pill-plek rounded-2xl text-xs font-bold border border-white/60">
                        <span class="text-[#163144]">Total Penjualan Kotor (Gross Sales)</span>
                        <span class="font-mono text-[#163144] font-black">Rp {{ number_format($grossSales, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-rose-600 font-bold py-1">
                        <span>Potongan Diskon & Promo Member</span>
                        <span class="font-mono font-black">−Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl glass-card-plek border border-white/80 flex items-center justify-between mt-1">
                        <span class="text-xs font-black text-[#163144]">Total Omzet Bersih (Net Sales):</span>
                        <span class="text-base font-black text-[#163144] font-mono">
                            Rp {{ number_format($netSales, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Beban Pokok Penjualan (HPP / COGS) & Laba Kotor -->
            <div class="glass-card-plek rounded-[24px] overflow-hidden">
                <div class="p-4 bg-white/40 border-b border-white/60 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">2. Beban Pokok Penjualan (HPP)</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Biaya modal beli produk beku yang terjual</p>
                    </div>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-900">
                        Modal Pokok
                    </span>
                </div>

                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-slate-600 font-semibold">Total Produk Terjual</span>
                        <span class="font-mono font-bold text-[#163144]">{{ number_format($totalUnitsSold, 0, ',', '.') }} Unit</span>
                    </div>

                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-slate-600 font-semibold">Total Beban Pokok (HPP)</span>
                        <span class="font-mono font-bold text-amber-800">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between mt-1">
                        <div>
                            <span class="text-xs font-black text-[#163144] block">Laba Kotor Persediaan (Gross Profit):</span>
                            <span class="text-[11px] text-[#386797] font-bold">Margin Kotor: {{ $grossProfitMargin }}%</span>
                        </div>
                        <span class="text-base font-black text-[#163144] font-mono">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Rincian Beban Operasional (OPEX) -->
            <div class="glass-card-plek rounded-[24px] overflow-hidden">
                <div class="p-4 bg-white/40 border-b border-white/60 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">3. Rincian Beban Operasional (OPEX)</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Beban biaya listrik, karyawan, perlengkapan, dan toko</p>
                    </div>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full glass-pill-plek text-rose-700">
                        Beban Usaha
                    </span>
                </div>

                <div class="p-5 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-white/40 font-semibold">
                        <span class="text-slate-600">Listrik PLN & Utilitas (Daya Freezer)</span>
                        <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($electricityExpenses, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-white/40 font-semibold">
                        <span class="text-slate-600">Gaji Karyawan & Kasir</span>
                        <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($salaryExpenses, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-white/40 font-semibold">
                        <span class="text-slate-600">Plastik Kresek, Ice Gel & Packing</span>
                        <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($packagingExpenses, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-white/40 font-semibold">
                        <span class="text-slate-600">Servis & Perawatan Freezer</span>
                        <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($maintenanceExpenses, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-white/40 font-semibold">
                        <span class="text-slate-600">Sewa Tempat / Kios Toko</span>
                        <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($rentExpenses, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-white/40 font-semibold">
                        <span class="text-slate-600">Beban Operasional Lainnya</span>
                        <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($otherExpenses, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex items-center justify-between mt-2">
                        <span class="text-xs font-black text-rose-900">Total Beban Operasional (OPEX):</span>
                        <span class="text-base font-black text-rose-700 font-mono">
                            Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Final True Net Profit & Financial Health Scorecard -->
        <div class="space-y-4">
            
            <!-- Final Statement: LABA BERSIH RIIL (TRUE NET PROFIT) -->
            <div class="glass-card-plek rounded-[24px] overflow-hidden">
                <div class="p-4 bg-white/40 border-b border-white/60 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">4. Laba Bersih Riil Usaha (Net Profit)</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Keuntungan bersih murni setelah seluruh HPP dan Beban Operasional</p>
                    </div>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full glass-pill-plek {{ $trueNetProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }}">
                        Hasil Akhir
                    </span>
                </div>

                <div class="p-5 space-y-4">
                    
                    <!-- Profit Display Box -->
                    <div class="p-5 rounded-2xl glass-pill-plek border border-white/60 text-center space-y-1.5">
                        <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider block">Total Laba Bersih Riil</span>
                        <div class="text-3xl sm:text-4xl font-black font-mono {{ $trueNetProfit >= 0 ? 'text-emerald-800' : 'text-rose-600' }}">
                            Rp {{ number_format($trueNetProfit, 0, ',', '.') }}
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full glass-pill-plek {{ $trueNetProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }} text-xs font-black font-mono mt-1">
                            <span>Margin Bersih: {{ $netProfitMargin }}%</span>
                        </div>
                    </div>

                    <!-- Visual Composition Bar (Omset Breakdown) -->
                    <div class="space-y-2 pt-2">
                        <div class="flex justify-between text-xs font-bold text-[#163144]">
                            <span>Struktur Komposisi Omset:</span>
                            <span class="font-mono font-black">Rp {{ number_format($netSales, 0, ',', '.') }} (100%)</span>
                        </div>
                        
                        @php
                            $hppPercent = $netSales > 0 ? min(100, max(0, round(($totalHpp / $netSales) * 100, 1))) : 0;
                            $opexPercent = $netSales > 0 ? min(100 - $hppPercent, max(0, round(($totalExpenses / $netSales) * 100, 1))) : 0;
                            $netPercent = max(0, 100 - ($hppPercent + $opexPercent));
                        @endphp
                        <div class="h-3 w-full bg-white/70 rounded-full overflow-hidden flex border border-white/80">
                            <div style="width: {{ $hppPercent }}%" class="bg-amber-500 h-full" title="HPP Modal: {{ $hppPercent }}%"></div>
                            <div style="width: {{ $opexPercent }}%" class="bg-rose-500 h-full" title="Beban OPEX: {{ $opexPercent }}%"></div>
                            <div style="width: {{ $netPercent }}%" class="bg-[#163144] h-full" title="Laba Bersih: {{ $netPercent }}%"></div>
                        </div>

                        <div class="grid grid-cols-3 text-[11px] text-slate-600 pt-1 text-center font-bold">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span>HPP ({{ $hppPercent }}%)</span>
                            </div>
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                <span>OPEX ({{ $opexPercent }}%)</span>
                            </div>
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#163144]"></span>
                                <span>Laba ({{ $netPercent }}%)</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Financial Health Scorecard & Balance Sheet Overview -->
            <div class="glass-card-plek rounded-[24px] overflow-hidden">
                <div class="p-4 bg-white/40 border-b border-white/60 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">5. Indikator Kesehatan & Neraca Usaha</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Kekuatan modal, likuiditas, dan titik impas</p>
                    </div>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144]">
                        Neraca Usaha
                    </span>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    
                    <!-- Break-Even Point (BEP) -->
                    <div class="p-3.5 glass-pill-plek rounded-2xl border border-white/60 flex items-center justify-between">
                        <div>
                            <span class="font-black text-[#163144] block">Titik Impas Omzet (Break-Even Point):</span>
                            <span class="text-[11px] text-slate-500 font-semibold">Minimal omzet periode ini untuk menutup seluruh OPEX</span>
                        </div>
                        <span class="font-mono font-black text-[#163144] text-sm">
                            Rp {{ number_format($bepRevenue, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Balance Sheet Components -->
                    <div class="space-y-2 pt-1">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 block">Rincian Total Kekayaan Bersih Toko (Net Worth):</span>
                        
                        <div class="flex justify-between py-1 border-b border-white/40 font-semibold">
                            <span class="text-slate-600">(+) Valuasi Persediaan Stok (HPP)</span>
                            <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($totalInventoryValuation, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-white/40 font-semibold">
                            <span class="text-slate-600">(+) Piutang di Pelanggan (Bon)</span>
                            <span class="font-mono font-bold text-[#386797]">Rp {{ number_format($totalActiveReceivables, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-white/40 font-semibold">
                            <span class="text-slate-600">(+) Nilai Aset Fisik & Freezer</span>
                            <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($totalFixedAssetsValuation, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-white/40 text-rose-600 font-bold">
                            <span>(−) Sisa Hutang ke Supplier</span>
                            <span class="font-mono font-black">−Rp {{ number_format($totalActivePayables, 0, ',', '.') }}</span>
                        </div>

                        <div class="p-3.5 rounded-2xl glass-card-plek border border-white/80 flex items-center justify-between mt-2">
                            <span class="font-black text-[#163144]">Total Kekayaan Bersih Usaha (Net Worth):</span>
                            <span class="font-mono font-black text-[#163144] text-base">
                                Rp {{ number_format($totalNetWorth, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
