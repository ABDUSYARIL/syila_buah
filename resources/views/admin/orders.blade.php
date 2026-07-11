@extends('layouts.admin')

@section('title', 'Kelola Pesanan - Admin Syila Buah')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-dark">Kelola Pesanan</h1>
        <p class="text-sm text-gray-muted">{{ count($orders) }} total pesanan</p>
    </div>

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
                    <a href="{{ route('admin.orders', ['status' => $s, 'search' => $search]) }}" 
                       class="px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all border block select-none
                        {{ $status === $s ? 'bg-primary text-white border-primary shadow-soft' : 'bg-white border-gray-light text-gray-muted hover:border-primary hover:text-primary' }}">
                        {{ $s }}
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
                        @foreach(['Invoice', 'Pelanggan', 'Tanggal', 'Total', 'Pembayaran', 'Status', 'Aksi'] as $h)
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
                                    <button onclick="alert('Detail Invoice: {{ $o['id'] }}\nPelanggan: {{ $o['customer'] }}\nTotal: {{ \App\Http\Controllers\ProductData::rp($o['total']) }}\nAlamat: {{ $o['address'] }}')" class="px-2.5 py-1.5 rounded-xl bg-green-light text-primary text-xs font-semibold hover:bg-green-200 cursor-pointer shadow-sm">Detail</button>
                                    @if($o['payStatus'] === 'Menunggu Verifikasi')
                                        <button onclick="alert('Pembayaran pesanan {{ $o['id'] }} diverifikasi!')" class="px-2.5 py-1.5 rounded-xl bg-blue-50 text-blue-600 text-xs font-semibold hover:bg-blue-100 cursor-pointer shadow-sm">Verifikasi</button>
                                    @endif
                                    @if($o['status'] === 'Diproses')
                                        <button onclick="alert('Pesanan {{ $o['id'] }} diubah statusnya menjadi Dikirim!')" class="px-2.5 py-1.5 rounded-xl bg-[#FFF3E0] text-accent text-xs font-semibold hover:bg-[#FFE0B2] cursor-pointer shadow-sm">Kirim</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
