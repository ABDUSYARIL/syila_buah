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
        $salesData = ProductData::$salesData;
        $topProducts = ProductData::$topProducts;
        $chartColors = ProductData::$CHART_COLORS;

        $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $totalRevenue = $orders->sum('total');
        $productsCount = $products->count();

        $stats = [
            ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'change' => '+18%', 'icon' => 'payments', 'color' => 'bg-green-light text-primary'],
            ['label' => 'Total Pesanan', 'value' => $orders->count(), 'change' => '+23%', 'icon' => 'local_mall', 'color' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Produk Terdaftar', 'value' => $productsCount, 'change' => 'Stabil', 'icon' => 'inventory_2', 'color' => 'bg-[#FFF3E0] text-accent'],
            ['label' => 'Pesanan Hari Ini', 'value' => $ordersToday, 'change' => 'vs kemarin', 'icon' => 'calendar_today', 'color' => 'bg-purple-50 text-purple-600'],
        ];

        return view('admin.dashboard', compact('orders', 'products', 'salesData', 'topProducts', 'chartColors', 'stats'));
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

    public function stock()
    {
        $products = Product::all();
        return view('admin.stock', compact('products'));
    }

    public function addStock(Request $request)
    {
        $productId = $request->input('product_id');
        $qty = $request->input('qty');
        $purchasePrice = $request->input('purchase_price');
        $notes = $request->input('notes');

        // Create StockEntry
        $entry = StockEntry::create([
            'product_id' => $productId,
            'supplier_id' => 1, // default supplier seeded
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

        return redirect()->back()->with('success', 'Stok masuk berhasil disimpan!');
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

    public function orders(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', 'Semua');
        
        $query = Order::with([
            'user',
            'payment',
            'orderItems.product'
        ])->orderBy('created_at', 'desc');

        if ($status !== 'Semua') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->get();

        // Transform into arrays matching view expects
        $orders = $orders->map(function($o) {
            return [
                'id' => $o->invoice_no,
                'customer' => $o->user->name ?? 'Pelanggan',
                'date' => $o->created_at->format('d M Y'),
                'total' => $o->total,
                'payStatus' => $o->payment->payment_status ?? 'Menunggu',
                'proof' => $o->payment->proof_of_payment ?? null,
                'status' => $o->status,
                'address' => $o->shipping_address,
                'method' => $o->shipping_method,
                'items' => $o->orderItems->map(function($item){
            return [
                'product' => $item->product->name,
                'qty' => $item->qty,
                'price' => $item->price,
                'subtotal' => $item->qty * $item->price,
            ];
        })->toArray(),
            ];
        });

        return view('admin.orders', compact('orders', 'search', 'status'));
    }

    public function admins()
    {
        $admins = User::where('role', 'admin')->get();
        
        // Transform into array matching view expectations
        $admins = $admins->map(function($a) {
            return [
                'name' => $a->name,
                'email' => $a->email,
                'role' => ucfirst($a->role) . ' Admin',
                'phone' => $a->phone ?? '-',
                'status' => ucfirst($a->status),
                'lastLogin' => $a->updated_at->format('d M Y H:i')
            ];
        });

        return view('admin.admins', compact('admins'));
    }

    public function storeAdmin(Request $request)
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
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.admins')->with('success', 'Admin baru berhasil ditambahkan.');
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

    public function rejectOrder($invoice)
    {
        $order = Order::where('invoice_no', $invoice)->firstOrFail();

        // Update order status to Dibatalkan
        $order->status = 'Dibatalkan';
        $order->save();

        // Optionally update payment status
        if ($order->payment) {
            $order->payment->payment_status = 'Dibatalkan';
            $order->payment->save();
        }

        return redirect()->back()->with('success', 'Pesanan ' . $order->invoice_no . ' dibatalkan.');
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
}