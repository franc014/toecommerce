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

#### Step 3: Add eager loading before accessing relationships

Accessors that depend on relationships will still work, but require eager loading:

```php
// Before calling accessors, eager load the relationships:
Product::with(['taxes', 'discounts', 'variants'])->get();

// Now accessors work without N+1:
$product->price_with_taxes_in_dollars;  // Uses loaded $this->taxes
$product->has_discounts;                 // Uses loaded $this->discounts
```

#### Step 4: Update CartController to use explicit mapping

```php
// app/Http/Controllers/CartController.php
public function show(Request $request)
{
    $cart = Cart::byUICartId($request->input('id'))
        ->with(['items.purchasable'])  // Eager load
        ->firstOrFail();

    return [
        'ui_cart_id' => $cart->ui_cart_id,
        'items' => $cart->items->map(fn($item) => [
            'id' => $item->id,
            'title' => $item->title,
            'price_in_dollars' => $item->price_in_dollars,
            'total_in_dollars' => $item->total_in_dollars,
            'quantity' => $item->quantity,
            'image_url' => $item->image_url,
            // ... map all needed fields
        ]),
        'cart_aggregation' => [
            'total_without_taxes_in_dollars' => $cart->total_without_taxes_in_dollars,
            'total_with_taxes_in_dollars' => $cart->total_with_taxes_in_dollars,
            'items_count' => $cart->items_count,
        ],
    ];
}
```

**Benefits of Hybrid Strategy:**

- ✅ No broken Vue components (controllers already map fields explicitly)
- ✅ No surprise N+1 queries
- ✅ Explicit control over what data is sent to frontend
- ✅ Minimal code changes
- ✅ Accessors still available when needed (just call them explicitly)

---

### 2. Missing Eager Loading in Content Transformers

**Severity:** 🔴 Critical
**Impact:** 4+ extra queries per product
**Memory Impact:** High

**Locations:**

- `app/CMS/ProductsTransformable.php:13`
- `app/CMS/FeaturedProductTransformable.php`
- `app/CMS/CollectionsTransformable.php`

**Problem:**

```php
// ProductsTransformable.php
$products = Product::whereIn('id', $productsIds)->get();

foreach ($products as $key => $product) {
    // Each of these triggers a query:
    $product->price_with_taxes_in_dollars;  // taxes relationship
    $product->hasPublishedVariants();        // variants query
    $product->variants;                      // variants query
    $product->isDroppingStock();             // settings query
    $product->has_discounts;                 // discounts query
    $product->discountsForList;              // discounts query
}
```

**Recommended Fix:**

```php
$products = Product::whereIn('id', $productsIds)
    ->with(['taxes', 'discounts', 'media', 'variants'])
    ->get();
```

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

### 4. ProductPageController Missing Eager Loading

**Severity:** 🔴 Critical
**Impact:** 5+ extra queries per product page view
**Memory Impact:** High

**Location:** `app/Http/Controllers/ProductPageController.php:22`

**Problem:**

The product comes from route model binding without any eager loading:

```php
public function __invoke(Product $product)
{
    // $product loaded via route binding, no relationships
    // Later access triggers N+1:
    $product->variants;      // Query
    $product->taxes;         // Query
    $product->discounts;     // Query
    $product->productImages(); // Query

    $relatedProducts = $product->relatedProducts(); // N+1 in related products too
}
```

**Recommended Fix:**

```php
public function __invoke(Product $product)
{
    $product->load([
        'taxes',
        'discounts',
        'variants.taxes',
        'variants.discounts',
        'media'
    ]);

    // Or use ->loadMissing() to only load what's not already loaded
    $product->loadMissing(['taxes', 'discounts', 'variants', 'media']);

    // ...
}
```

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

### 7. Related Products N+1

**Severity:** 🟠 Important
**Impact:** 4+ queries per related product
**Memory Impact:** Medium

**Location:** `app/Models/Product.php:197-208`

**Problem:**

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
            ->get(); // No eager loading!
    }
    return null;
}
```

**Recommended Fix:**

```php
public function relatedProducts(): ?EloquentCollection
{
    $collections = $this->productCollections->pluck('id')->toArray();

    if (count($collections) > 0) {
        return Product::published()
            ->with(['taxes', 'discounts', 'media', 'variants'])
            ->whereHas('productCollections', function ($query) use ($collections) {
                $query->whereIn('product_collections.id', $collections);
            })
            ->where('id', '!=', $this->id)
            ->limit(4) // Limit results
            ->get();
    }
    return null;
}
```

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

| #   | Issue                                    | Severity        | Queries Saved         | Memory Impact | Priority |
| --- | ---------------------------------------- | --------------- | --------------------- | ------------- | -------- |
| 1   | N+1 on `$appends` accessors (Hybrid fix) | 🔴 Critical     | 3-5 per model         | High          | 1        |
| 2   | Missing eager loading in transformers    | 🔴 Critical     | 4 per product         | High          | 2        |
| 3   | Uncached middleware queries              | 🔴 Critical     | 5 per request         | Medium        | 3        |
| 4   | ProductPageController missing eager load | 🔴 Critical     | 5+ per page           | High          | 4        |
| 5   | Filament navigation badges               | 🟠 Important    | 10+ per admin page    | Medium        | 5        |
| 6   | ProductVariant taxes accessor            | 🟠 Important    | 1 per variant         | Medium        | 6        |
| 7   | Related products N+1                     | 🟠 Important    | 4 per related product | Medium        | 7        |
| 8   | User model accessors                     | 🟠 Important    | 2 per user            | Medium        | 8        |
| 9   | Missing database indexes                 | 🟡 Optimization | N/A                   | Query speed   | 9        |
| 10  | Queue heavy operations                   | 🟡 Optimization | N/A                   | Low           | 10       |
| 11  | Settings caching                         | 🟡 Optimization | 2 per request         | Low           | 11       |
| 12  | Inertia deferred props                   | 🟡 Optimization | N/A                   | Low           | 12       |
| 13  | Redis for cache/queue                    | 🟡 Optimization | N/A                   | N/A           | 13       |

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

2. **Add eager loading to all CMS transformers:**
    - `ProductsTransformable` - add `->with(['taxes', 'discounts', 'media', 'variants'])`
    - `FeaturedProductTransformable` - same eager loading
    - `CollectionsTransformable` - verify eager loading

3. **Cache menus and settings in HandleInertiaRequests middleware:**
    - Wrap `Menu::byName()` calls in `Cache::remember()`
    - Wrap settings in `Cache::remember()`
    - Create observers for cache invalidation

4. **Add eager loading to ProductPageController:**
    - Add `$product->load(['taxes', 'discounts', 'variants.taxes', 'variants.discounts', 'media'])`
    - Update `relatedProducts()` to include eager loading

### Phase 2 - Important

5. **Cache Filament navigation badges:**
    - Add `Cache::remember()` to all `getNavigationBadge()` methods
    - Set 5-minute TTL

6. **Fix ProductVariant taxes accessor:**
    - Ensure `product.taxes` is eager loaded when fetching variants
    - Update queries to include `->with('product.taxes')`

7. **Add eager loading to related products:**
    - Update `Product::relatedProducts()` method
    - Add `->with(['taxes', 'discounts', 'media', 'variants'])`
    - Add `->limit(4)` to constrain results

8. **Fix User model accessors:**
    - Remove `$appends` from User model
    - Use `withCount()` where billing/shipping info counts are needed

### Phase 3 - Optimizations

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
