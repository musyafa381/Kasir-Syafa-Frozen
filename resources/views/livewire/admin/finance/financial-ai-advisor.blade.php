<div 
    x-data="{
        // Margin & Target Parameters
        marginPercent: {{ $grossMarginPercent }},
        marginDecimal: {{ $effectiveGrossMarginDecimal }},
        totalOpex: {{ $totalOpex }},
        daysInPeriod: {{ $daysInPeriod }},
        targetNetMargin: 15,
        getTargetOmzetTotal() {
            let mDec = this.marginDecimal;
            let tDec = Math.max(0.01, Math.min(0.40, this.targetNetMargin / 100));
            let denom = mDec - tDec;
            return denom > 0.02 ? (this.totalOpex / denom) : (this.totalOpex / mDec * 1.5);
        },
        getTargetOmzetDaily() {
            return this.getTargetOmzetTotal() / Math.max(1, this.daysInPeriod);
        },

        // Pembagian Alokasi Keuangan (%)
        allocRestock: {{ $allocRestockPercent }},
        allocOpex: {{ $allocOpexPercent }},
        allocPayroll: {{ $allocPayrollPercent }},
        allocOwner: {{ $allocOwnerPercent }},
        allocEmergency: {{ $allocEmergencyPercent }},
        allocExpansion: {{ $allocExpansionPercent }},
        safeProfitBase: {{ max(0, $netProfit > 0 ? $netProfit : $grossProfit) }},

        setAllocationPreset(restock, opex, payroll, owner, emergency, expansion) {
            this.allocRestock = restock;
            this.allocOpex = opex;
            this.allocPayroll = payroll;
            this.allocOwner = owner;
            this.allocEmergency = emergency;
            this.allocExpansion = expansion;
        },

        getTotalAllocPercent() {
            return Number(this.allocRestock) + Number(this.allocOpex) + Number(this.allocPayroll) + Number(this.allocOwner) + Number(this.allocEmergency) + Number(this.allocExpansion);
        },

        getAllocNominal(percent) {
            return this.safeProfitBase * (percent / 100);
        },

        // Freezer Simulator State
        freezerPrice: {{ (float) $simAssetPrice }},
        freezerRevenue: {{ (float) $simEstimatedMonthlyRevenue }},
        freezerElectricity: {{ (float) $simExtraElectricity }},
        setFreezerPreset(price, rev, elec) {
            this.freezerPrice = price;
            this.freezerRevenue = rev;
            this.freezerElectricity = elec;
        },
        adjustFreezerPrice(delta) {
            this.freezerPrice = Math.max(0, this.freezerPrice + delta);
        },
        adjustFreezerRevenue(delta) {
            this.freezerRevenue = Math.max(0, this.freezerRevenue + delta);
        },
        adjustFreezerElectricity(delta) {
            this.freezerElectricity = Math.max(0, this.freezerElectricity + delta);
        },
        getFreezerGrossProfit() {
            return this.freezerRevenue * this.marginDecimal;
        },
        getFreezerNetContribution() {
            return this.getFreezerGrossProfit() - this.freezerElectricity;
        },
        getFreezerPaybackMonths() {
            let net = this.getFreezerNetContribution();
            return net > 0 ? (this.freezerPrice / net).toFixed(1) : 0;
        },
        getFreezerStatus() {
            let net = this.getFreezerNetContribution();
            let pb = parseFloat(this.getFreezerPaybackMonths());
            if (net <= 0) return { label: 'Tidak Layak', color: 'rose', bgClass: 'glass-pill-plek text-rose-700 ring-1 ring-rose-300', note: 'Rekomendasi: Belum disarankan. Tambahan beban listrik dan margin omzet baru belum menghasilkan surplus.' };
            if (pb <= 12) return { label: 'Sangat Layak', color: 'emerald', bgClass: 'glass-pill-plek text-emerald-800 ring-1 ring-emerald-300', note: 'Rekomendasi: Sangat layak dieksekusi. Balik modal cepat (' + pb + ' bulan) dan langsung memperkuat profit bersih toko.' };
            if (pb <= 24) return { label: 'Layak dengan Catatan', color: 'navy', bgClass: 'glass-pill-plek text-[#163144] border border-[#a0bddb]/60', note: 'Rekomendasi: Cukup layak (balik modal ' + pb + ' bulan). Disarankan gunakan tabungan ekspansi atau cicilan bertahap.' };
            return { label: 'Evaluasi Ulang', color: 'amber', bgClass: 'glass-pill-plek text-amber-800 ring-1 ring-amber-300', note: 'Rekomendasi: Waktu balik modal cukup lama (' + pb + ' bulan). Pertimbangkan sewa atau kapasitas lebih kecil.' };
        },

        // Multi-Employee Payroll Simulator State
        empCount: {{ $empCount }},
        salaryPerPerson: {{ (float) $empSalaryPerPerson }},
        allowancePerPerson: {{ (float) $empAllowancePerPerson }},
        setSalaryPreset(amt) {
            this.salaryPerPerson = amt;
        },
        adjustSalary(delta) {
            this.salaryPerPerson = Math.max(0, this.salaryPerPerson + delta);
        },
        adjustAllowance(delta) {
            this.allowancePerPerson = Math.max(0, this.allowancePerPerson + delta);
        },
        getTotalMonthlyPayroll() {
            return (Number(this.salaryPerPerson) + Number(this.allowancePerPerson)) * Math.max(1, Number(this.empCount));
        },
        getRequiredMonthlySales() {
            return this.marginDecimal > 0 ? (this.getTotalMonthlyPayroll() / this.marginDecimal) : 0;
        },
        getRequiredDailySales() {
            return this.getRequiredMonthlySales() / 30;
        },
        getPayrollBurdenRatio() {
            let sales = {{ max(1, $netSales) }};
            return ((this.getTotalMonthlyPayroll() / sales) * 100).toFixed(1);
        },

        // Currency Formatter Helper
        formatRp(val) {
            let num = Math.round(Number(val) || 0);
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }"
    class="space-y-4 print:space-y-4 text-slate-800"
