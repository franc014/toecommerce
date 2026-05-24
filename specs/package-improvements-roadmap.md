# ToEcommerce Core — Package Improvement Roadmap

> Drafted after comprehensive codebase review.
> Goal: Transform the package into a robust, reusable, well-tested ecommerce core.

---

## Context

The `toecommerce-core` package (`franc014/toecommerce-core`) is a Filament v5 plugin containing ecommerce domain logic: products, variants, carts, orders, discounts, taxes, collections, and a storefront settings layer. It currently mixes business logic into Eloquent models, lacks meaningful tests, has factory namespace mismatches, ships Laravel default migrations, and contains hardcoded dependencies on the host app (`App\Mail\OrderStatusChanged`, `App\Models\User`, `config('app.dashboard.allowed-admin-email')`).

The host app (`toecommerce`) contains the HTTP layer (Inertia.js + Vue 3), CMS models, and a large test suite that partially tests package logic.

---

## Guiding Principles

1. **Package owns the domain.** All cart, order, payment, discount, and stock logic lives in the package.
2. **Host app owns the HTTP/UI layer.** Controllers, Inertia pages, Blade views, and route definitions stay in the host app.
3. **Services over Models.** Business logic is extracted from Eloquent models into dedicated service classes.
4. **Events over Direct Mail.** The package fires events; the host app decides how to handle them (mail, SMS, logs).
5. **Contracts over Concrete Users.** The package provides interfaces/traits; the host app implements them on its own `User` model.
6. **Tests prove correctness.** Every service, model behavior, and exception path must have a test.

---

## Phase 1: Foundation & Critical Fixes

**Goal: Make the package installable without fatal errors.**

### 1.1 Fix Factory Namespace Imports in All Models

**Problem:** Every model imports `use Database\Factories\{Model}Factory` but the actual factories live in `JFA\ToecommerceCore\Database\Factories\`. This will break factory resolution and static analysis.

**Files affected:** All 14 models in `src/Models/`.

**Action:**
- Update `use Database\Factories\XFactory;` to `use JFA\ToecommerceCore\Database\Factories\XFactory;` in every model.
- Verify the `@use HasFactory<XFactory>` docblock resolves correctly.
- Run `php artisan test` in the host app to verify factory resolution still works.

### 1.2 Remove Laravel Default Migrations from Package

**Problem:** The package ships:
- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`

These conflict with existing Laravel installations.

**Action:**
- Delete the three files above from `database/migrations/`.
- Verify no remaining migration references these tables.

### 1.3 Rename All Migrations to `.stub` & Switch to `discoversMigrations()`

**Problem:** All real migrations have hardcoded 2025 timestamps. For new installations, timestamps should be generated at publish time.

**Action:**
- Rename every package migration from `YYYY_MM_DD_HHMMSS_create_xxx_table.php` to `create_xxx_table.php.stub`.
- Remove the timestamp prefix from all filenames.
- Update `ToecommerceCoreServiceProvider::configurePackage()`:
  ```php
  $package->name(static::$name)
      ->hasTranslations()
      ->hasViews(static::$viewNamespace)
      ->discoversMigrations(); // replaces ->hasMigrations()
  ```
- Remove the `getMigrations()` override if present (it returns an empty array anyway).
- Delete the unused `create_toecommerce_core_table.php.stub` boilerplate.

### 1.4 Register Config File in Service Provider

**Problem:** `config/toecommerce-core.php` exists but `->hasConfigFile()` is never called, so the config is never published or merged.

**Action:**
- Add `->hasConfigFile()` to `configurePackage()`.
- Populate `config/toecommerce-core.php` with a meaningful structure (user model reference, currency, payment settings). See Phase 2 for full config content.

### 1.5 Remove Dead Code

**Actions:**
- Delete `src/ToecommerceCoreCommand.php` (empty boilerplate command) or implement a useful install command.
- Delete `src/Testing/TestsToecommerceCore.php` if it remains empty, or implement a useful Livewire test mixin.
- Remove the `stubs/` publishing logic from `packageBooted()` and delete the empty `stubs/` directory.
- Remove or implement `src/ToecommerceCore.php` (currently an empty class referenced by the Facade).

