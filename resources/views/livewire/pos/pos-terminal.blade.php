<div 
    class="pos-glass flex flex-col h-screen w-full text-slate-800 antialiased select-none overflow-hidden font-sans"
    x-data="posTerminalData()"
    @focus-search.window="focusSearch(true)"
>

    <!-- ================= 1. TOPBAR HEADER POS ================= -->
    <header class="pos-glass-header h-14 sm:h-16 px-2.5 sm:px-6 flex items-center justify-between gap-1.5 sm:gap-4 shrink-0 z-20">
        
        <!-- Brand & Terminal Info (Two-line layout on mobile for clean breathing room) -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
            <img 
                src="{{ asset('images/logo.svg') }}" 
                alt="Logo Syafa Frozen" 
                class="w-8 h-8 sm:w-10 sm:h-10 object-contain drop-shadow-xs shrink-0"
            >
            <div class="min-w-0 flex flex-col justify-center">
                <div class="flex items-center gap-1.5">
                    <h1 class="text-xs sm:text-base font-black text-slate-900 leading-tight truncate">
                        <span class="sm:hidden">Syafa Frozen</span>
                        <span class="hidden sm:inline">Syafa Frozen Food</span>
                    </h1>
                </div>

                <!-- Outlet Cabang Aktif Subtitle & Modal Trigger -->
                @if(isset($activeOutlet) && $activeOutlet)
                    <button 
                        type="button"
                        wire:click="openOutletModal"
                        class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-bold text-slate-500 hover:text-[#163144] transition-colors cursor-pointer text-left truncate leading-tight group mt-0.5"
                        title="Klik untuk memilih / mengganti cabang toko"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="truncate max-w-[120px] xs:max-w-[160px] sm:max-w-xs group-hover:underline">{{ $activeOutlet->name }}</span>
                        <svg class="w-2.5 h-2.5 text-slate-400 group-hover:text-[#163144] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                @else
                    <button 
                        type="button"
                        wire:click="openOutletModal"
                        class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-bold text-amber-600 hover:text-amber-800 transition-colors cursor-pointer text-left leading-tight mt-0.5"
                        title="Klik untuk memilih cabang toko"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0 animate-ping"></span>
                        <span class="underline">Pilih Cabang</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Right: Shift Status & Menu Pengaturan (Simpel & Rapi) -->
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">

            <!-- Antrean Ditahan Badge (Hanya tampil jika ada antrean) -->
            @if($this->holdCount > 0)
                <button 
                    type="button" 
                    wire:click="openHoldListModal" 
                    class="glass-pill-plek inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full text-[#163144] hover:bg-white/80 text-[10px] sm:text-xs font-extrabold cursor-pointer active:scale-95 transition"
                    title="Daftar transaksi yang sedang ditahan"
                >
                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="hidden sm:inline">Antrean</span>
                    <span class="bg-[#163144] text-white rounded-full px-1.5 py-0.5 text-[9px] font-black leading-none">{{ $this->holdCount }}</span>
                </button>
            @endif

            <!-- Shift Status Button (Responsive: Compact on Mobile, Detailed on Desktop) -->
            @if($this->activeShift)
                <button 
                    type="button" 
                    wire:click="openCloseShiftModal" 
                    class="glass-pill-plek inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full text-[#163144] hover:bg-white/80 text-[10px] sm:text-xs font-extrabold cursor-pointer active:scale-95 transition"
                    title="Shift aktif - Klik untuk tutup shift"
                >
                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                    <span class="sm:hidden text-[10px] font-black text-emerald-700">Shift ON</span>
                    <span class="hidden sm:inline truncate max-w-[130px] md:max-w-none">Shift: Rp {{ number_format($this->activeShift->starting_cash, 0, ',', '.') }}</span>
                    <span class="text-[#386797] underline font-bold text-[10px] hidden md:inline">Tutup</span>
                </button>
            @else
                <button 
                    type="button" 
                    wire:click="openOpenShiftModal" 
                    class="btn-glass-amber inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full text-[10px] sm:text-xs active:scale-95 transition cursor-pointer"
                    title="Klik untuk membuka shift kasir"
                >
                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-[10px] sm:text-xs">Buka Shift</span>
                </button>
            @endif

            <!-- Menu Pengaturan & Akun (Dropdown Ringkas) -->
            <div x-data="{ showMenu: false }" class="relative" @click.outside="showMenu = false">
                <button 
                    type="button"
                    @click="showMenu = !showMenu"
                    class="glass-pill-plek inline-flex items-center gap-1.5 p-0.5 sm:px-3 sm:py-1.5 rounded-full text-[#163144] hover:bg-white/90 text-[11px] sm:text-xs font-extrabold cursor-pointer transition shadow-2xs active:scale-95"
                    title="Menu & Pengaturan POS"
                >
                    <!-- Avatar Inisial Bulat (Iconic di Mobile) -->
                    <div class="w-7 h-7 sm:w-6 sm:h-6 rounded-full bg-[#163144] text-white font-black text-[11px] sm:text-xs flex items-center justify-center shrink-0 shadow-2xs relative">
                        {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                        <span class="w-2 h-2 rounded-full bg-emerald-500 border-2 border-white absolute -bottom-0.5 -right-0.5"></span>
                    </div>

                    <!-- Nama Kasir & Chevron di Layar Tablet/Desktop -->
                    <span class="hidden sm:inline truncate max-w-[85px] md:max-w-[120px]">{{ auth()->user()->name ?? 'Kasir' }}</span>
                    <svg class="hidden sm:block w-3 h-3 text-[#2c527a] shrink-0 transition-transform duration-200" :class="{ 'rotate-180': showMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu Card -->
                <div 
                    x-show="showMenu"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                    class="absolute right-0 top-full mt-2 w-64 sm:w-72 rounded-2xl bg-white/95 backdrop-blur-2xl border border-slate-200/90 shadow-xl p-2 z-50 text-slate-800"
                >
                    <!-- Header Info Akun & Jam -->
                    <div class="p-2.5 rounded-xl bg-slate-50/80 border border-slate-100 mb-1.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-extrabold text-xs text-slate-900 truncate leading-tight">{{ auth()->user()->name ?? 'Kasir' }}</p>
                                <p class="text-[10px] text-slate-500 font-medium truncate flex items-center gap-1 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="font-mono text-blue-700 font-semibold" x-text="timeString"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Menu List -->
                    <div class="space-y-0.5">
                        <!-- Dashboard Admin -->
                        <a 
                            href="{{ route('admin.dashboard') }}" 
                            wire:navigate 
                            class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition"
                        >
                            <div class="min-w-0 flex-1">
                                <span class="block leading-tight">Dashboard Admin</span>
                                <span class="block text-[10px] text-slate-400 font-normal">Laporan & data toko</span>
                            </div>
                        </a>

                        <!-- Ganti Cabang Toko -->
                        <button 
                            type="button" 
                            wire:click="openOutletModal" 
                            @click="showMenu = false"
                            class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition text-left cursor-pointer"
                        >
                            <div class="min-w-0 flex-1">
                                <span class="block leading-tight">Ganti Cabang Toko</span>
                                <span class="block text-[10px] text-slate-400 font-normal truncate">{{ $activeOutlet->name ?? 'Pilih Cabang' }}</span>
                            </div>
                        </button>

                        <!-- Kelola Shift Kasir -->
                        @if($this->activeShift)
                            <button 
                                type="button" 
                                wire:click="openCloseShiftModal" 
                                @click="showMenu = false"
                                class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-amber-800 hover:bg-amber-50 transition text-left cursor-pointer"
                            >
                                <div class="min-w-0 flex-1">
                                    <span class="block leading-tight">Tutup Sesi Shift</span>
                                    <span class="block text-[10px] text-amber-600 font-normal">Modal: Rp {{ number_format($this->activeShift->starting_cash, 0, ',', '.') }}</span>
                                </div>
                            </button>
                        @else
                            <button 
                                type="button" 
                                wire:click="openOpenShiftModal" 
                                @click="showMenu = false"
                                class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition text-left cursor-pointer"
                            >
                                <div class="min-w-0 flex-1">
                                    <span class="block leading-tight">Buka Shift Kasir</span>
                                    <span class="block text-[10px] text-slate-400 font-normal">Mulai sesi kasir baru</span>
                                </div>
                            </button>
                        @endif

                        <!-- Antrean Ditahan (Jika Ada) -->
                        @if($this->holdCount > 0)
                            <button 
                                type="button" 
                                wire:click="openHoldListModal" 
                                @click="showMenu = false"
                                class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-800 transition text-left cursor-pointer"
                            >
                                <div class="min-w-0 flex-1 flex items-center justify-between">
                                    <div>
                                        <span class="block leading-tight">Antrean Ditahan</span>
                                        <span class="block text-[10px] text-slate-400 font-normal">Transaksi pending</span>
                                    </div>
                                    <span class="bg-[#163144] text-white rounded-full px-1.5 py-0.5 text-[9px] font-black">{{ $this->holdCount }}</span>
                                </div>
                            </button>
                        @endif
                    </div>

                    <!-- Divider & Tombol Logout -->
                    <div class="pt-1 mt-1 border-t border-slate-100">
                        <form method="POST" action="{{ route('logout') }}" id="pos-logout-form">
                            @csrf
                            <button 
                                type="button" 
                                @click="showMenu = false; window.customConfirm ? window.customConfirm({ title: 'Konfirmasi Keluar', message: 'Apakah Anda yakin ingin mengakhiri sesi kasir dan keluar dari sistem aplikasi?', confirmText: 'Ya, Keluar', onConfirm: () => document.getElementById('pos-logout-form').submit() }) : document.getElementById('pos-logout-form').submit()"
                                class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer"
                            >
                                <div class="min-w-0 flex-1">
                                    <span class="block leading-tight">Keluar (Logout)</span>
                                    <span class="block text-[10px] text-rose-400 font-normal">Akhiri sesi kasir</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ================= APPLE DYNAMIC ISLAND TOP NOTIFICATION ALERT ================= -->
    @if($posAlert)
        <div 
            x-data="{ show: true, touchStartY: 0 }"
            x-show="show"
            x-init="setTimeout(() => { show = false; setTimeout(() => $wire.set('posAlert', null), 300); }, 4200)"
            @touchstart="touchStartY = $event.touches[0].clientY"
            @touchend="if (touchStartY - $event.changedTouches[0].clientY > 25) { show = false; setTimeout(() => $wire.set('posAlert', null), 300); }"
            x-transition:enter="transition transform duration-500 ease-[cubic-bezier(0.175,0.885,0.32,1.275)]"
            x-transition:enter-start="opacity-0 -translate-y-12 scale-85 blur-[4px]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100 blur-0"
            x-transition:leave="transition transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100 blur-0"
            x-transition:leave-end="opacity-0 -translate-y-12 scale-90 blur-[4px]"
            class="fixed top-3.5 sm:top-5 inset-x-0 z-[9999] flex justify-center pointer-events-none px-3.5 sm:px-4"
        >
            <div 
                @click="show = false; setTimeout(() => $wire.set('posAlert', null), 300)"
                class="pointer-events-auto max-w-[440px] w-full p-3.5 sm:p-4 rounded-[26px] border bg-white/80 hover:bg-white/90 backdrop-blur-3xl text-[#163144] flex items-center gap-3.5 transition select-none cursor-pointer group ring-1 ring-black/5
                {{ $posAlertType === 'error' ? 'border-rose-300/80 shadow-[0_20px_45px_-10px_rgba(244,63,94,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]' : ($posAlertType === 'warning' ? 'border-amber-300/80 shadow-[0_20px_45px_-10px_rgba(245,158,11,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]' : ($posAlertType === 'success' ? 'border-emerald-300/80 shadow-[0_20px_45px_-10px_rgba(16,185,129,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]' : 'border-sky-300/80 shadow-[0_20px_45px_-10px_rgba(14,165,233,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]')) }}"
                title="Klik atau usap ke atas untuk menutup"
            >
                <!-- Transparent Frosted Glass Icon Capsule -->
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 border
                    {{ $posAlertType === 'error' ? 'bg-rose-500/15 text-rose-700 border-rose-400/40 shadow-xs shadow-rose-500/20' : ($posAlertType === 'warning' ? 'bg-amber-500/15 text-amber-800 border-amber-400/40 shadow-xs shadow-amber-500/20' : ($posAlertType === 'success' ? 'bg-emerald-500/15 text-emerald-700 border-emerald-400/40 shadow-xs shadow-emerald-500/20' : 'bg-sky-500/15 text-[#163144] border-sky-400/40 shadow-xs shadow-sky-500/20')) }}"
                >
                    @if($posAlertType === 'error')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    @elseif($posAlertType === 'warning')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @elseif($posAlertType === 'success')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>

                <!-- Text Info -->
                <div class="flex-1 min-w-0 pr-1">
                    <div class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest font-mono">
                        <span class="w-1.5 h-1.5 rounded-full {{ $posAlertType === 'error' ? 'bg-rose-500' : ($posAlertType === 'warning' ? 'bg-amber-500' : ($posAlertType === 'success' ? 'bg-emerald-500' : 'bg-sky-500')) }}"></span>
                        <span class="{{ $posAlertType === 'error' ? 'text-rose-700' : ($posAlertType === 'warning' ? 'text-amber-800' : ($posAlertType === 'success' ? 'text-emerald-700' : 'text-[#163144]')) }}">{{ $posAlertType === 'error' ? 'Peringatan' : ($posAlertType === 'warning' ? 'Perhatian' : ($posAlertType === 'success' ? 'Sukses' : 'Kasir POS')) }}</span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500 font-sans normal-case tracking-normal text-[10px]">baru saja</span>
                    </div>
                    <p class="text-xs sm:text-[13px] text-[#163144] font-black mt-0.5 leading-snug break-words">{{ $posAlert }}</p>
                </div>

                <!-- Dismiss Button -->
                <button 
                    type="button" 
                    @click.stop="show = false; setTimeout(() => $wire.set('posAlert', null), 300)" 
                    class="w-7 h-7 rounded-full bg-white/70 hover:bg-white active:scale-90 flex items-center justify-center text-slate-400 hover:text-[#163144] border border-white/80 transition cursor-pointer shrink-0 shadow-2xs"
                    title="Tutup"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= STEP 1: KATALOG & KERANJANG BELANJA =================== -->
    <!-- ========================================================================= -->
    @if($currentStep === 'catalog')
        <div class="flex-1 flex flex-col lg:flex-row overflow-hidden relative">

            <!-- ================= KOLOM KIRI (KATALOG PRODUK) ================= -->
            <main class="flex-1 flex flex-col h-full overflow-hidden bg-transparent">

                <!-- Search Bar Cepat (Desktop, Tablet & Mobile) -->
                <div class="p-2.5 sm:p-3 pos-glass-bar flex items-center gap-2 sm:gap-3 shrink-0">
                    <div class="relative flex-1">
                        <input 
                            id="pos-search-input"
                            x-ref="posSearchInput"
                            wire:model.live.debounce.150ms="search"
                            wire:keydown.enter="submitBarcodeOrSearch"
                            type="text" 
                            placeholder="Cari produk / barcode... (F2)" 
                            class="w-full pl-3 pr-10 sm:pr-14 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition outline-none"
                        >
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center gap-1.5">
                            @if($search)
                                <button wire:click="$set('search', '')" @click="focusSearch()" class="text-slate-400 hover:text-slate-600 text-xs p-1 cursor-pointer">✕</button>
                            @endif
                            <span class="text-[10px] font-mono text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.2 rounded font-bold hidden sm:inline-block">F2</span>
                        </div>
                    </div>

                    <!-- Tombol Pilih Member (Mobile) -->
                    <button 
                        wire:click="goToCustomer" 
                        class="px-2.5 sm:px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-bold text-slate-700 shrink-0 lg:hidden cursor-pointer flex items-center gap-1"
                    >
                        <span class="truncate max-w-[70px] sm:max-w-[100px]">{{ $customerName }}</span>
                        @if($isLoyalty)
                            <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                        @endif
                    </button>
                </div>

                <!-- Loyalty Status Banner (if Member Active) -->
                @if($isLoyalty)
                    <div class="px-3 sm:px-4 py-2 bg-blue-600 text-white text-xs font-semibold flex items-center justify-between shadow-2xs shrink-0">
                        <div class="flex items-center gap-1.5 min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-blue-300 animate-pulse shrink-0"></span>
                            <span class="truncate">Harga Loyalty: <strong>{{ $customerName }}</strong></span>
                        </div>
                        <button wire:click="toggleLoyalty" @click="focusSearch()" class="text-[10px] sm:text-[11px] underline font-bold hover:text-blue-100 cursor-pointer shrink-0">
                            Harga Reguler
                        </button>
                    </div>
                @endif

                <!-- Filter Kategori Horizontal (Blue & White) -->
                <div class="px-3 sm:px-4 py-2 pos-glass-bar shrink-0">
                    <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5">
                        <button 
                            wire:click="setCategory(null)"
                            @click="focusSearch()"
                            class="shrink-0 px-3 py-1.5 sm:px-3.5 sm:py-1.5 rounded-xl text-xs font-bold transition cursor-pointer
                            {{ is_null($selectedCategoryId) ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200' }}"
                        >
                            Semua ({{ $products->count() }})
                        </button>

                        @foreach($categories as $cat)
                            <button 
                                wire:click="setCategory({{ $cat->id }})"
                                @click="focusSearch()"
                                class="shrink-0 px-3 py-1.5 sm:px-3.5 sm:py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer
                                {{ $selectedCategoryId === $cat->id ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200' }}"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Grid Produk (Blue & White) -->
                <div class="flex-1 overflow-y-auto p-2.5 sm:p-4 lg:p-5 pb-32 sm:pb-36 lg:pb-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2 sm:gap-3.5">
                        
                        <!-- Card Tambah Produk Baru Cepat (Fast Action) -->
                        <button 
                            type="button" 
                            wire:click="openQuickProductModal"
                            class="group relative flex flex-col items-center justify-center text-center p-3 sm:p-4 rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-500 bg-blue-50/40 hover:bg-blue-50/90 transition-all duration-150 cursor-pointer min-h-[130px] sm:min-h-[150px] shadow-2xs hover:shadow-xs active:scale-[0.98]"
                            title="Tambah produk baru langsung ke katalog POS"
                        >
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-blue-600 group-hover:bg-blue-700 text-white flex items-center justify-center font-bold text-lg sm:text-xl shadow-xs transition mb-1.5 sm:mb-2">
                                +
                            </div>
                            <span class="font-bold text-xs sm:text-sm text-blue-900 group-hover:text-blue-700 leading-tight">
                                Tambah Produk
                            </span>
                            <span class="text-[10px] text-blue-600/80 mt-0.5 hidden sm:block font-medium">
                                Input Cepat ke POS
                            </span>
                        </button>

                        @foreach($products as $product)
                            @php
                                $hasBox = $product->hasBoxOption();
                                $unitKey = "{$product->id}:unit";
                                $boxKey = "{$product->id}:box";
                                $unitCartQty = isset($cart[$unitKey]) ? $cart[$unitKey]['qty'] : 0;
                                $boxCartQty = isset($cart[$boxKey]) ? $cart[$boxKey]['qty'] : 0;
                                $totalCartQty = $unitCartQty + $boxCartQty;
                                $isInCart = $totalCartQty > 0;
                                $branchStock = $product->getStockForOutlet($activeOutletId);
                                $isOutOfStock = $branchStock <= 0;
                                $isLow = $product->isLowStock($activeOutletId);
                                $effectivePrice = (float) $product->getEffectivePrice($isLoyalty, $activeOutletId);
                                $boxEffectivePrice = $hasBox ? (float) $product->getBoxEffectivePrice($isLoyalty, $activeOutletId) : 0;
                                $isUnitDisabled = $isOutOfStock || ($effectivePrice <= 0);
                                $isBoxDisabled = $isOutOfStock || ($boxEffectivePrice <= 0) || ($branchStock < $product->pieces_per_box);
                            @endphp

                            @if(!$hasBox)
                                <!-- Single Unit Product -->
                                <div 
                                    @if(!$isUnitDisabled) wire:click="addToCart({{ $product->id }}, 'unit')" @click="focusSearch()" @endif
                                    class="group relative flex flex-col justify-between bg-white rounded-2xl border transition-all duration-150 p-2.5 sm:p-3 text-left select-none
                                    {{ !$isUnitDisabled ? 'cursor-pointer active:scale-[0.98]' : 'bg-slate-50' }}
                                    {{ $isInCart ? 'border-blue-500 bg-blue-50/20 ring-2 ring-blue-500/20 shadow-xs' : 'border-slate-200/90 hover:border-blue-400 hover:shadow-xs' }}"
                                >
                                    <!-- Bagian Atas: Nama Produk & Tombol Quick Stock -->
                                    <div>
                                        <div class="flex items-start justify-between gap-1.5">
                                            <h3 class="font-bold text-slate-800 text-[11px] sm:text-xs leading-snug line-clamp-2 flex-1 group-hover:text-blue-600 transition-colors {{ $isOutOfStock ? 'text-slate-400' : '' }}">
                                                {{ $product->name }}
                                            </h3>

                                            <div class="flex items-center gap-1 shrink-0">
                                                @if($isInCart)
                                                    <span class="bg-blue-600 text-white font-black text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded-lg flex items-center justify-center shadow-xs">
                                                        {{ $totalCartQty }}
                                                    </span>
                                                @endif
                                                <!-- Tombol Tambah Stok Cepat (Lingkaran +) -->
                                                <button 
                                                    type="button"
                                                    wire:click.stop="openQuickStockModal({{ $product->id }})"
                                                    class="w-5 h-5 sm:w-5.5 sm:h-5.5 rounded-full bg-slate-100 hover:bg-blue-600 text-slate-600 hover:text-white border border-slate-200/90 flex items-center justify-center font-bold text-xs transition-all duration-150 cursor-pointer shadow-2xs hover:scale-110 active:scale-90"
                                                    title="Tambah / Koreksi Stok Cepat ({{ $product->name }})"
                                                >
                                                    +
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Badge Status Stok Basic -->
                                        <div class="flex items-center gap-1 mt-1 flex-wrap">
                                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md border shrink-0
                                                {{ $branchStock <= 0 ? 'bg-red-50 text-red-600 border-red-200' : ($isLow ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-500 border-slate-200') }}">
                                                Stok: {{ $branchStock }} {{ $product->unit }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Bagian Bawah: Harga Satuan & Tombol Visual Tambah -->
                                    <div class="mt-2.5 pt-2 border-t border-slate-100">
                                        <div class="w-full py-1.5 px-2 rounded-xl bg-slate-50 {{ !$isUnitDisabled ? 'group-hover:bg-blue-50 group-hover:border-blue-300' : '' }} border border-slate-200 flex items-center justify-between transition">
                                            <div class="text-left min-w-0 pr-1">
                                                <span class="text-[9px] text-slate-500 font-semibold block">Harga Satuan</span>
                                                @if($effectivePrice > 0)
                                                    <span class="text-xs sm:text-sm font-black {{ $isOutOfStock ? 'text-slate-400' : 'text-blue-700' }} truncate block">
                                                        Rp {{ number_format($effectivePrice, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-xs sm:text-sm font-black text-slate-400 truncate block">
                                                        Rp 0
                                                    </span>
                                                @endif
                                            </div>
                                            @if(!$isUnitDisabled)
                                                <span class="px-2 py-1 bg-blue-600 group-hover:bg-blue-700 text-white font-bold text-[10px] rounded-lg shadow-2xs shrink-0 transition">
                                                    Tambah
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-slate-200 text-slate-400 font-bold text-[10px] rounded-lg shadow-2xs shrink-0">
                                                    {{ $effectivePrice <= 0 ? 'Harga 0' : 'Habis' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Dual Action Product (Dus & Eceran) -->
                                <div 
                                    class="group relative flex flex-col justify-between bg-white rounded-2xl border transition-all duration-150 p-2.5 sm:p-3 text-left
                                    {{ $isInCart ? 'border-blue-500 bg-blue-50/20 ring-2 ring-blue-500/20 shadow-xs' : 'border-slate-200/90 hover:border-blue-400 hover:shadow-xs' }}
                                    {{ ($isUnitDisabled && $isBoxDisabled) ? 'bg-slate-50' : '' }}"
                                >
                                    <!-- Bagian Atas: Nama Produk & Tombol Quick Stock -->
                                    <div>
                                        <div class="flex items-start justify-between gap-1.5">
                                            <h3 
                                                @if(!$isUnitDisabled) wire:click="addToCart({{ $product->id }}, 'unit')" @click="focusSearch()" @endif
                                                class="font-bold text-slate-800 text-[11px] sm:text-xs leading-snug line-clamp-2 flex-1 group-hover:text-blue-600 transition-colors {{ !$isUnitDisabled ? 'cursor-pointer' : '' }} {{ ($isUnitDisabled && $isBoxDisabled) ? 'text-slate-400' : '' }}"
                                                title="{{ !$isUnitDisabled ? 'Klik untuk tambah ecer' : '' }}"
                                            >
                                                {{ $product->name }}
                                            </h3>

                                            <div class="flex items-center gap-1 shrink-0">
                                                @if($isInCart)
                                                    <span class="bg-blue-600 text-white font-black text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded-lg flex items-center justify-center shadow-xs">
                                                        {{ $totalCartQty }}
                                                    </span>
                                                @endif
                                                <!-- Tombol Tambah Stok Cepat (Lingkaran +) -->
                                                <button 
                                                    type="button"
                                                    wire:click.stop="openQuickStockModal({{ $product->id }})"
                                                    class="w-5 h-5 sm:w-5.5 sm:h-5.5 rounded-full bg-slate-100 hover:bg-blue-600 text-slate-600 hover:text-white border border-slate-200/90 flex items-center justify-center font-bold text-xs transition-all duration-150 cursor-pointer shadow-2xs hover:scale-110 active:scale-90"
                                                    title="Tambah / Koreksi Stok Cepat ({{ $product->name }})"
                                                >
                                                    +
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Badge Status Dus & Stok Basic -->
                                        <div class="flex items-center gap-1 mt-1 flex-wrap">
                                            <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md border shrink-0
                                                {{ $branchStock <= 0 ? 'bg-red-50 text-red-600 border-red-200' : ($isLow ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-500 border-slate-200') }}">
                                                Stok: {{ $branchStock }} {{ $product->unit }}
                                            </span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200">
                                                1 Dus = {{ $product->pieces_per_box }} {{ $product->unit }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Bagian Bawah: Tombol Aksi Tambah (Ecer vs Dus) -->
                                    <div class="mt-2.5 pt-2 border-t border-slate-100">
                                        <div class="grid grid-cols-2 gap-1.5">
                                            <!-- Tombol Eceran -->
                                            <button 
                                                type="button"
                                                wire:click="addToCart({{ $product->id }}, 'unit')"
                                                @click="focusSearch()"
                                                @if($isUnitDisabled) disabled @endif
                                                class="p-1.5 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 text-left transition cursor-pointer active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                <span class="text-[9px] font-bold text-slate-500 block">Pcs / Ecer</span>
                                                <span class="text-[10px] sm:text-[11px] font-black {{ $effectivePrice > 0 ? 'text-blue-700' : 'text-slate-400' }} block truncate">
                                                    Rp {{ number_format($effectivePrice, 0, ',', '.') }}
                                                </span>
                                                @if($unitCartQty > 0)
                                                    <span class="text-[8px] font-black text-blue-600 bg-blue-100 px-1 py-0.2 rounded mt-0.5 inline-block">
                                                        Di keranjang: {{ $unitCartQty }}
                                                    </span>
                                                @endif
                                            </button>

                                            <!-- Tombol Kardus -->
                                            <button 
                                                type="button"
                                                wire:click="addToCart({{ $product->id }}, 'box')"
                                                @click="focusSearch()"
                                                @if($isBoxDisabled) disabled @endif
                                                class="p-1.5 rounded-xl bg-amber-50/70 hover:bg-amber-100 border border-amber-200/90 text-left transition cursor-pointer active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                <span class="text-[9px] font-black text-amber-800 block flex items-center justify-between">
                                                    <span>Dus</span>
                                                    <span class="text-[8px] font-normal text-amber-600 font-mono">(@ {{ $product->pieces_per_box }} {{ $product->unit }})</span>
                                                </span>
                                                <span class="text-[10px] sm:text-[11px] font-black {{ $boxEffectivePrice > 0 ? 'text-amber-700' : 'text-slate-400' }} block truncate">
                                                    Rp {{ number_format($boxEffectivePrice, 0, ',', '.') }}
                                                </span>
                                                @if($boxCartQty > 0)
                                                    <span class="text-[8px] font-black text-amber-700 bg-amber-200 px-1 py-0.2 rounded mt-0.5 inline-block">
                                                        Di keranjang: {{ $boxCartQty }} Dus
                                                    </span>
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </main>

            <!-- ================= KOLOM KANAN (SIDEBAR KERANJANG - DESKTOP & TABLET) ================= -->
            <aside class="hidden lg:flex flex-col w-80 xl:w-96 2xl:w-[400px] shrink-0 pos-glass-aside h-full">

                <!-- Cart Header -->
                <div class="p-3.5 border-b border-white/60 flex items-center justify-between bg-transparent shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-sm">Pesanan</span>
                        <span class="bg-blue-100 text-blue-800 text-[11px] font-bold px-2 py-0.2 rounded-full">
                            {{ count($cart) }} Item
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        @if(!empty($cart))
                            <button 
                                type="button" 
                                wire:click="openHoldModal"
                                class="px-2.5 py-1 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 font-bold text-xs flex items-center gap-1.5 transition cursor-pointer shadow-2xs active:scale-95"
                                title="Tahan transaksi untuk melayani antrean pelanggan berikutnya"
                            >
                                <span>Tahan</span>
                            </button>

                            <div x-data="{ confirming: false }" class="relative">
                                <button 
                                    x-show="!confirming"
                                    @click="confirming = true"
                                    type="button"
                                    class="text-[11px] font-semibold text-slate-500 hover:text-red-600 px-2 py-1 transition cursor-pointer"
                                >
                                    Kosongkan
                                </button>

                                <div 
                                    x-show="confirming" 
                                    x-cloak
                                    @click.away="confirming = false"
                                    class="flex items-center gap-1.5 bg-red-50 border border-red-200 px-2 py-1 rounded-xl shadow-xs"
                                >
                                    <span class="text-[10px] text-red-700 font-bold">Hapus?</span>
                                    <button 
                                        wire:click="clearCart" 
                                        @click="confirming = false"
                                        class="text-[10px] bg-red-600 text-white font-bold px-2 py-0.5 rounded-lg shadow-2xs"
                                    >
                                        Ya
                                    </button>
                                    <button 
                                        @click="confirming = false"
                                        class="text-[10px] text-slate-600 px-1 hover:text-slate-900"
                                    >
                                        Batal
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Pill di Sidebar -->
                <div class="p-3 bg-blue-50/50 border-b border-slate-200 flex items-center justify-between shrink-0">
                    <div class="min-w-0 pr-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Pelanggan</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="font-bold text-xs text-slate-900 truncate">{{ $customerName }}</span>
                            @if($isLoyalty)
                                <span class="text-[9px] font-bold bg-blue-100 text-blue-800 border border-blue-200 px-1.5 py-0.2 rounded">
                                    Member
                                </span>
                            @endif
                        </div>
                    </div>
                    <button 
                        wire:click="goToCustomer" 
                        class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 text-slate-700 hover:text-blue-700 font-bold text-xs shadow-2xs transition cursor-pointer"
                    >
                        {{ $selectedCustomerId ? 'Ganti' : 'Pilih Member' }}
                    </button>
                </div>

                <!-- Cart Items Scrollable List -->
                <div class="flex-1 overflow-y-auto p-3 space-y-2">
                    @if(empty($cart))
                        <div class="flex flex-col items-center justify-center h-48 text-center text-slate-400 px-4">
                            <p class="font-bold text-xs text-slate-600">Keranjang masih kosong</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Klik produk di katalog atau scan barcode untuk menambahkan item.</p>
                        </div>
                    @else
                        @foreach($cart as $cartKey => $item)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h4 class="font-bold text-xs text-slate-900 truncate">{{ $item['name'] }}</h4>
                                        @if(($item['unit_type'] ?? 'unit') === 'box')
                                            <span class="text-[9px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 px-1.5 py-0.2 rounded-md">
                                                DUS ({{ $item['pieces_per_unit'] }} {{ $item['unit'] }})
                                            </span>
                                        @else
                                            <span class="text-[9px] font-bold bg-slate-200 text-slate-700 px-1.5 py-0.2 rounded-md">
                                                {{ $item['unit'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <!-- Price Switcher Badge (Regular vs Loyalty/Sales) -->
                                        @if(!empty($item['loyalty_price']) && $item['loyalty_price'] != $item['regular_price'])
                                            <button 
                                                type="button" 
                                                wire:click="toggleItemPriceType('{{ $cartKey }}')" 
                                                class="text-[9px] font-bold px-1.5 py-0.5 rounded border transition cursor-pointer flex items-center gap-0.5 {{ ($item['price_type'] ?? 'regular') === 'loyalty' ? 'bg-amber-100 text-amber-900 border-amber-300 hover:bg-amber-200' : 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200' }}"
                                                title="Klik untuk beralih antara Harga Reguler & Harga Sales/Member"
                                            >
                                                <span>{{ ($item['price_type'] ?? 'regular') === 'loyalty' ? 'Sales' : 'Reg' }}:</span>
                                                <span class="font-mono">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                                <span class="text-[8px] text-slate-400">⇄</span>
                                            </button>
                                        @else
                                            <span class="text-[11px] text-slate-500 font-mono">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                        @endif

                                        <!-- Item Discount Badge / Trigger -->
                                        @if(!empty($item['discount_amount']) && $item['discount_amount'] > 0)
                                            <button 
                                                type="button" 
                                                wire:click="openItemDiscountModal('{{ $cartKey }}')" 
                                                class="text-[9px] font-bold bg-rose-100 text-rose-700 border border-rose-300 px-1.5 py-0.5 rounded-md hover:bg-rose-200 transition cursor-pointer flex items-center gap-0.5"
                                                title="Klik untuk ubah diskon item"
                                            >
                                                <span>-Rp {{ number_format($item['discount_amount'], 0, ',', '.') }}</span>
                                            </button>
                                        @else
                                            <button 
                                                type="button" 
                                                wire:click="openItemDiscountModal('{{ $cartKey }}')" 
                                                class="text-[9px] font-semibold text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-1 py-0.5 rounded transition cursor-pointer"
                                                title="Beri diskon khusus produk ini"
                                            >
                                                % Disc
                                            </button>
                                        @endif

                                        <span>•</span>
                                        <!-- Item Subtotal -->
                                        @if(!empty($item['discount_amount']) && $item['discount_amount'] > 0)
                                            <span class="text-[10px] text-slate-400 line-through font-mono">
                                                Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                            </span>
                                            <span class="font-bold text-emerald-600 font-mono text-[11px]">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="font-bold text-slate-800 font-mono text-[11px]">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        wire:click="removeFromCart('{{ $cartKey }}')" 
                                        @click="focusSearch()"
                                        title="Hapus Item"
                                        class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer text-xs"
                                    >
                                        ✕
                                    </button>
                                    <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg p-0.5">
                                        <button 
                                            wire:click="decrementQty('{{ $cartKey }}')" 
                                            @click="focusSearch()"
                                            class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs transition cursor-pointer"
                                        >
                                            −
                                        </button>
                                        <span class="w-6 text-center font-bold text-xs text-slate-800">
                                            {{ $item['qty'] }}
                                        </span>
                                        <button 
                                            wire:click="incrementQty('{{ $cartKey }}')" 
                                            @click="focusSearch()"
                                            class="w-6 h-6 rounded bg-blue-600 hover:bg-blue-700 flex items-center justify-center font-bold text-white text-xs transition cursor-pointer"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Ringkasan Pembayaran Bawah (Desktop) -->
                <div class="p-3.5 bg-slate-50 border-t border-slate-200 space-y-2.5">
                    <div class="space-y-1 text-xs text-slate-600">
                        @if($this->getNumericDiscountAmount() > 0)
                            <div class="flex justify-between items-center text-rose-600 font-semibold text-xs py-0.5">
                                <span class="text-[11px]">Diskon ({{ $this->selectedDiscount?->name ?? 'Promo' }}):</span>
                                <span class="font-mono text-xs">- Rp {{ number_format($this->getNumericDiscountAmount(), 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if($this->getNumericTaxAmount() > 0)
                            <div class="flex justify-between items-center text-indigo-700 font-semibold text-xs py-0.5">
                                <span class="text-[11px]">Pajak ({{ $this->selectedTax?->name ?? 'PPN' }}):</span>
                                <span class="font-mono text-xs">+ Rp {{ number_format($this->getNumericTaxAmount(), 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-baseline justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Total Tagihan</span>
                        <span class="text-xl font-black text-blue-600">
                            Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Tombol Lanjut ke Halaman Pembayaran (Desktop) -->
                    <button 
                        wire:click="goToPayment"
                        @if(empty($cart)) disabled @endif
                        class="w-full py-3.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-black text-sm flex items-center justify-between transition shadow-md shadow-blue-600/20 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <span>Lanjut ke Pembayaran</span>
                        <span class="font-mono text-xs bg-white/20 px-2 py-0.5 rounded-lg">
                            Rp {{ number_format($this->grandTotal, 0, ',', '.') }} →
                        </span>
                    </button>
                </div>
            </aside>

            <!-- ================= MOBILE FLOATING BOTTOM BAR ================= -->
            @if(!$mobileCartOpen)
                <div class="lg:hidden fixed bottom-6 sm:bottom-8 inset-x-3.5 sm:inset-x-6 z-30 pointer-events-none max-w-lg mx-auto">
                    <div class="pointer-events-auto glass-card-plek bg-white/95 backdrop-blur-xl rounded-2xl p-2 sm:p-2.5 flex items-center justify-between gap-2.5 shadow-[0_12px_36px_-6px_rgba(22,49,68,0.22),0_4px_14px_rgba(22,49,68,0.08)] border border-white/90">
                        
                        <!-- Tappable Cart Summary -->
                        <button 
                            type="button"
                            wire:click="openMobileCartModal"
                            class="flex items-center gap-2.5 text-left bg-white/70 hover:bg-white active:bg-slate-100 p-1.5 sm:p-2 rounded-xl border border-[#a0bddb]/40 transition cursor-pointer min-w-0 group shadow-2xs"
                        >
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs shadow-[#163144]/30 font-mono">
                                {{ count($cart) }}
                            </div>
                            <div class="min-w-0 pr-1">
                                <p class="text-[10px] text-slate-500 font-extrabold truncate flex items-center gap-1">
                                    <span>{{ $isLoyalty ? 'Member' : 'Umum' }} • Item</span>
                                    <span class="text-[#163144] text-[9px] group-hover:-translate-y-0.5 transition-transform">▲</span>
                                </p>
                                <p class="text-xs sm:text-sm font-black text-[#163144] font-mono truncate">
                                    Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                                </p>
                            </div>
                        </button>

                        <!-- Tombol Bayar Sekarang -->
                        <button 
                            wire:click="goToPayment"
                            @if(empty($cart)) disabled @endif
                            class="flex-1 h-11 px-3.5 sm:px-4 rounded-xl font-black text-xs sm:text-sm flex items-center justify-between gap-1.5 transition cursor-pointer shadow-md disabled:cursor-not-allowed
                            {{ empty($cart) ? 'bg-slate-100 text-slate-400 border border-slate-200/80 shadow-none' : 'bg-[#163144] hover:bg-[#20445e] active:scale-[0.98] text-white shadow-lg shadow-[#163144]/25' }}"
                        >
                            <span class="flex items-center gap-1.5 font-bold tracking-wide">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Bayar
                            </span>
                            <span class="flex items-center gap-1 font-black text-xs font-mono {{ empty($cart) ? 'text-slate-400' : 'bg-white/20 px-2 py-0.5 rounded-lg border border-white/20' }}">
                                @if(empty($cart))
                                    0
                                @else
                                    {{ count($cart) }}
                                @endif
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- ================= MOBILE CART DEDICATED FULL-PAGE VIEW ================= -->
            @if($mobileCartOpen)
                <div 
                    class="lg:hidden fixed inset-0 z-50 flex flex-col mobile-cart-page overflow-hidden select-none"
                >
                    <!-- Mobile Page Sticky Header (Navigasi Halaman) -->
                    <header class="h-16 px-3.5 sm:px-4 bg-white/85 backdrop-blur-xl border-b border-white/70 flex items-center justify-between gap-2.5 shrink-0 shadow-xs z-20">
                        <!-- Back Button to Catalog -->
                        <button 
                            type="button"
                            wire:click="closeMobileCartModal"
                            class="w-10 h-10 rounded-full bg-white/90 border border-white/80 shadow-2xs flex items-center justify-center text-[#163144] hover:bg-white active:scale-95 transition cursor-pointer shrink-0"
                            title="Kembali ke Katalog Produk"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <!-- Page Title & Counter -->
                        <div class="flex items-center gap-2 min-w-0">
                            <h2 class="font-black text-base sm:text-lg text-[#163144] tracking-tight truncate">Keranjang</h2>
                            <span class="glass-pill-plek bg-[#163144]/10 text-[#163144] text-xs font-black px-2.5 py-0.5 rounded-full border border-[#163144]/15 font-mono shrink-0">
                                {{ count($cart) }} Item
                            </span>
                        </div>

                        <!-- Action Pills: Tahan & Kosongkan -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if(!empty($cart))
                                <button 
                                    type="button" 
                                    wire:click="openHoldModal"
                                    class="btn-glass-action text-xs font-black py-1.5 px-3 shadow-2xs"
                                    title="Tahan transaksi sementara"
                                >
                                    <span>Tahan</span>
                                </button>
                                <button 
                                    wire:click="clearCart" 
                                    type="button"
                                    class="btn-glass-rose text-xs font-black py-1.5 px-3 shadow-2xs"
                                    title="Kosongkan keranjang"
                                >
                                    <span>Kosongkan</span>
                                </button>
                            @endif
                        </div>
                    </header>

                    <!-- Customer Info Banner (Frosted Widget) -->
                    <div class="px-4 pt-3.5 pb-2 shrink-0">
                        <div class="glass-card-plek bg-white/75 backdrop-blur-md rounded-2xl p-3 border border-white/80 shadow-xs flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#a0bddb]/40 to-[#163144]/15 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-black text-[#2c527a] uppercase tracking-wider block">Pelanggan Aktif</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="font-black text-xs text-[#163144] truncate">{{ $customerName }}</span>
                                        @if($isLoyalty)
                                            <span class="text-[9px] font-black bg-[#163144] text-white px-2 py-0.5 rounded-full shadow-2xs font-mono">
                                                Member
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button 
                                wire:click="goToCustomer" 
                                type="button"
                                class="glass-pill-plek px-3.5 py-1.5 rounded-full text-[#163144] hover:bg-[#163144] hover:text-white font-extrabold text-xs shrink-0 cursor-pointer shadow-2xs transition active:scale-95 border border-[#163144]/15"
                            >
                                {{ $selectedCustomerId ? 'Ganti' : 'Pilih Member' }}
                            </button>
                        </div>
                    </div>

                    <!-- Page Items List & Extras (Scrollable Body) -->
                    <div class="flex-1 overflow-y-auto px-4 py-2 space-y-3 overscroll-contain">
                        @if(empty($cart))
                            <div class="py-16 text-center text-slate-400">
                                <div class="w-20 h-20 rounded-3xl bg-white/70 text-[#163144] flex items-center justify-center mx-auto mb-3.5 border border-[#a0bddb]/40 shadow-xs">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                                <h3 class="text-base font-black text-[#163144]">Keranjang Masih Kosong</h3>
                                <p class="text-xs text-slate-500 mt-1">Belum ada produk yang ditambahkan ke pesanan kasir.</p>
                                <button 
                                    type="button"
                                    wire:click="closeMobileCartModal"
                                    class="mt-4 px-5 py-2.5 rounded-full bg-[#163144] hover:bg-[#20445e] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 cursor-pointer"
                                >
                                    Mulai Pilih Produk
                                </button>
                            </div>
                        @else

                            <!-- Cart Item Cards -->
                            @foreach($cart as $cartKey => $item)
                                <div class="glass-card-plek bg-white/85 hover:bg-white backdrop-blur-md rounded-2xl p-3.5 border border-white/90 shadow-2xs transition space-y-2.5">
                                    <!-- Card Top Row: Title, Unit Badge, Subtotal -->
                                    <div class="flex items-start justify-between gap-2.5">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="font-black text-sm text-[#163144] leading-snug">{{ $item['name'] }}</h4>
                                                @if(($item['unit_type'] ?? 'unit') === 'box')
                                                    <span class="text-[9px] font-black bg-amber-100 text-amber-900 border border-amber-300 px-1.5 py-0.5 rounded-md">
                                                        DUS ({{ $item['pieces_per_unit'] }} {{ $item['unit'] }})
                                                    </span>
                                                @else
                                                    <span class="text-[9px] font-bold bg-[#a0bddb]/25 text-[#163144] border border-[#a0bddb]/35 px-1.5 py-0.5 rounded-md">
                                                        {{ $item['unit'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Subtotal -->
                                        <div class="text-right shrink-0">
                                            @if(!empty($item['discount_amount']) && $item['discount_amount'] > 0)
                                                <span class="text-[10px] text-slate-400 line-through block font-mono">
                                                    Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                                </span>
                                                <span class="font-black text-sm text-emerald-600 block font-mono">
                                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="font-black text-sm text-[#163144] block font-mono">
                                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Card Bottom Row: Price Badges on Left, Stepper on Right -->
                                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100/80">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <!-- Price Switcher Badge -->
                                            @if(!empty($item['loyalty_price']) && $item['loyalty_price'] != $item['regular_price'])
                                                <button 
                                                    type="button" 
                                                    wire:click="toggleItemPriceType('{{ $cartKey }}')" 
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-lg border transition cursor-pointer flex items-center gap-1 {{ ($item['price_type'] ?? 'regular') === 'loyalty' ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-[#a0bddb]/20 text-[#163144] border-[#a0bddb]/40' }}"
                                                >
                                                    <span>{{ ($item['price_type'] ?? 'regular') === 'loyalty' ? 'Sales' : 'Reg' }}:</span>
                                                    <span class="font-mono">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                                    <span class="text-[8px] text-slate-400">⇄</span>
                                                </button>
                                            @else
                                                <span class="text-xs text-slate-500 font-mono font-bold">
                                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                </span>
                                            @endif

                                            <!-- Item Discount Badge -->
                                            @if(!empty($item['discount_amount']) && $item['discount_amount'] > 0)
                                                <button 
                                                    type="button" 
                                                    wire:click="openItemDiscountModal('{{ $cartKey }}')" 
                                                    class="text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300 px-2 py-0.5 rounded-md flex items-center gap-1 cursor-pointer"
                                                >
                                                    <span>-Rp {{ number_format($item['discount_amount'], 0, ',', '.') }}</span>
                                                </button>
                                            @else
                                                <button 
                                                    type="button" 
                                                    wire:click="openItemDiscountModal('{{ $cartKey }}')" 
                                                    class="text-[10px] font-extrabold text-[#2c527a] hover:text-[#163144] bg-[#a0bddb]/15 hover:bg-[#a0bddb]/30 px-2 py-0.5 rounded-md transition border border-[#a0bddb]/30 cursor-pointer"
                                                >
                                                    % Diskon
                                                </button>
                                            @endif

                                            <!-- Hapus item button -->
                                            <button 
                                                type="button" 
                                                wire:click="removeFromCart('{{ $cartKey }}')" 
                                                class="text-[10px] text-rose-500 hover:text-rose-700 font-bold ml-1 hover:underline cursor-pointer"
                                            >
                                                Hapus
                                            </button>
                                        </div>

                                        <!-- Stepper Kontrol Kuantitas -->
                                        <div class="flex items-center gap-1 bg-slate-100/90 border border-slate-200/80 rounded-xl p-0.5 shadow-2xs shrink-0">
                                            <button 
                                                type="button" 
                                                wire:click="decrementQty('{{ $cartKey }}')"
                                                class="w-7 h-7 rounded-lg bg-white hover:bg-slate-50 active:scale-90 flex items-center justify-center font-black text-[#163144] text-xs cursor-pointer border border-slate-200/60 shadow-2xs transition"
                                            >
                                                −
                                            </button>
                                            <span class="w-6 text-center font-black text-xs text-[#163144] font-mono">
                                                {{ $item['qty'] }}
                                            </span>
                                            <button 
                                                type="button" 
                                                wire:click="incrementQty('{{ $cartKey }}')"
                                                class="w-7 h-7 rounded-lg bg-[#163144] hover:bg-[#20445e] active:scale-90 flex items-center justify-center font-black text-white text-xs cursor-pointer shadow-xs shadow-[#163144]/30 transition"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Bagian Bawah Item: Tombol Tambah Produk & Widget Promo & Catatan -->
                            <div class="pt-1 space-y-2.5">
                                <!-- Tombol Tambah Produk Lain -->
                                <button 
                                    type="button" 
                                    wire:click="closeMobileCartModal"
                                    class="w-full py-3 px-4 rounded-2xl border-2 border-dashed border-[#163144]/20 hover:border-[#163144]/40 bg-white/40 hover:bg-white/70 text-[#163144] text-xs font-black flex items-center justify-center gap-2 transition active:scale-98 cursor-pointer shadow-2xs"
                                >
                                    <span>Tambah Produk Lain dari Katalog</span>
                                </button>

                                <!-- Kartu Diskon Promo & Catatan Transaksi -->
                                <div class="glass-card-plek bg-white/75 backdrop-blur-md rounded-2xl p-3.5 border border-white/80 shadow-2xs space-y-3">
                                    <!-- Promo Trigger -->
                                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                                        <div class="flex items-center gap-2.5">
                                            <div>
                                                <span class="text-xs font-black text-[#163144] block">Promo & Diskon Faktur</span>
                                                <span class="text-[10px] text-slate-500 font-medium">
                                                    @if($this->getNumericDiscountAmount() > 0)
                                                        Hemat Rp {{ number_format($this->getNumericDiscountAmount(), 0, ',', '.') }}
                                                    @else
                                                        Gunakan voucher / promo toko
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <button 
                                            type="button" 
                                            wire:click="openDiscountModal"
                                            class="glass-pill-plek px-3 py-1.5 rounded-full text-xs font-extrabold text-[#163144] hover:bg-white cursor-pointer shadow-2xs transition active:scale-95"
                                        >
                                            {{ $this->selectedDiscountId ? 'Ubah Promo' : 'Pilih Promo' }}
                                        </button>
                                    </div>

                                    <!-- Catatan Transaksi -->
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Catatan Pembayaran (Opsional):</label>
                                        <input 
                                            type="text" 
                                            wire:model.live.debounce.300ms="paymentNotes" 
                                            placeholder="Contoh: Titip plastik, pembayaran via QRIS, dll..."
                                            class="w-full px-3 py-2 text-xs bg-white/90 border border-slate-200/80 rounded-xl focus:bg-white focus:border-[#163144] outline-none transition font-medium"
                                        >
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sticky Bottom Checkout Bar (Docked Edge-to-Edge) -->
                    @if(!empty($cart))
                        <footer class="p-4 sm:p-5 bg-white/95 backdrop-blur-2xl border-t border-white/80 shadow-[0_-12px_36px_rgba(22,49,68,0.08)] space-y-3 shrink-0 pb-6 sm:pb-8 z-20">
                            <!-- Summary Breakdown -->
                            <div class="space-y-1.5 text-xs">
                                <div class="flex justify-between items-center text-slate-500 font-medium">
                                    <span>Subtotal Item:</span>
                                    <span class="font-black text-[#163144] font-mono">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($this->getNumericDiscountAmount() > 0)
                                    <div class="flex justify-between items-center text-rose-600 font-bold">
                                        <span>Diskon Promo:</span>
                                        <span class="font-mono">- Rp {{ number_format($this->getNumericDiscountAmount(), 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if($this->getNumericTaxAmount() > 0)
                                    <div class="flex justify-between items-center text-[#2c527a] font-bold">
                                        <span>Pajak (PPN):</span>
                                        <span class="font-mono">+ Rp {{ number_format($this->getNumericTaxAmount(), 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="pt-2 border-t border-slate-200/80 flex justify-between items-baseline">
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wider text-[#163144] block">Total Pembayaran</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Sudah termasuk pajak & diskon</span>
                                    </div>
                                    <span class="text-xl sm:text-2xl font-black text-[#163144] font-mono tracking-tight">
                                        Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Lanjut ke Pembayaran -->
                            <button 
                                wire:click="goToPayment"
                                class="w-full h-13 sm:h-14 px-4 sm:px-5 rounded-2xl bg-gradient-to-r from-[#163144] via-[#1c3d55] to-[#244b68] hover:from-[#20445e] hover:to-[#2c5878] active:scale-[0.98] text-white font-black text-sm flex items-center justify-between shadow-xl shadow-[#163144]/25 transition cursor-pointer"
                            >
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Lanjut ke Pembayaran</span>
                                </span>
                                <span class="flex items-center gap-1.5 font-mono font-black text-xs sm:text-sm bg-white/20 px-3 py-1.5 rounded-xl border border-white/25 shadow-2xs">
                                    <span>Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </button>
                        </footer>
                    @endif
                </div>
            @endif

        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= MODAL PILIH PELANGGAN & MEMBER ======================== -->
    <!-- ========================================================================= -->
    @if($showCustomerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl modal-content-animate my-auto">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Pilih Pelanggan / Member</h3>
                            <p class="text-xs text-slate-500 font-medium">Member mendapatkan harga khusus loyalty secara otomatis</p>
                        </div>
                    </div>
                    <button wire:click="closeCustomerModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                    
                    <!-- Search Input -->
                    <div class="relative">
                        <input 
                            wire:model.live.debounce.250ms="customerSearch" 
                            type="text" 
                            placeholder="Cari nama member, nomor WhatsApp, atau kode..." 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:border-[#163144] outline-none transition"
                            autofocus
                        >
                    </div>

                    <!-- Option Pelanggan Umum -->
                    <button 
                        wire:click="selectCustomer(null)" 
                        class="w-full p-3.5 rounded-2xl border transition cursor-pointer text-left flex items-center justify-between
                        {{ is_null($selectedCustomerId) ? 'border-[#163144] bg-[#a0bddb]/20 ring-2 ring-[#163144]/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
                    >
                        <div>
                            <span class="font-black text-[#163144] text-xs block">Pelanggan Umum</span>
                            <span class="text-[11px] text-slate-500">Gunakan harga reguler standar toko</span>
                        </div>
                        @if(is_null($selectedCustomerId))
                            <span class="text-xs font-black text-[#163144] bg-[#163144]/10 px-2.5 py-1 rounded-full">✓ Dipilih</span>
                        @else
                            <span class="text-xs font-bold text-slate-400">Pilih</span>
                        @endif
                    </button>

                    <!-- Header Daftar Member -->
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Daftar Member Terdaftar</span>
                        <button 
                            type="button"
                            wire:click="openQuickMemberModal" 
                            class="text-xs font-extrabold text-[#163144] hover:underline cursor-pointer"
                        >
                            Daftarkan Member Baru
                        </button>
                    </div>

                    <!-- Member List Scrollable -->
                    <div class="max-h-64 overflow-y-auto space-y-2 pr-1">
                        @forelse($customers as $cust)
                            <button 
                                wire:click="selectCustomer({{ $cust->id }})" 
                                class="w-full p-3 rounded-2xl border transition cursor-pointer text-left flex items-center justify-between
                                {{ $selectedCustomerId === $cust->id ? 'border-[#163144] bg-[#a0bddb]/20 ring-2 ring-[#163144]/20' : 'border-slate-200/80 bg-slate-50 hover:bg-white hover:border-[#a0bddb]' }}"
                            >
                                <div class="min-w-0 pr-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-[#163144] text-xs truncate">{{ $cust->name }}</span>
                                        <span class="text-[10px] font-mono font-black bg-[#163144]/10 text-[#163144] px-2 py-0.5 rounded-full">
                                            {{ $cust->member_code }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5">
                                        {{ $cust->phone ? 'WA: ' . $cust->phone : 'Tanpa nomor HP' }}
                                        @if($cust->address) • {{ Str::limit($cust->address, 25) }} @endif
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    @if($selectedCustomerId === $cust->id)
                                        <span class="text-xs font-black text-[#163144] bg-[#163144]/15 px-2.5 py-1 rounded-full">Aktif</span>
                                    @else
                                        <span class="text-xs font-bold text-[#163144]">Pilih</span>
                                    @endif
                                </div>
                            </button>
                        @empty
                            <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                <p>Tidak ditemukan member dengan kata kunci tersebut.</p>
                            </div>
                        @endforelse
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 px-6 bg-white/80 border-t border-slate-100 flex justify-between items-center shrink-0">
                    <button 
                        type="button" 
                        wire:click="openQuickMemberModal" 
                        class="px-5 py-2.5 rounded-full bg-[#163144]/10 hover:bg-[#163144]/20 text-[#163144] font-black text-xs transition active:scale-95 cursor-pointer"
                    >
                        Tambah Member
                    </button>
                    <button 
                        type="button" 
                        wire:click="closeCustomerModal" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= MODAL DAFTAR MEMBER CEPAT ============================= -->
    <!-- ========================================================================= -->
    @if($showQuickMemberModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl modal-content-animate my-auto">
                
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Daftar Member Cepat</h3>
                            <p class="text-xs text-slate-500 font-medium">Daftarkan pelanggan langsung dari kasir</p>
                        </div>
                    </div>
                    <button wire:click="closeQuickMemberModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit="saveQuickMember" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Nama Lengkap Pelanggan <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="quickMemberName" 
                            type="text" 
                            placeholder="Contoh: Ibu Rina / Warung Berkah" 
                            required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border {{ $errors->has('quickMemberName') ? 'border-rose-400' : 'border-slate-200 focus:border-[#163144]' }} rounded-xl text-xs font-bold text-[#163144] outline-none"
                            autofocus
                        >
                        @error('quickMemberName') <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Nomor WhatsApp / HP</label>
                        <input 
                            wire:model="quickMemberPhone" 
                            type="text" 
                            placeholder="081234567890" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-[#163144] rounded-xl text-xs font-semibold text-[#163144] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Alamat Domisili</label>
                        <textarea 
                            wire:model="quickMemberAddress" 
                            rows="2" 
                            placeholder="Alamat lengkap / patokan lokasi..." 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-[#163144] rounded-xl text-xs text-[#163144] font-medium outline-none resize-none"
                        ></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            wire:click="closeQuickMemberModal" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                        >
                            Simpan & Pilih
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= MODAL PILIH DISKON & PROMO ============================ -->
    <!-- ========================================================================= -->
    @if($showDiscountModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl modal-content-animate my-auto">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Pilih Diskon & Promo</h3>
                            <p class="text-xs text-slate-500 font-medium">Pilih dari master promo aktif untuk diterapkan pada transaksi</p>
                        </div>
                    </div>
                    <button wire:click="closeDiscountModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                    
                    <!-- Option: Tanpa Diskon / Reset Diskon -->
                    <button 
                        wire:click="selectDiscount(null)" 
                        class="w-full p-3.5 rounded-2xl border transition cursor-pointer text-left flex items-center justify-between
                        {{ is_null($selectedDiscountId) ? 'border-[#163144] bg-[#a0bddb]/20 ring-2 ring-[#163144]/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
                    >
                        <div>
                            <span class="font-black text-[#163144] text-xs block">Tanpa Promo (Diskon Rp 0)</span>
                            <span class="text-[11px] text-slate-500">Harga standar tanpa potongan diskon tambahan</span>
                        </div>
                        @if(is_null($selectedDiscountId))
                            <span class="text-xs font-black text-[#163144] bg-[#163144]/10 px-2.5 py-1 rounded-full">✓ Dipilih</span>
                        @else
                            <span class="text-xs font-bold text-slate-400">Pilih →</span>
                        @endif
                    </button>

                    <div class="pt-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Daftar Promo Tersedia</span>
                        
                        <div class="space-y-2">
                            @forelse($activeDiscounts as $promo)
                                @php
                                    $calculated = $promo->calculateDiscount($this->subtotal);
                                    $isEligible = $this->subtotal >= (float)$promo->min_purchase;
                                    $isSelected = $selectedDiscountId === $promo->id;
                                @endphp
                                <button 
                                    wire:click="selectDiscount({{ $promo->id }})" 
                                    class="w-full p-3.5 rounded-2xl border transition cursor-pointer text-left flex items-start justify-between gap-3
                                    {{ $isSelected ? 'border-[#163144] bg-[#a0bddb]/20 ring-2 ring-[#163144]/20' : ($isEligible ? 'border-slate-200 bg-slate-50 hover:bg-white hover:border-[#a0bddb]' : 'border-slate-200/60 bg-slate-50/50 opacity-80 hover:opacity-100') }}"
                                >
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-black text-[#163144] text-xs truncate">{{ $promo->name }}</span>
                                            @if($promo->code)
                                                <span class="text-[10px] font-mono font-black bg-[#163144]/10 text-[#163144] px-2 py-0.5 rounded-full">
                                                    {{ $promo->code }}
                                                </span>
                                            @endif
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $promo->type === 'percentage' ? 'bg-[#163144]/10 text-[#163144]' : 'bg-emerald-100 text-emerald-800' }}">
                                                {{ $promo->formatted_value }}
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-slate-500 mt-1">
                                            @if($promo->min_purchase > 0)
                                                <span>Min. belanja: <strong class="text-slate-700">{{ $promo->formatted_min_purchase }}</strong></span>
                                            @else
                                                <span class="text-emerald-600 font-medium">Tanpa min. belanja</span>
                                            @endif

                                            @if($promo->type === 'percentage' && $promo->max_discount_amount)
                                                <span>Maks: <strong class="text-slate-700">Rp {{ number_format($promo->max_discount_amount, 0, ',', '.') }}</strong></span>
                                            @endif

                                            @if($promo->end_date)
                                                <span>s/d {{ $promo->end_date->format('d/m/Y') }}</span>
                                            @endif
                                        </div>

                                        @if(!$isEligible && $this->subtotal > 0)
                                            <div class="text-[10px] font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded mt-1.5 inline-block border border-amber-200">
                                                Kurang Rp {{ number_format($promo->min_purchase - $this->subtotal, 0, ',', '.') }} untuk memenuhi syarat
                                            </div>
                                        @endif
                                    </div>

                                    <div class="text-right shrink-0">
                                        @if($isSelected)
                                            <span class="text-xs font-black text-[#163144] bg-[#163144]/15 px-2.5 py-1 rounded-full block mb-1">✓ Aktif</span>
                                        @else
                                            <span class="text-xs font-bold text-[#163144] block mb-1">Pilih →</span>
                                        @endif

                                        @if($calculated > 0)
                                            <span class="text-xs font-black text-[#163144] block">
                                                - Rp {{ number_format($calculated, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </button>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                    <p>Belum ada master promo / diskon yang aktif di sistem.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Kelola diskon di menu Penjualan > Diskon & Promo.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 px-6 bg-white/80 border-t border-slate-100 flex justify-end shrink-0">
                    <button 
                        type="button" 
                        wire:click="closeDiscountModal" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= MODAL DISKON PER ITEM ================================ -->
    <!-- ========================================================================= -->
    @if($showItemDiscountModal && $editingCartKey && isset($cart[$editingCartKey]))
        @php
            $itemBeingEdited = $cart[$editingCartKey];
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl modal-content-animate my-auto">
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Diskon & Harga Produk</h3>
                            <p class="text-xs text-slate-500 font-medium truncate max-w-[240px]">{{ $itemBeingEdited['name'] }}</p>
                        </div>
                    </div>
                    <button wire:click="closeItemDiscountModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Ringkasan Item & Pilihan Tipe Harga -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500">Harga Satuan Aktif:</span>
                            <span class="font-black text-[#163144] font-mono text-sm">Rp {{ number_format($itemBeingEdited['price'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500">Kuantitas:</span>
                            <span class="font-bold text-slate-800">{{ $itemBeingEdited['qty'] }} {{ $itemBeingEdited['unit_label'] }}</span>
                        </div>

                        <!-- Selector Tipe Harga -->
                        <div class="pt-2 border-t border-slate-200">
                            <label class="block text-[11px] font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Pilih Tipe Harga Item Ini:</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    type="button" 
                                    wire:click="setItemPriceType('{{ $editingCartKey }}', 'regular')" 
                                    class="p-2.5 rounded-xl border text-left transition cursor-pointer {{ ($itemBeingEdited['price_type'] ?? 'regular') === 'regular' ? 'border-[#163144] bg-[#a0bddb]/20 ring-2 ring-[#163144]/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
                                >
                                    <span class="block text-[11px] font-bold text-slate-800">Harga Reguler</span>
                                    <span class="block text-xs font-mono font-black text-[#163144]">Rp {{ number_format($itemBeingEdited['regular_price'], 0, ',', '.') }}</span>
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="setItemPriceType('{{ $editingCartKey }}', 'loyalty')" 
                                    class="p-2.5 rounded-xl border text-left transition cursor-pointer {{ ($itemBeingEdited['price_type'] ?? 'regular') === 'loyalty' ? 'border-amber-500 bg-amber-50/70 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
                                >
                                    <span class="block text-[11px] font-bold text-slate-800">Sales / Member</span>
                                    <span class="block text-xs font-mono font-black text-amber-700">Rp {{ number_format($itemBeingEdited['loyalty_price'], 0, ',', '.') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Mode Diskon -->
                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1.5 uppercase tracking-wider">Jenis Diskon Tambahan</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                type="button" 
                                wire:click="$set('itemDiscountType', 'percent')" 
                                class="py-2.5 px-3 rounded-xl border text-xs font-bold transition cursor-pointer flex items-center justify-center gap-1.5 {{ $itemDiscountType === 'percent' ? 'border-[#163144] bg-[#a0bddb]/20 text-[#163144] ring-2 ring-[#163144]/20 font-black' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}"
                            >
                                <span>% Persentase</span>
                            </button>
                            <button 
                                type="button" 
                                wire:click="$set('itemDiscountType', 'fixed')" 
                                class="py-2.5 px-3 rounded-xl border text-xs font-bold transition cursor-pointer flex items-center justify-center gap-1.5 {{ $itemDiscountType === 'fixed' ? 'border-[#163144] bg-[#a0bddb]/20 text-[#163144] ring-2 ring-[#163144]/20 font-black' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}"
                            >
                                <span>Rp Potongan / Unit</span>
                            </button>
                        </div>
                    </div>

                    <!-- Input Nilai Diskon -->
                    <div>
                        <label class="block text-xs font-extrabold text-[#163144] mb-1 uppercase tracking-wider">
                            {{ $itemDiscountType === 'percent' ? 'Besar Diskon Item (%)' : 'Potongan Nominal per Satuan (Rp)' }}
                        </label>
                        <div class="relative">
                            @if($itemDiscountType === 'fixed')
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-black text-xs">Rp</span>
                            @endif
                            <input 
                                type="number" 
                                step="{{ $itemDiscountType === 'percent' ? '0.5' : '100' }}"
                                min="0" 
                                max="{{ $itemDiscountType === 'percent' ? '100' : '' }}"
                                wire:model="itemDiscountRate" 
                                placeholder="{{ $itemDiscountType === 'percent' ? 'Contoh: 10' : 'Contoh: 2000' }}" 
                                class="w-full py-2.5 {{ $itemDiscountType === 'fixed' ? 'pl-9 pr-3.5' : 'px-3.5' }} rounded-xl border border-slate-200 bg-slate-50 focus:border-[#163144] outline-none text-sm font-black text-[#163144]"
                                autofocus
                            />
                            @if($itemDiscountType === 'percent')
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">%</span>
                            @endif
                        </div>
                    </div>

                    <!-- Quick buttons -->
                    @if($itemDiscountType === 'percent')
                        <div class="flex gap-1.5 flex-wrap">
                            @foreach([5, 10, 15, 20, 25, 50] as $preset)
                                <button 
                                    type="button" 
                                    wire:click="$set('itemDiscountRate', {{ $preset }})" 
                                    class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-[#a0bddb]/20 hover:text-[#163144] text-slate-600 text-xs font-extrabold transition cursor-pointer"
                                >
                                    {{ $preset }}%
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="flex gap-1.5 flex-wrap">
                            @foreach([500, 1000, 2000, 5000, 10000] as $preset)
                                <button 
                                    type="button" 
                                    wire:click="$set('itemDiscountRate', {{ $preset }})" 
                                    class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-[#a0bddb]/20 hover:text-[#163144] text-slate-600 text-[11px] font-extrabold transition cursor-pointer"
                                >
                                    Rp {{ number_format($preset, 0, ',', '.') }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        @if($itemBeingEdited['discount_amount'] > 0)
                            <button 
                                type="button" 
                                wire:click="removeItemDiscount('{{ $editingCartKey }}')" 
                                class="px-4 py-2 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition cursor-pointer"
                            >
                                Hapus Diskon
                            </button>
                        @else
                            <div></div>
                        @endif

                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                wire:click="closeItemDiscountModal" 
                                class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                            >
                                Batal
                            </button>
                            <button 
                                type="button" 
                                wire:click="applyItemDiscount" 
                                class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= MODAL PILIH PAJAK & PPN =============================== -->
    <!-- ========================================================================= -->
    @if($showTaxModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 overflow-hidden flex flex-col shadow-2xl modal-content-animate my-auto">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0 font-black font-mono">
                            %
                        </div>
                        <div>
                            <h3 class="font-black text-base text-[#163144] leading-tight">Pilih Tarif Pajak Penjualan</h3>
                            <p class="text-xs text-slate-500 font-medium">Pilih master pajak aktif untuk diterapkan pada transaksi</p>
                        </div>
                    </div>
                    <button wire:click="closeTaxModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto">
                    
                    <!-- Option: Tanpa Pajak (Non-PPN) -->
                    <button 
                        wire:click="selectTax(null)" 
                        class="w-full p-3.5 rounded-2xl border transition cursor-pointer text-left flex items-center justify-between
                        {{ (!$enableTax || is_null($selectedTaxId)) ? 'border-[#163144] bg-[#a0bddb]/20 ring-2 ring-[#163144]/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
                    >
                        <div>
                            <span class="font-black text-[#163144] text-xs block">Tanpa Pajak (Non-PPN)</span>
                            <span class="text-[11px] text-slate-500">Transaksi dibebaskan dari pungutan pajak (Pajak Rp 0)</span>
                        </div>
                        @if(!$enableTax || is_null($selectedTaxId))
                            <span class="text-xs font-black text-[#163144] bg-[#163144]/10 px-2.5 py-1 rounded-full">✓ Dipilih</span>
                        @else
                            <span class="text-xs font-bold text-slate-400">Pilih</span>
                        @endif
                    </button>

                    <div class="pt-1">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Daftar Pajak Aktif</span>
                        
                        <div class="space-y-2">
                            @forelse($activeTaxes as $tax)
                                @php
                                    $taxable = max(0, $this->subtotal - $this->getNumericDiscountAmount());
                                    $calculated = $tax->calculateTax($taxable);
                                    $isSelected = $enableTax && $selectedTaxId === $tax->id;
                                @endphp
                                <button 
                                    wire:click="selectTax({{ $tax->id }})" 
                                    class="w-full p-3.5 rounded-2xl border transition cursor-pointer text-left flex items-start justify-between gap-3
                                    {{ $isSelected ? 'border-[#163144] bg-[#a0bddb]/20 ring-2 ring-[#163144]/20' : 'border-slate-200 bg-slate-50 hover:bg-white hover:border-[#a0bddb]' }}"
                                >
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-black text-[#163144] text-xs truncate">{{ $tax->name }}</span>
                                            @if($tax->code)
                                                <span class="text-[10px] font-mono font-black bg-[#163144]/10 text-[#163144] px-2 py-0.5 rounded-full">
                                                    {{ $tax->code }}
                                                </span>
                                            @endif
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-mono">
                                                {{ $tax->formatted_rate }}
                                            </span>
                                            @if($tax->is_default)
                                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                    Default Kasir
                                                </span>
                                            @endif
                                        </div>

                                        @if($tax->description)
                                            <p class="text-[11px] text-slate-500 mt-1">{{ $tax->description }}</p>
                                        @endif
                                    </div>

                                    <div class="text-right shrink-0">
                                        @if($isSelected)
                                            <span class="text-xs font-black text-[#163144] bg-[#163144]/15 px-2.5 py-1 rounded-full block mb-1">✓ Aktif</span>
                                        @else
                                            <span class="text-xs font-bold text-[#163144] block mb-1">Pilih</span>
                                        @endif

                                        @if($calculated > 0)
                                            <span class="text-xs font-black text-[#163144] font-mono block">
                                                + Rp {{ number_format($calculated, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </button>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                    <p>Belum ada master tarif pajak aktif di sistem.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Kelola pajak di menu Penjualan & Pelanggan > Pajak Penjualan (PPN).</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 px-6 bg-white/80 border-t border-slate-100 flex justify-end shrink-0">
                    <button 
                        type="button" 
                        wire:click="closeTaxModal" 
                        class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    >
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= STEP 2: HALAMAN PEMBAYARAN & CHECKOUT ================= -->
    <!-- ========================================================================= -->
    @if($currentStep === 'payment')
        <div class="flex-1 flex flex-col overflow-y-auto bg-transparent p-2.5 sm:p-4 lg:p-6">
            
            <div class="max-w-5xl w-full mx-auto space-y-3 sm:space-y-4">
                
                <!-- Nav Header Responsive -->
                <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                        <button 
                            wire:click="backToCatalog" 
                            class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-1 transition cursor-pointer shrink-0"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            <span class="hidden sm:inline">Kembali</span>
                            <span class="sm:hidden">Kembali</span>
                        </button>
                        <div class="min-w-0">
                            <h2 class="text-xs sm:text-base font-black text-slate-900 truncate">Pembayaran Transaksi</h2>
                            <p class="text-[11px] text-slate-500 hidden sm:block">Pilih metode pembayaran dan masukkan nominal</p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-[10px] sm:text-xs text-slate-400 font-semibold block">Total Tagihan:</span>
                        <span class="text-sm sm:text-xl font-black text-blue-600 font-mono">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-5">
                    
                    <!-- Kolom Kiri: Rincian Pesanan (5 cols) -->
                    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 p-3.5 sm:p-5 space-y-3 sm:space-y-4 shadow-xs flex flex-col">
                        
                        <!-- Customer Info Card -->
                        <div class="p-3 sm:p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div class="min-w-0 pr-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Pelanggan Transaksi</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="font-bold text-xs sm:text-sm text-slate-900 truncate">{{ $customerName }}</span>
                                    @if($isLoyalty)
                                        <span class="text-[9px] sm:text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 px-1.5 py-0.2 rounded-md shrink-0">
                                            Member
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <button 
                                wire:click="goToCustomer" 
                                class="px-2.5 py-1 rounded-xl bg-white border border-slate-200 hover:border-blue-400 text-blue-700 font-bold text-xs shadow-2xs transition cursor-pointer shrink-0"
                            >
                                Ganti
                            </button>
                        </div>

                        <!-- Item Breakdown Scrollable -->
                        <div class="flex-1 max-h-48 sm:max-h-72 overflow-y-auto space-y-2 pr-1">
                            <div class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 pb-1 border-b border-slate-100">
                                Rincian Item ({{ count($cart) }})
                            </div>
                            @foreach($cart as $cartKey => $item)
                                <div class="p-2 sm:p-2.5 rounded-xl bg-slate-50/80 border border-slate-200/80 flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-xs text-slate-900 truncate">{{ $item['name'] }}</p>
                                        <p class="text-[11px] text-slate-500 font-mono">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                    
                                    <!-- Pengatur Jumlah Item (+ / -) -->
                                    <div class="flex items-center gap-1 sm:gap-1.5 shrink-0">
                                        <button 
                                            type="button"
                                            wire:click="decrementQty('{{ $cartKey }}')"
                                            class="w-6 h-6 rounded-lg bg-white hover:bg-slate-200 active:bg-slate-300 border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs cursor-pointer active:scale-95 transition shadow-2xs"
                                            title="Kurangi 1"
                                        >
                                            −
                                        </button>
                                        <span class="w-6 text-center font-bold text-xs text-slate-900 font-mono">
                                            {{ $item['qty'] }}
                                        </span>
                                        <button 
                                            type="button"
                                            wire:click="incrementQty('{{ $cartKey }}')"
                                            class="w-6 h-6 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white flex items-center justify-center font-bold text-xs cursor-pointer active:scale-95 transition shadow-2xs"
                                            title="Tambah 1"
                                        >
                                            +
                                        </button>
                                    </div>

                                    <!-- Subtotal & Hapus -->
                                    <div class="text-right shrink-0 min-w-[70px]">
                                        <span class="font-bold text-xs text-slate-900 shrink-0 font-mono block">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </span>
                                        <button 
                                            type="button"
                                            wire:click="removeFromCart('{{ $cartKey }}')"
                                            class="text-[10px] text-rose-500 hover:text-rose-700 hover:underline cursor-pointer font-medium"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Financial Breakdown, Diskon & Pajak di Slide Pembayaran -->
                        <div class="pt-3 border-t border-slate-200 space-y-2.5 sm:space-y-3 text-xs text-slate-600">
                            <div class="flex justify-between items-center text-slate-700 font-medium">
                                <span>Total Belanja (Subtotal):</span>
                                <span class="font-bold text-slate-900 text-xs sm:text-sm font-mono">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                            </div>

                            <!-- Opsi Diskon / Promo Voucher di Slide Pembayaran -->
                            <div class="p-2.5 sm:p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] font-bold text-slate-800 uppercase tracking-wider">
                                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span>Diskon / Promo</span>
                                    </div>
                                    <button 
                                        type="button" 
                                        wire:click="openDiscountModal" 
                                        class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                                    >
                                        {{ $this->selectedDiscount ? 'Ganti Promo' : '' }}
                                    </button>
                                </div>

                                @if($this->selectedDiscount)
                                    <div class="p-2 sm:p-2.5 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-xs text-rose-950 truncate">{{ $this->selectedDiscount->name }}</span>
                                                <span class="text-[9px] sm:text-[10px] font-bold px-1.5 py-0.2 rounded bg-rose-200 text-rose-900 font-mono">
                                                    {{ $this->selectedDiscount->formatted_value }}
                                                </span>
                                            </div>
                                            @if($this->selectedDiscount->min_purchase > 0 && $this->subtotal < $this->selectedDiscount->min_purchase)
                                                <p class="text-[10px] text-rose-600 font-semibold mt-0.5">
                                                    Min. belanja Rp {{ number_format($this->selectedDiscount->min_purchase, 0, ',', '.') }} belum terpenuhi
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="font-bold text-xs text-rose-600 font-mono">
                                                - Rp {{ number_format($this->getNumericDiscountAmount(), 0, ',', '.') }}
                                            </span>
                                            <button 
                                                type="button" 
                                                wire:click="removeDiscount" 
                                                title="Hapus Promo"
                                                class="w-5 h-5 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-100 transition cursor-pointer text-xs"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-2 rounded-xl bg-white border border-dashed border-slate-300 flex justify-between items-center text-xs">
                                        <span class="text-slate-400 italic text-[10px] sm:text-[11px]">Belum ada promo dipakai</span>
                                        <button 
                                            type="button" 
                                            wire:click="openDiscountModal" 
                                            class="text-[11px] font-semibold text-blue-600 hover:underline cursor-pointer"
                                        >
                                            Pakai Voucher
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Opsi Pajak Penjualan (PPN) di Slide Pembayaran -->
                            <div class="p-2.5 sm:p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer select-none">
                                        <input 
                                            type="checkbox" 
                                            wire:click="toggleTax" 
                                            {{ $enableTax ? 'checked' : '' }}
                                            class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
                                        >
                                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-800 uppercase tracking-wider">
                                            Kenakan Pajak (PPN)
                                        </span>
                                    </label>

                                    @if($enableTax)
                                        <button 
                                            type="button" 
                                            wire:click="openTaxModal" 
                                            class="text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                                        >
                                            {{ $selectedTaxId ? 'Ganti Tarif' : '+ Pilih Tarif' }}
                                        </button>
                                    @endif
                                </div>

                                @if($enableTax && $this->selectedTax)
                                    <div class="p-2 sm:p-2.5 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-between">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-xs text-indigo-950 truncate">{{ $this->selectedTax->name }}</span>
                                                <span class="text-[9px] sm:text-[10px] font-bold px-1.5 py-0.2 rounded bg-indigo-200 text-indigo-900 font-mono">
                                                    {{ $this->selectedTax->formatted_rate }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="font-bold text-xs text-indigo-700 font-mono">
                                                + Rp {{ number_format($this->getNumericTaxAmount(), 0, ',', '.') }}
                                            </span>
                                            <button 
                                                type="button" 
                                                wire:click="removeTax" 
                                                title="Nonaktifkan Pajak"
                                                class="w-5 h-5 rounded flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-100 transition cursor-pointer text-xs"
                                            >
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                @elseif($enableTax)
                                    <div class="p-2 rounded-xl bg-amber-50 border border-amber-200 flex justify-between items-center text-xs">
                                        <button type="button" wire:click="openTaxModal" class="text-amber-800 font-bold hover:underline cursor-pointer text-[11px]">
                                            Klik di sini untuk memilih pajak...
                                        </button>
                                        <span class="font-semibold text-slate-500 font-mono">Rp 0</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-between pt-2 border-t border-slate-200 items-baseline">
                                <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-900">Total Tagihan Bersih:</span>
                                <span class="text-base sm:text-xl font-black text-blue-600 font-mono">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                    </div>

                    <!-- Kolom Kanan: Pengaturan Pembayaran (7 cols) -->
                    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 p-3.5 sm:p-5 sm:p-6 space-y-3.5 sm:space-y-5 shadow-xs">
                        
                        <!-- Pilihan Metode Pembayaran (5 Grid Responsive) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-2">
                                <button 
                                    type="button"
                                    wire:click="setPaymentMethod('cash')"
                                    class="py-2.5 sm:py-3 px-2 rounded-2xl font-bold text-xs border transition cursor-pointer text-center flex flex-col items-center justify-center gap-1
                                    {{ $paymentMethod === 'cash' ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-500/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}"
                                >
                                    <span>Tunai</span>
                                </button>
                                <button 
                                    type="button"
                                    wire:click="setPaymentMethod('qris')"
                                    class="py-2.5 sm:py-3 px-2 rounded-2xl font-bold text-xs border transition cursor-pointer text-center flex flex-col items-center justify-center gap-1
                                    {{ $paymentMethod === 'qris' ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-500/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}"
                                >
                                    <span>QRIS</span>
                                </button>
                                <button 
                                    type="button"
                                    wire:click="setPaymentMethod('transfer')"
                                    class="py-2.5 sm:py-3 px-2 rounded-2xl font-bold text-xs border transition cursor-pointer text-center flex flex-col items-center justify-center gap-1
                                    {{ $paymentMethod === 'transfer' ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-500/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}"
                                >
                                    <span>Transfer Bank</span>
                                </button>
                                <button 
                                    type="button"
                                    wire:click="setPaymentMethod('tempo')"
                                    class="py-2.5 sm:py-3 px-2 rounded-2xl font-bold text-xs border transition cursor-pointer text-center flex flex-col items-center justify-center gap-1
                                    {{ $paymentMethod === 'tempo' ? 'bg-amber-600 text-white border-amber-600 shadow-sm ring-2 ring-amber-500/20' : 'bg-amber-50/70 hover:bg-amber-100 text-amber-900 border-amber-200' }}"
                                >
                                    <span>Hutang</span>
                                </button>
                                <button 
                                    type="button"
                                    wire:click="setPaymentMethod('split')"
                                    class="col-span-2 sm:col-span-1 py-2.5 sm:py-3 px-2 rounded-2xl font-bold text-xs border transition cursor-pointer text-center flex flex-col items-center justify-center gap-1
                                    {{ $paymentMethod === 'split' ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-500/20' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}"
                                >
                                    <span>Kombinasi</span>
                                </button>
                            </div>
                        </div>

                        <!-- Notice jika Tempo tapi belum pilih Customer -->
                        @if($paymentMethod === 'tempo' && !$selectedCustomerId)
                            <div class="p-3 sm:p-4 bg-amber-50 border border-amber-300 rounded-2xl flex items-start gap-2.5 sm:gap-3">
                                <div class="w-6 h-6 rounded-lg bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                    !
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-amber-900">Wajib Memilih Pelanggan / Member</p>
                                    <p class="text-[11px] text-amber-700 mt-0.5 leading-snug">Penjualan tempo memerlukan identitas pelanggan untuk pencatatan buku piutang.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Input Nominal Tunai -->
                        @if($paymentMethod === 'cash')
                            <div 
                                class="space-y-3 bg-slate-50 p-3 sm:p-4 rounded-2xl border border-slate-200"
                                x-data="{
                                    displayPaid: '0',
                                    init() {
                                        this.updateDisplay($wire.paidAmount);
                                        $watch('$wire.paidAmount', val => this.updateDisplay(val));
                                    },
                                    updateDisplay(val) {
                                        let num = parseInt(val) || 0;
                                        this.displayPaid = num > 0 ? num.toLocaleString('id-ID') : '0';
                                    },
                                    handleInput(e) {
                                        let clean = e.target.value.replace(/[^0-9]/g, '');
                                        let num = parseInt(clean) || 0;
                                        this.displayPaid = num > 0 ? num.toLocaleString('id-ID') : '0';
                                        $wire.set('paidAmount', num);
                                    }
                                }"
                            >
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nominal Uang Diterima</label>
                                
                                <div class="flex items-stretch rounded-2xl border border-slate-300 bg-white overflow-hidden focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20">
                                    <div class="px-4 bg-slate-100 border-r border-slate-200 flex items-center justify-center font-black text-slate-500 text-base select-none">
                                        Rp
                                    </div>
                                    <input 
                                        type="text" 
                                        inputmode="numeric"
                                        x-model="displayPaid"
                                        @input="handleInput($event)"
                                        @focus="$event.target.select()"
                                        class="w-full px-4 py-2.5 sm:py-3 bg-transparent text-lg sm:text-2xl font-black text-slate-900 outline-none font-mono"
                                        autofocus
                                    >
                                </div>

                                <!-- Quick Buttons -->
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5 sm:gap-2">
                                    <button type="button" wire:click="setQuickPaid({{ $this->grandTotal }})" class="py-2 px-1 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs border border-blue-200 cursor-pointer text-center">
                                        Uang Pas
                                    </button>
                                    <button type="button" wire:click="setQuickPaid(50000)" class="py-2 px-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-semibold text-xs border border-slate-200 cursor-pointer text-center font-mono">
                                        50.000
                                    </button>
                                    <button type="button" wire:click="setQuickPaid(100000)" class="py-2 px-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-semibold text-xs border border-slate-200 cursor-pointer text-center font-mono">
                                        100.000
                                    </button>
                                    <button type="button" wire:click="setQuickPaid(150000)" class="py-2 px-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-semibold text-xs border border-slate-200 cursor-pointer text-center font-mono">
                                        150.000
                                    </button>
                                    <button type="button" wire:click="setQuickPaid(200000)" class="py-2 px-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-semibold text-xs border border-slate-200 cursor-pointer text-center font-mono">
                                        200.000
                                    </button>
                                    <button type="button" wire:click="setQuickPaid(500000)" class="py-2 px-1 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-semibold text-xs border border-slate-200 cursor-pointer text-center font-mono">
                                        500.000
                                    </button>
                                </div>

                                <!-- Kembalian Box -->
                                <div class="p-3 sm:p-3.5 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between">
                                    <div>
                                        <span class="text-xs text-blue-900 font-bold block">Uang Kembalian:</span>
                                        <span class="text-[10px] sm:text-[11px] text-blue-600">Diberikan ke pembeli</span>
                                    </div>
                                    <span class="text-lg sm:text-2xl font-black text-blue-700 font-mono">
                                        Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Input Split / Pembayaran Kombinasi (Tunai + QRIS / Transfer) -->
                        @if($paymentMethod === 'split')
                            <div class="space-y-3.5 bg-slate-50 p-3.5 sm:p-5 rounded-2xl border border-slate-200">
                                
                                <!-- Bagian 1: Porsi Uang Tunai -->
                                <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200 space-y-2.5 shadow-2xs">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                            1. Porsi Uang Tunai
                                        </label>
                                        <span class="text-[11px] font-bold text-slate-500">
                                            Total Tagihan: <strong class="text-blue-700 font-mono">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</strong>
                                        </span>
                                    </div>

                                    <div class="flex items-stretch rounded-xl border border-slate-300 bg-white overflow-hidden focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20">
                                        <div class="px-3.5 bg-slate-100 border-r border-slate-200 flex items-center justify-center font-bold text-slate-500 text-sm select-none">
                                            Rp
                                        </div>
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.300ms="splitCashAmount"
                                            class="w-full px-3.5 py-2.5 bg-transparent text-base sm:text-lg font-black text-slate-900 outline-none font-mono"
                                            placeholder="0"
                                            autofocus
                                        >
                                    </div>

                                    <!-- Quick Cash Presets -->
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase mr-0.5">Cepat:</span>
                                        @foreach([10000, 20000, 50000, 100000, 200000] as $cashPreset)
                                            <button 
                                                type="button" 
                                                wire:click="setSplitCashPreset({{ $cashPreset }})" 
                                                class="px-2 py-1 rounded-lg bg-slate-50 hover:bg-blue-50 hover:text-blue-700 border border-slate-200 text-slate-700 font-semibold text-[11px] font-mono transition cursor-pointer"
                                            >
                                                {{ number_format($cashPreset, 0, ',', '.') }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Bagian 2: Porsi Non-Tunai -->
                                <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200 space-y-2.5 shadow-2xs">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                            2. Porsi Non-Tunai
                                        </label>

                                        <!-- Toggle QRIS vs Transfer -->
                                        <div class="flex items-center gap-1 p-0.5 rounded-lg bg-slate-100 border border-slate-200">
                                            <button 
                                                type="button" 
                                                wire:click="$set('splitNonCashMethod', 'qris')" 
                                                class="px-2.5 py-1 rounded-md text-xs font-bold cursor-pointer transition {{ $splitNonCashMethod === 'qris' ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                                            >
                                                QRIS
                                            </button>
                                            <button 
                                                type="button" 
                                                wire:click="$set('splitNonCashMethod', 'transfer')" 
                                                class="px-2.5 py-1 rounded-md text-xs font-bold cursor-pointer transition {{ $splitNonCashMethod === 'transfer' ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}"
                                            >
                                                Transfer Bank
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-stretch rounded-xl border border-slate-300 bg-white overflow-hidden focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20">
                                        <div class="px-3.5 bg-slate-100 border-r border-slate-200 flex items-center justify-center font-bold text-slate-500 text-sm select-none">
                                            Rp
                                        </div>
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.300ms="splitNonCashAmount"
                                            class="w-full px-3.5 py-2.5 bg-transparent text-base sm:text-lg font-black text-blue-700 outline-none font-mono"
                                            placeholder="0"
                                        >
                                        <button 
                                            type="button" 
                                            wire:click="fillSplitNonCashRemainder"
                                            class="px-3 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs border-l border-blue-200 transition cursor-pointer shrink-0 flex items-center gap-1"
                                            title="Hitung sisa otomatis ke non-tunai"
                                        >
                                            <span>Isi Sisa</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Ringkasan & Kalkulasi Selisih Split -->
                                @php
                                    $cleanSplitCash = is_numeric($splitCashAmount) ? (float)$splitCashAmount : (float)preg_replace('/[^0-9]/', '', (string)$splitCashAmount);
                                    $cleanSplitNonCash = is_numeric($splitNonCashAmount) ? (float)$splitNonCashAmount : (float)preg_replace('/[^0-9]/', '', (string)$splitNonCashAmount);
                                    $totalSplitPaid = $cleanSplitCash + $cleanSplitNonCash;
                                    $splitDiff = $totalSplitPaid - (float)$this->grandTotal;
                                @endphp
                                <div class="p-3 sm:p-3.5 rounded-xl bg-blue-50 border border-blue-200 space-y-1.5 text-xs">
                                    <div class="flex justify-between items-center text-slate-600">
                                        <span>Total Terbayar (Tunai + {{ strtoupper($splitNonCashMethod) }}):</span>
                                        <span class="font-bold font-mono text-sm text-slate-900">Rp {{ number_format($totalSplitPaid, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="pt-1 border-t border-blue-200 flex justify-between items-center font-bold {{ $splitDiff < 0 ? 'text-rose-600' : ($splitDiff == 0 ? 'text-emerald-700' : 'text-blue-700') }}">
                                        <span>{{ $splitDiff < 0 ? 'Kurang Bayar:' : ($splitDiff == 0 ? 'Pembayaran Pas:' : 'Kembalian Tunai:') }}</span>
                                        <span class="font-mono text-base">
                                            Rp {{ number_format(abs($splitDiff), 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Notice QRIS / Transfer -->
                        @if($paymentMethod === 'qris' || $paymentMethod === 'transfer')
                            <div class="p-3.5 sm:p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-between">
                                <div class="pr-2">
                                    <p class="text-xs font-bold text-blue-900">Pembayaran Non-Tunai ({{ strtoupper($paymentMethod) }})</p>
                                    <p class="text-[11px] text-blue-700 mt-0.5">Pastikan dana telah berhasil diterima di rekening / QRIS toko.</p>
                                </div>
                                <span class="text-base sm:text-lg font-black text-blue-700 font-mono shrink-0">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <!-- Form Khusus Tempo (DP & Jatuh Tempo) -->
                        @if($paymentMethod === 'tempo')
                            <div class="space-y-3 sm:space-y-4 bg-amber-50/60 p-3.5 sm:p-5 rounded-2xl border border-amber-200">
                                
                                <!-- DP Input -->
                                <div 
                                    x-data="{
                                        displayPaid: '0',
                                        init() {
                                            this.updateDisplay($wire.paidAmount);
                                            $watch('$wire.paidAmount', val => this.updateDisplay(val));
                                        },
                                        updateDisplay(val) {
                                            let num = parseInt(val) || 0;
                                            this.displayPaid = num > 0 ? num.toLocaleString('id-ID') : '0';
                                        },
                                        handleInput(e) {
                                            let clean = e.target.value.replace(/[^0-9]/g, '');
                                            let num = parseInt(clean) || 0;
                                            this.displayPaid = num > 0 ? num.toLocaleString('id-ID') : '0';
                                            $wire.set('paidAmount', num);
                                        }
                                    }"
                                >
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Uang Muka / DP Diterima</label>
                                        <span class="text-[10px] sm:text-[11px] font-semibold text-slate-500">Bisa Rp 0</span>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-slate-400 text-base">Rp</span>
                                        <input 
                                            type="text" 
                                            inputmode="numeric"
                                            x-model="displayPaid"
                                            @input="handleInput($event)"
                                            @focus="$event.target.select()"
                                            class="w-full pl-11 pr-4 py-2 sm:py-2.5 bg-white border border-amber-300 rounded-xl text-base sm:text-lg font-black text-slate-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none"
                                        >
                                    </div>

                                    <!-- Quick DP Buttons -->
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 mt-2">
                                        <button type="button" wire:click="setQuickPaid(0)" class="py-1.5 px-1 rounded-lg bg-white hover:bg-amber-100 text-amber-900 font-bold text-xs border border-amber-200 cursor-pointer">
                                            DP Rp 0
                                        </button>
                                        <button type="button" wire:click="setQuickPaid({{ (int) round($this->grandTotal * 0.25) }})" class="py-1.5 px-1 rounded-lg bg-white hover:bg-amber-100 text-amber-900 font-bold text-xs border border-amber-200 cursor-pointer">
                                            DP 25%
                                        </button>
                                        <button type="button" wire:click="setQuickPaid({{ (int) round($this->grandTotal * 0.5) }})" class="py-1.5 px-1 rounded-lg bg-white hover:bg-amber-100 text-amber-900 font-bold text-xs border border-amber-200 cursor-pointer">
                                            DP 50%
                                        </button>
                                        <button type="button" wire:click="setQuickPaid({{ $this->grandTotal }})" class="py-1.5 px-1 rounded-lg bg-white hover:bg-amber-100 text-amber-900 font-bold text-xs border border-amber-200 cursor-pointer">
                                            Lunas
                                        </button>
                                    </div>
                                </div>

                                <!-- Ringkasan Sisa Hutang Piutang -->
                                @php
                                    $dpNumeric = $this->getNumericPaidAmount();
                                    $remainingDebt = max(0, $this->grandTotal - $dpNumeric);
                                @endphp
                                <div class="p-3 sm:p-3.5 rounded-xl bg-white border border-amber-200 space-y-1 text-xs">
                                    <div class="flex justify-between text-slate-600">
                                        <span>Total Tagihan:</span>
                                        <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-blue-700 font-semibold">
                                        <span>Uang Muka (DP):</span>
                                        <span class="font-mono">Rp {{ number_format($dpNumeric, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between pt-1.5 border-t border-slate-100 font-black text-amber-800">
                                        <span>SISA PIUTANG (BON):</span>
                                        <span class="text-sm sm:text-base font-mono">Rp {{ number_format($remainingDebt, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Tanggal Jatuh Tempo -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Jatuh Tempo</label>
                                    <input 
                                        wire:model="tempoDueDate" 
                                        type="date" 
                                        class="w-full px-3.5 py-2 sm:py-2.5 bg-white border border-amber-300 rounded-xl text-xs font-bold text-slate-800 outline-none focus:border-amber-500"
                                    >
                                    <div class="grid grid-cols-3 gap-1.5 mt-2">
                                        <button type="button" wire:click="setTempoDueDays(7)" class="py-1.5 px-1 bg-white hover:bg-amber-100 text-amber-900 rounded-lg text-xs font-bold border border-amber-200 cursor-pointer">
                                            +7 Hari
                                        </button>
                                        <button type="button" wire:click="setTempoDueDays(14)" class="py-1.5 px-1 bg-white hover:bg-amber-100 text-amber-900 rounded-lg text-xs font-bold border border-amber-200 cursor-pointer">
                                            +14 Hari
                                        </button>
                                        <button type="button" wire:click="setTempoDueDays(30)" class="py-1.5 px-1 bg-white hover:bg-amber-100 text-amber-900 rounded-lg text-xs font-bold border border-amber-200 cursor-pointer">
                                            +30 Hari
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @endif

                        <!-- Catatan Struk (Opsional) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Struk (Opsional)</label>
                            <input 
                                wire:model="paymentNotes" 
                                type="text" 
                                placeholder="Keterangan tambahan untuk pesanan ini..." 
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-blue-500"
                            >
                        </div>

                        <!-- Action Submit Payment -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2.5 sm:gap-3">
                            <button 
                                type="button" 
                                wire:click="backToCatalog" 
                                class="px-3 sm:px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 font-bold text-xs transition cursor-pointer shrink-0"
                            >
                                Batal
                            </button>

                            <button 
                                type="button" 
                                wire:click="processPayment"
                                wire:loading.attr="disabled"
                                @if($paymentMethod === 'tempo' && !$selectedCustomerId) disabled @endif
                                class="flex-1 py-3 sm:py-3.5 px-4 sm:px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-black text-xs sm:text-sm transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-600/20 text-center"
                            >
                                <span wire:loading.remove>
                                    {{ $paymentMethod === 'tempo' ? 'Simpan Penjualan Tempo' : 'Selesaikan Transaksi' }}
                                </span>
                                <span wire:loading>Memproses Transaksi...</span>
                            </button>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ================= STEP 3: HALAMAN STRUK & SELESAI ======================= -->
    <!-- ========================================================================= -->
    @if($currentStep === 'receipt' && $lastTransaction)
        @php
            $receiptSetting = \App\Models\ReceiptSetting::getSettings();
            $receiptPaperSize = $receiptSetting->default_paper_size ?? '58mm';
        @endphp
        <div 
            class="flex-1 flex flex-col overflow-y-auto bg-transparent p-2.5 sm:p-4 lg:p-8"
        >
            <div class="max-w-4xl w-full mx-auto space-y-3 sm:space-y-5">
                
                <!-- Success Header Banner -->
                <div class="p-3.5 sm:p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-lg sm:text-xl shrink-0 shadow-2xs">
                            ✓
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-xs sm:text-base font-black text-slate-900 leading-tight">Transaksi Berhasil</h2>
                                <span class="text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $lastTransaction->payment_status === 'paid' ? 'Lunas' : ($lastTransaction->payment_status === 'partial' ? 'Sebagian' : 'Tempo') }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-mono mt-0.5 truncate">Invoice: <strong class="text-slate-800">{{ $lastTransaction->invoice_number }}</strong> • {{ $lastTransaction->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <button 
                        wire:click="startNewTransaction" 
                        class="w-full sm:w-auto px-4 sm:px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-2 shrink-0"
                    >
                        <span>Transaksi Baru</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-5 items-start">
                    
                    <!-- Kolom Kiri: Preview Struk Thermal (6 cols) -->
                    <div class="lg:col-span-6 flex justify-center">
                        
                        <!-- Printable Thermal Receipt Paper -->
                        <div 
                            id="printable-receipt"
                            class="w-full bg-white rounded-xl border border-slate-300 shadow-md text-neutral-900 transition-all duration-150 select-text {{ $receiptPaperSize === '80mm' ? 'max-w-[340px] p-4 text-xs' : 'max-w-[270px] p-3 text-[11px]' }}"
                            style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Courier New', Courier; line-height: 1.35;"
                        >
                            
                            <!-- Header Toko / Cabang -->
                            <div class="text-center mb-1.5 space-y-0.5">
                                <h3 class="text-sm sm:text-base font-bold tracking-tight text-black leading-tight uppercase">
                                    {{ $receiptSetting->store_name ?: 'Syafa Frozen Food' }}
                                </h3>
                                @if($lastTransaction->outlet?->name)
                                    <div class="text-[9.5px] text-neutral-600 font-medium leading-tight">
                                        {{ $lastTransaction->outlet->name }}
                                    </div>
                                @endif
                                @php
                                    $receiptAddress = $lastTransaction->outlet?->address ?: $receiptSetting->store_address;
                                    $receiptPhone = $lastTransaction->outlet?->phone ?: $receiptSetting->store_phone;
                                    $receiptHeaderNotes = $lastTransaction->outlet?->receipt_header ?: $receiptSetting->header_notes;
                                    $receiptFooterNotes = $lastTransaction->outlet?->receipt_footer ?: $receiptSetting->footer_notes;
                                @endphp
                                @if($receiptAddress)
                                    <p class="text-[9px] leading-tight text-neutral-700 px-0.5">
                                        {{ $receiptAddress }}
                                    </p>
                                @endif
                                @if($receiptPhone)
                                    <p class="text-[9px] text-neutral-700">
                                        No.Tlp {{ $receiptPhone }}
                                    </p>
                                @endif
                                @if($receiptHeaderNotes)
                                    <p class="text-[9px] text-neutral-700">
                                        {{ $receiptHeaderNotes }}
                                    </p>
                                @endif
                            </div>

                            <!-- Divider 1 -->
                            <div class="border-b border-dashed border-slate-400 my-1.5"></div>

                            <!-- Info Tanggal, Kasir & No. Invoice -->
                            <div class="text-[10px] text-neutral-900 flex justify-between items-start leading-tight">
                                <div>
                                    <div>{{ $lastTransaction->created_at->format('Y-m-d') }}</div>
                                    <div>{{ $lastTransaction->created_at->format('H:i:s') }}</div>
                                </div>
                                <div class="text-right">
                                    @if($receiptSetting->show_cashier_name)
                                        <div class="truncate max-w-[130px]">Kasir : {{ $lastTransaction->user->name ?? '-' }}</div>
                                    @endif
                                    <div class="font-mono text-[9px]">{{ $lastTransaction->invoice_number }}</div>
                                </div>
                            </div>

                            @if($receiptSetting->show_customer_name && $lastTransaction->customer_name)
                                <div class="text-[9.5px] text-neutral-900 mt-0.5">
                                    Pelanggan : {{ $lastTransaction->customer_name }} {{ $lastTransaction->is_loyalty ? '(Member)' : '' }}
                                </div>
                            @endif

                            <!-- Divider 2 -->
                            <div class="border-b border-dashed border-slate-400 my-1.5"></div>

                            <!-- Daftar Item Transaksi -->
                            <div class="space-y-1.5 text-[10px] text-neutral-900">
                                @php $totalItemQty = 0; @endphp
                                @foreach($lastTransaction->items as $item)
                                    @php $totalItemQty += $item->quantity; @endphp
                                    <div>
                                        <div class="font-bold uppercase text-[9.5px] leading-tight mb-0.5 flex items-center justify-between">
                                            <span>{{ $item->product_name }}</span>
                                            <div class="flex items-center gap-1">
                                                @if($item->price_type === 'loyalty')
                                                    <span class="text-[7.5px] font-bold bg-amber-100 text-amber-900 border border-amber-300 px-0.5 rounded">SALES</span>
                                                @endif
                                                @if($item->isBox())
                                                    <span class="text-[8px] font-black bg-neutral-200 px-0.5 rounded">DUS</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center text-neutral-800">
                                            <span>
                                                @if($item->isBox())
                                                    {{ $item->quantity }} Dus (@ {{ $item->pieces_per_unit }} {{ $item->product?->unit ?? 'pcs' }}) &nbsp;x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                                @else
                                                    {{ $item->quantity }} &nbsp;x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                                @endif
                                            </span>
                                            <span class="font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        @if($item->discount_amount > 0)
                                            <div class="flex justify-between text-[8.5px] text-neutral-600 italic pl-1">
                                                <span>↳ Potongan: -Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</span>
                                                <span class="line-through text-neutral-400">Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Divider 3 -->
                            <div class="border-b border-dashed border-slate-400 my-1.5"></div>

                            <!-- Kalkulasi & Total Transaksi -->
                            <div class="space-y-0.5 text-[10px] text-neutral-900">
                                <div class="flex justify-between">
                                    <span>Total QTY : {{ $totalItemQty }}</span>
                                    <span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Sub Total</span>
                                    <span>Rp {{ number_format($lastTransaction->subtotal, 0, ',', '.') }}</span>
                                </div>

                                @if($lastTransaction->discount_amount > 0)
                                    <div class="flex justify-between text-neutral-700 text-[9px]">
                                        <span>Diskon Promo {{ $lastTransaction->discount ? "({$lastTransaction->discount->name})" : '' }}</span>
                                        <span>- Rp {{ number_format($lastTransaction->discount_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @if($lastTransaction->tax_amount > 0)
                                    <div class="flex justify-between text-neutral-700 text-[9px]">
                                        <span>Pajak {{ $lastTransaction->tax_rate > 0 ? '(' . number_format($lastTransaction->tax_rate, 1) . '%)' : '' }}</span>
                                        <span>+ Rp {{ number_format($lastTransaction->tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between font-bold text-[11px] pt-1">
                                    <span>Total</span>
                                    <span>Rp {{ number_format($lastTransaction->grand_total, 0, ',', '.') }}</span>
                                </div>

                                @if($lastTransaction->payment_method === 'tempo')
                                    <div class="flex justify-between pt-0.5">
                                        <span>Metode</span>
                                        <span class="font-bold">TEMPO</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Uang Muka (DP)</span>
                                        <span>Rp {{ number_format($lastTransaction->paid_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between font-bold">
                                        <span>Sisa Piutang</span>
                                        <span>Rp {{ number_format($lastTransaction->remaining_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Jatuh Tempo</span>
                                        <span>{{ $lastTransaction->due_date ? $lastTransaction->due_date->format('d/m/Y') : '-' }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between pt-0.5">
                                        <span>Bayar ({{ ucfirst($lastTransaction->payment_method) }})</span>
                                        <span>Rp {{ number_format($lastTransaction->paid_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Kembalian</span>
                                        <span>Rp {{ number_format($lastTransaction->change_amount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Divider 4 -->
                            <div class="border-b border-dashed border-slate-400 my-1.5"></div>

                            <!-- Footer Struk -->
                            <div class="text-center text-[9px] text-neutral-800 space-y-0.5 pt-0.5">
                                @if($receiptSetting->footer_link)
                                    <div>Link Kritik dan Saran:</div>
                                    <div class="font-normal break-all">{{ $receiptSetting->footer_link }}</div>
                                @endif

                                @if($receiptFooterNotes)
                                    <div class="pt-0.5 leading-tight">{{ $receiptFooterNotes }}</div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <!-- Kolom Kanan: Ringkasan & Tombol Aksi (6 cols) -->
                    <div class="lg:col-span-6 space-y-3 sm:space-y-4">
                        
                        <!-- Rincian Ringkasan Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-3.5 sm:p-5 space-y-3 shadow-xs">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-xs sm:text-sm text-slate-900">Ringkasan Transaksi</h3>
                                <span class="text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 uppercase font-mono">
                                    {{ $lastTransaction->payment_method }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 sm:gap-3 text-xs">
                                <div class="p-2.5 sm:p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Total Belanja</span>
                                    <span class="font-black text-blue-700 text-sm sm:text-base font-mono">Rp {{ number_format($lastTransaction->grand_total, 0, ',', '.') }}</span>
                                </div>
                                <div class="p-2.5 sm:p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Metode Bayar</span>
                                    <span class="font-bold text-slate-800 text-xs sm:text-sm uppercase">{{ $lastTransaction->payment_method }}</span>
                                </div>
                                <div class="p-2.5 sm:p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Uang Diterima</span>
                                    <span class="font-bold text-slate-800 text-xs sm:text-sm font-mono">Rp {{ number_format($lastTransaction->paid_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="p-2.5 sm:p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase tracking-wider font-bold block">{{ $lastTransaction->payment_method === 'tempo' ? 'Sisa Piutang' : 'Kembalian' }}</span>
                                    <span class="font-black {{ $lastTransaction->payment_method === 'tempo' ? 'text-amber-700' : 'text-emerald-700' }} text-xs sm:text-sm font-mono">
                                        Rp {{ number_format($lastTransaction->payment_method === 'tempo' ? $lastTransaction->remaining_amount : $lastTransaction->change_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Opsi Cetak & Berbagi Struk Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-3 sm:p-4 space-y-2.5 shadow-xs">
                            <h3 class="font-bold text-xs text-slate-800">Opsi Cetak & Berbagi Struk</h3>

                            <div class="grid grid-cols-3 gap-1.5 sm:gap-2">
                                <button 
                                    type="button" 
                                    @click="printThermalReceipt('printable-receipt', '{{ $receiptPaperSize }}')" 
                                    class="py-2 px-1.5 sm:px-2.5 rounded-lg sm:rounded-xl bg-slate-900 hover:bg-slate-800 active:bg-black text-white font-semibold text-[11px] sm:text-xs cursor-pointer flex items-center justify-center gap-1 sm:gap-1.5 shadow-2xs transition"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    <span class="truncate">Cetak Struk</span>
                                </button>

                                <button 
                                    type="button" 
                                    onclick="downloadReceiptImage('printable-receipt', '{{ $lastTransaction->invoice_number }}')"
                                    class="py-2 px-1.5 sm:px-2.5 rounded-lg sm:rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-semibold text-[11px] sm:text-xs cursor-pointer flex items-center justify-center gap-1 sm:gap-1.5 transition border border-slate-200/80"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    <span class="truncate">Unduh Gambar</span>
                                </button>

                                <a 
                                    href="{{ $lastTransaction->getWhatsAppUrl($whatsappPhone) }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="py-2 px-1.5 sm:px-2.5 rounded-lg sm:rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-[11px] sm:text-xs flex items-center justify-center gap-1 sm:gap-1.5 transition cursor-pointer shadow-2xs shadow-emerald-600/20 text-center"
                                >
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    <span class="truncate">Kirim WA</span>
                                </a>
                            </div>

                            <div class="pt-2 border-t border-slate-100">
                                <button 
                                    type="button" 
                                    wire:click="startNewTransaction" 
                                    class="w-full py-2.5 sm:py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-xs cursor-pointer flex items-center justify-center gap-2 transition"
                                >
                                    <span>Transaksi Baru (F1)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL PETUNJUK & UNDUH APLIKASI (PWA) ================= -->
    <div 
        x-show="showPwaModal" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate"
        @keydown.escape.window="showPwaModal = false"
    >
        <div 
            @click.outside="showPwaModal = false"
            class="glass-card-plek bg-white/95 rounded-[32px] shadow-2xl border border-white/90 w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] modal-content-animate my-auto"
        >
            <!-- Header Modal -->
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-[#163144] leading-tight">Unduh & Pasang Aplikasi POS</h3>
                        <p class="text-xs text-slate-500 font-medium">Bisa dibuka langsung tanpa buka browser</p>
                    </div>
                </div>
                <button 
                    @click="showPwaModal = false" 
                    type="button"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                >
                    ✕
                </button>
            </div>

            <!-- Body Modal -->
            <div class="p-6 overflow-y-auto space-y-4 text-xs text-slate-600">
                <!-- Status PWA / Tombol Pasang Langsung -->
                <div class="p-4 rounded-2xl bg-[#a0bddb]/15 border border-[#a0bddb]/40 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="w-10 h-10 object-contain p-1 shrink-0 drop-shadow-xs">
                        <div class="min-w-0">
                            <p class="font-black text-[#163144] text-xs truncate">Syafa Frozen POS</p>
                            <p class="text-[11px] text-slate-500 font-medium">Versi Ringan & Cepat (PWA)</p>
                        </div>
                    </div>
                    <button 
                        @click="triggerDirectInstall()" 
                        type="button" 
                        class="px-5 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 shrink-0 cursor-pointer flex items-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Pasang Sekarang</span>
                    </button>
                </div>

                <!-- Petunjuk Berdasarkan Perangkat -->
                <div>
                    <p class="font-black text-[#163144] text-xs uppercase tracking-wider mb-2.5">Panduan Pemasangan Manual:</p>
                    
                    <!-- Tabs Selector -->
                    <div class="flex rounded-full bg-slate-100 p-1 gap-1 border border-slate-200 mb-3.5">
                        <button 
                            type="button" 
                            @click="pwaTab = 'android'" 
                            :class="pwaTab === 'android' ? 'bg-[#163144] text-white font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="flex-1 py-1.5 text-center text-xs rounded-full transition cursor-pointer flex items-center justify-center gap-1"
                        >
                            📱 Android
                        </button>
                        <button 
                            type="button" 
                            @click="pwaTab = 'ios'" 
                            :class="pwaTab === 'ios' ? 'bg-[#163144] text-white font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="flex-1 py-1.5 text-center text-xs rounded-full transition cursor-pointer flex items-center justify-center gap-1"
                        >
                            🍏 iPhone/iPad
                        </button>
                        <button 
                            type="button" 
                            @click="pwaTab = 'desktop'" 
                            :class="pwaTab === 'desktop' ? 'bg-[#163144] text-white font-black shadow-xs' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="flex-1 py-1.5 text-center text-xs rounded-full transition cursor-pointer flex items-center justify-center gap-1"
                        >
                            💻 Laptop/PC
                        </button>
                    </div>

                    <!-- Android Guide -->
                    <div x-show="pwaTab === 'android'" class="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <p>Buka halaman ini di browser <strong>Google Chrome</strong> pada HP Android Anda.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <p>Tekan tombol menu titik tiga (<strong>⋮</strong>) di pojok kanan atas browser.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <p>Pilih opsi <strong>"Instal aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>.</p>
                        </div>
                    </div>

                    <!-- iOS Safari Guide -->
                    <div x-show="pwaTab === 'ios'" class="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <p>Buka halaman ini menggunakan browser <strong>Safari</strong> di iPhone/iPad.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <p>Ketuk tombol <strong>Share / Bagikan</strong> (<span class="font-mono font-bold">⎋</span> ikon panah ke atas) di menu bawah browser.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <p>Gulir ke bawah lalu pilih <strong>"Tambah ke Layar Utama" (Add to Home Screen)</strong>.</p>
                        </div>
                    </div>

                    <!-- Laptop / PC Guide -->
                    <div x-show="pwaTab === 'desktop'" class="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <p>Gunakan browser <strong>Google Chrome</strong> atau <strong>Microsoft Edge</strong>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <p>Lihat di sebelah kanan kolom URL (Address Bar), klik ikon <strong>Install Aplikasi</strong> (ikon komputer dengan panah ke bawah).</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#163144] text-white font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <p>Atau klik menu titik tiga (<strong>⋮</strong>) > <strong>Simpan dan Bagikan</strong> > <strong>Instal Syafa POS</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="p-4 px-6 bg-white/80 border-t border-slate-100 flex justify-end shrink-0">
                <button 
                    @click="showPwaModal = false" 
                    type="button" 
                    class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ================= MODAL PEMILIHAN CABANG KASIR ================= -->
    @if($showOutletModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 overflow-hidden shadow-2xl flex flex-col max-h-[90vh] modal-content-animate my-auto">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-base text-[#163144] leading-tight">Pilih Cabang Kasir</h2>
                            <p class="text-xs text-slate-500 font-medium">Pilih cabang toko tempat kasir beroperasi</p>
                        </div>
                    </div>
                    @if($activeOutletId)
                        <button 
                            type="button" 
                            wire:click="closeOutletModal" 
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black"
                        >
                            ✕
                        </button>
                    @endif
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-3 overflow-y-auto">
                    <p class="text-xs text-slate-500 font-medium">
                        Silakan pilih cabang untuk memuat harga dan stok yang sesuai:
                    </p>

                    <div class="space-y-2.5 pt-1">
                        @foreach($allOutlets as $outlet)
                            @php
                                $isSelected = ($activeOutletId === $outlet->id);
                            @endphp
                            <button 
                                type="button" 
                                wire:click="selectOutlet({{ $outlet->id }})" 
                                class="w-full p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-center justify-between group
                                {{ $isSelected 
                                    ? 'border-[#163144] bg-[#a0bddb]/20 shadow-xs ring-2 ring-[#163144]/20' 
                                    : 'border-slate-200 bg-white hover:border-[#a0bddb] hover:bg-slate-50' }}"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 font-black text-xs
                                        {{ $isSelected ? 'bg-[#163144] text-white shadow-sm' : 'bg-slate-100 text-[#163144] group-hover:bg-[#a0bddb]/30' }}">
                                        {{ substr($outlet->code ?: 'CB', 0, 3) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <h3 class="font-black text-xs sm:text-sm text-[#163144] transition truncate">
                                                {{ $outlet->name }}
                                            </h3>
                                            @if($outlet->is_main)
                                                <span class="px-2 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] font-black text-[9px] uppercase">Pusat</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-black text-[9px] uppercase">Cabang</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5 font-mono truncate">
                                            Kode: {{ $outlet->code }} {{ $outlet->address ? '• ' . Str::limit($outlet->address, 25) : '' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="shrink-0 pl-2">
                                    @if($isSelected)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#163144] text-white font-black text-[11px] shadow-xs">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-slate-100 group-hover:bg-[#163144] group-hover:text-white text-slate-600 font-bold text-[11px] transition">
                                            Pilih
                                        </span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Footer if active outlet already exists -->
                @if($activeOutletId)
                    <div class="p-4 px-6 bg-white/80 border-t border-slate-100 flex items-center justify-end shrink-0">
                        <button 
                            type="button" 
                            wire:click="closeOutletModal" 
                            class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                        >
                            Tutup
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif

    <!-- ================= MODAL BUKA SHIFT KASIR ================= -->
    @if($showOpenShiftModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 shadow-2xl flex flex-col modal-content-animate my-auto overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-base text-[#163144] leading-tight">Buka Shift Kasir Baru</h2>
                            <p class="text-xs text-slate-500 font-medium">Kasir: <strong class="text-[#163144]">{{ auth()->user()->name }}</strong></p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeOpenShiftModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit="startShift" class="p-5 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">
                            Modal Kas Awal di Laci (Uang Kembalian) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-stretch rounded-2xl border border-slate-300 bg-slate-50/80 focus-within:bg-white focus-within:border-[#163144] focus-within:ring-2 focus-within:ring-[#163144]/20 overflow-hidden shadow-2xs transition">
                            <span class="inline-flex items-center px-4 bg-slate-100 border-r border-slate-200 text-[#163144] font-black text-sm select-none">
                                Rp
                            </span>
                            <input 
                                type="number" 
                                wire:model="shiftStartingCash" 
                                required
                                class="w-full px-4 py-3 bg-transparent border-0 text-base sm:text-lg font-black text-[#163144] outline-none font-mono"
                                placeholder="0"
                                autofocus
                            >
                        </div>
                        
                        <!-- Quick Starting Cash Buttons -->
                        <div class="flex items-center gap-1.5 flex-wrap mt-2.5">
                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-wider mr-1">Pilihan Cepat:</span>
                            @foreach([100000, 150000, 200000, 300000, 500000] as $startPreset)
                                <button 
                                    type="button" 
                                    wire:click="setShiftStartingCashPreset({{ $startPreset }})" 
                                    class="px-3 py-1 rounded-full bg-slate-100 hover:bg-[#a0bddb]/30 hover:text-[#163144] hover:border-[#a0bddb] border border-slate-200 text-slate-700 font-black text-xs font-mono transition active:scale-95 cursor-pointer shadow-2xs"
                                >
                                    {{ number_format($startPreset, 0, ',', '.') }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">Catatan Buka Shift (Opsional)</label>
                        <textarea 
                            wire:model="shiftOpeningNotes" 
                            rows="2" 
                            class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition shadow-2xs"
                            placeholder="Contoh: Shift Pagi / Modal kembalian pecahan 2rb, 5rb, 10rb lengkap."
                        ></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" wire:click="closeOpenShiftModal" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">
                            Mulai Sesi Shift Kasir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL TUTUP SHIFT KASIR (LAPORAN Z) ================= -->
    @if($showCloseShiftModal && $this->activeShift)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 shadow-2xl flex flex-col modal-content-animate my-auto overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-sm sm:text-base text-[#163144] leading-tight">Tutup Shift & Rekap Kas Laci</h2>
                            <p class="text-[11px] text-slate-500 mt-0.5 font-medium">Kasir: <strong class="text-[#163144]">{{ $this->activeShift->user?->name }}</strong> • {{ $this->activeShift->outlet?->name }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeCloseShiftModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-xs font-black">✕</button>
                </div>

                <form wire:submit="submitCloseShift" class="p-5 space-y-3.5">
                    <!-- Shift Financial Calculations -->
                    <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-2 text-xs shadow-2xs">
                        <div class="flex justify-between text-slate-500 text-[11px]">
                            <span>Mulai: <strong class="text-[#163144] font-mono">{{ $this->activeShift->opened_at->translatedFormat('d/m/Y H:i') }}</strong></span>
                            <span>Modal Awal: <strong class="text-[#163144] font-mono">Rp {{ number_format($this->activeShift->starting_cash, 0, ',', '.') }}</strong></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 text-emerald-900">
                                <span class="text-[10px] uppercase font-black text-emerald-700 block">Penjualan Tunai</span>
                                <span class="font-black font-mono text-xs">Rp {{ number_format($this->activeShift->total_cash_sales, 0, ',', '.') }}</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-[#a0bddb]/20 border border-[#a0bddb]/40 text-[#163144]">
                                <span class="text-[10px] uppercase font-black text-[#163144] block">Non-Tunai (QRIS/Trf)</span>
                                <span class="font-black font-mono text-xs">Rp {{ number_format($this->activeShift->total_qris_sales + $this->activeShift->total_transfer_sales, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-slate-200 text-xs font-black text-[#163144] items-baseline">
                            <span>Ekspektasi Kas di Laci:</span>
                            <span class="font-mono text-sm text-[#163144]">Rp {{ number_format($this->activeShift->expected_ending_cash, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Input Hitungan Fisik -->
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider">
                            Hasil Hitung Uang Fisik di Laci Kasir <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-stretch rounded-2xl border border-slate-300 bg-white focus-within:border-[#163144] focus-within:ring-2 focus-within:ring-[#163144]/20 overflow-hidden shadow-2xs transition">
                            <span class="inline-flex items-center px-4 bg-slate-100 border-r border-slate-200 text-[#163144] font-black text-xs select-none">
                                Rp
                            </span>
                            <input 
                                type="number" 
                                wire:model.live.debounce.300ms="shiftActualEndingCash" 
                                required
                                class="w-full px-4 py-2.5 bg-transparent border-0 text-base font-black text-[#163144] outline-none font-mono"
                                placeholder="0"
                                autofocus
                            >
                        </div>
                        
                        @php
                            $diff = (float)$shiftActualEndingCash - (float)$this->activeShift->expected_ending_cash;
                        @endphp
                        <div class="p-2.5 rounded-xl border flex items-center justify-between text-xs font-bold shadow-2xs
                            {{ $diff == 0 ? 'bg-emerald-50/80 text-emerald-800 border-emerald-200' : ($diff < 0 ? 'bg-rose-50/80 text-rose-800 border-rose-200' : 'bg-[#a0bddb]/20 text-[#163144] border-[#a0bddb]/40') }}">
                            <span class="text-[11px] font-black">Selisih (Fisik vs Sistem):</span>
                            <span class="font-mono text-xs sm:text-sm font-black">
                                {{ $diff > 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }} 
                                <span class="text-[10px] font-bold">({{ $diff == 0 ? 'Pas' : ($diff < 0 ? 'Kas Kurang' : 'Kas Lebih') }})</span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">Catatan Penutupan Shift</label>
                        <textarea 
                            wire:model="shiftClosingNotes" 
                            rows="2" 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition shadow-2xs"
                            placeholder="Contoh: Serah terima ke shift malam, uang dimasukkan ke brankas."
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" wire:click="closeCloseShiftModal" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">
                            Konfirmasi Tutup Shift & Laporan Z
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL STRUK REKAP LAPORAN Z (SELESAI TUTUP SHIFT) ================= -->
    @if($showShiftReceiptModal && $lastClosedShift)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-sm w-full border border-white/90 shadow-2xl flex flex-col modal-content-animate my-auto overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-700 border border-emerald-500/20 flex items-center justify-center font-black">✓</div>
                        <div>
                            <h3 class="font-black text-sm text-[#163144]">Shift Berhasil Ditutup</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Rekapitulasi Laporan Z Kasir</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeShiftReceiptModal" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-xs font-black">✕</button>
                </div>

                <div class="p-4 sm:p-5 space-y-3.5 text-xs">
                    <!-- Thermal Receipt Shift Format -->
                    <div id="shift-summary-print" class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90 font-mono text-[11px] space-y-2">
                        <div class="text-center font-black text-[#163144]">
                            <p class="text-xs uppercase">REKAP TUTUP SHIFT (LAPORAN Z)</p>
                            <p class="text-[10px] text-slate-500 font-medium">{{ $lastClosedShift->outlet?->name ?? 'Syafa Frozen Food' }}</p>
                        </div>
                        <div class="border-b border-dashed border-slate-300"></div>
                        <div class="flex justify-between"><span>Kasir:</span><span class="font-bold text-slate-800">{{ $lastClosedShift->user?->name }}</span></div>
                        <div class="flex justify-between"><span>Buka:</span><span>{{ $lastClosedShift->opened_at->format('d/m/y H:i') }}</span></div>
                        <div class="flex justify-between"><span>Tutup:</span><span>{{ $lastClosedShift->closed_at->format('d/m/y H:i') }}</span></div>
                        <div class="border-b border-dashed border-slate-300"></div>
                        <div class="flex justify-between"><span>Modal Awal:</span><span class="font-bold">Rp {{ number_format($lastClosedShift->starting_cash, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between font-bold text-emerald-800"><span>Penjualan Cash:</span><span>Rp {{ number_format($lastClosedShift->total_cash_sales, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Total Non-Cash:</span><span>Rp {{ number_format($lastClosedShift->total_qris_sales + $lastClosedShift->total_transfer_sales, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Total Struk:</span><span>{{ $lastClosedShift->total_transactions_count }} Nota</span></div>
                        <div class="border-b border-dashed border-slate-300"></div>
                        <div class="flex justify-between font-black text-slate-800"><span>Kas Sistem:</span><span>Rp {{ number_format($lastClosedShift->expected_ending_cash, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between font-black text-slate-900"><span>Kas Fisik Laci:</span><span>Rp {{ number_format($lastClosedShift->actual_ending_cash, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between font-black {{ $lastClosedShift->cash_difference < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                            <span>Selisih:</span>
                            <span>{{ $lastClosedShift->cash_difference > 0 ? '+' : '' }}Rp {{ number_format($lastClosedShift->cash_difference, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 pt-1">
                        <button 
                            type="button" 
                            onclick="window.print()" 
                            class="flex-1 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] active:bg-[#163144] text-white font-black text-xs text-center cursor-pointer shadow-md shadow-[#163144]/20 transition active:scale-95"
                        >
                            Cetak Rekap
                        </button>
                        <button 
                            type="button" 
                            wire:click="closeShiftReceiptModal" 
                            class="flex-1 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs text-center cursor-pointer transition active:scale-95 shadow-2xs"
                        >
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL TAHAN TRANSAKSI (HOLD) ================= -->
    @if($showHoldModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 shadow-2xl flex flex-col modal-content-animate my-auto overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-base text-[#163144] leading-tight">Tahan Transaksi (Hold Antrean)</h2>
                            <p class="text-xs text-slate-500 font-medium">{{ count($cart) }} item • Total <strong class="text-[#163144] font-black">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</strong></p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeHoldModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit="confirmHoldCart" class="p-5 sm:p-6 space-y-4">
                    <div class="p-3.5 rounded-2xl bg-[#a0bddb]/15 border border-[#a0bddb]/30 text-xs text-[#163144] leading-relaxed">
                        Keranjang belanja saat ini akan disimpan sementara ke antrean. Kasir langsung siap melayani pembeli berikutnya dan dapat memuat kembali transaksi ini kapan saja.
                    </div>

                    <div>
                        <label class="block text-xs font-black text-[#163144] uppercase tracking-wider mb-1.5">
                            Nama Pembeli / Catatan Antrean
                        </label>
                        <input 
                            type="text" 
                            wire:model="holdCustomerNote" 
                            placeholder="Contoh: Ibu Siti - ambil sosis tambahan / Antrean #2"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition"
                            autofocus
                        >
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" wire:click="closeHoldModal" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">
                            Simpan & Tahan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL DAFTAR ANTREAN TERTANAH (HOLD LIST) ================= -->
    @if($showHoldListModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 shadow-2xl flex flex-col max-h-[85vh] modal-content-animate my-auto overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-base text-[#163144] leading-tight">Daftar Antrean Ditahan</h2>
                            <p class="text-xs text-slate-500 font-medium">{{ count($holdTransactions) }} antrean transaksi menunggu dilanjutkan</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeHoldListModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <div class="p-5 space-y-3 overflow-y-auto flex-1">
                    @forelse($holdTransactions as $hold)
                        <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:border-[#a0bddb] transition flex items-center justify-between gap-3 shadow-2xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-[#163144] text-xs sm:text-sm truncate">{{ $hold->customer_name }}</span>
                                    <span class="font-mono text-[10px] font-black px-2.5 py-0.5 rounded-full bg-[#163144]/10 text-[#163144] border border-[#163144]/15 shrink-0">{{ $hold->hold_code }}</span>
                                </div>
                                <p class="text-xs text-slate-600 font-mono mt-1">
                                    {{ count($hold->cart_data ?? []) }} Item • Total: <strong class="text-[#163144] font-black">Rp {{ number_format($hold->grand_total, 0, ',', '.') }}</strong>
                                </p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    Ditahan: {{ $hold->created_at->diffForHumans() }} ({{ $hold->created_at->format('H:i') }})
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button 
                                    type="button" 
                                    wire:click="resumeHoldTransaction({{ $hold->id }})" 
                                    class="px-4 py-2 rounded-full bg-[#163144] hover:bg-[#2c527a] active:bg-[#163144] text-white font-black text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                                >
                                    Lanjutkan
                                </button>
                                <button 
                                    type="button" 
                                    @click="window.customConfirm ? window.customConfirm({ title: 'Hapus Antrean', message: 'Apakah Anda yakin ingin membatalkan dan menghapus antrean transaksi ini?', confirmText: 'Ya, Hapus', type: 'danger', onConfirm: () => $wire.deleteHoldTransaction({{ $hold->id }}) }) : $wire.deleteHoldTransaction({{ $hold->id }})"
                                    class="w-8 h-8 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition active:scale-95 cursor-pointer text-xs font-black"
                                    title="Hapus Antrean"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400">
                            <p class="text-3xl mb-2">⏸️</p>
                            <p class="text-xs font-bold text-slate-600">Tidak ada transaksi yang sedang ditahan.</p>
                            <p class="text-[11px] text-slate-400 mt-1">Gunakan tombol "Tahan" pada keranjang belanja untuk menahan antrean.</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 sm:p-5 bg-white/80 border-t border-slate-100 flex items-center justify-end shrink-0">
                    <button type="button" wire:click="closeHoldListModal" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MODAL TAMBAH PRODUK CEPAT (POS FAST ACTION) ================= -->
    @if($showQuickProductModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-lg w-full border border-white/90 shadow-2xl flex flex-col modal-content-animate my-auto overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-sm sm:text-base text-[#163144] leading-tight">Tambah Produk Baru Cepat</h2>
                            <p class="text-[11px] text-slate-500 font-medium">Langsung aktif & siap dijual di <strong class="text-[#163144]">{{ $this->activeOutlet?->name ?? 'Cabang Aktif' }}</strong></p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeQuickProductModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-sm font-black">✕</button>
                </div>

                <form wire:submit="saveQuickProduct" class="p-5 sm:p-6 space-y-4">
                    <!-- Nama Produk -->
                    <div>
                        <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                            Nama Produk / Barang <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="quickProductName" 
                            required
                            placeholder="Contoh: Sosis Bakar Jumbo 500g"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition"
                            autofocus
                        >
                        @error('quickProductName') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Barcode & Kategori (2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                                Barcode / Kode (Opsional)
                            </label>
                            <input 
                                type="text" 
                                wire:model="quickProductBarcode" 
                                placeholder="Scan barcode..."
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs font-mono text-slate-900 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition"
                            >
                            @error('quickProductBarcode') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                                Kategori Produk
                            </label>
                            <select 
                                wire:model="quickProductCategoryId"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs font-bold text-slate-800 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition cursor-pointer"
                            >
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('quickProductCategoryId') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Harga Modal & Harga Jual (2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                                Harga Modal / Beli (HPP)
                            </label>
                            <div class="flex items-stretch rounded-2xl border border-slate-300 bg-slate-50/80 focus-within:bg-white focus-within:border-[#163144] focus-within:ring-2 focus-within:ring-[#163144]/20 overflow-hidden transition">
                                <span class="inline-flex items-center px-3.5 bg-slate-100 border-r border-slate-200 text-[#163144] font-black text-xs select-none">Rp</span>
                                <input 
                                    type="number" 
                                    wire:model="quickProductCostPrice" 
                                    placeholder="0"
                                    class="w-full px-3.5 py-2.5 bg-transparent border-0 text-xs sm:text-sm font-bold text-slate-900 outline-none font-mono"
                                >
                            </div>
                            @error('quickProductCostPrice') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                                Harga Jual Reguler <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-stretch rounded-2xl border border-slate-300 bg-slate-50/80 focus-within:bg-white focus-within:border-[#163144] focus-within:ring-2 focus-within:ring-[#163144]/20 overflow-hidden transition">
                                <span class="inline-flex items-center px-3.5 bg-slate-100 border-r border-slate-200 text-[#163144] font-black text-xs select-none">Rp</span>
                                <input 
                                    type="number" 
                                    wire:model="quickProductSellingPrice" 
                                    required
                                    placeholder="0"
                                    class="w-full px-3.5 py-2.5 bg-transparent border-0 text-xs sm:text-sm font-bold text-slate-900 outline-none font-mono"
                                >
                            </div>
                            @error('quickProductSellingPrice') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Satuan & Stok Awal (2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                                Satuan Barang <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                wire:model="quickProductUnit"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs font-bold text-slate-800 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition cursor-pointer"
                            >
                                <option value="pack">Pack</option>
                                <option value="pcs">Pcs</option>
                                <option value="kg">Kg</option>
                                <option value="dus">Dus</option>
                                <option value="gram">Gram</option>
                                <option value="botol">Botol</option>
                                <option value="ikat">Ikat</option>
                            </select>
                            @error('quickProductUnit') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                                Stok Awal di Cabang Ini
                            </label>
                            <input 
                                type="number" 
                                wire:model="quickProductStock" 
                                placeholder="0"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none font-mono transition"
                            >
                            @error('quickProductStock') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Auto Add to Cart Checkbox -->
                    <div class="p-3.5 rounded-2xl bg-[#a0bddb]/15 border border-[#a0bddb]/30 flex items-center gap-2.5">
                        <input 
                            type="checkbox" 
                            id="quickProductAutoAddToCart" 
                            wire:model="quickProductAutoAddToCart"
                            class="w-4 h-4 text-[#163144] border-slate-300 rounded focus:ring-[#163144] cursor-pointer"
                        >
                        <label for="quickProductAutoAddToCart" class="text-xs font-black text-[#163144] cursor-pointer select-none">
                            Otomatis masukkan produk ini ke keranjang setelah disimpan
                        </label>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" wire:click="closeQuickProductModal" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">
                            Simpan & Jual Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= MODAL TAMBAH STOK CEPAT (POS FAST ACTION) ================= -->
    @if($showQuickStockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-[#0e1e2b]/55 backdrop-blur-md overflow-y-auto modal-backdrop-animate">
            <div class="glass-card-plek bg-white/95 rounded-[32px] max-w-md w-full border border-white/90 shadow-2xl flex flex-col modal-content-animate my-auto overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white/60 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#a0bddb]/30 flex items-center justify-center text-[#163144] border border-[#a0bddb]/40 shadow-2xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-black text-sm sm:text-base text-[#163144] leading-tight">Tambah Stok Cepat</h2>
                            <p class="text-[11px] text-slate-500 font-medium truncate max-w-[240px]">{{ $quickStockProductName }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeQuickStockModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition active:scale-95 cursor-pointer text-xs font-black">✕</button>
                </div>

                <form wire:submit="saveQuickStock" class="p-5 space-y-3.5">
                    <!-- Info Produk & Sisa Stok -->
                    <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-200/90 flex items-center justify-between shadow-2xs">
                        <div>
                            <span class="text-[10px] uppercase font-black text-slate-400 block">Stok Saat Ini (Cabang Aktif)</span>
                            <span class="font-black font-mono text-base {{ $quickStockCurrentStock <= 0 ? 'text-rose-600' : 'text-[#163144]' }}">
                                {{ $quickStockCurrentStock }} {{ $quickStockProductUnit }}
                            </span>
                        </div>
                        <span class="text-[10px] font-black px-3 py-1 rounded-full {{ $quickStockCurrentStock <= 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $quickStockCurrentStock <= 0 ? 'Stok Habis' : 'Tersedia' }}
                        </span>
                    </div>

                    <!-- Input Jumlah Tambahan Stok -->
                    <div>
                        <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">
                            Jumlah Tambahan Stok <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-stretch rounded-2xl border border-slate-300 bg-slate-50/80 focus-within:bg-white focus-within:border-[#163144] focus-within:ring-2 focus-within:ring-[#163144]/20 overflow-hidden shadow-2xs transition">
                            <span class="inline-flex items-center px-4 bg-slate-100 border-r border-slate-200 text-[#163144] font-black text-sm select-none">+</span>
                            <input 
                                type="number" 
                                wire:model="quickStockQuantity" 
                                required
                                min="1"
                                placeholder="Contoh: 10"
                                class="w-full px-4 py-2.5 bg-transparent border-0 text-base font-black text-[#163144] outline-none font-mono"
                                autofocus
                            >
                        </div>
                        @error('quickStockQuantity') <span class="text-rose-600 text-[11px] font-bold mt-0.5 block">{{ $message }}</span> @enderror

                        <!-- Preset Pilihan Cepat -->
                        <div class="flex items-center gap-1.5 flex-wrap mt-2.5">
                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-wider mr-1">Cepat:</span>
                            @foreach([5, 10, 20, 50, 100] as $presetQty)
                                <button 
                                    type="button" 
                                    wire:click="setQuickStockPreset({{ $presetQty }})" 
                                    class="px-3 py-1 rounded-full bg-slate-100 hover:bg-[#a0bddb]/30 hover:text-[#163144] hover:border-[#a0bddb] border border-slate-200 text-slate-700 font-black text-xs font-mono transition active:scale-95 cursor-pointer shadow-2xs"
                                >
                                    +{{ $presetQty }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Catatan / Alasan -->
                    <div>
                        <label class="block text-[11px] font-black text-[#163144] uppercase tracking-wider mb-1">Catatan / Alasan</label>
                        <input 
                            type="text" 
                            wire:model="quickStockNotes" 
                            placeholder="Contoh: Restock cepat dari kasir / freezer baru diisi"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-[#163144] focus:ring-2 focus:ring-[#163144]/20 outline-none transition shadow-2xs"
                        >
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" wire:click="closeQuickStockModal" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition active:scale-95 cursor-pointer shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#163144] hover:bg-[#2c527a] text-white font-black text-xs shadow-md shadow-[#163144]/20 transition active:scale-95 hover:scale-105 cursor-pointer">
                            Simpan & Tambah Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ================= CONTINUOUS AUTOFOCUS SCRIPT ENGINE ================= -->
    <script>
        (function() {
            function refocusPosSearch(force = false) {
                // Pencarian TIDAK lagi aktif otomatis. Hanya fokus jika diminta manual (F2).
                if (force !== 'manual') return;

                const input = document.getElementById('pos-search-input');
                if (!input) return;

                // Jangan rebut fokus jika modal member/pelanggan/PWA/cabang sedang terbuka
                const activeModal = document.querySelector('.fixed.z-50:not([style*="display: none"]):not([style*="display:none"])');
                if (activeModal) {
                    return;
                }

                // Jangan rebut fokus jika sedang mengedit input diskon atau input lainnya
                const active = document.activeElement;
                if (!force && active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.tagName === 'SELECT' || active.tagName === 'BUTTON')) {
                    if (active === input) return;
                    if (active.id && active.id !== 'pos-search-input') return;
                    if (active.closest && active.closest('.fixed')) return;
                }

                input.focus();
            }

            window.refocusPosSearch = refocusPosSearch;

            function attachPosFocusListeners() {
                // Inisialisasi awal
                setTimeout(() => refocusPosSearch(true), 50);
                setTimeout(() => refocusPosSearch(true), 150);
                setTimeout(() => refocusPosSearch(true), 350);

                // Livewire 3 commit & morph hooks
                if (typeof Livewire !== 'undefined') {
                    Livewire.hook('commit', ({ succeed }) => {
                        succeed(() => {
                            setTimeout(() => refocusPosSearch(), 30);
                            setTimeout(() => refocusPosSearch(), 100);
                        });
                    });

                    Livewire.hook('morph.updated', () => {
                        setTimeout(() => refocusPosSearch(), 30);
                    });

                    Livewire.on('focus-search', () => {
                        setTimeout(() => refocusPosSearch(true), 20);
                        setTimeout(() => refocusPosSearch(true), 80);
                    });
                }
            }

            // Global Document Click / Mouseup: kembalikan fokus ke pencarian setelah klik produk / tombol keranjang
            document.addEventListener('click', function(e) {
                if (e.target.closest('.fixed.z-50') || e.target.closest('input[x-model="displayDiscount"]')) {
                    return;
                }
                setTimeout(() => refocusPosSearch(), 40);
                setTimeout(() => refocusPosSearch(), 120);
            }, true);

            document.addEventListener('mouseup', function(e) {
                if (e.target.closest('.fixed.z-50') || e.target.closest('input[x-model="displayDiscount"]')) {
                    return;
                }
                setTimeout(() => refocusPosSearch(), 40);
                setTimeout(() => refocusPosSearch(), 120);
            }, true);

            // (Auto-redirect keypress ke kolom pencarian dinonaktifkan; gunakan F2 atau klik kolom pencarian)

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', attachPosFocusListeners);
            } else {
                attachPosFocusListeners();
            }

            document.addEventListener('livewire:navigated', attachPosFocusListeners);
        })();

        function posTerminalData() {
            return {
                timeString: '',
                deferredPrompt: null,
                showPwaModal: false,
                pwaTab: 'desktop',
                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // Deteksi platform awal untuk tab panduan PWA
                    const ua = navigator.userAgent || '';
                    if (/iPad|iPhone|iPod/.test(ua) && !window.MSStream) {
                        this.pwaTab = 'ios';
                    } else if (/android/i.test(ua)) {
                        this.pwaTab = 'android';
                    } else {
                        this.pwaTab = 'desktop';
                    }

                    // Listener event instalasi PWA
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                    });

                    window.addEventListener('appinstalled', () => {
                        this.deferredPrompt = null;
                        this.showPwaModal = false;
                    });
                    
                    // Auto focus pencarian saat pertama masuk halaman kasir
                    this.focusSearch(true);
                    setTimeout(() => this.focusSearch(true), 100);
                    setTimeout(() => this.focusSearch(true), 300);

                    // Listener event dari Livewire (setelah input barang / scan barcode / ganti step)
                    window.addEventListener('focus-search', () => {
                        this.focusSearch(true);
                    });

                    if (typeof Livewire !== 'undefined') {
                        Livewire.on('focus-search', () => {
                            this.focusSearch(true);
                        });
                    }

                    document.addEventListener('livewire:navigated', () => {
                        this.focusSearch(true);
                    });

                    this.$watch('$wire.showCustomerModal', (val) => {
                        if (!val) {
                            setTimeout(() => this.focusSearch(true), 50);
                        }
                    });

                    this.$watch('$wire.showQuickMemberModal', (val) => {
                        if (!val) {
                            setTimeout(() => this.focusSearch(true), 50);
                        }
                    });

                    this.$watch('$wire.showDiscountModal', (val) => {
                        if (!val) {
                            setTimeout(() => this.focusSearch(true), 50);
                        }
                    });

                    this.$watch('$wire.currentStep', (val) => {
                        if (val === 'catalog') {
                            setTimeout(() => this.focusSearch(true), 50);
                        }
                    });

                    window.addEventListener('keydown', (e) => {
                        const currentStep = @js($currentStep);

                        if (e.key === 'F1') {
                            e.preventDefault();
                            if (currentStep === 'receipt') {
                                @this.call('startNewTransaction');
                            } else if (currentStep !== 'catalog') {
                                @this.call('backToCatalog');
                            }
                        } else if (e.key === 'F2') {
                            if (currentStep === 'catalog') {
                                e.preventDefault();
                                this.focusSearch('manual');
                            }
                        } else if (e.key === 'F3') {
                            e.preventDefault();
                            if (currentStep !== 'receipt') {
                                @this.call('goToCustomer');
                            }
                        } else if (e.key === 'F4') {
                            e.preventDefault();
                            if (currentStep !== 'receipt') {
                                @this.call('openDiscountModal');
                            }
                        } else if (e.key === 'F8') {
                            if (currentStep === 'receipt') {
                                e.preventDefault();
                                window.print();
                            }
                        } else if (e.key === 'F9') {
                            e.preventDefault();
                            if (currentStep === 'catalog') {
                                @this.call('goToPayment');
                            } else if (currentStep === 'payment') {
                                @this.call('processPayment');
                            }
                        } else if (e.key === 'Escape') {
                            if (this.showPwaModal) {
                                this.showPwaModal = false;
                            } else if (currentStep === 'customer' || currentStep === 'payment') {
                                @this.call('backToCatalog');
                            } else if (currentStep === 'receipt') {
                                @this.call('startNewTransaction');
                            }
                        } else if (e.key === 'Enter') {
                            if (currentStep === 'receipt') {
                                e.preventDefault();
                                @this.call('startNewTransaction');
                            }
                        }
                    });
                },
                openInstallModal() {
                    if (this.deferredPrompt) {
                        this.triggerDirectInstall();
                    } else {
                        this.showPwaModal = true;
                    }
                },
                async triggerDirectInstall() {
                    if (this.deferredPrompt) {
                        this.deferredPrompt.prompt();
                        const { outcome } = await this.deferredPrompt.userChoice;
                        if (outcome === 'accepted') {
                            this.showPwaModal = false;
                        }
                        this.deferredPrompt = null;
                    } else {
                        this.showPwaModal = true;
                    }
                },
                updateClock() {
                    const now = new Date();
                    this.timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
                },
                focusSearch(force = false) {
                    this.$nextTick(() => {
                        if (typeof window.refocusPosSearch === 'function') {
                            window.refocusPosSearch(force);
                        } else {
                            setTimeout(() => {
                                const activeModal = document.querySelector('.fixed.z-50:not([style*="display: none"])');
                                if (activeModal && activeModal.querySelector('input, select, textarea')) {
                                    return;
                                }
                                const active = document.activeElement;
                                if (!force && active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.tagName === 'SELECT')) {
                                    if (active.id === 'pos-search-input') return;
                                    if (active.closest && active.closest('.fixed')) return;
                                }
                                const input = document.getElementById('pos-search-input') || this.$refs.posSearchInput;
                                if (input) {
                                    input.focus();
                                }
                            }, 30);
                        }
                    });
                }
            };
        }
    </script>

</div>
