<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Syila Buah</title>
    
    <!-- Fonts: Poppins & Material Symbols Rounded -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-bg-light text-gray-dark font-sans min-h-screen">
    <div class="min-h-screen flex" x-data="{ remember: false, showPw: false }">
        <!-- Left panel -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <img
                src="https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=900&h=1080&fit=crop&auto=format"
                alt="Buah segar berkualitas"
                class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-700 ease-out"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-green-dark/85 to-primary/40"></div>
            <div class="relative z-10 flex flex-col justify-end p-12 text-white h-full w-full">
                <div class="flex items-center gap-3 mb-8 animate-float">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                        <span class="material-symbols-rounded text-white">eco</span>
                    </div>
                    <span class="font-bold text-2xl tracking-tight">SyilaBuah</span>
                </div>
                <h1 class="text-4xl font-bold leading-tight mb-4 tracking-tight drop-shadow-md">Buah Segar Berkualitas<br />Setiap Hari</h1>
                <p class="text-white/80 text-base leading-relaxed max-w-md drop-shadow-sm">Dapatkan buah pilihan terbaik langsung dari kebun segar, diantarkan ke pintu rumah Anda.</p>
                <div class="flex gap-6 mt-8 border-t border-white/10 pt-8">
                    @foreach([
                        ['label' => 'Produk', 'value' => '12+'],
                        ['label' => 'Pelanggan', 'value' => '2.4K+'],
                        ['label' => 'Rating', 'value' => '4.9 ★']
                    ] as $s)
                        <div class="text-left">
                            <p class="font-bold text-2xl tracking-tight text-accent">{{ $s['value'] }}</p>
                            <p class="text-white/70 text-xs font-medium">{{ $s['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right panel -->
        <div class="flex-grow flex items-center justify-center bg-bg-light px-6 py-12">
            <div class="w-full max-w-md">
                <!-- Logo mobile -->
                <div class="lg:hidden flex items-center gap-2 mb-8 justify-center">
                    <div class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center shadow-md">
                        <span class="material-symbols-rounded text-white">eco</span>
                    </div>
                    <span class="font-bold text-xl text-gray-dark tracking-tight">Syila<span class="text-primary">Buah</span></span>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-8 hover:shadow-soft-hover transform hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center shadow-md">
                            <span class="material-symbols-rounded text-white text-base">eco</span>
                        </div>
                        <span class="font-bold text-lg leading-none tracking-tight">Syila<span class="text-primary">Buah</span></span>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-dark tracking-tight">Masuk ke Akun</h2>
                    <p class="text-gray-muted text-sm mb-8 mt-1">Masukkan email dan password Anda.</p>

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                        @csrf
                        <!-- Email Input -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-dark">Email</label>
                            <div class="relative group">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                    <span class="material-symbols-rounded text-lg">mail</span>
                                </div>
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    placeholder="contoh@email.com"
                                    class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                                />
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-dark">Password</label>
                            <div class="relative group">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                    <span class="material-symbols-rounded text-lg">lock</span>
                                </div>
                                <input
                                    :type="showPw ? 'text' : 'password'"
                                    name="password"
                                    required
                                    placeholder="Masukkan password"
                                    class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10 pr-12"
                                />
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <button type="button" @click="showPw = !showPw" class="text-gray-muted hover:text-primary transition-colors cursor-pointer">
                                        <span class="material-symbols-rounded text-lg" x-text="showPw ? 'visibility_off' : 'visibility'">visibility</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mt-4 mb-6">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <div
                                    @click="remember = !remember"
                                    class="w-5 h-5 rounded flex items-center justify-center border-2 transition-all duration-200"
                                    :class="remember ? 'bg-primary border-primary shadow-soft' : 'border-gray-light'"
                                >
                                    <span class="material-symbols-rounded text-white text-sm" x-show="remember">check</span>
                                </div>
                                <input type="hidden" name="remember" :value="remember ? 1 : 0">
                                <span class="text-sm text-gray-muted font-medium">Ingat Saya</span>
                            </label>
                            <button type="button" class="text-sm text-primary font-medium hover:underline">Lupa Password?</button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-300 select-none bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-6 py-3 text-base cursor-pointer shadow-soft hover:shadow-soft-hover"
                            >
                                Masuk
                            </button>
                            
                            <button
                                type="submit"
                                name="login_admin"
                                value="1"
                                class="w-full inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-300 select-none border-2 border-primary text-primary hover:bg-green-light px-6 py-3 text-base cursor-pointer hover:shadow-soft"
                            >
                                Masuk sebagai Admin
                            </button>

                            <div class="text-center pt-2">
                                <span class="text-sm text-gray-muted font-medium">Belum punya akun? </span>
                                <a href="{{ route('register') }}" class="text-sm text-primary font-bold hover:underline">Daftar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
