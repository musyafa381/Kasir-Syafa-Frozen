<div class="space-y-3">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Shift & Kas Kasir</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                Rekap sesi shift kasir, saldo kas awal, penjualan tunai/non-tunai, dan selisih kas fisik (Laporan Z).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('pos.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#163144] hover:bg-[#244262] text-white font-extrabold text-xs shadow-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>Buka Terminal POS</span>
            </a>
        </div>
    </div>

    <!-- 4 EXECUTIVE KPI SUMMARY STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- 1. Total Sesi Shift -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Total Sesi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Shift Kasir</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Sesi Kasir
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($totalShifts) }} <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase">Sesi</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Sedang Aktif:</span>
                    <span class="font-bold text-[#163144]">{{ $shifts->where('status', 'open')->count() }} Shift</span>
                </p>
            </div>
        </div>

        <!-- 2. Total Penjualan Tunai -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Total Kas</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Kas Tunai</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Cash Masuk
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalCashSales, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Tunai Laci:</span>
                    <span class="font-bold text-[#163144]">Cash Fisik</span>
                </p>
            </div>
        </div>

        <!-- 3. Total QRIS & Transfer -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Non-Tunai</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">QRIS & Transfer</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Digital Pay
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalNonCashSales, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Tipe Bayar:</span>
                    <span class="font-bold text-[#163144]">Bank & E-Wallet</span>
                </p>
            </div>
        </div>

        <!-- 4. Selisih Fisik Laci -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Audit Kasir</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Selisih Fisik Laci</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek font-extrabold text-[9px] sm:text-[10px] shrink-0 text-[#163144]">
                    {{ $totalOverShort == 0 ? 'Pas / Sesuai' : ($totalOverShort < 0 ? 'Kas Kurang' : 'Kas Lebih') }}
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $totalOverShort > 0 ? '+' : '' }}Rp {{ number_format($totalOverShort, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Kesesuaian:</span>
                    <span class="font-bold text-[#163144]">
                        {{ $totalOverShort == 0 ? '100% Sesuai' : ($totalOverShort < 0 ? 'Ada Selisih Kurang' : 'Ada Selisih Lebih') }}
                    </span>
                </p>
            </div>
        </div>

    </div>

    <!-- Filters Section -->
    <div class="glass-card-plek rounded-[24px] p-4 space-y-3">
        
        <!-- Status Tabs (Swipeable on Mobile) -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0 sm:flex-wrap">
            <button 
                type="button" 
                wire:click="$set('statusFilter', 'all')"
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusFilter === 'all' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Semua Shift
            </button>
            <button 
                type="button" 
                wire:click="$set('statusFilter', 'open')"
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusFilter === 'open' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Sedang Aktif
            </button>
            <button 
                type="button" 
                wire:click="$set('statusFilter', 'closed')"
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusFilter === 'closed' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Selesai
            </button>
        </div>

        <!-- Filter Dropdowns & Date Range -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 pt-2 border-t border-white/40">
            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari kasir atau cabang..."
                    class="w-full px-4 py-2 rounded-2xl glass-pill-plek text-xs font-semibold text-[#163144] placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                >
            </div>

            <!-- Outlet Filter -->
            <div>
                <select 
                    wire:model.live="outletFilter" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer transition"
                >
                    <option value="">-- Semua Cabang Toko --</option>
                    @foreach($outlets as $o)
                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <input 
                    type="date" 
                    wire:model.live="startDate" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition cursor-pointer"
                >
            </div>

            <!-- End Date -->
            <div>
                <input 
                    type="date" 
                    wire:model.live="endDate" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition cursor-pointer"
                >
            </div>
        </div>
    </div>

    <!-- Shifts Table (Desktop & Mobile Card List) -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        
        <!-- Desktop Table (>= sm) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Kasir & Cabang</th>
                        <th class="p-3.5 whitespace-nowrap">Waktu Sesi Shift</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Kas Awal</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Penjualan Tunai</th>
                        <th class="p-3.5 text-right whitespace-nowrap">QRIS / Transfer</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Kas Fisik Laci</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Selisih</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/30 font-semibold">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-white/40 transition">
                            <!-- Kasir & Cabang -->
                            <td class="p-3.5 pl-5">
                                <span class="font-black text-[#163144] text-sm block">
                                    {{ $shift->user?->name ?? 'Kasir' }}
                                </span>
                                <span class="text-[10px] text-slate-400 block truncate">
                                    Cabang: {{ $shift->outlet?->name ?? 'Semua Cabang' }}
                                </span>
                            </td>

                            <!-- Waktu Shift -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="text-[#163144] text-xs block font-black">
                                    Buka: {{ $shift->opened_at->translatedFormat('d/m/Y H:i') }}
                                </span>
                                <span class="text-[10px] text-slate-400 block font-semibold">
                                    Tutup: {{ $shift->closed_at ? $shift->closed_at->translatedFormat('d/m/Y H:i') : 'Masih Berlangsung' }}
                                </span>
                            </td>

                            <!-- Kas Awal -->
                            <td class="p-3.5 text-right font-black text-[#163144] whitespace-nowrap">
                                Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
                            </td>

                            <!-- Penjualan Tunai -->
                            <td class="p-3.5 text-right font-black text-[#163144] whitespace-nowrap">
                                Rp {{ number_format($shift->total_cash_sales, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-400 block font-normal">{{ $shift->total_transactions_count }} struk</span>
                            </td>

                            <!-- QRIS / Transfer -->
                            <td class="p-3.5 text-right font-black text-[#163144] whitespace-nowrap">
                                Rp {{ number_format($shift->total_qris_sales + $shift->total_transfer_sales, 0, ',', '.') }}
                            </td>

                            <!-- Kas Fisik Laci -->
                            <td class="p-3.5 text-right font-black text-[#163144] whitespace-nowrap">
                                @if($shift->status === 'closed')
                                    Rp {{ number_format($shift->actual_ending_cash ?? 0, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 italic font-semibold text-xs">-</span>
                                @endif
                            </td>

                            <!-- Selisih -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($shift->status === 'closed' && $shift->cash_difference !== null)
                                    <span class="font-black text-xs px-2.5 py-1 rounded-full glass-pill-plek inline-block text-[#163144]">
                                        {{ $shift->cash_difference > 0 ? '+' : '' }}Rp {{ number_format($shift->cash_difference, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic font-semibold">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="font-extrabold text-[10px] uppercase px-2.5 py-1 rounded-full glass-pill-plek inline-block text-[#163144]">
                                    {{ $shift->status === 'open' ? 'Aktif' : 'Ditutup' }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <button 
                                    type="button"
                                    wire:click="openDetailModal({{ $shift->id }})" 
                                    class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-extrabold"
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
                            <td colspan="9" class="text-center py-10 text-slate-400 font-semibold">
                                Tidak ada data sesi shift kasir pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List (< sm) -->
        <div class="sm:hidden divide-y divide-white/30">
            @forelse($shifts as $shift)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-black text-[#163144] text-sm block">{{ $shift->user?->name ?? 'Kasir' }}</span>
                            <span class="text-[10px] text-slate-400 block font-semibold">{{ $shift->outlet?->name ?? 'Semua Cabang' }}</span>
                        </div>
                        <span class="font-black text-[9px] uppercase px-2.5 py-0.5 rounded-full glass-pill-plek shrink-0 text-[#163144]">
                            {{ $shift->status === 'open' ? '● Aktif' : '✓ Ditutup' }}
                        </span>
                    </div>

                    <div class="text-[11px] text-slate-600 space-y-1 glass-pill-plek p-3 rounded-2xl border border-white/60">
                        <div class="flex justify-between">
                            <span class="font-semibold text-slate-400">Buka:</span>
                            <span class="font-bold text-[#163144]">{{ $shift->opened_at->translatedFormat('d/m/y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-slate-400">Tutup:</span>
                            <span class="font-bold text-[#163144]">{{ $shift->closed_at ? $shift->closed_at->translatedFormat('d/m/y H:i') : 'Berlangsung' }}</span>
                        </div>
                        <div class="flex justify-between pt-1 border-t border-white/40">
                            <span class="font-semibold text-slate-400">Kas Awal:</span>
                            <span class="font-black text-[#163144]">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-slate-400">Penjualan Tunai:</span>
                            <span class="font-black text-[#163144]">Rp {{ number_format($shift->total_cash_sales, 0, ',', '.') }} ({{ $shift->total_transactions_count }} struk)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-slate-400">QRIS / Transfer:</span>
                            <span class="font-black text-[#163144]">Rp {{ number_format($shift->total_qris_sales + $shift->total_transfer_sales, 0, ',', '.') }}</span>
                        </div>
                        @if($shift->status === 'closed' && $shift->cash_difference !== null)
                            <div class="flex justify-between pt-1 border-t border-white/40 font-black text-[#163144]">
                                <span>Selisih Laci:</span>
                                <span>{{ $shift->cash_difference > 0 ? '+' : '' }}Rp {{ number_format($shift->cash_difference, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end pt-0.5">
                        <button 
                            type="button"
                            wire:click="openDetailModal({{ $shift->id }})" 
                            class="btn-glass-action inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-extrabold"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Lihat Rincian & Rekap</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 font-semibold px-4">
                    Tidak ada data sesi shift kasir.
                </div>
            @endforelse
        </div>

        {{ $shifts->links() }}
    </div>

    <!-- Modal Detail & Rekap Shift (Laporan Z) -->
    @if($showDetailModal && $selectedShift)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col relative my-auto modal-content-animate max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Rekap Sesi Shift (Laporan Z)</h3>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">Kasir: {{ $selectedShift->user?->name }} • {{ $selectedShift->opened_at->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="closeDetailModal" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                    >
                        ✕
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto bg-transparent">
                    
                    <!-- Shift Overview Box -->
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2.5">
                        <div class="flex justify-between items-center text-slate-600">
                            <span class="font-semibold">Status Sesi:</span>
                            <span class="font-black uppercase px-2.5 py-0.5 rounded-full text-[10px] glass-pill-plek text-[#163144]">
                                {{ $selectedShift->status === 'open' ? 'Sedang Aktif' : 'Sudah Ditutup' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span class="font-semibold">Waktu Mulai:</span>
                            <span class="font-black text-[#163144]">{{ $selectedShift->opened_at->translatedFormat('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span class="font-semibold">Waktu Selesai:</span>
                            <span class="font-black text-[#163144]">{{ $selectedShift->closed_at ? $selectedShift->closed_at->translatedFormat('d/m/Y H:i:s') : '-' }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span class="font-semibold">Cabang Penugasan:</span>
                            <span class="font-black text-[#163144]">{{ $selectedShift->outlet?->name ?? 'Pusat' }}</span>
                        </div>
                    </div>

                    <!-- Breakdown Penjualan -->
                    <div class="space-y-2">
                        <h4 class="font-black uppercase tracking-wider text-[11px] text-[#163144]">Rincian Kas & Pembayaran</h4>
                        
                        <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden shadow-2xs bg-white">
                            <div class="flex justify-between p-3 bg-white font-semibold">
                                <span class="text-slate-600">Modal Kas Awal:</span>
                                <span class="font-black text-[#163144] font-mono">Rp {{ number_format($selectedShift->starting_cash, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between p-3 bg-slate-50/50 font-semibold">
                                <span class="text-slate-700">Total Penjualan Tunai (+):</span>
                                <span class="font-black text-[#163144] font-mono">Rp {{ number_format($selectedShift->total_cash_sales, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between p-3 bg-white font-semibold">
                                <span class="text-slate-700">Total QRIS:</span>
                                <span class="font-black text-[#163144] font-mono">Rp {{ number_format($selectedShift->total_qris_sales, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between p-3 bg-slate-50/50 font-semibold">
                                <span class="text-slate-700">Total Transfer Bank:</span>
                                <span class="font-black text-[#163144] font-mono">Rp {{ number_format($selectedShift->total_transfer_sales, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between p-3 bg-white font-semibold">
                                <span class="text-slate-700">Total Tempo (Piutang):</span>
                                <span class="font-black text-[#163144] font-mono">Rp {{ number_format($selectedShift->total_tempo_sales, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between p-3 bg-slate-100/70 font-extrabold">
                                <span class="text-slate-800">Ekspektasi Kas Laci (Sistem):</span>
                                <span class="font-black text-[#163144] font-mono">Rp {{ number_format($selectedShift->expected_ending_cash, 0, ',', '.') }}</span>
                            </div>
                            @if($selectedShift->status === 'closed')
                                <div class="flex justify-between p-3 bg-white font-extrabold">
                                    <span class="text-slate-800">Uang Fisik Dihitung Kasir:</span>
                                    <span class="font-black text-[#163144] font-mono">Rp {{ number_format($selectedShift->actual_ending_cash ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between p-3 bg-slate-50 font-black text-[#163144]">
                                    <span>Selisih Kas Laci:</span>
                                    <span class="font-mono">{{ $selectedShift->cash_difference > 0 ? '+' : '' }}Rp {{ number_format($selectedShift->cash_difference ?? 0, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($selectedShift->opening_notes || $selectedShift->closing_notes)
                        <div class="p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100 text-xs space-y-1.5">
                            @if($selectedShift->opening_notes)
                                <p><strong class="text-[#163144]">Catatan Buka Shift:</strong> <span class="text-slate-600 font-semibold">{{ $selectedShift->opening_notes }}</span></p>
                            @endif
                            @if($selectedShift->closing_notes)
                                <p><strong class="text-[#163144]">Catatan Tutup Shift:</strong> <span class="text-slate-600 font-semibold">{{ $selectedShift->closing_notes }}</span></p>
                            @endif
                        </div>
                    @endif

                </div>

                <!-- Modal Actions -->
                <div class="p-4 px-6 border-t border-slate-100 bg-slate-50/60 flex items-center justify-end gap-2 shrink-0">
                    <button 
                        type="button" 
                        wire:click="closeDetailModal" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
