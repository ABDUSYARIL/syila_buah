@extends('layouts.admin')

@section('title', 'Manajemen Stok - Admin Syila Buah')

@section('content')
<div class="space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-dark">Manajemen Stok</h1>
        <p class="text-sm text-gray-muted">Catat stok masuk dan lakukan penyesuaian stok buah secara akurat.</p>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-light border border-primary/20 text-primary font-semibold text-sm flex items-center gap-2 shadow-sm animate-float">
            <span class="material-symbols-rounded text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Low Stock Warning (Pemberitahuan Stok Rendah) -->
    {{-- Menghitung jumlah produk yang stoknya menipis (< 50) secara dinamis dari database --}}
    @php
        $lowStockProductsCount = $products->where('stock', '<', 50)->count();
    @endphp
    {{-- Peringatan hanya tampil jika ada produk yang stoknya di bawah batas minimal --}}
    @if($lowStockProductsCount > 0)
        {{-- Alert peringatan stok rendah yang dapat diklik untuk menuju halaman Dashboard --}}
        {{-- Diarahkan ke bagian 'Peringatan Stok' di dashboard agar admin bisa langsung cek dan isi stok --}}
        <a href="{{ route('admin.dashboard') }}#peringatan-stok" class="block p-4 rounded-xl bg-[#FFF3E0] border border-accent/20 text-accent font-medium text-sm flex items-start gap-3 shadow-sm hover:opacity-90 hover:shadow-soft transition-all duration-300">
            <span class="material-symbols-rounded text-lg mt-0.5 flex-shrink-0 animate-bounce">notifications_active</span>
            <div>
                <span class="font-bold">Peringatan Stok Menipis:</span> Terdapat <span class="font-bold text-[#E65100]">{{ $lowStockProductsCount }} produk</span> dengan stok di bawah batas minimal (50 unit). Klik di sini untuk melihat daftar produk di Dashboard.
            </div>
        </a>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form Stok Masuk (Penambahan) -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3 flex items-center gap-2">
                <span class="material-symbols-rounded text-primary">add_circle</span> Stok Masuk
            </h3>
            
            <form action="{{ route('admin.stock.add') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-dark">Pilih Produk</label>
                        <select name="product_id" required class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none cursor-pointer">
                            @foreach($products as $p)
                                <option value="{{ $p['id'] }}">{{ $p['name'] }} (Sisa: {{ $p['stock'] }} {{ $p['unit'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-dark">Jumlah Masuk</label>
                        <input type="number" name="qty" min="1" required placeholder="Contoh: 50" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-dark">Pemasok (Supplier)</label>
                        <div class="relative group">
                            <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-gray-muted text-lg transition-colors group-focus-within:text-primary">group</span>
                            <input type="text" name="supplier" required placeholder="Contoh: Tani Makmur" class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-light bg-white text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-dark">Harga Barang Datang (Rp)</label>
                        <input type="number" name="purchase_price" required placeholder="Contoh: 15000" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-gray-dark">Catatan Tambahan</label>
                    <input type="text" name="notes" placeholder="Contoh: Buah segar dari kebun ciwidey..." class="w-full rounded-xl border border-gray-light bg-white px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all duration-300 select-none bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-4 py-2.5 text-sm cursor-pointer shadow-soft hover:shadow-soft-hover">
                    <span class="material-symbols-rounded text-sm">add</span> Simpan Stok Masuk
                </button>
            </form>
        </div>

        <!-- Form Penyesuaian Stok (Koreksi) -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3 flex items-center gap-2">
                <span class="material-symbols-rounded text-accent">autorenew</span> Penyesuaian Stok (Opname)
            </h3>
            
            <form action="{{ route('admin.stock.adjust') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-dark">Pilih Produk</label>
                        <select name="product_id" required class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all appearance-none cursor-pointer">
                            @foreach($products as $p)
                                <option value="{{ $p['id'] }}">{{ $p['name'] }} (Sisa: {{ $p['stock'] }} {{ $p['unit'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-dark">Jumlah Stok Sebenarnya</label>
                        <input type="number" name="qty" min="0" required placeholder="Contoh: 145" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-gray-dark">Alasan Penyesuaian</label>
                    <select name="type" required>
                        <option value="Buah Busuk/Rusak">Buah Busuk / Rusak</option>
                        <option value="Selisih Perhitungan Opname">Selisih Perhitungan Opname</option>
                        <option value="Retur Pelanggan">Retur Pelanggan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-gray-dark">Keterangan Detail</label>
                    <input type="text" name="notes" placeholder="Contoh: Ditemukan 5 kg buah apel membusuk di gudang..." class="w-full rounded-xl border border-gray-light bg-white px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" />
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all duration-300 select-none bg-accent text-white hover:bg-accent-hover active:bg-accent-active active:translate-y-0.5 active:shadow-inner px-4 py-2.5 text-sm cursor-pointer shadow-soft hover:shadow-soft-hover">
                    <span class="material-symbols-rounded text-sm">autorenew</span> Simpan Penyesuaian
                </button>
            </form>
        </div>
    </div>

    {{-- ===================================================================
         RIWAYAT AKTIVITAS LOG STOK — 3 Tab Terpisah
         Tab 1: Stok Masuk   (log pengisian stok dari supplier)
         Tab 2: Stok Keluar  (log pengurangan stok dari penjualan/penyesuaian)
         Tab 3: Stok Saat Ini (ringkasan stok terkini setiap produk dari database)
         Riwayat yang sudah lebih dari 1 bulan akan otomatis dihapus oleh sistem.
         =================================================================== --}}
    <div id="riwayat-log-stok" class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden hover:shadow-soft-hover transition-all duration-300">
        <div class="p-6 border-b border-bg-light flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-dark text-base flex items-center gap-2">
                    Riwayat Aktivitas Log Stok
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-green-light text-primary border border-primary/20">
                        Auto-Hapus: {{ $retentionDays == 7 ? '1 Minggu' : ($retentionDays == 30 ? '1 Bulan' : ($retentionDays == 90 ? '3 Bulan' : 'Nonaktif')) }}
                    </span>
                </h3>
                <p class="text-xs text-gray-muted mt-1">Data terintegrasi langsung dengan database. Riwayat otomatis dihapus berdasarkan durasi yang Anda tentukan.</p>
            </div>

            <!-- Modal Button / Trigger Pembersihan Riwayat Stok -->
            <div x-data="{ openClearModal: false }">
                <button @click="openClearModal = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-xs font-semibold border border-red-200 transition-all cursor-pointer shadow-sm">
                    <span class="material-symbols-rounded text-sm">delete_sweep</span>
                    Atur & Hapus Riwayat
                </button>

                <!-- Modal Form Pembersihan Riwayat Stok -->
                <div x-show="openClearModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" x-transition.opacity>
                    <div @click.outside="openClearModal = false" class="bg-white rounded-2xl shadow-xl border border-gray-light w-full max-w-md p-6 space-y-5">
                        <div class="flex items-center justify-between border-b border-bg-light pb-3">
                            <h4 class="font-bold text-gray-dark text-base flex items-center gap-2">
                                <span class="material-symbols-rounded text-red-500">auto_delete</span>
                                Pembersihan Riwayat Log Stok
                            </h4>
                            <button @click="openClearModal = false" class="text-gray-muted hover:text-gray-dark text-lg cursor-pointer">&times;</button>
                        </div>

                        <form action="{{ route('admin.stock.clear-history') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-dark mb-1">Target Log Riwayat Stok</label>
                                <select name="stock_type" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10">
                                    <option value="semua" selected>Semua Log (Stok Masuk & Stok Keluar)</option>
                                    <option value="masuk">Hanya Stok Masuk (Penambahan Stok)</option>
                                    <option value="keluar">Hanya Stok Keluar (Penjualan / Penyesuaian)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-dark mb-1">Rentang Waktu Hapus Riwayat</label>
                                <select name="period" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10">
                                    <option value="7_days">Lebih dari 1 Minggu (7 Hari)</option>
                                    <option value="30_days" selected>Lebih dari 1 Bulan (30 Hari)</option>
                                    <option value="90_days">Lebih dari 3 Bulan (90 Hari)</option>
                                    <option value="all">Hapus Semua Riwayat Sesuai Target</option>
                                </select>
                                <p class="text-[11px] text-gray-muted mt-1">Data log stok yang lebih tua dari rentang waktu di atas akan dihapus dari sistem.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-dark mb-1">Simpan Pengaturan Otomatis (Retensi Log)</label>
                                <select name="auto_retention" class="w-full rounded-xl border border-gray-light bg-white px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10">
                                    <option value="7" {{ $retentionDays == 7 ? 'selected' : '' }}>Otomatis Hapus > 1 Minggu</option>
                                    <option value="30" {{ $retentionDays == 30 ? 'selected' : '' }}>Otomatis Hapus > 1 Bulan</option>
                                    <option value="90" {{ $retentionDays == 90 ? 'selected' : '' }}>Otomatis Hapus > 3 Bulan</option>
                                    <option value="0" {{ $retentionDays == 0 ? 'selected' : '' }}>Nonaktifkan Hapus Otomatis</option>
                                </select>
                            </div>

                            <div class="pt-3 border-t border-bg-light flex items-center justify-end gap-2">
                                <button type="button" @click="openClearModal = false" class="px-4 py-2 text-xs font-semibold rounded-xl border border-gray-light text-gray-dark hover:bg-bg-light cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus riwayat log stok sesuai rentang waktu yang dipilih?');" class="px-4 py-2 text-xs font-bold rounded-xl bg-red-500 text-white hover:bg-red-600 transition-all cursor-pointer shadow-soft">
                                    Hapus Riwayat Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Navigator — menggunakan URL query parameter 'tab' agar state tab tetap saat paginasi --}}
        <div class="flex border-b border-bg-light px-6 pt-3 gap-1">
            @foreach([
                ['key' => 'masuk',    'label' => 'Stok Masuk',    'icon' => 'arrow_downward', 'color' => 'text-primary',    'count' => $stockMasuk->total()],
                ['key' => 'keluar',   'label' => 'Stok Keluar',   'icon' => 'arrow_upward',   'color' => 'text-red-500',    'count' => $stockKeluar->total()],
                ['key' => 'saat_ini', 'label' => 'Stok Saat Ini', 'icon' => 'inventory_2',    'color' => 'text-accent',     'count' => $stockSaatIni->total()],
            ] as $tab)
                {{-- Setiap tab memiliki link ke URL yang sama dengan parameter tab berbeda --}}
                <a href="{{ request()->fullUrlWithQuery(['tab' => $tab['key']]) }}"
                   class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold rounded-t-xl border-b-2 transition-all duration-200 whitespace-nowrap
                    {{ $activeTab === $tab['key']
                        ? 'border-primary text-primary bg-green-light/40'
                        : 'border-transparent text-gray-muted hover:text-primary hover:border-primary/30' }}">
                    <span class="material-symbols-rounded text-sm {{ $tab['color'] }}">{{ $tab['icon'] }}</span>
                    {{ $tab['label'] }}
                    {{-- Badge jumlah total data pada setiap tab --}}
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold
                        {{ $activeTab === $tab['key'] ? 'bg-primary text-white' : 'bg-bg-light text-gray-muted' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- ===== TAB 1: STOK MASUK ===== --}}
        @if($activeTab === 'masuk')
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-bg-light">
                        <tr class="border-b border-gray-light text-left text-gray-muted font-semibold text-xs">
                            <th class="py-3 px-6 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Produk</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Jenis</th>
                            <th class="py-3 px-4 uppercase tracking-wider text-right">Jumlah Masuk</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockMasuk as $log)
                            <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                                <td class="py-3.5 px-6 text-gray-muted text-xs">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3.5 px-4 font-bold text-gray-dark">{{ $log->product->name ?? 'Produk' }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-light text-primary">
                                        <span class="material-symbols-rounded text-[10px]">arrow_downward</span> {{ $log->transaction_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-primary">
                                    +{{ $log->qty }} {{ $log->product->unit ?? '' }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $log->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-muted text-xs font-semibold">
                                    <span class="material-symbols-rounded text-2xl block mb-1">inventory</span>
                                    Belum ada log stok masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginasi Tab Stok Masuk --}}
            @if($stockMasuk->hasPages())
                <div class="px-6 py-4 border-t border-bg-light flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-xs text-gray-muted">Menampilkan {{ $stockMasuk->firstItem() }}–{{ $stockMasuk->lastItem() }} dari {{ $stockMasuk->total() }} log</p>
                    <div class="flex items-center gap-1">
                        @if($stockMasuk->onFirstPage())
                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">‹ Sebelumnya</span>
                        @else
                            <a href="{{ $stockMasuk->previousPageUrl() }}&tab=masuk" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">‹ Sebelumnya</a>
                        @endif
                        @foreach($stockMasuk->getUrlRange(1, $stockMasuk->lastPage()) as $page => $url)
                            @if($page == $stockMasuk->currentPage())
                                <span class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center bg-primary text-white shadow-soft">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&tab=masuk" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-white border border-gray-light text-gray-muted hover:border-primary hover:text-primary transition-all">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($stockMasuk->hasMorePages())
                            <a href="{{ $stockMasuk->nextPageUrl() }}&tab=masuk" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">Berikutnya ›</a>
                        @else
                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">Berikutnya ›</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        {{-- ===== TAB 2: STOK KELUAR ===== --}}
        @if($activeTab === 'keluar')
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-bg-light">
                        <tr class="border-b border-gray-light text-left text-gray-muted font-semibold text-xs">
                            <th class="py-3 px-6 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Produk</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Jenis</th>
                            <th class="py-3 px-4 uppercase tracking-wider text-right">Jumlah Keluar</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockKeluar as $log)
                            <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                                <td class="py-3.5 px-6 text-gray-muted text-xs">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3.5 px-4 font-bold text-gray-dark">{{ $log->product->name ?? 'Produk' }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600">
                                        <span class="material-symbols-rounded text-[10px]">arrow_upward</span> {{ $log->transaction_type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-red-500">
                                    {{ $log->qty }} {{ $log->product->unit ?? '' }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $log->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-muted text-xs font-semibold">
                                    <span class="material-symbols-rounded text-2xl block mb-1">trending_down</span>
                                    Belum ada log stok keluar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginasi Tab Stok Keluar --}}
            @if($stockKeluar->hasPages())
                <div class="px-6 py-4 border-t border-bg-light flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-xs text-gray-muted">Menampilkan {{ $stockKeluar->firstItem() }}–{{ $stockKeluar->lastItem() }} dari {{ $stockKeluar->total() }} log</p>
                    <div class="flex items-center gap-1">
                        @if($stockKeluar->onFirstPage())
                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">‹ Sebelumnya</span>
                        @else
                            <a href="{{ $stockKeluar->previousPageUrl() }}&tab=keluar" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">‹ Sebelumnya</a>
                        @endif
                        @foreach($stockKeluar->getUrlRange(1, $stockKeluar->lastPage()) as $page => $url)
                            @if($page == $stockKeluar->currentPage())
                                <span class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center bg-primary text-white shadow-soft">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&tab=keluar" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-white border border-gray-light text-gray-muted hover:border-primary hover:text-primary transition-all">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($stockKeluar->hasMorePages())
                            <a href="{{ $stockKeluar->nextPageUrl() }}&tab=keluar" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">Berikutnya ›</a>
                        @else
                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">Berikutnya ›</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        {{-- ===== TAB 3: STOK SAAT INI ===== --}}
        @if($activeTab === 'saat_ini')
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-bg-light">
                        <tr class="border-b border-gray-light text-left text-gray-muted font-semibold text-xs">
                            <th class="py-3 px-6 uppercase tracking-wider">Produk</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Kategori</th>
                            <th class="py-3 px-4 uppercase tracking-wider text-right">Stok Sekarang</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Status Stok</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockSaatIni as $p)
                            <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-gray-dark">{{ $p->name }}</td>
                                <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $p->category->name ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-right font-bold {{ $p->stock <= 20 ? 'text-red-500' : ($p->stock <= 50 ? 'text-accent' : 'text-primary') }}">
                                    {{ $p->stock }} {{ $p->unit }}
                                </td>
                                <td class="py-3.5 px-4">
                                    {{-- Badge warna berdasarkan level stok --}}
                                    @if($p->stock <= 20)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600">
                                            <span class="material-symbols-rounded text-[10px]">warning</span> Kritis
                                        </span>
                                    @elseif($p->stock <= 50)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#FFF3E0] text-accent">
                                            <span class="material-symbols-rounded text-[10px]">info</span> Menipis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-light text-primary">
                                            <span class="material-symbols-rounded text-[10px]">check_circle</span> Aman
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $p->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-muted text-xs font-semibold">
                                    Belum ada data produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginasi Tab Stok Saat Ini --}}
            @if($stockSaatIni->hasPages())
                <div class="px-6 py-4 border-t border-bg-light flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-xs text-gray-muted">Menampilkan {{ $stockSaatIni->firstItem() }}–{{ $stockSaatIni->lastItem() }} dari {{ $stockSaatIni->total() }} produk</p>
                    <div class="flex items-center gap-1">
                        @if($stockSaatIni->onFirstPage())
                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">‹ Sebelumnya</span>
                        @else
                            <a href="{{ $stockSaatIni->previousPageUrl() }}&tab=saat_ini" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">‹ Sebelumnya</a>
                        @endif
                        @foreach($stockSaatIni->getUrlRange(1, $stockSaatIni->lastPage()) as $page => $url)
                            @if($page == $stockSaatIni->currentPage())
                                <span class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center bg-primary text-white shadow-soft">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&tab=saat_ini" class="w-8 h-8 rounded-lg text-xs font-semibold flex items-center justify-center bg-white border border-gray-light text-gray-muted hover:border-primary hover:text-primary transition-all">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($stockSaatIni->hasMorePages())
                            <a href="{{ $stockSaatIni->nextPageUrl() }}&tab=saat_ini" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-gray-light text-gray-dark hover:border-primary hover:text-primary transition-all">Berikutnya ›</a>
                        @else
                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-bg-light text-gray-muted cursor-not-allowed select-none">Berikutnya ›</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
