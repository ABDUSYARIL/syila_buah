@extends('layouts.app')

@section('title', $product['name'] . ' - Syila Buah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8" x-data="{ 
    qty: 1, 
    maxStock: {{ $product['stock'] }}, 
    price: {{ $product['price'] }}, 
    showGuestDialog: false,
    isLoggedIn: {{ (session('role') === 'pelanggan' || session('role') === 'customer') ? 'true' : 'false' }}
}">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-muted mb-6">
        <a href="{{ route('landing') }}" class="hover:text-primary transition-colors">Beranda</a>
        <span class="material-symbols-rounded text-sm text-gray-muted">chevron_right</span>
        <a href="{{ route('catalog') }}" class="hover:text-primary transition-colors">Produk</a>
        <span class="material-symbols-rounded text-sm text-gray-muted">chevron_right</span>
        <span class="text-gray-dark font-medium">{{ $product['name'] }}</span>
    </div>

    <!-- Product Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
        <!-- Left: Image Display -->
        <div class="space-y-4">
            <div class="aspect-square rounded-2xl bg-white border border-gray-light p-8 flex items-center justify-center shadow-soft hover:shadow-soft-hover transition-all duration-300">
                <img src="{{ \App\Http\Controllers\ProductData::img($product['img'], 600, 600) }}" alt="{{ $product['name'] }}" class="max-w-full max-h-full object-contain transform hover:scale-105 transition-transform duration-300" />
            </div>
            <div class="grid grid-cols-4 gap-3">
                @for ($i = 0; $i < 4; $i++)
                    <div class="aspect-square rounded-xl bg-white border-2 p-2 flex items-center justify-center cursor-pointer transition-all duration-300 {{ $i === 0 ? 'border-primary' : 'border-gray-light hover:border-primary/50' }}">
                        <img src="{{ \App\Http\Controllers\ProductData::img($product['img'], 120, 120) }}" alt="" class="max-w-full max-h-full object-contain" />
                    </div>
                @endfor
            </div>
        </div>

        <!-- Right: Info -->
        <div class="space-y-6">
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-light text-primary">{{ $product->category->name ?? 'Buah' }}</span>
                <h1 class="text-3xl font-extrabold text-gray-dark mt-2 tracking-tight">{{ $product['name'] }}</h1>
                <div class="flex items-center gap-3 mt-2">
                    <div class="flex items-center gap-0.5">
                        @for($i=1; $i<=5; $i++)
                            <span class="material-symbols-rounded text-base text-accent {{ $i <= round($product['rating']) ? 'fill-1' : 'text-gray-200' }}">star</span>
                        @endfor
                    </div>
                    <span class="text-sm text-gray-muted">({{ $product['sold'] }} terjual)</span>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl border border-gray-light p-6 shadow-soft">
                <p class="text-3xl font-extrabold text-primary leading-none">{{ \App\Http\Controllers\ProductData::rp($product['price']) }}</p>
                <p class="text-xs text-gray-muted mt-2">per {{ $product['unit'] }}</p>
            </div>
            
            <div class="space-y-2 text-sm">
                <div class="flex items-center gap-2 text-gray-muted">
                    <span class="material-symbols-rounded text-lg text-primary">inventory_2</span>
                    <span>Stok: <span class="font-semibold {{ $product['stock'] <= 20 ? 'text-red-500' : 'text-gray-dark' }}">{{ $product['stock'] }} {{ $product['unit'] }}</span></span>
                </div>
                <div class="flex items-center gap-2 text-gray-muted">
                    <span class="material-symbols-rounded text-lg text-primary">local_shipping</span>
                    <span>Estimasi Pengiriman: 1-2 Hari Kerja</span>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-dark mb-2">Deskripsi Produk</h4>
                <p class="text-sm text-gray-muted leading-relaxed">{{ $product['desc'] }}</p>
            </div>

            <!-- Qty & Order Form -->
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-6 pt-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                
                <!-- Qty Selector -->
                <div>
                    <p class="text-sm font-semibold text-gray-dark mb-3">Jumlah ({{ $product['unit'] }})</p>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border border-gray-light rounded-xl overflow-hidden bg-white shadow-sm">
                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-10 flex items-center justify-center hover:bg-bg-light transition-colors cursor-pointer">
                                <span class="material-symbols-rounded text-primary text-base">remove</span>
                            </button>
                            <span class="w-12 text-center text-gray-dark font-bold text-base" x-text="qty"></span>
                            <button type="button" @click="qty = Math.min(maxStock, qty + 1)" class="w-10 h-10 flex items-center justify-center hover:bg-bg-light transition-colors cursor-pointer">
                                <span class="material-symbols-rounded text-primary text-base">add</span>
                            </button>
                        </div>
                        <input type="hidden" name="qty" :value="qty">
                        <p class="text-sm text-gray-muted font-medium">Total: <span class="text-primary font-bold text-lg" x-text="'Rp ' + (price * qty).toLocaleString('id-ID')"></span></p>
                    </div>
                </div>

                <!-- Submit buttons -->
                <div class="flex gap-4 pt-2">
                    <button
                        type="submit"
                        @click.prevent="if(isLoggedIn) { $el.form.submit() } else { showGuestDialog = true }"
                        class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl border-2 border-primary text-primary hover:bg-green-light px-6 py-3 text-base transition-all duration-300 shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 cursor-pointer"
                    >
                        <span class="material-symbols-rounded text-lg">shopping_cart</span> Tambah ke Keranjang
                    </button>
                    <button
                        type="submit"
                        @click.prevent="if(isLoggedIn) { $el.form.action='{{ route('cart.add') }}?checkout=true'; $el.form.submit() } else { showGuestDialog = true }"
                        class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl bg-accent text-white hover:bg-accent-hover active:bg-accent-active px-6 py-3 text-base transition-all duration-300 shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 cursor-pointer"
                    >
                        Beli Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Related Products -->
    @if(count($related) > 0)
        <div class="mt-16 border-t border-gray-light pt-12">
            <h2 class="text-2xl font-extrabold text-gray-dark mb-6 tracking-tight">Produk Terkait</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($related as $p)
                    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden group flex flex-col justify-between hover:shadow-soft-hover transform hover:-translate-y-1.5 transition-all duration-300">
                        <a href="{{ route('product.detail', $p['id']) }}" class="block">
                            <div class="relative bg-white overflow-hidden aspect-square p-4 flex items-center justify-center border-b border-bg-light">
                                <img
                                    src="{{ \App\Http\Controllers\ProductData::img($p['img'], 400, 400) }}"
                                    alt="{{ $p['name'] }}"
                                    class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-300"
                                />
                            </div>
                        </a>
                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <p class="text-xs text-gray-muted font-medium uppercase tracking-wider mb-1">{{ $p->category->name ?? 'Buah' }}</p>
                                <a href="{{ route('product.detail', $p['id']) }}" class="hover:text-primary transition-colors">
                                    <h3 class="font-bold text-gray-dark text-sm leading-tight mb-2 h-10 overflow-hidden">{{ $p['name'] }}</h3>
                                </a>
                            </div>
                            <div class="flex items-center justify-between border-t border-bg-light pt-3">
                                <div>
                                    <p class="font-extrabold text-primary text-sm leading-none">{{ \App\Http\Controllers\ProductData::rp($p['price']) }}</p>
                                    <p class="text-[10px] text-gray-muted mt-0.5">per {{ $p['unit'] }}</p>
                                </div>
                                <a href="{{ route('product.detail', $p['id']) }}" class="inline-flex items-center justify-center rounded-xl bg-green-light text-primary hover:bg-primary hover:text-white px-3 py-2 text-xs transition-colors duration-300">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Guest Login Dialog Modal -->
    <div 
        x-show="showGuestDialog" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-200" 
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center px-4" 
        style="display: none;"
    >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-dark/50 backdrop-blur-sm" @click="showGuestDialog = false"></div>

        <!-- Dialog Box (3D style) -->
        <div class="bg-white rounded-2xl shadow-3d border border-gray-light max-w-sm w-full p-8 z-10 text-center relative transform transition-all duration-300 hover:scale-102">
            <button @click="showGuestDialog = false" class="absolute top-4 right-4 text-gray-muted hover:text-gray-dark transition-colors cursor-pointer">
                <span class="material-symbols-rounded text-lg">close</span>
            </button>
            
            <div class="w-16 h-16 rounded-full bg-orange-50 text-accent flex items-center justify-center mx-auto mb-4 shadow-sm animate-bounce">
                <span class="material-symbols-rounded text-3xl">account_circle</span>
            </div>
            
            <h3 class="text-lg font-bold text-gray-dark mb-2 tracking-tight">Perlu Masuk Akun</h3>
            <p class="text-sm text-gray-muted leading-relaxed mb-6">Silakan masuk terlebih dahulu untuk melanjutkan transaksi.</p>
            
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center font-bold rounded-xl bg-primary text-white hover:bg-primary-hover active:bg-primary-active px-4 py-2.5 text-sm shadow-soft transition-all duration-300">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center font-bold rounded-xl border-2 border-primary text-primary hover:bg-green-light px-4 py-2.5 text-sm transition-all duration-300">
                    Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
