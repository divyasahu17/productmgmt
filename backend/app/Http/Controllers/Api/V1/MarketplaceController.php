<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function categories()
    {
        $categories = \App\Models\Category::where('status', true)->get();
        return response()->json(['data' => $categories]);
    }

    public function products(Request $request)
    {
        $query = \App\Models\Product::with('category')->where('status', true);
        
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $products]);
    }

    public function showProduct($id)
    {
        $product = \App\Models\Product::with('category')->where('status', true)->findOrFail($id);
        return response()->json(['data' => $product]);
    }
}
