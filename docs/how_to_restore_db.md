# How to Restore Database from Backup

This guide documents the process to restore the PostgreSQL database from a local backup file using `pg_restore`.

## Prerequisites

- Docker containers must be running (Laravel Sail)
- PostgreSQL container (`toecommerce-pgsql-1`) must be healthy
- Backup files are stored in: `storage/app/private/backup-restore-temp/`

## Quick Restore Process

### Step 1: Find the Most Recent Backup

List available backups and identify the most recent one:

```bash
ls -lt storage/app/private/backup-restore-temp/*.zip | head -1
```

### Step 2: Drop and Recreate the Database

Since there are usually active connections, first terminate them, then drop and recreate:

```bash
# Terminate active connections
docker exec toecommerce-pgsql-1 psql -U sail -d postgres -c "
  SELECT pg_terminate_backend(pid)
  FROM pg_stat_activity
  WHERE datname = 'laravel' AND pid <> pg_backend_pid();
"

# Drop the database
docker exec toecommerce-pgsql-1 dropdb -U sail laravel

# Recreate the database
docker exec toecommerce-pgsql-1 createdb -U sail laravel
```

**Note:** If you get "database is being accessed by other users", the `pg_terminate_backend` command handles that.

### Step 3: Copy Backup to Container

Copy the database dump file from the extracted backup to the PostgreSQL container:

```bash
# Replace <backup-name> with the actual backup filename
docker cp storage/app/private/backup-restore-temp/<backup-name>/db-dumps/postgresql-main.backup \
  toecommerce-pgsql-1:/tmp/backup.backup
```

### Step 4: Restore the Database

Use `pg_restore` to restore the database:

```bash
docker exec toecommerce-pgsql-1 pg_restore -U sail -d laravel --verbose /tmp/backup.backup
```

#### Alternative: Using psql for plain SQL backups

If the backup is a plain SQL dump (not pg_dump format), use psql instead:

```bash
docker exec -i toecommerce-pgsql-1 psql -U sail -d laravel < \
  storage/app/private/backup-restore-temp/<backup-name>/db-dumps/postgresql-main.backup
```

### Step 5: Verify the Restore

Check that tables and data were restored:

```bash
# List tables
docker exec toecommerce-pgsql-1 psql -U sail -d laravel -c "\dt"

# Count products
docker exec toecommerce-pgsql-1 psql -U sail -d laravel -c "SELECT COUNT(*) FROM products;"

# Count users
docker exec toecommerce-pgsql-1 psql -U sail -d laravel -c "SELECT COUNT(*) FROM users;"
```

### Step 6: Cleanup

Remove the temporary backup file from the container:

```bash
docker exec toecommerce-pgsql-1 rm /tmp/backup.backup
```

## Expected Warnings (Safe to Ignore)

During restore, you may see errors like:

```
ERROR: role "neon_superuser" does not exist
ERROR: role "cloud_admin" does not exist
```

These are **safe to ignore**. They occur because the backup was created on a cloud PostgreSQL provider (like Neon) with specific roles that don't exist in your local Docker database. The actual data is restored correctly.

## Complete One-Liner Script

For quick reference, here's the complete process in sequence:

```bash
# Configuration
BACKUP_NAME="2026-04-04-09-00-06-0b1711cd-2d70-4e0f-b8cd-574baef6f813"

# Step 1: Terminate connections
docker exec toecommerce-pgsql-1 psql -U sail -d postgres -c \
  "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = 'laravel' AND pid <> pg_backend_pid();"

# Step 2: Drop and recreate database
docker exec toecommerce-pgsql-1 dropdb -U sail laravel
docker exec toecommerce-pgsql-1 createdb -U sail laravel

# Step 3: Copy backup
docker cp storage/app/private/backup-restore-temp/${BACKUP_NAME}/db-dumps/postgresql-main.backup \
  toecommerce-pgsql-1:/tmp/backup.backup

# Step 4: Restore
docker exec toecommerce-pgsql-1 pg_restore -U sail -d laravel /tmp/backup.backup

# Step 5: Verify
docker exec toecommerce-pgsql-1 psql -U sail -d laravel -c "SELECT COUNT(*) FROM products;"

# Step 6: Cleanup
docker exec toecommerce-pgsql-1 rm /tmp/backup.backup
```

## Troubleshooting

### "Database is being accessed by other users"

The `pg_terminate_backend` command in Step 2 handles this automatically.

### Permission Denied Errors

If you see permission errors, make sure you're using the `sail` user:

- Username: `sail`
- Password: `password` (defined in docker-compose.yml)

### pg_restore vs psql

- Use `pg_restore` for `.backup` files (custom format from pg_dump)
- Use `psql` for plain `.sql` files

### Restore Takes Too Long

For large databases, you can speed up by removing the `--verbose` flag or using `--jobs=4` for parallel restore (if supported).

## Related Commands

### View All Tables

```bash
docker exec toecommerce-pgsql-1 psql -U sail -d laravel -c "\dt"
```

### View Table Structure

```bash
docker exec toecommerce-pgsql-1 psql -U sail -d laravel -c "\d table_name"
```

### Reset Sequences (if needed after restore)

```bash
docker exec toecommerce-pgsql-1 psql -U sail -d laravel -c "
  SELECT setval('products_id_seq', (SELECT MAX(id) FROM products));
"
```

## Backup Configuration

Backups are created using `spatie/laravel-backup` package. Configuration is in:

- `config/backup.php`
- Environment variables in `.env`

Backup files include:

- Database dump (`db-dumps/postgresql-main.backup`)
- Application files (excluding vendor and node_modules)

---

**Last Updated:** 2026-04-04  
**Database:** PostgreSQL 17  
**Container:** toecommerce-pgsql-1
