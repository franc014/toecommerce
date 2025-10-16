<?php

namespace App\Http\Controllers;

use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\PlaceOrderForEmptyCartException;
use Inertia\Inertia;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
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

                    'billingInfo' => $billingInfo,
                    'shippingInfo' => $shippingInfo,
                    'gatewayInfo' => [
                        //'payphoneAPIURL' => config('app.payphone_app_url'),
                        'storeId' => config('app.payphone.store_id'),
                        'token' => config('app.payphone.token'),
                        'clientTransactionId' => (string) Str::ulid(),
                        'payment' => [
                            'amount' =>  $order->total_amount,
                            'amountWithTax' =>  $order->total_with_taxes,
                            'amountWithoutTax' => $order->total_without_taxes,
                            'tax' =>  $order->total_computed_taxes
                        ]
                    ]
                ]
            );
        } catch (PlaceOrderForEmptyCartException $e) {
            return redirect(route('storefront.products'));
        } catch (CartAlreadyPaidException $e) {
            return redirect(route('storefront.products'));
        }
    }
}
