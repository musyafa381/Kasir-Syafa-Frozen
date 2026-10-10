<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - Syafa Frozen Food</title>
    <link rel="icon" type="image/png" href="{{ asset('kasirku.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen p-4 sm:p-8">

    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-end no-print">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 print:border-none print:shadow-none print:p-0 print:m-0 print:max-w-full">
        
        <!-- Header Kop -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kasirku.png') }}" alt="Logo Syafa" class="w-14 h-14 rounded-2xl object-contain border border-slate-100 p-0.5">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $receiptSetting->store_name ?: 'SYAFA FROZEN FOOD' }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">Laporan Laba Rugi Komprehensif (Income Statement)</p>
                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $receiptSetting->store_address }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-black uppercase tracking-wider">
                    LAPORAN LABA RUGI
                </span>
                <p class="text-xs font-bold text-slate-700 font-mono mt-2">
                    Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}
                </p>
                <p class="text-[11px] text-slate-400">Dicetak: {{ date('d/m/Y H:i') }} WIB</p>
            </div>
        </div>

        <!-- Income Statement Breakdown Structure -->
        <div class="space-y-6 text-xs mb-8">
            
            <!-- 1. PENDAPATAN -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="p-3 bg-slate-900 text-white font-bold text-xs uppercase tracking-wider flex justify-between">
                    <span>1. PENDAPATAN OPERASIONAL (REVENUE)</span>
                    <span class="font-mono">JUMLAH (RP)</span>
                </div>
                <div class="p-4 space-y-2 bg-white font-mono">
                    <div class="flex justify-between py-1 text-slate-700">
                        <span>Penjualan Bersih Toko (Gross Sales)</span>
                        <span class="font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-slate-100 font-bold text-slate-900">
                        <span>TOTAL PENDAPATAN BERSIH</span>
                        <span class="text-blue-700 text-sm">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. HPP (HARGA POKOK PENJUALAN) -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="p-3 bg-slate-900 text-white font-bold text-xs uppercase tracking-wider flex justify-between">
                    <span>2. BEBAN POKOK PENJUALAN (HPP / COGS)</span>
                    <span class="font-mono">JUMLAH (RP)</span>
                </div>
                <div class="p-4 space-y-2 bg-white font-mono">
                    <div class="flex justify-between py-1 text-slate-700">
                        <span>Total Biaya Modal Produk Terjual (HPP)</span>
                        <span class="text-rose-600 font-bold">- Rp {{ number_format($totalCogs, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-slate-100 font-bold text-slate-900 bg-blue-50/50 p-2 rounded-xl">
                        <span>LABA KOTOR PENJUALAN (GROSS PROFIT)</span>
                        <span class="text-blue-700 text-sm">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. BIAYA OPERASIONAL (OPEX) -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="p-3 bg-slate-900 text-white font-bold text-xs uppercase tracking-wider flex justify-between">
                    <span>3. BIAYA & BEBAN OPERASIONAL TOKO (OPEX)</span>
                    <span class="font-mono">JUMLAH (RP)</span>
                </div>
                <div class="p-4 space-y-2 bg-white font-mono">
                    @forelse($expenses as $exp)
                        <div class="flex justify-between py-1 text-slate-700">
                            <span class="font-sans">{{ $exp->title }} <em class="text-[11px] text-slate-400">({{ $exp->category ?: 'Operasional' }})</em></span>
                            <span class="text-rose-600 font-bold">- Rp {{ number_format($exp->amount, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="py-3 text-center text-slate-400 font-sans">Tidak ada catatan biaya operasional pada periode ini.</div>
                    @endforelse
                    <div class="flex justify-between py-2 border-t border-slate-100 font-bold text-slate-900">
                        <span>TOTAL BIAYA OPERASIONAL</span>
                        <span class="text-rose-600 text-sm">- Rp {{ number_format($totalExpenses, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 4. FINAL NET PROFIT CARD -->
            <div class="p-6 rounded-2xl bg-slate-900 text-white flex items-center justify-between shadow-md">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LABA BERSIH RIIL USAHA (NET PROFIT)</span>
                    <span class="text-2xl font-black font-mono text-emerald-400 mt-1 block">
                        {{ $netProfit >= 0 ? '+' : '' }}Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </span>
                </div>
                <div class="text-right text-xs">
                    <span class="text-slate-400 block font-mono">Margin Laba Bersih:</span>
                    <span class="text-base font-black font-mono text-white">
                        {{ $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>

        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-6 border-t border-slate-200">
            <div>
                <p class="text-slate-500 mb-16">Dibuat Oleh (Admin Keuangan),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ auth()->user()->name ?? 'Finance Syafa' }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Mengetahui (Owner Toko),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">Management Syafa</p>
            </div>
        </div>

    </div>

</body>
</html>
