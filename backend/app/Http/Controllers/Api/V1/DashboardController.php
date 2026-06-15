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

    public function stats()
    {
        $totalProducts = Product::count();
        $totalCategories = \App\Models\Category::count();
        $lowStockCount = Product::where('stock', '<=', 5)->count();
        
        // Calculate Total Value (sum of price * stock)
        $totalValue = Product::selectRaw('SUM(price * stock) as total_value')->value('total_value');

        // Chart Data 1: Products per Category
        $categoriesDistribution = \App\Models\Category::withCount('products')
            ->having('products_count', '>', 0)
            ->get()
            ->map(function ($cat) {
                return [
                    'label' => $cat->name,
                    'count' => $cat->products_count
                ];
            });

        // Chart Data 2: Product Status (Active vs Inactive)
        $activeProducts = Product::where('status', true)->count();
        $inactiveProducts = Product::where('status', false)->count();

        return response()->json([
            'data' => [
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
                'low_stock_count' => $lowStockCount,
                'total_value' => $totalValue ? (float) $totalValue : 0,
                'chart_data' => $categoriesDistribution,
                'status_data' => [
                    'active' => $activeProducts,
                    'inactive' => $inactiveProducts
                ]
            ]
        ]);
    }
}
