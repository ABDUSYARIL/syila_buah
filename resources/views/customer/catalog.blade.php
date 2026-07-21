@extends('layouts.app')

@section('title', 'Katalog Produk Buah Segar - Syila Buah')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8" x-data="{ activeCategory: '{{ $category }}', sortValue: '{{ $sort }}' }">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight">Katalog Produk</h1>
        <p class="text-sm text-gray-muted mt-1">Pilih dari bermacam buah lokal dan impor segar kualitas terbaik kami.</p>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 mb-8 hover:shadow-soft-hover transition-all duration-300">
        <form action="{{ route('catalog') }}" method="GET" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search bar -->
                <div class="relative flex-1 group">
                    <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">search</span>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Cari buah segar kesukaan Anda..." 
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-light text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-bg-light"
                    />
                </div>
                
                <!-- Sorting dropdown -->
                <div class="w-full md:w-60 relative">
                    <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted">sort</span>
                    <select 
                        name="sort" 
                        onchange="this.form.submit()"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-light text-sm text-gray-dark bg-bg-light focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none cursor-pointer"
                    >
                        <option value="default" {{ $sort === 'default' ? 'selected' : '' }}>Urutkan Default</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga: Terendah ke Tinggi</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga: Tertinggi ke Rendah</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama: A - Z</option>
                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Nama: Z - A</option>
                    </select>
                    <span class="material-symbols-rounded absolute right-3 top-1/2 -translate-y-1/2 text-gray-muted pointer-events-none">unfold_more</span>
                </div>
            </div>

            <!-- Category filter tabs -->
            <div class="pt-2 border-t border-bg-light">
                <p class="text-xs font-bold text-gray-muted uppercase tracking-wider mb-3">Kategori Kategori</p>
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <input type="hidden" name="category" :value="activeCategory">
                    @foreach (['Semua', 'Buah Lokal', 'Buah Impor'] as $c)
                        <button
                            type="submit"
                            @click="activeCategory = '{{ $c }}'"
                            class="flex-shrink-0 px-4 py-2 rounded-xl text-xs font-semibold transition-all border cursor-pointer select-none"
                            :class="activeCategory === '{{ $c }}' ? 'bg-primary text-white border-primary shadow-soft' : 'bg-white text-gray-muted border-gray-light hover:border-primary hover:text-primary'"
                        >
                            {{ $c }}
                        </button>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    @if(count($products) === 0)
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-gray-light p-6 shadow-soft">
            <span class="material-symbols-rounded text-gray-muted text-5xl mb-4">search_off</span>
            <p class="text-lg font-semibold text-gray-dark">Produk Tidak Ditemukan</p>
            <p class="text-sm text-gray-muted mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $p)
                @php $isOutOfStock = ($p['stock'] <= 0); @endphp
                <div class="card-3d overflow-hidden group flex flex-col justify-between rounded-2xl {{ $isOutOfStock ? 'opacity-80 bg-gray-50/50' : '' }}">
                    @if($isOutOfStock)
                        <div class="block cursor-not-allowed select-none">
                            <div class="relative bg-white overflow-hidden aspect-square p-4 flex items-center justify-center border-b border-bg-light">
                                <img
                                    src="{{ \App\Http\Controllers\ProductData::img($p['img'], 400, 400) }}"
                                    alt="{{ $p['name'] }}"
                                    class="max-w-full max-h-full object-contain filter grayscale opacity-50"
                                />
                                <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-xl text-[10px] font-bold bg-red-100 text-red-600 border border-red-200 shadow-sm">
                                        Stok Habis
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('product.detail', $p['id']) }}" class="block">
                            <div class="relative bg-white overflow-hidden aspect-square p-4 flex items-center justify-center border-b border-bg-light">
                                <img
                                    src="{{ \App\Http\Controllers\ProductData::img($p['img'], 400, 400) }}"
                                    alt="{{ $p['name'] }}"
                                    class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-300"
                                />
                                <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-xl text-[10px] font-bold bg-green-light text-primary border border-primary/10 shadow-sm">
                                        Stok: {{ $p->stock }} {{ $p->unit }}
                                    </span>
                                    @if($p['stock'] <= 50)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-xl text-[10px] font-bold {{ $p['stock'] <= 20 ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600' }}">
                                            {{ $p['stock'] <= 20 ? 'Hampir Habis' : 'Stok Terbatas' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif

                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div>
                            <p class="text-xs text-gray-muted font-medium uppercase tracking-wider mb-1">{{ $p->category->name ?? 'Buah' }}</p>
                            @if($isOutOfStock)
                                <h3 class="font-bold text-gray-400 text-sm leading-tight mb-2 h-10 overflow-hidden cursor-not-allowed select-none">{{ $p['name'] }}</h3>
                            @else
                                <a href="{{ route('product.detail', $p['id']) }}" class="hover:text-primary transition-colors">
                                    <h3 class="font-bold text-gray-dark text-sm leading-tight mb-2 h-10 overflow-hidden">{{ $p['name'] }}</h3>
                                </a>
                            @endif
                            <div class="flex items-center gap-1 mb-3">
                                <span class="text-xs text-gray-muted">{{ $p['sold'] }} terjual</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between border-t border-bg-light pt-3">
                            <div>
                                <p class="font-extrabold {{ $isOutOfStock ? 'text-gray-400' : 'text-primary' }} text-base leading-none">{{ \App\Http\Controllers\ProductData::rp($p['price']) }}</p>
                                <p class="text-[10px] text-gray-muted mt-0.5">per {{ $p['unit'] }}</p>
                            </div>
                            @if($isOutOfStock)
                                <button type="button" disabled class="inline-flex items-center justify-center gap-1.5 font-bold rounded-xl bg-gray-200 text-gray-400 px-3.5 py-2.5 text-xs cursor-not-allowed select-none border border-gray-300">
                                    Stok Habis <span class="material-symbols-rounded text-sm">block</span>
                                </button>
                            @else
                                <a href="{{ route('product.detail', $p['id']) }}" class="inline-flex items-center justify-center gap-1.5 font-bold rounded-xl bg-green-light text-primary hover:bg-primary hover:text-white px-3.5 py-2.5 text-xs transition-all duration-300">
                                    Detail <span class="material-symbols-rounded text-sm">visibility</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
