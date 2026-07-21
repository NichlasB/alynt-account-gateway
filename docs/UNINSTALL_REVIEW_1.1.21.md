# Alynt Account Gateway Uninstall Review

## Summary

- Uninstall file exists: Yes.
- WordPress uninstall guard: Yes, `WP_UNINSTALL_PLUGIN`.
- Capability check: Intentionally omitted. WordPress controls plugin deletion,
  and an additional current-user check could prevent legitimate WP-CLI cleanup.
- Preserve-data option: Not implemented. Deleting the plugin intentionally
  removes plugin-owned data; deactivation preserves it.
- Plugin-owned data categories: 4.
- Missing cleanup after this review: 0.
- Multisite: Site-by-site activation is supported; uninstall cleans every site
  in bounded batches of 100 blogs.

## Findings And Corrections

### Queued Webhook Events

- Severity: Medium.
- Created in: `includes/services/class-webhook-retry-scheduler.php`.
- Previous cleanup: Deactivation cleared delivery and retry hooks, but
  `uninstall.php` did not.
- Correction: Uninstall now clears
  `alynt_ag_deliver_account_created_webhook` and
  `alynt_ag_retry_account_created_webhook`.
- Regression evidence: `CleanupLifecycleTest` asserts both hooks are cleared.

### Multisite Site Data

- Severity: Medium.
- Created in: per-site activation through `includes/class-activator.php` and
  `includes/class-database.php`.
- Previous cleanup: Only the current blog prefix was removed.
- Correction: Multisite uninstall enumerates site IDs in batches of 100,
  switches to each blog, performs the same complete cleanup, and restores the
  previous blog after each site.
- Regression evidence: the multisite lifecycle test verifies independent
  option, table, transient/lock, and scheduled-event cleanup for two blogs.

## Complete Data Inventory

### Options

- `alynt_ag_settings`: removed.
- `alynt_ag_db_version`: removed.
- Dynamic `alynt_ag_lock_*` operation-lock options: pattern-removed.

The plugin does not create network options or site transients.

### Custom Tables

- `{prefix}alynt_ag_pending_registrations`: dropped.
- `{prefix}alynt_ag_webhook_logs`: dropped.
- `{prefix}alynt_ag_verification_logs`: dropped.
- `{prefix}alynt_ag_consent_records`: dropped.
- `{prefix}alynt_ag_audit_logs`: dropped.
- `{prefix}alynt_ag_diagnostics_logs`: dropped.

Table names come from the canonical database registry. A self-contained
fallback list preserves cleanup if that registry file is unavailable.

### Transients

- `alynt_ag_rl_*` rate-limit buckets: option and timeout rows removed.
- `alynt_ag_rl_meta_*` rate-limit metadata: option and timeout rows removed.

### Scheduled Events

- `alynt_ag_retention_cleanup`: cleared.
- `alynt_ag_retention_cleanup_continue`: cleared.
- `alynt_ag_deliver_account_created_webhook`: cleared.
- `alynt_ag_retry_account_created_webhook`: cleared.

### Data The Plugin Does Not Own

No plugin-owned post meta, user meta, term meta, custom post types,
taxonomies, roles, capabilities, upload directories, or generated files were
found.

Brand logos and gateway backgrounds are references to WordPress media-library
attachments selected by the site owner. Uninstall removes the references with
the settings option but retains the attachments. WordPress users,
WooCommerce customers and orders, standard profile fields, menus, and
third-party provider records also remain under their owning systems.

## Validation Boundary

Automated tests execute the uninstall script against deterministic WordPress
and database shims. Normal, reverse-order, and fixed-random PHPUnit runs each
pass with 527 tests and 3,687 assertions. PHPCS, the production build, npm and
Composer security audits, Composer validation, project-wide PHP syntax, and
Git diff hygiene also pass.

A final isolated real-WordPress uninstall smoke remains part of the
consolidated component-testing workflow after prompt 13; it must use disposable
plugin data and must not run against a production site.
