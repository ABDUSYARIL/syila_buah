@extends('layouts.admin')

@section('title', 'Laporan Penjualan - Admin Syila Buah')

@section('content')
{{-- ============================================================ --}}
{{-- CSS KHUSUS CETAK PDF (hanya berlaku saat window.print() dipanggil) --}}
{{-- Memastikan seluruh halaman laporan tercetak lengkap tanpa terpotong --}}
{{-- ============================================================ --}}
<style>
@media print {
    /* ---- 1. Sembunyikan seluruh elemen navigasi admin dan tombol kontrol ---- */
    /* Sidebar admin, header topbar, footer, tombol filter, dll disembunyikan */
    aside,
    header,
    nav,
    footer,
    button,
    form,
    .print\:hidden,
    [x-data] > .flex.items-center.justify-between.mb-6 {
        display: none !important;
    }
    
    /* ---- 2. Atur halaman cetak A4 Landscape agar cukup menampung semua konten ---- */
    /* Landscape dipilih agar tabel dan grafik tidak terpotong kanan-kiri */
    @page {
        size: A4 landscape;
        margin: 10mm 12mm;
    }

    /* ---- 3. Reset body agar tampilan bersih tanpa background berwarna ---- */
    html, body {
        background-color: #ffffff !important;
        color: #1a202c !important;
        font-size: 10pt !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* ---- 4. Hapus margin/padding dari layout utama agar menggunakan lebar penuh ---- */
    main, .min-h-screen, [class*="flex-grow"], [class*="p-6"], [class*="p-8"] {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow: visible !important;
    }

    /* ---- 5. Hapus shadow dan border radius yang tidak perlu di media cetak ---- */
    .card-3d, .shadow-soft, [class*="shadow"], .bg-white, [class*="rounded"] {
        box-shadow: none !important;
        border-radius: 4px !important;
        background: #fff !important;
        border: 1px solid #e2e8f0 !important;
    }

    /* ---- 6. Pastikan grid kolom tetap tampil dengan lebar proporsional ---- */
    .grid { display: grid !important; }
    .grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
    .lg\:grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
    .lg\:grid-cols-3 { grid-template-columns: repeat(3, 1fr) !important; }
    .lg\:grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
    .lg\:col-span-2 { grid-column: span 2 !important; }
    .gap-4 { gap: 8px !important; }
    .gap-6 { gap: 10px !important; }

    /* ---- 7. Pastikan canvas grafik tidak terpotong dan tampil proporsional ---- */
    canvas {
        max-width: 100% !important;
        height: auto !important;
    }

    /* ---- 8. Jangan potong tabel atau kartu di tengah halaman ---- */
    table, .bg-white, canvas, .card-3d {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    /* ---- 9. Tampilkan header laporan khusus cetak yang tersembunyi saat web ---- */
    .print\:block { display: block !important; }
    .hidden.print\:block { display: block !important; }
}
</style>

<div x-data="{ period: @json($period) }">
    
    <!-- Header khusus cetak (HANYA tampil saat dokumen dicetak fisik) -->
    <div class="hidden print:block text-center border-b-2 border-gray-800 pb-4 mb-6">
        <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">LAPORAN PENJUALAN - TOKO SYILA BUAH</h1>
        <p class="text-xs text-gray-500 mt-1">Periode Analisis: {{ $period }} | Tanggal Cetak Dokumen: {{ date('d M Y H:i') }}</p>
    </div>

    <div class="flex items-center justify-between mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-dark">Laporan Penjualan</h1>
            <p class="text-sm text-gray-muted">Analitik dan statistik bisnis Syila Buah</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl border border-primary text-primary hover:bg-green-light px-3.5 py-2 text-xs cursor-pointer transition-colors shadow-sm">
                <span class="material-symbols-rounded text-sm">print</span> Cetak PDF
            </button>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="flex gap-2 mb-6">
        @foreach(['Harian', 'Bulanan', 'Tahunan'] as $p)
            <a href="{{ route('admin.reports', ['period' => $p]) }}"
                class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all border select-none"
                :class="period === '{{ $p }}' ? 'bg-primary text-white border-primary shadow-soft' : 'bg-white border-gray-light text-gray-muted hover:border-primary hover:text-primary'"
            >
                {{ $p }}
            </a>
        @endforeach
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($stats as $stat)
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-5 hover:shadow-soft-hover transition-all duration-300">
                <p class="text-xs text-gray-muted mb-1 font-medium">{{ $stat['label'] }}</p>
                <p class="text-xl font-extrabold {{ $stat['color'] }} leading-none tracking-tight">{{ $stat['value'] }}</p>
                <p class="text-[10px] text-primary font-bold mt-2">{{ $stat['change'] }} vs periode sebelumnya</p>
            </div>
        @endforeach
    </div>

    <!-- Graphs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-4">
        <!-- Revenue Bar Chart -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-4">Pendapatan {{ $period }}</h3>
            <div class="relative h-56 w-full">
                <canvas id="revBarChart"></canvas>
            </div>
        </div>
        
        <!-- Orders Area Chart -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-4">Jumlah Pesanan {{ $period }}</h3>
            <div class="relative h-56 w-full">
                <canvas id="ordersAreaChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Sales Data Table (Rincian Tabel Penjualan Dinamis) -->
    {{-- Tabel rincian data penjualan per periode --}}
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden mt-6">
        <div class="p-6 border-b border-bg-light">
            <h3 class="font-bold text-gray-dark text-base">Rincian Data Penjualan</h3>
            <p class="text-xs text-gray-muted mt-1">Tabel rincian angka pendapatan dan pesanan per periode secara detail.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light text-left text-gray-muted font-semibold text-xs uppercase tracking-wider">
                        <th class="py-3 px-6">Periode</th>
                        <th class="py-3 px-4 text-center">Jumlah Pesanan</th>
                        <th class="py-3 px-4 text-right">Total Pendapatan</th>
                        <th class="py-3 px-6 text-right">Rata-rata Nilai Pesanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bg-light">
                    @foreach($salesData as $row)
                        @php
                            // Menghitung rata-rata pendapatan per pesanan
                            $avg = $row['orders'] > 0 ? $row['revenue'] / $row['orders'] : 0;
                        @endphp
                        <tr class="hover:bg-bg-light/50 transition-colors">
                            <td class="py-3.5 px-6 font-semibold text-gray-dark">{{ $row['month'] }}</td>
                            <td class="py-3.5 px-4 text-center font-medium text-gray-dark">{{ number_format($row['orders'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-primary">{{ \App\Http\Controllers\ProductData::rp($row['revenue']) }}</td>
                            <td class="py-3.5 px-6 text-right font-medium text-gray-muted">{{ \App\Http\Controllers\ProductData::rp($avg) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products Table (Tabel Produk Terlaris Dinamis) -->
    {{-- Tabel produk dengan penjualan unit tertinggi --}}
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden mt-6">
        <div class="p-6 border-b border-bg-light">
            <h3 class="font-bold text-gray-dark text-base">Produk Terlaris</h3>
            <p class="text-xs text-gray-muted mt-1">Daftar produk dengan penjualan unit tertinggi pada periode ini.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light text-left text-gray-muted font-semibold text-xs uppercase tracking-wider">
                        <th class="py-3 px-6">Nama Produk</th>
                        <th class="py-3 px-4 text-center">Unit Terjual</th>
                        <th class="py-3 px-6 text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bg-light">
                    @foreach($topProducts as $p)
                        <tr class="hover:bg-bg-light/50 transition-colors">
                            <td class="py-3.5 px-6 font-semibold text-gray-dark">{{ $p['name'] }}</td>
                            <td class="py-3.5 px-4 text-center font-medium text-gray-dark">{{ number_format($p['sold'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-6 text-right font-bold text-primary">{{ \App\Http\Controllers\ProductData::rp($p['revenue']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const salesData = {!! json_encode($salesData) !!};

    const labels = salesData.map(item => item.month);
    const revenues = salesData.map(item => item.revenue);
    const orders = salesData.map(item => item.orders);

    const revCtx = document.getElementById('revBarChart');

    if(revCtx){
        new Chart(revCtx,{
            type:'bar',
            data:{
                labels:labels,
                datasets:[{
                    label:'Pendapatan',
                    data:revenues,
                    backgroundColor:'#4CAF50',
                    borderRadius:6
                }]
            }
        });
    }

    const orderCanvas = document.getElementById('ordersAreaChart');

    if(orderCanvas){

        const ctx = orderCanvas.getContext('2d');

        const gradient = ctx.createLinearGradient(0,0,0,220);
        gradient.addColorStop(0,'rgba(255,152,0,.2)');
        gradient.addColorStop(1,'rgba(255,152,0,0)');

        new Chart(ctx,{
            type:'line',
            data:{
                labels:labels,
                datasets:[{
                    label:'Jumlah Pesanan',
                    data:orders,
                    borderColor:'#FF9800',
                    backgroundColor:gradient,
                    fill:true,
                    tension:.4
                }]
            }
        });

    }

});

// ============================================================
// Konversi Canvas Grafik → Gambar Statis Sebelum Dicetak
// ------------------------------------------------------------
// Chart.js merender grafik di atas elemen <canvas>.
// Sayangnya, beberapa browser tidak bisa mencetak canvas dengan benar.
// Solusi: sebelum print, semua canvas dikonversi menjadi <img> statis.
// Setelah print selesai, <img> dihapus dan canvas dikembalikan seperti semula.
// ============================================================
window.addEventListener('beforeprint', function () {
    // Cari semua elemen canvas yang ada di halaman laporan
    document.querySelectorAll('canvas').forEach(function (canvas) {
        // Buat elemen <img> baru dengan isi yang sama persis seperti canvas
        const img = document.createElement('img');
        img.src      = canvas.toDataURL('image/png'); // Ambil data gambar dari canvas
        img.style.width  = canvas.offsetWidth + 'px';   // Samakan lebar
        img.style.height = canvas.offsetHeight + 'px';  // Samakan tinggi
        img.setAttribute('data-canvas-replacement', 'true'); // Tandai agar bisa dihapus setelah print

        // Sembunyikan canvas asli dan masukkan gambar penggantinya ke DOM
        canvas.style.display = 'none';
        canvas.parentNode.insertBefore(img, canvas.nextSibling);
    });
});

window.addEventListener('afterprint', function () {
    // Setelah mencetak, hapus semua gambar pengganti dan kembalikan canvas
    document.querySelectorAll('img[data-canvas-replacement]').forEach(function (img) {
        img.remove(); // Hapus gambar statis pengganti
    });
    // Tampilkan kembali semua canvas yang sebelumnya disembunyikan
    document.querySelectorAll('canvas').forEach(function (canvas) {
        canvas.style.display = '';
    });
});
</script>
@endsection