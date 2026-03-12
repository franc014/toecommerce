# Plan: Implement Discount System for E-Commerce Platform

## Overview

Create a complete discount management system where admins can define discounts with name and date ranges, apply them to products via Filament, and choose how multiple discounts combine (highest vs. sum). The implementation follows existing patterns (Taxes) and uses the settings plugin for configuration.

---

## Step 1: Create Database Migrations

### 1.1: Create Discounts Table Migration
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_discounts_table.php`

Create `discounts` table with:
- `id` (primary key)
- `name` (string, unique) — Display label
- `percentage` (decimal 5,2) — Discount amount (0-100)
- `start_date` (datetime) — When discount begins
- `end_date` (datetime, nullable) — When discount expires (null = no expiry)
- `active` (boolean, default true) — Enable/disable flag
- `description` (text, nullable) — Admin notes
- `timestamps`

### 1.2: Create Discount-Product Pivot Table Migration
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_discount_product_table.php`

Create `discount_product` pivot table with:
- `id` (primary key)
- `discount_id` (foreign key, unsigned bigInteger)
- `product_id` (foreign key, unsigned bigInteger) — cascade on delete
- `created_at` (timestamp)

Add indexes on `discount_id` and `product_id`.

### 1.3: Update StorefrontSettings Database Migration
**File:** `database/settings/2025_11_10_194305_create_storefront_settings.php`

Add new settings entry:
```php
$this->migrator->add('storefront.discount_calculation_mode', 'highest');
```

This sets the default to 'highest' discount when multiple apply.

---

## Step 2: Create Models & Relationships

### 2.1: Create Discount Model
**File:** `app/Models/Discount.php`

Create model with:
- Relationship: `products()` BelongsToMany
- Method: `isCurrentlyActive()` — Check if active flag is true AND current date is between start/end dates
- Method: `isAppliedToProducts()` — Check if discount is currently applied to any product (for deletion protection)

Include factory trait: `HasFactory`

### 2.2: Create Discount Factory
**File:** `database/factories/DiscountFactory.php`

Generate discount test data with:
- `name` — unique sentence (e.g., "Summer Sale 2026")
- `percentage` — random 5-50
- `start_date` — today's date
- `end_date` — 30 days from today
- `active` — true
- `description` — paragraph

Include factory states for variations:
- `active()` — sets date range to current/future
- `inactive()` — sets active flag to false
- `expired()` — sets end_date to past date
- `indefinite()` — sets end_date to null

### 2.3: Update Product Model
**File:** `app/Models/Product.php`

Add:
- Relationship: `discounts()` BelongsToMany
- Scope or method: `activeDiscounts()` — Returns only discounts that are currently active (active=true AND within date range)
- Method: `getAppliedDiscount()` — Returns the discount based on StorefrontSettings logic:
  - If `discount_calculation_mode` = 'highest': return the single discount with highest percentage
  - If `discount_calculation_mode` = 'sum': return sum of all discounts (capped at 100%)
- Method: `getDiscountedPrice()` — Calculate final price after applying discount
- Method: `hasActiveDiscount()` — Boolean check for existence of active discount

---

## Step 3: Create Filament Resource for Discounts

### 3.1: Create DiscountResource
**File:** `app/Filament/Resources/DiscountResource.php`

Set up base resource:
- Model: `Discount`
- Title attribute: `name`
- Navigation group: "Store" (same as Products, Categories)
- Navigation icon: `Heroicon::OutlinedTicket`
- Record title attribute: `name`

### 3.2: Create DiscountForm Schema
**File:** `app/Filament/Resources/Schemas/DiscountForm.php` (or inline in DiscountResource)

Form fields:
- **TextInput** `name`
  - Required, unique, max 100 characters
- **TextInput** `percentage`
  - Required, numeric, min 0.01, max 100, suffix '%'
- **DateTimePicker** `start_date`
  - Required
  - Disable dates before today
- **DateTimePicker** `end_date`
  - Optional (nullable)
  - If provided, must be after `start_date`
- **Textarea** `description`
  - Optional, max 500 characters
- **Toggle** `active`
  - Default true
  - Controls enable/disable without deleting

**Important:** NO product selector in DiscountResource form. Products are assigned separately.

