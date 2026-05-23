# ToEcommerce Codebase Guidelines

> Auto-generated from codebase analysis on 2026-05-22
> Based on Laravel 13 + Vue 3 + Inertia.js v3 + Filament v5

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Backend (Laravel)](#backend-laravel)
    - [Ecommerce Core Package](#ecommerce-core-package-franc014toecommerce-core)
3. [Frontend (Vue + Inertia)](#frontend-vue--inertia)
4. [Testing (Pest PHP)](#testing-pest-php)
5. [Styling (Tailwind CSS v4)](#styling-tailwind-css-v4)
6. [Key Patterns & Conventions](#key-patterns--conventions)

---

## Architecture Overview

### Tech Stack

| Layer                 | Technology                      | Version |
| --------------------- | ------------------------------- | ------- |
| Backend               | Laravel                         | 13.x    |
| Admin Panel           | Filament                        | 5.x     |
| Frontend Framework    | Vue                             | 3.5.x   |
| SSR Framework         | Inertia.js                      | 3.x     |
| Styling               | Tailwind CSS                    | 4.x     |
| Build Tool            | Vite                            | 7.x     |
| Testing               | Pest PHP                        | 4.x     |
| State Management      | Pinia                           | 3.x     |
| UI Components         | shadcn/vue + Reka UI            | 2.x     |
| Icons                 | Lucide Vue                      | -       |
| Toast Notifications   | vue-sonner                      | -       |
| Animations            | GSAP                            | -       |
| Backups               | spatie/laravel-backup           | 10.x    |
| Spam Protection       | spatie/laravel-honeypot         | 4.x     |
| Authorization         | bezhansalleh/filament-shield    | 4.x     |
| Schema.org Markup     | spatie/schema-org               | 3.x     |
| Debugging             | spatie/laravel-ray              | -       |
| **Ecommerce Core**    | **franc014/toecommerce-core**   | **dev-main** |

### Application Structure

```
toecommerce/
├── app/                          # Laravel application code
│   ├── CMS/                      # Content Management System
│   │   ├── ContentResolver.php   # Transforms section content
│   │   ├── ContentTransformable.php  # Transformer interface
│   │   ├── CollectionsTransformable.php
│   │   ├── FeatureTransformable.php
│   │   ├── FeaturedProductTransformable.php
│   │   ├── ImageTransformable.php
│   │   ├── ProductsTransformable.php
│   │   └── RichTextTransformable.php
│   ├── Filament/                 # CMS Filament resources (app-specific)
│   │   ├── Pages/
│   │   │   └── ManageCompanyInfo.php
│   │   └── Resources/            # CMS resources: Menus, Pages, Sections
│   ├── Http/
│   │   ├── Controllers/          # Page & API controllers
│   │   │   ├── {Name}PageController.php  # Extends PageController
│   │   │   ├── CartController.php
│   │   │   ├── PaymentController.php
│   │   │   └── ...
│   │   ├── Middleware/
│   │   └── Requests/             # Form request validation
│   ├── Models/                   # CMS models: Page, Section, Menu, MenuItem, Contact
│   ├── Settings/                 # App-level settings: CompanySettings
│   ├── Traits/                   # App-level traits: Metatags
│   └── Utils/                    # Utility classes
├── toecommerce-core/             # Ecommerce core package (symlinked as vendor)
│   └── src/
│       ├── Casts/
│       ├── Enums/
│       ├── Exceptions/
│       ├── Filament/
│       │   ├── Pages/            # ManageStorefront
│       │   └── Resources/        # 8 ecommerce resources
│       ├── Models/               # 13 ecommerce models
│       ├── Settings/             # StorefrontSettings
│       ├── Traits/
│       ├── ToecommerceCorePlugin.php
│       └── ToecommerceCoreServiceProvider.php
├── resources/
│   ├── css/
│   │   ├── app.css               # Tailwind CSS v4 entry point
│   │   ├── filament/admin/       # Filament admin theme
│   │   └── *.css                 # Component-specific styles (17 files)
│   └── js/
│       ├── pages/                # Inertia.js page components (10 pages)
│       ├── components/           # Vue components (212 total)
│       │   ├── ui/               # 26 shadcn/vue UI component sets (140 files)
│       │   ├── {feature}/        # Feature-specific components
│       │   └── *.vue             # Shared business components
│       ├── layouts/              # Layout components
│       ├── stores/               # Pinia stores (cart, cartDrawer)
│       ├── composables/          # Vue composables (useAppearance, useCartItemQuantity, useInitials)
│       ├── routes/               # Wayfinder generated route functions
│       ├── actions/              # Wayfinder generated controller action functions
│       ├── wayfinder/            # Wayfinder core utilities
│       ├── lib/                  # Utility libraries (cn(), codyhouse carousel/swipe)
│       └── types/                # TypeScript definitions
├── tests/
│   ├── Feature/                  # Feature tests (14 files)
│   ├── Unit/                     # Unit tests (17 files)
│   └── Pest.php                  # Pest configuration
└── routes/
    ├── web.php                   # Web routes
    └── console.php               # Console commands
```

---

## Backend (Laravel)

### Ecommerce Core Package (`franc014/toecommerce-core`)

The ecommerce domain logic — models, enums, traits, casts, settings, exceptions, Filament resources, and migrations — lives in a separate package at `../toecommerce-core` (symlinked into `vendor/franc014/toecommerce-core` via a path repository in `composer.json`).

#### Namespace

All package code uses the `JFA\ToecommerceCore\` namespace.

#### Directory Structure

```
toecommerce-core/
├── src/
│   ├── Casts/                          # Money cast
│   ├── Database/
│   │   └── Factories/                  # 13 model factories
│   ├── Enums/                          # 6 enums
│   ├── Exceptions/                     # 6 exception classes
│   ├── Filament/
│   │   ├── Pages/                      # ManageStorefront
│   │   └── Resources/                  # 8 ecommerce resources
│   │       ├── Categories/
│   │       ├── Discounts/
│   │       ├── Orders/
│   │       ├── ProductCollections/
│   │       ├── Products/
│   │       ├── ProductVariants/
│   │       ├── UserInfoEntries/
│   │       └── Users/
│   ├── Models/                         # 13 models + Purchasable interface
│   ├── Settings/                       # StorefrontSettings
│   ├── Traits/                         # 5 traits (Discountable, MoneyFormat, etc.)
│   ├── ToecommerceCorePlugin.php       # Filament plugin for resource/page discovery
│   └── ToecommerceCoreServiceProvider.php  # Service provider
├── database/
│   └── migrations/                     # 32 core ecommerce migrations
├── resources/
│   ├── lang/                           # Translation files (en/es)
│   └── views/                          # Blade views (HeroBlock)
└── composer.json
```

#### What's in the Package vs What Stays in the App

| Component | Package | App | Notes |
|---|---|---|---|
| Ecommerce Models (Product, Order, Cart, etc.) | ✅ | ❌ | 13 models |
| Ecommerce Enums (OrderStatus, ProductStatus, etc.) | ✅ | ❌ | 6 enums |
| Ecommerce Traits (Discountable, MoneyFormat, etc.) | ✅ | ❌ | 5 traits |
| Money Cast | ✅ | ❌ | |
| StorefrontSettings | ✅ | ❌ | |
| Ecommerce Exceptions (6 classes) | ✅ | ❌ | |
| Ecommerce Filament Resources (8) | ✅ | ❌ | Discovered via ToecommerceCorePlugin |
| Ecommerce Migrations (32) | ✅ | ✅ | Package provides them; app has already-run copies |
| Model Factories (13) | ✅ | ❌ | Registered via ServiceProvider |
| CMS Models (Page, Section, Menu, Contact) | ❌ | ✅ | App-specific |
| CMS Filament Resources (Pages, Sections, Menus) | ❌ | ✅ | Registered via direct discovery |
| CompanySettings | ❌ | ✅ | App-specific |
| Metatags trait | ❌ | ✅ | App-specific |
| Controllers | ❌ | ✅ | App-level HTTP layer |
| CMS Transformables | ❌ | ✅ | Content transformation pipeline |
| Exceptions/Mails (order notifications) | ❌ | ✅ | App-level: `App\Mail\OrderStatusChanged`, `App\Events\OrderConfirmed` |

#### Package Registration

1. **Service Provider**: `ToecommerceCoreServiceProvider` registers translations, views, migrations, and factory resolution. Auto-discovered by Laravel via package's `composer.json`.

2. **Filament Plugin**: `ToecommerceCorePlugin` discovers package resources and pages on whatever panel it's registered to:

    ```php
    // In AdminPanelProvider or CustomerPanelProvider
    ->plugins([
        ToecommerceCorePlugin::make(),
    ])
    ```

    The plugin is registered on both the **admin** (`/admin`) and **customer** (`/customer`) panels.

#### Factory Resolution

Package factories require explicit `$model` property to override Laravel's default `Factory::modelName()` resolver (which appends `App\`):

```php
class ProductFactory extends Factory
{
    protected $model = \JFA\ToecommerceCore\Models\Product::class;

    public function definition(): array { /* ... */ }
}
```

The `ToecommerceCoreServiceProvider` registers a custom factory name resolver via `Factory::guessFactoryNamesUsing()` so `JFA\ToecommerceCore\Models\Product` → `JFA\ToecommerceCore\Database\Factories\ProductFactory`.

#### Plugin Auto-Registration

The `ToecommerceCorePlugin` uses `discoverResources()` and `discoverPages()` to auto-register all package Filament resources and pages on the panel it's attached to:

```php
class ToecommerceCorePlugin implements Plugin
{
    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'JFA\\ToecommerceCore\\Filament\\Resources',
            )
            ->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'JFA\\ToecommerceCore\\Filament\\Pages',
            );
    }
}
```

#### Customer Panel Awareness

The `OrderResource` in the package has built-in customer-panel awareness:

```php
// Navigation badge shows user's own order count in customer panel
if (Filament::getCurrentPanel()->getId() === 'customer') {
    return static::getModel()::query()->where('user_id', auth()->user()->id)->count();
}

// Query scoped to user's own orders in customer panel
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    if (Filament::getCurrentPanel()->getId() === 'customer') {
        $query = $query->where('user_id', auth()->user()->id);
    }
    return $query;
}
```

#### Tailwind CSS Integration

Package Blade views containing Tailwind classes require `@source` in the Filament theme CSS:

```css
/* resources/css/filament/admin/theme.css */
@source '../../vendor/franc014/toecommerce-core/resources/views';
```

### Models

#### Naming Conventions

- **Models**: Singular, PascalCase (`Product`, `OrderItem`)
- **Traits**: PascalCase, descriptive (`MoneyFormat`, `Discountable`)
- **Interfaces**: PascalCase (`Purchasable`)
- **Enums**: PascalCase (`ProductStatus`, `StockControlModes`)

#### Model Structure Pattern

```php
<?php

namespace JFA\ToecommerceCore\Models;

use JFA\ToecommerceCore\Casts\Money;
use JFA\ToecommerceCore\Enums\ProductStatus;
use JFA\ToecommerceCore\Traits\Discountable;
use JFA\ToecommerceCore\Traits\MoneyFormat;
use JFA\ToecommerceCore\Traits\Publishable;
use JFA\ToecommerceCore\Traits\Taxable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

class Product extends Model implements HasMedia, HasRichContent, Purchasable
{
    use Discountable, HasFactory, HasTags, InteractsWithMedia,
        InteractsWithRichContent, MoneyFormat, Publishable, Taxable;

    // Use casts() method (Laravel 12 style) instead of $casts property
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'status' => ProductStatus::class,
            'price' => Money::class,  // Custom cast
            'variant_options' => 'array',
            'description' => 'array',
        ];
    }

    // Appended accessors
    protected $appends = [
        'price_in_dollars',
        'price_with_taxes_in_dollars',
        'has_discounts',
        'discounted_price_in_dollars'
    ];

    // Relationships
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeWithStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Business logic methods
    public function hasVariants(): bool
    {
        return $this->variants()->count() >= 1;
    }

    // Interface implementation (Purchasable)
    public function dataforCart(): array
    {
        return [
            'purchasable_id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'slug' => $this->slug,
            'image' => $this->main_image,
            'taxes' => json_encode($this->taxes->select(['name', 'percentage'])),
            'purchasable_type' => Product::class,
        ];
    }
}
```

#### Key Model Patterns

1. **Polymorphic Relations**: Use for cart items supporting multiple purchasable types

    ```php
    // CartItem model
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }
    ```

2. **Interface-based Design**: `Purchasable` interface for cart-eligible items

    ```php
    interface Purchasable
    {
        public function dataforCart(): array;
    }
    ```

3. **Trait-based Functionality**: Split functionality into focused traits
    - `Discountable` - Discount calculation logic
    - `Taxable` - Tax calculation logic
    - `Publishable` - Publication status management
    - `MoneyFormat` - Currency formatting accessors
    - `HasProductVariation` - Product variation helpers
    - `Metatags` - SEO meta tags generation

4. **Money Pattern**: Store values in cents (integers), convert to dollars via cast

    ```php
    // app/Casts/Money.php
    public function get($model, string $key, $value, array $attributes): int
    {
        return $value / 100; // Convert cents to dollars
    }

    public function set($model, string $key, $value, array $attributes): int
    {
        return $value * 100; // Convert dollars to cents
    }
    ```

5. **Rich Content**: Implement `HasRichContent` for Filament rich editor fields using `InteractsWithRichContent` trait

    ```php
    use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
    use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;

    class Product extends Model implements HasRichContent
    {
        use InteractsWithRichContent;

        public function setUpRichContent(): void
        {
            $this->registerRichContent('description');
        }
    }
    ```

6. **Laravel 13 Casts Method**: Always use `casts()` method instead of `$casts` property

7. **Accessor Pattern**: Use `Attribute::make()` for computed accessors (Laravel 13 style)

    ```php
    use Illuminate\Database\Eloquent\Casts\Attribute;

    public function productImagesForList(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->productImages()->take(2)->map(fn ($image) => $image->getFullUrl())
        );
    }
    ```

### Controllers

#### Naming Conventions

- **Page Controllers**: `{Name}PageController` (`HomePageController`, `ProductPageController`)
- **Resource Controllers**: `{Resource}Controller` (`CartController`, `OrderController`)
- **Invokable Controllers**: Single `__invoke()` method for single-action routes

#### Page Controller Pattern

Page controllers extend an abstract `PageController` base class, not `Controller` directly. They use a CMS-driven approach with transformables:

```php
<?php

