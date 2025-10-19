<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\ProductStatus;
use App\Traits\HasProductVariation;
use App\Traits\MoneyFormat;
use App\Traits\Publishable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariant extends Model implements HasMedia, Purchasable
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory, HasProductVariation, InteractsWithMedia, MoneyFormat, Publishable;

    protected $appends = ['price_in_dollars', 'formatted_variation', 'taxes'];

    protected function casts(): array
    {

        return [
            'sizes' => 'array',
            'published_at' => 'datetime',
            'price' => Money::class,
            'status' => ProductStatus::class,
            'variation' => 'array',
        ];

    }

    public function dataforCart(): array
    {

        return [
            'purchasable_id' => $this->id,
            'purchasable_type' => ProductVariant::class,
            'title' => $this->title,
            'price' => $this->price,
            'slug' => $this->slug,
            'image' => $this->main_image ?? $this->product->main_image,
            'taxes' => json_encode($this->taxes->select(['name', 'percentage'])),
            'variation' => $this->variation,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->product->taxes
        );

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

    public function computedTaxes(): float
    {
        return $this->price * ($this->product->taxes->sum('percentage') / 100);
    }
}
