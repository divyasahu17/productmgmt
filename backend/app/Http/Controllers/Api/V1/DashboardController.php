<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;

class DashboardController extends Controller
{
    public function lowStock()
    {
        $lowStockProducts = \Illuminate\Support\Facades\Cache::remember('dashboard_low_stock', 300, function () {
            return Product::where('stock', '<=', 5)
                ->with('category')
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get();
        });

        return response()->json([
            'data' => $lowStockProducts
        ]);
    }

    public function chartData()
    {
        $chartData = \Illuminate\Support\Facades\Cache::remember('dashboard_chart_data', 300, function () {
            $categoryData = \App\Models\Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name as label', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('categories.name')
                ->get();

            $labels = $categoryData->pluck('label');
            $data = $categoryData->pluck('count');

            // Active vs Inactive
            $statusData = \App\Models\Product::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();
                
            $activeCount = $statusData->where('status', true)->first()->count ?? 0;
            $inactiveCount = $statusData->where('status', false)->first()->count ?? 0;

            return [
                'categories' => [
                    'labels' => $labels,
                    'data' => $data
                ],
                'status' => [
                    'active' => $activeCount,
                    'inactive' => $inactiveCount
                ]
            ];
        });

        return response()->json($chartData);
    }
}
