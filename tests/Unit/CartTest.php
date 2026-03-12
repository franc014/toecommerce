<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;

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

test('can get a cart item by purchasable', function () {
    [$purchasable, $cart] = createCartWithItem([
        'price' => 50,
        'title' => 'Product 1',
        'slug' => 'product-1',
    ]);

    $item = $cart->getItemByPurchasable($purchasable->id, $purchasable->getMorphClass());

    expect($item)->toBeInstanceOf(CartItem::class);

    expect($item->title)->toBe($purchasable->title);
    expect($item->slug)->toBe($purchasable->slug);
    expect($item->price)->toBe($purchasable->price);
    expect($item->quantity)->toBe(4);
    expect($item->total)->toBe(4 * $purchasable->price);

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

test('getting totals', function () {

    $itemATotalWithTaxes = 120 * (1 + 0.15 + 0.10); // 150 // comp: 30
    $itemBTotalWithTaxes = 40 * (1 + 0.15); // 46// comp 6
    $itemCTotalWithTaxes = 100; // 100

    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
            'price' => 40,
            'quantity' => 3,
            'total' => 120,
            'taxes' => json_encode([
                [
                    'percentage' => 15,
                    'name' => 'IVA',
                ],
                [
                    'percentage' => 10,
                    'name' => 'ISD',
                ],
            ]),
            'total_with_taxes' => $itemATotalWithTaxes,
            'computed_taxes' => 30,
        ],
        [
            'price' => 20,
            'quantity' => 2,
            'total' => 40,
            'taxes' => json_encode([
                [
                    'percentage' => 15,
                    'name' => 'IVA',
                ],
            ]),
            'total_with_taxes' => $itemBTotalWithTaxes,
            'computed_taxes' => 6,
        ],
        [
            'price' => 50,
            'quantity' => 2,
            'total' => 100,
            'taxes' => json_encode([]),
            'total_with_taxes' => $itemCTotalWithTaxes,
            'computed_taxes' => 0,
        ]
    )), 'items')->create();

    expect($cart->fresh()->total_without_taxes)->toBe(100.0);
    expect($cart->fresh()->total_without_taxes_in_dollars)->toBe('$100');
    expect($cart->fresh()->total_with_taxes)->toBe(160.0);
    expect($cart->fresh()->total_with_taxes_in_dollars)->toBe('$160');
    expect($cart->fresh()->total_computed_taxes)->toBe(36.0);
    expect($cart->fresh()->total_computed_taxes_in_dollars)->toBe('$36');
    expect($cart->fresh()->total_amount)->toBe(296.0);
    expect($cart->fresh()->total_amount_in_dollars)->toBe('$296');

});

test('getting totals when items have discounts', function () {

    $itemATotalWithTaxes = 120 * (1 + 0.15 + 0.10); // 150 // computed tax: 30
    $itemBTotalWithTaxes = 32 * (1 + 0.15); // 36.8 // computed tax 4.8 (20% discount applied)
    $itemCTotalWithTaxes = 100; // 100 -> no taxes

    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
            'price' => 40,
            'quantity' => 3,
            'total' => 120,
            'taxes' => json_encode([
                [
                    'percentage' => 15,
                    'name' => 'IVA',
                ],
                [
                    'percentage' => 10,
                    'name' => 'ISD',
                ],
            ]),
            'total_with_taxes' => $itemATotalWithTaxes,
            'computed_taxes' => 30,
        ],
        [
            'price' => 20,
            'quantity' => 2,
            'total' => 36.8,
            'taxes' => json_encode([
                [
                    'percentage' => 15,
                    'name' => 'IVA',
                ],
            ]),
            'has_discount' => true,
            // discount of 20%
            'discounted_price' => 16,
            'total_with_taxes' => $itemBTotalWithTaxes,
            'computed_taxes' => 4.8,
        ],
        [
            'price' => 50,
            'quantity' => 2,
            'total' => 100,
            'taxes' => json_encode([]),
            'total_with_taxes' => $itemCTotalWithTaxes,
            'computed_taxes' => 0,
        ]
    )), 'items')->create();

    expect($cart->fresh()->total_without_taxes)->toBe(100.0);
    expect($cart->fresh()->total_without_taxes_in_dollars)->toBe('$100');
    expect($cart->fresh()->total_with_taxes)->toBe(156.8);
    expect($cart->fresh()->total_computed_taxes)->toBe(34.8);

    expect($cart->fresh()->total_computed_taxes_in_dollars)->toBe('$34.8');
    expect($cart->fresh()->total_amount)->toBe(291.6);
    expect($cart->fresh()->total_amount_in_dollars)->toBe('$291.6');

});

test('getting the total count of items in the cart', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
    ])), 'items')->create();

    expect($cart->items_count)->toBe(5);
});

it('is empty', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create();
    expect($cart->items)->toHaveCount(2);
    $cart->empty();
    expect($cart->fresh()->items)->toHaveCount(0);
    expect($cart->fresh()->items_count)->toBe(0);
});

test('a cart can have an unpaid order', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create();
    $order = Order::placeFor(User::factory()->create(), $cart);
    expect($cart->order->id)->toBe($order->id);
    expect($cart->hasUnpaidOrder())->toBeTrue();
});

test('a cart is paid', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'paid_at' => now(),
    ]);
    expect($cart->isPaid())->toBeTrue();
});
