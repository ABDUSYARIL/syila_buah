<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\StockEntry;
use App\Models\StockAdjustment;
use App\Models\StockHistory;
use App\Models\Supplier;
use App\Http\Controllers\ProductData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Tambahan: Import Hash untuk enkripsi password

class AdminController extends Controller
{
    public function dashboard()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        $products = Product::all();
        $salesData = ProductData::getSalesData('Bulanan');
        $topProducts = ProductData::getTopProducts('Bulanan');
        $chartColors = ProductData::$CHART_COLORS;
        $lowStockProducts = Product::where('stock', '<', 50)->orderBy('stock', 'asc')->take(5)->get();

        $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $totalRevenue = Order::whereNotIn('status', ['Dibatalkan', 'Menunggu Pembayaran'])->sum('total');
        $productsCount = $products->count();

        $stats = [
            ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'change' => '+18%', 'icon' => 'payments', 'color' => 'bg-green-light text-primary'],
            ['label' => 'Total Pesanan', 'value' => $orders->count(), 'change' => '+23%', 'icon' => 'local_mall', 'color' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Produk Terdaftar', 'value' => $productsCount, 'change' => 'Stabil', 'icon' => 'inventory_2', 'color' => 'bg-[#FFF3E0] text-accent'],
            ['label' => 'Pesanan Hari Ini', 'value' => $ordersToday, 'change' => 'vs kemarin', 'icon' => 'calendar_today', 'color' => 'bg-purple-50 text-purple-600'],
        ];

        return view('admin.dashboard', compact('orders', 'products', 'salesData', 'topProducts', 'chartColors', 'stats', 'lowStockProducts'));
    }

