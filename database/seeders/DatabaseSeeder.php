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
    }
}
