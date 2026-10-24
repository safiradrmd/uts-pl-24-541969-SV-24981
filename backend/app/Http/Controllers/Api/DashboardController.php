<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $today = now()->toDateString();
        $todaySales = Sale::query()->whereDate('created_at', $today);

        return response()->json([
            'status' => 'success',
            'data' => [
                'products_count' => Product::count(),
                'units_in_stock' => (int) Product::sum('stock'),
                'today_transactions' => (clone $todaySales)->count(),
                'today_revenue' => (int) (clone $todaySales)->sum('subtotal'),
                'low_stock_products' => Product::query()
                    ->where('stock', '<=', 5)
                    ->orderBy('stock')
                    ->orderBy('name')
                    ->limit(6)
                    ->get(['id', 'name', 'stock']),
                'recent_sales' => Sale::query()
                    ->with('items:id,sale_id,product_name,quantity,subtotal')
                    ->latest()
                    ->limit(6)
                    ->get(['id', 'reference', 'subtotal', 'created_at']),
            ],
        ]);
    }
}
