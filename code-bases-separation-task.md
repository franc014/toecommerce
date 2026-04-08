# E-commerce Codebase Separation Guide

## Overview

This guide details how to separate the Laravel e-commerce application into two distinct repositories:

- **Backend Repository** (`toecommerce/admin`): Contains the Filament admin dashboard as a reusable composer package
- **Storefront Repository** (`toecommerce/storefront`): The client-facing e-commerce application that consumes the backend package

**Key Principle**: Database, storage, and images remain unchanged. Both applications connect to the same database and use the same storage configuration.

---

## Phase 1: Create Core Package (`toecommerce/core`)

### 1.1 Package Structure

```
toecommerce-core/
├── src/
│   ├── Contracts/
│   │   ├── Purchasable.php
│   │   └── ContentTransformable.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── ProductVariant.php
│   │   ├── Category.php
│   │   ├── ProductCollection.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── User.php
│   │   ├── Tax.php
│   │   ├── Discount.php
│   │   ├── Page.php
│   │   ├── Section.php
│   │   ├── Menu.php
│   │   └── MenuItem.php
│   ├── Traits/
│   │   ├── Discountable.php
│   │   ├── Taxable.php
│   │   ├── MoneyFormat.php
│   │   ├── Publishable.php
│   │   └── HasProductVariation.php
│   ├── Casts/
│   │   └── Money.php
│   ├── Enums/
│   │   ├── ProductStatus.php
│   │   ├── DiscountStatus.php
│   │   └── OrderStatus.php
│   ├── CMS/
│   │   ├── ContentResolver.php
│   │   └── Transformers/
│   │       ├── ImageTransformable.php
│   │       ├── ProductsTransformable.php
│   │       ├── CollectionsTransformable.php
│   │       ├── FeaturedProductTransformable.php
│   │       └── RichTextTransformable.php
│   ├── Settings/
│   │   ├── StorefrontSettings.php
│   │   └── CompanySettings.php
│   └── CoreServiceProvider.php
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_products_table.php
│       ├── 0001_01_01_000001_create_categories_table.php
│       ├── 0001_01_01_000002_create_product_collections_table.php
│       ├── 0001_01_01_000003_create_product_variants_table.php
│       ├── 0001_01_01_000004_create_carts_table.php
│       ├── 0001_01_01_000005_create_cart_items_table.php
│       ├── 0001_01_01_000006_create_orders_table.php
│       ├── 0001_01_01_000007_create_order_items_table.php
│       ├── 0001_01_01_000008_create_taxes_table.php
│       ├── 0001_01_01_000009_create_discounts_table.php
│       ├── 0001_01_01_000010_create_pages_table.php
│       ├── 0001_01_01_000011_create_sections_table.php
│       ├── 0001_01_01_000012_create_menus_table.php
│       └── ... (all remaining migrations)
├── config/
│   └── toecommerce.php
├── tests/
│   ├── TestCase.php
│   ├── Unit/
│   │   ├── Models/
│   │   ├── Traits/
│   │   └── Services/
│   └── Feature/
│       ├── CartTest.php
│       ├── CheckoutTest.php
│       ├── ProductTest.php
│       └── OrderTest.php
└── composer.json
```

### 1.2 composer.json

```json
{
    "name": "toecommerce/core",
    "description": "Core domain logic for ToEcommerce platform",
    "type": "library",
    "license": "proprietary",
    "require": {
        "php": "^8.2",
        "illuminate/database": "^12.0",
        "illuminate/support": "^12.0",
        "illuminate/http": "^12.0",
        "spatie/laravel-permission": "^6.0",
        "spatie/laravel-settings": "^3.0",
        "spatie/laravel-tags": "^4.0"
    },
    "require-dev": {
        "orchestra/testbench": "^9.0",
        "pestphp/pest": "^4.0",
        "pestphp/pest-plugin-laravel": "^4.0"
    },
    "autoload": {
        "psr-4": {
            "ToEcommerce\\Core\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "ToEcommerce\\Core\\Tests\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": ["ToEcommerce\\Core\\CoreServiceProvider"]
        }
    },
    "scripts": {
        "test": "vendor/bin/pest"
    }
}
```

### 1.3 Core Models

Move all models from `app/Models/` to `src/Models/`. **Remove all Filament-specific traits and methods**.

