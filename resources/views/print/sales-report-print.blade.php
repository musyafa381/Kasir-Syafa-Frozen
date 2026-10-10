<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Syafa Frozen Food</title>
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

    <div class="max-w-5xl mx-auto mb-6 flex items-center justify-end no-print">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 print:border-none print:shadow-none print:p-0 print:m-0 print:max-w-full">
        
        <!-- Header Kop Laporan -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kasirku.png') }}" alt="Logo Syafa" class="w-14 h-14 rounded-2xl object-contain border border-slate-100 p-0.5">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $receiptSetting->store_name ?: 'SYAFA FROZEN FOOD' }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">Laporan Rekapitulasi Arus Penjualan & Omset Kasir</p>
                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $receiptSetting->store_address }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs font-black uppercase tracking-wider">
                    LAPORAN PENJUALAN
                </span>
                <p class="text-xs font-bold text-slate-700 font-mono mt-2">
                    Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}
                </p>
                <p class="text-[11px] text-slate-400">Dicetak: {{ date('d/m/Y H:i') }} WIB</p>
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-4 gap-4 mb-6 text-xs">
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">TOTAL TRANSAKSI</span>
                <span class="text-lg font-black font-mono text-slate-900 mt-1 block">{{ $transactions->count() }} Nota</span>
            </div>
            <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-200">
                <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider block">TOTAL OMSET KOTOR</span>
                <span class="text-lg font-black font-mono text-blue-700 mt-1 block">Rp {{ number_format($totalOmset, 0, ',', '.') }}</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">TOTAL HPP MODAL</span>
                <span class="text-lg font-black font-mono text-slate-700 mt-1 block">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
            </div>
            <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200">
                <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider block">LABA KOTOR PENJUALAN</span>
                <span class="text-lg font-black font-mono text-emerald-700 mt-1 block">+Rp {{ number_format($totalLabaKotor, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tabel Rincian Transaksi -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-3 w-10 text-center">No</th>
                        <th class="py-3 px-3">No. Invoice</th>
                        <th class="py-3 px-3">Waktu</th>
                        <th class="py-3 px-3">Kasir</th>
                        <th class="py-3 px-3">Pelanggan</th>
                        <th class="py-3 px-3 text-center">Metode</th>
                        <th class="py-3 px-3 text-right">Subtotal</th>
                        <th class="py-3 px-3 text-right">Omset (Rp)</th>
                        <th class="py-3 px-3 text-right">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions as $idx => $trx)
                        @php
                            $hpp = 0;
                            foreach($trx->items as $it) { $hpp += ($it->cost_price * $it->quantity); }
                            $profit = $trx->grand_total - $hpp;
                        @endphp
                        <tr class="{{ $idx % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="py-2.5 px-3 text-center font-mono text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-3 font-bold font-mono text-slate-900">{{ $trx->invoice_number }}</td>
                            <td class="py-2.5 px-3 font-mono text-slate-600">{{ $trx->created_at->format('d/m H:i') }}</td>
                            <td class="py-2.5 px-3 text-slate-700">{{ $trx->user->name ?? '-' }}</td>
                            <td class="py-2.5 px-3 text-slate-800">{{ $trx->customer_name }}</td>
                            <td class="py-2.5 px-3 text-center uppercase font-mono text-[11px] font-semibold">{{ $trx->payment_method }}</td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-700">+Rp {{ number_format($profit, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada data transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-100 font-bold font-mono text-xs border-t-2 border-slate-300">
                    <tr>
                        <td colspan="7" class="py-3 px-3 text-right text-slate-900">TOTAL AKUMULASI:</td>
                        <td class="py-3 px-3 text-right text-blue-700 font-black">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-emerald-700 font-black">+Rp {{ number_format($totalLabaKotor, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-6 border-t border-slate-200">
            <div>
                <p class="text-slate-500 mb-16">Dibuat Oleh (Admin / Kasir),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ auth()->user()->name ?? 'Staf Kasir' }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Mengetahui (Owner Toko),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">Management Syafa</p>
            </div>
        </div>

    </div>

</body>
</html>