### 1.6 Verify Host App Still Works

**Action:**
- Run `composer dump-autoload` in both the package and the host app.
- Run host app tests to ensure factory resolution and migrations still function.
- If the host app has already-run migration copies, confirm they are not affected by the `.stub` rename (they are independent files).

---

## Phase 2: Decouple Host App Dependencies

**Goal: Remove all `App\*` references and hardcoded host app config from the package.**

### 2.1 Replace Package `User` Model with Contract + Trait

**Problem:** `JFA\ToecommerceCore\Models\User` extends `Authenticatable` and implements `FilamentUser`. This conflicts with every Laravel app that already has its own `User` model.

**Action:**
- Create `src/Contracts/ToecommerceUser.php`:
  ```php
  interface ToecommerceUser
  {
      public function orders(): HasMany;
      public function billingInfoEntry(): HasMany;
      public function shippingInfoEntry(): HasMany;
      public function mainBillingInfoEntry(): ?UserInfoEntry;
      public function mainShippingInfoEntry(): ?UserInfoEntry;
  }
  ```
- Create `src/Concerns/HasToecommerceUser.php` trait with the methods currently on the package `User` model.
- Update `composer.json` autoload to include `src/Contracts/` if not already covered.
- Update `config/toecommerce-core.php`:
  ```php
  return [
      'user_model' => App\Models\User::class,
  ];
  ```
- Create a helper method or binding to resolve the user model class from config.
- Update all model relationships that reference `User::class` to use `config('toecommerce-core.user_model')` or a bound interface.
- Delete `src/Models/User.php`.
- Update the host app's `App\Models\User` to implement `ToecommerceUser` and use `HasToecommerceUser`.

### 2.2 Decouple `App\Mail\OrderStatusChanged`