**Example: Product.php**

```php
<?php

namespace ToEcommerce\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags;
use ToEcommerce\Core\Contracts\Purchasable;
use ToEcommerce\Core\Traits\Discountable;
use ToEcommerce\Core\Traits\MoneyFormat;
use ToEcommerce\Core\Traits\Publishable;
use ToEcommerce\Core\Traits\Taxable;
use ToEcommerce\Core\Casts\Money;

class Product extends Model implements Purchasable
{
    use HasFactory;
    use HasTags;
    use Discountable;
    use MoneyFormat;
    use Publishable;
    use Taxable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'sku',
        'stock_quantity',
        'track_stock',
        'is_active',
        'published_at',
        'meta_title',
        'meta_description',
        'featured',
        'user_id',
    ];

    protected $casts = [
        'price' => Money::class,
        'compare_at_price' => Money::class,
        'is_active' => 'boolean',
        'track_stock' => 'boolean',
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(ProductCollection::class, 'product_collection_product');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class);
    }

    public function discounts(): MorphToMany
    {
        return $this->morphToMany(Discount::class, 'discountable');
    }

    // Purchasable interface implementation
    public function getPrice(): int
    {
        return $this->price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isAvailable(): bool
    {
        if (!$this->isPublished()) {
            return false;
        }

        if ($this->track_stock && $this->stock_quantity <= 0) {
            return false;
        }

        return true;
    }

    public function getStockQuantity(): int
    {
        return $this->stock_quantity;
    }
}
```

### 1.4 CoreServiceProvider.php

```php
<?php

namespace ToEcommerce\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/toecommerce.php',
            'toecommerce'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/toecommerce.php' => config_path('toecommerce.php'),
            ], 'toecommerce-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'toecommerce-migrations');
        }
    }
}
```

---

## Phase 2: Create Admin Package (`toecommerce/admin`)

### 2.1 Package Structure

```
toecommerce-admin/
├── src/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   ├── ProductResource.php
│   │   │   ├── OrderResource.php
│   │   │   ├── UserResource.php
│   │   │   ├── ProductCollectionResource.php
│   │   │   ├── CategoryResource.php
│   │   │   ├── DiscountResource.php
│   │   │   ├── SectionResource.php
│   │   │   ├── PageResource.php
│   │   │   └── MenuResource.php
│   │   ├── Pages/
│   │   │   ├── Dashboard.php
│   │   │   └── Settings.php
│   │   └── AdminPanelProvider.php
│   ├── Payment/
│   │   ├── PayphoneGateway.php
│   │   ├── PayphonePayment.php
│   │   ├── PayphoneTransactionIdGenerator.php
│   │   └── TransactionIdGenerator.php
│   ├── Models/
│   │   └── (Extended models if needed)
│   └── AdminServiceProvider.php
├── resources/
│   └── css/
│       └── theme.css
├── tests/
│   ├── Unit/
│   └── Feature/
│       └── Filament/
│           ├── ProductResourceTest.php
│           ├── OrderResourceTest.php
│           └── ... (all admin functionality tests)
└── composer.json
```

### 2.2 composer.json

```json
{
    "name": "toecommerce/admin",
    "description": "Filament admin dashboard for ToEcommerce",
    "type": "library",
    "license": "proprietary",
    "require": {
        "php": "^8.2",
        "toecommerce/core": "^1.0",
        "filament/filament": "^4.0",
        "filament/spatie-laravel-media-library-plugin": "^4.0",
        "filament/spatie-laravel-settings-plugin": "^4.0",
        "filament/spatie-laravel-tags-plugin": "^4.0",
        "bezhansalleh/filament-shield": "^4.0",
        "spatie/laravel-media-library": "^11.0",
        "spatie/laravel-backup": "^9.0"
    },
    "require-dev": {
        "orchestra/testbench": "^9.0",
        "pestphp/pest": "^4.0",
        "pestphp/pest-plugin-laravel": "^4.0"
    },
    "autoload": {
        "psr-4": {
            "ToEcommerce\\Admin\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "ToEcommerce\\Admin\\Tests\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": ["ToEcommerce\\Admin\\AdminServiceProvider"]
        }
    },
    "scripts": {
        "test": "vendor/bin/pest"
    }
}
```

