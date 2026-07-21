<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function landingPage()
    {
        // Display 8 products on the landing page
        $products = Product::where('status', 'aktif')->take(8)->get();
        return view('customer.landing', compact('products'));
    }

    public function catalog(Request $request)
    {
        $category = $request->query('category', 'Semua');
        $search = $request->query('search', '');
        $sort = $request->query('sort', 'default');

        $query = Product::query()->where('status', 'aktif');

        // Apply Category Filter
        if ($category !== 'Semua') {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
        }

        // Apply Search Filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply Sorting
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        }

        $products = $query->get();

        return view('customer.catalog', compact('products', 'category', 'search', 'sort'));
    }

    public function home(Request $request)
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('home')]);
            return redirect()->route('login');
        }

        $search = $request->query('search', '');
        $category = $request->query('category', 'Semua');

        $query = Product::query()->where('status', 'aktif');

        if ($category !== 'Semua') {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
        }

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $products = $query->get();

        // Newer products (last 4 created)
        $newest = Product::where('status', 'aktif')->orderBy('created_at', 'desc')->take(4)->get();
        
        // Bestsellers (mock or sort by rating/stock for demo since we don't have sold counts in DB table directly)
        $bestsellers = Product::where('status', 'aktif')->orderBy('stock', 'asc')->take(4)->get();

        return view('customer.home', compact('products', 'newest', 'bestsellers', 'category', 'search'));
    }

    public function product($id)
    {
        $product = Product::findOrFail($id);
        $related = Product::where('status', 'aktif')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('customer.product', compact('product', 'related'));
    }

    public function cart()
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('cart')]);
            return redirect()->route('login');
        }

        $cart = session('cart', []);
        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        // Enforce login for Add to Cart or Buy Now
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => url()->previous()]);
            return redirect()->route('login');
        }

        $productId = (int) $request->input('product_id');
        $qty = max(1, (int) $request->input('qty', 1));

        $product = Product::findOrFail($productId);

        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'Stok produk ' . $product->name . ' sedang habis dan tidak dapat dipesan.');
        }

        $cart = session('cart', []);

        $currentQtyInCart = isset($cart[$productId]) ? $cart[$productId]['qty'] : 0;
        if (($currentQtyInCart + $qty) > $product->stock) {
            $maxCanAdd = max(0, $product->stock - $currentQtyInCart);
            if ($maxCanAdd <= 0) {
                return redirect()->back()->with('error', 'Stok yang dapat dipesan untuk ' . $product->name . ' sudah mencapai batas maksimal stok (' . $product->stock . ' ' . $product->unit . ').');
            }
            $qty = $maxCanAdd;
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $qty;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->name,
                'price' => $product->price,
                'unit' => $product->unit,
                'img' => $product->image,
                'qty' => $qty
            ];
        }

        session(['cart' => $cart]);

        if ($request->query('checkout')) {
            return redirect()->route('checkout');
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function updateCart(Request $request)
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('cart')]);
            return redirect()->route('login');
        }

        $productId = (int) $request->input('product_id');
        $qty = (int) $request->input('qty');

        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            if ($qty <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['qty'] = $qty;
            }
        }

        session(['cart' => $cart]);

        return redirect()->route('cart');
    }

    public function removeFromCart($id)
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('cart')]);
            return redirect()->route('login');
        }

        $cart = session('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }
        session(['cart' => $cart]);
        return redirect()->route('cart');
    }

    public function checkout()
    {
        // Enforce login for Checkout
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('checkout')]);
            return redirect()->route('login');
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            // Seed a mock item if empty for demo
            $p1 = Product::find(1) ?? Product::first();
            $p2 = Product::find(12) ?? Product::first();
            if ($p1 && $p2) {
                $cart = [
                    $p1->id => ['product_id' => $p1->id, 'name' => $p1->name, 'price' => $p1->price, 'unit' => $p1->unit, 'img' => $p1->image, 'qty' => 2],
                    $p2->id => ['product_id' => $p2->id, 'name' => $p2->name, 'price' => $p2->price, 'unit' => $p2->unit, 'img' => $p2->image, 'qty' => 1]
                ];
                session(['cart' => $cart]);
            }
        }

        return view('customer.checkout', compact('cart'));
    }

    public function payment(Request $request)
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('payment')]);
            return redirect()->route('login');
        }

        // Get shipping & payment info from checkout form parameters
        $shippingMethod = $request->query('shipping_method', 'Diantar');
        $shippingAddress = $request->query('shipping_address', 'Jl. Melati No. 12, Bandung');
        $notes = $request->query('notes', '');
        $payMethod = $request->query('pay_method', 'transfer');
        $paymentMethod = ($payMethod === 'qris') ? 'QRIS' : 'Transfer Bank';

        // Create the actual Order and items in database, and deduct stock
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('home');
        }

        $subtotal = 0;
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $shippingCost = ($shippingMethod === 'Ambil di Tempat') ? 0 : 15000;
        $total = $subtotal + $shippingCost;
        $invoiceNo = 'SB-' . date('ymd') . '-' . sprintf('%03d', rand(1, 999));

        // Enforce stock decrease rule
        \DB::beginTransaction();
        try {
            $userId = \App\Models\User::where('role', 'pelanggan')->first()->id ?? 2;

            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_no' => $invoiceNo,
                'shipping_address' => $shippingAddress,
                'shipping_method' => $shippingMethod,
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total' => $total,
                'status' => 'Menunggu Pembayaran',
                'notes' => $notes
            ]);

            // Save payment method in payments table
            Payment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'payment_status' => 'Menunggu'
            ]);

            foreach($cart as $productId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'qty' => $item['qty'],
                    'price' => $item['price']
                ]);

                // Deduct stock
                $product = Product::findOrFail($productId);
                $product->stock = max(0, $product->stock - $item['qty']);
                $product->save();

                // Create stock history log
                // "Setiap perubahan stok wajib tercatat pada tabel Riwayat Stok."
                StockHistory::create([
                    'product_id' => $product->id,
                    'reference_id' => $order->id,
                    'reference_type' => 'Order',
                    'qty' => -$item['qty'],
                    'transaction_type' => 'Checkout'
                ]);
            }

            // Save order ID to session for payment page
            session(['last_order_id' => $order->id]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors('Gagal memproses pesanan: ' . $e->getMessage());
        }

        // Clear cart
        session()->forget('cart');

        return view('customer.payment', compact('order'));
    }

    public function submitPayment(Request $request)
    {
        if (session('role') !== 'pelanggan') {
            return redirect()->route('login');
        }

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'proof_file' => 'required|image|mimes:jpg,jpeg,png|max:4096',
        ], [
            'proof_file.required' => 'Bukti pembayaran wajib diunggah.',
        ]);

        $orderId = $request->input('order_id');
        $order = Order::findOrFail($orderId);

        // Update order status to Menunggu Verifikasi
        $order->status = 'Menunggu Verifikasi';
        $order->save();

        $proofPath = $request->file('proof_file')->store('payments', 'public');

        // Update Payment entry with proof
        $payment = Payment::where('order_id', $order->id)->first();
        if ($payment) {
            $payment->update([
                'proof_of_payment' => $proofPath,
                'payment_date' => Carbon::now(),
                'payment_status' => 'Menunggu',
            ]);
        } else {
            Payment::create([
                'order_id' => $order->id,
                'method' => $request->input('payment_method', 'Transfer Bank'),
                'proof_of_payment' => $proofPath,
                'payment_status' => 'Menunggu',
                'payment_date' => Carbon::now(),
            ]);
        }

        return redirect()->route('order.status', ['order_id' => $order->id]);
    }

    public function orderStatus(Request $request)
    {
        $orderId = $request->query('order_id') ?? session('last_order_id');
        $order = Order::find($orderId) ?? Order::orderBy('created_at', 'desc')->first();
        
        return view('customer.order-status', compact('order'));
    }

    public function orderDetail(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::with(['orderItems.product', 'payment'])->find($orderId) ?? Order::with(['orderItems.product', 'payment'])->orderBy('created_at', 'desc')->first();

        return view('customer.order-detail', compact('order'));
    }

    public function history()
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('history')]);
            return redirect()->route('login');
        }

        $userId = Auth::id();

        $orders = Order::with(['orderItems.product', 'payment'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($o) {
                return [
                    'db_id' => $o->id,
                    'id' => $o->invoice_no,
                    'date' => $o->created_at->format('d M Y H:i'),
                    'total' => $o->total,
                    'status' => $o->status,
                    'items' => $o->orderItems->map(function($it) {
                        return [
                            'name' => $it->product->name ?? 'Produk',
                            'qty' => $it->qty,
                            'unit' => $it->product->unit ?? '',
                            'price' => $it->price,
                            'img' => $it->product->image ?? null
                        ];
                    })->toArray(),
                    'payment' => $o->payment ? [
                        'method' => $o->payment->method,
                        'status' => $o->payment->payment_status,
                        'proof' => $o->payment->proof_of_payment,
                        'date' => $o->payment->payment_date ? $o->payment->payment_date->format('d M Y H:i') : null
                    ] : null
                ];
            });

        return view('customer.history', compact('orders'));
    }

    public function profile()
    {
        // Enforce login for profile page
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('profile')]);
            return redirect()->route('login');
        }

        return view('customer.profile');
    }

    public function editProfile()
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('profile.edit')]);
            return redirect()->route('login');
        }
        return view('customer.edit-profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? $user->phone;
        $user->address = $data['address'] ?? $user->address;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        session(['username' => $user->name]);

        return redirect()->route('profile')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function changePassword()
    {
        if (session('role') !== 'pelanggan') {
            session(['url.intended' => route('profile.change-password')]);
            return redirect()->route('login');
        }
        return view('customer.change-password');
    }

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

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.'])->withInput();
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password Anda berhasil diperbarui!');
    }
}