namespace App\Http\Controllers;

use App\CMS\ProductsTransformable;

class ProductPageController extends PageController
{
    public function __construct()
    {
        parent::__construct(
            componentView: 'Product',
            slug: 'product',
            transformables: [
                new ProductsTransformable,
            ],
            extendedData: []
        );
    }
}
```

The abstract `PageController` base class:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Traits\Metatags;
use Inertia\Inertia;

abstract class PageController extends Controller
{
    use Metatags;

    public function __construct(
        protected readonly string $componentView,
        protected readonly string $slug,
        protected readonly array $transformables,
        protected readonly array $extendedData = []
    ) {}

    public function __invoke()
    {
        $page = Page::bySlug($this->slug);

        foreach ($page->sectionsForUI($this->transformables) as $section) {
            $component = Str::studly($section['slug']);
            $components[] = ['class' => $component, 'content' => $section['content']];
        }

        return Inertia::render($this->componentView, [
            'components' => fn () => collect($components)->keyBy('class'),
            'metatags' => fn () => $this->metatags(),
            ...$this->extendedData,
        ]);
    }
}
```

#### API Controller Pattern (JSON responses)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Cart;

class CartController extends Controller
{
    public function create(Request $request)
    {
        $request->validate(['id' => 'required|uuid']);

        $cart = Cart::create(['ui_cart_id' => $request->input('id')]);

        return response()->json([
            'ui_cart_id' => $cart->ui_cart_id,
            'items' => []
        ])->cookie('cart', $cart->ui_cart_id, 60 * 24 * 30);
    }

