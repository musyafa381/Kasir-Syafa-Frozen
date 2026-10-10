<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Stock Opname Fisik - Syafa Frozen Food</title>
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
            <span>Cetak Lembar Kerja</span>
        </button>
    </div>

    <div class="max-w-5xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 print:border-none print:shadow-none print:p-0 print:m-0 print:max-w-full">
        
        <!-- Header Kop Lembar Kerja -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('kasirku.png') }}" alt="Logo Syafa" class="w-14 h-14 rounded-2xl object-contain border border-slate-100 p-0.5">
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $receiptSetting->store_name ?: 'SYAFA FROZEN FOOD' }}
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">Lembar Kerja Penghitungan Fisik Barang (Stock Opname Sheet)</p>
                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">Freezer & Gudang Penyimpanan Makanan Beku</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs font-black uppercase tracking-wider">
                    FORM STOCK OPNAME
                </span>
                <p class="text-xs font-bold text-slate-700 font-mono mt-2">
                    Tanggal: {{ $printDate->format('d/m/Y') }}
                </p>
                <p class="text-[11px] text-slate-400 font-mono">Pukul: {{ $printDate->format('H:i') }} WIB</p>
            </div>
        </div>

        <!-- Instruksi Petugas -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-600 mb-6 leading-relaxed">
            <strong class="text-slate-900 font-bold block mb-1">Panduan Pengisian:</strong>
            <ol class="list-decimal list-inside space-y-0.5 text-[11px]">
                <li>Hitung fisik produk per pack/kemasan yang ada di dalam freezer secara teliti.</li>
                <li>Tuliskan jumlah hitungan aktual pada kolom <strong>"Fisik Aktual"</strong> menggunakan pulpen.</li>
                <li>Beri tanda centang pada kolom <strong>"Kondisi Bagus"</strong> atau tuliskan catatan jika kemasan rusak/kempes/bocor.</li>
                <li>Tandatangani lembar ini setelah seluruh rak/freezer selesai dihitung dan serahkan ke Admin POS.</li>
            </ol>
        </div>

        <!-- Tabel Stock Opname Form -->
        <div class="border border-slate-300 rounded-xl overflow-hidden mb-6">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-900 text-white text-[10px] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-3 w-8 text-center border-r border-slate-800">No</th>
                        <th class="py-3 px-3 w-28 border-r border-slate-800">Barcode / SKU</th>
                        <th class="py-3 px-3 border-r border-slate-800">Nama Produk Frozen</th>
                        <th class="py-3 px-3 w-28 border-r border-slate-800">Kategori</th>
                        <th class="py-3 px-3 w-20 text-center border-r border-slate-800">Stok Sistem</th>
                        <th class="py-3 px-3 w-24 text-center border-r border-slate-800 bg-amber-950 text-amber-200">Fisik Aktual</th>
                        <th class="py-3 px-3 w-20 text-center border-r border-slate-800">Selisih (+/-)</th>
                        <th class="py-3 px-3 w-32 text-center">Paraf / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse($products as $idx => $p)
                        <tr class="h-10 {{ $idx % 2 == 0 ? 'bg-white' : 'bg-slate-50/40' }}">
                            <td class="py-2 px-3 text-center font-mono text-slate-500 border-r border-slate-200">{{ $idx + 1 }}</td>
                            <td class="py-2 px-3 font-mono text-[10px] text-slate-500 border-r border-slate-200">{{ $p->barcode ?: $p->sku ?: '-' }}</td>
                            <td class="py-2 px-3 font-bold text-slate-900 border-r border-slate-200">{{ $p->name }}</td>
                            <td class="py-2 px-3 text-slate-600 border-r border-slate-200 text-[11px]">{{ $p->category->name ?? '-' }}</td>
                            <td class="py-2 px-3 text-center font-mono font-bold text-slate-700 border-r border-slate-200">{{ $p->stock }}</td>
                            <td class="py-2 px-3 text-center border-r border-slate-200 bg-amber-50/40">
                                <!-- Blank cell for handwriting -->
                            </td>
                            <td class="py-2 px-3 text-center border-r border-slate-200">
                                <!-- Blank cell for calculation -->
                            </td>
                            <td class="py-2 px-3 text-center text-slate-400 text-[10px]">
                                <!-- Blank cell for notes -->
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada produk dalam database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Pelaksana & Pemeriksa -->
        <div class="grid grid-cols-3 gap-6 text-center text-xs pt-6 border-t border-slate-200">
            <div>
                <p class="text-slate-500 mb-16">Petugas Penghitung (Fisik),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[140px]">( ................................... )</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Petugas Input / Verifikator,</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[140px]">( ................................... )</p>
            </div>
            <div>
                <p class="text-slate-500 mb-16">Mengetahui (Owner Toko),</p>
                <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 inline-block min-w-[140px]">Management Syafa</p>
            </div>
        </div>

    </div>

</body>
</html>
