<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

Route::get('/', function () {
    return view('welcome');
});

// Login routes
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/admin/dashboard');
    }
    return view('login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    if (Auth::attempt($credentials)) {
        return redirect('/admin/dashboard');
    }
    return back()->withErrors(['email' => 'Invalid credentials']);
})->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Product management
    Route::get('/products', [DashboardController::class, 'products'])->name('products');
    Route::get('/products/create', [DashboardController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [DashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [DashboardController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [DashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [DashboardController::class, 'deleteProduct'])->name('products.delete');

    // Order management
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::put('/orders/{order}/status', [DashboardController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/orders/status-updates', [DashboardController::class, 'getOrderStatusUpdates'])->name('orders.status-updates');

    // Chat management
    Route::get('/chats', [DashboardController::class, 'chats'])->name('chats');
    Route::get('/orders/{order}/chat', [DashboardController::class, 'orderChat'])->name('orders.chat');
    Route::post('/orders/{order}/chat', [DashboardController::class, 'sendChatMessage'])->name('orders.send-chat');
    Route::get('/orders/{order}/chat/messages', [DashboardController::class, 'getChatMessages'])->name('orders.chat.messages');
    // Unread chat count (AJAX) for admin sidebar badge
    Route::get('/chats/unread-count', [DashboardController::class, 'unreadChatCount'])->name('chats.unread-count');

    // Livewire Chat Test Route
    Route::get('/orders/{order}/chat-livewire', function (\App\Models\Order $order) {
        return view('admin.orders.chat-livewire', compact('order'));
    })->name('orders.chat.livewire');

    // Voucher management
    Route::resource('vouchers', VoucherController::class);
});

// Test payment route
Route::get('/payment', function () {
    return view('payment');
});
Route::post('/payment', [PaymentController::class, 'createTransaction']);

// Broadcasting auth endpoint that accepts session or Bearer token (Sanctum) auth
Route::post('/broadcasting/auth', function (Request $request) {
    // If session auth exists, use it
    if (Auth::check()) {
        return Broadcast::auth($request);
    }

    // Try bearer token (Sanctum)
    $header = $request->header('Authorization');
    if ($header && preg_match('/Bearer\s+(.*)$/i', $header, $m)) {
        $token = $m[1];
        $pat = PersonalAccessToken::findToken($token);
        if ($pat && $pat->tokenable) {
            Auth::setUser($pat->tokenable);
            return Broadcast::auth($request);
        }
    }

    return response('Unauthorized.', 403);
});