    public function show(Request $request)
    {
        $cart = Cart::byUICartId($request->input('id'))->firstOrFail();

        if ($cart->isPaid()) {
            abort(404);
        }

        return [
            'ui_cart_id' => $cart->ui_cart_id,
            'items' => $cart->items->toArray(),
            'cart_aggregation' => [
                'total_without_taxes_in_dollars' => $cart->total_without_taxes_in_dollars,
                'total_with_taxes_in_dollars' => $cart->total_with_taxes_in_dollars,
                'items_count' => $cart->items_count,
            ]
        ];
    }
}
```

### Form Requests

Always create Form Request classes for validation:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserInfoEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
        ];
    }
}
```

### Filament Resources

#### Resource Organization Pattern

Ecommerce resources live in the package (`JFA\ToecommerceCore\Filament\Resources\`), CMS resources stay in the app (`App\Filament\Resources\`). Both follow the same structure:

```
{namespace}/Filament/Resources/{Resource}/
├── {Resource}Resource.php          # Main resource class
├── Pages/
│   ├── List{Resource}s.php         # Index page
│   ├── Create{Resource}.php        # Create page (optional)
│   ├── Edit{Resource}.php          # Edit page
│   └── View{Resource}.php          # View page
├── Schemas/
│   ├── {Resource}Form.php          # Form schema
│   └── {Resource}Infolist.php      # Infolist schema
├── Tables/
│   └── {Resource}sTable.php        # Table schema
└── RelationManagers/
    └── {Relation}RelationManager.php
```

#### Filament v5 Patterns

1. **Actions use `Filament\Actions\Action`**, not `Filament\Tables\Actions\`
    ```php
    use Filament\Actions\Action;
    use Filament\Actions\EditAction;
    use Filament\Actions\ViewAction;
    use Filament\Actions\DeleteBulkAction;
    ```

2. **Icons use `Filament\Support\Icons\Heroicon` enum**
    ```php
    use Filament\Support\Icons\Heroicon;
    // Usage: Heroicon::OutlinedRectangleStack
    ```

3. **Schema uses `Filament\Schemas\Schema`** (not old form schema)

4. **Resource properties use correct union types with `BackedEnum` and `UnitEnum`**
    ```php
    use BackedEnum;
    use UnitEnum;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('firesources.store');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    ```

5. **Custom navigation icon**: Override `getNavigationIcon()` for custom SVG icons
    ```php
    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'icon-box';
    }
    ```

#### Resource Class Pattern

```php
<?php

