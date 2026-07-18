@extends('layouts.app')

@section('title', 'Status Pesanan - Syila Buah')

@section('content')
@php
    // Mengambil status pesanan ter-update dari database secara riil
    $status = $order->status ?? 'Menunggu Pembayaran';
    
    // Menghitung sisa waktu pembayaran secara riil (15 menit = 900 detik) sejak pesanan dibuat
    $createdAt = isset($order->created_at) ? \Carbon\Carbon::parse($order->created_at) : now();
    $diffInSeconds = now()->diffInSeconds($createdAt, false);
    $remainingSeconds = max(0, 900 - abs($diffInSeconds));
@endphp
{{-- Inisialisasi AlpineJS dengan mengaitkan status dan sisa waktu pembayaran secara dinamis --}}
<div class="max-w-3xl mx-auto px-6 py-8" x-data="{ 
    currentStatus: '{{ $status }}', // Mengikat status pesanan langsung dari kolom status di database
    countdown: {{ $remainingSeconds }}, // Mengikat sisa waktu hitung mundur riil dari database
    get formattedTime() {
        // Memformat sisa detik menjadi format Menit:Detik (MM:SS)
        const mm = String(Math.floor(this.countdown / 60)).padStart(2, '0');
        const ss = String(this.countdown % 60).padStart(2, '0');
        return `${mm}:${ss}`;
    },
    init() {
        // Melakukan pengurangan hitung mundur setiap 1 detik
        setInterval(() => {
            if (this.countdown > 0) this.countdown--;
        }, 1000);
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
        
        {{-- Tampilan status riil dari database. Panel simulasi toggler status dihapus untuk mode produksi --}}

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

    <!-- Help Banner / Bantuan Layanan Pelanggan -->
    {{-- Menghubungkan pembeli langsung ke admin WhatsApp dengan format pesan berisi invoice --}}
    <div class="mt-8 bg-white rounded-2xl border border-gray-light p-6 text-center hover:shadow-soft transition-all">
        <h4 class="font-bold text-gray-dark text-sm mb-1">Ada kendala dalam pesanan Anda?</h4>
        <p class="text-xs text-gray-muted leading-relaxed mb-4">Hubungi layanan pelanggan kami, kami siap membantu Anda.</p>
        @php
            // Menyusun format pesan WhatsApp dinamis
            $waMessage = rawurlencode("Halo Admin Syila Buah, saya ingin bertanya tentang pesanan saya dengan nomor Invoice: " . ($order->invoice_no ?? '') . ". Mohon bantuannya.");
        @endphp
        <a href="https://wa.me/6281234567890?text={{ $waMessage }}" target="_blank" class="inline-flex items-center justify-center gap-2 font-semibold rounded-xl border border-primary text-primary hover:bg-green-light px-5 py-2.5 text-xs transition-all cursor-pointer shadow-sm">
            <span class="material-symbols-rounded text-sm">support_agent</span> Hubungi Admin (WhatsApp)
        </a>
    </div>
</div>
@endsection
