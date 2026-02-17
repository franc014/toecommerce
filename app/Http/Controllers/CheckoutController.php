<?php

namespace App\Http\Controllers;

use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\PlaceOrderForEmptyCartException;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Honeypot\Honeypot;

class CheckoutController extends Controller
{
    public function __invoke(Request $request, Honeypot $honeypot)
    {

        try {
            $cart = Cart::byUICartId($request->cookie('cart'))->firstOrFail();
            $billingInfo = auth()->user()->mainBillingInfoEntry();
            $shippingInfo = auth()->user()->mainShippingInfoEntry();
            $cart->assingUser(auth()->user());
            $order = Order::placeFor(auth()->user(), $cart);

            return Inertia::render(
                'Checkout',
                [
                    'order' => $order,
                    'billingInfo' => $billingInfo,
                    'shippingInfo' => $shippingInfo,
                    'gatewayInfo' => [
                        // 'payphoneAPIURL' => config('app.payphone_app_url'),
                        'storeId' => config('app.payphone.store_id'),
                        'token' => config('app.payphone.token'),
                        'payment' => [
                            'amount' => $order->getRawOriginal('total_amount'),
                            'amountWithTax' => $order->getRawOriginal('total_with_taxes'),
                            'amountWithoutTax' => $order->getRawOriginal('total_without_taxes'),
                            'tax' => $order->getRawOriginal('total_computed_taxes'),
                        ],

                    ],
                    'honeypot' => $honeypot,
                ]
            );
        } catch (PlaceOrderForEmptyCartException $e) {
            return redirect(route('storefront.products'));
        } catch (CartAlreadyPaidException $e) {
            return redirect(route('storefront.products'));
        }
    }
}
