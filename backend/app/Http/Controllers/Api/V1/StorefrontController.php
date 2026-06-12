<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class StorefrontController extends Controller
{
    /**
     * Get all active categories for the public storefront.
     */
    public function categories()
    {
        // Only return categories that are marked as active
        $categories = Category::where('status', true)->get();
        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * Get all active products for the public storefront.
     */
    public function products(Request $request)
    {
        $query = Product::where('status', true)->with('category');

        // Optional category filter
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // We could add pagination here if there are many products,
        // but for now we'll get them all or limit them.
        $products = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $products
        ]);
    }
}
