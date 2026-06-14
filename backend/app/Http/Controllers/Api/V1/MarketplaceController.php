<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function categories(Request $request)
    {
        $perPage = $request->input('per_page', 8);
        $categories = \App\Models\Category::where('status', true)->paginate($perPage);
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
        $query = \App\Models\Product::with('category')->where('status', true);
        
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }
        
        $perPage = $request->input('per_page', 8);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);
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
        $product = \App\Models\Product::with('category')->where('status', true)->findOrFail($id);
        return response()->json(['data' => new \App\Http\Resources\ProductResource($product)]);
    }
}
