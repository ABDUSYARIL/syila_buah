@extends('layouts.app')

@section('title', 'Detail Pesanan - Syila Buah')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Header Navigation -->
    <div class="mb-8">
        <a href="{{ route('history') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-muted hover:text-primary transition-colors">
            <span class="material-symbols-rounded text-base">arrow_back</span> Kembali ke Riwayat Pesanan
        </a>
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight mt-3">Detail Pesanan</h1>
    </div>

    <!-- Timeline Summary Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 mb-6 hover:shadow-soft transition-all">
        <div class="flex flex-col sm:flex-row justify-between border-b border-bg-light pb-4 mb-4 gap-2">
            <div>
                <p class="text-xs text-gray-muted">Nomor Invoice</p>
                <p class="font-mono font-bold text-gray-dark text-base mt-0.5">SB-240703-006</p>
            </div>
            <div>
                <p class="text-xs text-gray-muted sm:text-right">Tanggal Transaksi</p>
                <p class="font-bold text-gray-dark mt-0.5 sm:text-right">03 Jul 2025 · 14:45 WIB</p>
            </div>
        </div>
        
        <!-- Timeline Steps Row -->
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            @foreach([
                ['l' => 'Dibuat', 'i' => 'done_all', 'done' => true],
                ['l' => 'Bayar', 'i' => 'payment', 'done' => true],
                ['l' => 'Verifikasi', 'i' => 'verified_user', 'done' => true],
                ['l' => 'Diproses', 'i' => 'local_mall', 'done' => false],
                ['l' => 'Dikirim', 'i' => 'local_shipping', 'done' => false],
                ['l' => 'Selesai', 'i' => 'check_circle', 'done' => false]
            ] as $step)
                <div class="flex flex-col items-center text-center p-2 rounded-xl {{ $step['done'] ? 'bg-green-light/40 border border-primary/10' : 'border border-transparent' }}">
                    <span class="material-symbols-rounded text-sm {{ $step['done'] ? 'text-primary' : 'text-gray-300' }}">{{ $step['i'] }}</span>
                    <p class="text-[10px] font-bold mt-1 {{ $step['done'] ? 'text-primary' : 'text-gray-300' }}">{{ $step['l'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Product list and Details Layout (2 columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Product List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft transition-all">
                <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3">Daftar Produk</h3>
                
                <div class="divide-y divide-bg-light">
                    @foreach([
                        ['name' => 'Apel Fuji', 'qty' => 2, 'unit' => 'Kg', 'price' => 35000, 'img' => '1560806887-1e4cd0b6cbd6'],
                        ['name' => 'Stroberi', 'qty' => 1, 'unit' => 'Pack', 'price' => 45000, 'img' => '1464965911861-746a04b4bca6'],
                        ['name' => 'Mangga Harum Manis', 'qty' => 3, 'unit' => 'Kg', 'price' => 28000, 'img' => '1553279768-865429fa0078']
                    ] as $item)
                        <div class="flex items-center gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="w-14 h-14 rounded-xl border border-gray-light bg-white p-1.5 flex items-center justify-center flex-shrink-0">
                                <img src="{{ \App\Http\Controllers\ProductData::img($item['img'], 100, 100) }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain" />
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="font-bold text-gray-dark text-sm leading-tight truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-muted mt-0.5">{{ $item['qty'] }} {{ $item['unit'] }} · {{ \App\Http\Controllers\ProductData::rp($item['price']) }} / {{ $item['unit'] }}</p>
                            </div>
                            <p class="font-bold text-gray-dark text-sm">{{ \App\Http\Controllers\ProductData::rp($item['price'] * $item['qty']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Info Card -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft transition-all">
                <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3">Informasi Pengiriman</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-muted">Alamat Tujuan</p>
                        <p class="font-bold text-gray-dark mt-0.5">Rina Kartika</p>
                        <p class="text-gray-muted mt-0.5 leading-relaxed text-xs">Jl. Melati No. 12, RT 03/RW 05, Kel. Sukasari, Kec. Cicendo, Kota Bandung, Jawa Barat 40171</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-bg-light">
                        <div>
                            <p class="text-xs text-gray-muted">Metode Pengiriman</p>
                            <p class="font-semibold text-gray-dark mt-0.5">Reguler (1-2 Hari)</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-muted">No. Resi Pengiriman</p>
                            <p class="font-mono font-bold text-primary mt-0.5">SYLA-REG-43890123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Summary and Payment Proof -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft transition-all">
                <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3">Rincian Pembayaran</h3>
                
                <div class="space-y-2 text-sm text-gray-muted border-b border-bg-light pb-4 mb-4">
                    <div class="flex justify-between">
                        <span>Total Harga (3 barang)</span>
                        <span class="font-semibold text-gray-dark">Rp 199.000</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-gray-dark">Rp 15.000</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode Pembayaran</span>
                        <span class="font-semibold text-gray-dark">Transfer Bank (BCA)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status Pembayaran</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-light text-primary">Sudah Bayar</span>
                    </div>
                </div>

                <div class="flex justify-between font-bold text-gray-dark text-base">
                    <span>Total Pembayaran</span>
                    <span class="text-primary font-extrabold text-lg">Rp 214.000</span>
                </div>
            </div>

            <!-- Proof of Payment (Mock Image) -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft transition-all">
                <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3">Bukti Pembayaran</h3>
                <div class="border border-gray-light rounded-xl overflow-hidden shadow-sm aspect-[4/3] bg-bg-light flex items-center justify-center p-4">
                    <img src="https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?w=300&h=200&fit=crop&auto=format" alt="Struk Transfer" class="max-w-full max-h-full object-cover rounded shadow-sm border border-gray-light" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
