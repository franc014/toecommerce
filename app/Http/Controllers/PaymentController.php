<?php

namespace App\Http\Controllers;

use App\Facades\PayphonePaymentGateway;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function confirm(Request $request)
    {
        $confirmation = PayphonePaymentGateway::confirm(request()->id, request()->clientTransactionId);
        $cartUiId = $request->cookie('cart');

        $cart = Cart::byUICartId($cartUiId)->firstOrFail();

        $order = $cart->order;

        $order->confirm($confirmation);

        $cart->update([
            'paid_at' => now(),
        ]);



        return redirect()->route('storefront.products');
    }
}
