# Phase 3: Validation Criteria

## How to Verify This Phase Is Complete

These checks must all pass before this phase can be merged.

---

## Check 1: Services Exist

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Services/CartService.php
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Services/OrderService.php
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Services/PaymentService.php
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Services/InventoryService.php
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Services/DiscountService.php
```

**Expected result:** All files exist.

---

## Check 2: Services Registered

**Command:**
```bash
grep -n "CartService::class\|OrderService::class\|PaymentService::class\|InventoryService::class\|DiscountService::class" /Users/franciscoandrade/Herd/toecommerce-core/src/ToecommerceCoreServiceProvider.php
```

**Expected result:** Shows singleton registrations.

---

## Check 3: Model Delegates Exist

**Command:**
```bash
grep -n "@deprecated" /Users/franciscoandrade/Herd/toecommerce-core/src/Models/Cart.php
```

**Expected result:** Shows deprecated annotations on old methods.

**Command:**
```bash
grep -n "@deprecated" /Users/franciscoandrade/Herd/toecommerce-core/src/Models/Order.php
```

**Expected result:** Shows deprecated annotations on old methods.

---

## Check 4: No Fatal Errors

**Command:** (in host app)
```bash
cd /Users/franciscoandrade/Herd/toecommerce
php artisan test --compact
```

**Expected result:** All tests pass.

---

## Merge Criteria

- [ ] Check 1: All 5 service files exist.
- [ ] Check 2: Services registered as singletons.
- [ ] Check 3: Model delegates exist with @deprecated.
- [ ] Check 4: Host app tests pass.
- [ ] Git diff reviewed: only intended files changed.
