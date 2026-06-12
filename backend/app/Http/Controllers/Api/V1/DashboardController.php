<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;

class DashboardController extends Controller
{
    public function lowStock()
    {
        // Fetch products with stock <= 5
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->with('category')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return response()->json([
            'data' => $lowStockProducts
        ]);
    }
}
