<div class="space-y-3">

    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Pemasok (Supplier) & Distributor</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                Kelola data pemasok makanan beku, kontak PIC, dan riwayat pasokan stok.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.purchases.index') }}" 
                wire:navigate 
                class="btn-glass-action"
            >
                <span>Riwayat Pembelian & Restock</span>
            </a>

            <button 
                type="button"
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#163144] hover:bg-[#244262] text-white font-extrabold text-xs shadow-xs transition cursor-pointer"
            >
                <span>Tambah Supplier</span>
            </button>
        </div>
    </div>

    <!-- 3 EXECUTIVE KPI SUMMARY STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
        
        <!-- Total Pemasok -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Total Mitra</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Semua Supplier</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Terdaftar
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    {{ $totalSuppliers }} <span class="text-xs font-bold text-slate-400 uppercase">Mitra</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Database:</span>
                    <span class="font-bold text-[#163144]">Mitra Terdaftar</span>
                </p>
            </div>
        </div>

        <!-- Supplier Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Status Aktif</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Bisa Restock</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-700 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Aktif
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    {{ $activeSuppliers }} <span class="text-xs font-bold text-slate-400 uppercase">Pemasok</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Ketersediaan:</span>
                    <span class="font-bold text-[#163144]">Bisa Order Barang</span>
                </p>
            </div>
        </div>

        <!-- Sistem Tempo -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Skema Bayar</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Sistem Tempo</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Kredit
                </span>
            </div>
            
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate font-mono">
                    {{ $tempoSuppliers }} <span class="text-xs font-bold text-slate-400 uppercase">Mitra</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Fasilitas:</span>
                    <span class="font-bold text-[#163144]">Supplier Kredit / Hutang</span>
                </p>
            </div>
        </div>

    </div>

    <!-- Filter & Search Toolbar -->
    <div class="glass-card-plek rounded-[24px] p-4 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari nama supplier, kode, PIC, atau no. telepon..." 
                    class="w-full pl-3.5 pr-10 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] transition"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs font-bold cursor-pointer">✕</button>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <select 
                    wire:model.live="statusFilter" 
                    class="px-3.5 py-2 rounded-2xl glass-pill-plek text-xs font-bold text-[#163144] outline-none focus:ring-2 focus:ring-[#a0bddb] cursor-pointer transition"
                >
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table & Mobile Card View of Suppliers -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[760px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/40">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Supplier & Kode</th>
                        <th class="p-3.5 whitespace-nowrap">Kontak PIC / WA</th>
                        <th class="p-3.5 whitespace-nowrap">Termin Bayar</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Produk Terkait</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Faktur Masuk</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/30 font-semibold">
                    @forelse($suppliers as $sup)
                        <tr class="hover:bg-white/40 transition">
                            
                            <!-- Nama & Kode -->
                            <td class="p-3.5 pl-5">
                                <div class="font-black text-[#163144] text-sm">{{ $sup->name }}</div>
                                <span class="text-[10px] font-mono text-slate-400 font-bold">{{ $sup->code ?: '-' }}</span>
                            </td>

                            <!-- Kontak PIC & WA -->
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="text-[#163144] font-bold">{{ $sup->contact_person ?: '-' }}</div>
                                @if($sup->phone)
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[11px] font-mono text-slate-500 font-bold">{{ $sup->phone }}</span>
                                        @if($sup->whatsapp_number)
                                            <a 
                                                href="https://wa.me/{{ $sup->whatsapp_number }}" 
                                                target="_blank" 
                                                class="px-1.5 py-0.2 rounded-full glass-pill-plek text-emerald-700 font-black text-[9px] hover:bg-white transition"
                                                title="Chat WhatsApp Pemasok"
                                            >
                                                WA
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400">Tidak ada no HP</span>
                                @endif
                            </td>

                            <!-- Termin Bayar -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if($sup->payment_term === 'cash')
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[10px]">Tunai (Cash)</span>
                                @elseif($sup->payment_term === 'tempo_7')
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-700 font-extrabold text-[10px]">Tempo 7 Hari</span>
                                @elseif($sup->payment_term === 'tempo_14')
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-amber-700 font-extrabold text-[10px]">Tempo 14 Hari</span>
                                @elseif($sup->payment_term === 'tempo_30')
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-purple-700 font-extrabold text-[10px]">Tempo 30 Hari</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-slate-600 font-extrabold text-[10px]">Kustom</span>
                                @endif
                            </td>

                            <!-- Jumlah Produk -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-black text-xs glass-pill-plek text-[#163144] font-mono">
                                    {{ $sup->products_count }} Produk
                                </span>
                            </td>

                            <!-- Jumlah Faktur Masuk -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-black text-xs glass-pill-plek text-[#163144] font-mono">
                                    {{ $sup->purchases_count }} Faktur
                                </span>
                            </td>

                            <!-- Status Aktif -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($sup->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-emerald-700 font-extrabold text-[10px]">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-slate-400 font-extrabold text-[10px]">Nonaktif</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        type="button"
                                        wire:click="openDetail({{ $sup->id }})" 
                                        class="btn-glass-action"
                                        title="Detail Supplier & Riwayat Pasokan"
                                    >
                                        <span>Detail</span>
                                    </button>
                                    <button 
                                        type="button"
                                        wire:click="openEditModal({{ $sup->id }})" 
                                        class="btn-glass-action"
                                        title="Edit Data Supplier"
                                    >
                                        <span>Edit</span>
                                    </button>
                                    <button 
                                        type="button"
                                        wire:click="confirmDelete({{ $sup->id }})" 
                                        class="btn-glass-action"
                                        title="Hapus Supplier"
                                    >
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400 font-semibold">
                                Belum ada data supplier ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/30">
            @forelse($suppliers as $sup)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-black text-[#163144] text-sm block">{{ $sup->name }}</span>
                            <div class="flex items-center gap-2 text-[11px] text-slate-500 font-semibold mt-0.5">
                                @if($sup->code)<span class="font-mono font-bold glass-pill-plek px-1.5 py-0.2 rounded-full text-[10px]">{{ $sup->code }}</span>@endif
                                <span>{{ $sup->contact_person ?: 'PIC -' }}</span>
                            </div>
                        </div>
                        <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold glass-pill-plek text-[#163144]">
                            {{ $sup->is_active ? '✓ Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs glass-pill-plek p-2.5 rounded-2xl border border-white/60">
                        <span class="font-bold text-[#163144] font-mono">{{ $sup->phone ?: 'No HP -' }}</span>
                        <span class="text-slate-500 font-semibold">{{ $sup->products_count }} Produk • {{ $sup->purchases_count }} Faktur</span>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1">
                        @if($sup->whatsapp_number)
                            <a 
                                href="https://wa.me/{{ $sup->whatsapp_number }}" 
                                target="_blank" 
                                class="btn-glass-action"
                            >
                                <span>WA</span>
                            </a>
                        @endif
                        <button 
                            type="button" 
                            wire:click="openDetail({{ $sup->id }})" 
                            class="btn-glass-action"
                        >
                            <span>Detail</span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="openEditModal({{ $sup->id }})" 
                            class="btn-glass-action"
                        >
                            <span>Edit</span>
                        </button>
                        <button 
                            type="button" 
                            wire:click="confirmDelete({{ $sup->id }})" 
                            class="btn-glass-action"
                        >
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-semibold text-xs">
                    Belum ada data supplier ditemukan.
                </div>
            @endforelse
        </div>

        @if($suppliers->hasPages())
            <div class="p-3 border-t border-white/40">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL FORM TAMBAH / EDIT SUPPLIER -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[90vh] relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">{{ $editingId ? 'Edit Data Supplier' : 'Tambah Supplier Baru' }}</h3>
                            <span class="text-xs text-slate-400 font-semibold block">Kelola mitra distributor & kulakan produk</span>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="$set('showFormModal', false)" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveSupplier" class="p-5 sm:p-6 space-y-4 overflow-y-auto">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Nama Supplier -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Nama Supplier / PT / Distributor *</label>
                            <input 
                                wire:model="name" 
                                type="text" 
                                placeholder="Contoh: PT Sumber Pangan / CV Fiesta Jaya" 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-bold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                            >
                            @error('name') <span class="text-rose-600 text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Kode Supplier -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Kode Supplier</label>
                            <input 
                                wire:model="code" 
                                type="text" 
                                placeholder="Contoh: SUP-001" 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-mono font-bold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                            >
                            @error('code') <span class="text-rose-600 text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nama PIC / Sales -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Nama PIC / Sales</label>
                            <input 
                                wire:model="contact_person" 
                                type="text" 
                                placeholder="Contoh: Pak Budi" 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-bold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                            >
                            @error('contact_person') <span class="text-rose-600 text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- No Telepon / WA -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">No. Telepon / WhatsApp</label>
                            <input 
                                wire:model="phone" 
                                type="text" 
                                placeholder="Contoh: 081234567890" 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-mono font-bold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                            >
                            @error('phone') <span class="text-rose-600 text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Email</label>
                            <input 
                                wire:model="email" 
                                type="email" 
                                placeholder="Contoh: order@supplier.com" 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-bold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                            >
                            @error('email') <span class="text-rose-600 text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Termin Bayar Default -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Ketentuan Pembayaran Default</label>
                            <select 
                                wire:model="payment_term" 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-bold text-[#163144] outline-none focus:border-[#163144] focus:bg-white cursor-pointer transition shadow-2xs"
                            >
                                <option value="cash">Tunai Langsung (Cash / Transfer)</option>
                                <option value="tempo_7">Tempo 7 Hari (Hutang 1 Minggu)</option>
                                <option value="tempo_14">Tempo 14 Hari (Hutang 2 Minggu)</option>
                                <option value="tempo_30">Tempo 30 Hari (Hutang 1 Bulan)</option>
                                <option value="custom">Kustom / Kesepakatan Lain</option>
                            </select>
                            @error('payment_term') <span class="text-rose-600 text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Alamat Gudang / Kantor -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Alamat Kantor / Gudang</label>
                            <textarea 
                                wire:model="address" 
                                rows="2" 
                                placeholder="Alamat lengkap supplier..." 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-semibold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                            ></textarea>
                            @error('address') <span class="text-rose-600 text-[11px] mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Catatan -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                            <input 
                                wire:model="notes" 
                                type="text" 
                                placeholder="Contoh: Minimal order 10 karton free ongkir..." 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 text-xs font-semibold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                            >
                        </div>

                        <!-- Status Aktif Checkbox -->
                        <div class="sm:col-span-2 flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50/70 border border-slate-200/70 shadow-2xs">
                            <input 
                                wire:model="is_active" 
                                type="checkbox" 
                                id="supplier_active_check" 
                                class="w-4 h-4 rounded text-[#163144] focus:ring-[#163144] border-slate-300 cursor-pointer"
                            >
                            <label for="supplier_active_check" class="text-xs font-bold text-[#163144] cursor-pointer">
                                Supplier Aktif (dapat dipilih pada form pembelian & restock)
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button 
                            type="button" 
                            wire:click="$set('showFormModal', false)" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 cursor-pointer transition active:scale-95 hover:scale-105"
                        >
                            Simpan Supplier
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

    <!-- MODAL DETAIL SUPPLIER -->
    @if($showDetailModal && $selectedSupplier)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-2xl w-full border border-white/90 shadow-2xl flex flex-col max-h-[90vh] relative my-auto modal-content-animate overflow-hidden">
                
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Profil Supplier</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-slate-500 font-bold">{{ $selectedSupplier->name }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono text-[10px] font-black">{{ $selectedSupplier->code ?: '-' }}</span>
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

                <div class="p-6 space-y-4 overflow-y-auto">
                    
                    <!-- Informasi Kontak Card -->
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-2 text-xs font-semibold">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Kontak PIC / Sales</span>
                                <span class="font-black text-[#163144]">{{ $selectedSupplier->contact_person ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Nomor Telepon / WA</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono font-black text-[#163144]">{{ $selectedSupplier->phone ?: '-' }}</span>
                                    @if($selectedSupplier->whatsapp_number)
                                        <a 
                                            href="https://wa.me/{{ $selectedSupplier->whatsapp_number }}" 
                                            target="_blank" 
                                            class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] hover:bg-emerald-700 transition inline-flex items-center gap-1 shadow-2xs"
                                        >
                                            <span>Chat WA</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Email</span>
                                <span class="text-[#163144]">{{ $selectedSupplier->email ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Termin Pembayaran</span>
                                <span class="font-black text-[#163144] uppercase">{{ $selectedSupplier->payment_term }}</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Alamat Gudang</span>
                                <span class="text-[#163144]">{{ $selectedSupplier->address ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab / List Produk yang Disuplai -->
                    <div class="space-y-2">
                        <h4 class="font-black text-xs text-[#163144] uppercase tracking-wider">Daftar Produk yang Disuplai ({{ $selectedSupplier->products->count() }})</h4>
                        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden max-h-48 overflow-y-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-500 font-black uppercase text-[9px] border-b border-slate-100">
                                    <tr>
                                        <th class="p-2.5 pl-4">Produk</th>
                                        <th class="p-2.5 text-right">Harga Modal (HPP)</th>
                                        <th class="p-2.5 text-right">Harga Jual</th>
                                        <th class="p-2.5 pr-4 text-center">Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-semibold">
                                    @forelse($selectedSupplier->products as $prod)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="p-2.5 pl-4 font-bold text-[#163144]">{{ $prod->name }}</td>
                                            <td class="p-2.5 text-right font-mono text-slate-600">Rp {{ number_format($prod->cost_price, 0, ',', '.') }}</td>
                                            <td class="p-2.5 text-right font-mono font-black text-[#163144]">Rp {{ number_format($prod->selling_price, 0, ',', '.') }}</td>
                                            <td class="p-2.5 pr-4 text-center font-mono font-black text-[#163144]">{{ $prod->stock }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-slate-400 text-xs font-semibold">
                                                Belum ada produk yang dikaitkan dengan supplier ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Riwayat Faktur Pembelian Terakhir -->
                    <div class="space-y-2">
                        <h4 class="font-black text-xs text-[#163144] uppercase tracking-wider">Riwayat Faktur Pembelian Terbaru</h4>
                        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden max-h-48 overflow-y-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-500 font-black uppercase text-[9px] border-b border-slate-100">
                                    <tr>
                                        <th class="p-2.5 pl-4">No. PO & Tanggal</th>
                                        <th class="p-2.5 text-right">Total Belanja</th>
                                        <th class="p-2.5 text-center">Metode</th>
                                        <th class="p-2.5 pr-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-semibold">
                                    @forelse($selectedSupplier->purchases as $po)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="p-2.5 pl-4">
                                                <span class="font-black font-mono text-[#163144] block">{{ $po->invoice_number }}</span>
                                                <span class="text-[10px] text-slate-400 font-mono">{{ $po->purchase_date->format('d/m/Y') }}</span>
                                            </td>
                                            <td class="p-2.5 text-right font-mono font-black text-[#163144]">
                                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="p-2.5 text-center uppercase text-[10px] font-bold text-slate-600">
                                                {{ $po->payment_type }}
                                            </td>
                                            <td class="p-2.5 pr-4 text-center">
                                                @if($po->payment_status === 'paid')
                                                    <span class="px-2 py-0.2 rounded-full glass-pill-plek text-emerald-700 font-extrabold text-[9px]">Lunas</span>
                                                @else
                                                    <span class="px-2 py-0.2 rounded-full glass-pill-plek text-amber-700 font-extrabold text-[9px]">Tempo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-slate-400 text-xs font-semibold">
                                                Belum ada transaksi pembelian dengan supplier ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="p-4 bg-slate-50/60 border-t border-slate-100 flex justify-end">
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

    <!-- MODAL HAPUS SUPPLIER -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm w-full border border-white/90 p-6 sm:p-7 space-y-4 shadow-2xl text-center relative my-auto modal-content-animate">
                <div class="w-14 h-14 rounded-2xl bg-rose-500/15 text-rose-700 flex items-center justify-center mx-auto border border-rose-200/60 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <div>
                    <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">Hapus Supplier?</h3>
                    <p class="text-xs text-slate-600 mt-1 font-semibold leading-relaxed">
                        Apakah Anda yakin ingin menghapus supplier <strong class="text-[#163144]">"{{ $deletingSupplierName }}"</strong>?
                    </p>
                </div>

                @if($deletingSupplierPurchaseCount > 0)
                    <div class="p-3.5 bg-rose-50/70 rounded-2xl border border-rose-200/70 text-rose-900 text-xs font-semibold text-left shadow-2xs">
                        Supplier ini memiliki {{ $deletingSupplierPurchaseCount }} faktur pembelian tercatat. Supplier tidak dapat dihapus demi menjaga integritas data riwayat keuangan.
                    </div>
                @endif

                <div class="flex items-center justify-center gap-2.5 pt-2">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteModal', false)" 
                        class="flex-1 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Batal
                    </button>
                    @if($deletingSupplierPurchaseCount === 0)
                        <button 
                            type="button" 
                            wire:click="deleteSupplier" 
                            class="flex-1 py-2.5 rounded-full bg-rose-600/90 hover:bg-rose-700 text-white font-black text-xs shadow-md shadow-rose-950/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                        >
                            Hapus Supplier
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
