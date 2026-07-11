@extends('layouts.admin')

@section('title', 'Kelola Admin - Admin Syila Buah')

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-dark">Kelola Admin</h1>
            <p class="text-sm text-gray-muted">{{ count($admins) }} admin terdaftar</p>
        </div>
        <button class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 bg-primary text-white hover:bg-primary-hover active:bg-primary-active shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 px-4 py-2.5 text-sm cursor-pointer">
            <span class="material-symbols-rounded text-base">add</span> Tambah Admin
        </button>
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
                            
                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    {{ $a['status'] === 'Aktif' ? 'bg-green-light text-primary' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $a['status'] }}
                                </span>
                            </td>
                            
                            <!-- Last Login -->
                            <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $a['lastLogin'] }}</td>
                            
                            <!-- Aksi -->
                            <td class="py-3.5 px-4">
                                <div class="flex gap-1.5">
                                    <button class="w-8 h-8 rounded-xl bg-green-light text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all cursor-pointer shadow-sm"><span class="material-symbols-rounded text-sm">visibility</span></button>
                                    <button class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all cursor-pointer shadow-sm"><span class="material-symbols-rounded text-sm">edit</span></button>
                                    <button class="w-8 h-8 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all cursor-pointer shadow-sm"><span class="material-symbols-rounded text-sm">cancel</span></button>
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
