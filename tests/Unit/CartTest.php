<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Sequence;

function createCartWithoutItem(array $productData, $isVariant = false)
{

    if ($isVariant) {
        $purchasable = ProductVariant::factory([
            'product_id' => Product::factory()
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
        'description' => 'IVA 15%'
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
            'product_id' => $product->id
        ]);
        /*  $cart = Cart::factory()->has(CartItem::factory()->count(1)->state([
             'title' => $purchasable->title,
             'slug' => $purchasable->slug,
             'price' => $purchasable->price,
             'quantity' => 4,
             'total' => 4 * $purchasable->price,
             'color' => $purchasable->color,
             'sizes' => $purchasable->sizes
         ]), 'items')->create(); */
    } else {
        $purchasable = $product;
    }



    $cart =  Cart::factory()->has(CartItem::factory()->count(1)->state([
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
                'name' => $iva->name
            ],
            [
                'percentage' => $isd->percentage,
                'name' => $isd->name
            ]
        ])
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



test('getting a cart item by id', function () {

    [$product, $cart] = createCartWithItem([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 50,
    ]);

    expect($cart->itemById($product->id))->toBeInstanceOf(CartItem::class);
    expect($cart->itemById($product->id)->id)->toBe($cart->items->first()->id);
});





test('getting the total without taxes', function () {

    $itemATotalWithTaxes = 120.00 * (1 + 0.15 + 0.10);
    $itemBTotalWithTaxes = 40.00 * (1 + 0.15);
    $itemCTotalWithTaxes = 100;


    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'taxes' => json_encode([
            [
                'percentage' => 15,
                'name' => 'IVA'
            ],
            [
                'percentage' => 10,
                'name' => 'ISD'
            ]
        ]),
        'total_with_taxes' => $itemATotalWithTaxes
        ],
        [
        'price' => 20.00,
        'quantity' => 2,
        'total' => 40.00,
        'taxes' => json_encode([
        [
                'percentage' => 15,
                'name' => 'IVA'
        ]
        ]),
        'total_with_taxes' => $itemBTotalWithTaxes
        ],
        [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'taxes' => json_encode([

        ]),
        'total_with_taxes' => $itemCTotalWithTaxes
        ]
    )), 'items')->create();



    expect($cart->total_without_taxes)->toBe(100.0);
});

test('getting the total without taxes in dollars', function () {

    $itemATotalWithTaxes = 120.00 * (1 + 0.15 + 0.10);
    $itemBTotalWithTaxes = 40.00 * (1 + 0.15);
    $itemCTotalWithTaxes = 100;


    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'taxes' => json_encode([
            [
                'percentage' => 15,
                'name' => 'IVA'
            ],
            [
                'percentage' => 10,
                'name' => 'ISD'
            ]
        ]),
        'total_with_taxes' => $itemATotalWithTaxes
        ],
        [
        'price' => 20.00,
        'quantity' => 2,
        'total' => 40.00,
        'taxes' => json_encode([
        [
                'percentage' => 15,
                'name' => 'IVA'
        ]
        ]),
        'total_with_taxes' => $itemBTotalWithTaxes
        ],
        [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'taxes' => json_encode([

        ]),
        'total_with_taxes' => $itemCTotalWithTaxes
        ]
    )), 'items')->create();


    expect($cart->total_without_taxes_in_dollars)->toBe('$100');
});

test('getting the total with taxes', function () {

    $itemATotalWithTaxes = 120.00 * (1 + 0.15 + 0.10);
    $itemBTotalWithTaxes = 40.00 * (1 + 0.15);
    $itemCTotalWithTaxes = 100;


    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'taxes' => json_encode([
            [
                'percentage' => 15,
                'name' => 'IVA'
            ],
            [
                'percentage' => 10,
                'name' => 'ISD'
            ]
        ]),
        'total_with_taxes' => $itemATotalWithTaxes
        ],
        [
        'price' => 20.00,
        'quantity' => 2,
        'total' => 40.00,
        'taxes' => json_encode([
        [
                'percentage' => 15,
                'name' => 'IVA'
        ]
        ]),
        'total_with_taxes' => $itemBTotalWithTaxes
        ],
        [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'taxes' => json_encode([

        ]),
        'total_with_taxes' => $itemCTotalWithTaxes
        ]
    )), 'items')->create();

    expect($cart->total_with_taxes)->toBe(160.0);
});

