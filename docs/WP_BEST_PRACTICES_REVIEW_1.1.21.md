# Alynt Account Gateway WordPress Best Practices Review

Date: 2026-07-20

## Scope

This record covers Phase 1 and Phase 2 of
`04-WP_BEST_PRACTICES_REVIEW_PROMPT.md` against the immutable public v1.1.21
baseline. The review inspected 225 tracked PHP files for WordPress-specific
API, hook, database, option, transient, meta, AJAX, REST, asset, path, header,
structure, coding-standard, and PHP-compatibility concerns.

All corrective work is isolated on
`prerelease/v1.1.21-revalidation` and is intended for a separately approved
v1.1.22 candidate. No package was published and no LocalWP, staging, or
production site was changed.

## Phase 1 Result

The read-only inventory found seven actionable issues:

- one database table-existence query used `LIKE` without escaping SQL wildcard
  characters in the table name
- five `get_option()` calls relied on the implicit `false` default instead of
  declaring a type-appropriate fallback
- one legacy gateway-preview path added the plugin version to Cloudflare's
  canonical Turnstile API URL

No deprecated WordPress or PHP functions, hook issues, meta-key issues, AJAX
authorization issues, REST routes, hardcoded plugin paths, incomplete plugin
headers, or coding-standard violations were found.

## Implemented Fixes

### Database Table Verification

File:

- `includes/class-database.php`

The installation verification query now passes the plugin table name through
`$wpdb->esc_like()` before preparing `SHOW TABLES LIKE`. Underscores and other
SQL wildcard characters therefore remain literal, preventing a similarly
named table from being mistaken for the required plugin-owned table.

### Explicit Option Defaults

Files:

- `includes/class-database.php`
- `admin/settings-page/class-security-failure-triage.php`
- `admin/settings-page/class-settings-transfer.php`

Database-version reads now default to an empty string, settings verification
defaults to an empty array, and date/time formatting defaults to `Y-m-d` and
`H:i`. Existing saved values and normal WordPress behavior remain unchanged,
while missing or malformed environment defaults have deterministic types.

### Canonical Turnstile Script URL

File:

- `admin/settings-page/class-gateway-preview.php`

The legacy authenticated preview fallback now suppresses WordPress's `ver`
query parameter for the Cloudflare Turnstile script, matching the primary
frontend asset loader and Cloudflare's canonical API URL requirement.

## Reviewed Exceptions

- `wp_ajax_alynt_ag_preview_gateway` intentionally returns a complete,
  nonce-protected preview document rather than a JSON payload. It is retained
  as a documented compatibility fallback and exits after rendering.
- Core user-meta keys `first_name`, `last_name`, and `_new_email` intentionally
  remain unprefixed because they are WordPress-owned fields.
- Plugin settings remain autoloaded because the gateway reads them during
  frontend routing; they are active configuration rather than archival data.
- Direct SQL remains limited to plugin-owned tables, aggregate transient
  inspection, schema verification, and uninstall cleanup where no equivalent
  WordPress CRUD API exists.
- External URLs are limited to documented Cloudflare and Reoon API endpoints
  and the GPL license URI.

## Validation Evidence

- Production asset build: passed.
- Stable POT generation: 1,137 strings; timestamp-only churn removed.
- PHPCS/WPCS and PHP compatibility lint: passed.
- PHP syntax: 225 files passed.
- JavaScript/MJS syntax: 17 files passed.
- PHPUnit: 457 tests and 3,334 assertions passed.
- npm advisory audit: zero vulnerabilities.
- Composer advisory audit: no advisories.
- Source ceilings: zero production PHP files over 300 lines, zero source
  JavaScript files over 250 lines, and zero source CSS files over 500 lines.
- `git diff --check`: passed for the reviewed source.

## Outcome

All seven approved Phase 1 findings are fixed. There are no deferred WordPress
Best Practices Review findings and no known release-blocking issue from this
prompt. The published v1.1.21 release remains unchanged; these corrections
belong to a later, separately approved v1.1.22 candidate.

Next ordered workflow:
`05-DATABASE_REVIEW_PROMPT.md`.
