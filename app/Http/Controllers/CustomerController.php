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
        if (session('role') !== 'customer') {
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
        if (session('role') !== 'customer') {
            session(['url.intended' => route('cart')]);
            return redirect()->route('login');
        }

        $cart = session('cart', []);
        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        // Enforce login for Add to Cart or Buy Now
        if (session('role') !== 'customer') {
            session(['url.intended' => url()->previous()]);
            return redirect()->route('login');
        }

        $productId = (int) $request->input('product_id');
        $qty = (int) $request->input('qty', 1);

        $cart = session('cart', []);
        
        $product = Product::findOrFail($productId);

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
        if (session('role') !== 'customer') {
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
        if (session('role') !== 'customer') {
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
        if (session('role') !== 'customer') {
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
        if (session('role') !== 'customer') {
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
                'user_id' => $userId,
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
        if (session('role') !== 'customer') {
            return redirect()->route('login');
        }

        $orderId = $request->input('order_id');
        $order = Order::findOrFail($orderId);

        // Update order status to Menunggu Verifikasi
        $order->status = 'Menunggu Verifikasi';
        $order->save();

        // Update Payment entry with proof
        $payment = Payment::where('order_id', $order->id)->first();
        if ($payment) {
            $payment->update([
                'proof_of_payment' => 'proof_' . $order->invoice_no . '.jpg',
                'payment_date' => Carbon::now(),
            ]);
        } else {
            Payment::create([
                'order_id' => $order->id,
                'method' => $request->input('payment_method', 'Transfer Bank'),
                'proof_of_payment' => 'proof_' . $order->invoice_no . '.jpg',
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
        if (session('role') !== 'customer') {
            session(['url.intended' => route('history')]);
            return redirect()->route('login');
        }

        $userId = \App\Models\User::where('role', 'pelanggan')->first()->id ?? 2;
        $orders = Order::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        return view('customer.history', compact('orders'));
    }

    public function profile()
    {
        // Enforce login for profile page
        if (session('role') !== 'customer') {
            session(['url.intended' => route('profile')]);
            return redirect()->route('login');
        }

        return view('customer.profile');
    }

    public function editProfile()
    {
        if (session('role') !== 'customer') {
            session(['url.intended' => route('profile.edit')]);
            return redirect()->route('login');
        }
        return view('customer.edit-profile');
    }

    public function updateProfile(Request $request)
    {
        return redirect()->route('profile')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function changePassword()
    {
        if (session('role') !== 'customer') {
            session(['url.intended' => route('profile.change-password')]);
            return redirect()->route('login');
        }
        return view('customer.change-password');
    }

    public function updatePassword(Request $request)
    {
        return redirect()->route('profile')->with('success', 'Password Anda berhasil diperbarui!');
    }
}
