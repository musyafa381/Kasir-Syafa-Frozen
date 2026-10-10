<div class="space-y-4">

    <!-- Top Breadcrumb & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-[#386797] uppercase tracking-wider mb-0.5">
                <a href="{{ route('admin.employees.index') }}" wire:navigate class="hover:underline">Karyawan & Staf</a>
                <span>•</span>
                <span class="text-[#163144] font-black">{{ $isEditing ? 'Edit Data' : 'Tambah Baru' }}</span>
            </div>
            <h1 class="text-xl font-black text-[#163144] tracking-tight">
                {{ $isEditing ? 'Edit Data Karyawan: ' . $name : 'Tambah Karyawan Baru' }}
            </h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                {{ $isEditing ? 'Perbarui profil staf, standar gaji, atau rekening pembayaran.' : 'Daftarkan profil staf operasional, standar gaji pokok, dan rekening bank.' }}
            </p>
        </div>

        <div>
            <a 
                href="{{ route('admin.employees.index') }}" 
                wire:navigate 
                class="btn-glass-action inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl font-extrabold text-xs"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit="save" class="space-y-4">

        <!-- Card 1: Informasi Pribadi & Pekerjaan -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/40 pb-3">
                <div class="w-7 h-7 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs">1</div>
                <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">Informasi Pribadi & Pekerjaan</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                <!-- Kode Karyawan -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Kode Karyawan <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        wire:model="employee_code" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl font-mono text-xs font-black text-[#163144] shadow-2xs"
                        readonly
                    >
                    @error('employee_code') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Lengkap -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        placeholder="Contoh: Budi Santoso / Mas Yulai" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        required
                    >
                    @error('name') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Posisi / Jabatan -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Posisi / Jabatan <span class="text-rose-500">*</span></label>
                    <input 
                        type="text" 
                        wire:model="position" 
                        placeholder="Kasir POS / Staff Gudang / Pramuniaga" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                        required
                    >
                    @error('position') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Status Kepegawaian -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Status Kepegawaian <span class="text-rose-500">*</span></label>
                    <select 
                        wire:model="employment_status" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                    >
                        <option value="tetap">Karyawan Tetap</option>
                        <option value="kontrak">Kontrak</option>
                        <option value="harian">Harian Lepas</option>
                        <option value="part_time">Part Time</option>
                    </select>
                    @error('employment_status') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- No. Telepon / WhatsApp -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                    <input 
                        type="text" 
                        wire:model="phone" 
                        placeholder="08123456789" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl font-mono text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    @error('phone') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Mulai Bekerja -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Tanggal Mulai Bekerja</label>
                    <input 
                        type="date" 
                        wire:model="join_date" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] focus:bg-white focus:border-[#163144] outline-none shadow-2xs cursor-pointer"
                    >
                    @error('join_date') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Alamat Domisili -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Alamat Domisili</label>
                    <input 
                        type="text" 
                        wire:model="address" 
                        placeholder="Alamat tempat tinggal staf" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                    @error('address') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Standar Gaji & Rekening Pembayaran -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/40 pb-3">
                <div class="w-7 h-7 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs">2</div>
                <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">Standar Gaji & Rekening Pembayaran</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                <!-- Gaji Pokok -->
                <div 
                    x-data="{
                        displayVal: '',
                        init() {
                            this.updateDisplay($wire.base_salary);
                            $watch('$wire.base_salary', val => this.updateDisplay(val));
                        },
                        updateDisplay(val) {
                            if (!val && val !== 0) { this.displayVal = ''; return; }
                            let num = parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
                            this.displayVal = num ? num.toLocaleString('id-ID') : '';
                        },
                        handleInput(e) {
                            let clean = e.target.value.replace(/[^0-9]/g, '');
                            let num = parseInt(clean) || 0;
                            this.displayVal = num ? num.toLocaleString('id-ID') : '';
                            $wire.set('base_salary', num);
                        }
                    }"
                >
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Gaji Pokok Default (Rp) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center rounded-xl border border-white/80 bg-white/70 focus-within:bg-white focus-within:border-[#163144] overflow-hidden shadow-2xs transition">
                        <span class="px-3.5 py-2.5 bg-white/50 text-slate-500 font-black text-xs border-r border-white/80 select-none shrink-0">
                            Rp
                        </span>
                        <input 
                            type="text" 
                            x-model="displayVal"
                            @input="handleInput($event)"
                            @focus="$event.target.select()"
                            placeholder="Contoh: 2.800.000" 
                            class="w-full px-3.5 py-2 bg-transparent font-mono text-xs font-black text-[#163144] outline-none placeholder:text-slate-400"
                            required
                        >
                    </div>
                    @error('base_salary') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Hubungkan Akun User/Kasir (Searchable) -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Hubungkan ke Akun Login Kasir / POS (Opsional)</label>
                    <x-searchable-select 
                        wire:model="user_id" 
                        :options="$userOptions" 
                        placeholder="— Tidak Terhubung ke Akun User —" 
                        empty-option="— Tidak Terhubung ke Akun User —" 
                        threshold="1"
                        sublabel-field="sublabel"
                    />
                    @error('user_id') <span class="text-rose-500 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Bank / E-Wallet -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Nama Bank / E-Wallet</label>
                    <input 
                        type="text" 
                        wire:model="bank_name" 
                        placeholder="BCA / BRI / Mandiri / Cash" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                </div>

                <!-- No. Rekening -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">No. Rekening</label>
                    <input 
                        type="text" 
                        wire:model="bank_account_number" 
                        placeholder="1234567890" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl font-mono text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                </div>

                <!-- Atas Nama Rekening -->
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                    <input 
                        type="text" 
                        wire:model="bank_account_holder" 
                        placeholder="Nama pemilik buku tabungan" 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-bold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                </div>
            </div>
        </div>

        <!-- Card 3: Status & Catatan -->
        <div class="glass-card-plek rounded-[24px] p-5 sm:p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/40 pb-3">
                <div class="w-7 h-7 rounded-xl bg-[#163144] text-white flex items-center justify-center font-black text-xs">3</div>
                <h2 class="text-xs sm:text-sm font-black text-[#163144] uppercase tracking-wider">Status & Catatan</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                <div>
                    <label class="block text-xs font-extrabold text-[#163144] uppercase tracking-wider mb-1.5">Catatan Staf (Opsional)</label>
                    <input 
                        type="text" 
                        wire:model="notes" 
                        placeholder="Catatan tambahan mengenai karyawan ini..." 
                        class="w-full px-3.5 py-2.5 bg-white/70 border border-white/80 rounded-xl text-xs font-semibold text-[#163144] placeholder-slate-400 focus:bg-white focus:border-[#163144] outline-none shadow-2xs"
                    >
                </div>

                <div class="flex items-center pt-5">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            wire:model="is_active" 
                            class="w-4 h-4 text-[#163144] rounded border-slate-300 focus:ring-[#163144] cursor-pointer"
                        >
                        <span class="text-xs font-bold text-[#163144]">Status Karyawan Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a 
                href="{{ route('admin.employees.index') }}" 
                wire:navigate 
                class="btn-glass-action px-5 py-2.5 rounded-2xl font-extrabold text-xs"
            >
                Batal
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl glass-pill-dark text-white font-extrabold text-xs shadow-md hover:opacity-95 transition cursor-pointer"
            >
                <span>{{ $isEditing ? 'Simpan Perubahan' : 'Tambah Karyawan' }}</span>
            </button>
        </div>
    </form>
</div>
