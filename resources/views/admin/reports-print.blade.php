<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi Database - Syila Buah (Periode {{ $period }})</title>
    <!-- Tailwind via Vite / CDN untuk stylesheet cetak -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans p-6 print:p-0 print:bg-white">

    <!-- Control Header (Disembunyikan saat dicetak) -->
    <div class="max-w-4xl mx-auto mb-6 bg-white p-4 rounded-xl shadow border border-gray-200 flex items-center justify-between no-print">
        <div>
            <h2 class="font-bold text-gray-800 text-sm">Dokumen Laporan Resmi Database</h2>
            <p class="text-xs text-gray-500">Siap dicetak atau disimpan sebagai dokumen PDF.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-bold shadow flex items-center gap-1 cursor-pointer">
                <span>🖨️ Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-semibold">
                Tutup
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen Cetak -->
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-lg border border-gray-200 print:shadow-none print:border-none print:p-0">
        
        <!-- Kop Surat Toko -->
        <div class="border-b-2 border-gray-800 pb-4 mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black tracking-wider text-green-700 uppercase">SYILA BUAH</h1>
                <p class="text-xs text-gray-600 font-medium">Toko Buah Segar Premium & Parsel Berkualitas</p>
                <p class="text-[11px] text-gray-500">Jl. Raya Syila Buah No. 88 | Email: info@syilabuah.id | Telp: 0812-3456-7890</p>
            </div>
            <div class="text-right">
                <span class="inline-block bg-green-100 text-green-800 text-xs font-extrabold px-3 py-1 rounded-full border border-green-300 mb-1">
                    LAPORAN RESMI DATABASE
                </span>
                <p class="text-xs text-gray-600 font-bold">Periode: {{ $period }}</p>
                <p class="text-[10px] text-gray-400">Dicetak pada: {{ date('d M Y H:i:s') }}</p>
            </div>
        </div>

        <!-- Ringkasan Eksekutif KPI -->
        <div class="grid grid-cols-4 gap-3 mb-6">
            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-center">
                <p class="text-[10px] text-gray-500 font-bold uppercase">Total Pendapatan</p>
                <p class="text-sm font-black text-green-700 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-center">
                <p class="text-[10px] text-gray-500 font-bold uppercase">Total Pesanan</p>
                <p class="text-sm font-black text-blue-700 mt-1">{{ number_format($totalOrders, 0, ',', '.') }} Transaksi</p>
            </div>
            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-center">
                <p class="text-[10px] text-gray-500 font-bold uppercase">Stok Masuk DB</p>
                <p class="text-sm font-black text-emerald-700 mt-1">+{{ number_format($totalStockMasuk, 0, ',', '.') }} unit</p>
            </div>
            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-center">
                <p class="text-[10px] text-gray-500 font-bold uppercase">Stok Keluar DB</p>
                <p class="text-sm font-black text-red-600 mt-1">-{{ number_format($totalStockKeluar, 0, ',', '.') }} unit</p>
            </div>
        </div>

        <!-- 1. Rincian Data Penjualan -->
        <div class="mb-6">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider mb-2 border-l-4 border-green-600 pl-2">
                1. Rincian Data Penjualan (DB)
            </h3>
            <table class="w-full text-xs border border-gray-300">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300 font-bold text-gray-700 text-left">
                        <th class="p-2 border-r">Periode</th>
                        <th class="p-2 border-r text-center">Jumlah Pesanan</th>
                        <th class="p-2 border-r text-right">Total Pendapatan</th>
                        <th class="p-2 text-right">Rata-rata / Pesanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($salesData as $row)
                        @php $avg = $row['orders'] > 0 ? $row['revenue'] / $row['orders'] : 0; @endphp
                        <tr>
                            <td class="p-2 border-r font-semibold">{{ $row['month'] }}</td>
                            <td class="p-2 border-r text-center">{{ number_format($row['orders'], 0, ',', '.') }}</td>
                            <td class="p-2 border-r text-right font-bold text-green-700">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                            <td class="p-2 text-right">Rp {{ number_format($avg, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 2. Produk Terlaris -->
        <div class="mb-6">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider mb-2 border-l-4 border-green-600 pl-2">
                2. Produk Terlaris (Top Selling Items)
            </h3>
            <table class="w-full text-xs border border-gray-300">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300 font-bold text-gray-700 text-left">
                        <th class="p-2 border-r">Nama Produk</th>
                        <th class="p-2 border-r text-center">Total Terjual</th>
                        <th class="p-2 text-right">Total Pendapatan Produk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($topProducts as $tp)
                        <tr>
                            <td class="p-2 border-r font-semibold">{{ $tp['name'] }}</td>
                            <td class="p-2 border-r text-center font-bold">{{ number_format($tp['sold'], 0, ',', '.') }} unit</td>
                            <td class="p-2 text-right font-bold text-green-700">Rp {{ number_format($tp['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 3. Riwayat Stok Masuk -->
        <div class="mb-6">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider mb-2 border-l-4 border-green-600 pl-2">
                3. Riwayat Stok Masuk (Database Stock In Log)
            </h3>
            <table class="w-full text-xs border border-gray-300">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300 font-bold text-gray-700 text-left">
                        <th class="p-2 border-r">Tanggal & Waktu</th>
                        <th class="p-2 border-r">Nama Produk</th>
                        <th class="p-2 border-r text-center">Jumlah Masuk</th>
                        <th class="p-2">Catatan / Supplier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stockMasukList as $sm)
                        <tr>
                            <td class="p-2 border-r text-gray-600">{{ $sm->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-2 border-r font-semibold">{{ $sm->product->name ?? '-' }}</td>
                            <td class="p-2 border-r text-center font-bold text-green-700">+{{ $sm->qty }} {{ $sm->product->unit ?? 'unit' }}</td>
                            <td class="p-2 text-gray-600">{{ $sm->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-3 text-center text-gray-500">Tidak ada riwayat stok masuk pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 4. Riwayat Stok Keluar -->
        <div class="mb-8">
            <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider mb-2 border-l-4 border-red-600 pl-2">
                4. Riwayat Stok Keluar (Database Stock Out Log)
            </h3>
            <table class="w-full text-xs border border-gray-300">
                <thead class="bg-gray-100">
                    <tr class="border-b border-gray-300 font-bold text-gray-700 text-left">
                        <th class="p-2 border-r">Tanggal & Waktu</th>
                        <th class="p-2 border-r">Nama Produk</th>
                        <th class="p-2 border-r text-center">Jumlah Keluar</th>
                        <th class="p-2">Keterangan Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($stockKeluarList as $sk)
                        <tr>
                            <td class="p-2 border-r text-gray-600">{{ $sk->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-2 border-r font-semibold">{{ $sk->product->name ?? '-' }}</td>
                            <td class="p-2 border-r text-center font-bold text-red-600">{{ $sk->qty }} {{ $sk->product->unit ?? 'unit' }}</td>
                            <td class="p-2 text-gray-600">{{ $sk->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-3 text-center text-gray-500">Tidak ada riwayat stok keluar pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Kolom Pengesahan / Tanda Tangan -->
        <div class="mt-12 flex justify-between items-end text-xs">
            <div class="text-center w-48">
                <p class="text-gray-500">Dibuat Oleh,</p>
                <div class="h-16"></div>
                <p class="font-bold border-b border-gray-800 pb-0.5">Staf Operasional</p>
                <p class="text-[10px] text-gray-500">Syila Buah</p>
            </div>
            <div class="text-center w-48">
                <p class="text-gray-500">Disetujui Oleh,</p>
                <div class="h-16"></div>
                <p class="font-bold border-b border-gray-800 pb-0.5">{{ Auth::user()->name ?? 'Manajer Toko' }}</p>
                <p class="text-[10px] text-gray-500">Administrator Syila Buah</p>
            </div>
        </div>

    </div>

</body>
</html>
