<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json($carts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check stock
        $requestedQuantity = $request->quantity;
        
        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('product_id', $request->product_id)
                        ->first();

        $currentCartQuantity = $cartItem ? $cartItem->quantity : 0;
        
        if ($currentCartQuantity + $requestedQuantity > $product->stock) {
            return response()->json([
                'message' => 'Cannot add to cart. Only ' . $product->stock . ' items available in stock.'
            ], 422);
        }

        if ($cartItem) {
            $cartItem->quantity += $requestedQuantity;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $requestedQuantity
            ]);
        }

        return response()->json($cartItem->load('product'), 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('user_id', $request->user()->id)->findOrFail($id);
        $product = $cartItem->product;

        if ($request->quantity > $product->stock) {
            return response()->json([
                'message' => 'Cannot update cart. Only ' . $product->stock . ' items available in stock.'
            ], 422);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json($cartItem->load('product'));
    }

    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', $request->user()->id)->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart.']);
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Cart cleared.']);
    }
}
