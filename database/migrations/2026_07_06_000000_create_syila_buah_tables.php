<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Buah Lokal, Buah Impor, Buah Potong, Parsel Buah
            $table->timestamps();
        });

        // 2. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('unit'); // Kg, Buah, Pack, Sisir, Paket
            $table->integer('stock')->default(0);
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
        });

        // 3. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // 4. Stock Entries (Stok Masuk)
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('purchase_price', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Stock Adjustments (Penyesuaian Stok)
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('qty'); // jumlah stok setelah penyesuaian (opname)
            $table->integer('difference'); // selisih (+/-)
            $table->string('type'); // Buah Busuk, Buah Rusak, Penyusutan, Kehilangan
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Stock Histories (Log Perubahan Stok)
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedBigInteger('reference_id')->nullable(); // id dari stock_entry, order, stock_adjustment
            $table->string('reference_type')->nullable(); // StockEntry, Order, StockAdjustment, Pembatalan
            $table->integer('qty'); // perubahan stok (positif/negatif)
            $table->string('transaction_type'); // Stok Masuk, Checkout, Penyesuaian, Pembatalan
            $table->timestamps();
        });

        // 7. Carts
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 8. Cart Items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('qty');
            $table->timestamps();
        });

        // 9. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_no')->unique();
            $table->text('shipping_address');
            $table->string('shipping_method'); // Ambil di Tempat, Diantar
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('status')->default('Menunggu Pembayaran'); // Menunggu Pembayaran, Menunggu Verifikasi, Diproses, Dikirim, Selesai, Dibatalkan
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 10. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        // 11. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('method'); // Transfer Bank, QRIS
            $table->string('proof_of_payment')->nullable();
            $table->string('payment_status')->default('Menunggu'); // Menunggu, Lunas, Ditolak
            $table->timestamp('payment_date')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('stock_histories');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_entries');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
