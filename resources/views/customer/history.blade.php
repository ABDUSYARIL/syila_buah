@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Syila Buah')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight">Riwayat Pesanan</h1>
        <p class="text-sm text-gray-muted mt-1">Daftar transaksi pembelian buah segar Anda di Syila Buah.</p>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden hover:shadow-soft-hover transition-all duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light">
                        @foreach (['Invoice', 'Tanggal', 'Total', 'Status', 'Aksi'] as $header)
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-muted uppercase tracking-wider">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $o)
                        <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                            <!-- Invoice -->
                            <td class="py-4.5 px-6 font-mono text-xs font-bold text-gray-dark">
                                {{ $o['id'] }}
                            </td>
                            
                            <!-- Tanggal -->
                            <td class="py-4.5 px-6 text-gray-muted">
                                {{ $o['date'] }}
                            </td>
                            
                            <!-- Total -->
                            <td class="py-4.5 px-6 font-bold text-primary">
                                {{ \App\Http\Controllers\ProductData::rp($o['total']) }}
                            </td>
                            
                            <!-- Status -->
                            <td class="py-4.5 px-6">
                                @php
                                    $statusClass = match($o['status']) {
                                        'Selesai' => 'bg-green-light text-primary',
                                        'Dikirim' => 'bg-blue-50 text-blue-600',
                                        'Diproses' => 'bg-purple-50 text-purple-600',
                                        'Menunggu Pembayaran' => 'bg-accent/15 text-accent',
                                        default => 'bg-gray-100 text-gray-500'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ $o['status'] }}
                                </span>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="py-4.5 px-6">
                                <a href="{{ route('order.detail') }}" class="inline-flex items-center justify-center gap-1.5 font-bold rounded-xl bg-green-light text-primary hover:bg-primary hover:text-white px-3.5 py-2 text-xs transition-colors duration-300">
                                    Detail <span class="material-symbols-rounded text-sm">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
