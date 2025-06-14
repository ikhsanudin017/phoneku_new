<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            // Data dummy untuk testing
            $stats = [
                'total_orders' => Order::count() ?? 0,
                'total_users' => User::where('role', 'user')->count() ?? 0,
                'total_revenue' => Order::where('status', 'completed')->sum('total') ?? 0
            ];

            $recentOrders = Order::with(['user'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->user->name ?? 'Unknown',
                        'total' => $order->total ?? 0,
                        'status' => $order->status ?? 'pending',
                        'created_at' => $order->created_at->format('Y-m-d H:i:s')
                    ];
                });

            $popularProducts = Product::withCount('orderItems as sold')
                ->orderBy('sold', 'desc')
                ->take(5)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image' => $product->image,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'sold' => $product->sold
                    ];
                });

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'popular_products' => $popularProducts
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            // Return minimal data jika error
            return response()->json([
                'success' => true,
                'stats' => [
                    'total_orders' => 0,
                    'total_users' => 0,
                    'total_revenue' => 0
                ],
                'recent_orders' => [],
                'popular_products' => []
            ]);
        }
    }
}
