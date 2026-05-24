# Phase 2: Decouple Host App Dependencies — Requirements

## Overview

This phase removes all `App\*` namespace references and hardcoded host app configuration from the `toecommerce-core` package. A reusable Laravel package must never depend on classes or config keys that belong to the host application.

## Scope

**In scope:**
- Replace package `User` model with `ToecommerceUser` contract + `HasToecommerceUser` trait
- Decouple `App\Mail\OrderStatusChanged` by firing `OrderStatusChanged` event
- Remove hardcoded `config('app.dashboard.allowed-admin-email')` from User model
- Create package-level mailables (optional but recommended)
- Update host app's `User` model to implement the new contract
- Update all package model relationships that reference `User::class`

**Out of scope:**
- Extracting business logic to services (Phase 3)
- Adding tests (Phase 4)
- Adding type hints (Phase 5)
- Rewriting README (Phase 6)

## Decisions

### 1. User Model → Contract + Trait

**Decision:** Remove `JFA\ToecommerceCore\Models\User` entirely. Provide `JFA\ToecommerceCore\Contracts\ToecommerceUser` interface and `JFA\ToecommerceCore\Concerns\HasToecommerceUser` trait.

**Rationale:**
- Every Laravel app already has `App\Models\User`.
- A package must not ship a concrete `User` model that conflicts.
- Contracts allow the host app to opt-in by implementing the interface on its own model.
- The trait provides default method implementations, reducing boilerplate.

**Host app changes required:**
- `App\Models\User` must `implements ToecommerceUser`
- `App\Models\User` must `use HasToecommerceUser`
- `App\Models\User` must keep its own `FilamentUser` implementation

### 2. App\Mail → Events

**Decision:** Remove `Mail::to($this->user->email)->send(new OrderStatusChanged(...))` from `Order::setStatus()`. Fire `JFA\ToecommerceCore\Events\OrderStatusChanged` event instead.

**Rationale:**
- A package must never reference `App\Mail\*` classes.
- Events allow the host app to decide how to handle notifications.
- This follows Laravel best practices for package development.

### 3. Config-Driven User Model Resolution

**Decision:** All package relationships that reference `User::class` must resolve from `config('toecommerce-core.user_model')`.

**Rationale:**
- Hardcoded `JFA\ToecommerceCore\Models\User` references would break when the model is deleted.
- Config allows the host app to use any User model.

## Context

### Files to be modified (Package)
- `src/Models/Order.php` — Remove `Mail::send()`, fire event
- `src/Models/User.php` — Delete
- `src/Models/OrderStatusHistory.php` — Update `user()` relationship
- `src/Models/Cart.php` — Update `user()` relationship
- `src/Models/Product.php` — Update `user()` relationship
- `src/Models/UserInfoEntry.php` — Update `user()` relationship
- `database/factories/` — Update User references

### Files to be created (Package)
- `src/Contracts/ToecommerceUser.php`
- `src/Concerns/HasToecommerceUser.php`
- `src/Events/OrderStatusChanged.php`

### Files to be modified (Host App)
- `app/Models/User.php` — Implement `ToecommerceUser`, use `HasToecommerceUser`
- `config/auth.php` — Update User model reference
- `bootstrap/providers.php` — Add `EventServiceProvider`
- Various test files — Update `use` statements

### Files to be created (Host App)
- `app/Listeners/SendOrderStatusChangedNotification.php`
- `app/Providers/EventServiceProvider.php`
- `database/factories/UserFactory.php`

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Host app auth breaks when package User model is deleted | Medium | High | Ensure host app User implements ToecommerceUser |
| Order status change emails stop sending | Medium | High | Create listener in host app before deploying |
| Filament panel access breaks | Medium | High | Move `canAccessPanel()` to host app User model |

## Acceptance Criteria (Summary)

1. Package no longer contains `src/Models/User.php`.
2. Package no longer references `App\Mail\OrderStatusChanged`.
3. Package no longer references `config('app.dashboard.allowed-admin-email')`.
4. `ToecommerceUser` contract and `HasToecommerceUser` trait exist.
5. `Order::setStatus()` fires an event instead of sending mail.
6. Host app tests pass after implementing the contract and listener.
7. All package model relationships reference User via config.