### 2.3 AdminServiceProvider.php

```php
<?php

namespace ToEcommerce\Admin;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any admin-specific bindings
    }

    public function boot(): void
    {
        // Filament configuration
        Filament::serving(function () {
            // Global Filament configuration
        });
    }
}
```

### 2.4 AdminPanelProvider.php

```php
<?php

namespace ToEcommerce\Admin\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ToEcommerce\Admin\Filament\Resources;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'ToEcommerce\\Admin\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'ToEcommerce\\Admin\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
```

### 2.5 Payment Gateway (Generic - in Admin Package)

Move payment utilities from `app/Utils/` to the admin package.

**PaymentGateway Interface** (in core):

```php
<?php

namespace ToEcommerce\Core\Contracts;

interface PaymentGateway
{
    public function initiatePayment(array $data): array;
    public function confirmPayment(string $transactionId): array;
    public function getPaymentStatus(string $transactionId): string;
}
```

**PayphoneGateway** (in admin):

```php
<?php

namespace ToEcommerce\Admin\Payment;

use ToEcommerce\Core\Contracts\PaymentGateway;

class PayphoneGateway implements PaymentGateway
{
    public function initiatePayment(array $data): array
    {
        // Implementation
    }

    public function confirmPayment(string $transactionId): array
    {
        // Implementation
    }

    public function getPaymentStatus(string $transactionId): string
    {
        // Implementation
    }
}
```

---

## Phase 3: Storefront Repository Setup

### 3.1 Updated Structure

```
toecommerce-storefront/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomePageController.php
│   │   │   ├── ProductsPageController.php
│   │   │   ├── ProductPageController.php
│   │   │   ├── CollectionsPageController.php
│   │   │   ├── CollectionPageController.php
│   │   │   ├── CartController.php
│   │   │   ├── CartItemController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── ContactPageController.php
│   │   │   ├── AboutPageController.php
│   │   │   ├── PageController.php
│   │   │   ├── TermsAndConditionsPageController.php
│   │   │   ├── PrivacyPolicyPageController.php
│   │   │   ├── UserInfoEntryController.php
│   │   │   └── OrderController.php
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/
│   │       ├── StoreUserInfoEntryRequest.php
│   │       └── UpdateUserInfoEntryRequest.php
│   └── Providers/
│       └── AppServiceProvider.php
├── resources/
│   ├── js/
│   │   ├── app.ts
│   │   ├── ssr.ts
│   │   ├── pages/
│   │   ├── components/
│   │   ├── stores/
│   │   ├── composables/
│   │   ├── types/
│   │   └── routes/
│   ├── css/
│   │   └── app.css
│   └── views/
│       └── app.blade.php
├── routes/
│   └── web.php
├── database/
│   ├── factories/          (Keep or use core factories)
│   ├── seeders/
│   └── migrations/         (Published from core)
├── tests/
│   ├── Feature/            (Integration tests)
│   └── Unit/
├── composer.json
└── package.json
```

### 3.2 composer.json

```json
{
    "name": "toecommerce/storefront",
    "description": "ToEcommerce storefront application",
    "type": "project",
    "require": {
        "php": "^8.2",
        "toecommerce/core": "^1.0",
        "toecommerce/admin": "^1.0",
        "inertiajs/inertia-laravel": "^2.0",
        "laravel/framework": "^12.0",
        "laravel/wayfinder": "^0.1.12",
        "spatie/schema-org": "^3.23",
        "spatie/laravel-permission": "^6.0",
        "tightenco/ziggy": "^2.4"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:your-org/toecommerce-core.git"
        },
        {
            "type": "vcs",
            "url": "git@github.com:your-org/toecommerce-admin.git"
        }
    ],
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "scripts": {
        "post-autoload-dump": ["Illuminate\\Foundation\\ComposerScripts::postAutoloadDump", "@php artisan package:discover --ansi"]
    }
}
```

### 3.3 routes/web.php (Keep all routes)

