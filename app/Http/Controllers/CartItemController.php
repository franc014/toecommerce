<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductOutOfStockException;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function addOrUpdate(Request $request)
    {
        try {
            $cart = Cart::byUICartId($request->input('ui_cart_id'))->first();
            $product = Product::find($request->input('product_id'));
            $quantity = $request->input('quantity');
            $product->setQuantityForCart($quantity);
            $cart->addOrUpdateItem($product->dataforCart());
            return ['ui_cart_id' => $cart->ui_cart_id, 'items' => $cart->fresh()->items->toArray()];
        } catch (ProductOutOfStockException $e) {
            ray('in the catch');
            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Product is out of stock'
                ]
            ], 422);
        }
    }
}
