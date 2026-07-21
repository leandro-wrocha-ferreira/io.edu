# AGENTS.md

## Project

Fresh CodeIgniter 3 project (no custom application code yet). PHP >= 5.3.7.

## Structure

- `index.php` — front controller; sets `ENVIRONMENT` via `CI_ENV` env var (default: `development`)
- `application/` — all app code: controllers, models, views, config, libraries, helpers
- `system/` — framework core (do not modify)
- Default controller: `Welcome` (maps to `/`)

## Dev commands

```bash
composer install          # install dev deps (phpunit 4-9, vfsstream)
composer test:coverage    # run tests with coverage (sqlite config)
```

No CI workflows, linting, or typecheck configured.

## Conventions

- **Indentation:** tabs (per `.editorconfig`)
- **Line endings:** LF
- **Charset:** UTF-8
- **Database:** `mysqli` driver, Query Builder enabled, no credentials set — configure `application/config/database.php` before use
- **Autoloads:** nothing auto-loaded by default (`application/config/autoload.php`)
- **Routing:** `translate_uri_dashes` is OFF; controller methods map directly to URL segments

## Gotchas

- `vendor/` is gitignored; must `composer install` before running tests
- `application/cache/*` and `application/logs/*` are gitignored (keep `index.html` placeholders)
- Post-install/update scripts patch `vfsStream.php` for PHP 8+ compat — don't skip `composer install`
