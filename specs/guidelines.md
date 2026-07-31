# ToEcommerce Codebase Guidelines

> Extracted from a thorough review of the codebase on 2026-07-30.
> Supersedes the root `guidelines.md` (which was generated for Laravel 12 / Filament 4 / Inertia 2 and is now outdated).
>
> Canonical tooling/version rules live in `AGENTS.md` (Laravel Boost). This file focuses on **project-specific structure, conventions, and patterns** verified against the actual code.

## Table of Contents

1. [Tech Stack (verified)](#tech-stack-verified)
2. [Application Structure](#application-structure)
3. [Backend (Laravel)](#backend-laravel)
4. [Frontend (Vue 3 + Inertia 3)](#frontend-vue-3--inertia-3)
5. [Testing (Pest PHP)](#testing-pest-php)
6. [Styling (Tailwind CSS v4)](#styling-tailwind-css-v4)
7. [Key Project Patterns](#key-project-patterns)
8. [Naming & Organization Summary](#naming--organization-summary)
9. [Build & Verification Commands](#build--verification-commands)

---

## Tech Stack (verified)

| Layer              | Technology                          | Version (from composer.json / package.json) |
| ------------------ | ----------------------------------- | ------------------------------------------- |
| PHP                | PHP                                 | ^8.2 (AGENTS.md targets 8.4)                |
| Backend            | Laravel                             | ^13.0                                       |
| Admin Panel        | Filament                            | ^5.0                                        |
| CMS Rich Content   | Filament RichEditor                 | (bundled w/ Filament 5)                     |
| Frontend Framework | Vue                                 | ^3.5                                        |
| SSR Framework      | Inertia.js (inertia-laravel)        | ^3.0 / @inertiajs/vue3 ^3.0                 |
| Media              | Spatie Media Library                | (filament media plugin ^5)                  |
| Tags               | Spatie Tags                         | (filament tags plugin ^5)                   |
| Settings           | Spatie Laravel Settings             | (filament settings plugin ^5)               |
| Auth/Permissions   | Filament Shield                     | ^4.0                                        |
| Styling            | Tailwind CSS                        | ^4.1                                        |
| UI Components      | shadcn-style + Reka UI              | reka-ui ^2.6                                |
| State Mgmt         | Pinia                               | ^3.0                                        |
| HTTP Client (FE)   | Inertia `useHttp` (Axios removed)   | @inertiajs/vue3 ^3.0                        |
| Build Tool         | Vite                                | ^7.0                                        |
| Testing            | Pest PHP                            | ^4.1                                        |
| Formatting        | Laravel Pint / Prettier / ESLint    | pint ^1.18 / prettier ^3.4 / eslint ^9.17   |

**Notable differences vs. the old root `guidelines.md`:** Laravel 12→13, Filament 4→5, Inertia 2→3. Inertia v3 removes Axios (use the built-in XHR client / `useHttp`), removes `Inertia::lazy()` (use `Inertia::optional()`), and renames `invalid`→`httpException` / `exception`→`networkError`.

---

## Application Structure

```
toecommerce/
├── app/
│   ├── Casts/                    # Custom Eloquent casts (Money, etc.)
│   ├── CMS/                      # Content transformation pipeline
│   │   ├── ContentResolver.php
│   │   └── *Transformable.php
│   ├── Console/                  # Artisan commands
│   ├── Enums/                    # Backed enums (ProductStatus, StockControlModes, ...)
│   ├── Events/ / Listeners/     # Event-driven logic
│   ├── Exceptions/ / Providers/ # Exception handling & service providers
│   ├── Facades/ / Observers/    # Facades & model observers
│   ├── Filament/                 # Admin panel
│   │   ├── Resources/           # One folder per resource
│   │   │   └── {Resource}/      # ├─ {Resource}Resource.php
│   │   │                        # ├─ Pages/  Schemas/  Tables/  RelationManagers/
│   │   ├── Pages/               # Standalone Filament pages (ManageStorefront, ManageCompanyInfo)
│   │   ├── Actions/             # Reusable Filament actions (DiscountsAction, BulkDiscountsAction)
│   │   ├── Forms/               # Shared form builders (ContentBuilder, ContentBlocks)
│   │   ├── Exports/ / Imports/  # Spatie Excel exporters/importers
│   ├── Http/
│   │   ├── Controllers/          # {Name}PageController, {Resource}Controller
│   │   ├── Middleware/
│   │   └── Requests/             # Form request classes (StoreUserInfoEntryRequest, ...)
│   ├── Mail/ / Rules/ / Policies/ # Mailable classes, custom rules, authorization policies
│   ├── Models/                   # Eloquent models (+ interfaces like Purchasable)
│   ├── Settings/                 # Spatie Settings classes (StorefrontSettings)
│   ├── Traits/                   # Focused reusable behavior (Discountable, Taxable, ...)
│   └── Utils/                    # Helper/utility classes
├── resources/
│   ├── js/
│   │   ├── pages/                # Inertia page components (Home.vue, Product.vue, ...)
│   │   ├── components/           # Vue components (ui/ subfolder for shadcn-style lib)
│   │   ├── layouts/              # Layout components
│   │   ├── stores/              # Pinia stores (cartStore.ts, cartDrawerStore.ts)
│   │   ├── composables/         # Vue composables
│   │   ├── actions/             # Wayfinder controller-action functions (@/actions)
│   │   ├── routes/              # Wayfinder named-route functions (@/routes)
│   │   ├── lib/                 # Utilities (cn(), etc.)
│   │   ├── types/               # TypeScript type defs (index.d.ts)
│   │   └── wayfinder/           # Wayfinder bootstrap
│   └── css/                      # Tailwind v4 CSS-first stylesheets
├── routes/                       # web.php, console.php
├── database/                     # migrations, factories, seeders
├── tests/                        # Pest: Feature/, Unit/, Database/
└── config/ / public/ / storage/
```

---

## Backend (Laravel)

### Models

- **Naming:** Singular PascalCase (`Product`, `OrderItem`, `ProductVariant`).
- **Interfaces:** `Purchasable` (in `app/Models/Purchasable.php`) defines `dataforCart(): array` for cart-eligible items. Product & ProductVariant implement it.
- **Casts:** Use the `casts()` method (Laravel 13 style), NOT the `$casts` property.
- **Traits:** Split cross-cutting behavior into focused traits:
  - `Discountable`, `Taxable`, `Publishable`, `MoneyFormat`, `HasProductVariation`, `Metatags`.
- **Money pattern:** Stored as **integer cents**; `App\Casts\Money` converts to/from dollars.
  ```php
  protected function casts(): array
  {
      return [
          'price' => Money::class, // dollars in/out, cents at rest
          'status' => ProductStatus::class,
          'published_at' => 'datetime',
          'variant_options' => 'array',
      ];
  }
  ```
- **Rich content:** Uses Filament RichEditor `InteractsWithRichContent` / `HasRichContent` (register fields via `setUpRichContent()`).
- **Relations:** Explicit return types (`BelongsTo`, `BelongsToMany`, `HasMany`, `MorphTo`). Polymorphic cart items use `morphTo()`.
- **Scopes:** `scopeWithStock()`, `scopePublished()` (used by factories/traits).
- **Appends:** Computed `$appends` accessors (e.g. `price_in_dollars`, `has_discounts`).

### Controllers

- **Page controllers:** `{Name}PageController` with `__invoke(Request, ...$routeModelBinding): Response` returning `Inertia::render('PageName', [...])`.
  - Example route: `Route::get('/products/{product:slug}', ProductPageController::class)->name('storefront.product');`
- **Resource / API controllers:** `{Resource}Controller` with explicit action methods returning JSON (`CartController::create/show/empty`, `CartItemController::addOrUpdate/remove`).
- **Route model binding** via route key (`:slug`) is used throughout.
- All extend `App\Http\Controllers\Controller`.

### Form Requests

- Always create a Form Request class under `app/Http/Requests/` for validation.
  - `authorize(): bool` returns `true` (authorization handled via Policies/Shield elsewhere).
  - `rules(): array` holds validation; `StoreUserInfoEntryRequest`, `SendContactRequest` exist.

### Routing conventions (`routes/web.php`)

- Storefront routes are named with a `storefront.` prefix (`storefront.home`, `storefront.products`, `storefront.cart.create`, …).
- Public page routes use invokable controllers; cart/checkout routes are `POST` JSON endpoints.
- Protected routes wrapped in `->middleware([Authenticate::class])` group (checkout, orders, user-info).
- **Precognition** middleware (`HandlePrecognitiveRequests`) + **Honeypot** (`ProtectAgainstSpam`) applied to contact & user-info forms.
- Prefer named routes + `route()` in backend; prefer Wayfinder route functions in frontend.

### Filament Resources (v5)

Folder-per-resource, each containing `Pages/`, `Schemas/`, `Tables/`, `RelationManagers/`:

```
app/Filament/Resources/Products/
├── ProductResource.php
├── Pages/      (ListProducts, CreateProduct, EditProduct, ViewProduct, ManageProductVariants)
├── Schemas/    (ProductForm, ProductInfolist)
├── Tables/     (ProductsTable)
└── RelationManagers/ (VariantsRelationManager)
```

**Resource class pattern** (namespace is plural: `App\Filament\Resources\Products`):
```php
namespace App\Filament\Resources\Products;

use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('firesources.store'); // localization via firesources lang file
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema); // logic lives in Schemas/*Form.php
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
        return ['variants' => VariantsRelationManager::class];
    }
    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'view'  => ViewProduct::route('/{record}'),
            'edit'  => EditProduct::route('/{record}/edit'),
        ];
    }
}
```

**Key Filament v5 notes (from AGENTS.md):**
- Static `make()` component init; `Get $get` / `Set $set` for reactive fields; `->live(onBlur: true)` on text inputs.
- Layout via `Section`/`Grid` with explicit `->columnSpan()` / `->columnSpanFull()`.
- `Repeater::make('rel')->relationship()->schema(...)` (use `->schema()`, not `->fields()`).
- `Select::make('author_id')->relationship('author', 'name')` for BelongsTo (no `BelongsToSelect`).
- Correct namespaces: `Filament\Forms\Components\`, `Filament\Infolists\Components\`, `Filament\Schemas\Components\`, `Filament\Tables\Columns\`, `Filament\Tables\Filters\`, `Filament\Actions\`, `Filament\Support\Icons\Heroicon`.
- Preserve union property types (`$navigationIcon`, `$navigationGroup`, `$view`).
- `->visibility('public')` for public files; never `->dehydrated(false)` on fields that must be saved.
- Authorization via Filament Shield + `app/Policies/*Policy.php` (one policy per model).

---

## Frontend (Vue 3 + Inertia 3)

### Directory layout (`resources/js`)

- `pages/` — Inertia pages (`Home.vue`, `Product.vue`, `Checkout.vue`, …).
- `components/` — Vue components; `components/ui/` holds shadcn-style primitives (Button, Card, Dialog, …), each in its own folder with `index.ts` exporting the component + `cva` variants.
- `layouts/` — Layout components (`StorefrontLayout`, app shell).
- `stores/` — Pinia stores (`cartStore.ts`, `cartDrawerStore.ts`).
- `composables/` — Vue composables (`useAppearance`, `useCartItemQuantity`, …).
- `actions/` + `routes/` — **Wayfinder** generated TypeScript for controller actions (`@/actions`) and named routes (`@/routes`). Regenerate with `wayfinder:generate`.
- `lib/` — utilities (`cn()` from `clsx` + `tailwind-merge`).
- `types/` — TS type defs (`CartItem`, `CartAggregation`, `DataForCart`, …).

### Page component pattern

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import Hero from '@/components/home/Hero.vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const components = page.props.components as PageComponents;
</script>

<template>
    <div class="flex flex-col gap-24">
        <Hero :content="components.hero" />
    </div>
</template>
```

- **Single root element** per Vue component (Inertia/Vue rule).
- Props arrive as `page.props.*`; type them via `resources/js/types`.

### State management (Pinia)

```ts
import { create, show, empty } from '@/routes/cart';
import { addOrUpdate, remove } from '@/routes/cart/items';
import { useHttp } from '@inertiajs/vue3';
import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
    state: () => ({ id: '' as string, aggregation: {} as CartAggregation, items: [] as CartItem[] }),
    actions: {
        async addOrUpdateItem(data: DataForCart) {
            const http = useHttp({ ...data });
            await http.post(addOrUpdate().url);
            await this.getCartFromDB(this.id);
        },
    },
    getters: {
        isEmpty: (state) => state.items.length === 0,
    },
});
```

- **Inertia v3 `useHttp`** is the HTTP client (Axios is removed). Call `.post(routeFn().url)`.
- UUIDs via `uuid` package (`uuidv7`) for cart IDs.

### Wayfinder usage

```ts
import { show, create, empty } from '@/routes/cart';
import { addOrUpdate, remove } from '@/routes/cart/items';

await useHttp({ id: cartId }).post(create().url);
```

Import from `@/routes` (named routes) or `@/actions` (controller actions). Never hardcode URLs.

### UI component pattern (shadcn-style + Reka UI)

```vue
<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Primitive, type PrimitiveProps } from 'reka-ui';
import { buttonVariants } from '.';

interface Props extends PrimitiveProps {
    variant?: NonNullable<Parameters<typeof buttonVariants>[0]>['variant'];
    size?: NonNullable<Parameters<typeof buttonVariants>[0]>['size'];
}
const props = withDefaults(defineProps<Props>(), { as: 'button' });
</script>

<template>
    <Primitive :as="as" :class="cn(buttonVariants({ variant, size }), props.class)">
        <slot />
    </Primitive>
</template>
```

`index.ts` exports the component + `cva` variants (`class-variance-authority`). `cn()` = `twMerge(clsx(...))`.

---

## Testing (Pest PHP)

- **Suites:** `tests/Feature/`, `tests/Unit/`, `tests/Database/`. Most tests are Feature.
- **Bootstrap:** `tests/Pest.php` extends `Tests\TestCase`, applies `RefreshDatabase` to Feature+Unit, defines helper fns (`setStrictMode()`, `setDiscountCalculationMode()`) and custom expectations.
- **Declarations:** `test('descriptive name', fn () => ...)` or `it('...', fn () => ...)`.
- **Factories:** Always use model factories; leverage custom states (`Product::factory()->published()->create()`).
- **Common assertions:** `expect()->toBe()/->toHaveCount()`, `$this->assertDatabaseHas()`, `actingAs()`, `withCookie()`, `assertInertia()`, `Event::fake()`, `Mail::fake()`.
- **Helper fns** defined in test files for setup reuse (`createCartWithItem()`, `validParams()`); share state via `test()->cart = $cart`.
- **Filament tests:** use `pestphp/pest-plugin-livewire`; `livewire(ListProducts::class)`, `->fillForm()`, `->call('save')`, `->assertNotified()`, `->assertHasNoFormErrors()`. Acting as a user before panel tests.
- **Run:** `php artisan test --compact` (single file or `--filter=`).

---

## Styling (Tailwind CSS v4)

- **CSS-first config** — no `tailwind.config.js`. Uses `@import 'tailwindcss';` and `@theme inline { … }` in `resources/css/app.css`.
- Per-section stylesheets imported into `app.css` (`@import './header.css';` etc.).
- **Theme tokens** as CSS variables (`--color-background`, `--color-primary`, `--radius`, fonts `Raleway`/`Berkshire Swash`, …).
- `@plugin "@tailwindcss/typography";` for prose.
- `@source` directives point Tailwind at Blade + TS files (including vendor pagination views).
- Opacity modifier syntax (`bg-black/50`); utilities like `.wrapper`, `.section-spacing` defined under `@layer utilities`.
- Dark mode variant present but commented (`@custom-variant dark`).

---

## Key Project Patterns

### Money handling
Store integers (cents) in DB; cast with `App\Casts\Money`. Set in dollars, persists as cents. Accessor/appended helpers in `MoneyFormat` trait (`*_in_dollars`).

### Settings (Spatie)
`App\Settings\StorefrontSettings` (group `storefront`) exposes config + helpers (`isAppInStrictMode()`). Tests mutate via `app(StorefrontSettings::class)->save()`.

### Content transformation pipeline (`app/CMS`)
`ContentResolver` maps content `type` → `*Transformable` classes (products, collections, featured product, image, rich_text). Used to render dynamic storefront sections.

### Cart / polymorphic purchasables
`CartItem` is `morphTo()` a `Purchasable`. `Product` and `ProductVariant` implement `Purchasable::dataforCart()`. The cart store calls backend route functions (`@/routes/cart/items`) via `useHttp().post()`.

### Authorization
Filament Shield for admin roles + one Policy per model under `app/Policies/`. Controllers gate via `Authenticate` middleware; forms use Precognition + Honeypot.

### Localization
Filament labels use `__('firesources.*')` (see `lang/`). Navigation groups & model labels resolved through the `firesources` lang file.

---

## Naming & Organization Summary

| Type                   | Convention                | Example                                |
| ---------------------- | ------------------------- | -------------------------------------- |
| Models                 | Singular PascalCase       | `Product`, `OrderItem`                 |
| Interfaces             | PascalCase                | `Purchasable`                          |
| Traits                 | PascalCase, descriptive   | `Discountable`, `MoneyFormat`          |
| Enums                  | PascalCase                | `ProductStatus`, `StockControlModes`   |
| Page Controllers       | `{Name}PageController`    | `HomePageController`, `ProductPageController` |
| Resource Controllers   | `{Resource}Controller`    | `CartController`, `OrderController`    |
| Form Requests          | `{Verb}{Model}Request`    | `StoreUserInfoEntryRequest`            |
| Filament Resources     | `{Resource}Resource` (folder `Resources/{Plural}`) | `Products/ProductResource` |
| Filament Pages         | `List/Create/Edit/View{Resource}` | `ListProducts`, `EditProduct` |
| Settings               | `{Name}Settings`          | `StorefrontSettings`                   |
| Vue Pages / Components | PascalCase `.vue`         | `Home.vue`, `ProductCard.vue`          |
| UI lib components      | `components/ui/{name}/`   | `ui/button/Button.vue` + `index.ts`    |
| Pinia stores           | `camelCaseStore.ts`       | `cartStore.ts`, `cartDrawerStore.ts`   |
| Wayfinder routes/actions | generated `@/routes`, `@/actions` | `cart`, `cart/items` |
| Test files             | `PascalCaseTest.php`      | `AddToCartTest.php`                    |
| Test functions         | descriptive lowercase     | `test('can add product to cart')`      |

---

## Build & Verification Commands

```bash
# Frontend dev / build
npm run dev
npm run build              # production bundle (run if UI changes don't appear)
npm run lint               # eslint --fix
npm run format             # prettier --write resources/

# Backend dev
composer run dev           # serves app + queue + pail + vite concurrently
php artisan serve          # standalone

# Formatting / static
vendor/bin/pint --format agent        # Laravel Pint (run after editing PHP)
./vendor/bin/pest                     # or: php artisan test --compact

# Wayfinder (regenerate TS route/action bindings after route changes)
php artisan wayfinder:generate

# Database
php artisan migrate
php artisan tinker --execute '...'    # debugging (single quotes; double quotes inside)
```

### General rules (from AGENTS.md)
- Follow existing conventions; check sibling files before creating.
- Use descriptive names (`isRegisteredForDiscounts`, not `discount()`).
- Prefer existing components/utilities before writing new ones.
- Don't change dependencies or create new base folders without approval.
- Every change must be programmatically tested (Pest).
- Use `search-docs` before code changes; prefer Boost tools (`database-schema`, `get-absolute-url`, `browser-logs`).
- Site served by Herd at `https://toecommerce.test` (never run a server manually).
