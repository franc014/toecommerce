# Performance Improvements v2 - Comprehensive Codebase Review

> Generated: 2026-03-13  
> Updated: 2026-03-13 (Automatic Eager Loading Considerations)  
> Target: ToEcommerce Laravel Application  
> Review Type: Thorough Performance Audit

---

## Executive Summary

This document provides a comprehensive performance audit of the ToEcommerce codebase, identifying **22 performance issues** across multiple categories:

- **3 Critical** - High impact, immediate action required
- **12 Important** - Significant impact, address within 2 weeks
- **7 Low** - Minor improvements, address as time permits

**Important Note:** The application has `Model::automaticallyEagerLoadRelationships()` enabled in `AppServiceProvider`, which mitigates several potential N+1 issues with simple relationships.

**Estimated Overall Impact:** 30-50% faster page loads after implementing Critical and Important fixes.

---

## Automatic Eager Loading Impact

The application has the following enabled in `AppServiceProvider::boot()`:

```php
Model::automaticallyEagerLoadRelationships();
```

### What This Handles ✅

- **Simple relationships** accessed as properties: `$product->taxes`, `$product->variants`, `$product->discounts`
- **Automatic batch loading** when iterating over collections
- **Prevents N+1** for basic relationship access patterns

```php
// NO N+1 - handled automatically:
$products = Product::all();
foreach ($products as $product) {
    $product->taxes;      // Auto-loaded for all products in 1 query
    $product->variants;   // Auto-loaded for all products in 1 query
}
```

### What This Does NOT Handle ❌

- **Nested relationships**: `$variant->taxes` (requires loading through parent product)
- **Method calls on relationships**: `$product->taxes()->count()` vs `$product->taxes->count()`
- **Custom query methods**: `validDiscounts()` with `->where()->get()`
- **Eager loading hints**: `->with(['variants.taxes'])` for nested access
- **Existence checks**: Using `count()` instead of `exists()`

```php
// STILL N+1 - automatic loading doesn't help:
foreach ($products as $product) {
    foreach ($product->variants as $variant) {
        $variant->taxes;  // Each variant queries its parent product's taxes
    }
}
```

**Key Takeaway:** Many issues in this audit are **less severe** than they would be without automatic eager loading, but nested relationships and custom query methods still require attention.

---

## Issues Mitigated by Automatic Eager Loading

The following issues are **partially or fully resolved** by `Model::automaticallyEagerLoadRelationships()`:

### ✅ Fully Resolved

- **Simple relationship access** in views and controllers:
    - `$product->taxes` - Now auto-loaded
    - `$product->variants` - Now auto-loaded
    - `$product->discounts` - Now auto-loaded
    - `$product->media` - Now auto-loaded

### ⚠️ Partially Resolved

- **Issue #4** (`$appends`) - Still fires unnecessary queries but no longer N+1
- **Issue #11** (`count()` vs `exists()`) - Less critical now for simple checks
- **Issue #16** (CMS Transformers missing eager load) - Simple relationships auto-loaded, but nested still need manual loading

### ❌ Still Relevant (Not Auto-Loaded)

- **Issue #3** - Custom query methods (`validDiscounts()` with `->where()`)
- **Issue #5** - Nested relationships (`$variant->taxes` through parent product)
- **Issue #7** - CMS transformers using `->count()` method calls
- **Issue #8** - ProductPageController nested eager loading needs
- **Issue #9** - Product variant taxes accessor (nested relationship)
- **Issue #10** - Cart stock check loading many records
- **Issue #12** - Filament actions loading all records
- **Issue #13** - CartItem polymorphic eager loading complexity

---

## Critical Issues (Immediate Action Required)

### 1. Missing Database Indexes on Foreign Keys and Query Columns

**Files:**

- Multiple migration files (see detailed list below)

**Missing Indexes Identified:**

