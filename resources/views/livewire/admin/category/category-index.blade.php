<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Kategori Produk</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Kelola pengelompokan produk frozen food toko Anda.</p>
        </div>
        <div>
            <button 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Tambah Kategori Baru</span>
            </button>
        </div>
    </div>

    <!-- Search Box -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4">
        <div class="relative max-w-md">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari nama kategori atau deskripsi..." 
                class="w-full pl-3.5 pr-10 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
            >
            @if($search)
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#163144] text-xs cursor-pointer">✕</button>
            @endif
        </div>
    </div>

    <!-- Table & Mobile Card View of Categories -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[720px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Nama Kategori</th>
                        <th class="p-3.5">Deskripsi</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Jumlah Produk</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($categories as $category)
                        <tr class="hover:bg-white/40 transition">
                            <td class="p-3.5 pl-5 font-black text-[#163144] text-sm whitespace-nowrap">
                                {{ $category->name }}
                            </td>
                            <td class="p-3.5 text-slate-500 font-semibold max-w-xs truncate">
                                {{ $category->description ?: '-' }}
                            </td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <a 
                                    href="{{ route('admin.categories.show', $category->slug) }}" 
                                    wire:navigate 
                                    class="inline-block px-3 py-1 rounded-full font-black text-xs glass-pill-plek text-[#163144] hover:bg-white transition"
                                    title="Klik untuk melihat daftar produk"
                                >
                                    {{ $category->products_count }} Produk
                                </a>
                            </td>
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a 
                                        href="{{ route('admin.categories.show', $category->slug) }}" 
                                        wire:navigate 
                                        class="btn-glass-action"
                                    >
                                        Detail Produk
                                    </a>
                                    <button 
                                        wire:click="openEditModal({{ $category->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $category->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-slate-400 font-semibold">
                                Belum ada data kategori ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($categories as $category)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-[#163144] text-sm">{{ $category->name }}</h3>
                            @if($category->description)
                                <p class="text-[11px] text-slate-500 font-semibold line-clamp-2 mt-0.5">{{ $category->description }}</p>
                            @endif
                        </div>
                        <a 
                            href="{{ route('admin.categories.show', $category->slug) }}" 
                            wire:navigate 
                            class="shrink-0 px-2.5 py-1 rounded-full font-black text-xs glass-pill-plek text-[#163144]"
                        >
                            {{ $category->products_count }} Produk
                        </a>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-white/40">
                        <a 
                            href="{{ route('admin.categories.show', $category->slug) }}" 
                            wire:navigate 
                            class="btn-glass-action"
                        >
                            Lihat Produk
                        </a>
                        <button 
                            wire:click="openEditModal({{ $category->id }})" 
                            class="btn-glass-action"
                        >
                            Edit
                        </button>
                        <button 
                            wire:click="confirmDelete({{ $category->id }})" 
                            class="btn-glass-action"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                    Belum ada data kategori ditemukan.
                </div>
            @endforelse
        </div>

        {{ $categories->links() }}
    </div>

    <!-- Modal Form Tambah / Edit Kategori -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden shadow-2xl relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center shrink-0 border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base sm:text-lg tracking-tight">{{ $editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
                            <span class="text-xs text-slate-400 font-semibold block">Kelompok & klasifikasi produk toko</span>
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

                <form wire:submit="saveCategory" class="p-5 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Nama Kategori</label>
                        <input 
                            wire:model="name" 
                            type="text" 
                            placeholder="Contoh: Sosis & Nugget" 
                            required
                            class="w-full px-4 py-2.5 bg-slate-50/80 border {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-200/80 focus:border-[#163144] focus:bg-white' }} rounded-2xl text-xs font-bold text-[#163144] outline-none transition shadow-2xs"
                        >
                        @error('name')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Deskripsi (Opsional)</label>
                        <textarea 
                            wire:model="description" 
                            rows="3" 
                            placeholder="Keterangan singkat kategori..." 
                            class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200/80 rounded-2xl text-xs font-semibold text-[#163144] outline-none focus:border-[#163144] focus:bg-white transition shadow-2xs"
                        ></textarea>
                    </div>

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
                            <span wire:loading.remove>{{ $editingCategoryId ? 'Simpan Perubahan' : 'Tambah Kategori' }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus dengan 2 Pilihan Aksi -->
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
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Kategori</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            @if($deletingCategoryProductCount > 0)
                                Kategori <strong class="text-[#163144]">"{{ $deletingCategoryName }}"</strong> memiliki <strong class="text-[#163144]">{{ $deletingCategoryProductCount }} produk</strong> terkait. Pilih tindakan yang diinginkan:
                            @else
                                Apakah Anda yakin ingin menghapus kategori <strong class="text-[#163144]">"{{ $deletingCategoryName }}"</strong>?
                            @endif
                        </p>
                    </div>
                </div>

                @if($deletingCategoryProductCount > 0)
                    <!-- Pilihan Aksi Penghapusan -->
                    <div class="space-y-2 text-left pt-1">
                        <!-- Opsi 1: Lepaskan Kategori (Default / Aman) -->
                        <label class="flex items-start gap-3 p-3.5 rounded-2xl border transition cursor-pointer shadow-2xs {{ $deleteMode === 'detach' ? 'bg-[#a0bddb]/15 border-[#a0bddb]/70 ring-1 ring-[#a0bddb]/40' : 'bg-slate-50/80 border-slate-200/80 hover:bg-slate-100/80' }}">
                            <input 
                                type="radio" 
                                wire:model.live="deleteMode" 
                                value="detach" 
                                class="mt-0.5 text-[#163144] focus:ring-[#163144] cursor-pointer"
                            >
                            <div>
                                <span class="font-black text-xs text-[#163144] block">Hapus Kategorinya Saja (Aman)</span>
                                <span class="text-[11px] text-slate-500 font-semibold leading-relaxed block mt-0.5">
                                    {{ $deletingCategoryProductCount }} produk tetap ada dan otomatis diubah menjadi <strong>"Tanpa Kategori"</strong>.
                                </span>
                            </div>
                        </label>

                        <!-- Opsi 2: Hapus Kategori & Semua Produknya -->
                        <label class="flex items-start gap-3 p-3.5 rounded-2xl border transition cursor-pointer shadow-2xs {{ $deleteMode === 'delete_products' ? 'bg-rose-50/70 border-rose-300 ring-1 ring-rose-400/40' : 'bg-slate-50/80 border-slate-200/80 hover:bg-slate-100/80' }}">
                            <input 
                                type="radio" 
                                wire:model.live="deleteMode" 
                                value="delete_products" 
                                class="mt-0.5 text-rose-600 focus:ring-rose-500 cursor-pointer"
                            >
                            <div>
                                <span class="font-black text-xs text-rose-700 block">Hapus Kategori & {{ $deletingCategoryProductCount }} Produk di Dalamnya</span>
                                <span class="text-[11px] text-slate-500 font-semibold leading-relaxed block mt-0.5">
                                    Kategori dan seluruh produk di dalamnya akan <strong>dihapus total</strong> dari katalog.
                                </span>
                            </div>
                        </label>
                    </div>
                @endif

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
                        wire:click="deleteCategory" 
                        class="px-5 py-2 rounded-full {{ $deleteMode === 'delete_products' && $deletingCategoryProductCount > 0 ? 'bg-[#e1144b] hover:bg-[#c90f40] text-white shadow-[#e1144b]/20' : 'bg-[#163144] hover:bg-[#20445e] text-white shadow-[#163144]/20' }} font-black text-xs cursor-pointer transition active:scale-95 shadow-md"
                    >
                        {{ $deleteMode === 'delete_products' && $deletingCategoryProductCount > 0 ? 'Hapus Semua' : 'Hapus Kategori' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
