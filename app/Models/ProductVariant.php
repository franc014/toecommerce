<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariant extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory, InteractsWithMedia;

    protected $casts = [
        'sizes' => 'array',
        'price' => Money::class,
        'status' => ProductStatus::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceWithTaxesInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->priceWithTaxes())
        );
    }

    public function priceWithTaxes(): float
    {
        $price = (int) $this->getAttributes()['price'];

        return round(($price * (1 + $this->product->taxes->sum('percentage') / 100)) / 100, 2);
    }
}
