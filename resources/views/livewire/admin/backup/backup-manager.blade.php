<div class="space-y-4">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">Backup Data & Sinkronisasi Cloud</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Arsipkan data transaksi toko secara fleksibel, unduh format Excel/CSV/JSON & sinkronkan ke Google Drive.</p>
        </div>

        <div class="w-full sm:w-auto">
            <a 
                href="{{ $googleDriveFolderUrl ?: 'https://drive.google.com' }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="btn-glass-action inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                </svg>
                <span>Buka Google Drive Toko</span>
            </a>
        </div>
    </div>

    <!-- 3 Summary Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
        
        <!-- 1. Total Arsip -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#163144] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Arsip</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Total Arsip</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#163144] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Database
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ $totalBackupsCount }} <span class="text-[10px] sm:text-xs font-bold text-slate-500 font-sans">File</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">File arsip tersimpan</p>
            </div>
        </div>

        <!-- 2. Ukuran Penyimpanan -->
        <div class="glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-[#386797] flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Ukuran</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Kapasitas Server</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-[#386797] font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Storage
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-base sm:text-xl font-black text-[#163144] font-mono tracking-tight block">
                    {{ $totalStorageFormatted }}
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Total pemakaian disk</p>
            </div>
        </div>

        <!-- 3. Google Drive Status -->
        <div class="col-span-2 sm:col-span-1 glass-card-plek rounded-[22px] p-3 sm:p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-white/40 gap-1">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/70 text-amber-600 flex items-center justify-center border border-white/80 shadow-2xs shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 block leading-tight truncate">Cloud</span>
                        <span class="font-bold text-[#163144] text-[11px] sm:text-xs block truncate">Google Drive</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full glass-pill-plek text-emerald-800 font-extrabold text-[9px] sm:text-[10px] shrink-0">
                    Cloud
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <span class="text-xs sm:text-sm font-black text-emerald-800 flex items-center gap-1.5 truncate">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>Tersinkronisasi Manual</span>
                </span>
                <p class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5 truncate">Siap upload ke folder cloud</p>
            </div>
        </div>

    </div>

    <!-- Generator & Google Drive Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

        <!-- Left 2 Cols: Form Buat Backup Baru -->
        <div class="lg:col-span-2 glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="pb-3 border-b border-white/40 flex items-center justify-between">
                <div>
                    <h2 class="font-black text-[#163144] text-sm">Buat Arsip Backup Transaksi Baru</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Pilih periode dan format file yang ingin dicadangkan ke arsip.</p>
                </div>
                <span class="px-3 py-1 rounded-full glass-pill-plek text-[#163144] font-black text-xs">
                    Database Aman
                </span>
            </div>

            <form wire:submit="generateBackup" class="space-y-4">
                
                <!-- 1. Mode Backup -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#163144] mb-1.5">Pilih Mode Periode:</label>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="$set('backupMode', 'monthly')"
                            class="px-4 py-2 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $backupMode === 'monthly' ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
                        >
                            Arsip Bulanan
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('backupMode', 'custom')"
                            class="px-4 py-2 rounded-full text-xs font-extrabold transition cursor-pointer shrink-0 {{ $backupMode === 'custom' ? 'bg-[#163144] text-white shadow-md' : 'btn-glass-action' }}"
                        >
                            Rentang Tanggal
                        </button>
                    </div>
                </div>

                <!-- 2. Inputs by Mode -->
                @if($backupMode === 'monthly')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Bulan Transaksi</label>
                            <select 
                                wire:model.live="selectedMonth" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                            >
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Tahun</label>
                            <select 
                                wire:model.live="selectedYear" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs cursor-pointer"
                            >
                                @for($y = 2024; $y <= 2030; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Mulai Tanggal</label>
                            <input 
                                type="date" 
                                wire:model="startDate" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs"
                            >
                            @error('startDate') <span class="text-[11px] text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                            <input 
                                type="date" 
                                wire:model="endDate" 
                                class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] outline-none focus:bg-white focus:border-[#163144] shadow-2xs"
                            >
                            @error('endDate') <span class="text-[11px] text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <!-- 3. Format File -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#163144] mb-1.5">Pilih Format File:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="p-3.5 rounded-2xl border cursor-pointer transition flex items-start gap-2.5 {{ $backupFormat === 'excel' ? 'bg-white/80 border-[#163144] shadow-2xs' : 'glass-pill-plek border-white/60 hover:bg-white/60' }}">
                            <input type="radio" wire:model.live="backupFormat" value="excel" class="mt-0.5 text-[#163144] focus:ring-[#163144]">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-xs text-[#163144]">Excel (.xls)</span>
                                    <span class="text-[9px] font-black text-emerald-800 glass-pill-plek px-1.5 py-0.2 rounded-full font-mono">Utama</span>
                                </div>
                                <span class="text-[11px] text-slate-500 font-medium block mt-1 leading-tight">Tabel rapi bergaris, kolom terpisah otomatis & total rekap.</span>
                            </div>
                        </label>

                        <label class="p-3.5 rounded-2xl border cursor-pointer transition flex items-start gap-2.5 {{ $backupFormat === 'csv' ? 'bg-white/80 border-[#163144] shadow-2xs' : 'glass-pill-plek border-white/60 hover:bg-white/60' }}">
                            <input type="radio" wire:model.live="backupFormat" value="csv" class="mt-0.5 text-[#163144] focus:ring-[#163144]">
                            <div>
                                <span class="font-black text-xs text-[#163144] block">CSV File (.csv)</span>
                                <span class="text-[11px] text-slate-500 font-medium block mt-1 leading-tight">Data pemisah titik koma standar untuk integrasi eksternal.</span>
                            </div>
                        </label>

                        <label class="p-3.5 rounded-2xl border cursor-pointer transition flex items-start gap-2.5 {{ $backupFormat === 'json' ? 'bg-white/80 border-[#163144] shadow-2xs' : 'glass-pill-plek border-white/60 hover:bg-white/60' }}">
                            <input type="radio" wire:model.live="backupFormat" value="json" class="mt-0.5 text-[#163144] focus:ring-[#163144]">
                            <div>
                                <span class="font-black text-xs text-[#163144] block">JSON (.json)</span>
                                <span class="text-[11px] text-slate-500 font-medium block mt-1 leading-tight">Struktur data mentah untuk migrasi atau cadangan developer.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Preview Info Box -->
                <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Estimasi Data Terdeteksi:</span>
                        <span class="font-black text-xs text-[#163144] font-mono">
                            {{ $previewCount }} Transaksi Selesai • Total Rp {{ number_format($previewAmount, 0, ',', '.') }}
                        </span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black font-mono glass-pill-plek {{ $previewCount > 0 ? 'text-emerald-800' : 'text-amber-800' }}">
                        {{ $previewCount > 0 ? 'Siap Dicadangkan' : 'Belum Ada Transaksi' }}
                    </span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-6 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md hover:opacity-95 transition cursor-pointer disabled:opacity-50"
                    >
                        <svg wire:loading.remove wire:target="generateBackup" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <svg wire:loading wire:target="generateBackup" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Buat Arsip & Download File Sekarang</span>
                    </button>
                </div>

            </form>
        </div>

        <!-- Right 1 Col: Google Drive Integration & Folder Link -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-white/40">
                    <div class="w-8 h-8 rounded-xl bg-white/70 text-amber-600 flex items-center justify-center border border-white/80 shadow-2xs">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-black text-[#163144] text-sm">Penyimpanan Google Drive</h2>
                        <p class="text-[11px] text-slate-500 font-semibold">Simpan salinan cadangan di Cloud Drive</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs text-slate-600 leading-relaxed font-semibold">
                    <p class="font-black text-[#163144]">Cara Menyimpan ke Google Drive:</p>
                    <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-500 font-medium pl-0.5">
                        <li>Klik tombol <strong>"Buat Arsip & Download"</strong>.</li>
                        <li>Klik tombol <strong>"Drive"</strong> pada tabel arsip di bawah.</li>
                        <li>Google Drive terbuka, Anda tinggal <em>drag & drop</em> file backup ke folder toko.</li>
                    </ol>
                </div>

                <!-- Custom Google Drive Folder URL Setting -->
                <div class="pt-2 border-t border-white/40 space-y-2">
                    <label class="block text-[10px] font-extrabold text-[#163144] uppercase tracking-wider">Tautan Folder Google Drive Toko:</label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="url" 
                            wire:model="googleDriveFolderUrl"
                            placeholder="https://drive.google.com/drive/folders/..."
                            class="w-full px-3.5 py-2 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none truncate shadow-2xs"
                        >
                        <button 
                            type="button" 
                            wire:click="saveGoogleDriveUrl"
                            class="btn-glass-action px-3.5 py-2 rounded-xl text-xs font-extrabold shrink-0 cursor-pointer"
                        >
                            Simpan
                        </button>
                    </div>
                    <span class="text-[10px] text-slate-400 block font-medium">Tautan ini memudahkan membuka folder backup Google Drive dengan 1 klik.</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl glass-pill-plek border border-white/60 text-[#163144] text-[11px] font-semibold leading-relaxed">
                🛡️ <strong>Keamanan Maksimal</strong>: Backup berkala setiap akhir bulan menjamin seluruh riwayat penjualan tetap aman tersimpan di cloud.
            </div>
        </div>

    </div>

    <!-- Table Section: Riwayat Arsip Backup Tersimpan -->
    <div class="glass-card-plek rounded-[24px] overflow-hidden">
        
        <div class="p-4 border-b border-white/40 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="font-black text-[#163144] text-sm">Riwayat Arsip Backup di Server</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Daftar file arsip penjualan yang telah dibuat dan siap diunduh kembali.</p>
            </div>
        </div>

        <!-- Desktop Table (>= sm) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[650px]">
                <thead class="bg-white/40 text-[#163144] font-black uppercase text-[10px] border-b border-white/60 tracking-wider">
                    <tr>
                        <th class="p-3.5 pl-5 whitespace-nowrap">Nama File & Format</th>
                        <th class="p-3.5 whitespace-nowrap">Periode Transaksi</th>
                        <th class="p-3.5 text-center whitespace-nowrap">Jumlah</th>
                        <th class="p-3.5 text-right whitespace-nowrap">Total Omset</th>
                        <th class="p-3.5 whitespace-nowrap">Ukuran & Waktu</th>
                        <th class="p-3.5 pr-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 font-semibold">
                    @forelse($backupLogs as $log)
                        <tr class="hover:bg-white/40 transition">
                            <!-- Filename & Format -->
                            <td class="p-3.5 pl-5">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-[9px] uppercase font-mono shrink-0 glass-pill-plek text-[#163144]">
                                        {{ $log->backup_format === 'excel' ? 'XLS' : ($log->backup_format === 'csv' ? 'CSV' : 'JSON') }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="font-extrabold text-[#163144] block truncate text-xs" title="{{ $log->filename }}">{{ $log->filename }}</span>
                                        <span class="text-[10px] text-slate-400 block truncate">Dibuat oleh: {{ $log->user?->name ?? 'Admin' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Period -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-bold text-[#163144] block text-xs">{{ $log->period_label }}</span>
                                <span class="text-[10px] text-slate-400 font-mono block">{{ $log->start_date?->format('d/m/Y') }} - {{ $log->end_date?->format('d/m/Y') }}</span>
                            </td>

                            <!-- Count -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-mono font-black text-xs inline-block">
                                    {{ number_format($log->total_transactions) }} nota
                                </span>
                            </td>

                            <!-- Total Omzet -->
                            <td class="p-3.5 text-right font-mono font-black text-[#163144] text-xs whitespace-nowrap">
                                Rp {{ number_format($log->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- File size & Date -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-mono text-[#163144] block font-bold text-xs">{{ $log->formatted_file_size }}</span>
                                <span class="text-[10px] text-slate-400 block font-mono">{{ $log->created_at?->translatedFormat('d/m/Y H:i') }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                    
                                    <!-- Download Button -->
                                    <button 
                                        type="button" 
                                        wire:click="downloadBackup({{ $log->id }})"
                                        class="btn-glass-action inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-xs"
                                        title="Download File ke Komputer/HP"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Download</span>
                                    </button>

                                    <!-- Google Drive Sync Link -->
                                    <a 
                                        href="{{ $googleDriveFolderUrl ?: 'https://drive.google.com' }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full font-bold text-xs"
                                        title="Buka Google Drive untuk Upload"
                                    >
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z"/>
                                        </svg>
                                        <span>Drive</span>
                                    </a>

                                    <!-- Delete Button -->
                                    <button 
                                        type="button" 
                                        @click="window.customConfirm ? window.customConfirm({ title: 'Hapus File Backup', message: 'Apakah Anda yakin ingin menghapus arsip file backup ini dari server?', confirmText: 'Ya, Hapus', type: 'danger', onConfirm: () => $wire.deleteBackup({{ $log->id }}) }) : $wire.deleteBackup({{ $log->id }})"
                                        class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full font-bold text-xs"
                                        title="Hapus File Arsip"
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
                            <td colspan="6" class="text-center py-10 text-slate-400 font-semibold">
                                Belum ada file arsip backup yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List (< sm) -->
        <div class="sm:hidden divide-y divide-white/40">
            @forelse($backupLogs as $log)
                <div class="p-3.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-[9px] uppercase font-mono shrink-0 glass-pill-plek text-[#163144]">
                                {{ $log->backup_format === 'excel' ? 'XLS' : ($log->backup_format === 'csv' ? 'CSV' : 'JSON') }}
                            </div>
                            <div class="min-w-0">
                                <span class="font-extrabold text-[#163144] block truncate text-xs">{{ $log->filename }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">{{ $log->period_label }} • {{ $log->formatted_file_size }}</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full glass-pill-plek text-[#163144] font-mono font-black text-[10px] shrink-0">
                            {{ number_format($log->total_transactions) }} nota
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-[11px] glass-pill-plek p-2.5 rounded-2xl border border-white/60">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Total Omset</span>
                            <span class="font-mono font-black text-[#163144] text-xs">Rp {{ number_format($log->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block font-semibold">Waktu Dibuat</span>
                            <span class="font-mono text-[10px] text-slate-500 font-bold">{{ $log->created_at?->translatedFormat('d/m/y H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-1.5 pt-0.5">
                        <button 
                            type="button" 
                            wire:click="downloadBackup({{ $log->id }})"
                            class="btn-glass-action inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-xs"
                        >
                            Download
                        </button>

                        <a 
                            href="{{ $googleDriveFolderUrl ?: 'https://drive.google.com' }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full font-bold text-xs"
                        >
                            Drive
                        </a>

                        <button 
                            type="button" 
                            @click="window.customConfirm ? window.customConfirm({ title: 'Hapus File Backup', message: 'Apakah Anda yakin ingin menghapus arsip file backup ini dari server?', confirmText: 'Ya, Hapus', type: 'danger', onConfirm: () => $wire.deleteBackup({{ $log->id }}) }) : $wire.deleteBackup({{ $log->id }})"
                            class="btn-glass-action inline-flex items-center gap-1 px-3 py-1.5 rounded-full font-bold text-xs"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 font-semibold px-4">
                    Belum ada file arsip backup yang dibuat.
                </div>
            @endforelse
        </div>

        @if($backupLogs->hasPages())
            <div class="p-4 border-t border-white/60 bg-white/40">
                {{ $backupLogs->links() }}
            </div>
        @endif

    </div>

</div>
