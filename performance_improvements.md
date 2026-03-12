# Performance Improvements Analysis

> Generated: 2026-02-19
> Target Platform: Laravel Cloud (memory-constrained environment)

## Executive Summary

This document outlines performance improvements for the ToEcommerce application, a Laravel 12 + Inertia.js v2 + Filament v4 e-commerce platform. The analysis focuses on reducing database queries, memory usage, and optimizing for Laravel Cloud's memory-constrained environment.

---

## Tech Stack Overview

| Layer              | Technology   | Version |
| ------------------ | ------------ | ------- |
| Backend            | Laravel      | 12.52   |
| Admin Panel        | Filament     | 4.7     |
| Frontend Framework | Vue          | 3.5     |
| SSR Framework      | Inertia.js   | 2.x     |
| Styling            | Tailwind CSS | 4.x     |
| Database           | PostgreSQL   | -       |
| Cache Driver       | Database     | -       |
| Queue Driver       | Database     | -       |

---

## Critical Issues (High Impact)

### 1. N+1 Queries on Model Accessors

**Severity:** 🔴 Critical
**Impact:** 3-5 extra queries per model instance
**Memory Impact:** High

**Locations:**

- `app/Models/Product.php:42`
- `app/Models/ProductVariant.php:24`
- `app/Models/Cart.php:22`
- `app/Models/CartItem.php:22`
- `app/Models/User.php:67`

**Problem:**

The `$appends` attribute forces ALL accessors to load on every model retrieval, even when the data isn't needed:

```php
// Product.php
protected $appends = [
    'price_in_dollars',
    'price_with_taxes_in_dollars',
    'formatted_taxes',
    'has_discounts',
    'discounted_price_in_dollars'
];
```

Each appended accessor triggers additional queries:

- `has_discounts` → calls `validDiscounts()` → database query
- `discounted_price_in_dollars` → calls `discountedPrice()` → queries discounts
- `price_with_taxes_in_dollars` → accesses `$this->taxes` relationship

**Impact Calculation:**

- Product listing with 20 products = 20 × 5 accessors = 100 potential queries
- Cart with 10 items = 10 × 7 accessors = 70 potential queries

**Current State Analysis:**

The application already uses manual mapping in most controllers, making `$appends` redundant:

```php
// ProductsPageController.php:15-30 - Already mapping explicitly
$products = Product::published()->with('variants')->paginate(...)->through(function ($product) {
    return [
        'price_in_dollars' => $product->price_in_dollars,  // Manual mapping!
        'has_discounts' => $product->has_discounts,        // Manual mapping!
        // ...
    ];
});

// ProductPageController.php:26-46 - Already mapping explicitly
$data = [
    'price_in_dollars' => $product->price_in_dollars,
    'has_discounts' => $product->has_discounts,
    // ...
];
```

This means `$appends` is causing double work in some cases - the accessor fires from `$appends`, then we explicitly call it again.

**Where `$appends` IS Currently Needed:**

Only in API-style responses where models are returned directly:

- `CartController::show()` - returns `$cart->items->toArray()`
- `CartItemController` responses
- Any `$model->toArray()` or `$model->toJson()` calls

**Recommended Fix - Hybrid Strategy:**

#### Step 1: Remove `$appends` from models that use manual mapping

**Models to update:**

```php
// app/Models/Product.php - REMOVE $appends
// protected $appends = [...]; // Delete this

// app/Models/ProductVariant.php - REMOVE $appends
// protected $appends = [...]; // Delete this

// app/Models/User.php - REMOVE $appends
// protected $appends = [...]; // Delete this

// app/Models/Order.php - REMOVE $appends
// protected $appends = [...]; // Delete this
```

#### Step 2: Keep minimal `$appends` on Cart/CartItem (API-style responses)

```php
// app/Models/Cart.php - KEEP but minimize
protected $appends = [
    'total_without_taxes_in_dollars',
    'total_with_taxes_in_dollars',
    'items_count',
];

// app/Models/CartItem.php - KEEP but minimize
protected $appends = [
    'price_in_dollars',
    'total_in_dollars',
    'image_url',
];
```

#### Step 3: Understand automatic eager loading

With `Model::automaticallyEagerLoadRelationships()` enabled, relationship-based accessors work without N+1:

