<?php

use App\Enums\StockControlModes;
use App\Exceptions\ProductOutOfStockException;
use App\Models\AppSettings;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use Illuminate\Support\Facades\Exceptions;

function createCartWithoutItem(array $productData, $isVariant = false)
{
    if ($isVariant) {
        $purchasable = ProductVariant::factory([
            'product_id' => Product::factory()->state([
                'main_image_path' => 'product.jpg'
            ])
        ])->published()->create($productData);
    } else {
        $purchasable = Product::factory()->published()->create($productData);
    }

    $cart = Cart::factory()->create();
    return [$purchasable, $cart];
}

function createCartWithItem(array $data, $isVariant = false)
{

    if ($isVariant) {
        $purchasable = ProductVariant::factory()->published()->create($data);
        $cart = Cart::factory()->has(CartItem::factory()->count(1)->state([
            'title' => $purchasable->title,
            'slug' => $purchasable->slug,
            'price' => $purchasable->price,
            'quantity' => 4,
            'total' => 4 * $purchasable->price,
            'color' => $purchasable->color,
            'sizes' => $purchasable->sizes
        ]))->create();
    }
    $purchasable = Product::factory()->published()->create($data);

    $cart =  Cart::factory()->has(CartItem::factory()->count(1)->state([
        'purchasable_id' => $purchasable->id,
        'purchasable_type' => Product::class,
        'title' => $purchasable->title,
        'slug' => $purchasable->slug,
        'price' => $purchasable->price,
        'quantity' => 4,
        'total' => 4 * $purchasable->price
    ]), 'items')->create();

    return [$purchasable, $cart];
}

test('can get a cart item by purchasable_id', function () {
    [$purchasable, $cart] = createCartWithItem([
     'price' => 50,
     'title' => 'Product 1',
     'slug' => 'product-1',
    ]);

    $item = $cart->getItemByPurchasableId($purchasable->id);

    expect($item)->toBeInstanceOf(CartItem::class);

    expect($item->title)->toBe($purchasable->title);
    expect($item->slug)->toBe($purchasable->slug);
    expect($item->price)->toBe($purchasable->price);
    expect($item->quantity)->toBe(4);
    expect($item->total)->toBe(4 * $purchasable->price);

});

it('adds a product to cart', function () {
    $taxIVA = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%'
    ]);

    $taxISD = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%'
    ]);

    [$product, $cart] = createCartWithoutItem([
        'price' => 50,
        'main_image_path' => 'product.jpg',
    ]);

    expect($product)->toBeInstanceOf(Product::class);
    expect($cart->items)->toHaveCount(0);


    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    $product->setQuantityForCart(4);
    $cart->addOrUpdateItem($product->dataforCart());

    $itemTaxes = [
        [
            'name' => $taxIVA->name,
            'percentage' => $taxIVA->percentage,
        ],
        [
            'name' => $taxISD->name,
            'percentage' => $taxISD->percentage,
        ],
    ];

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items[0]->title)->toBe($product->title);
    expect($cart->fresh()->items[0]->slug)->toBe($product->slug);
    expect($cart->fresh()->items[0]->quantity)->toBe(4);
    expect($cart->fresh()->items[0]->total)->toBe(4 * 50.00);
    expect($cart->fresh()->items[0]->taxes)->toBe(json_encode($itemTaxes));
    expect($cart->fresh()->items[0]->total_with_taxes)->toBe(4 * $product->priceWithTaxes());
    expect($cart->fresh()->items[0]->image)->toBe('product.jpg');
});

test('getting a cart item by id', function () {

    [$product, $cart] = createCartWithItem([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 50,
    ]);

    expect($cart->itemById($product->id))->toBeInstanceOf(CartItem::class);
    expect($cart->itemById($product->id)->id)->toBe($cart->items->first()->id);
});

test('can update quantity of a cart item', function () {


    [$product, $cart] = createCartWithItem([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 50,
    ]);

    expect($cart->items)->toHaveCount(1);

    $newQuantity = 5;

    $product->setQuantityForCart($newQuantity);

    $cart->addOrUpdateItem($product->dataforCart());

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items[0]->quantity)->toBe($newQuantity);
    expect($cart->fresh()->items[0]->total)->toBe($newQuantity * $product->price);



});




test('in strict mode, trying to add a product that is out of stock according
to the quantity in the cart throws an exception', function () {

    Exceptions::fake();

    [$product, $cart] = createCartWithoutItem([
       'price' => 50,
       'main_image_path' => 'product.jpg',
       'stock' => 0
    ]);

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value
    ]);

    $this->assertThrows(
        fn () => $cart->addOrUpdateItem($product->dataforCart()),
        ProductOutOfStockException::class
    );

});

test('in strict mode, trying to update a product that is out of stock according
to the quantity in the cart throws an exception', function () {

    Exceptions::fake();

    [$product, $cart] = createCartWithItem([
       'price' => 50,
       'main_image_path' => 'product.jpg',
       'stock' => 5
    ]);

    $newQuantity = 6;

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value
    ]);

    $product->setQuantityForCart($newQuantity);

    $this->assertThrows(
        fn () => $cart->addOrUpdateItem($product->dataforCart()),
        ProductOutOfStockException::class
    );

});
