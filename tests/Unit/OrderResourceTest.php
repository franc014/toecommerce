<?php

use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

function createOrderForResourceTest(array $orderOverrides = [], int $itemCount = 0): Order
{
    $user = User::factory()->create();
    $cart = Cart::factory()->create();
    $order = Order::factory()->create(array_merge([
        'user_id' => $user->id,
        'cart_id' => $cart->id,
        'total_amount' => 150.00,
        'total_with_taxes' => 100.00,
        'total_without_taxes' => 50.00,
        'total_computed_taxes' => 25.00,
    ], $orderOverrides));

    if ($itemCount > 0) {
        $product = Product::factory()->published()->create();

        for ($i = 0; $i < $itemCount; $i++) {
            // Create CartItem manually to avoid factory creating extra data
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'purchasable_id' => $product->id,
                'purchasable_type' => Product::class,
                'title' => "Cart Item {$i}",
                'slug' => "cart-item-{$i}",
                'image' => $product->main_image,
                'price' => 25.50,
                'quantity' => 1,
                'total' => 25.50,
                'total_with_taxes' => 25.50,
                'computed_taxes' => 0,
                'has_discount' => false,
                'discount_percentage' => 0,
            ]);

            OrderItem::factory()->create([
                'order_id' => $order->id,
                'cart_item_id' => $cartItem->id,
                'purchasable_id' => $product->id,
                'purchasable_type' => Product::class,
                'title' => "Product {$i}",
                'slug' => "product-{$i}",
                'price' => 25.50,
                'quantity' => 1,
                'total' => 25.50,
                'total_with_taxes' => 25.50,
                'computed_taxes' => 0,
            ]);
        }
    }

    return $order;
}

test('transforms order to array with all expected fields', function () {
    $order = createOrderForResourceTest([
        'total_amount' => 150.00,
        'total_with_taxes' => 100.00,
        'total_without_taxes' => 50.00,
        'total_computed_taxes' => 25.00,
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array)->toHaveKeys([
        'id',
        'code',
        'user_id',
        'cart_id',
        'total_amount',
        'total_with_taxes',
        'total_without_taxes',
        'total_computed_taxes',
        'paid_at',
        'payphone_metadata',
        'created_at',
        'updated_at',
        'total_with_taxes_in_dollars',
        'total_without_taxes_in_dollars',
        'total_computed_taxes_in_dollars',
        'total_amount_in_dollars',
        'order_items',
    ]);
});

test('formats monetary values in dollars correctly', function () {
    $order = createOrderForResourceTest([
        'total_amount' => 150.00,
        'total_with_taxes' => 100.00,
        'total_without_taxes' => 50.00,
        'total_computed_taxes' => 25.00,
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['total_amount_in_dollars'])->toBe('$150')
        ->and($array['total_with_taxes_in_dollars'])->toBe('$100')
        ->and($array['total_without_taxes_in_dollars'])->toBe('$50')
        ->and($array['total_computed_taxes_in_dollars'])->toBe('$25');
});

test('includes order items when relationship is loaded', function () {
    $order = createOrderForResourceTest([], 3);
    $order->load('orderItems');

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['order_items'])->not->toBeNull()
        ->and($array['order_items'])->toBeInstanceOf(AnonymousResourceCollection::class)
        ->and(count($array['order_items']))->toBeGreaterThanOrEqual(3);
});

test('order items is missing when relationship is not loaded', function () {
    $order = createOrderForResourceTest([], 3);
    // Don't load the relationship

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    // whenLoaded returns null when relationship is not loaded
    expect($array['order_items'])->toBeNull();
});

test('includes correct order metadata', function () {
    $order = createOrderForResourceTest([
        'code' => 'TEST-ORDER-123',
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['user_id'])->toBe($order->user_id)
        ->and($array['cart_id'])->toBe($order->cart_id)
        ->and($array['code'])->toBe('TEST-ORDER-123');
});

test('includes timestamps', function () {
    $order = createOrderForResourceTest([]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['created_at'])->not->toBeNull()
        ->and($array['updated_at'])->not->toBeNull();
});

test('handles null paid_at when order is not paid', function () {
    $order = createOrderForResourceTest([
        'paid_at' => null,
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['paid_at'])->toBeNull();
});

test('includes paid_at timestamp when order is paid', function () {
    $order = createOrderForResourceTest([
        'paid_at' => now(),
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['paid_at'])->not->toBeNull();
});

test('includes payphone metadata when present', function () {
    $metadata = json_encode(['transaction_id' => '12345', 'status' => 'approved']);
    $order = createOrderForResourceTest([
        'payphone_metadata' => $metadata,
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['payphone_metadata'])->toBe($metadata);
});

test('includes null payphone metadata when not present', function () {
    $order = createOrderForResourceTest([
        'payphone_metadata' => null,
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['payphone_metadata'])->toBeNull();
});

test('includes all raw monetary values as floats', function () {
    $order = createOrderForResourceTest([
        'total_amount' => 150.50,
        'total_with_taxes' => 100.25,
        'total_without_taxes' => 50.25,
        'total_computed_taxes' => 25.00,
    ]);

    $resource = OrderResource::make($order);
    $array = $resource->toArray(new Request);

    expect($array['total_amount'])->toBe(150.50)
        ->and($array['total_with_taxes'])->toBe(100.25)
        ->and($array['total_without_taxes'])->toBe(50.25)
        ->and($array['total_computed_taxes'])->toBe(25.00);
});
