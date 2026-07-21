# Alynt Account Gateway Error Handling Review

Date: 2026-07-20

## Scope

This record covers Phase 1 and Phase 2 of
`03-ERROR_HANDLING_REVIEW_PROMPT.md` against the immutable public v1.1.21
baseline. The review inspected 256 tracked PHP, JavaScript/MJS, and CSS source
files, concentrating on user interactions, persistence, remote requests,
email delivery, webhooks, privacy callbacks, scheduled cleanup, and admin
operations.

All corrective work is isolated on
`prerelease/v1.1.21-revalidation` and is intended for a separately approved
v1.1.22 candidate. No package was published and no LocalWP, staging, or
production site was changed.

## Phase 1 Result

The read-only inventory classified the implementation as complex because
failures crossed registration transactions, privacy callbacks, scheduled
tasks, external services, frontend redirects, and admin tooling. Eighteen
planned findings were approved for Phase 2:

- persistence results that could be mistaken for success
- partial registration completion without compensating rollback
- database read failures presented as empty activity
- webhook failures without bounded asynchronous retry
- email fallback paths without diagnostics
- frontend nonce failures escaping the branded gateway
- recoverable form values and focus not preserved after redirects
- slow forms without duplicate-submission protection
- consequential admin actions without confirmation
- non-email settings tabs without unsaved-change protection
- invalid raw dashboard-link JSON clearing a previously saved list

No critical security disclosure, unbounded bulk operation, filesystem write,
PDF generation, or frontend AJAX failure path was found in scope.

## Implemented Fixes

### Privacy Export And Erasure

Files:

- `includes/services/class-privacy-exporter.php`
- `includes/services/class-privacy-eraser.php`

Solution:

- Database query failures now return bounded `WP_Error` results instead of
  exporting an apparently empty dataset.
- Partial erasure reports retained data and a user-facing message instead of
  claiming complete deletion.
- Diagnostics record the operation category without exposing SQL or database
  error details.

Recovery:

- WordPress can retry the exporter or eraser callback.
- Successfully removed records remain removed; failed records are reported as
  retained.

### Registration Transactions And Persistent State

Files:

- `includes/services/class-registration-completion.php`
- `includes/services/class-registration-pending-store.php`
- `includes/services/class-registration-confirmation.php`
- `includes/services/class-registration-request-handler.php`
- `includes/class-database.php`
- `includes/class-retention-cleanup.php`
- `includes/class-settings-defaults.php`
- `admin/settings-page/class-settings-transfer.php`

Solution:

- User profile, pending-registration, consent, and confirmation writes are
  checked before success is returned.
- A newly created user is deleted and the pending record is restored when
  account finalization cannot complete.
- Pending-record query errors remain database failures rather than becoming
  false expired-token states.
- Database installation verifies every plugin table before updating the stored
  schema version.
- Rate-limit storage fails closed with retry guidance.
- Retention cleanup, diagnostics clearing, settings import, and tab-default
  restoration verify persistence and return accurate notices.

Recovery:

- Registration can be retried from the preserved pending state.
- Administrative actions keep the prior saved configuration when persistence
  cannot be verified.

### Webhooks And Email

Files:

- `includes/services/class-webhook-dispatcher.php`
- `includes/services/class-webhook-retry-scheduler.php`
- `includes/services/class-webhook-delivery-logger.php`
- `includes/services/class-email-wordpress-filters.php`
- `includes/class-deactivator.php`

Solution:

- Account-created webhook transport and HTTP failures schedule at most two
  asynchronous WordPress-Cron retries with bounded backoff.
- Test webhooks never retry.
- Invalid JSON encoding returns a failure before network delivery.
- Retry count and response metadata are retained without storing payload bodies
  unless debug payload logging is enabled.
- Retry scheduling and delivery-log persistence failures return or record
  bounded diagnostics.
- Branded email rendering falls back to WordPress content and logs the fallback.
- Failed cleanup of a suppressed email-change marker records diagnostics.
- Deactivation clears the retry hook.

Recovery:

- Production account-created events retry without holding the registration
  request open.
- Email delivery retains a WordPress fallback rather than producing empty
  content.

### Branded Frontend Recovery