test('getting the total with taxes in dollars', function () {

    $itemATotalWithTaxes = 120.00 * (1 + 0.15 + 0.10);
    $itemBTotalWithTaxes = 40.00 * (1 + 0.15);
    $itemCTotalWithTaxes = 100;


    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'taxes' => json_encode([
            [
                'percentage' => 15,
                'name' => 'IVA'
            ],
            [
                'percentage' => 10,
                'name' => 'ISD'
            ]
        ]),
        'total_with_taxes' => $itemATotalWithTaxes
        ],
        [
        'price' => 20.00,
        'quantity' => 2,
        'total' => 40.00,
        'taxes' => json_encode([
        [
                'percentage' => 15,
                'name' => 'IVA'
        ]
        ]),
        'total_with_taxes' => $itemBTotalWithTaxes
        ],
        [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'taxes' => json_encode([

        ]),
        'total_with_taxes' => $itemCTotalWithTaxes
        ]
    )), 'items')->create();

    expect($cart->total_with_taxes_in_dollars)->toBe('$160');
});


test('getting the total computed taxes', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'total_with_taxes' => 40.00 * (1 + 0.15) * 3,
        'computed_taxes' => 40.00 * 3 * 0.15
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'total_with_taxes' => 50.00 * 2 * (1 + 0.15),
        'computed_taxes' => 50.00 * 2 * 0.15
    ])), 'items')->create();


    $computedTaxes = 40.00 * 3 * 0.15 + 50.00 * 2 * 0.15;

    expect($cart->fresh()->total_computed_taxes)->toBe($computedTaxes);
});

test('getting the total computed taxes in dollars', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'total_with_taxes' => 40.00 * (1 + 0.15) * 3,
        'computed_taxes' => 40.00 * 3 * 0.15
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'total_with_taxes' => 50.00 * 2 * (1 + 0.15),
        'computed_taxes' => 50.00 * 2 * 0.15
    ])), 'items')->create();


    $computedTaxes = 40.00 * 3 * 0.15 + 50.00 * 2 * 0.15;

    expect($cart->fresh()->total_computed_taxes_in_dollars)->toBe('$'.$computedTaxes);
});

test('getting the total', function () {

    $itemATotalWithTaxes = 120.00 * (1 + 0.15 + 0.10);
    $itemBTotalWithTaxes = 40.00 * (1 + 0.15);
    $itemCTotalWithTaxes = 100;


    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'taxes' => json_encode([
            [
                'percentage' => 15,
                'name' => 'IVA'
            ],
            [
                'percentage' => 10,
                'name' => 'ISD'
            ]
        ]),
        'total_with_taxes' => $itemATotalWithTaxes
        ],
        [
        'price' => 20.00,
        'quantity' => 2,
        'total' => 40.00,
        'taxes' => json_encode([
        [
                'percentage' => 15,
                'name' => 'IVA'
        ]
        ]),
        'total_with_taxes' => $itemBTotalWithTaxes
        ],
        [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'taxes' => json_encode([

        ]),
        'total_with_taxes' => $itemCTotalWithTaxes
        ]
    )), 'items')->create();

    expect($cart->total)->toBe($itemATotalWithTaxes + $itemBTotalWithTaxes + $itemCTotalWithTaxes);
});

test('getting the total in dollars', function () {

    $itemATotalWithTaxes = 120.00 * (1 + 0.15 + 0.10);
    $itemBTotalWithTaxes = 40.00 * (1 + 0.15);
    $itemCTotalWithTaxes = 100;


    $cart = Cart::factory()->has(CartItem::factory()->count(3)->state(new Sequence(
        [
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'taxes' => json_encode([
            [
                'percentage' => 15,
                'name' => 'IVA'
            ],
            [
                'percentage' => 10,
                'name' => 'ISD'
            ]
        ]),
        'total_with_taxes' => $itemATotalWithTaxes
        ],
        [
        'price' => 20.00,
        'quantity' => 2,
        'total' => 40.00,
        'taxes' => json_encode([
        [
                'percentage' => 15,
                'name' => 'IVA'
        ]
        ]),
        'total_with_taxes' => $itemBTotalWithTaxes
        ],
        [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'taxes' => json_encode([

        ]),
        'total_with_taxes' => $itemCTotalWithTaxes
        ]
    )), 'items')->create();

    expect($cart->total_in_dollars)->toBe('$'.$itemATotalWithTaxes + $itemBTotalWithTaxes + $itemCTotalWithTaxes);
});

test('getting the total count of items in the cart', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00
    ])), 'items')->create();

    expect($cart->items_count)->toBe(5);
});

test('cart can empty', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create();
    expect($cart->items)->toHaveCount(2);
    $cart->empty();
    expect($cart->fresh()->items)->toHaveCount(0);
    expect($cart->fresh()->items_count)->toBe(0);
});
