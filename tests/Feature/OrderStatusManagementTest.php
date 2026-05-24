<?php

use JFA\ToecommerceCore\Exceptions\InvalidOrderStatusTransitionException;
use App\Mail\OrderStatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use JFA\ToecommerceCore\Enums\OrderStatus;
use JFA\ToecommerceCore\Models\Order;
use JFA\ToecommerceCore\Models\OrderStatusHistory;
use App\Models\User;

beforeEach(function () {
    Mail::fake();
});

test('order status can be changed from pending to shipping', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::PENDING,
    ]);

    expect($order->status)->toBe(OrderStatus::PENDING);

    $order->setStatus(OrderStatus::SHIPPING);

    expect($order->fresh()->status)->toBe(OrderStatus::SHIPPING);
});

test('order status can be changed from shipping to shipped', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::SHIPPING,
    ]);

    $order->setStatus(OrderStatus::SHIPPED);

    expect($order->fresh()->status)->toBe(OrderStatus::SHIPPED);
});

test('order status can be canceled from pending', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::CANCELED);

    expect($order->fresh()->status)->toBe(OrderStatus::CANCELED);
});

test('order status can be canceled from shipping', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::SHIPPING,
    ]);

    $order->setStatus(OrderStatus::CANCELED);

    expect($order->fresh()->status)->toBe(OrderStatus::CANCELED);
});

test('order status cannot be changed from shipped to pending', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::SHIPPED,
    ]);

    expect(function () use ($order) {
        $order->setStatus(OrderStatus::PENDING);
    })->toThrow(InvalidOrderStatusTransitionException::class);
});

test('order status cannot be changed from canceled to shipping', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::CANCELED,
    ]);

    expect(function () use ($order) {
        $order->setStatus(OrderStatus::SHIPPING);
    })->toThrow(InvalidOrderStatusTransitionException::class);
});

test('setting same status is idempotent', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::PENDING);

    expect($order->fresh()->status)->toBe(OrderStatus::PENDING);

    // No history entry should be created for same status
    expect($order->statusHistories()->count())->toBe(0);
});

test('status change creates history entry', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::SHIPPING, 'Order is being prepared for shipment');

    $history = $order->statusHistories()->first();

    expect($history)->not->toBeNull();
    expect($history->from_status)->toBe(OrderStatus::PENDING);
    expect($history->to_status)->toBe(OrderStatus::SHIPPING);
    expect($history->changed_by)->toBe($admin->id);
    expect($history->notes)->toBe('Order is being prepared for shipment');
});

test('status change queues email notification to customer', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::SHIPPING);

    Mail::assertQueued(OrderStatusChanged::class, function ($mail) use ($user, $order) {
        return $mail->hasTo($user->email)
            && $mail->order->id === $order->id
            && $mail->newStatus === OrderStatus::SHIPPING;
    });
});

test('status change email includes old and new status', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::SHIPPING);

    Mail::assertQueued(OrderStatusChanged::class, function ($mail) {
        return $mail->newStatus === OrderStatus::SHIPPING
            && $mail->oldStatus === OrderStatus::PENDING;
    });
});

test('order has status histories relationship', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::SHIPPING);
    $order->setStatus(OrderStatus::SHIPPED);

    $order->refresh();
    expect($order->statusHistories)->toHaveCount(2);

    // Check that both transitions are recorded
    $toStatuses = $order->statusHistories->pluck('to_status')->toArray();
    expect($toStatuses)->toContain(OrderStatus::SHIPPING);
    expect($toStatuses)->toContain(OrderStatus::SHIPPED);
});

test('status history tracks who made the change', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::SHIPPING);

    $history = OrderStatusHistory::first();
    expect($history->changedBy->id)->toBe($admin->id);
});

test('multiple status changes create multiple history entries', function () {
    $admin = User::factory()->create();
    Auth::login($admin);

    $order = Order::factory()->create([
        'status' => OrderStatus::PENDING,
    ]);

    $order->setStatus(OrderStatus::SHIPPING, 'First transition');
    $order->setStatus(OrderStatus::SHIPPED, 'Second transition');

    expect(OrderStatusHistory::count())->toBe(2);

    $firstHistory = OrderStatusHistory::where('to_status', OrderStatus::SHIPPING)->first();
    $secondHistory = OrderStatusHistory::where('to_status', OrderStatus::SHIPPED)->first();

    expect($firstHistory->notes)->toBe('First transition');
    expect($secondHistory->notes)->toBe('Second transition');
});
