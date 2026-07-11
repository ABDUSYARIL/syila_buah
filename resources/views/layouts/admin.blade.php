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
    
    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-bg-light text-gray-dark font-sans min-h-screen antialiased" x-data="{ collapsed: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside :class="collapsed ? 'w-16' : 'w-64'" class="bg-green-dark text-white flex flex-col transition-all duration-300 flex-shrink-0 min-h-screen shadow-lg">
            <!-- Header -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-white/10">
                <div class="flex items-center gap-2" x-show="!collapsed" x-transition.fade>
                    <div class="w-7 h-7 rounded-lg bg-primary flex items-center justify-center shadow-md">
                        <span class="material-symbols-rounded text-white text-base">eco</span>
                    </div>
                    <span class="font-bold text-sm leading-none tracking-tight">Syila<span class="text-green-200">Buah</span></span>
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
                        ['route' => 'admin.admins', 'icon' => 'manage_accounts', 'label' => 'Kelola Admin'],
                        ['route' => 'admin.reports', 'icon' => 'trending_up', 'label' => 'Laporan'],
                        ['route' => 'admin.profile', 'icon' => 'account_circle', 'label' => 'Profil'],
                    ];
                    $currentRoute = request()->route()->getName();
                @endphp
                @foreach($menuItems as $item)
                    @php
                        $isActive = ($currentRoute === $item['route'] || str_starts_with($currentRoute, $item['route'] . '.'));
                    @endphp
                    <a href="{{ route($item['route']) }}" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all {{ $isActive ? 'bg-primary text-white border-r-4 border-accent' : 'text-green-100 hover:bg-white/10' }}">
                        <span class="flex-shrink-0"><span class="material-symbols-rounded text-xl">{{ $item['icon'] }}</span></span>
                        <span x-show="!collapsed" x-transition.fade>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            
            <!-- Logout -->
            <div class="p-4 border-t border-white/10">
                <a href="{{ route('login') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-green-100 hover:bg-white/10 transition-all">
                    <span class="material-symbols-rounded text-xl">logout</span>
                    <span x-show="!collapsed" x-transition.fade>Keluar</span>
                </a>
            </div>
        </aside>
        
        <!-- Main content area -->
        <div class="flex-grow min-w-0 flex flex-col">
            <!-- Top bar -->
            <header class="h-16 bg-white border-b border-gray-light flex items-center justify-between px-6 flex-shrink-0 shadow-soft">
                <div>
                    <p class="font-semibold text-gray-dark text-sm">Selamat datang, Admin!</p>
                    <p class="text-xs text-gray-muted" id="current-date"></p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="relative w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors">
                        <span class="material-symbols-rounded text-gray-muted text-lg">notifications</span>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-accent animate-ping"></span>
                    </button>
                    <div class="w-9 h-9 rounded-xl bg-green-light flex items-center justify-center">
                        <span class="material-symbols-rounded text-primary">account_circle</span>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Grid -->
            <main class="flex-grow p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script to format date -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const today  = new Date();
            document.getElementById('current-date').textContent = today.toLocaleDateString('id-ID', options);
        });
    </script>
    @yield('scripts')
</body>
</html>
