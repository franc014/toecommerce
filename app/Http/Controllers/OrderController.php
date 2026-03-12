<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function cancelOrder(Request $request)
    {
        $order = auth()->user()->orders()->findOrFail($request->order);
        $order->cancel();

        return redirect()->route('storefront.products')->with('order-cancelled', 'La orden ha sido cancelada.');
    }
}
