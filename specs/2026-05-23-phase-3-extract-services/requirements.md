# Phase 3: Extract Business Logic to Services — Requirements

## Overview

This phase moves heavy business logic out of Eloquent models into dedicated, testable, single-purpose service classes. This is the core architectural improvement that makes the package robust and maintainable.

## Scope

**In scope:**
- Create `CartService` and extract cart operations from `Cart` model
- Create `OrderService` and extract order placement/management from `Order` model
- Create `PaymentService` and extract payment confirmation from `Order` model
- Create `InventoryService` for stock management
- Create `DiscountService` for discount calculations
- Register all services in the service provider
- Update `CartItem` model hooks to use services (or an observer)
- Keep thin delegate methods on models OR remove them entirely

**Out of scope:**
- Adding comprehensive tests (Phase 4)
- Type hint fixes (Phase 5)
- README/CHANGELOG updates (Phase 6)

## Decisions

### 1. Services Over Models

**Decision:** Extract all business logic that involves multiple models, calculations, or external concerns into service classes.

**Rationale:**
- Eloquent models should represent data and relationships, not complex workflows.
- Services are easier to unit test in isolation.
- Services can be mocked in controller tests.
- Services make the domain logic explicit and discoverable.

### 2. Keep Model Delegates (For Now)

**Decision:** Keep thin delegate methods on models that forward to services.

**Rationale:**
- Prevents breaking the host app immediately.
- Allows gradual migration of controllers to use services directly.
- Mark delegates as `@deprecated` to signal future removal.

### 3. Service Provider Registration

**Decision:** Register services as singletons in the service provider.

**Rationale:**
- Services may hold state or dependencies (like `StorefrontSettings`).
- Singletons ensure the same instance is reused within a request.
- Allows dependency injection in controllers and other services.

## Context

### Package Files to Create
- `src/Services/CartService.php`
- `src/Services/OrderService.php`
- `src/Services/PaymentService.php`
- `src/Services/InventoryService.php`
- `src/Services/DiscountService.php`

### Package Files to Modify
- `src/Models/Cart.php` — Deprecate `addOrUpdateItem`, `updateItem`, `updateCartTally`, `productOutOfStockCheck`, `empty`
- `src/Models/Order.php` — Deprecate `placeFor`, `addItem`, `updateItem`, `removeItem`, `updateOrderTally`, `cancel`, `confirm`, `confirmCashOnDelivery`, `confirmBankTransfer`, `markAsPaid`, `setStatus`
- `src/Models/CartItem.php` — Update `saved`/`deleted` hooks or move to observer
- `src/ToecommerceCoreServiceProvider.php` — Register services

### Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Host app controllers break if model methods are removed | High | High | Keep model delegates, mark @deprecated |
| Circular dependencies between services | Low | Medium | Services depend on models, not other services |
| `CartItem` hooks no longer auto-update tally | Medium | High | Ensure hooks call CartService or use observer |

## Acceptance Criteria (Summary)

1. `CartService` handles all cart CRUD and tally logic.
2. `OrderService` handles order placement and item management.
3. `PaymentService` handles all payment confirmations.
4. `InventoryService` handles stock checks.
5. `DiscountService` handles discount calculations.
6. Services are registered as singletons in the service provider.
7. Model delegate methods exist and forward to services.
8. Host app tests pass.