namespace JFA\ToecommerceCore\Filament\Resources\Products;

use JFA\ToecommerceCore\Filament\Resources\Products\Pages\ListProducts;
use JFA\ToecommerceCore\Filament\Resources\Products\Pages\CreateProduct;
use JFA\ToecommerceCore\Filament\Resources\Products\Pages\EditProduct;
use JFA\ToecommerceCore\Filament\Resources\Products\Pages\ViewProduct;
use JFA\ToecommerceCore\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use JFA\ToecommerceCore\Filament\Resources\Products\Schemas\ProductForm;
use JFA\ToecommerceCore\Filament\Resources\Products\Schemas\ProductInfolist;
use JFA\ToecommerceCore\Filament\Resources\Products\Tables\ProductsTable;
use JFA\ToecommerceCore\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return __('firesources.product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('firesources.products');
    }

    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('firesources.store');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'variants' => VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
```

### Routes

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
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

// Public storefront pages (CMS-driven, all invokable PageControllers)
Route::get('/', HomePageController::class)->name('storefront.home');
Route::get('/products', ProductsPageController::class)->name('storefront.products');
Route::get('/products/{product:slug}', ProductPageController::class)->name('storefront.product');
Route::get('/collections', CollectionsPageController::class)->name('storefront.collections');
Route::get('/collections/{collection:slug}', CollectionPageController::class)->name('storefront.collection');
Route::get('/about', AboutPageController::class)->name('storefront.about');
Route::get('/contact', ContactPageController::class)->name('storefront.contact');
Route::get('/terminos-y-condiciones', TermsAndConditionsPageController::class)->name('storefront.terms-and-conditions');
Route::get('/politica-de-privacidad', PrivacyPolicyPageController::class)->name('storefront.privacy-policy');

// Cart API routes (JSON responses)
Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create');
Route::post('/cart/show', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/items/addOrUpdate', [CartItemController::class, 'addOrUpdate'])->name('cart.items.addOrUpdate');
Route::post('/cart/items/remove', [CartItemController::class, 'remove'])->name('cart.items.remove');
Route::post('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');

// Contact form (with Precognition + Honeypot)
Route::post('/contact', [ContactPageController::class, 'sendMessage'])
    ->middleware([HandlePrecognitiveRequests::class, ProtectAgainstSpam::class])
    ->name('storefront.send-message');

// Payment confirmation
Route::get('/payments/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');

// Protected routes (authenticated users)
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/checkout', CheckoutController::class)->name('storefront.checkout');
    Route::post('/orders/select-payment-method', [OrderController::class, 'selectPaymentMethod'])->name('storefront.orders.select-payment-method');
    Route::post('/orders/cancel', [OrderController::class, 'cancelOrder'])->name('storefront.orders.cancel');
    Route::post('/user-info', [UserInfoEntryController::class, 'store'])
        ->middleware([HandlePrecognitiveRequests::class, ProtectAgainstSpam::class])
        ->name('storefront.user-info-entry.store');
    Route::put('/user-info/{id}', [UserInfoEntryController::class, 'update'])
        ->middleware([HandlePrecognitiveRequests::class, ProtectAgainstSpam::class])
        ->name('storefront.user-info-entry.update');
    Route::post('/shipping-info/use-billing', [UserInfoEntryController::class, 'useBillingAsShipping'])
        ->name('storefront.user-info-entry.use-billing-as-shipping');
});

// Login redirects to Filament customer auth panel
Route::get('/login', fn () => Inertia::location('/customer/login'))->name('login');
```

---

## Frontend (Vue + Inertia)

### Directory Structure

```
resources/js/
├── pages/                    # Inertia.js page components (10 pages)
│   ├── Home.vue
│   ├── Product.vue
│   ├── Products.vue
│   ├── Checkout.vue
│   ├── About.vue
│   ├── Contact.vue
│   ├── Collection.vue
│   ├── Collections.vue
│   ├── PrivacyPolicy.vue
│   └── TermsAndConditions.vue
├── components/
│   ├── ui/                  # 26 shadcn/vue UI component sets (140 files)
│   │   ├── button/
│   │   ├── card/
│   │   ├── dialog/
│   │   ├── sheet/
│   │   ├── sidebar/
│   │   ├── sonner/
│   │   ├── stepper/
│   │   └── ... (26 total)
│   ├── {feature}/           # Feature-specific (home/, about/, contact/, privacy_policy/, terms_and_conditions/)
│   └── *.vue                # Shared business components
├── layouts/
│   └── StorefrontLayout.vue
├── stores/                  # Pinia stores
│   ├── cartStore.ts          # Cart state + actions + DB sync
│   └── cartDrawerStore.ts    # Cart drawer UI state
├── composables/             # Vue composables
│   ├── useAppearance.ts
│   ├── useCartItemQuantity.ts
│   └── useInitials.ts
├── routes/                  # Wayfinder generated route functions
│   ├── index.ts              # Login route
│   ├── boost/
│   ├── cart/
│   │   ├── index.ts          # create, show, empty
│   │   └── items/            # addOrUpdate, remove
│   ├── default-livewire/
│   ├── filament/
│   ├── livewire/
│   ├── payments/
│   ├── storage/
│   └── storefront/
│       ├── index.ts          # Page routes
│       ├── orders/
│       └── user-info-entry/
├── actions/                 # Wayfinder generated controller action functions
│   ├── App/
│   │   └── Http/Controllers/  # Typed controller action wrappers
│   └── Filament/
│       ├── Actions/
│       ├── Auth/
│       ├── Http/
│       └── Pages/
├── wayfinder/               # Wayfinder core utilities (queryParams, RouteDefinition, etc.)
├── lib/                     # Utility libraries
│   ├── utils.ts              # cn() helper, urlIsActive()
│   └── codyhouse/            # Carousel, slideshow, swipe utilities
└── types/                   # TypeScript definitions
    ├── index.d.ts            # All app types (Product, Cart, Order, etc.)
    └── globals.d.ts          # Global type augmentations
```

