<?php

namespace App\Http\Controllers;

use App\Events\BankTransferReceiptUploaded;
use App\Events\CashOnDeliveryConfirmed;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use JFA\ToecommerceCore\Enums\PaymentMethods;

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
            'payment_receipt' => ['required_if:payment_method,bank_transfer', 'image', 'max:50'],
        ]);

        $order = auth()->user()->orders()->findOrFail($validated['order_id']);

        if ($validated['payment_method'] === PaymentMethods::CASH_ON_DELIVERY->value) {
            $order->confirmCashOnDelivery();

            CashOnDeliveryConfirmed::dispatch($order);

            return response()->json(['message' => __('storefront.cash_on_delivery_confirmed')])->withoutCookie('cart');
        }

        if ($validated['payment_method'] === PaymentMethods::BANK_TRANSFER->value) {
            $receiptFile = $request->file('payment_receipt');
            $filename = "receipts/order-{$order->code}-".now()->timestamp.'.'.$receiptFile->getClientOriginalExtension();
            $receiptPath = $receiptFile->storeAs('', $filename);

            $order->confirmBankTransfer($receiptPath);

            BankTransferReceiptUploaded::dispatch($order);

            return response()->json(['message' => __('storefront.bank_transfer_confirmed')])->withoutCookie('cart');
        }
    }
}
