<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\ProductStatus;
use App\Traits\MoneyFormat;
use App\Traits\Publishable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Illuminate\Support\Str;

class Product extends Model implements HasMedia, Purchasable
{
    use HasFactory, HasTags, InteractsWithMedia, MoneyFormat, Publishable;


    protected $casts = [
        'published_at' => 'datetime',
        'status' => ProductStatus::class,
        'price' => Money::class,
        'variant_options' => 'array',
    ];


    /* protected static function booted(): void
    {
        static::saved(function (Product $product) {
            $product->generateVariants();
        });
    } */


    public function dataforCart(): array
    {

        return [
            'purchasable_id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'slug' => $this->slug,
            //'image' => $this->main_image_path,
            'taxes' => json_encode($this->taxes->select(['name', 'percentage'])),
            'purchasable_type' => Product::class
        ];
    }

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

    public function variants(): HasMany
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

    public function hasVariants(): bool
    {
        return $this->variants()->count() >= 1;
    }

    public function hasPublishedVariants(): bool
    {
        return $this->variants()->published()->count() >= 1;
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

    public function productImages()
    {
        return $this->getMedia('product-images');
    }


    public function productImagesForList():Attribute
    {
        return Attribute::make(
            get: fn () => $this->productImages()->take(2)->map(function ($image) {
                return $image->getFullUrl();
            })
        );
    }

    public function formattedVariantOptions_bk():array
    {
        $options = collect($this->variant_options);

        $options = $options->map(function ($option) {
            $values = collect($option['values']);
            $values = $values->pluck('value');

            return [$option['name'] => $values->toArray()];
        });

        return $options->collapse()->all();
    }


    public function formattedVariantOptions():array
    {
        $options = collect($this->variant_options);

        $transformed = collect([]);

        $pairs = collect([]);

        foreach ($options as $key => $option) {
            $transformed[$option['name']] = collect($option['values'])->pluck('value')->toArray();
        }

        foreach ($transformed as $key => $options) {
            //ray($key, $options);
            $lowL = collect([]);

            foreach ($options as $keyp => $value) {
                $lowL->push([$key => $value]);
            }

            $pairs->push($lowL);

        }

        return $pairs->toArray();
    }

    private function generateCombinations(): Collection
    {
        $options = $this->formattedVariantOptions();
        return collect(array_shift($options))
            ->crossJoin(...$options)
            ->map(function ($combo) {
                // $combo is an array of arrays, merge them
                return array_merge(...$combo);
            });
    }


    public function generateVariants(): void
    {

        foreach ($this->generateCombinations() as $combination) {
            $values = collect($combination)->values()->join('-');
            $title = $this->title.'-'.$values;
            $slug = Str::slug($title);

            $this->variants()->create([
               'title' => $title,
               'slug' => $slug,
               'variation' => $combination,
               'price' => 0,
               'stock' => 0,
               'status' => ProductStatus::DRAFT,
               'sku' => '',
            ]);
        }


    }
}
