@extends('layouts.admin')

@section('title', 'Profil Admin - Syila Buah')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-dark mb-6">Profil Admin</h1>
    
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
        <!-- Profile Header Section -->
        <div class="flex flex-col sm:flex-row items-start gap-6 mb-6">
            <div class="relative">
                <div class="w-20 h-20 rounded-2xl bg-green-light flex items-center justify-center shadow-sm">
                    <span class="material-symbols-rounded text-primary text-3xl">person</span>
                </div>
                <button onclick="alert('Unggah foto profil baru...')" class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center shadow-md hover:bg-primary-hover border border-white cursor-pointer transition-colors">
                    <span class="material-symbols-rounded text-sm">photo_camera</span>
                </button>
            </div>
            <div class="flex-grow space-y-1">
                <p class="text-xl font-bold text-gray-dark">Syila Admin</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#FFF3E0] text-[#E65100]">Super Admin</span>
                <p class="text-xs text-gray-muted mt-1.5">Bergabung sejak Januari 2024</p>
            </div>
        </div>

        <!-- Profile Details List -->
        <div class="divide-y divide-bg-light mb-6 text-sm">
            @foreach([
                ['l' => 'Nama', 'v' => 'Syila Admin'],
                ['l' => 'Email', 'v' => 'admin@syilabuah.id'],
                ['l' => 'Nomor HP', 'v' => '081234567890'],
                ['l' => 'Role', 'v' => 'Super Admin']
            ] as $info)
                <div class="flex justify-between py-3">
                    <span class="text-gray-muted font-medium">{{ $info['l'] }}</span>
                    <span class="font-bold text-gray-dark">{{ $info['v'] }}</span>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 border-t border-bg-light pt-6">
            <button onclick="alert('Mengedit profil admin...')" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl border-2 border-primary text-primary hover:bg-green-light px-4 py-2.5 text-sm cursor-pointer hover:shadow-soft transition-all duration-300">
                <span class="material-symbols-rounded text-base">edit</span> Edit Profil
            </button>
            <a href="{{ route('admin.change-password') }}" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl text-gray-muted hover:text-primary hover:bg-green-light px-4 py-2.5 text-sm transition-all duration-300">
                <span class="material-symbols-rounded text-base">security</span> Ganti Password
            </a>
        </div>
    </div>
</div>
@endsection
