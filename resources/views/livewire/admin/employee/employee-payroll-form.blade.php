<div class="space-y-4">
    @php
        $words = preg_split("/\s+/", trim($employee->name));
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($employee->name, 0, 2));
        }
    @endphp

    <!-- Top Navigation & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-[#386797] uppercase tracking-wider mb-0.5">
                <a href="{{ route('admin.employees.index') }}" wire:navigate class="hover:underline">Karyawan</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('admin.employees.show', $employee->id) }}" wire:navigate class="hover:underline">{{ $employee->name }}</a>
                <span class="text-slate-300">/</span>
                <span class="font-black text-[#163144]">{{ $isEditing ? 'Edit Slip Gaji' : 'Proses Gaji Baru' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-black text-[#163144] tracking-tight">
                    {{ $isEditing ? 'Edit Data Penggajian' : 'Proses Penggajian Karyawan' }}
                </h1>
                @if ($isEditing)
                    <span class="font-mono text-xs font-black glass-pill-plek text-[#163144] px-2.5 py-0.5 rounded-full">
                        {{ $payroll->payroll_number }}
                    </span>
                @else
                    <span class="text-[11px] font-black glass-pill-plek text-emerald-800 px-2.5 py-0.5 rounded-full">
                        + Penggajian Baru
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                {{ $isEditing 
                    ? 'Perbarui rincian pendapatan atau potongan gaji. Biaya pada Buku Beban OPEX akan disesuaikan otomatis.' 
                    : 'Hitung rincian gaji pokok, bonus, dan potongan kasbon staf. Slip gaji dan beban OPEX otomatis terbit.' }}
            </p>
        </div>

        <div>
            <a 
                href="{{ route('admin.employees.show', $employee->id) }}" 
                wire:navigate 
                class="btn-glass-action inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Detail</span>
            </a>
        </div>
    </div>

    <!-- Employee Information Header Banner -->
    <div class="glass-card-plek rounded-[24px] p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <!-- Left: Avatar & Identity -->
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#163144] text-white font-black text-xl sm:text-2xl flex items-center justify-center shadow-md shadow-[#163144]/20 shrink-0 select-none">
                    {{ $initials }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-black text-[#163144]">{{ $employee->name }}</h2>
                        <span class="font-mono text-xs font-black glass-pill-plek text-[#163144] px-2.5 py-0.5 rounded-full">{{ $employee->employee_code }}</span>
                        @if ($employee->user)
                            <span class="text-[11px] font-bold text-[#386797] glass-pill-plek px-2.5 py-0.5 rounded-full">
                                Akun POS: {{ $employee->user->name }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 font-semibold mt-1 flex flex-wrap items-center gap-2">
                        <span class="text-[#163144] font-black">{{ $employee->position }}</span>
                        <span>•</span>
                        <span class="capitalize">{{ $employee->employment_status }}</span>
                        @if ($employee->bank_name)
                            <span>•</span>
                            <span class="glass-pill-plek px-2.5 py-0.5 rounded-full text-[#163144] font-bold">
                                {{ $employee->bank_name }}: <strong class="font-mono">{{ $employee->bank_account_number }}</strong>
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Right: Quick Stat Chips -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-3 lg:pt-0 border-t lg:border-t-0 border-white/40">
                <div class="glass-pill-plek px-4 py-2.5 rounded-2xl border border-white/60">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Gaji Pokok Default</span>
                    <span class="font-mono font-black text-[#163144] text-sm mt-0.5 block">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</span>
                </div>
                <div class="glass-pill-plek px-4 py-2.5 rounded-2xl border border-white/60">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 block">Sisa Saldo Kasbon</span>
                    <span class="font-mono font-black text-amber-900 text-sm mt-0.5 block">
                        Rp {{ number_format($this->max_available_loan_deduction, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Form -->
    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

            <!-- LEFT COLUMN: Form Inputs (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">

                <!-- 1. Periode & Tanggal -->
                <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-2 border-b border-white/40 pb-3">
                        <div class="w-7 h-7 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs">1</div>
                        <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">Periode & Tanggal Pembayaran</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <!-- Bulan -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Bulan Periode <span class="text-rose-500">*</span></label>
                            <select wire:model.live="period_month" class="w-full px-3.5 py-2.5 rounded-xl border border-white/80 text-xs font-bold text-[#163144] bg-white/70 focus:bg-white focus:border-[#163144] cursor-pointer outline-none shadow-2xs">
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <!-- Tahun -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Tahun <span class="text-rose-500">*</span></label>
                            <input type="number" wire:model.live="period_year" class="w-full px-3.5 py-2.5 rounded-xl border border-white/80 font-mono text-xs font-bold text-[#163144] bg-white/70 focus:bg-white focus:border-[#163144] outline-none shadow-2xs">
                            @error('period_year') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tanggal Bayar -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Tanggal Bayar <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="payment_date" class="w-full px-3.5 py-2.5 rounded-xl border border-white/80 text-xs font-bold text-[#163144] bg-white/70 focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer">
                            @error('payment_date') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- 2. Komponen Penerimaan (+) -->
                <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-white/40 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs">2</div>
                            <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">Komponen Penerimaan (Penambah)</h2>
                        </div>
                        <span class="text-xs font-black text-emerald-800 glass-pill-plek px-3 py-1 rounded-full">
                            Subtotal: Rp {{ number_format($this->total_income, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Gaji Pokok Input Auto Rupiah -->
                    <div 
                        x-data="{
                            displayVal: '',
                            init() {
                                this.updateDisplay($wire.payroll_base_salary);
                                $watch('$wire.payroll_base_salary', val => this.updateDisplay(val));
                            },
                            updateDisplay(val) {
                                if (!val && val !== 0) { this.displayVal = ''; return; }
                                let num = parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                            },
                            handleInput(e) {
                                let clean = e.target.value.replace(/[^0-9]/g, '');
                                let num = parseInt(clean) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                                $wire.set('payroll_base_salary', num);
                            }
                        }"
                    >
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider">Gaji Pokok (Rp) <span class="text-rose-500">*</span></label>
                            @if ((int)$payroll_base_salary !== (int)$employee->base_salary)
                                <button 
                                    type="button" 
                                    wire:click="$set('payroll_base_salary', {{ (int)$employee->base_salary }})" 
                                    class="text-[11px] font-bold text-[#386797] hover:underline transition cursor-pointer"
                                >
                                    ↺ Reset ke Default (Rp {{ number_format($employee->base_salary, 0, ',', '.') }})
                                </button>
                            @endif
                        </div>
                        <div class="flex items-center rounded-xl border border-white/80 bg-white/70 focus-within:bg-white focus-within:border-[#163144] overflow-hidden shadow-2xs transition">
                            <span class="px-3.5 py-2.5 bg-white/50 text-slate-500 font-black text-xs border-r border-white/80 select-none shrink-0">
                                Rp
                            </span>
                            <input 
                                type="text" 
                                x-model="displayVal"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                placeholder="0"
                                class="w-full px-3.5 py-2.5 bg-transparent font-mono text-sm font-black text-[#163144] outline-none placeholder:text-slate-400"
                                required
                            >
                        </div>
                        @error('payroll_base_salary') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Bonus / Insentif Input Auto Rupiah -->
                    <div 
                        x-data="{
                            displayVal: '',
                            init() {
                                this.updateDisplay($wire.allowance_amount);
                                $watch('$wire.allowance_amount', val => this.updateDisplay(val));
                            },
                            updateDisplay(val) {
                                if (!val && val !== 0) { this.displayVal = ''; return; }
                                let num = parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                            },
                            handleInput(e) {
                                let clean = e.target.value.replace(/[^0-9]/g, '');
                                let num = parseInt(clean) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                                $wire.set('allowance_amount', num);
                            }
                        }"
                    >
                        <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Bonus / Lembur / Tunjangan Lain (Rp)</label>
                        <div class="flex items-center rounded-xl border border-white/80 bg-white/70 focus-within:bg-white focus-within:border-emerald-500 overflow-hidden shadow-2xs transition">
                            <span class="px-3.5 py-2.5 bg-white/50 text-slate-500 font-black text-xs border-r border-white/80 select-none shrink-0">
                                Rp
                            </span>
                            <input 
                                type="text" 
                                x-model="displayVal"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                placeholder="0"
                                class="w-full px-3.5 py-2.5 bg-transparent font-mono text-sm font-black text-emerald-800 outline-none placeholder:text-slate-400"
                            >
                        </div>
                        @error('allowance_amount') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror

                        <!-- Keterangan Bonus -->
                        <div class="mt-2">
                            <input 
                                type="text" 
                                wire:model="allowance_notes" 
                                placeholder="Keterangan bonus (misal: Insentif target omset + lembur 3 jam)" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-white/80 text-xs bg-white/70 focus:bg-white focus:border-[#163144] outline-none text-[#163144] transition placeholder:text-slate-400 shadow-2xs"
                            >
                        </div>
                    </div>
                </div>

                <!-- 3. Komponen Potongan (-) -->
                <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-white/40 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs">3</div>
                            <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">Komponen Potongan (Pengurang)</h2>
                        </div>
                        <span class="text-xs font-black text-rose-700 glass-pill-plek px-3 py-1 rounded-full">
                            Subtotal: Rp {{ number_format($this->total_deduction, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Kasbon Helper Banner if employee has remaining loans -->
                    @if ($this->max_available_loan_deduction > 0)
                        <div class="p-3.5 rounded-2xl bg-amber-50/90 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="space-y-0.5">
                                <span class="font-extrabold text-amber-950 flex items-center gap-1.5">
                                    <span>💡</span> Karyawan memiliki sisa kasbon:
                                </span>
                                <span class="font-mono font-black text-amber-800 text-sm block">
                                    Rp {{ number_format($this->max_available_loan_deduction, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    wire:click="$set('loan_deduction', {{ (int)($this->max_available_loan_deduction / 2) }})" 
                                    class="btn-glass-action px-3 py-1 rounded-full text-xs font-bold"
                                >
                                    Potong 50%
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="applyMaxLoanDeduction" 
                                    class="px-3.5 py-1 rounded-full bg-amber-600 hover:bg-amber-700 text-white font-black text-xs transition cursor-pointer shadow-xs"
                                >
                                    Potong Semua
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Potongan Kasbon Input Auto Rupiah -->
                    <div 
                        x-data="{
                            displayVal: '',
                            init() {
                                this.updateDisplay($wire.loan_deduction);
                                $watch('$wire.loan_deduction', val => this.updateDisplay(val));
                            },
                            updateDisplay(val) {
                                if (!val && val !== 0) { this.displayVal = ''; return; }
                                let num = parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                            },
                            handleInput(e) {
                                let clean = e.target.value.replace(/[^0-9]/g, '');
                                let num = parseInt(clean) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                                $wire.set('loan_deduction', num);
                            }
                        }"
                    >
                        <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Potongan Kasbon / Pinjaman (Rp)</label>
                        <div class="flex items-center rounded-xl border border-white/80 bg-white/70 focus-within:bg-white focus-within:border-rose-500 overflow-hidden shadow-2xs transition">
                            <span class="px-3.5 py-2.5 bg-white/50 text-slate-500 font-black text-xs border-r border-white/80 select-none shrink-0">
                                Rp
                            </span>
                            <input 
                                type="text" 
                                x-model="displayVal"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                placeholder="0"
                                class="w-full px-3.5 py-2.5 bg-transparent font-mono text-sm font-black text-rose-700 outline-none placeholder:text-slate-400"
                            >
                        </div>
                        @error('loan_deduction') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Potongan Lainnya Input Auto Rupiah -->
                    <div 
                        x-data="{
                            displayVal: '',
                            init() {
                                this.updateDisplay($wire.other_deduction);
                                $watch('$wire.other_deduction', val => this.updateDisplay(val));
                            },
                            updateDisplay(val) {
                                if (!val && val !== 0) { this.displayVal = ''; return; }
                                let num = parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                            },
                            handleInput(e) {
                                let clean = e.target.value.replace(/[^0-9]/g, '');
                                let num = parseInt(clean) || 0;
                                this.displayVal = num ? num.toLocaleString('id-ID') : '';
                                $wire.set('other_deduction', num);
                            }
                        }"
                    >
                        <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Potongan Lainnya (Absensi / Keterlambatan / Denda Rp)</label>
                        <div class="flex items-center rounded-xl border border-white/80 bg-white/70 focus-within:bg-white focus-within:border-rose-500 overflow-hidden shadow-2xs transition">
                            <span class="px-3.5 py-2.5 bg-white/50 text-slate-500 font-black text-xs border-r border-white/80 select-none shrink-0">
                                Rp
                            </span>
                            <input 
                                type="text" 
                                x-model="displayVal"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                placeholder="0"
                                class="w-full px-3.5 py-2.5 bg-transparent font-mono text-sm font-black text-rose-700 outline-none placeholder:text-slate-400"
                            >
                        </div>
                        @error('other_deduction') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror

                        <!-- Keterangan Potongan -->
                        <div class="mt-2">
                            <input 
                                type="text" 
                                wire:model="deduction_notes" 
                                placeholder="Keterangan potongan (misal: Izin tidak masuk 1 hari)" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-white/80 text-xs bg-white/70 focus:bg-white focus:border-[#163144] outline-none text-[#163144] transition placeholder:text-slate-400 shadow-2xs"
                            >
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Settlement Card, Method & Action (5 Cols, Sticky) -->
            <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-6">

                <!-- Settlement Card -->
                <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-white/40 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs">4</div>
                            <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">Ringkasan Slip Gaji</h2>
                        </div>
                        <span class="text-[10px] font-black glass-pill-plek text-[#163144] px-2.5 py-0.5 rounded-full">Real-time</span>
                    </div>

                    <!-- Breakdown Table -->
                    <div class="space-y-2.5 text-xs font-semibold">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Gaji Pokok:</span>
                            <span class="font-mono font-bold text-[#163144]">Rp {{ number_format((float)($payroll_base_salary ?: 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Bonus / Tunjangan:</span>
                            <span class="font-mono font-bold text-emerald-800">+Rp {{ number_format((float)($allowance_amount ?: 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[#163144] font-bold border-t border-white/40 pt-2">
                            <span>Total Penerimaan:</span>
                            <span class="font-mono text-emerald-800 font-black">Rp {{ number_format($this->total_income, 0, ',', '.') }}</span>
                        </div>

                        <div class="border-t border-dashed border-white/60 my-2"></div>

                        <div class="flex items-center justify-between text-slate-600">
                            <span>Potongan Kasbon:</span>
                            <span class="font-mono font-bold text-rose-600">−Rp {{ number_format((float)($loan_deduction ?: 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Potongan Lainnya:</span>
                            <span class="font-mono font-bold text-rose-600">−Rp {{ number_format((float)($other_deduction ?: 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[#163144] font-bold border-t border-white/40 pt-2">
                            <span>Total Potongan:</span>
                            <span class="font-mono text-rose-600 font-black">Rp {{ number_format($this->total_deduction, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Grand Total Take Home Pay Highlight Box -->
                    <div class="p-5 rounded-2xl bg-gradient-to-r from-[#163144] via-[#1f425b] to-[#163144] text-white shadow-md shadow-[#163144]/20 space-y-1.5">
                        <span class="text-[11px] font-bold text-[#a0bddb] uppercase tracking-wider block">Gaji Bersih Diterima (Take Home Pay)</span>
                        <p class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-white">
                            Rp {{ number_format($this->calculated_net_salary, 0, ',', '.') }}
                        </p>
                        <div class="pt-2 flex items-center justify-between text-[11px] text-[#a0bddb] border-t border-white/20">
                            <span>Buku Beban OPEX:</span>
                            <span class="font-bold text-white">✓ Otomatis Tercatat</span>
                        </div>
                    </div>

                    <!-- Payment Method & Notes -->
                    <div class="space-y-3.5 pt-1">
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Metode Pembayaran <span class="text-rose-500">*</span></label>
                            <select wire:model.live="payroll_payment_method" class="w-full px-3.5 py-2.5 rounded-xl border border-white/80 text-xs font-bold text-[#163144] bg-white/70 focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer transition">
                                <option value="transfer">Transfer Bank ({{ $employee->bank_name ?: 'Rekening Karyawan' }})</option>
                                <option value="cash">Uang Tunai (Cash Toko)</option>
                            </select>
                            @if ($payroll_payment_method === 'transfer' && $employee->bank_account_number)
                                <p class="text-[11px] text-slate-500 font-semibold mt-1">
                                    Tujuan: <strong>{{ $employee->bank_name }}</strong> No. <span class="font-mono font-bold text-[#163144]">{{ $employee->bank_account_number }}</span> a.n. {{ $employee->bank_account_holder }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                            <textarea 
                                wire:model="payroll_notes" 
                                rows="2" 
                                placeholder="Catatan slip atau referensi transfer..." 
                                class="w-full px-3.5 py-2 rounded-xl border border-white/80 text-xs bg-white/70 focus:bg-white focus:border-[#163144] outline-none text-[#163144] transition placeholder:text-slate-400 shadow-2xs"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Action Submit -->
                    <div class="space-y-2 pt-2 border-t border-white/40">
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md shadow-[#163144]/20 transition cursor-pointer flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ $isEditing ? 'Simpan Perubahan Slip Gaji' : 'Bayar & Terbitkan Slip Gaji' }}</span>
                        </button>

                        <a 
                            href="{{ route('admin.employees.show', $employee->id) }}" 
                            wire:navigate 
                            class="btn-glass-action w-full py-2.5 px-4 rounded-2xl font-extrabold text-xs text-center justify-center block"
                        >
                            Batal
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>