```php
<?php

use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionPageController;
use App\Http\Controllers\CollectionsPageController;
use App\Http\Controllers\ContactPageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrivacyPolicyPageController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\ProductsPageController;
use App\Http\Controllers\TermsAndConditionsPageController;
use App\Http\Controllers\UserInfoEntryController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', HomePageController::class)->name('storefront.home');
Route::get('/products', ProductsPageController::class)->name('storefront.products');
Route::get('/products/{product:slug}', ProductPageController::class)->name('storefront.product');
Route::get('/collections', CollectionsPageController::class)->name('storefront.collections');
Route::get('/collections/{collection:slug}', CollectionPageController::class)->name('storefront.collection');
Route::get('/about', AboutPageController::class)->name('storefront.about');
Route::get('/contact', ContactPageController::class)->name('storefront.contact');
Route::get('/terminos-y-condiciones', TermsAndConditionsPageController::class)->name('storefront.terms-and-conditions');
Route::get('/politica-de-privacidad', PrivacyPolicyPageController::class)->name('storefront.privacy-policy');

// Cart routes
Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create');
Route::post('/cart/show', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/items/addOrUpdate', [CartItemController::class, 'addOrUpdate'])->name('cart.items.addOrUpdate');
Route::post('/cart/items/remove', [CartItemController::class, 'remove'])->name('cart.items.remove');
Route::post('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');

// Contact form
Route::post('/contact', [ContactPageController::class, 'sendMessage'])
    ->middleware([HandlePrecognitiveRequests::class])
    ->name('storefront.send-message');

// Login route - redirect to Filament customer panel
Route::get('/login', function () {
    return Inertia::location('/customer/login');
})->name('login');

// Payment confirmation
Route::get('/payments/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');

// Authenticated routes
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/checkout', CheckoutController::class)->name('storefront.checkout');
    Route::post('/orders/cancel', [OrderController::class, 'cancelOrder'])->name('storefront.orders.cancel');
    Route::post('/user-info', [UserInfoEntryController::class, 'store'])->middleware(HandlePrecognitiveRequests::class)->name('storefront.user-info-entry.store');
    Route::put('/user-info/{id}', [UserInfoEntryController::class, 'update'])->middleware(HandlePrecognitiveRequests::class)->name('storefront.user-info-entry.update');
    Route::post('/shipping-info/use-billing', [UserInfoEntryController::class, 'useBillingAsShipping'])->name('storefront.user-info-entry.use-billing-as-shipping');
});
```

### 3.4 Updated Controllers

Controllers should use core models:

```php
<?php

namespace App\Http\Controllers;

use ToEcommerce\Core\Models\Product;
use ToEcommerce\Core\Models\ProductCollection;
use ToEcommerce\Core\CMS\ContentResolver;
use Inertia\Inertia;
use Inertia\Response;

class HomePageController extends Controller
{
    public function __construct(
        private ContentResolver $contentResolver
    ) {}

    public function __invoke(): Response
    {
        $featuredProducts = Product::query()
            ->published()
            ->where('featured', true)
            ->limit(8)
            ->get();

        $sections = $this->contentResolver->resolve('home');

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'sections' => $sections,
        ]);
    }
}
```

### 3.5 Service Provider Registration

**bootstrap/providers.php:**

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    ToEcommerce\Core\CoreServiceProvider::class,
    ToEcommerce\Admin\AdminServiceProvider::class,
];
```

---

## Phase 4: Database Strategy

### 4.1 Migration Publishing

Core package provides all migrations. Storefront publishes them once:

```bash
# In storefront repository
composer require toecommerce/core

# Publish migrations
php artisan vendor:publish --tag=toecommerce-migrations

# Run migrations
php artisan migrate
```

### 4.2 Existing Database Compatibility

For existing databases, add checks in core migrations:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            // ... schema definition
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### 4.3 Database Configuration

Both storefront and admin connect to the **same database**. No changes needed to `.env` database configuration.

---

## Phase 5: Testing Strategy

### 5.1 Core Package Tests

Tests verify domain logic works correctly:

```php
<?php

namespace ToEcommerce\Core\Tests\Feature;

