# Phase 2: Decouple Host App Dependencies — Plan

## Task Group 1: Create ToecommerceUser Contract & Trait

**Goal:** Define the interface and reusable trait that replace the package User model.

**Files to create:**
- `src/Contracts/ToecommerceUser.php`
- `src/Concerns/HasToecommerceUser.php`

**Steps:**
1.1. Create `src/Contracts/ToecommerceUser.php` with method signatures for orders, billing/shipping info entries, and computed attributes.
1.2. Create `src/Concerns/HasToecommerceUser.php` trait with implementations copied from the package User model (without `FilamentUser` or `canAccessPanel()`).

---

## Task Group 2: Remove Package User Model

**Goal:** Delete `src/Models/User.php` and update all references.

**Files:**
- `src/Models/User.php` — Delete
- `database/factories/UserFactory.php` — Delete
- `src/Models/Order.php` — Update `user()` relationship
- `src/Models/OrderStatusHistory.php` — Update `changedBy()` relationship
- `src/Models/Product.php` — Update `user()` relationship
- `src/Models/UserInfoEntry.php` — Update `user()` relationship
- `database/factories/OrderFactory.php` — Update User reference
- `database/factories/OrderStatusHistoryFactory.php` — Update User reference
- `database/factories/CartFactory.php` — Update User reference

**Steps:**
2.1. Delete `src/Models/User.php` and `database/factories/UserFactory.php`.
2.2. Replace all `User::class` references in model relationships with `config('toecommerce-core.user_model')`.
2.3. Update package factories to resolve the user model from config.

---

## Task Group 3: Decouple App\Mail from Order Model

**Goal:** Remove direct mail sending from `Order::setStatus()` and replace with event.

**Files to create:**
- `src/Events/OrderStatusChanged.php`

**Files to modify:**
- `src/Models/Order.php`

**Steps:**
3.1. Create `src/Events/OrderStatusChanged.php` with `Order`, `newStatus`, and `oldStatus` properties.
3.2. Remove `App\Mail\OrderStatusChanged` import and `Mail` facade from `Order.php`.
3.3. Replace `Mail::send()` with `event(new OrderStatusChangedEvent(...))`.

---

## Task Group 4: Update Host App User Model

**Goal:** Make the host app User implement the new contract.

**Files to modify (Host App):**
- `app/Models/User.php`
- `config/auth.php`
- `database/factories/UserFactory.php` (create if missing)

**Steps:**
4.1. Create `App\Models\User` with `FilamentUser` and `ToecommerceUser` implementations.
4.2. Add `HasToecommerceUser` trait.
4.3. Move `canAccessPanel()` from package to host app User.
4.4. Update `config/auth.php` to use `App\Models\User`.
4.5. Create host app `UserFactory`.

---

## Task Group 5: Create Host App Event Listener

**Goal:** Restore order status email notifications via event listener.

**Files to create (Host App):**
- `app/Listeners/SendOrderStatusChangedNotification.php`
- `app/Providers/EventServiceProvider.php`

**Files to modify (Host App):**
- `bootstrap/providers.php`

**Steps:**
5.1. Create listener that sends `App\Mail\OrderStatusChanged` on `OrderStatusChanged` event.
5.2. Create `EventServiceProvider` and register the listener.
5.3. Register `EventServiceProvider` in `bootstrap/providers.php`.

---

## Task Group 6: Validation

**Goal:** Confirm package and host app are still functional.

**Steps:**
6.1. Run `composer dump-autoload` in both package and host app.
6.2. Run host app tests.
6.3. Verify no `App\Mail` or `App\Models\User` references remain in package source.

---

## Execution Order

1. Task Group 1 — Create contract + trait
2. Task Group 3 — Create event, modify Order model
3. Task Group 2 — Delete User model, update relationships
4. Task Group 4 — Update host app User model
5. Task Group 5 — Create event listener
6. Task Group 6 — Validation
