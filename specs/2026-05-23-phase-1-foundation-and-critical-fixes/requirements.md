# Phase 1: Foundation & Critical Fixes — Requirements

## Overview

This phase makes the `toecommerce-core` package installable and correct at a structural level. It fixes the critical bugs and structural misconfigurations that would cause a fresh install to fail.

## Scope

**In scope:**
- Fix factory namespace imports in all package models
- Remove Laravel framework default migrations (users, cache, jobs)
- Convert all package migrations to `.stub` files and switch to `discoversMigrations()`
- Register the config file in the service provider
- Remove dead boilerplate code
- Ensure the host app continues to work after these changes

**Out of scope:**
- Extracting business logic to services (Phase 3)
- Decoupling the `User` model (Phase 2)
- Removing `App\Mail` dependency (Phase 2)
- Adding tests (Phase 4)
- Adding type hints (Phase 5)
- Rewriting README (Phase 6)

## Decisions

### 1. Factory Namespace Fix

**Decision:** Update every model's factory import from `Database\Factories\XFactory` to `JFA\ToecommerceCore\Database\Factories\XFactory`.

**Rationale:** The `spatie/laravel-package-tools` factory name resolver maps `JFA\ToecommerceCore\Models\Product` → `JFA\ToecommerceCore\Database\Factories\ProductFactory`. However, the `@use HasFactory<XFactory>` docblock must resolve correctly for PHPStan and IDE autocompletion.

### 2. Migration `.stub` Convention

**Decision:** Rename all migrations from `YYYY_MM_DD_HHMMSS_create_xxx_table.php` to `create_xxx_table.php.stub`.

**Rationale:** Hardcoded 2025 timestamps are stale and cause unpredictable ordering in fresh Laravel installations. `spatie/laravel-package-tools` handles `.stub` files by prepending the current timestamp during publish.

### 3. Remove Laravel Default Migrations

**Decision:** Delete `0001_01_01_000000_create_users_table.php`, `0001_01_01_000001_create_cache_table.php`, and `0001_01_01_000002_create_jobs_table.php`.

**Rationale:** These are Laravel framework migrations. Every Laravel app already has them. Including them guarantees a "table already exists" error on `php artisan migrate`.

### 4. Register Config File

**Decision:** Add `->hasConfigFile()` to `ToecommerceCoreServiceProvider::configurePackage()`.

**Rationale:** The file `config/toecommerce-core.php` exists but is never loaded or published. This means `config('toecommerce-core.*')` calls silently return null.

### 5. Clean Up Dead Code

**Decision:** Remove empty boilerplate classes and the unused `stubs/` directory.

**Rationale:** Dead code creates confusion, triggers PHPStan warnings, and misleads consumers about the package's API surface.

## Context

### Repository
- **Package repo:** `/Users/franciscoandrade/Herd/toecommerce-core`
- **Current branch:** `phase-1-foundation-and-critical-fixes`
- **Host app:** `/Users/franciscoandrade/Herd/toecommerce`

### Files to be modified
- `src/ToecommerceCoreServiceProvider.php`
- `src/Models/*.php` (14 models)
- `database/migrations/` (all files)
- `config/toecommerce-core.php`
- `src/ToecommerceCoreCommand.php`
- `src/Testing/TestsToecommerceCore.php`
- `src/ToecommerceCore.php`
- `stubs/`

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Host app tests fail after factory namespace change | Medium | High | Run host app test suite after fix |
| Migration `.stub` rename breaks existing installs | Low | Medium | Existing installs have their own migration copies |
| Config registration causes merge issues | Low | Low | Host app already has empty config |

## Acceptance Criteria (Summary)

1. All 14 models import the correct factory namespace.
2. No Laravel default migrations exist in the package.
3. All package migrations are `.stub` files with no timestamps.
4. `ToecommerceCoreServiceProvider` registers config, translations, views, and discovers migrations.
5. Dead boilerplate code is removed.
6. Host app tests pass.
7. Package tests (existing) still pass.