### 3.3: Create DiscountTable Schema
**File:** `app/Filament/Resources/Tables/DiscountTable.php` (or inline in DiscountResource)

Table columns:
- **TextColumn** `name`
  - Searchable, sortable
- **TextColumn** `percentage`
  - Sortable, show with % symbol
- **TextColumn** `start_date`
  - Format as dateTime, sortable
- **TextColumn** `end_date`
  - Format as dateTime, nullable, sortable
  - Toggleable (hidden by default)
- **IconColumn** `active`
  - Boolean icon, sortable
- **TextColumn** (computed) for status
  - Show: "Active", "Scheduled", "Expired", or "Inactive" based on dates and active flag

Record actions:
- `EditAction::make()`
- `DeleteAction::make()` with before callback to prevent deletion if currently applied

No bulk actions needed at resource level.

---

## Step 4: Add Discounts to Product Management

### 4.1: Add Row Action to ProductsTable
**File:** `app/Filament/Resources/Products/Tables/ProductsTable.php`

Add new action to `recordActions` array (after the 'taxes' action):

```php
Action::make('discounts')
    ->label(__('firesources.discounts'))
    ->icon(Heroicon::OutlinedTicket)
    ->color('success')
    ->schema([
        CheckboxList::make('discounts')
            ->label(__('firesources.discounts'))
            ->options(function () {
                // Query: Only active discounts (active=true AND within date range)
                return Discount::query()
                    ->where('active', true)
                    ->where('start_date', '<=', now())
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    })
                    ->pluck('name', 'id');
            })
    ])
    ->fillForm(function (Product $record) {
        return [
            'discounts' => $record->discounts()->pluck('id'),
        ];
    })
    ->action(function (Product $record, array $data) {
        $record->discounts()->sync($data['discounts']);
    })
    ->after(function () {
        return Notification::make()
            ->success()
            ->title(__('firesources.discounts_updated'))
            ->send();
    })
```

**Pattern:** Follow the exact same pattern as the existing 'taxes' action.

### 4.2: Add Bulk Action to ProductsTable Toolbar
**File:** `app/Filament/Resources/Products/Tables/ProductsTable.php`

Add new action to `toolbarActions` array (after the 'assignTaxes' action):

```php
BulkAction::make('assignDiscounts')
    ->label(__('firesources.assign_discounts'))
    ->icon(Heroicon::OutlinedTicket)
    ->color('success')
    ->schema([
        CheckboxList::make('discounts')
            ->label(__('firesources.discounts'))
            ->options(function () {
                // Query: Only active discounts
                return Discount::query()
                    ->where('active', true)
                    ->where('start_date', '<=', now())
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    })
                    ->pluck('name', 'id');
            })
            ->default([])
    ])
    ->action(function (array $data, Collection $records) {
        foreach ($records as $record) {
            $record->discounts()->sync($data['discounts']);
        }
    })
    ->after(function () {
        return Notification::make()
            ->success()
            ->title(__('firesources.discounts_assigned'))
            ->send();
    })
```

**Pattern:** Follow the existing 'assignTaxes' bulk action pattern exactly.

---

## Step 5: Update StorefrontSettings

### 5.1: Add Property to StorefrontSettings Class
**File:** `app/Settings/StorefrontSettings.php`

Add new public property:
```php
public string $discount_calculation_mode = 'highest';
```

This property will be persisted via the settings plugin.

### 5.2: Add Settings Field to ManageStorefront Page
**File:** `app/Filament/Pages/ManageStorefront.php`

Add import at top:
```php
use Filament\Forms\Components\Select;
```

Add new field to the form schema (after stock_control_mode):
```php
Select::make('discount_calculation_mode')
    ->label(__('firesources.discount_calculation_mode'))
    ->options([
        'highest' => __('firesources.use_highest_discount'),
        'sum' => __('firesources.sum_discounts'),
    ])
    ->default('highest')
    ->required()
    ->helperText(__('firesources.discount_calculation_mode_help'))
```

### 5.3: Add Validation for Sum Mode
**File:** `app/Settings/StorefrontSettings.php` or validation logic in controllers

