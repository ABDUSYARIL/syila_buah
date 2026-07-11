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
                <p class="font-mono font-bold text-gray-dark text-base mt-0.5">{{ $order->invoice_no }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-muted sm:text-right">Tanggal Transaksi</p>
                <p class="font-bold text-gray-dark mt-0.5 sm:text-right">{{ $order->created_at->format('d M Y · H:i') }}</p>
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
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center gap-4 py-4 first:pt-0 last:pb-0">
                            <div class="w-14 h-14 rounded-xl border border-gray-light bg-white p-1.5 flex items-center justify-center flex-shrink-0">
                                <img src="{{ \App\Http\Controllers\ProductData::img($item->product->image ?? $item->product->img ?? '', 100, 100) }}" alt="{{ $item->product->name ?? 'Produk' }}" class="max-w-full max-h-full object-contain" />
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="font-bold text-gray-dark text-sm leading-tight truncate">{{ $item->product->name ?? 'Produk' }}</p>
                                <p class="text-xs text-gray-muted mt-0.5">{{ $item->qty }} {{ $item->product->unit ?? '' }} · {{ \App\Http\Controllers\ProductData::rp($item->price) }} / {{ $item->product->unit ?? '' }}</p>
                            </div>
                            <p class="font-bold text-gray-dark text-sm">{{ \App\Http\Controllers\ProductData::rp($item->price * $item->qty) }}</p>
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
                        <span>Total Harga ({{ $order->orderItems->sum('qty') }} barang)</span>
                        <span class="font-semibold text-gray-dark">{{ \App\Http\Controllers\ProductData::rp($order->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-gray-dark">{{ \App\Http\Controllers\ProductData::rp($order->shipping_cost) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode Pembayaran</span>
                        <span class="font-semibold text-gray-dark">{{ $order->payment->method ?? 'Belum dipilih' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status Pembayaran</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ ($order->payment->payment_status ?? 'Menunggu') === 'Lunas' ? 'bg-green-light text-primary' : 'bg-orange-50 text-orange-600' }}">{{ $order->payment->payment_status ?? 'Menunggu' }}</span>
                    </div>
                    @if($order->payment && $order->payment->payment_date)
                        <div class="flex justify-between">
                            <span>Tanggal Pembayaran</span>
                            <span class="font-semibold text-gray-dark">{{ $order->payment->payment_date->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between font-bold text-gray-dark text-base">
                    <span>Total Pembayaran</span>
                    <span class="text-primary font-extrabold text-lg">{{ \App\Http\Controllers\ProductData::rp($order->total) }}</span>
                </div>
            </div>

            <!-- Proof of Payment (Mock Image) -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft transition-all">
                <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3">Bukti Pembayaran</h3>
                <div class="border border-gray-light rounded-xl overflow-hidden shadow-sm aspect-[4/3] bg-bg-light flex items-center justify-center p-4">
                    @if($order->payment && $order->payment->proof_of_payment)
                        <img src="{{ asset('storage/' . $order->payment->proof_of_payment) }}" alt="Bukti Pembayaran" class="max-w-full max-h-full object-cover rounded shadow-sm border border-gray-light" />
                    @else
                        <div class="text-sm text-gray-muted">Belum ada bukti pembayaran yang diunggah.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
