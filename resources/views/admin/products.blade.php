@extends('layouts.admin')

@section('title', 'Manajemen Produk - Admin Syila Buah')

@section('content')
<div x-data="{ 
    modalOpen: false,
    isEdit: false,
    editId: '',
    editName: '',
    editCategoryId: '',
    editPrice: '',
    editUnit: 'Kg',
    editDescription: '',
    editStock: 0,
    editImageUrl: '',
    imagePreview: '',
    openAddModal() {
        this.isEdit = false;
        this.editId = '';
        this.editName = '';
        this.editCategoryId = '1';
        this.editPrice = '';
        this.editUnit = 'Kg';
        this.editDescription = '';
        this.editStock = 0;
        this.editImageUrl = '';
        this.imagePreview = ''; // DIPERBAIKI: Untuk tambah produk, pratinjau gambar dikosongkan
        this.modalOpen = true;
    },
    openEditModal(p) {
        this.isEdit = true;
        this.editId = p.id;
        this.editName = p.name;
        this.editCategoryId = p.category_id;
        this.editPrice = p.price;
        this.editUnit = p.unit;
        this.editDescription = p.description || '';
        this.editStock = p.stock;
        this.editImageUrl = p.image || '';
        
        // Menentukan pratinjau gambar berdasarkan tipe path yang tersimpan di database:
        // 1. Jika URL eksternal (http/https) → gunakan langsung
        // 2. Jika path lokal /storage/... → gabungkan dengan base URL aplikasi
        // 3. Jika ID Unsplash → bangun URL Unsplash
        if (!p.image) {
            this.imagePreview = '';
        } else if (p.image.startsWith('http://') || p.image.startsWith('https://')) {
            // URL eksternal, langsung pakai
            this.imagePreview = p.image;
        } else if (p.image.startsWith('/storage/') || p.image.startsWith('storage/')) {
            // Path lokal Laravel storage — gabungkan dengan APP_URL agar muncul dengan benar
            this.imagePreview = '{{ rtrim(config("app.url"), "/") }}' + (p.image.startsWith('/') ? '' : '/') + p.image;
        } else {
            // ID foto Unsplash
            this.imagePreview = 'https://images.unsplash.com/photo-' + p.image + '?w=150&h=150&fit=crop&auto=format';
        }
            
        this.modalOpen = true;
    }
}">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-dark">Manajemen Produk</h1>
            <p class="text-sm text-gray-muted">{{ count($products) }} total produk terdaftar</p>
        </div>
        <button @click="openAddModal()" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl bg-primary text-white hover:bg-primary-hover active:bg-primary-active px-4 py-2.5 text-sm cursor-pointer shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 transition-all">
            <span class="material-symbols-rounded text-base">add</span> Tambah Produk
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-green-light border border-primary/20 text-primary px-4 py-3 rounded-xl text-sm font-semibold mb-6 flex items-center gap-2">
            <span class="material-symbols-rounded">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-4 mb-4 hover:shadow-soft transition-all duration-300">
        <form action="{{ route('admin.products') }}" method="GET" class="flex gap-3">
            <div class="relative flex-grow group">
                <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted transition-colors group-focus-within:text-primary">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Cari nama produk..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-light text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-bg-light" 
                />
            </div>
            
            <select name="category" onchange="this.form.submit()" class="rounded-xl border border-gray-light text-sm text-gray-muted px-4 bg-white focus:outline-none focus:border-primary">
                <option value="Semua">Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->name }}" {{ $categoryName === $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            
            <button type="submit" class="inline-flex items-center justify-center font-semibold rounded-xl bg-primary text-white px-4 py-2.5 text-sm cursor-pointer hover:bg-primary-hover transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light">
                        @foreach(['Produk', 'Kategori', 'Harga', 'Satuan', 'Stok', 'Aksi'] as $header)
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-muted uppercase tracking-wide">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                            <!-- Produk (Avatar & Nama) -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-white border border-gray-light p-1.5 flex items-center justify-center flex-shrink-0">
                                        <img src="{{ \App\Http\Controllers\ProductData::img($p->image, 80, 80) }}" alt="{{ $p->name }}" class="max-w-full max-h-full object-contain" />
                                    </div>
                                    <span class="font-bold text-gray-dark text-sm">{{ $p->name }}</span>
                                </div>
                            </td>
                            
                            <!-- Kategori -->
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    {{ ($p->category->name ?? 'Buah Lokal') === 'Buah Lokal' ? 'bg-green-light text-primary' : 'bg-blue-50 text-blue-600' }}">
                                    {{ $p->category->name ?? 'Buah' }}
                                </span>
                            </td>
                            
                            <!-- Harga -->
                            <td class="py-3 px-4 font-bold text-gray-dark">
                                {{ \App\Http\Controllers\ProductData::rp($p->price) }}
                            </td>
                            
                            <!-- Satuan -->
                            <td class="py-3 px-4 text-gray-muted">
                                {{ $p->unit }}
                            </td>
                            
                            <!-- Stok -->
                            <td class="py-3 px-4">
                                <span class="font-semibold {{ $p->stock <= 20 ? 'text-red-500 font-bold' : 'text-gray-dark' }}">
                                    {{ $p->stock }}
                                </span>
                            </td>
                            
                            <!-- Aksi -->
                            <td class="py-3 px-4">
                                <div class="flex gap-1.5">
                                    <!-- Edit Button -->
                                    <button 
                                        type="button"
                                        @click="openEditModal({{ json_encode($p) }})"
                                        class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all cursor-pointer shadow-sm"
                                    >
                                        <span class="material-symbols-rounded text-sm">edit</span>
                                    </button>
                                    
                                    <!-- Delete Button Form -->
                                    <form action="{{ route('admin.products.delete', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all cursor-pointer shadow-sm">
                                            <span class="material-symbols-rounded text-sm">delete</span>
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

    <!-- Modal Form (Add / Edit) -->
    <div 
        x-show="modalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50"
        x-transition
        x-cloak
    >
        <div 
            @click.away="modalOpen = false" 
            class="bg-white rounded-3xl w-full max-w-lg shadow-soft border border-gray-light p-6 overflow-hidden relative"
        >
            <button 
                type="button" 
                @click="modalOpen = false" 
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-bg-light flex items-center justify-center text-gray-muted hover:text-gray-dark transition-colors cursor-pointer"
            >
                <span class="material-symbols-rounded text-lg">close</span>
            </button>

            <h2 class="text-xl font-extrabold text-gray-dark tracking-tight mb-4" x-text="isEdit ? 'Edit Produk' : 'Tambah Produk Baru'"></h2>

            <form action="{{ route('admin.products.save') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="id" :value="editId" />

                <!-- Nama -->
                <div>
                    <label class="block text-xs font-bold text-gray-muted uppercase tracking-wider mb-1">Nama Produk</label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        x-model="editName"
                        class="w-full rounded-xl border border-gray-light px-4 py-2.5 text-sm text-gray-dark focus:outline-none focus:border-primary" 
                        placeholder="Contoh: Apel Fuji Segar"
                    />
                </div>

                <!-- Kategori & Satuan -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-muted uppercase tracking-wider mb-1">Kategori</label>
                        <select 
                            name="category_id" 
                            required 
                            x-model="editCategoryId"
                            class="w-full rounded-xl border border-gray-light px-4 py-2.5 text-sm text-gray-dark bg-white focus:outline-none focus:border-primary"
                        >
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-muted uppercase tracking-wider mb-1">Satuan</label>
                        <select 
                            name="unit" 
                            required 
                            x-model="editUnit"
                            class="w-full rounded-xl border border-gray-light px-4 py-2.5 text-sm text-gray-dark bg-white focus:outline-none focus:border-primary"
                        >
                            @foreach(['Kg', 'Buah', 'Pack', 'Sisir', 'Paket'] as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Harga & Stok Awal -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-muted uppercase tracking-wider mb-1">Harga Jual (Rp)</label>
                        <input 
                            type="number" 
                            name="price" 
                            required 
                            x-model="editPrice"
                            class="w-full rounded-xl border border-gray-light px-4 py-2.5 text-sm text-gray-dark focus:outline-none focus:border-primary" 
                            placeholder="Contoh: 35000"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-muted uppercase tracking-wider mb-1">Stok Awal</label>
                        <input 
                            type="number" 
                            name="stock" 
                            x-model="editStock"
                            :disabled="isEdit"
                            class="w-full rounded-xl border border-gray-light px-4 py-2.5 text-sm text-gray-dark focus:outline-none focus:border-primary disabled:opacity-50 disabled:bg-gray-100" 
                            placeholder="Contoh: 100"
                        />
                        <span class="text-[10px] text-gray-muted mt-1 block" x-show="isEdit">*Stok produk hanya bisa diubah via menu Stok Masuk / Penyesuaian</span>
                    </div>
                </div>

                <!-- Gambar (Upload File / URL) -->
                <div class="border-t border-bg-light pt-3 space-y-3">
                    <label class="block text-xs font-bold text-gray-muted uppercase tracking-wider">Gambar Produk</label>
                    <div class="grid grid-cols-2 gap-4 items-center">
                        <div class="w-full aspect-square border border-gray-light rounded-2xl flex items-center justify-center p-2 bg-bg-light relative overflow-hidden">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="max-w-full max-h-full object-contain" />
                            </template>
                            <template x-if="!imagePreview">
                                <span class="material-symbols-rounded text-gray-muted text-4xl">image</span>
                            </template>
                        </div>
                        <div class="space-y-2">
                            <!-- File input -->
                            <label class="block cursor-pointer bg-green-light border border-primary/20 hover:bg-primary hover:text-white text-primary text-xs font-semibold px-3 py-2 text-center rounded-xl transition-colors">
                                <input 
                                    type="file" 
                                    name="image_file" 
                                    accept="image/*" 
                                    class="hidden" 
                                    @change="const file = $event.target.files[0]; if(file) { imagePreview = URL.createObjectURL(file); }"
                                />
                                Pilih File Foto
                            </label>
                            <p class="text-center text-[10px] text-gray-muted font-bold">ATAU masukkan URL:</p>
                            <input 
                                type="text" 
                                name="image_url" 
                                x-model="editImageUrl"
                                @input="imagePreview = editImageUrl"
                                class="w-full rounded-xl border border-gray-light px-3 py-1.5 text-xs text-gray-dark focus:outline-none focus:border-primary" 
                                placeholder="https://unsplash.com/..."
                            />
                        </div>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-bold text-gray-muted uppercase tracking-wider mb-1">Deskripsi</label>
                    <textarea 
                        name="description" 
                        rows="3"
                        x-model="editDescription"
                        class="w-full rounded-xl border border-gray-light px-4 py-2 text-sm text-gray-dark focus:outline-none focus:border-primary resize-none" 
                        placeholder="Detail keterangan buah segar..."
                    ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 justify-end pt-3 border-t border-bg-light">
                    <button 
                        type="button" 
                        @click="modalOpen = false" 
                        class="px-4 py-2.5 rounded-xl border border-gray-light text-gray-muted hover:text-gray-dark font-semibold text-sm cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-hover active:bg-primary-active text-white font-semibold text-sm cursor-pointer shadow-soft hover:shadow-soft-hover"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection