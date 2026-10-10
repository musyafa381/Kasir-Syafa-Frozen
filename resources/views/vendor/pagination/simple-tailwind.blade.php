@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between gap-3 px-4 py-3 bg-white/30 border-t border-white/60">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3.5 py-1.5 rounded-xl bg-white/20 text-slate-400 font-bold text-xs inline-flex items-center gap-1 cursor-not-allowed select-none opacity-50 border border-white/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Sebelumnya</span>
            </span>
        @else
            <a 
                href="{{ $paginator->previousPageUrl() }}" 
                class="px-3.5 py-1.5 rounded-xl glass-pill-plek text-[#163144] hover:bg-white/80 font-bold text-xs inline-flex items-center gap-1 transition shadow-2xs select-none"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a 
                href="{{ $paginator->nextPageUrl() }}" 
                class="px-3.5 py-1.5 rounded-xl glass-pill-plek text-[#163144] hover:bg-white/80 font-bold text-xs inline-flex items-center gap-1 transition shadow-2xs select-none"
            >
                <span>Selanjutnya</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span class="px-3.5 py-1.5 rounded-xl bg-white/20 text-slate-400 font-bold text-xs inline-flex items-center gap-1 cursor-not-allowed select-none opacity-50 border border-white/30">
                <span>Selanjutnya</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif

    </nav>
@endif
