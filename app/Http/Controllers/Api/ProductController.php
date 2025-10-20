<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('scent', 'like', "%{$searchTerm}%");
        }

        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        $products = $query->get();
        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'scent' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|string|max:255',
            'rating' => 'numeric|min:0|max:5',
            'reviews' => 'integer|min:0',
            'stock' => 'integer|min:0',
            'bestseller' => 'boolean',
            'volume' => 'required|string|max:50'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'price' => 'numeric|min:0',
            'category' => 'string|max:255',
            'brand' => 'string|max:255',
            'scent' => 'string',
            'description' => 'string',
            'image' => 'string|max:255',
            'rating' => 'numeric|min:0|max:5',
            'reviews' => 'integer|min:0',
            'stock' => 'integer|min:0',
            'bestseller' => 'boolean',
            'volume' => 'string|max:50'
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
