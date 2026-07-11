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

    <!-- Alert Low Stock Warning -->
    <div class="p-4 rounded-xl bg-[#FFF3E0] border border-accent/20 text-accent font-medium text-sm flex items-start gap-3 shadow-sm">
        <span class="material-symbols-rounded text-lg mt-0.5 flex-shrink-0">info</span>
        <div>
            <span class="font-bold">Pemberitahuan:</span> Terdapat <span class="font-bold text-[#E65100]">3 produk</span> dengan stok di bawah batas minimal (50 unit). Disarankan untuk segera mencatat stok masuk.
        </div>
    </div>

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

    <!-- Riwayat Logs Stok -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden hover:shadow-soft-hover transition-all duration-300">
        <div class="p-6 border-b border-bg-light">
            <h3 class="font-bold text-gray-dark text-base">Riwayat Aktivitas Log Stok</h3>
            <p class="text-xs text-gray-muted mt-1">Daftar transaksi stok masuk dan koreksi stok opname terakhir.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg-light">
                    <tr class="border-b border-gray-light text-left text-gray-muted font-semibold text-xs">
                        <th class="py-3 px-6 uppercase tracking-wider">Tanggal</th>
                        <th class="py-3 px-4 uppercase tracking-wider">Produk</th>
                        <th class="py-3 px-4 uppercase tracking-wider">Jenis Aktivitas</th>
                        <th class="py-3 px-4 uppercase tracking-wider text-right">Jumlah</th>
                        <th class="py-3 px-4 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        ['date' => '02 Jul 2025 15:40', 'name' => 'Apel Fuji', 'type' => 'Stok Masuk', 'qty' => '+50 Kg', 'notes' => 'Supplier: Tani Jaya, Beli: Rp12.000/Kg'],
                        ['date' => '02 Jul 2025 14:10', 'name' => 'Stroberi', 'type' => 'Opname (Rusak)', 'qty' => '-3 Pack', 'notes' => 'Stroberi membusuk di chiller'],
                        ['date' => '01 Jul 2025 10:20', 'name' => 'Pisang Cavendish', 'type' => 'Stok Masuk', 'qty' => '+20 Sisir', 'notes' => 'Supplier: Agro Prima, Beli: Rp8.000/Sisir'],
                        ['date' => '30 Jun 2025 18:30', 'name' => 'Alpukat Mentega', 'type' => 'Opname (Selisih)', 'qty' => '-2 Kg', 'notes' => 'Selisih timbangan gudang']
                    ] as $log)
                        <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                            <td class="py-3.5 px-6 text-gray-muted text-xs">{{ $log['date'] }}</td>
                            <td class="py-3.5 px-4 font-bold text-gray-dark">{{ $log['name'] }}</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ str_starts_with($log['type'], 'Stok Masuk') ? 'bg-green-light text-primary' : 'bg-red-50 text-red-600' }}">
                                    {{ $log['type'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold {{ str_starts_with($log['qty'], '+') ? 'text-primary' : 'text-red-500' }}">{{ $log['qty'] }}</td>
                            <td class="py-3.5 px-4 text-gray-muted text-xs">{{ $log['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
