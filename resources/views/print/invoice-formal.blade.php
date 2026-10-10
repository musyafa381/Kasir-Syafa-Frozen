<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - {{ $transaction->invoice_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('kasirku.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen p-4 sm:p-8">

    <!-- Top Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-end no-print">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- Main Printable Invoice Paper (A4 Style) -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 print:border-none print:shadow-none print:p-0 print:m-0 print:max-w-full">
        
        <!-- Header Kop Toko -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kasirku.png') }}" alt="Logo Syafa" class="w-16 h-16 rounded-2xl object-contain border border-slate-100 p-1">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $receiptSetting->store_name ?: 'SYAFA FROZEN FOOD' }}
                    </h1>
                    <p class="text-xs text-slate-600 font-medium mt-0.5">
                        {{ $receiptSetting->store_address ?: 'Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Bantul, DIY' }}
                    </p>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">
                        No. Telp / WhatsApp: {{ $receiptSetting->store_phone ?: '089649399694' }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-xs font-black uppercase tracking-wider">
                    FAKTUR PENJUALAN
                </span>
                <p class="text-sm font-black font-mono text-slate-900 mt-2">{{ $transaction->invoice_number }}</p>
                <p class="text-xs text-slate-500 font-medium">{{ $transaction->created_at->translatedFormat('d F Y - H:i') }} WIB</p>
            </div>
        </div>

        <!-- Metadata Transaksi & Pelanggan -->
        <div class="grid grid-cols-2 gap-6 my-6 text-xs bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">DITUJUKAN KEPADA:</span>
                <h3 class="text-sm font-bold text-slate-900">
                    {{ $transaction->customer_name }}
                    @if($transaction->is_loyalty)
                        <span class="text-[10px] text-blue-700 bg-blue-100 px-1.5 py-0.5 rounded font-mono font-bold">MEMBER</span>
                    @endif
                </h3>
                @if($transaction->customer?->phone)
                    <p class="text-slate-600 font-mono mt-0.5">Telp: {{ $transaction->customer->phone }}</p>
                @endif
                @if($transaction->customer?->address)
                    <p class="text-slate-500 mt-0.5">{{ $transaction->customer->address }}</p>
                @endif
            </div>
            <div class="space-y-1.5 border-l border-slate-200 pl-6">
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Kasir Bertugas:</span> <strong class="text-slate-900">{{ $transaction->user->name ?? 'Kasir' }}</strong></p>
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Metode Pembayaran:</span> <strong class="text-slate-900 uppercase font-mono">{{ $transaction->payment_method }}</strong></p>
                <p class="flex justify-between"><span class="text-slate-500 font-medium">Status Pembayaran:</span> <strong class="text-emerald-700 uppercase font-bold">{{ $transaction->payment_status === 'paid' ? 'LUNAS' : ($transaction->payment_status === 'partial' ? 'DICICIL SEBAGIAN' : 'BELUM LUNAS') }}</strong></p>
            </div>
        </div>

        <!-- Tabel Item Pembelian -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Produk Frozen</th>
                        <th class="py-3 px-4 text-center w-28">Kuantitas / Satuan</th>
                        <th class="py-3 px-4 text-right w-36">Harga Satuan</th>
                        <th class="py-3 px-4 text-right w-36">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($transaction->items as $idx => $item)
                        <tr class="{{ $idx % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="py-3 px-4 text-center font-mono text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $item->product_name }}
                                @if($item->price_type === 'loyalty')
                                    <span class="text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-300 px-1.5 py-0.2 rounded ml-1">
                                        SALES
                                    </span>
                                @endif
                                @if($item->isBox())
                                    <span class="text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 px-1.5 py-0.2 rounded ml-1">
                                        DUS
                                    </span>
                                @endif
                                @if($item->discount_amount > 0)
                                    <span class="text-[10px] text-rose-600 block font-normal mt-0.5">
                                        Diskon Item: -Rp {{ number_format($item->discount_amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold">
                                @if($item->isBox())
                                    <span>{{ $item->quantity }} Dus</span>
                                    <span class="text-[10px] text-slate-500 block font-normal">({{ $item->quantity * $item->pieces_per_unit }} {{ $item->product?->unit ?? 'pcs' }})</span>
                                @else
                                    <span>{{ $item->quantity }} {{ $item->product?->unit ?? 'pcs' }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-500 block font-normal">/ {{ $item->isBox() ? 'dus' : ($item->product?->unit ?? 'pcs') }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                @if($item->discount_amount > 0)
                                    <span class="text-[10px] text-slate-400 line-through block font-normal">
                                        Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}
                                    </span>
                                @endif
                                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Calculation & Notes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start text-xs mb-8">
            <div class="space-y-3">
                @if($transaction->notes)
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <strong class="text-slate-700 font-bold block mb-1">Catatan Transaksi:</strong>
                        <p class="text-slate-600">{{ $transaction->notes }}</p>
                    </div>
                @endif
                <div class="text-[11px] text-slate-500 leading-relaxed">
                    <p class="font-bold text-slate-700 mb-0.5">Syarat & Ketentuan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        <li>Barang frozen food yang telah dibeli harap segera dimasukkan ke dalam freezer (-18°C).</li>
                        <li>Komplain kualitas barang maksimal 1x24 jam dengan menyertakan bukti faktur ini.</li>
                    </ul>
                </div>
            </div>

            <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-2 font-mono text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Total QTY:</span>
                    <span>{{ $transaction->items->sum('quantity') }} unit</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($transaction->discount_amount > 0)
                    <div class="flex justify-between text-rose-600 font-bold">
                        <span>Diskon ({{ $transaction->discount->name ?? 'Diskon' }}):</span>
                        <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($transaction->tax_amount > 0)
                    <div class="flex justify-between text-slate-700 font-bold">
                        <span>Pajak ({{ $transaction->tax->name ?? 'PPN' }}):</span>
                        <span>+ Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t-2 border-slate-300">
                    <span>TOTAL TAGIHAN:</span>
                    <span class="text-blue-700">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-700 pt-1">
                    <span>Jumlah Bayar:</span>
                    <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-700">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-6 border-t border-slate-200">
            <div>
                <p class="text-slate-500 mb-16">Penerima / Pelanggan,</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ $transaction->customer_name }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Hormat Kami (Kasir / Admin),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[160px]">{{ $transaction->user->name ?? 'Owner Syafa' }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-[10px] text-slate-400 mt-8 pt-4 border-t border-slate-100">
            {{ $receiptSetting->footer_link ? 'Kritik & Saran: ' . $receiptSetting->footer_link . ' | ' : '' }}
            Terima kasih telah berbelanja di Syafa Frozen Food.
        </div>

    </div>

</body>
</html>
