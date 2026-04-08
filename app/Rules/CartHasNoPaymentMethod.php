<?php

namespace App\Rules;

use App\Models\Cart;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CartHasNoPaymentMethod implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cart = Cart::byUICartId($value)->first();

        if ($cart && $cart->order()->whereNotNull('payment_method')->exists()) {
            $fail(__('storefront.cart_order_already_has_payment_method'));
        }
    }
}
