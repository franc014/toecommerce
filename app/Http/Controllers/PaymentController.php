<?php

namespace App\Http\Controllers;

use App\Exceptions\OrderAlreadyConfirmedException;
use App\Exceptions\PayphoneTransactionErrorException;
use App\Utils\ConfirmsPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function confirm(Request $request)
    {

        try {
            $confirms = new ConfirmsPayment($request->cookie('cart'), [
                'id' => $request->id,
                'clientTransactionId' => $request->clientTransactionId,
            ]);

            $order = $confirms->handle();

            return response()->redirectTo(route('filament.customer.resources.orders.view', ['record' => $order->code]))->withoutCookie('cart');

        } catch (OrderAlreadyConfirmedException $e) {
            return redirect(route('storefront.products'))->with('order-confirmation-error', 'La orden ya ha sido confirmada.');
        } catch (PayphoneTransactionErrorException $e) {
            return redirect(route('storefront.products'))->with('order-confirmation-error', 'La transacción ha fallado. Inténtalo de nuevo o contacta con el administrador.');
        }

    }
}
