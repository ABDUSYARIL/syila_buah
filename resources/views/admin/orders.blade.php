@extends('layouts.admin')

@section('title', 'Kelola Pesanan - Admin Syila Buah')

@section('content')
<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-dark flex items-center gap-2">
                Kelola Pesanan
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-green-light text-primary border border-primary/20">
                    Auto-Hapus Final: {{ $retentionDays == 7 ? '1 Minggu' : ($retentionDays == 30 ? '1 Bulan' : ($retentionDays == 90 ? '3 Bulan' : 'Nonaktif')) }}
                </span>
            </h1>
            {{-- Menampilkan total keseluruhan pesanan dari database, bukan hanya di halaman ini --}}
            <p class="text-sm text-gray-muted">{{ $orders->total() }} total pesanan terdaftar</p>
        </div>

        <!-- Modal Button / Trigger Pembersihan Riwayat Pesanan -->
        <div x-data="{ openClearOrdersModal: false }">
            <button @click="openClearOrdersModal = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-xs font-semibold border border-red-200 transition-all cursor-pointer shadow-sm">
                <span class="material-symbols-rounded text-sm">delete_sweep</span>
                Atur & Hapus Riwayat
            </button>

            <!-- Modal Form Pembersihan Riwayat Pesanan Selesai / Dibatalkan -->
            <div x-show="openClearOrdersModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" x-transition.opacity>
                <div @click.outside="openClearOrdersModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-light w-full max-w-md p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-bg-light pb-3">
                        <h4 class="font-bold text-gray-dark text-base flex items-center gap-2">
                            <span class="material-symbols-rounded text-red-500">auto_delete</span>
                            Pembersihan Riwayat Pesanan
                        </h4>
                        <button @click="openClearOrdersModal = false" class="text-gray-muted hover:text-gray-dark text-lg cursor-pointer">&times;</button>
                    </div>

                    <form action="{{ route('admin.orders.clear-history') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-dark mb-1">Rentang Waktu Hapus Pesanan (Selesai/Dibatalkan)</label>
                            <select name="period" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10">
                                <option value="7_days">Lebih dari 1 Minggu (7 Hari)</option>
                                <option value="30_days" selected>Lebih dari 1 Bulan (30 Hari)</option>
                                <option value="90_days">Lebih dari 3 Bulan (90 Hari)</option>
                                <option value="all_completed">Semua Pesanan Selesai & Dibatalkan</option>
                            </select>
                            <p class="text-[11px] text-gray-muted mt-1">Hanya memicu penghapusan riwayat pesanan yang sudah <strong>Selesai</strong> atau <strong>Dibatalkan</strong>.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-dark mb-1">Simpan Pengaturan Otomatis (Retensi Pesanan)</label>
                            <select name="auto_retention" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10">
                                <option value="7" {{ $retentionDays == 7 ? 'selected' : '' }}>Otomatis Hapus Final > 1 Minggu</option>
                                <option value="30" {{ $retentionDays == 30 ? 'selected' : '' }}>Otomatis Hapus Final > 1 Bulan</option>
                                <option value="90" {{ $retentionDays == 90 ? 'selected' : '' }}>Otomatis Hapus Final > 3 Bulan</option>
                                <option value="0" {{ $retentionDays == 0 ? 'selected' : '' }}>Nonaktifkan Hapus Otomatis</option>
                            </select>
                        </div>

                        <div class="pt-3 border-t border-bg-light flex items-center justify-end gap-2">
                            <button type="button" @click="openClearOrdersModal = false" class="px-4 py-2 text-xs font-semibold rounded-xl border border-gray-light text-gray-dark hover:bg-bg-light cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus riwayat pesanan (Selesai/Dibatalkan) sesuai rentang waktu yang dipilih?');" class="px-4 py-2 text-xs font-bold rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all cursor-pointer shadow-soft">
                                Hapus Riwayat Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-light text-primary text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 rounded bg-red-50 text-red-600 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-4 mb-4 hover:shadow-soft transition-all duration-300">
        <form action="{{ route('admin.orders') }}" method="GET" class="flex flex-col sm:flex-row gap-3 flex-wrap">
            <div class="relative flex-grow min-w-48 group">
                <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari invoice / pelanggan..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-light text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-bg-light" 
                />
            </div>
            
            <div class="flex gap-2 flex-wrap">
                @foreach(['Semua', 'Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai'] as $s)
                    {{-- Setiap tombol filter dilengkapi badge angka merah kecil yang menunjukkan jumlah pesanan di status tersebut --}}
                    <a href="{{ route('admin.orders', ['status' => $s, 'search' => $search]) }}" 
                       class="relative px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all border block select-none
                        {{ $status === $s ? 'bg-primary text-white border-primary shadow-soft' : 'bg-white border-gray-light text-gray-muted hover:border-primary hover:text-primary' }}">
                        {{ $s }}
                        {{-- Badge notifikasi angka — hanya tampil jika ada pesanan di status ini dan bukan filter 'Semua' atau 'Selesai' --}}
                        @php $count = $statusCounts[$s] ?? 0; @endphp
                        @if($count > 0 && $s !== 'Semua' && $s !== 'Selesai')
                            <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] rounded-full bg-red-500 text-white text-[9px] font-black flex items-center justify-center px-1 shadow-sm">
                                {{ $count > 99 ? '99+' : $count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light">
                        @foreach(['Invoice', 'Pelanggan', 'Tanggal', 'Total', 'Pembayaran', 'Bukti', 'Status', 'Aksi'] as $h)
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-muted uppercase tracking-wide">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $o)
                        <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-dark font-semibold">{{ $o['id'] }}</td>
                            <td class="py-3.5 px-4 font-medium text-gray-dark">{{ $o['customer'] }}</td>
                            <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $o['date'] }}</td>
                            <td class="py-3.5 px-4 font-bold text-primary">{{ \App\Http\Controllers\ProductData::rp($o['total']) }}</td>
                            
                            <!-- Pembayaran -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    {{ $o['payStatus'] === 'Lunas' ? 'bg-green-light text-primary' : ($o['payStatus'] === 'Menunggu' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600') }}">
                                    {{ $o['payStatus'] }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                @if($o['proof'])
                                    <a href="{{ asset('storage/' . $o['proof']) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-primary text-primary text-xs font-semibold hover:bg-primary/10 transition-colors">
                                        <span class="material-symbols-rounded text-sm">visibility</span>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-muted">Tidak ada</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    {{ $o['status'] === 'Selesai' ? 'bg-green-light text-primary' : ($o['status'] === 'Dikirim' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600') }}">
                                    {{ $o['status'] }}
                                </span>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="py-3.5 px-4">
                                <div class="flex gap-1.5">

                                    <!-- Detail -->
                                    <button
                                        onclick='showDetail(@json($o))'
                                        class="px-2.5 py-1.5 rounded-xl bg-green-light text-primary text-xs font-semibold hover:bg-green-200 cursor-pointer shadow-sm">
                                        Detail
                                    </button>

                                    @if($o['payStatus'] === 'Menunggu Verifikasi' || $o['payStatus'] === 'Menunggu')
                                        <form action="{{ route('admin.orders.accept', $o['id']) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-xl bg-green-light text-primary text-xs font-semibold hover:bg-green-200 cursor-pointer shadow-sm">
                                                Terima
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.orders.reject', $o['id']) }}"
                                            method="POST"
                                            style="display:inline"
                                            onsubmit="return confirm('Yakin tolak pesanan {{ $o['id'] }}?');">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100 cursor-pointer shadow-sm">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif

                                    @if($o['status'] === 'Diproses')
                                        <form action="{{ route('admin.orders.ship', $o['id']) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-xl bg-[#FFF3E0] text-accent text-xs font-semibold hover:bg-[#FFE0B2] cursor-pointer shadow-sm">
                                                Kirim
                                            </button>
                                        </form>
                                    @endif

                                    @if($o['status'] === 'Dikirim')
                                        <form action="{{ route('admin.orders.complete', $o['id']) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-2.5 py-1.5 rounded-xl bg-green-light text-primary text-xs font-semibold hover:bg-green-200 cursor-pointer shadow-sm">
                                                Selesai
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Komponen navigasi paginasi untuk berpindah halaman pesanan --}}
        {{-- Hanya tampil jika total pesanan lebih dari 10 (1 halaman) --}}
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-bg-light flex items-center justify-between gap-4 flex-wrap">
                {{-- Info jumlah pesanan yang sedang ditampilkan --}}
                <p class="text-xs text-gray-muted">
                    Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
                </p>
                {{-- Tombol navigasi antar halaman --}}
                <div class="flex items-center gap-1">
                    {{-- Tombol Sebelumnya --}}
                    @if($orders->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">‹ Sebelumnya</span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}&status={{ $status }}&search={{ $search }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">‹ Sebelumnya</a>
                    @endif

                    {{-- Nomor-nomor halaman --}}
                    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if($page == $orders->currentPage())
                            <span class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center bg-primary text-white shadow-soft">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}&status={{ $status }}&search={{ $search }}" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-white border border-gray-light text-gray-muted hover:border-primary hover:text-primary transition-all">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Tombol Berikutnya --}}
                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}&status={{ $status }}&search={{ $search }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">Berikutnya ›</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">Berikutnya ›</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-4xl rounded-2xl p-6 shadow-xl">

        <div class="flex justify-between items-center mb-5">

            <h2 class="text-xl font-bold">
                Detail Pesanan
            </h2>

            <button onclick="closeDetail()"
                class="text-2xl font-bold">
                ×
            </button>

        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">

            <div>
                <p class="text-gray-500">Invoice</p>
                <p id="d_invoice" class="font-semibold"></p>
            </div>

            <div>
                <p class="text-gray-500">Tanggal</p>
                <p id="d_date"></p>
            </div>

            <div>
                <p class="text-gray-500">Pelanggan</p>
                <p id="d_customer"></p>
            </div>

            <div>
                <p class="text-gray-500">Status</p>
                <p id="d_status"></p>
            </div>

            <div>
                <p class="text-gray-500">Pembayaran</p>
                <p id="d_payment"></p>
            </div>

            <div>
                <p class="text-gray-500">Pengiriman</p>
                <p id="d_method"></p>
            </div>

            <div class="col-span-2">
                <p class="text-gray-500">Alamat</p>
                <p id="d_address"></p>
            </div>

        </div>

        <h3 class="font-bold mb-3">
            Daftar Produk
        </h3>

        <table class="w-full border">

            <thead class="bg-gray-100">

                <tr>

                    <th class="border p-2">Produk</th>

                    <th class="border p-2">Qty</th>

                    <th class="border p-2">Harga</th>

                    <th class="border p-2">Subtotal</th>

                </tr>

            </thead>

            <tbody id="detailItems">

            </tbody>

        </table>

        <div class="text-right mt-5">

            <span class="font-bold">
                Total :
            </span>

            <span id="d_total"
                class="font-bold text-lg text-primary">
            </span>

        </div>

    </div>

</div>
<script>

function showDetail(order){

    document.getElementById('d_invoice').innerText = order.id;
    document.getElementById('d_date').innerText = order.date;
    document.getElementById('d_customer').innerText = order.customer;
    document.getElementById('d_status').innerText = order.status;
    document.getElementById('d_payment').innerText = order.payStatus;
    document.getElementById('d_method').innerText = order.method;
    document.getElementById('d_address').innerText = order.address;

    document.getElementById('d_total').innerText =
        "Rp " + Number(order.total).toLocaleString("id-ID");

    let rows = "";

    if(order.items){

        order.items.forEach(function(item){

            rows += `
                <tr>

                    <td class="border p-2">${item.product}</td>

                    <td class="border p-2 text-center">${item.qty}</td>

                    <td class="border p-2 text-right">
                        Rp ${Number(item.price).toLocaleString("id-ID")}
                    </td>

                    <td class="border p-2 text-right">
                        Rp ${Number(item.subtotal).toLocaleString("id-ID")}
                    </td>

                </tr>
            `;

        });

    }

    document.getElementById("detailItems").innerHTML = rows;

    document.getElementById("detailModal").classList.remove("hidden");
    document.getElementById("detailModal").classList.add("flex");

}

function closeDetail(){

    document.getElementById("detailModal").classList.add("hidden");
    document.getElementById("detailModal").classList.remove("flex");

}

</script>
@endsection