# Porting `ToEcommerce` Core Backstore to `phi-ecommerce-core`

> **Status:** Implementation complete. **Package is ready for use by consuming apps.**
> **Constraint:** The current `ToEcommerce` application is left completely untouched. The package is built as a standalone Filament plugin for future apps.

## Decisions captured

| Topic | Decision |
|-------|----------|
| **Menus / MenuItems** | Stay in `ToEcommerce` as CMS logic. Not ported. |
| **StorefrontSettings** | Moved into `phi-ecommerce-core` (admin control panel only). |
| **User model** | Package defines a `JFA\PhiEcommerceCore\Contracts\User` contract and a `user_model` config. No User model or migration is shipped; consuming apps provide their own. |
| **Users / UserInfoEntries resources** | Ported to the package and bound to `config('phi-ecommerce-core.user_model')`. |
| **App integration** | **None.** The current `ToEcommerce` app will not use the package. |
| **Spatie dependencies** | Required directly by `phi-ecommerce-core`. |
| **Policies / authorization** | Kept in `ToEcommerce`; package stays policy-free. Consuming apps register their own policies. |
| **Tests** | Only admin/backstore tests move to the package; storefront/CMS tests stay in `ToEcommerce`. |
| **Storefront code** | **Not ported.** Only admin/Filament functionality is included. |
| **Cart model** | Not shipped; consuming apps provide their own Cart/CartItem. Order migration references carts/order_items tables. |
| **Purchasable contract** | Removed (was storefront-only). |

---

## Scope

### Move to `phi-ecommerce-core` (admin/backstore only)

| Category | Items |
|----------|-------|
| **Filament Resources** | `Categories`, `Discounts`, `Orders`, `ProductCollections`, `ProductVariants`, `Products`, `UserInfoEntries`, `Users` |
| **Filament Pages** | `ManageStorefront` |
| **Filament Actions** | `DiscountsAction`, `BulkDiscountsAction` |
| **Models** | `Category`, `Discount`, `Order`, `OrderItem`, `OrderStatusHistory`, `Product`, `ProductCollection`, `ProductVariant`, `Tax`, `UserInfoEntry` |
| **Interfaces / Contracts** | `User` (no `Purchasable`) |
| **Casts** | `Money` |
| **Enums** | `DiscountCalculationModes`, `DiscountStatus`, `OrderStatus`, `PaymentMethods`, `ProductStatus`, `StockControlModes` |
| **Traits** | `Discountable` (admin-only: discounts relation only), `Publishable` |
| **Settings** | `StorefrontSettings` |
| **Factories** | One per ported model |
| **Migrations** | Core-owned table migrations (see list below) |
| **Translations** | `firesources` keys used by core resources |

### Keep in `ToEcommerce`

- **Storefront:** `Cart`, `CartItem`, cart/checkout/payment controllers, product-listing pages, contact form.
- **CMS:** `Page`, `PageSection`, `Section`, `Menu`, `MenuItem`, `ContentBuilder`, `ContentBlocks`, `RichContentCustomBlocks`, `ManageCompanyInfo`.
- **Auth / Authorization:** `User` model, Filament Shield roles/permissions, all policies.
- **Storefront-only traits:** `Metatags`, `Taxable`, `HasProductVariation`, `MoneyFormat`, `FormatsMoney`, `HasPriceInDollars`, `HasTotalsInDollars`.

---

## Install command

Create a package install command (`phi-ecommerce-core:install`) that automates first-time setup in a consuming app.

### What it publishes

1. **Package config file**
   - `config/phi-ecommerce-core.php` → `config/phi-ecommerce-core.php`

2. **Package migrations**
   - All migrations from `database/migrations/` → `database/migrations/`

3. **Spatie package migrations** (required by the ported core models)
   - `spatie/laravel-media-library` migrations
   - `spatie/laravel-tags` migrations
   - `spatie/laravel-settings` migrations

### Command behavior

- Run `php artisan phi-ecommerce-core:install`.
- Publish config and migrations using the standard Laravel `vendor:publish` mechanism.
- After publishing, ask the user:
  - *"Would you like to run the migrations now?"*