>

    <!-- Page Header & Period Selector -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">AI Financial Advisor & Alokasi Toko</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Sistem pembagian keuangan, batas aman hak pemilik, simulasi penggajian karyawan, dan efisiensi operasional.</p>
        </div>
        
        <!-- Action Buttons & Period Selector -->
        <div class="flex flex-wrap items-center gap-2 print:hidden">
            <div class="flex items-center gap-1.5 glass-pill-plek p-1 rounded-2xl">
                <label class="text-[11px] font-extrabold text-[#163144] pl-2">Basis Analisis:</label>
                <select 
                    wire:model.live="analysisPeriod" 
                    class="px-3 py-1.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none cursor-pointer shadow-2xs"
                >
                    <option value="30_days">30 Hari Terakhir</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="90_days">90 Hari Terakhir</option>
                </select>
            </div>

            <button 
                type="button" 
                onclick="window.print()" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Analisis</span>
            </button>
        </div>
    </div>

    <!-- Ringkasan Eksekutif Keuangan Card -->
    <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <div class="flex items-center gap-2">
                <span class="px-3 py-0.5 rounded-full glass-pill-plek text-[#163144] font-black text-[10px] uppercase tracking-wider">
                    Ringkasan Analisis Keuangan
                </span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs font-bold text-slate-500">Basis {{ $daysInPeriod }} Hari Teranalisis</span>
            </div>
            <h2 class="text-base sm:text-lg font-black text-[#163144] tracking-tight">
                @if($overallHealthScore >= 75)
                    Kondisi Finansial Toko Sangat Sehat & Likuid
                @elseif($overallHealthScore >= 50)
                    Kondisi Finansial Stabil, Perhatikan Plafon Belanja Stok
                @else
                    Kondisi Finansial Perlu Pengetatan & Penagihan Piutang
                @endif
            </h2>
            <p class="text-xs text-slate-600 font-semibold leading-relaxed">
                @if($netProfit > 0)
                    Toko mencatatkan laba bersih sebesar <strong class="text-[#163144]">Rp {{ number_format($netProfit, 0, ',', '.') }}</strong> (Margin {{ number_format($netProfitMargin, 1) }}%). Rata-rata omzet harian <strong class="text-[#163144]">Rp {{ number_format($dailyAverageSales, 0, ',', '.') }}</strong> telah melampaui Titik Impas (BEP) harian Rp {{ number_format($bepOmzetHarian, 0, ',', '.') }}. Hak pemilik aman ditarik dengan batas wajar.
                @else
                    Omzet periode ini belum menutup total beban operasional toko. Prioritaskan peningkatan volume penjualan produk ber-margin tinggi dan tunda penarikan dividen pemilik sampai kas stabil.
                @endif
            </p>
        </div>

        <!-- Health Score Widget -->
        <div class="glass-pill-plek p-4 rounded-2xl border border-white/60 flex items-center gap-4 shrink-0 justify-center">
            <div class="text-center">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Skor Kesehatan</span>
                <span class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-[#163144] mt-0.5 block">
                    {{ $overallHealthScore }}<span class="text-xs font-bold text-slate-400">/100</span>
                </span>
                <span class="text-[10px] font-black px-3 py-0.5 rounded-full inline-block mt-1 glass-pill-plek
                    {{ $overallHealthScore >= 75 ? 'text-emerald-800' : ($overallHealthScore >= 50 ? 'text-amber-800' : 'text-rose-700') }}">
                    {{ $overallHealthScore >= 75 ? 'Likuid & Aman' : ($overallHealthScore >= 50 ? 'Waspada Moderat' : 'Perlu Evaluasi') }}
                </span>
            </div>
        </div>
    </div>

    <!-- 4 Key Financial Guardrail Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        
        <!-- 1. BEP & Omzet Harian -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Omzet</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Omzet Harian</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    BEP
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($dailyAverageSales, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] font-bold mt-0.5 truncate {{ $dailyAverageSales >= $bepOmzetHarian ? 'text-emerald-700' : 'text-rose-600' }}">
                    Min BEP: Rp {{ number_format($bepOmzetHarian, 0, ',', '.') }}/hari
                </p>
            </div>
        </div>

        <!-- 2. Plafon Belanja Stok Mingguan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Belanja</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Plafon Stok</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Mingguan
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($recommendedPurchaseCeilingWeekly, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Maksimal belanja / minggu</p>
            </div>
        </div>

        <!-- 3. Rasio Beban Listrik Freezer -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Listrik</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Beban Freezer</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ number_format($electricityRatio, 1) }}%
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ number_format($electricityRatio, 1) }}% <span class="text-[10px] sm:text-xs font-bold text-slate-400 font-sans">omzet</span>
                </span>
                <p class="text-[10px] sm:text-[11px] font-bold mt-0.5 truncate {{ $electricityRatio <= 12 ? 'text-emerald-700' : 'text-amber-800' }}">
                    {{ $electricityRatio <= 12 ? 'Efisiensi Baik (< 12%)' : 'Perlu Optimasi (> 12%)' }}
                </p>
            </div>
        </div>

        <!-- 4. Laba Bersih / True Profit -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Profit</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Laba Bersih</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ number_format($netProfitMargin, 1) }}%
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black font-mono tracking-tight block {{ $netProfit >= 0 ? 'text-emerald-800' : 'text-rose-600' }}">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Margin: {{ number_format($netProfitMargin, 1) }}%</p>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 1: PEMBAGIAN & ALOKASI KEUANGAN TOKO LENGKAP (6 POS UTAMA) ======== -->
    <!-- ========================================================================= -->
    <div class="glass-card-plek rounded-[24px] p-4 sm:p-5 space-y-4 sm:space-y-5">
        
        <!-- Header Alokasi & Presets -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-white/40">
            <div>
                <h2 class="text-base font-black text-[#163144]">Pembagian & Alokasi Keuangan Toko (6 Pos Utama)</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Panduan pembagian pendapatan & laba untuk operasional harian, gaji, hak pemilik, belanja stok, dan dana darurat.</p>
            </div>

            <!-- Quick Presets -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0 sm:flex-wrap print:hidden">
                <span class="text-xs font-extrabold text-slate-400 mr-1 shrink-0">Preset:</span>
                <button 
                    type="button" 
                    @click="setAllocationPreset(40, 20, 15, 10, 10, 5)"
                    class="btn-glass-action text-xs px-3 py-1.5 rounded-full font-bold shrink-0"
                >
                    Standar UMKM (40/20/15/10/10/5)
                </button>
                <button 
                    type="button" 
                    @click="setAllocationPreset(45, 15, 15, 10, 10, 5)"
                    class="btn-glass-action text-xs px-3 py-1.5 rounded-full font-bold shrink-0"
                >
                    Agresif Restock (45/15/15/10/10/5)
                </button>
                <button 
                    type="button" 
                    @click="setAllocationPreset(35, 20, 15, 15, 10, 5)"
                    class="btn-glass-action text-xs px-3 py-1.5 rounded-full font-bold shrink-0"
                >
                    Prioritas Dividen (35/20/15/15/10/5)
                </button>
            </div>
        </div>

        <!-- Total Allocation Bar -->
        <div class="space-y-1.5 glass-pill-plek p-3.5 rounded-2xl border border-white/60">
            <div class="flex items-center justify-between text-xs font-bold">
                <span class="text-[#163144]">Total Pembagian Alokasi:</span>
                <span class="font-mono font-black" :class="getTotalAllocPercent() === 100 ? 'text-emerald-700' : 'text-rose-600'" x-text="getTotalAllocPercent() + '% (Total 100%)'"></span>
            </div>
            <div class="w-full h-3 bg-white/70 rounded-full overflow-hidden flex border border-white/80">
                <div class="bg-[#163144] h-full transition-all duration-300" :style="'width: ' + allocRestock + '%'" title="Modal Restock"></div>
                <div class="bg-[#386797] h-full transition-all duration-300" :style="'width: ' + allocOpex + '%'" title="Operasional Harian"></div>
                <div class="bg-[#5b89b8] h-full transition-all duration-300" :style="'width: ' + allocPayroll + '%'" title="Penggajian"></div>
                <div class="bg-amber-600 h-full transition-all duration-300" :style="'width: ' + allocOwner + '%'" title="Hak Pemilik"></div>
                <div class="bg-emerald-600 h-full transition-all duration-300" :style="'width: ' + allocEmergency + '%'" title="Dana Darurat"></div>
                <div class="bg-purple-600 h-full transition-all duration-300" :style="'width: ' + allocExpansion + '%'" title="Tabungan Ekspansi"></div>
            </div>
        </div>

        <!-- Grid 6 Kartu Pos Keuangan -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
            
            <!-- 1. Modal Belanja Stok (Restock) -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">1. Modal Belanja Stok</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Kulakan frozen food & perputaran dagang</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-black text-xs font-mono" x-text="allocRestock + '%'">{{ $allocRestockPercent }}%</span>
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Alokasi Rupiah</span>
                    <p class="text-xl font-black text-[#163144] font-mono mt-0.5" x-text="formatRp(getAllocNominal(allocRestock))">
                        Rp {{ number_format($allocRestockNominal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="pt-2.5 border-t border-white/40 flex items-center justify-between text-[11px] print:hidden">
                    <span class="text-slate-500 font-bold">Atur Persentase:</span>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="allocRestock = Math.max(0, allocRestock - 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">-</button>
                        <span class="w-8 text-center font-black font-mono text-xs text-[#163144]" x-text="allocRestock + '%'"></span>
                        <button type="button" @click="allocRestock = Math.min(100, allocRestock + 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">+</button>
                    </div>
                </div>
            </div>

            <!-- 2. Operasional Harian Toko (OPEX) -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">2. Operasional Harian (OPEX)</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Listrik PLN, plastik packing, kebersihan, wifi</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-black text-xs font-mono" x-text="allocOpex + '%'">{{ $allocOpexPercent }}%</span>
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Alokasi Rupiah</span>
                    <p class="text-xl font-black text-[#163144] font-mono mt-0.5" x-text="formatRp(getAllocNominal(allocOpex))">
                        Rp {{ number_format($allocOpexNominal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="pt-2.5 border-t border-white/40 flex items-center justify-between text-[11px] print:hidden">
                    <span class="text-slate-500 font-bold">Atur Persentase:</span>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="allocOpex = Math.max(0, allocOpex - 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">-</button>
                        <span class="w-8 text-center font-black font-mono text-xs text-[#163144]" x-text="allocOpex + '%'"></span>
                        <button type="button" @click="allocOpex = Math.min(100, allocOpex + 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">+</button>
                    </div>
                </div>
            </div>

            <!-- 3. Pos Penggajian Karyawan (Payroll) -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">3. Pos Penggajian Karyawan</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Cadangan gaji pokok, uang makan, lembur</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-black text-xs font-mono" x-text="allocPayroll + '%'">{{ $allocPayrollPercent }}%</span>
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Alokasi Rupiah</span>
                    <p class="text-xl font-black text-[#163144] font-mono mt-0.5" x-text="formatRp(getAllocNominal(allocPayroll))">
                        Rp {{ number_format($allocPayrollNominal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="pt-2.5 border-t border-white/40 flex items-center justify-between text-[11px] print:hidden">
                    <span class="text-slate-500 font-bold">Atur Persentase:</span>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="allocPayroll = Math.max(0, allocPayroll - 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">-</button>
                        <span class="w-8 text-center font-black font-mono text-xs text-[#163144]" x-text="allocPayroll + '%'"></span>
                        <button type="button" @click="allocPayroll = Math.min(100, allocPayroll + 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">+</button>
                    </div>
                </div>
            </div>

            <!-- 4. Hak Pemilik / Prive (Dividen Owner Aman) -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">4. Hak Pemilik / Dividen</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Keuntungan pasif & prive aman pemilik</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-900 font-black text-xs font-mono" x-text="allocOwner + '%'">{{ $allocOwnerPercent }}%</span>
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Alokasi Rupiah</span>
                    <p class="text-xl font-black text-[#163144] font-mono mt-0.5" x-text="formatRp(getAllocNominal(allocOwner))">
                        Rp {{ number_format($allocOwnerNominal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="pt-2.5 border-t border-white/40 flex items-center justify-between text-[11px] print:hidden">
                    <span class="text-slate-500 font-bold">Atur Persentase:</span>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="allocOwner = Math.max(0, allocOwner - 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">-</button>
                        <span class="w-8 text-center font-black font-mono text-xs text-[#163144]" x-text="allocOwner + '%'"></span>
                        <button type="button" @click="allocOwner = Math.min(100, allocOwner + 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">+</button>
                    </div>
                </div>
            </div>

            <!-- 5. Dana Darurat & Maintenance Freezer -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">5. Dana Darurat & Servis</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Servis freezer, solar genset, risiko rusak</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-black text-xs font-mono" x-text="allocEmergency + '%'">{{ $allocEmergencyPercent }}%</span>
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Alokasi Rupiah</span>
                    <p class="text-xl font-black text-emerald-800 font-mono mt-0.5" x-text="formatRp(getAllocNominal(allocEmergency))">
                        Rp {{ number_format($allocEmergencyNominal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="pt-2.5 border-t border-white/40 flex items-center justify-between text-[11px] print:hidden">
                    <span class="text-slate-500 font-bold">Atur Persentase:</span>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="allocEmergency = Math.max(0, allocEmergency - 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">-</button>
                        <span class="w-8 text-center font-black font-mono text-xs text-[#163144]" x-text="allocEmergency + '%'"></span>
                        <button type="button" @click="allocEmergency = Math.min(100, allocEmergency + 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">+</button>
                    </div>
                </div>
            </div>

            <!-- 6. Tabungan Ekspansi Toko -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 flex flex-col justify-between space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-black text-xs text-[#163144] uppercase tracking-wider">6. Tabungan Ekspansi</h3>
                        <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Beli freezer baru, renovasi kios, cabang</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-purple-900 font-black text-xs font-mono" x-text="allocExpansion + '%'">{{ $allocExpansionPercent }}%</span>
                </div>

                <div>
                    <span class="text-[10px] text-slate-400 block font-bold uppercase">Alokasi Rupiah</span>
                    <p class="text-xl font-black text-purple-900 font-mono mt-0.5" x-text="formatRp(getAllocNominal(allocExpansion))">
                        Rp {{ number_format($allocExpansionNominal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="pt-2.5 border-t border-white/40 flex items-center justify-between text-[11px] print:hidden">
                    <span class="text-slate-500 font-bold">Atur Persentase:</span>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="allocExpansion = Math.max(0, allocExpansion - 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">-</button>
                        <span class="w-8 text-center font-black font-mono text-xs text-[#163144]" x-text="allocExpansion + '%'"></span>
                        <button type="button" @click="allocExpansion = Math.min(100, allocExpansion + 5)" class="w-6 h-6 rounded-full btn-glass-action text-[#163144] font-bold cursor-pointer">+</button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Analisis Keamanan Penarikan Kas Hak Pemilik (Owner Draw Safety Limit) -->
        <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider glass-pill-plek
                        {{ $ownerDrawStatus === 'aman_ditarik' ? 'text-emerald-800' : ($ownerDrawStatus === 'tahan_rugi' ? 'text-rose-700' : 'text-amber-800') }}">
                        {{ $ownerDrawStatus === 'aman_ditarik' ? 'Hak Pemilik Aman Ditarik' : ($ownerDrawStatus === 'tahan_rugi' ? 'Tahan Penarikan (Defisit)' : 'Waspada Hutang / BEP') }}
                    </span>
                    <span class="text-xs font-black text-[#163144]">Batas Penarikan Kas Hak Pemilik Bulan Ini</span>
                </div>
                <p class="text-xs text-slate-600 font-semibold">
                    @if($ownerDrawStatus === 'aman_ditarik')
                        Kas toko dalam kondisi sehat dan tidak ada hutang supplier jatuh tempo. Pemilik aman menarik dividen hingga maksimal <strong class="text-[#163144]">Rp {{ number_format($maxSafeOwnerDraw, 0, ',', '.') }}</strong>.
                    @elseif($ownerDrawStatus === 'tahan_rugi')
                        Toko belum mencatatkan laba bersih pada periode ini. Disarankan <strong>tidak melakukan penarikan dividen (Prive)</strong> agar arus kas tidak defisit.
                    @else
                        Ada hutang supplier sebesar <strong class="text-amber-800">Rp {{ number_format($totalOverduePayable, 0, ',', '.') }}</strong> yang harus diprioritaskan pelunasannya terlebih dahulu sebelum mengambil hak pemilik.
                    @endif
                </p>
            </div>

            <div class="text-right shrink-0 bg-white/70 p-3.5 rounded-2xl border border-white/80 shadow-2xs">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase block">Maksimal Aman Ditarik:</span>
                <span class="text-xl font-black font-mono {{ $maxSafeOwnerDraw > 0 ? 'text-emerald-800' : 'text-slate-500' }}">
                    Rp {{ number_format($maxSafeOwnerDraw, 0, ',', '.') }}
                </span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 2: SIMULATOR & INPUT PENGGAJIAN KARYAWAN LENGKAP ================ -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- Kolom Kiri: Form Input & Simulator Jumlah Karyawan & Gaji -->
        <div class="lg:col-span-2 glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-white/40">
                <div>
                    <h3 class="font-black text-[#163144] text-sm sm:text-base">Kalkulator Penggajian & Jumlah Karyawan</h3>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Hitung total beban gaji toko dan target omzet yang wajib dicapai kasir</p>
                </div>

                @if($realActiveEmployeesCount > 0)
                    <button 
                        type="button" 
                        wire:click="useRealEmployeeData" 
                        class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs flex items-center gap-1.5 self-start sm:self-auto"
                    >
                        <svg class="w-3.5 h-3.5 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Tarik Data Riil Master ({{ $realActiveEmployeesCount }} Orang)</span>
                    </button>
                @endif
            </div>

            <!-- Form Grid: Jumlah Karyawan, Gaji Pokok, Tunjangan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                
                <!-- 1. Input Jumlah Karyawan -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold text-[#163144] uppercase tracking-wider">Jumlah Karyawan</label>
                    <div class="flex items-center border border-white/80 rounded-xl bg-white/70 overflow-hidden shadow-2xs">
                        <button 
                            type="button" 
                            @click="empCount = Math.max(1, empCount - 1); $wire.set('simEmployeeCount', empCount)" 
                            class="w-10 h-10 bg-white/50 hover:bg-white font-black text-[#163144] text-sm transition cursor-pointer"
                        >-</button>
                        <input 
                            type="number" 
                            min="1" 
                            x-model.number="empCount"
                            @change="$wire.set('simEmployeeCount', empCount)"
                            class="w-full text-center bg-transparent font-black font-mono text-sm text-[#163144] outline-none"
                        >
                        <button 
                            type="button" 
                            @click="empCount = empCount + 1; $wire.set('simEmployeeCount', empCount)" 
                            class="w-10 h-10 bg-white/50 hover:bg-white font-black text-[#163144] text-sm transition cursor-pointer"
                        >+</button>
                    </div>
                    <span class="text-[10px] text-slate-500 block text-center font-bold" x-text="empCount + ' Orang Karyawan Toko'"></span>
                </div>

                <!-- 2. Gaji Pokok per Orang -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold text-[#163144] uppercase tracking-wider">Gaji Pokok / Orang</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-black text-slate-400 pointer-events-none">Rp</span>
                        <input 
                            type="number" 
                            step="50000" 
                            x-model.number="salaryPerPerson"
                            class="w-full pl-9 pr-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-mono font-black text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        >
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-500 font-bold">
                        <span class="font-mono text-[#163144]" x-text="formatRp(salaryPerPerson)"></span>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="adjustSalary(-100000)" class="px-1.5 py-0.5 btn-glass-action rounded-lg font-extrabold cursor-pointer">-100k</button>
                            <button type="button" @click="adjustSalary(100000)" class="px-1.5 py-0.5 btn-glass-action rounded-lg font-extrabold cursor-pointer">+100k</button>
                        </div>
                    </div>
                </div>

                <!-- 3. Tunjangan & Makan per Orang -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold text-[#163144] uppercase tracking-wider">Tunjangan / Orang</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-black text-slate-400 pointer-events-none">Rp</span>
                        <input 
                            type="number" 
                            step="20000" 
                            x-model.number="allowancePerPerson"
                            class="w-full pl-9 pr-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-mono font-black text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        >
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-500 font-bold">
                        <span class="font-mono text-[#163144]" x-text="formatRp(allowancePerPerson)"></span>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="adjustAllowance(-50000)" class="px-1.5 py-0.5 btn-glass-action rounded-lg font-extrabold cursor-pointer">-50k</button>
                            <button type="button" @click="adjustAllowance(50000)" class="px-1.5 py-0.5 btn-glass-action rounded-lg font-extrabold cursor-pointer">+50k</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Quick Presets Standar Gaji -->
            <div class="space-y-1.5 pt-1">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Pilihan Cepat Standar Posisi:</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button 
                        type="button" 
                        @click="setSalaryPreset(1200000); allowancePerPerson = 150000"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Part-Time / Kasir</span>
                        <span class="text-[10px] text-slate-500 font-bold block">Rp 1.2 Jt + 150k</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setSalaryPreset(1800000); allowancePerPerson = 200000"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Full-Time Standar</span>
                        <span class="text-[10px] text-[#386797] font-bold block">Rp 1.8 Jt + 200k</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setSalaryPreset(2500000); allowancePerPerson = 300000"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Staf Senior / Gudang</span>
                        <span class="text-[10px] text-slate-500 font-bold block">Rp 2.5 Jt + 300k</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setSalaryPreset(3200000); allowancePerPerson = 400000"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Kepala Toko</span>
                        <span class="text-[10px] text-slate-500 font-bold block">Rp 3.2 Jt + 400k</span>
                    </button>
                </div>
            </div>

            <!-- Rekomendasi Beban Gaji & Target Penjualan -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-600 font-bold">Total Beban Gaji Semua Karyawan / Bulan:</span>
                    <span class="text-base font-black font-mono text-[#163144]" x-text="formatRp(getTotalMonthlyPayroll())">
                        Rp {{ number_format($totalMonthlyPayrollBurden, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-slate-600 font-bold">
                    <span>Target Tambahan Omzet Harian (BEP Gaji):</span>
                    <span class="font-black font-mono text-[#386797]" x-text="'+ ' + formatRp(getRequiredDailySales()) + ' /hari'"></span>
                </div>
                <div class="flex items-center justify-between text-slate-600 font-bold">
                    <span>Target Tambahan Omzet Bulanan (BEP Gaji):</span>
                    <span class="font-black font-mono text-[#386797]" x-text="'+ ' + formatRp(getRequiredMonthlySales()) + ' /bulan'"></span>
                </div>
                <div class="pt-2 border-t border-white/40 text-[11px] text-slate-500 font-semibold leading-relaxed">
                    Dengan <strong class="text-[#163144]" x-text="empCount + ' karyawan'"></strong> berbeban gaji total <strong class="text-[#163144]" x-text="formatRp(getTotalMonthlyPayroll())"></strong>, toko wajib menghasilkan omzet minimal <strong class="text-[#163144]" x-text="formatRp(getRequiredDailySales()) + ' per hari'"></strong> hanya untuk menutup biaya penggajian tanpa memotong modal belanja.
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Riil Beban Operasional Toko (OPEX Breakdown) -->
        <div class="glass-card-plek rounded-[24px] p-5 space-y-4">
            <div class="pb-3 border-b border-white/40">
                <h3 class="font-black text-[#163144] text-sm">Rincian Beban Toko Aktual (OPEX)</h3>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Pengeluaran tercatat dalam basis analisis</p>
            </div>

            <div class="space-y-2 text-xs">
                
                <!-- Listrik & Utilitas -->
                <div class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-[#163144] block">Listrik PLN Freezer</span>
                        <span class="text-[10px] text-slate-400 font-semibold">{{ number_format($electricityRatio, 1) }}% dari omzet</span>
                    </div>
                    <span class="font-black font-mono text-[#163144]">Rp {{ number_format($electricityOpex, 0, ',', '.') }}</span>
                </div>

                <!-- Gaji Karyawan Riil Tercatat -->
                <div class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-[#163144] block">Gaji Karyawan</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Penggajian tercatat</span>
                    </div>
                    <span class="font-black font-mono text-[#163144]">Rp {{ number_format($payrollOpex, 0, ',', '.') }}</span>
                </div>

                <!-- Plastik & Packing -->
                <div class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-[#163144] block">Plastik & Packing</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Kantong kresek & ice gel</span>
                    </div>
                    <span class="font-black font-mono text-[#163144]">Rp {{ number_format($packagingOpex, 0, ',', '.') }}</span>
                </div>

                <!-- Servis Freezer -->
                <div class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-[#163144] block">Servis & Perawatan</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Tambah freon & perbaikan</span>
                    </div>
                    <span class="font-black font-mono text-[#163144]">Rp {{ number_format($maintenanceOpex, 0, ',', '.') }}</span>
                </div>

                <!-- Sewa / Lain-lain -->
                <div class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-[#163144] block">Sewa & Operasional Lain</span>
                        <span class="text-[10px] text-slate-400 font-semibold">Kebersihan, wifi, dll</span>
                    </div>
                    <span class="font-black font-mono text-[#163144]">Rp {{ number_format($rentOpex + $otherOpex, 0, ',', '.') }}</span>
                </div>

            </div>

            <div class="pt-3 border-t border-white/40 flex items-center justify-between text-xs">
                <span class="font-extrabold text-slate-500">Total Beban OPEX:</span>
                <span class="font-black font-mono text-sm text-[#163144]">Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 3: SIMULASI INVESTASI FREEZER & TARGET BEP ====================== -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <!-- Simulator 1: Kelayakan Beli Freezer Baru -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-white/40">
                <div>
                    <h3 class="font-black text-[#163144] text-sm">Simulasi Investasi Freezer Baru</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Cek payback period dan kelayakan beli freezer baru terhadap arus kas</p>
                </div>
                <span 
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black transition"
                    :class="getFreezerStatus().bgClass"
                    x-text="getFreezerStatus().label"
                >
                    {{ str_replace('_', ' ', $simInvestmentFeasibility) }}
                </span>
            </div>

            <!-- Quick Preset Buttons Freezer -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Pilihan Cepat Kapasitas:</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button 
                        type="button" 
                        @click="setFreezerPreset(3500000, 2500000, 180000)"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Kapasitas 200L</span>
                        <span class="text-[9px] text-slate-500 font-bold block">Rp 3.5 Jt</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setFreezerPreset(4800000, 3500000, 230000)"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Kapasitas 350L</span>
                        <span class="text-[9px] text-slate-500 font-bold block">Rp 4.8 Jt</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setFreezerPreset(6500000, 4500000, 280000)"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Kapasitas 500L</span>
                        <span class="text-[9px] text-[#386797] font-bold block">Rp 6.5 Jt</span>
                    </button>
                    <button 
                        type="button" 
                        @click="setFreezerPreset(12000000, 9000000, 550000)"
                        class="p-2.5 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/80 text-left transition cursor-pointer"
                    >
                        <span class="font-black text-[11px] text-[#163144] block">Kapasitas 1000L</span>
                        <span class="text-[9px] text-slate-500 font-bold block">Rp 12 Jt</span>
                    </button>
                </div>
            </div>

            <!-- Form Simulator Inputs with Rupiah Helpers -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider">Harga Freezer</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-xs font-black text-slate-400 pointer-events-none">Rp</span>
                        <input 
                            type="number" 
                            step="100000" 
                            x-model.number="freezerPrice"
                            class="w-full pl-8 pr-2 py-1.5 bg-white/70 border border-white/80 rounded-xl text-xs font-mono font-black text-[#163144] focus:bg-white focus:border-[#163144] outline-none"
                        >
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider">Target Omzet/Bln</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-xs font-black text-[#386797] pointer-events-none">Rp</span>
                        <input 
                            type="number" 
                            step="100000" 
                            x-model.number="freezerRevenue"
                            class="w-full pl-8 pr-2 py-1.5 bg-white/70 border border-white/80 rounded-xl text-xs font-mono font-black text-[#386797] focus:bg-white focus:border-[#163144] outline-none"
                        >
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider">Listrik PLN/Bln</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-xs font-black text-amber-500 pointer-events-none">Rp</span>
                        <input 
                            type="number" 
                            step="20000" 
                            x-model.number="freezerElectricity"
                            class="w-full pl-8 pr-2 py-1.5 bg-white/70 border border-white/80 rounded-xl text-xs font-mono font-black text-amber-800 focus:bg-white focus:border-[#163144] outline-none"
                        >
                    </div>
                </div>
            </div>

            <!-- Hasil Rekomendasi Simulator -->
            <div class="p-4 rounded-2xl glass-pill-plek border border-white/60 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-600 font-bold">Kontribusi Laba Bersih Tambahan:</span>
                    <span class="font-black font-mono text-emerald-800" x-text="'+ ' + formatRp(getFreezerNetContribution()) + '/bulan'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-600 font-bold">Estimasi Balik Modal (Payback):</span>
                    <span class="font-black font-mono text-[#163144]" x-text="getFreezerPaybackMonths() + ' Bulan'"></span>
                </div>
                <div class="pt-2 border-t border-white/40 text-[11px] text-slate-500 font-semibold leading-relaxed" x-text="getFreezerStatus().note"></div>
            </div>
        </div>

        <!-- Target Penjualan Minimum & Optimal (BEP) -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-white/40">
                <div>
                    <h3 class="font-black text-[#163144] text-sm">Target Penjualan Minimum & BEP</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Batas omzet wajib toko untuk mencegah kerugian</p>
                </div>
                <span class="px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-xs font-mono">
                    Margin: {{ number_format($grossMarginPercent, 1) }}%
                </span>
            </div>

            <!-- Progress Meter BEP -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-600">Pencapaian Titik Impas (BEP):</span>
                    <span class="font-black font-mono {{ $dailyAverageSales >= $bepOmzetHarian ? 'text-emerald-800' : 'text-rose-600' }}">
                        {{ number_format($bepAchievementPercent, 1) }}% dari BEP
                    </span>
                </div>
                <div class="w-full h-3 bg-white/70 rounded-full overflow-hidden flex border border-white/80">
                    <div 
                        class="h-full rounded-full transition-all duration-500 {{ $dailyAverageSales >= $bepOmzetHarian ? 'bg-emerald-600' : 'bg-rose-600' }}"
                        style="width: {{ min(100, $bepAchievementPercent) }}%"
                    ></div>
                </div>
            </div>

            <!-- 2 Kolom Komparasi: Titik Impas vs Target Optimal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60">
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-600 block">Titik Impas (BEP Minimal)</span>
                    <p class="text-base font-black text-[#163144] font-mono mt-1">
                        Rp {{ number_format($bepOmzetHarian, 0, ',', '.') }} <span class="text-[10px] font-bold text-slate-500">/hari</span>
                    </p>
                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">Rp {{ number_format($bepOmzetTotal, 0, ',', '.') }} /periode</span>
                </div>

                <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60">
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block">Target Optimal Toko</span>
                    <p class="text-base font-black text-[#163144] font-mono mt-1">
                        <span x-text="formatRp(getTargetOmzetDaily())">Rp {{ number_format($targetOmzetHarian, 0, ',', '.') }}</span> <span class="text-[10px] font-bold text-slate-500">/hari</span>
                    </p>
                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5" x-text="formatRp(getTargetOmzetTotal()) + ' /periode'"></span>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 4: DETEKSI DEADSTOCK & REKOMENDASI DISKON ======================= -->
    <!-- ========================================================================= -->
    @if($deadstockCandidates->count() > 0)
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-white/40">
                <div>
                    <h3 class="font-black text-[#163144] text-sm">Deteksi Deadstock & Rekomendasi Diskon Kasir</h3>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Produk frozen food dengan stok menumpuk di freezer yang penjualannya lambat dalam periode ini</p>
                </div>
                <a href="{{ route('admin.discounts.index') }}" wire:navigate class="text-xs font-black text-[#386797] hover:underline print:hidden">
                    Buat Voucher Diskon Kasir →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($deadstockCandidates as $deadProduct)
                    @php
                        $productProfit = $deadProduct->selling_price - $deadProduct->cost_price;
                        $productMarginPercent = $deadProduct->selling_price > 0 ? ($productProfit / $deadProduct->selling_price) * 100 : 0;
                        $maxSafeDiscountPercent = max(5, floor($productMarginPercent - 5));
                    @endphp
                    <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 flex flex-col justify-between space-y-2">
                        <div>
                            <span class="font-extrabold text-[#163144] text-xs block truncate">{{ $deadProduct->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono font-semibold block">Sisa Stok: {{ $deadProduct->stock }} {{ $deadProduct->unit?->name ?? 'pcs' }}</span>
                        </div>

                        <div class="pt-2 border-t border-white/40 flex items-center justify-between text-xs">
                            <span class="text-[11px] text-slate-500 font-semibold">Rekomendasi Diskon:</span>
                            <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-900 font-black text-[10px] font-mono">
                                Diskon Maks {{ $maxSafeDiscountPercent }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
