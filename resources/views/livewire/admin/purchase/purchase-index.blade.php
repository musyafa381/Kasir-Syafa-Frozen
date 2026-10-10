<div class="space-y-3">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 sm:pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Pembelian & Stok Masuk (Restock)</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Catat faktur pengadaan barang dari supplier, update otomatis stok & HPP, serta kelola hutang tempo.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.suppliers.index') }}" 
                wire:navigate 
                class="btn-glass-action"
            >
                <span>Kelola Supplier</span>
            </a>

            <a 
                href="{{ route('admin.purchases.create') }}" 
                wire:navigate 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Buat Faktur Pembelian</span>
            </a>
        </div>
    </div>

    <!-- 4 Executive KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- 1. Total Belanja Periode -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Pengadaan</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Belanja</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-mono font-bold text-[9px] sm:text-[10px] shrink-0">
                    {{ $totalPurchasesCount }} Faktur
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block truncate">
                    Rp {{ number_format($totalPurchasesAmount, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Total faktur belanja stok</p>
            </div>
        </div>

        <!-- 2. Terbayar Lunas -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kas Keluar</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Sudah Terbayar</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-bold text-[9px] sm:text-[10px] shrink-0">
                    Lunas
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-emerald-700 font-mono tracking-tight block truncate">
                    Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Belanja stok terbayar</p>
            </div>
        </div>

        <!-- 3. Sisa Hutang Tempo -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-amber-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Hutang Dagang</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Sisa Tempo</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200/60 font-bold text-[9px] sm:text-[10px] shrink-0">
                    {{ $totalTempoCount }} Tempo
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-amber-700 font-mono tracking-tight block truncate">
                    Rp {{ number_format($totalTempoRemaining, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Kewajiban belum lunas</p>
            </div>
        </div>

        <!-- 4. Pemasok Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Mitra Toko</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Supplier</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-[9px] sm:text-[10px] shrink-0">
                    Mitra Aktif
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block truncate">
                    {{ $suppliers->count() }} Pemasok
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Distributor & agen toko</p>
            </div>
        </div>

    </div>

    <!-- Unified Filters & Search Card -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4 space-y-3">
        
        <!-- Period Quick Presets & Total Found in one row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1 sm:pb-0 -mx-1 px-1">
                <span class="text-[10px] sm:text-[11px] font-black uppercase tracking-wider text-slate-400 mr-1 shrink-0">Periode:</span>
                
                @php
                    $presets = [
                        'today' => 'Hari Ini',
                        'yesterday' => 'Kemarin',
                        'last_7_days' => '7 Hari',
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
                        class="shrink-0 px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $period === $key ? 'bg-[#163144] text-white shadow-xs font-black' : 'glass-pill-plek text-slate-600 hover:text-[#163144]' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <span class="text-xs text-slate-500 font-bold shrink-0 hidden sm:inline">
                Menemukan <strong class="text-[#163144] font-mono">{{ $purchases->total() }}</strong> faktur pembelian
            </span>
        </div>

        <!-- Filters Grid: Search Input + Dates + Dropdowns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5 sm:gap-3 pt-2.5 border-t border-white/60">
            
            <!-- Search Box (Span 2 col on Desktop) -->
            <div class="sm:col-span-2 relative">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Cari Faktur / Supplier</label>
                <div class="relative">
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="No PO, faktur, nama supplier..." 
                        class="w-full pl-8 pr-7 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if($search)
                        <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer font-bold">✕</button>
                    @endif
                </div>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input 
                    wire:model.live="startDate" 
                    type="date" 
                    class="w-full px-2.5 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                >
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input 
                    wire:model.live="endDate" 
                    type="date" 
                    class="w-full px-2.5 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                >
            </div>

            <!-- Cabang / Outlet Filter -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Cabang Penerima</label>
                <select 
                    wire:model.live="outletFilter" 
                    class="w-full px-2.5 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                >
                    <option value="">Semua Cabang</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}{{ $outlet->is_main ? ' (Pusat)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Supplier Filter (Searchable Dropdown) -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Filter Supplier</label>
                <x-searchable-select 
                    wire:model.live="supplierFilter" 
                    :options="$suppliers" 
                    placeholder="Semua Supplier" 
                    empty-option="Semua Supplier" 
                />
            </div>

            <!-- Payment Status Filter -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Status Pembayaran</label>
                <select 
                    wire:model.live="paymentStatusFilter" 
                    class="w-full px-2.5 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                >
                    <option value="">Semua Status</option>
                    <option value="paid">Lunas</option>
                    <option value="partial">Sebagian (Tempo)</option>
                    <option value="unpaid">Belum Dibayar (Tempo)</option>
                    <option value="overdue">Jatuh Tempo</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Table & Mobile Card View of Purchases -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[850px]">
                <thead class="bg-[#163144]/5 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">No. Faktur PO & Tanggal</th>
                        <th class="p-3.5 whitespace-nowrap">Supplier & Cabang</th>
                        <th class="p-3.5 whitespace-nowrap">Jenis Bayar</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Total Belanja</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Terbayar / Sisa</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-medium">
                    @forelse($purchases as $po)
                        <tr class="hover:bg-white/60 transition">
                            
                            <!-- No PO & Tanggal -->
                            <td class="p-3.5 pl-5">
                                <span class="font-black text-[#163144] font-mono block">{{ $po->invoice_number }}</span>
                                <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                                    <span>{{ $po->purchase_date->format('d/m/Y') }}</span>
                                    @if($po->supplier_invoice_number)
                                        <span class="font-mono bg-[#a0bddb]/20 text-[#163144] px-1.5 py-0.2 rounded text-[9px] font-bold">
                                            Faktur: {{ $po->supplier_invoice_number }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Supplier & Cabang -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-bold text-[#163144] block">{{ $po->supplier->name ?? 'Supplier Dihapus' }}</span>
                                <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-[#a0bddb]/30 text-[#163144] font-bold text-[9px]">
                                        🏢 {{ $po->outlet->name ?? 'Pusat' }}
                                    </span>
                                    <span>• Petugas: {{ $po->user->name ?? 'Admin' }}</span>
                                </div>
                            </td>

                            <!-- Jenis Bayar & Jatuh Tempo -->
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="font-black uppercase text-[10px] text-[#163144]">{{ $po->payment_type }}</div>
                                @if($po->payment_type === 'tempo' && $po->due_date)
                                    <span class="text-[10px] font-mono {{ $po->is_overdue ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                        Tempo: {{ $po->due_date->format('d/m/Y') }}
                                        @if($po->is_overdue) (Terlambat) @endif
                                    </span>
                                @endif
                            </td>

                            <!-- Total Belanja -->
                            <td class="p-3.5 text-right font-mono font-black text-[#163144] whitespace-nowrap">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                <div class="text-[10px] text-slate-400 font-sans font-normal">{{ $po->items->count() }} item produk</div>
                            </td>

                            <!-- Terbayar / Sisa -->
                            <td class="p-3.5 text-right whitespace-nowrap font-mono">
                                <div class="text-emerald-700 font-bold">Rp {{ number_format($po->paid_amount, 0, ',', '.') }}</div>
                                @if($po->remaining_amount > 0)
                                    <div class="text-[10px] text-amber-700 font-bold">Sisa: Rp {{ number_format($po->remaining_amount, 0, ',', '.') }}</div>
                                @endif
                            </td>

                            <!-- Status Bayar -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($po->payment_status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-[10px]">Lunas</span>
                                @elseif($po->is_overdue)
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200 font-bold text-[10px]">Jatuh Tempo</span>
                                @elseif($po->payment_status === 'partial')
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[10px]">Sebagian</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-bold text-[10px]">Belum Bayar</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a 
                                        href="{{ route('admin.print.purchases.po', $po->id) }}"
                                        target="_blank"
                                        class="btn-glass-action"
                                        title="Cetak Surat Pesanan (PO)"
                                    >
                                        <span>PO</span>
                                    </a>
                                    <button 
                                        type="button"
                                        wire:click="openDetail({{ $po->id }})" 
                                        class="btn-glass-action"
                                        title="Detail Faktur & Pelunasan"
                                    >
                                        <span>Detail</span>
                                    </button>
                                    <button 
                                        type="button"
                                        wire:click="confirmDelete({{ $po->id }})" 
                                        class="btn-glass-action"
                                        title="Batalkan Faktur Pembelian (Rollback Stok)"
                                    >
                                        <span>Batal</span>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Belum ada faktur pembelian yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($purchases as $po)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-black text-[#163144] font-mono text-xs block">{{ $po->invoice_number }}</span>
                            <span class="text-[11px] text-slate-500">{{ $po->purchase_date->format('d/m/Y') }} • {{ $po->supplier->name ?? 'Supplier Dihapus' }} • <strong class="text-[#163144]">🏢 {{ $po->outlet->name ?? 'Pusat' }}</strong></span>
                        </div>
                        <div>
                            @if($po->payment_status === 'paid')
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold text-[10px]">Lunas</span>
                            @elseif($po->is_overdue)
                                <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200 font-bold text-[10px]">Jatuh Tempo</span>
                            @elseif($po->payment_status === 'partial')
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[10px]">Sebagian</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-bold text-[10px]">Belum Bayar</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 bg-[#163144]/5 p-2.5 rounded-2xl text-xs font-mono border border-white/80">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-black font-sans block">Total Belanja</span>
                            <span class="font-black text-[#163144]">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-black font-sans block">Terbayar</span>
                            <span class="font-bold text-emerald-700">Rp {{ number_format($po->paid_amount, 0, ',', '.') }}</span>
                            @if($po->remaining_amount > 0)
                                <span class="text-[10px] text-amber-700 block font-bold">Sisa: Rp {{ number_format($po->remaining_amount, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-white/40">
                        <span class="text-[11px] text-slate-400 uppercase font-bold">{{ $po->payment_type }}</span>
                        <div class="flex items-center gap-1.5">
                            <a 
                                href="{{ route('admin.print.purchases.po', $po->id) }}"
                                target="_blank"
                                class="btn-glass-action"
                            >
                                <span>PO</span>
                            </a>
                            <button 
                                type="button"
                                wire:click="openDetail({{ $po->id }})" 
                                class="btn-glass-action"
                            >
                                <span>Detail</span>
                            </button>
                            <button 
                                type="button"
                                wire:click="confirmDelete({{ $po->id }})" 
                                class="btn-glass-action"
                            >
                                <span>Batal</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada faktur pembelian yang sesuai dengan filter yang dipilih.
                </div>
            @endforelse
        </div>

        @if($purchases->hasPages())
            <div class="p-4 border-t border-white/60 bg-white/40">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL DETAIL FAKTUR PEMBELIAN & PELUNASAN TEMPO -->
    @if($showDetailModal && $selectedPurchase)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek rounded-[32px] max-w-2xl w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[90vh] relative my-auto modal-content-animate bg-white/95">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Faktur Pembelian</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black">{{ $selectedPurchase->invoice_number }}</span>
                                <span class="text-xs text-slate-500 font-semibold">{{ $selectedPurchase->purchase_date->format('d F Y') }}</span>
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

                <div class="p-6 space-y-5 overflow-y-auto">
                    
                    <!-- Info Header Card -->
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase block">Supplier</span>
                            <span class="font-bold text-[#163144]">{{ $selectedPurchase->supplier->name ?? '-' }}</span>
                            @if($selectedPurchase->supplier_invoice_number)
                                <span class="text-[10px] text-slate-500 block font-mono">No. Faktur: {{ $selectedPurchase->supplier_invoice_number }}</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase block">Cabang Penerima</span>
                            <span class="inline-flex items-center gap-1 font-bold text-[#163144] mt-0.5">
                                🏢 {{ $selectedPurchase->outlet->name ?? 'Pusat' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase block">Metode Pembayaran</span>
                            <span class="font-black uppercase text-[#163144]">{{ $selectedPurchase->payment_type }}</span>
                            @if($selectedPurchase->payment_type === 'tempo' && $selectedPurchase->due_date)
                                <span class="text-[10px] text-amber-700 block font-mono font-bold">Jatuh Tempo: {{ $selectedPurchase->due_date->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase block">Petugas Penerima</span>
                            <span class="font-semibold text-[#163144]">{{ $selectedPurchase->user->name ?? 'Admin' }}</span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="space-y-2">
                        <h4 class="font-black text-xs text-[#163144] uppercase tracking-wider">Rincian Barang Masuk</h4>
                        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-2xs">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-500 font-black uppercase text-[9px] border-b border-slate-100">
                                    <tr>
                                        <th class="p-2.5 pl-4">Produk</th>
                                        <th class="p-2.5 text-center">Jumlah (Qty)</th>
                                        <th class="p-2.5 text-right">Harga Beli Satuan</th>
                                        <th class="p-2.5 pr-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @foreach($selectedPurchase->items as $item)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="p-2.5 pl-4 font-bold text-[#163144]">
                                                {{ $item->product_name }}
                                                @if($item->update_cost_price)
                                                    <span class="text-[9px] font-mono text-[#163144] bg-[#a0bddb]/30 px-1.5 py-0.2 rounded-full ml-1 font-bold">HPP Tersinkron</span>
                                                @endif
                                            </td>
                                            <td class="p-2.5 text-center font-mono font-black text-[#163144]">{{ $item->quantity }}</td>
                                            <td class="p-2.5 text-right font-mono text-slate-700">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                            <td class="p-2.5 pr-4 text-right font-mono font-black text-[#163144]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totals & Payment Status -->
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-2 text-xs">
                        <div class="flex justify-between font-bold text-slate-700">
                            <span>Total Nilai Faktur:</span>
                            <span class="font-mono font-black text-[#163144]">Rp {{ number_format($selectedPurchase->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-emerald-700">
                            <span>Total Sudah Dibayar:</span>
                            <span class="font-mono font-black">Rp {{ number_format($selectedPurchase->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedPurchase->remaining_amount > 0)
                            <div class="flex justify-between font-black text-amber-700 pt-1 border-t border-slate-200">
                                <span>Sisa Hutang Tempo:</span>
                                <span class="font-mono text-sm">Rp {{ number_format($selectedPurchase->remaining_amount, 0, ',', '.') }}</span>
                            </div>
                        @else
                            <div class="flex justify-between font-black text-[#163144] pt-1 border-t border-slate-200">
                                <span>Status Faktur:</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-[10px] font-black">Lunas Sepenuhnya</span>
                            </div>
                        @endif
                    </div>

                    <!-- Form Cicil / Pelunasan Hutang (Jika ada sisa) -->
                    @if($selectedPurchase->remaining_amount > 0)
                        <div class="p-4 bg-amber-50/70 border border-amber-200/80 rounded-2xl space-y-3">
                            <div>
                                <h4 class="font-black text-xs text-amber-900 uppercase tracking-wider">Catat Pembayaran / Pelunasan Hutang</h4>
                                <p class="text-[10px] text-amber-700 font-semibold">Masukkan nominal pembayaran cicilan atau pelunasan faktur ini</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                    <input 
                                        wire:model="additionalPayment" 
                                        type="number" 
                                        min="1" 
                                        max="{{ $selectedPurchase->remaining_amount }}" 
                                        class="w-full pl-9 pr-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-mono font-black text-slate-900 outline-none focus:ring-2 focus:ring-amber-400 shadow-2xs"
                                    >
                                </div>
                                <button 
                                    type="button" 
                                    wire:click="recordPayment" 
                                    class="px-5 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                                >
                                    Simpan Pembayaran
                                </button>
                            </div>
                        </div>
                    @endif

                </div>

                <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between">
                    <a 
                        href="{{ route('admin.print.purchases.po', $selectedPurchase->id) }}" 
                        target="_blank"
                        class="px-5 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs cursor-pointer inline-flex items-center gap-1.5 shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak Surat Pesanan (PO A4)</span>
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
    @endif

    <!-- MODAL BATAL / HAPUS FAKTUR PEMBELIAN -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek rounded-[32px] max-w-sm w-full border border-white/90 p-6 space-y-4 shadow-2xl text-center relative my-auto modal-content-animate bg-white/95">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                
                <div>
                    <h3 class="font-black text-[#163144] text-lg">Batalkan Faktur Pembelian?</h3>
                    <p class="text-xs text-slate-500 mt-1 font-semibold">
                        Apakah Anda yakin ingin membatalkan faktur <strong class="text-[#163144] font-mono font-black">{{ $deletingPurchaseInvoice }}</strong>?
                    </p>
                </div>

                <div class="p-3 bg-rose-50/80 rounded-2xl border border-rose-200 text-rose-800 text-[11px] font-bold text-left">
                    Peringatan: Stok barang yang telah masuk dari faktur ini akan otomatis dikurangi kembali (*rollback*) dari stok master produk.
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
                        wire:click="deletePurchase" 
                        class="flex-1 py-2.5 rounded-full bg-rose-600/90 hover:bg-rose-700 text-white font-black text-xs cursor-pointer transition active:scale-95 hover:scale-105 shadow-md shadow-rose-950/20"
                    >
                        Ya, Batalkan & Rollback
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