```php
// NO manual with() needed - automatic eager loading handles this:
Product::get();

foreach ($products as $product) {
    $product->price_with_taxes_in_dollars;  // $this->taxes auto-eager loaded
    $product->has_discounts;                 // Still N+1! Calls validDiscounts() method
}
```

**Important distinction:**

- **Relationship accessors** (accessing `$this->taxes`, `$this->variants`) → Handled automatically
- **Method-based accessors** (calling `$this->validDiscounts()`, custom queries) → Still N+1, needs caching or eager loading

#### Step 4: Choose a CartItem Serialization Strategy

When removing `$appends` from CartItem, choose one of these approaches to ensure Vue components receive the expected data:

---

**Option A: Override `toArray()` in CartItem Model**

```php
// app/Models/CartItem.php
public function toArray(): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'slug' => $this->slug,
        'price_in_dollars' => $this->price_in_dollars,
        'total_in_dollars' => $this->total_in_dollars,
        'quantity' => $this->quantity,
        'image_url' => $this->image_url,
        'formatted_variation' => $this->formatted_variation,
        'has_discount' => $this->has_discount,
        'discounted_price_in_dollars' => $this->discounted_price_in_dollars,
    ];
}
```

| Pros                            | Cons                                                         |
| ------------------------------- | ------------------------------------------------------------ |
| ✅ Cleaner controller code      | ❌ `toArray()` called in all contexts (queues, logs, events) |
| ✅ Centralized mapping          | ❌ Less flexibility for different endpoints                  |
| ✅ Consistent output everywhere | ❌ Could affect queued jobs that serialize CartItem          |

---

**Option B: Manual Mapping in Controller**

```php
// app/Http/Controllers/CartController.php
public function show(Request $request)
{
    $cart = Cart::byUICartId($request->input('id'))
        ->with(['items.purchasable'])
        ->firstOrFail();

    return [
        'ui_cart_id' => $cart->ui_cart_id,
        'items' => $cart->items->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'price_in_dollars' => $item->price_in_dollars,
            'total_in_dollars' => $item->total_in_dollars,
            'quantity' => $item->quantity,
            'image_url' => $item->image_url,
            'formatted_variation' => $item->formatted_variation,
            'has_discount' => $item->has_discount,
            'discounted_price_in_dollars' => $item->discounted_price_in_dollars,
        ]),
        'cart_aggregation' => [
            'total_without_taxes_in_dollars' => $cart->total_without_taxes_in_dollars,
            'total_with_taxes_in_dollars' => $cart->total_with_taxes_in_dollars,
            'items_count' => $cart->items_count,
        ],
    ];
}
```

| Pros                                               | Cons                                        |
| -------------------------------------------------- | ------------------------------------------- |
| ✅ Explicit - see exactly what's returned          | ❌ More verbose                             |
| ✅ Different endpoints can return different fields | ❌ Potential duplication across controllers |
| ✅ No side effects on queues/logs                  |                                             |
| ✅ Consistent with "remove `$appends`" strategy    |                                             |

---

**Option C: API Resource (Recommended)**

```php
// app/Http/Resources/CartItemResource.php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'price_in_dollars' => $this->price_in_dollars,
            'total_in_dollars' => $this->total_in_dollars,
            'quantity' => $this->quantity,
            'image_url' => $this->image_url,
            'formatted_variation' => $this->formatted_variation,
            'has_discount' => $this->has_discount,
            'discounted_price_in_dollars' => $this->discounted_price_in_dollars,
        ];
    }
}

// app/Http/Controllers/CartController.php
public function show(Request $request)
{
    $cart = Cart::byUICartId($request->input('id'))
        ->with(['items.purchasable'])
        ->firstOrFail();

    return [
        'ui_cart_id' => $cart->ui_cart_id,
        'items' => CartItemResource::collection($cart->items),
        'cart_aggregation' => [
            'total_without_taxes_in_dollars' => $cart->total_without_taxes_in_dollars,
            'total_with_taxes_in_dollars' => $cart->total_with_taxes_in_dollars,
            'items_count' => $cart->items_count,
        ],
    ];
}
```

| Pros                                          | Cons                           |
| --------------------------------------------- | ------------------------------ |
| ✅ Laravel's idiomatic pattern                | ❌ Additional file to maintain |
| ✅ Centralized, reusable                      |                                |
| ✅ Different resources for different contexts |                                |
| ✅ No side effects on model serialization     |                                |
| ✅ Testable in isolation                      |                                |
| ✅ Can add metadata, conditional fields       |                                |

