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

class AdminController extends Controller
{
    public function dashboard()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        $products = Product::all();
        $salesData = ProductData::$salesData;
        $topProducts = ProductData::$topProducts;
        $chartColors = ProductData::$CHART_COLORS;

        return view('admin.dashboard', compact('orders', 'products', 'salesData', 'topProducts', 'chartColors'));
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
        $qty = (int) $request->input('qty'); // actual stock value
        $type = $request->input('type'); // Buah Busuk, Buah Rusak, Penyusutan, Kehilangan
        $notes = $request->input('notes');

        $product = Product::findOrFail($productId);
        $difference = $qty - $product->stock;

        // Create StockAdjustment
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
        
        $query = Order::query()->orderBy('created_at', 'desc');

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
                'status' => $o->status,
                'address' => $o->shipping_address,
                'method' => $o->shipping_method
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

    public function reports()
    {
        $salesData = ProductData::$salesData;
        $topProducts = ProductData::$topProducts;
        return view('admin.reports', compact('salesData', 'topProducts'));
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function changePassword()
    {
        return view('admin.change-password');
    }

    public function updatePassword(Request $request)
    {
        return redirect()->route('admin.profile')->with('success', 'Password admin berhasil diperbarui!');
    }
}
