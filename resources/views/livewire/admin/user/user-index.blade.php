<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Kelola Kasir & Pengguna</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Kelola akun staf kasir dan administrator sistem kasir.</p>
        </div>
        <div class="w-full sm:w-auto">
            <button 
                wire:click="openCreateModal" 
                class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Tambah Kasir / Admin Baru</span>
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4">
        <div class="relative max-w-md">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari nama, email, atau nomor HP pengguna..." 
                class="w-full pl-3.5 pr-10 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
            >
            @if($search)
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
            @endif
        </div>
    </div>

    <!-- Table of Users -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        
        <!-- Desktop Table (>= sm) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Nama & Email</th>
                        <th class="p-3.5 whitespace-nowrap">Role Akses</th>
                        <th class="p-3.5 whitespace-nowrap">Cabang Penugasan</th>
                        <th class="p-3.5 whitespace-nowrap">No. HP</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Total Transaksi Kasir</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Status</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($users as $u)
                        <tr class="hover:bg-white/40 transition">
                            <!-- Nama & Email -->
                            <td class="p-3.5 pl-5">
                                <span class="font-extrabold text-[#163144] text-sm block">
                                    {{ $u->name }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono block">
                                    {{ $u->email }}
                                </span>
                            </td>

                            <!-- Role -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-mono font-black text-xs uppercase px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144]">
                                    {{ $u->role }}
                                </span>
                            </td>

                            <!-- Cabang Penugasan -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if($u->outlet)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#163144]"></span>
                                        {{ $u->outlet->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">
                                        {{ $u->role === 'admin' ? 'Semua Cabang' : 'Belum Ditugaskan' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Telepon -->
                            <td class="p-3.5 font-mono text-slate-600 whitespace-nowrap">
                                {{ $u->phone ?: '-' }}
                            </td>

                            <!-- Total Transaksi & Omset Kasir -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-mono font-black text-[#163144] block">
                                    {{ number_format($u->transactions_count, 0, ',', '.') }} Struk
                                </span>
                                <span class="text-[10px] text-[#386797] font-mono font-bold block">
                                    Rp {{ number_format($u->total_sales_handled ?? 0, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <button 
                                    wire:click="toggleStatus({{ $u->id }})" 
                                    @if(auth()->id() === $u->id) disabled @endif
                                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase transition cursor-pointer glass-pill-plek
                                    {{ $u->is_active ? 'text-emerald-800' : 'text-slate-400' }}
                                    {{ auth()->id() === $u->id ? 'opacity-60 cursor-not-allowed' : '' }}"
                                >
                                    {{ $u->is_active ? '✓ Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            <!-- Aksi -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        wire:click="openEditModal({{ $u->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Edit
                                    </button>
                                    @if(auth()->id() !== $u->id)
                                        <button 
                                            wire:click="confirmDelete({{ $u->id }})" 
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
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List (< sm) -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($users as $u)
                <div class="p-3.5 space-y-3">
                    <!-- Top row: Name & Role Badge -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-extrabold text-[#163144] text-sm block">{{ $u->name }}</span>
                            <span class="text-[11px] text-slate-400 font-mono block">{{ $u->email }}</span>
                        </div>
                        <span class="font-mono font-black text-[10px] uppercase px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] shrink-0">
                            {{ $u->role }}
                        </span>
                    </div>

                    <!-- Outlet & Phone Info -->
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if($u->outlet)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-[10px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#163144]"></span>
                                {{ $u->outlet->name }}
                            </span>
                        @else
                            <span class="text-slate-400 text-[11px] italic">
                                {{ $u->role === 'admin' ? 'Semua Cabang' : 'Belum Ditugaskan' }}
                            </span>
                        @endif

                        @if($u->phone)
                            <span class="text-slate-600 font-mono text-[11px] glass-pill-plek px-2.5 py-0.5 rounded-full font-bold">
                                📞 {{ $u->phone }}
                            </span>
                        @endif
                    </div>

                    <!-- Performance Box: Transactions & Omset -->
                    <div class="flex items-center justify-between text-xs glass-pill-plek p-2.5 rounded-2xl border border-white/60">
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Total Transaksi</span>
                            <span class="font-mono font-black text-[#163144] text-xs">{{ number_format($u->transactions_count, 0, ',', '.') }} Struk</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Total Penjualan</span>
                            <span class="font-mono font-black text-[#163144] text-xs">Rp {{ number_format($u->total_sales_handled ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Bottom row: Status Toggle & Actions -->
                    <div class="flex items-center justify-between pt-1 border-t border-white/40">
                        <button 
                            wire:click="toggleStatus({{ $u->id }})" 
                            @if(auth()->id() === $u->id) disabled @endif
                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase transition cursor-pointer glass-pill-plek text-[#163144]
                            {{ auth()->id() === $u->id ? 'opacity-60 cursor-not-allowed' : '' }}"
                        >
                            {{ $u->is_active ? '✓ Aktif' : 'Nonaktif' }}
                        </button>

                        <div class="inline-flex items-center gap-1.5">
                            <button 
                                wire:click="openEditModal({{ $u->id }})" 
                                class="btn-glass-action"
                            >
                                Edit
                            </button>
                            @if(auth()->id() !== $u->id)
                                <button 
                                    wire:click="confirmDelete({{ $u->id }})" 
                                    class="btn-glass-action"
                                >
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 font-semibold px-4">
                    Tidak ada data pengguna.
                </div>
            @endforelse
        </div>

        {{ $users->links() }}
    </div>

    <!-- Modal Form Tambah / Edit Pengguna -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">{{ $editingUserId ? 'Edit Akun Pengguna' : 'Tambah Kasir / Admin Baru' }}</h3>
                            <span class="text-xs text-slate-400 font-semibold block">Pengaturan hak akses & penugasan cabang</span>
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

                <form wire:submit="saveUser" class="p-5 sm:p-6 space-y-4">
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input 
                            wire:model="name" 
                            type="text" 
                            placeholder="Contoh: Rina Anggraini" 
                            required
                            class="w-full px-4 py-2.5 bg-slate-50/80 border {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-200/80 focus:border-[#163144] focus:bg-white' }} rounded-2xl text-xs font-bold text-[#163144] outline-none transition shadow-2xs"
                        >
                        @error('name') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email & No HP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Email Login</label>
                            <input 
                                wire:model="email" 
                                type="email" 
                                placeholder="kasir@syafafrozen.com" 
                                required
                                class="w-full px-4 py-2.5 bg-slate-50/80 border {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-200/80 focus:border-[#163144] focus:bg-white' }} rounded-2xl text-xs font-bold text-[#163144] outline-none transition shadow-2xs"
                            >
                            @error('email') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                            <input 
                                wire:model="phone" 
                                type="text" 
                                placeholder="08123456789" 
                                class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/80 rounded-2xl text-xs font-bold text-[#163144] focus:border-[#163144] focus:bg-white outline-none transition shadow-2xs"
                            >
                            @error('phone') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Role & Cabang Penugasan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Peran / Role</label>
                            <select 
                                wire:model="role" 
                                class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/80 rounded-2xl text-xs font-bold text-[#163144] focus:border-[#163144] focus:bg-white outline-none cursor-pointer transition shadow-2xs"
                            >
                                <option value="cashier">Kasir (Hanya POS)</option>
                                <option value="admin">Administrator (Akses Penuh)</option>
                            </select>
                            @error('role') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Cabang Penugasan</label>
                            <select 
                                wire:model="outlet_id" 
                                class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/80 rounded-2xl text-xs font-bold text-[#163144] focus:border-[#163144] focus:bg-white outline-none cursor-pointer transition shadow-2xs"
                            >
                                <option value="">-- Bebas / Semua Cabang --</option>
                                @foreach($outlets as $o)
                                    <option value="{{ $o->id }}">
                                        {{ $o->name }} {{ $o->is_main ? '(Pusat)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('outlet_id') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">
                            Password {{ $editingUserId ? '(Kosongkan jika tetap)' : '' }}
                        </label>
                        <input 
                            wire:model="password" 
                            type="password" 
                            placeholder="{{ $editingUserId ? '••••••' : 'Minimal 6 karakter' }}" 
                            class="w-full px-4 py-2.5 bg-slate-50/80 border {{ $errors->has('password') ? 'border-rose-400' : 'border-slate-200/80 focus:border-[#163144] focus:bg-white' }} rounded-2xl text-xs font-bold text-[#163144] outline-none transition shadow-2xs"
                        >
                        @error('password') <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div class="pt-1">
                        <label class="flex items-center gap-2.5 text-xs font-bold text-[#163144] cursor-pointer p-3 rounded-2xl bg-slate-50/70 border border-slate-200/70 shadow-2xs">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="w-4 h-4 rounded border-slate-300 text-[#163144] focus:ring-[#163144] cursor-pointer"
                            >
                            <span>Akun aktif dan diizinkan login ke sistem</span>
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
                            <span wire:loading.remove>{{ $editingUserId ? 'Simpan Perubahan' : 'Buat Akun' }}</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Akun</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus akun pengguna <strong class="text-[#163144] font-black">"{{ $deletingUserName }}"</strong>?
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
                        wire:click="deleteUser" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
