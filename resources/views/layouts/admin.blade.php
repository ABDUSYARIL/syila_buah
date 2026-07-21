<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Syila Buah')</title>
    
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
    
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        html { 
            scroll-behavior: smooth; 
        }

        /* Smooth feedback pada elemen interaktif */
        a, button {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body class="bg-bg-light text-gray-dark font-sans min-h-screen antialiased" x-data="{ collapsed: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="collapsed ? 'w-16' : 'w-64'" class="bg-green-dark text-white flex flex-col transition-all duration-300 flex-shrink-0 min-h-screen shadow-lg">
            <!-- Header -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-white/10">
                <div x-show="!collapsed" x-transition.fade>
                    @include('partials.logo', ['textColor' => 'text-white', 'spanColor' => 'text-green-200'])
                </div>
                <button @click="collapsed = !collapsed" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10 transition-colors ml-auto cursor-pointer">
                    <span class="material-symbols-rounded text-sm" x-text="collapsed ? 'menu_open' : 'menu'">menu</span>
                </button>
            </div>
            
            <!-- Menu -->
            <nav class="flex-grow py-4 overflow-y-auto space-y-1">
                @php
                    $menuItems = [
                        ['route' => 'admin.dashboard', 'icon' => 'bar_chart', 'label' => 'Dashboard'],
                        ['route' => 'admin.products', 'icon' => 'inventory_2', 'label' => 'Manajemen Produk'],
                        ['route' => 'admin.stock', 'icon' => 'layers', 'label' => 'Manajemen Stok'],
                        ['route' => 'admin.orders', 'icon' => 'receipt_long', 'label' => 'Kelola Pesanan'],
                        ['route' => 'admin.users', 'icon' => 'manage_accounts', 'label' => 'Kelola User'],
                        ['route' => 'admin.reports', 'icon' => 'trending_up', 'label' => 'Laporan'],
                    ];
                    $currentRoute = request()->route() ? request()->route()->getName() : '';
                @endphp
                @foreach($menuItems as $item)
                    @php
                        $isActive = ($currentRoute === $item['route'] || str_starts_with($currentRoute, $item['route'] . '.'));
                        $routeUrl = Route::has($item['route']) ? route($item['route']) : url('admin/' . str_replace('admin.', '', $item['route']));
                    @endphp
                    <a href="{{ $routeUrl }}" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all {{ $isActive ? 'bg-primary text-white border-r-4 border-accent' : 'text-green-100 hover:bg-white/10' }}">
                        <span class="flex-shrink-0"><span class="material-symbols-rounded text-xl">{{ $item['icon'] }}</span></span>
                        <span x-show="!collapsed" x-transition.fade>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            
            <!-- Logout -->
            <div class="p-4 border-t border-white/10">
                <a href="{{ route('logout') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-green-100 hover:bg-white/10 transition-all">
                    <span class="material-symbols-rounded text-xl">logout</span>
                    <span x-show="!collapsed" x-transition.fade>Keluar</span>
                </a>
            </div>
        </aside>
        
        <!-- Main content area -->
        <div class="flex-grow min-w-0 flex flex-col">
            <!-- Top bar (Header Menu Atas Admin) -->
            <header class="h-16 bg-white border-b border-gray-light flex items-center justify-between px-6 flex-shrink-0 shadow-soft">
                <div>
                    <p class="font-semibold text-gray-dark text-sm">Selamat datang, {{ Auth::user()->name ?? 'Admin' }}!</p>
                    <p class="text-xs text-gray-muted" id="current-date"></p>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Menambahkan ikon pemberitahuan stok rendah yang terhubung langsung ke Manajemen Stok --}}
                    <div class="relative" x-data="{ notifOpen: false }">
                        @php
                            // Menghitung & mengambil produk riil yang stoknya di bawah batas minimal (50 unit)
                            $lowStockProducts = \App\Models\Product::where('stock', '<', 50)->orderBy('stock', 'asc')->get();
                            $lowStockCount = $lowStockProducts->count();
                        @endphp
                        
                        <!-- Lonceng Pemberitahuan Stok -->
                        <button 
                            type="button" 
                            @click="notifOpen = !notifOpen" 
                            class="relative w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 border shadow-sm cursor-pointer {{ $lowStockCount > 0 ? 'bg-red-50 text-red-500 hover:bg-red-100 border-red-200' : 'bg-bg-light text-gray-muted hover:text-gray-dark border-gray-light' }}" 
                            title="Peringatan Stok Menipis ({{ $lowStockCount }} Produk)"
                        >
                            <span class="material-symbols-rounded text-xl {{ $lowStockCount > 0 ? 'animate-bounce text-red-600' : '' }}">
                                {{ $lowStockCount > 0 ? 'notifications_active' : 'notifications' }}
                            </span>
                            @if($lowStockCount > 0)
                                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                    {{ $lowStockCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Dropdown List Produk Stok Menipis -->
                        <div 
                            x-show="notifOpen" 
                            @click.outside="notifOpen = false" 
                            x-transition:enter="transition ease-out duration-200" 
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                            x-transition:leave="transition ease-in duration-150" 
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2" 
                            class="absolute right-0 top-12 w-80 sm:w-96 bg-white rounded-2xl shadow-3d border border-gray-light z-50 p-4" 
                            style="display: none;"
                        >
                            <div class="flex items-center justify-between border-b border-bg-light pb-3 mb-3">
                                <h4 class="font-extrabold text-sm text-gray-dark flex items-center gap-1.5">
                                    <span class="material-symbols-rounded text-red-500 text-lg">warning</span> Peringatan Stok Menipis
                                </h4>
                                <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 border border-red-200">
                                    {{ $lowStockCount }} Produk
                                </span>
                            </div>

                            @if($lowStockCount > 0)
                                <div class="max-h-72 overflow-y-auto space-y-2 pr-1">
                                    @foreach($lowStockProducts as $lp)
                                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-bg-light/80 border border-gray-light hover:bg-orange-50/60 transition-colors">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <img src="{{ \App\Http\Controllers\ProductData::img($lp->image, 80, 80) }}" alt="" class="w-10 h-10 rounded-lg object-contain bg-white p-1 border border-gray-light flex-shrink-0" />
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-gray-dark truncate">{{ $lp->name }}</p>
                                                    <p class="text-[10px] font-semibold {{ $lp->stock <= 0 ? 'text-red-600 font-extrabold' : ($lp->stock <= 20 ? 'text-red-500 font-bold' : 'text-orange-600') }}">
                                                        {{ $lp->stock <= 0 ? 'Stok Habis (0 ' . $lp->unit . ')' : 'Sisa: ' . $lp->stock . ' ' . $lp->unit }}
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ route('admin.stock') }}" class="flex-shrink-0 px-3 py-1.5 rounded-lg bg-primary text-white text-[11px] font-bold hover:bg-primary-hover transition-colors shadow-sm">
                                                + Stok
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-8 text-center text-gray-muted">
                                    <span class="material-symbols-rounded text-green-500 text-3xl mb-1">check_circle</span>
                                    <p class="text-xs font-semibold text-gray-dark">Semua Stok Aman</p>
                                    <p class="text-[10px] text-gray-muted mt-0.5">Tidak ada produk dengan stok di bawah 50 unit.</p>
                                </div>
                            @endif

                            <div class="border-t border-bg-light pt-3 mt-3 text-center">
                                <a href="{{ route('admin.stock') }}" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                                    Kelola Manajemen Stok <span class="material-symbols-rounded text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- PROFIL DI KANAN ATAS (Profil Dropdown Header) -->
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button 
                            type="button" 
                            @click="profileOpen = !profileOpen" 
                            class="flex items-center gap-2.5 p-1.5 pr-3 rounded-xl hover:bg-bg-light transition-all border border-gray-light cursor-pointer"
                        >
                            @php
                                $authUser = Auth::user();
                                $avatarUrl = ($authUser && $authUser->avatar) ? asset('storage/' . $authUser->avatar) : null;
                            @endphp
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="" class="w-8 h-8 rounded-lg object-cover border border-primary/20" />
                            @else
                                <div class="w-8 h-8 rounded-lg bg-green-light text-primary font-extrabold flex items-center justify-center border border-primary/20 text-xs">
                                    {{ strtoupper(substr($authUser->name ?? 'Admin', 0, 2)) }}
                                </div>
                            @endif
                            <div class="text-left hidden sm:block">
                                <p class="text-xs font-bold text-gray-dark leading-tight truncate max-w-[120px]">{{ $authUser->name ?? 'Admin' }}</p>
                                <p class="text-[10px] text-gray-muted font-semibold capitalize">{{ ucfirst($authUser->role ?? 'admin') }}</p>
                            </div>
                            <span class="material-symbols-rounded text-gray-muted text-base transition-transform duration-200" :class="profileOpen ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <!-- Menu Dropdown Profil -->
                        <div 
                            x-show="profileOpen" 
                            @click.outside="profileOpen = false" 
                            x-transition:enter="transition ease-out duration-200" 
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                            x-transition:leave="transition ease-in duration-150" 
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2" 
                            class="absolute right-0 top-12 w-56 bg-white rounded-2xl shadow-3d border border-gray-light z-50 p-2 space-y-1" 
                            style="display: none;"
                        >
                            <div class="px-3 py-2 border-b border-bg-light mb-1">
                                <p class="text-xs font-extrabold text-gray-dark truncate">{{ $authUser->name ?? 'Admin' }}</p>
                                <p class="text-[10px] text-gray-muted truncate">{{ $authUser->email ?? 'admin@syilabuah.id' }}</p>
                            </div>
                            
                            <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-gray-dark rounded-xl hover:bg-bg-light transition-colors">
                                <span class="material-symbols-rounded text-primary text-base">account_circle</span>
                                Profil Saya
                            </a>

                            <a href="{{ route('admin.change-password') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-gray-dark rounded-xl hover:bg-bg-light transition-colors">
                                <span class="material-symbols-rounded text-accent text-base">lock</span>
                                Ganti Password
                            </a>

                            <div class="border-t border-bg-light my-1"></div>

                            <a href="{{ route('logout') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-red-600 rounded-xl hover:bg-red-50 transition-colors">
                                <span class="material-symbols-rounded text-red-500 text-base">logout</span>
                                Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Grid -->
            <main class="flex-grow p-6 overflow-y-auto page-animate">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script Format Tanggal & Smooth Page Transition -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const today  = new Date();
            const dateEl = document.getElementById('current-date');
            if (dateEl) dateEl.textContent = today.toLocaleDateString('id-ID', options);

            // Smooth feedback saat mengklik link navigasi internal
            document.querySelectorAll('aside a, header a').forEach(link => {
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
