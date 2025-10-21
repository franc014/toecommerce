<?php

use App\Enums\StockControlModes;
use App\Exceptions\ProductOutOfStockException;
use App\Models\AppSettings;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\User;
use App\Utils\PerformsAddsToCart;
use App\Utils\ResolvesPurchasable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Exceptions;

test('a product can be added to the cart', function () {

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

    [$product, $cart] = createCartWithoutItem([
        'price' => 50,
    ]);

    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    expect($product)->toBeInstanceOf(Product::class);
    expect($cart->items)->toHaveCount(0);

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($product->id, 'product'), 4);

    $addsToCart->handle();

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
    expect($cart->fresh()->items[0]->price)->toBe($product->price);
    expect($cart->fresh()->items[0]->computed_taxes)->toBe(4 * $product->price * ($taxIVA->percentage + $taxISD->percentage) / 100);
    expect($cart->fresh()->items[0]->total_with_taxes)->toBe(4 * $product->priceWithTaxes());
    // expect($cart->fresh()->items[0]->image)->toBe('product.jpg');
});

test('a product added to the cart is also added to an existing unpaid order', function () {

    $user = User::factory()->create();

    [$product, $cart] = createCartWithItem([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 50,
        'user_id' => $user->id,
    ]);

    expect($cart->items)->toHaveCount(1);

    $order = Order::placeFor($user, $cart);

    expect($order->orderItems)->toHaveCount(1);

    $productToAdd = Product::factory()->published()->create();

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($productToAdd->id, 'product'), 2);

    $addsToCart->handle();

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($order->fresh()->orderItems)->toHaveCount(2);

});


test('a product variant can be added to the cart', function () {

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

    $product = Product::factory()->published()->create();

    $cart = Cart::factory()->create();

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'title' => 'Variant 1',
        'slug' => 'variant-1',
        'price' => 50,
    ]);

    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    expect($cart->items)->toHaveCount(0);

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($variant->id, 'product_variant'), 4);

    $addsToCart->handle();

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
    expect($cart->fresh()->items[0]->title)->toBe($variant->title);
    expect($cart->fresh()->items[0]->slug)->toBe($variant->slug);
    expect($cart->fresh()->items[0]->quantity)->toBe(4);
    expect($cart->fresh()->items[0]->total)->toBe(4 * 50.00);
    expect($cart->fresh()->items[0]->taxes)->toBe(json_encode($itemTaxes));
    expect($cart->fresh()->items[0]->total_with_taxes)->toBe(4 * $variant->priceWithTaxes());
    expect($cart->fresh()->items[0]->computed_taxes)->toBe(4 * $variant->price * ($taxIVA->percentage + $taxISD->percentage) / 100);
    // expect($cart->fresh()->items[0]->image)->toBe('product.jpg');
});

test('trying to add a non purchasable type to the cart throws an exception', function () {
    $cart = Cart::factory()->create();
    Exceptions::fake();
    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable(1, 'any'), 4);

    $this->assertThrows(
        fn () => $addsToCart->handle(),
        BindingResolutionException::class
    );
});

test('can update quantity of a cart item', function () {

    [$product, $cart] = createCartWithItem([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 50,
    ]);

    expect($cart->items)->toHaveCount(1);

    $newQuantity = 5;

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($product->id, 'product'), $newQuantity);

    $addsToCart->handle();

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items[0]->quantity)->toBe($newQuantity);
    expect($cart->fresh()->items[0]->total)->toBe($newQuantity * $product->price);
    expect($cart->fresh()->items[0]->total_with_taxes)->toBe($newQuantity * $product->priceWithTaxes());
    expect($cart->fresh()->items[0]->computed_taxes)->toBe($newQuantity * $product->price * $product->taxes->sum('percentage') / 100);

});


it('updates order item quantity after updating quantity of a cart item', function () {

    $user = User::factory()->create();


    [$product, $cart] = createCartWithItem([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 50,
    ]);

    $cart->user_id = $user->id;
    $cart->save();

    $order = Order::placeFor($user, $cart);

    expect($cart->items)->toHaveCount(1);
    expect($order->orderItems)->toHaveCount(1);

    $newQuantity = 5;

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($product->id, 'product'), $newQuantity);

    $cartItem = $addsToCart->handle();

    expect($order->fresh()->orderItems)->toHaveCount(1);
    expect($order->fresh()->orderItems[0]->quantity)->toBe($newQuantity);
    expect($order->fresh()->orderItems[0]->total)->toBe($cartItem->total);
    expect($order->fresh()->orderItems[0]->total_with_taxes)->toBe($cartItem->total_with_taxes);

    expect($order->fresh()->total_amount)->toBe($cart->fresh()->total_amount);
    expect($order->fresh()->total_with_taxes)->toBe($cart->fresh()->total_with_taxes);
    expect($order->fresh()->total_without_taxes)->toBe($cart->fresh()->total_without_taxes);
    expect($order->fresh()->total_computed_taxes)->toBe($cart->fresh()->total_computed_taxes);
    expect($order->fresh()->paid_at)->toBeNull();

});



test('in strict mode, trying to add a product that is out of stock according
to the quantity in the cart throws an exception', function () {

    Exceptions::fake();

    [$product, $cart] = createCartWithoutItem([
        'price' => 50,
        'stock' => 0,
    ]);

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
    ]);

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($product->id, 'product'), 1);

    $this->assertThrows(
        fn () => $addsToCart->handle(),
        ProductOutOfStockException::class
    );

});

test('in strict mode, trying to add a variant that is out of stock according
to the quantity in the cart throws an exception', function () {

    Exceptions::fake();

    [$variant, $cart] = createCartWithoutItem([
        'price' => 50,
        'stock' => 0,
    ], true);

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
    ]);

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($variant->id, 'product_variant'), 1);

    $this->assertThrows(
        fn () => $addsToCart->handle(),
        ProductOutOfStockException::class
    );

});

test('in strict mode, trying to update a product that is out of stock according
to the quantity in the cart throws an exception', function () {

    Exceptions::fake();

    [$product, $cart] = createCartWithItem([
        'price' => 50,
        'stock' => 5,
    ]);

    $newQuantity = 6;

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
    ]);

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($product->id, 'product'), $newQuantity);

    $this->assertThrows(
        fn () => $addsToCart->handle(),
        ProductOutOfStockException::class
    );

});

test('in strict mode, trying to update a variant that is out of stock according
to the quantity in the cart throws an exception', function () {

    Exceptions::fake();

    [$variant, $cart] = createCartWithItem([
        'price' => 50,
        'stock' => 5,
    ], true);

    $newQuantity = 6;

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
    ]);

    $addsToCart = new PerformsAddsToCart($cart, new ResolvesPurchasable($variant->id, 'product_variant'), $newQuantity);

    $this->assertThrows(
        fn () => $addsToCart->handle(),
        ProductOutOfStockException::class
    );

});
