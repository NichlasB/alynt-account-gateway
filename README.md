# Alynt Account Gateway

Alynt Account Gateway provides a branded account experience for WordPress login, registration, password management, account emails, dashboards, WooCommerce account handling, and account-related integrations.

The plugin is private Alynt-distributed software and includes `GitHub Plugin URI: NichlasB/alynt-account-gateway` for Alynt Plugin Updater compatibility.

## Current Status

This repository is in scaffold state. Frontend output is disabled by default and feature implementations are intentionally staged behind safe foundations.

## Development

```powershell
npm install
php ./composer.phar install
npm run build
npm run lint
npm test
```

If Composer is available globally, `composer install` can be used instead of `php ./composer.phar install`.

## Diagnostics

Diagnostics are disabled by default. Administrators can enable them under Settings -> Account Gateway -> Advanced / Tools. When enabled, the plugin stores structured events in a plugin-owned database table, redacts sensitive context before persistence/export, and provides admin-only recent-event viewing, CSV export, clear controls, and a small health summary.

## Documentation

- `docs/IMPLEMENTATION_PLAN.md`
- `docs/SCAFFOLD_MASTER_PROMPT.md`
- `docs/SETTINGS.md`
- `docs/HOOKS.md`
