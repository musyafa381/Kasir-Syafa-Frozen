<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Penyesuaian & Riwayat Mutasi Stok</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Catat kulakan stok masuk, barang rusak/expired, dan audit riwayat arus barang.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a 
                href="{{ route('admin.purchases.restock') }}" 
                wire:navigate 
                class="btn-glass-action font-extrabold text-xs px-3.5 py-2 rounded-2xl"
            >
                <span>Perencanaan Kulakan</span>
            </a>

            <a 
                href="{{ route('admin.print.stock-opname.sheet') }}" 
                target="_blank" 
                class="btn-glass-action inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-4 h-4 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Cetak Form Opname</span>
            </a>

            <button 
                wire:click="openAdjustmentModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Catat Penyesuaian Stok</span>
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4 grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
        <!-- Filter Produk (Searchable Dropdown) -->
        <div>
            <x-searchable-select 
                wire:model.live="filterProduct" 
                :options="$products" 
                placeholder="Semua Produk" 
                empty-option="Semua Produk" 
            />
        </div>

        <!-- Filter Tipe Mutasi -->
        <div>
            <select 
                wire:model.live="filterType" 
                class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
            >
                <option value="">Semua Jenis Mutasi</option>
                <option value="in">Stok Masuk (Kulakan / Restock)</option>
                <option value="out">Stok Keluar (Penjualan / Rusak / Expired)</option>
                <option value="adjustment">Koreksi Opname</option>
            </select>
        </div>
    </div>

    <!-- Table & Mobile Card View of Stock Mutations -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Waktu</th>
                        <th class="p-3.5 whitespace-nowrap">Produk</th>
                        <th class="p-3.5 whitespace-nowrap">Tipe Mutasi</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Sebelum</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Perubahan (Qty)</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Sisa Akhir</th>
                        <th class="p-3.5 whitespace-nowrap">Ref No</th>
                        <th class="p-3.5 pr-5 whitespace-nowrap">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($mutations as $m)
                        <tr class="hover:bg-white/40 transition">
                            <td class="p-3.5 pl-5 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-3.5">
                                <span class="font-extrabold text-[#163144] text-xs sm:text-sm block">
                                    {{ $m->product->name ?? 'Produk Dihapus' }}
                                </span>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if($m->type === 'in')
                                    <span class="px-2.5 py-0.5 rounded-full font-black text-[10px] uppercase glass-pill-plek text-emerald-800">
                                        Masuk / Kulakan
                                    </span>
                                @elseif($m->type === 'out')
                                    <span class="px-2.5 py-0.5 rounded-full font-black text-[10px] uppercase glass-pill-plek text-rose-700">
                                        Keluar
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full font-black text-[10px] uppercase glass-pill-plek text-[#163144]">
                                        Koreksi
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center text-slate-500 font-mono font-bold">
                                {{ $m->stock_before }}
                            </td>
                            <td class="p-3.5 text-center font-black font-mono">
                                <span class="{{ $m->quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center font-black text-[#163144] font-mono">
                                {{ $m->stock_after }}
                            </td>
                            <td class="p-3.5">
                                <p class="text-[#163144] font-bold text-[11px] font-mono">{{ $m->reference ?: '-' }}</p>
                            </td>
                            <td class="p-3.5 pr-5 text-slate-600 text-[11px] font-semibold">
                                {{ $m->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400 font-semibold">
                                Belum ada riwayat mutasi stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($mutations as $m)
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-extrabold text-[#163144] text-sm leading-snug">{{ $m->product->name ?? 'Produk Dihapus' }}</h3>
                            <span class="text-[10px] font-mono text-slate-400 font-semibold">{{ $m->created_at->format('d/m/Y H:i') }} • {{ $m->user->name ?? 'Sistem' }}</span>
                        </div>
                        <div>
                            @if($m->type === 'in')
                                <span class="px-2 py-0.5 rounded-full font-black text-[10px] uppercase glass-pill-plek text-emerald-800">
                                    Masuk
                                </span>
                            @elseif($m->type === 'out')
                                <span class="px-2 py-0.5 rounded-full font-black text-[10px] uppercase glass-pill-plek text-rose-700">
                                    Keluar
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full font-black text-[10px] uppercase glass-pill-plek text-[#163144]">
                                    Koreksi
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 glass-pill-plek p-2.5 rounded-2xl border border-white/60 text-center font-mono text-xs">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-black font-sans block">Awal</span>
                            <span class="text-slate-600 font-bold">{{ $m->stock_before }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-black font-sans block">Mutasi</span>
                            <span class="font-black {{ $m->quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-black font-sans block">Akhir</span>
                            <span class="font-black text-[#163144]">{{ $m->stock_after }}</span>
                        </div>
                    </div>

                    @if($m->reference || $m->description)
                        <div class="text-[11px] text-slate-500 font-semibold truncate">
                            @if($m->reference)<span class="font-bold text-[#163144] font-mono">{{ $m->reference }}</span>@endif
                            @if($m->reference && $m->description) • @endif
                            @if($m->description)<span>{{ $m->description }}</span>@endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                    Belum ada riwayat mutasi stok.
                </div>
            @endforelse
        </div>

        @if($mutations->hasPages())
            <div class="p-4 border-t border-white/60 bg-white/40">
                {{ $mutations->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah Penyesuaian Stok (Arctic Navy Glass) -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base leading-tight">Catat Penyesuaian Stok</h3>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">Tambah stok masuk, barang rusak, atau koreksi</p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="$set('showFormModal', false)" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit="saveAdjustment" class="p-6 space-y-4">
                    
                    <!-- Pilih Produk -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Pilih Produk</label>
                        <x-searchable-select 
                            wire:model="product_id" 
                            :options="$products" 
                            placeholder="— Pilih Produk —" 
                            required 
                        />
                        @error('product_id') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Jenis Mutasi -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Jenis Penyesuaian</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button 
                                type="button" 
                                wire:click="$set('type', 'in')" 
                                class="py-2.5 px-2 rounded-2xl font-black text-xs text-center transition cursor-pointer
                                {{ $type === 'in' ? 'bg-[#163144] text-white shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                + Masuk / Beli
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('type', 'out')" 
                                class="py-2.5 px-2 rounded-2xl font-black text-xs text-center transition cursor-pointer
                                {{ $type === 'out' ? 'bg-rose-600 text-white shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                − Rusak / Expired
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('type', 'adjustment')" 
                                class="py-2.5 px-2 rounded-2xl font-black text-xs text-center transition cursor-pointer
                                {{ $type === 'adjustment' ? 'bg-[#163144] text-white shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                Koreksi Opname
                            </button>
                        </div>
                    </div>

                    <!-- Jumlah Qty -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">
                            {{ $type === 'adjustment' ? 'Set Total Stok Akhir Menjadi' : 'Jumlah (Qty)' }}
                        </label>
                        <input 
                            wire:model="quantity" 
                            type="number" 
                            min="1" 
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-black text-[#163144] outline-none font-mono"
                        >
                        @error('quantity') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- No Referensi / Faktur -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">No. Referensi / Faktur Supplier (Opsional)</label>
                        <input 
                            wire:model="reference" 
                            type="text" 
                            placeholder="Contoh: PO-SUPPLIER-012" 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none font-mono"
                        >
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Keterangan / Alasan</label>
                        <input 
                            wire:model="description" 
                            type="text" 
                            placeholder="Contoh: Kemasan sobek saat pengiriman / Kulakan mingguan" 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                        >
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="$set('showFormModal', false)" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer disabled:opacity-50"
                        >
                            <span wire:loading.remove>Simpan Mutasi Stok</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

</div>