use ToEcommerce\Core\Models\Product;
use ToEcommerce\Core\Models\Cart;
use ToEcommerce\Core\Models\User;
use ToEcommerce\Core\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_product_to_cart()
    {
        $product = Product::factory()->create([
            'price' => 1000,
            'stock_quantity' => 10,
        ]);

        $cart = Cart::create();
        $cart->addItem($product, 2);

        $this->assertEquals(2000, $cart->total);
        $this->assertEquals(1, $cart->items->count());
    }

    public function test_cart_calculates_discounts_correctly()
    {
        $product = Product::factory()->create(['price' => 1000]);
        $product->discounts()->create([
            'type' => 'percentage',
            'value' => 10,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $cart = Cart::create();
        $cart->addItem($product, 1);

        $this->assertEquals(900, $cart->discounted_total);
    }
}
```

### 5.2 Admin Package Tests

Tests verify Filament functionality:

```php
<?php

namespace ToEcommerce\Admin\Tests\Feature;

use ToEcommerce\Admin\Filament\Resources\ProductResource;
use ToEcommerce\Core\Models\Product;
use ToEcommerce\Admin\Tests\TestCase;
use Filament\Actions\DeleteAction;

class ProductResourceTest extends TestCase
{
    public function test_can_list_products()
    {
        $products = Product::factory()->count(5)->create();

        livewire(ProductResource\Pages\ListProducts::class)
            ->assertCanSeeTableRecords($products);
    }

    public function test_can_create_product()
    {
        livewire(ProductResource\Pages\CreateProduct::class)
            ->fillForm([
                'name' => 'Test Product',
                'slug' => 'test-product',
                'price' => 1000,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 1000,
        ]);
    }
}
```

### 5.3 TestCase Base Classes

**Core TestCase:**

```php
<?php

namespace ToEcommerce\Core\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use ToEcommerce\Core\CoreServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ToEcommerce\\Core\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            CoreServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        // Run migrations
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }
}
```

---

## Phase 6: Configuration

### 6.1 Core Configuration

**config/toecommerce.php:**

```php
<?php

return [
    'currency' => env('TOECOMMERCE_CURRENCY', 'USD'),
    'currency_symbol' => env('TOECOMMERCE_CURRENCY_SYMBOL', '$'),

    'products' => [
        'per_page' => env('TOECOMMERCE_PRODUCTS_PER_PAGE', 12),
        'image_disk' => env('TOECOMMERCE_IMAGE_DISK', 'public'),
    ],

    'orders' => [
        'number_prefix' => env('TOECOMMERCE_ORDER_PREFIX', 'ORD-'),
        'number_padding' => 6,
    ],

    'payment' => [
        'gateway' => env('TOECOMMERCE_PAYMENT_GATEWAY', 'payphone'),
        'payphone_token' => env('PAYPHONE_TOKEN'),
        'payphone_store_id' => env('PAYPHONE_STORE_ID'),
    ],
];
```

### 6.2 Storefront .env

```env
# Database (same for both applications)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toecommerce
DB_USERNAME=root
DB_PASSWORD=

# ToEcommerce Settings
TOECOMMERCE_CURRENCY=USD
TOECOMMERCE_CURRENCY_SYMBOL=$
TOECOMMERCE_PRODUCTS_PER_PAGE=12
TOECOMMERCE_IMAGE_DISK=s3
TOECOMMERCE_PAYMENT_GATEWAY=payphone

# Payment Gateway
PAYPHONE_TOKEN=your_token_here
PAYPHONE_STORE_ID=your_store_id

# Storage (for images - unchanged)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

---

## Phase 7: Image/Storage Handling

**No changes needed!** The current image system works as-is:

1. **Admin uploads images** via Filament using Spatie Media Library
2. **Images stored on S3** (or configured disk)
3. **Storefront reads image URLs** directly from the database
4. **Both applications use the same storage configuration**

Core models access media through relationships if needed, or storefront can use the `getFirstMediaUrl()` method if Spatie package is available.

---

## Phase 8: Implementation Steps

### Week 1: Setup Core Package

1. Create `toecommerce/core` repository
2. Move all models to core package
3. Move traits, casts, enums, CMS transformers
4. Move settings classes
5. Move all migrations
6. Set up CoreServiceProvider
7. Write core tests

### Week 2: Setup Admin Package

1. Create `toecommerce/admin` repository
2. Move all Filament resources
3. Move payment gateway classes
4. Create AdminServiceProvider
5. Update Filament resources to use core models
6. Write admin tests

### Week 3: Migrate Storefront

1. Update storefront composer.json
2. Install core and admin packages
3. Update controllers to use core models
4. Update service providers
5. Publish migrations
6. Verify all routes work
7. Run storefront tests

### Week 4: Testing & Refinement

