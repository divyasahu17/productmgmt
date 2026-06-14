<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get the authenticated user's cart items.
     */
    public function index(Request $request)
    {
        $items = $request->user()->cartItems()->with('product.category')->get();
        
        $formattedItems = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'product' => new ProductResource($item->product),
            ];
        });

        return response()->json($formattedItems);
    }

    /**
     * Add an item to the cart or update its quantity if it already exists.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'Requested quantity exceeds available stock.'], 422);
        }

        $cartItem = $request->user()->cartItems()->updateOrCreate(
            ['product_id' => $request->product_id],
            ['quantity' => $request->quantity]
        );

        return response()->json([
            'message' => 'Cart updated successfully.',
            'item' => [
                'id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'product' => new ProductResource($product),
            ]
        ]);
    }

    /**
     * Sync local cart with backend.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'items' => 'array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->stock >= $item['quantity']) {
                    $user->cartItems()->updateOrCreate(
                        ['product_id' => $item['product_id']],
                        ['quantity' => \DB::raw('quantity + ' . $item['quantity'])]
                    );
                }
            }
        }

        return $this->index($request);
    }

    /**
     * Remove the specified item from the cart.
     */
    public function destroy(Request $request, $productId)
    {
        $request->user()->cartItems()->where('product_id', $productId)->delete();
        
        return response()->json(['message' => 'Item removed from cart.']);
    }

    /**
     * Clear the entire cart.
     */
    public function clear(Request $request)
    {
        $request->user()->cartItems()->delete();

        return response()->json(['message' => 'Cart cleared.']);
    }
}
