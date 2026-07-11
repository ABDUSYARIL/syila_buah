<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Syila Buah</title>
    
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
<body class="bg-bg-light text-gray-dark font-sans min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg" x-data="{ terms: false, showPw: false }">
        <!-- Logo -->
        <div class="flex items-center gap-2 mb-6 justify-center">
            <div class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center shadow-md animate-float">
                <span class="material-symbols-rounded text-white text-base">eco</span>
            </div>
            <span class="font-bold text-xl text-gray-dark tracking-tight">Syila<span class="text-primary">Buah</span></span>
        </div>
        
        <!-- Registration Card -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-8 hover:shadow-soft-hover transform hover:-translate-y-1 transition-all duration-300">
            <h2 class="text-2xl font-bold text-gray-dark tracking-tight">Buat Akun</h2>
            <p class="text-gray-muted text-sm mb-6 mt-1">Isi data diri Anda untuk mulai berbelanja</p>
            
            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Nama Lengkap -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Nama Lengkap</label>
                    <div class="relative group">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-rounded text-lg">person</span>
                        </div>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="Masukkan nama lengkap"
                            class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                        />
                    </div>
                </div>

                <!-- Email -->
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

                <!-- Nomor HP -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Nomor HP</label>
                    <div class="relative group">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-rounded text-lg">phone</span>
                        </div>
                        <input
                            type="tel"
                            name="phone"
                            required
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                        />
                    </div>
                </div>

                <!-- Alamat Lengkap -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Alamat Lengkap</label>
                    <div class="relative group">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-rounded text-lg">map_pin</span>
                        </div>
                        <input
                            type="text"
                            name="address"
                            required
                            placeholder="Jl. Contoh No. 1, Kota, Provinsi"
                            class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                        />
                    </div>
                </div>

                <!-- Password -->
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
                            placeholder="Min. 8 karakter"
                            class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10 pr-12"
                        />
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <button type="button" @click="showPw = !showPw" class="text-gray-muted hover:text-primary transition-colors cursor-pointer">
                                <span class="material-symbols-rounded text-lg" x-text="showPw ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Konfirmasi Password</label>
                    <div class="relative group">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-rounded text-lg">lock</span>
                        </div>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            placeholder="Ulangi password"
                            class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                        />
                    </div>
                </div>

                <!-- Checkbox Syarat & Ketentuan -->
                <div class="flex items-start gap-2.5 mt-4 mb-6">
                    <div
                        @click="terms = !terms"
                        class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200 cursor-pointer select-none"
                        :class="terms ? 'bg-primary border-primary shadow-soft' : 'border-gray-light'"
                    >
                        <span class="material-symbols-rounded text-white text-sm" x-show="terms">check</span>
                    </div>
                    <input type="hidden" name="terms" :value="terms ? 1 : 0">
                    <span class="text-sm text-gray-muted font-medium leading-none">Saya menyetujui <button type="button" class="text-primary font-bold hover:underline">syarat dan ketentuan</button>.</span>
                </div>

                <!-- Submit Button -->
                <div class="space-y-3 mt-6">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-300 select-none bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-6 py-3 text-base shadow-soft hover:shadow-soft-hover cursor-pointer"
                        :disabled="!terms"
                        :class="!terms ? 'opacity-50 cursor-not-allowed' : ''"
                    >
                        Daftar
                    </button>
                    <div class="text-center">
                        <span class="text-sm text-gray-muted font-medium">Sudah punya akun? </span>
                        <a href="{{ route('login') }}" class="text-sm text-primary font-bold hover:underline">Masuk</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