| Table            | Column(s)                                       | Priority |
| ---------------- | ----------------------------------------------- | -------- |
| products         | slug, status                                    | High     |
| products         | status + published_at (composite)               | High     |
| product_variants | product_id, status                              | High     |
| product_tax      | tax_id                                          | High     |
| cart_items       | purchasable_id + purchasable_type (composite)   | High     |
| order_items      | purchasable_id + purchasable_type (composite)   | High     |
| discountables    | discountable_id + discountable_type (composite) | High     |
| orders           | user_id, cart_id, paid_at                       | Medium   |
| categories       | slug                                            | Medium   |
| pages            | slug, status                                    | Medium   |
| menus            | slug                                            | Medium   |

**Impact:** Slow queries on large datasets; full table scans on common lookups. **Not affected by automatic eager loading.**

**Recommended Fix:**

```php
// Create migration: 2026_03_13_add_performance_indexes.php
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->index('slug');
        $table->index('status');
        $table->index(['status', 'published_at']);
    });

    Schema::table('product_variants', function (Blueprint $table) {
        $table->index(['product_id', 'status']);
    });

    Schema::table('product_tax', function (Blueprint $table) {
        $table->index('tax_id');
    });

    Schema::table('cart_items', function (Blueprint $table) {
        $table->index(['purchasable_type', 'purchasable_id']);
    });

    Schema::table('order_items', function (Blueprint $table) {
        $table->index(['purchasable_type', 'purchasable_id']);
    });

    Schema::table('discountables', function (Blueprint $table) {
        $table->index(['discountable_type', 'discountable_id']);
    });

    // Add remaining indexes...
}
```

---

### 2. `::all()` Without Limits on Potentially Large Tables

**Files:**

- `app/Models/Discount.php:30`
- `app/Filament/Forms/ContentBlocks.php:229,243`

**Issue:**
Loading all records into memory without pagination or chunking. **Not affected by automatic eager loading** (memory issue, not query issue).

```php
// app/Models/Discount.php
public static function setStatus()
{
    $now = now();
    foreach (Discount::all() as $discount) {  // Loads ALL discounts
        // ...
    }
}

// app/Filament/Forms/ContentBlocks.php
->options(Product::query()->published()->get()->pluck('title', 'id'))  // ALL products
```

**Impact:** Memory exhaustion on large datasets; slow form loading.

**Recommended Fix:**

```php
// Use chunking for batch processing
public static function setStatus()
{
    $now = now();
    Discount::query()->chunkById(100, function ($discounts) use ($now) {
        foreach ($discounts as $discount) {
            // Process
        }
    });
}

// Use query builder pluck() instead of get()->pluck()
->options(function () {
    return Cache::remember('product_options', 3600, function () {
        return Product::published()->pluck('title', 'id');
    });
})
```

---

### 3. Custom Query Methods in Accessors (Not Auto-Loaded)

**Files:**

- `app/Traits/Discountable.php:20-24`
- `app/Traits/Taxable.php` (method calls vs property access)

**Issue:**
`validDiscounts()` uses `->where()->get()` which is a custom query, not simple relationship access. **Automatic eager loading does NOT handle this.**

```php
// app/Traits/Discountable.php
public function validDiscounts(): Collection
{
    return $this->discounts()->where('status', DiscountStatus::ACTIVE->value)
        ->get();  // Custom query - not auto-loaded!
}

public function hasDiscounts(): Attribute
{
    return Attribute::make(
        get: fn () => ! $this->validDiscounts()->isEmpty()  // Query every time
    );
}
```

**Impact:** Called repeatedly in views, causing N+1 even with automatic eager loading enabled.

**Recommended Fix:**

```php
// Option 1: Cache within instance
private ?Collection $cachedValidDiscounts = null;

public function validDiscounts(): Collection
{
    if ($this->cachedValidDiscounts === null) {
        $this->cachedValidDiscounts = $this->discounts()
            ->where('status', DiscountStatus::ACTIVE->value)
            ->get();
    }
    return $this->cachedValidDiscounts;
}

// Option 2: Use exists() for checks
public function hasDiscounts(): Attribute
{
    return Attribute::make(
        get: fn () => $this->discounts()
            ->where('status', DiscountStatus::ACTIVE->value)
            ->exists()
    );
}
```

---

## Important Issues (Address Within 2 Weeks)

### 4. Model `$appends` Still Causes Unnecessary Accessor Calls

**Status:** ⚠️ **Severity Reduced** (was Critical, now Important)

**Files:**

