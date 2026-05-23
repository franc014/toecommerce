<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Rules\CartHasNoPaymentMethod;
use Illuminate\Http\Request;
use JFA\ToecommerceCore\Models\Cart;

class CartController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'id' => 'required | uuid',
        ]);

        $UICartId = $request->input('id');

        $cart = Cart::create([
            'ui_cart_id' => $UICartId,
        ]);

        return response()->json(['ui_cart_id' => $cart->ui_cart_id, 'items' => []])
            ->cookie('cart', $cart->ui_cart_id, 60 * 24 * 30);
    }

    public function show(Request $request)
    {
        $cart = Cart::byUICartId($request->input('id'))->firstOrFail();

        if ($cart->isPaid()) {
            abort(404);
        }

        return new CartResource($cart)->resolve();
    }

    public function empty(Request $request)
    {
        $request->validate([
            'id' => ['required', 'uuid', new CartHasNoPaymentMethod],
        ]);

        $cart = Cart::byUICartId($request->input('id'))->firstOrFail();
        $cart->empty();

        return response()->json(['ui_cart_id' => $cart->ui_cart_id, 'items' => [], 'message' => __('storefront.cart_emptied')]);
    }
}
