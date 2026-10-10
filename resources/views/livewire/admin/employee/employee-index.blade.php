<div class="space-y-4">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Karyawan & Staf Toko</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Kelola data karyawan, riwayat penggajian terintegrasi OPEX, dan kasbon staf.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.print.payroll.summary') }}" 
                target="_blank" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-3.5 h-3.5 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Rekap Gaji (PDF)</span>
            </a>

            <a 
                href="{{ route('admin.print.payroll.excel') }}" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-3.5 h-3.5 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Ekspor Excel</span>
            </a>

            <a 
                href="{{ route('admin.employees.create') }}" 
                wire:navigate 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Tambah Karyawan</span>
            </a>
        </div>
    </div>

    <!-- 3 KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
        <!-- Card 1: Total Karyawan Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Personalia</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Karyawan Aktif</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Aktif
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ $totalActiveEmployees }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Orang</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Tim operasional toko</p>
            </div>
        </div>

        <!-- Card 2: Penggajian Bulan Ini (OPEX) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Beban Gaji</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Gaji Bulan Ini</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Beban OPEX
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($totalMonthlyPayroll, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-emerald-700 font-bold mt-0.5 truncate">Terintegrasi laporan laba rugi</p>
            </div>
        </div>

        <!-- Card 3: Total Kasbon Berjalan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-amber-600 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kasbon</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Kasbon Belum Lunas</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-amber-900 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Pinjaman
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black font-mono tracking-tight block {{ $totalOutstandingLoans > 0 ? 'text-amber-800' : 'text-[#163144]' }}">
                    Rp {{ number_format($totalOutstandingLoans, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Dipotong saat payroll</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <!-- Search -->
            <div class="relative w-full sm:max-w-md">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari nama, kode, nomor WhatsApp, jabatan..." 
                    class="w-full pl-3.5 pr-10 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
                @endif
            </div>

            <!-- Position & Status Filter -->
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                <select 
                    wire:model.live="positionFilter" 
                    class="w-1/2 sm:w-auto px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                >
                    <option value="">Semua Posisi</option>
                    @foreach ($positions as $pos)
                        <option value="{{ $pos }}">{{ $pos }}</option>
                    @endforeach
                </select>

                <select 
                    wire:model.live="statusFilter" 
                    class="w-1/2 sm:w-auto px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                >
                    <option value="all">Semua Status</option>
                    <option value="active">Karyawan Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table of Employees -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Nama Karyawan</th>
                        <th class="p-3.5 whitespace-nowrap">Kode / ID</th>
                        <th class="p-3.5 whitespace-nowrap">Posisi & Status</th>
                        <th class="p-3.5 whitespace-nowrap">No. WhatsApp / HP</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Gaji Pokok</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Sisa Kasbon</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Status</th>
                        <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-white/40 transition">
                            <!-- Nama Karyawan & User link -->
                            <td class="p-3.5 pl-5">
                                <a href="{{ route('admin.employees.show', $emp->id) }}" wire:navigate class="font-extrabold text-[#163144] text-sm hover:underline block">
                                    {{ $emp->name }}
                                </a>
                                @if ($emp->user)
                                    <span class="text-[10px] text-[#386797] font-bold glass-pill-plek px-2 py-0.5 rounded-full inline-block mt-0.5">
                                        Akun POS: {{ $emp->user->name }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kode -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-mono font-black text-xs glass-pill-plek text-[#163144] px-2.5 py-0.5 rounded-full">
                                    {{ $emp->employee_code }}
                                </span>
                            </td>

                            <!-- Posisi & Status Kerja -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-bold text-[#163144] block">{{ $emp->position }}</span>
                                <span class="text-[11px] text-slate-500 capitalize font-medium">{{ $emp->employment_status }}</span>
                            </td>

                            <!-- Telepon -->
                            <td class="p-3.5 font-mono text-slate-600 whitespace-nowrap font-semibold">
                                {{ $emp->phone ?: '-' }}
                            </td>

                            <!-- Gaji Pokok -->
                            <td class="p-3.5 text-right whitespace-nowrap font-mono">
                                <span class="font-black text-[#163144] block text-xs">
                                    Rp {{ number_format($emp->base_salary, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Sisa Kasbon -->
                            <td class="p-3.5 text-right whitespace-nowrap font-mono">
                                @if ($emp->total_remaining_loan > 0)
                                    <span class="font-black text-amber-900 glass-pill-plek px-2 py-0.5 rounded-full text-xs">
                                        Rp {{ number_format($emp->total_remaining_loan, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs font-semibold">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase glass-pill-plek {{ $emp->is_active ? 'text-emerald-800' : 'text-slate-400' }}">
                                    {{ $emp->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <!-- Aksi Side by Side -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a 
                                        href="{{ route('admin.employees.show', $emp->id) }}" 
                                        wire:navigate 
                                        class="btn-glass-action"
                                    >
                                        Detail & Gaji
                                    </a>
                                    <a 
                                        href="{{ route('admin.employees.edit', $emp->id) }}" 
                                        wire:navigate 
                                        class="btn-glass-action"
                                        title="Edit Data"
                                    >
                                        Edit
                                    </a>
                                    <button 
                                        wire:click="confirmDelete({{ $emp->id }})" 
                                        class="btn-glass-action"
                                        title="Hapus"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400 font-semibold">
                                Belum ada data karyawan sesuai pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse ($employees as $emp)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="font-mono font-black text-[10px] px-2 py-0.5 rounded-full glass-pill-plek text-[#163144]">
                                    {{ $emp->employee_code }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase glass-pill-plek text-[#163144]">
                                    {{ $emp->is_active ? '✓ Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <a href="{{ route('admin.employees.show', $emp->id) }}" wire:navigate class="font-black text-[#163144] text-sm hover:underline block">
                                {{ $emp->name }}
                            </a>
                            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">
                                <span class="font-bold text-[#163144]">{{ $emp->position }}</span> • <span class="capitalize">{{ $emp->employment_status }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Gaji Pokok</span>
                            <span class="font-mono font-black text-[#163144] text-sm block">
                                Rp {{ number_format($emp->base_salary, 0, ',', '.') }}
                            </span>
                            @if ($emp->total_remaining_loan > 0)
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-full glass-pill-plek text-amber-900 inline-block mt-0.5">
                                    Kasbon: Rp {{ number_format($emp->total_remaining_loan, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-white/40">
                        <span class="text-[11px] text-slate-500 font-mono font-semibold">
                            {{ $emp->phone ?: 'No WA: -' }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <a 
                                href="{{ route('admin.employees.show', $emp->id) }}" 
                                wire:navigate 
                                class="btn-glass-action"
                            >
                                Detail & Gaji
                            </a>
                            <a 
                                href="{{ route('admin.employees.edit', $emp->id) }}" 
                                wire:navigate 
                                class="btn-glass-action"
                            >
                                Edit
                            </a>
                            <button 
                                wire:click="confirmDelete({{ $emp->id }})" 
                                class="btn-glass-action"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-semibold">
                    Belum ada data karyawan.
                </div>
            @endforelse
        </div>

        {{ $employees->links() }}
    </div>

    <!-- Modal Delete Confirmation -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm w-full p-6 shadow-2xl border border-white/90 text-center space-y-4 relative my-auto modal-content-animate">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-[#163144]">Hapus Data Karyawan?</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed font-semibold">
                        Apakah Anda yakin ingin menghapus data karyawan <strong class="text-[#163144] font-black">"{{ $deletingEmployeeName }}"</strong>? Riwayat payroll terkait juga akan terpengaruh.
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteModal', false)" 
                        class="flex-1 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="deleteEmployee" 
                        class="flex-1 py-2.5 rounded-full bg-rose-600/90 hover:bg-rose-700 text-white font-black text-xs shadow-md shadow-rose-950/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
