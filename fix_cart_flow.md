# Cart Flow Improvement Plan

## Current Issues Identified

1. **Relationship caching problem** - `$this->items` caches the collection, causing stale data in `updateCartTally()`
2. **Multiple database queries** - `getItemByPurchasable()` loads all items just to find one
3. **Tight coupling** - `addOrUpdateItem()` does too much (stock check, find item, create/update, return)
4. **Event-driven tally updates** - can lead to race conditions
5. **Inconsistent data flow** - tallies updated via events, but items updated directly

## Implemented Improvements

### ✅ 1. Use Query Builder for Item Lookup (Performance)

**Status:** IMPLEMENTED
**File:** `app/Models/Cart.php`

**Changes:**

- `getItemByPurchasable()` - Changed from `$this->items` (collection) to `$this->items()` (query builder)
- `itemById()` - Changed from `$this->items->findOrFail()` to `$this->items()->findOrFail()`

**Before:**

```php
public function getItemByPurchasable(int $purchasableId, string $purchasableType): ?CartItem
{
    return $this->items->where('purchasable_id', $purchasableId)
        ->where('purchasable_type', $purchasableType)
        ->first();
}

public function itemById(int $id): ?CartItem
{
    return $this->items->findOrFail($id);
}
```

**After:**

```php
public function getItemByPurchasable(int $purchasableId, string $purchasableType): ?CartItem
{
    return $this->items() // Use query builder, not collection
        ->where('purchasable_id', $purchasableId)
        ->where('purchasable_type', $purchasableType)
        ->first();
}

public function itemById(int $id): ?CartItem
{
    return $this->items()->findOrFail($id);
}
```

**Benefits:**

- Does not load entire items collection into memory
- Single targeted SQL query
- Prevents relationship caching issues
- Better performance for carts with many items

### ✅ 2. Explicit Tally Recalculation (Reliability)

**Status:** IMPLEMENTED
**Files:**

- `app/Models/Cart.php` - Modified `addOrUpdateItem()` and `updateCartTally()`
- `app/Models/CartItem.php` - Modified `saved` event listener

**Changes:**

1. Modified `updateCartTally()` to refresh relationship: `$this->load('items')`
2. Modified `static::saved` event in `CartItem` to: `$cartItem->cart->load('items')->updateCartTally()`
3. Added explicit `updateCartTally()` call in `addOrUpdateItem()` after operations
4. Added `$cartItem->refresh()` in `addOrUpdateItem()` after updating existing items

**Benefits:**

- Predictable tally updates
- No race conditions from stale data
- Clear data flow
- Easier to debug and test
- Fixed stale model instance issue when updating items

## Pending Improvements

### 3. Add Database-Level Constraints (Data Integrity)

**Status:** PENDING
**Priority:** Medium

Add a unique constraint to prevent duplicate cart items:

```php
// Migration
$table->unique(['cart_id', 'purchasable_id', 'purchasable_type'], 'unique_cart_item');
```

**Benefits:**

- Prevents data corruption at database level
- Catches race conditions
- Enforces business rules

### 4. Optimistic Locking (Concurrency)

**Status:** PENDING
**Priority:** Low (unless high concurrency issues observed)

Add version/updated_at checks to prevent race conditions:

```php
// In updateCartTally()
$this->update([
    'total_without_taxes' => $totalWithoutTaxes,
    'total_with_taxes' => $totalWithTaxes,
    // ... other fields
    'updated_at' => now(), // Forces fresh timestamp
]);
```

**Benefits:**

- Handles high concurrency scenarios
- Prevents lost updates

### 5. Extract Cart Operations to a Service (Maintainability)

**Status:** PENDING
**Priority:** Low

Move business logic out of the model:

```php
class CartService
{
    public function addItem(Cart $cart, Purchasable $item, int $quantity): CartItem
    {
        // All logic here
    }
}
```

**Benefits:**

- Single Responsibility Principle
- Easier to test
- Better separation of concerns
- Can inject dependencies

## Testing Checklist

After implementing changes:

- [ ] Run `AddToCartTest` - verify flaky test is fixed
- [ ] Run all cart-related tests
- [ ] Check for impacts on:
    - [ ] Order creation
    - [ ] Cart merging (user login)
    - [ ] Cart restoration
    - [ ] Discount calculations
    - [ ] Tax calculations

## Migration Path

1. ✅ Deploy improvements 1 & 2 (current)
2. Monitor for issues in production
3. Implement improvement 3 (DB constraints) during next deployment window
4. Evaluate need for improvements 4 & 5 based on production metrics

## Implementation Results

### Test Results (After Implementation)

- ✅ All 259 tests passing (1221 assertions)
- ✅ Flaky test "can add a variant to the cart after a product has been added" now passes consistently (5/5 runs)
- ✅ All AddToCartTest tests passing (29 tests, 113 assertions)
- ✅ All CheckoutTest tests passing (43 tests, 166 assertions)
- ✅ PerformsAddToCartTest tests passing (including the previously failing order item update test)

### Key Issues Fixed

1. **Stale Model Instance Issue**
    - Problem: When updating an existing cart item, `addOrUpdateItem()` was returning the original model instance that had stale data
    - Solution: Added `$cartItem->refresh()` after calling `updateItem()` to reload the model with fresh database values

2. **Relationship Caching in Helper Methods**
    - Problem: `itemById()` was using `$this->items` (cached collection) instead of `$this->items()` (fresh query)
    - Solution: Changed both `getItemByPurchasable()` and `itemById()` to use query builder

3. **Race Condition in Event-Driven Updates**
    - Problem: The `saved` event was calling `updateCartTally()` which used cached relationship data
    - Solution: Modified event to refresh relationship first: `$cartItem->cart->load('items')->updateCartTally()`

### Lessons Learned

- **Query Builder vs Collection**: In Laravel, `$this->relation` returns a cached collection, while `$this->relation()` returns a query builder. When you need fresh data, always use the query builder.

- **Model Instance Staleness**: When you fetch a model, then call a method that finds the same model again (by ID), modifies it, and saves, you now have TWO different instances in memory representing the same database row. Use `refresh()` or `fresh()` to synchronize.

- **Event Ordering**: Laravel fires `saving` → `creating/updating` → `created/updated` → `saved`. If you need to act on updated values in events, be aware that `updated` fires before `saved`.

- **Testing Race Conditions**: Flaky tests that pass "most of the time" usually indicate timing issues with:
    - Database transactions
    - Event listeners
    - Relationship caching
    - Model instance staleness

### Code Style

All changes passed Laravel Pint linting with no issues.
