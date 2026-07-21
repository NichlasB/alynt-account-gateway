# Alynt Account Gateway Database Review

Date: 2026-07-20

## Scope

This record covers Phase 1 and Phase 2 of
`05-DATABASE_REVIEW_PROMPT.md` against the immutable public v1.1.21 baseline.
The review inventoried all plugin-owned tables, query patterns, WordPress data
storage, relationships, lifecycle operations, retention policies, privacy
callbacks, uninstall behavior, and schema migration behavior.

All corrective work is isolated on
`prerelease/v1.1.21-revalidation` and targets a separately approved v1.1.22
candidate. No package was published and no LocalWP, staging, or production
site was changed.

## Inventory Summary

- Custom tables: 6.
- Query patterns: 51, including 45 operation sites and 6 table definitions.
- Post-meta keys: 0.
- Core-owned user-meta keys: 3 (`first_name`, `last_name`, `_new_email`).
- Unique options read: 6; plugin-owned options: `alynt_ag_settings` and
  `alynt_ag_db_version`.
- Plugin transient families: rate-limit counters and privacy-preserving
  rate-limit metadata.
- Schema issues: 0.
- query-security issues: 0.
- Approved data-integrity and lifecycle findings: 7.
- Approved index recommendations: 1.

## Custom Tables

### Pending Registrations

Table: `{$wpdb->prefix}alynt_ag_pending_registrations`

Stores pending email-confirmation registrations, hashed confirmation tokens,
same-site return paths, lifecycle status, optional created-user relationship,
and UTC timestamps.

Indexes:

- primary key `id`
- `user_id`
- `email`
- `token_hash`
- `status`
- `expires_at`

### Webhook Logs

Table: `{$wpdb->prefix}alynt_ag_webhook_logs`

Stores account-created webhook delivery metadata, bounded retry state,
optional debug payloads, response status, and UTC creation time.

Indexes:

- primary key `id`
- `event_name`
- `user_id`
- `success`
- `created_at`
- `success_created_at` after the v0.1.7 schema migration

### Verification Logs

Table: `{$wpdb->prefix}alynt_ag_verification_logs`

Stores Turnstile, Reoon, rate-limit, and registration-flow outcomes plus
manual review decisions.

Indexes:

- primary key `id`
- `email`
- `provider`
- `status`
- `review_decision`
- `reviewed_at`
- `created_at`

### Consent Records

Table: `{$wpdb->prefix}alynt_ag_consent_records`

Stores registration consent evidence, configured legal paths, settings
fingerprint, plugin version, optional WordPress user relationship, and UTC
creation time.

Indexes:

- primary key `id`
- `user_id`
- `email`
- `created_at`

### Audit Logs

Table: `{$wpdb->prefix}alynt_ag_audit_logs`

Stores privacy-conscious administrator and system audit events.

Indexes:

- primary key `id`
- `user_id`
- `action`
- `created_at`

### Diagnostics Logs

Table: `{$wpdb->prefix}alynt_ag_diagnostics_logs`

Stores severity-filtered, redacted operational diagnostics with optional
correlation identifiers.

Indexes:

- primary key `id`
- `level`
- `category`
- `event_code`
- `created_at`

All six tables use `$wpdb->prefix`, `dbDelta()`,
`$wpdb->get_charset_collate()`, unsigned bigint primary keys, and explicit
uninstall removal.

## Implemented Changes

### Registration Write Integrity

Files:

- `includes/services/class-registration-pending-store.php`
- `includes/services/class-registration-confirmation.php`
- `includes/services/class-registration-completion.php`
- `includes/services/class-privacy-service.php`

Creating a pending registration now:

- treats an existing-row cleanup database error as a failed request
- captures the pending-registration insert ID before inserting consent
- removes the newly inserted pending row if consent persistence fails
- records a bounded critical diagnostic if compensating deletion fails
- returns the captured pending ID instead of a later related insert ID

State transitions that must change persisted data now require one or more
affected rows. Confirmation remains concurrency-safe and idempotent: the
update is conditional on `status = pending`, and a zero-row result is accepted
only after a fresh token lookup confirms that another request already moved
the still-valid row to `email_confirmed`.

Token renewal, account creation, pending-state restoration, and consent
attachment no longer accept zero affected rows as success.

### Lifecycle Status And UTC Consistency

File:

- `admin/settings-page/class-security-review-ui.php`

