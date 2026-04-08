# Implementation Plan for Cart Flash Messages

## Overview

Add translated flash messages for cart operations and wire them up from backend to frontend.

## Changes Required

### 1. Translation Files

**Files:** `lang/en/storefront.php`, `lang/es/storefront.php`

Add these translation keys:

- `cart_item_added` - "Item added to cart" / "Producto agregado al carrito"
- `cart_item_removed` - "Item removed from cart" / "Producto eliminado del carrito"
- `cart_emptied` - "Cart emptied" / "Carrito vaciado"
- `cart_item_not_found` - "Product not found" / "Producto no encontrado"
- `cart_error` - "An error occurred while updating your cart" / "Ocurrió un error al actualizar tu carrito"

### 2. CartItemController

**File:** `app/Http/Controllers/CartItemController.php`

Changes:

- Line 45: Replace `'message' => 'very valid'` with translated message using `__('storefront.cart_item_added')`
- Lines 49-54: Add translation for error message
- Method `remove()`: Add return statement with JSON response and translated message

### 3. CartController (for emptyCart)

**File:** `app/Http/Controllers/CartController.php`

Check if empty cart method exists and add translated message to its JSON response.

### 4. cartStoreActions.ts

**File:** `resources/js/stores/cartStoreActions.ts`

Changes:

- `addOrUpdateItem()`: Return `response.data` after the axios call
- `removeItem()`: Return `response.data` after the axios call
- `emptyCart()`: Return `response.data` after the axios call

### 5. StorefrontLayout.vue

**File:** `resources/js/layouts/StorefrontLayout.vue`

Changes:

- Line 31: Replace hardcoded `'Item added to cart'` with `result.message`
- Line 44: Replace hardcoded `'Item removed from cart'` with `result.message`
- Add handler for `emptyCart` action if not present

## Dependencies

None - all changes are within existing codebase.

## Testing

1. Add item to cart - should show translated success message
2. Remove item from cart - should show translated success message
3. Empty cart - should show translated success message
4. Error scenarios - should show translated error messages
