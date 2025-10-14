<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Facades\PayphoneClientTransactionIdGenerator;
use App\Models\Cart;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $billingInfo = auth()->user()->mainBillingInfoEntry();
        $shippingInfo = auth()->user()->mainShippingInfoEntry();

        $cart = Cart::byUICartId($request->input('ui_cart_id'))->firstOrFail();

        return Inertia::render(
            'Checkout',
            [
                'billingInfo' => $billingInfo,
                'shippingInfo' => $shippingInfo,
                'gatewayInfo' => [
                    //'payphoneAPIURL' => config('app.payphone_app_url'),
                    'clientTransactionId' => PayphoneClientTransactionIdGenerator::generate(),
                    'payment' => [
                        'amount' => $cart->total * 100,
                        'amountWithTax' => $cart->total_with_taxes * 100,
                        'amountWithoutTax' => $cart->total_without_taxes * 100,
                        'tax' => $cart->total_computed_taxes * 100
                    ]
                ]
            ]
        );
    }
}
