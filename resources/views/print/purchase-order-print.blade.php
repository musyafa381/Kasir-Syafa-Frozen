<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pesanan (PO) - {{ $purchase->invoice_number }}</title>
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
        
        <!-- Header Kop Surat Pesanan -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kasirku.png') }}" alt="Logo Syafa" class="w-14 h-14 rounded-2xl object-contain border border-slate-100 p-0.5">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $receiptSetting->store_name ?: 'SYAFA FROZEN FOOD' }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">Surat Pesanan Pembelian Barang (Purchase Order)</p>
                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $receiptSetting->store_address }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs font-black uppercase tracking-wider">
                    PURCHASE ORDER
                </span>
                <p class="text-sm font-black font-mono text-slate-900 mt-2">{{ $purchase->invoice_number }}</p>
                <p class="text-xs text-slate-500">Tanggal: {{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : $purchase->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Info Supplier & Purchase Details -->
        <div class="grid grid-cols-2 gap-6 my-6 text-xs bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">SUPPLIER / DISTRIBUTOR:</span>
                <h3 class="text-sm font-bold text-slate-900">{{ $purchase->supplier->name ?? 'Supplier Umum' }}</h3>
                @if($purchase->supplier?->phone)
                    <p class="text-slate-600 font-mono mt-0.5">Telp: {{ $purchase->supplier->phone }}</p>
                @endif
                @if($purchase->supplier?->address)
                    <p class="text-slate-500 mt-0.5">{{ $purchase->supplier->address }}</p>
                @endif
            </div>
            <div class="space-y-1.5 border-l border-slate-200 pl-6">
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Petugas Order:</span> <strong class="text-slate-900">{{ $purchase->user->name ?? 'Admin' }}</strong></p>
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Sistem Pembayaran:</span> <strong class="text-slate-900 uppercase font-mono">{{ $purchase->payment_type ?? 'Tunai' }}</strong></p>
                @if($purchase->due_date)
                    <p class="flex justify-between"><span class="text-slate-500 font-medium">Jatuh Tempo:</span> <strong class="text-rose-700 font-mono">{{ $purchase->due_date->format('d/m/Y') }}</strong></p>
                @endif
            </div>
        </div>

        <!-- Tabel Item Pesanan -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Produk Frozen</th>
                        <th class="py-3 px-4 text-center w-24">Jumlah Order</th>
                        <th class="py-3 px-4 text-right w-32">Harga Beli Satuan</th>
                        <th class="py-3 px-4 text-right w-36">Total Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($purchase->items as $idx => $item)
                        <tr class="{{ $idx % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="py-3 px-4 text-center font-mono text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->product_name ?? $item->product?->name ?? 'Produk' }}</td>
                            <td class="py-3 px-4 text-center font-mono font-bold">{{ $item->quantity }} {{ $item->product?->unit ?? 'pack' }}</td>
                            <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($item->unit_cost ?? $item->cost_price ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100 font-bold font-mono text-xs border-t-2 border-slate-300">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right text-slate-900">TOTAL NILAI PEMBELIAN:</td>
                        <td class="py-3 px-4 text-right text-blue-700 text-sm font-black">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-6 border-t border-slate-200">
            <div>
                <p class="text-slate-500 mb-16">Supplier / Sales Distributor,</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ $purchase->supplier->name ?? 'Sales' }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Pemesan (Syafa Frozen Food),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ $purchase->user->name ?? 'Management Syafa' }}</p>
            </div>
        </div>

    </div>

</body>
</html>
