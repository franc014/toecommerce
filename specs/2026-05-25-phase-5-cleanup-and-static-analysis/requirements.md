# Phase 5: Cleanup, Bug Fixes & Static Analysis

## Goal
Fix real bugs discovered by static analysis, remove dead code, clean unused imports, and ensure package health.

## Background
After Phases 1–4 introduced new services, traits, and tests, PHPStan and Rector surfaced latent bugs and dead code. Phase 5 addresses these without adding features.

## Requirements

### R1: Fix Enum Comparison Bugs
- **R1.1**: `DiscountService` compared `DiscountCalculationModes->value` (string) against enum objects using `===`, which always evaluated to `false`.
  - Fixed by comparing enum objects directly: `$calculationMode === DiscountCalculationModes::HIGHEST`.
- **R1.2**: Removed unreachable `return $product->price;` and `return 0;` fallbacks since enum is exhaustive.

### R2: Remove Dead Code in `Discountable` Trait
- **R2.1**: `discountPercentage()` had unreachable `return 0;` after `if/elseif` on exhaustive enum.
- **R2.2**: `discountedPrice()` had unreachable `return 0;` after `if/elseif` on exhaustive enum.
- Removed both.

### R3: Remove Unused Imports
- **R3.1**: `InventoryService` imported `ProductVariant` but never used it.

### R4: Verify Package Health
- **R4.1**: All 289 unit tests pass.
- **R4.2**: PHPStan on changed files shows zero new errors introduced by Phases 1–4.

## Out of Scope
- Fixing pre-existing PHPStan property-notFound errors for Eloquent magic properties.
- Adding new features or tests.

## Acceptance Criteria
- [ ] `DiscountService` uses enum-to-enum comparison.
- [ ] `Discountable` trait has no unreachable statements.
- [ ] `InventoryService` has no unused imports.
- [ ] All tests pass.
- [ ] No new PHPStan errors on changed code.
