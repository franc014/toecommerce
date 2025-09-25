<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function create(Request $request)
    {
        $UICartId = $request->input('id');

        $cart = Cart::create([
            'ui_cart_id' => $UICartId,
        ]);

        session()->put('cart', $cart);

        return ['ui_cart_id' => $cart->ui_cart_id, 'items' => []];

    }

    public function show(Request $request)
    {
        $cart = Cart::byUICartId($request->input('id'))->first();

        return ['ui_cart_id' => $cart->ui_cart_id, 'items' => $cart->items->toArray()];
    }
}
