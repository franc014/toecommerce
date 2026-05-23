<?php

use App\Http\Resources\OrderItemResource;
use Illuminate\Http\Request;
use JFA\ToecommerceCore\Models\Cart;
use JFA\ToecommerceCore\Models\CartItem;
use JFA\ToecommerceCore\Models\Order;
use JFA\ToecommerceCore\Models\OrderItem;
use JFA\ToecommerceCore\Models\Product;
use JFA\ToecommerceCore\Models\User;

function createOrderItemWithRelations(array $overrides = []): OrderItem
{
    $user = User::factory()->create();
    $cart = Cart::factory()->create();
    $product = Product::factory()->published()->create();

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'title' => 'Test Product',
        'slug' => 'test-product',
        'price' => 25.50,
        'quantity' => 1,
        'total' => 25.50,
        'total_with_taxes' => 25.50,
        'computed_taxes' => 0,
    ]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'cart_id' => $cart->id,
    ]);

    return OrderItem::factory()->create(array_merge([
        'order_id' => $order->id,
        'cart_item_id' => $cartItem->id,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'title' => 'Test Product',
        'slug' => 'test-product',
        'price' => 25.50,
        'quantity' => 1,
        'total' => 25.50,
        'total_with_taxes' => 25.50,
        'computed_taxes' => 0,
        'has_discount' => false,
        'discount_percentage' => 0,
        'discounted_price' => null,
    ], $overrides));
}

test('transforms order item to array with all expected fields', function () {
    $orderItem = createOrderItemWithRelations([
        'quantity' => 3,
        'total' => 76.50,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array)->toHaveKeys([
        'id',
        'cart_item_id',
        'purchasable_id',
        'purchasable_type',
        'title',
        'image',
        'slug',
        'price',
        'quantity',
        'taxes',
        'total',
        'total_with_taxes',
        'computed_taxes',
        'has_discount',
        'discount_percentage',
        'discounted_price',
        'price_in_dollars',
        'total_in_dollars',
        'total_with_taxes_in_dollars',
        'computed_taxes_in_dollars',
        'discounted_price_in_dollars',
    ]);
});

test('formats price and total in dollars correctly', function () {
    $orderItem = createOrderItemWithRelations([
        'price' => 25.50,
        'quantity' => 3,
        'total' => 76.50,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array['price_in_dollars'])->toBe('$25.5')
        ->and($array['total_in_dollars'])->toBe('$76.5');
});

test('returns correct quantity as integer', function () {
    $orderItem = createOrderItemWithRelations([
        'quantity' => 5,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array['quantity'])->toBe(5)
        ->and($array['quantity'])->toBeInt();
});

test('includes discount fields when discount is applied', function () {
    $orderItem = createOrderItemWithRelations([
        'price' => 25.50,
        'has_discount' => true,
        'discount_percentage' => 10,
        'discounted_price' => 22.95,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array['has_discount'])->toBeTrue()
        ->and($array['discount_percentage'])->toBe(10)
        ->and($array['discounted_price'])->toBe(22.95)
        ->and($array['discounted_price_in_dollars'])->toBe('$22.95');
});

test('includes discount fields when no discount is applied', function () {
    $orderItem = createOrderItemWithRelations([
        'price' => 25.50,
        'has_discount' => false,
        'discount_percentage' => 0,
        'discounted_price' => null,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array['has_discount'])->toBeFalse()
        ->and($array['discount_percentage'])->toBe(0)
        ->and($array['discounted_price'])->toBe(0.0)
        ->and($array['discounted_price_in_dollars'])->toContain('$0');
});

test('includes tax fields correctly when taxes are applied', function () {
    $taxes = [
        ['name' => 'IVA', 'percentage' => 15],
    ];

    $orderItem = createOrderItemWithRelations([
        'price' => 25.50,
        'quantity' => 2,
        'total' => 51.00,
        'taxes' => json_encode($taxes),
        'computed_taxes' => 7.65,
        'total_with_taxes' => 58.65,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array['taxes'])->toBeJson()
        ->and($array['computed_taxes'])->toBe(7.65)
        ->and($array['total_with_taxes'])->toBe(58.65)
        ->and($array['computed_taxes_in_dollars'])->toBe('$7.65')
        ->and($array['total_with_taxes_in_dollars'])->toBe('$58.65');
});

test('includes tax fields correctly when no taxes are applied', function () {
    $orderItem = createOrderItemWithRelations([
        'price' => 25.50,
        'quantity' => 2,
        'total' => 51.00,
        'taxes' => null,
        'computed_taxes' => 0,
        'total_with_taxes' => 51.00,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array['taxes'])->toBeNull()
        ->and($array['computed_taxes'])->toBe(0.0)
        ->and($array['total_with_taxes'])->toBe(51.00)
        ->and($array['computed_taxes_in_dollars'])->toContain('$0')
        ->and($array['total_with_taxes_in_dollars'])->toBe('$51');
});

test('includes all monetary values as floats', function () {
    $orderItem = createOrderItemWithRelations([
        'price' => 25.50,
        'total' => 51.00,
        'discounted_price' => 22.95,
        'computed_taxes' => 7.65,
        'total_with_taxes' => 58.65,
    ]);

    $resource = OrderItemResource::make($orderItem);
    $array = $resource->toArray(new Request);

    expect($array['price'])->toBe(25.50)
        ->and($array['total'])->toBe(51.00)
        ->and($array['discounted_price'])->toBe(22.95)
        ->and($array['computed_taxes'])->toBe(7.65)
        ->and($array['total_with_taxes'])->toBe(58.65);
});
