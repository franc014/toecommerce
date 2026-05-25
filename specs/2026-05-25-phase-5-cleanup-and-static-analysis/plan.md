# Phase 5: Cleanup Plan

## Changes

### `src/Services/DiscountService.php`
- Change `$calculationMode === DiscountCalculationModes::HIGHEST->value` → `$calculationMode === DiscountCalculationModes::HIGHEST`
- Change `$calculationMode === DiscountCalculationModes::SUM->value` → `$calculationMode === DiscountCalculationModes::SUM`
- Remove unreachable `return $product->price;` at end of `calculateDiscountedPrice()`
- Remove unreachable `return 0;` at end of `discountPercentage()`

### `src/Traits/Discountable.php`
- Remove unreachable `return 0;` at end of `discountPercentage()`
- Remove unreachable `return 0;` at end of `discountedPrice()`

### `src/Services/InventoryService.php`
- Remove `use JFA\ToecommerceCore\Models\ProductVariant;`

## Verification
- `vendor/bin/pest --compact`
- `vendor/bin/phpstan analyse src/Services/DiscountService.php src/Services/InventoryService.php src/Traits/Discountable.php --memory-limit=512M`
