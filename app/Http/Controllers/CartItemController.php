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

        $request->validate([
            'ui_cart_id' => 'required | uuid',
            'product_id' => 'required | integer',
            'quantity' => 'required | integer',
        ]);

        try {
            $cart = Cart::byUICartId($request->input('ui_cart_id'))->firstOrFail();
            $product = Product::findOrFail($request->input('product_id'));
            $quantity = $request->input('quantity');
            $product->setQuantityForCart($quantity);
            $item = $cart->addOrUpdateItem($product->dataforCart());
            return ['item' => $item];
        } catch (ProductOutOfStockException $e) {

            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Product is out of stock'
                ]
            ], 422);
        }
    }
}
