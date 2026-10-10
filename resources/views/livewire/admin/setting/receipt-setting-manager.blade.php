<div class="space-y-4">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Pengaturan Struk Kasir</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Kustomisasi identitas toko, footer, dan format cetak nota kasir thermal (58mm & 80mm).</p>
        </div>
        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full sm:w-auto">
            <button 
                type="button"
                @click="window.customConfirm ? window.customConfirm({ title: 'Reset Pengaturan Struk', message: 'Apakah Anda yakin ingin mengembalikan semua pengaturan struk ke pengaturan standar bawaan?', confirmText: 'Ya, Reset', type: 'warning', onConfirm: () => $wire.resetToDefaults() }) : $wire.resetToDefaults()"
                class="btn-glass-action inline-flex items-center justify-center gap-1.5 sm:gap-2 px-3.5 py-2.5 rounded-2xl font-extrabold text-xs cursor-pointer"
            >
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="truncate">Reset Standar</span>
            </button>
            <button 
                type="button"
                wire:click="save"
                class="inline-flex items-center justify-center gap-1.5 sm:gap-2 px-4 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <svg class="w-3.5 h-3.5 shrink-0 text-[#a0bddb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="truncate">Simpan Pengaturan</span>
            </button>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

        <!-- Form Column (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <form wire:submit="save" class="space-y-4">
                
                <!-- Identitas Toko Card -->
                <div class="p-5 sm:p-6 rounded-[24px] glass-card-plek space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-white/40">
                        <div class="w-8 h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs font-black text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-[#163144]">Identitas Toko</h2>
                            <p class="text-[11px] text-slate-500 font-semibold">Informasi utama yang dicetak di bagian paling atas nota.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Nama Toko -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Nama Toko / Usaha <span class="text-rose-500">*</span></label>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="store_name" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs transition"
                                placeholder="Contoh: Syafa frozen food"
                            />
                            @error('store_name') <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Alamat Toko -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Alamat Lengkap Toko</label>
                            <textarea 
                                wire:model.live.debounce.300ms="store_address" 
                                rows="3"
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs transition resize-none"
                                placeholder="Contoh: Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Bantul..."
                            ></textarea>
                            @error('store_address') <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nomor Telepon / WA -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Nomor Telepon / WhatsApp</label>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="store_phone" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs transition"
                                placeholder="Contoh: 089649399694"
                            />
                            @error('store_phone') <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Catatan Header Tambahan -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Catatan Header Tambahan (Opsional)</label>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="header_notes" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs transition"
                                placeholder="Contoh: NIB / Kode Outlet / Slogan Toko"
                            />
                            @error('header_notes') <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer & Pesan Card -->
                <div class="p-5 sm:p-6 rounded-[24px] glass-card-plek space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-white/40">
                        <div class="w-8 h-8 rounded-xl bg-white/70 text-emerald-700 flex items-center justify-center border border-white/80 shadow-2xs font-black text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-[#163144]">Pesan & Informasi Bawah (Footer)</h2>
                            <p class="text-[11px] text-slate-500 font-semibold">Teks ucapan terima kasih, kebijakan retur, dan link saran pembeli.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Pesan / Catatan Footer -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Pesan Footer / Catatan Struk</label>
                            <textarea 
                                wire:model.live.debounce.300ms="footer_notes" 
                                rows="2"
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs transition resize-none"
                                placeholder="Contoh: Barang yang sudah dibeli tidak dapat ditukar/dikembalikan."
                            ></textarea>
                            @error('footer_notes') <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Link Kritik & Saran -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Link Kritik & Saran / E-Receipt</label>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="footer_link" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs transition"
                                placeholder="Contoh: kasirpintar.co.id/e-receipt/syafafrozen atau link kuesioner"
                            />
                            @error('footer_link') <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Opsi Tampilan & Ukuran Printer Card -->
                <div class="p-5 sm:p-6 rounded-[24px] glass-card-plek space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-white/40">
                        <div class="w-8 h-8 rounded-xl bg-white/70 text-[#386797] flex items-center justify-center border border-white/80 shadow-2xs font-black text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-[#163144]">Opsi Tampilan & Printer Thermal</h2>
                            <p class="text-[11px] text-slate-500 font-semibold">Pilih detail yang akan ditampilkan di struk serta ukuran kertas default.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Ukuran Kertas Default -->
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-2">Ukuran Kertas Default POS</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition {{ $default_paper_size === '58mm' ? 'bg-white/80 border-[#163144] shadow-2xs text-[#163144] font-black' : 'glass-pill-plek border-white/60 hover:bg-white/60 text-slate-700 font-bold' }}">
                                    <input type="radio" wire:model.live="default_paper_size" value="58mm" class="text-[#163144] focus:ring-[#163144]">
                                    <div class="min-w-0">
                                        <p class="text-xs">58 mm (Standard)</p>
                                        <p class="text-[10px] text-slate-500 font-medium">Printer thermal mini portable</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition {{ $default_paper_size === '80mm' ? 'bg-white/80 border-[#163144] shadow-2xs text-[#163144] font-black' : 'glass-pill-plek border-white/60 hover:bg-white/60 text-slate-700 font-bold' }}">
                                    <input type="radio" wire:model.live="default_paper_size" value="80mm" class="text-[#163144] focus:ring-[#163144]">
                                    <div class="min-w-0">
                                        <p class="text-xs">80 mm (Wide)</p>
                                        <p class="text-[10px] text-slate-500 font-medium">Printer thermal kasir besar</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-2 space-y-2.5">
                            <!-- Toggle Nama Kasir -->
                            <label class="flex items-center justify-between p-3 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/60 cursor-pointer transition">
                                <div>
                                    <span class="text-xs font-black text-[#163144] block">Tampilkan Nama Kasir</span>
                                    <span class="text-[11px] text-slate-500 font-medium">Menampilkan "Kasir : [Nama Kasir]" di sisi kanan tanggal nota.</span>
                                </div>
                                <input type="checkbox" wire:model.live="show_cashier_name" class="w-4 h-4 text-[#163144] rounded border-slate-300 focus:ring-[#163144] cursor-pointer">
                            </label>

                            <!-- Toggle Nama Pelanggan -->
                            <label class="flex items-center justify-between p-3 rounded-2xl glass-pill-plek border border-white/60 hover:bg-white/60 cursor-pointer transition">
                                <div>
                                    <span class="text-xs font-black text-[#163144] block">Tampilkan Nama Pelanggan</span>
                                    <span class="text-[11px] text-slate-500 font-medium">Menampilkan baris nama pembeli / pelanggan jika dipilih saat transaksi.</span>
                                </div>
                                <input type="checkbox" wire:model.live="show_customer_name" class="w-4 h-4 text-[#163144] rounded border-slate-300 focus:ring-[#163144] cursor-pointer">
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bottom -->
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto px-6 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
                    >
                        Simpan Semua Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Preview Column (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="sticky top-6">
                <!-- Preview Header & Switcher -->
                <div class="flex items-center justify-between p-4 rounded-[22px] glass-card-plek mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h2 class="text-xs font-black text-[#163144] uppercase tracking-wider">Live Preview Struk</h2>
                    </div>
                    <div class="flex items-center gap-1 glass-pill-plek p-1 rounded-xl">
                        <button 
                            type="button" 
                            wire:click="$set('preview_paper_size', '58mm')"
                            class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer {{ $preview_paper_size === '58mm' ? 'bg-white text-[#163144] shadow-2xs' : 'text-slate-500 hover:text-[#163144]' }}"
                        >
                            58mm
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('preview_paper_size', '80mm')"
                            class="px-2.5 py-1 text-[11px] font-black rounded-lg transition cursor-pointer {{ $preview_paper_size === '80mm' ? 'bg-white text-[#163144] shadow-2xs' : 'text-slate-500 hover:text-[#163144]' }}"
                        >
                            80mm
                        </button>
                    </div>
                </div>

                <!-- Receipt Container -->
                <div class="glass-card-plek p-4 sm:p-6 rounded-[24px] flex justify-center overflow-x-auto shadow-inner">
                    
                    <div 
                        class="bg-white text-black text-xs font-mono shadow-md transition-all duration-300 rounded-sm"
                        style="width: {{ $preview_paper_size === '80mm' ? '76mm' : '54mm' }}; padding: {{ $preview_paper_size === '80mm' ? '12px' : '8px' }}; line-height: 1.35; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Courier New', Courier;"
                    >
                        <!-- Header Toko -->
                        <div class="text-center mb-2">
                            <h3 class="text-base sm:text-lg font-normal tracking-tight text-black mb-1 leading-tight">
                                {{ $store_name ?: 'Syafa frozen food' }}
                            </h3>
                            @if($store_address)
                                <p class="text-[11px] leading-tight text-neutral-800 px-1">
                                    {{ $store_address }}
                                </p>
                            @endif
                            @if($store_phone)
                                <p class="text-[11px] text-neutral-800 mt-0.5">
                                    No.Tlp {{ $store_phone }}
                                </p>
                            @endif
                            @if($header_notes)
                                <p class="text-[11px] text-neutral-800 mt-0.5">
                                    {{ $header_notes }}
                                </p>
                            @endif
                        </div>

                        <!-- Divider 1 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Info Row: Tanggal, Kasir & No. Invoice -->
                        <div class="text-[11px] text-neutral-900 flex justify-between items-start leading-tight">
                            <div>
                                <div>{{ date('Y-m-d') }}</div>
                                <div>{{ date('H:i:s') }}</div>
                            </div>
                            <div class="text-right">
                                @if($show_cashier_name)
                                    <div>Kasir : amir ud</div>
                                @endif
                                <div>INV-{{ date('Ymd') }}-0012</div>
                            </div>
                        </div>

                        @if($show_customer_name)
                            <div class="text-[11px] text-neutral-900 mt-1">
                                Pelanggan : Pelanggan Umum
                            </div>
                        @endif

                        <!-- Divider 2 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Daftar Item Belanja -->
                        <div class="space-y-2 text-[11px] text-neutral-900">
                            <div>
                                <div class="font-bold">ILM TEMPURA</div>
                                <div class="flex justify-between items-center text-neutral-800">
                                    <span>10 &nbsp;x Rp 14.000</span>
                                    <span>Rp 140.000</span>
                                </div>
                            </div>

                            <div>
                                <div class="font-bold">ILM BINTANG</div>
                                <div class="flex justify-between items-center text-neutral-800">
                                    <span>10 &nbsp;x Rp 14.000</span>
                                    <span>Rp 140.000</span>
                                </div>
                            </div>

                            <div>
                                <div class="font-bold">ILM PANDA</div>
                                <div class="flex justify-between items-center text-neutral-800">
                                    <span>15 &nbsp;x Rp 14.500</span>
                                    <span>Rp 217.500</span>
                                </div>
                            </div>

                            <div>
                                <div class="font-bold">saos primasari 550g</div>
                                <div class="flex justify-between items-center text-neutral-800">
                                    <span>5 &nbsp;x Rp 4.000</span>
                                    <span>Rp 20.000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Divider 3 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Total dan Rincian Pembayaran -->
                        <div class="space-y-1 text-[11px] text-neutral-900">
                            <div class="flex justify-between">
                                <span>Total QTY : 40</span>
                                <span></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sub Total</span>
                                <span>Rp 517.500</span>
                            </div>
                            <div class="flex justify-between font-bold text-xs pt-1">
                                <span>Total</span>
                                <span>Rp 517.500</span>
                            </div>
                            <div class="flex justify-between pt-0.5">
                                <span>Bayar (Cash)</span>
                                <span>Rp 520.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kembalian</span>
                                <span>Rp 2.500</span>
                            </div>
                        </div>

                        <!-- Divider 4 -->
                        <div class="border-b border-dashed border-slate-300 my-2"></div>

                        <!-- Footer Struk -->
                        <div class="text-center text-[10px] text-neutral-800 space-y-1 pt-1">
                            @if($footer_link)
                                <div>Link Kritik dan Saran:</div>
                                <div class="font-normal break-all">{{ $footer_link }}</div>
                            @endif

                            @if($footer_notes)
                                <div class="pt-1 leading-tight">{{ $footer_notes }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-center text-[11px] text-slate-500 font-semibold mt-2">
                    * Format font dan kerapian baris akan tercetak persis seperti tampilan di atas pada kertas thermal.
                </div>
            </div>
        </div>

    </div>

</div>
