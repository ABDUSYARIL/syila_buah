@extends('layouts.admin')

@section('title', 'Kelola Pesanan - Admin Syila Buah')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-light shadow-soft">
        <div>
            <h1 class="text-2xl font-black text-gray-dark tracking-tight flex items-center gap-2">
                Kelola Pesanan
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-green-light text-primary border border-primary/20">
                    Auto-Hapus: {{ $retentionDays == 7 ? '1 Minggu' : ($retentionDays == 30 ? '1 Bulan' : ($retentionDays == 90 ? '3 Bulan' : 'Nonaktif')) }}
                </span>
            </h1>
            <p class="text-xs text-gray-muted mt-1 font-medium">{{ $orders->total() }} total pesanan terdaftar di sistem</p>
        </div>

        <!-- Modal Trigger Pembersihan Riwayat -->
        <div x-data="{ openClearOrdersModal: false }">
            <button @click="openClearOrdersModal = true" type="button" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold border border-red-200 transition-all cursor-pointer shadow-xs">
                <span class="material-symbols-rounded text-base">delete_sweep</span>
                Atur & Hapus Riwayat
            </button>

            <!-- Modal Form Pembersihan Riwayat -->
            <div x-show="openClearOrdersModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" x-transition.opacity>
                <div @click.outside="openClearOrdersModal = false" class="bg-white rounded-3xl shadow-2xl border border-gray-light w-full max-w-md p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-bg-light pb-3">
                        <h4 class="font-extrabold text-gray-dark text-base flex items-center gap-2">
                            <span class="material-symbols-rounded text-red-500">auto_delete</span>
                            Pembersihan Riwayat Pesanan
                        </h4>
                        <button @click="openClearOrdersModal = false" class="text-gray-muted hover:text-gray-dark text-xl font-bold cursor-pointer">&times;</button>
                    </div>

                    <form action="{{ route('admin.orders.clear-history') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-dark mb-1">Rentang Waktu Hapus Pesanan (Selesai/Dibatalkan)</label>
                            <select name="period" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-xs font-semibold focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10">
                                <option value="7_days">Lebih dari 1 Minggu (7 Hari)</option>
                                <option value="30_days" selected>Lebih dari 1 Bulan (30 Hari)</option>
                                <option value="90_days">Lebih dari 3 Bulan (90 Hari)</option>
                                <option value="all_completed">Semua Pesanan Selesai & Dibatalkan</option>
                            </select>
                            <p class="text-[11px] text-gray-muted mt-1">Hanya memicu penghapusan riwayat pesanan yang sudah <strong>Selesai</strong> atau <strong>Dibatalkan</strong>.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-dark mb-1">Simpan Pengaturan Otomatis (Retensi Pesanan)</label>
                            <select name="auto_retention" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-xs font-semibold focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10">
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
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus riwayat pesanan (Selesai/Dibatalkan)?');" class="px-4 py-2 text-xs font-bold rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all cursor-pointer shadow-soft">
                                Hapus Riwayat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-green-light border border-primary/20 text-primary font-bold text-xs flex items-center gap-2 shadow-xs">
            <span class="material-symbols-rounded text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 font-bold text-xs flex items-center gap-2 shadow-xs">
            <span class="material-symbols-rounded text-lg">cancel</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Bar (Tanpa 'Semua' dan 'Menunggu Pembayaran') -->
    <div class="bg-white rounded-3xl shadow-soft border border-gray-light p-4">
        <form action="{{ route('admin.orders') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Form Pencarian -->
            <div class="relative w-full md:w-80 group">
                <span class="material-symbols-rounded absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary text-lg">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari invoice / pelanggan..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-gray-light text-xs font-medium text-gray-dark placeholder-gray-400 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-bg-light" 
                />
            </div>
            
            <!-- Filter Tabs Kategori Status Pesanan -->
            <div class="flex gap-2 flex-wrap w-full md:w-auto">
                @foreach(['Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'] as $s)
                    @php $count = $statusCounts[$s] ?? 0; @endphp
                    <a href="{{ route('admin.orders', ['status' => $s, 'search' => $search]) }}" 
                       class="relative px-4 py-2.5 rounded-2xl text-xs font-extrabold transition-all border flex items-center gap-2 select-none shadow-xs
                        {{ $status === $s ? 'bg-primary text-white border-primary shadow-soft scale-[1.02]' : 'bg-white border-gray-light text-gray-600 hover:border-primary hover:text-primary hover:bg-green-light/30' }}">
                        <span>{{ $s }}</span>
                        @if($count > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black tracking-tight {{ $status === $s ? 'bg-white/25 text-white' : ($s === 'Menunggu Verifikasi' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700') }}">
                                {{ $count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Table Card Pesanan Rapi & Tidak Bertumpuk -->
    <div class="bg-white rounded-3xl shadow-soft border border-gray-light overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-bg-light border-b border-gray-light text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-5 w-36 whitespace-nowrap">Invoice</th>
                        <th class="py-4 px-5 min-w-[150px]">Pelanggan</th>
                        <th class="py-4 px-5 w-28 whitespace-nowrap">Tanggal</th>
                        <th class="py-4 px-5 w-32 whitespace-nowrap">Total</th>
                        <th class="py-4 px-5 w-36 whitespace-nowrap">Pembayaran</th>
                        <th class="py-4 px-5 w-28 whitespace-nowrap text-center">Bukti Bayar</th>
                        <th class="py-4 px-5 w-32 whitespace-nowrap text-center">Status Pesanan</th>
                        <th class="py-4 px-5 min-w-[200px] text-right whitespace-nowrap">Aksi Verifikasi & Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-light text-xs">
                    @forelse($orders as $o)
                        <tr class="hover:bg-bg-light/50 transition-colors">
                            <!-- Invoice -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-lg border border-gray-200 text-xs">
                                    {{ $o['id'] }}
                                </span>
                            </td>

                            <!-- Pelanggan -->
                            <td class="py-4 px-5">
                                <span class="font-bold text-gray-900 block max-w-[180px] truncate" title="{{ $o['customer'] }}">
                                    {{ $o['customer'] }}
                                </span>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-4 px-5 whitespace-nowrap text-gray-500 font-medium">
                                {{ $o['date'] }}
                            </td>

                            <!-- Total -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="font-black text-primary text-sm">
                                    {{ \App\Http\Controllers\ProductData::rp($o['total']) }}
                                </span>
                            </td>
                            
                            <!-- Status Pembayaran -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                @php
                                    $payBadge = match($o['payStatus']) {
                                        'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                                        'Menunggu Verifikasi', 'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-300',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $payBadge }}">
                                    {{ $o['payStatus'] }}
                                </span>
                            </td>

                            <!-- Bukti Pembayaran -->
                            <td class="py-4 px-5 whitespace-nowrap text-center">
                                @if($o['proof'])
                                    <a href="{{ asset('storage/' . $o['proof']) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-primary text-primary text-[11px] font-bold hover:bg-primary hover:text-white transition-all shadow-xs">
                                        <span class="material-symbols-rounded text-sm">visibility</span>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-[11px] text-gray-400 font-medium italic">Belum ada</span>
                                @endif
                            </td>

                            <!-- Status Pesanan -->
                            <td class="py-4 px-5 whitespace-nowrap text-center">
                                @php
                                    $statusBadge = match($o['status']) {
                                        'Selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'Dikirim' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'Diproses' => 'bg-purple-100 text-purple-800 border-purple-300',
                                        'Dibatalkan' => 'bg-red-100 text-red-700 border-red-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black border {{ $statusBadge }}">
                                    {{ $o['status'] }}
                                </span>
                            </td>
                            
                            <!-- Aksi Verifikasi & Kelola -->
                            <td class="py-4 px-5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-nowrap">

                                    <!-- Detail -->
                                    <button
                                        type="button"
                                        onclick='showDetail(@json($o))'
                                        class="px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-bold cursor-pointer transition-colors shadow-xs">
                                        Detail
                                    </button>

                                    <!-- TOMBOL VERIFIKASI PESANAN -->
                                    @if($o['status'] === 'Menunggu Verifikasi' || $o['status'] === 'Menunggu Pembayaran' || $o['payStatus'] === 'Menunggu' || $o['payStatus'] === 'Menunggu Verifikasi')
                                        <form action="{{ route('admin.orders.accept', $o['id']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin MEMVERIFIKASI dan MENERIMA pesanan {{ $o['id'] }} untuk diproses?');"
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-extrabold cursor-pointer transition-all shadow-xs inline-flex items-center gap-1">
                                                <span class="material-symbols-rounded text-sm">verified</span>
                                                Verifikasi
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tombol Kirim -->
                                    @if($o['status'] === 'Diproses')
                                        <form action="{{ route('admin.orders.ship', $o['id']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-extrabold cursor-pointer transition-all shadow-xs inline-flex items-center gap-1">
                                                <span class="material-symbols-rounded text-sm">local_shipping</span>
                                                Kirim
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tombol Selesai -->
                                    @if($o['status'] === 'Dikirim')
                                        <form action="{{ route('admin.orders.complete', $o['id']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-[11px] font-extrabold cursor-pointer transition-all shadow-xs inline-flex items-center gap-1">
                                                <span class="material-symbols-rounded text-sm">check_circle</span>
                                                Selesai
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tombol Batalkan -->
                                    @if($o['status'] !== 'Selesai' && $o['status'] !== 'Dibatalkan')
                                        <button 
                                            type="button"
                                            onclick='openCancelModal("{{ $o['id'] }}")'
                                            class="px-2.5 py-1.5 rounded-xl bg-red-50 text-red-600 border border-red-200 text-[11px] font-bold hover:bg-red-600 hover:text-white transition-all cursor-pointer shadow-xs inline-flex items-center gap-1">
                                            <span class="material-symbols-rounded text-sm">cancel</span>
                                            Batalkan
                                        </button>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-rounded text-4xl text-gray-300">inbox</span>
                                    <p class="font-bold text-xs text-gray-600">Tidak ada pesanan ditemukan untuk filter ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-bg-light flex items-center justify-between gap-4 flex-wrap">
                <p class="text-xs text-gray-muted font-medium">
                    Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
                </p>
                <div class="flex items-center gap-1">
                    @if($orders->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">‹ Sebelumnya</span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}&status={{ $status }}&search={{ $search }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">‹ Sebelumnya</a>
                    @endif

                    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if($page == $orders->currentPage())
                            <span class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center bg-primary text-white shadow-soft">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}&status={{ $status }}&search={{ $search }}" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-white border border-gray-light text-gray-muted hover:border-primary hover:text-primary transition-all">{{ $page }}</a>
                        @endif
                    @endforeach

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

<!-- Modal Input Alasan Pembatalan Pesanan -->
<div id="cancelModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-bg-light pb-3">
            <h3 class="font-extrabold text-gray-dark text-base flex items-center gap-2">
                <span class="material-symbols-rounded text-red-500">cancel</span>
                Batalkan Pesanan <span id="c_invoice_display" class="font-mono text-primary"></span>
            </h3>
            <button type="button" onclick="closeCancelModal()" class="text-gray-muted hover:text-gray-dark text-xl font-bold">&times;</button>
        </div>

        <form id="cancelForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-dark uppercase tracking-wider mb-2">Pilih / Tulis Alasan Pembatalan <span class="text-red-500">*</span></label>
                
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach([
                        'Stok buah habis / tidak layak',
                        'Bukti pembayaran tidak sesuai',
                        'Alamat di luar area pengiriman',
                        'Pesanan duplikat / kesalahan sistem',
                        'Permintaan pembatalan dari pelanggan'
                    ] as $opt)
                        <button 
                            type="button" 
                            onclick="setCancelReason('{{ $opt }}')" 
                            class="px-2.5 py-1 rounded-lg bg-bg-light border border-gray-light text-[11px] font-semibold text-gray-dark hover:border-red-300 hover:bg-red-50 hover:text-red-600 transition-all cursor-pointer"
                        >
                            {{ $opt }}
                        </button>
                    @endforeach
                </div>

                <textarea 
                    name="cancel_reason" 
                    id="cancel_reason_input" 
                    rows="3" 
                    required 
                    placeholder="Masukkan alasan lengkap pembatalan pesanan ini..." 
                    class="w-full rounded-xl border border-gray-light p-3 text-xs text-gray-dark focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 resize-none"
                ></textarea>
                <p class="text-[10px] text-gray-muted mt-1">*Alasan ini akan dicatat ke database dan diinformasikan kepada pelanggan.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-bg-light">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 rounded-xl border border-gray-light text-xs font-semibold text-gray-dark hover:bg-bg-light cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white text-xs font-bold hover:bg-red-600 shadow-soft transition-all cursor-pointer">
                    Proses Pembatalan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-3xl rounded-3xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5 border-b border-bg-light pb-3">
            <h2 class="text-xl font-extrabold text-gray-dark">
                Detail Pesanan
            </h2>
            <button onclick="closeDetail()"
                class="text-2xl font-bold text-gray-muted hover:text-gray-dark cursor-pointer">
                ×
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5 text-sm">
            <div>
                <p class="text-xs text-gray-muted font-medium">Invoice</p>
                <p id="d_invoice" class="font-bold font-mono text-gray-dark"></p>
            </div>
            <div>
                <p class="text-xs text-gray-muted font-medium">Tanggal</p>
                <p id="d_date" class="font-semibold text-gray-dark"></p>
            </div>
            <div>
                <p class="text-xs text-gray-muted font-medium">Pelanggan</p>
                <p id="d_customer" class="font-semibold text-gray-dark"></p>
            </div>
            <div>
                <p class="text-xs text-gray-muted font-medium">Status Pesanan</p>
                <p id="d_status" class="font-bold"></p>
            </div>
            <div>
                <p class="text-xs text-gray-muted font-medium">Pembayaran</p>
                <p id="d_payment" class="font-semibold text-gray-dark"></p>
            </div>
            <div>
                <p class="text-xs text-gray-muted font-medium">Pengiriman</p>
                <p id="d_method" class="font-semibold text-gray-dark"></p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-gray-muted font-medium">Alamat</p>
                <p id="d_address" class="font-medium text-gray-dark"></p>
            </div>

            <div id="d_cancel_reason_container" class="col-span-2 hidden bg-red-50 p-3.5 rounded-2xl border border-red-200">
                <p class="text-xs font-bold text-red-700 mb-0.5 flex items-center gap-1">
                    <span class="material-symbols-rounded text-sm">info</span> Alasan Pembatalan:
                </p>
                <p id="d_cancel_reason" class="text-xs text-red-600 font-semibold"></p>
            </div>
        </div>

        <h3 class="font-bold text-gray-dark mb-3 text-sm">
            Daftar Produk Dipesan
        </h3>

        <div class="overflow-x-auto rounded-2xl border border-gray-light mb-4">
            <table class="w-full text-xs">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light text-gray-muted font-semibold">
                        <th class="py-2.5 px-3 text-left">Produk</th>
                        <th class="py-2.5 px-3 text-center">Qty</th>
                        <th class="py-2.5 px-3 text-right">Harga</th>
                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="detailItems" class="divide-y divide-bg-light">
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-3 border-t border-bg-light">
            <div id="d_action_buttons" class="flex items-center gap-2">
                <!-- Action button injected dynamically in showDetail -->
            </div>
            <div class="text-right">
                <span class="font-bold text-xs text-gray-dark mr-2">Total Pembayaran:</span>
                <span id="d_total" class="font-extrabold text-lg text-primary"></span>
            </div>
        </div>
    </div>
</div>

<script>
function openCancelModal(invoiceNo) {
    document.getElementById('c_invoice_display').innerText = invoiceNo;
    document.getElementById('cancelForm').action = "/admin/orders/" + invoiceNo + "/reject";
    document.getElementById('cancel_reason_input').value = "";
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
}

function setCancelReason(reason) {
    document.getElementById('cancel_reason_input').value = reason;
}

function showDetail(order){
    document.getElementById('d_invoice').innerText = order.id;
    document.getElementById('d_date').innerText = order.date;
    document.getElementById('d_customer').innerText = order.customer;
    document.getElementById('d_status').innerText = order.status;
    document.getElementById('d_payment').innerText = order.payStatus;
    document.getElementById('d_method').innerText = order.method;
    document.getElementById('d_address').innerText = order.address;

    const cancelContainer = document.getElementById('d_cancel_reason_container');
    if (order.status === 'Dibatalkan' && order.cancel_reason) {
        document.getElementById('d_cancel_reason').innerText = order.cancel_reason;
        cancelContainer.classList.remove('hidden');
    } else {
        cancelContainer.classList.add('hidden');
    }

    document.getElementById('d_total').innerText = "Rp " + Number(order.total).toLocaleString("id-ID");

    let actionBtnHtml = "";
    if (order.status === 'Menunggu Verifikasi' || order.status === 'Menunggu Pembayaran' || order.payStatus === 'Menunggu' || order.payStatus === 'Menunggu Verifikasi') {
        actionBtnHtml = `
            <form action="/admin/orders/${order.id}/accept" method="POST" class="inline">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin MEMVERIFIKASI dan MENERIMA pesanan ${order.id} untuk diproses?');" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold cursor-pointer shadow-soft inline-flex items-center gap-1">
                    <span class="material-symbols-rounded text-sm">verified</span> Verifikasi & Terima Pesanan
                </button>
            </form>
        `;
    }
    document.getElementById('d_action_buttons').innerHTML = actionBtnHtml;

    let rows = "";
    if(order.items){
        order.items.forEach(function(item){
            rows += `
                <tr>
                    <td class="py-2.5 px-3 font-semibold text-gray-dark">${item.product}</td>
                    <td class="py-2.5 px-3 text-center font-medium">${item.qty}</td>
                    <td class="py-2.5 px-3 text-right font-medium">Rp ${Number(item.price).toLocaleString("id-ID")}</td>
                    <td class="py-2.5 px-3 text-right font-bold text-primary">Rp ${Number(item.subtotal).toLocaleString("id-ID")}</td>
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