---

**Recommendation:** Option C (API Resource) is preferred because it centralizes mapping without affecting model serialization in other contexts. However, Option A (`toArray()` override) is acceptable for CartItem since:

- CartItem is primarily used in cart API responses
- Carts are ephemeral and unlikely to be serialized in queues
- Simpler implementation (no new file)

Choose based on your team's preference and project complexity.

**Benefits of Hybrid Strategy:**

- ✅ No broken Vue components (controllers already map fields explicitly)
- ✅ No surprise N+1 queries
- ✅ Explicit control over what data is sent to frontend
- ✅ Minimal code changes
- ✅ Accessors still available when needed (just call them explicitly)

---

### 2. Nested Relationship Eager Loading in Transformers

**Severity:** 🟡 Optimization (Automatic eager loading handles simple relationships)
**Impact:** Fewer queries per product list
**Memory Impact:** Low

**Locations:**

- `app/CMS/ProductsTransformable.php:13`
- `app/CMS/FeaturedProductTransformable.php`
- `app/CMS/CollectionsTransformable.php`

**Current State:**

The application has `Model::automaticallyEagerLoadRelationships()` enabled in `AppServiceProvider.php:44`, which prevents N+1 queries on simple relationships automatically.

```php
// This is NOT an N+1 problem anymore thanks to automatic eager loading:
$products = Product::whereIn('id', $productsIds)->get();

foreach ($products as $product) {
    $product->taxes;      // Auto-eager loaded (1 query for all products)
    $product->discounts;  // Auto-eager loaded (1 query for all products)
    $product->variants;   // Auto-eager loaded (1 query for all products)
}
```

**What Automatic Eager Loading Does NOT Handle:**

1. **Nested relationships** - accessing `$variant->taxes` inside a loop still causes N+1
2. **Accessor-triggered queries** - like `has_discounts` which calls `validDiscounts()` internally

**When Manual `with()` Is Still Beneficial:**

```php
// NESTED RELATIONSHIPS - still need explicit eager loading
$products = Product::whereIn('id', $productsIds)
    ->with(['variants.taxes', 'variants.discounts'])  // Nested!
    ->get();

// Now $variant->taxes won't cause N+1
foreach ($products as $product) {
    foreach ($product->variants as $variant) {
        $variant->taxes;  // Would be N+1 without explicit with()
    }
}
```

**Recommended Fix for ProductsTransformable:**

```php
// No manual eager loading needed - automatic eager loading handles simple relationships
// Note: ProductVariant::taxes is an Attribute (accessor), not a relationship, so it cannot be eager loaded
$products = Product::whereIn('id', $productsIds)->get();
```

**Note:** Simple relationships (`taxes`, `discounts`, `media`, `variants`) are handled automatically by `Model::automaticallyEagerLoadRelationships()`. Only nested relationships need explicit `with()`, but only if they are actual relationships - not accessors/attributes.

---

### 3. Uncached Queries in HandleInertiaRequests Middleware

**Severity:** 🔴 Critical
**Impact:** 5 queries on every request
**Memory Impact:** Medium

**Location:** `app/Http/Middleware/HandleInertiaRequests.php:42-47`

**Problem:**

```php
public function share(Request $request): array
{
    $mainMenu = Menu::byName('main');        // Query 1
    $footerMenu = Menu::byName('footer');    // Query 2
    $legalMenu = Menu::byName('legal');      // Query 3

    $company = app(CompanySettings::class)->toArray();    // Query 4
    $storeFront = app(StorefrontSettings::class)->toArray(); // Query 5
    // ...
}
```

These 5 queries execute on **every single page load**, regardless of whether the data changes.

**Recommended Fix:**

```php
public function share(Request $request): array
{
    $mainMenu = Cache::remember('menu.main', now()->addHour(),
        fn() => Menu::byName('main')
    );
    $footerMenu = Cache::remember('menu.footer', now()->addHour(),
        fn() => Menu::byName('footer')
    );
    $legalMenu = Cache::remember('menu.legal', now()->addHour(),
        fn() => Menu::byName('legal')
    );

    $company = Cache::remember('settings.company', now()->addHour(),
        fn() => app(CompanySettings::class)->toArray()
    );
    $storeFront = Cache::remember('settings.storefront', now()->addHour(),
        fn() => app(StorefrontSettings::class)->toArray()
    );
    // ...
}
```

