<?php

namespace App\Traits;

use App\Enums\DiscountCalculationModes;
use App\Enums\DiscountStatus;
use App\Models\Discount;
use App\Settings\StorefrontSettings;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait Discountable
{
    public function discounts(): MorphToMany
    {
        return $this->morphToMany(Discount::class, 'discountable');
    }

    public function validDiscounts(): Collection
    {
        return $this->discounts()->where('status', DiscountStatus::ACTIVE->value)
            ->get();
    }

    public function hasDiscounts(): Attribute
    {
        // ray($this->validDiscounts()->isEmpty());

        return Attribute::make(
            get: fn () => ! $this->validDiscounts()->isEmpty()
        );
    }

    public function discountedPrice(): float
    {
        $storefrontSettings = app(StorefrontSettings::class);
        $calculationMode = $storefrontSettings->discount_calculation_mode;

        $validDiscounts = $this->validDiscounts();

        if ($validDiscounts->isEmpty()) {
            return 0;
        }

        $discountedPrices = $validDiscounts->map(function ($discount) {
            $discountAmount = $this->price * ($discount->percentage / 100);

            return $this->price - $discountAmount;
        });

        if ($calculationMode === DiscountCalculationModes::HIGHEST) {
            return min($discountedPrices->toArray());
        } elseif ($calculationMode === DiscountCalculationModes::SUM) {
            return $this->price - $discountedPrices->reduce(function ($carry, $item) {
                return $carry + ($this->price - $item);
            }, 0);
        }

        return 0;
    }

    public function discountedPriceInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->discountedPrice())
        );
    }

    public function discountsForList(): Attribute
    {
        $storefrontSettings = app(StorefrontSettings::class);
        $calculationMode = $storefrontSettings->discount_calculation_mode;

        if ($calculationMode === DiscountCalculationModes::HIGHEST) {
            return Attribute::make(
                get: fn () => $this->validDiscounts()->map(function ($discount) {
                    return [
                        'name' => $discount->name,
                        'percentage' => $discount->percentage,
                    ];
                })->sortByDesc('percentage')->take(1)->values()->toArray()
            );

        }

        return Attribute::make(
            get: function () {
                return $this->validDiscounts()->map(function ($discount) {
                    return [
                        'name' => $discount->name,
                        'percentage' => $discount->percentage,
                    ];
                })->values()->toArray();
            }
        );
    }

    public function discountedPriceWithTaxes(): float
    {
        $price = $this->discountedPrice() * 100;

        return round(($price * (1 + $this->taxes->sum('percentage') / 100)) / 100, 2);
    }
}