### Page Component Pattern

```vue
<template>
    <div>
        <AppHead :metaTags="metaTags" :company="company" />
        <Hero :content="heroContent" />
        <RecentProducts :content="recentProductsContent" />
        <Collections :content="collectionsContent" />
        <OurPromise :content="ourPromiseContent" />
    </div>
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import Collections from '@/components/home/Collections.vue';
import Hero from '@/components/home/Hero.vue';
import RecentProducts from '@/components/home/RecentProducts.vue';
import OurPromise from '@/components/home/OurPromise.vue';
import AppHead from '@/components/AppHead.vue';
import { Company, Metatags, PageComponentContent, PageComponents } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const metaTags = page.props.metatags as Metatags;
const company = page.props.company as Company;
const components = page.props.components as PageComponents;

const heroContent = computed(() => components['Hero'].content as PageComponentContent);
const recentProductsContent = computed(() => components['RecentProducts'].content as PageComponentContent);
const collectionsContent = computed(() => components['Collections'].content as PageComponentContent);
const ourPromiseContent = computed(() => components['OurPromise'].content as PageComponentContent);

defineOptions({ layout: StorefrontLayout });
</script>
```

### Component Naming Conventions

- **General Components**: PascalCase (`ProductCard.vue`, `CartItem.vue`)
- **Page-specific Components**: Grouped in folders by feature
    - `home/Hero.vue`, `home/Collections.vue`
    - `contact/ContactForm.vue`, `contact/CompanyInfo.vue`
- **UI Components**: Each in own folder with index.ts
    - `ui/button/Button.vue`
    - `ui/button/index.ts` - exports component + variants

### State Management (Pinia)

```typescript
// stores/cartStore.ts
import { create, empty, show } from '@/routes/cart';
import { addOrUpdate, remove } from '@/routes/cart/items';
import { useHttp } from '@inertiajs/vue3'; // Inertia v3 built-in XHR
import { defineStore } from 'pinia';
import { v7 as uuidv7 } from 'uuid';

export const useCartStore = defineStore('cart', {
    state: () => ({
        id: '' as string,
        aggregation: {} as CartAggregation,
        items: [] as CartItem[],
    }),

    actions: {
        async init(cookieCart: string) { /* ... */ },
        async addOrUpdateItem(data: DataForCart) {
            const http = useHttp({ ...data });
            const response = await http.post(addOrUpdate().url);
            await this.getCartFromDB(this.id);
            return response;
        },
        async removeItem(data) { /* ... */ },
        async emptyCart(data) { /* ... */ },
        productInItem(productSlug: string) { /* ... */ },
        async createCartInDB(cartId: string) { /* ... */ },
        async getCartFromDB(cartId: string) { /* ... */ },
    },

    getters: {
        cartItems: (state) => state.items.sort((a, b) => a.id - b.id),
        isEmpty: (state) => state.items.length === 0,
    },
});
```

```typescript
// stores/cartDrawerStore.ts
import { defineStore } from 'pinia';

export const useCartDrawerStore = defineStore('cartDrawer', {
    state: () => ({ isOpen: false }),
    actions: {
        toggle() { this.isOpen = !this.isOpen; },
        open() { this.isOpen = true; },
        close() { this.isOpen = false; },
    },
});
```

### HTTP Requests (Inertia v3)

Use `useHttp` from `@inertiajs/vue3` instead of Axios for XHR requests:

```typescript
import { useHttp } from '@inertiajs/vue3';

const http = useHttp({ id: cartId });
const response = await http.post(create().url);
```

### Wayfinder Routes & Actions Usage

```typescript
// Import generated route functions
import { show, create, empty } from '@/routes/cart';
import { addOrUpdate, remove } from '@/routes/cart/items';
import { home, product } from '@/routes/storefront';

// Each route exports { url, method } and shortcut methods
create().url;    // "/cart/create"
create().post;   // { url: "/cart/create", method: "post" }
home().url;      // "/"
product({ product: 'product-slug' }).url; // "/products/product-slug"

// Using with Inertia v3's useHttp (not Axios)
import { useHttp } from '@inertiajs/vue3';

const http = useHttp({ id: cartId });
const response = await http.post(create().url);
```

#### Wayfinder Controller Actions

Wayfinder also generates typed action wrappers for controllers:

```typescript
// resources/js/actions/ (auto-generated)
import { App } from '@/actions';

// Typed wrappers for controller methods
App.Http.Controllers.CartController.create({ id: cartId });
```

### UI Component Pattern (shadcn/vue)

```vue
<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Primitive, type PrimitiveProps } from 'reka-ui';
import { buttonVariants } from '.';

interface Props extends PrimitiveProps {
    variant?: NonNullable<Parameters<typeof buttonVariants>[0]>['variant'];
    size?: NonNullable<Parameters<typeof buttonVariants>[0]>['size'];
    as?: string;
}

const props = withDefaults(defineProps<Props>(), {
    as: 'button',
});
</script>

<template>
    <Primitive data-slot="button" :as="as" :as-child="asChild" :class="cn(buttonVariants({ variant, size }), props.class)">
        <slot />
    </Primitive>
</template>
```

