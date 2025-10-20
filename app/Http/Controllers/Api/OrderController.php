<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with(['orderItems.product:id,name,image,volume'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Return only important fields to the frontend
        $payload = $orders->map(function ($o) {
            return [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'total_price' => (float) $o->total_price,
                'subtotal' => (float) $o->subtotal,
                'voucher_discount' => (float) $o->voucher_discount,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'payment_method' => $o->payment_method,
                'shipping_method' => $o->shipping_method,
                'shipping_address' => $o->shipping_address,
                'phone' => $o->phone,
                'name' => $o->name,
                'city' => $o->city,
                'postal_code' => $o->postal_code,
                'notes' => $o->notes,
                'snap_token' => $o->snap_token,
                'created_at' => $o->created_at,
                'updated_at' => $o->updated_at,
                'orderItems' => $o->orderItems->map(function ($it) {
                    return [
                        'id' => $it->id,
                        'product_id' => $it->product_id,
                        'quantity' => $it->quantity,
                        'price' => (float) $it->price,
                        'product' => $it->product ? [
                            'id' => $it->product->id,
                            'name' => $it->product->name,
                            'image' => $it->product->image,
                            'volume' => $it->product->volume ?? null,
                        ] : null,
                    ];
                })->values(),
            ];
        });

        return response()->json($payload);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'shipping_method' => 'required|string|in:jne,jnt,sicepat,cod',
            'payment_method' => 'required|string|in:cash_on_delivery,bank_transfer,gopay,ovo,dana,online_payment',
            'notes' => 'nullable|string|max:500',
            'voucher_code' => 'nullable|string',
        ]);

        $user = $request->user();
        $cart = $user->cart()->with('cartItems.product')->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $subtotal = $cart->cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $discount = 0;
        $appliedVoucher = null;

        // Apply voucher if provided
        if ($request->voucher_code) {
            $voucher = \App\Models\Voucher::where('code', $request->voucher_code)->first();

            if ($voucher && $voucher->isValid()) {
                $discount = $voucher->calculateDiscount($subtotal);
                $appliedVoucher = $voucher;

                // Increment voucher usage count
                $voucher->increment('used_count');
            }
        }

        $totalPrice = $subtotal - $discount;

        $order = DB::transaction(function () use ($user, $cart, $totalPrice, $subtotal, $discount, $appliedVoucher, $request) {
            // Determine initial status based on payment method
            $initialStatus = $this->getInitialOrderStatus($request->payment_method);
            $initialPaymentStatus = $this->getInitialPaymentStatus($request->payment_method);

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => $initialStatus,
                'payment_status' => $initialPaymentStatus,
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'shipping_method' => $request->shipping_method,
                'phone' => $request->phone,
                'name' => $request->name,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'notes' => $request->notes,
                'voucher_code' => $appliedVoucher ? $appliedVoucher->code : null,
                'voucher_discount' => $discount,
                'subtotal' => $subtotal,
            ]);

            // Create order items from cart items
            foreach ($cart->cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);
            }

            // Clear cart
            $cart->cartItems()->delete();

            return $order;
        });

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
        ], 201);
    }

    public function show(Request $request, Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load(['orderItems.product:id,name,image,volume']);

        $payload = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'total_price' => (float) $order->total_price,
            'subtotal' => (float) $order->subtotal,
            'voucher_discount' => (float) $order->voucher_discount,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'shipping_method' => $order->shipping_method,
            'shipping_address' => $order->shipping_address,
            'phone' => $order->phone,
            'name' => $order->name,
            'city' => $order->city,
            'postal_code' => $order->postal_code,
            'notes' => $order->notes,
            'snap_token' => $order->snap_token,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'orderItems' => $order->orderItems->map(function ($it) {
                return [
                    'id' => $it->id,
                    'product_id' => $it->product_id,
                    'quantity' => $it->quantity,
                    'price' => (float) $it->price,
                    'product' => $it->product ? [
                        'id' => $it->product->id,
                        'name' => $it->product->name,
                        'image' => $it->product->image,
                        'volume' => $it->product->volume ?? null,
                    ] : null,
                ];
            })->values(),
        ];

        return response()->json($payload);
    }

    public function cancel(Request $request, Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow cancellation for pending or waiting orders
        if (!in_array($order->status, ['pending', 'waiting'])) {
            return response()->json(['message' => 'Order cannot be cancelled'], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Order cancelled successfully',
            'order' => $order
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Ensure user can only update their own orders
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    private function getInitialOrderStatus($paymentMethod)
    {
        // Set initial order status based on payment method
        if ($paymentMethod === 'cash_on_delivery') {
            return 'waiting'; // COD goes to waiting for approval
        }

        return 'pending'; // All other payment methods stay pending until payment is completed
    }

    private function getInitialPaymentStatus($paymentMethod)
    {
        // Set initial payment status based on payment method
        if ($paymentMethod === 'cash_on_delivery') {
            return 'cod'; // COD is considered paid upon delivery
        }

        return 'unpaid'; // Other methods start as unpaid
    }

    public function updatePaymentMethod(Request $request, Order $order)
    {
        // Ensure user can only update their own orders
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow payment method changes for unpaid orders
        if ($order->payment_status !== 'unpaid') {
            return response()->json(['message' => 'Cannot change payment method for paid orders'], 400);
        }

        $request->validate([
            'payment_method' => 'required|string|in:cash_on_delivery,bank_transfer,gopay,ovo,dana,online_payment',
        ]);

        // Update payment method and adjust status accordingly
        $newPaymentMethod = $request->payment_method;
        $newStatus = $this->getInitialOrderStatus($newPaymentMethod);
        $newPaymentStatus = $this->getInitialPaymentStatus($newPaymentMethod);

        $order->update([
            'payment_method' => $newPaymentMethod,
            'status' => $newStatus,
            'payment_status' => $newPaymentStatus,
        ]);

        return response()->json([
            'message' => 'Payment method updated successfully',
            'order' => $order->load('orderItems.product')
        ]);
    }
}