- If the user agrees, run `php artisan migrate`.
- The command should be safe to run on a fresh app. If migrations already exist, the publish step should avoid overwriting (or warn before overwriting).

### Implementation approach

Use the install command provided by `spatie/laravel-package-tools` in `PhiEcommerceCoreServiceProvider`, or create a dedicated `phi-ecommerce-core:install` command. The install step should call the equivalent of:

```bash
php artisan vendor:publish --tag=phi-ecommerce-core-config
php artisan vendor:publish --tag=phi-ecommerce-core-migrations
php artisan vendor:publish --tag=media-library-migrations
php artisan vendor:publish --tag=tags-migrations
php artisan vendor:publish --tag=settings-migrations
```

Tags and exact names should be verified against the installed versions of the Spatie packages.

---

## Phase-by-phase implementation

### Phase 1 — Package skeleton prep

1. Create directories in `phi-ecommerce-core`:
   - `src/{Models,Casts,Enums,Traits,Settings,Contracts,Filament/Resources,Filament/Pages,Filament/Actions,Database/Factories}`
   - `database/migrations`
   - `resources/lang/en/firesources.php`
   - `config/phi-ecommerce-core.php`

2. Update `composer.json` to require:
   - `spatie/laravel-media-library`
   - `spatie/laravel-tags`
   - `spatie/laravel-settings`
   - `filament/spatie-laravel-media-library-plugin`
   - `filament/spatie-laravel-tags-plugin`
   - `filament/spatie-laravel-settings-plugin`

3. Add `config/phi-ecommerce-core.php` with:
   - `user_model` → default `App\Models\User::class`
   - No `cart_model`/`cart_item_model` (admin-only package)

4. Register config, migrations, translations, and the install command in `PhiEcommerceCoreServiceProvider`.

5. Register core resources, pages, and actions in `PhiEcommerceCorePlugin`.

### Phase 2 — Port core classes (admin/backstore only)

1. Copy classes into `JFA\PhiEcommerceCore\...` namespaces.
2. Replace all internal `App\...` references with package equivalents.
3. Create `JFA\PhiEcommerceCore\Contracts\User` contract (no `Purchasable`).
4. Replace `App\Models\User` references with `config('phi-ecommerce-core.user_model')` in:
   - `Order::user()`
   - `UserInfoEntry::user()`
   - `Product::user()`
   - `Users` and `UserInfoEntries` Filament resources
5. Set `protected static ?string $factory` on each package model to its package factory.
6. Port Filament resources preserving the folder-per-resource structure.
7. **Strip all storefront-only methods** from models (cart operations, checkout, storefront data, tax calculations, etc.) — keep only admin/Filament functionality.
8. **Remove storefront-only traits** (`Taxable`, `HasProductVariation`, `MoneyFormat`, `FormatsMoney`, `HasPriceInDollars`, `HasTotalsInDollars`).
9. **Add proper docblocks** to all models for PHPStan (generic types, property annotations).

### Phase 3 — Port migrations

Copy these migrations into `phi-ecommerce-core/database/migrations/` (keep original timestamps):

- `create_products_table`
- `create_categories_table`
- `create_product_collections_table`
- `create_product_variants_table`
- `create_category_product_table`
- `create_product_product_collection_table`
- `create_taxes_table`
- `create_product_tax_table`
- `create_orders_table`
- `create_order_items_table`
- `create_user_info_entries_table`
- `create_discounts_table`
- `create_discountables_table`
- Additive core migrations:
  - product `summary` column
  - order `payment_method` / `status` columns
  - discount columns on `cart_items` / `order_items`
  - removal of discount columns from `products` / `product_variants`
  - performance indexes

**Do not ship** `create_users_table`, spatie package migrations, Laravel defaults, or CMS/storefront migrations.

### Phase 4 — Port factories

1. Copy matching core factories into `phi-ecommerce-core/database/factories/`.
2. Update namespaces to `JFA\PhiEcommerceCore\Database\Factories`.
3. Wire factories to package models via `protected static ?string $factory`.
4. For tests that require a `User`, create a test fixture in `tests/Fixtures/User.php` implementing the `User` contract.

