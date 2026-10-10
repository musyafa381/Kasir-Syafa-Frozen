<div class="space-y-6" x-data="{ format: 'story', isDownloading: false, copyToast: false }">

    <!-- Top Action Bar (Hidden when Printing) -->
    <div class="print:hidden flex flex-col md:flex-row md:items-center justify-between gap-4 pb-3 border-b border-white/40">
        
        <!-- Left: Back Button & Title -->
        <div class="flex items-center gap-3">
            <a 
                href="{{ route('admin.discounts.index') }}" 
                wire:navigate 
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl glass-pill-plek text-[#163144] hover:bg-white font-extrabold text-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali</span>
            </a>
            <span class="text-xs font-semibold text-slate-300">|</span>
            <div>
                <h1 class="text-base sm:text-lg font-black text-[#163144] tracking-tight leading-tight">Studio Poster & Story Promo</h1>
                <p class="text-[11px] text-slate-500 font-semibold">Promo: <strong class="text-[#163144]">{{ $discount->name }}</strong></p>
            </div>
        </div>

        <!-- Right: Actions (Copy WA, Print, Download) -->
        <div class="flex flex-wrap items-center gap-2">
            
            <!-- Format Switcher Segmented Control -->
            <div class="inline-flex p-1 glass-card-plek rounded-2xl">
                <button 
                    type="button" 
                    @click="format = 'story'"
                    :class="format === 'story' ? 'glass-pill-dark text-white shadow-xs font-black' : 'text-slate-500 hover:text-[#163144] font-bold'"
                    class="px-3 py-1.5 rounded-xl text-xs flex items-center gap-1.5 transition cursor-pointer"
                >
                    <span>Story (9:16)</span>
                </button>
                <button 
                    type="button" 
                    @click="format = 'poster'"
                    :class="format === 'poster' ? 'glass-pill-dark text-white shadow-xs font-black' : 'text-slate-500 hover:text-[#163144] font-bold'"
                    class="px-3 py-1.5 rounded-xl text-xs flex items-center gap-1.5 transition cursor-pointer"
                >
                    <span>Poster A3</span>
                </button>
            </div>

            <!-- Copy Caption WA Button -->
            <button 
                type="button" 
                onclick="copyWhatsAppCaption()"
                class="px-3.5 py-2 rounded-xl glass-pill-plek text-[#163144] hover:bg-white font-bold text-xs flex items-center gap-1.5 transition cursor-pointer"
            >
                <svg class="w-4 h-4 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                <span id="copy-btn-text">Salin Teks WA</span>
            </button>

            <!-- Direct Print Button -->
            <button 
                type="button" 
                onclick="printPromoPoster()"
                class="px-3.5 py-2 rounded-xl glass-pill-plek text-[#163144] hover:bg-white font-bold text-xs flex items-center gap-1.5 transition cursor-pointer"
            >
                <svg class="w-4 h-4 text-[#163144]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak</span>
            </button>

            <!-- Download High-DPI PNG Button -->
            <button 
                type="button" 
                onclick="downloadPromoImage('{{ Str::slug($discount->name) }}')"
                class="px-4 py-2 rounded-xl glass-pill-dark text-white font-black text-xs shadow-md flex items-center gap-1.5 transition cursor-pointer active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Download PNG</span>
            </button>

        </div>
    </div>

    <!-- Main Canvas Area -->
    <div class="flex flex-col items-center justify-center py-4 sm:py-8">
        
        <!-- Ratio Indicator Tag -->
        <div class="print:hidden mb-4 flex items-center gap-2">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white text-slate-600 border border-slate-200/80 shadow-2xs">
                Ukuran Aktif: <span class="text-blue-600" x-text="format === 'story' ? '📱 Story WhatsApp / Instagram (9:16)' : '🖨️ Poster Cetak A3 / Toko'"></span>
            </span>
        </div>

        <!-- POSTER RENDER TARGET CONTAINER -->
        <div 
            id="promo-poster-canvas"
            :class="format === 'story' ? 'w-[360px] sm:w-[400px] aspect-[9/16]' : 'w-[420px] sm:w-[480px] aspect-[1/1.414]'"
            class="bg-gradient-to-b from-sky-600 via-blue-700 to-indigo-950 text-white rounded-3xl p-6 sm:p-7 relative overflow-hidden shadow-2xl flex flex-col justify-between border-4 border-white/30 select-none transition-all duration-300"
        >
            <!-- Ambient Glow Orbs & Subtle Texture -->
            <div class="absolute -top-14 -left-14 w-48 h-48 rounded-full bg-cyan-300/30 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-14 -right-14 w-48 h-48 rounded-full bg-blue-400/30 blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#ffffff18_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

            <!-- 1. Header Toko Syafa Frozen -->
            <div class="relative z-10 flex items-center justify-between pb-3 border-b border-white/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white p-1.5 shadow-lg shrink-0 flex items-center justify-center">
                        <img src="{{ asset('kasirku.png') }}" alt="Logo Syafa Frozen" class="w-full h-full object-contain">
                    </div>
                    <div class="text-left">
                        <span class="font-black text-sm tracking-tight text-white block leading-none">SYAFA FROZEN FOOD</span>
                        <span class="text-[10px] font-semibold text-cyan-200 block mt-1">Pusat Aneka Makanan Beku Berkualitas</span>
                    </div>
                </div>
                <div class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-[9px] font-black uppercase text-amber-300 tracking-wider shadow-sm">
                    ❄️ PROMO SPESIAL
                </div>
            </div>

            <!-- 2. Hero Body Diskon & Voucher -->
            <div class="relative z-10 py-4 my-auto text-center space-y-3.5">
                
                <!-- Nama Promo Badge & Title -->
                <div>
                    <span class="px-3.5 py-1 rounded-full bg-gradient-to-r from-amber-300 to-orange-400 text-slate-950 font-black text-[10px] tracking-wider uppercase shadow-md inline-block">
                        PENAWARAN TERBAIK
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight uppercase drop-shadow-md mt-2">
                        {{ $discount->name }}
                    </h2>
                </div>

                <!-- Mega Nominal / Persen Diskon Card -->
                <div class="py-1">
                    <div class="inline-block p-1.5 rounded-3xl bg-gradient-to-b from-white/30 to-white/10 backdrop-blur-md border border-white/30 shadow-xl">
                        <div class="px-6 sm:px-8 py-3 rounded-2xl bg-slate-950/40 backdrop-blur-sm text-center">
                            <span class="text-[10px] uppercase font-extrabold tracking-widest text-cyan-200 block">
                                {{ $discount->type === 'percentage' ? 'POTONGAN PERSENTASE' : 'POTONGAN LANGSUNG' }}
                            </span>
                            <div class="text-4xl sm:text-5xl font-black tracking-tight font-mono my-1 text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-yellow-300 to-orange-300 drop-shadow-md">
                                {{ $discount->formatted_value }}
                            </div>
                            @if ($discount->type === 'percentage' && $discount->max_discount_amount)
                                <span class="text-[10px] font-bold text-amber-200 block">
                                    Maksimal Potongan Rp {{ number_format($discount->max_discount_amount, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kupon Kode Voucher (Jika ada kode) -->
                @if ($discount->code)
                    <div class="pt-1">
                        <div class="relative bg-white text-slate-900 rounded-2xl p-3 shadow-lg border-2 border-dashed border-slate-300 max-w-xs mx-auto text-center overflow-hidden">
                            <!-- Side Notches -->
                            <div class="absolute -left-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-blue-900 rounded-full"></div>
                            <div class="absolute -right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-blue-900 rounded-full"></div>

                            <span class="text-[9px] uppercase font-extrabold tracking-widest text-slate-400 block">KODE VOUCHER KASIR</span>
                            <div class="text-base font-black font-mono tracking-widest text-blue-700 my-0.5 uppercase">
                                {{ $discount->code }}
                            </div>
                            <span class="text-[9px] text-slate-500 block font-medium">Tunjukkan atau sebutkan kode ini saat transaksi di kasir</span>
                        </div>
                    </div>
                @endif

                <!-- Detail Syarat & Periode Grid -->
                <div class="grid grid-cols-2 gap-2.5 text-left pt-1">
                    <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 text-xs">
                        <span class="text-cyan-200 block text-[9px] font-bold uppercase tracking-wider">Min. Belanja:</span>
                        <span class="font-bold text-white block truncate text-xs">
                            {{ $discount->min_purchase > 0 ? $discount->formatted_min_purchase : 'Tanpa Minimum' }}
                        </span>
                    </div>
                    <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 text-xs">
                        <span class="text-cyan-200 block text-[9px] font-bold uppercase tracking-wider">Periode Promo:</span>
                        <span class="font-bold text-white block truncate text-xs">
                            @if ($discount->start_date && $discount->end_date)
                                {{ $discount->start_date->format('d/m') }} - {{ $discount->end_date->format('d/m/Y') }}
                            @elseif ($discount->end_date)
                                s/d {{ $discount->end_date->format('d/m/Y') }}
                            @elseif ($discount->start_date)
                                Mulai {{ $discount->start_date->format('d/m/Y') }}
                            @else
                                Selalu Berlaku
                            @endif
                        </span>
                    </div>
                </div>

                @if ($discount->notes)
                    <p class="text-[10px] text-cyan-100 italic px-2 leading-tight">
                        *{{ $discount->notes }}
                    </p>
                @endif

            </div>

            <!-- 3. Footer Toko Syafa Frozen -->
            <div class="relative z-10 pt-3 border-t border-white/20 text-center space-y-1">
                <p class="text-[10px] font-bold text-amber-300 uppercase tracking-wide">
                    Kunjungi Outlet Syafa Frozen Food Sekarang!
                </p>
                <p class="text-[9px] text-cyan-100 font-medium">
                    Melayani Eceran & Grosir • Aneka Makanan Beku Segar & Halal
                </p>
            </div>

        </div>

    </div>

</div>

<!-- High-DPI Poster Generation & Print Logic -->
<script>
    function loadHtml2CanvasLib() {
        return new Promise((resolve, reject) => {
            if (window.html2canvas) {
                return resolve(window.html2canvas);
            }
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            script.onload = () => resolve(window.html2canvas);
            script.onerror = () => reject(new Error('Gagal memuat library html2canvas'));
            document.head.appendChild(script);
        });
    }

    async function downloadPromoImage(slugName) {
        const target = document.getElementById('promo-poster-canvas');
        if (!target) return;

        try {
            const h2c = await loadHtml2CanvasLib();
            const canvas = await h2c(target, {
                scale: 3, // High DPI Ultra Crisp Resolution
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                logging: false
            });

            const link = document.createElement('a');
            link.download = `syafa-promo-${slugName || 'diskon'}.png`;
            link.href = canvas.toDataURL('image/png', 1.0);
            link.click();
        } catch (err) {
            console.error(err);
            alert('Gagal mendownload gambar. Silakan coba lagi.');
        }
    }

    function printPromoPoster() {
        const target = document.getElementById('promo-poster-canvas');
        if (!target) return;

        const printWin = window.open('', '_blank');
        if (!printWin) {
            alert('Pop-up terblokir di browser Anda. Izinkan pop-up untuk mencetak.');
            return;
        }

        printWin.document.write('<!DOCTYPE html><html><head><title>Cetak Poster Promo - Syafa Frozen</title>');
        printWin.document.write('<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"><\/script>');
        printWin.document.write('<style>');
        printWin.document.write('@page { size: A3 portrait; margin: 0; }');
        printWin.document.write('* { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; box-sizing: border-box; }');
        printWin.document.write('body { margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #fff; font-family: sans-serif; }');
        printWin.document.write('</style></head><body>');
        printWin.document.write('<div style="width: 600px; margin: auto;">' + target.outerHTML + '</div>');
        printWin.document.write('<script>');
        printWin.document.write('setTimeout(function() { window.print(); window.close(); }, 700);');
        printWin.document.write('<\/script></body></html>');
        printWin.document.close();
    }

    function copyWhatsAppCaption() {
        const nama = '{{ $discount->name }}';
        const nilai = '{{ $discount->formatted_value }}';
        const tipe = '{{ $discount->type === 'percentage' ? 'Persentase' : 'Potongan Nominal Langsung' }}';
        const kode = '{{ $discount->code ?: '-' }}';
        const minBeli = '{{ $discount->min_purchase > 0 ? $discount->formatted_min_purchase : 'Tanpa Minimum Belanja' }}';
        const periode = '{{ $discount->start_date ? $discount->start_date->format('d/m/Y') : 'Sekarang' }} s/d {{ $discount->end_date ? $discount->end_date->format('d/m/Y') : 'Seterusnya' }}';

        let text = '❄️ *SPESIAL PROMO SYAFA FROZEN FOOD* ❄️\n\n';
        text += '🔥 *' + nama + '*\n';
        text += '💰 *Nilai Potongan:* ' + nilai + ' (' + tipe + ')\n';
        if (kode !== '-') {
            text += '🎟️ *Kode Voucher:* ' + kode + '\n';
        }
        text += '🛒 *Syarat Min. Belanja:* ' + minBeli + '\n';
        text += '📅 *Periode Promo:* ' + periode + '\n\n';
        text += 'Yuk segera dapatkan frozen food berkualitas dengan harga hemat! Kunjungi outlet Syafa Frozen Food sekarang juga!\n\n';
        text += '📍 *Syafa Frozen Food*\n📲 Melayani Eceran & Grosir';

        navigator.clipboard.writeText(text).then(() => {
            const btnText = document.getElementById('copy-btn-text');
            if (btnText) {
                btnText.innerText = 'Tersalin! ✅';
                setTimeout(() => { btnText.innerText = 'Salin Teks WA'; }, 3000);
            }
        });
    }
</script>
