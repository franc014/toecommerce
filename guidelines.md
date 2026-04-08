# ToEcommerce Codebase Guidelines

> Auto-generated from codebase analysis on 2026-02-09
> Based on Laravel 12 + Vue 3 + Inertia.js v2 + Filament v4

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Backend (Laravel)](#backend-laravel)
3. [Frontend (Vue + Inertia)](#frontend-vue--inertia)
4. [Testing (Pest PHP)](#testing-pest-php)
5. [Styling (Tailwind CSS v4)](#styling-tailwind-css-v4)
6. [Key Patterns & Conventions](#key-patterns--conventions)

---

## Architecture Overview

### Tech Stack

| Layer              | Technology           | Version |
| ------------------ | -------------------- | ------- |
| Backend            | Laravel              | 12.x    |
| Admin Panel        | Filament             | 4.x     |
| Frontend Framework | Vue                  | 3.x     |
| SSR Framework      | Inertia.js           | 2.x     |
| Styling            | Tailwind CSS         | 4.x     |
| Build Tool         | Vite                 | 7.x     |
| Testing            | Pest PHP             | 4.x     |
| State Management   | Pinia                | 3.x     |
| UI Components      | shadcn/vue + Reka UI | -       |

### Application Structure

```
toecommerce/
├── app/                          # Laravel application code
│   ├── Casts/                    # Custom Eloquent casts (Money, etc.)
│   ├── CMS/                      # Content Management System
│   │   ├── ContentResolver.php
│   │   └── *Transformable.php    # Content transformers
│   ├── Enums/                    # PHP Enums
│   ├── Filament/                 # Filament admin resources
│   │   ├── Resources/
│   │   │   └── {Resource}/
│   │   │       ├── Pages/
│   │   │       ├── Schemas/      # Form & Infolist schemas
│   │   │       ├── Tables/       # Table configurations
│   │   │       └── RelationManagers/
│   │   └── ...
│   ├── Http/
│   │   ├── Controllers/          # Page & API controllers
│   │   ├── Middleware/
│   │   └── Requests/             # Form request validation
│   ├── Models/                   # Eloquent models
│   ├── Settings/                 # Spatie Settings classes
│   ├── Traits/                   # Reusable traits
│   └── Utils/                    # Utility classes
├── resources/
│   ├── js/
│   │   ├── pages/                # Inertia.js page components
│   │   ├── components/           # Vue components
│   │   │   ├── ui/              # shadcn/vue UI components
│   │   │   └── {feature}/       # Feature-specific components
│   │   ├── layouts/             # Layout components
│   │   ├── stores/              # Pinia stores
│   │   ├── composables/         # Vue composables
│   │   ├── routes/              # Wayfinder generated routes
│   │   └── types/               # TypeScript definitions
│   └── css/                     # Tailwind CSS stylesheets
├── tests/
│   ├── Feature/                 # Feature tests
│   ├── Unit/                    # Unit tests
│   └── Pest.php                 # Pest configuration
└── routes/
    └── web.php                  # Web routes
```

---

## Backend (Laravel)

### Models

#### Naming Conventions

- **Models**: Singular, PascalCase (`Product`, `OrderItem`)
- **Traits**: PascalCase, descriptive (`MoneyFormat`, `Discountable`)
- **Interfaces**: PascalCase (`Purchasable`)
- **Enums**: PascalCase (`ProductStatus`, `StockControlModes`)

#### Model Structure Pattern

```php
<?php

namespace App\Models;

use App\Casts\Money;
use App\Enums\ProductStatus;
use App\Traits\Discountable;
use App\Traits\MoneyFormat;
use App\Traits\Publishable;
use App\Traits\Taxable;
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

5. **Laravel 12 Casts Method**: Always use `casts()` method instead of `$casts` property

### Controllers

#### Naming Conventions

- **Page Controllers**: `{Name}PageController` (`HomePageController`, `ProductPageController`)
- **Resource Controllers**: `{Resource}Controller` (`CartController`, `OrderController`)
- **Invokable Controllers**: Single `__invoke()` method for single-action routes

#### Controller Patterns

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductPageController extends Controller
{
    public function __invoke(Request $request, Product $product): Response
    {
        return Inertia::render('Product', [
            'product' => $product->load(['variants', 'taxes']),
            'relatedProducts' => $product->relatedProducts(),
        ]);
    }
}
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Returns JSON for API-like interactions
    public function create(Request $request)
    {
        $request->validate([
            'id' => 'required|uuid',
        ]);

        $cart = Cart::create([
            'ui_cart_id' => $request->input('id'),
        ]);

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

```
app/Filament/Resources/{Resource}/
├── {Resource}Resource.php          # Main resource class
├── Pages/
│   ├── List{Resource}s.php         # Index page
│   ├── Create{Resource}.php        # Create page
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

#### Resource Class Pattern

```php
<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
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

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

// Public storefront routes
Route::get('/', HomePageController::class)->name('storefront.home');
Route::get('/products', [ProductsPageController::class, 'index'])->name('storefront.products');
Route::get('/products/{product:slug}', ProductPageController::class)->name('storefront.product');

// Cart API routes (JSON responses)
Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create');
Route::post('/cart/show', [CartController::class, 'show'])->name('cart.show');

// Protected routes
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/checkout', CheckoutController::class)->name('storefront.checkout');
    Route::post('/orders/cancel', [OrderController::class, 'cancelOrder'])
        ->name('storefront.orders.cancel');

    // Precognition validation
    Route::post('/user-info', [UserInfoEntryController::class, 'store'])
        ->middleware([HandlePrecognitiveRequests::class])
        ->name('storefront.user-info-entry.store');
});
```

---

## Frontend (Vue + Inertia)

### Directory Structure

```
resources/js/
├── pages/                    # Inertia.js page components
│   ├── Home.vue
│   ├── Product.vue
│   ├── Products.vue
│   ├── Checkout.vue
│   └── ...
├── components/
│   ├── ui/                  # shadcn/vue UI components (27 components)
│   │   ├── button/
│   │   ├── card/
│   │   ├── dialog/
│   │   └── ...
│   ├── home/                # Page-specific sections
│   ├── contact/
│   ├── about/
│   └── *.vue                # Shared business components
├── layouts/
│   └── StorefrontLayout.vue
├── stores/                  # Pinia stores
│   ├── cartStore.ts
│   ├── cartStoreActions.ts
│   └── cartDrawerStore.ts
├── composables/             # Vue composables
│   ├── useAppearance.ts
│   └── useCartItemQuantity.ts
├── routes/                  # Wayfinder generated routes
│   ├── storefront/
│   └── cart/
└── types/                   # TypeScript definitions
    └── index.d.ts
```

### Page Component Pattern

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import Hero from '@/components/home/Hero.vue';
import Collections from '@/components/home/Collections.vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const components = page.props.components as PageComponents;
</script>

<template>
    <div class="flex flex-col gap-24">
        <Hero :content="components.hero" />
        <Collections :collections="components.collections" />
    </div>
</template>
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
import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
    state: () => ({
        id: '' as string,
        aggregation: {} as CartAggregation,
        items: [] as CartItem[],
    }),

    actions: {
        init,
        addOrUpdateItem,
        removeItem,
        emptyCart,
        productInItem,
    },

    getters: {
        cartItems: (state) => state.items.sort((a, b) => a.created_at - b.created_at),
        isEmpty: (state) => state.items.length === 0,
        itemsCount: (state) => state.aggregation?.items_count || 0,
    },
});
```

### Wayfinder Routes Usage

```typescript
// Import generated routes
import { show, create, empty } from '@/routes/cart';
import { addOrUpdate, remove } from '@/routes/cart/items';

// Using routes
const cartDB = await axios.post(create().url, { id: cartId });
await axios.post(addOrUpdate().url, data);

// Route object structure
show().url; // "/cart/show"
create().url; // "/cart/create"
product({ product: 'slug' }).url; // "/products/slug"
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
├── TestCase.php          # Base TestCase with custom assertions
├── Feature/              # Feature tests (14 files)
│   ├── AddToCartTest.php
│   ├── CheckoutTest.php
│   ├── ContactFormTest.php
│   └── ...
└── Unit/                 # Unit tests (13 files)
    ├── CartTest.php
    ├── ProductTest.php
    └── ...
```

### Test Structure Pattern

```php
<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;

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

use App\Enums\DiscountCalculationModes;
use App\Enums\StockControlModes;
use App\Settings\StorefrontSettings;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

// Custom expectation
expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

// Global helper functions
function setStrictMode(StockControlModes $mode = StockControlModes::STRICT)
{
    $sfSettings = app(StorefrontSettings::class);
    $sfSettings->stock_control_mode = $mode;
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
| `assertInertia()`                      | Inertia page props assertions                   |
| `Event::fake()` / `Mail::fake()`       | Event and mail mocking                          |
| `RefreshDatabase`                      | Database reset between tests                    |
| Factory states                         | Model variations (`->published()`, `->draft()`) |

---

## Styling (Tailwind CSS v4)

### Tailwind v4 Configuration

```css
/* resources/css/app.css */
@import 'tailwindcss';
@import 'tw-animate-css';

/* Component-specific stylesheets */
@import './header.css';
@import './hero.css';
@import './product-card.css';

/* Plugins */
@plugin "@tailwindcss/typography";

/* Source paths */
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.ts';

/* Theme configuration (CSS-first) */
@theme inline {
    --font-sans: 'Raleway', ui-sans-serif, system-ui, sans-serif;
    --font-serif: 'Berkshire Swash', ui-serif, system-ui, serif;

    --radius: 0.5rem;
    --radius-lg: var(--radius);

    --color-background: var(--background);
    --color-foreground: var(--foreground);
    --color-primary: var(--primary);
    --color-secondary: var(--secondary);
    --color-accent: var(--accent);
    --color-destructive: var(--destructive);
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
        @apply mx-auto w-[calc(100%_-_2.5rem)] max-w-7xl lg:w-[calc(100%_-_4rem)];
    }

    .section-spacing {
        @apply py-20;
    }
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

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StorefrontSettings extends Settings
{
    public int $products_per_page;
    public string $stock_control_mode;
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

### Content Transformation Pipeline

```php
<?php

namespace App\CMS;

use App\CMS\Contracts\ContentTransformable;

class ContentResolver
{
    protected array $transformers = [
        'products' => ProductsTransformable::class,
        'collections' => CollectionsTransformable::class,
        'featured_product' => FeaturedProductTransformable::class,
        'image' => ImageTransformable::class,
        'rich_text' => RichTextTransformable::class,
    ];

    public function resolve(array $content): array
    {
        $type = $content['type'];

        if (isset($this->transformers[$type])) {
            $transformer = app($this->transformers[$type]);
            return $transformer->transform($content);
        }

        return $content;
    }
}
```

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

### Naming Summary

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
├── Models/                    # Business entities
├── Http/Controllers/          # Request handlers
├── Http/Requests/             # Validation rules
├── Filament/Resources/        # Admin UI
├── CMS/                       # Content management
├── Settings/                  # Configuration
├── Traits/                    # Shared functionality
└── Utils/                     # Helper classes

resources/js/
├── pages/                     # Inertia page components
├── components/                # Vue components
│   ├── ui/                   # shadcn/vue UI library
│   └── {feature}/            # Feature-specific
├── stores/                    # Pinia state
├── routes/                    # Wayfinder routes
└── composables/               # Vue composables
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

# Code formatting
vendor/bin/pint --dirty

# Linting
npm run lint
npm run format
```

---

## Statistics

- **PHP Files**: ~90 files in app/
- **Vue Components**: 217 components
- **Feature Tests**: 14 files
- **Unit Tests**: 13 files
- **Filament Resources**: 10+ resources
- **UI Components**: 27 shadcn/vue components

---

_Generated from codebase analysis - Last updated: 2026-02-09_
