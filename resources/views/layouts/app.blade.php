<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Syila Buah - Buah Segar Berkualitas')</title>
    
    <!-- Google Fonts: Poppins & Material Symbols Rounded -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- Styles & Scripts via Vite & Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4CAF50',
                        'primary-hover': '#43A047',
                        'primary-active': '#388E3C',
                        accent: '#FF9800',
                        'accent-hover': '#F57C00',
                        'gray-dark': '#2D3748',
                        'gray-muted': '#718096',
                        'gray-light': '#E2E8F0',
                        'bg-light': '#F8F9FA',
                        'green-light': '#E8F5E9',
                        'green-dark': '#1B5E20'
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    boxShadow: {
                        soft: '0 8px 30px rgba(0, 0, 0, 0.04)',
                        'soft-hover': '0 20px 50px rgba(76, 175, 80, 0.12)',
                        '3d': '0 15px 35px rgba(0, 0, 0, 0.05), 0 5px 15px rgba(0, 0, 0, 0.02)'
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Smooth Page Transition Styles -->
    <style>
        @keyframes calmPageEnter {
            0% {
                opacity: 0;
                pointer-events: none;
            }
            85% {
                opacity: 0.95;
                pointer-events: none;
            }
            100% {
                opacity: 1;
                pointer-events: auto;
            }
        }

        .page-animate {
            animation: calmPageEnter 0.65s cubic-bezier(0.25, 1, 0.5, 1) forwards;
            will-change: opacity;
        }

        html { scroll-behavior: smooth; }

        a, button {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body class="bg-bg-light text-gray-dark font-sans min-h-screen flex flex-col antialiased">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-light shadow-soft transition-all duration-300" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">            <!-- Logo -->
            <a href="{{ (session('role') === 'pelanggan' || session('role') === 'customer') ? route('home') : route('landing') }}">
                @include('partials.logo')
            </a>

            <!-- Links desktop -->
            <div class="hidden md:flex items-center gap-6">
                @if(request()->routeIs('landing'))
                    <a href="#tentang-kami" class="text-sm text-gray-muted hover:text-primary font-medium transition-colors">Tentang Kami</a>
                    <a href="#kontak" class="text-sm text-gray-muted hover:text-primary font-medium transition-colors">Kontak</a>
                @elseif(session('role') === 'pelanggan' || session('role') === 'customer')
                    <a href="{{ route('home') }}" class="text-sm {{ request()->routeIs('home') ? 'text-primary font-semibold' : 'text-gray-muted hover:text-primary font-medium' }} transition-colors">Beranda</a>
                    <a href="{{ route('catalog') }}" class="text-sm {{ request()->routeIs('catalog') ? 'text-primary font-semibold' : 'text-gray-muted hover:text-primary font-medium' }} transition-colors">Produk</a>
                    <a href="{{ route('history') }}" class="text-sm {{ request()->routeIs('history') ? 'text-primary font-semibold' : 'text-gray-muted hover:text-primary font-medium' }} transition-colors">Riwayat Pesanan</a>
                    <a href="{{ route('profile') }}" class="text-sm {{ request()->routeIs('profile') ? 'text-primary font-semibold' : 'text-gray-muted hover:text-primary font-medium' }} transition-colors">Profil</a>
                @else
                    <a href="{{ route('landing') }}" class="text-sm text-gray-muted hover:text-primary font-medium transition-colors">Beranda</a>
                    <a href="{{ route('catalog') }}" class="text-sm {{ request()->routeIs('catalog') ? 'text-primary font-semibold' : 'text-gray-muted hover:text-primary font-medium' }} transition-colors">Produk</a>
                @endif
            </div>

            <!-- Right Actions -->
            <div class="hidden md:flex items-center gap-4">
                @if(request()->routeIs('landing'))
                    @if(Illuminate\Support\Facades\Auth::check() || session('username') || session('role'))
                        @php
                            $userRole = session('role') ?? (Illuminate\Support\Facades\Auth::check() ? Illuminate\Support\Facades\Auth::user()->role : 'pelanggan');
                            $homeRoute = ($userRole === 'admin') ? route('admin.dashboard') : route('home');
                        @endphp
                        <a href="{{ $homeRoute }}" class="text-sm text-primary font-bold hover:text-primary-hover transition-colors">Beranda</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-muted hover:text-primary font-semibold transition-colors">Login</a>
                    @endif
                    <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover active:bg-primary-active px-5 py-2.5 text-sm shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 transition-all duration-300">
                        Lihat Produk
                    </a>
                @elseif(session('role') === 'pelanggan' || session('role') === 'customer')
                    <!-- Logged in actions -->
                    <a href="{{ route('cart') }}" class="relative w-10 h-10 rounded-xl flex items-center justify-center hover:bg-green-light text-gray-dark transition-all duration-300 hover:shadow-soft">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        @php
                            $cartCount = collect(session('cart', []))->sum('qty');
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-5 h-5 rounded-full bg-accent text-white text-[10px] flex items-center justify-center font-bold animate-pulse">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile') }}" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-green-light text-gray-dark transition-all duration-300 hover:shadow-soft">
                        <span class="material-symbols-rounded">person</span>
                    </a>
                    <a href="{{ route('logout') }}" class="text-sm text-red-500 font-semibold hover:text-red-700 transition-colors">Keluar</a>
                @else
                    <!-- Guest actions -->
                    <a href="{{ route('login') }}" class="text-sm text-gray-muted hover:text-primary font-semibold transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover active:bg-primary-active px-5 py-2.5 text-sm shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 transition-all duration-300">
                        Daftar
                    </a>
                @endif
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center gap-2 md:hidden">
                @if(!request()->routeIs('landing') && (session('role') === 'pelanggan' || session('role') === 'customer'))
                    <a href="{{ route('cart') }}" class="relative w-10 h-10 rounded-xl flex items-center justify-center hover:bg-green-light text-gray-dark transition-colors">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        @if(collect(session('cart', []))->sum('qty') > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-5 h-5 rounded-full bg-accent text-white text-[10px] flex items-center justify-center font-bold">{{ collect(session('cart', []))->sum('qty') }}</span>
                        @endif
                    </a>
                @endif
                <button @click="mobileOpen = !mobileOpen" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-rounded" x-text="mobileOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 -translate-y-4" class="md:hidden bg-white border-t border-gray-light px-6 py-4 space-y-2">
            @if(request()->routeIs('landing'))
                <a href="#tentang-kami" @click="mobileOpen = false" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Tentang Kami</a>
                <a href="#kontak" @click="mobileOpen = false" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Kontak</a>
                <div class="pt-4 border-t border-gray-light flex flex-col gap-2">
                    @if(Illuminate\Support\Facades\Auth::check() || session('username') || session('role'))
                        @php
                            $userRole = session('role') ?? (Illuminate\Support\Facades\Auth::check() ? Illuminate\Support\Facades\Auth::user()->role : 'pelanggan');
                            $homeRoute = ($userRole === 'admin') ? route('admin.dashboard') : route('home');
                        @endphp
                        <a href="{{ $homeRoute }}" class="block py-2 text-center text-sm text-primary font-bold">Beranda</a>
                    @else
                        <a href="{{ route('login') }}" class="block py-2 text-center text-sm text-gray-muted hover:text-primary font-semibold">Login</a>
                    @endif
                    <a href="{{ route('catalog') }}" class="block py-2.5 text-center text-sm font-semibold rounded-xl bg-primary text-white">Lihat Produk</a>
                </div>
            @elseif(session('role') === 'pelanggan' || session('role') === 'customer')
                <a href="{{ route('home') }}" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Beranda</a>
                <a href="{{ route('catalog') }}" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Produk</a>
                <a href="{{ route('history') }}" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Riwayat Pesanan</a>
                <a href="{{ route('profile') }}" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Profil</a>
                <a href="{{ route('logout') }}" class="block py-2 text-sm text-red-500 font-semibold">Keluar</a>
            @else
                <a href="{{ route('landing') }}" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Beranda</a>
                <a href="{{ route('catalog') }}" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Produk</a>
                <a href="{{ route('landing') }}#tentang-kami" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Tentang Kami</a>
                <a href="{{ route('landing') }}#kontak" class="block py-2 text-sm text-gray-muted hover:text-primary font-medium">Kontak</a>
                <div class="pt-4 border-t border-gray-light flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="block py-2 text-center text-sm text-gray-muted hover:text-primary font-semibold">Masuk</a>
                    <a href="{{ route('register') }}" class="block py-2.5 text-center text-sm font-semibold rounded-xl bg-primary text-white">Daftar</a>
                </div>
            @endif
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow page-animate">
        <!-- Flash Session Notifications & Stock Alerts -->
        <div class="max-w-7xl mx-auto px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-sm font-semibold flex items-center justify-between shadow-soft">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-rounded text-lg text-green-600">check_circle</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold flex items-center justify-between shadow-soft">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-rounded text-lg text-red-600">error</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if(session('warning'))
                <div class="mb-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-semibold flex items-center justify-between shadow-soft">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-rounded text-lg text-amber-600">warning</span>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold space-y-1 shadow-soft">
                    <div class="flex items-center gap-2.5 font-bold mb-1">
                        <span class="material-symbols-rounded text-lg text-red-600">error</span>
                        <span>Terjadi Kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs pl-7 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-light text-gray-dark mt-16 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="space-y-4">
                @include('partials.logo')
                <p class="text-sm text-gray-muted leading-relaxed">Buah segar berkualitas premium, langsung dari kebun terpilih ke meja makan Anda.</p>
            </div>
            
            <div>
                <h4 class="font-bold text-sm text-gray-dark mb-4">Informasi</h4>
                <ul class="space-y-2 text-sm text-gray-muted">
                    <li>Pontianak, Kalimantan Barat</li>
                    <li>Jam Operasional: 08.00 - 21.00 WIB</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-bold text-sm text-gray-dark mb-4">Kontak Kami</h4>
                <ul class="space-y-2 text-sm text-gray-muted">
                    <li class="flex items-center gap-2"><span class="material-symbols-rounded text-primary text-sm">phone</span> 0812-3456-7890</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-rounded text-primary text-sm">mail</span> hello@syilabuah.id</li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-sm text-gray-dark mb-4">Media Sosial</h4>
                <div class="flex gap-3">
                    @foreach (['facebook', 'instagram', 'twitter', 'youtube'] as $s)
                        <button class="w-10 h-10 rounded-xl bg-bg-light hover:bg-primary hover:text-white transition-all duration-300 flex items-center justify-center text-gray-muted hover:shadow-soft cursor-pointer">
                            <span class="material-symbols-rounded text-base">public</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-t border-gray-light py-6 bg-bg-light">
            <p class="text-center text-xs text-gray-muted">© 2025 Syila Buah. Semua hak dilindungi undang-undang.</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('nav a, footer a').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (!href) return;
                    const isExternal = href.startsWith('http') && !href.includes(window.location.hostname);
                    const isBlank = this.getAttribute('target') === '_blank';
                    const isSpecial = href.startsWith('#') || href.startsWith('javascript:');

                    if (!isExternal && !isBlank && !isSpecial && href !== '#') {
                        const mainContent = document.querySelector('main');
                        if (mainContent) {
                            mainContent.style.opacity = '0.7';
                            mainContent.style.transition = 'opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
                        }
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
