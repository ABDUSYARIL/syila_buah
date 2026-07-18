<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Syila Buah</title>
    
    <!-- Fonts: Mengimpor font Poppins dan Material Symbols untuk ikon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <!-- Aset CSS dan JS Laravel Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS: Digunakan untuk logika interaktif modal, checkbox, dan visibilitas password -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-bg-light text-gray-dark font-sans min-h-screen">
    <!-- Kontainer utama dengan flexbox untuk membagi layar menjadi 2 panel di desktop -->
    <div class="min-h-screen flex" x-data="{ terms: false, showPw: false, showConfirmPw: false, showTermsModal: false }">
        
        <!-- PANEL KIRI (Khusus layar besar/desktop): Ilustrasi buah segar premium dan branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Gambar latar belakang buah-buahan segar warna-warni -->
            <img
                src="https://images.unsplash.com/photo-1619546813926-a78fa6372cd2?w=900&h=1080&fit=crop&auto=format"
                alt="Buah segar warna-warni"
                class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-700 ease-out"
            />
            <!-- Overlay gradien hijau premium agar teks tetap terbaca dengan jelas -->
            <div class="absolute inset-0 bg-gradient-to-br from-green-dark/90 to-primary/50"></div>
            <!-- Konten teks promosi di atas panel kiri -->
            <div class="relative z-10 flex flex-col justify-end p-12 text-white h-full w-full">
                <div class="mb-8 animate-float">
                    @include('partials.logo', ['textColor' => 'text-white', 'spanColor' => 'text-white'])
                </div>
                <h1 class="text-4xl font-bold leading-tight mb-4 tracking-tight drop-shadow-md">Bergabunglah dengan Kami<br />Nikmati Buah Terbaik</h1>
                <p class="text-white/80 text-base leading-relaxed max-w-md drop-shadow-sm">Buat akun untuk memesan buah segar terpilih langsung dari petani lokal secara cepat, aman, dan bergaransi.</p>
                <!-- Statistik/Keunggulan Toko Syila Buah -->
                <div class="flex gap-6 mt-8 border-t border-white/10 pt-8">
                    @foreach([
                        ['label' => 'Kesegaran', 'value' => '100%'],
                        ['label' => 'Kurir Cepat', 'value' => '1-6 Jam'],
                        ['label' => 'Garansi', 'value' => 'Uang Kembali']
                    ] as $s)
                        <div class="text-left">
                            <p class="font-bold text-2xl tracking-tight text-accent">{{ $s['value'] }}</p>
                            <p class="text-white/70 text-xs font-medium">{{ $s['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- PANEL KANAN: Form Pendaftaran dengan layout grid agar rapi dan berwarna -->
        <div class="flex-grow flex items-center justify-center bg-bg-light px-6 py-12">
            <div class="w-full max-w-2xl">
                <!-- Logo versi mobile (hanya muncul saat layar kecil) -->
                <div class="lg:hidden mb-6 flex justify-center">
                    @include('partials.logo')
                </div>

                <!-- Kartu Form Pendaftaran -->
                <div class="bg-white rounded-3xl shadow-soft border-t-8 border-primary p-8 hover:shadow-soft-hover transition-all duration-300">
                    <div class="mb-4">
                        @include('partials.logo')
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-dark tracking-tight">Buat Akun Baru</h2>
                    <p class="text-gray-muted text-sm mb-6 mt-1">Daftarkan diri Anda untuk mulai menikmati buah berkualitas tinggi.</p>

                    <!-- Menampilkan Error Validasi dari Backend -->
                    @if($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Input Pendaftaran -->
                    <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Grid 2 Kolom untuk input data agar lebih teratur di layar desktop -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Input Nama Lengkap -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-dark">Nama Lengkap</label>
                                <div class="relative group">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                        <span class="material-symbols-rounded text-lg">person</span>
                                    </div>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        placeholder="Masukkan nama lengkap"
                                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                                    />
                                </div>
                            </div>

                            <!-- Input Email -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-dark">Email</label>
                                <div class="relative group">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                        <span class="material-symbols-rounded text-lg">mail</span>
                                    </div>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        placeholder="contoh@email.com"
                                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                                    />
                                </div>
                            </div>

                            <!-- Input Nomor HP -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-dark">Nomor HP</label>
                                <div class="relative group">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                        <span class="material-symbols-rounded text-lg">phone</span>
                                    </div>
                                    <input
                                        type="tel"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        required
                                        placeholder="08xxxxxxxxxx"
                                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                                    />
                                </div>
                            </div>

                            <!-- Input Alamat Lengkap -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-dark">Alamat Lengkap</label>
                                <div class="relative group">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                        <span class="material-symbols-rounded text-lg">map</span>
                                    </div>
                                    <input
                                        type="text"
                                        name="address"
                                        value="{{ old('address') }}"
                                        required
                                        placeholder="Jl. Melati No. 12, Kota Bandung"
                                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10"
                                    />
                                </div>
                            </div>

                            <!-- Input Password -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-dark">Password</label>
                                <div class="relative group">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                        <span class="material-symbols-rounded text-lg">lock</span>
                                    </div>
                                    <input
                                        :type="showPw ? 'text' : 'password'"
                                        name="password"
                                        required
                                        placeholder="Min. 8 karakter"
                                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10 pr-12"
                                    />
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <button type="button" @click="showPw = !showPw" class="text-gray-muted hover:text-primary transition-colors cursor-pointer">
                                            <span class="material-symbols-rounded text-lg" x-text="showPw ? 'visibility_off' : 'visibility'">visibility</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Konfirmasi Password -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-dark">Konfirmasi Password</label>
                                <div class="relative group">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                                        <span class="material-symbols-rounded text-lg">lock_clock</span>
                                    </div>
                                    <input
                                        :type="showConfirmPw ? 'text' : 'password'"
                                        name="password_confirmation"
                                        required
                                        placeholder="Masukkan ulang password"
                                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10 pr-12"
                                    />
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <button type="button" @click="showConfirmPw = !showConfirmPw" class="text-gray-muted hover:text-primary transition-colors cursor-pointer">
                                            <span class="material-symbols-rounded text-lg" x-text="showConfirmPw ? 'visibility_off' : 'visibility'">visibility</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Persetujuan Syarat & Ketentuan -->
                        <div class="flex items-start gap-2.5 mt-4 mb-4">
                            <div
                                @click="terms = !terms"
                                class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200 cursor-pointer select-none"
                                :class="terms ? 'bg-primary border-primary shadow-soft' : 'border-gray-light'"
                            >
                                <span class="material-symbols-rounded text-white text-sm" x-show="terms">check</span>
                            </div>
                            <input type="hidden" name="terms" :value="terms ? 1 : 0">
                            <span class="text-xs text-gray-muted font-medium leading-normal">
                                Saya menyetujui <button type="button" @click="showTermsModal = true" class="text-primary font-bold hover:underline cursor-pointer">syarat dan ketentuan</button> yang berlaku di Syila Buah.
                            </span>
                        </div>

                        <!-- Tombol Submit Pendaftaran dan Link Kembali ke Login -->
                        <div class="space-y-3 pt-2">
                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-300 select-none bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-6 py-3 text-base shadow-soft hover:shadow-soft-hover cursor-pointer"
                                :disabled="!terms"
                                :class="!terms ? 'opacity-50 cursor-not-allowed' : ''"
                            >
                                Daftar Akun
                            </button>
                            <div class="text-center">
                                <span class="text-sm text-gray-muted font-medium">Sudah punya akun? </span>
                                <a href="{{ route('login') }}" class="text-sm text-primary font-bold hover:underline">Masuk</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL INTERAKTIF: Syarat & Ketentuan (Muncul saat tombol syarat & ketentuan diklik) -->
        <div x-show="showTermsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-transition x-cloak>
            <div @click.away="showTermsModal = false" class="bg-white rounded-3xl w-full max-w-lg p-6 shadow-2xl relative border-t-8 border-primary animate-float">
                <!-- Tombol Close Modal -->
                <button type="button" @click="showTermsModal = false" class="absolute top-4 right-4 text-gray-muted hover:text-gray-dark transition-colors cursor-pointer">
                    <span class="material-symbols-rounded text-xl">close</span>
                </button>
                <!-- Judul Modal -->
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-dark flex items-center gap-2">
                        <span class="material-symbols-rounded text-primary">gavel</span> Syarat & Ketentuan
                    </h3>
                    <p class="text-xs text-gray-muted mt-1">Harap baca syarat dan ketentuan layanan Syila Buah dengan seksama.</p>
                </div>
                <!-- Konten Teks Syarat & Ketentuan -->
                <div class="space-y-3.5 text-xs text-gray-muted overflow-y-auto max-h-80 pr-2 leading-relaxed">
                    <div>
                        <p class="font-bold text-gray-dark mb-1">1. Ketentuan Umum</p>
                        <p>Dengan mendaftarkan akun di platform Syila Buah, Anda menyatakan setuju dan tunduk pada seluruh peraturan operasional, kebijakan transaksi, dan pengiriman barang yang kami tetapkan.</p>
                    </div>
                    
                    <div>
                        <p class="font-bold text-gray-dark mb-1">2. Kualitas Buah & Kesegaran</p>
                        <p>Kami senantiasa menjamin bahwa buah yang dikirimkan dalam kondisi segar dan premium. Apabila terjadi komplain terkait kerusakan buah saat pengiriman, mohon segera lapor ke admin maksimal 1x24 jam setelah diterima.</p>
                    </div>
                    
                    <div>
                        <p class="font-bold text-gray-dark mb-1">3. Kebijakan Pembayaran & Pembatalan Otomatis</p>
                        <p>Pembayaran transaksi pemesanan buah dilakukan menggunakan QRIS atau Transfer Bank manual. Pembeli diberikan batas waktu pembayaran maksimal 15 menit. Jika melampaui batas waktu, sistem otomatis membatalkan pesanan.</p>
                    </div>
                    
                    <div>
                        <p class="font-bold text-gray-dark mb-1">4. Kebijakan Pengiriman</p>
                        <p>Pengiriman paket buah dengan opsi diantar kurir berlangsung dalam waktu 1-6 jam setelah status pembayaran diverifikasi. Untuk opsi ambil di tempat, pembeli dapat mengambil langsung di toko fisik kami.</p>
                    </div>
                    
                    <div>
                        <p class="font-bold text-gray-dark mb-1">5. Keamanan Data Pribadi</p>
                        <p>Seluruh informasi data diri Anda (Nama, Email, HP, dan Alamat) hanya dimanfaatkan untuk kemudahan bertransaksi di Syila Buah dan dilindungi dengan protokol privasi terbaik kami.</p>
                    </div>
                </div>
                <!-- Tombol Mengerti untuk Menutup Modal -->
                <div class="mt-6 flex justify-end">
                    <button type="button" @click="showTermsModal = false" class="px-5 py-2.5 rounded-xl bg-primary text-white hover:bg-primary-hover font-semibold text-xs transition-colors cursor-pointer shadow-soft">
                        Saya Mengerti & Setuju
                    </button>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
