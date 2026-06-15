<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketplaceController extends Controller
{
    public function categories(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $page = $request->input('page', 1);
        $cacheKey = "marketplace_categories_page_{$page}_limit_{$perPage}";

        $categories = Cache::remember($cacheKey, 86400, function () use ($perPage) {
            return \App\Models\Category::where('status', true)->paginate($perPage);
        });

        return response()->json([
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'total' => $categories->total()
            ]
        ]);
    }

    public function products(Request $request)
    {
        $categoryId = $request->input('category_id', 'all');
        $perPage = $request->input('per_page', 8);
        $page = $request->input('page', 1);
        $cacheKey = "marketplace_products_cat_{$categoryId}_page_{$page}_limit_{$perPage}";

        $products = Cache::remember($cacheKey, 86400, function () use ($categoryId, $perPage, $request) {
            $query = \App\Models\Product::with('category')->where('status', true);
            
            if ($categoryId !== 'all') {
                $query->where('category_id', $categoryId);
            }
            
            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        });

        return response()->json([
            'data' => \App\Http\Resources\ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total()
            ]
        ]);
    }

    public function showProduct($id)
    {
        $cacheKey = "marketplace_product_{$id}";
        $product = Cache::remember($cacheKey, 86400, function () use ($id) {
            return \App\Models\Product::with('category')->where('status', true)->findOrFail($id);
        });
        
        return response()->json(['data' => new \App\Http\Resources\ProductResource($product)]);
    }
}
