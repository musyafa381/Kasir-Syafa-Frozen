<div class="space-y-3">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Retur Penjualan & Pengembalian Barang</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                Monitoring riwayat pengembalian barang dari pelanggan, audit mutasi stok, dan nilai pengembalian dana (refund).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.transactions.index') }}" 
                wire:navigate
                class="btn-glass-action inline-flex items-center gap-2 px-4 py-2 rounded-full font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Riwayat Transaksi</span>
            </a>
        </div>
    </div>

    <!-- 4 EXECUTIVE KPI CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- 1. Total Nilai Refund -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Total Dana</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Nilai Refund</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Refund Kas
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($stats['total_refund'], 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Pengembalian:</span>
                    <span class="font-bold text-[#163144]">Dana Pelanggan</span>
                </p>
            </div>
        </div>

        <!-- 2. Total Kasus Retur -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Frekuensi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Kasus Retur</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Struk Retur
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($stats['total_cases']) }} <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase">Kasus</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Audit Nota:</span>
                    <span class="font-bold text-[#163144]">Transaksi Asal</span>
                </p>
            </div>
        </div>

        <!-- 3. Total Barang Diretur -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Fisik Barang</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Item Diretur</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Kuantitas
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($stats['total_items']) }} <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase">Pcs</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Produk Kembali:</span>
                    <span class="font-bold text-[#163144]">Audit Fisik</span>
                </p>
            </div>
        </div>

        <!-- 4. Barang Masuk Stok (Restock) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Inventori</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Masuk Stok</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Restocked
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($stats['total_restocked']) }} <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase">Pcs</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Kembali ke Stok:</span>
                    <span class="font-bold text-[#163144]">Otomatis Sync</span>
                </p>
            </div>
        </div>

    </div>

    <!-- Filters & Tabs Section -->
    <div class="glass-card-plek rounded-[24px] p-4 space-y-3">
        
        <!-- Period Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0 sm:flex-wrap">
            <button 
                type="button" 
                wire:click="setPeriod('all')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === 'all' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Semua Waktu
            </button>
            <button 
                type="button" 
                wire:click="setPeriod('today')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === 'today' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Hari Ini
            </button>
            <button 
                type="button" 
                wire:click="setPeriod('this_week')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === 'this_week' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Minggu Ini
            </button>
            <button 
                type="button" 
                wire:click="setPeriod('this_month')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === 'this_month' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Bulan Ini
            </button>
            <button 
                type="button" 
                wire:click="setPeriod('this_year')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === 'this_year' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Tahun Ini
            </button>
            <button 
                type="button" 
                wire:click="setPeriod('custom')" 
                class="px-3.5 py-1.5 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $period === 'custom' ? 'bg-[#163144] text-white shadow-xs' : 'btn-glass-action' }}"
            >
                Rentang Kustom
            </button>
        </div>

        <!-- Filter Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 pt-2 border-t border-white/40">
            
            <!-- Search Keyword -->
            <div class="{{ $period === 'custom' ? 'lg:col-span-1' : 'lg:col-span-2' }} relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari no. retur, invoice, pelanggan, atau produk..." 
                    class="w-full px-4 py-2 rounded-2xl glass-pill-plek text-xs font-semibold text-[#163144] placeholder-slate-400 outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
                @endif
            </div>

            <!-- Custom Date Range -->
            @if($period === 'custom')
                <div class="flex items-center gap-1.5">
                    <input 
                        wire:model.live="startDate" 
                        type="date" 
                        class="w-full px-3 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition cursor-pointer"
                    >
                    <span class="text-xs text-slate-400 font-bold">-</span>
                    <input 
                        wire:model.live="endDate" 
                        type="date" 
                        class="w-full px-3 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition cursor-pointer"
                    >
                </div>
            @endif

            <!-- Alasan Filter -->
            <div>
                <select 
                    wire:model.live="reasonFilter" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer transition"
                >
                    <option value="all">-- Semua Alasan Retur --</option>
                    @foreach($reasons as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Restock Filter -->
            <div>
                <select 
                    wire:model.live="restockFilter" 
                    class="w-full px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer transition"
                >
                    <option value="all">-- Semua Status Restock --</option>
                    <option value="1">Masuk Stok (Restocked)</option>
                    <option value="0">Tidak Restock (Rusak/Dibuang)</option>
                </select>
            </div>

        </div>

    </div>

    <!-- Table of Returns -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        
        <!-- Desktop View (>= sm) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[850px]">
                <thead class="text-[#163144]/60 font-black uppercase text-[10px] border-b border-white/40">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">No. Retur & Tanggal</th>
                        <th class="p-3.5 whitespace-nowrap">No. Invoice Asal</th>
                        <th class="p-3.5 whitespace-nowrap">Pelanggan & Kasir</th>
                        <th class="p-3.5 whitespace-nowrap">Rincian Barang Diretur</th>
                        <th class="p-3.5 whitespace-nowrap">Alasan & Catatan</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Status Restock</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Nilai Refund</th>
                        <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/30 font-semibold">
                    @forelse($returns as $ret)
                        <tr class="hover:bg-white/40 transition">
                            
                            <!-- No. Retur & Tanggal -->
                            <td class="p-3.5 pl-5 whitespace-nowrap">
                                <button 
                                    type="button" 
                                    wire:click="openDetail({{ $ret->id }})" 
                                    class="font-black text-[#163144] hover:text-[#386797] block text-left cursor-pointer text-xs"
                                >
                                    {{ $ret->return_number }}
                                </button>
                                <span class="text-[10px] text-slate-400 block font-semibold mt-0.5">
                                    {{ $ret->created_at->translatedFormat('d/m/Y H:i') }} WIB
                                </span>
                            </td>

                            <!-- No. Invoice Asal -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-xs inline-block">
                                    {{ $ret->invoice_number ?: ($ret->transaction?->invoice_number ?? '-') }}
                                </span>
                            </td>

                            <!-- Pelanggan & Kasir -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-black text-[#163144] text-xs block">
                                    {{ $ret->customer?->name ?: ($ret->transaction?->customer_name ?: 'Pelanggan Umum') }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-semibold">
                                    Kasir: {{ $ret->user?->name ?? 'Admin' }}
                                </span>
                            </td>

                            <!-- Rincian Barang Diretur -->
                            <td class="p-3.5 min-w-[220px]">
                                <div class="space-y-1">
                                    @foreach($ret->items as $item)
                                        <div class="flex items-center justify-between gap-2 text-[11px] glass-pill-plek px-2.5 py-1 rounded-xl border border-white/60">
                                            <span class="font-bold text-[#163144] truncate max-w-[140px]" title="{{ $item->product_name }}">
                                                {{ $item->product_name }}
                                            </span>
                                            <span class="font-black text-[#163144] shrink-0">
                                                {{ $item->quantity }}x @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Alasan & Catatan -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold glass-pill-plek text-[#163144]">
                                    {{ $ret->reason }}
                                </span>
                                @if($ret->notes)
                                    <p class="text-[10px] text-slate-500 mt-1 italic truncate max-w-xs" title="{{ $ret->notes }}">
                                        "{{ $ret->notes }}"
                                    </p>
                                @endif
                            </td>

                            <!-- Status Restock -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold glass-pill-plek text-[#163144]">
                                    <span>{{ $ret->restock ? 'Masuk Stok' : 'Tidak Restock' }}</span>
                                </span>
                            </td>

                            <!-- Nilai Refund -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-black text-[#163144] text-xs block">
                                    Rp {{ number_format($ret->total_refund, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Aksi Side by Side -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        type="button" 
                                        wire:click="openDetail({{ $ret->id }})" 
                                        class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full font-extrabold text-xs"
                                        title="Lihat Bukti Retur"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Detail</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="confirmDelete({{ $ret->id }})" 
                                        class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full font-extrabold text-xs"
                                        title="Hapus / Batalkan Retur"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <p class="font-bold text-[#163144] text-xs">Belum ada data transaksi yang diretur.</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Retur diproses melalui tombol "Retur" pada Riwayat Transaksi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List (< sm) -->
        <div class="sm:hidden divide-y divide-white/30">
            @forelse($returns as $ret)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <button 
                                type="button" 
                                wire:click="openDetail({{ $ret->id }})" 
                                class="font-black text-[#163144] text-sm block text-left"
                            >
                                {{ $ret->return_number }}
                            </button>
                            <span class="text-[10px] text-slate-400 block font-semibold">{{ $ret->created_at->translatedFormat('d/m/Y H:i') }} WIB</span>
                        </div>
                        <span class="font-black text-[9px] uppercase px-2.5 py-0.5 rounded-full glass-pill-plek shrink-0 text-[#163144]">
                            {{ $ret->restock ? '● Masuk Stok' : '✕ No Restock' }}
                        </span>
                    </div>

                    <div class="text-[11px] text-slate-600 space-y-1 glass-pill-plek p-3 rounded-2xl border border-white/60">
                        <div class="flex justify-between">
                            <span class="font-semibold text-slate-400">Invoice Asal:</span>
                            <span class="font-black text-[#163144]">{{ $ret->invoice_number ?: ($ret->transaction?->invoice_number ?? '-') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-slate-400">Pelanggan:</span>
                            <span class="font-bold text-[#163144]">{{ $ret->customer?->name ?: ($ret->transaction?->customer_name ?: 'Pelanggan Umum') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-slate-400">Alasan:</span>
                            <span class="font-bold text-[#163144]">{{ $ret->reason }}</span>
                        </div>
                        <div class="flex justify-between pt-1 border-t border-white/40">
                            <span class="font-semibold text-slate-400">Total Refund:</span>
                            <span class="font-black text-[#163144]">Rp {{ number_format($ret->total_refund, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-0.5">
                        <button 
                            type="button" 
                            wire:click="openDetail({{ $ret->id }})" 
                            class="btn-glass-action inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-extrabold"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Detail</span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="confirmDelete({{ $ret->id }})" 
                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-extrabold"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 font-semibold px-4">
                    Belum ada data transaksi yang diretur.
                </div>
            @endforelse
        </div>

        {{ $returns->links() }}
    </div>

    <!-- ================= MODAL DETAIL BUKTI RETUR (PRINT & WA) ================= -->
    @if($showDetailModal && $selectedReturn)
        @php
            $receiptSetting = \App\Models\ReceiptSetting::getSettings();
            $receiptPaperSize = $receiptSetting->default_paper_size ?? '58mm';
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[92vh] relative my-auto modal-content-animate">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base leading-tight">Bukti Retur Penjualan</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black">{{ $selectedReturn->return_number }}</span>
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

                <!-- Receipt Body Preview -->
                <div class="p-4 sm:p-6 overflow-y-auto bg-slate-50/70 flex justify-center">
                    
                    <div 
                        id="return-receipt-printable" 
                        class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 w-full max-w-[340px] text-xs font-mono text-slate-800 space-y-3 select-text"
                    >
                        <!-- Header Toko -->
                        <div class="text-center space-y-1">
                            @if($receiptSetting->show_logo && $receiptSetting->logo_path)
                                <img src="{{ asset('storage/' . $receiptSetting->logo_path) }}" alt="Logo" class="h-10 mx-auto object-contain mb-1">
                            @endif
                            <h2 class="font-bold text-base text-slate-900 uppercase tracking-tight">{{ $receiptSetting->store_name }}</h2>
                            <p class="text-[10px] text-slate-500 leading-tight">{{ $receiptSetting->store_address }}</p>
                            @if($receiptSetting->store_phone)
                                <p class="text-[10px] text-slate-500">Telp/WA: {{ $receiptSetting->store_phone }}</p>
                            @endif
                        </div>

                        <!-- Divider 1 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Info Tanda Terima Retur -->
                        <div class="text-center">
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-[#163144] font-bold text-[10px] uppercase tracking-wider">
                                BUKTI TANDA TERIMA RETUR
                            </span>
                        </div>

                        <div class="space-y-1 text-[11px] text-slate-600 pt-1">
                            <div class="flex justify-between">
                                <span>No. Retur:</span>
                                <span class="font-bold text-slate-900">{{ $selectedReturn->return_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>No. Invoice Asal:</span>
                                <span class="font-bold text-slate-900">{{ $selectedReturn->invoice_number ?: ($selectedReturn->transaction?->invoice_number ?? '-') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tanggal:</span>
                                <span>{{ $selectedReturn->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Petugas:</span>
                                <span>{{ $selectedReturn->user->name ?? 'Admin' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pelanggan:</span>
                                <span>{{ $selectedReturn->customer?->name ?: ($selectedReturn->transaction?->customer_name ?: 'Pelanggan Umum') }}</span>
                            </div>
                        </div>

                        <!-- Divider 2 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Daftar Item Retur -->
                        <div class="space-y-1.5 text-[11px]">
                            <div class="font-bold text-slate-900 text-[10px] uppercase">Rincian Barang Diretur:</div>
                            @foreach($selectedReturn->items as $item)
                                <div>
                                    <div class="font-bold text-slate-900">{{ $item->product_name }}</div>
                                    <div class="flex justify-between text-slate-600 text-[10px]">
                                        <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                        <span class="font-bold text-[#163144]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Divider 3 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Total Refund & Alasan -->
                        <div class="space-y-1 text-[11px]">
                            <div class="flex justify-between font-bold text-slate-900 text-xs">
                                <span>TOTAL DANA REFUND:</span>
                                <span class="text-[#163144] font-black">Rp {{ number_format($selectedReturn->total_refund, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-600 pt-1">
                                <span>Alasan Retur:</span>
                                <span class="font-bold text-slate-800">{{ $selectedReturn->reason }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-600">
                                <span>Status Inventori:</span>
                                <span class="font-bold text-[#163144]">
                                    {{ $selectedReturn->restock ? 'Dikembalikan ke Stok Toko' : 'Tidak Masuk Stok' }}
                                </span>
                            </div>
                            @if($selectedReturn->notes)
                                <div class="text-[10px] text-slate-600 pt-1 border-t border-slate-100">
                                    <span class="font-bold">Catatan:</span> {{ $selectedReturn->notes }}
                                </div>
                            @endif
                        </div>

                        <!-- Divider 4 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Footer Struk -->
                        <div class="text-center text-[10px] text-slate-500 space-y-0.5">
                            <p>Bukti sah pengembalian barang & refund dana.</p>
                            <p>{{ $receiptSetting->store_name }}</p>
                        </div>

                    </div>
                </div>

                <!-- Print & Share Toolbar -->
                <div class="p-4 px-6 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between gap-2 shrink-0">
                    <div class="flex items-center gap-2">
                        <button 
                            type="button"
                            onclick="downloadReceiptImage('return-receipt-printable', '{{ $selectedReturn->return_number }}')"
                            class="px-4 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Unduh PNG
                        </button>
                        <button 
                            type="button" 
                            @click="printThermalReceipt('return-receipt-printable', '{{ $receiptPaperSize }}')" 
                            class="px-5 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Bukti</span>
                        </button>
                    </div>

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
    @endif

    <!-- Modal Konfirmasi Hapus Retur -->
    @if($showDeleteModal && $deletingReturn)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="rounded-[28px] max-w-md w-full border border-white/50 overflow-hidden shadow-2xl shadow-[#163144]/30 p-6 relative my-auto confirm-card bg-white/30 backdrop-blur-2xl">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-[#e1144b] border border-rose-300/40 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-base text-[#163144] tracking-tight">Hapus Data Retur Penjualan</h3>
                        <p class="text-xs text-slate-700 font-semibold mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data retur <strong class="text-[#163144] font-black">{{ $deletingReturn->return_number }}</strong>?
                            @if($deletingReturn->restock)
                                <span class="block text-slate-800 font-bold mt-1">Stok produk yang sebelumnya dikembalikan ke sistem akan dikurangi kembali secara otomatis.</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="border-t border-slate-300/40 pt-3.5 mt-4 flex items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteModal', false)" 
                        class="px-5 py-2 rounded-full bg-white/50 hover:bg-white/75 border border-white/40 text-slate-700 font-black text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="deleteReturn" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
