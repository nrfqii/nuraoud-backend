<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Load cart by user id and eager load items
        $cart = Cart::with('cartItems.product')->where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['cart' => null, 'items' => [], 'total' => 0]);
        }

        return response()->json([
            'cart' => $cart,
            'items' => $cart->cartItems,
            'total' => $cart->total
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Get or create cart for user
        // Ensure a Cart exists for the user
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Check if item already exists in cart
    $cartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
                'price' => $product->price
            ]);
        } else {
            $cart->cartItems()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]);
        }

        // Return updated cart
        $cart->load('cartItems.product');
        return response()->json([
            'message' => 'Item added to cart successfully',
            'cart' => $cart,
            'items' => $cart->cartItems,
            'total' => $cart->total
        ]);
    }

    public function updateItem(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Ensure the cart item belongs to the authenticated user
        if (!$cartItem->cart || $cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cart item updated successfully']);
    }

    public function removeItem(CartItem $cartItem)
    {
        // Ensure the cart item belongs to the authenticated user
        if (!$cartItem->cart || $cartItem->cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart successfully']);
    }

    public function clearCart()
    {
        $user = Auth::user();
        $cart = $user->cart;

        if ($cart) {
            $cart->cartItems()->delete();
        }

        return response()->json(['message' => 'Cart cleared successfully']);
    }
}
