<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $featuredPhones = Product::phones()->featured()->take(4)->get();
            $featuredAccessories = Product::accessories()->featured()->take(4)->get();
            $phones = Product::phones()->latest()->take(4)->get();
            $accessories = Product::accessories()->latest()->take(4)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'featuredPhones' => $featuredPhones,
                    'featuredAccessories' => $featuredAccessories,
                    'phones' => $phones,
                    'accessories' => $accessories
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home data'
            ], 500);
        }
    }

    public function allProducts(Request $request)
    {
        try {
            $category = $request->input('category');
            $search = $request->input('search');
            $perPage = $request->input('per_page', 12);
            
            $query = Product::query();
            
            if ($category) {
                $query->where('category', $category);
            }
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            $products = $query->latest()->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }
    
    public function showProduct($id)
    {
        try {
            $product = Product::with(['reviews' => function($query) {
                $query->latest()->take(5);
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }
}
