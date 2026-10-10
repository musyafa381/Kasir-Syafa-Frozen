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

    <!-- Top Navigation / Breadcrumb -->
    <div class="flex items-center justify-between pb-1">
        <a 
            href="{{ route('admin.employees.index') }}" 
            wire:navigate 
            class="btn-glass-action inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full font-bold text-xs"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Daftar Karyawan</span>
        </a>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.employees.edit', $employee->id) }}" 
                wire:navigate 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-bold text-xs"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Profil</span>
            </a>

            @if ($employee->is_active)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black glass-pill-plek text-emerald-800">
                    ✓ Karyawan Aktif
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black glass-pill-plek text-slate-400">
                    Nonaktif
                </span>
            @endif
        </div>
    </div>

    <!-- Profile Overview Card -->
    <div class="glass-card-plek rounded-[28px] p-5 sm:p-6 space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <!-- Left: Avatar & Personal Info -->
            <div class="flex items-start gap-4 sm:gap-5">
                <!-- Avatar with clear 2-letter initials -->
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#163144] text-white font-black text-xl sm:text-2xl flex items-center justify-center shadow-md shadow-[#163144]/20 shrink-0 select-none tracking-tight">
                    {{ $initials }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-[#163144] tracking-tight">{{ $employee->name }}</h1>
                        <span class="font-mono text-xs font-black glass-pill-plek text-[#163144] px-2.5 py-0.5 rounded-full">{{ $employee->employee_code }}</span>
                        @if ($employee->user)
                            <span class="text-[11px] font-bold text-[#386797] glass-pill-plek px-2 py-0.5 rounded-full">
                                User POS: {{ $employee->user->name }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs font-semibold text-slate-500 mt-1 flex flex-wrap items-center gap-2">
                        <span class="text-[#163144] font-bold">{{ $employee->position }}</span>
                        <span>•</span>
                        <span class="capitalize">{{ $employee->employment_status }}</span>
                        @if ($employee->join_date)
                            <span>•</span>
                            <span>Mulai kerja: {{ $employee->join_date->format('d M Y') }}</span>
                        @endif
                    </p>
                    <div class="flex flex-wrap items-center gap-2.5 mt-2.5 text-xs text-slate-600">
                        @if ($employee->phone)
                            <span class="inline-flex items-center gap-1.5 font-mono glass-pill-plek px-2.5 py-1 rounded-xl font-bold text-[#163144]">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $employee->phone }}
                            </span>
                        @endif
                        @if ($employee->bank_name)
                            <span class="inline-flex items-center gap-1.5 glass-pill-plek px-2.5 py-1 rounded-xl font-bold text-[#163144]">
                                {{ $employee->bank_name }}: <strong class="font-mono text-[#163144]">{{ $employee->bank_account_number }}</strong> (a.n. {{ $employee->bank_account_holder }})
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 pt-3 lg:pt-0 border-t lg:border-t-0 border-white/40">
                <!-- Catat Kasbon -->
                <button 
                    wire:click="openLoanModal" 
                    class="btn-glass-action inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl font-extrabold text-xs"
                >
                    <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Catat Kasbon</span>
                </button>

                <!-- Dedicated Page: Proses Gaji Baru -->
                <a 
                    href="{{ route('admin.employees.payroll.create', $employee->id) }}" 
                    wire:navigate 
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md shadow-[#163144]/20 transition cursor-pointer"
                >
                    <svg class="w-4 h-4 text-[#a0bddb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Proses Gaji Baru</span>
                </a>
            </div>
        </div>

        <!-- Metrics Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 pt-3 border-t border-white/40">
            <div class="glass-pill-plek p-3 rounded-2xl border border-white/60">
                <p class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider truncate">Gaji Pokok</p>
                <p class="text-sm sm:text-lg font-black text-[#163144] font-mono mt-0.5 truncate">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</p>
            </div>
            <div class="glass-pill-plek p-3 rounded-2xl border border-white/60">
                <p class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider truncate">Total Diterima</p>
                <p class="text-sm sm:text-lg font-black text-[#386797] font-mono mt-0.5 truncate">Rp {{ number_format($employee->total_paid_salary, 0, ',', '.') }}</p>
            </div>
            <div class="glass-pill-plek p-3 rounded-2xl border border-white/60">
                <p class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider truncate">Sisa Kasbon</p>
                <p class="text-sm sm:text-lg font-black {{ $employee->total_remaining_loan > 0 ? 'text-amber-800' : 'text-[#163144]' }} font-mono mt-0.5 truncate">
                    Rp {{ number_format($employee->total_remaining_loan, 0, ',', '.') }}
                </p>
            </div>
            <div class="glass-pill-plek p-3 rounded-2xl border border-white/60">
                <p class="text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider truncate">Frekuensi</p>
                <p class="text-sm sm:text-lg font-black text-[#163144] font-mono mt-0.5 truncate">{{ $payrolls->count() }} <span class="text-[10px] sm:text-xs font-bold text-slate-400 font-sans">Kali</span></p>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="space-y-3">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-1 px-1">
            <button 
                wire:click="setTab('payroll')" 
                class="px-4 py-2 rounded-full text-xs font-extrabold transition cursor-pointer flex items-center gap-2 shrink-0 {{ $activeTab === 'payroll' ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Riwayat Penggajian ({{ $payrolls->count() }})</span>
            </button>

            <button 
                wire:click="setTab('loans')" 
                class="px-4 py-2 rounded-full text-xs font-extrabold transition cursor-pointer flex items-center gap-2 shrink-0 {{ $activeTab === 'loans' ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Riwayat Kasbon ({{ $loans->count() }})</span>
                @if ($employee->total_remaining_loan > 0)
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                @endif
            </button>
        </div>

        <!-- Tab 1: PAYROLL HISTORY -->
        @if ($activeTab === 'payroll')
            <div class="glass-card-plek rounded-[24px] overflow-hidden">
                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[780px]">
                        <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                            <tr>
                                <th class="p-3.5 pl-5 whitespace-nowrap">No. Slip & Periode</th>
                                <th class="p-3.5 whitespace-nowrap">Tgl Bayar</th>
                                <th class="p-3.5 whitespace-nowrap text-right">Gaji Pokok</th>
                                <th class="p-3.5 whitespace-nowrap text-right">Bonus/Tunj.</th>
                                <th class="p-3.5 whitespace-nowrap text-right">Potongan</th>
                                <th class="p-3.5 whitespace-nowrap text-right">Gaji Bersih</th>
                                <th class="p-3.5 whitespace-nowrap text-center">Metode & OPEX</th>
                                <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/40 font-semibold">
                            @forelse ($payrolls as $pay)
                                <tr class="hover:bg-white/40 transition">
                                    <!-- No Slip & Periode -->
                                    <td class="p-3.5 pl-5">
                                        <span class="font-extrabold text-[#163144] text-sm block">
                                            {{ $pay->period_label }}
                                        </span>
                                        <span class="font-mono text-[10px] text-[#163144] font-black glass-pill-plek px-2 py-0.5 rounded-full inline-block mt-0.5">
                                            {{ $pay->payroll_number }}
                                        </span>
                                    </td>

                                    <!-- Tgl Bayar -->
                                    <td class="p-3.5 font-mono text-slate-600 whitespace-nowrap">
                                        {{ $pay->payment_date->format('d/m/Y') }}
                                    </td>

                                    <!-- Gaji Pokok -->
                                    <td class="p-3.5 text-right font-mono text-[#163144] font-bold whitespace-nowrap">
                                        Rp {{ number_format($pay->base_salary, 0, ',', '.') }}
                                    </td>

                                    <!-- Tunjangan -->
                                    <td class="p-3.5 text-right font-mono whitespace-nowrap">
                                        @if ($pay->allowance_amount > 0)
                                            <span class="text-emerald-800 font-black block">+Rp {{ number_format($pay->allowance_amount, 0, ',', '.') }}</span>
                                            @if ($pay->allowance_notes)
                                                <span class="text-[10px] text-slate-400 font-sans truncate max-w-[120px] block ml-auto">{{ $pay->allowance_notes }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 font-normal">-</span>
                                        @endif
                                    </td>

                                    <!-- Potongan -->
                                    <td class="p-3.5 text-right font-mono whitespace-nowrap">
                                        @php
                                            $totDed = $pay->loan_deduction + $pay->other_deduction;
                                        @endphp
                                        @if ($totDed > 0)
                                            <span class="text-rose-600 font-black block">−Rp {{ number_format($totDed, 0, ',', '.') }}</span>
                                            @if ($pay->loan_deduction > 0)
                                                <span class="text-[10px] text-amber-800 font-sans block">Kasbon: Rp {{ number_format($pay->loan_deduction, 0, ',', '.') }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 font-normal">-</span>
                                        @endif
                                    </td>

                                    <!-- Gaji Bersih -->
                                    <td class="p-3.5 text-right font-mono font-black text-[#163144] text-sm whitespace-nowrap">
                                        Rp {{ number_format($pay->net_salary, 0, ',', '.') }}
                                    </td>

                                    <!-- Metode & OPEX -->
                                    <td class="p-3.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase glass-pill-plek text-[#163144]">
                                            {{ $pay->payment_method }}
                                        </span>
                                        <span class="block text-[10px] text-emerald-800 font-bold mt-0.5">✓ Masuk OPEX</span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <!-- Cetak Slip Gaji (Buka Halaman Cetak) -->
                                            <a 
                                                href="{{ route('admin.employees.payroll.print', ['employeeId' => $employee->id, 'payrollId' => $pay->id]) }}" 
                                                title="Cetak Slip Gaji"
                                                class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs inline-flex items-center gap-1.5"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                <span>Slip</span>
                                            </a>

                                            <!-- Edit Payroll -->
                                            <a 
                                                href="{{ route('admin.employees.payroll.edit', ['employeeId' => $employee->id, 'payrollId' => $pay->id]) }}" 
                                                wire:navigate
                                                title="Edit Slip Gaji"
                                                class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs inline-flex items-center gap-1"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Edit</span>
                                            </a>

                                            <!-- Hapus Payroll -->
                                            <button 
                                                wire:click="confirmDeletePayroll({{ $pay->id }})" 
                                                title="Batalkan Slip Gaji Ini"
                                                class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400 font-semibold">
                                        Belum ada riwayat penggajian untuk karyawan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List View -->
                <div class="sm:hidden divide-y divide-white/40">
                    @forelse ($payrolls as $pay)
                        <div class="p-3.5 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-extrabold text-[#163144] text-sm block">{{ $pay->period_label }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono font-semibold">{{ $pay->payment_date->format('d/m/Y') }} • {{ $pay->payroll_number }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase glass-pill-plek text-[#163144]">
                                    {{ $pay->payment_method }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between glass-pill-plek p-2.5 rounded-2xl border border-white/60">
                                <div>
                                    <span class="text-[9px] text-slate-400 uppercase font-bold block">Gaji Bersih Diterima</span>
                                    <span class="font-mono font-black text-[#163144] text-sm">Rp {{ number_format($pay->net_salary, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-[10px] text-emerald-800 font-bold">
                                    ✓ Masuk OPEX
                                </span>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 pt-1">
                                <a 
                                    href="{{ route('admin.employees.payroll.print', ['employeeId' => $employee->id, 'payrollId' => $pay->id]) }}" 
                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                >
                                    Slip
                                </a>
                                <a 
                                    href="{{ route('admin.employees.payroll.edit', ['employeeId' => $employee->id, 'payrollId' => $pay->id]) }}" 
                                    wire:navigate 
                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                >
                                    Edit
                                </a>
                                <button 
                                    wire:click="confirmDeletePayroll({{ $pay->id }})" 
                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 font-semibold">
                            Belum ada riwayat penggajian.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Tab 2: LOAN HISTORY -->
        @if ($activeTab === 'loans')
            <div class="glass-card-plek rounded-[24px] overflow-hidden">
                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[780px]">
                        <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                            <tr>
                                <th class="p-3.5 pl-5 whitespace-nowrap">No. Kasbon & Tgl</th>
                                <th class="p-3.5 whitespace-nowrap">Keperluan / Catatan</th>
                                <th class="p-3.5 whitespace-nowrap text-right">Total Kasbon</th>
                                <th class="p-3.5 whitespace-nowrap text-right">Sudah Dibayar</th>
                                <th class="p-3.5 whitespace-nowrap text-right">Sisa Kasbon</th>
                                <th class="p-3.5 whitespace-nowrap text-center">Status</th>
                                <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/40 font-semibold">
                            @forelse ($loans as $loan)
                                <tr class="hover:bg-white/40 transition">
                                    <!-- No & Tgl -->
                                    <td class="p-3.5 pl-5 whitespace-nowrap">
                                        <span class="font-mono font-black text-[#163144] text-xs block">
                                            {{ $loan->loan_number }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono">
                                            {{ $loan->loan_date->format('d/m/Y') }}
                                        </span>
                                    </td>

                                    <!-- Alasan -->
                                    <td class="p-3.5">
                                        <span class="font-bold text-[#163144] block">{{ $loan->reason }}</span>
                                        @if ($loan->notes)
                                            <span class="text-[11px] text-slate-400 block">{{ $loan->notes }}</span>
                                        @endif
                                    </td>

                                    <!-- Total Pinjaman -->
                                    <td class="p-3.5 text-right font-mono font-bold text-[#163144] whitespace-nowrap">
                                        Rp {{ number_format($loan->amount, 0, ',', '.') }}
                                    </td>

                                    <!-- Sudah Dibayar -->
                                    <td class="p-3.5 text-right font-mono text-emerald-800 font-bold whitespace-nowrap">
                                        Rp {{ number_format($loan->paid_amount, 0, ',', '.') }}
                                    </td>

                                    <!-- Sisa Saldo -->
                                    <td class="p-3.5 text-right font-mono font-black {{ $loan->remaining_amount > 0 ? 'text-amber-800' : 'text-slate-400' }} whitespace-nowrap">
                                        Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}
                                    </td>

                                    <!-- Status -->
                                    <td class="p-3.5 text-center whitespace-nowrap">
                                        @if ($loan->status === 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black glass-pill-plek text-emerald-800">
                                                ✓ Lunas
                                            </span>
                                        @elseif ($loan->status === 'partial')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black glass-pill-plek text-amber-800">
                                                Sebagian
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black glass-pill-plek text-rose-700">
                                                Belum Lunas
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            @if ($loan->remaining_amount > 0)
                                                <button 
                                                    wire:click="openPayLoanModal({{ $loan->id }})" 
                                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                                >
                                                    Bayar Tunai
                                                </button>
                                            @endif

                                            <!-- Edit Kasbon -->
                                            <button 
                                                wire:click="openEditLoanModal({{ $loan->id }})" 
                                                title="Edit Kasbon"
                                                class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs inline-flex items-center gap-1"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Hapus Kasbon jika belum ada cicilan -->
                                            @if ($loan->paid_amount == 0)
                                                <button 
                                                    wire:click="confirmDeleteLoan({{ $loan->id }})" 
                                                    title="Hapus Kasbon"
                                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                                >
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 font-semibold">
                                        Belum ada riwayat pinjaman/kasbon untuk karyawan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List View -->
                <div class="sm:hidden divide-y divide-white/40">
                    @forelse ($loans as $loan)
                        <div class="p-3.5 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <span class="font-mono text-[10px] text-[#163144] font-black glass-pill-plek px-2 py-0.5 rounded-full inline-block mb-1">
                                        {{ $loan->loan_number }}
                                    </span>
                                    <h4 class="font-bold text-[#163144] text-sm">{{ $loan->reason }}</h4>
                                    <span class="text-[11px] text-slate-400 font-mono">Tgl: {{ $loan->loan_date->format('d/m/Y') }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase shrink-0 glass-pill-plek {{ $loan->status === 'paid' ? 'text-emerald-800' : 'text-amber-800' }}">
                                    {{ $loan->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-1.5 text-[11px] glass-pill-plek p-2 rounded-2xl border border-white/60 font-mono text-center">
                                <div>
                                    <span class="text-[9px] text-slate-400 block uppercase font-sans font-bold">Pinjaman</span>
                                    <span class="text-[#163144] font-black">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] text-slate-400 block uppercase font-sans font-bold">Dibayar</span>
                                    <span class="text-emerald-800 font-black">Rp {{ number_format($loan->paid_amount, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] text-slate-400 block uppercase font-sans font-bold">Sisa</span>
                                    <span class="font-black {{ $loan->remaining_amount > 0 ? 'text-amber-800' : 'text-slate-400' }}">Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 pt-1">
                                @if ($loan->remaining_amount > 0)
                                    <button 
                                        wire:click="openPayLoanModal({{ $loan->id }})" 
                                        class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                    >
                                        Bayar
                                    </button>
                                @endif
                                <button 
                                    wire:click="openEditLoanModal({{ $loan->id }})" 
                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                >
                                    Edit
                                </button>
                                @if ($loan->paid_amount == 0)
                                    <button 
                                        wire:click="confirmDeleteLoan({{ $loan->id }})" 
                                        class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                    >
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 font-semibold">
                            Belum ada riwayat kasbon.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Form Catat Kasbon Baru (dengan Auto Format Rupiah) -->
    @if ($showLoanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl relative my-auto modal-content-animate">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base leading-tight">Catat Kasbon / Pinjaman</h3>
                            <p class="text-xs text-slate-500 font-medium">Karyawan: <strong class="text-[#163144]">{{ $employee->name }}</strong></p>
                        </div>
                    </div>
                    <button wire:click="$set('showLoanModal', false)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit.prevent="saveLoan" class="p-6 space-y-4">
                    <!-- Nominal Pinjaman Auto Rupiah -->
                    <div 
                        x-data="{
                            displayVal: '',
                            init() {
                                this.updateDisplay($wire.loan_amount);
                                $watch('$wire.loan_amount', val => this.updateDisplay(val));
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
                                $wire.set('loan_amount', num);
                            }
                        }"
                    >
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Nominal Pinjaman (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-sm">Rp</span>
                            <input 
                                type="text" 
                                x-model="displayVal"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                placeholder="Contoh: 300.000" 
                                class="w-full pl-11 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base font-black text-[#163144] focus:border-[#163144] outline-none"
                                required
                            >
                        </div>
                        @error('loan_amount') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="loan_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                        @error('loan_date') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Alasan Pinjaman <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="loan_reason" placeholder="Contoh: Kebutuhan darurat / berobat" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                        @error('loan_reason') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model="loan_notes" placeholder="Catatan internal..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showLoanModal', false)" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">Simpan Kasbon</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Form Edit Kasbon (dengan Auto Format Rupiah) -->
    @if ($showEditLoanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl relative my-auto modal-content-animate">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base leading-tight">Edit Data Kasbon</h3>
                            <p class="text-xs text-slate-500 font-medium">Karyawan: <strong class="text-[#163144]">{{ $employee->name }}</strong></p>
                        </div>
                    </div>
                    <button wire:click="$set('showEditLoanModal', false)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit.prevent="saveEditLoan" class="p-6 space-y-4">
                    @if ($edit_loan_paid > 0)
                        <div class="p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-xs font-semibold">
                            💡 Kasbon ini telah dicicil sebesar: <strong class="font-mono font-black">Rp {{ number_format($edit_loan_paid, 0, ',', '.') }}</strong>. Nominal baru tidak boleh lebih kecil dari jumlah cicilan.
                        </div>
                    @endif

                    <!-- Nominal Pinjaman Auto Rupiah -->
                    <div 
                        x-data="{
                            displayVal: '',
                            init() {
                                this.updateDisplay($wire.edit_loan_amount);
                                $watch('$wire.edit_loan_amount', val => this.updateDisplay(val));
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
                                $wire.set('edit_loan_amount', num);
                            }
                        }"
                    >
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Total Nominal Pinjaman (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-sm">Rp</span>
                            <input 
                                type="text" 
                                x-model="displayVal"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                class="w-full pl-11 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base font-black text-[#163144] focus:border-[#163144] outline-none"
                                required
                            >
                        </div>
                        @error('edit_loan_amount') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="edit_loan_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                        @error('edit_loan_date') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Alasan Pinjaman <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="edit_loan_reason" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                        @error('edit_loan_reason') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model="edit_loan_notes" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showEditLoanModal', false)" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Form Bayar Tunai Kasbon (dengan Auto Format Rupiah) -->
    @if ($showPayLoanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl relative my-auto modal-content-animate">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base leading-tight">Pelunasan Tunai Kasbon</h3>
                            <p class="text-xs text-slate-500 font-medium">Pembayaran langsung di luar potongan gaji.</p>
                        </div>
                    </div>
                    <button wire:click="$set('showPayLoanModal', false)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit.prevent="savePayLoan" class="p-6 space-y-4">
                    <!-- Nominal Bayar Auto Rupiah -->
                    <div 
                        x-data="{
                            displayVal: '',
                            init() {
                                this.updateDisplay($wire.pay_loan_amount);
                                $watch('$wire.pay_loan_amount', val => this.updateDisplay(val));
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
                                $wire.set('pay_loan_amount', num);
                            }
                        }"
                    >
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Nominal Bayar (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-sm">Rp</span>
                            <input 
                                type="text" 
                                x-model="displayVal"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                class="w-full pl-11 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base font-black text-[#163144] focus:border-[#163144] outline-none"
                                required
                            >
                        </div>
                        @error('pay_loan_amount') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Tanggal Bayar</label>
                        <input type="date" wire:model="pay_loan_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Catatan Pembayaran</label>
                        <input type="text" wire:model="pay_loan_notes" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-[#163144] bg-slate-50 focus:border-[#163144] outline-none">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showPayLoanModal', false)" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Delete Kasbon Confirmation -->
    @if ($showDeleteLoanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm w-full p-6 shadow-2xl border border-white/90 text-center space-y-4 relative my-auto modal-content-animate">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100/80 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-[#163144]">Hapus Catatan Kasbon?</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Kasbon <strong class="text-[#163144] font-bold font-mono">"{{ $deletingLoanNumber }}"</strong> akan dihapus permanen dari sistem.
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" wire:click="$set('showDeleteLoanModal', false)" class="flex-1 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">Batal</button>
                    <button type="button" wire:click="deleteLoan" class="flex-1 py-2.5 rounded-full bg-rose-600/90 hover:bg-rose-700 text-white font-black text-xs shadow-md shadow-rose-950/20 transition active:scale-95 hover:scale-105 cursor-pointer">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Delete Payroll Confirmation -->
    @if ($showDeletePayrollModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm w-full p-6 shadow-2xl border border-white/90 text-center space-y-4 relative my-auto modal-content-animate">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100/80 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-[#163144]">Batalkan Payroll Ini?</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Catatan penggajian <strong class="text-[#163144] font-bold font-mono">"{{ $deletingPayrollNumber }}"</strong> akan dihapus, beban gaji di Buku OPEX akan otomatis dihapus, dan saldo kasbon akan dikembalikan.
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" wire:click="$set('showDeletePayrollModal', false)" class="flex-1 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">Batal</button>
                    <button type="button" wire:click="deletePayroll" class="flex-1 py-2.5 rounded-full bg-rose-600/90 hover:bg-rose-700 text-white font-black text-xs shadow-md shadow-rose-950/20 transition active:scale-95 hover:scale-105 cursor-pointer">Ya, Batalkan</button>
                </div>
            </div>
        </div>
    @endif
</div>
