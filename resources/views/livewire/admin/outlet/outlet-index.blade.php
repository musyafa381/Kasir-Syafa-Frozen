<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Kelola Cabang Toko</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Daftar cabang toko, penugasan staf kasir, kustomisasi struk, dan pemisahan stok/harga antar cabang.</p>
        </div>
        <div>
            <button 
                type="button" 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Tambah Cabang Baru</span>
            </button>
        </div>
    </div>

    <!-- 3 Stat KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
        
        <!-- Total Cabang -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Cabang</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Cabang</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Multi
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ $totalOutlets }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Cabang</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Jaringan toko terdaftar</p>
            </div>
        </div>

        <!-- Cabang Aktif Beroperasi -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Status</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Cabang Aktif</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Buka
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-emerald-800 font-mono tracking-tight block">
                    {{ $activeOutlets }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Outlet</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Melayani kasir aktif</p>
            </div>
        </div>

        <!-- Katalog Produk -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#386797] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Katalog</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Katalog Produk</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Katalog
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ $totalProductsCount }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Item</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Stok & harga terpisah per cabang</p>
            </div>
        </div>

    </div>

    <!-- Search & Filter Card -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative max-w-md w-full">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari nama cabang, kode, atau alamat..." 
                    class="w-full pl-3.5 pr-10 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <select 
                    wire:model.live="filterStatus" 
                    class="px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                >
                    <option value="all">Semua Status Cabang</option>
                    <option value="active">Cabang Aktif</option>
                    <option value="inactive">Cabang Non-Aktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table of Outlets -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Kode</th>
                        <th class="p-3.5 whitespace-nowrap">Nama Cabang & Alamat</th>
                        <th class="p-3.5 whitespace-nowrap">No. HP / Kontak</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Kasir Bertugas</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Total Transaksi</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Status</th>
                        <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($outlets as $outlet)
                        <tr class="hover:bg-white/40 transition">
                            
                            <!-- Kode Cabang -->
                            <td class="p-3.5 pl-5 whitespace-nowrap">
                                <span class="font-mono font-black text-xs glass-pill-plek text-[#163144] px-2.5 py-0.5 rounded-full">
                                    {{ $outlet->code }}
                                </span>
                            </td>

                            <!-- Nama Cabang & Alamat -->
                            <td class="p-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-[#163144] text-sm">{{ $outlet->name }}</span>
                                    @if($outlet->is_main)
                                        <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#386797] font-black text-[10px] uppercase">
                                            Pusat
                                        </span>
                                    @endif
                                </div>
                                @if($outlet->address)
                                    <p class="text-[11px] text-slate-500 font-medium mt-0.5 line-clamp-1 max-w-sm">{{ $outlet->address }}</p>
                                @endif
                            </td>

                            <!-- Telepon -->
                            <td class="p-3.5 font-mono text-slate-600 whitespace-nowrap">
                                {{ $outlet->phone ?: '-' }}
                            </td>

                            <!-- Kasir Bertugas -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full glass-pill-plek font-black text-[#163144] font-mono text-[11px]">
                                    {{ $outlet->users_count }} Orang
                                </span>
                            </td>

                            <!-- Total Transaksi -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-mono font-black text-[#163144] block">
                                    {{ number_format($outlet->transactions_count, 0, ',', '.') }} Struk
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <button 
                                    type="button" 
                                    wire:click="toggleStatus({{ $outlet->id }})" 
                                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase transition cursor-pointer glass-pill-plek {{ $outlet->is_active ? 'text-emerald-800' : 'text-slate-400' }}"
                                >
                                    {{ $outlet->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            <!-- Aksi -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    @if(!$outlet->is_main)
                                        <button 
                                            type="button" 
                                            wire:click="setAsMain({{ $outlet->id }})" 
                                            class="btn-glass-action"
                                            title="Jadikan Cabang Utama / Toko Pusat"
                                        >
                                            Set Pusat
                                        </button>
                                    @endif

                                    <button 
                                        type="button" 
                                        wire:click="openEditModal({{ $outlet->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Edit
                                    </button>

                                    @if(!$outlet->is_main)
                                        <button 
                                            type="button" 
                                            wire:click="confirmDelete({{ $outlet->id }})" 
                                            class="btn-glass-action"
                                        >
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400 font-semibold">
                                Tidak ada data cabang toko yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($outlets as $outlet)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="font-mono font-black text-[#163144] glass-pill-plek px-2 py-0.5 rounded-full text-[10px]">
                                    {{ $outlet->code }}
                                </span>
                                @if($outlet->is_main)
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#386797] font-black text-[10px] uppercase">
                                        Pusat
                                    </span>
                                @endif
                                <button 
                                    type="button" 
                                    wire:click="toggleStatus({{ $outlet->id }})" 
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase transition cursor-pointer glass-pill-plek {{ $outlet->is_active ? 'text-emerald-800' : 'text-slate-400' }}"
                                >
                                    {{ $outlet->is_active ? '✓ Aktif' : 'Nonaktif' }}
                                </button>
                            </div>
                            <h4 class="font-black text-[#163144] text-sm leading-snug">{{ $outlet->name }}</h4>
                            @if($outlet->address)
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5 line-clamp-1">{{ $outlet->address }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Transaksi</span>
                            <span class="font-mono font-black text-[#163144] text-xs block">
                                {{ number_format($outlet->transactions_count, 0, ',', '.') }} Struk
                            </span>
                            <span class="text-[10px] text-slate-500 font-mono block mt-0.5">
                                Kasir: {{ $outlet->users_count }} Org
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-white/40">
                        <span class="text-[11px] text-slate-500 font-mono font-semibold">
                            {{ $outlet->phone ?: 'No HP: -' }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            @if(!$outlet->is_main)
                                <button 
                                    type="button" 
                                    wire:click="setAsMain({{ $outlet->id }})" 
                                    class="btn-glass-action"
                                >
                                    Set Pusat
                                </button>
                            @endif
                            <button 
                                type="button" 
                                wire:click="openEditModal({{ $outlet->id }})" 
                                class="btn-glass-action"
                            >
                                Edit
                            </button>
                            @if(!$outlet->is_main)
                                <button 
                                    type="button" 
                                    wire:click="confirmDelete({{ $outlet->id }})" 
                                    class="btn-glass-action"
                                >
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-semibold">
                    Tidak ada data cabang toko
                </div>
            @endforelse
        </div>

        {{ $outlets->links() }}
    </div>

    <!-- Modal Form Tambah / Edit Cabang -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[92vh] relative my-auto modal-content-animate">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">{{ $editingOutletId ? 'Edit Data Cabang Toko' : 'Tambah Cabang Toko Baru' }}</h3>
                            <span class="text-xs text-slate-400 font-semibold block">Informasi cabang, kontak, dan kustomisasi struk kasir</span>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="$set('showModal', false)" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit="save" class="p-5 sm:p-6 space-y-4 overflow-y-auto">
                    
                    <!-- Kode Cabang & Nama Cabang -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Kode Cabang</label>
                            <input 
                                type="text" 
                                wire:model="code" 
                                placeholder="CBG-02"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50/80 border {{ $errors->has('code') ? 'border-rose-400' : 'border-slate-200/80 focus:border-[#163144] focus:bg-white' }} rounded-2xl text-xs font-mono font-black text-[#163144] uppercase outline-none transition shadow-2xs"
                            >
                            @error('code') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Nama Cabang / Toko</label>
                            <input 
                                type="text" 
                                wire:model="name" 
                                placeholder="Contoh: Syafa Frozen - Cabang 2" 
                                required
                                class="w-full px-4 py-2.5 bg-slate-50/80 border {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-200/80 focus:border-[#163144] focus:bg-white' }} rounded-2xl text-xs font-bold text-[#163144] outline-none transition shadow-2xs"
                            >
                            @error('name') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Nomor Telepon & Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">No. WhatsApp / Telepon</label>
                            <input 
                                type="text" 
                                wire:model="phone" 
                                placeholder="08123456789" 
                                class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/80 rounded-2xl text-xs font-bold text-[#163144] focus:border-[#163144] focus:bg-white outline-none transition shadow-2xs"
                            >
                            @error('phone') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col justify-center space-y-2 pt-2">
                            <label class="flex items-center gap-2.5 text-xs font-bold text-[#163144] cursor-pointer p-2.5 rounded-xl bg-slate-50/70 border border-slate-200/70 shadow-2xs">
                                <input 
                                    type="checkbox" 
                                    wire:model="is_main" 
                                    class="w-4 h-4 rounded text-[#163144] focus:ring-[#163144] cursor-pointer"
                                >
                                <span>Jadikan Cabang Utama (Pusat)</span>
                            </label>

                            <label class="flex items-center gap-2.5 text-xs font-bold text-[#163144] cursor-pointer p-2.5 rounded-xl bg-slate-50/70 border border-slate-200/70 shadow-2xs">
                                <input 
                                    type="checkbox" 
                                    wire:model="is_active" 
                                    class="w-4 h-4 rounded text-[#163144] focus:ring-[#163144] cursor-pointer"
                                >
                                <span>Cabang Aktif Beroperasi</span>
                            </label>
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Alamat Lengkap Cabang</label>
                        <textarea 
                            wire:model="address" 
                            rows="2" 
                            placeholder="Jl. Raya Barat No. 88, Ruko Blok A..." 
                            class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/80 rounded-2xl text-xs font-semibold text-[#163144] focus:border-[#163144] focus:bg-white outline-none transition shadow-2xs resize-none"
                        ></textarea>
                        @error('address') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kustomisasi Struk Kasir Khusus Cabang Ini -->
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-3 shadow-2xs">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Kustomisasi Struk Kasir Cabang:</span>
                        
                        <div>
                            <label class="block text-[11px] font-bold text-[#163144] mb-1">Catatan Header Struk (Opsional)</label>
                            <input 
                                type="text" 
                                wire:model="receipt_header" 
                                placeholder="Contoh: Sedia Frozen Food Murah & Lengkap" 
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-[#163144] focus:border-[#163144] outline-none shadow-2xs"
                            >
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-[#163144] mb-1">Pesan Footer Struk (Opsional)</label>
                            <input 
                                type="text" 
                                wire:model="receipt_footer" 
                                placeholder="Contoh: Terima kasih atas kunjungan Anda" 
                                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-[#163144] focus:border-[#163144] outline-none shadow-2xs"
                            >
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="$set('showModal', false)" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove>{{ $editingOutletId ? 'Simpan Perubahan' : 'Buat Cabang Toko' }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Cabang -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4 my-auto confirm-card">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-[#e1144b] border border-rose-300/40 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Cabang</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data cabang toko <strong class="text-[#163144] font-black">"{{ $deletingOutletName }}"</strong>?
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
                        wire:click="deleteOutlet" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
