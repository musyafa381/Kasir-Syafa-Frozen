<div class="min-h-screen flex flex-col justify-center items-center py-10 px-4 sm:px-6 relative overflow-hidden">

    <!-- Ambient Frozen Glow Spots -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#38BDF8]/15 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-[#163144]/15 blur-3xl"></div>
    </div>

    <div class="w-full max-w-sm relative z-10">
        
        <!-- Header Brand Minimalis Frosted -->
        <div class="mb-7 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white/80 backdrop-blur-xl p-3.5 mb-3.5 border border-white/90 shadow-lg shadow-[#163144]/8 hover:scale-105 transition-transform duration-300">
                <img 
                    src="{{ asset('images/logo.svg') }}" 
                    alt="Logo Syafa Frozen" 
                    class="w-full h-full object-contain drop-shadow-xs"
                >
            </div>
            <h1 class="text-2xl sm:text-[26px] font-black tracking-tight text-[#163144]">
                Syafa Frozen Food
            </h1>
            <p class="text-xs text-slate-600 mt-1 font-semibold">
                Point of Sale &amp; Inventory System
            </p>
        </div>

        <!-- Frosted Glass Login Card -->
        <div class="glass-card-plek bg-white/60 backdrop-blur-2xl rounded-[32px] p-7 sm:p-8 border border-white/80 shadow-2xl shadow-[#163144]/10 text-slate-800">
            
            <form wire:submit="authenticate" class="space-y-4">
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-[11px] font-black uppercase tracking-wider text-[#163144] mb-2">
                        Alamat Email
                    </label>
                    <input 
                        wire:model="email" 
                        type="email" 
                        id="email" 
                        placeholder="nama@syafafrozen.com" 
                        autocomplete="email"
                        required
                        class="w-full px-4 py-3 bg-white/75 border {{ $errors->has('email') ? 'border-red-400 ring-2 ring-red-400/20' : 'border-white/90 focus:border-[#a0bddb] focus:ring-4 focus:ring-[#a0bddb]/25' }} rounded-2xl text-slate-800 placeholder-slate-400 text-sm font-semibold transition-all duration-200 outline-none shadow-2xs focus:bg-white"
                    >
                    @error('email')
                        <p class="text-rose-600 text-xs mt-1.5 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-[11px] font-black uppercase tracking-wider text-[#163144] mb-2">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <input 
                            wire:model="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            type="password"
                            id="password" 
                            placeholder="••••••••" 
                            autocomplete="current-password"
                            required
                            class="w-full pl-4 pr-11 py-3 bg-white/75 border {{ $errors->has('password') ? 'border-red-400 ring-2 ring-red-400/20' : 'border-white/90 focus:border-[#a0bddb] focus:ring-4 focus:ring-[#a0bddb]/25' }} rounded-2xl text-slate-800 placeholder-slate-400 text-sm font-semibold transition-all duration-200 outline-none shadow-2xs focus:bg-white"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#163144] p-1 rounded-lg transition-colors cursor-pointer focus:outline-none"
                            :title="showPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'"
                        >
                            <!-- Eye icon (lihat) -->
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye-slash icon (sembunyikan) -->
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-600 text-xs mt-1.5 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 px-5 bg-[#163144] hover:bg-[#20435c] active:bg-[#0e1e2b] text-white font-black rounded-2xl text-sm transition-all duration-200 shadow-lg shadow-[#163144]/25 hover:shadow-xl hover:shadow-[#163144]/35 active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 mt-2"
                >
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span>Masuk ke Sistem</span>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span>Memproses...</span>
                    </span>
                </button>
            </form>
        </div>

        <p class="text-center text-xs font-bold text-[#163144]/65 mt-6 tracking-wide">
            &copy; {{ date('Y') }} Syafa Frozen Food
        </p>

    </div>

</div>