```typescript
// index.ts
import { cva, type VariantProps } from 'class-variance-authority';
export { default as Button } from './Button.vue';

export const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium whitespace-nowrap transition-colors focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50',
    {
        variants: {
            variant: {
                default: 'bg-primary text-primary-foreground shadow hover:bg-primary/90',
                destructive: 'bg-destructive text-white shadow-sm hover:bg-destructive/90',
                outline: 'border bg-background shadow-sm hover:bg-accent hover:text-accent-foreground',
                secondary: 'bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80',
                ghost: 'hover:bg-accent hover:text-accent-foreground',
                link: 'text-primary underline-offset-4 hover:underline',
            },
            size: {
                default: 'h-9 px-4 py-2',
                sm: 'h-8 rounded-md px-3 text-xs',
                lg: 'h-10 rounded-md px-8',
                icon: 'h-9 w-9',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);
```

### Key Utilities

```typescript
// lib/utils.ts
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}
```

---

## Testing (Pest PHP)

### Directory Structure

```
tests/
├── Pest.php              # Pest configuration & global helpers
├── TestCase.php          # Base TestCase
├── Feature/              # Feature tests (14 files)
│   ├── AddToCartTest.php
│   ├── CheckoutTest.php
│   ├── ContactFormTest.php
│   ├── PagesResponseTest.php
│   ├── PageTest.php
│   ├── PaymentTest.php
│   ├── RemoveFromCartTest.php
│   ├── ShowProductListingTest.php
│   ├── ShowProductsByCollectionTest.php
│   ├── UICartTest.php
│   ├── UpdateUserInfoEntryTest.php
│   ├── ViewCollectionsListTest.php
│   ├── ViewProductsListTest.php
│   └── OrderStatusManagementTest.php
└── Unit/                 # Unit tests (17 files)
    ├── CartTest.php
    ├── CartItemTest.php
    ├── CartItemResourceTest.php
    ├── ConfirmsPaymentTest.php
    ├── DiscountTest.php
    ├── MenuItemTest.php
    ├── MenuTest.php
    ├── OrderItemResourceTest.php
    ├── OrderResourceTest.php
    ├── OrderTest.php
    ├── PayphoneTransactionIdGeneratorTest.php
    ├── PerformsAddToCartTest.php
    ├── ProductTest.php
    ├── ProductVariantTest.php
    ├── ResolvesPurchasableTest.php
    ├── SectionTest.php
    └── UserTest.php
```

### Test Structure Pattern

```php
<?php

use JFA\ToecommerceCore\Models\Cart;
use JFA\ToecommerceCore\Models\Product;
use JFA\ToecommerceCore\Models\CartItem;

// Test using 'test()' function with descriptive names
test('can add a published product to the cart', function () {
    // Arrange
    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create(['ui_cart_id' => $uiCartId]);

    // Act
    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => 1,
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    // Assert
    expect($cart->fresh()->items)->toHaveCount(1);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $product->title,
        'price' => $product->price * 100, // Money stored in cents
        'quantity' => 1,
    ]);
});

// Alternative using 'it()' alias
it('belongs to a user', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    expect($order->user->id)->toBe($user->id);
});
```

### beforeEach Hook

```php
<?php

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'customer@example.com',
        'phone' => '1234567890',
        'name' => 'John Doe',
    ]);
});
```

### Helper Functions in Tests

```php
<?php

// Local helper functions for reusable setup
function createCartWithItem(array $data, $isVariant = false) {
    $product = Product::factory()->published()->create($data);
    $cart = Cart::factory()->create();

    if ($isVariant) {
        $purchasable = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);
    } else {
        $purchasable = $product;
    }

    $cart->addOrUpdateItem($purchasable, 1);

    return [$purchasable, $cart];
}

function validParams(array $overrides = []) {
    return [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        ...$overrides,
    ];
}

// Access test instance
test()->cart = $cart;
test()->order = $order;
```

### Pest Configuration (Pest.php)

```php
<?php

use JFA\ToecommerceCore\Enums\DiscountCalculationModes;
use JFA\ToecommerceCore\Enums\StockControlModes;
use JFA\ToecommerceCore\Models\Cart;
use JFA\ToecommerceCore\Models\CartItem;
use JFA\ToecommerceCore\Models\Product;
use JFA\ToecommerceCore\Models\ProductVariant;
use JFA\ToecommerceCore\Models\Tax;
use JFA\ToecommerceCore\Settings\StorefrontSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

beforeEach(function () {
    app()->forgetInstance(StorefrontSettings::class);
});

// Custom expectation
expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

// Global helper functions
function createCartWithoutItem(array $productData, $isVariant = false)
{
    if ($isVariant) {
        $purchasable = ProductVariant::factory([
            'product_id' => Product::factory(),
        ])->published()->create($productData);
    } else {
        $purchasable = Product::factory()->published()->create($productData);
    }

    $cart = Cart::factory()->create();

    return [$purchasable, $cart];
}

function createCartWithItem(array $data, $isVariant = false)
{
    $iva = Tax::factory()->create(['name' => 'IVA', 'percentage' => 15, 'description' => 'IVA 15%']);
    $isd = Tax::factory()->create(['name' => 'ISD', 'percentage' => 10, 'description' => 'ISD 10%']);

    $product = Product::factory()->published()->create($data);
    $product->taxes()->attach([$iva->id, $isd->id]);

    if ($isVariant) {
        $purchasable = ProductVariant::factory()->published()->create([
            ...$data, 'product_id' => $product->id,
        ]);
    } else {
        $purchasable = $product;
    }

    $cart = Cart::factory()->has(CartItem::factory()->count(1)->state([
        'purchasable_id' => $purchasable->id,
        'purchasable_type' => Product::class,
        'title' => $purchasable->title,
        'slug' => $purchasable->slug,
        'price' => $purchasable->price,
        'quantity' => 4,
        'total' => 4 * $purchasable->price,
        'taxes' => json_encode([
            ['percentage' => $iva->percentage, 'name' => $iva->name],
            ['percentage' => $isd->percentage, 'name' => $isd->name],
        ]),
    ]), 'items')->create();

    return [$purchasable, $cart];
}

function setStrictMode(StockControlModes $mode = StockControlModes::STRICT)
{
    $sfSettings = app(StorefrontSettings::class);
    $sfSettings->stock_control_mode = $mode;
    $sfSettings->save();
}

function setPaginationNumber(int $paginationNumber = 10)
{
    $sfSettings = app(StorefrontSettings::class);
    $sfSettings->products_per_page = $paginationNumber;
    $sfSettings->save();
}

function setDiscountCalculationMode(DiscountCalculationModes $mode = DiscountCalculationModes::HIGHEST)
{
    $sfSettings = app(StorefrontSettings::class);
    $sfSettings->discount_calculation_mode = $mode;
    $sfSettings->save();
}
```

