<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use App\Enums\DiscountCalculationModes;
use App\Enums\StockControlModes;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Settings\StorefrontSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

beforeEach(function () {
    app()->forgetInstance(StorefrontSettings::class);
});

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createCartWithoutItem(array $productData, $isVariant = false)
{

    if ($isVariant) {
        $purchasable = ProductVariant::factory([
            'product_id' => Product::factory(),
        ])->published()->create($productData);
    } else {
        $purchasable = Product::factory()->published()->create($productData);
    }

    $cart = Cart::factory()->create();

    return [$purchasable, $cart];
}

function createCartWithItem(array $data, $isVariant = false)
{
    $iva = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%',
    ]);

    $isd = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%',
    ]);

    $product = Product::factory()->published()->create($data);

    $product->taxes()->attach([$iva->id, $isd->id]);

    if ($isVariant) {
        $purchasable = ProductVariant::factory()->published()->create([
            ...$data,
            'product_id' => $product->id,
        ]);
    } else {
        $purchasable = $product;
    }

    $cart = Cart::factory()->has(CartItem::factory()->count(1)->state([
        'purchasable_id' => $purchasable->id,
        'purchasable_type' => Product::class,
        'title' => $purchasable->title,
        'slug' => $purchasable->slug,
        'price' => $purchasable->price,
        'quantity' => 4,
        'total' => 4 * $purchasable->price,
        'taxes' => json_encode([
            [
                'percentage' => $iva->percentage,
                'name' => $iva->name,
            ],
            [
                'percentage' => $isd->percentage,
                'name' => $isd->name,
            ],
        ]),
    ]), 'items')->create();

    return [$purchasable, $cart];
}

function setStrictMode(StockControlModes $mode = StockControlModes::STRICT)
{
    $sfSettings = app(StorefrontSettings::class);
    $sfSettings->stock_control_mode = $mode;
    $sfSettings->save();
}

function setPaginationNumber(int $paginationNumber = 10)
{
    $sfSettings = app(StorefrontSettings::class);
    $sfSettings->products_per_page = $paginationNumber;
    $sfSettings->save();
}

function setDiscountCalculationMode(DiscountCalculationModes $mode = DiscountCalculationModes::HIGHEST)
{
    $sfSettings = app(StorefrontSettings::class);
    $sfSettings->discount_calculation_mode = $mode;
    $sfSettings->save();
}