- `app/Models/Cart.php:23`
- `app/Models/OrderItem.php:17`

**Issue:**
`$appends` causes accessors to fire during serialization. While automatic eager loading prevents the N+1, it still causes unnecessary queries when the data isn't needed.

```php
// app/Models/Cart.php
protected $appends = [
    'total_without_taxes_in_dollars',
    'total_with_taxes_in_dollars',
    'items_count',  // Still fires even with auto eager loading
    // ...
];
```

**Impact:** Unnecessary queries during serialization, though not N+1 thanks to automatic eager loading.

**Recommended Fix:**

```php
// Remove from $appends - accessors still work when called explicitly
protected $appends = [
    // Keep only what's truly needed for all API responses
];

// Access explicitly when needed
$cart->items_count;  // Works fine, single query
```

---

### 5. Nested Relationship N+1 - ProductPageController

**Status:** ⚠️ **Still Relevant** - Automatic eager loading doesn't handle nested relationships

**Files:**

- `app/Http/Controllers/ProductPageController.php:134,53-69`

**Issue:**
Accessing `$variant->taxes` (which goes through `$variant->product->taxes`) is a nested relationship that automatic eager loading doesn't handle.

```php
// In relatedProducts() - nested access
foreach ($product->variants as $variant) {
    $variant->taxes;  // N+1! Each variant queries separately
}
```

**Impact:** Product detail page performs multiple queries for variant taxes.

**Recommended Fix:**

```php
// In ProductPageController::__invoke()
$product->loadMissing([
    'variants.product.taxes',  // Nested eager load
    'variants.discounts',
    'taxes',
    'tags',
    'productCollections',
]);

// Or in relatedProducts() method
return Product::published()
    ->with(['variants.product.taxes', 'taxes', 'media'])
    ->whereHas('productCollections', function ($query) use ($collections) {
        $query->whereIn('product_collections.id', $collections);
    })
    ->where('id', '!=', $this->id)
    ->limit(4)
    ->get();
```

---

### 6. Filament Navigation Badges Execute Queries on Every Page Load

**Files:**

- `app/Filament/Resources/Orders/OrderResource.php:53-61`
- `app/Filament/Resources/ProductVariants/ProductVariantResource.php:45-48`
- `app/Filament/Resources/Products/ProductResource.php:51-54`

**Issue:**
Each resource's `getNavigationBadge()` method executes a count query on every admin page load.

```php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::query()->count();  // Query on every page
}
```

**Impact:** With 11 resources, this causes 11+ count queries per admin page load.

**Recommended Fix:**

```php
public static function getNavigationBadge(): ?string
{
    $cacheKey = 'navigation_badge.' . static::getModel();

    return Cache::remember($cacheKey, 300, function () {  // 5 minutes
        return static::getModel()::query()->count();
    });
}

// Create observers to clear cache
// app/Observers/ProductObserver.php
public function saved(Product $product): void
{
    Cache::forget('navigation_badge.' . Product::class);
}
```

---

### 7. N+1 in CMS Content Transformers

**File:** `app/CMS/CollectionsTransformable.php:22`

**Issue:**
Counting products within a loop causes N+1 queries.

```php
foreach ($collections as $key => $collection) {
    $item['data']['collections'][$key] = [
        'products_count' => $collection->products()->count(),  // N+1
    ];
}
```

**Recommended Fix:**

```php
// Use withCount() in the query
$collections = ProductCollection::whereIn('id', $collectionsIds)
    ->withCount('products')
    ->get();

// Then access as property
'products_count' => $collection->products_count,
```

---

### 8. Missing Eager Loading in ProductPageController

**File:** `app/Http/Controllers/ProductPageController.php:134,53-69`

**Issue:**
Accessing relationships without eager loading in `relatedProducts()` and meta keywords generation.

```php
// Line 134
->keywords($this->product->tags()->pluck('name')->implode(', '))

// Lines 53-69
$relatedProducts = $product->relatedProducts()?->map(function ($product) {
    return [
        'has_variants' => $product->hasPublishedVariants(),
        'has_discounts' => $product->has_discounts,
        'discounts' => $product->discountsForList,
    ];
});
```

**Recommended Fix:**