### Common Testing Patterns

| Pattern                                | Usage                                           |
| -------------------------------------- | ----------------------------------------------- |
| `test()` / `it()`                      | Test declarations                               |
| `beforeEach()`                         | Shared setup                                    |
| `expect()->toBe()` / `->toHaveCount()` | Primary assertions                              |
| `$this->assertDatabaseHas()`           | Database state assertions                       |
| `actingAs()`                           | Authentication                                  |
| `withCookie()`                         | Session/cookie testing                          |
| `Mail::fake()`                         | Mail mocking                                    |
| `RefreshDatabase`                      | Database reset between tests                    |
| Factory states                         | Model variations (`->published()`, `->draft()`) |
| Precognition validation                | `HandlePrecognitiveRequests` middleware in tests |

---

## Styling (Tailwind CSS v4)

### Tailwind v4 Configuration

```css
/* resources/css/app.css */
@import 'tailwindcss';
@import 'tw-animate-css';

/* Component-specific stylesheets (17 files) */
@import './header.css';
@import './hero.css';
@import './slideshow.css';
@import './payphone-button.css';
@import './product-card.css';
@import './video-feature.css';
@import './collections-grid.css';
@import './collection-card.css';
@import './ourpromise.css';
@import './footer.css';
@import './article.css';
@import './values.css';
@import './feature_card.css';
@import './details-list.css';
@import './socials.css';
@import './payphone.css';

/* Plugins */
@plugin "@tailwindcss/typography";

/* Source paths */
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.ts';

/* Theme configuration (CSS-first) */
@theme inline {
    --font-sans: 'Raleway', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
    --font-sans2: 'Inter', ui-sans-serif, system-ui, sans-serif, ...;
    --font-serif: 'Berkshire Swash', ui-serif, system-ui, serif, ...;
    --font-serif2: 'Original Surfer', ui-serif, system-ui, serif, ...;

    --radius: 0.5rem;
    --radius-lg: var(--radius);
    --radius-md: calc(var(--radius) - 2px);
    --radius-sm: calc(var(--radius) - 4px);

    --color-background: var(--background);
    --color-foreground: var(--foreground);
    --color-primary: var(--primary);
    --color-secondary: var(--secondary);
    --color-accent: var(--accent);
    --color-destructive: var(--destructive);
    /* Full shadcn color tokens including card, popover, muted, chart, sidebar */
}

@layer base {
    * {
        @apply border-border outline-ring/50;
    }
    body {
        @apply bg-background;
    }

    h1 {
        @apply text-3xl font-semibold md:text-6xl;
    }

    h2 {
        @apply text-4xl font-semibold md:text-5xl;
    }
}

/* Custom utility classes */
@layer utilities {
    .wrapper {
        @apply mx-auto w-[calc(100%_-_2.5rem)] max-w-7xl lg:w-[calc(100%_-_4rem)] lg:justify-between;
    }

    .section-spacing {
        @apply py-20;
    }

    .products-grid { @apply flex flex-col gap-10; }
    .checkout-grid { display: grid; grid-template-columns: 1fr; }
    .product-grid { display: grid; grid-template-columns: 1fr; }
}
```

### Key Tailwind v4 Patterns

1. **CSS-first configuration** - No `tailwind.config.js`, use `@theme` directive
2. **Import via CSS** - `@import 'tailwindcss'` instead of `@tailwind` directives
3. **CSS variables for theming** - `--color-primary`, `--color-background`, etc.
4. **Opacity modifier syntax** - `bg-black/50` instead of `bg-opacity-50`
5. **No `corePlugins`** - Not supported in v4

---

## Key Patterns & Conventions

### Money Handling

Always store money in cents (integers) and use the Money cast:

```php
// Migration
$table->integer('price'); // Store in cents

// Model
protected function casts(): array
{
    return [
        'price' => Money::class, // Converts cents <-> dollars
    ];
}

// Usage
$product->price = 19.99; // Set in dollars
$product->save(); // Stored as 1999 (cents)
```

### Settings Pattern (Spatie)

```php
<?php

namespace JFA\ToecommerceCore\Settings;

use JFA\ToecommerceCore\Enums\StockControlModes;
use Spatie\LaravelSettings\Settings;

class StorefrontSettings extends Settings
{
    public int $products_per_page;
    public string $stock_control_mode;    // Stored as string, mapped to enum via accessors
    public string $discount_calculation_mode;

    public function isAppInStrictMode(): bool
    {
        return $this->stock_control_mode === StockControlModes::STRICT->value;
    }

    public static function group(): string
    {
        return 'storefront';
    }
}
```

App-level settings stay in the app:
- `CompanySettings` - Stores company info (name, address, phone, email, social media, working days)

### Content Transformation Pipeline

```php
<?php

namespace App\CMS;

use App\Models\Section;

class ContentResolver
{
    private ?MediaCollection $images;

    public function __construct(private Section $section)
    {
        $this->images = $section->hasMedia('*') ? $section->getMedia('*') : null;
    }

    public function resolve(array $transformables = []): array
    {
        $data = $this->section->content;

        $data = collect($data)->map(function ($item) use ($transformables) {
            foreach ($transformables as $transformable) {
                $item = $transformable->transform($item);
            }
            return $item;
        });

        $contentByType = $data->groupBy('type');

        $content = $contentByType->map(function ($content) {
            return $content->pluck('data')->all();
        });

        return array_merge($content->toArray(), ['images' => $this->images]);
    }
}
```

