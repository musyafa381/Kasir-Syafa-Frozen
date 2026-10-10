<!DOCTYPE html>
<html lang="id" class="h-full bg-zinc-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'POS Kasir' }} - Syafa Frozen Food</title>
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
        .font-mono, [class*="font-mono"],
        td, th, .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @media print {
            body * {
                visibility: hidden;
            }
            #printable-receipt, #printable-receipt * {
                visibility: visible;
            }
            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: var(--print-receipt-width, 42mm) !important;
                max-width: var(--print-receipt-width, 42mm) !important;
                margin: 0 auto;
                padding: 0 !important;
                color: #000 !important;
                background: #fff !important;
                border: none !important;
                box-shadow: none !important;
                font-family: Arial, Helvetica, sans-serif !important;
            }
            #printable-receipt .border-b,
            #printable-receipt .border-t,
            #printable-receipt .border-dashed {
                border-color: #000000 !important;
                border-style: dashed !important;
                border-width: 1px !important;
                height: 0 !important;
            }
            @page {
                size: auto;
                margin: 0mm;
            }
        }
    </style>
</head>
<body class="h-full bg-zinc-100 text-zinc-900 antialiased overflow-hidden select-none">
    {{ $slot }}

    <!-- Apple iOS / Dynamic Island Top Notification Alerts (POS) -->
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

    <!-- Global Themed Confirmation & Alert Modal (Matching Confirmation Screenshot) -->
    <div 
        x-data="{
            open: false,
            title: 'Konfirmasi',
            message: '',
            confirmText: 'Lanjutkan',
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

                window.addEventListener('confirm-action', (e) => {
                    let opts = e.detail;
                    if (Array.isArray(opts)) opts = opts[0];
                    if (window.customConfirm) window.customConfirm(opts);
                });
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

        <!-- Modal Dialog Card (Frosted Glass Theme Matching Screenshot) -->
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

            <!-- Action Buttons (Divider + Right-Aligned Rounded Pills) -->
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

    @livewireScripts

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
