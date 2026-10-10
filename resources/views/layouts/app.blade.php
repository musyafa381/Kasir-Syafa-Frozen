<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} - Syafa Frozen Food</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-badge.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#163144">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Syafa POS">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Urbanist Global Override — All Weights (100-900) */
        body, html, *, *::before, *::after,
        input, select, textarea, button, optgroup,
        h1, h2, h3, h4, h5, h6, p, span, a, label, th, td, li,
        .font-sans, .font-mono, .font-serif,
        [class*="font-"] {
            font-family: 'Urbanist', system-ui, -apple-system, BlinkMacSystemFont, sans-serif !important;
            font-feature-settings: 'liga' 1, 'calt' 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Tabular Numbers for consistent number widths */
        .font-mono, [class*="font-mono"],
        td, th, .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full text-slate-800 antialiased selection:bg-[#a0bddb]/40 selection:text-[#163144] font-sans p-0 m-0 min-h-screen" 
      :class="sidebarOpen ? 'overflow-hidden md:overflow-auto' : ''"
      x-data="{ sidebarOpen: false, isScrolled: false }"
      x-effect="if (window.innerWidth < 768) { document.documentElement.classList.toggle('overflow-hidden', sidebarOpen); document.body.classList.toggle('overflow-hidden', sidebarOpen); }"
      @scroll.window="isScrolled = (window.pageYOffset > 15)">

    <!-- Full-Width Top Navbar (Fixed Edge-to-Edge from Far Left to Far Right) -->
    <header 
        class="fixed top-0 left-0 right-0 z-50 w-full glass-navbar transition-all duration-250"
        :class="isScrolled ? 'is-scrolled' : ''"
    >
        <div class="w-full flex items-center justify-between px-3.5 sm:px-6 lg:px-8 h-11 sm:h-12">
            <!-- Left: Mobile Toggle + Brand + Page Title -->
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <button 
                    type="button"
                    @click="sidebarOpen = !sidebarOpen" 
                    class="md:hidden p-1 rounded-lg hover:bg-white/50 transition text-slate-700 hover:text-[#163144] cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <a href="{{ route('admin.dashboard') }}" wire:navigate.hover class="flex items-center gap-2 sm:gap-2.5 group">
                    <img src="{{ asset('images/logo.svg') }}" alt="Syafa Frozen Food" class="w-7 h-7 sm:w-8 sm:h-8 object-contain transition-transform duration-300 group-hover:scale-105 drop-shadow-xs">
                    <span class="text-xs sm:text-[13px] font-black text-[#163144] tracking-tight uppercase">Syafa Frozen Food</span>
                </a>

                <div class="h-3.5 w-px bg-slate-400/50 hidden sm:block"></div>

                <h2 class="text-[11px] sm:text-xs font-extrabold text-slate-700 truncate hidden sm:block">
                    {{ $title ?? 'Dashboard' }}
                </h2>
            </div>

            <!-- Right: User Info & Quick Logout -->
            <div x-data="{ userMenuOpen: false }" class="relative flex items-center gap-2 sm:gap-2.5" @click.outside="userMenuOpen = false">
                <button 
                    type="button" 
                    @click="userMenuOpen = !userMenuOpen"
                    class="flex items-center gap-1.5 sm:gap-2 p-0.5 sm:px-2 sm:py-1 rounded-full hover:bg-white/60 transition cursor-pointer"
                    title="Menu Akun & Logout"
                >
                    <span class="hidden md:inline text-xs font-bold text-[#163144]">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <div class="relative">
                        <div class="w-7 h-7 rounded-full bg-white/85 text-[#163144] font-black text-[11px] flex items-center justify-center border border-white shadow-2xs uppercase">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 border border-white absolute -bottom-0.5 -right-0.5"></span>
                    </div>
                    <svg class="w-3 h-3 text-slate-500 transition-transform duration-200 hidden sm:block" :class="userMenuOpen ? 'rotate-180 text-[#163144]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="userMenuOpen" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute right-0 top-full mt-1.5 w-48 rounded-2xl bg-white/95 backdrop-blur-xl border border-white shadow-xl shadow-[#163144]/15 p-1.5 z-50 text-slate-700"
                >
                    <div class="px-2.5 py-1.5 border-b border-slate-100 mb-1">
                        <p class="text-xs font-black text-[#163144] truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-[10px] text-slate-400 capitalize font-medium">{{ auth()->user()->role ?? 'Kasir' }}</p>
                    </div>
                    <a href="{{ route('pos.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-[#163144] transition">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>POS Kasir</span>
                    </a>
                    <button 
                        type="button" 
                        @click="userMenuOpen = false; window.customConfirm ? window.customConfirm({ title: 'Konfirmasi Keluar', message: 'Apakah Anda yakin ingin mengakhiri sesi kasir dan keluar dari sistem aplikasi?', confirmText: 'Ya, Keluar', onConfirm: () => document.getElementById('logout-form').submit() }) : document.getElementById('logout-form').submit()"
                        class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-xl text-xs font-extrabold text-rose-600 hover:bg-rose-50 transition cursor-pointer text-left"
                    >
                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar (Logout)</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container Layout (Sidebar + Content Below Fixed Navbar) -->
    <div class="w-full flex-1 flex flex-col md:flex-row relative pt-11 sm:pt-12">
        
        <!-- Mobile Sidebar Backdrop (Smooth Blur & Fade) -->
        <div 
            x-show="sidebarOpen" 
            x-cloak
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false" 
            @touchmove.prevent
            @wheel.prevent
            class="fixed inset-0 bg-[#0e1e2b]/55 z-40 md:hidden backdrop-blur-md"
        ></div>

        <!-- Sleek Frosted Sidebar (Smooth Spring Slide, Elegant on Desktop & Mobile) -->
        <aside 
            x-data="{ 
                searchMenu: '',
                activeDropdown: @js(
                    request()->routeIs('admin.transactions.*', 'admin.shifts.*', 'admin.returns.*', 'admin.reports.index', 'admin.receivables.*') ? 'sales' :
                    (request()->routeIs('admin.customers.*', 'admin.discounts.*', 'admin.taxes.*') ? 'marketing' :
                    (request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.stock.*', 'admin.reports.inventory-valuation') ? 'products' :
                    (request()->routeIs('admin.purchases.*', 'admin.suppliers.*', 'admin.reports.accounts-payable') ? 'purchases' :
                    (request()->routeIs('admin.reports.profit-loss', 'admin.expenses.*', 'admin.assets.*', 'admin.employees.*', 'admin.finance.ai-advisor') ? 'finance' :
                    (request()->routeIs('admin.outlets.*', 'admin.backup.*', 'admin.users.*', 'admin.settings.*') ? 'outlets' : 'sales')))))
                ),
                toggle(menu) {
                    this.activeDropdown = (this.activeDropdown === menu) ? null : menu;
                },
                isOpen(menu) {
                    if (this.searchMenu && this.searchMenu.trim().length > 0) return true;
                    return this.activeDropdown === menu;
                },
                matches(text) {
                    if (!this.searchMenu || this.searchMenu.trim().length === 0) return true;
                    return text.toLowerCase().includes(this.searchMenu.toLowerCase());
                }
            }"
            :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0'"
            class="fixed md:sticky top-0 md:top-12 bottom-0 left-0 z-40 w-72 lg:w-76 h-dvh md:h-[calc(100dvh-48px)] max-h-[calc(100dvh-48px)] frosted-sidebar text-slate-700 flex flex-col shrink-0 overflow-hidden transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
        >
            <!-- Mobile Header Bar inside Drawer -->
            <div class="flex md:hidden items-center justify-between px-4 pt-3 pb-2 border-b border-white/40 shrink-0">
                <span class="text-xs font-black text-[#163144] tracking-tight uppercase">Navigasi Kasir</span>
                <button 
                    type="button" 
                    @click="sidebarOpen = false" 
                    class="px-2.5 py-1 rounded-xl bg-white/50 hover:bg-white/80 text-slate-700 hover:text-[#163144] text-[11px] font-black transition cursor-pointer flex items-center gap-1"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Tutup</span>
                </button>
            </div>

            <!-- Search & Quick Action Buttons -->
            <div class="px-3.5 pt-2 pb-1.5 shrink-0">

                <!-- 2 Segmented Big Square Action Buttons (Home & Dashboard) -->
                <div class="grid grid-cols-2 gap-2 mt-0.5">
                    <a href="{{ route('pos.index') }}" wire:navigate.hover class="p-2 rounded-xl glass-pill-plek hover:glass-card-plek text-center flex flex-col items-center justify-center gap-1 group transition">
                        <div class="w-6 h-6 rounded-lg bg-white/60 flex items-center justify-center text-[#163144] group-hover:scale-110 transition-transform shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-black text-[#163144] block">POS Kasir</span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" wire:navigate.hover class="p-2 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-white/90 text-[#163144] shadow-sm border border-white' : 'glass-pill-plek' }} text-center flex flex-col items-center justify-center gap-1 group transition">
                        <div class="w-6 h-6 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-[#163144] text-white' : 'bg-white/60 text-[#163144]' }} flex items-center justify-center group-hover:scale-110 transition-transform shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        </div>
                        <span class="text-[11px] font-black {{ request()->routeIs('admin.dashboard') ? 'text-[#163144]' : 'text-slate-600' }} block">Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Accordion Nav Links List (Scrolls independently if menus are tall) -->
            <nav class="flex-1 min-h-0 overflow-y-auto px-3.5 py-1.5 space-y-1 no-scrollbar">
                @if(auth()->user()->isAdmin())
                <!-- Dropdown 1: Penjualan (TRX) -->
                @php
                    $isSalesActive = request()->routeIs('admin.transactions.*', 'admin.shifts.*', 'admin.returns.*', 'admin.reports.index', 'admin.receivables.*');
                @endphp
                <div class="rounded-2xl transition overflow-hidden {{ $isSalesActive ? 'bg-white/40 border border-white/60' : '' }}">
                    <!-- Header Button -->
                    <button 
                        type="button" 
                        @click="toggle('sales')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-2xl text-xs font-black transition cursor-pointer {{ $isSalesActive ? 'text-[#163144]' : 'text-slate-700 hover:bg-white/50' }}"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isSalesActive ? 'bg-[#163144] text-white' : 'bg-white/60 text-[#163144]' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="truncate">Penjualan</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full glass-pill-plek text-slate-500 font-bold">TRX</span>
                            <svg 
                                class="w-3.5 h-3.5 transition-transform duration-200" 
                                :class="isOpen('sales') ? 'rotate-180 text-[#163144]' : 'rotate-0 text-slate-400'" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Submenu Items -->
                    <div 
                        x-show="isOpen('sales')" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="pl-4 pr-1.5 pb-2 pt-1 space-y-1 border-l-2 border-white/60 ml-4 mt-0.5"
                    >
                        <a 
                            href="{{ route('admin.transactions.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Riwayat Transaksi')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.transactions.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Riwayat Transaksi</span>
                        </a>
                        <a 
                            href="{{ route('admin.shifts.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Shift Kas Kasir')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.shifts.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Shift Kas Kasir (Z)</span>
                        </a>
                        <a 
                            href="{{ route('admin.returns.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Retur Penjualan')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.returns.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Retur Penjualan</span>
                        </a>
                        <a 
                            href="{{ route('admin.reports.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Laporan Penjualan')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.reports.index') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Laporan Penjualan</span>
                        </a>
                        <a 
                            href="{{ route('admin.receivables.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Buku Piutang')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.receivables.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Buku Piutang</span>
                        </a>
                    </div>
                </div>

                <!-- Dropdown 2: Marketing & Member (LOYAL) -->
                @php
                    $isMarketingActive = request()->routeIs('admin.customers.*', 'admin.discounts.*', 'admin.taxes.*');
                @endphp
                <div class="rounded-2xl transition overflow-hidden {{ $isMarketingActive ? 'bg-white/40 border border-white/60' : '' }}">
                    <!-- Header Button -->
                    <button 
                        type="button" 
                        @click="toggle('marketing')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-2xl text-xs font-black transition cursor-pointer {{ $isMarketingActive ? 'text-[#163144]' : 'text-slate-700 hover:bg-white/50' }}"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isMarketingActive ? 'bg-[#163144] text-white' : 'bg-white/60 text-[#163144]' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="truncate">Marketing & Member</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full glass-pill-plek text-slate-500 font-bold">LOYAL</span>
                            <svg 
                                class="w-3.5 h-3.5 transition-transform duration-200" 
                                :class="isOpen('marketing') ? 'rotate-180 text-[#163144]' : 'rotate-0 text-slate-400'" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Submenu Items -->
                    <div 
                        x-show="isOpen('marketing')" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="pl-4 pr-1.5 pb-2 pt-1 space-y-1 border-l-2 border-white/60 ml-4 mt-0.5"
                    >
                        <a 
                            href="{{ route('admin.customers.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Pelanggan Member')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.customers.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Pelanggan Member</span>
                        </a>
                        <a 
                            href="{{ route('admin.discounts.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Diskon & Promo')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.discounts.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Diskon & Promo</span>
                        </a>
                        <a 
                            href="{{ route('admin.taxes.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Pajak Penjualan')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.taxes.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Pajak Penjualan (PPN)</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Dropdown 3: Persediaan & Stok (STOCK) -->
                @php
                    $isStockActive = request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.stock.*', 'admin.reports.inventory-valuation');
                @endphp
                <div class="rounded-2xl transition overflow-hidden {{ $isStockActive ? 'bg-white/40 border border-white/60' : '' }}">
                    <!-- Header Button -->
                    <button 
                        type="button" 
                        @click="toggle('products')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-2xl text-xs font-black transition cursor-pointer {{ $isStockActive ? 'text-[#163144]' : 'text-slate-700 hover:bg-white/50' }}"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isStockActive ? 'bg-[#163144] text-white' : 'bg-white/60 text-[#163144]' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <span class="truncate">Persediaan & Stok</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full glass-pill-plek text-slate-500 font-bold">STOCK</span>
                            <svg 
                                class="w-3.5 h-3.5 transition-transform duration-200" 
                                :class="isOpen('products') ? 'rotate-180 text-[#163144]' : 'rotate-0 text-slate-400'" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Submenu Items -->
                    <div 
                        x-show="isOpen('products')" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="pl-4 pr-1.5 pb-2 pt-1 space-y-1 border-l-2 border-white/60 ml-4 mt-0.5"
                    >
                        <a 
                            href="{{ route('admin.products.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Katalog Produk')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.products.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Katalog Produk</span>
                        </a>
                        <a 
                            href="{{ route('admin.categories.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Kategori Produk')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.categories.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Kategori Produk</span>
                        </a>
                        <a 
                            href="{{ route('admin.stock.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Penyesuaian Stok')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.stock.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Penyesuaian Stok</span>
                        </a>
                        <a 
                            href="{{ route('admin.reports.inventory-valuation') }}" 
                            wire:navigate.hover 
                            x-show="matches('Valuasi Aset Stok')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.reports.inventory-valuation') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Valuasi Aset Stok</span>
                        </a>
                    </div>
                </div>

                <!-- Dropdown 4: Kulakan & Supplier (RESTOCK) -->
                @php
                    $isPurchasesActive = request()->routeIs('admin.purchases.*', 'admin.suppliers.*', 'admin.reports.accounts-payable');
                @endphp
                <div class="rounded-2xl transition overflow-hidden {{ $isPurchasesActive ? 'bg-white/40 border border-white/60' : '' }}">
                    <!-- Header Button -->
                    <button 
                        type="button" 
                        @click="toggle('purchases')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-2xl text-xs font-black transition cursor-pointer {{ $isPurchasesActive ? 'text-[#163144]' : 'text-slate-700 hover:bg-white/50' }}"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isPurchasesActive ? 'bg-[#163144] text-white' : 'bg-white/60 text-[#163144]' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <span class="truncate">Kulakan & Supplier</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full glass-pill-plek text-slate-500 font-bold">RESTOCK</span>
                            <svg 
                                class="w-3.5 h-3.5 transition-transform duration-200" 
                                :class="isOpen('purchases') ? 'rotate-180 text-[#163144]' : 'rotate-0 text-slate-400'" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Submenu Items -->
                    <div 
                        x-show="isOpen('purchases')" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="pl-4 pr-1.5 pb-2 pt-1 space-y-1 border-l-2 border-white/60 ml-4 mt-0.5"
                    >
                        <a 
                            href="{{ route('admin.purchases.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Riwayat Pembelian')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.purchases.index') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Riwayat Pembelian</span>
                        </a>
                        <a 
                            href="{{ route('admin.purchases.create') }}" 
                            wire:navigate.hover 
                            x-show="matches('Input Faktur Masuk')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.purchases.create') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Input Faktur Masuk</span>
                        </a>
                        <a 
                            href="{{ route('admin.purchases.restock') }}" 
                            wire:navigate.hover 
                            x-show="matches('Rencana Kulakan')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.purchases.restock') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Rencana Kulakan</span>
                        </a>
                        <a 
                            href="{{ route('admin.suppliers.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Pemasok Supplier')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.suppliers.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Pemasok (Supplier)</span>
                        </a>
                        <a 
                            href="{{ route('admin.reports.accounts-payable') }}" 
                            wire:navigate.hover 
                            x-show="matches('Buku Hutang')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.reports.accounts-payable') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Buku Hutang</span>
                        </a>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                <!-- Dropdown 5: Keuangan (FINANCE) -->
                @php
                    $isFinanceActive = request()->routeIs('admin.reports.profit-loss', 'admin.expenses.*', 'admin.assets.*', 'admin.employees.*', 'admin.finance.ai-advisor');
                    $isOutletsActive = request()->routeIs('admin.outlets.*', 'admin.backup.*', 'admin.users.*', 'admin.settings.*');
                @endphp
                <div class="rounded-2xl transition overflow-hidden {{ $isFinanceActive ? 'bg-white/40 border border-white/60' : '' }}">
                    <!-- Header Button -->
                    <button 
                        type="button" 
                        @click="toggle('finance')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-2xl text-xs font-black transition cursor-pointer {{ $isFinanceActive ? 'text-[#163144]' : 'text-slate-700 hover:bg-white/50' }}"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isFinanceActive ? 'bg-[#163144] text-white' : 'bg-white/60 text-[#163144]' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="truncate">Keuangan</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full glass-pill-plek text-slate-500 font-bold">FINANCE</span>
                            <svg 
                                class="w-3.5 h-3.5 transition-transform duration-200" 
                                :class="isOpen('finance') ? 'rotate-180 text-[#163144]' : 'rotate-0 text-slate-400'" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Submenu Items -->
                    <div 
                        x-show="isOpen('finance')" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="pl-4 pr-1.5 pb-2 pt-1 space-y-1 border-l-2 border-white/60 ml-4 mt-0.5"
                    >
                        <a 
                            href="{{ route('admin.finance.ai-advisor') }}" 
                            wire:navigate.hover 
                            x-show="matches('AI Financial Advisor')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.finance.ai-advisor') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>AI Advisor Keuangan</span>
                            <span class="px-2 py-0.5 rounded-full bg-[#a0bddb] text-[#163144] font-black text-[9px]">AI</span>
                        </a>
                        <a 
                            href="{{ route('admin.reports.profit-loss') }}" 
                            wire:navigate.hover 
                            x-show="matches('Laporan Laba Rugi')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.reports.profit-loss') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Laporan Laba Rugi</span>
                        </a>
                        <a 
                            href="{{ route('admin.expenses.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Beban Toko Biaya')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.expenses.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Beban & Biaya Toko</span>
                        </a>
                        <a 
                            href="{{ route('admin.employees.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Karyawan & Gaji')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.employees.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Karyawan & Gaji</span>
                        </a>
                    </div>
                </div>

                <!-- Dropdown 6: Outlet & Sistem (OUTLET) -->
                <div class="rounded-2xl transition overflow-hidden {{ $isOutletsActive ? 'bg-white/40 border border-white/60' : '' }}">
                    <!-- Header Button -->
                    <button 
                        type="button" 
                        @click="toggle('outlets')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-2xl text-xs font-black transition cursor-pointer {{ $isOutletsActive ? 'text-[#163144]' : 'text-slate-700 hover:bg-white/50' }}"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 {{ $isOutletsActive ? 'bg-[#163144] text-white' : 'bg-white/60 text-[#163144]' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l1.5-5h15L21 9M5 9v11h14V9M9 20v-6h6v6M3 9a3 3 0 006 0 3 3 0 006 0 3 3 0 006 0" />
                                </svg>
                            </div>
                            <span class="truncate">Outlet & Sistem</span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full glass-pill-plek text-slate-500 font-bold">OUTLET</span>
                            <svg 
                                class="w-3.5 h-3.5 transition-transform duration-200" 
                                :class="isOpen('outlets') ? 'rotate-180 text-[#163144]' : 'rotate-0 text-slate-400'" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <!-- Submenu Items -->
                    <div 
                        x-show="isOpen('outlets')" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="pl-4 pr-1.5 pb-2 pt-1 space-y-1 border-l-2 border-white/60 ml-4 mt-0.5"
                    >
                        <a 
                            href="{{ route('admin.outlets.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Cabang Toko')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.outlets.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Cabang Toko</span>
                            <span class="px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-800 text-[9px] font-bold">Multi</span>
                        </a>
                        <a 
                            href="{{ route('admin.backup.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Cloud Backup Database')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.backup.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Cloud Backup</span>
                        </a>
                        <a 
                            href="{{ route('admin.users.index') }}" 
                            wire:navigate.hover 
                            x-show="matches('Kelola Kasir User')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.users.*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Kelola Kasir & User</span>
                        </a>
                        <a 
                            href="{{ route('admin.settings.store') }}" 
                            wire:navigate.hover 
                            x-show="matches('Informasi Profil Toko')"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.settings.store*') ? 'bg-white/90 text-[#163144] shadow-2xs border border-white' : 'text-slate-600 hover:bg-white/50 hover:text-[#163144]' }}"
                        >
                            <span>Informasi Toko</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>

            <!-- Bottom Dock Area (Pinned firmly at bottom of sidebar on all screen sizes) -->
            <div class="px-3 py-2 shrink-0 space-y-1.5 mt-auto border-t border-white/50 bg-white/40 backdrop-blur-md shadow-[0_-4px_16px_rgba(22,49,68,0.04)]">
                <!-- Mini Icon Bar -->
                <div class="flex items-center justify-around py-0 text-slate-400">
                    <!-- Icon 1: Mode Matahari (Belum Fungsi Dulu) -->
                    <button type="button" title="Mode Tampilan (Segera Hadir)" class="p-1 rounded-xl text-slate-300 opacity-60 cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    <!-- Icon 2: Cloud Backup -->
                    <a 
                        href="{{ route('admin.backup.index') }}" 
                        wire:navigate.hover 
                        title="Cloud Backup & Database" 
                        class="p-1 rounded-xl transition {{ request()->routeIs('admin.backup.*') ? 'bg-white/90 text-[#163144] shadow-xs font-bold' : 'hover:bg-white/50 hover:text-[#163144]' }}"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </a>

                    <!-- Icon 3: Pengaturan Struk -->
                    <a 
                        href="{{ route('admin.settings.receipt') }}" 
                        wire:navigate.hover 
                        title="Pengaturan Struk Kasir" 
                        class="p-1 rounded-xl transition {{ request()->routeIs('admin.settings.receipt*') ? 'bg-white/90 text-[#163144] shadow-xs font-bold' : 'hover:bg-white/50 hover:text-[#163144]' }}"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>

                    <!-- Icon 4: Informasi Toko -->
                    <a 
                        href="{{ route('admin.settings.store') }}" 
                        wire:navigate.hover 
                        title="Informasi Toko" 
                        class="p-1 rounded-xl transition {{ request()->routeIs('admin.settings.store*') ? 'bg-white/90 text-[#163144] shadow-xs font-bold' : 'hover:bg-white/50 hover:text-[#163144]' }}"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                </div>

                <!-- Bottom Logout Button & User Avatar -->
                <div class="flex items-center gap-1.5 pt-0">
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button 
                            type="button" 
                            @click="window.customConfirm ? window.customConfirm({ title: 'Konfirmasi Keluar', message: 'Apakah Anda yakin ingin mengakhiri sesi kasir dan keluar dari sistem aplikasi?', confirmText: 'Ya, Keluar', onConfirm: () => document.getElementById('logout-form').submit() }) : document.getElementById('logout-form').submit()"
                            class="w-full py-1.5 px-3 rounded-full bg-rose-600/90 hover:bg-rose-700 active:scale-[0.98] text-white font-extrabold text-xs text-center flex items-center justify-center gap-1.5 transition-all shadow-xs border border-rose-400/30 cursor-pointer group"
                        >
                            <svg class="w-3.5 h-3.5 text-rose-100 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="tracking-wide">Keluar (Logout)</span>
                        </button>
                    </form>

                    <div class="relative group cursor-pointer" title="{{ auth()->user()->name ?? 'User' }} ({{ auth()->user()->role ?? 'Admin' }})">
                        <div class="w-7 h-7 rounded-full bg-[#163144] text-white font-black text-xs flex items-center justify-center shadow-xs uppercase border border-white/60 group-hover:scale-105 transition-transform">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 border-2 border-white absolute -bottom-0.5 -right-0.5 shadow-2xs"></span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Dashboard Viewport (Full Width) -->
        <main class="flex-1 min-w-0 px-3 sm:px-6 lg:px-8 pt-3.5 sm:pt-6 pb-16">
            {{ $slot }}
        </main>

    </div>

    <!-- Global Themed Confirmation & Alert Modal (Replaces Browser Dialogs) -->
    <div 
        x-data="{
            open: false,
            title: 'Konfirmasi Keluar',
            message: 'Apakah Anda yakin ingin keluar dari sistem Syafa Frozen Food?',
            confirmText: 'Ya, Keluar',
            cancelText: 'Batal',
            type: 'danger', // danger, warning, info
            actionCallback: null,
            init() {
                window.customConfirm = (opts) => {
                    this.title = opts.title || 'Konfirmasi';
                    this.message = opts.message || '';
                    this.confirmText = opts.confirmText || 'Lanjutkan';
                    this.cancelText = opts.cancelText || 'Batal';
                    this.type = opts.type || 'danger';
                    this.actionCallback = opts.onConfirm || null;
                    this.open = true;
                };
            }
        }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    >
        <!-- Modal Backdrop -->
        <div 
            x-show="open"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false" 
            class="fixed inset-0 bg-[#0e1e2b]/55 backdrop-blur-md"
        ></div>

        <!-- Modal Dialog Card (Frosted Glass Theme) -->
        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-md p-6 rounded-[28px] bg-white/30 backdrop-blur-2xl border border-white/50 shadow-2xl shadow-[#163144]/30 z-10 space-y-4"
        >
            <div class="flex items-start gap-4">
                <div 
                    :class="{
                        'bg-rose-500/15 text-[#e1144b] border-rose-300/40': type === 'danger',
                        'bg-amber-500/15 text-amber-700 border-amber-300/60': type === 'warning',
                        'bg-[#a0bddb]/30 text-[#163144] border-[#a0bddb]/60': type === 'info'
                    }"
                    class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border shadow-2xs"
                >
                    <template x-if="type === 'danger'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </template>
                    <template x-if="type === 'warning'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </template>
                    <template x-if="type === 'info'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-black text-[#163144] tracking-tight" x-text="title"></h3>
                    <p class="text-xs font-semibold text-slate-700 mt-1 leading-relaxed" x-text="message"></p>
                </div>
            </div>

            <!-- Action Buttons (Divider + Right-Aligned Rounded Pills Matching Screenshot) -->
            <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-slate-300/40">
                <button 
                    type="button" 
                    @click="open = false" 
                    class="px-5 py-2 rounded-full bg-white/50 hover:bg-white/75 border border-white/40 text-slate-700 font-black text-xs transition active:scale-95 cursor-pointer shadow-2xs"
                    x-text="cancelText"
                ></button>
                <button 
                    type="button" 
                    @click="if (actionCallback) actionCallback(); open = false;" 
                    :class="{
                        'bg-[#e1144b] hover:bg-[#c90f40] text-white shadow-md shadow-[#e1144b]/20': type === 'danger',
                        'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-950/20': type === 'warning',
                        'bg-[#163144] hover:bg-[#20445e] text-white shadow-[#163144]/20': type === 'info'
                    }"
                    class="px-5 py-2 rounded-full font-black text-xs transition active:scale-95 cursor-pointer"
                    x-text="confirmText"
                ></button>
            </div>
        </div>
    </div>

    <!-- Apple iOS / Dynamic Island Top Notification Alerts -->
    <div 
        x-data="{
            toasts: [],
            addToast(message, type = 'success') {
                if (!message) return;
                const id = Date.now() + Math.random();
                this.toasts.push({ id, message, type });
                setTimeout(() => this.removeToast(id), 4500);
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
            handleEvent(e) {
                let data = e.detail;
                if (Array.isArray(data) && data.length > 0) {
                    data = data[0];
                }
                if (typeof data === 'string') {
                    this.addToast(data, 'success');
                } else if (data && typeof data === 'object') {
                    const msg = data.message || data.title || data.text || '';
                    const typ = data.type || 'success';
                    this.addToast(msg, typ);
                }
            }
        }"
        x-init="
            @if(session('success'))
                addToast(@js(session('success')), 'success');
            @endif
            @if(session('error'))
                addToast(@js(session('error')), 'error');
            @endif
            @if(session('warning'))
                addToast(@js(session('warning')), 'warning');
            @endif
            @if(session('status'))
                addToast(@js(session('status')), 'success');
            @endif
            window.addEventListener('toast', e => handleEvent(e));
            window.addEventListener('swal:toast', e => handleEvent(e));
        "
        class="fixed top-3.5 sm:top-5 inset-x-0 z-[9999] flex flex-col items-center gap-2 max-w-[440px] w-full mx-auto pointer-events-none px-3.5 sm:px-4"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                x-show="true"
                x-data="{ touchStartY: 0 }"
                @touchstart="touchStartY = $event.touches[0].clientY"
                @touchend="if (touchStartY - $event.changedTouches[0].clientY > 25) removeToast(toast.id)"
                x-transition:enter="transition transform duration-500 ease-[cubic-bezier(0.175,0.885,0.32,1.275)]"
                x-transition:enter-start="opacity-0 -translate-y-12 scale-85 blur-[4px]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100 blur-0"
                x-transition:leave="transition transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100 blur-0"
                x-transition:leave-end="opacity-0 -translate-y-12 scale-90 blur-[4px]"
                :class="{
                    'border-emerald-300/80 shadow-[0_20px_45px_-10px_rgba(16,185,129,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]': toast.type === 'success',
                    'border-rose-300/80 shadow-[0_20px_45px_-10px_rgba(244,63,94,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]': toast.type === 'error',
                    'border-amber-300/80 shadow-[0_20px_45px_-10px_rgba(245,158,11,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]': toast.type === 'warning',
                    'border-sky-300/80 shadow-[0_20px_45px_-10px_rgba(14,165,233,0.2),0_4px_16px_rgba(22,49,68,0.08),inset_0_1px_1px_rgba(255,255,255,0.95)]': toast.type === 'info'
                }"
                class="pointer-events-auto w-full p-3.5 sm:p-4 rounded-[26px] border bg-white/80 hover:bg-white/90 backdrop-blur-3xl text-[#163144] flex items-center gap-3.5 transition select-none cursor-pointer group ring-1 ring-black/5"
                @click="removeToast(toast.id)"
                title="Klik atau usap ke atas untuk menutup"
            >
                <!-- Transparent Frosted Glass Icon Capsule -->
                <div 
                    :class="{
                        'bg-emerald-500/15 text-emerald-700 border-emerald-400/40 shadow-xs shadow-emerald-500/20': toast.type === 'success',
                        'bg-rose-500/15 text-rose-700 border-rose-400/40 shadow-xs shadow-rose-500/20': toast.type === 'error',
                        'bg-amber-500/15 text-amber-800 border-amber-400/40 shadow-xs shadow-amber-500/20': toast.type === 'warning',
                        'bg-sky-500/15 text-[#163144] border-sky-400/40 shadow-xs shadow-sky-500/20': toast.type === 'info'
                    }"
                    class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 border"
                >
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                </div>

                <!-- Content Area -->
                <div class="flex-1 min-w-0 pr-1">
                    <div class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest font-mono">
                        <span 
                            class="w-1.5 h-1.5 rounded-full"
                            :class="{
                                'bg-emerald-500': toast.type === 'success',
                                'bg-rose-500': toast.type === 'error',
                                'bg-amber-500': toast.type === 'warning',
                                'bg-sky-500': toast.type === 'info'
                            }"
                        ></span>
                        <span 
                            :class="{
                                'text-emerald-700': toast.type === 'success',
                                'text-rose-700': toast.type === 'error',
                                'text-amber-800': toast.type === 'warning',
                                'text-[#163144]': toast.type === 'info'
                            }"
                            x-text="toast.type === 'success' ? 'Sukses' : (toast.type === 'error' ? 'Peringatan' : (toast.type === 'warning' ? 'Perhatian' : 'Pemberitahuan'))"
                        ></span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500 font-sans normal-case tracking-normal text-[10px]">baru saja</span>
                    </div>
                    <p class="text-xs sm:text-[13px] text-[#163144] font-black mt-0.5 leading-snug break-words" x-text="toast.message"></p>
                </div>

                <!-- Minimalist Close Action -->
                <button 
                    type="button" 
                    @click.stop="removeToast(toast.id)" 
                    class="w-7 h-7 rounded-full bg-white/70 hover:bg-white active:scale-90 flex items-center justify-center text-slate-400 hover:text-[#163144] border border-white/80 transition cursor-pointer shrink-0 shadow-2xs"
                    title="Tutup"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @livewireScripts

    <script>
        // Smooth fade-out for Livewire-rendered confirmation modals (same motion as customConfirm).
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.removing', ({ el, skip }) => {
                if (el.nodeType === 1 && el.classList.contains('confirm-backdrop') && !el.dataset.leaving) {
                    skip();
                    el.dataset.leaving = '1';
                    el.classList.add('confirm-leaving');
                    setTimeout(() => el.remove(), 220);
                }
            });
        });
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset("sw.js") }}')
                    .then((reg) => console.log('PWA Service Worker registered:', reg.scope))
                    .catch((err) => console.error('PWA Service Worker registration failed:', err));
            });
        }
    </script>
</body>
</html>
