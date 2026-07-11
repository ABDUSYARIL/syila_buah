@extends('layouts.app')

@section('title', 'Pembayaran - Syila Buah')

@section('content')
@php
    $payment = $order->payment ?? new \App\Models\Payment(['method' => 'Transfer Bank']);
    $method = $payment->method;
@endphp
<div class="max-w-lg mx-auto px-6 py-8" x-data="{ 
    uploaded: false, 
    previewSrc: '',
    countdown: 15 * 60,
    init() {
        setInterval(() => {
            if (this.countdown > 0) this.countdown--;
        }, 1000);
    },
    get formattedTime() {
        const mm = String(Math.floor(this.countdown / 60)).padStart(2, '0');
        const ss = String(this.countdown % 60).padStart(2, '0');
        return `${mm}:${ss}`;
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight mt-3">Pembayaran</h1>
        <p class="text-sm text-gray-muted mt-1">Selesaikan transfer pembayaran Anda sesuai panduan di bawah.</p>
    </div>

    <!-- Countdown Timer & Warning Info -->
    <div class="bg-white rounded-2xl border-2 border-accent p-6 shadow-soft hover:shadow-soft-hover transition-all duration-300 text-center relative overflow-hidden mb-6">
        <div class="absolute -top-12 -left-12 w-24 h-24 bg-accent/5 rounded-full blur-xl"></div>
        <div class="flex items-center justify-center gap-1.5 text-accent mb-2">
            <span class="material-symbols-rounded text-lg">schedule</span>
            <span class="font-bold text-sm">Selesaikan Pembayaran Sebelum</span>
        </div>
        <p class="text-4xl font-black text-accent py-2 tracking-tight" x-text="formattedTime"></p>
        <p class="text-xs text-gray-muted leading-relaxed px-4">
            Pesanan akan dibatalkan otomatis apabila pembayaran tidak dilakukan sebelum batas waktu dan stok produk akan dikembalikan ke gudang.
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 space-y-6 hover:shadow-soft-hover transition-all duration-300">
        <!-- Invoice & Status -->
        <div class="bg-green-light rounded-xl p-4 flex justify-between items-center border border-primary/10">
            <div>
                <p class="text-xs text-gray-muted font-medium">Nomor Invoice</p>
                <p class="font-mono font-bold text-gray-dark mt-0.5">{{ $order->invoice_no }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-muted text-right font-medium">Metode Pembayaran</p>
                <p class="font-bold text-primary text-right mt-0.5">{{ $method }}</p>
            </div>
        </div>

        <!-- Total Pembayaran -->
        <div class="text-center border-b border-bg-light pb-6">
            <p class="text-xs text-gray-muted font-bold uppercase tracking-wider mb-1">Total Pembayaran</p>
            <p class="text-4xl font-black text-primary tracking-tight">{{ \App\Http\Controllers\ProductData::rp($order->total) }}</p>
        </div>

        <!-- CONDITIONAL DISPLAY: QRIS -->
        @if($method === 'QRIS')
            <div class="text-center bg-bg-light rounded-2xl p-5 border border-gray-light">
                <p class="font-bold text-gray-dark text-sm mb-3 flex items-center justify-center gap-1.5">
                    <span class="material-symbols-rounded text-primary text-lg">qr_code_2</span> Scan QRIS
                </p>
                <div class="inline-block border-2 border-primary rounded-2xl p-3 bg-white shadow-soft">
                    <div class="w-44 h-44 bg-bg-light rounded-xl flex flex-col items-center justify-center relative overflow-hidden">
                        <span class="material-symbols-rounded text-gray-dark text-6xl">qr_code</span>
                        <p class="absolute bottom-2 text-[10px] text-gray-muted font-bold tracking-widest">SYILABUAH</p>
                    </div>
                </div>
                <p class="text-[11px] text-gray-muted mt-3 font-medium">Mendukung pembayaran via DANA, GoPay, OVO, ShopeePay, & LinkAja.</p>
            </div>
        @endif

        <!-- CONDITIONAL DISPLAY: BANK TRANSFER -->
        @if($method === 'Transfer Bank')
            <div class="space-y-3">
                <p class="font-bold text-gray-dark text-sm flex items-center gap-1.5">
                    <span class="material-symbols-rounded text-primary text-lg">account_balance</span> Rekening Transfer Bank
                </p>
                @foreach([
                    ['bank' => 'BCA', 'no' => '1234-5678-90', 'name' => 'Syila Buah'],
                    ['bank' => 'Mandiri', 'no' => '0987-6543-21', 'name' => 'Syila Buah']
                ] as $r)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-light bg-white hover:border-primary/30 transition-all shadow-sm">
                        <div>
                            <p class="text-xs text-gray-muted font-semibold">{{ $r['bank'] }} · a/n {{ $r['name'] }}</p>
                            <p class="font-bold text-gray-dark text-base mt-0.5 tracking-tight">{{ $r['no'] }}</p>
                        </div>
                        <button type="button" @click="alert('Nomor rekening {{ $r['bank'] }} disalin!')" class="inline-flex items-center justify-center font-semibold rounded-xl border border-primary text-primary hover:bg-green-light px-3 py-1.5 text-xs transition-colors cursor-pointer">
                            Salin
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Upload Bukti Pembayaran -->
        <form action="{{ route('payment.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2 border-t border-bg-light">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="payment_method" value="{{ $method }}">
            
            <div>
                <p class="font-bold text-gray-dark text-sm mb-3 flex items-center gap-1.5">
                    <span class="material-symbols-rounded text-primary text-lg">receipt_long</span> Upload Bukti Pembayaran
                </p>
                
                <!-- Box Upload (Real upload with HP Camera support) -->
                <div x-show="!uploaded">
                    <label class="cursor-pointer border-2 border-dashed border-primary rounded-xl p-6 text-center hover:bg-green-light transition-all duration-300 transform hover:scale-[1.01] flex flex-col items-center justify-center">
                        <input type="file" name="proof_file" accept="image/*" class="hidden" 
                               @change="const file = $event.target.files[0]; if (file) { previewSrc = URL.createObjectURL(file); uploaded = true; }" />
                        <span class="material-symbols-rounded text-primary text-3xl mb-2 animate-bounce">photo_camera</span>
                        <p class="text-sm font-semibold text-primary">Ambil Foto / Pilih File Bukti</p>
                        <p class="text-[10px] text-gray-muted mt-1 font-medium">Mendukung kamera smartphone langsung & galeri gambar</p>
                    </label>
                </div>

                <!-- Preview Box -->
                <div x-show="uploaded" class="relative rounded-xl overflow-hidden border-2 border-primary shadow-soft p-4 bg-green-light/20 flex flex-col items-center">
                    <button type="button" @click="uploaded = false; previewSrc=''" class="absolute top-2 right-2 w-7 h-7 bg-white text-gray-muted hover:text-gray-dark rounded-full flex items-center justify-center shadow-soft cursor-pointer transition-colors">
                        <span class="material-symbols-rounded text-base">close</span>
                    </button>
                    
                    <div class="flex items-center gap-2 text-primary font-bold text-sm mb-3">
                        <span class="material-symbols-rounded text-lg">check_circle</span> Bukti Berhasil Terunggah
                    </div>
                    
                    <img :src="previewSrc" alt="Preview Bukti Pembayaran" class="max-w-[200px] h-auto rounded-lg shadow-sm border border-gray-light object-cover" />
                </div>
            </div>

            <!-- Submit button -->
            <button
                type="submit"
                class="w-full inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all duration-300 select-none bg-primary text-white hover:bg-primary-hover active:bg-primary-active active:translate-y-0.5 active:shadow-inner px-4 py-3 text-base shadow-soft hover:shadow-soft-hover cursor-pointer"
                :disabled="!uploaded"
                :class="!uploaded ? 'opacity-50 cursor-not-allowed' : ''"
            >
                <span class="material-symbols-rounded text-lg">send</span> Kirim Bukti Pembayaran
            </button>
        </form>
    </div>
</div>
@endsection
