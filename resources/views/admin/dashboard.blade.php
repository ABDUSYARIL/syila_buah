@extends('layouts.admin')

@section('title', 'Admin Dashboard - Syila Buah')

@section('content')
<div class="space-y-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-dark">Dashboard</h1>
        <p class="text-sm text-gray-muted">Ikhtisar data penjualan dan stok buah Anda hari ini.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Total Pendapatan', 'value' => 'Rp 128,5 Jt', 'change' => '+18%', 'icon' => 'payments', 'color' => 'bg-green-light text-primary'],
                ['label' => 'Total Pesanan', 'value' => '934', 'change' => '+23%', 'icon' => 'local_mall', 'color' => 'bg-blue-50 text-blue-600'],
                ['label' => 'Produk Terdaftar', 'value' => '12', 'change' => 'Stabil', 'icon' => 'inventory_2', 'color' => 'bg-[#FFF3E0] text-accent'],
                ['label' => 'Total Admin', 'value' => '3', 'change' => 'Aktif', 'icon' => 'group', 'color' => 'bg-purple-50 text-purple-600'],
            ];
        @endphp
        @foreach($stats as $c)
            <div class="bg-white rounded-2xl p-5 border border-gray-light shadow-soft hover:shadow-soft-hover transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-muted font-medium mb-1">{{ $c['label'] }}</p>
                    <p class="text-xl font-extrabold text-gray-dark leading-none tracking-tight">{{ $c['value'] }}</p>
                    <p class="text-[10px] text-primary font-bold mt-1.5">{{ $c['change'] }} vs bulan lalu</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $c['color'] }} shadow-sm">
                    <span class="material-symbols-rounded text-xl">{{ $c['icon'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts & Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue line-area chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <div class="flex items-center justify-between mb-4 border-b border-bg-light pb-4">
                <h3 class="font-bold text-gray-dark text-base">Tren Pendapatan Bulanan</h3>
                <span class="text-xs text-gray-muted font-medium">Bulan berjalan</span>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="revChart"></canvas>
            </div>
        </div>

        <!-- Doughnut chart for top categories -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <div class="flex items-center justify-between mb-4 border-b border-bg-light pb-4">
                <h3 class="font-bold text-gray-dark text-base">Top Produk</h3>
                <span class="text-xs text-gray-muted font-medium">Berdasarkan unit</span>
            </div>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-soft border border-gray-light overflow-hidden hover:shadow-soft-hover transition-all duration-300">
            <div class="p-6 border-b border-bg-light flex items-center justify-between">
                <h3 class="font-bold text-gray-dark text-base">Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders') }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-0.5">
                    Lihat Semua <span class="material-symbols-rounded text-sm">chevron_right</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-bg-light">
                        <tr class="border-b border-gray-light text-left text-gray-muted font-semibold text-xs">
                            <th class="py-3 px-4 uppercase tracking-wider">Invoice</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Pelanggan</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Total</th>
                            <th class="py-3 px-4 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders->take(4) as $o)
                            <tr class="border-b border-bg-light hover:bg-bg-light/50 transition-colors">
                                <td class="py-3 px-4 font-mono text-xs font-bold text-gray-dark">{{ $o->invoice_no }}</td>
                                <td class="py-3 px-4 font-medium text-gray-dark">{{ $o->user->name ?? 'Pelanggan' }}</td>
                                <td class="py-3 px-4 font-bold text-primary">{{ \App\Http\Controllers\ProductData::rp($o->total) }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold 
                                        {{ $o->status === 'Selesai' ? 'bg-green-light text-primary' : ($o->status === 'Dikirim' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600') }}">
                                        {{ $o->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok Tip Alert -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-3 flex items-center gap-1.5">
                <span class="material-symbols-rounded text-primary">warning</span> Peringatan Stok
            </h3>
            <div class="space-y-3.5">
                @foreach([
                    ['name' => 'Anggur Merah', 'stock' => 35, 'unit' => 'Kg'],
                    ['name' => 'Buah Naga Merah', 'stock' => 40, 'unit' => 'Kg'],
                    ['name' => 'Semangka Merah', 'stock' => 45, 'unit' => 'Kg']
                ] as $low)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-orange-50/50 border border-accent/15">
                        <div>
                            <p class="text-xs font-bold text-gray-dark leading-tight">{{ $low['name'] }}</p>
                            <p class="text-[10px] text-gray-muted mt-0.5">Sisa Stok: <span class="font-bold text-accent">{{ $low['stock'] }} {{ $low['unit'] }}</span></p>
                        </div>
                        <a href="{{ route('admin.stock') }}" class="px-2.5 py-1.5 rounded-xl bg-accent text-white text-[10px] font-bold hover:bg-accent-hover transition-colors">
                            Isi Stok
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const labels = {!! json_encode(array_column($salesData, 'month')) !!};
        const revenue = {!! json_encode(array_column($salesData, 'revenue')) !!};
        
        // Line chart setup
        const revCtx = document.getElementById('revChart').getContext('2d');
        const gradient = revCtx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(76, 175, 80, 0.25)');
        gradient.addColorStop(1, 'rgba(76, 175, 80, 0)');
        
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: revenue,
                    borderColor: '#4CAF50',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#4CAF50',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000) + 'Jt';
                            }
                        }
                    }
                }
            }
        });

        // Top products doughnut chart setup
        const topProducts = {!! json_encode(array_column($topProducts, 'name')) !!};
        const sold = {!! json_encode(array_column($topProducts, 'sold')) !!};
        
        const topCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topCtx, {
            type: 'doughnut',
            data: {
                labels: topProducts,
                datasets: [{
                    data: sold,
                    backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#9C27B0', '#F44336'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10, family: 'Poppins' }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
