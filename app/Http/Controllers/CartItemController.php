<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductOutOfStockException;
use App\Models\Cart;
use App\Utils\PerformsAddsToCart;
use App\Utils\ResolvesPurchasable;
use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;

class CartItemController extends Controller
{


    public function addOrUpdate(Request $request)
    {


        $request->validate([
            'ui_cart_id' => 'required | uuid',
            'product_id' => 'required | integer',
            'purchasable_type' => 'required | string',
            'quantity' => ['required', 'integer', 'min:1', function (string $attribute, mixed $value, Closure $fail) use ($request) {
                $request->validate([
                    'purchasable_type' => 'required | string',
                    'product_id' => 'required | integer',
                ]);

                $purchasableId = $request->input('product_id');
                $purchasableType = $request->input('purchasable_type');

                $resolver = new ResolvesPurchasable($purchasableId, $purchasableType);
                $purchasable = $resolver->resolve();

                if ($value > $purchasable->stock) {
                    $fail("The {$attribute} should be less than or equal to {$purchasable->stock}");
                }
            }],
        ]);



        try {
            $cart = Cart::byUICartId($request->input('ui_cart_id'))->firstOrFail();

            $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($request->input('product_id'), $request->input('purchasable_type')), $request->input('quantity'));

            //ray($addsToCart);

            $item = $addsToCart->handle();



            return ['item' => $item];

        } catch (BindingResolutionException $e) {

            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Product not found',
                ],
            ], 404);

        } /* catch (ProductOutOfStockException $e) {

            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Product is out of stock',
                ],
            ], 422);
        } */
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