**Cache Invalidation Strategy:**

Create an observer to clear cache when menus/settings are updated:

```php
// app/Observers/MenuObserver.php
public function updated(Menu $menu): void
{
    Cache::forget('menu.' . $menu->slug);
}

// app/Observers/SettingsObserver.php
public function updated($settings): void
{
    Cache::forget('settings.' . $settings::group());
}
```

---

### 4. ProductPageController Nested Relationships

**Severity:** 🟡 Optimization
**Impact:** 2-3 extra queries for nested relationships
**Memory Impact:** Low

**Location:** `app/Http/Controllers/ProductPageController.php:22`

**Current State:**

The product comes from route model binding. Automatic eager loading handles simple relationships, but **nested relationships** still need explicit loading:

```php
public function __invoke(Product $product)
{
    // Simple relationships - handled by automatic eager loading:
    $product->variants;      // OK - auto eager loaded
    $product->taxes;         // OK - auto eager loaded
    $product->discounts;     // OK - auto eager loaded

    // NESTED RELATIONSHIPS - still cause N+1:
    foreach ($product->variants as $variant) {
        $variant->taxes;     // N+1! Each variant triggers a query
    }
}
```

**Recommended Fix:**

```php
public function __invoke(Product $product)
{
    // Load nested relationships explicitly
    $product->loadMissing([
        'variants.taxes',
        'variants.discounts',
    ]);

    // ...
}
```

**Note:** Simple relationships (`taxes`, `discounts`, `media`) are handled automatically. Only nested relationships like `variants.taxes` need explicit loading.

---

## Important Issues (Medium Impact)

### 5. Filament Navigation Badge Count Queries

**Severity:** 🟠 Important
**Impact:** 10+ queries on every admin panel page load
**Memory Impact:** Medium

**Location:** `app/Filament/Resources/Products/ProductResource.php:52`

**Problem:**

Each Filament resource can define a navigation badge. The application has 11 resources:

```php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::count(); // Query executed for each resource
}
```

With 11 resources, this is 11 count queries on every admin page load.

**Recommended Fix:**

Option A - Cache the counts:

```php
public static function getNavigationBadge(): ?string
{
    return Cache::remember(
        'products.count',
        now()->addMinutes(5),
        fn() => static::getModel()::count()
    );
}
```

Option B - Disable badges for less critical resources:

```php
public static function getNavigationBadge(): ?string
{
    return null; // Remove badge entirely
}
```

Option C - Use lazy badge evaluation (Filament v4):

```php
public static function getNavigationBadge(): ?string
{
    return static::getModel()::cachedCount(); // Use a cached count method
}
```

---

### 6. ProductVariant Taxes Accessor N+1

**Severity:** 🟠 Important
**Impact:** 1 query per variant
**Memory Impact:** Medium

**Location:** `app/Models/ProductVariant.php:59-64`

**Problem:**

```php
public function taxes(): Attribute
{
    return Attribute::make(
        get: fn () => $this->product->taxes
    );
}
```

This accessor accesses the parent product relationship without eager loading, causing N+1 when variants are listed.

**Recommended Fix:**

Option A - Define a proper relationship:

```php
public function taxes()
{
    return $this->belongsTo(Product::class)->with('taxes');
}

// Access via: $variant->product->taxes
```

Option B - Eager load product when fetching variants:

```php
// In queries
ProductVariant::with('product.taxes')->get();
```

---

### 7. Related Products Nested Relationships

**Severity:** 🟡 Optimization
**Impact:** Queries for nested relationships on related products
**Memory Impact:** Low

**Location:** `app/Models/Product.php:197-208`

**Current State:**

Automatic eager loading handles simple relationships on the returned products. However, if you access nested relationships (like `variant->taxes`) on related products, that would still be N+1.

```php
public function relatedProducts(): ?EloquentCollection
{
    $collections = $this->productCollections->pluck('id')->toArray();

    if (count($collections) > 0) {
        return Product::published()
            ->whereHas('productCollections', function ($query) use ($collections) {
                $query->whereIn('product_collections.id', $collections);
            })
            ->where('id', '!=', $this->id)
            ->get();
    }
    return null;
}
```

