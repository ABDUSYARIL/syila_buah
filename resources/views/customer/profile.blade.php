@extends('layouts.app')

@section('title', 'Profil Saya - Syila Buah')

@section('content')
@php $user = Auth::user(); @endphp
<div class="max-w-2xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight">Profil Saya</h1>
        <p class="text-sm text-gray-muted mt-1">Kelola informasi data diri dan pengaturan akun Anda.</p>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-light border border-primary/20 text-primary font-semibold text-sm flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-8 space-y-8 hover:shadow-soft-hover transition-all duration-300">
        <!-- Profile Header Section -->
        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-bg-light">
            <div class="relative group">
                @if($user && $user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-2xl object-cover shadow-md" />
                @else
                    <div class="w-24 h-24 rounded-2xl bg-green-light flex items-center justify-center shadow-md">
                        <span class="material-symbols-rounded text-primary text-4xl">person</span>
                    </div>
                @endif
            </div>
            <div class="text-center sm:text-left space-y-1">
                <p class="text-xl font-bold text-gray-dark">{{ $user->name ?? 'Pelanggan' }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-light text-primary">{{ $user->role === 'admin' ? 'Admin' : 'Pelanggan' }}</span>
                <p class="text-xs text-gray-muted mt-1.5">Terdaftar sejak: {{ $user->created_at ? $user->created_at->format('F Y') : '-' }}</p>
            </div>
        </div>

        <!-- Information List -->
        <div class="divide-y divide-bg-light text-sm">
            @foreach([
                ['label' => 'Nama Lengkap', 'val' => $user->name ?? '-', 'icon' => 'person'],
                ['label' => 'Email', 'val' => $user->email ?? '-', 'icon' => 'mail'],
                ['label' => 'Nomor HP', 'val' => $user->phone ?? '-', 'icon' => 'phone'],
                ['label' => 'Alamat Pengiriman', 'val' => $user->address ?? '-', 'icon' => 'map']
            ] as $info)
                <div class="flex flex-col sm:flex-row sm:justify-between py-4 gap-1">
                    <span class="text-gray-muted flex items-center gap-2 font-medium">
                        <span class="material-symbols-rounded text-primary text-lg">{{ $info['icon'] }}</span> {{ $info['label'] }}
                    </span>
                    <span class="font-bold text-gray-dark sm:text-right max-w-sm">{{ $info['val'] }}</span>
                </div>
            @endforeach
        </div>

        <!-- Quick actions -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center gap-2 font-bold rounded-xl border-2 border-primary text-primary hover:bg-green-light px-5 py-3 text-sm transition-all duration-300 shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5">
                <span class="material-symbols-rounded text-lg">edit</span> Edit Profil
            </a>
            <a href="{{ route('profile.change-password') }}" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl text-gray-muted hover:text-primary hover:bg-green-light px-5 py-3 text-sm transition-all duration-300">
                <span class="material-symbols-rounded text-lg">security</span> Ganti Password
            </a>
        </div>
    </div>
</div>
@endsection