```php
// In ProductPageController::__invoke()
$product->loadMissing([
    'variants.discounts',
    'taxes',
    'tags',
    'productCollections',
]);

// In relatedProducts() method
return Product::published()
    ->with(['variants.discounts', 'taxes', 'media'])
    ->whereHas('productCollections', function ($query) use ($collections) {
        $query->whereIn('product_collections.id', $collections);
    })
    ->where('id', '!=', $this->id)
    ->limit(4)
    ->get();
```

---

### 9. Product Variant Taxes Accessor N+1

**File:** `app/Models/ProductVariant.php:65-68`

**Issue:**
When variants are loaded without the parent product relationship, accessing `taxes` causes N+1.

```php
public function getTaxesAttribute()
{
    return $this->product->taxes;  // Accesses parent product relationship
}
```

**Recommended Fix:**
Ensure `product.taxes` is eager loaded when fetching variants:

```php
// In queries that fetch variants
ProductVariant::with('product.taxes')->get();

// In CartItem model, update $with array
protected $with = ['purchasable' => function ($morphTo) {
    $morphTo->morphWith([
        Product::class => ['taxes', 'discounts'],
        ProductVariant::class => ['product.taxes', 'discounts'],
    ]);
}];
```

---

### 10. Cart::allByProductInOpenCarts May Load Too Many Records

**File:** `app/Models/CartItem.php:78-86`

**Issue:**
Method returns all matching records when only aggregate data is needed for stock checks.

```php
public function scopeAllByProductInOpenCarts($query, $purchasable_id, $purchasable_type)
{
    return $query->where('purchasable_id', $purchasable_id)
        ->where('purchasable_type', $purchasable_type)
        ->whereHas('cart', function ($q) {
            $q->where('paid_at', null);
        })
        ->get();  // Could return many records
}
```

**Recommended Fix:**

```php
// Add a new scope for stock calculations
public function scopeSumQuantityInOpenCarts($query, $purchasable_id, $purchasable_type): int
{
    return $query->where('purchasable_id', $purchasable_id)
        ->where('purchasable_type', $purchasable_type)
        ->whereHas('cart', function ($q) {
            $q->where('paid_at', null);
        })
        ->sum('quantity');
}

// Use in Cart.php
$totalQuantity = CartItem::sumQuantityInOpenCarts($data['purchasable_id'], $data['purchasable_type'])
    + $data['quantity'];
```

---

### 11. Count Queries in Model Methods (Use exists() Instead)

**Files:**

- `app/Models/Product.php:103-111`
- `app/Models/Order.php:46`
- `app/Models/Page.php:50`

**Issue:**
Using `count()` when `exists()` is more efficient for existence checks.

```php
public function hasVariants(): bool
{
    return $this->variants()->count() >= 1;
}

public function hasPublishedVariants(): bool
{
    return $this->variants()->published()->count() >= 1;
}
```

**Recommended Fix:**

```php
public function hasVariants(): bool
{
    return $this->variants()->exists();
}

public function hasPublishedVariants(): bool
{
    return $this->variants()->published()->exists();
}

public function hasItems(): bool
{
    return $this->orderItems()->exists();
}
```

---

### 12. Filament Actions Loading All Records

**Files:**

- `app/Filament/Actions/DiscountsAction.php:23-29`
- `app/Filament/Actions/BulkDiscountsAction.php:19`
- `app/Filament/Resources/Products/Tables/ProductsTable.php:100,105`

**Issue:**
Loading all records then plucking, or using `get()` when query builder methods suffice.

```php
// Loads all then plucks
'taxes' => $record->taxes()->get()->pluck('id')

// Loads all taxes into memory
return Tax::all()->pluck('name', 'id');
```

**Recommended Fix:**

```php
// Use query builder pluck directly
'taxes' => $record->taxes()->pluck('id')

// Cache options
->options(function () {
    return Cache::remember('valid_discounts_options', 300, function () {
        return Discount::valid()->pluck('name', 'id');
    });
})
```

---

### 13. CartItem Global `$with` Causes N+1 with Polymorphic Relations

**File:** `app/Models/CartItem.php:21`

