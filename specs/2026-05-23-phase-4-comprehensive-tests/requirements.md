# Phase 4: Comprehensive Test Coverage — Requirements

## Overview

Move relevant host app tests into package. Add service tests, cast tests, enum tests, factory tests. Package must prove correctness programmatically.

## Scope

**In scope:**
- Move host app unit tests into package (Cart, Order, Product, Discount, CartItem, ConfirmsPayment, AddToCart, PerformsAddToCart)
- Write service tests (CartService, OrderService, PaymentService, InventoryService, DiscountService)
- Write event tests (OrderStatusChanged)
- Write cast tests (Money)
- Write enum tests (OrderStatus)
- Write factory resolution tests

**Out of scope:**
- Host app feature tests (HTTP endpoints, Inertia pages)
- Filament resource tests (stay in host app)
- CMS model tests (stay in host app)

## Decisions

### 1. Unit Tests Belong in Package

**Decision:** Any test that directly tests package model methods or service classes moves into package.

**Rationale:** Package must be self-contained. Host app should only test its own code.

### 2. Pest PHP

**Decision:** Use Pest (already configured). Follow existing `test()` / `it()` patterns.

### 3. TestCase Uses Testbench

**Decision:** Package tests extend Orchestra Testbench with LazilyRefreshDatabase. Already configured.

### 4. Factory States

**Decision:** Use existing factory states (`published()`, `paid()`) where available.

## Context

### Tests to move from host app to package

| Source | Package Target |
|---|---|
| tests/Unit/CartTest.php | tests/Unit/Models/CartTest.php |
| tests/Unit/OrderTest.php | tests/Unit/Models/OrderTest.php |
| tests/Unit/ProductTest.php | tests/Unit/Models/ProductTest.php |
| tests/Unit/DiscountTest.php | tests/Unit/Models/DiscountTest.php |
| tests/Unit/CartItemTest.php | tests/Unit/Models/CartItemTest.php |
| tests/Unit/ConfirmsPaymentTest.php | tests/Unit/Services/PaymentServiceTest.php |
| tests/Feature/AddToCartTest.php | tests/Feature/CartOperationsTest.php |
| tests/Unit/PerformsAddToCartTest.php | tests/Unit/Services/CartServiceTest.php |

### New tests to create

- tests/Unit/Services/CartServiceTest.php
- tests/Unit/Services/OrderServiceTest.php
- tests/Unit/Services/PaymentServiceTest.php
- tests/Unit/Services/InventoryServiceTest.php
- tests/Unit/Services/DiscountServiceTest.php
- tests/Unit/Events/OrderStatusChangedEventTest.php
- tests/Unit/Casts/MoneyCastTest.php
- tests/Unit/Enums/OrderStatusTest.php
- tests/Unit/FactoryResolutionTest.php

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Host app tests break after moving to package | Medium | High | Keep copies in host app during transition, or ensure host app runs package tests |
| Factory resolution fails in package test context | Medium | High | TestCase already has custom resolver |
| Missing test coverage for edge cases | Medium | Medium | Start with existing tests, add edge cases iteratively |

## Acceptance Criteria

1. Package contains tests for all services.
2. Package contains tests for Money cast round-trip.
3. Package contains tests for OrderStatus enum transitions.
4. Package contains tests for factory resolution.
5. `composer test` in package runs and passes.
6. Host app tests still pass.
