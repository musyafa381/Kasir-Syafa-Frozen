<div class="space-y-4">

    <!-- Hero Card Profil Toko -->
    <div class="glass-card-plek rounded-[28px] p-6 sm:p-8 relative overflow-hidden">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-white/90 p-2 border border-white/80 shadow-md shrink-0 flex items-center justify-center">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo Toko" class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-mono font-black text-[10px] uppercase">
                            Official Store
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-black text-[10px] flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Terverifikasi
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-[#163144] tracking-tight leading-tight">
                        {{ $settings->store_name ?: 'Syafa Frozen Food' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-semibold mt-0.5">
                        {{ $settings->header_notes ?: 'Pusat Makanan Beku Berkualitas, Higienis & Terlengkap' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-stretch sm:self-auto flex-wrap">
                <a 
                    href="{{ url('download-logo.html') }}" 
                    target="_blank"
                    class="btn-glass-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl font-extrabold text-xs text-[#163144] hover:text-sky-700"
                >
                    <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Paket Logo</span>
                </a>
                <a 
                    href="{{ route('admin.settings.receipt') }}" 
                    wire:navigate 
                    class="btn-glass-action flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl font-extrabold text-xs"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Edit Profil Toko</span>
                </a>
                <a 
                    href="{{ route('pos.index') }}" 
                    wire:navigate 
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
                >
                    <span>Buka POS Kasir</span>
                    <span class="text-[9px] font-mono opacity-60">F1</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 KPI Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        <!-- Total Cabang -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="font-bold text-[#163144] text-[11px] sm:text-xs truncate">Jaringan Outlet</span>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px]">
                    Cabang
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ $outlets->count() }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Outlet Aktif</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Pusat & Cabang terhubung</p>
            </div>
        </div>

        <!-- Total Katalog Produk -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#386797] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="font-bold text-[#163144] text-[11px] sm:text-xs truncate">Katalog Produk</span>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#386797] font-extrabold text-[9px] sm:text-[10px]">
                    SKU
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ number_format($totalProducts, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Item</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Frozen food & olahan</p>
            </div>
        </div>

        <!-- Pelanggan Terdaftar -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="font-bold text-[#163144] text-[11px] sm:text-xs truncate">Pelanggan & Member</span>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-extrabold text-[9px] sm:text-[10px]">
                    Member
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-emerald-800 font-mono tracking-tight block">
                    {{ number_format($totalCustomers, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Orang</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Database mitra & retail</p>
            </div>
        </div>

        <!-- Total Struk Kasir Selesai -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="font-bold text-[#163144] text-[11px] sm:text-xs truncate">Total Transaksi</span>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px]">
                    Struk
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ number_format($totalTransactions, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">Nota</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Total penjualan selesai</p>
            </div>
        </div>
    </div>

    <!-- 2 Column Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
        
        <!-- Left: Profil Kontak & Jam Operasional -->
        <div class="space-y-4">
            
            <!-- Detail Kontak & Alamat -->
            <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-white/40">
                    <div class="w-8 h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs font-black text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-[#163144]">Alamat & Kontak Resmi Toko</h2>
                        <p class="text-[11px] text-slate-500 font-semibold">Informasi lokasi toko pusat dan saluran komunikasi resmi.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-start gap-3 p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-400 font-extrabold w-24 shrink-0">Alamat Toko</span>
                        <div class="text-[#163144] font-bold flex-1 leading-relaxed">
                            {{ $settings->store_address ?: 'Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Kab. Bantul, D.I. Yogyakarta' }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-400 font-extrabold w-24 shrink-0">WhatsApp / Telp</span>
                        <div class="text-[#163144] font-mono font-black flex-1 flex items-center gap-2">
                            <span>{{ $settings->store_phone ?: '0896-4939-9694' }}</span>
                            @if($settings->store_phone)
                                <a 
                                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->store_phone) }}" 
                                    target="_blank" 
                                    class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold hover:underline"
                                >
                                    Chat WA
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-400 font-extrabold w-24 shrink-0">Email Toko</span>
                        <div class="text-[#163144] font-mono font-bold flex-1">
                            halo@syafafrozen.com / admin@syafafrozen.com
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-400 font-extrabold w-24 shrink-0">Link / E-Receipt</span>
                        <div class="text-[#386797] font-mono font-bold flex-1 break-all">
                            {{ $settings->footer_link ?: 'https://syafafrozen.com' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jam Operasional & Layanan -->
            <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-white/40">
                    <div class="w-8 h-8 rounded-xl bg-white/70 text-[#386797] flex items-center justify-center border border-white/80 shadow-2xs font-black text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-[#163144]">Jam Operasional & Layanan</h2>
                        <p class="text-[11px] text-slate-500 font-semibold">Waktu buka toko dan jenis transaksi yang didukung.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 space-y-1">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Senin – Sabtu</span>
                        <span class="text-sm font-black text-[#163144] font-mono block">07.30 – 21.00 WIB</span>
                        <span class="text-[11px] text-emerald-800 font-bold block">✓ Buka Setiap Hari</span>
                    </div>

                    <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 space-y-1">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Minggu & Libur Nasional</span>
                        <span class="text-sm font-black text-[#163144] font-mono block">08.00 – 20.30 WIB</span>
                        <span class="text-[11px] text-emerald-800 font-bold block">✓ Tetap Melayani Kasir</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-white/40 space-y-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#163144] block">Metode Pembayaran Kasir:</span>
                    <div class="flex flex-wrap gap-1.5">
                        <span class="px-3 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-xs">💵 Tunai (Cash)</span>
                        <span class="px-3 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-xs">📱 QRIS Dinamis</span>
                        <span class="px-3 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-xs">💳 Transfer Bank (BCA / BRI / Mandiri)</span>
                        <span class="px-3 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-xs">📋 Piutang Member / Kasbon</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Jaringan Cabang & Info Lisensi POS -->
        <div class="space-y-4">
            
            <!-- Daftar Cabang Aktif -->
            <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-white/40">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs font-black text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-[#163144]">Jaringan Cabang Toko</h2>
                            <p class="text-[11px] text-slate-500 font-semibold">Cabang yang sedang terhubung ke sistem POS.</p>
                        </div>
                    </div>
                    <a 
                        href="{{ route('admin.outlets.index') }}" 
                        wire:navigate 
                        class="btn-glass-action text-[10px] px-3 py-1 rounded-full font-bold"
                    >
                        Kelola
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse($outlets as $outlet)
                        <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-black text-[10px] glass-pill-plek px-2 py-0.5 rounded-full text-[#163144]">
                                        {{ $outlet->code }}
                                    </span>
                                    <h4 class="font-black text-[#163144] text-xs truncate">{{ $outlet->name }}</h4>
                                    @if($outlet->is_main)
                                        <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#386797] font-black text-[9px] uppercase">
                                            Pusat
                                        </span>
                                    @endif
                                </div>
                                @if($outlet->address)
                                    <p class="text-[11px] text-slate-500 font-medium mt-1 line-clamp-1">{{ $outlet->address }}</p>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-mono font-bold text-slate-600 block">{{ $outlet->phone ?: '-' }}</span>
                                <span class="text-[9px] text-emerald-800 font-extrabold uppercase block mt-0.5">● Aktif</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs font-semibold">
                            Belum ada cabang toko terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Info Lisensi & Sistem POS -->
            <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-white/40">
                    <div class="w-8 h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs font-black text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-[#163144]">Sistem POS & Lisensi Aplikasi</h2>
                        <p class="text-[11px] text-slate-500 font-semibold">Status instalasi, versi software, dan dukungan teknis.</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-500 font-bold">Nama Software</span>
                        <span class="font-black text-[#163144]">Kasir Syafa Frozen POS</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-500 font-bold">Edisi & Tema</span>
                        <span class="font-black text-[#163144] font-mono">v2.5 (Arctic Navy Frosted Glass)</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-500 font-bold">Status Lisensi</span>
                        <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-black text-[10px] uppercase">
                            ✓ Enterprise Lifetime
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-500 font-bold">Cloud Sync & Backup</span>
                        <span class="font-black text-amber-700">Google Drive Integrated</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl glass-pill-plek border border-white/60">
                        <span class="text-slate-500 font-bold">Dukungan Printer</span>
                        <span class="font-black text-[#163144]">Thermal 58mm & 80mm ESC/POS</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 text-[11px] text-slate-600 font-semibold leading-relaxed">
                    💡 <strong>Tips Toko</strong>: Pastikan nomor telepon WhatsApp dan alamat toko selalu terupdate di menu Pengaturan Struk agar informasi nota pelanggan selalu akurat.
                </div>
            </div>

        </div>

    </div>

</div>
