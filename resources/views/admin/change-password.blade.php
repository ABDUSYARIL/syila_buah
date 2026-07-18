@extends('layouts.admin')

@section('title', 'Ganti Password Admin - Syila Buah')

@section('content')
<div class="max-w-lg" x-data="{ show: { current: false, new: false, confirm: false } }">
    <a href="{{ route('admin.profile') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-muted hover:text-primary mb-6 transition-colors">
        <span class="material-symbols-rounded text-base">arrow_back</span> Profil Admin
    </a>
    
    <h1 class="text-2xl font-bold text-gray-dark mb-6">Ganti Password</h1>

    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
        <form action="{{ route('admin.change-password.update') }}" method="POST" class="space-y-4">
            @csrf

            @if ($errors->any())
                <div class="rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @foreach([
                ['key' => 'current', 'label' => 'Password Lama', 'name' => 'current_password'],
                ['key' => 'new', 'label' => 'Password Baru', 'name' => 'password'],
                ['key' => 'confirm', 'label' => 'Konfirmasi Password Baru', 'name' => 'password_confirmation']
            ] as $field)
                <!-- Field Input -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-dark">{{ $field['label'] }}</label>
                    <div class="relative group">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-rounded text-lg">shield</span>
                        </div>
                        <input
                            :type="show.{{ $field['key'] }} ? 'text' : 'password'"
                            name="{{ $field['name'] }}"
                            required
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-gray-light bg-white px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all pl-10 pr-12"
                        />
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <button type="button" @click="show.{{ $field['key'] }} = !show.{{ $field['key'] }}" class="text-gray-muted hover:text-primary transition-colors cursor-pointer">
                                <span class="material-symbols-rounded text-base" x-text="show.{{ $field['key'] }} ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Action buttons -->
            <div class="flex gap-3 mt-6 border-t border-bg-light pt-6">
                <a href="{{ route('admin.profile') }}" class="w-1/2 inline-flex items-center justify-center gap-2 font-semibold rounded-xl border-2 border-primary text-primary hover:bg-green-light px-4 py-3 text-sm transition-all duration-300">
                    Batal
                </a>
                <button
                    type="submit"
                    class="w-1/2 inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all duration-300 select-none bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-4 py-3 text-sm shadow-soft hover:shadow-soft-hover cursor-pointer"
                >
                    <span class="material-symbols-rounded text-base">check</span> Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
