<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Kelola Pajak & PPN</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Master tarif pajak penjualan untuk opsi pungutan PPN / PB1 pada terminal kasir POS.</p>
        </div>
        <div>
            <button 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>Tambah Pajak Baru</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        
        <!-- Pajak Aktif -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Master</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Pajak Aktif</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Tarif
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $totalActive }} <span class="text-xs font-semibold text-slate-400">tarif</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Status:</span>
                    <span class="font-bold text-[#163144]">Aktif di POS</span>
                </p>
            </div>
        </div>

        <!-- Pajak Utama (Default) -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Standar</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Pajak Utama</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Default
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ $defaultTax ? $defaultTax->name : '-' }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Tarif:</span>
                    <span class="font-bold text-[#163144]">{{ $defaultTax ? $defaultTax->formatted_rate : 'Belum Ada' }}</span>
                </p>
            </div>
        </div>

        <!-- Transaksi Kena Pajak -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Aktivitas</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Transaksi Pajak</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Trx
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    {{ number_format($totalTransactionsWithTax, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">kali</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Frekuensi:</span>
                    <span class="font-bold text-[#163144]">Struk Terkena Pajak</span>
                </p>
            </div>
        </div>

        <!-- Total PPN Terkumpul -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kewajiban</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">PPN Terkumpul</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    IDR
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-2xl font-black text-[#163144] tracking-tight truncate">
                    Rp {{ number_format($totalTaxCollected, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 sm:mt-1 flex items-center justify-between truncate">
                    <span>Akumulasi:</span>
                    <span class="font-bold text-[#163144]">Pungutan Kasir</span>
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
                    placeholder="Cari nama atau kode pajak..." 
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
            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
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

    <!-- Table of Taxes -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[780px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] tracking-wider border-b border-white/60">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Nama Pajak</th>
                        <th class="p-3.5 whitespace-nowrap">Kode Pajak</th>
                        <th class="p-3.5 whitespace-nowrap text-right">Tarif (%)</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Total Pemakaian</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Pajak Utama</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="p-3.5 pr-5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse ($taxes as $tax)
                        <tr class="hover:bg-white/40 transition">
                            <!-- Nama Pajak & Keterangan -->
                            <td class="p-3.5 pl-5">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-[#163144] text-sm block">
                                        {{ $tax->name }}
                                    </span>
                                    @if ($tax->is_default)
                                        <span class="font-extrabold text-[9px] px-2 py-0.5 rounded-full glass-pill-plek text-[#163144]">
                                            Default Kasir
                                        </span>
                                    @endif
                                </div>
                                @if ($tax->description)
                                    <span class="text-[11px] text-slate-500 font-medium block truncate max-w-xs mt-0.5">
                                        {{ $tax->description }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kode Pajak -->
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($tax->code)
                                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-xl glass-pill-plek text-[#163144]">
                                        {{ $tax->code }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">- Tanpa Kode -</span>
                                @endif
                            </td>

                            <!-- Tarif (%) -->
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <span class="font-black text-[#163144] block text-sm font-mono">
                                    {{ $tax->formatted_rate }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold font-mono">
                                    Persentase PPN
                                </span>
                            </td>

                            <!-- Total Pemakaian -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-xl font-black text-xs glass-pill-plek text-[#163144] font-mono">
                                    {{ $tax->transactions_count }}x
                                </span>
                            </td>

                            <!-- Pajak Utama -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if ($tax->is_default)
                                    <span class="px-2.5 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-[10px] inline-flex items-center gap-1">
                                        ✓ Utama
                                    </span>
                                @else
                                    <button 
                                        wire:click="setDefault({{ $tax->id }})" 
                                        class="btn-glass-action text-[10px]"
                                        title="Jadikan pajak ini sebagai opsi default saat kasir mengaktifkan pajak"
                                    >
                                        Set Utama
                                    </button>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <button 
                                    wire:click="toggleStatus({{ $tax->id }})" 
                                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase transition cursor-pointer glass-pill-plek text-[#163144] hover:bg-white/80"
                                >
                                    {{ $tax->is_active ? '✓ Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            <!-- Aksi Side by Side -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        wire:click="openEditModal({{ $tax->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $tax->id }})" 
                                        class="btn-glass-action"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400 font-bold">
                                Belum ada data tarif pajak sesuai pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse ($taxes as $tax)
                <div class="p-3.5 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="font-black text-[#163144] text-sm">{{ $tax->name }}</h4>
                                @if ($tax->is_default)
                                    <span class="font-extrabold text-[8px] px-1.5 py-0.2 rounded-full glass-pill-plek text-[#163144]">
                                        Default
                                    </span>
                                @endif
                            </div>
                            @if ($tax->description)
                                <p class="text-[10px] text-slate-400 font-semibold line-clamp-1 mt-0.5">{{ $tax->description }}</p>
                            @endif
                        </div>
                        <button 
                            wire:click="toggleStatus({{ $tax->id }})" 
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase glass-pill-plek text-[#163144]"
                        >
                            {{ $tax->is_active ? '✓ Aktif' : 'Nonaktif' }}
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2 glass-pill-plek p-2.5 rounded-xl text-xs">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Tarif Pajak</span>
                            <span class="font-mono font-black text-[#163144]">{{ $tax->formatted_rate }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase font-bold block">Pemakaian</span>
                            <span class="font-mono font-black text-[#163144]">{{ $tax->transactions_count }}x digunakan</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-1 border-t border-white/40">
                        @if (!$tax->is_default)
                            <button 
                                wire:click="setDefault({{ $tax->id }})" 
                                class="btn-glass-action"
                            >
                                Set Utama
                            </button>
                        @endif
                        <button 
                            wire:click="openEditModal({{ $tax->id }})" 
                            class="btn-glass-action"
                        >
                            Edit
                        </button>
                        <button 
                            wire:click="confirmDelete({{ $tax->id }})" 
                            class="btn-glass-action"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    Belum ada data tarif pajak.
                </div>
            @endforelse
        </div>

        {{ $taxes->links() }}
    </div>

    <!-- Modal Form Tambah / Edit Pajak -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 shadow-2xl flex flex-col relative my-auto modal-content-animate overflow-hidden">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">{{ $editingTaxId ? 'Edit Master Pajak' : 'Tambah Master Pajak Baru' }}</h3>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">Pengaturan tarif pungutan pajak penjualan toko</p>
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

                <!-- Modal Body -->
                <form wire:submit="saveTax" class="p-5 sm:p-6 space-y-4">
                    
                    <!-- Nama Pajak -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Nama Pajak <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Contoh: PPN 11% / PB1 10%"
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                        >
                        @error('name') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Grid: Kode & Tarif (%) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        
                        <!-- Kode Pajak -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Kode <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                            <input 
                                type="text" 
                                wire:model="code" 
                                placeholder="Contoh: PPN11"
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-mono uppercase font-bold text-[#163144] outline-none"
                            >
                            @error('code') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tarif (%) -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Tarif Pajak (%) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="100" 
                                    wire:model="rate" 
                                    placeholder="11.00"
                                    class="w-full pl-3.5 pr-8 py-2.5 rounded-xl glass-input-plek text-xs font-mono font-black text-[#163144] outline-none"
                                >
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">%</span>
                            </div>
                            @error('rate') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <!-- Keterangan / Deskripsi -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Keterangan <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
                        <input 
                            type="text" 
                            wire:model="description" 
                            placeholder="Contoh: Pajak Pertambahan Nilai standar retail"
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                        >
                        @error('description') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Checkboxes: Aktif & Default -->
                    <div class="pt-2 space-y-2 border-t border-slate-200/80">
                        <label class="flex items-center gap-2 text-xs font-bold text-[#163144] cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="w-4 h-4 rounded border-slate-300 text-[#163144] focus:ring-[#a0bddb]"
                            >
                            <span>Pajak aktif & dapat dipilih di POS Kasir</span>
                        </label>

                        <label class="flex items-center gap-2 text-xs font-bold text-[#163144] cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model="is_default" 
                                class="w-4 h-4 rounded border-slate-300 text-[#163144] focus:ring-[#a0bddb]"
                            >
                            <span>Jadikan Pajak Utama (Otomatis terpilih saat kasir mengaktifkan pajak)</span>
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
                            <span wire:loading.remove>{{ $editingTaxId ? 'Simpan Perubahan' : 'Tambah Pajak' }}</span>
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
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Pajak</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data pajak <strong class="text-[#163144]">"{{ $deletingTaxName }}"</strong>? Riwayat transaksi terdahulu tetap aman tersimpan.
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
                        wire:click="deleteTax" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
