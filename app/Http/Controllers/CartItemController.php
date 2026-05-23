<?php

namespace App\Http\Controllers;

use App\Rules\CartHasNoPaymentMethod;
use App\Rules\ProductStockAvailable;
use App\Utils\PerformsAddsToCart;
use App\Utils\ResolvesPurchasable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use JFA\ToecommerceCore\Models\Cart;

class CartItemController extends Controller
{
    public function addOrUpdate(Request $request)
    {
        $request->validate([
            'ui_cart_id' => ['required', 'uuid', new CartHasNoPaymentMethod],
            'product_id' => 'required | integer',
            'purchasable_type' => 'required | string',
            'quantity' => ['required', 'integer', 'min:1', new ProductStockAvailable($request)],
        ]);

        try {
            $cart = Cart::byUICartId($request->input('ui_cart_id'))->firstOrFail();

            $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($request->input('product_id'), $request->input('purchasable_type')), $request->input('quantity'));

            $item = $addsToCart->handle();

            return response()->json(['item' => $item, 'message' => __('storefront.cart_item_added')]);

        } catch (BindingResolutionException $e) {

            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => __('storefront.cart_item_not_found'),
                ],
            ], 404);
        }
    }

    public function remove(Request $request)
    {
        $request->validate([
            'ui_cart_id' => ['required', 'uuid', new CartHasNoPaymentMethod],
            'item_id' => 'required | integer',
        ]);

        $cart = Cart::byUICartId($request->input('ui_cart_id'))->firstOrFail();
        $cart->removeItem($request->input('item_id'));

        return response()->json(['message' => __('storefront.cart_item_removed')]);
    }
}
