@extends('layouts.app')

@section('title', 'Status Pesanan - Syila Buah')

@section('content')
@php
    $status = $order->status ?? 'Menunggu Pembayaran';
@endphp
<div class="max-w-3xl mx-auto px-6 py-8" x-data="{ 
    currentStatus: '{{ $status }}',
    countdown: 14 * 60 + 52,
    get formattedTime() {
        const mm = String(Math.floor(this.countdown / 60)).padStart(2, '0');
        const ss = String(this.countdown % 60).padStart(2, '0');
        return `${mm}:${ss}`;
    },
    init() {
        setInterval(() => {
            if (this.countdown > 0) this.countdown--;
        }, 1000);

        // Auto-update status progression every 6 seconds
        setInterval(() => {
            if (this.currentStatus === 'Menunggu Pembayaran') {
                // Keep waiting for payment or trigger to verification
            } else if (this.currentStatus === 'Menunggu Verifikasi') {
                this.currentStatus = 'Diproses';
            } else if (this.currentStatus === 'Diproses') {
                this.currentStatus = 'Dikirim';
            } else if (this.currentStatus === 'Dikirim') {
                this.currentStatus = 'Selesai';
            }
        }, 6000);
    }
}">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight">Status Pesanan</h1>
            <p class="text-sm text-gray-muted mt-1">Invoice: <span class="font-mono font-bold text-gray-dark">{{ $order->invoice_no ?? 'SB-240703-006' }}</span></p>
        </div>
        <a href="{{ route('order.detail', ['order_id' => $order->id ?? 1]) }}" class="inline-flex items-center gap-1.5 text-sm text-primary font-bold hover:underline">
            Detail Pesanan <span class="material-symbols-rounded text-sm">arrow_forward</span>
        </a>
    </div>

    <!-- Timeline Wrapper Card -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-8 space-y-8 hover:shadow-soft-hover transition-all duration-300">
        
        <!-- Live Status Toggler for Mock Demonstration -->
        <div class="bg-bg-light rounded-xl p-3 border border-gray-light flex flex-wrap gap-2 items-center justify-center">
            <span class="text-xs font-bold text-gray-muted uppercase tracking-wider mr-2">Simulasi Status:</span>
            @foreach(['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'] as $st)
                <button 
                    @click="currentStatus = '{{ $st }}'; if('{{ $st }}' === 'Dibatalkan') { alert('Pembayaran melewati batas 15 menit! Pesanan otomatis DIBATALKAN dan stok dikembalikan ke gudang.') }"
                    class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all border cursor-pointer"
                    :class="currentStatus === '{{ $st }}' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-gray-muted border-gray-light hover:border-primary/50'"
                >
                    {{ $st }}
                </button>
            @endforeach
        </div>

        <!-- Countdown Timer (Show only when status is 'Menunggu Pembayaran') -->
        <div x-show="currentStatus === 'Menunggu Pembayaran'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             class="bg-accent/10 border-2 border-accent rounded-xl p-5 text-center flex flex-col items-center">
            <span class="material-symbols-rounded text-accent text-3xl mb-1.5 animate-pulse">schedule</span>
            <p class="text-xs text-accent font-bold uppercase tracking-wider">Selesaikan Pembayaran Dalam</p>
            <p class="text-3xl font-black text-accent tracking-tight mt-1" x-text="formattedTime"></p>
        </div>

        <!-- Order Cancelled Banner -->
        <div x-show="currentStatus === 'Dibatalkan'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             class="bg-red-50 border-2 border-red-500 rounded-xl p-5 text-center flex flex-col items-center">
            <span class="material-symbols-rounded text-red-500 text-3xl mb-1.5">cancel</span>
            <p class="text-sm font-bold text-red-500 uppercase tracking-wider">Pesanan Dibatalkan</p>
            <p class="text-xs text-gray-muted leading-relaxed mt-1">Pembayaran melewati batas waktu 15 menit. Stok produk otomatis dikembalikan ke gudang.</p>
        </div>

        <!-- Vertical/Horizontal Timeline -->
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-8 md:gap-4 pt-4" :class="currentStatus === 'Dibatalkan' ? 'opacity-40 pointer-events-none' : ''">
            <!-- Background bar -->
            <div class="hidden md:block absolute left-4 right-4 top-1/2 -translate-y-1/2 h-1 bg-gray-light z-0"></div>
            
            @php
                $steps = [
                    ['id' => 'buat', 'label' => 'Pesanan Dibuat', 'icon' => 'assignment_turned_in', 'trigger' => ['Pesanan Dibuat', 'Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai']],
                    ['id' => 'bayar', 'label' => 'Menunggu Pembayaran', 'icon' => 'payment', 'trigger' => ['Menunggu Pembayaran', 'Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai']],
                    ['id' => 'verifikasi', 'label' => 'Menunggu Verifikasi', 'icon' => 'verified_user', 'trigger' => ['Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai']],
                    ['id' => 'proses', 'label' => 'Diproses', 'icon' => 'local_mall', 'trigger' => ['Diproses', 'Dikirim', 'Selesai']],
                    ['id' => 'kirim', 'label' => 'Dikirim', 'icon' => 'local_shipping', 'trigger' => ['Dikirim', 'Selesai']],
                    ['id' => 'selesai', 'label' => 'Selesai', 'icon' => 'check_circle', 'trigger' => ['Selesai']]
                ];
            @endphp

            @foreach ($steps as $index => $step)
                <div class="relative z-10 flex md:flex-col items-center gap-4 md:gap-2 flex-1 text-left md:text-center w-full">
                    <!-- Icon Bubble -->
                    <div 
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500 shadow-sm border-2"
                        :class="[{!! json_encode($step['trigger']) !!}].flat().includes(currentStatus) 
                            ? 'bg-primary text-white border-primary scale-110 shadow-md' 
                            : 'bg-white text-gray-300 border-gray-light'"
                    >
                        <span class="material-symbols-rounded text-base">{{ $step['icon'] }}</span>
                    </div>
                    <!-- Label -->
                    <div>
                        <p class="text-xs font-bold leading-tight"
                           :class="[{!! json_encode($step['trigger']) !!}].flat().includes(currentStatus) ? 'text-gray-dark' : 'text-gray-300'">
                            {{ $step['label'] }}
                        </p>
                        <p class="text-[10px] text-gray-muted mt-0.5" x-show="currentStatus === '{{ $step['label'] }}'">Saat ini</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Help Banner -->
    <div class="mt-8 bg-white rounded-2xl border border-gray-light p-6 text-center hover:shadow-soft transition-all">
        <h4 class="font-bold text-gray-dark text-sm mb-1">Ada kendala dalam pesanan Anda?</h4>
        <p class="text-xs text-gray-muted leading-relaxed mb-4">Hubungi layanan pelanggan kami siap membantu 24/7.</p>
        <button onclick="alert('Membuka chat WhatsApp...')" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl border border-primary text-primary hover:bg-green-light px-4 py-2 text-xs transition-colors cursor-pointer">
            <span class="material-symbols-rounded text-sm">support_agent</span> Hubungi Customer Service
        </button>
    </div>
</div>
@endsection
