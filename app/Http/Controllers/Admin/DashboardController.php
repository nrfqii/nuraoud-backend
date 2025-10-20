<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $bestsellerCount = Product::where('bestseller', true)->count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'waiting')->count();
        $processingOrders = Order::where('status', 'processing')->count();

        // Chart data
        $categoryData = [
            Product::where('category', 'Oud')->count(),
            Product::where('category', 'Perfume')->count(),
            Product::where('category', 'Attar')->count(),
            Product::where('category', 'Oil')->count()
        ];

        $orderStatusData = [
            Order::where('status', 'waiting')->count(),
            Order::where('status', 'processing')->count(),
            Order::where('status', 'shipped')->count(),
            Order::where('status', 'delivered')->count(),
            Order::where('status', 'cancelled')->count()
        ];

        $monthlySales = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlySales[] = Order::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $month)
                ->sum('total_price');
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalStock',
            'bestsellerCount',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'categoryData',
            'orderStatusData',
            'monthlySales'
        ));
    }

    public function products()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        return view('admin.products.create');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'scent' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'volume' => 'required|string|max:50',
            'bestseller' => 'boolean'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'brand' => $request->brand,
            'scent' => $request->scent,
            'description' => $request->description,
            'image' => $imagePath,
            'stock' => $request->stock,
            'volume' => $request->volume,
            'bestseller' => $request->boolean('bestseller', false),
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully');
    }

    public function editProduct(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'scent' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'volume' => 'required|string|max:50',
            'bestseller' => 'boolean'
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'brand' => $request->brand,
            'scent' => $request->scent,
            'description' => $request->description,
            'image' => $imagePath,
            'stock' => $request->stock,
            'volume' => $request->volume,
            'bestseller' => $request->boolean('bestseller', false),
        ]);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    public function deleteProduct(Product $product)
    {
        // Delete image file
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function orders()
    {
        $orders = Order::with('user', 'orderItems.product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:waiting,processing,shipped,cancelled'
        ]);

        // Prevent admin from setting status to 'delivered' directly
        // 'delivered' status should only be set by user when they confirm receipt
        if ($request->status === 'delivered') {
            return redirect()->back()->with('error', 'Delivered status can only be set by customer confirmation');
        }

        // Prevent admin from changing status if order is already delivered
        if ($order->status === 'delivered') {
            return redirect()->back()->with('error', 'Cannot change status of completed orders');
        }

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function chats()
    {
        // Get orders that have chat messages
        $ordersWithChats = Order::whereHas('chatMessages')
            ->with(['user', 'chatMessages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->withCount(['chatMessages as unread_by_admin_count' => function($q) {
                $q->where('is_admin', false)->where('is_admin_read', false);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.chats.index', compact('ordersWithChats'));
    }

    /**
     * Return the count of orders with unread customer messages for admin.
     * An order is considered "unread" for admin when the latest chat message
     * for that order comes from a customer (is_admin = false).
     */
    public function unreadChatCount()
    {
        // Count chat messages that are from customers and not yet marked read by admin
        $unread = ChatMessage::where('is_admin', false)
            ->where('is_admin_read', false)
            ->distinct('order_id')
            ->count('order_id');

        return response()->json(['unread' => (int) $unread]);
    }

    public function orderChat(Order $order)
    {
        // Load chat messages for this order
        $messages = ChatMessage::where('order_id', $order->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark customer messages as read for admin view
        ChatMessage::where('order_id', $order->id)
            ->where('is_admin', false)
            ->where('is_admin_read', false)
            ->update(['is_admin_read' => true]);

        return view('admin.orders.chat', compact('order', 'messages'));
    }

    public function sendChatMessage(Request $request, Order $order)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'is_admin' => true,
            'message' => $request->message,
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json(['message' => $message->load('user')]);
        }

        return redirect()->back();
    }

    public function getChatMessages(Order $order)
    {
        // Only allow admin or order owner
        $user = Auth::user();
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = ChatMessage::where('order_id', $order->id)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        // If the requesting user is an admin, mark customer messages as read
        if ($user->role === 'admin') {
            ChatMessage::where('order_id', $order->id)
                ->where('is_admin', false)
                ->where('is_admin_read', false)
                ->update(['is_admin_read' => true]);
        }

        return response()->json(['messages' => $messages]);
    }

    public function getOrderStatusUpdates()
    {
        // Get recent orders that might have status changes
        $orders = Order::select('id', 'status', 'updated_at')
            ->where('updated_at', '>', now()->subMinutes(30)) // Only check orders updated in last 30 minutes
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(['orders' => $orders]);
    }
}