```php
<?php

namespace App\CMS;

interface ContentTransformable
{
    public function transform(array $item): array;
}
```

Transformers (each implements `ContentTransformable`):
- `ImageTransformable` - Resolves media library images
- `ProductsTransformable` - Resolves product references
- `CollectionsTransformable` - Resolves collection references
- `FeaturedProductTransformable` - Resolves featured product
- `FeatureTransformable` - Resolves feature blocks
- `RichTextTransformable` - Processes rich text content

Content is resolved via `PageController` which passes transformables to `ContentResolver` through the `Page` model's `sectionsForUI()` method. The resolved content is grouped by component type and passed to Inertia as page props.

### Cart Item Polymorphic Pattern

```php
<?php

// CartItem model
class CartItem extends Model
{
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }
}

// Product model - implements Purchasable
class Product extends Model implements Purchasable
{
    public function dataforCart(): array
    {
        return [
            'purchasable_id' => $this->id,
            'purchasable_type' => Product::class,
            // ...
        ];
    }
}

// Adding to cart
$cart->addOrUpdateItem($product, $quantity);
$cart->addOrUpdateItem($productVariant, $quantity); // Same method, different types
```

#### Inertia v3 Features in Use

- **`useHttp` hook** — Built-in XHR client (replaces Axios) for cart API interactions
- **Deferred props** — Use `fn () =>` closure syntax for lazy-loading props (e.g., `'components' => fn () => ...`)
- **Precognition** — Real-time validation via `laravel-precognition-vue` on checkout forms

### Vite Configuration

```typescript
// vite.config.ts
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts', 'resources/css/filament/admin/theme.css'],
            ssr: 'resources/js/ssr.ts',
            refresh: [...refreshPaths, "app/Filament/**", "app/Livewire/**", "app/Providers/Filament/**", "app/Models/**"],
        }),
        tailwindcss(),
        wayfinder(),
        vue({ template: { transformAssetUrls: { base: null, includeAbsolute: false } } }),
    ],
    resolve: { alias: { '@': path.resolve(__dirname, './resources/js') } },
});
```

## Naming Summary

| Type                   | Convention              | Example                              |
| ---------------------- | ----------------------- | ------------------------------------ |
| **Models**             | Singular, PascalCase    | `Product`, `OrderItem`               |
| **Controllers**        | PascalCase + Controller | `ProductPageController`              |
| **Filament Resources** | PascalCase + Resource   | `ProductResource`                    |
| **Filament Pages**     | Action + Resource       | `ListProducts`, `CreateProduct`      |
| **Traits**             | PascalCase, descriptive | `Discountable`, `MoneyFormat`        |
| **Enums**              | PascalCase              | `ProductStatus`, `StockControlModes` |
| **Settings**           | PascalCase + Settings   | `StorefrontSettings`                 |
| **Vue Components**     | PascalCase              | `ProductCard.vue`, `Hero.vue`        |
| **Vue Pages**          | PascalCase              | `Home.vue`, `Product.vue`            |
| **Test Files**         | PascalCase + Test       | `AddToCartTest.php`                  |
| **Test Functions**     | Descriptive lowercase   | `test('can add product to cart')`    |

### File Organization Summary

```
app/
├── Models/                    # CMS entities (Page, Section, Menu, MenuItem, Contact)
├── Http/Controllers/          # Request handlers (17 controllers)
├── Http/Requests/             # Validation rules
├── Filament/Resources/        # CMS admin UI (Menus, Pages, Sections)
├── Filament/Pages/            # App-level pages (ManageCompanyInfo)
├── CMS/                       # Content management (8 files)
├── Settings/                  # CompanySettings
├── Traits/                    # Metatags
├── Utils/                     # Helper classes (ConfirmsPayment, PerformsAddsToCart, ResolvesPurchasable)

vendor/franc014/toecommerce-core/src/
├── Models/                    # Ecommerce entities (13 models)
├── Enums/                     # 6 enums
├── Traits/                    # 5 traits
├── Casts/                     # Money cast
├── Exceptions/                # 6 exception classes
├── Settings/                  # StorefrontSettings
├── Filament/Resources/        # Ecommerce admin UI (8 resources)
├── Database/Factories/        # 13 factories
├── Filament/Pages/            # ManageStorefront

resources/js/
├── pages/                     # Inertia page components (10 pages)
├── components/                # Vue components (212 total)
│   ├── ui/                   # 26 shadcn/vue UI sets (140 files)
│   └── {feature}/            # Feature-specific (home, about, contact, etc.)
├── stores/                    # Pinia state (cart, cartDrawer)
├── routes/                    # Wayfinder generated route functions
├── actions/                   # Wayfinder controller action functions
├── wayfinder/                 # Wayfinder core utilities
├── composables/               # Vue composables (3)
├── lib/                       # Utilities (cn(), codyhouse carousel)
└── types/                     # TypeScript definitions
```

---

## Build Commands

```bash
# Development
npm run dev
composer run dev

# Production build
npm run build

# Testing
php artisan test --compact
php artisan test --compact tests/Feature/AddToCartTest.php

# Code formatting (app)
vendor/bin/pint --dirty

# Code formatting (package)
cd ../toecommerce-core && vendor/bin/pint --dirty && cd ../toecommerce

# Linting
npm run lint
npm run format
```

---

## Statistics

- **PHP Files**: ~100 files in app/ + package
- **Vue Components**: 212 components
- **Feature Tests**: 14 files
- **Unit Tests**: 17 files
- **Filament Resources**: 11 total — 8 ecommerce (package) + 3 CMS (app)
- **UI Components**: 26 shadcn/vue component sets (140 .vue files)
- **Package Tests**: 289 passing, 0 failing

---

_Generated from codebase analysis - Last updated: 2026-05-22_
