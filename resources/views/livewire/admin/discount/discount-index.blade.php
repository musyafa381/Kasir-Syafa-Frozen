<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Kelola Diskon & Promo</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Atur potongan harga tetap (nominal) atau persentase promo untuk transaksi kasir.</p>
        </div>
        <div>
            <button 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Tambah Diskon Baru</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        <!-- Promo Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Status</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Promo Aktif</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Kasir
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $totalActive }} <span class="text-xs font-semibold text-slate-400">voucher</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Ketersediaan:</span>
                    <span class="font-bold text-[#163144]">Aktif Digunakan</span>
                </p>
            </div>
        </div>

        <!-- Diskon Persen (%) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kategori</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Persentase (%)</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    %
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $totalPercentage }} <span class="text-xs font-semibold text-slate-400">promo</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Tipe:</span>
                    <span class="font-bold text-[#163144]">Diskon Persen</span>
                </p>
            </div>
        </div>

        <!-- Potongan Tetap (Rp) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kategori</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Nominal Tetap</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    IDR
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $totalFixed }} <span class="text-xs font-semibold text-slate-400">promo</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Tipe:</span>
                    <span class="font-bold text-[#163144]">Potongan Rupiah</span>
                </p>
            </div>
        </div>

        <!-- Total Digunakan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Statistik</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Pemakaian</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Trx
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $totalUsage }} <span class="text-xs font-semibold text-slate-400">kali</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Frekuensi:</span>
                    <span class="font-bold text-[#163144]">Tercatat di Kasir</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="glass-card-plek rounded-[24px] p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <!-- Search -->
            <div class="relative w-full sm:max-w-md">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari nama atau kode promo..." 
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

            <!-- Filters -->
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end flex-wrap">
                <select 
                    wire:model.live="filterType" 
                    class="px-3.5 py-2.5 rounded-xl glass-pill-plek text-xs font-extrabold text-[#163144] outline-none cursor-pointer"
                >
                    <option value="all">Semua Tipe Diskon</option>
                    <option value="fixed">Potongan Tetap (Rp)</option>
                    <option value="percentage">Persentase (%)</option>
                </select>

                <select 
                    wire:model.live="filterStatus" 
                    class="px-3.5 py-2.5 rounded-xl glass-pill-plek text-xs font-extrabold text-[#163144] outline-none cursor-pointer"
                >
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table of Discounts -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Nama Promo</th>
                        <th class="p-3.5 whitespace-nowrap">Kode Promo</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Nilai Diskon</th>
                        <th class="p-3.5 whitespace-nowrap">Syarat Belanja</th>
                        <th class="p-3.5 whitespace-nowrap">Periode Berlaku</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Pemakaian</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse ($discounts as $discount)
                        <tr class="hover:bg-white/40 transition">
                            <!-- Nama Promo & Catatan -->
                            <td class="p-3.5 pl-5">
                                <span class="font-black text-[#163144] text-sm block">
                                    {{ $discount->name }}
                                </span>
                                @if ($discount->notes)
                                    <span class="text-[11px] text-slate-500 font-medium block truncate max-w-xs">
                                        {{ $discount->notes }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kode Promo -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($discount->code)
                                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-xl glass-pill-plek text-[#163144]">
                                        {{ $discount->code }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">- Tanpa Kode -</span>
                                @endif
                            </td>

                            <!-- Nilai Diskon -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-black text-[#163144] block text-sm">
                                    {{ $discount->formatted_value }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold font-mono">
                                    {{ $discount->type === 'percentage' ? 'Persentase (%)' : 'Potongan Tetap (Rp)' }}
                                </span>
                            </td>

                            <!-- Syarat Belanja -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-bold text-[#163144] block text-xs">
                                    {{ $discount->min_purchase > 0 ? $discount->formatted_min_purchase : 'Rp 0 (Tanpa Min)' }}
                                </span>
                                @if ($discount->type === 'percentage' && $discount->max_discount_amount)
                                    <span class="text-[10px] text-slate-400 font-bold font-mono block">
                                        Maks: Rp {{ number_format($discount->max_discount_amount, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 font-medium font-mono block">
                                        Min. Belanja
                                    </span>
                                @endif
                            </td>

                            <!-- Periode Berlaku -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($discount->start_date || $discount->end_date)
                                    <span class="font-bold text-[#163144] block text-xs">
                                        @if ($discount->start_date && $discount->end_date)
                                            {{ $discount->start_date->format('d/m/Y') }} - {{ $discount->end_date->format('d/m/Y') }}
                                        @elseif ($discount->start_date)
                                            Mulai {{ $discount->start_date->format('d/m/Y') }}
                                        @else
                                            Sampai {{ $discount->end_date->format('d/m/Y') }}
                                        @endif
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-bold font-mono block">Masa Berlaku</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-[10px]">
                                        Selalu Berlaku
                                    </span>
                                @endif
                            </td>

                            <!-- Pemakaian -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-xl font-black text-xs glass-pill-plek text-[#163144] font-mono">
                                    {{ $discount->transactions_count }}x
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <button 
                                    wire:click="toggleStatus({{ $discount->id }})" 
                                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase transition cursor-pointer glass-pill-plek text-[#163144] hover:bg-white/80"
                                >
                                    {{ $discount->is_active ? '✓ Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            <!-- Aksi Side by Side -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a 
                                        href="{{ route('admin.discounts.poster', $discount->id) }}" 
                                        wire:navigate
                                        class="btn-glass-action gap-1.5"
                                        title="Buka Studio Cetak Poster & Story Promo"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Poster</span>
                                    </a>
                                    <button 
                                        wire:click="openEditModal({{ $discount->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $discount->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400 font-bold">
                                Belum ada data promo atau diskon sesuai pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse ($discounts as $discount)
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-black text-[#163144] text-sm">{{ $discount->name }}</h4>
                            @if ($discount->code)
                                <span class="font-mono font-bold text-[10px] px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] mt-0.5 inline-block">
                                    {{ $discount->code }}
                                </span>
                            @endif
                        </div>
                        <button 
                            wire:click="toggleStatus({{ $discount->id }})" 
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase glass-pill-plek text-[#163144]"
                        >
                            {{ $discount->is_active ? '✓ Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2 glass-pill-plek p-2.5 rounded-xl text-xs">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Nilai Diskon</span>
                            <span class="font-mono font-black text-[#163144]">{{ $discount->formatted_value }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Pemakaian</span>
                            <span class="font-mono font-black text-[#163144]">{{ $discount->transactions_count }}x digunakan</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                        <span>Min: {{ $discount->min_purchase > 0 ? $discount->formatted_min_purchase : 'Rp 0' }}</span>
                        <span>
                            @if ($discount->start_date && $discount->end_date)
                                {{ $discount->start_date->format('d/m') }} - {{ $discount->end_date->format('d/m/Y') }}
                            @else
                                Selalu Berlaku
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-white/40">
                        <a 
                            href="{{ route('admin.discounts.poster', $discount->id) }}" 
                            wire:navigate
                            class="btn-glass-action"
                        >
                            Poster
                        </a>
                        <button 
                            wire:click="openEditModal({{ $discount->id }})" 
                            class="btn-glass-action"
                        >
                            Edit
                        </button>
                        <button 
                            wire:click="confirmDelete({{ $discount->id }})" 
                            class="btn-glass-action"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada data promo atau diskon.
                </div>
            @endforelse
        </div>

        {{ $discounts->links() }}
    </div>

    <!-- Modal Form Tambah / Edit Diskon -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 shadow-2xl flex flex-col relative my-auto modal-content-animate overflow-hidden">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">{{ $editingDiscountId ? 'Edit Promo & Diskon' : 'Tambah Promo Diskon Baru' }}</h3>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">Atur nominal potongan, kode voucher, dan syarat minimal belanja</p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="$set('showModal', false)" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit="saveDiscount" class="p-5 sm:p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                    
                    <!-- Tipe Diskon Selector -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Tipe Diskon <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <button 
                                type="button" 
                                wire:click="$set('type', 'fixed')"
                                class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-2xl border text-xs font-black transition cursor-pointer {{ $type === 'fixed' ? 'bg-[#163144] text-white border-[#163144] shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                <span class="w-2 h-2 rounded-full {{ $type === 'fixed' ? 'bg-white' : 'bg-slate-300' }}"></span>
                                Potongan Tetap (Rp)
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('type', 'percentage')"
                                class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-2xl border text-xs font-black transition cursor-pointer {{ $type === 'percentage' ? 'bg-[#163144] text-white border-[#163144] shadow-md' : 'glass-pill-plek text-[#163144] hover:bg-white' }}"
                            >
                                <span class="w-2 h-2 rounded-full {{ $type === 'percentage' ? 'bg-white' : 'bg-slate-300' }}"></span>
                                Persentase (%)
                            </button>
                        </div>
                    </div>

                    <!-- Nama Diskon -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Nama Diskon / Promo <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Misal: Diskon Gajian 10% / Voucher Belanja 15rb"
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                        >
                        @error('name') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kode Promo & Nilai Diskon -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Kode Promo -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Kode Promo <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                            <input 
                                type="text" 
                                wire:model="code" 
                                placeholder="Misal: GAJIAN10"
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-mono uppercase font-bold text-[#163144] outline-none"
                            >
                            @error('code') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Nilai Diskon Input -->
                        @if ($type === 'fixed')
                            <div 
                                x-data="{
                                    display: '',
                                    init() {
                                        this.updateDisplay($wire.value);
                                        $watch('$wire.value', val => this.updateDisplay(val));
                                    },
                                    updateDisplay(val) {
                                        let num = parseInt(val) || 0;
                                        this.display = num > 0 ? num.toLocaleString('id-ID') : '0';
                                    },
                                    handleInput(e) {
                                        let clean = e.target.value.replace(/[^0-9]/g, '');
                                        let num = parseInt(clean) || 0;
                                        this.display = num > 0 ? num.toLocaleString('id-ID') : '0';
                                        $wire.set('value', num);
                                    }
                                }"
                            >
                                <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Nominal Potongan <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-slate-400 text-xs">Rp</span>
                                    <input 
                                        type="text" 
                                        x-model="display"
                                        @input="handleInput($event)"
                                        @focus="$event.target.select()"
                                        class="w-full pl-9 pr-3 py-2.5 rounded-xl glass-input-plek text-xs font-black text-[#163144] outline-none font-mono"
                                    >
                                </div>
                                @error('value') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Persentase (%) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0.01" 
                                        max="100" 
                                        wire:model="value" 
                                        placeholder="10"
                                        class="w-full pl-3 pr-8 py-2.5 rounded-xl glass-input-plek text-xs font-black text-[#163144] outline-none font-mono"
                                    >
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center font-bold text-slate-400 text-xs">%</span>
                                </div>
                                @error('value') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    <!-- Syarat Min Belanja & Batas Maksimal (if percentage) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Minimal Belanja -->
                        <div 
                            x-data="{
                                display: '',
                                init() {
                                    this.updateDisplay($wire.min_purchase);
                                    $watch('$wire.min_purchase', val => this.updateDisplay(val));
                                },
                                updateDisplay(val) {
                                    let num = parseInt(val) || 0;
                                    this.display = num > 0 ? num.toLocaleString('id-ID') : '0';
                                },
                                handleInput(e) {
                                    let clean = e.target.value.replace(/[^0-9]/g, '');
                                    let num = parseInt(clean) || 0;
                                    this.display = num > 0 ? num.toLocaleString('id-ID') : '0';
                                    $wire.set('min_purchase', num);
                                }
                            }"
                        >
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Minimal Belanja <span class="text-[10px] font-normal text-slate-400">(0 = Tanpa Min)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-slate-400 text-xs">Rp</span>
                                <input 
                                    type="text" 
                                    x-model="display"
                                    @input="handleInput($event)"
                                    @focus="$event.target.select()"
                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none font-mono"
                                >
                            </div>
                            @error('min_purchase') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Batas Maks Diskon (if percentage) -->
                        @if ($type === 'percentage')
                            <div 
                                x-data="{
                                    display: '',
                                    init() {
                                        this.updateDisplay($wire.max_discount_amount);
                                        $watch('$wire.max_discount_amount', val => this.updateDisplay(val));
                                    },
                                    updateDisplay(val) {
                                        let num = parseInt(val) || 0;
                                        this.display = num > 0 ? num.toLocaleString('id-ID') : '0';
                                    },
                                    handleInput(e) {
                                        let clean = e.target.value.replace(/[^0-9]/g, '');
                                        let num = parseInt(clean) || 0;
                                        this.display = num > 0 ? num.toLocaleString('id-ID') : '0';
                                        $wire.set('max_discount_amount', num);
                                    }
                                }"
                            >
                                <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Maks. Potongan <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center font-bold text-slate-400 text-xs">Rp</span>
                                    <input 
                                        type="text" 
                                        x-model="display"
                                        @input="handleInput($event)"
                                        @focus="$event.target.select()"
                                        placeholder="Kosongkan jika tanpa batas"
                                        class="w-full pl-9 pr-3 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none font-mono"
                                    >
                                </div>
                                @error('max_discount_amount') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div class="flex items-center text-slate-400 text-xs italic pt-6">
                                Potongan tetap langsung mengurangi total belanja.
                            </div>
                        @endif
                    </div>

                    <!-- Masa Berlaku (Start Date & End Date) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                            <input 
                                type="date" 
                                wire:model="start_date" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs text-[#163144] font-bold outline-none font-mono"
                            >
                            @error('start_date') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Tanggal Berakhir <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                            <input 
                                type="date" 
                                wire:model="end_date" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs text-[#163144] font-bold outline-none font-mono"
                            >
                            @error('end_date') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Catatan / Syarat Ketentuan -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Catatan / Keterangan (Opsional)</label>
                        <input 
                            type="text" 
                            wire:model="notes" 
                            placeholder="Contoh: Khusus member platinum / promo akhir pekan"
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                        >
                    </div>

                    <!-- Status Aktif Checkbox -->
                    <div class="pt-1">
                        <label class="flex items-center gap-2 text-xs font-bold text-[#163144] cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="w-4 h-4 rounded border-slate-300 text-[#163144] focus:ring-[#a0bddb]"
                            >
                            <span>Promo aktif & dapat dipilih langsung oleh kasir di POS</span>
                        </label>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
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
                            <span wire:loading.remove>{{ $editingDiscountId ? 'Simpan Perubahan' : 'Tambah Promo' }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4 my-auto confirm-card">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-[#e1144b] border border-rose-300/40 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Promo</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus promo <strong class="text-[#163144]">"{{ $deletingDiscountName }}"</strong>? Riwayat transaksi terdahulu tetap aman tersimpan.
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
                        wire:click="deleteDiscount" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
