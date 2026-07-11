@extends('layouts.app')

@section('title', 'Edit Profil - Syila Buah')

@section('content')
@php $u = Auth::user(); @endphp
<div class="max-w-2xl mx-auto px-6 py-8" x-data="{ 
    photoUploaded: false,
    photoSrc: '{{ $u && $u->avatar ? asset('storage/' . $u->avatar) : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&auto=format' }}'
}">
    <div class="mb-8">
        <a href="{{ route('profile') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-muted hover:text-primary transition-colors">
            <span class="material-symbols-rounded text-base">arrow_back</span> Kembali ke Profil
        </a>
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight mt-3">Edit Profil</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-8 hover:shadow-soft-hover transition-all duration-300">
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            
            <!-- Upload Foto Section -->
            <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-bg-light">
                <div class="relative">
                    @php $u = Auth::user(); @endphp
                    <img :src="photoSrc" alt="Foto profil" class="w-24 h-24 rounded-2xl object-cover shadow-md border border-gray-light" />
                    <input type="file" name="avatar" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer rounded-2xl" @change="photoUploaded = true; photoSrc = URL.createObjectURL($event.target.files[0])" />
                </div>
                <div class="text-center sm:text-left space-y-1">
                    <p class="text-sm font-semibold text-gray-dark">Foto Profil</p>
                    <p class="text-xs text-gray-muted leading-relaxed">Format JPG atau PNG. Ukuran maksimal 2MB.</p>
                    <p class="text-[10px] text-primary font-bold mt-1" x-show="photoUploaded">✓ Foto baru berhasil dipilih</p>
                </div>
            </div>

            <!-- Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Nama Lengkap</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', Auth::user()->name ?? '') }}"
                        required
                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                    />
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', Auth::user()->email ?? '') }}"
                        required
                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                    />
                </div>

                <!-- Nomor HP -->
                <div class="flex flex-col sm:col-span-2 gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Nomor HP</label>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone', Auth::user()->phone ?? '') }}"
                        required
                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all"
                    />
                </div>

                <!-- Alamat Lengkap -->
                <div class="flex flex-col sm:col-span-2 gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">Alamat Pengiriman</label>
                    <textarea
                        name="address"
                        required
                        rows="3"
                        class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all resize-none"
                    >{{ old('address', Auth::user()->address ?? '') }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t border-bg-light">
                <a href="{{ route('profile') }}" class="w-1/2 inline-flex items-center justify-center gap-2 font-bold rounded-xl border-2 border-primary text-primary hover:bg-green-light px-5 py-3 text-sm transition-all duration-300">
                    Batal
                </a>
                <button
                    type="submit"
                    class="w-1/2 inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all duration-300 select-none bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-5 py-3 text-sm shadow-soft hover:shadow-soft-hover cursor-pointer"
                >
                    <span class="material-symbols-rounded text-lg">check</span> Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
