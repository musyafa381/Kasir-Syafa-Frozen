<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Pelanggan Setia & Member</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Kelola daftar pembeli setia yang berhak mendapatkan harga khusus loyalty.</p>
        </div>
        <div>
            <button 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Daftarkan Pelanggan Baru</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        <!-- 1. Total Member -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Database</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Member</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    User
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($totalCustomers, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Terdaftar:</span>
                    <span class="font-bold text-[#163144]">Pelanggan Setia</span>
                </p>
            </div>
        </div>

        <!-- 2. Member Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Loyalty</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Member Aktif</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Aktif
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($totalActive, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Status:</span>
                    <span class="font-bold text-[#163144]">Dapat Diskon</span>
                </p>
            </div>
        </div>

        <!-- 3. Total Transaksi Member -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Aktivitas</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Belanja Member</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Trx
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($totalMemberOrders, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">kali</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Frekuensi:</span>
                    <span class="font-bold text-[#163144]">Kunjungan Kasir</span>
                </p>
            </div>
        </div>

        <!-- 4. Total Omset Member -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kontribusi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Omset Member</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    IDR
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalMemberSales, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Akumulasi:</span>
                    <span class="font-bold text-[#163144]">Total Belanja</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="glass-card-plek rounded-[24px] p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative w-full sm:max-w-md">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari nama, nomor WhatsApp, atau kode member..." 
                class="w-full pl-9 pr-10 py-2.5 rounded-2xl glass-input-plek text-xs font-bold text-[#163144] placeholder-slate-400 outline-none"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            @if($search)
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
            @endif
        </div>
        <div class="text-xs font-bold text-slate-500 self-end sm:self-center">
            Menampilkan <span class="text-[#163144] font-black">{{ $customers->total() }}</span> pelanggan
        </div>
    </div>

    <!-- Table & Mobile Card View -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Nama Pelanggan</th>
                        <th class="p-3.5 whitespace-nowrap">Kode Member</th>
                        <th class="p-3.5 whitespace-nowrap">No. WhatsApp / HP</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Total Belanja</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Status Loyalty</th>
                        <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-white/40 transition">
                            <!-- Nama & Alamat -->
                            <td class="p-3.5 pl-5">
                                <span class="font-black text-[#163144] text-sm block">
                                    {{ $customer->name }}
                                </span>
                                @if($customer->address)
                                    <span class="text-[11px] text-slate-500 block truncate max-w-xs font-medium">
                                        {{ $customer->address }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kode Member -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-xl glass-pill-plek text-[#163144]">
                                    {{ $customer->member_code }}
                                </span>
                            </td>

                            <!-- Telepon -->
                            <td class="p-3.5 font-mono text-slate-600 font-bold whitespace-nowrap">
                                {{ $customer->phone ?: '-' }}
                            </td>

                            <!-- Total Belanja -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-black text-[#163144] block text-xs">
                                    Rp {{ number_format($customer->transactions_sum_grand_total ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold font-mono">
                                    {{ $customer->transactions_count }}x transaksi
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <button 
                                    wire:click="toggleStatus({{ $customer->id }})" 
                                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase transition cursor-pointer glass-pill-plek text-[#163144] hover:bg-white/80"
                                >
                                    {{ $customer->is_active ? '✓ Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            <!-- Aksi Side by Side -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        wire:click="openEditModal({{ $customer->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $customer->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 font-bold">
                                Belum ada data pelanggan setia sesuai pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($customers as $customer)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="font-black text-[#163144] text-sm block truncate">{{ $customer->name }}</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono font-bold text-[10px] px-2 py-0.5 rounded-lg glass-pill-plek text-[#163144]">
                                    {{ $customer->member_code }}
                                </span>
                                @if($customer->phone)
                                    <span class="text-[11px] text-slate-600 font-mono font-bold">{{ $customer->phone }}</span>
                                @endif
                            </div>
                        </div>
                        <button 
                            wire:click="toggleStatus({{ $customer->id }})" 
                            class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase transition cursor-pointer glass-pill-plek text-[#163144]"
                        >
                            {{ $customer->is_active ? '✓ Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    @if($customer->address)
                        <p class="text-[11px] text-slate-500 font-medium truncate">{{ $customer->address }}</p>
                    @endif

                    <div class="flex items-center justify-between pt-2 border-t border-white/40">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block">Total Belanja</span>
                            <span class="font-black text-[#163144] text-xs font-mono">
                                Rp {{ number_format($customer->transactions_sum_grand_total ?? 0, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-400 font-normal">({{ $customer->transactions_count }}x)</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button 
                                wire:click="openEditModal({{ $customer->id }})" 
                                class="btn-glass-action"
                            >
                                Edit
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $customer->id }})" 
                                class="btn-glass-action"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-bold text-xs">
                    Belum ada data pelanggan setia sesuai pencarian.
                </div>
            @endforelse
        </div>

        {{ $customers->links() }}
    </div>

    <!-- Modal Form Tambah / Edit Pelanggan -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">{{ $editingCustomerId ? 'Edit Pelanggan Setia' : 'Daftarkan Pelanggan Baru' }}</h3>
                            <span class="text-xs text-slate-400 font-semibold block">Lengkapi identitas untuk akses Harga Loyalty</span>
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

                <form wire:submit="saveCustomer" class="p-5 sm:p-6 space-y-4">
                    
                    <!-- Nama Pelanggan -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input 
                            wire:model="name" 
                            type="text" 
                            placeholder="Contoh: Ibu Hajjah Maryam / Kedai Berkah" 
                            required
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-200/80 focus:border-[#163144] focus:bg-white' }} text-xs font-bold text-[#163144] outline-none transition shadow-2xs"
                        >
                        @error('name') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kode Member & No WhatsApp -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-black text-[#163144] uppercase tracking-wider">Kode Member</label>
                                <button type="button" wire:click="generateCode" class="text-[10px] font-extrabold text-[#386797] hover:underline cursor-pointer">Otomatis</button>
                            </div>
                            <input 
                                wire:model="member_code" 
                                type="text" 
                                required
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 focus:border-[#163144] focus:bg-white text-xs font-mono font-bold text-[#163144] outline-none transition shadow-2xs"
                            >
                            @error('member_code') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                            <input 
                                wire:model="phone" 
                                type="text" 
                                placeholder="081234567890" 
                                class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 focus:border-[#163144] focus:bg-white text-xs font-bold text-[#163144] outline-none transition shadow-2xs"
                            >
                            @error('phone') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Alamat (Opsional)</label>
                        <textarea 
                            wire:model="address" 
                            rows="2" 
                            placeholder="Alamat domisili atau lokasi usaha..." 
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 focus:border-[#163144] focus:bg-white text-xs font-semibold text-[#163144] outline-none transition shadow-2xs"
                        ></textarea>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Catatan Khusus (Opsional)</label>
                        <input 
                            wire:model="notes" 
                            type="text" 
                            placeholder="Contoh: Reseller langganan / pemilik resto..." 
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50/80 border border-slate-200/80 focus:border-[#163144] focus:bg-white text-xs font-semibold text-[#163144] outline-none transition shadow-2xs"
                        >
                    </div>

                    <!-- Status Aktif -->
                    <div class="pt-1">
                        <label class="flex items-center gap-2.5 text-xs font-bold text-[#163144] cursor-pointer p-3 rounded-2xl bg-slate-50/70 border border-slate-200/70 shadow-2xs">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="w-4 h-4 rounded border-slate-300 text-[#163144] focus:ring-[#163144] cursor-pointer"
                            >
                            <span>Member aktif & berhak atas Harga Loyalty</span>
                        </label>
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
                            <span wire:loading.remove>{{ $editingCustomerId ? 'Simpan Perubahan' : 'Daftarkan Member' }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4 my-auto confirm-card">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-[#e1144b] border border-rose-300/40 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Pelanggan</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data member <strong class="text-[#163144]">"{{ $deletingCustomerName }}"</strong>?
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
                        wire:click="deleteCustomer" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
