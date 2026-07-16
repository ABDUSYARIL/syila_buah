@extends('layouts.admin')

@section('title', 'Laporan Penjualan - Admin Syila Buah')

@section('content')
<div x-data="{ period: @json($period) }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-dark">Laporan Penjualan</h1>
            <p class="text-sm text-gray-muted">Analitik dan statistik bisnis Syila Buah</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl border border-primary text-primary hover:bg-green-light px-3.5 py-2 text-xs cursor-pointer transition-colors shadow-sm">
                <span class="material-symbols-rounded text-sm">print</span> Cetak PDF
            </button>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="flex gap-2 mb-6">
        @foreach(['Harian', 'Bulanan', 'Tahunan'] as $p)
            <a href="{{ route('admin.reports', ['period' => $p]) }}"
                class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all border select-none"
                :class="period === '{{ $p }}' ? 'bg-primary text-white border-primary shadow-soft' : 'bg-white border-gray-light text-gray-muted hover:border-primary hover:text-primary'"
            >
                {{ $p }}
            </a>
        @endforeach
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($stats as $stat)
            <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-5 hover:shadow-soft-hover transition-all duration-300">
                <p class="text-xs text-gray-muted mb-1 font-medium">{{ $stat['label'] }}</p>
                <p class="text-xl font-extrabold {{ $stat['color'] }} leading-none tracking-tight">{{ $stat['value'] }}</p>
                <p class="text-[10px] text-primary font-bold mt-2">{{ $stat['change'] }} vs periode sebelumnya</p>
            </div>
        @endforeach
    </div>

    <!-- Graphs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-4">
        <!-- Revenue Bar Chart -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-4">Pendapatan {{ $period }}</h3>
            <div class="relative h-56 w-full">
                <canvas id="revBarChart"></canvas>
            </div>
        </div>
        
        <!-- Orders Area Chart -->
        <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
            <h3 class="font-bold text-gray-dark text-base mb-4 border-b border-bg-light pb-4">Jumlah Pesanan {{ $period }}</h3>
            <div class="relative h-56 w-full">
                <canvas id="ordersAreaChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const salesData = {!! json_encode($salesData) !!};

    const labels = salesData.map(item => item.month);
    const revenues = salesData.map(item => item.revenue);
    const orders = salesData.map(item => item.orders);

    const revCtx = document.getElementById('revBarChart');

    if(revCtx){
        new Chart(revCtx,{
            type:'bar',
            data:{
                labels:labels,
                datasets:[{
                    label:'Pendapatan',
                    data:revenues,
                    backgroundColor:'#4CAF50',
                    borderRadius:6
                }]
            }
        });
    }

    const orderCanvas = document.getElementById('ordersAreaChart');

    if(orderCanvas){

        const ctx = orderCanvas.getContext('2d');

        const gradient = ctx.createLinearGradient(0,0,0,220);
        gradient.addColorStop(0,'rgba(255,152,0,.2)');
        gradient.addColorStop(1,'rgba(255,152,0,0)');

        new Chart(ctx,{
            type:'line',
            data:{
                labels:labels,
                datasets:[{
                    label:'Jumlah Pesanan',
                    data:orders,
                    borderColor:'#FF9800',
                    backgroundColor:gradient,
                    fill:true,
                    tension:.4
                }]
            }
        });

    }

});
</script>
@endsection