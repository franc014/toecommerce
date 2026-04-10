<?php

namespace App\Rules;

use App\Utils\ResolvesPurchasable;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;
use Illuminate\Translation\PotentiallyTranslatedString;

class ProductStockAvailable implements ValidationRule
{
    public function __construct(private Request $request) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $purchasableId = $this->request->input('product_id');
        $purchasableType = $this->request->input('purchasable_type');

        // Skip validation if required fields are missing (let required rules handle those)
        if (! is_int($purchasableId) && ! is_numeric($purchasableId)) {
            return;
        }

        if (empty($purchasableType)) {
            return;
        }

        $resolver = new ResolvesPurchasable((int) $purchasableId, $purchasableType);
        $purchasable = $resolver->resolve();

        if ($value > $purchasable->stock) {
            $fail("The {$attribute} should be less than or equal to {$purchasable->stock}");
        }
    }
}