**Issue:**
Global eager loading of polymorphic `purchasable` relation doesn't properly eager load nested relationships for different types.

```php
protected $with = ['purchasable'];  // Always eager loads, but not nested relations
```

**Recommended Fix:**

```php
// Remove global $with
// protected $with = ['purchasable'];

// Handle eager loading in CartController::show()
$cart = Cart::byUICartId($request->input('id'))
    ->with(['items.purchasable' => function ($morphTo) {
        $morphTo->morphWith([
            Product::class => ['variants.discounts', 'taxes'],
            ProductVariant::class => ['product.taxes', 'discounts'],
        ]);
    }])
    ->firstOrFail();
```

---

### 14. Polymorphic Queries Without Type Index

**Files:**

- `database/migrations/2026_01_28_000751_create_discountables_table.php`
- `database/migrations/2025_09_24_211813_create_cart_items_table.php`

**Issue:**
Polymorphic tables missing composite indexes on type+id columns.

**Recommended Fix:**

```php
// In existing migrations or new migration
Schema::table('cart_items', function (Blueprint $table) {
    $table->index(['purchasable_type', 'purchasable_id']);
});

Schema::table('discountables', function (Blueprint $table) {
    $table->index(['discountable_type', 'discountable_id']);
});

Schema::table('order_items', function (Blueprint $table) {
    $table->index(['purchasable_type', 'purchasable_id']);
});
```

---

### 15. Collections Page Missing Eager Loading

**File:** `app/Http/Controllers/CollectionsPageController.php:15`

**Issue:**
Loading collections without eager loading relationships that may be accessed.

```php
$collections = ProductCollection::query()->get()->map(function ($collection) {
    // Each collection may lazy load products relationship
    return [
        'products_count' => $collection->products()->count(),
    ];
});
```

**Recommended Fix:**

```php
$collections = ProductCollection::query()
    ->withCount('products')
    ->get()
    ->map(function ($collection) {
        return [
            'id' => $collection->id,
            'title' => $collection->title,
            'slug' => $collection->slug,
            'featured_image' => $collection->featured_image,
            'products_count' => $collection->products_count,
        ];
    });
```

---

## Low Severity Issues (Nice to Have)

### 16. CMS Transformers Missing Eager Loading

**Files:**

- `app/CMS/ProductsTransformable.php:13`
- `app/CMS/FeaturedProductTransformable.php:14`

**Issue:**
Missing eager load for `taxes` relationship which is accessed via Taxable trait.

**Recommended Fix:**

```php
// ProductsTransformable
$products = Product::whereIn('id', $productsIds)
    ->with(['variants.discounts', 'taxes', 'media'])
    ->get();

// FeaturedProductTransformable
$product = Product::with(['variants.discounts', 'taxes', 'media'])->find($productsId);
```

---

### 17. Duplicate Image URL in CartItemResource

**File:** `app/Http/Resources/CartItemResource.php:20,33`

**Issue:**
`image_url` key defined twice in the array.

**Recommended Fix:**
Remove duplicate key (line 33).

---

### 18. Debug Statement in Production Code

**File:** `app/Observers/MenuItemObserver.php:12`

**Issue:**
`ray()` debug call present in observer.

```php
public function created(MenuItem $menuItem): void
{
    ray($menuItem);  // Debug statement
    Cache::forget('menu.' . $menuItem->menu->slug);
}
```

**Recommended Fix:**
Remove the `ray()` call.

---

### 19. Large Commented Code Block in CartItem

**File:** `app/Models/CartItem.php:102-131`

**Issue:**
Large commented-out `toArray()` method.

**Recommended Fix:**
Remove commented code if not needed.

---

### 20. Menu Cache Not Cleared on Menu Update

**File:** `app/Observers/MenuItemObserver.php`

**Issue:**
Cache only cleared when menu items are updated, not when Menu itself changes.

**Recommended Fix:**
Create a MenuObserver:

```php
// app/Observers/MenuObserver.php
class MenuObserver
{
    public function updated(Menu $menu): void
    {
        Cache::forget('menu.' . $menu->slug);
    }

    public function deleted(Menu $menu): void
    {
        Cache::forget('menu.' . $menu->slug);
    }
}
```

---

