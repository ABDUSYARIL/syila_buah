@extends('layouts.app')

@section('title', 'Keranjang Belanja - Syila Buah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight">Keranjang Belanja</h1>
        <p class="text-sm text-gray-muted mt-1">Kelola buah segar yang telah Anda pilih sebelum melanjutkan transaksi.</p>
    </div>

    @if(empty($cart))
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-gray-light p-6 shadow-soft">
            <div class="w-16 h-16 rounded-2xl bg-green-light flex items-center justify-center mb-4">
                <span class="material-symbols-rounded text-primary text-3xl">shopping_cart</span>
            </div>
            <p class="text-lg font-bold text-gray-dark">Keranjang Kosong</p>
            <p class="text-sm text-gray-muted mt-1 mb-6">Mulai belanja buah segar pilihan Anda</p>
            <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover active:bg-primary-active px-5 py-2.5 text-sm shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 transition-all duration-300">
                Mulai Belanja
            </a>
        </div>
    @else
        @php
            $subtotal = 0;
            foreach($cart as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }
            // Standard flat rate shipping cost
            $ongkir = 15000;
            $total = $subtotal + $ongkir;
        @endphp
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Table Card -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-bg-light">
                            <tr class="border-b border-gray-light">
                                <th class="text-left py-4 px-6 text-xs font-bold text-gray-muted uppercase tracking-wider">Foto</th>
                                <th class="text-left py-4 px-4 text-xs font-bold text-gray-muted uppercase tracking-wider">Produk</th>
                                <th class="text-left py-4 px-4 text-xs font-bold text-gray-muted uppercase tracking-wider">Harga</th>
                                <th class="text-center py-4 px-4 text-xs font-bold text-gray-muted uppercase tracking-wider">Jumlah</th>
                                <th class="text-right py-4 px-4 text-xs font-bold text-gray-muted uppercase tracking-wider">Subtotal</th>
                                <th class="text-center py-4 px-6 text-xs font-bold text-gray-muted uppercase tracking-wider">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $productId => $item)
                                <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                                    <!-- Foto -->
                                    <td class="py-4 px-6">
                                        <div class="w-16 h-16 rounded-xl bg-white border border-gray-light p-2 flex items-center justify-center">
                                            <img src="{{ \App\Http\Controllers\ProductData::img($item['img'], 120, 120) }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain" />
                                        </div>
                                    </td>
                                    
                                    <!-- Produk -->
                                    <td class="py-4 px-4">
                                        <p class="font-bold text-gray-dark text-sm">{{ $item['name'] }}</p>
                                        <p class="text-xs text-gray-muted mt-0.5">{{ $item['unit'] }}</p>
                                    </td>
                                    
                                    <!-- Harga -->
                                    <td class="py-4 px-4 font-semibold text-gray-dark">
                                        {{ \App\Http\Controllers\ProductData::rp($item['price']) }}
                                    </td>
                                    
                                    <!-- Jumlah -->
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center">
                                            <div class="flex items-center border border-gray-light rounded-xl overflow-hidden bg-white shadow-sm">
                                                <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $productId }}">
                                                    <input type="hidden" name="qty" value="{{ $item['qty'] - 1 }}">
                                                    <button type="submit" class="w-8 h-8 flex items-center justify-center hover:bg-bg-light transition-colors cursor-pointer">
                                                        <span class="material-symbols-rounded text-primary text-xs">remove</span>
                                                    </button>
                                                </form>
                                                <span class="w-8 text-center text-xs font-bold text-gray-dark">{{ $item['qty'] }}</span>
                                                <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $productId }}">
                                                    <input type="hidden" name="qty" value="{{ $item['qty'] + 1 }}">
                                                    <button type="submit" class="w-8 h-8 flex items-center justify-center hover:bg-bg-light transition-colors cursor-pointer">
                                                        <span class="material-symbols-rounded text-primary text-xs">add</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Subtotal -->
                                    <td class="py-4 px-4 text-right font-extrabold text-primary">
                                        {{ \App\Http\Controllers\ProductData::rp($item['price'] * $item['qty']) }}
                                    </td>
                                    
                                    <!-- Hapus -->
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('cart.remove', $productId) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-colors flex items-center justify-center mx-auto cursor-pointer">
                                                <span class="material-symbols-rounded text-base">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Sidebar -->
            <div>
                <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 sticky top-24 hover:shadow-soft-hover transition-all duration-300">
                    <h3 class="font-bold text-gray-dark text-base border-b border-bg-light pb-4 mb-4">Ringkasan Belanja</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-muted">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-dark">{{ \App\Http\Controllers\ProductData::rp($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-muted">
                            <span>Ongkos Kirim</span>
                            <span class="font-semibold text-gray-dark">{{ \App\Http\Controllers\ProductData::rp($ongkir) }}</span>
                        </div>
                        <div class="border-t border-bg-light pt-3 flex justify-between font-bold text-gray-dark text-base">
                            <span>Total</span>
                            <span class="text-primary font-extrabold text-lg">{{ \App\Http\Controllers\ProductData::rp($total) }}</span>
                        </div>
                    </div>

                    <div class="pt-6">
                        <a href="{{ route('checkout') }}" class="w-full inline-flex items-center justify-center gap-2 font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover active:bg-primary-active shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 transition-all duration-300 px-4 py-3 text-base">
                            Checkout <span class="material-symbols-rounded text-lg">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
