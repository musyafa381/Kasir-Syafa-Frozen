<div class="space-y-3">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 sm:pb-3">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Inventaris Aset & Mesin Freezer</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Pencatatan inventaris mesin pendingin, chest freezer, display showcase, dan peralatan toko.</p>
        </div>

        <div>
            <button 
                type="button" 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Aset / Freezer Baru</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Cards (Fixed Assets Metrics) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
        
        <!-- Total Nilai Investasi Aset -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Investasi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Aset</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-[9px] sm:text-[10px] shrink-0">
                    Kapital
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block truncate">
                    Rp {{ number_format($totalAssetValuation, 0, ',', '.') }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Modal peralatan toko</p>
            </div>
        </div>

        <!-- Total Unit Freezer -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Pendingin</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Mesin Freezer</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-bold text-[9px] sm:text-[10px] shrink-0">
                    Freezer
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block truncate">
                    {{ $totalFreezerUnits }} <span class="text-xs font-semibold text-slate-500">Unit</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Chest & Showcase</p>
            </div>
        </div>

        <!-- Kondisi Baik / Normal -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Kondisi</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Kondisi Baik</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-bold text-[9px] sm:text-[10px] shrink-0">
                    Normal
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-emerald-700 font-mono tracking-tight block truncate">
                    {{ $totalGoodConditionUnits }} <span class="text-xs font-semibold text-slate-500">Unit</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Siap operasional</p>
            </div>
        </div>

        <!-- Perlu Perbaikan / Servis -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-amber-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Maintenance</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Perlu Servis</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200/60 font-bold text-[9px] sm:text-[10px] shrink-0">
                    Perbaikan
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-amber-700 font-mono tracking-tight block truncate">
                    {{ $totalMaintenanceUnits }} <span class="text-xs font-semibold text-slate-500">Unit</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Perlu pemeliharaan</p>
            </div>
        </div>

    </div>

    <!-- Filters Section -->
    <div class="glass-card-plek rounded-[22px] p-3.5 sm:p-4 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            
            <!-- Search Keyword -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari nama aset, kode, merk, spesifikasi..." 
                    class="w-full pl-9 pr-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <select 
                    wire:model.live="categoryFilter" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] cursor-pointer shadow-2xs"
                >
                    <option value="">Semua Kategori Aset</option>
                    <option value="freezer_refrigeration">Mesin Freezer & Pendingin</option>
                    <option value="pos_hardware">Hardware Kasir & Komputer</option>
                    <option value="operational_equipment">Peralatan Operasional (Genset/Timbangan)</option>
                    <option value="furniture_facility">Fasilitas & Rak Display Toko</option>
                    <option value="other">Peralatan Lainnya</option>
                </select>
            </div>

            <!-- Condition Filter -->
            <div>
                <select 
                    wire:model.live="conditionFilter" 
                    class="w-full px-3 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] outline-none focus:bg-white focus:border-[#163144] cursor-pointer shadow-2xs"
                >
                    <option value="">Semua Status Kondisi</option>
                    <option value="good">Berfungsi Sangat Baik</option>
                    <option value="fair">Berfungsi Normal</option>
                    <option value="maintenance">Dalam Servis / Perbaikan</option>
                    <option value="broken">Rusak / Tidak Berfungsi</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Table of Assets -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs min-w-[850px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4 whitespace-nowrap">Kode & Nama Aset</th>
                        <th class="py-3 px-4 whitespace-nowrap">Kategori Aset</th>
                        <th class="py-3 px-4 whitespace-nowrap">Merk & Spesifikasi</th>
                        <th class="py-3 px-4 whitespace-nowrap">Lokasi Penempatan</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Nilai Perolehan (Rp)</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Kondisi Unit</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-slate-50/80 transition">
                            
                            <!-- Asset Code & Name -->
                            <td class="py-3 px-4">
                                <span class="font-mono font-bold text-blue-800 text-[10px] bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200 inline-block mb-0.5">
                                    {{ $asset->asset_code }}
                                </span>
                                <span class="font-bold text-slate-900 block text-xs">{{ $asset->name }}</span>
                                @if($asset->purchase_date)
                                    <span class="text-[10px] text-slate-400 font-mono block mt-0.5">
                                        Dibeli: {{ $asset->purchase_date->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Category Badge -->
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $asset->category === 'freezer_refrigeration' ? 'bg-cyan-100 text-cyan-800' : 'bg-slate-100 text-slate-800' }}">
                                    {{ \App\Models\Asset::getCategoryLabel($asset->category) }}
                                </span>
                            </td>

                            <!-- Brand & Specs -->
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-800 block">{{ $asset->brand ?: '-' }}</span>
                                <span class="text-[10px] text-slate-500 font-mono block mt-0.5">{{ $asset->specs ?: 'Tanpa rincian spesifikasi' }}</span>
                            </td>

                            <!-- Location -->
                            <td class="py-3 px-4">
                                <span class="text-xs text-slate-700">{{ $asset->location ?: 'Area Toko' }}</span>
                            </td>

                            <!-- Purchase Cost -->
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 text-sm">
                                Rp {{ number_format($asset->purchase_cost, 0, ',', '.') }}
                            </td>

                            <!-- Condition Badge -->
                            <td class="py-3 px-4 text-center">
                                @php
                                    $condStyles = [
                                        'good' => 'bg-blue-100 text-blue-800',
                                        'fair' => 'bg-blue-100 text-blue-800',
                                        'maintenance' => 'bg-amber-100 text-amber-800',
                                        'broken' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $condStyles[$asset->condition] ?? 'bg-slate-100 text-slate-800' }}">
                                    {{ \App\Models\Asset::getConditionLabel($asset->condition) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button 
                                        type="button" 
                                        wire:click="openEditModal({{ $asset->id }})" 
                                        class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold"
                                        title="Edit Data Aset"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="confirmDelete({{ $asset->id }})" 
                                        class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold"
                                        title="Hapus Aset"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-700">Belum ada data inventaris aset atau freezer</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Gunakan tombol "Tambah Aset / Freezer Baru" untuk mencatat unit pendingin toko Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List View -->
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse($assets as $asset)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <span class="font-mono font-bold text-blue-800 text-[10px] bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200 inline-block mb-1">
                                {{ $asset->asset_code }}
                            </span>
                            <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $asset->name }}</h4>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $asset->category === 'freezer_refrigeration' ? 'bg-cyan-100 text-cyan-800' : 'bg-slate-100 text-slate-800' }}">
                                    {{ \App\Models\Asset::getCategoryLabel($asset->category) }}
                                </span>
                                @php
                                    $condStyles = [
                                        'good' => 'bg-blue-100 text-blue-800',
                                        'fair' => 'bg-blue-100 text-blue-800',
                                        'maintenance' => 'bg-amber-100 text-amber-800',
                                        'broken' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold {{ $condStyles[$asset->condition] ?? 'bg-slate-100 text-slate-800' }}">
                                    {{ \App\Models\Asset::getConditionLabel($asset->condition) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] text-slate-400 block uppercase">Nilai Aset</span>
                            <span class="font-mono font-black text-slate-900 text-sm block">
                                Rp {{ number_format($asset->purchase_cost, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if($asset->brand || $asset->specs || $asset->location)
                        <div class="text-[11px] text-slate-500 bg-slate-50 p-2 rounded-xl border border-slate-100 space-y-0.5">
                            @if($asset->brand)
                                <div><span class="font-semibold text-slate-700">Merk:</span> {{ $asset->brand }}</div>
                            @endif
                            @if($asset->specs)
                                <div><span class="font-semibold text-slate-700">Spesifikasi:</span> {{ $asset->specs }}</div>
                            @endif
                            @if($asset->location)
                                <div><span class="font-semibold text-slate-700">Lokasi:</span> {{ $asset->location }}</div>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[10px] text-slate-400 font-mono">
                            {{ $asset->purchase_date ? 'Beli: ' . $asset->purchase_date->format('d/m/Y') : '' }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button" 
                                wire:click="openEditModal({{ $asset->id }})" 
                                class="btn-glass-action px-3 py-1.5 rounded-full text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button 
                                type="button" 
                                wire:click="confirmDelete({{ $asset->id }})" 
                                class="btn-glass-action px-3 py-1.5 rounded-full text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <p class="text-xs font-bold text-slate-700">Belum ada data inventaris aset</p>
                </div>
            @endforelse
        </div>

        {{ $assets->links() }}
    </div>

    <!-- ================= MODAL TAMBAH / EDIT ASET ================= -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl relative my-auto modal-content-animate">
                
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 text-[#163144] flex items-center justify-center border border-[#a0bddb]/40 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-[#163144] text-base leading-tight">
                                {{ $editingAssetId ? 'Edit Data Aset & Freezer' : 'Tambah Aset / Freezer Baru' }}
                            </h3>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">Pencatatan spesifikasi, lokasi, dan nilai modal fisik</p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        wire:click="closeFormModal" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit="saveAsset" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    
                    <!-- Nama Aset -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Nama Aset / Unit <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="assetName" 
                            type="text" 
                            placeholder="Contoh: Chest Freezer RSA 500L (Unit A)" 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                            autofocus
                        >
                        @error('assetName') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Kategori Aset -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Kategori Aset</label>
                            <select 
                                wire:model="assetCategory" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                            >
                                <option value="freezer_refrigeration">Mesin Freezer & Pendingin</option>
                                <option value="pos_hardware">Hardware Kasir & Komputer</option>
                                <option value="operational_equipment">Peralatan Operasional (Genset/Timbangan)</option>
                                <option value="furniture_facility">Fasilitas & Rak Display Toko</option>
                                <option value="other">Peralatan Lainnya</option>
                            </select>
                        </div>

                        <!-- Merk / Brand -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Merk / Brand (Opsional)</label>
                            <input 
                                wire:model="assetBrand" 
                                type="text" 
                                placeholder="Contoh: RSA / Sanden / Gea" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                            >
                        </div>
                    </div>

                    <!-- Spesifikasi / Kapasitas / Daya -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Spesifikasi Unit (Daya Watt / Kapasitas)</label>
                        <input 
                            wire:model="assetSpecs" 
                            type="text" 
                            placeholder="Contoh: 500 Liter, 240 Watt, Suhu Pembekuan -22°C" 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Lokasi Penempatan -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Lokasi Penempatan</label>
                            <input 
                                wire:model="assetLocation" 
                                type="text" 
                                placeholder="Contoh: Toko Depan / Gudang Beku" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                            >
                        </div>

                        <!-- Kondisi Unit -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Kondisi Fisik & Fungsi</label>
                            <select 
                                wire:model="assetCondition" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none"
                            >
                                <option value="good">Berfungsi Sangat Baik</option>
                                <option value="fair">Berfungsi Normal</option>
                                <option value="maintenance">Dalam Servis / Perbaikan</option>
                                <option value="broken">Rusak / Tidak Berfungsi</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Tanggal Pembelian / Perolehan -->
                        <div>
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Tanggal Beli / Perolehan</label>
                            <input 
                                wire:model="assetPurchaseDate" 
                                type="date" 
                                class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-bold text-[#163144] outline-none font-mono"
                            >
                        </div>

                        <!-- Harga Beli / Nilai Modal Perolehan -->
                        <div 
                            x-data="{
                                displayCost: '0',
                                init() {
                                    this.updateDisplay($wire.assetPurchaseCost);
                                    $watch('$wire.assetPurchaseCost', val => this.updateDisplay(val));
                                },
                                updateDisplay(val) {
                                    let num = parseInt(val) || 0;
                                    this.displayCost = num > 0 ? num.toLocaleString('id-ID') : '0';
                                },
                                handleInput(e) {
                                    let clean = e.target.value.replace(/[^0-9]/g, '');
                                    let num = parseInt(clean) || 0;
                                    this.displayCost = num > 0 ? num.toLocaleString('id-ID') : '0';
                                    $wire.set('assetPurchaseCost', num);
                                }
                            }"
                        >
                            <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Harga Beli / Nilai Aset (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-slate-400 text-xs">Rp</span>
                                <input 
                                    type="text" 
                                    x-model="displayCost"
                                    @input="handleInput($event)"
                                    @focus="$event.target.select()"
                                    class="w-full pl-11 pr-3.5 py-2.5 rounded-xl glass-input-plek text-base font-black text-[#163144] outline-none font-mono"
                                >
                            </div>
                            @error('assetPurchaseCost') <p class="text-rose-500 text-[11px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Catatan / Riwayat Servis -->
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                        <input 
                            wire:model="assetNotes" 
                            type="text" 
                            placeholder="Keterangan nomor seri, garansi, teknisi servis..." 
                            class="w-full px-3.5 py-2.5 rounded-xl glass-input-plek text-xs font-semibold text-[#163144] outline-none"
                        >
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="closeFormModal" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                        >
                            {{ $editingAssetId ? 'Simpan Perubahan' : 'Simpan Aset' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- ================= MODAL KONFIRMASI HAPUS ================= -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto confirm-backdrop">
            <div class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4 my-auto confirm-card">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-[#e1144b] border border-rose-300/40 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black text-[#163144] text-base tracking-tight">Konfirmasi Hapus Aset</h3>
                        <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed">
                            Apakah Anda yakin ingin menghapus data inventaris aset <strong class="text-[#163144]">"{{ $deletingAssetName }}"</strong>?
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
                        wire:click="deleteAsset" 
                        class="px-5 py-2 rounded-full bg-[#e1144b] hover:bg-[#c90f40] text-white font-black text-xs shadow-md shadow-[#e1144b]/20 transition active:scale-95 cursor-pointer"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
