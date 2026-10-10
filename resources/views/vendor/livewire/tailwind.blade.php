@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 bg-white/30 border-t border-white/60">
        
        <!-- Info Ringkasan Data (Kiri) -->
        <div class="text-xs text-slate-500 font-semibold text-center sm:text-left">
            <span>Menampilkan</span>
            <span class="font-black text-[#163144] font-mono">{{ $paginator->firstItem() ?? 0 }}</span>
            <span>sampai</span>
            <span class="font-black text-[#163144] font-mono">{{ $paginator->lastItem() ?? 0 }}</span>
            <span>dari</span>
            <span class="font-black text-[#163144] font-mono">{{ $paginator->total() }}</span>
            <span>data</span>
        </div>

        <!-- Tombol / Badge Nomor Halaman (Kanan) -->
        <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
            @if ($paginator->hasPages())
                {{-- Tombol Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1.5 rounded-xl bg-white/20 text-slate-400 font-bold text-xs inline-flex items-center gap-1 cursor-not-allowed select-none opacity-50 border border-white/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Sebelumnya</span>
                    </span>
                @else
                    <button 
                        type="button"
                        wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                        wire:loading.attr="disabled"
                        class="px-3 py-1.5 rounded-xl glass-pill-plek text-[#163144] hover:bg-white/80 font-bold text-xs inline-flex items-center gap-1 transition shadow-2xs cursor-pointer select-none"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Sebelumnya</span>
                    </button>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($elements as $element)
                    {{-- String Pemisah "..." --}}
                    @if (is_string($element))
                        <span class="px-2 py-1 text-xs text-slate-400 font-black select-none">{{ $element }}</span>
                    @endif

                    {{-- Array Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="min-w-8 h-8 px-2 rounded-xl bg-[#163144] text-white font-black text-xs flex items-center justify-center shadow-xs select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <button 
                                    type="button"
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    class="min-w-8 h-8 px-2 rounded-xl glass-pill-plek text-[#163144] hover:bg-white/80 font-bold text-xs flex items-center justify-center transition shadow-2xs cursor-pointer select-none"
                                >
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Selanjutnya --}}
                @if ($paginator->hasMorePages())
                    <button 
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                        wire:loading.attr="disabled"
                        class="px-3 py-1.5 rounded-xl glass-pill-plek text-[#163144] hover:bg-white/80 font-bold text-xs inline-flex items-center gap-1 transition shadow-2xs cursor-pointer select-none"
                    >
                        <span>Selanjutnya</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <span class="px-3 py-1.5 rounded-xl bg-white/20 text-slate-400 font-bold text-xs inline-flex items-center gap-1 cursor-not-allowed select-none opacity-50 border border-white/30">
                        <span>Selanjutnya</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                @endif
            @else
                <span class="min-w-8 h-8 px-2 rounded-xl bg-[#163144] text-white font-black text-xs flex items-center justify-center shadow-xs select-none">
                    1
                </span>
            @endif
        </div>

    </nav>
@endif
