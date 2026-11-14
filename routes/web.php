<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ShippingRateController as AdminShippingRateController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — O'Clock Watch E-Commerce (Tahap 4)
|--------------------------------------------------------------------------
*/

// ─── Halaman Publik Utama (CUS-01, CUS-02, CUS-03) ───────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

// ─── Guest Customer Authentication Routes ────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// ─── Guest Admin Login Routes ────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });
});

// ─── Authenticated Common Routes ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ─── Customer Protected Routes ───────────────────────────────────────────────
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/profile', [CustomerProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->whereNumber('item')->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->whereNumber('item')->name('cart.destroy');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/quote', [CheckoutController::class, 'quote'])->name('checkout.quote');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [PaymentController::class, 'index'])->name('orders.index');
        // Invoice page for a paid order (customer)
        Route::get('/orders/{order}/invoice', [PaymentController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/payment', [PaymentController::class, 'show'])->name('payment.show');
    Route::get('/orders/{order}/payment/status', [PaymentController::class, 'status'])->middleware('throttle:30,1')->name('payment.status');
    Route::post('/orders/{order}/payment/refresh', [PaymentController::class, 'refresh'])->middleware('throttle:6,1')->name('payment.refresh');
});

// ─── Admin Protected Routes ──────────────────────────────────────────────────
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Operasional
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Pembuatan Akun Administrator Baru (Terbatas untuk Admin yang Login)
    Route::get('/register', [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AdminAuthController::class, 'register'])->name('register.post');

    // CRUD Kategori Jam Tangan
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // CRUD Produk Jam Tangan & Stok
    Route::resource('products', AdminProductController::class);
    Route::patch('products/{product}/stock', [AdminProductController::class, 'updateStock'])->name('products.stock');
    Route::patch('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Manajemen Tarif Pengiriman Internal
    Route::resource('shipping-rates', AdminShippingRateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('shipping-rates/{shipping_rate}/toggle-status', [AdminShippingRateController::class, 'toggleStatus'])->name('shipping-rates.toggle-status');

    // Daftar Pelanggan (Read-Only)
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');

    // Pesanan Pelanggan (Read-Only pada Tahap 3)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderStatusController::class, 'update'])->name('orders.status.update')->middleware('can:update,order');
});
