<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index()
    {
        $user = Auth::user();

        $cartItems = Cart::where('user_id', $user->id)
            ->with('product')
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'data' => $cartItems,
            'subtotal' => $subtotal,
            'cart_count' => $cartItems->sum('quantity')
        ]);
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'integer|min:1',
            'color' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $user = Auth::user();
        $quantity = $request->input('quantity', 1);
        $selectedColor = $request->input('color');

        if ($quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Kuantitas melebihi stok yang tersedia'
            ], 400);
        }

        $cart = Cart::where('user_id', $user->id)
                   ->where('product_id', $product->id)
                   ->where('color', $selectedColor)
                   ->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $quantity;
            if ($newQuantity > $product->stock) {
                $maxAddable = $product->stock - $cart->quantity;
                if ($maxAddable <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk sudah mencapai batas maksimal di keranjang Anda'
                    ], 400);
                }
                $cart->quantity = $product->stock;
                $cart->save();

                $cartCount = Cart::where('user_id', $user->id)->sum('quantity');

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menambahkan maksimal {$maxAddable} items ke keranjang",
                    'cart_count' => $cartCount
                ]);
            }
            $cart->quantity = $newQuantity;
        } else {
            $cart = new Cart([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'color' => $selectedColor
            ]);
        }

        $cart->save();

        $cartCount = Cart::where('user_id', $user->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan'
            ], 404);
        }

        if ($cartItem->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ], 403);
        }

        $product = $cartItem->product;
        if (!$product) {
            $cartItem->delete();
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan dan telah dihapus dari keranjang'
            ], 404);
        }

        $newQuantity = $request->input('quantity');

        if ($newQuantity <= 0) {
            $cartItem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang'
            ]);
        }

        if ($newQuantity > $product->stock) {
            $newQuantity = $product->stock;
            $message = 'Kuantitas disesuaikan dengan stok yang tersedia (Stok: ' . $product->stock . ')';
        } else {
            $message = 'Kuantitas berhasil diperbarui';
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $cartItem->load('product')
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item keranjang tidak ditemukan'
            ], 404);
        }

        if ($cartItem->user_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang'
        ]);
    }

    /**
     * Get cart count
     */
    public function count()
    {
        $user = Auth::user();
        $cartCount = Cart::where('user_id', $user->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $user = Auth::user();
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }
}
