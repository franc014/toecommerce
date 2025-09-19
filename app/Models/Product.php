<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, HasTags, InteractsWithMedia;

    protected $casts = [
        'id' => 'integer',
        'published_at' => 'datetime',
        'user_id' => 'integer',
        'status' => ProductStatus::class,
        'price' => Money::class,
    ];

    public function scopeWithStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function priceWithTaxesInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->priceWithTaxes())
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function productCollections(): BelongsToMany
    {
        return $this->belongsToMany(ProductCollection::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasVariants()
    {
        return $this->productVariants->count() >= 1;
    }

    public static function bySlug(string $slug)
    {
        return self::where('slug', $slug)->first();
    }

    public function hasTaxes(): bool
    {
        return $this->taxes->count() >= 1;
    }

    public function priceWithTaxes(): float
    {
        $price = (int) $this->getAttributes()['price'];

        return round(($price * (1 + $this->taxes->sum('percentage') / 100)) / 100, 2);
    }
}
