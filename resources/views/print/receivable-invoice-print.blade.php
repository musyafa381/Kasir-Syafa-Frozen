<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tagihan Piutang - {{ $transaction->invoice_number }}</title>
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
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 print:border-none print:shadow-none print:p-0 print:m-0 print:max-w-full">
        
        <!-- Header Kop Surat Tagihan -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kasirku.png') }}" alt="Logo Syafa" class="w-14 h-14 rounded-2xl object-contain border border-slate-100 p-0.5">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $receiptSetting->store_name ?: 'SYAFA FROZEN FOOD' }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">Surat Tagihan Pembayaran Tempo (Accounts Receivable Invoice)</p>
                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $receiptSetting->store_address }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs font-black uppercase tracking-wider">
                    SURAT TAGIHAN TEMPO
                </span>
                <p class="text-sm font-black font-mono text-slate-900 mt-2">{{ $transaction->invoice_number }}</p>
                <p class="text-xs text-slate-500">Tanggal Nota: {{ $transaction->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Info Pelanggan & Jatuh Tempo Card -->
        <div class="grid grid-cols-2 gap-6 my-6 text-xs bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">DITAGIHKAN KEPADA:</span>
                <h3 class="text-sm font-bold text-slate-900">{{ $transaction->customer_name }}</h3>
                @if($transaction->customer?->phone)
                    <p class="text-slate-600 font-mono mt-0.5">No. HP / WA: {{ $transaction->customer->phone }}</p>
                @endif
                @if($transaction->customer?->address)
                    <p class="text-slate-500 mt-0.5">{{ $transaction->customer->address }}</p>
                @endif
            </div>
            <div class="space-y-1.5 border-l border-slate-200 pl-6">
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Jatuh Tempo:</span> <strong class="text-rose-700 font-mono font-bold">{{ $transaction->due_date ? $transaction->due_date->translatedFormat('d F Y') : '-' }}</strong></p>
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Total Nilai Belanja:</span> <strong class="text-slate-900 font-mono">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</strong></p>
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Sudah Dibayar (DP):</span> <strong class="text-emerald-700 font-mono">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</strong></p>
                <p class="flex justify-between pt-1 border-t border-slate-200"><span class="text-slate-700 font-bold">SISA TAGIHAN:</span> <strong class="text-rose-700 font-mono font-black text-sm">Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}</strong></p>
            </div>
        </div>

        <!-- Tabel Item Pembelian -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Produk Frozen</th>
                        <th class="py-3 px-4 text-center w-20">Qty</th>
                        <th class="py-3 px-4 text-right w-32">Harga Satuan</th>
                        <th class="py-3 px-4 text-right w-36">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($transaction->items as $idx => $item)
                        <tr class="{{ $idx % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="py-3 px-4 text-center font-mono text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->product_name }}</td>
                            <td class="py-3 px-4 text-center font-mono font-bold">{{ $item->quantity }}</td>
                            <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Instruksi Pembayaran Transfer -->
        <div class="p-4 bg-blue-50/60 rounded-2xl border border-blue-200 text-xs text-slate-700 mb-8 leading-relaxed">
            <strong class="text-blue-900 font-bold block mb-1">Informasi Pembayaran / Pelunasan:</strong>
            <p>Pembayaran dapat ditransfer ke rekening toko Syafa Frozen Food atau tunai langsung ke kasir toko sebelum tanggal jatuh tempo.</p>
            <p class="font-mono font-bold text-blue-900 mt-1">Konfirmasi Pembayaran via WhatsApp: {{ $receiptSetting->store_phone ?: '089649399694' }}</p>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-6 border-t border-slate-200">
            <div>
                <p class="text-slate-500 mb-16">Pelanggan,</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ $transaction->customer_name }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Hormat Kami (Admin / Kasir),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ $transaction->user->name ?? 'Management Syafa' }}</p>
            </div>
        </div>

    </div>

</body>
</html>
