<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductData
{
    public static $PRODUCTS = [
        [
            'id' => 1,
            'name' => 'Apel Fuji',
            'category' => 'Buah Impor',
            'price' => 35000,
            'unit' => 'Kg',
            'stock' => 150,
            'rating' => 4.8,
            'sold' => 234,
            'img' => '1560806887-1e4cd0b6cbd6',
            'desc' => 'Apel Fuji segar berkualitas premium langsung dari kebun pilihan. Rasa manis segar, tekstur renyah, kaya vitamin C dan serat.',
            'tags' => ['Premium', 'Impor', 'Vitamin C']
        ],
        [
            'id' => 2,
            'name' => 'Jeruk Pontianak',
            'category' => 'Buah Lokal',
            'price' => 25000,
            'unit' => 'Kg',
            'stock' => 200,
            'rating' => 4.7,
            'sold' => 189,
            'img' => '1611080626919-7cf5a9dbab5b',
            'desc' => 'Jeruk Pontianak asli produk lokal unggulan. Rasa manis sedikit asam, kaya vitamin C, cocok untuk jus segar.',
            'tags' => ['Lokal', 'Segar', 'Vitamin C']
        ],
        [
            'id' => 3,
            'name' => 'Mangga Harum Manis',
            'category' => 'Buah Lokal',
            'price' => 28000,
            'unit' => 'Kg',
            'stock' => 120,
            'rating' => 4.9,
            'sold' => 312,
            'img' => '1553279768-865429fa0078',
            'desc' => 'Mangga Harum Manis pilihan dari Probolinggo. Daging buah lembut, manis, harum. Favorit pelanggan kami.',
            'tags' => ['Terlaris', 'Lokal', 'Manis']
        ],
        [
            'id' => 4,
            'name' => 'Pisang Cavendish',
            'category' => 'Buah Lokal',
            'price' => 18000,
            'unit' => 'Sisir',
            'stock' => 80,
            'rating' => 4.6,
            'sold' => 156,
            'img' => '1571771894821-ce9b6c11b08e',
            'desc' => 'Pisang Cavendish matang sempurna. Rasa manis lembut, cocok sebagai camilan sehat dan bahan smoothie.',
            'tags' => ['Lokal', 'Sehat', 'Camilan']
        ],
        [
            'id' => 5,
            'name' => 'Alpukat Mentega',
            'category' => 'Buah Lokal',
            'price' => 32000,
            'unit' => 'Kg',
            'stock' => 90,
            'rating' => 4.7,
            'sold' => 167,
            'img' => '1523049673857-eb18f1d7b578',
            'desc' => 'Alpukat mentega Jawa berkualitas premium. Daging buah lembut, creamy, kaya lemak baik.',
            'tags' => ['Premium', 'Lokal', 'Bergizi']
        ],
        [
            'id' => 6,
            'name' => 'Anggur Merah',
            'category' => 'Buah Impor',
            'price' => 65000,
            'unit' => 'Kg',
            'stock' => 35,
            'rating' => 4.8,
            'sold' => 145,
            'img' => 'https://media.istockphoto.com/id/2232478825/id/foto/anggur-merah-terisolasi.jpg?s=1024x1024&w=is&k=20&c=EFCbzmpzSULwIye19ca5HB4IbziuIGFo4wHVIeaG6r0=',
            'desc' => 'Anggur merah impor premium. Biji kecil, rasa manis segar, kaya antioksidan resveratrol.',
            'tags' => ['Premium', 'Impor', 'Antioksidan']
        ],
        [
            'id' => 7,
            'name' => 'Semangka Merah',
            'category' => 'Buah Lokal',
            'price' => 12000,
            'unit' => 'Kg',
            'stock' => 45,
            'rating' => 4.5,
            'sold' => 98,
            'img' => '1589618474799-23a705820059',
            'desc' => 'Semangka merah segar berair tinggi. Kandungan air 92%, menyegarkan di cuaca panas, kaya likopen.',
            'tags' => ['Lokal', 'Segar', 'Menyegarkan']
        ],
        [
            'id' => 8,
            'name' => 'Melon Hijau',
            'category' => 'Buah Lokal',
            'price' => 22000,
            'unit' => 'Buah',
            'stock' => 60,
            'rating' => 4.4,
            'sold' => 87,
            'img' => '1628102479305-9d571325d9ec',
            'desc' => 'Melon hijau manis dan segar. Daging buah lunak dengan rasa manis khas, cocok untuk berbagai sajian.',
            'tags' => ['Lokal', 'Manis', 'Segar']
        ],
        [
            'id' => 9,
            'name' => 'Pepaya California',
            'category' => 'Buah Lokal',
            'price' => 15000,
            'unit' => 'Kg',
            'stock' => 110,
            'rating' => 4.5,
            'sold' => 134,
            'img' => '1615485290382-441e4d049cb5',
            'desc' => 'Pepaya California manis dan lembut. Baik untuk pencernaan, kaya papain, vitamin A dan C.',
            'tags' => ['Lokal', 'Sehat', 'Pencernaan']
        ],
        [
            'id' => 10,
            'name' => 'Buah Naga Merah',
            'category' => 'Buah Lokal',
            'price' => 38000,
            'unit' => 'Kg',
            'stock' => 40,
            'rating' => 4.8,
            'sold' => 89,
            'img' => '1615485290382-441e4d049cb5',
            'desc' => 'Buah naga merah segar. Rasa manis lembut, kaya antioksidan and serat. Baik untuk daya tahan tubuh.',
            'tags' => ['Lokal', 'Antioksidan', 'Sehat']
        ],
        [
            'id' => 11,
            'name' => 'Nanas Madu',
            'category' => 'Buah Lokal',
            'price' => 20000,
            'unit' => 'Buah',
            'stock' => 75,
            'rating' => 4.6,
            'sold' => 112,
            'img' => '1550258987-190a2d41a8ba',
            'desc' => 'Nanas madu Subang terkenal. Rasa sangat manis hampir tanpa rasa asam, ukuran besar, daging tebal.',
            'tags' => ['Lokal', 'Manis', 'Premium']
        ],
        [
            'id' => 12,
            'name' => 'Stroberi',
            'category' => 'Buah Impor',
            'price' => 45000,
            'unit' => 'Pack',
            'stock' => 55,
            'rating' => 4.9,
            'sold' => 278,
            'img' => '1464965911861-746a04b4bca6',
            'desc' => 'Stroberi segar berkualitas tinggi. Rasa manis asam khas, kaya vitamin C. Per pack 250gr.',
            'tags' => ['Favorit', 'Impor', 'Antioksidan']
        ],
    ];

    public static $ORDERS = [
        [
            'id' => 'SB-240701-001',
            'customer' => 'Rina Kartika',
            'date' => '01 Jul 2025',
            'total' => 187500,
            'payStatus' => 'Lunas',
            'status' => 'Selesai',
            'items' => [['productId' => 1, 'qty' => 2], ['productId' => 12, 'qty' => 1]],
            'address' => 'Jl. Melati No.12, Bandung',
            'method' => 'Transfer Bank'
        ],
        [
            'id' => 'SB-240701-002',
            'customer' => 'Budi Santoso',
            'date' => '01 Jul 2025',
            'total' => 96000,
            'payStatus' => 'Lunas',
            'status' => 'Dikirim',
            'items' => [['productId' => 3, 'qty' => 2], ['productId' => 4, 'qty' => 2]],
            'address' => 'Jl. Cempaka No.5, Bogor',
            'method' => 'QRIS'
        ],
        [
            'id' => 'SB-240701-003',
            'customer' => 'Siti Nurhaliza',
            'date' => '02 Jul 2025',
            'total' => 234000,
            'payStatus' => 'Menunggu',
            'status' => 'Menunggu Pembayaran',
            'items' => [['productId' => 6, 'qty' => 2], ['productId' => 5, 'qty' => 2]],
            'address' => 'Jl. Anggrek No.8, Jakarta',
            'method' => 'Transfer Bank'
        ],
        [
            'id' => 'SB-240702-004',
            'customer' => 'Ahmad Fauzi',
            'date' => '02 Jul 2025',
            'total' => 65000,
            'payStatus' => 'Menunggu Verifikasi',
            'status' => 'Menunggu Verifikasi',
            'items' => [['productId' => 2, 'qty' => 2], ['productId' => 7, 'qty' => 1]],
            'address' => 'Jl. Mawar No.3, Surabaya',
            'method' => 'QRIS'
        ],
        [
            'id' => 'SB-240702-005',
            'customer' => 'Dewi Rahayu',
            'date' => '02 Jul 2025',
            'total' => 148000,
            'payStatus' => 'Lunas',
            'status' => 'Diproses',
            'items' => [['productId' => 8, 'qty' => 2], ['productId' => 11, 'qty' => 2]],
            'address' => 'Jl. Dahlia No.15, Medan',
            'method' => 'Transfer Bank'
        ],
    ];

    public static $ADMINS_DATA = [
        ['id' => 1, 'name' => 'Syila Admin', 'email' => 'admin@syilabuah.id', 'role' => 'Super Admin', 'phone' => '081234567890', 'status' => 'Aktif', 'lastLogin' => '02 Jul 2025 08:30'],
        ['id' => 2, 'name' => 'Rizky Pratama', 'email' => 'rizky@syilabuah.id', 'role' => 'Admin', 'phone' => '081298765432', 'status' => 'Aktif', 'lastLogin' => '02 Jul 2025 09:15'],
        ['id' => 3, 'name' => 'Mega Lestari', 'email' => 'mega@syilabuah.id', 'role' => 'Admin', 'phone' => '085678901234', 'status' => 'Nonaktif', 'lastLogin' => '28 Jun 2025 14:22'],
    ];

    public static $salesData = [
        ['month' => 'Jan', 'revenue' => 12400000, 'orders' => 89],
        ['month' => 'Feb', 'revenue' => 15600000, 'orders' => 112],
        ['month' => 'Mar', 'revenue' => 18200000, 'orders' => 134],
        ['month' => 'Apr', 'revenue' => 16800000, 'orders' => 123],
        ['month' => 'Mei', 'revenue' => 21300000, 'orders' => 156],
        ['month' => 'Jun', 'revenue' => 19700000, 'orders' => 142],
        ['month' => 'Jul', 'revenue' => 24500000, 'orders' => 178],
    ];

    public static $dailySalesData = [
        ['month' => 'Sen', 'revenue' => 2200000, 'orders' => 24],
        ['month' => 'Sel', 'revenue' => 1850000, 'orders' => 19],
        ['month' => 'Rab', 'revenue' => 2400000, 'orders' => 27],
        ['month' => 'Kam', 'revenue' => 2150000, 'orders' => 21],
        ['month' => 'Jum', 'revenue' => 2800000, 'orders' => 31],
        ['month' => 'Sab', 'revenue' => 3200000, 'orders' => 36],
        ['month' => 'Min', 'revenue' => 2700000, 'orders' => 29],
    ];

    public static $yearlySalesData = [
        ['month' => '2020', 'revenue' => 143000000, 'orders' => 1120],
        ['month' => '2021', 'revenue' => 157000000, 'orders' => 1290],
        ['month' => '2022', 'revenue' => 169000000, 'orders' => 1365],
        ['month' => '2023', 'revenue' => 184000000, 'orders' => 1480],
        ['month' => '2024', 'revenue' => 198000000, 'orders' => 1545],
    ];

    public static $topProducts = [
        ['name' => 'Mangga Harum Manis', 'sold' => 312, 'revenue' => 8736000],
        ['name' => 'Stroberi', 'sold' => 278, 'revenue' => 12510000],
        ['name' => 'Apel Fuji', 'sold' => 234, 'revenue' => 8190000],
        ['name' => 'Alpukat Mentega', 'sold' => 167, 'revenue' => 5344000],
        ['name' => 'Jeruk Pontianak', 'sold' => 189, 'revenue' => 4725000],
    ];

    public static $CHART_COLORS = ['#4CAF50', '#FF9800', '#2196F3', '#9C27B0', '#F44336'];

    public static function getSalesData(string $period = 'Bulanan')
    {
        $validStatuses = ['Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai'];

        if ($period === 'Harian') {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $sales = Order::whereIn('status', $validStatuses)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->select(
                    DB::raw('DAYOFWEEK(created_at) as day_num'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('COUNT(id) as orders')
                )
                ->groupBy('day_num')
                ->get()
                ->keyBy('day_num');

            $days = [
                2 => 'Sen',
                3 => 'Sel',
                4 => 'Rab',
                5 => 'Kam',
                6 => 'Jum',
                7 => 'Sab',
                1 => 'Min'
            ];

            $data = [];
            foreach ($days as $dayNum => $dayName) {
                $record = $sales->get($dayNum);
                $data[] = [
                    'month' => $dayName,
                    'revenue' => $record ? (float)$record->revenue : 0,
                    'orders' => $record ? (int)$record->orders : 0
                ];
            }
            return $data;
        }

        if ($period === 'Tahunan') {
            $currentYear = Carbon::now()->year;
            $startYear = $currentYear - 4;

            $sales = Order::whereIn('status', $validStatuses)
                ->whereYear('created_at', '>=', $startYear)
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('COUNT(id) as orders')
                )
                ->groupBy('year')
                ->get()
                ->keyBy('year');

            $data = [];
            for ($y = $startYear; $y <= $currentYear; $y++) {
                $record = $sales->get($y);
                $data[] = [
                    'month' => (string)$y,
                    'revenue' => $record ? (float)$record->revenue : 0,
                    'orders' => $record ? (int)$record->orders : 0
                ];
            }
            return $data;
        }

        // Bulanan (current year)
        $currentYear = Carbon::now()->year;
        $sales = Order::whereIn('status', $validStatuses)
            ->whereYear('created_at', $currentYear)
            ->select(
                DB::raw('MONTH(created_at) as month_num'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(id) as orders')
            )
            ->groupBy('month_num')
            ->get()
            ->keyBy('month_num');

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $data = [];
        foreach ($months as $num => $name) {
            $record = $sales->get($num);
            $data[] = [
                'month' => $name,
                'revenue' => $record ? (float)$record->revenue : 0,
                'orders' => $record ? (int)$record->orders : 0
            ];
        }
        return $data;
    }

    public static function getTopProducts(string $period = 'Bulanan')
    {
        $validStatuses = ['Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai'];

        $query = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', $validStatuses);

        if ($period === 'Harian') {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $query->whereBetween('orders.created_at', [$startOfWeek, $endOfWeek]);
        } elseif ($period === 'Tahunan') {
            $query->whereYear('orders.created_at', '>=', Carbon::now()->year - 4);
        } else {
            $query->whereYear('orders.created_at', Carbon::now()->year);
        }

        $topProducts = $query->select(
            'products.name',
            DB::raw('SUM(order_items.qty) as sold'),
            DB::raw('SUM(order_items.qty * order_items.price) as revenue')
        )
        ->groupBy('products.id', 'products.name')
        ->orderBy('sold', 'desc')
        ->take(5)
        ->get();

        return $topProducts->map(function($p) {
            return [
                'name' => $p->name,
                'sold' => (int)$p->sold,
                'revenue' => (float)$p->revenue
            ];
        })->toArray();
    }

    public static function rp($n)
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    // Fungsi pembantu untuk menghasilkan URL gambar produk yang tepat
    // Mendukung gambar dari Unsplash (berupa ID foto), URL eksternal, atau file upload lokal di storage Laravel
    public static function img($id, $w = 400, $h = 400)
    {
        // Jika tidak ada gambar, gunakan gambar default dari Unsplash
        if (empty($id)) {
            return "https://images.unsplash.com/photo-1610832958506-aa56368176cf?w={$w}&h={$h}&fit=crop&auto=format";
        }
        
        // Jika gambar adalah URL eksternal (http/https), kembalikan apa adanya
        if (str_starts_with($id, 'http://') || str_starts_with($id, 'https://')) {
            return $id;
        }

        // Jika gambar adalah path lokal yang tersimpan di storage Laravel (/storage/... atau storage/...)
        // Gunakan fungsi asset() agar URL gambar selalu tepat relatif terhadap domain aplikasi
        if (str_starts_with($id, '/storage/')) {
            // Hapus awalan "/" agar asset() dapat memproses path dengan benar
            return asset(ltrim($id, '/'));
        }
        
        if (str_starts_with($id, 'storage/')) {
            return asset($id);
        }
        
        // Jika yang disimpan adalah ID foto Unsplash, bangun URL Unsplash lengkap
        return "https://images.unsplash.com/photo-{$id}?w={$w}&h={$h}&fit=crop&auto=format";
    }

    public static function getProduct($id)
    {
        foreach (self::$PRODUCTS as $p) {
            if ($p['id'] == $id) {
                return $p;
            }
        }
        return self::$PRODUCTS[0];
    }
}
