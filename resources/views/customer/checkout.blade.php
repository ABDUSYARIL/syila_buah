@extends('layouts.app')

@section('title', 'Checkout - Syila Buah')

@section('content')
@php
    $subtotal = 0;
    foreach($cart as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }
@endphp
@php
    $subtotal = 0;
    foreach($cart as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }
@endphp

@php
    $subtotal = 0;
    foreach($cart as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }
@endphp

<div class="max-w-5xl mx-auto px-6 py-8" x-data="{
    shipMethod: 'Diantar',
    payMethod: 'transfer',
    items: @js(array_values($cart)),
    get subtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
    },
    get ongkir() {
        return this.shipMethod === 'Diantar' ? 15000 : 0;
    },
    get total() {
        return this.subtotal + this.ongkir;
    }
}">
    <div class="mb-8">
        <a href="{{ route('cart') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-muted hover:text-primary transition-colors">
            <span class="material-symbols-rounded text-base">arrow_back</span> Kembali ke Keranjang
        </a>
        <h1 class="text-3xl font-extrabold text-gray-dark tracking-tight mt-3">Checkout</h1>
        <p class="text-sm text-gray-muted mt-1">Konfirmasi pesanan buah segar Anda sebelum melakukan pembayaran.</p>
    </div>

    <form action="{{ route('payment') }}" method="GET" @submit="if(items.length === 0) { alert('Harap isi minimal 1 barang!'); $event.preventDefault(); }">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Alamat Pengiriman (Textarea) -->
                <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
                    <h3 class="font-bold text-gray-dark flex items-center gap-2 mb-4">
                        <span class="material-symbols-rounded text-primary">map</span> Alamat Pengiriman
                    </h3>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-gray-muted">Tulis alamat lengkap Anda secara manual:</label>
                        <textarea
                            name="shipping_address"
                            required
                            rows="4"
                            class="w-full rounded-xl border border-gray-light px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all resize-none bg-bg-light"
                            placeholder="Contoh: Jl. Melati No. 12, RT 03/RW 05, Kel. Sukasari, Kec. Cicendo, Kota Bandung, Jawa Barat 40171"
                        >Jl. Melati No. 12, RT 03/RW 05, Kel. Sukasari, Kec. Cicendo, Kota Bandung, Jawa Barat 40171</textarea>
                    </div>
                </div>

                <!-- Metode Pengiriman (Ambil di Tempat / Diantar) -->
                <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
                    <h3 class="font-bold text-gray-dark flex items-center gap-2 mb-4">
                        <span class="material-symbols-rounded text-primary">local_shipping</span> Metode Pengiriman
                    </h3>
                    <div class="space-y-4">
                        <!-- Option 1: Ambil di Tempat -->
                        <label class="block p-4 rounded-xl border-2 cursor-pointer transition-all duration-300"
                            :class="shipMethod === 'Ambil di Tempat' ? 'border-primary bg-green-light/30' : 'border-gray-light hover:border-green-200'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                        :class="shipMethod === 'Ambil di Tempat' ? 'border-primary' : 'border-gray-300'">
                                        <div class="w-2.5 h-2.5 rounded-full bg-primary" x-show="shipMethod === 'Ambil di Tempat'"></div>
                                    </div>
                                    <input type="radio" name="shipping_method" value="Ambil di Tempat" class="hidden" x-model="shipMethod" />
                                    <div>
                                        <p class="text-sm font-bold text-gray-dark">Ambil di Tempat</p>
                                        <p class="text-xs text-gray-muted mt-1">Pesanan dapat diambil langsung di Toko Syila Buah.</p>
                                    </div>
                                </div>
                                <span class="text-sm font-extrabold text-primary">Rp 0</span>
                            </div>
                        </label>

                        <!-- Option 2: Diantar -->
                        <label class="block p-4 rounded-xl border-2 cursor-pointer transition-all duration-300"
                            :class="shipMethod === 'Diantar' ? 'border-primary bg-green-light/30' : 'border-gray-light hover:border-green-200'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                        :class="shipMethod === 'Diantar' ? 'border-primary' : 'border-gray-300'">
                                        <div class="w-2.5 h-2.5 rounded-full bg-primary" x-show="shipMethod === 'Diantar'"></div>
                                    </div>
                                    <input type="radio" name="shipping_method" value="Diantar" class="hidden" x-model="shipMethod" />
                                    <div>
                                        <p class="text-sm font-bold text-gray-dark">Diantar</p>
                                        <p class="text-xs text-gray-muted mt-1">Estimasi pengiriman 1–6 jam.</p>
                                    </div>
                                </div>
                                <span class="text-sm font-extrabold text-primary">Rp 15.000</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
                    <h3 class="font-bold text-gray-dark flex items-center gap-2 mb-4">
                        <span class="material-symbols-rounded text-primary">payment</span> Metode Pembayaran
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Option 1: Transfer Bank -->
                        <label class="block p-4 rounded-xl border-2 cursor-pointer transition-all duration-300"
                            :class="payMethod === 'transfer' ? 'border-primary bg-green-light/30' : 'border-gray-light hover:border-green-200'">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                    :class="payMethod === 'transfer' ? 'border-primary' : 'border-gray-300'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary" x-show="payMethod === 'transfer'"></div>
                                </div>
                                <input type="radio" name="pay_method" value="transfer" class="hidden" x-model="payMethod" />
                                <div>
                                    <p class="text-sm font-bold text-gray-dark">Transfer Bank</p>
                                    <p class="text-[10px] text-gray-muted mt-0.5">Transfer manual ke BCA / Mandiri</p>
                                </div>
                            </div>
                        </label>

                        <!-- Option 2: QRIS -->
                        <label class="block p-4 rounded-xl border-2 cursor-pointer transition-all duration-300"
                            :class="payMethod === 'qris' ? 'border-primary bg-green-light/30' : 'border-gray-light hover:border-green-200'">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                    :class="payMethod === 'qris' ? 'border-primary' : 'border-gray-300'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary" x-show="payMethod === 'qris'"></div>
                                </div>
                                <input type="radio" name="pay_method" value="qris" class="hidden" x-model="payMethod" />
                                <div>
                                    <p class="text-sm font-bold text-gray-dark">QRIS (Scan Code)</p>
                                    <p class="text-[10px] text-gray-muted mt-0.5">Scan otomatis e-wallet / m-banking</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Bank Transfer Detail Display -->
                    <div x-show="payMethod === 'transfer'" x-transition class="bg-bg-light rounded-xl p-4 border border-gray-light space-y-3">
                        <p class="text-xs font-bold text-gray-dark flex items-center gap-1">
                            <span class="material-symbols-rounded text-primary text-sm">account_balance</span> Detail Rekening Transfer:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="bg-white p-3 rounded-lg border border-gray-light shadow-sm">
                                <p class="font-bold text-gray-muted uppercase text-[9px]">Bank BCA</p>
                                <p class="font-bold text-gray-dark text-sm mt-0.5">1234-5678-90</p>
                                <p class="text-[10px] text-gray-muted mt-0.5">a/n Syila Buah</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg border border-gray-light shadow-sm">
                                <p class="font-bold text-gray-muted uppercase text-[9px]">Bank Mandiri</p>
                                <p class="font-bold text-gray-dark text-sm mt-0.5">0987-6543-21</p>
                                <p class="text-[10px] text-gray-muted mt-0.5">a/n Syila Buah</p>
                            </div>
                        </div>
                    </div>

                    <!-- QRIS Detail Display -->
                    <div x-show="payMethod === 'qris'" x-transition class="bg-bg-light rounded-xl p-4 border border-gray-light text-center flex flex-col items-center">
                        <p class="text-xs font-bold text-gray-dark flex items-center gap-1 mb-2">
                            <span class="material-symbols-rounded text-primary text-sm">qr_code_2</span> Scan Kode QRIS di Bawah:
                        </p>
                        <div class="inline-block border border-gray-light rounded-xl p-2 bg-white shadow-sm">
                            <div class="w-32 h-32 bg-bg-light rounded-lg flex flex-col items-center justify-center relative overflow-hidden">
                                <span class="material-symbols-rounded text-gray-dark text-5xl">qr_code</span>
                                <p class="absolute bottom-1 text-[8px] text-gray-muted font-bold tracking-wider">SYILABUAH</p>
                            </div>
                        </div>
                        <p class="text-[9px] text-gray-muted mt-2">Mendukung DANA, OVO, GoPay, LinkAja, ShopeePay, & m-banking.</p>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300">
                    <h3 class="font-bold text-gray-dark mb-3">Catatan</h3>
                    <textarea
                        name="notes"
                        class="w-full rounded-xl border border-gray-light px-4 py-3 text-sm text-gray-dark placeholder-gray-muted focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all resize-none bg-bg-light"
                        rows="3"
                        placeholder="Contoh: Tolong buah jangan terlalu matang..."
                    ></textarea>
                </div>
            </div>

            <!-- Right: Summary Card -->
            <div>
                <div class="bg-white rounded-2xl shadow-soft border border-gray-light p-6 hover:shadow-soft-hover transition-all duration-300 sticky top-24">
                    <h3 class="font-bold text-gray-dark mb-4 border-b border-bg-light pb-4">Ringkasan Pesanan</h3>
                    
                    <!-- Products list dynamically handled in AlpineJS -->
                    <div class="space-y-4 max-h-72 overflow-y-auto pr-1 border-b border-bg-light pb-4 mb-4">
                        <template x-for="(item, index) in items" :key="item.product_id">
                            <div class="flex items-center gap-3">
                                <!-- Hidden inputs to submit updated cart arrays -->
                                <input type="hidden" name="product_ids[]" :value="item.product_id" />
                                <input type="hidden" name="qty[]" :value="item.qty" />
                                
                                <div class="w-10 h-10 rounded-xl bg-white border border-gray-light p-1.5 flex items-center justify-center flex-shrink-0">
                                    <img :src="'https://images.unsplash.com/photo-' + item.img + '?w=60&h=60&fit=crop&auto=format'" alt="Product image" class="max-w-full max-h-full object-contain" />
                                </div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-xs font-bold text-gray-dark truncate leading-snug" x-text="item.name"></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <button type="button" @click="if(item.qty > 1) { item.qty--; } else { items.splice(index, 1); }" class="w-5 h-5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-dark flex items-center justify-center text-xs font-bold transition-colors cursor-pointer select-none">-</button>
                                        <span class="text-xs font-extrabold text-gray-dark w-4 text-center" x-text="item.qty"></span>
                                        <button type="button" @click="item.qty++" class="w-5 h-5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-dark flex items-center justify-center text-xs font-bold transition-colors cursor-pointer select-none">+</button>
                                        <span class="text-[10px] text-gray-muted" x-text="'/ ' + item.unit"></span>
                                    </div>
                                </div>
                                <p class="text-xs font-bold text-gray-dark" x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></p>
                            </div>
                        </template>
                        <div x-show="items.length === 0" class="text-center py-4">
                            <p class="text-xs text-gray-muted">Keranjang kosong. Silakan belanja kembali.</p>
                        </div>
                    </div>

                    <!-- Costs list -->
                    <div class="space-y-2 text-sm text-gray-muted border-b border-bg-light pb-4 mb-4">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-dark" x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Ongkos Kirim</span>
                            <span class="font-semibold text-gray-dark" x-text="'Rp ' + ongkir.toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <div class="flex justify-between font-bold text-gray-dark text-base">
                        <span>Total Pembayaran</span>
                        <span class="text-primary font-extrabold text-lg" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 font-bold rounded-xl bg-primary text-white hover:bg-primary-hover active:bg-primary-active shadow-soft hover:shadow-soft-hover transform hover:-translate-y-0.5 transition-all duration-300 px-4 py-3 text-base cursor-pointer">
                            Buat Pesanan <span class="material-symbols-rounded text-lg">check</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection