<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

test('price should be saved as integer in the database', function () {
    Product::factory()->create([
        'price' => 10.99,
    ]);

    $this->assertDatabaseHas('products', [
        'price' => 1099,
    ]);
});

test('getting price as decimal value', function () {
    $product = Product::factory()->create([
        'price' => 10.99,
    ]);

    $this->assertEquals('10.99', $product->price);
});

test('getting price in dollars', function () {
    $product = Product::factory()->create([
        'price' => 10.99,
    ]);

    $this->assertEquals('$10.99', $product->priceInDollars);
});

test('getting price with taxes in dollars', function () {
    $product = Product::factory()->has(Tax::factory()->state(['percentage' => 15]))->create([
        'price' => 10.99,
    ]);

    $this->assertEquals('$'.round(floatval(10.99 * (1 + (15 / 100))), 2), $product->priceWithTaxesInDollars);
});

test('publishing a product', function () {
    $product = Product::factory()->draft()->create();

    $product->publish();

    expect($product->fresh()->status)->toBe(ProductStatus::ACTIVE);
    expect($product->fresh()->published_at)->not()->toBeNull();
});

test('unpublishing a product', function () {
    $product = Product::factory()->published()->create();

    $product->unpublish();

    expect($product->fresh()->status)->toBe(ProductStatus::DRAFT);
    expect($product->fresh()->published_at)->toBeNull();
});

test('archiving a product', function () {
    $product = Product::factory()->published()->create();

    $product->archive();

    expect($product->fresh()->status)->toBe(ProductStatus::ARCHIVED);
    expect($product->fresh()->archived_at)->not()->toBeNull();
});

test('get published products', function () {
    $publishedProducts = Product::factory(3)->published()->create();
    Product::factory(2)->draft()->create();

    foreach ($publishedProducts as $product) {
        expect($product->status)->toBe(ProductStatus::ACTIVE);
        expect($product->published_at)->not()->toBeNull();
    }
    expect(Product::published()->get()->count())->toBe(3);
});

test('get products with stock greater than 0', function () {
    $products = Product::factory(3)->state(new Sequence(['stock' => 50], ['stock' => 0], ['stock' => 100]))->create();
    expect(Product::withStock()->get()->count())->toBe(2);
    Product::withStock()->get()->assertEquals([$products[0], $products[2]]);
});

test('get draft products', function () {
    Product::factory(3)->draft()->create();

    Product::factory(2)->published()->create();

    Product::factory()->archived()->create();

    expect(Product::draft()->get()->count())->toBe(3);
});

test('get archived products', function () {
    Product::factory()->draft()->create();

    Product::factory(3)->published()->create();

    $archivedProducts = Product::factory(2)->archived()->create();

    foreach ($archivedProducts as $product) {
        expect($product->status)->toBe(ProductStatus::ARCHIVED);
        expect($product->archived_at)->not()->toBeNull();
    }

    expect(Product::archived()->get()->count())->toBe(2);
});

test('can get formatted taxes', function () {
    $taxA = Tax::factory()->create(['percentage' => 15, 'description' => 'IVA 15%', 'name' => 'IVA']);
    $taxB = Tax::factory()->create(['percentage' => 10, 'description' => 'ISD 10%', 'name' => 'ISD']);

    $product = Product::factory()->create();

    $product->taxes()->attach([$taxA->id, $taxB->id]);

    expect($product->formatted_taxes)->toBe('IVA (15%), ISD (10%)');
});

// variants

test('a product can define variant options', function () {
    $variantOptions = [
        [
            'name' => 'size',
            'values' => [
                ['value' => 'small'],
                ['value' => 'medium'],
                ['value' => 'large'],
            ],
        ],

        [
            'name' => 'color',
            'values' => [
                ['value' => 'red'],
                ['value' => 'blue'],
                ['value' => 'green'],
            ],
        ],

        [
            'name' => 'material',
            'values' => [
                ['value' => 'leather'],
                ['value' => 'fabric'],
                ['value' => 'synthetic'],
            ],
        ],

    ];

    $product = Product::factory()->create(
        [
            'title' => 'Sneakers',
            'slug' => 'sneakers',
            'variant_options' => $variantOptions,
        ]
    );

    expect($product->formattedVariantOptions())->toBe([
        [['size' => 'small'], ['size' => 'medium'], ['size' => 'large']],
        [['color' => 'red'], ['color' => 'blue'], ['color' => 'green']],
        [['material' => 'leather'], ['material' => 'fabric'], ['material' => 'synthetic']],
    ]);
});

test('can generate as many variants as variant options permutations', function () {
    $variantOptions = [
        [
            'name' => 'size',
            'values' => [
                ['value' => 'small'],
                ['value' => 'medium'],
                ['value' => 'large'],
            ],
        ],

        [
            'name' => 'color',
            'values' => [
                ['value' => 'red'],
                ['value' => 'blue'],
                ['value' => 'green'],
            ],
        ],

        [
            'name' => 'material',
            'values' => [
                ['value' => 'leather'],
                ['value' => 'fabric'],
                ['value' => 'synthetic'],
            ],
        ],
    ];

    $product = Product::factory()->create(
        [
            'title' => 'Sneakers',
            'slug' => 'sneakers',
            'variant_options' => $variantOptions,
        ]
    );

    $product->generateVariants();

    expect($product->variants()->count())->toBe(27);

    expect($product->variants[0]->price)->toBe($product->price);

    expect($product->variants[0]->variation)->toBe([
        'size' => 'small',
        'color' => 'red',
        'material' => 'leather']);
    expect($product->variants[26]->variation)->toBe([
        'size' => 'large',
        'color' => 'green',
        'material' => 'synthetic',
    ]);
});

// review...
test('can not generate variants when no variant options are defined for a product', function () {
    $product = Product::factory()->create(
        [
            'title' => 'Sneakers',
            'slug' => 'sneakers',
            'variant_options' => null,
        ]
    );

    $product->generateVariants();

    expect($product->variants()->count())->toBe(0);

});

test('verifying product has published variants', function () {
    $product = Product::factory()->create();
    ProductVariant::factory()->published()->count(2)->create([
        'product_id' => $product->id,
    ]);

    ProductVariant::factory()->draft()->count(1)->create([
        'product_id' => $product->id,
    ]);

    expect($product->hasPublishedVariants())->toBeTrue();

});

test('verifying product does not have published variants', function () {
    $product = Product::factory()->create();

    ProductVariant::factory()->draft()->count(2)->create([
        'product_id' => $product->id,
    ]);

    expect($product->hasPublishedVariants())->toBeFalse();

});

it('gets a product by slug', function () {
    $productA = Product::factory()->create([
        'slug' => 'product-slug-a',
    ]);

    $productB = Product::factory()->create([
        'slug' => 'product-slug-b',
    ]);

    expect(Product::bySlug('product-slug-a')->slug)->toBe($productA->slug);
    expect(Product::bySlug('product-slug-b')->slug)->toBe($productB->slug);
    expect(Product::bySlug('product-slug-a')->slug)->not()->toBe($productB->slug);
    expect(Product::bySlug('product-slug-b')->slug)->not()->toBe($productA->slug);
});

it('has taxes associated with it', function () {
    $product = Product::factory()->create();
    $taxIva = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%',
    ]);

    $taxIsd = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%',
    ]);

    $product->taxes()->attach($taxIva->id);
    $product->taxes()->attach($taxIsd->id);

    expect($product->taxes)->not()->toBeNull();
    expect($product->taxes)->toHaveCount(2);
    expect($product->taxes[0]->name)->toBe($taxIva->name);
    expect($product->taxes[1]->name)->toBe($taxIsd->name);
    expect($product->taxes[0]->percentage)->toBe($taxIva->percentage);
    expect($product->taxes[1]->percentage)->toBe($taxIsd->percentage);
});

it('calculates product price with taxes', function () {
    $taxIVA = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%',
    ]);

    $taxISD = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%',
    ]);

    $product = Product::factory()->published()->create([
        'price' => 52.32,
    ]);

    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    expect($product->priceWithTaxes())->toBe((5232 * (1 + ($taxIVA->percentage / 100) + ($taxISD->percentage / 100))) / 100);
});

it('calculates computed price with taxes', function () {
    $taxIVA = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%',
    ]);

    $taxISD = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%',
    ]);

    $product = Product::factory()->published()->create([
        'price' => 52.32,
    ]);

    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    expect($product->computedTaxes())->toBe((5232 * ($taxISD->percentage + $taxIVA->percentage) / 100) / 100);
});

it('verifies product has taxes', function () {
    $taxIVA = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%',
    ]);

    $taxISD = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%',
    ]);

    $product = Product::factory()->published()->create([
        'price' => 52.32,
    ]);

    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    expect($product->hasTaxes())->toBeTrue();
});

test('can get images as media associated to a product', function () {
    $product = Product::factory()->create();
    $imageA = Media::create([
        'model_id' => $product->id,
        'model_type' => Product::class,
        'collection_name' => 'product-images',
        'name' => 't-shirt.jpg',
        'file_name' => 't-shirt.jpg',
        'disk' => 'public',
        'size' => 1000,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
        'order_column' => 1,

    ]);

    $imageB = Media::create([
        'model_id' => $product->id,
        'model_type' => Product::class,
        'collection_name' => 'product-images',
        'name' => 'jeans.jpg',
        'file_name' => 'jeans.jpg',
        'disk' => 'public',
        'size' => 1000,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
        'order_column' => 2,

    ]);

    expect($product->productImages())->toHaveCount(2);

    $product->productImages()->assertEquals([$imageA, $imageB]);

});

test('can get product images as URLs for products list component', function () {

    $product = Product::factory()->create();
    $imageA = Media::create([
        'model_id' => $product->id,
        'model_type' => Product::class,
        'collection_name' => 'product-images',
        'name' => 't-shirt.jpg',
        'file_name' => 't-shirt.jpg',
        'disk' => 'public',
        'size' => 1000,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
        'order_column' => 1,

    ]);

    $imageB = Media::create([
        'model_id' => $product->id,
        'model_type' => Product::class,
        'collection_name' => 'product-images',
        'name' => 'jeans.jpg',
        'file_name' => 'jeans.jpg',
        'disk' => 'public',
        'size' => 1000,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
        'order_column' => 2,

    ]);

    $imageC = Media::create([
        'model_id' => $product->id,
        'model_type' => Product::class,
        'collection_name' => 'product-images',
        'name' => 'jeans.jpg',
        'file_name' => 'jeans.jpg',
        'disk' => 'public',
        'size' => 1000,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
        'order_column' => 2,

    ]);

    expect($product->productImagesForList)->toHaveCount(2);
    expect($product->productImagesForList->toArray())->toEqual([$imageA->getFullUrl(), $imageB->getFullUrl()]);

});
