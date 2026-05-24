# Phase 1: Validation Criteria

## How to Verify This Phase Is Complete

---

## Check 1: Factory Namespaces Are Correct

**Command:**
```bash
rg "use Database\\Factories" /Users/franciscoandrade/Herd/toecommerce-core/src/Models/
```

**Expected result:** No matches.

---

## Check 2: No Laravel Default Migrations Exist

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/database/migrations/ | grep "^0001_01_01"
```

**Expected result:** Empty output.

---

## Check 3: All Migrations Are `.stub` Files

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/database/migrations/*.php 2>/dev/null
```

**Expected result:** Empty output.

**Command:**
```bash
ls /Users/franciscoandrade/Herd/toecommerce-core/database/migrations/*.stub | wc -l
```

**Expected result:** At least 25 files.

---

## Check 4: Service Provider Uses `discoversMigrations()`

**Command:**
```bash
grep -n "discoversMigrations" /Users/franciscoandrade/Herd/toecommerce-core/src/ToecommerceCoreServiceProvider.php
```

**Expected result:** Match found in `configurePackage()`.

---

## Check 5: Config File Is Registered

**Command:**
```bash
grep -n "hasConfigFile" /Users/franciscoandrade/Herd/toecommerce-core/src/ToecommerceCoreServiceProvider.php
```

**Expected result:** Match found in `configurePackage()`.

---

## Check 6: Dead Code Removed

**Verification:**
- Confirm `src/ToecommerceCoreCommand.php` no longer exists.
- Confirm `src/Testing/TestsToecommerceCore.php` no longer exists.
- Confirm `stubs/` directory no longer exists.
- Confirm `src/ToecommerceCore.php` no longer exists.

---

## Check 7: Host App Tests Pass

**Command:**
```bash
cd /Users/franciscoandrade/Herd/toecommerce && php artisan test --compact
```

**Expected result:** All tests pass.

---

## Merge Criteria

- [ ] Check 1: No `Database\Factories` imports remain in package models.
- [ ] Check 2: No Laravel default migrations exist.
- [ ] Check 3: All migrations are `.stub` files.
- [ ] Check 4: Service provider uses `discoversMigrations()`.
- [ ] Check 5: Config file is registered.
- [ ] Check 6: Dead boilerplate code removed.
- [ ] Check 7: Host app tests pass.