### 21. Settings Caching Already Properly Configured

**Status:** ✅ Already handled properly

The application correctly caches settings in `HandleInertiaRequests` middleware.

---

### 22. Discountable Trait Repeated Queries

**File:** `app/Traits/Discountable.php`

**Issue:**
`validDiscounts()` is called multiple times within the same request context.

**Recommended Fix:**
Cache the result within the instance:

```php
private ?Collection $cachedValidDiscounts = null;

public function validDiscounts(): Collection
{
    if ($this->cachedValidDiscounts === null) {
        $this->cachedValidDiscounts = $this->discounts()
            ->where('status', DiscountStatus::ACTIVE->value)
            ->get();
    }

    return $this->cachedValidDiscounts;
}
```

---

## Implementation Priority

### Phase 1 - Critical (This Week)

1. ✅ **Issue #1** - Add missing database indexes
2. ✅ **Issue #2** - Fix `::all()` without limits on large tables
3. ✅ **Issue #3** - Cache validDiscounts in Discountable trait (custom query methods)

### Phase 2 - Important (Weeks 2-3)

4. ✅ **Issue #4** - Fix `$appends` causing unnecessary accessor calls
5. ✅ **Issue #5** - Add nested eager loading to ProductPageController
6. ✅ **Issue #6** - Cache Filament navigation badges
7. ✅ **Issue #7** - Fix CMS transformers N+1
8. ✅ **Issue #9** - Fix Product Variant taxes accessor N+1
9. ✅ **Issue #10** - Optimize `allByProductInOpenCarts` scope
10. ✅ **Issue #13** - Fix CartItem polymorphic eager loading
11. ✅ **Issue #12** - Optimize Filament form queries
12. ✅ **Issue #14** - Add polymorphic relationship indexes

### Phase 3 - Low Priority (Week 4+)

13. ✅ **Issue #17** - Remove duplicate Image URL in CartItemResource
14. ✅ **Issue #18** - Remove ray() debug call
15. ✅ **Issue #19** - Clean up commented code in CartItem
16. ✅ **Issue #20** - Add MenuObserver for cache clearing
17. ✅ **Issue #22** - Cache Discountable trait repeated queries

---

## Positive Findings

The codebase already implements several good performance practices:

1. **Effective Cache Usage:**
    - `HandleInertiaRequests` middleware caches menus and settings
    - `Page::bySlug()` caches page lookups

2. **Proper Eager Loading:**
    - `ProductsPageController` uses `with(['variants.discounts'])`
    - `CollectionPageController` eager loads variants

3. **Inertia Optimizations:**
    - `Inertia::once()` used for shared data
    - Proper use of deferred props where applicable

4. **Automatic Eager Loading:**
    - `Model::automaticallyEagerLoadRelationships()` enabled in AppServiceProvider

---

## Performance Impact Summary

| Fix Category       | Query Reduction    | Response Time | Memory Usage |
| ------------------ | ------------------ | ------------- | ------------ |
| Database Indexes   | 0-20%              | 30-70%        | No change    |
| $appends Fix       | 50-90% (cart APIs) | 40-60%        | Reduced      |
| Filament Badges    | 3-5 queries/page   | 100-300ms     | No change    |
| N+1 in CMS         | 80-95% (homepage)  | 50-80%        | Reduced      |
| count() → exists() | 20-40%             | 10-30%        | No change    |

**Overall Estimated Improvement:** 40-60% faster page loads

---

## Testing Recommendations

After implementing each fix, verify with:

```php
// Enable query logging
DB::listen(function ($query) {
    logger($query->sql, $query->bindings);
});

// Add performance assertions to tests
test('product listing generates less than 10 queries', function () {
    Product::factory()->count(20)->create();

    DB::enableQueryLog();
    $response = $this->get('/products');
    $queries = DB::getQueryLog();

    expect(count($queries))->toBeLessThan(10);
});
```

---

## Notes

- This audit assumes the application may grow significantly
- Many issues are currently mitigated by small dataset size
- Implementing these fixes proactively will prevent future performance problems
- Consider using Laravel Telescope in production to monitor actual query patterns

---

_Generated from comprehensive codebase analysis - 2026-03-13_
