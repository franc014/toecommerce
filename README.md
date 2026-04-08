# ToEcommerce

A full-featured, modern e-commerce platform built with Laravel 12, Vue 3, and Filament v4.

## Overview

ToEcommerce is a complete online store solution featuring a robust product catalog, shopping cart, checkout flow, and comprehensive admin panel. Built with modern technologies and best practices, it provides a scalable foundation for online retail businesses.

## Core Features

### E-Commerce Functionality

- **Product Catalog** - Products organized by categories and collections
- **Product Variants** - Support for size, color, and other attribute variations
- **Shopping Cart** - Polymorphic cart system handling both products and variants
- **Checkout Flow** - Multi-step checkout with user authentication
- **Order Management** - Complete order lifecycle from creation to fulfillment
- **Payment Processing** - Integrated with Payphone payment gateway
- **Tax System** - Configurable tax rates applied to products
- **Discounts & Promotions** - Percentage-based discounts with flexible calculation modes

### Content Management

- **CMS Pages** - Dynamic pages with reusable section blocks
- **Rich Content Editor** - Custom blocks including hero sections and CTAs
- **Menu Management** - Hierarchical navigation menus
- **Media Library** - Image management via Spatie Media Library

### Admin Panel (Filament v4)

- Full management interface for products, orders, and users
- Storefront settings configuration
- Export/Import capabilities
- Role-based access control (Filament Shield)

## Technology Stack

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

## Architecture Highlights

### Money Handling

Stores currency as cents (integers) using a custom `Money` cast to avoid floating-point errors:

```php
// Stored as 1999 cents, accessed as $19.99
$product->price = 19.99;
```

### Settings Pattern

Configurable store behavior using Spatie Settings:

- Stock control modes (strict vs lenient)
- Products per page for pagination
- Discount calculation modes

### Polymorphic Cart System

Single cart handles both products and variants via the `Purchasable` interface:

```php
$cart->addOrUpdateItem($product, $quantity);
$cart->addOrUpdateItem($productVariant, $quantity);
```

### Content Transformation Pipeline

CMS content is processed through transformer classes for flexible content blocks.

## Project Structure

```
toecommerce/
├── app/
│   ├── Casts/              # Custom Eloquent casts (Money)
│   ├── CMS/                # Content management transformers
│   ├── Enums/              # PHP Enums
│   ├── Filament/           # Admin panel resources
│   ├── Http/
│   │   ├── Controllers/    # Page & API controllers
│   │   └── Requests/       # Form request validation
│   ├── Models/             # Eloquent models
│   ├── Settings/           # Spatie Settings classes
│   ├── Traits/             # Reusable traits (Discountable, Taxable, etc.)
│   └── Utils/              # Utility classes
├── resources/
│   ├── js/
│   │   ├── pages/          # Inertia.js page components
│   │   ├── components/     # Vue components
│   │   │   └── ui/         # shadcn/vue UI library
│   │   ├── stores/         # Pinia state management
│   │   └── routes/         # Wayfinder generated routes
│   └── css/                # Tailwind CSS stylesheets
├── tests/
│   ├── Feature/            # Feature tests
│   └── Unit/               # Unit tests
└── routes/
    └── web.php             # Web routes
```

## Development

### Prerequisites

- PHP 8.4+
- Node.js 18+
- Composer
- MySQL/PostgreSQL

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd toecommerce

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed
```

### Development Server

```bash
# Start all development servers
composer run dev

# Or run individually
php artisan serve
npm run dev
```

### Testing

```bash
# Run all tests
php artisan test --compact

# Run specific test file
php artisan test --compact tests/Feature/AddToCartTest.php

# Run with filter
php artisan test --compact --filter=can_add_product
```

### Code Quality

```bash
# Format PHP code
vendor/bin/pint --dirty

# Lint JavaScript/TypeScript
npm run lint

# Format JavaScript/TypeScript
npm run format
```

### Production Build

```bash
npm run build
```

## Key Components

### Models

- **Product** - Core product entity with variants, categories, and collections
- **ProductVariant** - Product variations with attributes
- **Cart** - Shopping cart with items and aggregation
- **CartItem** - Polymorphic cart items (products or variants)
- **Order** - Customer orders with line items
- **Category** - Product categorization
- **ProductCollection** - Curated product groups
- **Discount** - Promotional discounts
- **Tax** - Tax rates

### Traits

- `Discountable` - Discount calculation logic
- `Taxable` - Tax calculation logic
- `Publishable` - Publication status management
- `MoneyFormat` - Currency formatting

### Frontend State

- **cartStore** - Shopping cart state management
- **cartDrawerStore** - Cart drawer visibility state

## Configuration

Key settings are managed through the `StorefrontSettings` class:

```php
// config/settings.php or via Filament admin
'storefront' => [
    'products_per_page' => 12,
    'stock_control_mode' => 'strict', // or 'lenient'
    'discount_calculation_mode' => 'highest', // or 'sum', 'stacked'
],
```

## Testing

The project includes comprehensive test coverage:

- **14 Feature Tests** - HTTP endpoints, form submissions, cart operations
- **13 Unit Tests** - Model behavior, business logic, relationships

Test patterns use Pest PHP with factory states:

```php
test('can add published product to cart', function () {
    $product = Product::factory()->published()->create();
    // ... test logic
});
```

## License

MIT License

## Credits

Built with [Laravel](https://laravel.com), [Filament](https://filamentphp.com), [Vue](https://vuejs.org), and [Inertia.js](https://inertiajs.com).

---

_Generated from codebase analysis - Last updated: 2026-02-09_
