# Phase 3: Extract Business Logic to Services — Plan

## Task Group 1: Create CartService

**Goal:** Extract all cart operations from `Cart` model.

**File to create:** `src/Services/CartService.php`

**Methods to extract from `Cart`:**
- `addOrUpdateItem(array $data): CartItem`
- `updateItem(int $itemId, $quantity): void`
- `updateCartTally(): void`
- `productOutOfStockCheck(array $data): void`
- `empty(): void`

**Steps:**
1.1. Create `CartService` class with `StorefrontSettings` injected.
1.2. Implement `addOrUpdateItem(Cart $cart, array $data): CartItem`.
1.3. Implement `updateItem(Cart $cart, int $itemId, int $quantity): void`.
1.4. Implement `removeItem(Cart $cart, int $itemId): void`.
1.5. Implement `recalculateTally(Cart $cart): void`.
1.6. Implement `empty(Cart $cart): void`.
1.7. Implement `assertInStock(Purchasable $purchasable, int $quantity): void`.
1.8. In `Cart.php`, add `@deprecated` delegate methods that call `app(CartService::class)`.
1.9. In `CartItem.php`, update `saved`/`deleted` hooks to call `CartService::recalculateTally()`.

---

## Task Group 2: Create OrderService

**Goal:** Extract order placement and management from `Order` model.

**File to create:** `src/Services/OrderService.php`

**Methods to extract from `Order`:**
- `placeFor(User $user, Cart $cart): Order`
- `addItem(CartItem $item): void`
- `updateItem(CartItem $cartItem): void`
- `removeItem(CartItem $cartItem): void`
- `updateOrderTally(): void`
- `cancel(): void`

**Steps:**
2.1. Create `OrderService` class.
2.2. Implement `placeOrder(ToecommerceUser $user, Cart $cart): Order`.
2.3. Implement `addItemFromCart(Order $order, CartItem $item): void`.
2.4. Implement `updateItemFromCart(Order $order, CartItem $cartItem): void`.
2.5. Implement `removeItem(Order $order, int $itemId): void`.
2.6. Implement `recalculateTally(Order $order): void`.
2.7. Implement `cancel(Order $order): void`.
2.8. In `Order.php`, add `@deprecated` delegate methods.

---

## Task Group 3: Create PaymentService

**Goal:** Extract payment confirmation logic from `Order` model.

**File to create:** `src/Services/PaymentService.php`

**Methods to extract from `Order`:**
- `confirm(string $payphoneConfirmation): void`
- `confirmCashOnDelivery(): void`
- `confirmBankTransfer(string $receiptPath): void`
- `markAsPaid(): void`

**Steps:**
3.1. Create `PaymentService` class.
3.2. Implement `confirmPayphone(Order $order, array $confirmationData): void`.
3.3. Implement `confirmCashOnDelivery(Order $order): void`.
3.4. Implement `confirmBankTransfer(Order $order, string $receiptPath): void`.
3.5. Implement `markAsPaid(Order $order, ?PaymentMethods $method = null): void`.
3.6. In `Order.php`, add `@deprecated` delegate methods.

---

## Task Group 4: Create InventoryService

**Goal:** Extract stock checking logic from `Cart` model.

**File to create:** `src/Services/InventoryService.php`

**Steps:**
4.1. Create `InventoryService` class.
4.2. Implement `checkAvailability(Purchasable $purchasable, int $quantity): bool`.
4.3. Implement `decrementStock(Purchasable $purchasable, int $quantity): void`.
4.4. Implement `incrementStock(Purchasable $purchasable, int $quantity): void`.
4.5. Update `CartService` to use `InventoryService` for stock checks.

---

## Task Group 5: Create DiscountService

**Goal:** Extract discount calculation from `Discountable` trait.

**File to create:** `src/Services/DiscountService.php`

**Steps:**
5.1. Create `DiscountService` class with `StorefrontSettings` injected.
5.2. Implement `calculateDiscountedPrice(Product $product): int`.
5.3. Implement `applyToCartItem(CartItem $item): void`.
5.4. Update `CartService` to use `DiscountService` when adding/updating items.

---

## Task Group 6: Register Services & Update Hooks

**Goal:** Wire services into the application.

**Steps:**
6.1. Add `registeringPackage()` to `ToecommerceCoreServiceProvider`:
  ```php
  public function registeringPackage(): void
  {
      $this->app->singleton(CartService::class);
      $this->app->singleton(OrderService::class);
      $this->app->singleton(PaymentService::class);
      $this->app->singleton(InventoryService::class);
      $this->app->singleton(DiscountService::class);
  }
  ```
6.2. Update `CartItem` model hooks to use `CartService` instead of direct `Cart::updateCartTally()`.
6.3. Verify no logic remains in `CartItem` hooks that should be in services.

---

## Task Group 7: Validation

**Goal:** Confirm everything still works.

**Steps:**
7.1. Run `composer dump-autoload` in package.
7.2. Run `composer dump-autoload` in host app.
7.3. Run host app tests.
7.4. Verify no fatal errors from missing methods.

---

## Execution Order

1. Task Group 4 — InventoryService (lowest dependency)
2. Task Group 5 — DiscountService (lowest dependency)
3. Task Group 1 — CartService (depends on InventoryService, DiscountService)
4. Task Group 2 — OrderService (depends on CartService concepts)
5. Task Group 3 — PaymentService (depends on OrderService concepts)
6. Task Group 6 — Register services, update hooks
7. Task Group 7 — Validation
