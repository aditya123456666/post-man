<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'data' => $request->user()->only(['id', 'name', 'email', 'is_admin']),
        ]);
    });

    Route::post('/orders/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders/history', [OrderController::class, 'history']);

    Route::middleware('can:manage-products')->group(function (): void {
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    });
});
