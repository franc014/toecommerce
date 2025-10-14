<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

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

        //session()->put('cart', $cart);

        return response()->json(['ui_cart_id' => $cart->ui_cart_id, 'items' => []])
        ->cookie('cart', $cart->ui_cart_id, 60 * 24 * 30);

    }

    public function show(Request $request)
    {
        $cart = Cart::byUICartId($request->input('id'))->firstOrFail();

        return ['ui_cart_id' => $cart->ui_cart_id, 'items' => $cart->items->toArray(), 'cart_aggregation' => [
            'total_without_taxes_in_dollars' => $cart->total_without_taxes_in_dollars,
            'total_with_taxes_in_dollars' => $cart->total_with_taxes_in_dollars,
            'total_computed_taxes_in_dollars' => $cart->total_computed_taxes_in_dollars,
            'total_in_dollars' => $cart->total_in_dollars,
            'items_count' => $cart->items_count,
        ]];
    }

    public function empty(Request $request)
    {
        $cart = Cart::byUICartId($request->input('id'))->firstOrFail();
        $cart->empty();
        return ['ui_cart_id' => $cart->ui_cart_id, 'items' => []];
    }


}
