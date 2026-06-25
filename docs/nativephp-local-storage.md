# NativePHP Local SQLite Storage

## Current Configuration

The mobile app has a dedicated SQLite connection named `mobile_local`.

- Connection: `mobile_local`
- Config: `config/database.php`, `config/mobile_local.php`
- Default database file: `storage/app/mobile/mobile-local.sqlite`
- Migration path: `database/migrations/mobile-local`
- Health command: `php artisan mobile:local-health`

The local database file is intentionally stored under `storage/app/mobile` so it is writable in a packaged NativePHP mobile runtime. Keep authentication tokens and other secrets in NativePHP secure storage, not in this SQLite database.

Laravel prepares the configured SQLite directory and empty database file during application boot. Schema changes are still applied explicitly with the mobile-local migration command below.

## Environment

No local override is required for development. The default path is generated with Laravel's `storage_path()` helper.

If a packaged runtime needs a custom location, set an absolute path:

```dotenv
NATIVEPHP_LOCAL_DB_DATABASE=/absolute/path/to/mobile-local.sqlite
NATIVEPHP_LOCAL_DB_FOREIGN_KEYS=true
```

Avoid relative override paths for `NATIVEPHP_LOCAL_DB_DATABASE`; SQLite paths are resolved from the process working directory and can drift between Herd, CLI, simulator, and packaged app contexts.

## Running Migrations

Run only the mobile-local migration path against the mobile-local connection:

```bash
php artisan migrate --database=mobile_local --path=database/migrations/mobile-local
```

New local-only tables should use the same path:

```bash
php artisan make:migration create_mobile_notes_table --create=mobile_notes --path=database/migrations/mobile-local --no-interaction
```

Set the migration connection explicitly:

```php
protected $connection = 'mobile_local';
```

Models that read or write those tables should also set:

```php
protected $connection = 'mobile_local';
```

## Health Check

After migrations run, verify read/write access:

```bash
php artisan mobile:local-health
```

The command writes a non-sensitive probe value to `mobile_local_health_checks`, reads it back through Eloquent, and exits with `0` only when the value matches.

Expected output includes:

```text
Connection: mobile_local
Database: /path/to/storage/app/mobile/mobile-local.sqlite
Migrations: /path/to/database/migrations/mobile-local
Mobile local SQLite storage can write and read data.
```

## NativePHP Simulator Checklist

Before launching a simulator or emulator build:

```bash
php artisan config:clear
php artisan migrate --database=mobile_local --path=database/migrations/mobile-local
php artisan mobile:local-health
```

Then run the NativePHP mobile command documented in `docs/nativephp-run.md` for the target platform.
