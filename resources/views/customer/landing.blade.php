@extends('layouts.app')

@section('title', 'Syila Buah - Buah Segar Berkualitas')

@section('content')
<div class="space-y-24 pb-16">
    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 pt-8 md:pt-16 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-light text-primary">
                <span class="material-symbols-rounded text-sm">eco</span> Pilihan Terbaik
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-dark leading-tight tracking-tight">
                Buah Segar Berkualitas untuk Kebutuhan Sehari-hari
            </h1>
            <p class="text-gray-muted text-base md:text-lg leading-relaxed max-w-md">
                Tersedia berbagai pilihan buah lokal dan impor dengan kualitas terbaik yang dapat dipesan secara online.
            </p>
            <div class="pt-2">
                <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 font-bold rounded-xl bg-primary text-white btn-3d px-6 py-3 text-base">
                    Lihat Produk <span class="material-symbols-rounded text-lg">arrow_forward</span>
                </a>
            </div>
        </div>
        <div class="flex justify-center relative">
            <div class="absolute -inset-4 bg-primary/5 rounded-full blur-3xl opacity-30"></div>
            <div class="w-full max-w-md aspect-square glass-premium rounded-3xl p-6 shadow-3d hover:shadow-3d-hover transform hover:scale-[1.03] transition-all duration-500 animate-float">
                <img
                    src="https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=600&h=600&fit=crop&auto=format"
                    alt="Keranjang Buah Segar"
                    class="w-full h-full object-contain rounded-2xl"
                />
            </div>
        </div>
    </section>

    <!-- Deskripsi Singkat & Keunggulan -->
    <section class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-light text-primary">
                <span class="material-symbols-rounded text-sm">store</span> Tentang Syila Buah
            </span>
            <h2 class="text-3xl font-extrabold text-gray-dark tracking-tight text-gradient-green">Menyediakan Kesegaran Terbaik</h2>
            <p class="text-sm text-gray-muted leading-relaxed">
                Syila Buah adalah toko buah segar premium terpercaya. Kami mendedikasikan diri untuk menyediakan buah-buahan lokal dan impor berkualitas unggul dengan sistem penyimpanan khusus untuk mempertahankan nutrisi buah tetap utuh sampai ke tangan Anda.
            </p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['title' => 'Buah Segar', 'desc' => 'Buah dipilih langsung agar kualitas tetap terjaga.', 'icon' => 'apple', 'color' => 'bg-green-light text-primary'],
                ['title' => 'Pengiriman Cepat', 'desc' => 'Pesanan diproses dengan cepat sesuai alamat tujuan.', 'icon' => 'local_shipping', 'color' => 'bg-[#FFF3E0] text-accent'],
                ['title' => 'Produk Berkualitas', 'desc' => 'Produk disimpan dengan standar kualitas yang baik.', 'icon' => 'inventory_2', 'color' => 'bg-blue-50 text-blue-600'],
                ['title' => 'Pemesanan Mudah', 'desc' => 'Belanja buah secara online kapan saja.', 'icon' => 'shopping_cart', 'color' => 'bg-[#F3E5F5] text-purple-600']
            ] as $k)
                <div class="card-3d p-6 flex flex-col items-center text-center rounded-2xl">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 {{ $k['color'] }} shadow-sm">
                        <span class="material-symbols-rounded text-2xl">{{ $k['icon'] }}</span>
                    </div>
                    <h3 class="font-bold text-gray-dark text-base">{{ $k['title'] }}</h3>
                    <p class="text-xs text-gray-muted leading-relaxed mt-2">{{ $k['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Informasi Alamat & Jam Operasional -->
    <section id="tentang-kami" class="max-w-7xl mx-auto px-6 border-t border-gray-light pt-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-light text-primary">
                        <span class="material-symbols-rounded text-sm">info</span> Informasi Toko
                    </span>
                    <h2 class="text-3xl font-extrabold text-gray-dark mt-3 tracking-tight">Kunjungi Outlet Kami</h2>
                </div>
                <p class="text-sm text-gray-muted leading-relaxed">
                    Rasakan pengalaman memilih buah segar secara langsung dengan mengunjungi outlet fisik Syila Buah. Staff kami siap membantu merekomendasikan pilihan buah terbaik untuk konsumsi harian maupun kebutuhan hampers Anda.
                </p>
                <div class="card-3d p-6 rounded-2xl">
                    <h4 class="font-bold text-gray-dark text-sm flex items-center gap-2">
                        <span class="material-symbols-rounded text-primary text-lg">schedule</span> Jam Operasional
                    </h4>
                    <p class="text-xs text-gray-muted mt-2 leading-relaxed">
                        Kami buka setiap hari untuk melayani kebutuhan buah segar Anda:
                        <br />
                        <span class="font-semibold text-gray-dark mt-1 block">Senin - Minggu: 08.00 - 21.00 WIB</span>
                    </p>
                </div>
            </div>
            
            <div id="kontak" class="card-3d p-8 rounded-3xl space-y-6">
                <h3 class="font-extrabold text-gray-dark text-lg border-b border-bg-light pb-4">Kontak & Lokasi</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-green-light flex items-center justify-center text-primary shadow-sm flex-shrink-0">
                            <span class="material-symbols-rounded">map</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-muted font-semibold">Alamat Lengkap</p>
                            <p class="text-sm font-semibold text-gray-dark mt-0.5">Jl. Melati No. 12, Cicendo, Kota Bandung, Jawa Barat</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-green-light flex items-center justify-center text-primary shadow-sm flex-shrink-0">
                            <span class="material-symbols-rounded">support_agent</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-muted font-semibold">Hubungi Kami (WhatsApp)</p>
                            <p class="text-sm font-semibold text-primary mt-0.5">0812-3456-7890</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-green-light flex items-center justify-center text-primary shadow-sm flex-shrink-0">
                            <span class="material-symbols-rounded">mail</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-muted font-semibold">Email Resmi</p>
                            <p class="text-sm font-semibold text-gray-dark mt-0.5">hello@syilabuah.id</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
