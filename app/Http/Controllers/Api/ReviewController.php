<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'reviews' => 'required|array|min:1',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
        ]);

        $user = $request->user();

        // Ensure user can only review their own orders
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ensure order is completed
        if ($order->status !== 'delivered') {
            return response()->json(['message' => 'Can only review completed orders'], 400);
        }

        $reviews = [];

        DB::transaction(function () use ($request, $order, $user, &$reviews) {
            foreach ($request->reviews as $reviewData) {
                // Check if user already reviewed this product in this order
                $existingReview = Review::where('user_id', $user->id)
                    ->where('order_id', $order->id)
                    ->where('product_id', $reviewData['product_id'])
                    ->first();

                if ($existingReview) {
                    // Update existing review
                    $existingReview->update([
                        'rating' => $reviewData['rating'],
                    ]);
                    $reviews[] = $existingReview;
                } else {
                    // Create new review
                    $review = Review::create([
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'product_id' => $reviewData['product_id'],
                        'rating' => $reviewData['rating'],
                    ]);
                    $reviews[] = $review;
                }

                // Update product rating
                $this->updateProductRating($reviewData['product_id']);
            }
        });

        return response()->json([
            'message' => 'Reviews submitted successfully',
            'reviews' => $reviews,
        ], 201);
    }

    public function getOrderReviews(Request $request, Order $order)
    {
        // Ensure user can only view reviews for their own orders
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reviews = Review::where('order_id', $order->id)
            ->where('user_id', $request->user()->id)
            ->with('product')
            ->get();

        return response()->json(['reviews' => $reviews]);
    }

    public function getProductReviews(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => $product->rating,
            'total_reviews' => $product->reviews_count,
        ]);
    }

    private function updateProductRating($productId)
    {
        $product = Product::find($productId);
        $reviews = $product->reviews;

        if ($reviews->count() > 0) {
            $averageRating = $reviews->avg('rating');
            $product->update([
                'rating' => round($averageRating, 1),
                'reviews' => $reviews->count(),
            ]);
        }
    }
}
