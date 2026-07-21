@extends('layouts.admin')

@section('title', 'Kelola User - Admin Syila Buah')

@section('content')
<div x-data="{ 
    modalOpen: false, 
    inactivityModalOpen: false,
    editMode: false, 
    currentUserId: null, 
    userData: { name: '', email: '', phone: '', role: 'admin', status: 'aktif' },
    showPw1: false,
    showPw2: false
}">
    <!-- Judul Halaman dan Tombol Aksi Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-dark">Kelola User</h1>
            <p class="text-xs text-gray-muted mt-0.5">Manajemen Pengguna (Pelanggan) & Administrator Toko</p>
        </div>

        <div class="flex items-center gap-2">
            @if($roleFilter === 'customer')
                <!-- Tombol Pengaturan Inaktivitas & Hapus Akun Mati untuk Pelanggan -->
                <button type="button" 
                        @click="inactivityModalOpen = true" 
                        class="inline-flex items-center gap-2 font-bold rounded-xl transition-all duration-200 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white shadow-soft hover:shadow-soft-hover px-4 py-2.5 text-xs cursor-pointer">
                    <span class="material-symbols-rounded text-base">timer_off</span> 
                    Batas Inaktivitas & Hapus Akun Mati
                    @if($inactiveCustomerCount > 0)
                        <span class="bg-white/25 text-white text-[10px] font-black px-2 py-0.5 rounded-full">
                            {{ $inactiveCustomerCount }} Mati
                        </span>
                    @endif
                </button>
            @else
                <!-- Tombol Tambah Admin Baru (Admin HANYA bisa menambah Admin) -->
                <button type="button" 
                        @click="modalOpen = true; editMode = false; currentUserId = null; userData = { name: '', email: '', phone: '', role: 'admin', status: 'aktif' }; showPw1 = false; showPw2 = false" 
                        class="inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all duration-200 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white shadow-soft hover:shadow-soft-hover px-4 py-2.5 text-xs cursor-pointer">
                    <span class="material-symbols-rounded text-base">person_add</span> 
                    + Tambah Admin Baru
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-2xl bg-green-light border border-primary/20 text-primary font-semibold text-xs flex items-center gap-2 shadow-soft">
            <span class="material-symbols-rounded text-base">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Notifikasi Error -->
    @if(session('error'))
        <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 font-semibold text-xs flex items-center gap-2 shadow-soft">
            <span class="material-symbols-rounded text-base">cancel</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Tampilan Error Validasi Input Form -->
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-xs text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Tab Peran (Role Filter: HANYA Pelanggan & Admin) & Search Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-4 mb-4 space-y-3">
        <form action="{{ route('admin.users') }}" method="GET" class="flex flex-col sm:flex-row gap-3 justify-between items-center">
            
            <!-- Tabs Filter Role -->
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.users', ['role' => 'customer', 'search' => $search]) }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-extrabold transition-all border flex items-center gap-2 select-none {{ $roleFilter === 'customer' ? 'bg-blue-600 text-white border-blue-600 shadow-soft' : 'bg-bg-light text-gray-muted border-gray-light hover:text-blue-600' }}">
                   <span class="material-symbols-rounded text-base">group</span>
                   Pelanggan
                   <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $roleFilter === 'customer' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600 border border-blue-200' }}">
                       {{ $customerCount }}
                   </span>
                </a>
                <a href="{{ route('admin.users', ['role' => 'admin', 'search' => $search]) }}" 
                   class="px-5 py-2.5 rounded-xl text-xs font-extrabold transition-all border flex items-center gap-2 select-none {{ $roleFilter === 'admin' ? 'bg-purple-600 text-white border-purple-600 shadow-soft' : 'bg-bg-light text-gray-muted border-gray-light hover:text-purple-600' }}">
                   <span class="material-symbols-rounded text-base">shield_person</span>
                   Admin
                   <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $roleFilter === 'admin' ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-600 border border-purple-200' }}">
                       {{ $adminCount }}
                   </span>
                </a>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-72 group">
                <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">search</span>
                <input 
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama, email, HP..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-light text-xs text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-bg-light" 
                />
                <input type="hidden" name="role" value="{{ $roleFilter }}" />
            </div>
        </form>
    </div>

    <!-- MODAL 1: Tambah / Edit Admin Baru (Hanya untuk Peran Admin) -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" x-cloak>
        <div @click.away="modalOpen = false" class="w-full max-w-xl rounded-3xl bg-white p-6 shadow-2xl border border-gray-light">
            <!-- Header Modal -->
            <div class="flex items-center justify-between mb-5 border-b border-gray-light pb-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-dark" x-text="editMode ? 'Ubah Data Admin' : 'Tambah Admin Baru'"></h2>
                    <p class="text-xs text-gray-muted" x-text="editMode ? 'Perbarui informasi kredensial admin.' : 'Isi form untuk menambahkan administrator baru.'"></p>
                </div>
                <button type="button" @click="modalOpen = false" class="text-gray-muted hover:text-gray-dark text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <!-- Form Admin -->
            <form :action="editMode ? '{{ url('admin/users/update') }}/' + currentUserId : '{{ route('admin.users.store') }}'" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="flex flex-col gap-1.5 sm:col-span-2">
                        <label class="text-xs font-extrabold text-gray-dark uppercase tracking-wider">Nama Lengkap Admin</label>
                        <input type="text" name="name" x-model="userData.name" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-xs text-gray-dark focus:outline-none focus:border-purple-600" />
                    </div>
                    <!-- Email -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-extrabold text-gray-dark uppercase tracking-wider">Email Admin</label>
                        <input type="email" name="email" x-model="userData.email" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-xs text-gray-dark focus:outline-none focus:border-purple-600" />
                    </div>
                    <!-- Nomor HP -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-extrabold text-gray-dark uppercase tracking-wider">Nomor HP</label>
                        <input type="tel" name="phone" x-model="userData.phone" class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-xs text-gray-dark focus:outline-none focus:border-purple-600" placeholder="08..." />
                    </div>
                    <!-- Role (Terkunci Admin) -->
                    <input type="hidden" name="role" value="admin" />
                    <!-- Status Akun -->
                    <div class="flex flex-col gap-1.5 sm:col-span-2">
                        <label class="text-xs font-extrabold text-gray-dark uppercase tracking-wider">Status Akun</label>
                        <select name="status" x-model="userData.status" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-xs text-gray-dark focus:outline-none focus:border-purple-600 cursor-pointer font-semibold">
                            <option value="aktif">Aktif (Dapat Login)</option>
                            <option value="tidak aktif">Tidak Aktif (Terblokir)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-light pt-3">
                    <!-- Password -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-extrabold text-gray-dark uppercase tracking-wider">Password</label>
                        <div class="relative">
                            <input :type="showPw1 ? 'text' : 'password'" name="password" :required="!editMode" 
                                class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 pr-10 text-xs text-gray-dark focus:outline-none focus:border-purple-600" 
                                placeholder="Minimal 8 karakter"
                            />
                            <button type="button" @click="showPw1 = !showPw1" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-muted hover:text-purple-600 cursor-pointer">
                                <span class="material-symbols-rounded text-base" x-text="showPw1 ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        <span class="text-[10px] text-gray-muted" x-show="editMode">*Kosongkan jika tidak ingin mengubah password</span>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-extrabold text-gray-dark uppercase tracking-wider">Konfirmasi Password</label>
                        <div class="relative">
                            <input :type="showPw2 ? 'text' : 'password'" name="password_confirmation" :required="!editMode" 
                                class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 pr-10 text-xs text-gray-dark focus:outline-none focus:border-purple-600" 
                                placeholder="Ulangi password"
                            />
                            <button type="button" @click="showPw2 = !showPw2" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-muted hover:text-purple-600 cursor-pointer">
                                <span class="material-symbols-rounded text-base" x-text="showPw2 ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Batal / Simpan -->
                <div class="flex gap-3 justify-end pt-3 border-t border-gray-light">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-gray-light text-gray-dark hover:bg-bg-light text-xs font-bold cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-extrabold cursor-pointer shadow-soft">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Pengaturan Inaktivitas & Hapus Akun Mati Pelanggan -->
    <div x-show="inactivityModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs" x-cloak>
        <div @click.away="inactivityModalOpen = false" class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl border border-gray-light">
            <div class="flex items-center justify-between mb-4 border-b border-gray-light pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <span class="material-symbols-rounded text-lg">timer_off</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-dark">Batas Inaktivitas & Akun Mati</h2>
                        <p class="text-xs text-gray-muted">Atur otomatisasi deaktivasi & hapus akun pelanggan mati.</p>
                    </div>
                </div>
                <button type="button" @click="inactivityModalOpen = false" class="text-gray-muted hover:text-gray-dark text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('admin.users.clear-inactive') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-950 space-y-1">
                    <p class="font-bold flex items-center gap-1">
                        <span class="material-symbols-rounded text-base text-amber-600">info</span> 
                        Info Inaktivitas Akun Pelanggan
                    </p>
                    <p>Pelanggan yang tidak pernah login/beraktivitas melebihi batas hari yang ditentukan akan otomatis berubah statusnya menjadi <strong>Tidak Aktif (Mati)</strong>.</p>
                </div>

                <!-- Pilihan Batas Hari -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-extrabold text-gray-dark uppercase tracking-wider">Batas Waktu Tanpa Aktivitas (Batas Akun Mati)</label>
                    <select name="inactivity_days" class="w-full rounded-xl border border-gray-light bg-white px-4 py-2.5 text-xs text-gray-dark focus:outline-none focus:border-amber-500 font-semibold cursor-pointer">
                        <option value="30" {{ $inactivityDays == 30 ? 'selected' : '' }}>30 Hari Tanpa Aktivitas</option>
                        <option value="60" {{ $inactivityDays == 60 ? 'selected' : '' }}>60 Hari Tanpa Aktivitas</option>
                        <option value="90" {{ $inactivityDays == 90 ? 'selected' : '' }}>90 Hari Tanpa Aktivitas</option>
                        <option value="180" {{ $inactivityDays == 180 ? 'selected' : '' }}>180 Hari Tanpa Aktivitas</option>
                        <option value="0" {{ $inactivityDays == 0 ? 'selected' : '' }}>Nonaktifkan Batas Inaktivitas</option>
                    </select>
                </div>

                <!-- Toggle Auto Delete -->
                <div class="flex items-start gap-3 p-3 border border-gray-light rounded-xl bg-bg-light/50">
                    <input type="checkbox" id="auto_delete" name="auto_delete" value="1" {{ session('user_auto_delete_inactive') ? 'checked' : '' }} class="mt-0.5 rounded text-amber-600 focus:ring-amber-500 cursor-pointer">
                    <label for="auto_delete" class="text-xs text-gray-dark cursor-pointer select-none">
                        <span class="font-bold block">Hapus Otomatis Akun Mati</span>
                        <span class="text-[11px] text-gray-muted">Setiap kali halaman dibuka, sistem akan otomatis menghapus akun pelanggan berstatus Tidak Aktif.</span>
                    </label>
                </div>

                <!-- Tombol Aksi Hapus Manual Akun Mati & Simpan -->
                <div class="pt-3 border-t border-gray-light flex flex-col sm:flex-row gap-2 justify-between items-center">
                    @if($inactiveCustomerCount > 0)
                        <button type="submit" name="action_delete_now" value="1" onclick="return confirm('Apakah Anda yakin ingin menghapus seluruh {{ $inactiveCustomerCount }} akun pelanggan mati (tidak aktif) secara permanen?');" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-soft flex items-center justify-center gap-1 cursor-pointer">
                            <span class="material-symbols-rounded text-sm">delete_forever</span>
                            Hapus {{ $inactiveCustomerCount }} Akun Mati Sekarang
                        </button>
                    @else
                        <span class="text-[11px] text-gray-400 font-medium">Saat ini tidak ada akun pelanggan mati.</span>
                    @endif

                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="inactivityModalOpen = false" class="px-3 py-2 rounded-xl border border-gray-light text-gray-dark hover:bg-bg-light text-xs font-bold cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-extrabold shadow-soft cursor-pointer">Simpan Batas</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar User -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light text-gray-muted font-bold uppercase tracking-wider text-left">
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Nomor HP</th>
                        <th class="py-3.5 px-4">Terakhir Aktif</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        @if($roleFilter === 'customer')
                            <th class="py-3.5 px-4 text-center">Kontrol Akses Status</th>
                        @endif
                        <th class="py-3.5 px-4 text-center">Aksi Hapus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-light">
                    @forelse($users as $u)
                        <tr class="hover:bg-bg-light/40 transition-colors">
                            <!-- Pengguna -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm border {{ $u['raw_role'] === 'admin' ? 'bg-purple-50 text-purple-600 border-purple-200' : 'bg-blue-50 text-blue-600 border-blue-200' }}">
                                        <span class="material-symbols-rounded text-base">{{ $u['raw_role'] === 'admin' ? 'admin_panel_settings' : 'person' }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-dark">{{ $u['name'] }}</p>
                                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $u['role'] }}</span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Email -->
                            <td class="py-3.5 px-4 font-medium text-gray-600">{{ $u['email'] }}</td>
                            
                            <!-- HP -->
                            <td class="py-3.5 px-4 text-gray-600 font-medium">{{ $u['phone'] }}</td>
                            
                            <!-- Terakhir Login/Aktif -->
                            <td class="py-3.5 px-4 text-gray-500 font-medium">{{ $u['lastLogin'] }}</td>
                            
                            <!-- Status Akun -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold 
                                    {{ strtolower($u['status']) === 'aktif' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ strtolower($u['status']) === 'aktif' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                    {{ strtolower($u['status']) === 'aktif' ? 'Aktif' : 'Tidak Aktif (Mati)' }}
                                </span>
                            </td>
                            
                            <!-- Kontrol Akses Status khusus Pelanggan (Aktifkan / Nonaktifkan) -->
                            @if($roleFilter === 'customer')
                                <td class="py-3.5 px-4 text-center">
                                    <form action="{{ route('admin.users.toggle-status', $u['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @if(strtolower($u['status']) === 'aktif')
                                            <button type="submit" onclick="return confirm('Nonaktifkan akun pelanggan {{ addslashes($u['name']) }}?');" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 font-extrabold text-[11px] inline-flex items-center gap-1 transition-all cursor-pointer">
                                                <span class="material-symbols-rounded text-sm">block</span>
                                                Nonaktifkan Akun
                                            </button>
                                        @else
                                            <button type="submit" onclick="return confirm('Aktifkan kembali akun pelanggan {{ addslashes($u['name']) }}?');" class="px-3 py-1.5 rounded-xl bg-green-50 hover:bg-green-100 border border-green-300 text-green-800 font-extrabold text-[11px] inline-flex items-center gap-1 transition-all cursor-pointer">
                                                <span class="material-symbols-rounded text-sm">check_circle</span>
                                                Aktifkan Akun
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            @endif

                            <!-- Aksi (Edit untuk Admin / Hapus untuk Pelanggan & Admin) -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex gap-1.5 justify-center items-center">
                                    @if($u['raw_role'] === 'admin')
                                        <!-- Edit Admin Button -->
                                        <button 
                                            type="button"
                                            @click="modalOpen = true; editMode = true; currentUserId = {{ $u['id'] }}; userData = { name: '{{ addslashes($u['name']) }}', email: '{{ $u['email'] }}', phone: '{{ $u['phone'] !== '-' ? $u['phone'] : '' }}', role: 'admin', status: '{{ $u['status'] }}' }; showPw1 = false; showPw2 = false"
                                            class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 border border-purple-200 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all cursor-pointer shadow-xs"
                                            title="Ubah Data Admin"
                                        >
                                            <span class="material-symbols-rounded text-sm">edit</span>
                                        </button>
                                    @endif

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.users.delete', $u['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ addslashes($u['name']) }} secara permanen?');" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="w-8 h-8 rounded-xl bg-red-50 text-red-600 border border-red-200 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all cursor-pointer shadow-xs"
                                            title="Hapus Akun User"
                                        >
                                            <span class="material-symbols-rounded text-sm">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $roleFilter === 'customer' ? 7 : 6 }}" class="py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-rounded text-3xl text-gray-400">group_off</span>
                                    <p class="font-semibold text-xs text-gray-600">Tidak ada data {{ $roleFilter === 'admin' ? 'Admin' : 'Pelanggan' }} ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
