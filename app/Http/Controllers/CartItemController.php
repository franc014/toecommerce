<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductOutOfStockException;
use App\Models\Cart;
use App\Utils\PerformsAddsToCart;
use App\Utils\ResolvesPurchasable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function addOrUpdate(Request $request)
    {

        $request->validate([
            'ui_cart_id' => 'required | uuid',
            'product_id' => 'required | integer',
            'quantity' => 'required | integer',
            'purchasable_type' => 'required | string',
        ]);

        try {
            $cart = Cart::byUICartId($request->input('ui_cart_id'))->firstOrFail();

            $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($request->input('product_id'), $request->input('purchasable_type')), $request->input('quantity'));
            $item = $addsToCart->handle();

            return ['item' => $item];

        } catch (BindingResolutionException $e) {

            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Product not found',
                ],
            ], 404);

        } catch (ProductOutOfStockException $e) {

            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Product is out of stock',
                ],
            ], 422);
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'ui_cart_id' => 'required | uuid',
            'item_id' => 'required | integer',
        ]);

        $cart = Cart::byUICartId($request->input('ui_cart_id'))->firstOrFail();
        $cart->removeItem($request->input('item_id'));
    }
}