**Recommended Fix (if nested relationships are accessed):**

```php
public function relatedProducts(): ?EloquentCollection
{
    $collections = $this->productCollections->pluck('id')->toArray();

    if (count($collections) > 0) {
        return Product::published()
            ->with(['variants.taxes'])  // Nested relationships only
            ->whereHas('productCollections', function ($query) use ($collections) {
                $query->whereIn('product_collections.id', $collections);
            })
            ->where('id', '!=', $this->id)
            ->limit(4)
            ->get();
    }
    return null;
}
```

**Note:** Simple relationships (`taxes`, `discounts`, `media`, `variants`) are handled automatically. Only add `with()` if you access nested relationships in the view.

---

### 8. User Model Accessors Triggering Queries

**Severity:** 🟠 Important
**Impact:** 2 extra queries per user load
**Memory Impact:** Medium

**Location:** `app/Models/User.php:67, 99-110`

**Problem:**

```php
protected $appends = ['has_billing_info', 'has_shipping_info'];

public function hasBillingInfo(): Attribute
{
    return Attribute::make(
        get: fn () => $this->billingInfoEntry->count() > 0 // Triggers query
    );
}
```

**Recommended Fix:**

```php
// Remove $appends, use withCount instead when needed:
User::withCount(['billingInfoEntry as has_billing_info' => fn($q) => $q->where('is_main', true)])
    ->get();
```

---

## Optimization Opportunities

### 9. Missing Database Indexes

**Severity:** 🟡 Optimization
**Impact:** Query speed improvement
**Memory Impact:** N/A

**Recommended Indexes:**

```php
// Migration for adding indexes
Schema::table('products', function (Blueprint $table) {
    $table->index('status');
    $table->index('slug');
    $table->index('published_at');
    $table->index(['status', 'published_at']); // Composite for published scope
});

Schema::table('orders', function (Blueprint $table) {
    $table->index('paid_at');
    $table->index('user_id');
    $table->index(['user_id', 'paid_at']);
});

Schema::table('carts', function (Blueprint $table) {
    $table->index('paid_at');
    $table->index('user_id');
});

Schema::table('discounts', function (Blueprint $table) {
    $table->index('status');
    $table->index(['status', 'start_date', 'end_date']);
});

Schema::table('pages', function (Blueprint $table) {
    $table->index('slug');
    $table->index('status');
});
```

---

### 10. Queue Heavy Operations

**Severity:** 🟡 Optimization
**Impact:** Response time improvement
**Memory Impact:** Low

**Locations to verify:**

- `app/Listeners/SendOrderConfirmationNotification.php`
- `app/Listeners/ReduceProductStock.php`

**Verification Checklist:**

```php
// Listeners should implement ShouldQueue
class SendOrderConfirmationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderConfirmed $event): void
    {
        // Send email
    }
}
```

---

### 11. Enable Spatie Settings Caching

**Severity:** 🟡 Optimization
**Impact:** 2 queries per request saved
**Memory Impact:** Low

**Location:** `app/Providers/AppServiceProvider.php`

**Recommended Fix:**

```php
public function boot(): void
{
    // Enable settings caching in production
    if ($this->app->environment('production')) {
        \Spatie\LaravelSettings\Settings::cache();
    }
}
```

Note: Requires cache invalidation when settings are updated.

---

### 12. Use Inertia Deferred Props for Heavy Data

**Severity:** 🟡 Optimization
**Impact:** Initial page load speed
**Memory Impact:** Low

**Location:** `app/Http/Controllers/ProductsPageController.php`

**Current Implementation:**

```php
$this->extendedData = [
    'products' => fn () => $products,
];
```

**Recommended Fix:**

```php
use Inertia\Inertia;

return Inertia::render('Products', [
    'products' => Inertia::defer(fn () => $products),
]);
```

This defers the heavy product data load until after the initial page render.

---

### 13. Consider Redis for Cache/Queue

**Severity:** 🟡 Optimization
**Impact:** Cache/Queue performance
**Memory Impact:** N/A

**Current State:**

- Cache: Database driver
- Queue: Database driver

**Recommendation:**

For Laravel Cloud, consider using Redis if available:

- Lower latency for cache operations
- Better queue performance
- Reduced database load

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

---

## Summary Table

