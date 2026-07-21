<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
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

        $products = $query->get();
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
        $status = $request->query('status', 'Semua');
        
        // ── BADGE COUNT: Hitung jumlah pesanan per status untuk ditampilkan sebagai notif angka ──
        // Diambil secara efisien dengan groupBy tanpa memuat semua data
        $statusCounts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        // Tambahkan total 'Semua' sebagai jumlah keseluruhan
        $statusCounts['Semua'] = array_sum($statusCounts);

        // Membangun query pesanan beserta relasi pengguna, pembayaran, dan item pesanan
        $query = Order::with([
            'user',       // Relasi ke data pengguna/pelanggan
            'payment',    // Relasi ke data pembayaran
            'orderItems.product' // Relasi ke item pesanan beserta produknya
        ])->orderBy('created_at', 'desc'); // Urutkan dari yang terbaru

        // Terapkan filter status jika bukan 'Semua'
        if ($status !== 'Semua') {
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

    // Fungsi untuk menampilkan daftar admin
    public function admins()
    {
        // Mengambil semua data pengguna yang memiliki role 'admin'
        $admins = User::where('role', 'admin')->get();
        
        // Melakukan pemetaan data agar sesuai dengan variabel tampilan di view
        $admins = $admins->map(function($a) {
            return [
                'id' => $a->id, // MENYERTAKAN ID DARI DATABASE agar tombol Edit/Hapus berfungsi akurat
                'name' => $a->name,
                'email' => $a->email,
                'role' => ucfirst($a->role) . ' Admin',
                'phone' => $a->phone ?? '-',
                'status' => $a->status, // Mengirim status asli ('aktif' / 'tidak aktif') ke view
                'lastLogin' => $a->updated_at->format('d M Y H:i')
            ];
        });

        // Mengirim daftar admin ke view admin/admins
        return view('admin.admins', compact('admins'));
    }

    // Fungsi untuk menyimpan data admin baru (Create)
    public function storeAdmin(Request $request)
    {
        // Validasi input data admin baru
        $data = $request->validate([
            'name' => 'required|string|max:255', // Nama wajib diisi, maksimal 255 karakter
            'email' => 'required|email|max:255|unique:users,email', // Email wajib diisi, valid, unik di tabel users
            'phone' => 'nullable|string|max:30', // Nomor HP opsional, teks, maksimal 30 karakter
            'password' => 'required|string|min:8|confirmed', // Password wajib diisi, minimal 8 karakter, harus sama dengan kolom konfirmasi
            'status' => 'required|in:aktif,tidak aktif', // Status wajib diisi (aktif/tidak aktif)
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Menyimpan data admin baru ke database
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']), // Enkripsi password sebelum disimpan
            'role' => 'admin', // Mengunci role sebagai admin
            'status' => $data['status'],
        ]);

        // Mengalihkan kembali dengan rute admin.admins disertai pesan flash sukses
        return redirect()->route('admin.admins')->with('success', 'Admin baru berhasil ditambahkan.');
    }

    // Fungsi untuk memperbarui data admin (Update)
    public function updateAdmin(Request $request, $id)
    {
        // Mencari data admin berdasarkan id di tabel users
        $admin = User::findOrFail($id);
        
        // Validasi input data admin
        $data = $request->validate([
            'name' => 'required|string|max:255', // Nama wajib diisi, maksimal 255 karakter
            'email' => 'required|email|max:255|unique:users,email,' . $id, // Email unik kecuali miliknya sendiri
            'phone' => 'nullable|string|max:30', // Nomor HP opsional
            'password' => 'nullable|string|min:8|confirmed', // Password opsional saat edit (diisi hanya jika mau mengganti password)
            'status' => 'required|in:aktif,tidak aktif', // Status wajib diisi
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Memperbarui atribut-atribut data admin
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->phone = $data['phone'] ?? null;
        $admin->status = $data['status'];
        
        // Hanya mengubah password jika password baru diisi oleh admin
        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }
        
        $admin->save(); // Menyimpan perubahan data admin ke database

        // Mengalihkan kembali dengan rute admin.admins disertai pesan flash sukses
        return redirect()->route('admin.admins')->with('success', 'Data admin berhasil diperbarui.');
    }

    // Fungsi untuk menghapus data admin (Delete)
    public function deleteAdmin($id)
    {
        // Mencari data admin berdasarkan id di tabel users
        $admin = User::findOrFail($id);
        
        // Mencegah penghapusan akun milik sendiri demi alasan keamanan
        if ($admin->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $admin->delete(); // Menghapus data admin dari database

        // Mengalihkan kembali dengan rute admin.admins disertai pesan flash sukses
        return redirect()->route('admin.admins')->with('success', 'Admin berhasil dihapus.');
    }

    public function reports(Request $request)
    {
        $period = $request->query('period', 'Bulanan');
        $salesData = ProductData::getSalesData($period);
        $topProducts = ProductData::getTopProducts($period);

        $totalRevenue = collect($salesData)->sum('revenue');
        $totalOrders = collect($salesData)->sum('orders');
        $averageOrder = $totalOrders ? round($totalRevenue / $totalOrders) : 0;
        $productsSold = $totalOrders * 3;

        $stats = [
            ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'change' => '+18%', 'color' => 'text-primary'],
            ['label' => 'Total Pesanan', 'value' => number_format($totalOrders, 0, ',', '.'), 'change' => '+23%', 'color' => 'text-blue-600'],
            ['label' => 'Rata-rata Pesanan', 'value' => 'Rp ' . number_format($averageOrder, 0, ',', '.'), 'change' => '+5%', 'color' => 'text-accent'],
            ['label' => 'Produk Terjual', 'value' => number_format($productsSold, 0, ',', '.') . ' unit', 'change' => '+31%', 'color' => 'text-purple-600'],
        ];

        return view('admin.reports', compact('salesData', 'topProducts', 'period', 'stats'));
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

    // Fungsi untuk memproses penolakan / pembatalan pesanan oleh admin
    public function rejectOrder($invoice)
    {
        // Mencari data pesanan berdasarkan invoice beserta relasi item dan produk secara riil
        $order = Order::with('orderItems.product')->where('invoice_no', $invoice)->firstOrFail();

        // Hanya memproses jika status belum Dibatalkan
        if ($order->status !== 'Dibatalkan') {
            \DB::beginTransaction(); // Memulai transaksi database agar operasi atomik/aman
            try {
                // Mengubah status pesanan menjadi Dibatalkan
                $order->status = 'Dibatalkan';
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

        return redirect()->back()->with('success', 'Pesanan ' . $order->invoice_no . ' berhasil dibatalkan dan stok dikembalikan.');
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