    public function products(Request $request)
    {
        $search = $request->query('search', '');
        $categoryName = $request->query('category', 'Semua');
        
        $query = Product::query();

        if ($categoryName !== 'Semua') {
            $query->whereHas('category', function($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Diurutkan berdasarkan aktivitas terbaru (updated_at terbaru)
        $products = $query->orderBy('updated_at', 'desc')->get();
        $categories = Category::all();

        return view('admin.products', compact('products', 'categories', 'search', 'categoryName'));
    }

    public function saveProduct(Request $request)
    {
        $id = $request->input('id');
        
        $data = [
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'unit' => $request->input('unit'),
            'description' => $request->input('description'),
            'status' => $request->input('status', 'aktif'),
        ];

        // Handle Image upload or URL input
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $data['image'] = '/storage/' . $path;
        } elseif ($request->input('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        if ($id) {
            $product = Product::findOrFail($id);
            $product->update($data);
            $msg = 'Produk berhasil diperbarui!';
        } else {
            $data['stock'] = $request->input('stock', 0);
            $product = Product::create($data);
            $msg = 'Produk baru berhasil ditambahkan!';

            if ($product->stock > 0) {
                StockHistory::create([
                    'product_id' => $product->id,
                    'qty' => $product->stock,
                    'transaction_type' => 'Stok Masuk',
                    'reference_type' => 'StockEntry',
                    'reference_id' => 0
                ]);
            }
        }

        return redirect()->route('admin.products')->with('success', $msg);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus!');
    }

    // Fungsi untuk menampilkan halaman Manajemen Stok
    public function stock()
    {
        // ── AUTO-DELETE: Hapus riwayat log stok berdasarkan pengaturan retensi ──
        // Default: 30 hari (1 bulan), dapat disesuaikan ke 7 hari (1 minggu) atau rentang lainnya oleh admin
        $retentionDays = (int) session('stock_retention_days', 30);
        if ($retentionDays > 0) {
            StockHistory::where('created_at', '<', now()->subDays($retentionDays))->delete();
        }

        // Mengambil data semua produk & pemasok untuk keperluan dropdown/input formulir stok
        $products = Product::orderBy('name', 'asc')->get();
        $suppliers = Supplier::all();

        // ── TAB 1: LOG STOK MASUK ──
        // Menampilkan semua log dengan kuantitas positif (stok masuk / bertambah)
        $stockMasuk = StockHistory::with('product')
            ->where('qty', '>', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'masuk_page');

        // ── TAB 2: LOG STOK KELUAR ──
        // Menampilkan semua log dengan kuantitas negatif (stok keluar / berkurang)
        $stockKeluar = StockHistory::with('product')
            ->where('qty', '<', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'keluar_page');

        // ── TAB 3: STOK SAAT INI ──
        // Menampilkan ringkasan stok terkini dari setiap produk (langsung dari tabel products)
        $stockSaatIni = Product::orderBy('name', 'asc')->paginate(10, ['*'], 'saat_ini_page');

        // Tab aktif dari URL parameter (default: masuk)
        $activeTab = request()->query('tab', 'masuk');

        // Mengirim data ke view halaman Manajemen Stok
        return view('admin.stock', compact('products', 'suppliers', 'stockMasuk', 'stockKeluar', 'stockSaatIni', 'activeTab', 'retentionDays'));
    }

    public function addStock(Request $request)
    {
        $productId = $request->input('product_id');
        $qty = (int) $request->input('qty');
        $supplierName = trim($request->input('supplier', ''));
        $purchasePrice = $request->input('purchase_price');
        $notes = $request->input('notes');

        // Cari atau buat Pemasok (Supplier) baru berdasarkan input nama pemasok dari formulir
        $supplier = null;
        if (!empty($supplierName)) {
            $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
        } else {
            $supplier = Supplier::first() ?? Supplier::create(['name' => 'Default']);
        }

        // Create StockEntry dengan ID Pemasok yang sesuai
        $entry = StockEntry::create([
            'product_id' => $productId,
            'supplier_id' => $supplier->id,
            'qty' => $qty,
            'purchase_price' => $purchasePrice,
            'notes' => $notes
        ]);

        // Update Product Stock
        $product = Product::findOrFail($productId);
        $product->stock += $qty;
        $product->save();

        // Create StockHistory log
        StockHistory::create([
            'product_id' => $product->id,
            'reference_id' => $entry->id,
            'reference_type' => 'StockEntry',
            'qty' => $qty,
            'transaction_type' => 'Stok Masuk'
        ]);

        return redirect()->back()->with('success', 'Stok masuk berhasil disimpan (Pemasok: ' . $supplier->name . ')!');
    }

    public function adjustStock(Request $request)
    {

        $productId = $request->input('product_id');
        $qty = (int) $request->input('qty');
        $type = $request->input('type');
        $notes = $request->input('notes');

        $product = Product::findOrFail($productId);
        $difference = $qty - $product->stock;

        $adjustment = StockAdjustment::create([
            'product_id' => $productId,
            'qty' => $qty,
            'difference' => $difference,
            'type' => $type,
            'notes' => $notes
        ]);

        // Update Product Stock
        $product->stock = $qty;
        $product->save();

        // Create StockHistory log
        StockHistory::create([
            'product_id' => $product->id,
            'reference_id' => $adjustment->id,
            'reference_type' => 'StockAdjustment',
            'qty' => $difference,
            'transaction_type' => 'Penyesuaian'
        ]);

        return redirect()->back()->with('success', 'Penyesuaian opname stok berhasil disimpan!');
    }

    // Fungsi untuk menampilkan halaman Kelola Pesanan dengan fitur paginasi
    public function orders(Request $request)
    {
        // ── AUTO-DELETE: Hapus pesanan yang sudah berstatus Selesai/Dibatalkan berdasarkan retensi ──
        // Default: 30 hari (1 bulan), dapat disesuaikan ke 7 hari (1 minggu) atau rentang lainnya oleh admin
        $retentionDays = (int) session('order_retention_days', 30);
        if ($retentionDays > 0) {
            Order::whereIn('status', ['Selesai', 'Dibatalkan'])
                ->where('created_at', '<', now()->subDays($retentionDays))
                ->delete();
        }

        // Mengambil nilai filter pencarian dan status dari parameter URL
        $search = $request->query('search', '');
        $status = $request->query('status', 'Menunggu Verifikasi');
        if ($status === 'Semua' || $status === 'Menunggu Pembayaran') {
            $status = 'Menunggu Verifikasi';
        }
        
        // ── BADGE COUNT: Hitung jumlah pesanan per status untuk ditampilkan sebagai notif angka ──
        $statusCounts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        
        // Gabungkan count Menunggu Pembayaran & Menunggu Verifikasi
        $pendingTotal = ($statusCounts['Menunggu Verifikasi'] ?? 0) + ($statusCounts['Menunggu Pembayaran'] ?? 0);
        $statusCounts['Menunggu Verifikasi'] = $pendingTotal;

        // Membangun query pesanan beserta relasi pengguna, pembayaran, dan item pesanan
        $query = Order::with([
            'user',       // Relasi ke data pengguna/pelanggan
            'payment',    // Relasi ke data pembayaran
            'orderItems.product' // Relasi ke item pesanan beserta produknya
        ])->orderBy('created_at', 'desc'); // Urutkan dari yang terbaru

        // Terapkan filter status
        if ($status === 'Menunggu Verifikasi') {
            $query->whereIn('status', ['Menunggu Verifikasi', 'Menunggu Pembayaran']);
        } else {
            $query->where('status', $status);
        }

        // Terapkan filter pencarian berdasarkan nomor invoice atau nama pelanggan
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Mengambil data pesanan dengan paginasi — ditampilkan 10 pesanan per halaman
        // Menggunakan through() agar transformasi data tidak merusak objek paginasi Laravel
        $orders = $query->paginate(10)->through(function($o) {
            return [
                'id'       => $o->invoice_no,           // Nomor invoice pesanan
                'customer' => $o->user->name ?? 'Pelanggan', // Nama pelanggan
                'date'     => $o->created_at->format('d M Y'), // Tanggal pesanan
                'total'    => $o->total,                // Total harga
                'payStatus'=> $o->payment->payment_status ?? 'Menunggu', // Status pembayaran
                'proof'    => $o->payment->proof_of_payment ?? null,     // Path bukti pembayaran
                'status'   => $o->status,               // Status pesanan (Diproses, Dikirim, dll)
                'address'  => $o->shipping_address,     // Alamat pengiriman
                'method'   => $o->shipping_method,      // Metode pengiriman
                'cancel_reason' => $o->cancel_reason,   // Alasan pembatalan
                // Detail item yang dipesan beserta harga dan subtotal masing-masing
                'items'    => $o->orderItems->map(function($item) {
                    return [
                        'product'  => $item->product->name ?? '-',
                        'qty'      => $item->qty,
                        'price'    => $item->price,
                        'subtotal' => $item->qty * $item->price,
                    ];
                })->toArray(),
            ];
        });

        // Kirim data pesanan, kata kunci pencarian, status filter, jumlah badge, dan retensi hari ke view
        return view('admin.orders', compact('orders', 'search', 'status', 'statusCounts', 'retentionDays'));
    }

    // Fungsi untuk menampilkan Kelola User (Hanya Pelanggan & Admin)
    public function users(Request $request)
    {
        // Pengaturan Inaktivitas Akun (Retention Days)
        $inactivityDays = (int) session('user_inactivity_days', 30);
        if ($inactivityDays > 0) {
            $threshold = now()->subDays($inactivityDays);
            // Nonaktifkan otomatis akun pelanggan yang tidak aktif melebihi batas waktu
            User::whereIn('role', ['pelanggan', 'customer', 'user'])
                ->where('status', 'aktif')
                ->where(function($q) use ($threshold) {
                    $q->where('last_login_at', '<', $threshold)
                      ->orWhere(function($sub) use ($threshold) {
                          $sub->whereNull('last_login_at')
                              ->where('updated_at', '<', $threshold);
                      });
                })
                ->update(['status' => 'tidak aktif']);

            // Hapus otomatis jika fitur auto-delete aktif
            if (session('user_auto_delete_inactive', false)) {
                User::whereIn('role', ['pelanggan', 'customer', 'user'])
                    ->where('status', 'tidak aktif')
                    ->delete();
            }
        }

        $roleFilter = $request->query('role', 'customer');
        if ($roleFilter === 'pelanggan') {
            $roleFilter = 'customer';
        }

        $search = $request->query('search', '');

        $query = User::query();

        if ($roleFilter === 'admin') {
            $roleFilter = 'admin';
            $query->where('role', 'admin');
        } else {
            $roleFilter = 'customer';
            $query->whereIn('role', ['pelanggan', 'customer', 'user']);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $allUsers = $query->orderBy('created_at', 'desc')->get();

        $users = $allUsers->map(function($u) {
            $isCustomer = in_array(strtolower($u->role), ['pelanggan', 'customer', 'user']);
            $lastActiveDate = $u->last_login_at ?? $u->updated_at;
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $isCustomer ? 'Pelanggan' : 'Admin',
                'raw_role' => $isCustomer ? 'customer' : 'admin',
                'phone' => $u->phone ?? '-',
                'status' => $u->status ?? 'aktif',
                'lastLogin' => $lastActiveDate ? $lastActiveDate->format('d M Y H:i') : '-'
            ];
        });

        $adminCount = User::where('role', 'admin')->count();
        $customerCount = User::whereIn('role', ['pelanggan', 'customer', 'user'])->count();
        $inactiveCustomerCount = User::whereIn('role', ['pelanggan', 'customer', 'user'])->where('status', 'tidak aktif')->count();

        return view('admin.users', compact(
            'users', 
            'roleFilter', 
            'search', 
            'adminCount', 
            'customerCount', 
            'inactiveCustomerCount',
            'inactivityDays'
        ));
    }

    public function admins(Request $request)
    {
        return $this->users($request);
    }

    // Hanya Admin yang bisa ditambahkan oleh Admin (Pengguna/Pelanggan tidak bisa ditambah oleh Admin)
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:aktif,tidak aktif',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => 'admin', // Mengunci role sebagai admin
            'password' => Hash::make($data['password']),
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.users', ['role' => 'admin'])
            ->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function storeAdmin(Request $request)
    {
        return $this->storeUser($request);
    }

    // Memperbarui data user
    // - Jika Pelanggan: Admin HANYA dapat mengubah status akun (Aktif / Tidak Aktif)
    // - Jika Admin: Admin dapat mengubah Nama, Email, HP, Status, dan Password
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $isCustomer = in_array(strtolower($user->role), ['pelanggan', 'customer', 'user']);

        if ($isCustomer) {
            // Pelanggan: HANYA ubah status
            $request->validate([
                'status' => 'required|in:aktif,tidak aktif',
            ]);
            $user->status = $request->input('status');
            $user->save();

            return redirect()->route('admin.users', ['role' => 'customer'])
                ->with('success', 'Status akun pelanggan ' . $user->name . ' berhasil diubah menjadi ' . ucfirst($user->status) . '.');
        }

        // Admin: Ubah data lengkap
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:aktif,tidak aktif',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->status = $data['status'];
        
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        
        $user->save();

        return redirect()->route('admin.users', ['role' => 'admin'])
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function updateAdmin(Request $request, $id)
    {
        return $this->updateUser($request, $id);
    }

    // Toggle status Aktif / Tidak Aktif secara cepat
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = ($user->status === 'aktif') ? 'tidak aktif' : 'aktif';
        $user->save();

        $roleParam = in_array(strtolower($user->role), ['pelanggan', 'customer', 'user']) ? 'customer' : 'admin';

        return redirect()->route('admin.users', ['role' => $roleParam])
            ->with('success', 'Status akun ' . $user->name . ' diubah menjadi ' . ucfirst($user->status) . '.');
    }

    // Pengaturan inaktivitas & Pembersihan manual / otomatis akun pelanggan mati (tidak aktif)
    public function clearInactiveUsers(Request $request)
    {
        $inactivityDays = (int) $request->input('inactivity_days', 30);
        $autoDelete = $request->has('auto_delete') ? true : false;

        session([
            'user_inactivity_days' => $inactivityDays,
            'user_auto_delete_inactive' => $autoDelete
        ]);

        $deletedCount = 0;
        if ($request->has('action_delete_now')) {
            // Hapus manual semua akun pelanggan yang berstatus 'tidak aktif'
            $deletedCount = User::whereIn('role', ['pelanggan', 'customer', 'user'])
                ->where('status', 'tidak aktif')
                ->delete();
        }

        $msg = "Pengaturan batas inaktivitas akun disimpan ({$inactivityDays} hari).";
        if ($deletedCount > 0) {
            $msg .= " Berhasil menghapus {$deletedCount} akun pelanggan mati (tidak aktif).";
        }

        return redirect()->route('admin.users', ['role' => 'customer'])->with('success', $msg);
    }

    // Fungsi untuk menghapus data user (Delete)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus.');
    }

    public function deleteAdmin($id)
    {
        return $this->deleteUser($id);
    }

    public function reports(Request $request)
    {
        $period = $request->query('period', 'Bulanan');
        $salesData = ProductData::getSalesData($period);
        $topProducts = ProductData::getTopProducts($period);

        // Mengambil data riwayat stok masuk dan keluar secara riil dari database (StockHistory)
        $stockQuery = StockHistory::with('product')->orderBy('created_at', 'desc');

        if ($period === 'Harian') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $stockQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($period === 'Tahunan') {
            $stockQuery->whereYear('created_at', '>=', Carbon::now()->year - 4);
        } else {
            $stockQuery->whereYear('created_at', Carbon::now()->year);
        }

        $allStockHistories = $stockQuery->get();

        // Memisahkan Riwayat Stok Masuk (qty > 0) dan Stok Keluar (qty < 0)
        $stockMasukList = $allStockHistories->filter(fn($h) => $h->qty > 0);
        $stockKeluarList = $allStockHistories->filter(fn($h) => $h->qty < 0);

        $totalStockMasuk = $stockMasukList->sum('qty');
        $totalStockKeluar = abs($stockKeluarList->sum('qty'));

        $totalRevenue = collect($salesData)->sum('revenue');
        $totalOrders = collect($salesData)->sum('orders');
        $averageOrder = $totalOrders ? round($totalRevenue / $totalOrders) : 0;
        
        $productsSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai'])
            ->sum('order_items.qty');

        $stats = [
            ['label' => 'Total Pendapatan DB', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'change' => 'Realisasi DB', 'color' => 'text-primary'],
            ['label' => 'Total Pesanan DB', 'value' => number_format($totalOrders, 0, ',', '.'), 'change' => 'Realisasi DB', 'color' => 'text-blue-600'],
            ['label' => 'Total Stok Masuk DB', 'value' => number_format($totalStockMasuk, 0, ',', '.') . ' unit', 'change' => 'Stok Masuk', 'color' => 'text-green-600'],
            ['label' => 'Total Stok Keluar DB', 'value' => number_format($totalStockKeluar, 0, ',', '.') . ' unit', 'change' => 'Stok Keluar', 'color' => 'text-red-500'],
        ];

        return view('admin.reports', compact(
            'salesData', 
            'topProducts', 
            'period', 
            'stats', 
            'stockMasukList', 
            'stockKeluarList', 
            'totalStockMasuk', 
            'totalStockKeluar'
        ));
    }

    // Fungsi untuk menampilkan halaman cetak PDF dokumen laporan berbasis DB lengkap
    public function printReport(Request $request)
    {
        $period = $request->query('period', 'Bulanan');
        $salesData = ProductData::getSalesData($period);
        $topProducts = ProductData::getTopProducts($period);

        $stockQuery = StockHistory::with('product')->orderBy('created_at', 'desc');

        if ($period === 'Harian') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $stockQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($period === 'Tahunan') {
            $stockQuery->whereYear('created_at', '>=', Carbon::now()->year - 4);
        } else {
            $stockQuery->whereYear('created_at', Carbon::now()->year);
        }

        $allStockHistories = $stockQuery->get();

        $stockMasukList = $allStockHistories->filter(fn($h) => $h->qty > 0);
        $stockKeluarList = $allStockHistories->filter(fn($h) => $h->qty < 0);

        $totalStockMasuk = $stockMasukList->sum('qty');
        $totalStockKeluar = abs($stockKeluarList->sum('qty'));

        $totalRevenue = collect($salesData)->sum('revenue');
        $totalOrders = collect($salesData)->sum('orders');
        $averageOrder = $totalOrders ? round($totalRevenue / $totalOrders) : 0;

        $productsSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['Menunggu Verifikasi', 'Diproses', 'Dikirim', 'Selesai'])
            ->sum('order_items.qty');

        return view('admin.reports-print', compact(
            'salesData', 
            'topProducts', 
            'period', 
            'totalRevenue',
            'totalOrders',
            'averageOrder',
            'productsSold',
            'stockMasukList', 
            'stockKeluarList', 
            'totalStockMasuk', 
            'totalStockKeluar'
        ));
    }

