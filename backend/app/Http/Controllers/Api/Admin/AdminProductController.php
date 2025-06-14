<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminProductController extends BaseAdminController
{
    /**
     * Display a listing of products with filtering and pagination
     */
    public function index(Request $request)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $query = Product::query();

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Price range filter
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Stock filter
            if ($request->filled('stock_status')) {
                switch ($request->stock_status) {
                    case 'in_stock':
                        $query->where('stock', '>', 0);
                        break;
                    case 'out_of_stock':
                        $query->where('stock', 0);
                        break;
                    case 'low_stock':
                        $query->where('stock', '>', 0)->where('stock', '<=', 10);
                        break;
                }
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage);

            return $this->successResponse([
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch products: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch products', null, 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|in:handphone,accessory',
                'stock' => 'required|integer|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'is_featured' => 'boolean',
                'color' => 'nullable|string',
                'selectedColors' => 'nullable|array',
                'selectedColors.*' => 'string',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'image3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $data = $validator->validated();
            $data['slug'] = Str::slug($data['name']);
            $data['status'] = 'active';

            // Handle image uploads
            $imageFields = ['image', 'image2', 'image3'];
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $image = $request->file($field);
                    $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('products', $filename, 'public');
                    $data[$field] = $path;
                }
            }

            $product = Product::create($data);

            $this->logAdminAction('Created new product', [
                'product_id' => $product->id,
                'product_name' => $product->name
            ]);

            return $this->successResponse($product, 'Product created successfully', 201);

        } catch (\Exception $e) {
            Log::error('Product creation failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to create product', null, 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $product = Product::findOrFail($id);
            return $this->successResponse($product);
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch product: ' . $e->getMessage());
            return $this->errorResponse('Product not found', null, 404);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'category' => 'sometimes|string|in:handphone,accessory',
                'stock' => 'sometimes|integer|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'is_featured' => 'sometimes|boolean',
                'color' => 'nullable|string',
                'selectedColors' => 'nullable|array',
                'selectedColors.*' => 'string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'image3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'sometimes|string|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $data = $validator->validated();

            if (isset($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
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
                    $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('products', $filename, 'public');
                    $data[$field] = $path;
                }
            }

            $product->update($data);
            $product->refresh();

            $this->logAdminAction('Updated product', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'updated_fields' => array_keys($data)
            ]);

            return $this->successResponse($product, 'Product updated successfully');

        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to update product', null, 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $product = Product::findOrFail($id);

            // Delete associated images
            $imageFields = ['image', 'image2', 'image3'];
            foreach ($imageFields as $field) {
                if ($product->$field) {
                    Storage::disk('public')->delete($product->$field);
                }
            }

            $productName = $product->name;
            $product->delete();

            $this->logAdminAction('Deleted product', [
                'product_id' => $id,
                'product_name' => $productName
            ]);

            return $this->successResponse(null, 'Product deleted successfully');

        } catch (\Exception $e) {
            Log::error('Product deletion failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete product', null, 500);
        }
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus($id)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $product = Product::findOrFail($id);
            $product->status = $product->status === 'active' ? 'inactive' : 'active';
            $product->save();

            $this->logAdminAction('Toggled product status', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'new_status' => $product->status
            ]);

            return $this->successResponse($product, 'Product status updated successfully');

        } catch (\Exception $e) {
            Log::error('Product status toggle failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to update product status', null, 500);
        }
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(Request $request)
    {
        if ($response = $this->validateAdmin()) {
            return $response;
        }

        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:products,id'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $products = Product::whereIn('id', $request->ids)->get();

            foreach ($products as $product) {
                // Delete associated images
                $imageFields = ['image', 'image2', 'image3'];
                foreach ($imageFields as $field) {
                    if ($product->$field) {
                        Storage::disk('public')->delete($product->$field);
                    }
                }
                $product->delete();
            }

            $this->logAdminAction('Bulk deleted products', [
                'product_ids' => $request->ids,
                'count' => count($request->ids)
            ]);

            return $this->successResponse(null, 'Products deleted successfully');

        } catch (\Exception $e) {
            Log::error('Bulk product deletion failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete products', null, 500);
        }
    }
}
