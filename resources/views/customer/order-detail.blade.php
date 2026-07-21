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

    @if(strtolower(trim($order->status)) === 'dibatalkan')
        <!-- Alert Penjelasan Pembatalan Pesanan -->
        <div class="mb-6 p-5 rounded-2xl bg-red-50 border-2 border-red-400 text-red-900 shadow-soft">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 font-bold">
                    <span class="material-symbols-rounded text-xl">cancel</span>
                </div>
                <div class="space-y-1.5 flex-grow">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <h4 class="font-black text-sm text-red-700 uppercase tracking-wider">Pesanan Dibatalkan</h4>
                        <span class="text-[10px] font-extrabold bg-red-100 text-red-800 px-2.5 py-0.5 rounded-full border border-red-300">
                            Dibatalkan oleh: Administrator (Admin Toko)
                        </span>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-red-200 mt-1">
                        <p class="text-xs font-bold text-gray-800">Alasan Pembatalan Pesanan:</p>
                        <p class="text-xs font-semibold text-red-600 mt-0.5">
                            "{{ $order->cancel_reason ?? 'Dibatalkan oleh admin toko.' }}"
                        </p>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Stok produk pesanan ini telah otomatis dikembalikan ke stok toko.</p>
                </div>
            </div>
        </div>
    @endif

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
        @php
    $status = strtolower(trim($order->status));

    $steps = [
        ['label' => 'Dibuat', 'icon' => 'done_all'],
        ['label' => 'Bayar', 'icon' => 'payment'],
        ['label' => 'Verifikasi', 'icon' => 'verified_user'],
        ['label' => 'Diproses', 'icon' => 'local_mall'],
        ['label' => 'Dikirim', 'icon' => 'local_shipping'],
        ['label' => 'Selesai', 'icon' => 'check_circle'],
    ];

    $currentStep = match ($status) {
        'menunggu pembayaran' => 1,
        'menunggu verifikasi' => 2,
        'diproses' => 3,
        'dikirim' => 4,
        'selesai' => 5,
        default => 0,
    };
@endphp
<div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
    @foreach($steps as $index => $step)
        @php
            $done = $index <= $currentStep;
        @endphp

        <div class="flex flex-col items-center text-center p-2 rounded-xl
            {{ $done
                ? 'bg-green-light/40 border border-primary/10'
                : 'border border-transparent' }}">

            <span class="material-symbols-rounded text-sm
                {{ $done
                    ? 'text-primary'
                    : 'text-gray-300' }}">
                {{ $step['icon'] }}
            </span>

            <p class="text-[10px] font-bold mt-1
                {{ $done
                    ? 'text-primary'
                    : 'text-gray-300' }}">
                {{ $step['label'] }}
            </p>
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

            @php
                $orderStatus = strtolower(trim((string) ($order->status ?? '')));
                $shippingAddress = filled($order->shipping_address) ? $order->shipping_address : 'Alamat belum tersedia.';
                $shippingMethod = filled($order->shipping_method) ? $order->shipping_method : '-';

                $statusBadge = match ($orderStatus) {
                    'menunggu pembayaran', 'pending', 'menunggu_pembayaran' => [
                        'label' => 'Menunggu Pembayaran',
                        'class' => 'bg-red-50 text-red-600 border border-red-200',
                    ],
                    'menunggu verifikasi', 'menunggu_verifikasi', 'verifikasi', 'verified' => [
                        'label' => 'Menunggu Verifikasi',
                        'class' => 'bg-orange-50 text-orange-600 border border-orange-200',
                    ],
                    'diproses', 'processing', 'proses', 'dikemas', 'packing' => [
                        'label' => 'Diproses',
                        'class' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                    ],
                    'dikirim', 'shipped', 'delivery', 'sent' => [
                        'label' => 'Dikirim',
                        'class' => 'bg-blue-50 text-blue-700 border border-blue-200',
                    ],
                    'selesai', 'completed', 'done', 'finished', 'success' => [
                        'label' => 'Selesai',
                        'class' => 'bg-green-50 text-green-700 border border-green-200',
                    ],
                    'dibatalkan', 'cancelled', 'canceled', 'batal' => [
                        'label' => 'Dibatalkan',
                        'class' => 'bg-gray-100 text-gray-600 border border-gray-300',
                    ],
                    default => [
                        'label' => ucwords(str_replace('_', ' ', $orderStatus ?: 'status belum tersedia')),
                        'class' => 'bg-gray-100 text-gray-600 border border-gray-300',
                    ],
                };
            @endphp

            <!-- Shipping Info Card -->
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft transition-all">
                <div class="flex items-start justify-between gap-3 mb-4 border-b border-bg-light pb-3">
                    <h3 class="font-bold text-gray-dark text-base">Informasi Pengiriman</h3>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusBadge['class'] }}">
                        {{ $statusBadge['label'] }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-muted">Alamat Tujuan</p>
                        <p class="font-bold text-gray-dark mt-0.5">{{ $shippingAddress }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-bg-light">
                        <div>
                            <p class="text-xs text-gray-muted">Metode Pengiriman</p>
                            <p class="font-semibold text-gray-dark mt-0.5">{{ $shippingMethod }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-muted">Status Pesanan</p>
                            <p class="font-semibold text-gray-dark mt-0.5">{{ $statusBadge['label'] }}</p>
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

            <!-- Hubungi Admin WhatsApp -->
            {{-- Menyediakan akses cepat menghubungi admin untuk konfirmasi pembayaran --}}
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft transition-all text-center">
                <h4 class="font-bold text-gray-dark text-sm mb-1">Butuh bantuan transaksi?</h4>
                <p class="text-xs text-gray-muted leading-relaxed mb-4">Hubungi admin WhatsApp kami untuk konfirmasi pembayaran cepat.</p>
                @php
                    // Menyusun teks pesan WhatsApp otomatis berisi nomor invoice
                    $waMsgDetail = rawurlencode("Halo Admin Syila Buah, saya ingin menanyakan status pesanan saya dengan Invoice: " . ($order->invoice_no ?? ''));
                @endphp
                <a href="https://wa.me/6281234567890?text={{ $waMsgDetail }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 font-semibold rounded-xl border border-primary text-primary hover:bg-green-light px-4 py-2.5 text-xs transition-all cursor-pointer shadow-sm">
                    <span class="material-symbols-rounded text-sm">support_agent</span> Chat WhatsApp Admin
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
