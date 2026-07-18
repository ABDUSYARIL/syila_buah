@extends('layouts.app')

@section('title', 'Beranda Pelanggan - Syila Buah')

@section('content')
<div class="space-y-12 pb-16" x-data="{ activeCategory: '{{ $category }}' }">
    <!-- Hero Kecil -->
    <div class="relative bg-green-dark text-white rounded-b-[2rem] overflow-hidden shadow-soft">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.1),transparent)]"></div>
        <div class="max-w-7xl mx-auto px-6 py-10 relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <p class="text-xs font-bold text-accent uppercase tracking-wider">Selamat datang kembali,</p>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mt-1">Temukan Buah Segar Pilihan Anda Hari Ini</h1>
            </div>
            
            <!-- Search bar -->
            <form action="{{ route('home') }}" method="GET" class="w-full md:w-80 relative group">
                <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-white/50 group-focus-within:text-white transition-colors">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari buah segar..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-white/20 bg-white/10 text-white placeholder-white/60 focus:outline-none focus:bg-white/20 focus:border-white transition-all text-sm"
                />
            </form>
        </div>
    </div>

    <!-- Category Tabs -->
    <section class="max-w-7xl mx-auto px-6">
        <h3 class="text-xs font-extrabold text-gray-muted uppercase tracking-wider mb-4">Pilih Berdasarkan Kategori</h3>
        <div class="flex gap-3 overflow-x-auto pb-2">
            @foreach (['Semua', 'Buah Lokal', 'Buah Impor'] as $c)
                <a
                    href="{{ route('home', ['category' => $c]) }}"
                    class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-semibold border block transition-all duration-300"
                    :class="activeCategory === '{{ $c }}' ? 'bg-primary text-white border-primary shadow-soft' : 'bg-white text-gray-muted border-gray-light hover:border-primary hover:text-primary'"
                >
                    {{ $c }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- Search Results / Filtered Products Grid -->
    @if(!empty($search) || $category !== 'Semua')
        <section class="max-w-7xl mx-auto px-6">
            <h2 class="text-xl font-extrabold text-gray-dark mb-6 tracking-tight">Hasil Pencarian & Filter</h2>
            @if(count($products) === 0)
                <div class="bg-white rounded-2xl border border-gray-light p-12 text-center shadow-soft">
                    <span class="material-symbols-rounded text-gray-muted text-4xl mb-2">search_off</span>
                    <p class="text-sm font-semibold text-gray-muted">Produk yang Anda cari tidak ditemukan.</p>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($products as $p)
                        <div class="card-3d overflow-hidden group flex flex-col justify-between rounded-2xl">
                            <a href="{{ route('product.detail', $p['id']) }}" class="block">
                                <div class="relative bg-white aspect-square p-4 flex items-center justify-center border-b border-bg-light">
                                    <img src="{{ \App\Http\Controllers\ProductData::img($p['img'], 400, 400) }}" alt="{{ $p['name'] }}" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-300" />
                                    <div class="absolute top-2 left-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-xl text-[10px] font-bold bg-green-light text-primary border border-primary/10 shadow-sm">
                                            Stok: {{ $p->stock }} {{ $p->unit }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <div class="p-4 flex-grow flex flex-col justify-between">
                                <div>
                                    <p class="text-xs text-gray-muted font-medium uppercase tracking-wider mb-1">{{ $p->category->name ?? 'Buah' }}</p>
                                    <a href="{{ route('product.detail', $p['id']) }}" class="hover:text-primary transition-colors">
                                        <h3 class="font-bold text-gray-dark text-sm leading-tight mb-2 h-10 overflow-hidden">{{ $p['name'] }}</h3>
                                    </a>
                                </div>
                                <div class="flex items-center justify-between border-t border-bg-light pt-3">
                                    <div>
                                        <p class="font-extrabold text-primary text-sm leading-none">{{ \App\Http\Controllers\ProductData::rp($p['price']) }}</p>
                                        <p class="text-[10px] text-gray-muted mt-0.5">/ {{ $p['unit'] }}</p>
                                    </div>
                                    <a href="{{ route('product.detail', $p['id']) }}" class="inline-flex items-center justify-center rounded-xl bg-green-light text-primary hover:bg-primary hover:text-white px-3 py-2 text-xs transition-colors duration-300 font-semibold">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        <!-- Logged-in Home default widgets -->

        <!-- Produk Terlaris -->
        <section class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-dark tracking-tight">Produk Terlaris 🔥</h2>
                    <p class="text-xs text-gray-muted">Pilihan paling favorit pelanggan kami hari ini.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($bestsellers as $p)
                    <div class="card-3d overflow-hidden group flex flex-col justify-between rounded-2xl">
                        <a href="{{ route('product.detail', $p['id']) }}" class="block">
                            <div class="relative bg-white aspect-square p-4 flex items-center justify-center border-b border-bg-light">
                                <img src="{{ \App\Http\Controllers\ProductData::img($p['img'], 400, 400) }}" alt="{{ $p['name'] }}" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-300" />
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-xl text-[10px] font-bold bg-green-light text-primary border border-primary/10 shadow-sm">
                                        Stok: {{ $p->stock }} {{ $p->unit }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <p class="text-xs text-gray-muted font-medium uppercase tracking-wider mb-1">{{ $p->category->name ?? 'Buah' }}</p>
                                <a href="{{ route('product.detail', $p['id']) }}" class="hover:text-primary transition-colors">
                                    <h3 class="font-bold text-gray-dark text-sm leading-tight mb-2 h-10 overflow-hidden">{{ $p['name'] }}</h3>
                                </a>
                            </div>
                            <div class="flex items-center justify-between border-t border-bg-light pt-3">
                                <div>
                                    <p class="font-extrabold text-primary text-sm leading-none">{{ \App\Http\Controllers\ProductData::rp($p['price']) }}</p>
                                    <p class="text-[10px] text-gray-muted mt-0.5">/ {{ $p['unit'] }}</p>
                                </div>
                                <a href="{{ route('product.detail', $p['id']) }}" class="inline-flex items-center justify-center rounded-xl bg-green-light text-primary hover:bg-primary hover:text-white px-3 py-2 text-xs transition-colors duration-300 font-semibold">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Produk Terbaru -->
        <section class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-dark tracking-tight">Produk Terbaru 🌱</h2>
                    <p class="text-xs text-gray-muted">Buah segar pilihan yang baru saja panen minggu ini.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($newest as $p)
                    <div class="card-3d overflow-hidden group flex flex-col justify-between rounded-2xl">
                        <a href="{{ route('product.detail', $p['id']) }}" class="block">
                            <div class="relative bg-white aspect-square p-4 flex items-center justify-center border-b border-bg-light">
                                <img src="{{ \App\Http\Controllers\ProductData::img($p['img'], 400, 400) }}" alt="{{ $p['name'] }}" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-300" />
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-xl text-[10px] font-bold bg-green-light text-primary border border-primary/10 shadow-sm">
                                        Stok: {{ $p->stock }} {{ $p->unit }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <p class="text-xs text-gray-muted font-medium uppercase tracking-wider mb-1">{{ $p->category->name ?? 'Buah' }}</p>
                                <a href="{{ route('product.detail', $p['id']) }}" class="hover:text-primary transition-colors">
                                    <h3 class="font-bold text-gray-dark text-sm leading-tight mb-2 h-10 overflow-hidden">{{ $p['name'] }}</h3>
                                </a>
                            </div>
                            <div class="flex items-center justify-between border-t border-bg-light pt-3">
                                <div>
                                    <p class="font-extrabold text-primary text-sm leading-none">{{ \App\Http\Controllers\ProductData::rp($p['price']) }}</p>
                                    <p class="text-[10px] text-gray-muted mt-0.5">/ {{ $p['unit'] }}</p>
                                </div>
                                <a href="{{ route('product.detail', $p['id']) }}" class="inline-flex items-center justify-center rounded-xl bg-green-light text-primary hover:bg-primary hover:text-white px-3 py-2 text-xs transition-colors duration-300 font-semibold">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
