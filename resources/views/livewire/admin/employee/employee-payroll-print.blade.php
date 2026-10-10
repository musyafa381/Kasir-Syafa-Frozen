<div class="space-y-4">

    <!-- Top Action Bar (Hidden when Printing) -->
    <div class="print:hidden flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-white/40">
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.employees.show', $employee->id) }}" 
                wire:navigate 
                class="btn-glass-action inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full font-bold text-xs"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Detail Karyawan</span>
            </a>
            <span class="font-mono text-xs font-black text-[#163144] glass-pill-plek px-3 py-1 rounded-full">
                No. Slip: {{ $payroll->payroll_number }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.employees.payroll.edit', ['employeeId' => $employee->id, 'payrollId' => $payroll->id]) }}" 
                wire:navigate 
                class="btn-glass-action inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Slip Gaji</span>
            </a>

            <button 
                type="button" 
                onclick="window.print()" 
                class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md cursor-pointer"
            >
                <svg class="w-3.5 h-3.5 text-[#a0bddb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Area Container -->
    <div class="flex justify-center">
        <div id="printable-slip" class="w-full max-w-3xl bg-white rounded-[28px] border border-white/80 shadow-md p-6 sm:p-10 space-y-6 text-[#163144] print:border-none print:shadow-none print:p-0 print:m-0 print:max-w-full">
            
            <!-- Kop Slip Gaji -->
            <div class="flex items-start justify-between border-b-2 border-[#163144] pb-5">
                <div class="flex items-center gap-3.5">
                    <img src="{{ asset('kasirku.png') }}" alt="Logo" class="w-14 h-14 rounded-2xl object-contain border border-slate-100 p-0.5">
                    <div>
                        <h2 class="text-xl font-black text-[#163144] tracking-tight leading-tight">SYAFA FROZEN FOOD</h2>
                        <p class="text-xs text-slate-500 font-semibold">Pusat Makanan Beku Berkualitas & Higienis</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Dokumen Resmi Penggajian Karyawan</p>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="text-base font-black text-[#163144] uppercase tracking-wider">SLIP GAJI KARYAWAN</h3>
                    <p class="text-sm font-black text-[#386797] font-mono mt-0.5">{{ $payroll->period_label }}</p>
                    <p class="text-xs text-slate-500 font-mono mt-0.5 font-bold">{{ $payroll->payroll_number }}</p>
                </div>
            </div>

            <!-- Metadata Karyawan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200/80">
                <div class="space-y-1.5">
                    <p class="flex items-center"><span class="text-slate-400 w-28 font-semibold">Nama Karyawan:</span> <strong class="text-[#163144] font-black text-sm">{{ $payroll->employee->name }}</strong></p>
                    <p class="flex items-center"><span class="text-slate-400 w-28 font-semibold">Kode / ID Staf:</span> <span class="font-mono text-[#163144] font-black">{{ $payroll->employee->employee_code }}</span></p>
                    <p class="flex items-center"><span class="text-slate-400 w-28 font-semibold">Jabatan / Posisi:</span> <span class="text-[#163144] font-bold">{{ $payroll->employee->position }}</span></p>
                </div>
                <div class="space-y-1.5 sm:border-l sm:border-slate-200 sm:pl-4">
                    <p class="flex items-center"><span class="text-slate-400 w-28 font-semibold">Tanggal Bayar:</span> <span class="font-mono text-[#163144] font-bold">{{ $payroll->payment_date->format('d F Y') }}</span></p>
                    <p class="flex items-center"><span class="text-slate-400 w-28 font-semibold">Metode Bayar:</span> <span class="uppercase font-black text-[#163144]">{{ $payroll->payment_method }}</span></p>
                    @if ($payroll->employee->bank_account_number)
                        <p class="flex items-center"><span class="text-slate-400 w-28 font-semibold">Rekening:</span> <span class="font-mono text-[#163144] font-bold">{{ $payroll->employee->bank_name }} {{ $payroll->employee->bank_account_number }}</span></p>
                    @endif
                </div>
            </div>

            <!-- 2 Columns: PENERIMAAN vs POTONGAN -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <!-- Kolom Penerimaan -->
                <div class="space-y-2 border border-slate-200 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-black text-[#163144] uppercase tracking-wider">A. PENERIMAAN</span>
                        <span class="text-[10px] text-emerald-800 font-extrabold">(+) JUMLAH</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-slate-600 font-semibold">Gaji Pokok</span>
                        <span class="font-mono font-black text-[#163144]">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                    </div>
                    @if ($payroll->allowance_amount > 0)
                        <div class="flex items-center justify-between py-1.5">
                            <div>
                                <span class="text-slate-600 font-semibold">Bonus / Lembur / Tunjangan</span>
                                @if ($payroll->allowance_notes)
                                    <span class="block text-[11px] text-slate-400 font-medium">{{ $payroll->allowance_notes }}</span>
                                @endif
                            </div>
                            <span class="font-mono font-black text-emerald-800">+Rp {{ number_format($payroll->allowance_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between border-t border-slate-200 pt-2.5 font-bold text-[#163144]">
                        <span>Total Penerimaan (A)</span>
                        <span class="font-mono text-[#386797] text-sm font-black">Rp {{ number_format($payroll->base_salary + $payroll->allowance_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Kolom Potongan -->
                <div class="space-y-2 border border-slate-200 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <span class="font-black text-[#163144] uppercase tracking-wider">B. POTONGAN</span>
                        <span class="text-[10px] text-rose-600 font-extrabold">(-) JUMLAH</span>
                    </div>
                    @if ($payroll->loan_deduction > 0)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-slate-600 font-semibold">Angsuran / Potongan Kasbon</span>
                            <span class="font-mono font-black text-rose-600">−Rp {{ number_format($payroll->loan_deduction, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($payroll->other_deduction > 0)
                        <div class="flex items-center justify-between py-1.5">
                            <div>
                                <span class="text-slate-600 font-semibold">Potongan Lainnya</span>
                                @if ($payroll->deduction_notes)
                                    <span class="block text-[11px] text-slate-400 font-medium">{{ $payroll->deduction_notes }}</span>
                                @endif
                            </div>
                            <span class="font-mono font-black text-rose-600">−Rp {{ number_format($payroll->other_deduction, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($payroll->loan_deduction == 0 && $payroll->other_deduction == 0)
                        <div class="py-4 text-slate-400 text-center text-[11px] italic font-medium">Tidak ada potongan gaji</div>
                    @endif
                    <div class="flex items-center justify-between border-t border-slate-200 pt-2.5 font-bold text-[#163144]">
                        <span>Total Potongan (B)</span>
                        <span class="font-mono text-rose-600 text-sm font-black">Rp {{ number_format($payroll->loan_deduction + $payroll->other_deduction, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Grand Total Take Home Pay -->
            <div class="p-5 rounded-2xl bg-gradient-to-r from-[#163144] via-[#1f425b] to-[#163144] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md">
                <div>
                    <span class="text-[10px] font-bold text-[#a0bddb] uppercase tracking-wider block">GAJI BERSIH DITERIMA (TAKE HOME PAY)</span>
                    <span class="text-2xl font-black font-mono">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</span>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[10px] text-[#a0bddb] block">Terbilang:</span>
                    <span class="text-xs font-semibold italic text-slate-100 capitalize">
                        # {{ \App\Services\ThermalReceiptPrinter::terbilang($payroll->net_salary) }} Rupiah #
                    </span>
                </div>
            </div>

            <!-- Tanda Tangan / Signature Row -->
            <div class="grid grid-cols-2 gap-8 pt-8 text-center text-xs">
                <div>
                    <p class="text-slate-400 font-semibold">Penerima,</p>
                    <div class="h-16"></div>
                    <p class="font-black text-[#163144] border-t border-slate-300 pt-1.5">{{ $payroll->employee->name }}</p>
                    <p class="text-[10px] text-slate-400 font-mono">{{ $payroll->employee->employee_code }}</p>
                </div>
                <div>
                    <p class="text-slate-400 font-semibold">Bag. Keuangan / Kasir,</p>
                    <div class="h-16"></div>
                    <p class="font-black text-[#163144] border-t border-slate-300 pt-1.5">{{ auth()->user()->name ?? 'Admin Toko' }}</p>
                    <p class="text-[10px] text-slate-400">Syafa Frozen Food</p>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="text-center pt-3 border-t border-slate-100 text-[10px] text-slate-400 font-medium">
                Slip ini sah dan diterbitkan otomatis oleh Sistem POS Kasir Syafa Frozen Food pada {{ now()->format('d/m/Y H:i') }}.
            </div>

        </div>
    </div>
</div>
