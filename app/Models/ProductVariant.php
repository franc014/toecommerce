<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\ProductStatus;
use App\Traits\Discountable;
use App\Traits\HasProductVariation;
use App\Traits\MoneyFormat;
use App\Traits\Publishable;
use App\Traits\Taxable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariant extends Model implements HasMedia, Purchasable
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use Discountable, HasFactory, HasProductVariation, InteractsWithMedia, MoneyFormat, Publishable, Taxable;

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
}
