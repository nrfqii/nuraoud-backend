<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createPayment(Request $request, Order $order)
    {
        // Ensure user can only create payment for their own orders
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow payment creation for unpaid orders
        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Order is already paid'], 400);
        }

        // Prepare transaction details
        $transaction_details = [
            'order_id' => $order->id . '_' . time(),
            'gross_amount' => (int) $order->total_price,
        ];

        // Apply voucher discount if exists
        $discount = $order->voucher_discount ?? 0;
        $subtotal = $order->subtotal ?? $order->total_price;

        // Prepare customer details
        $customer_details = [
            'first_name' => $order->name,
            'phone' => $order->phone,
            'billing_address' => [
                'address' => $order->shipping_address,
                'city' => $order->city,
                'postal_code' => $order->postal_code,
            ],
            'shipping_address' => [
                'address' => $order->shipping_address,
                'city' => $order->city,
                'postal_code' => $order->postal_code,
            ],
        ];

        // Prepare item details
        $item_details = [];
        foreach ($order->orderItems as $item) {
            $item_details[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];
        }

        // Add shipping cost if applicable
        if ($order->shipping_method !== 'cod') {
            $shipping_cost = $this->calculateShippingCost($order->shipping_method);
            $item_details[] = [
                'id' => 'shipping',
                'price' => $shipping_cost,
                'quantity' => 1,
                'name' => 'Shipping Cost',
            ];
            $transaction_details['gross_amount'] += $shipping_cost;
        }

        // Add voucher discount if applicable
        if ($discount > 0) {
            $item_details[] = [
                'id' => 'voucher_discount',
                'price' => -(int) $discount,
                'quantity' => 1,
                'name' => 'Voucher Discount (' . $order->voucher_code . ')',
            ];
        }

        // Determine enabled payments. Support test/simulate mode by passing ?simulate=1
        // Frontend can call POST /api/orders/{id}/payment?simulate=1 to enable multiple sandbox methods
        $simulate = $request->boolean('simulate');

        // Allow explicit override from request body (array of payment method keys), useful for testing
        $overrideEnabled = $request->input('enabled_payments');

        $enabledPayments = $overrideEnabled && is_array($overrideEnabled)
            ? $overrideEnabled
            : $this->getEnabledPayments($order->payment_method, $simulate);

        // Prepare transaction data
        $transaction_data = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            'enabled_payments' => $enabledPayments,
        ];

        try {
            // Create Snap token using Midtrans API
            $snapToken = Snap::getSnapToken($transaction_data);

            // Store snap token in order for later reference
            $order->update(['snap_token' => $snapToken]);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            // Log full exception context to help diagnose Midtrans API errors (avoid logging secrets)
            Log::error('Midtrans payment creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Failed to create payment'], 500);
        }
    }

    public function createTransaction(Request $request)
    {
        $amount = $request->input('amount', 10000);

        // Prepare transaction details
        $transaction_details = [
            'order_id' => 'test_' . time(),
            'gross_amount' => (int) $amount,
        ];

        // Prepare customer details
        $customer_details = [
            'first_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567890',
        ];

        // Prepare item details
        $item_details = [
            [
                'id' => 'test_item',
                'price' => (int) $amount,
                'quantity' => 1,
                'name' => 'Test Item',
            ],
        ];

        // Prepare transaction data
        $transaction_data = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        ];

        try {
            // Create Snap token using Midtrans API
            $snapToken = Snap::getSnapToken($transaction_data);

            return response()->json([
                'snap_token' => $snapToken,
                'client_key' => config('midtrans.client_key'),
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans test payment creation failed: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to create test payment'], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Extract order ID (remove timestamp suffix)
        $orderId = explode('_', $request->order_id)[0];
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update order based on payment status
        switch ($request->transaction_status) {
            case 'capture':
            case 'settlement':
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing', // After successful payment, status becomes "processing" (dalam proses)
                ]);
                break;
            case 'pending':
                $order->update([
                    'payment_status' => 'pending',
                    'status' => 'pending', // Payment pending, status should be "pending" (menunggu pembayaran)
                ]);
                break;
            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
                break;
        }

        return response()->json(['message' => 'Callback processed']);
    }

    /**
     * Confirm payment result coming from client (Midtrans Snap onSuccess/onPending)
     * This allows the frontend to notify backend after Snap completes to update order status.
     */
    public function confirmPaymentResult(Request $request, Order $order)
    {
        // Only owner can confirm
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'transaction_status' => 'required|string',
            'status_code' => 'nullable|string',
            'gross_amount' => 'nullable|numeric',
        ]);

        $txStatus = $request->input('transaction_status');

        switch ($txStatus) {
            case 'capture':
            case 'settlement':
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);
                break;
            case 'pending':
                $order->update([
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
                break;
            case 'deny':
            case 'cancel':
            case 'expire':
            case 'failure':
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
                break;
        }

        return response()->json(['message' => 'Order updated', 'order' => $order->fresh()]);
    }

    private function calculateShippingCost($shippingMethod)
    {
        // Simple shipping cost calculation - set to 0 for demo
        $costs = [
            'jne' => 0,
            'jnt' => 0,
            'sicepat' => 0,
            'cod' => 0,
        ];

        return $costs[$shippingMethod] ?? 0;
    }



    private function getEnabledPaymentsWithSimulate($paymentMethod, $simulate = false)
    {
        $paymentMap = [
            'bank_transfer' => ['bank_transfer'],
            'gopay' => ['gopay'],
            'ovo' => ['ovo'],
            'dana' => ['dana'],
            'shopeepay' => ['shopeepay'],
            'linkaja' => ['linkaja'],
            'credit_card' => ['credit_card'],
            'cash_on_delivery' => [], // COD doesn't need payment gateway
        ];

        if ($simulate) {
            // In simulate mode enable common e-wallets + bank transfer + credit card for broader testing
            return ['gopay', 'dana', 'ovo', 'shopeepay', 'linkaja', 'bank_transfer', 'credit_card'];
        }

        return $paymentMap[$paymentMethod] ?? ['bank_transfer'];
    }

    // Backwards-compatible helper: keep original method name used in code
    private function getEnabledPayments($paymentMethod, $simulate = false)
    {
        return $this->getEnabledPaymentsWithSimulate($paymentMethod, $simulate);
    }

    private function getOrderStatusForPayment($paymentMethod)
    {
        // Set order status based on payment method
        if (in_array($paymentMethod, ['bank_transfer', 'gopay', 'ovo', 'dana'])) {
            return 'processing'; // Transfer payments go to processing
        } elseif ($paymentMethod === 'cash_on_delivery') {
            return 'processing'; // COD also goes to processing
        }

        return 'waiting'; // Default for unpaid
    }
}