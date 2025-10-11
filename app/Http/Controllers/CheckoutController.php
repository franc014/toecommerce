<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $billingInfo = auth()->user()->mainBillingInfoEntry();
        $shippingInfo = auth()->user()->mainShippingInfoEntry();

        return Inertia::render(
            'Checkout',
            [
                'billingInfo' => $billingInfo,
                'shippingInfo' => $shippingInfo,
            ]
        );
    }
}
