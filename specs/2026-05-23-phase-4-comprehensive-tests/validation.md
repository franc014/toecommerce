# Phase 4: Validation Criteria

## How to Verify This Phase Is Complete

---

## Check 1: Service Tests Exist

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Services/CartServiceTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Services/OrderServiceTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Services/PaymentServiceTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Services/InventoryServiceTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Services/DiscountServiceTest.php
```

**Expected result:** All exist.

---

## Check 2: Model Tests Moved

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Models/CartTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Models/OrderTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Models/ProductTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Models/DiscountTest.php
ls /Users/franciscoandrade/Herd/toecommerce-core/tests/Unit/Models/CartItemTest.php
```

**Expected result:** All exist.

---

## Check 3: Package Tests Pass

**Command:**
```bash
cd /Users/franciscoandrade/Herd/toecommerce-core && composer test
```

**Expected result:** All pass.

---

## Check 4: Host App Tests Pass

**Command:**
```bash
cd /Users/franciscoandrade/Herd/toecommerce && php artisan test --compact
```

**Expected result:** All pass.

---

## Merge Criteria

- [ ] Check 1: Service tests exist.
- [ ] Check 2: Model tests moved.
- [ ] Check 3: Package tests pass.
- [ ] Check 4: Host app tests pass.