1. Run full test suite in all packages
2. Test image uploads and display
3. Test checkout flow end-to-end
4. Test order management
5. Verify settings work
6. Performance testing
7. Documentation updates

---

## Phase 9: Deployment

### 9.1 Package Versioning

Use semantic versioning:

- Core: `1.0.0`, `1.1.0`, `2.0.0`
- Admin: `1.0.0` (depends on core `^1.0`)

### 9.2 Private Package Hosting

**Option A: GitHub Private Repository**

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:your-org/toecommerce-core.git"
        },
        {
            "type": "vcs",
            "url": "git@github.com:your-org/toecommerce-admin.git"
        }
    ],
    "require": {
        "toecommerce/core": "^1.0",
        "toecommerce/admin": "^1.0"
    }
}
```

**Option B: Packagist Private (packagist.com)**

Configure authentication in `auth.json`:

```json
{
    "http-basic": {
        "repo.packagist.com": {
            "username": "token",
            "password": "your_token_here"
        }
    }
}
```

### 9.3 CI/CD Example

**.github/workflows/tests.yml for core package:**

```yaml
name: Tests

on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest
        strategy:
            matrix:
                php: [8.2, 8.3]

        steps:
            - uses: actions/checkout@v3

            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: ${{ matrix.php }}

            - name: Install dependencies
              run: composer install --no-interaction

            - name: Run tests
              run: vendor/bin/pest
```

---

## Key Points

1. **Database**: Both apps share the same database - no data synchronization needed
2. **Images**: Current image system works unchanged - admin uploads, storefront displays
3. **Routes**: All storefront routes stay in storefront repository
4. **Login**: Storefront login redirects to `/customer/login` (Filament customer panel in admin package)
5. **Tests**: Backend package tests verify functionality that storefront depends on
6. **CMS**: Transformation logic moved to core, accessible by both packages
7. **Payment**: Generic gateway interface in core, implementation in admin

---

## Migration Checklist

### Before Separation

- [ ] Write tests for all critical functionality
- [ ] Document current database schema
- [ ] Create backup of production database
- [ ] Identify all shared components

### During Separation

- [ ] Create core package with all models
- [ ] Refactor models to remove framework-specific code
- [ ] Create admin package extending core models
- [ ] Update storefront to use core models
- [ ] Move migrations to appropriate packages
- [ ] Test data integrity after model changes

### After Separation

- [ ] Run full test suite
- [ ] Verify image uploads work in admin
- [ ] Verify images display in storefront
- [ ] Test checkout flow end-to-end
- [ ] Test order management in admin
- [ ] Verify settings work in both
- [ ] Performance testing
- [ ] Documentation updates

---

## Common Pitfalls & Solutions

### Pitfall 1: Circular Dependencies

**Problem**: Core needs User, but User has admin traits.
**Solution**: Keep User in core with minimal fields, admin extends or uses roles.

### Pitfall 2: Migration Conflicts

**Problem**: Both packages have migrations with same timestamps.
**Solution**: Use different date prefixes or load migrations from core only.

### Pitfall 3: Configuration Conflicts

**Problem**: Config keys overlap.
**Solution**: Namespace all configs: `toecommerce.products.per_page`.

### Pitfall 4: Route Conflicts

**Problem**: Admin and storefront both define `/products`.
**Solution**: Admin uses `/admin/products`, storefront keeps `/products`.

### Pitfall 5: Asset Build Complexity

**Problem**: Two different build processes.
**Solution**: Admin uses Filament's built-in assets, storefront uses custom Vite build.

---

## Timeline Estimation

- **Phase 1** (Week 1): Core package structure and models
- **Phase 2** (Week 2): Admin package with Filament resources
- **Phase 3** (Week 3): Storefront migration and testing
- **Phase 4** (Week 4): Testing and refinement

**Total Estimated Time**: 4-6 weeks for a team of 2-3 developers

---

## Conclusion

This separation provides:

1. **Reusability**: Install backend in any Laravel app
2. **Maintainability**: Clear boundaries between concerns
3. **Scalability**: Storefront can be customized per client
4. **Consistency**: Shared domain logic prevents drift

The key is maintaining the shared domain in `toecommerce/core` while allowing both admin and storefront to extend as needed. Images and database remain unchanged, ensuring a smooth transition.
