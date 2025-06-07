<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Featured products
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'category' => 'required|in:handphone,accessory',
            'is_featured' => 'boolean',
            'stock' => 'required|integer|min:0',
            'color' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['image', 'image2', 'image3']);

        // Set is_featured to false if not provided
        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        // Handle image uploads
        $imageFields = ['image', 'image2', 'image3'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                try {
                    $image = $request->file($field);

                    if (!$image->isValid()) {
                        return response()->json([
                            'success' => false,
                            'message' => "File {$field} tidak valid."
                        ], 400);
                    }

                    $imageName = Str::slug($request->name) . '-' . $field . '-' . time() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('products', $imageName, 'public');

                    if (!$path) {
                        return response()->json([
                            'success' => false,
                            'message' => "Gagal menyimpan {$field} ke storage."
                        ], 500);
                    }

                    $data[$field] = $path;

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => "Error upload {$field}: " . $e->getMessage()
                    ], 500);
                }
            }
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dibuat',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Get related products
        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $product,
            'related_products' => $relatedProducts
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'category' => 'required|in:handphone,accessory',
            'is_featured' => 'boolean',
            'stock' => 'required|integer|min:0',
            'color' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['image', 'image2', 'image3']);

        // Set is_featured to false if not provided
        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        // Handle image uploads
        $imageFields = ['image', 'image2', 'image3'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if exists
                if ($product->$field) {
                    Storage::disk('public')->delete($product->$field);
                }

                $image = $request->file($field);
                $imageName = Str::slug($request->name) . '-' . $field . '-' . time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $imageName, 'public');
                $data[$field] = $path;
            }
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Delete product images if exist
        $imageFields = ['image', 'image2', 'image3'];
        foreach ($imageFields as $field) {
            if ($product->$field) {
                Storage::disk('public')->delete($product->$field);
            }
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    /**
     * Get featured products for homepage
     */
    public function featured(Request $request)
    {
        $category = $request->get('category');
        $limit = $request->get('limit', 4);

        $query = Product::where('is_featured', true);

        if ($category) {
            $query->where('category', $category);
        }

        $products = $query->latest()->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $category = $request->get('category');

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Query pencarian diperlukan'
            ], 400);
        }

        $productsQuery = Product::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        if ($category) {
            $productsQuery->where('category', $category);
        }

        $products = $productsQuery->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'query' => $query
        ]);
    }
}
