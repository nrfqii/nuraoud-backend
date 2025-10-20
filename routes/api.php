<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\VoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Product API routes
Route::apiResource('products', ProductController::class);

// Cart API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addItem']);
    Route::put('/cart/items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);

    // Voucher API routes
    Route::post('/vouchers/apply', [VoucherController::class, 'applyVoucher']);

    // Order API routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::put('/orders/{order}/payment-method', [OrderController::class, 'updatePaymentMethod']);

    // Payment API routes
    Route::post('/orders/{order}/payment', [PaymentController::class, 'createPayment']);
    Route::post('/orders/{order}/confirm-payment', [PaymentController::class, 'confirmPaymentResult']);

    // Chat API routes for order-specific chat
    Route::get('/orders/{order}/chat', [\App\Http\Controllers\Api\ChatController::class, 'index']);
    Route::post('/orders/{order}/chat', [\App\Http\Controllers\Api\ChatController::class, 'store']);

    // Review API routes
    Route::post('/orders/{order}/reviews', [ReviewController::class, 'store']);
    Route::get('/orders/{order}/reviews', [ReviewController::class, 'getOrderReviews']);
});

// Public review routes
Route::get('/products/{product}/reviews', [ReviewController::class, 'getProductReviews']);

// Payment callback (no auth required)
Route::post('/payment/callback', [PaymentController::class, 'handleCallback']);
