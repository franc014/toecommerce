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

            $confirms->handle();

            return response()->redirectTo(route('storefront.products'))->withoutCookie('cart');

        } catch (OrderAlreadyConfirmedException $e) {
            return redirect(route('storefront.products'));
        } catch (PayphoneTransactionErrorException $e) {
            return redirect(route('storefront.products'));
        }

    }
}
