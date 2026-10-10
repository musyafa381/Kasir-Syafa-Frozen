<div class="space-y-3">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Buku Piutang Pelanggan</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                Manajemen penagihan penjualan tempo (bon), cicilan pelanggan, dan jatuh tempo.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.print.receivables.book', ['status' => $statusTab, 'customer_id' => $customerId]) }}" 
                target="_blank" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Buku Piutang</span>
            </a>

            <a 
                href="{{ route('admin.print.receivables.excel', ['status' => $statusTab, 'customer_id' => $customerId]) }}" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Ekspor Excel</span>
            </a>
        </div>
    </div>

    <!-- 4 EXECUTIVE KPI CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- Total Piutang Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Tagihan</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Piutang Aktif</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Sisa Bon
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalActiveReceivables, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Dana Pelanggan:</span>
                    <span class="font-bold text-[#163144]">Belum Lunas</span>
                </p>
            </div>
        </div>

        <!-- Piutang Overdue -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Audit Jatuh Tempo</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Lewat Tempo</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Overdue
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalOverdueReceivables, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Perlu Tagih:</span>
                    <span class="font-bold text-[#163144]">Urgent</span>
                </p>
            </div>
        </div>

        <!-- Jatuh Tempo <= 7 Hari -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Jadwal Dekat</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Tempo ≤ 7 Hari</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Due Soon
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalDueSoonReceivables, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Pengingat WA:</span>
                    <span class="font-bold text-[#163144]">Minggu Ini</span>
                </p>
            </div>
        </div>

        <!-- Pelanggan Berhutang -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Debitur</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Pelanggan Bon</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    {{ $totalCustomerDebtCount }} Kontak
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $totalCustomerDebtCount }} <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase">Pelanggan</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Sudah Lunas:</span>
                    <span class="font-bold text-[#163144]">Rp {{ number_format($totalCollectedReceivables, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

    </div>

    <!-- Filters & Status Tabs -->
    <div class="glass-card-plek rounded-[24px] p-4 space-y-3">
        
        <!-- Status Tabs -->
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
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'overdue' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Lewat Tempo
            </button>
            <button 
                type="button" 
                wire:click="$set('statusTab', 'due_soon')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'due_soon' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Tempo ≤ 7 Hari
            </button>
            <button 
                type="button" 
                wire:click="$set('statusTab', 'paid')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'paid' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Lunas
            </button>
            <button 
                type="button" 
                wire:click="$set('statusTab', 'all')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $statusTab === 'all' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Semua Data
            </button>
        </div>

        <!-- Search and Customer Filter -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 pt-2 border-t border-white/40">
            
            <!-- Search Keyword -->
            <div class="sm:col-span-2 relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari no invoice, nama pelanggan, no HP WhatsApp..." 
                    class="w-full px-4 py-2 rounded-2xl glass-pill-plek text-xs font-semibold text-[#163144] placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                >
            </div>

            <!-- Customer Searchable Select -->
            @php
                $customerOptions = collect([['value' => '', 'label' => 'Semua Pelanggan', 'sublabel' => '']]);
                foreach ($customersList as $c) {
                    $customerOptions->push([
                        'value' => (string) $c->id,
                        'label' => $c->name,
                        'sublabel' => ($c->phone ? $c->phone . ' • ' : '') . ($c->member_code ?? '')
                    ]);
                }
            @endphp
            <div 
                x-data="searchableSelect({
                    options: {{ Js::from($customerOptions) }},
                    initialValue: '{{ $customerId ?? '' }}',
                    placeholder: 'Filter Pelanggan...',
                    wireProp: 'customerId',
                    isLive: true,
                    threshold: 0
                })"
                class="relative"
            >
                <button 
                    type="button" 
                    @click="toggle()" 
                    @keydown="onKeyDown($event)"
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] flex items-center justify-between outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer transition"
                >
                    <span class="truncate" x-text="selectedLabel"></span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div 
                    x-show="open" 
                    x-cloak 
                    @click.away="close()"
                    class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden"
                >
                    <div class="p-2 border-b border-slate-100">
                        <input 
                            x-ref="searchInput"
                            x-model="search"
                            type="text" 
                            placeholder="Ketik cari pelanggan..." 
                            class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-[#a0bddb]"
                        >
                    </div>
                    <ul x-ref="optionsList" class="max-h-56 overflow-y-auto p-1 space-y-0.5">
                        <template x-for="(opt, idx) in filteredOptions" :key="opt.value">
                            <li 
                                @click="select(opt.value)"
                                :class="{ 'bg-slate-100 text-[#163144] font-black': value === opt.value, 'hover:bg-slate-50 font-semibold text-slate-700': value !== opt.value }"
                                class="px-3 py-2 rounded-xl text-xs cursor-pointer flex flex-col"
                            >
                                <span x-text="opt.label"></span>
                                <span x-show="opt.sublabel" class="text-[10px] text-slate-400 font-semibold" x-text="opt.sublabel"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

        </div>

    </div>

    <!-- Table of Receivables -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs min-w-[850px]">
                <thead>
                    <tr class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                        <th class="py-3.5 px-4 pl-5 whitespace-nowrap">No. Invoice & Tanggal</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Pelanggan</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Total Belanja</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">DP / Dibayar</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Sisa Piutang</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Jatuh Tempo</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-4 pr-5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/30 font-semibold text-slate-700">
                    @forelse($receivables as $trx)
                        @php
                            $today = \Carbon\Carbon::today();
                            $isOverdue = $trx->payment_status !== 'paid' && $trx->due_date && $trx->due_date->lt($today);
                            $isDueSoon = $trx->payment_status !== 'paid' && $trx->due_date && !$isOverdue && $trx->due_date->lte($today->copy()->addDays(7));
                        @endphp
                        <tr class="hover:bg-white/40 transition">
                            
                            <!-- Invoice & Date -->
                            <td class="py-3.5 px-4 pl-5">
                                <button 
                                    type="button"
                                    wire:click="openDetail({{ $trx->id }})" 
                                    class="font-black text-[#163144] hover:text-[#386797] block text-left cursor-pointer"
                                >
                                    {{ $trx->invoice_number }}
                                </button>
                                <span class="text-[10px] text-slate-400 block font-semibold mt-0.5">
                                    {{ $trx->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>

                            <!-- Customer Info -->
                            <td class="py-3.5 px-4">
                                <div class="font-black text-[#163144] flex items-center gap-1.5">
                                    <span>{{ $trx->customer_name }}</span>
                                    @if($trx->is_loyalty)
                                        <span class="text-[9px] font-extrabold glass-pill-plek text-[#163144] px-1.5 py-0.2 rounded-full">
                                            Member
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">
                                    {{ $trx->customer?->phone ?: 'Tanpa HP' }}
                                </span>
                            </td>

                            <!-- Total Amount -->
                            <td class="py-3.5 px-4 text-right font-black text-[#163144]">
                                Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                            </td>

                            <!-- Paid / DP Amount -->
                            <td class="py-3.5 px-4 text-right font-bold text-[#163144]">
                                Rp {{ number_format($trx->paid_amount, 0, ',', '.') }}
                            </td>

                            <!-- Remaining Amount -->
                            <td class="py-3.5 px-4 text-right font-black text-[#163144]">
                                Rp {{ number_format($trx->remaining_amount, 0, ',', '.') }}
                            </td>

                            <!-- Due Date -->
                            <td class="py-3.5 px-4">
                                @if($trx->due_date)
                                    <span class="text-xs font-bold text-[#163144]">
                                        {{ $trx->due_date->format('d/m/Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <span class="text-[9px] font-extrabold text-[#163144] glass-pill-plek px-2 py-0.5 rounded-full block w-max mt-0.5">
                                            Telat {{ (int)$trx->due_date->diffInDays($today) }} Hari
                                        </span>
                                    @elseif($isDueSoon)
                                        <span class="text-[9px] font-extrabold text-[#163144] glass-pill-plek px-2 py-0.5 rounded-full block w-max mt-0.5">
                                            {{ (int)$today->diffInDays($trx->due_date) }} Hari Lagi
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-[11px] font-semibold">-</span>
                                @endif
                            </td>

                            <!-- Payment Status Badge -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold glass-pill-plek text-[#163144]">
                                    {{ $trx->payment_status === 'paid' ? 'Lunas' : ($trx->payment_status === 'partial' ? 'Dicicil Sebagian' : 'Belum Dibayar') }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 pr-5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    
                                    <!-- Button Catat Cicilan / Pelunasan -->
                                    @if($trx->remaining_amount > 0)
                                        <button 
                                            type="button" 
                                            wire:click="openPaymentModal({{ $trx->id }})" 
                                            class="btn-glass-action"
                                            title="Catat Cicilan / Pembayaran"
                                        >
                                            <span>Bayar</span>
                                        </button>
                                    @endif

                                    <!-- Button WhatsApp Reminder -->
                                    @if($trx->remaining_amount > 0 && $trx->customer?->phone)
                                        <a 
                                            href="{{ $this->getWhatsAppReminderUrl($trx) }}" 
                                            target="_blank"
                                            class="btn-glass-action"
                                            title="Kirim Pengingat Tagihan WhatsApp"
                                        >
                                            <span>WA</span>
                                        </a>
                                    @endif

                                    <!-- Button Detail / Nota Struk -->
                                    <button 
                                        type="button" 
                                        wire:click="openDetail({{ $trx->id }})" 
                                        class="btn-glass-action"
                                        title="Lihat Detail Nota"
                                    >
                                        <span>Detail</span>
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <p class="text-xs font-bold text-[#163144]">Tidak ada catatan piutang ditemukan</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan filter atau kata kunci pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($receivables as $trx)
                @php
                    $today = \Carbon\Carbon::today();
                    $isOverdue = $trx->payment_status !== 'paid' && $trx->due_date && $trx->due_date->lt($today);
                    $isDueSoon = $trx->payment_status !== 'paid' && $trx->due_date && !$isOverdue && $trx->due_date->lte($today->copy()->addDays(7));
                @endphp
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono font-black text-[#163144] text-xs block truncate">{{ $trx->invoice_number }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $trx->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold glass-pill-plek text-[#163144]">
                            {{ $trx->payment_status === 'paid' ? 'Lunas' : ($trx->payment_status === 'partial' ? 'Dicicil' : 'Belum Bayar') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <div class="min-w-0">
                            <span class="font-bold text-[#163144] block truncate">{{ $trx->customer_name }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $trx->customer?->phone ?: 'Tanpa HP' }}</span>
                        </div>
                        @if($trx->due_date)
                            <div class="text-right">
                                <span class="text-[10px] text-slate-500 font-bold block">Tempo: {{ $trx->due_date->format('d/m/Y') }}</span>
                                @if($isOverdue)
                                    <span class="text-[9px] font-black text-rose-700 bg-rose-50 px-2 py-0.2 rounded-full inline-block">Telat {{ (int)$trx->due_date->diffInDays($today) }} Hari</span>
                                @elseif($isDueSoon)
                                    <span class="text-[9px] font-black text-amber-700 bg-amber-50 px-2 py-0.2 rounded-full inline-block">{{ (int)$today->diffInDays($trx->due_date) }} Hari Lagi</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 glass-pill-plek p-2.5 rounded-xl text-xs">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Total Belanja</span>
                            <span class="font-mono font-black text-[#163144]">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Sisa Piutang</span>
                            <span class="font-mono font-black text-rose-600">Rp {{ number_format($trx->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-white/40">
                        @if($trx->remaining_amount > 0)
                            <button 
                                type="button" 
                                wire:click="openPaymentModal({{ $trx->id }})" 
                                class="btn-glass-action"
                            >
                                Bayar
                            </button>
                        @endif
                        @if($trx->remaining_amount > 0 && $trx->customer?->phone)
                            <a 
                                href="{{ $this->getWhatsAppReminderUrl($trx) }}" 
                                target="_blank"
                                class="btn-glass-action"
                            >
                                WA
                            </a>
                        @endif
                        <button 
                            type="button" 
                            wire:click="openDetail({{ $trx->id }})" 
                            class="btn-glass-action"
                        >
                            Detail
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400 text-xs">
                    Tidak ada catatan piutang ditemukan.
                </div>
            @endforelse
        </div>

        {{ $receivables->links() }}
    </div>

    <!-- ================= MODAL CATAT PEMBAYARAN CICILAN PIUTANG ================= -->
    @if($showPaymentModal && $selectedTransaction)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl modal-content-animate my-auto">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Catat Pembayaran Piutang</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black">{{ $selectedTransaction->invoice_number }}</span>
                            </div>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="closePaymentModal" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit="recordPayment" class="p-5 sm:p-6 space-y-4">
                    
                    <!-- Customer & Invoice Summary Card -->
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-semibold">Pelanggan:</span>
                            <span class="font-black text-[#163144]">{{ $selectedTransaction->customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-semibold">Total Tagihan Awal:</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($selectedTransaction->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-semibold">Sudah Dibayar (DP):</span>
                            <span class="font-bold text-[#163144]">Rp {{ number_format($selectedTransaction->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-slate-200/80 font-black text-[#163144]">
                            <span>Sisa Piutang Saat Ini:</span>
                            <span class="text-sm font-mono">Rp {{ number_format($selectedTransaction->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Nominal Pembayaran Input with Rupiah Format -->
                    <div 
                        x-data="{
                            displayAmount: '0',
                            init() {
                                this.updateDisplay($wire.paymentAmount);
                                $watch('$wire.paymentAmount', val => this.updateDisplay(val));
                            },
                            updateDisplay(val) {
                                let num = parseInt(val) || 0;
                                this.displayAmount = num > 0 ? num.toLocaleString('id-ID') : '0';
                            },
                            handleInput(e) {
                                let clean = e.target.value.replace(/[^0-9]/g, '');
                                let num = parseInt(clean) || 0;
                                this.displayAmount = num > 0 ? num.toLocaleString('id-ID') : '0';
                                $wire.set('paymentAmount', num);
                            }
                        }"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-black text-[#163144] uppercase tracking-wider">Nominal Pembayaran (Rp)</label>
                            <button 
                                type="button" 
                                @click="$wire.set('paymentAmount', {{ (int)$selectedTransaction->remaining_amount }})" 
                                class="text-[11px] font-black text-[#163144] hover:underline cursor-pointer"
                            >
                                Bayar Lunas Penuh
                            </button>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center font-black text-slate-400 text-sm">Rp</span>
                            <input 
                                type="text" 
                                x-model="displayAmount"
                                @input="handleInput($event)"
                                @focus="$event.target.select()"
                                class="w-full pl-12 pr-4 py-2.5 rounded-xl glass-input-plek text-base font-black text-[#163144] outline-none font-mono"
                                autofocus
                            >
                        </div>
                        @error('paymentAmount') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Metode Pembayaran Pelunasan -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Metode Penerimaan</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button 
                                type="button" 
                                wire:click="$set('paymentMethod', 'cash')"
                                class="py-2.5 rounded-2xl text-xs font-black transition cursor-pointer {{ $paymentMethod === 'cash' ? 'bg-[#163144] text-white shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                Tunai
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('paymentMethod', 'transfer')"
                                class="py-2.5 rounded-2xl text-xs font-black transition cursor-pointer {{ $paymentMethod === 'transfer' ? 'bg-[#163144] text-white shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                Transfer
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('paymentMethod', 'qris')"
                                class="py-2.5 rounded-2xl text-xs font-black transition cursor-pointer {{ $paymentMethod === 'qris' ? 'bg-[#163144] text-white shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                QRIS
                            </button>
                        </div>
                    </div>

                    <!-- Catatan Pelunasan -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Catatan (Opsional)</label>
                        <input 
                            wire:model="paymentNotes" 
                            type="text" 
                            placeholder="Keterangan cicilan..." 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                        >
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="closePaymentModal" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                        >
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- ================= MODAL DETAIL & NOTA STRUK ================= -->
    @if($showDetailModal && $selectedDetailTransaction)
        @php
            $receiptSetting = \App\Models\ReceiptSetting::getSettings();
            $receiptPaperSize = $receiptSetting->default_paper_size ?? '58mm';
        @endphp
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate"
        >
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[92vh] modal-content-animate my-auto">
                
                <!-- Action Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Nota Transaksi Tempo</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black">{{ $selectedDetailTransaction->invoice_number }}</span>
                            </div>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="closeDetail" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                    >
                        ✕
                    </button>
                </div>

                <!-- Printable Receipt Content -->
                <div 
                    class="p-4 sm:p-5 bg-white overflow-y-auto transition-all duration-150 select-text {{ $receiptPaperSize === '80mm' ? 'text-xs max-w-[340px] mx-auto' : 'text-[11px] max-w-[270px] mx-auto' }}"
                    id="printable-receivable-receipt"
                    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Courier New', Courier; line-height: 1.35;"
                >
                    <!-- Header Toko -->
                    <div class="text-center">
                        <h3 class="text-base sm:text-lg font-normal tracking-tight text-black mb-1 leading-tight">
                            {{ $receiptSetting->store_name ?: 'Syafa frozen food' }}
                        </h3>
                        @if($receiptSetting->store_address)
                            <p class="text-[11px] leading-tight text-neutral-800 px-1">
                                {{ $receiptSetting->store_address }}
                            </p>
                        @endif
                        @if($receiptSetting->store_phone)
                            <p class="text-[11px] text-neutral-800 mt-0.5">
                                No.Tlp {{ $receiptSetting->store_phone }}
                            </p>
                        @endif
                        @if($receiptSetting->header_notes)
                            <p class="text-[11px] text-neutral-800 mt-0.5">
                                {{ $receiptSetting->header_notes }}
                            </p>
                        @endif
                    </div>

                    <!-- Divider 1 -->
                    <div class="border-b border-dashed border-slate-300 my-2"></div>

                    <!-- Info Tanggal, Kasir & No. Invoice -->
                    <div class="text-[11px] text-neutral-900 flex justify-between items-start leading-tight">
                        <div>
                            <div>{{ $selectedDetailTransaction->created_at->format('Y-m-d') }}</div>
                            <div>{{ $selectedDetailTransaction->created_at->format('H:i:s') }}</div>
                        </div>
                        <div class="text-right">
                            @if($receiptSetting->show_cashier_name)
                                <div>Kasir : {{ $selectedDetailTransaction->user->name ?? '-' }}</div>
                            @endif
                            <div>{{ $selectedDetailTransaction->invoice_number }}</div>
                        </div>
                    </div>

                    @if($receiptSetting->show_customer_name && $selectedDetailTransaction->customer_name)
                        <div class="text-[11px] text-neutral-900 mt-1">
                            Pelanggan : {{ $selectedDetailTransaction->customer_name }}
                        </div>
                    @endif

                    <!-- Divider 2 -->
                    <div class="border-b border-dashed border-slate-300 my-2"></div>

                    <!-- Item List -->
                    <div class="space-y-2 text-[11px] text-neutral-900">
                        @foreach($selectedDetailTransaction->items as $item)
                            <div>
                                <div class="font-bold uppercase">{{ $item->product_name }}</div>
                                <div class="flex justify-between items-center text-neutral-800">
                                    <span>{{ $item->quantity }} &nbsp;x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Divider 3 -->
                    <div class="border-b border-dashed border-slate-300 my-2"></div>

                    <!-- Totals Calculation -->
                    <div class="space-y-1 text-[11px] text-neutral-900">
                        <div class="flex justify-between">
                            <span>Total QTY : {{ $selectedDetailTransaction->items->sum('quantity') }}</span>
                            <span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sub Total</span>
                            <span>Rp {{ number_format($selectedDetailTransaction->subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($selectedDetailTransaction->discount_amount > 0)
                            <div class="flex justify-between">
                                <span>Diskon:</span>
                                <span>- Rp {{ number_format($selectedDetailTransaction->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between font-bold text-xs pt-1">
                            <span>Total</span>
                            <span>Rp {{ number_format($selectedDetailTransaction->grand_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between pt-0.5">
                            <span>Metode</span>
                            <span class="font-bold">TEMPO</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Uang Muka (DP)</span>
                            <span>Rp {{ number_format($selectedDetailTransaction->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span>Sisa Piutang</span>
                            <span>Rp {{ number_format($selectedDetailTransaction->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Jatuh Tempo</span>
                            <span>{{ $selectedDetailTransaction->due_date ? $selectedDetailTransaction->due_date->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status</span>
                            <span class="font-bold">{{ $selectedDetailTransaction->payment_status === 'paid' ? 'LUNAS' : ($selectedDetailTransaction->payment_status === 'partial' ? 'DICICIL SEBAGIAN' : 'BELUM LUNAS') }}</span>
                        </div>
                    </div>

                    @if($selectedDetailTransaction->notes)
                        <div class="py-1 text-[10px] text-neutral-700">
                            <span class="font-bold block">Catatan:</span>
                            <p>{{ $selectedDetailTransaction->notes }}</p>
                        </div>
                    @endif

                    <!-- Divider 4 -->
                    <div class="border-b border-dashed border-slate-300 my-2"></div>

                    <!-- Footer Struk -->
                    <div class="text-center text-[10px] text-neutral-800 space-y-1 pt-0.5">
                        @if($receiptSetting->footer_link)
                            <div>Link Kritik dan Saran:</div>
                            <div class="font-normal break-all">{{ $receiptSetting->footer_link }}</div>
                        @endif

                        @if($receiptSetting->footer_notes)
                            <div class="pt-0.5 leading-tight">{{ $receiptSetting->footer_notes }}</div>
                        @endif
                    </div>
                </div>

                <!-- Print & WhatsApp Buttons -->
                <div class="p-4 px-6 bg-slate-50/60 border-t border-slate-100 space-y-2.5 shrink-0">
                    <div class="grid grid-cols-2 gap-2">
                        <button 
                            type="button" 
                            @click="printThermalReceipt('printable-receivable-receipt', '{{ $receiptPaperSize }}')" 
                            class="py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs cursor-pointer text-center flex items-center justify-center gap-1.5 shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105"
                        >
                            <span>Cetak Struk</span>
                        </button>
                        <button 
                            type="button" 
                            onclick="sendReceiptToWhatsAppImage('printable-receivable-receipt', '{{ $selectedDetailTransaction->customer?->phone ?? '' }}', '{{ $selectedDetailTransaction->invoice_number }}')"
                            class="py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs cursor-pointer flex items-center justify-center gap-1.5 shadow-md shadow-emerald-900/20 transition active:scale-95 hover:scale-105"
                        >
                            <span>Kirim WA</span>
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <a 
                            href="{{ route('admin.print.receivables.invoice', $selectedDetailTransaction->id) }}" 
                            target="_blank" 
                            class="btn-glass-action flex-1 py-2.5 rounded-full font-extrabold text-xs cursor-pointer text-center flex items-center justify-center gap-1"
                        >
                            <span>Surat Tagihan A4</span>
                        </a>
                        <button 
                            type="button" 
                            wire:click="closeDetail" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