The security dashboard now recognizes the production
`account_created` status as completed while retaining the historical
`completed` alias for compatibility. Expiry comparisons now use UTC, matching
the UTC timestamps persisted by registration services.

### Rate-Limit Read Failure Visibility

File:

- `admin/settings-page/class-security-rate-limits.php`

Failed options-table reads now return `WP_Error` through the active-bucket
summary path. The Security & Spam screen renders the sanitized database error
instead of silently presenting a zero-bucket healthy state.

### Bounded Privacy Export And Erasure

Files:

- `includes/services/class-privacy-exporter.php`
- `includes/services/class-privacy-eraser.php`

Privacy exports now:

- normalize the WordPress callback page
- read at most 100 rows from each relevant table per page
- use stable `created_at DESC, id DESC` ordering
- use deterministic `LIMIT` and `OFFSET`
- report `done = false` while any table fills the current page

Privacy erasure now:

- selects at most 101 matching IDs per target
- deletes at most 100 IDs per target and callback invocation
- always starts from the first remaining IDs instead of offsetting rows that
  disappear during erasure
- reports `done = false` while more records remain or a query failed
- distinguishes removed data from retained data after partial failure

This design keeps each callback bounded and avoids skipping records after
earlier pages are deleted.

### Bounded Retention Cleanup

Files:

- `includes/class-retention-cleanup.php`
- `includes/class-deactivator.php`
- `uninstall.php`

Each retention policy now deletes at most 500 rows per query. If any query
reaches that cap, one non-duplicated
`alynt_ag_retention_cleanup_continue` event is scheduled one minute later.
Deactivation and uninstall both clear the continuation hook.

This trades immediate backlog removal for shorter database locks and bounded
cron requests. The daily retention schedule remains unchanged.

### Webhook Retention Index

File:

- `includes/class-database.php`

The webhook table now includes:

```sql
KEY success_created_at (success, created_at)
```

This index matches the two retention queries that filter first by delivery
success and then by creation time. Existing single-column indexes remain for
other query shapes.

Database schema version `0.1.6` is bumped to `0.1.7`. The existing idempotent
`dbDelta()` installation path adds the index on the next request that detects
an older stored schema version.

## Design Decisions

- No foreign-key constraints were added to WordPress users. WordPress plugin
  tables commonly use soft relationships so user deletion and multisite table
  lifecycle remain under application control.
- No transaction requirement was added. The registration path uses checked
  writes and explicit compensation, preserving compatibility with WordPress
  hosts and table configurations that cannot guarantee transactional storage.
- Settings remain autoloaded because frontend routing needs them on active
  requests.
- Existing single-column indexes were retained; the review avoided speculative
  over-indexing of bounded, retention-managed admin and log queries.
- Direct SQL remains justified for plugin-owned tables, aggregate transient
  inspection, schema verification, privacy batching, retention, and uninstall.

## Migration And Rollback

Migration required: yes.

- From schema version: `0.1.6`.
- To schema version: `0.1.7`.
- Change: add `success_created_at (success, created_at)` through `dbDelta()`.
- Data transformation: none.
- Large-table impact: adding an index may briefly lock the webhook log table,
  depending on the database engine and host.
- Multisite: each site installation continues to use its own `$wpdb->prefix`
  tables and stored schema option.
- Rollback: automatic schema rollback is not supported by the existing
  migration system. Reverting plugin code leaves the additional index in
  place, which is backwards compatible and does not alter stored row data.

## Validation Evidence

- Focused database regression suites: 63 tests and 306 assertions passed.
- Production asset build: passed.
- POT generation: 1,140 strings.
- PHPCS/WPCS and PHP compatibility lint: passed.
- PHP syntax: 225 files passed.
- JavaScript/MJS syntax: 19 files passed, including build scripts.
- PHPUnit: 473 tests and 3,385 assertions passed.
- npm advisory audit: zero vulnerabilities.
- Composer advisory audit: no advisories.
- Source ceilings: 122 production PHP files at or below 300 lines, 17 source
  JavaScript/MJS files at or below 250 lines, and 14 source CSS files at or
  below 500 lines.
- `git diff --check`: passed for the reviewed source.

## Outcome

All approved Database Review findings are fixed. There are no deferred schema,
query-security, data-integrity, privacy-pagination, retention-batching, or
uninstall findings from this prompt. The published v1.1.21 release remains
unchanged; these corrections belong to a later, separately approved v1.1.22
candidate.

Next ordered workflow:
`06-PERFORMANCE_REVIEW_PROMPT.md`.
