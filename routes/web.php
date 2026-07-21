<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;

// Auth Routes
Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Guest & Landing Route
Route::get('/', [CustomerController::class, 'landingPage'])->name('landing');
Route::get('/catalog', [CustomerController::class, 'catalog'])->name('catalog');

// Customer Routes (Logged-in / Mock Session logic is implemented inside the controller)
Route::get('/home', [CustomerController::class, 'home'])->name('home');
Route::get('/product/{id}', [CustomerController::class, 'product'])->name('product.detail');
Route::get('/cart', [CustomerController::class, 'cart'])->name('cart');
Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [CustomerController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove/{id}', [CustomerController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
Route::get('/payment', [CustomerController::class, 'payment'])->name('payment');
Route::post('/payment/submit', [CustomerController::class, 'submitPayment'])->name('payment.submit');
Route::get('/order-status', [CustomerController::class, 'orderStatus'])->name('order.status');
Route::get('/order-detail', [CustomerController::class, 'orderDetail'])->name('order.detail');
Route::get('/history', [CustomerController::class, 'history'])->name('history');

Route::prefix('profile')->group(function () {
    Route::get('/', [CustomerController::class, 'profile'])->name('profile');
    Route::get('/edit', [CustomerController::class, 'editProfile'])->name('profile.edit');
    Route::post('/edit', [CustomerController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [CustomerController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/change-password', [CustomerController::class, 'updatePassword'])->name('profile.change-password.update');
});

// Rute Admin — semua rute di sini dilindungi oleh middleware 'admin.auth'
// Middleware ini memastikan hanya admin yang sudah login yang dapat mengakses halaman admin
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products/save', [AdminController::class, 'saveProduct'])->name('products.save');
    Route::post('/products/delete/{id}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    
    Route::get('/stock', [AdminController::class, 'stock'])->name('stock');
    Route::post('/stock/add', [AdminController::class, 'addStock'])->name('stock.add');
    Route::post('/stock/adjust', [AdminController::class, 'adjustStock'])->name('stock.adjust');
    Route::post('/stock/clear-history', [AdminController::class, 'clearStockHistory'])->name('stock.clear-history');
    
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::post('/orders/clear-history', [AdminController::class, 'clearOrderHistory'])->name('orders.clear-history');
    Route::post('/orders/{invoice}/accept', [AdminController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{invoice}/reject', [AdminController::class, 'rejectOrder'])->name('orders.reject');
    Route::post('/orders/{invoice}/ship', [AdminController::class, 'shipOrder'])->name('orders.ship');
    Route::post('/orders/{invoice}/complete', [AdminController::class, 'completeOrder'])->name('orders.complete');
    // Rute Kelola User (Admin & Pengguna / Pelanggan)
    Route::get('/users', [AdminController::class, 'users'])->name('users'); // Halaman daftar user (admin & pelanggan)
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store'); // Aksi tambah user (Admin baru)
    Route::post('/users/update/{id}', [AdminController::class, 'updateUser'])->name('users.update'); // Aksi update data user
    Route::post('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete'); // Aksi hapus data user
    Route::post('/users/toggle-status/{id}', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status'); // Aksi toggle status aktif/tidak aktif
    Route::post('/users/clear-inactive', [AdminController::class, 'clearInactiveUsers'])->name('users.clear-inactive'); // Aksi pengaturan inaktivitas & hapus akun mati

    // Backward compatibility alias rute /admins
    Route::get('/admins', function() { return redirect()->route('admin.users'); })->name('admins');
    Route::post('/admins', [AdminController::class, 'storeUser'])->name('admins.store');
    Route::post('/admins/update/{id}', [AdminController::class, 'updateUser'])->name('admins.update');
    Route::post('/admins/delete/{id}', [AdminController::class, 'deleteUser'])->name('admins.delete');
    
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports'); // Halaman laporan penjualan
    Route::get('/reports/print', [AdminController::class, 'printReport'])->name('reports.print'); // Halaman cetak PDF laporan DB
    
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::get('/change-password', [AdminController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password', [AdminController::class, 'updatePassword'])->name('change-password.update');
    Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
});
