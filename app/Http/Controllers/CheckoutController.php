<?php

namespace App\Http\Controllers;

use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\PlaceOrderForEmptyCartException;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

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
                        // 'payphoneAPIURL' => config('app.payphone_app_url'),
                        'storeId' => config('app.payphone.store_id'),
                        'token' => config('app.payphone.token'),
                        'clientTransactionId' => (string) Str::ulid(),
                        'payment' => [
                            'amount' => $order->total_amount * 100,
                            'amountWithTax' => $order->total_with_taxes * 100,
                            'amountWithoutTax' => $order->total_without_taxes * 100,
                            'tax' => $order->total_computed_taxes * 100,
                        ],
                    ],
                ]
            );
        } catch (PlaceOrderForEmptyCartException $e) {
            return redirect(route('storefront.products'));
        } catch (CartAlreadyPaidException $e) {
            return redirect(route('storefront.products'));
        }
    }
}
