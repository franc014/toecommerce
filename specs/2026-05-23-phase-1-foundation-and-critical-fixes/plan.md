# Phase 1: Foundation & Critical Fixes — Plan

## Task Group 1: Fix Factory Namespace Imports

**Goal:** Ensure every model references its factory in the correct package namespace.

**Files:** `src/Models/*.php` (14 models)

**Steps:**
1.1. Update `use Database\Factories\{Model}Factory;` to `use JFA\ToecommerceCore\Database\Factories\{Model}Factory;` in each file.
1.2. Verify the `@use HasFactory<{Model}Factory>` docblock is correct.
1.3. Verify factories in `database/factories/` declare `protected $model`.

**Affected models:** Cart, CartItem, Category, Discount, Order, OrderItem, OrderStatusHistory, Product, ProductCollection, ProductVariant, Tax, User, UserInfoEntry

---

## Task Group 2: Remove Laravel Default Migrations

**Goal:** Prevent "table already exists" errors on fresh installs.

**Files:** `database/migrations/`

**Steps:**
2.1. Delete `database/migrations/0001_01_01_000000_create_users_table.php`
2.2. Delete `database/migrations/0001_01_01_000001_create_cache_table.php`
2.3. Delete `database/migrations/0001_01_01_000002_create_jobs_table.php`

---

## Task Group 3: Convert Migrations to `.stub` Files

**Goal:** Use the Spatie package-tools convention for publishable migrations.

**Files:** `database/migrations/` (all remaining `.php` files), `src/ToecommerceCoreServiceProvider.php`

**Steps:**
3.1. Rename every migration from `YYYY_MM_DD_HHMMSS_create_xxx_table.php` to `create_xxx_table.php.stub`.
3.2. Delete `database/migrations/create_toecommerce_core_table.php.stub` (boilerplate).
3.3. Update `ToecommerceCoreServiceProvider::configurePackage()`:
   - Remove `->hasMigrations()`
   - Add `->discoversMigrations()`
3.4. Remove the `getMigrations()` method override.

---

## Task Group 4: Register Config File

**Goal:** Make `config/toecommerce-core.php` publishable and loadable.

**Files:** `src/ToecommerceCoreServiceProvider.php`, `config/toecommerce-core.php`

**Steps:**
4.1. Add `->hasConfigFile()` to `configurePackage()`.
4.2. Populate `config/toecommerce-core.php` with a minimal structure.

---

## Task Group 5: Remove Dead Code

**Goal:** Eliminate empty boilerplate classes and unused directories.

**Actions:**
5.1. Delete `src/ToecommerceCore.php` (empty class).
5.2. Delete `src/ToecommerceCoreCommand.php` (boilerplate command).
5.3. Delete `src/Testing/TestsToecommerceCore.php` (empty mixin).
5.4. Remove `stubs/` directory and publishing logic from `packageBooted()`.
5.5. Remove Facade alias from `composer.json`.

---

## Task Group 6: Validation

**Goal:** Confirm the package and host app are still functional.

**Steps:**
6.1. Run `composer dump-autoload` in the package.
6.2. Run `composer dump-autoload` in the host app.
6.3. Run host app tests: `php artisan test --compact`
6.4. Check PHP syntax of key files.

---

## Execution Order

1. Task Group 2 (Remove Laravel default migrations) — Low risk.
2. Task Group 3 (Convert to `.stub`) — Structural change.
3. Task Group 4 (Register config) — Service provider change.
4. Task Group 5 (Remove dead code) — Cleanup.
5. Task Group 1 (Fix factory namespaces) — Model changes.
6. Task Group 6 (Validation) — Verify everything.
