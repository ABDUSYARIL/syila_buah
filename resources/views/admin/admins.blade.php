@extends('layouts.admin')

@section('title', 'Kelola Admin - Admin Syila Buah')

@section('content')
{{-- Menginisialisasi AlpineJS dengan variabel modalOpen, editMode, currentAdminId, dan data form adminData --}}
<div x-data="{ 
    modalOpen: false, 
    editMode: false, 
    currentAdminId: null, 
    adminData: { name: '', email: '', phone: '', status: 'aktif' },
    showPw1: false, {{-- Status apakah password terlihat (toggle visibility) --}}
    showPw2: false  {{-- Status apakah konfirmasi password terlihat --}}
}">
    <!-- Judul Halaman dan Tombol Tambah -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-dark">Kelola Admin</h1>
            <p class="text-sm text-gray-muted">{{ count($admins) }} admin terdaftar</p>
        </div>
        {{-- Tombol Tambah Admin: mereset status editMode ke false, mengosongkan form, dan mereset showPw --}}
        <button type="button" @click="modalOpen = true; editMode = false; currentAdminId = null; adminData = { name: '', email: '', phone: '', status: 'aktif' }; showPw1 = false; showPw2 = false" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 bg-primary text-white hover:bg-primary-hover active:bg-primary-active shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 px-4 py-2.5 text-sm cursor-pointer">
            <span class="material-symbols-rounded text-base">add</span> Tambah Admin
        </button>
    </div>

    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-light border border-primary/20 text-primary font-semibold text-sm flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Notifikasi Error/Gagal -->
    @if(session('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 font-semibold text-sm flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">cancel</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Tampilan Error Validasi Input Form -->
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- MODAL INTERAKTIF: Tambah / Edit Admin -->
    <div x-show="modalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-4" x-cloak>
        <div @click.away="modalOpen = false" class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-2xl">
            <!-- Header Modal -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    {{-- Judul dan Subjudul modal dinamis sesuai dengan mode (Tambah / Edit) --}}
                    <h2 class="text-xl font-bold text-gray-dark" x-text="editMode ? 'Ubah Data Admin' : 'Tambah Admin Baru'"></h2>
                    <p class="text-sm text-gray-muted" x-text="editMode ? 'Perbarui informasi data admin terpilih.' : 'Isi data admin baru dan simpan.'"></p>
                </div>
                <button type="button" @click="modalOpen = false" class="text-gray-muted hover:text-gray-dark transition-colors cursor-pointer">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            {{-- Form dinamis: Rute aksi POST berubah otomatis sesuai mode edit/create menggunakan AlpineJS --}}
            <form :action="editMode ? '{{ url('admin/admins/update') }}/' + currentAdminId : '{{ route('admin.admins.store') }}'" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Input Nama Admin -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Nama</label>
                        <input type="text" name="name" x-model="adminData.name" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>
                    <!-- Input Email Admin -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Email</label>
                        <input type="email" name="email" x-model="adminData.email" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>
                    <!-- Input Nomor HP Admin -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Nomor HP</label>
                        <input type="tel" name="phone" x-model="adminData.phone" class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>
                    <!-- Pilihan Status Akun Admin -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Status</label>
                        <select name="status" x-model="adminData.status" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all cursor-pointer">
                            <option value="aktif">Aktif</option>
                            <option value="tidak aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Field Password dengan tombol mata (eye toggle) untuk melihat/menyembunyikan password --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Password</label>
                        {{-- Password hanya 'required' (wajib) ketika menambah baru, dan opsional ketika mengedit --}}
                        <div class="relative group">
                            <input :type="showPw1 ? 'text' : 'password'" name="password" :required="!editMode" 
                                class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 pr-12 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" 
                                placeholder="Minimal 8 karakter"
                            />
                            {{-- Tombol mata untuk toggle visibility password --}}
                            <button type="button" @click="showPw1 = !showPw1" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-muted hover:text-primary transition-colors cursor-pointer">
                                <span class="material-symbols-rounded text-lg" x-text="showPw1 ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        <span class="text-[10px] text-gray-muted mt-1" x-show="editMode">Kosongkan jika tidak ingin mengubah password admin</span>
                    </div>
                    {{-- Field Konfirmasi Password dengan eye toggle --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Konfirmasi Password</label>
                        <div class="relative group">
                            <input :type="showPw2 ? 'text' : 'password'" name="password_confirmation" :required="!editMode" 
                                class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 pr-12 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                                placeholder="Ulangi password"
                            />
                            {{-- Tombol mata untuk toggle visibility konfirmasi password --}}
                            <button type="button" @click="showPw2 = !showPw2" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-muted hover:text-primary transition-colors cursor-pointer">
                                <span class="material-symbols-rounded text-lg" x-text="showPw2 ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Batal / Simpan -->
                <div class="flex flex-wrap gap-3 justify-end pt-2 border-t border-bg-light">
                    <button type="button" @click="modalOpen = false" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-light text-gray-dark hover:bg-bg-light px-5 py-3 text-sm transition-all duration-300 cursor-pointer">Batal</button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary text-white hover:bg-primary-hover px-5 py-3 text-sm transition-all duration-300 cursor-pointer shadow-soft">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search / Filter Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-4 mb-4 hover:shadow-soft-hover transition-all duration-300">
        <div class="flex gap-3">
            <div class="relative flex-grow group">
                <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">search</span>
                <input placeholder="Cari admin..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-light text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-bg-light" />
            </div>
            <button class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 border-2 border-primary text-primary hover:bg-green-light px-4 py-2.5 text-sm cursor-pointer hover:shadow-soft">
                <span class="material-symbols-rounded text-base">filter_list</span> Filter
            </button>
        </div>
    </div>

    <!-- Admins Table -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light">
                        @foreach(['Admin', 'Email', 'Role', 'Nomor HP', 'Status', 'Terakhir Login', 'Aksi'] as $h)
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-muted uppercase tracking-wide">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $a)
                        <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                            <!-- Admin Profile -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-green-light flex items-center justify-center flex-shrink-0 shadow-sm border border-primary/5">
                                        <span class="material-symbols-rounded text-primary text-base">person</span>
                                    </div>
                                    <span class="font-semibold text-gray-dark">{{ $a['name'] }}</span>
                                </div>
                            </td>
                            
                            <!-- Email -->
                            <td class="py-3.5 px-4 text-gray-muted">{{ $a['email'] }}</td>
                            
                            <!-- Role -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    {{ $a['role'] === 'Super Admin' ? 'bg-[#FFF3E0] text-[#E65100]' : 'bg-blue-50 text-blue-600' }}">
                                    {{ $a['role'] }}
                                </span>
                            </td>
                            
                            <!-- HP -->
                            <td class="py-3.5 px-4 text-gray-muted">{{ $a['phone'] }}</td>
                            
                            <!-- Status (Aktif / Tidak Aktif) -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    {{ strtolower($a['status']) === 'aktif' ? 'bg-green-light text-primary' : 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($a['status']) }}
                                </span>
                            </td>
                            
                            <!-- Terakhir Login -->
                            <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $a['lastLogin'] }}</td>
                            
                            <!-- Aksi (Edit & Hapus) -->
                            <td class="py-3.5 px-4">
                                <div class="flex gap-1.5">
                                    {{-- Tombol Edit: Mengaktifkan mode edit modal, mengisi data admin terpilih, dan mereset status tampil password --}}
                                    <button 
                                        type="button"
                                        @click="modalOpen = true; editMode = true; currentAdminId = {{ $a['id'] }}; adminData = { name: '{{ addslashes($a['name']) }}', email: '{{ $a['email'] }}', phone: '{{ $a['phone'] !== '-' ? $a['phone'] : '' }}', status: '{{ $a['status'] }}' }; showPw1 = false; showPw2 = false"
                                        class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all cursor-pointer shadow-sm"
                                        title="Ubah Data Admin"
                                    >
                                        <span class="material-symbols-rounded text-sm">edit</span>
                                    </button>

                                    {{-- Tombol Hapus: Mengirim aksi POST untuk menghapus data admin dengan konfirmasi --}}
                                    <form action="{{ route('admin.admins.delete', $a['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin {{ addslashes($a['name']) }}?');" class="inline">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="w-8 h-8 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all cursor-pointer shadow-sm"
                                            title="Hapus Admin"
                                        >
                                            <span class="material-symbols-rounded text-sm">cancel</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