**Problem:** `Order::setStatus()` directly sends `App\Mail\OrderStatusChanged`. A package must never reference the `App\` namespace.

**Action:**
- Create `src/Events/OrderStatusChanged.php` event class:
  ```php
  class OrderStatusChanged
  {
      public function __construct(
          public Order $order,
          public OrderStatus $newStatus,
          public ?OrderStatus $oldStatus,
      ) {}
  }
  ```
- Remove `Mail::to($this->user->email)->send(new OrderStatusChanged(...))` from `Order::setStatus()`.
- Replace it with `event(new OrderStatusChanged($this, $newStatus, $currentStatus));`.
- The host app will register a listener for this event to send mail, push notifications, etc.

### 2.3 Remove Hardcoded Host Config from User Model

**Problem:** `User::canAccessPanel()` references `config('app.dashboard.allowed-admin-email')`.

**Action:**
- Move this logic out of the package entirely. The `FilamentUser::canAccessPanel()` implementation belongs in the host app's `User` model, not in a reusable package trait.
- The `HasToecommerceUser` trait should NOT implement `FilamentUser`. The host app handles Filament panel access.

### 2.4 Create Package-Level Mailable Stubs (Optional but Recommended)

**Action:**
- Create `src/Mail/OrderConfirmed.php` and `src/Mail/OrderStatusUpdated.php` as *package-level* mailables with generic Markdown templates.
- The host app can choose to use them or override them with its own listeners.
- These are optional because the events-based approach is preferred, but providing default mailables gives consumers a quick start.

### 2.5 Populate Config File

**Action:** Populate `config/toecommerce-core.php`:
```php
return [
    'user_model' => App\Models\User::class,

    'currency' => [
        'code' => 'USD',
        'symbol' => '$',
        'decimals' => 2,
    ],

    'stock' => [
        'control_mode' => \JFA\ToecommerceCore\Enums\StockControlModes::STRICT,
    ],

    'discounts' => [
        'calculation_mode' => \JFA\ToecommerceCore\Enums\DiscountCalculationModes::HIGHEST,
    ],

    'payment' => [
        'default' => 'payphone',
        'gateways' => [
            'payphone' => [
                // gateway-specific config keys
            ],
            'cash_on_delivery' => [],
            'bank_transfer' => [],
        ],
    ],

    'pagination' => [
        'products_per_page' => 12,
    ],
];
```

---

## Phase 3: Extract Business Logic to Services

**Goal: Move heavy logic out of Eloquent models into testable, single-purpose service classes.**

### 3.1 Create `CartService`

**Current logic in `Cart` model:**
- `addOrUpdateItem(array $data): CartItem`
- `updateItem(int $itemId, $quantity): void`
- `updateCartTally(): void`
- `productOutOfStockCheck(array $data): void`
- `empty(): void`

**Action:**
- Create `src/Services/CartService.php`:
  ```php
  class CartService
  {
      public function __construct(
          private StorefrontSettings $settings,
      ) {}

      public function addOrUpdateItem(Cart $cart, array $data): CartItem {}
      public function updateItem(Cart $cart, int $itemId, int $quantity): void {}
      public function removeItem(Cart $cart, int $itemId): void {}
      public function empty(Cart $cart): void {}
      public function recalculateTally(Cart $cart): void {}
      private function assertInStock(Purchasable $purchasable, int $quantity): void {}
  }
  ```
- Keep thin delegate methods on `Cart` model OR deprecate them. If kept, they should delegate to the service:
  ```php
  public function addOrUpdateItem(array $data): CartItem
  {
      return app(CartService::class)->addOrUpdateItem($this, $data);
  }
  ```
- Update `CartItem` model hooks (`saved`, `deleted`) to use the service or move tally logic into a dedicated observer.

### 3.2 Create `OrderService`

**Current logic in `Order` model:**
- `placeFor(User $user, Cart $cart): Order`
- `addItem(CartItem $item): void`
- `updateItem(CartItem $cartItem): void`
- `removeItem(CartItem $cartItem): void`
- `updateOrderTally(): void`
- `cancel(): void`

**Action:**
- Create `src/Services/OrderService.php`:
  ```php
  class OrderService
  {
      public function placeOrder(User $user, Cart $cart): Order {}
      public function addItemFromCart(Order $order, CartItem $item): void {}
      public function updateItemFromCart(Order $order, CartItem $cartItem): void {}
      public function removeItem(Order $order, int $itemId): void {}
      public function recalculateTally(Order $order): void {}
      public function cancel(Order $order): void {}
  }
  ```
- Update `Order` model to delegate or remove these methods.

### 3.3 Create `PaymentService`

**Current logic in `Order` model:**
- `confirm(string $payphoneConfirmation): void`
- `confirmCashOnDelivery(): void`
- `confirmBankTransfer(string $receiptPath): void`
- `markAsPaid(): void`

**Action:**
- Create `src/Services/PaymentService.php`:
  ```php
  class PaymentService
  {
      public function confirmPayphone(Order $order, array $confirmationData): void {}
      public function confirmCashOnDelivery(Order $order): void {}
      public function confirmBankTransfer(Order $order, string $receiptPath): void {}
      public function markAsPaid(Order $order, ?string $paymentMethod = null): void {}
  }
  ```
- The Payphone-specific JSON parsing and error handling moves here.
- `Order::confirm()` should be deprecated in favor of `PaymentService::confirmPayphone()`.

### 3.4 Create `InventoryService`

**Action:**
- Create `src/Services/InventoryService.php`:
  ```php
  class InventoryService
  {
      public function checkAvailability(Purchasable $purchasable, int $quantity): bool {}
      public function decrementStock(Purchasable $purchasable, int $quantity): void {}
      public function incrementStock(Purchasable $purchasable, int $quantity): void {}
  }
  ```
- Move stock checking logic out of `Cart` model.

### 3.5 Create `DiscountService`

**Action:**
- Create `src/Services/DiscountService.php`:
  ```php
  class DiscountService
  {
      public function calculateDiscountedPrice(Product $product): int {}
      public function applyDiscounts(Collection $items, DiscountCalculationModes $mode): void {}
  }
  ```
- Move calculation logic out of `Discountable` trait (or keep the trait as a thin wrapper).

### 3.6 Bind Services in Service Provider

**Action:**
- Register services as singletons in `ToecommerceCoreServiceProvider`:
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

### 3.7 Update Host App `App\Utils\PerformsAddsToCart`

**Action:**
- Refactor `App\Utils\PerformsAddsToCart` to use `CartService` instead of directly calling `Cart::addOrUpdateItem()`.
- After this refactor, decide whether `PerformsAddsToCart` is still needed or if controllers can call `CartService` directly.

---

## Phase 4: Comprehensive Test Coverage

**Goal: Move relevant tests from the host app into the package and add new ones for services.**

### 4.1 Audit Host App Tests

**Tests to MOVE into the package:**

| Host Test | Package Target | Why |
|---|---|---|
| `tests/Unit/CartTest.php` | `tests/Unit/CartTest.php` | Tests `Cart` model methods |
| `tests/Unit/OrderTest.php` | `tests/Unit/OrderTest.php` | Tests `Order::placeFor`, `confirm`, `setStatus` |
| `tests/Unit/ProductTest.php` | `tests/Unit/ProductTest.php` | Tests `Product` accessors, relationships, scopes |
| `tests/Unit/DiscountTest.php` | `tests/Unit/DiscountTest.php` | Tests discount calculation modes |
| `tests/Unit/CartItemTest.php` | `tests/Unit/CartItemTest.php` | Tests cart item totals, taxes, discounts |
| `tests/Unit/ConfirmsPaymentTest.php` | `tests/Unit/PaymentServiceTest.php` | Tests payment confirmation logic |
| `tests/Feature/AddToCartTest.php` | `tests/Feature/CartOperationsTest.php` | Tests `dataforCart()` and item creation |
| `tests/Unit/PerformsAddToCartTest.php` | `tests/Unit/CartServiceTest.php` | Refactor to test `CartService` instead of host utility |

**Tests to KEEP in the host app:**

| Host Test | Why |
|---|---|
| `tests/Feature/CheckoutTest.php` | Tests Inertia page, auth, cookies |
| `tests/Feature/UICartTest.php` | Tests HTTP endpoints (`CartController`) |
| `tests/Feature/PagesResponseTest.php` | Tests CMS pages |
| `tests/Feature/ContactFormTest.php` | Tests `App\Models\Contact` |
| `tests/Unit/MenuTest.php` | Tests `App\Models\Menu` |
| `tests/Unit/SectionTest.php` | Tests `App\Models\Section` |
| `tests/Feature/PaymentTest.php` | Tests host app payment controller + Payphone gateway interaction |
| `tests/Feature/ShowProductListingTest.php` | Tests host app storefront page |
| `tests/Feature/ShowProductsByCollectionTest.php` | Tests host app collection page |
| `tests/Feature/ViewCollectionsListTest.php` | Tests host app collections page |
| `tests/Feature/ViewProductsListTest.php` | Tests host app products page |
| `tests/Unit/ResolvesPurchasableTest.php` | Tests host app `ResolvesPurchasable` utility |
| `tests/Unit/UserTest.php` | Tests host app `User` model extensions |
| `tests/Feature/UpdateUserInfoEntryTest.php` | Tests host app `UserInfoEntryController` |
| `tests/Unit/MenuItemTest.php` | Tests host app `MenuItem` |
| `tests/Feature/PageTest.php` | Tests host app `Page` model |
| `tests/Unit/CartItemResourceTest.php` | Tests Filament resource in host app context |
| `tests/Unit/OrderItemResourceTest.php` | Tests Filament resource in host app context |
| `tests/Unit/OrderResourceTest.php` | Tests Filament resource in host app context |

### 4.2 Write New Service Tests

**Action:** Create the following new test files in the package:

- `tests/Unit/Services/CartServiceTest.php`
  - Adding items updates tally correctly
  - Updating quantity recalculates totals with taxes
  - Removing item updates tally
  - Emptying cart zeros out totals
  - Stock check throws `ProductOutOfStockException` in strict mode
  - Stock check allows over-purchase in lenient mode
  - Discounted prices are used when `has_discount` is true

- `tests/Unit/Services/OrderServiceTest.php`
  - `placeOrder` creates order with correct items
  - `placeOrder` throws `PlaceOrderForEmptyCartException` for empty cart
  - `placeOrder` throws `CartAlreadyPaidException` for paid cart
  - `placeOrder` returns existing order if already placed
  - `cancel` deletes order
  - `recalculateTally` sums with/without tax items correctly

- `tests/Unit/Services/PaymentServiceTest.php`
  - `confirmPayphone` stores metadata and marks paid
  - `confirmPayphone` throws `PayphoneTransactionErrorException` on errorCode
  - `confirmPayphone` throws `OrderAlreadyConfirmedException` if already paid
  - `confirmCashOnDelivery` sets method and status
  - `confirmBankTransfer` stores receipt path

- `tests/Unit/Services/InventoryServiceTest.php`
  - `checkAvailability` returns true/false based on stock
  - `decrementStock` reduces product/variant quantity

- `tests/Unit/Services/DiscountServiceTest.php`
  - `calculateDiscountedPrice` applies highest discount
  - `calculateDiscountedPrice` sums discounts when mode is SUM

### 4.3 Write Event Tests

**Action:**
- `tests/Unit/Events/OrderStatusChangedEventTest.php`
  - Assert event is dispatched with correct payload
  - Assert event has `ShouldDispatchAfterCommit` if inside a transaction

### 4.4 Write Factory Resolution Tests

**Action:**
- `tests/Unit/FactoryResolutionTest.php`
  - Every package model can be created via its factory
  - Factory namespace resolves correctly for all 14 models

### 4.5 Write Cast Tests

**Action:**
- `tests/Unit/Casts/MoneyCastTest.php`
  - `$model->price = 19.99` stores `1999` in DB
  - `$model->price` returns `19.99` from DB

### 4.6 Write Enum Tests

**Action:**
- `tests/Unit/Enums/OrderStatusTest.php`
  - Valid transitions return true
  - Invalid transitions return false
  - Transition to same status is idempotent

---

## Phase 5: Type Safety & Quality

**Goal: Make the package pass strict static analysis.**

### 5.1 Add Missing Type Hints & Return Types

**Actions:**
- Audit every method in `src/Models/` and add explicit types.
- Examples:
  - `Cart::scopeByUICartId(Builder $query, string $UICartId): Builder`
  - `Cart::updateItem(int $itemId, int $quantity): void`
  - `Cart::empty(): void`
  - `Order::placeFor(User $user, Cart $cart): Order`
  - `Order::confirm(string $payphoneConfirmation): void`
  - `Product::bySlug(string $slug): ?Product`
  - `Product::scopeWithStock(Builder $query): Builder`
  - `Discount::setStatus(): void` → clarify return type
  - `StorefrontSettings::isAppInStrictMode(): bool`

### 5.2 Bump PHPStan to Level 8

**Action:**
- Change `phpstan.neon.dist` from `level: 4` to `level: 8` (or `max`).
- Run `composer analyse` and fix every reported issue.
- Add a `phpstan-baseline.neon` only if there are intentional edge cases, not to silence real bugs.

### 5.3 Run Laravel Pint

**Action:**
- Run `vendor/bin/pint` (or `composer lint`) and commit formatting fixes.
- Ensure CI will fail on style violations.

### 5.4 Add Rector Rules (Optional)

**Action:**
- Update `rector.php` to include modernizing rules:
  - `TypedPropertyRector`
  - `ClassPropertyAssignToConstructorPromotionRector`
  - `AddVoidReturnTypeWhereNoReturnRector`
- Run `composer refactor` and review changes.

### 5.5 Add Strict Types Declaration

**Action:**
- Add `declare(strict_types=1);` to all new service classes and tests.
- Consider adding it to all existing model files during this phase.

---

## Phase 6: Documentation, Polish & Release Prep

**Goal: Make the package discoverable and usable by other developers (or your future self).**

### 6.1 Update README

**Action:** Replace boilerplate README with actual documentation:
- Installation instructions (`composer require`, publish config, publish migrations)
- Config reference with all available options
- Quick start for integrating the `ToecommerceUser` contract into the host `User` model
- Event reference (`OrderStatusChanged`, etc.)
- Service reference (when to use `CartService` vs model delegates)
- Payment gateway setup (Payphone config example)
- Testing notes (factories, `RefreshDatabase`)

### 6.2 Create CHANGELOG.md

**Action:** Start a changelog following [Keep a Changelog](https://keepachangelog.com/) format. Document the breaking changes from Phase 1–3.

### 6.3 Update GitHub Actions

**Action:**
- Ensure `.github/workflows/tests.yml` runs the package's Pest suite.
- Ensure `.github/workflows/phpstan.yml` uses the new level 8 config.
- Add a workflow step to verify migration stubs are all `.stub` files.

### 6.4 Tag a Release

**Action:**
- After all phases complete, tag `v1.0.0` (or next major version) since there are breaking changes.
- Document breaking changes:
  - Migration file rename (requires republish for new apps)
  - `User` model removal (requires host app to implement contract)
  - `App\Mail\OrderStatusChanged` no longer sent from model (requires host app listener)
  - `Order::placeFor()` and `Order::confirm()` deprecated in favor of `OrderService` / `PaymentService`

### 6.5 Update Host App

**Action:**
- Update host app `composer.json` to require the new package version.
- Implement `ToecommerceUser` on `App\Models\User`.
- Add event listeners for `OrderStatusChanged` to send mail.
- Refactor controllers to use `CartService` / `OrderService` / `PaymentService`.
- Run the full host app test suite to confirm nothing is broken.

---

## Appendix A: Directory Structure (Target State)

```
toecommerce-core/
├── config/
│   └── toecommerce-core.php           # ✅ Populated with real settings
├── database/
│   ├── factories/                     # ✅ Correct namespaces, PSR-4 autoloaded
│   └── migrations/                     # ✅ All .stub files, no timestamps, no Laravel defaults
├── resources/
│   ├── lang/                          # en, es translations
│   └── views/                         # HeroBlock blade views
├── src/
│   ├── Casts/
│   │   └── Money.php
│   ├── Commands/                      # ✅ Useful install command (optional)
│   ├── Concerns/                      # ✅ NEW: HasToecommerceUser trait
│   ├── Contracts/                     # ✅ NEW: ToecommerceUser interface
│   ├── Enums/
│   ├── Events/                        # ✅ NEW: OrderStatusChanged, OrderPlaced, etc.
│   ├── Exceptions/
│   ├── Facades/
│   ├── Filament/
│   │   ├── Actions/
│   │   ├── Forms/
│   │   ├── Pages/
│   │   └── Resources/                  # 8 ecommerce resources
│   ├── Mail/                          # ✅ NEW: Package-level mailables
│   ├── Models/                        # ✅ No User.php, all factory imports fixed
│   ├── Services/                      # ✅ NEW: CartService, OrderService, PaymentService, InventoryService, DiscountService
│   ├── Settings/
│   ├── Traits/
│   ├── ToecommerceCorePlugin.php
│   └── ToecommerceCoreServiceProvider.php  # ✅ hasConfigFile, discoversMigrations, service bindings
├── tests/
│   ├── Feature/                       # ✅ NEW: CartOperationsTest, etc.
│   ├── Unit/
│   │   ├── Casts/
│   │   ├── Enums/
│   │   ├── Events/
│   │   ├── Models/                    # ✅ CartTest, OrderTest, ProductTest, etc.
│   │   └── Services/                  # ✅ CartServiceTest, OrderServiceTest, etc.
│   ├── Pest.php
│   └── TestCase.php
├── composer.json
├── phpstan.neon.dist                  # ✅ level: 8
├── pint.json
├── rector.php
└── README.md                          # ✅ Real documentation
```

---

## Appendix B: Risk Register

| Risk | Mitigation |
|---|---|
| Host app `toecommerce` breaks during package refactor | Run host app tests after every phase. Make changes backward-compatible where possible during the transition. |
| Existing production database has already-run migrations | The `.stub` rename only affects *new* publishes. Existing apps have their own migration copies in `database/migrations/`. No impact. |
| `User` model removal breaks host app auth | Phase 2 includes a clear contract + trait. Host app implements it in one commit. |
| Services add complexity without benefit | Services are tested independently, making the package more robust than model-only logic. |
| Payphone-specific logic in `PaymentService` feels too vendor-locked | Later iterations can introduce a `PaymentDriver` interface. For now, extracting it from the `Order` model is the priority. |

---

*Roadmap drafted: 2026-05-23*
*Next step: Implement Phase 3.*
