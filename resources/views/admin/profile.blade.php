@extends('layouts.admin')

@section('title', 'Profil Admin - Syila Buah')

@section('content')
<div class="max-w-2xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight">Profil Admin</h1>
        <p class="text-sm text-gray-muted mt-1">Edit data profil admin dan simpan perubahan di bawah.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-light border border-primary/20 text-primary font-semibold text-sm flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php $user = Auth::user(); @endphp
    <div x-data="{ photoSrc: '{{ $user && $user->avatar ? asset('storage/' . $user->avatar) : '' }}' }" class="bg-white rounded-2xl shadow-soft border border-gray-light p-8 hover:shadow-soft-hover transition-all duration-300">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-bg-light">
                <div class="relative">
                    <img :src="photoSrc || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&auto=format'" alt="Avatar admin" class="w-24 h-24 rounded-2xl object-cover shadow-md border border-gray-light" />
                    <input type="file" name="avatar" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer rounded-2xl" @change="photoSrc = URL.createObjectURL($event.target.files[0])" />
                </div>
                <div class="space-y-1 text-center sm:text-left">
                    <p class="text-xl font-bold text-gray-dark">{{ $user->name }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#FFF3E0] text-[#E65100]">Admin</span>
                    <p class="text-xs text-gray-muted mt-1.5">Bergabung sejak {{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-dark">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Nomor HP</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Role</label>
                    <input type="text" value="{{ ucfirst($user->role) }}" disabled class="w-full rounded-xl border border-gray-light bg-gray-50 px-4 py-3 text-sm text-gray-dark" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-bg-light">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all duration-300 bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-5 py-3 text-sm shadow-soft hover:shadow-soft-hover">
                    <span class="material-symbols-rounded text-lg">check</span>
                    Simpan Profil
                </button>
                <a href="{{ route('admin.change-password') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 font-semibold rounded-xl border border-gray-light text-gray-dark hover:bg-green-light px-5 py-3 text-sm transition-all duration-300">
                    <span class="material-symbols-rounded text-lg">security</span>
                    Ganti Password
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
