<div class="space-y-3">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Buku Hutang Supplier (Accounts Payable)</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                Manajemen kewajiban hutang dagang kepada supplier, jadwal jatuh tempo tempo, dan pencatatan pelunasan.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.print.accounts-payable.report', ['status' => $statusTab]) }}" 
                target="_blank" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak PDF</span>
            </a>

            <a 
                href="{{ route('admin.print.accounts-payable.excel', ['status' => $statusTab]) }}" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Ekspor Excel</span>
            </a>

            <a 
                href="{{ route('admin.purchases.create') }}" 
                wire:navigate 
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#163144] hover:bg-[#244262] text-white font-extrabold text-xs shadow-xs transition cursor-pointer"
            >
                <span>Buat Faktur Pembelian</span>
            </a>
        </div>
    </div>

    <!-- 4 EXECUTIVE KPI SUMMARY STATS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- 1. Total Sisa Hutang Dagang -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Sisa Hutang</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Kewajiban Dagang</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Aktif
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    Rp {{ number_format($totalRemainingDebt, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Supplier:</span>
                    <span class="font-bold text-[#163144]">{{ $totalSuppliersWithDebtCount }} Pemasok</span>
                </p>
            </div>
        </div>

        <!-- 2. Hutang Jatuh Tempo (Overdue) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Lewat Tempo</span>
                        <span class="font-bold text-rose-600 text-[11px] sm:text-xs block truncate">Jatuh Tempo</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-rose-600 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Terlambat
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-rose-600 tracking-tight truncate font-mono">
                    Rp {{ number_format($totalOverdueDebt, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Prioritas:</span>
                    <span class="font-bold text-rose-600">Perlu Dilunasi</span>
                </p>
            </div>
        </div>

        <!-- 3. Segera Jatuh Tempo (< 7 Hari) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Tempo &le; 7 Hari</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Segera Tempo</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    1 Pekan
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    Rp {{ number_format($totalDueSoonDebt, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Tenggat Waktu:</span>
                    <span class="font-bold text-[#163144]">Dalam 7 Hari</span>
                </p>
            </div>
        </div>

        <!-- 4. Pemasok Terlibat -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Supplier Mitra</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Mitra Kreditur</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Pemasok
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    {{ $totalSuppliersWithDebtCount }} <span class="text-xs font-bold text-slate-400 uppercase">Pemasok</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Status Hutang:</span>
                    <span class="font-bold text-[#163144]">Tagihan Aktif</span>
                </p>
            </div>
        </div>

    </div>

    <!-- Filter Toolbar -->
    <div class="glass-card-plek rounded-[24px] p-4 space-y-3">
        
        <!-- Status Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0 sm:flex-wrap">
                <button 
                    type="button" 
                    wire:click="$set('statusTab', 'unpaid')" 
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'unpaid' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
                >
                    Belum Lunas
                </button>
                <button 
                    type="button" 
                    wire:click="$set('statusTab', 'overdue')" 
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'overdue' ? 'bg-rose-600 text-white shadow-xs' : 'btn-glass-action' }}"
                >
                    Lewat Jatuh Tempo
                </button>
                <button 
                    type="button" 
                    wire:click="$set('statusTab', 'due_soon')" 
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'due_soon' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
                >
                    Tempo &le; 7 Hari
                </button>
                <button 
                    type="button" 
                    wire:click="$set('statusTab', 'paid')" 
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'paid' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
                >
                    Riwayat Lunas
                </button>
                <button 
                    type="button" 
                    wire:click="$set('statusTab', 'all')" 
                    class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'all' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
                >
                    Semua Faktur
                </button>
            </div>

            <!-- Search -->
            <div class="relative w-full sm:w-72">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari PO, no faktur, nama supplier..." 
                    class="w-full pl-3.5 pr-8 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold cursor-pointer">✕</button>
                @endif
            </div>
        </div>

    </div>

    <!-- Accounts Payable Table -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[850px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/40">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Faktur PO & Tanggal</th>
                        <th class="p-3.5 whitespace-nowrap">Supplier Mitra</th>
                        <th class="p-3.5 whitespace-nowrap">Jatuh Tempo</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Total Belanja</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Sudah Dibayar</th>
                        <th class="p-3.5 text-right whitespace-nowrap font-black">Sisa Hutang</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/30 font-semibold">
                    @forelse($purchases as $po)
                        <tr class="hover:bg-white/40 transition">
                            
                            <!-- PO & Tanggal -->
                            <td class="p-3.5 pl-5">
                                <span class="font-black text-[#163144] font-mono block">{{ $po->invoice_number }}</span>
                                <div class="text-[10px] text-slate-400 font-semibold">
                                    <span>Tgl: {{ $po->purchase_date->format('d/m/Y') }}</span>
                                    @if($po->supplier_invoice_number)
                                        <span class="font-mono text-[#163144] ml-1">({{ $po->supplier_invoice_number }})</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Supplier -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-bold text-[#163144] block">{{ $po->supplier->name ?? 'Supplier Dihapus' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $po->supplier->phone ?: 'Tanpa no. HP' }}</span>
                            </td>

                            <!-- Jatuh Tempo & Days Remaining -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if($po->payment_type === 'tempo' && $po->due_date)
                                    @php
                                        $diffDays = (int) Carbon::today()->diffInDays($po->due_date, false);
                                    @endphp
                                    <span class="font-black font-mono {{ $po->is_overdue ? 'text-rose-600' : 'text-[#163144]' }}">
                                        {{ $po->due_date->format('d/m/Y') }}
                                    </span>
                                    <div class="text-[10px] mt-0.5 font-bold">
                                        @if($po->payment_status === 'paid')
                                            <span class="text-emerald-700">Lunas</span>
                                        @elseif($diffDays < 0)
                                            <span class="text-rose-600">Terlambat {{ abs($diffDays) }} hari</span>
                                        @elseif($diffDays === 0)
                                            <span class="text-rose-600">Jatuh tempo hari ini</span>
                                        @else
                                            <span class="text-slate-500">{{ $diffDays }} hari lagi</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 font-mono uppercase text-[10px]">{{ $po->payment_type }}</span>
                                @endif
                            </td>

                            <!-- Total Belanja -->
                            <td class="p-3.5 text-right font-mono font-black text-[#163144] whitespace-nowrap">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- Sudah Dibayar -->
                            <td class="p-3.5 text-right font-mono text-emerald-700 font-bold whitespace-nowrap">
                                Rp {{ number_format($po->paid_amount, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Hutang -->
                            <td class="p-3.5 text-right font-mono font-black whitespace-nowrap {{ $po->remaining_amount > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                                Rp {{ number_format($po->remaining_amount, 0, ',', '.') }}
                            </td>

                            <!-- Status Bayar -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($po->payment_status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-emerald-700 font-extrabold text-[10px]">Lunas</span>
                                @elseif($po->is_overdue)
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-rose-600 font-extrabold text-[10px]">Terlambat</span>
                                @elseif($po->payment_status === 'partial')
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-700 font-extrabold text-[10px]">Sebagian</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-slate-600 font-extrabold text-[10px]">Belum Bayar</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                @if($po->remaining_amount > 0)
                                    <button 
                                        type="button" 
                                        wire:click="openPaymentModal({{ $po->id }})" 
                                        class="btn-glass-action"
                                    >
                                        <span>Bayar / Cicil</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs font-bold">Tuntas</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400 font-semibold">
                                Tidak ada data hutang supplier yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="sm:hidden divide-y divide-white/30">
            @forelse($purchases as $po)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="font-mono font-bold text-[#163144] text-[10px] glass-pill-plek px-2 py-0.5 rounded-full inline-block mb-1">
                                {{ $po->invoice_number }}
                            </span>
                            <h4 class="font-black text-[#163144] text-sm leading-snug">{{ $po->supplier->name ?? 'Supplier Dihapus' }}</h4>
                            <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500 font-semibold mt-0.5">
                                <span>Tgl: {{ $po->purchase_date->format('d/m/Y') }}</span>
                                @if($po->due_date)
                                    <span>•</span>
                                    <span class="{{ $po->is_overdue ? 'text-rose-600 font-bold' : 'text-[#163144]' }}">Tempo: {{ $po->due_date->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @if($po->payment_status === 'paid')
                                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-700 font-extrabold text-[10px]">Lunas</span>
                            @elseif($po->is_overdue)
                                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-rose-600 font-extrabold text-[10px]">Terlambat</span>
                            @elseif($po->payment_status === 'partial')
                                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-amber-700 font-extrabold text-[10px]">Sebagian</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-slate-600 font-extrabold text-[10px]">Belum Bayar</span>
                            @endif
                            <span class="font-mono font-black text-amber-700 text-sm block mt-1">
                                Rp {{ number_format($po->remaining_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px] glass-pill-plek p-2.5 rounded-2xl font-mono text-center border border-white/60">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase font-sans font-bold">Total Belanja</span>
                            <span class="text-[#163144] font-black">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase font-sans font-bold">Sudah Dibayar</span>
                            <span class="text-emerald-700 font-bold">Rp {{ number_format($po->paid_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($po->remaining_amount > 0)
                        <div class="flex items-center justify-end pt-1">
                            <button 
                                type="button" 
                                wire:click="openPaymentModal({{ $po->id }})" 
                                class="btn-glass-action w-full justify-center py-2"
                            >
                                <span>Bayar / Cicil Hutang</span>
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-semibold">
                    <p class="text-xs font-bold text-[#163144]">Tidak ada data hutang supplier</p>
                </div>
            @endforelse
        </div>

        @if($purchases->hasPages())
            <div class="p-3 border-t border-white/40">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL CATAT PEMBAYARAN HUTANG SUPPLIER -->
    @if($showPaymentModal && $selectedPurchase)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-[#163144] text-base leading-tight">Pembayaran Hutang</h3>
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black">{{ $selectedPurchase->invoice_number }}</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Supplier: <strong class="text-[#163144]">{{ $selectedPurchase->supplier->name ?? '-' }}</strong></p>
                        </div>
                    </div>
                    <button type="button" wire:click="closePaymentModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit.prevent="recordPayment" class="p-6 space-y-4">
                    
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-2 text-xs font-semibold">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Supplier:</span>
                            <span class="font-bold text-[#163144]">{{ $selectedPurchase->supplier->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Faktur:</span>
                            <span class="font-mono font-bold text-[#163144]">Rp {{ number_format($selectedPurchase->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sudah Terbayar:</span>
                            <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($selectedPurchase->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-slate-200 text-amber-700 font-black">
                            <span>Sisa Hutang Saat Ini:</span>
                            <span class="font-mono text-sm">Rp {{ number_format($selectedPurchase->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Nominal Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-black text-slate-400">Rp</span>
                            <input 
                                wire:model="paymentAmount" 
                                type="number" 
                                min="1" 
                                max="{{ $selectedPurchase->remaining_amount }}" 
                                required
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-mono font-black text-[#163144] outline-none focus:border-[#163144]"
                            >
                        </div>
                        @error('paymentAmount') <span class="text-rose-500 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2.5 pt-2">
                        <button 
                            type="button" 
                            wire:click="closePaymentModal" 
                            class="flex-1 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="flex-1 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                        >
                            Simpan Pembayaran
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>