| #   | Issue                                      | Severity        | Queries Saved      | Memory Impact | Priority |
| --- | ------------------------------------------ | --------------- | ------------------ | ------------- | -------- |
| 1   | N+1 on `$appends` accessors (Hybrid fix)   | 🔴 Critical     | 3-5 per model      | High          | 1        |
| 2   | Nested relationships in transformers       | 🟡 Optimization | 1-2 per product    | Low           | 9        |
| 3   | Uncached middleware queries                | 🔴 Critical     | 5 per request      | Medium        | 2        |
| 4   | ProductPageController nested relationships | 🟡 Optimization | 2-3 per page       | Low           | 10       |
| 5   | Filament navigation badges                 | 🟠 Important    | 10+ per admin page | Medium        | 3        |
| 6   | ProductVariant taxes accessor (nested)     | 🟠 Important    | 1 per variant      | Medium        | 4        |
| 7   | Related products nested relationships      | 🟡 Optimization | 1-2 per product    | Low           | 11       |
| 8   | User model accessors                       | 🟠 Important    | 2 per user         | Medium        | 5        |
| 9   | Missing database indexes                   | 🟡 Optimization | N/A                | Query speed   | 12       |
| 10  | Queue heavy operations                     | 🟡 Optimization | N/A                | Low           | 13       |
| 11  | Settings caching                           | 🟡 Optimization | 2 per request      | Low           | 14       |
| 12  | Inertia deferred props                     | 🟡 Optimization | N/A                | Low           | 15       |
| 13  | Redis for cache/queue                      | 🟡 Optimization | N/A                | N/A           | 16       |

**Note:** With `Model::automaticallyEagerLoadRelationships()` enabled, simple relationships are handled automatically. Only nested relationships (e.g., `variants.taxes`) and accessor-triggered queries need manual intervention.

---

## Hybrid `$appends` Strategy Summary

### Models - Remove `$appends`

| Model          | File                            | Action            |
| -------------- | ------------------------------- | ----------------- |
| Product        | `app/Models/Product.php`        | Remove `$appends` |
| ProductVariant | `app/Models/ProductVariant.php` | Remove `$appends` |
| User           | `app/Models/User.php`           | Remove `$appends` |
| Order          | `app/Models/Order.php`          | Remove `$appends` |

### Models - Keep Minimal `$appends`

| Model    | File                      | Keep These Fields                                                              |
| -------- | ------------------------- | ------------------------------------------------------------------------------ |
| Cart     | `app/Models/Cart.php`     | `total_without_taxes_in_dollars`, `total_with_taxes_in_dollars`, `items_count` |
| CartItem | `app/Models/CartItem.php` | `price_in_dollars`, `total_in_dollars`, `image_url`                            |

### Controllers - Already Using Manual Mapping ✅

| Controller             | File                                              | Status              |
| ---------------------- | ------------------------------------------------- | ------------------- |
| ProductsPageController | `app/Http/Controllers/ProductsPageController.php` | Already explicit ✅ |
| ProductPageController  | `app/Http/Controllers/ProductPageController.php`  | Already explicit ✅ |
| HomePageController     | `app/Http/Controllers/HomePageController.php`     | Uses transformers   |

### Controllers - Needs Update

| Controller     | File                                      | Action                           |
| -------------- | ----------------------------------------- | -------------------------------- |
| CartController | `app/Http/Controllers/CartController.php` | Add explicit mapping in `show()` |

---

## Why Vue Components Won't Break

The key insight is that **controllers already manually map fields to Vue**, so Vue components receive the same data structure regardless of `$appends`.

### Before (with `$appends`):

```php
// Model
protected $appends = ['price_in_dollars', 'has_discounts'];

// Controller
return [
    'price_in_dollars' => $product->price_in_dollars,  // Accessor fires (query)
    'has_discounts' => $product->has_discounts,        // Accessor fires (query)
];

// Vue receives: { price_in_dollars: 19.99, has_discounts: true }
```

### After (without `$appends`):

```php
// Model
// protected $appends = [];  // Removed

// Controller
return [
    'price_in_dollars' => $product->price_in_dollars,  // Accessor fires (query) - SAME
    'has_discounts' => $product->has_discounts,        // Accessor fires (query) - SAME
];

// Vue receives: { price_in_dollars: 19.99, has_discounts: true }  // SAME
```

### The Difference:

