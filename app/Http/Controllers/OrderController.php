<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethods;
use App\Events\CashOnDeliveryConfirmed;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function cancelOrder(Request $request)
    {
        $order = auth()->user()->orders()->findOrFail($request->order);
        $order->cancel();

        return redirect()->route('storefront.products')->with('order-cancelled', 'La orden ha sido cancelada.');
    }

    public function selectPaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => ['required', 'string', Rule::in(array_map(fn ($case) => $case->value, PaymentMethods::cases()))],
        ]);

        $order = auth()->user()->orders()->findOrFail($validated['order_id']);

        if ($validated['payment_method'] === PaymentMethods::CASH_ON_DELIVERY->value) {
            $order->confirmCashOnDelivery();
            CashOnDeliveryConfirmed::dispatch($order);

            return response()->json(['message' => __('storefront.cash_on_delivery_confirmed')])->withoutCookie('cart');
        }
    }
}
