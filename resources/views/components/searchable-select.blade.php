@props([
    'options' => [],
    'valueField' => 'id',
    'labelField' => 'name',
    'sublabelField' => null,
    'placeholder' => 'Pilih...',
    'emptyOption' => null,
    'threshold' => 6,
    'wireModel' => null,
    'isLive' => false,
    'name' => null,
    'id' => null,
    'required' => false,
    'disabled' => false,
    'buttonClass' => null,
])

@php
    $normalizedOptions = [];

    if ($emptyOption !== null) {
        $normalizedOptions[] = [
            'value' => '',
            'label' => $emptyOption,
            'sublabel' => '',
        ];
    }

    foreach ($options as $key => $option) {
        if (is_array($option)) {
            $val = $option[$valueField] ?? ($option['value'] ?? $key);
            $lbl = $option[$labelField] ?? ($option['label'] ?? $option['name'] ?? $val);
            $sub = $sublabelField ? ($option[$sublabelField] ?? '') : ($option['sublabel'] ?? '');
        } elseif (is_object($option)) {
            $val = $option->{$valueField} ?? $option->id ?? $key;
            $lbl = $option->{$labelField} ?? $option->name ?? $val;
            $sub = '';
            if ($sublabelField && isset($option->{$sublabelField})) {
                $sub = (string)$option->{$sublabelField};
            } elseif (isset($option->stock) && isset($option->unit)) {
                $sub = 'Stok: ' . $option->stock . ' ' . $option->unit;
                if (isset($option->cost_price) && $option->cost_price > 0) {
                    $sub .= ' | HPP: Rp ' . number_format($option->cost_price, 0, ',', '.');
                }
            } elseif (isset($option->sku) && $option->sku) {
                $sub = 'SKU: ' . $option->sku;
            } elseif (isset($option->role)) {
                $sub = ucfirst($option->role);
            }
        } else {
            $val = $key;
            $lbl = $option;
            $sub = '';
        }

        $normalizedOptions[] = [
            'value' => (string)$val,
            'label' => (string)$lbl,
            'sublabel' => (string)$sub,
        ];
    }

    // Livewire wire:model detection
    $wireModelBinding = $attributes->wire('model');
    $wireProp = $wireModelBinding->value() ?: $wireModel;
    $live = $wireModelBinding->hasModifier('live') || $isLive;
    $inputId = $id ?: ($name ?: ($wireProp ?: 'select_' . uniqid()));
@endphp

<div 
    x-data="searchableSelect({
        options: {{ Js::from($normalizedOptions) }},
        placeholder: @js($placeholder),
        threshold: {{ (int)$threshold }},
        wireProp: @js($wireProp),
        isLive: {{ $live ? 'true' : 'false' }},
        initialValue: @js($attributes->get('value') ?? '')
    })"
    x-id="['searchable-select']"
    @click.outside="close()"
    @keydown="onKeyDown($event)"
    class="relative w-full text-left select-none"
