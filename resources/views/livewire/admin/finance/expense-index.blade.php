<div class="space-y-4">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Buku Beban Operasional (Beban Toko)</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Pencatatan biaya listrik freezer, gaji karyawan, plastik packing, dan operasional toko.</p>
        </div>

        <div>
            <button 
                type="button" 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Catat Pengeluaran Baru</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Cards (OPEX Metrics) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        
        <!-- Total Beban Periode Ini -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Total Beban</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Beban Periode</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    OPEX
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($totalFilteredExpenses, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Total beban terfilter</p>
            </div>
        </div>

        <!-- Beban Listrik & Utilitas -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Listrik</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Listrik & PLN</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-amber-900 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    PLN
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-amber-800 font-mono tracking-tight block">
                    Rp {{ number_format($electricityExpenses, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Daya freezer aktif</p>
            </div>
        </div>

        <!-- Beban Gaji Karyawan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Gaji</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Gaji Staf Toko</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Payroll
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#386797] font-mono tracking-tight block">
                    Rp {{ number_format($salaryExpenses, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Staf kasir & toko</p>
            </div>
        </div>

        <!-- Beban Servis & Perawatan Freezer -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Perawatan</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Rawat Freezer</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Servis
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    Rp {{ number_format($maintenanceExpenses, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Pemeliharaan freezer</p>
            </div>
        </div>

    </div>

    <!-- Filter Section & Period Presets -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4 space-y-3">
        
        <!-- Period Preset Pills (Swipeable) -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 border-b border-white/40">
            @php
                $presets = [
                    'today' => 'Hari Ini',
                    'this_week' => 'Minggu Ini',
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
                    class="shrink-0 px-3.5 py-1.5 rounded-full font-extrabold text-xs transition cursor-pointer
                    {{ $period === $key ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Search, Category & Date Pickers -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            
            <!-- Search Keyword -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari uraian pengeluaran..." 
                    class="w-full pl-9 pr-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Category Filter Dropdown -->
            <div>
                <select 
                    wire:model.live="categoryFilter" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
                    <option value="">Semua Kategori Beban</option>
                    <option value="electricity_utility">Listrik PLN & Utilitas</option>
                    <option value="salary_labor">Gaji Karyawan & Kasir</option>
                    <option value="packaging_supplies">Plastik & Kemasan</option>
                    <option value="maintenance_repair">Servis & Perawatan Freezer</option>
                    <option value="rent_premises">Sewa Tempat Toko</option>
                    <option value="other_operational">Operasional Lainnya</option>
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <input 
                    type="date" 
                    wire:model.live="startDate" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
            </div>

            <!-- End Date -->
            <div>
                <input 
                    type="date" 
                    wire:model.live="endDate" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                >
            </div>

        </div>

    </div>

    <!-- Table & Mobile Card View of Expenses -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs min-w-[850px]">
                <thead>
                    <tr class="bg-white/40 border-b border-white/60 text-[#163144] font-black uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4 whitespace-nowrap">No. Bukti & Tanggal</th>
                        <th class="py-3 px-4 whitespace-nowrap">Kategori Beban</th>
                        <th class="py-3 px-4 whitespace-nowrap">Uraian Pengeluaran</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Nominal (Rp)</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Sumber Dana</th>
                        <th class="py-3 px-4 whitespace-nowrap">Dicatat Oleh</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-white/40 transition">
                            
                            <!-- Expense Number & Date -->
                            <td class="py-3 px-4">
                                <span class="font-mono font-black text-[#163144] block">{{ $exp->expense_number }}</span>
                                <span class="text-[10px] text-slate-400 font-mono mt-0.5 block font-semibold">
                                    {{ $exp->expense_date->format('d/m/Y') }}
                                </span>
                            </td>

                            <!-- Category Badge -->
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black glass-pill-plek text-[#163144]">
                                    {{ \App\Models\Expense::getCategoryLabel($exp->category) }}
                                </span>
                            </td>

                            <!-- Title & Notes -->
                            <td class="py-3 px-4">
                                <span class="font-extrabold text-[#163144] block">{{ $exp->title }}</span>
                                @if($exp->notes)
                                    <span class="text-[10px] text-slate-400 block mt-0.5 font-semibold">{{ $exp->notes }}</span>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="py-3 px-4 text-right font-mono font-black text-[#163144] text-sm">
                                Rp {{ number_format($exp->amount, 0, ',', '.') }}
                            </td>

                            <!-- Payment Method -->
                            <td class="py-3 px-4 text-center">
                                <span class="font-mono text-[10px] font-extrabold uppercase glass-pill-plek text-[#163144] px-2 py-0.5 rounded-full">
                                    {{ $exp->payment_method }}
                                </span>
                            </td>

                            <!-- User / Staff -->
                            <td class="py-3 px-4">
                                <span class="text-xs text-slate-600 font-semibold">{{ $exp->user->name ?? 'Admin' }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($exp->payroll)
                                        <!-- Terintegrasi Penggajian: 1 Pintu via Detail Karyawan -->
                                        <a 
                                            href="{{ route('admin.employees.show', $exp->payroll->employee_id) }}" 
                                            wire:navigate 
                                            class="btn-glass-action inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-xs"
                                            title="Kelola & Batalkan dari Menu Detail Karyawan (1 Pintu)"
                                        >
                                            <svg class="w-3.5 h-3.5 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <span>Kelola</span>
                                        </a>
                                    @else
                                        <button 
                                            type="button" 
                                            wire:click="openEditModal({{ $exp->id }})" 
                                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold"
                                            title="Edit Pengeluaran"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Edit</span>
                                        </button>
                                        <button 
                                            type="button" 
                                            wire:click="confirmDelete({{ $exp->id }})" 
                                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold"
                                            title="Hapus Pengeluaran"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 font-semibold">
                                Belum ada catatan pengeluaran operasional.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($expenses as $exp)
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-extrabold text-[#163144] text-sm block leading-snug">{{ $exp->title }}</span>
                            <span class="text-[10px] text-slate-400 font-mono font-semibold">{{ $exp->expense_date->format('d/m/Y') }} • {{ $exp->expense_number }}</span>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black glass-pill-plek text-[#163144]">
                            {{ \App\Models\Expense::getCategoryLabel($exp->category) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between glass-pill-plek p-2.5 rounded-2xl border border-white/60">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Nominal</span>
                            <span class="font-mono font-black text-[#163144] text-sm">Rp {{ number_format($exp->amount, 0, ',', '.') }}</span>
                        </div>
                        <span class="font-mono text-[10px] font-extrabold uppercase glass-pill-plek text-[#163144] px-2 py-0.5 rounded-full">
                            {{ $exp->payment_method }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[10px] text-slate-400 font-semibold">Oleh: {{ $exp->user->name ?? 'Admin' }}</span>
                        <div class="flex items-center gap-1.5">
                            @if ($exp->payroll)
                                <a 
                                    href="{{ route('admin.employees.show', $exp->payroll->employee_id) }}" 
                                    wire:navigate 
                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                >
                                    Kelola Gaji
                                </a>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="openEditModal({{ $exp->id }})" 
                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                >
                                    Edit
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="confirmDelete({{ $exp->id }})" 
                                    class="btn-glass-action px-3 py-1.5 rounded-full font-bold text-xs"
                                >
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                    Belum ada catatan pengeluaran operasional.
                </div>
            @endforelse
        </div>

        {{ $expenses->links() }}
    </div>

    <!-- ================= MODAL TAMBAH / EDIT PENGELUARAN ================= -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base leading-tight">
                                {{ $editingExpenseId ? 'Edit Beban Operasional' : 'Catat Pengeluaran Baru' }}
                            </h3>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">Pencatatan kas keluar untuk operasional toko</p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="closeFormModal" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit="saveExpense" class="p-6 space-y-4">
                    
                    <!-- Kategori Beban -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Kategori Pengeluaran</label>
                        <select 
                            wire:model="expenseCategory" 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                        >
                            <option value="electricity_utility">Listrik PLN & Utilitas Toko</option>
                            <option value="salary_labor">Gaji Karyawan & Staf Kasir</option>
                            <option value="packaging_supplies">Plastik Kresek, Ice Gel & Packing</option>
                            <option value="maintenance_repair">Servis & Perawatan Freezer</option>
                            <option value="rent_premises">Sewa Tempat / Kios Toko</option>
                            <option value="other_operational">Operasional Lainnya</option>
                        </select>
                        @error('expenseCategory') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Uraian / Nama Pengeluaran -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Uraian / Keterangan</label>
                        <input 
                            wire:model="expenseTitle" 
                            type="text" 
                            placeholder="Contoh: Tagihan Listrik PLN 3 Freezer Bulan Ini" 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] placeholder-slate-400 outline-none"
                            autofocus
                        >
                        @error('expenseTitle') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nominal with Rupiah Auto-format -->
                    <div 
                        x-data="{
                            displayAmount: '0',
                            init() {
                                this.updateDisplay($wire.expenseAmount);
                                $watch('$wire.expenseAmount', val => this.updateDisplay(val));
                            },
                            updateDisplay(val) {
                                let num = parseInt(val) || 0;
                                this.displayAmount = num > 0 ? num.toLocaleString('id-ID') : '0';
                            },
                            handleInput(e) {
                                let clean = e.target.value.replace(/[^0-9]/g, '');
                                let num = parseInt(clean) || 0;
                                this.displayAmount = num > 0 ? num.toLocaleString('id-ID') : '0';
                                $wire.set('expenseAmount', num);
                            }
                        }"
                    >
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Nominal Pengeluaran (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-slate-400 text-sm">Rp</span>
                            <input 
                                type="text" 
                                x-model="displayAmount"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                class="w-full pl-11 pr-3.5 py-2.5 rounded-xl glass-input-plek text-base font-black text-[#163144] outline-none font-mono"
                            >
                        </div>
                        @error('expenseAmount') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Tanggal Pengeluaran -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Tanggal Bayar</label>
                            <input 
                                wire:model="expenseDate" 
                                type="date" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none font-mono"
                            >
                            @error('expenseDate') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Metode Pembayaran -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Sumber Dana</label>
                            <select 
                                wire:model="expensePaymentMethod" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                            >
                                <option value="cash">Kas Tunai Toko</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS Toko</option>
                            </select>
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                        <input 
                            wire:model="expenseNotes" 
                            type="text" 
                            placeholder="Keterangan nomor referensi struk, dll..." 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                        >
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="closeFormModal" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                        >
                            {{ $editingExpenseId ? 'Simpan Perubahan' : 'Catat Pengeluaran' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- ================= MODAL KONFIRMASI HAPUS ================= -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4 my-auto confirm-card">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-[#e1144b] border border-rose-300/40 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Pengeluaran</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus catatan pengeluaran <strong class="text-[#163144]">"{{ $deletingExpenseTitle }}"</strong>?
                        </p>
                    </div>
                </div>

                <!-- Action Buttons (Divider + Right-Aligned Rounded Pills) -->
                <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-slate-300/40">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteModal', false)" 
                        class="px-5 py-2 rounded-full bg-white/50 hover:bg-white/75 border border-white/40 text-slate-700 font-black text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="deleteExpense" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
