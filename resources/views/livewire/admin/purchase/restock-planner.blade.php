<div class="space-y-4">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Perencanaan Kulakan (Restock)</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Analisis persediaan stok menipis per supplier, susun order kulakan, kirim via WhatsApp, dan tarik data ke faktur pembelian.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.purchases.index') }}" 
                wire:navigate 
                class="btn-glass-action"
            >
                <span>Riwayat Pembelian</span>
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

    <!-- Flash Notifications -->
    @if(session()->has('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-2xs backdrop-blur-md">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-900 text-xs font-bold flex items-center justify-between shadow-2xs backdrop-blur-md">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">✕</button>
        </div>
    @endif

    <!-- 4 Executive KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        
        <!-- 1. Produk Stok Menipis -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-rose-600 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Inventory</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Stok Menipis</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-rose-700 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Alert
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    {{ $allLowStockCount }} <span class="text-xs font-sans text-slate-500 font-bold">Item</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Habis Total:</span>
                    <span class="font-bold text-rose-600">{{ $outOfStockCount }} Item</span>
                </p>
            </div>
        </div>

        <!-- 2. Supplier Terkait -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Pemasok</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Supplier Order</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Mitra
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    {{ $productsBySupplier->count() }} <span class="text-xs font-sans text-slate-500 font-bold">Pemasok</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Status:</span>
                    <span class="font-bold text-[#163144]">Perlu Kulakan</span>
                </p>
            </div>
        </div>

        <!-- 3. Estimasi Modal Kulakan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-600 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Estimasi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Modal Restock</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-700 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    HPP
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    Rp {{ number_format($totalEstCapital, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Kebutuhan:</span>
                    <span class="font-bold text-emerald-600">Restock Rekomendasi</span>
                </p>
            </div>
        </div>

        <!-- 4. Rencana Kulakan Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-amber-600 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Drafting</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Order Aktif</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-amber-700 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Pending
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    {{ $pendingPlansCount }} <span class="text-xs font-sans text-slate-500 font-bold">Rencana</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Tindak Lanjut:</span>
                    <span class="font-bold text-amber-600">Siap WA / Faktur</span>
                </p>
            </div>
        </div>

    </div>

    <!-- Navigation Tab Presets (Frosted Pill Bar) -->
    <div class="glass-card-plek rounded-2xl border border-white/80 p-1.5 sm:p-2 shadow-2xs flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar">
        <button 
            type="button" 
            wire:click="$set('activeTab', 'analysis')" 
            class="shrink-0 px-4 py-2.5 rounded-xl text-xs font-black transition cursor-pointer flex items-center justify-center gap-2 {{ $activeTab === 'analysis' ? 'bg-[#163144] text-white shadow-xs' : 'text-slate-600 hover:bg-white/60 hover:text-[#163144]' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>1. Analisis Stok Menipis</span>
            @if($allLowStockCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black {{ $activeTab === 'analysis' ? 'bg-white/20 text-white' : 'bg-[#a0bddb]/40 text-[#163144]' }}">
                    {{ $allLowStockCount }}
                </span>
            @endif
        </button>

        <button 
            type="button" 
            wire:click="$set('activeTab', 'orders')" 
            class="shrink-0 px-4 py-2.5 rounded-xl text-xs font-black transition cursor-pointer flex items-center justify-center gap-2 {{ $activeTab === 'orders' ? 'bg-[#163144] text-white shadow-xs' : 'text-slate-600 hover:bg-white/60 hover:text-[#163144]' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>2. Daftar Rencana Kulakan & WA</span>
            @if($pendingPlansCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black {{ $activeTab === 'orders' ? 'bg-white/20 text-white' : 'bg-[#a0bddb]/40 text-[#163144]' }}">
                    {{ $pendingPlansCount }}
                </span>
            @endif
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: ANALISIS STOK MENIPIS & SUSUN ORDER PER SUPPLIER -->
    <!-- ========================================================================= -->
    @if($activeTab === 'analysis')
        <div class="space-y-6">
            
            <!-- Filters & Global Action Bar -->
            <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Search & Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-1">
                    <!-- Search Keyword -->
                    <div class="relative">
                        <input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Cari nama produk atau barcode..." 
                            class="w-full pl-9 pr-8 py-2 bg-white/80 border border-white rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                        >
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs cursor-pointer font-bold">✕</button>
                        @endif
                    </div>

                    <!-- Filter Supplier -->
                    <div>
                        <select 
                            wire:model.live="selectedSupplierFilter" 
                            class="w-full px-3 py-2 bg-white/80 border border-white rounded-xl text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                        >
                            <option value="">Semua Supplier Pemasok</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Category -->
                    <div>
                        <select 
                            wire:model.live="selectedCategoryFilter" 
                            class="w-full px-3 py-2 bg-white/80 border border-white rounded-xl text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                        >
                            <option value="">Semua Kategori Produk</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Global Action -->
                <div class="flex items-center gap-2 shrink-0">
                    <button 
                        type="button" 
                        @click="window.customConfirm ? window.customConfirm({ title: 'Buat Rencana Kulakan', message: 'Buat rencana order kulakan otomatis untuk semua produk yang dicentang (dikelompokkan per supplier)?', confirmText: 'Ya, Buat Order', type: 'info', onConfirm: () => $wire.createPlanForAllSelected() }) : $wire.createPlanForAllSelected()"
                        class="px-4 py-2.5 rounded-full bg-[#163144] hover:bg-[#1f425c] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition cursor-pointer flex items-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Buat Semua Order Terpilih</span>
                    </button>
                </div>
            </div>

            <!-- List of Suppliers with their Low Stock Items -->
            @if($productsBySupplier->isEmpty())
                <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 overflow-hidden shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs min-w-[760px]">
                            <thead class="bg-[#163144]/5 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                                <tr>
                                    <th class="p-3.5 pl-5 w-10 text-center">Pilih</th>
                                    <th class="p-3.5 whitespace-nowrap min-w-[220px]">Produk Frozen Food</th>
                                    <th class="p-3.5 text-center whitespace-nowrap">Sisa Stok</th>
                                    <th class="p-3.5 text-center whitespace-nowrap">Batas Min</th>
                                    <th class="p-3.5 text-center whitespace-nowrap min-w-[140px]">Jumlah Pesan (Qty)</th>
                                    <th class="p-3.5 text-right whitespace-nowrap min-w-[130px]">HPP Modal (Rp)</th>
                                    <th class="p-3.5 pr-5 text-right whitespace-nowrap min-w-[130px]">Subtotal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/40 font-medium">
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-slate-400">
                                        <p class="text-xs font-bold text-[#163144]">Semua Persediaan Stok Aman</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada produk yang berada di bawah batas stok minimal (min stock alert).</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($productsBySupplier as $supplierId => $items)
                        @php
                            $supplier = $supplierId > 0 ? $suppliers->firstWhere('id', $supplierId) : null;
                            $supplierTotalCost = $items->sum(function($p) {
                                $input = $this->orderInputs[$p->id] ?? null;
                                if ($input && !empty($input['selected'])) {
                                    $qty = (int) $input['quantity'];
                                    $cost = (float) ($input['unit_cost'] ?: $p->cost_price);
                                    return $qty * $cost;
                                }
                                return 0;
                            });
                            $selectedCount = $items->filter(fn($p) => !empty($this->orderInputs[$p->id]['selected']))->count();
                        @endphp

                        <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 overflow-hidden shadow-xs">
                            <!-- Supplier Header Banner -->
                            <div class="bg-white/60 border-b border-white/60 p-4 sm:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-black text-[#163144]">
                                            {{ $supplier ? $supplier->name : 'Produk Belum Ditautkan Supplier' }}
                                        </h3>
                                        @if($supplier && $supplier->code)
                                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-[#163144] text-white font-bold">
                                                {{ $supplier->code }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2 flex-wrap">
                                        @if($supplier)
                                            @if($supplier->contact_person)
                                                <span>PIC: <strong class="text-slate-700">{{ $supplier->contact_person }}</strong></span>
                                                <span class="text-slate-300">•</span>
                                            @endif
                                            @if($supplier->phone)
                                                <span>Telp/WA: <strong class="font-mono text-slate-700">{{ $supplier->phone }}</strong></span>
                                            @endif
                                        @else
                                            <span class="text-amber-700 font-semibold">Pilihkan supplier agar pesanan bisa dikirim via WhatsApp.</span>
                                        @endif
                                    </p>
                                </div>

                                <!-- Action for this Supplier -->
                                <div class="flex items-center gap-3 self-end sm:self-auto">
                                    <div class="text-right hidden sm:block">
                                        <span class="text-[10px] text-slate-400 block font-bold">Est. Subtotal Order:</span>
                                        <span class="text-xs font-black font-mono text-[#163144]">Rp {{ number_format($supplierTotalCost, 0, ',', '.') }}</span>
                                    </div>

                                    @if($supplier)
                                        <button 
                                            type="button" 
                                            wire:click="createPlanForSupplier({{ $supplier->id }})" 
                                            class="px-4 py-2 rounded-full bg-[#163144] hover:bg-[#1f425c] text-white font-black text-xs shadow-xs transition cursor-pointer flex items-center gap-1.5"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            <span>Buat Order ({{ $selectedCount }} Item)</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Desktop Table View -->
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="w-full text-left text-xs min-w-[760px]">
                                    <thead class="bg-[#163144]/5 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                                        <tr>
                                            <th class="p-3.5 pl-5 w-10 text-center">
                                                <span class="sr-only">Pilih</span>
                                            </th>
                                            <th class="p-3.5 whitespace-nowrap min-w-[220px]">Produk Frozen Food</th>
                                            <th class="p-3.5 text-center whitespace-nowrap">Sisa Stok</th>
                                            <th class="p-3.5 text-center whitespace-nowrap">Batas Min</th>
                                            <th class="p-3.5 text-center whitespace-nowrap min-w-[140px]">Jumlah Pesan (Qty)</th>
                                            <th class="p-3.5 text-right whitespace-nowrap min-w-[130px]">HPP Modal (Rp)</th>
                                            <th class="p-3.5 pr-5 text-right whitespace-nowrap min-w-[130px]">Subtotal (Rp)</th>
                                            @if(!$supplier)
                                                <th class="p-3.5 text-center whitespace-nowrap min-w-[180px]">Pilihkan Supplier</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/40 font-medium">
                                        @foreach($items as $product)
                                            @php
                                                $input = $this->orderInputs[$product->id] ?? ['selected' => true, 'quantity' => 1, 'unit_cost' => (float)$product->cost_price];
                                                $isSelected = !empty($input['selected']);
                                                $qty = (int) ($input['quantity'] ?? 1);
                                                $cost = (float) ($input['unit_cost'] ?? $product->cost_price);
                                                $subtotal = $qty * $cost;
                                                $isOutOfStock = $product->stock <= 0;
                                            @endphp
                                            <tr class="hover:bg-white/60 transition {{ $isSelected ? 'bg-[#a0bddb]/10' : 'opacity-60' }}">
                                                <!-- Checkbox -->
                                                <td class="p-3.5 pl-5 text-center">
                                                    <input 
                                                        wire:model.live="orderInputs.{{ $product->id }}.selected" 
                                                        type="checkbox" 
                                                        class="w-4 h-4 rounded text-[#163144] focus:ring-[#a0bddb] border-slate-300 cursor-pointer"
                                                    >
                                                </td>

                                                <!-- Product Info -->
                                                <td class="p-3.5">
                                                    <div class="font-black text-[#163144] leading-snug">{{ $product->name }}</div>
                                                    <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                                        <span class="font-mono">{{ $product->barcode ?: 'No Barcode' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $product->category ? $product->category->name : 'Umum' }}</span>
                                                        @if($product->pieces_per_box > 1)
                                                            <span>•</span>
                                                            <span class="text-[#386797] font-bold">1 Dus = {{ $product->pieces_per_box }} {{ $product->unit }}</span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Current Stock -->
                                                <td class="p-3.5 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $isOutOfStock ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                                        {{ $product->stock }} {{ $product->unit }}
                                                    </span>
                                                </td>

                                                <!-- Min Alert -->
                                                <td class="p-3.5 text-center font-mono text-slate-500 font-bold">
                                                    {{ $product->min_stock_alert }} {{ $product->unit }}
                                                </td>

                                                <!-- Input Qty Order -->
                                                <td class="p-3.5 text-center">
                                                    <div class="inline-flex items-center h-8 rounded-full border border-white/80 bg-white/80 overflow-hidden shadow-2xs">
                                                        <button 
                                                            type="button" 
                                                            wire:click="$set('orderInputs.{{ $product->id }}.quantity', {{ max(1, $qty - 1) }})" 
                                                            class="px-2.5 h-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-r border-slate-200 cursor-pointer"
                                                        >-</button>
                                                        <input 
                                                            wire:model.live.debounce.300ms="orderInputs.{{ $product->id }}.quantity" 
                                                            type="number" 
                                                            min="1" 
                                                            class="w-16 h-full text-center px-1 text-xs font-mono font-black text-[#163144] outline-none"
                                                        >
                                                        <button 
                                                            type="button" 
                                                            wire:click="$set('orderInputs.{{ $product->id }}.quantity', {{ $qty + 1 }})" 
                                                            class="px-2.5 h-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs border-l border-slate-200 cursor-pointer"
                                                        >+</button>
                                                    </div>
                                                </td>

                                                <!-- Input Cost Price -->
                                                <td class="p-3.5 text-right">
                                                    <div class="relative inline-block w-28">
                                                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[10px] text-slate-400 font-bold">Rp</span>
                                                        <input 
                                                            wire:model.live.debounce.300ms="orderInputs.{{ $product->id }}.unit_cost" 
                                                            type="number" 
                                                            min="0" 
                                                            class="w-full pl-7 pr-2 py-1 text-right bg-white/80 border border-white rounded-xl text-xs font-mono font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb]"
                                                        >
                                                    </div>
                                                </td>

                                                <!-- Subtotal -->
                                                <td class="p-3.5 pr-5 text-right font-mono font-black text-[#163144]">
                                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                </td>

                                                <!-- Assign Supplier Quick Dropdown (if no supplier) -->
                                                @if(!$supplier)
                                                    <td class="p-3.5 text-center">
                                                        <select 
                                                            wire:change="assignSupplierToProduct({{ $product->id }}, $event.target.value)" 
                                                            class="w-full px-2 py-1 bg-amber-50 border border-amber-300 rounded-xl text-xs font-bold text-amber-900 outline-none"
                                                        >
                                                            <option value="">-- Tautkan Supplier --</option>
                                                            @foreach($suppliers as $sup)
                                                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="sm:hidden divide-y divide-white/40">
                                @foreach($items as $product)
                                    @php
                                        $input = $this->orderInputs[$product->id] ?? ['selected' => true, 'quantity' => 1, 'unit_cost' => (float)$product->cost_price];
                                        $isSelected = !empty($input['selected']);
                                        $qty = (int) ($input['quantity'] ?? 1);
                                        $cost = (float) ($input['unit_cost'] ?? $product->cost_price);
                                        $subtotal = $qty * $cost;
                                        $isOutOfStock = $product->stock <= 0;
                                    @endphp
                                    <div class="p-3.5 space-y-2.5 {{ $isSelected ? 'bg-[#a0bddb]/10' : 'opacity-60' }}">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-start gap-2.5 min-w-0">
                                                <input 
                                                    wire:model.live="orderInputs.{{ $product->id }}.selected" 
                                                    type="checkbox" 
                                                    class="w-4 h-4 mt-0.5 rounded text-[#163144] focus:ring-[#a0bddb] border-slate-300 cursor-pointer"
                                                >
                                                <div class="min-w-0">
                                                    <span class="font-black text-[#163144] text-xs block leading-tight">{{ $product->name }}</span>
                                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-0.5 flex-wrap">
                                                        <span class="font-mono">{{ $product->barcode ?: 'No Barcode' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $product->category ? $product->category->name : 'Umum' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $isOutOfStock ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ $isOutOfStock ? 'Habis' : 'Sisa ' . $product->stock . ' ' . $product->unit }}
                                                </span>
                                                <span class="block text-[9px] text-slate-400 mt-0.5">Min: {{ $product->min_stock_alert }} {{ $product->unit }}</span>
                                            </div>
                                        </div>

                                        <!-- Qty Stepper & Unit Cost -->
                                        <div class="grid grid-cols-2 gap-2 pt-1 items-center">
                                            <div>
                                                <span class="text-[10px] font-bold text-slate-400 block mb-1">Jumlah Pesan (Qty)</span>
                                                <div class="inline-flex items-center rounded-xl overflow-hidden border border-white bg-white/80 shadow-2xs">
                                                    <button 
                                                        type="button" 
                                                        wire:click="$set('orderInputs.{{ $product->id }}.quantity', {{ max(1, $qty - 1) }})" 
                                                        class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-black text-xs border-r border-slate-200"
                                                    >-</button>
                                                    <input 
                                                        wire:model.live.debounce.300ms="orderInputs.{{ $product->id }}.quantity" 
                                                        type="number" 
                                                        min="1" 
                                                        class="w-12 h-7 text-center font-mono font-black bg-transparent text-xs outline-none text-[#163144]"
                                                    >
                                                    <button 
                                                        type="button" 
                                                        wire:click="$set('orderInputs.{{ $product->id }}.quantity', {{ $qty + 1 }})" 
                                                        class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-black text-xs border-l border-slate-200"
                                                    >+</button>
                                                </div>
                                            </div>

                                            <div>
                                                <span class="text-[10px] font-bold text-slate-400 block mb-1">HPP Modal (Rp)</span>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-[10px] font-bold text-slate-400">Rp</span>
                                                    <input 
                                                        wire:model.live.debounce.300ms="orderInputs.{{ $product->id }}.unit_cost" 
                                                        type="number" 
                                                        min="0" 
                                                        class="w-full pl-6 pr-2 py-1 text-right font-mono font-black bg-white/80 border border-white rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#a0bddb] text-[#163144] shadow-2xs"
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subtotal & Supplier Assignment if unassigned -->
                                        <div class="flex items-center justify-between pt-1 border-t border-white/50 text-xs">
                                            <span class="text-[11px] text-slate-500 font-semibold">Subtotal:</span>
                                            <span class="font-mono font-black text-[#163144]">
                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        @if(!$supplier)
                                            <div class="pt-1">
                                                <select 
                                                    wire:change="assignSupplierToProduct({{ $product->id }}, $event.target.value)" 
                                                    class="w-full px-2.5 py-1.5 bg-amber-50 border border-amber-300 rounded-xl text-xs font-bold text-amber-900 outline-none shadow-2xs"
                                                >
                                                    <option value="">-- Tautkan Supplier --</option>
                                                    @foreach($suppliers as $sup)
                                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 2: DAFTAR RENCANA KULAKAN & KIRIM WA (ORDERS TAB) -->
    <!-- ========================================================================= -->
    @if($activeTab === 'orders')
        <div class="space-y-4">
            
            <!-- Filters & Search Bar for Orders -->
            <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <!-- Search Plan Number / Supplier -->
                <div class="relative flex-1 max-w-md">
                    <input 
                        wire:model.live.debounce.300ms="orderSearch" 
                        type="text" 
                        placeholder="Cari no. pesanan (PO-PLAN...) atau nama supplier..." 
                        class="w-full pl-9 pr-8 py-2 bg-white/80 border border-white rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if($orderSearch)
                        <button wire:click="$set('orderSearch', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs cursor-pointer font-bold">✕</button>
                    @endif
                </div>

                <!-- Status Filter Presets -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button 
                        type="button" 
                        wire:click="$set('orderStatusFilter', 'all')" 
                        class="px-3 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $orderStatusFilter === 'all' ? 'bg-[#163144] text-white shadow-xs' : 'glass-pill-plek text-slate-600 hover:text-[#163144]' }}"
                    >
                        Semua
                    </button>
                    <button 
                        type="button" 
                        wire:click="$set('orderStatusFilter', 'draft')" 
                        class="px-3 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $orderStatusFilter === 'draft' ? 'bg-amber-600 text-white shadow-xs' : 'glass-pill-plek text-slate-600 hover:text-[#163144]' }}"
                    >
                        Draft
                    </button>
                    <button 
                        type="button" 
                        wire:click="$set('orderStatusFilter', 'ordered')" 
                        class="px-3 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $orderStatusFilter === 'ordered' ? 'bg-emerald-600 text-white shadow-xs' : 'glass-pill-plek text-slate-600 hover:text-[#163144]' }}"
                    >
                        Sudah Di-WA
                    </button>
                    <button 
                        type="button" 
                        wire:click="$set('orderStatusFilter', 'received')" 
                        class="px-3 py-1.5 rounded-full text-xs font-bold transition cursor-pointer {{ $orderStatusFilter === 'received' ? 'bg-[#163144] text-white shadow-xs' : 'glass-pill-plek text-slate-600 hover:text-[#163144]' }}"
                    >
                        Selesai Masuk Faktur
                    </button>
                </div>
            </div>

            <!-- Orders Table & Mobile Card View -->
            <div class="glass-card-plek rounded-2xl sm:rounded-3xl border border-white/80 overflow-hidden shadow-xs">
                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[850px]">
                        <thead class="bg-[#163144]/5 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                            <tr>
                                <th class="p-3.5 pl-5 whitespace-nowrap">No. Ref & Tanggal</th>
                                <th class="p-3.5 whitespace-nowrap">Supplier Pemasok</th>
                                <th class="p-3.5 text-center whitespace-nowrap">Jumlah Item</th>
                                <th class="p-3.5 text-right whitespace-nowrap">Est. Total Biaya</th>
                                <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                                <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/40 font-medium">
                            @forelse($orders as $plan)
                                <tr class="hover:bg-white/60 transition">
                                    <!-- No Ref & Date -->
                                    <td class="p-3.5 pl-5">
                                        <span class="font-black text-[#163144] font-mono block text-xs">{{ $plan->plan_number }}</span>
                                        <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                            <span>{{ $plan->created_at->format('d/m/Y H:i') }}</span>
                                            <span class="text-slate-300">•</span>
                                            <span>{{ $plan->user ? $plan->user->name : 'Staff' }}</span>
                                        </div>
                                    </td>

                                    <!-- Supplier -->
                                    <td class="p-3.5 whitespace-nowrap">
                                        <span class="font-bold text-[#163144] block text-xs">{{ $plan->supplier ? $plan->supplier->name : 'Tanpa Supplier' }}</span>
                                        <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                            <span>{{ $plan->supplier && $plan->supplier->phone ? $plan->supplier->phone : '-' }}</span>
                                            @if($plan->supplier && $plan->supplier->contact_person)
                                                <span>(PIC: {{ $plan->supplier->contact_person }})</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Total Items -->
                                    <td class="p-3.5 text-center whitespace-nowrap font-mono">
                                        <span class="font-bold text-[#163144]">{{ $plan->items->count() }} Produk</span>
                                        <span class="text-[10px] text-slate-400 block font-sans">({{ $plan->items->sum('quantity_ordered') }} Unit)</span>
                                    </td>

                                    <!-- Total Cost -->
                                    <td class="p-3.5 text-right whitespace-nowrap font-mono font-black text-[#163144] text-xs">
                                        Rp {{ number_format($plan->total_estimated_cost, 0, ',', '.') }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="p-3.5 text-center whitespace-nowrap">
                                        @if($plan->status === 'draft')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                Draft
                                            </span>
                                        @elseif($plan->status === 'ordered')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Sudah Di-WA ({{ $plan->ordered_at ? $plan->ordered_at->format('d/m') : 'Dipesan' }})
                                            </span>
                                        @elseif($plan->status === 'received')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#a0bddb]/30 text-[#163144] border border-[#a0bddb]/50">
                                                Masuk Faktur ({{ $plan->purchase ? $plan->purchase->invoice_number : 'Received' }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                Dibatalkan
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            
                                            <!-- WhatsApp Button -->
                                            <button 
                                                type="button" 
                                                wire:click="openWhatsAppModal({{ $plan->id }})" 
                                                class="btn-glass-action"
                                                title="Preview & Kirim via WhatsApp"
                                            >
                                                <span>WA</span>
                                            </button>

                                            <!-- Tarik ke Faktur Pembelian (Import to Purchase Invoice) -->
                                            @if($plan->status !== 'received' && $plan->status !== 'cancelled')
                                                <a 
                                                    href="{{ route('admin.purchases.create', ['restock_id' => $plan->id]) }}" 
                                                    wire:navigate 
                                                    class="btn-glass-action"
                                                    title="Tarik data ke Faktur Pembelian / Penerimaan Barang"
                                                >
                                                    <span>Ke Faktur</span>
                                                </a>
                                            @endif

                                            <!-- Detail Modal Button -->
                                            <button 
                                                type="button" 
                                                wire:click="openDetailModal({{ $plan->id }})" 
                                                class="btn-glass-action"
                                                title="Lihat Rincian Item"
                                            >
                                                <span>Detail</span>
                                            </button>

                                            <!-- Cancel/Delete if not received -->
                                            @if($plan->status !== 'received')
                                                <button 
                                                    type="button" 
                                                    wire:click="confirmDelete({{ $plan->id }})" 
                                                    class="btn-glass-action"
                                                    title="Hapus Rencana Kulakan"
                                                >
                                                    <span>Hapus</span>
                                                </button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-slate-400">
                                        <p class="text-xs font-bold text-[#163144]">Belum ada daftar rencana kulakan</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Silakan buka tab "Analisis Stok Menipis" untuk menyusun order kulakan pertama Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden divide-y divide-white/40">
                    @forelse($orders as $plan)
                        <div class="p-3.5 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-black text-[#163144] font-mono text-xs block">{{ $plan->plan_number }}</span>
                                    <span class="text-[11px] text-slate-500 font-bold">{{ $plan->supplier ? $plan->supplier->name : 'Tanpa Supplier' }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $plan->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    @if($plan->status === 'draft')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            Draft
                                        </span>
                                    @elseif($plan->status === 'ordered')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Sudah Di-WA
                                        </span>
                                    @elseif($plan->status === 'received')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#a0bddb]/30 text-[#163144] border border-[#a0bddb]/50">
                                            Masuk Faktur
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1 border-t border-white/40">
                                <span class="text-[11px] text-slate-400">{{ $plan->items->count() }} Produk ({{ $plan->items->sum('quantity_ordered') }} Unit)</span>
                                <span class="font-black font-mono text-[#163144]">Rp {{ number_format($plan->total_estimated_cost, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                <button 
                                    type="button" 
                                    wire:click="openWhatsAppModal({{ $plan->id }})" 
                                    class="btn-glass-action flex-1 justify-center"
                                >
                                    <span>WA</span>
                                </button>

                                @if($plan->status !== 'received' && $plan->status !== 'cancelled')
                                    <a 
                                        href="{{ route('admin.purchases.create', ['restock_id' => $plan->id]) }}" 
                                        wire:navigate 
                                        class="btn-glass-action flex-1 justify-center"
                                    >
                                        <span>Ke Faktur</span>
                                    </a>
                                @endif

                                <button 
                                    type="button" 
                                    wire:click="openDetailModal({{ $plan->id }})" 
                                    class="btn-glass-action flex-1 justify-center"
                                >
                                    <span>Detail</span>
                                </button>

                                @if($plan->status !== 'received')
                                    <button 
                                        type="button" 
                                        wire:click="confirmDelete({{ $plan->id }})" 
                                        class="btn-glass-action"
                                    >
                                        <span>Hapus</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400">
                            Belum ada rencana kulakan.
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="p-4 border-t border-white/60 bg-white/40">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL: WHATSAPP PREVIEW & LINK GENERATOR -->
    <!-- ========================================================================= -->
    @if($showWhatsAppModal && $whatsAppPlan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[90vh] relative my-auto modal-content-animate bg-white/95">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 border border-emerald-200/60 shadow-2xs flex items-center justify-center shrink-0">
                            <span class="text-base">📱</span>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Format Order WhatsApp</h3>
                            <p class="text-xs text-slate-500 font-medium">Supplier: <strong class="text-[#163144]">{{ $whatsAppPlan->supplier ? $whatsAppPlan->supplier->name : 'Supplier' }}</strong></p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showWhatsAppModal', false)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4 overflow-y-auto">
                    <!-- Target Phone Info -->
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                        <span class="text-slate-500 font-extrabold">Nomor WhatsApp Tujuan:</span>
                        <span class="font-mono font-black text-[#163144]">
                            {{ $whatsAppPlan->supplier && $whatsAppPlan->supplier->phone ? $whatsAppPlan->supplier->phone : 'Nomor HP belum diisi di profil supplier' }}
                        </span>
                    </div>

                    <!-- Message Preview -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Format Teks Pesanan:</label>
                        <div 
                            id="wa-message-box" 
                            class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-800 whitespace-pre-wrap leading-relaxed max-h-56 overflow-y-auto select-all shadow-inner"
                        >{{ $whatsAppPlan->whatsapp_message }}</div>
                    </div>

                    <!-- Items Summary -->
                    <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span>Total: <strong class="text-[#163144]">{{ $whatsAppPlan->items->count() }} Jenis Produk</strong></span>
                        <span>Est. Total: <strong class="font-mono text-[#163144] text-sm">Rp {{ number_format($whatsAppPlan->total_estimated_cost, 0, ',', '.') }}</strong></span>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="p-4 px-6 border-t border-slate-100 bg-white/80 flex items-center justify-between gap-2 shrink-0">
                    <button 
                        type="button" 
                        wire:click="$set('showWhatsAppModal', false)" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>

                    <div class="flex items-center gap-2">
                        @if($whatsAppPlan->whatsapp_url)
                            <a 
                                href="{{ $whatsAppPlan->whatsapp_url }}" 
                                target="_blank" 
                                wire:click="markAsOrdered({{ $whatsAppPlan->id }})" 
                                class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-950/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                            >
                                <span>Buka WhatsApp & Kirim</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <button 
                                type="button" 
                                wire:click="markAsOrdered({{ $whatsAppPlan->id }})" 
                                class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                            >
                                Tandai Sudah Dipesan
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL: DETAIL RENCANA KULAKAN -->
    <!-- ========================================================================= -->
    @if($showDetailModal && $selectedPlan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek rounded-[32px] max-w-2xl w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[90vh] relative my-auto modal-content-animate bg-white/95">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-base text-[#163144] leading-tight">Rincian Rencana Kulakan</h3>
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black">{{ $selectedPlan->plan_number }}</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Supplier: <strong class="text-[#163144]">{{ $selectedPlan->supplier ? $selectedPlan->supplier->name : '-' }}</strong> • {{ $selectedPlan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4 overflow-y-auto">
                    <!-- Items Table -->
                    <div class="overflow-x-auto border border-slate-100 rounded-2xl overflow-hidden shadow-2xs">
                        <table class="w-full text-left text-xs min-w-[500px]">
                            <thead class="bg-slate-50 text-[10px] uppercase font-black text-[#163144] border-b border-slate-200/60">
                                <tr>
                                    <th class="p-3 pl-4">No</th>
                                    <th class="p-3">Produk</th>
                                    <th class="p-3 text-center">Sisa Stok</th>
                                    <th class="p-3 text-center">Qty Dipesan</th>
                                    <th class="p-3 text-right">Est. HPP</th>
                                    <th class="p-3 pr-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($selectedPlan->items as $idx => $item)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="p-3 pl-4 text-slate-400 font-mono font-bold">{{ $idx + 1 }}</td>
                                        <td class="p-3 font-bold text-[#163144]">{{ $item->product_name }}</td>
                                        <td class="p-3 text-center font-mono text-slate-500">{{ $item->current_stock }} {{ $item->unit }}</td>
                                        <td class="p-3 text-center font-mono font-black text-[#163144]">{{ $item->quantity_ordered }} {{ $item->unit }}</td>
                                        <td class="p-3 text-right font-mono">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                        <td class="p-3 pr-4 text-right font-mono font-black text-[#163144]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 font-black border-t border-slate-200">
                                <tr>
                                    <td colspan="5" class="p-3 text-right text-slate-700">Total Estimasi Biaya:</td>
                                    <td class="p-3 pr-4 text-right font-mono text-[#163144] text-sm">
                                        Rp {{ number_format($selectedPlan->total_estimated_cost, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($selectedPlan->notes)
                        <div class="p-3.5 bg-slate-50 rounded-2xl text-xs text-slate-600 border border-slate-100">
                            <span class="font-black text-[#163144] block text-[10px] uppercase tracking-wider mb-0.5">Catatan:</span>
                            {{ $selectedPlan->notes }}
                        </div>
                    @endif
                </div>

                <!-- Modal Actions -->
                <div class="p-4 px-6 border-t border-slate-100 bg-white/80 flex items-center justify-between gap-2 shrink-0">
                    <button 
                        type="button" 
                        wire:click="$set('showDetailModal', false)" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="openWhatsAppModal({{ $selectedPlan->id }})" 
                            class="px-5 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-950/20 transition active:scale-95 hover:scale-105 cursor-pointer flex items-center gap-1.5"
                        >
                            <span>📱 Buka Teks WA</span>
                        </button>

                        @if($selectedPlan->status !== 'received' && $selectedPlan->status !== 'cancelled')
                            <a 
                                href="{{ route('admin.purchases.create', ['restock_id' => $selectedPlan->id]) }}" 
                                wire:navigate 
                                class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer flex items-center gap-1.5"
                            >
                                <span>📥 Masukkan ke Faktur</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL: KONFIRMASI HAPUS RENCANA KULAKAN -->
    <!-- ========================================================================= -->
    @if($showDeleteModal && $deletingPlan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="rounded-[28px] max-w-md w-full border border-white/50 overflow-hidden shadow-2xl shadow-[#163144]/30 p-6 relative my-auto confirm-card bg-white/30 backdrop-blur-2xl">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-[#e1144b] border border-rose-300/40 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Hapus Rencana Kulakan</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus rencana pesanan <strong class="text-[#163144] font-mono font-bold">{{ $deletingPlan->plan_number }}</strong> ({{ $deletingPlan->supplier ? $deletingPlan->supplier->name : 'Tanpa Supplier' }})?
                        </p>
                        <div class="mt-2 text-[11px] bg-white/50 rounded-xl p-2.5 text-slate-700 border border-white/40 font-mono">
                            <div>Jumlah Produk: <span class="font-bold text-[#163144]">{{ $deletingPlan->items->count() }} Item</span></div>
                            <div>Est. Biaya: <span class="font-bold text-[#163144]">Rp {{ number_format($deletingPlan->total_estimated_cost, 0, ',', '.') }}</span></div>
                        </div>
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
                        wire:click="deletePlan" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
