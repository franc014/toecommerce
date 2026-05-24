# Phase 2: Validation Criteria

## How to Verify This Phase Is Complete

---

## Check 1: Package No Longer Contains User Model

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Models/User.php 2>/dev/null
```

**Expected result:** File does not exist.

---

## Check 2: Package No Longer References App\Mail

**Command:**
```bash
grep -r "App\\Mail" /Users/franciscoandrade/Herd/toecommerce-core/src/ 2>/dev/null
```

**Expected result:** Empty output.

---

## Check 3: Package No Longer References App\Models\User

**Command:**
```bash
grep -r "App\\Models\\User" /Users/franciscoandrade/Herd/toecommerce-core/src/ 2>/dev/null
```

**Expected result:** Empty output.

**Command:**
```bash
grep -r "config('app.dashboard.allowed-admin-email')" /Users/franciscoandrade/Herd/toecommerce-core/src/ 2>/dev/null
```

**Expected result:** Empty output.

---

## Check 4: Contract and Trait Exist

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Contracts/ToecommerceUser.php
ls /Users/franciscoandrade/Herd/toecommerce-core/src/Concerns/HasToecommerceUser.php
```

**Expected result:** Both files exist.

---

## Check 5: Order Model Fires Event

**Command:**
```bash
grep -n "OrderStatusChanged" /Users/franciscoandrade/Herd/toecommerce-core/src/Models/Order.php
```

**Expected result:** Contains `event(new OrderStatusChanged(...))`.

**Command:**
```bash
grep -n "Mail::to" /Users/franciscoandrade/Herd/toecommerce-core/src/Models/Order.php
```

**Expected result:** No matches.

---

## Check 6: Host App User Implements Contract

**Command:**
```bash
grep -n "ToecommerceUser" /Users/franciscoandrade/Herd/toecommerce/app/Models/User.php
```

**Expected result:** Shows `implements ToecommerceUser` or `use HasToecommerceUser`.

---

## Check 7: Host App Has Event Listener

**Command:**
```bash
grep -rn "OrderStatusChanged" /Users/franciscoandrade/Herd/toecommerce/app/Providers/EventServiceProvider.php
```

**Expected result:** Shows listener registration.

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce/app/Listeners/SendOrderStatusChangedNotification.php
```

**Expected result:** File exists.

---

## Check 8: Host App Tests Pass

**Command:**
```bash
cd /Users/franciscoandrade/Herd/toecommerce && php artisan test --compact
```

**Expected result:** All tests pass.

---

## Merge Criteria

- [ ] Check 1: Package User model deleted.
- [ ] Check 2: No `App\Mail` references in package.
- [ ] Check 3: No `App\Models\User` or hardcoded host config references.
- [ ] Check 4: `ToecommerceUser` contract and `HasToecommerceUser` trait exist.
- [ ] Check 5: `Order` model fires event instead of sending mail.
- [ ] Check 6: Host app User implements contract.
- [ ] Check 7: Host app has event listener.
- [ ] Check 8: Host app tests pass.