>
    <!-- Hidden input for standard forms / Livewire fallback -->
    @if($name)
        <input 
            type="hidden" 
            name="{{ $name }}" 
            :value="value"
            @if($required) required @endif
        >
    @endif

    <!-- Trigger Button -->
    <button 
        type="button"
        @click="toggle()"
        :aria-expanded="open"
        class="{{ $buttonClass ?: 'w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition flex items-center justify-between gap-2 shadow-2xs hover:border-slate-300' }}"
        @if($disabled) disabled @endif
    >
        <div class="flex items-center gap-2 min-w-0 flex-1">
            <span 
                class="truncate text-xs" 
                :class="{ 'text-slate-400 font-normal': !value && value !== 0, 'text-slate-900 font-bold': value || value === 0 }"
                x-text="selectedLabel"
            ></span>
        </div>

        <div class="flex items-center gap-1.5 shrink-0 text-slate-400">
            <!-- Clear Button if item selected & emptyOption available -->
            <template x-if="value !== '' && {{ $emptyOption !== null ? 'true' : 'false' }}">
                <span 
                    @click.stop="select('')" 
                    class="hover:text-red-500 hover:bg-red-50 rounded-full w-4 h-4 flex items-center justify-center text-[11px] transition cursor-pointer"
                    title="Kosongkan pilihan"
                >
                    ✕
                </span>
            </template>

            <!-- Arrow Chevron -->
            <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180 text-blue-600': open }" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    <!-- Floating Dropdown Menu -->
    <div 
        x-show="open" 
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        class="absolute left-0 top-full mt-1.5 z-50 bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden w-full min-w-[280px] sm:min-w-[340px] max-w-[480px]"
    >
        <!-- Search Input (Only shown if total options > threshold) -->
        <template x-if="shouldShowSearch">
            <div class="p-2.5 bg-slate-50/80 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    x-ref="searchInput"
                    x-model="search"
                    @keydown.stop="onKeyDown($event)"
                    type="text" 
                    placeholder="Ketik untuk mencari..." 
                    class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                >
                <template x-if="search">
                    <button 
                        type="button" 
                        @click="search = ''; $refs.searchInput.focus()" 
                        class="text-slate-400 hover:text-slate-600 text-xs px-1 cursor-pointer"
                    >
                        ✕
                    </button>
                </template>
            </div>
        </template>

        <!-- Options List -->
        <div 
            x-ref="optionsList" 
            class="max-h-60 overflow-y-auto divide-y divide-slate-100/80 p-1 no-scrollbar focus:outline-none"
        >
            <template x-for="(opt, idx) in filteredOptions" :key="opt.value + '_' + idx">
                <div 
                    @click="select(opt.value)"
                    @mouseenter="highlightedIndex = idx"
                    :class="{
                        'bg-blue-50/90 text-blue-900 border-l-2 border-blue-600': String(opt.value) === String(value),
                        'bg-slate-50 text-slate-900': idx === highlightedIndex && String(opt.value) !== String(value),
                        'text-slate-700': idx !== highlightedIndex && String(opt.value) !== String(value)
                    }"
                    class="px-3 py-2 rounded-xl text-xs flex flex-col gap-0.5 cursor-pointer transition select-none"
                >
                    <!-- Top Row: Product / Option Name & Selection Mark -->
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <span 
                            class="font-semibold text-slate-800 truncate" 
                            :class="{ 'text-blue-950 font-bold': String(opt.value) === String(value) }" 
                            x-text="opt.label"
                        ></span>

                        <!-- Selected Checkmark -->
                        <template x-if="String(opt.value) === String(value)">
                            <span class="text-blue-600 font-bold text-xs shrink-0">✓</span>
                        </template>
                    </div>

                    <!-- Bottom Row: Sublabel Badge (Stok & HPP) -->
                    <template x-if="opt.sublabel">
                        <div class="flex items-center gap-1.5 pt-0.5">
                            <span 
                                class="text-[10px] font-mono px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 inline-block font-medium"
                                :class="{ 'bg-blue-100/90 text-blue-800 font-bold': String(opt.value) === String(value) }"
                                x-text="opt.sublabel"
                            ></span>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Empty Results State -->
            <template x-if="filteredOptions.length === 0">
                <div class="p-4 text-center text-xs text-slate-400">
                    Tidak ada data yang sesuai dengan pencarian.
                </div>
            </template>
        </div>

        <!-- Footer Count Info (if searchable) -->
        <template x-if="shouldShowSearch">
            <div class="px-3 py-1.5 bg-slate-50 border-t border-slate-100 text-[10px] text-slate-400 flex items-center justify-between">
                <span>Ditemukan: <strong class="text-slate-600" x-text="filteredOptions.length"></strong> data</span>
                <span class="text-[9px] text-slate-400">Gunakan ↑ ↓ Enter</span>
            </div>
        </template>
    </div>
</div>
