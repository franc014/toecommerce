# Phase 4: Comprehensive Test Coverage — Plan

## Task Group 1: Move Existing Unit Tests into Package

**Goal:** Migrate host app unit tests that test package models.

**Files to move:**
- tests/Unit/CartTest.php → tests/Unit/Models/CartTest.php
- tests/Unit/OrderTest.php → tests/Unit/Models/OrderTest.php
- tests/Unit/ProductTest.php → tests/Unit/Models/ProductTest.php
- tests/Unit/DiscountTest.php → tests/Unit/Models/DiscountTest.php
- tests/Unit/CartItemTest.php → tests/Unit/Models/CartItemTest.php

**Steps:**
1.1. Copy test files from host app `tests/Unit/` to package `tests/Unit/Models/`.
1.2. Fix namespaces from `Tests\Unit` to `JFA\ToecommerceCore\Tests\Unit\Models`.
1.3. Verify `Pest.php` helpers (`createCartWithItem`, etc.) available in package.
1.4. Remove from host app only after package tests pass.

---

## Task Group 2: Create Service Tests

**Goal:** Test all five services in isolation.

**Files to create:**
- tests/Unit/Services/CartServiceTest.php
- tests/Unit/Services/OrderServiceTest.php
- tests/Unit/Services/PaymentServiceTest.php
- tests/Unit/Services/InventoryServiceTest.php
- tests/Unit/Services/DiscountServiceTest.php

**CartService tests:**
2.1. `addOrUpdateItem` updates tally correctly.
2.2. `updateItem` recalculates totals with taxes.
2.3. `removeItem` updates tally.
2.4. `empty` zeros out totals.
2.5. Stock check throws `ProductOutOfStockException` in strict mode.
2.6. Stock check allows over-purchase in lenient mode.
2.7. Discounted prices used when `has_discount` true.

**OrderService tests:**
2.8. `placeOrder` creates order with correct items.
2.9. `placeOrder` throws `PlaceOrderForEmptyCartException` for empty cart.
2.10. `placeOrder` throws `CartAlreadyPaidException` for paid cart.
2.11. `placeOrder` returns existing order if already placed.
2.12. `cancel` deletes order.
2.13. `recalculateTally` sums with/without tax items correctly.

**PaymentService tests:**
2.14. `confirmPayphone` stores metadata and marks paid.
2.15. `confirmPayphone` throws `PayphoneTransactionErrorException` on errorCode.
2.16. `confirmPayphone` throws `OrderAlreadyConfirmedException` if already paid.
2.17. `confirmCashOnDelivery` sets method and status.
2.18. `confirmBankTransfer` stores receipt path.

**InventoryService tests:**
2.19. `checkAvailability` returns true/false based on stock.
2.20. `assertInStock` throws when unavailable.
2.21. `decrementStock` reduces quantity.
2.22. `incrementStock` increases quantity.

**DiscountService tests:**
2.23. `calculateDiscountedPrice` applies highest discount.
2.24. `calculateDiscountedPrice` sums discounts when mode SUM.
2.25. `discountPercentage` returns correct percentage.

---

## Task Group 3: Create Event, Cast, Enum, Factory Tests

**Event tests:**
3.1. `OrderStatusChanged` dispatched with correct payload.

**Cast tests:**
3.2. Money cast: `$19.99` stores `1999`, reads back `19.99`.

**Enum tests:**
3.3. `OrderStatus::canTransitionTo` valid transitions return true.
3.4. `OrderStatus::canTransitionTo` invalid transitions return false.
3.5. Transition to same status is idempotent.

**Factory tests:**
3.6. Every package model creatable via its factory.
3.7. Factory namespace resolves correctly.

---

## Task Group 4: Validation

**Steps:**
4.1. Run `composer test` in package.
4.2. Run `php artisan test --compact` in host app.
4.3. Verify no fatal errors.

---

## Execution Order

1. Task Group 1 — Move existing tests
2. Task Group 3 — Quick wins (cast, enum, factory)
3. Task Group 2 — Service tests
4. Task Group 4 — Validation
