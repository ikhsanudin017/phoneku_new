<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function stats()
    {
        $stats = [
            'total_users' => User::where('role', 'customer')->count(),
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total'),
            'recent_orders' => Order::with(['user'])->latest()->take(5)->get(),
            'popular_products' => Product::withCount('orders')
                ->orderBy('orders_count', 'desc')
                ->take(5)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function recentOrders()
    {
        $orders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function popularProducts()
    {
        $products = Product::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