### Phase 5 — Port tests (admin/backstore only)

1. Copy/adapt admin-only Pest tests into `phi-ecommerce-core/tests/`.
2. Replace `App\...` references with `JFA\PhiEcommerceCore\...`.
3. Use the test fixture `User` model where a User is required.
4. **Leave all storefront, CMS, and app-specific tests** in `ToEcommerce` (Cart, CartItem, checkout, product listing, contact form tests).
5. **Remove storefront-specific model tests** (e.g., `ProductTest`, `ProductVariantTest`) — these test storefront behavior not present in the package.
6. **Add test fixtures** for `Cart` and `CartItem` (required for order migration foreign keys and `OrderFactory`).
7. **Set `cart_model` config** in `TestCase` to the test Cart fixture for factory resolution.
8. **Fake `StorefrontSettings`** in tests to avoid database dependency.

### Phase 6 — Package self-verification

Run inside `phi-ecommerce-core`:

```bash
composer dump-autoload
vendor/bin/pest --no-coverage   # Should pass all tests
vendor/bin/pint --test          # Should pass
vendor/bin/phpstan analyse --memory-limit=1G  # Should show 0 errors at level 4
```

**Current status:** All checks pass.

---

## Implementation decisions (post-porting refactor)

Decisions made during the Laravel best-practices review:

| Topic | Decision |
|-------|----------|
| **Mass assignment** | All models use explicit `$fillable` instead of `$guarded = []`. |
| **Cart dependency** | Removed `cart_id`/`cart_item_id` from package migrations entirely. Orders are decoupled from storefront carts. Consuming apps link carts to orders via their own code. |
| **Polymorphic relations** | `order_items` uses `$table->morphs('purchasable')` → `OrderItem::purchasable()` morphTo. `discountables` uses `$table->morphs('discountable')` → `Discountable::discounts()` morphToMany. |
| **Pivot tables** | Removed surrogate `id()` column; use composite primary keys instead. |
| **Money cast** | `Casts/Money.php` stores integer cents, displays dollars. Uses integer arithmetic (`(int) $value / 100`) to avoid floating-point precision loss. |
| **Translations** | `resources/lang/en/firesources.php` populated with all ~150 English keys used in UI (was empty). |
| **Hardcoded routes** | Removed storefront route references (`storefront.checkout`, `storefront.products`). |
| **Customer panel** | Removed all `Filament::getCurrentPanel()->getId() === 'customer'` checks. Package is admin-only. |
| **Redundant migrations** | Deleted "remove discount column" migrations; removed `discount` from `products`/`product_variants` create migrations. |
| **Test fixtures** | Removed `Cart`/`CartItem` fixtures and their test migrations (no longer referenced). |
| **N+1 eager loading** | Added `getEloquentQuery()` with `->with()` to all resources that display related data in tables/infolists. |
| **Migration publishing** | Switched from `discoversMigrations()` to explicit `hasMigrations($this->getMigrations())` (matches `phi-cms-core`). Source files keep their timestamps so Laravel can order them for tests; spatie/laravel-package-tools strips the timestamp prefix and regenerates a fresh one on publish, so consuming apps receive clean, sequentially-numbered migrations. `getMigrations()` lists all 19 files in dependency order. |

## Current status

All phases complete. The package is ready for use by consuming apps.

```bash
# Install in a consuming app:
composer require phi/ecommerce-core
php artisan phi-ecommerce-core:install
php artisan migrate
```

### Package requirements
- PHP 8.4+
- Laravel 11+
- Filament v5
- Spatie packages (required by package):
  - `spatie/laravel-medialibrary`
  - `spatie/laravel-tags`
  - `spatie/laravel-settings`

### Consuming app must provide
- `User` model (default: `App\Models\User`)
- Publish config and run migrations

### Verification
- `vendor/bin/phpstan analyse` → 0 errors
- `vendor/bin/pest` → 18 tests pass (admin/backstore only)
- `vendor/bin/pint --test` → passes
