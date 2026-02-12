# OrderInfoList Admin Action Implementation Plan

## Goal

Add an admin-only "Confirm Payment" action in the summary section of OrderInfoList that allows administrators to mark orders as paid.

## Changes Required

### 1. OrderInfolist.php

Add a header action to the summary Section with the following specifications:

**Action Details:**

- Name: `confirm-payment`
- Label: `__('firesources.confirm_payment')`
- Icon: `Heroicon::CheckCircle`
- Color: `success` (green)
- Location: Section header actions (right side of "Summary" heading)

**Visibility Logic (hidden callback):**

```php
->hidden(function ($record) {
    // Only visible in admin panel
    if (Filament::getCurrentPanel()->getId() !== 'admin') {
        return true;
    }
    // Only if order has items
    if (!$record->hasItems()) {
        return true;
    }
    // Only if not paid yet
    return $record->isConfirmed();
})
```

**Confirmation Modal:**

- `requiresConfirmation()`
- `modalHeading(__('firesources.confirm_payment'))`
- `modalDescription(__('firesources.confirm_payment_description'))`
- `modalSubmitActionLabel(__('firesources.confirm'))`

**Action Handler:**

```php
->action(function ($record) {
    $record->markAsPaid();
})
->successNotificationTitle(__('firesources.order_marked_as_paid'))
```

**Implementation:**
Add to the summary Section, before the schema array:

```php
->headerActions([
    Action::make('confirm-payment')
        ->label(__('firesources.confirm_payment'))
        ->icon(Heroicon::CheckCircle)
        ->color('success')
        ->requiresConfirmation()
        ->modalHeading(__('firesources.confirm_payment'))
        ->modalDescription(__('firesources.confirm_payment_description'))
        ->modalSubmitActionLabel(__('firesources.confirm'))
        ->hidden(function ($record) {
            if (Filament::getCurrentPanel()->getId() !== 'admin') {
                return true;
            }
            if (!$record->hasItems()) {
                return true;
            }
            return $record->isConfirmed();
        })
        ->action(function ($record) {
            $record->markAsPaid();
        })
        ->successNotificationTitle(__('firesources.order_marked_as_paid')),
])
```

### 2. Translation Keys Required

Add to `resources/lang/en/firesources.php` and `resources/lang/es/firesources.php`:

- `confirm_payment` - "Confirm Payment" / "Confirmar Pago"
- `confirm_payment_description` - "Are you sure you want to mark this order as paid?" / "¿Estás seguro de que deseas marcar este pedido como pagado?"
- `confirm` - "Confirm" / "Confirmar"
- `order_marked_as_paid` - "Order marked as paid successfully" / "Pedido marcado como pagado exitosamente"

## Testing Checklist

- [ ] Action only visible in admin panel
- [ ] Action hidden when order has no items
- [ ] Action hidden when order is already paid
- [ ] Confirmation modal appears on click
- [ ] Clicking confirm marks order as paid (paid_at = now())
- [ ] Success notification appears
- [ ] Page refreshes to show updated paid_at timestamp
