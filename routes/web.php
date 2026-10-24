<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\WebAuthController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\OrderController as StorefrontOrderController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $products = Product::query()->latest()->limit(4)->get();

    return view('welcome', compact('products'));
})->name('home');

Route::get('/shop', function () {
    $products = Product::query()
        ->when(request('search'), fn ($query, $search) => $query->where('name', 'like', '%'.$search.'%'))
        ->when(request('min_price'), fn ($query, $price) => $query->where('price', '>=', $price))
        ->when(request('max_price'), fn ($query, $price) => $query->where('price', '<=', $price))
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return view('shop', compact('products'));
})->name('shop');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [WebAuthController::class, 'create'])->name('login');
    Route::post('/login', [WebAuthController::class, 'store'])
        ->middleware('throttle:auth')
        ->name('login.store');
    Route::get('/register', [WebAuthController::class, 'createRegistration'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])
        ->middleware('throttle:auth')
        ->name('register.store');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/checkout', [CartController::class, 'checkoutForm'])
    ->middleware('auth')
    ->name('checkout.create');
Route::post('/checkout', [CartController::class, 'checkout'])
    ->middleware(['auth', 'throttle:10,1'])
    ->name('checkout.store');
Route::get('/orders/{order}', [StorefrontOrderController::class, 'show'])
    ->middleware('auth')
    ->name('orders.show');

Route::post('/logout', [WebAuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:manage-products'])
    ->group(function (): void {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::resource('products', ProductController::class)->except(['index', 'show']);
    });