Files:

- `includes/services/class-auth-request-handler.php`
- `includes/services/class-registration-request-handler.php`
- `includes/services/class-frontend-messages.php`
- `assets/src/frontend/modules/retained-fields.js`
- `assets/src/frontend/modules/submission-state.js`
- `assets/src/frontend/styles/forms.css`

Solution:

- Expired nonces redirect to the branded gateway with translated session-expired
  guidance instead of exposing a bare WordPress failure screen.
- Email, first name, last name, remember-me, and terms values are retained only
  for redirected error states.
- Passwords, nonces, and Turnstile tokens are never retained.
- The first invalid control receives focus.
- Valid form submissions set `aria-busy`, disable submit controls, and prevent a
  second submission.

Recovery:

- Visitors can correct the failed request without re-entering non-secret fields.
- Browser validation and existing password-policy validation continue to run
  before the busy state is applied.

### Admin Action And Data-Read Feedback

Files:

- `assets/src/admin/modules/form-state.js`
- `admin/settings-page/class-settings-tools.php`
- `admin/settings-page/class-diagnostics-tools.php`
- `admin/settings-page/class-security-review-ui.php`
- `admin/settings-page/class-webhook-tools.php`
- `admin/settings-page/class-security-pending.php`
- `admin/settings-page/class-security-signal-renderer-a.php`
- `includes/class-diagnostics-logger.php`
- `includes/class-settings-sanitizer.php`

Solution:

- Settings saves, provider checks, test emails, webhook tests, imports,
  diagnostics clearing, and verification review actions expose busy state and
  prevent duplicate submission.
- Import, permanent diagnostics clearing, and manual verification review require
  explicit confirmation.
- Non-email settings tabs warn before navigation with unsaved changes.
- Invalid raw dashboard-link JSON preserves the prior list and registers a
  translated settings error.
- Failed diagnostics, verification, pending-registration, and webhook-log reads
  display sanitized error notices instead of misleading empty states.
- Diagnostics CSV export stops with a bounded message when its source query
  fails.

Recovery:

- Existing settings and dashboard links remain intact after invalid input.
- Operators receive refresh and database-connection guidance without seeing SQL,
  table names, paths, or raw provider responses.

## Edge-Case Decisions

- Multiple admin log readers can fail independently; each result is normalized
  before summary calculations and each failure can produce its own notice.
- Error logging does not recursively write diagnostics when the diagnostics
  table itself cannot be read.
- Webhook retries are bounded, deduplicated through `wp_next_scheduled()`, and
  cleared on deactivation.
- New busy-state code preserves original button text and uses native form
  validation before disabling controls.
- Test fixtures now clear simulated transient-write failures during teardown so
  one failure test cannot contaminate later suites.

## Validation Evidence

- Production asset build: passed.
- Stable POT generation: 1,137 strings.
- PHPCS/WPCS and PHP compatibility lint: passed.
- PHP syntax: 225 files passed.
- JavaScript/MJS syntax: 17 files passed.
- PHPUnit: 457 tests and 3,334 assertions passed.
- Focused webhook regression suite: 13 tests and 74 assertions passed.
- npm advisory audit: zero vulnerabilities.
- Composer advisory audit: no advisories.
- Source ceilings: zero production PHP files over 300 lines, zero source
  JavaScript files over 250 lines, and zero source CSS files over 500 lines.
- `git diff --check`: passed for the reviewed source.

## Commit Evidence

- `5795591` Harden privacy database error handling
- `c5d396a` Harden registration persistence failures
- `fca04f4` Verify persistent operations before success
- `1953a41` Add resilient webhook retries and email diagnostics
- `b695f37` Keep frontend request failures in branded gateway
- `6e20098` Harden admin and frontend recovery states
- `0fa8522` Extract webhook failure collaborators

## Outcome

All approved Phase 1 findings are fixed with automated coverage. There are no
deferred Error Handling Review findings and no known release-blocking issue
from this prompt. The code changes require a later v1.1.22 candidate and the
remaining pre-release prompts must continue before release consideration.

Next ordered workflow:
`04-WP_BEST_PRACTICES_REVIEW_PROMPT.md`.
