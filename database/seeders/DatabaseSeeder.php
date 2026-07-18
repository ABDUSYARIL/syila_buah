<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockEntry;
use App\Models\StockHistory;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Admin & Pelanggan)
        $admin = User::create([
            'name' => 'Syila Admin',
            'email' => 'admin@syila.com',
            'phone' => '081234567890',
            'address' => 'Bandung',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $pelanggan = User::create([
            'name' => 'Rina Kartika',
            'email' => 'rina@email.com',
            'phone' => '081298765432',
            'address' => 'Jl. Melati No. 12, Bandung',
            'password' => Hash::make('pelanggan123'),
            'role' => 'pelanggan',
            'status' => 'aktif',
        ]);

        // 2. Seed Categories
        $catLokal = Category::create(['name' => 'Buah Lokal']);
        $catImpor = Category::create(['name' => 'Buah Impor']);
        $catPotong = Category::create(['name' => 'Buah Potong']);
        $catParsel = Category::create(['name' => 'Parsel Buah']);

        // 3. Seed Suppliers
        $sup1 = Supplier::create([
            'name' => 'Tani Makmur',
            'phone' => '08122334455',
            'email' => 'tani@makmur.com',
            'address' => 'Cicendo, Bandung'
        ]);

        $sup2 = Supplier::create([
            'name' => 'Agro Prima',
            'phone' => '08567890123',
            'email' => 'agro@prima.com',
            'address' => 'Ciwidey, Kab. Bandung'
        ]);

        // 4. Seed Products
        $productsData = [
            [
                'name' => 'Apel Fuji',
                'category_id' => $catImpor->id,
                'price' => 35000,
                'unit' => 'Kg',
                'stock' => 150,
                'img' => '1560806887-1e4cd0b6cbd6',
                'desc' => 'Apel Fuji segar berkualitas premium langsung dari kebun pilihan. Rasa manis segar, tekstur renyah, kaya vitamin C dan serat.'
            ],
            [
                'name' => 'Jeruk Pontianak',
                'category_id' => $catLokal->id,
                'price' => 25000,
                'unit' => 'Kg',
                'stock' => 200,
                'img' => '1611080626919-7cf5a9dbab5b',
                'desc' => 'Jeruk Pontianak asli produk lokal unggulan. Rasa manis sedikit asam, kaya vitamin C, cocok untuk jus segar.'
            ],
            [
                'name' => 'Mangga Harum Manis',
                'category_id' => $catLokal->id,
                'price' => 28000,
                'unit' => 'Kg',
                'stock' => 120,
                'img' => '1553279768-865429fa0078',
                'desc' => 'Mangga Harum Manis pilihan dari Probolinggo. Daging buah lembut, manis, harum. Favorit pelanggan kami.'
            ],
            [
                'name' => 'Pisang Cavendish',
                'category_id' => $catLokal->id,
                'price' => 18000,
                'unit' => 'Sisir',
                'stock' => 80,
                'img' => '1571771894821-ce9b6c11b08e',
                'desc' => 'Pisang Cavendish matang sempurna. Rasa manis lembut, cocok sebagai camilan sehat dan bahan smoothie.'
            ],
            [
                'name' => 'Alpukat Mentega',
                'category_id' => $catLokal->id,
                'price' => 32000,
                'unit' => 'Kg',
                'stock' => 90,
                'img' => '1523049673857-eb18f1d7b578',
                'desc' => 'Alpukat mentega Jawa berkualitas premium. Daging buah lembut, creamy, kaya lemak baik.'
            ],
            [
                'name' => 'Anggur Merah',
                'category_id' => $catImpor->id,
                'price' => 65000,
                'unit' => 'Kg',
                'stock' => 35,
                'img' => '1603052875302-d376b7c0639a',
                'desc' => 'Anggur merah impor premium. Biji kecil, rasa manis segar, kaya antioksidan resveratrol.'
            ],
            [
                'name' => 'Semangka Merah',
                'category_id' => $catLokal->id,
                'price' => 12000,
                'unit' => 'Kg',
                'stock' => 45,
                'img' => '1589618474799-23a705820059',
                'desc' => 'Semangka merah segar berair tinggi. Kandungan air 92%, menyegarkan di cuaca panas, kaya likopen.'
            ],
            [
                'name' => 'Melon Hijau',
                'category_id' => $catLokal->id,
                'price' => 22000,
                'unit' => 'Buah',
                'stock' => 60,
                'img' => '1628102479305-9d571325d9ec',
                'desc' => 'Melon hijau manis dan segar. Daging buah lunak dengan rasa manis khas, cocok untuk berbagai sajian.'
            ],
            [
                'name' => 'Pepaya California',
                'category_id' => $catLokal->id,
                'price' => 15000,
                'unit' => 'Kg',
                'stock' => 110,
                'img' => '1615485290382-441e4d049cb5',
                'desc' => 'Pepaya California manis dan lembut. Baik untuk pencernaan, kaya papain, vitamin A dan C.'
            ],
            [
                'name' => 'Buah Naga Merah',
                'category_id' => $catLokal->id,
                'price' => 38000,
                'unit' => 'Kg',
                'stock' => 40,
                'img' => '1615485290382-441e4d049cb5',
                'desc' => 'Buah naga merah segar. Rasa manis lembut, kaya antioksidan dan serat. Baik untuk daya tahan tubuh.'
            ],
            [
                'name' => 'Nanas Madu',
                'category_id' => $catLokal->id,
                'price' => 20000,
                'unit' => 'Buah',
                'stock' => 75,
                'img' => '1550258987-190a2d41a8ba',
                'desc' => 'Nanas madu Subang terkenal. Rasa sangat manis hampir tanpa rasa asam, ukuran besar, daging tebal.'
            ],
            [
                'name' => 'Stroberi',
                'category_id' => $catImpor->id,
                'price' => 45000,
                'unit' => 'Pack',
                'stock' => 55,
                'img' => '1464965911861-746a04b4bca6',
                'desc' => 'Stroberi segar berkualitas tinggi. Rasa manis asam khas, kaya vitamin C. Per pack 250gr.'
            ],
        ];

        foreach ($productsData as $pd) {
            // Create Product
            $product = Product::create([
                'category_id' => $pd['category_id'],
                'name' => $pd['name'],
                'image' => $pd['img'],
                'description' => $pd['desc'],
                'price' => $pd['price'],
                'unit' => $pd['unit'],
                'stock' => $pd['stock'],
                'status' => 'aktif',
            ]);

            // Create Stock Entry
            $se = StockEntry::create([
                'product_id' => $product->id,
                'supplier_id' => ($product->category_id == $catLokal->id ? $sup2->id : $sup1->id),
                'qty' => $pd['stock'],
                'purchase_price' => $pd['price'] * 0.7, // 30% margin
                'notes' => 'Stok awal panen kebun.'
            ]);

            // Create Stock History log
            StockHistory::create([
                'product_id' => $product->id,
                'reference_id' => $se->id,
                'reference_type' => 'StockEntry',
                'qty' => $pd['stock'],
                'transaction_type' => 'Stok Masuk'
            ]);
        }

        // 5. Seed extra customers
        $pelanggan2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'phone' => '085678901234',
            'address' => 'Jl. Cempaka No. 5, Bogor',
            'password' => Hash::make('pelanggan123'),
            'role' => 'pelanggan',
            'status' => 'aktif',
        ]);

        $pelanggan3 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@email.com',
            'phone' => '081233445566',
            'address' => 'Jl. Anggrek No. 8, Jakarta',
            'password' => Hash::make('pelanggan123'),
            'role' => 'pelanggan',
            'status' => 'aktif',
        ]);

        $users = [$pelanggan, $pelanggan2, $pelanggan3];
        $products = Product::all();

        // 6. Seed historical orders (yearly: 2022, 2023, 2024, 2025)
        $years = [2022, 2023, 2024, 2025];
        $orderCount = 1;
        foreach ($years as $year) {
            // Seed 8 orders per year
            for ($i = 0; $i < 8; $i++) {
                $user = $users[array_rand($users)];
                $date = \Carbon\Carbon::create($year, rand(1, 12), rand(1, 28), rand(9, 18), rand(0, 59));
                self::createMockOrder($user, $products, $date, $orderCount++);
            }
        }

        // 7. Seed monthly orders for current year (2026) up to current month (July)
        $currentYear = 2026;
        for ($month = 1; $month <= 7; $month++) {
            // Seed 5 orders per month
            for ($i = 0; $i < 5; $i++) {
                $user = $users[array_rand($users)];
                $date = \Carbon\Carbon::create($currentYear, $month, rand(1, 28), rand(9, 18), rand(0, 59));
                self::createMockOrder($user, $products, $date, $orderCount++);
            }
        }

        // 8. Seed daily orders for current week (July 13, 2026 to July 19, 2026)
        $startOfWeek = \Carbon\Carbon::now()->startOfWeek(); // Monday
        for ($d = 0; $d < 7; $d++) {
            $dayDate = $startOfWeek->copy()->addDays($d);
            if ($dayDate->isAfter(\Carbon\Carbon::now())) {
                continue;
            }
            // Seed 3 orders per day
            for ($i = 0; $i < 3; $i++) {
                $user = $users[array_rand($users)];
                $date = $dayDate->copy()->setHour(rand(9, 20))->setMinute(rand(0, 59));
                self::createMockOrder($user, $products, $date, $orderCount++);
            }
        }
    }

    private static function createMockOrder($user, $products, $date, $orderId)
    {
        $invoiceNo = 'SB-' . $date->format('ymd') . '-' . sprintf('%03d', $orderId);
        $shippingMethod = (rand(0, 1) === 0) ? 'Diantar' : 'Ambil di Tempat';
        $shippingCost = ($shippingMethod === 'Diantar') ? 15000 : 0;
        
        $subtotal = 0;
        $itemsCount = rand(1, 3);
        $selectedProducts = $products->random($itemsCount);

        // We create the order first
        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'invoice_no' => $invoiceNo,
            'shipping_address' => $user->address ?? 'Jl. Melati No. 12, Bandung',
            'shipping_method' => $shippingMethod,
            'shipping_cost' => $shippingCost,
            'subtotal' => 0, // Will update later
            'total' => 0, // Will update later
            'status' => 'Selesai', // Seed completed orders so they count in reports
            'notes' => 'Seeded transaction.',
            'created_at' => $date,
            'updated_at' => $date
        ]);

        foreach ($selectedProducts as $product) {
            $qty = rand(1, 3);
            $price = $product->price;
            $subtotal += $price * $qty;

            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $price,
                'created_at' => $date,
                'updated_at' => $date
            ]);

            // Create stock history log
            \App\Models\StockHistory::create([
                'product_id' => $product->id,
                'reference_id' => $order->id,
                'reference_type' => 'Order',
                'qty' => -$qty,
                'transaction_type' => 'Checkout',
                'created_at' => $date,
                'updated_at' => $date
            ]);
        }

        $total = $subtotal + $shippingCost;
        $order->update([
            'subtotal' => $subtotal,
            'total' => $total
        ]);

        // Create Payment record
        \App\Models\Payment::create([
            'order_id' => $order->id,
            'method' => (rand(0, 1) === 0) ? 'QRIS' : 'Transfer Bank',
            'proof_of_payment' => 'payments/seeded_proof.jpg',
            'payment_status' => 'Lunas',
            'payment_date' => $date,
            'created_at' => $date,
            'updated_at' => $date
        ]);
    }
}