Add method to StorefrontSettings:
```php
public function validateDiscountSum(): bool
{
    if ($this->discount_calculation_mode !== 'sum') {
        return true;
    }

    // Validate that active discounts don't exceed 100% when summed
    $totalDiscount = Discount::where('active', true)
        ->where('start_date', '<=', now())
        ->where(function ($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
        })
        ->sum('percentage');

    return $totalDiscount <= 100;
}
```

This validation should run when:
- Creating/updating a discount
- Changing the calculation mode to 'sum'
- Syncing discounts to a product in 'sum' mode

---

## Step 6: Add Deletion Protection

### 6.1: Protect Delete in DiscountResource
**File:** `app/Filament/Resources/DiscountResource.php`

Modify DeleteAction in table configuration:
```php
->before(function (Discount $record) {
    if ($record->isAppliedToProducts()) {
        throw new \Exception(
            __('firesources.cannot_delete_active_discount')
        );
    }
})
```

This prevents admins from deleting discounts currently applied to any product.

---

## Step 7: Add Translation Keys

Add to language files (`lang/en/firesources.php` and `lang/es/firesources.php`):

```php
'discount' => 'Discount',
'discounts' => 'Discounts',
'discounts_updated' => 'Discounts updated successfully',
'discounts_assigned' => 'Discounts assigned to products',
'assign_discounts' => 'Assign Discounts',
'discount_calculation_mode' => 'Discount Calculation Mode',
'use_highest_discount' => 'Use Highest Discount',
'sum_discounts' => 'Sum All Discounts',
'discount_calculation_mode_help' => 'Choose whether to apply only the highest discount or sum all applicable discounts (capped at 100%)',
'cannot_delete_active_discount' => 'Cannot delete a discount that is currently applied to products',
```

---

## Step 8: Write Tests

### 8.1: Create DiscountFeatureTest
**File:** `tests/Feature/DiscountFeatureTest.php`

Test cases:
1. Discount can be created via Filament form
2. Only active discounts appear in product assignment forms
3. Expired discounts don't appear in forms
4. Future-scheduled discounts don't appear in forms
5. Row action syncs discounts to single product
6. Bulk action syncs discounts to multiple products
7. `Product::activeDiscounts()` returns only currently active discounts
8. `Product::getAppliedDiscount()` returns highest when mode='highest'
9. `Product::getAppliedDiscount()` returns sum when mode='sum'
10. Sum mode caps discount at 100% max
11. Cannot delete discount applied to products
12. Can delete discount not applied to any products
13. Inactive discounts don't apply even if within date range
14. `isCurrentlyActive()` method works correctly

---

## Summary of Files to Create/Modify

### Files to Create:
- `database/migrations/YYYY_MM_DD_HHMMSS_create_discounts_table.php`
- `database/migrations/YYYY_MM_DD_HHMMSS_create_discount_product_table.php`
- `database/factories/DiscountFactory.php`
- `app/Models/Discount.php`
- `app/Filament/Resources/DiscountResource.php`
- `app/Filament/Resources/Schemas/DiscountForm.php` (optional, can be inline)
- `app/Filament/Resources/Tables/DiscountTable.php` (optional, can be inline)
- `tests/Feature/DiscountFeatureTest.php`

### Files to Modify:
- `database/settings/2025_11_10_194305_create_storefront_settings.php` — Add discount_calculation_mode setting
- `app/Models/Product.php` — Add discount relationship and methods
- `app/Settings/StorefrontSettings.php` — Add discount_calculation_mode property
- `app/Filament/Pages/ManageStorefront.php` — Add discount mode selector
- `app/Filament/Resources/Products/Tables/ProductsTable.php` — Add row action and bulk action
- `lang/en/firesources.php` — Add translation keys
- `lang/es/firesources.php` — Add translation keys

---

## Implementation Flow

1. **Database First:** Create migrations (Step 1)
2. **Model Layer:** Create Discount model and update Product model (Step 2)
3. **Filament Admin:** Create DiscountResource (Step 3)
4. **Product Integration:** Add row and bulk actions (Step 4)
5. **Settings:** Add discount calculation mode (Step 5)
6. **Validation:** Add deletion protection (Step 6)
7. **Localization:** Add translation keys (Step 7)
8. **Testing:** Write tests to verify (Step 8)

---

This plan is ready for implementation following the established patterns in the codebase!
