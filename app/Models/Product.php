<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\DiscountCalculationModes;
use App\Enums\DiscountStatus;
use App\Enums\ProductStatus;
use App\Settings\StorefrontSettings;
use App\Traits\MoneyFormat;
use App\Traits\Publishable;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Product extends Model implements HasMedia, HasRichContent, Purchasable
{
    use HasFactory, HasTags, InteractsWithMedia, InteractsWithRichContent, MoneyFormat, Publishable;

    protected $casts = [
        'published_at' => 'datetime',
        'status' => ProductStatus::class,
        'price' => Money::class,
        'variant_options' => 'array',
        'description' => 'array',
    ];

    protected $appends = ['price_in_dollars', 'price_with_taxes_in_dollars', 'formatted_taxes', 'discounted_price_in_dollars'];

    public function setUpRichContent(): void
    {
        $this->registerRichContent('description');
        // to use the media library provideer, it should be set up as nullable
        // ->fileAttachmentProvider(SpatieMediaLibraryFileAttachmentProvider::make());
    }

    public function dataforCart(): array
    {

        return [
            'purchasable_id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'slug' => $this->slug,
            'image' => $this->main_image,
            'taxes' => json_encode($this->taxes->select(['name', 'percentage'])),
            'purchasable_type' => Product::class,
        ];
    }

    public function scopeWithStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function isDroppingStock(): bool
    {
        $storefrontSettings = app(StorefrontSettings::class);
        if ($storefrontSettings->isAppInStrictMode()) {
            return $this->stock <= $this->stock_threshold_for_customers;
        }

        return false;
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

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class);
    }

    public function validDiscounts(): Collection
    {
        return $this->discounts()->where('status', DiscountStatus::ACTIVE->value)
            ->get();
    }

    public function hasDiscounts(): bool
    {
        return $this->validDiscounts()->count() >= 1;
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

    public function discountedPriceWithTaxes(): float
    {
        $price = $this->discountedPrice() * 100;

        return round(($price * (1 + $this->taxes->sum('percentage') / 100)) / 100, 2);
    }

    public function computedTaxes(): float
    {

        if ($this->hasDiscounts()) {
            return $this->discountedPrice() * ($this->taxes->sum('percentage') / 100);
        }

        return $this->price * ($this->taxes->sum('percentage') / 100);
    }

    public function discountedPrice(): float
    {
        $storefrontSettings = app(StorefrontSettings::class);
        $calculationMode = $storefrontSettings->discount_calculation_mode;

        $validDiscounts = $this->validDiscounts();

        if ($validDiscounts->isEmpty()) {
            return $this->price;
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

        return $this->price;
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

    public function productImages()
    {
        return $this->getMedia('product-images');
    }

    public function productImagesForList(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->productImages()->take(2)->map(function ($image) {
                return $image->getFullUrl();
            })
        );
    }

    public function formattedVariantOptions(): array
    {
        $options = collect($this->variant_options);

        $transformed = collect([]);

        $pairs = collect([]);

        foreach ($options as $key => $option) {
            $transformed[$option['name']] = collect($option['values'])->pluck('value')->toArray();
        }

        foreach ($transformed as $key => $options) {

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
                'price' => $this->price,
                'stock' => 0,
                'status' => ProductStatus::DRAFT,
                'sku' => '',
            ]);
        }
    }

    public function formattedTaxes(): Attribute
    {
        return Attribute::make(
            get: function () {
                $taxes = $this->taxes->map(function ($tax) {
                    return $tax->name.' ('.$tax->percentage.'%)';
                });

                return $taxes->implode(', ');
            }
        );
    }

    public function relatedProducts(): ?EloquentCollection
    {
        $collections = $this->productCollections->pluck('id')->toArray();

        if (count($collections) > 0) {
            return Product::published()->whereHas('productCollections', function ($query) use ($collections) {
                $query->whereIn('product_collections.id', $collections);
            })->where('id', '!=', $this->id)->get();
        } else {
            return null;
        }

    }
}