| Scenario                     | With `$appends`                  | Without `$appends`   |
| ---------------------------- | -------------------------------- | -------------------- |
| `$product->toArray()`        | Includes all appended fields     | No appended fields   |
| Controller explicit mapping  | Accessor called twice (wasteful) | Accessor called once |
| `$product->price_in_dollars` | Works                            | Works (identical)    |
| Vue component data           | Same structure                   | Same structure       |

**Key Point:** Accessors still exist and work exactly the same. They're just not automatically included in `toArray()`/`toJson()` anymore. Since controllers explicitly call the accessors they need, Vue components receive identical data.

---

## Implementation Priority

### Phase 1 - Critical (Do First)

1. **Implement Hybrid `$appends` Strategy:**
    - Remove `$appends` from `Product`, `ProductVariant`, `User`, `Order` models
    - Keep minimal `$appends` on `Cart`, `CartItem` (used in API responses)
    - Update `CartController::show()` to use explicit mapping
    - Verify all controllers already using manual mapping (they are)

2. **Cache menus and settings in HandleInertiaRequests middleware:**
    - Wrap `Menu::byName()` calls in `Cache::remember()`
    - Wrap settings in `Cache::remember()`
    - Create observers for cache invalidation

### Phase 2 - Important

3. **Cache Filament navigation badges:**
    - Add `Cache::remember()` to all `getNavigationBadge()` methods
    - Set 5-minute TTL

4. **Fix ProductVariant taxes accessor (nested relationship):**
    - Ensure `product.taxes` is eager loaded when fetching variants
    - Update queries to include `->with('product.taxes')` for nested access

5. **Fix User model accessors:**
    - Remove `$appends` from User model
    - Use `withCount()` where billing/shipping info counts are needed

### Phase 3 - Optimizations

6. **Add nested relationship eager loading to transformers:**
    - `ProductsTransformable` - add `->with(['variants.taxes'])` only if nested access is needed
    - `FeaturedProductTransformable` - same if applicable
    - **Note:** Simple relationships are handled automatically by `Model::automaticallyEagerLoadRelationships()`

7. **Add nested relationship eager loading to ProductPageController:**
    - Add `$product->loadMissing(['variants.taxes'])` only if variants' taxes are accessed

8. **Add nested relationship loading to related products:**
    - Update `Product::relatedProducts()` to include `->with(['variants.taxes'])` if needed
    - Add `->limit(4)` to constrain results

9. **Add database indexes:**
    - Create migration for all recommended indexes
    - Test query performance improvements

10. **Verify queue implementation:**
    - Confirm `SendOrderConfirmationNotification` implements `ShouldQueue`
    - Confirm `ReduceProductStock` implements `ShouldQueue`

11. **Enable settings caching:**
    - Add `\Spatie\LaravelSettings\Settings::cache()` in AppServiceProvider
    - Implement cache invalidation in settings observers

12. **Implement deferred props:**
    - Update `ProductsPageController` to use `Inertia::defer()`
    - Consider for other heavy data pages

13. **Evaluate Redis for cache/queue:**
    - Test Redis availability on Laravel Cloud
    - Update `.env` and config if available

---

## Automatic Eager Loading Note

With `Model::automaticallyEagerLoadRelationships()` enabled in `AppServiceProvider.php`, simple relationships are automatically eager loaded when first accessed. This means:

**You DON'T need manual `with()` for:**

```php
Product::with(['taxes', 'discounts', 'variants'])->get();
```

**You DO need manual `with()` for:**

```php
// Nested relationships
Product::with(['variants.taxes', 'variants.discounts'])->get();

// Relationships with constraints
Product::with(['variants' => fn($q) => $q->published()])->get();
```

---

## Testing Recommendations

After implementing each fix, verify with:

1. **Query Logging:**

```php
// In AppServiceProvider
DB::listen(function ($query) {
    logger($query->sql, $query->bindings);
});
```

2. **Debugbar/Laravel Telescope:** Monitor query count per page

3. **Load Testing:** Use Laravel Octane or load testing tools to verify memory usage

4. **Pest Tests:** Add performance assertions:

```php
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

- This document should be reviewed after each implementation phase
- Performance metrics should be captured before and after changes
- Consider implementing changes incrementally to isolate impact
- Monitor Laravel Cloud metrics dashboard for memory usage trends