    public function acceptOrder($invoice)
    {
        $order = Order::where('invoice_no', $invoice)->firstOrFail();

        // Update order status to Diproses
        $order->status = 'Diproses';
        $order->save();

        // If there's a payment record, mark as Lunas/terverifikasi when accepting
        if ($order->payment) {
            $order->payment->payment_status = 'Lunas';
            $order->payment->save();
        }

        return redirect()->back()->with('success', 'Pesanan ' . $order->invoice_no . ' diterima dan diproses.');
    }

    // Fungsi untuk memproses penolakan / pembatalan pesanan oleh admin (WAJIB MEMILIKI ALASAN)
    public function rejectOrder(Request $request, $invoice)
    {
        // Validasi alasan pembatalan
        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ], [
            'cancel_reason.required' => 'Alasan pembatalan pesanan wajib diisi.',
        ]);

        $cancelReason = trim($request->input('cancel_reason'));

        // Mencari data pesanan berdasarkan invoice beserta relasi item dan produk secara riil
        $order = Order::with('orderItems.product')->where('invoice_no', $invoice)->firstOrFail();

        // Hanya memproses jika status belum Dibatalkan
        if ($order->status !== 'Dibatalkan') {
            \DB::beginTransaction(); // Memulai transaksi database agar operasi atomik/aman
            try {
                // Mengubah status pesanan menjadi Dibatalkan & menyimpan alasan pembatalan
                $order->status = 'Dibatalkan';
                $order->cancel_reason = $cancelReason;
                $order->save();

                // Mengubah status pembayaran pesanan menjadi Dibatalkan
                if ($order->payment) {
                    $order->payment->payment_status = 'Dibatalkan';
                    $order->payment->save();
                }

                // Mengembalikan kuantitas stok produk ke gudang (karena batal membeli) dan mencatat ke log
                foreach ($order->orderItems as $item) {
                    $product = $item->product;
                    if ($product) {
                        $product->stock += $item->qty; // Mengembalikan stok produk
                        $product->save();

                        // Mencatat aktivitas pembatalan/pengembalian stok ke tabel riwayat log stok
                        StockHistory::create([
                            'product_id' => $product->id,
                            'reference_id' => $order->id,
                            'reference_type' => 'Order',
                            'qty' => $item->qty, // Jumlah positif karena stok bertambah kembali
                            'transaction_type' => 'Pembatalan'
                        ]);
                    }
                }
                
                \DB::commit(); // Menyimpan seluruh perubahan jika berhasil
            } catch (\Exception $e) {
                \DB::rollBack(); // Membatalkan seluruh perubahan jika terjadi kesalahan/gagal
                return redirect()->back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Pesanan ' . $order->invoice_no . ' berhasil dibatalkan (Alasan: ' . $cancelReason . ') dan stok dikembalikan.');
    }

    public function shipOrder($invoice)
    {
        $order = Order::where('invoice_no', $invoice)->firstOrFail();

        // Only allow shipping if order is in Diproses
        if ($order->status !== 'Diproses') {
            return redirect()->back()->with('error', 'Pesanan harus dalam status Diproses untuk dikirim.');
        }

        $order->status = 'Dikirim';
        $order->save();

        return redirect()->back()->with('success', 'Pesanan ' . $order->invoice_no . ' telah dikirim.');
    }

    public function completeOrder($invoice)
    {
        $order = Order::where('invoice_no', $invoice)->firstOrFail();

        // Only allow completing if order is in Dikirim
        if ($order->status !== 'Dikirim') {
            return redirect()->back()->with('error', 'Pesanan harus dalam status Dikirim untuk diselesaikan.');
        }

        $order->status = 'Selesai';
        $order->save();

        return redirect()->back()->with('success', 'Pesanan ' . $order->invoice_no . ' telah selesai.');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('admin.dashboard');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? $user->phone;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        session(['username' => $user->name]);

        return redirect()->route('admin.profile')->with('success', 'Profil admin berhasil diperbarui!');
    }

    public function changePassword()
    {
        return view('admin.change-password');
    }

    // ---  LOGIKA GANTI PASSWORD SECARA REAL DAN AMAN ---
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['current_password' => 'Sesi login habis, silakan login ulang.']);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.'])->withInput();
        }

        // Laravel User model has a 'hashed' cast on password, so assign the plain text password
        // and let the model handle hashing once.
        $user->password = $request->password;
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Password admin berhasil diperbarui!');
    }

    // Fungsi untuk menghapus riwayat log stok berdasarkan pilihan jenis log (masuk/keluar/semua) & rentang waktu (1 minggu, 1 bulan, 3 bulan, semua)
    public function clearStockHistory(Request $request)
    {
        $period = $request->input('period', '30_days');
        $stockType = $request->input('stock_type', 'semua');
        $autoRetention = $request->input('auto_retention');

        if ($autoRetention !== null) {
            session(['stock_retention_days' => (int) $autoRetention]);
        }

        $query = StockHistory::query();
        $typeLabel = '';

        if ($stockType === 'masuk') {
            $query->where('qty', '>', 0);
            $typeLabel = 'Stok Masuk';
        } elseif ($stockType === 'keluar') {
            $query->where('qty', '<', 0);
            $typeLabel = 'Stok Keluar';
        } else {
            $typeLabel = 'Semua Log Stok (Masuk & Keluar)';
        }

        $periodLabel = '';

        switch ($period) {
            case '7_days':
                $query->where('created_at', '<', now()->subDays(7));
                $periodLabel = 'lebih dari 1 minggu (7 hari)';
                break;
            case '30_days':
                $query->where('created_at', '<', now()->subDays(30));
                $periodLabel = 'lebih dari 1 bulan (30 hari)';
                break;
            case '90_days':
                $query->where('created_at', '<', now()->subDays(90));
                $periodLabel = 'lebih dari 3 bulan (90 hari)';
                break;
            case 'all':
                $periodLabel = 'seluruh waktu';
                break;
            default:
                $query->where('created_at', '<', now()->subDays(30));
                $periodLabel = 'lebih dari 1 bulan';
                break;
        }

        $deletedCount = $query->delete();

        $message = "Berhasil menghapus {$deletedCount} data riwayat {$typeLabel} ({$periodLabel}).";
        if ($autoRetention !== null) {
            $daysLabel = $autoRetention == 7 ? '1 Minggu' : ($autoRetention == 30 ? '1 Bulan' : ($autoRetention == 90 ? '3 Bulan' : 'Nonaktif'));
            $message .= " Preferensi pembersihan otomatis disimpan ke: {$daysLabel}.";
        }

        return redirect()->back()->with('success', $message);
    }

    // Fungsi untuk menghapus riwayat pesanan (Selesai / Dibatalkan) berdasarkan pilihan rentang waktu
    public function clearOrderHistory(Request $request)
    {
        $period = $request->input('period', '30_days');
        $autoRetention = $request->input('auto_retention');

        if ($autoRetention !== null) {
            session(['order_retention_days' => (int) $autoRetention]);
        }

        $query = Order::whereIn('status', ['Selesai', 'Dibatalkan']);
        $label = '';

        switch ($period) {
            case '7_days':
                $query->where('created_at', '<', now()->subDays(7));
                $label = 'lebih dari 1 minggu (7 hari)';
                break;
            case '30_days':
                $query->where('created_at', '<', now()->subDays(30));
                $label = 'lebih dari 1 bulan (30 hari)';
                break;
            case '90_days':
                $query->where('created_at', '<', now()->subDays(90));
                $label = 'lebih dari 3 bulan (90 hari)';
                break;
            case 'all_completed':
                $label = 'semua pesanan Selesai & Dibatalkan';
                break;
            default:
                $query->where('created_at', '<', now()->subDays(30));
                $label = 'lebih dari 1 bulan';
                break;
        }

        $deletedCount = $query->delete();

        $message = "Berhasil menghapus {$deletedCount} data riwayat pesanan Selesai/Dibatalkan ({$label}).";
        if ($autoRetention !== null) {
            $daysLabel = $autoRetention == 7 ? '1 Minggu' : ($autoRetention == 30 ? '1 Bulan' : ($autoRetention == 90 ? '3 Bulan' : 'Nonaktif'));
            $message .= " Preferensi pembersihan otomatis disimpan ke: {$daysLabel}.";
        }

        return redirect()->back()->with('success', $message);
    }
}