# Alynt Account Gateway Adversarial Test-Suite Review

## Scope

This document records both phases of
`07A-ADVERSARIAL_TEST_SUITE_REVIEW_PROMPT.md` against the immutable public
v1.1.21 baseline. Corrective work belongs to a separately approved v1.1.22
candidate. No package, release, site, database, or deployment operation was
performed during this workflow.

## Executive Summary

- Final status: Clean for the repository automated-test scope.
- Framework: PHPUnit 9.6 with custom WordPress and WooCommerce shims.
- Tests before: 487 tests and 3,443 assertions.
- Tests after: 526 tests and 3,675 assertions.
- Production defects fixed: 2.
- Harness defects fixed: 2 order-dependent fixture failures plus Cron-fake gaps.
- New automated enforcement: normal, reverse, and fixed-random PHPUnit runs,
  PHPCS, dependency audits, and production asset build in GitHub Actions.
- CI portability correction: Composer now resolves development dependencies
  against the supported PHP 7.4 floor.
- Coverage and mutation tooling: not configured; neither was added solely to
  manufacture a percentage.

Clean does not claim real WordPress, database, Cron, browser, staging, or live
behavior. Those remain explicit runtime handoffs.

## Baseline And Adversarial Evidence

The normal baseline passed with 487 tests and 3,443 assertions. A fixed-random
run failed because `SettingsPageWebhookUxTest` inherited date and time formats
from another test. A reverse-order run reproduced that failure and also raised
an undefined global-options error in `AuthRedirectTest` after lifecycle cleanup.

The affected fixtures now establish the state they consume. The final normal,
reverse, and fixed-random runs each pass with 526 tests and 3,675 assertions.

## Finding Disposition

| ID | Severity | Type | Disposition |
|---|---:|---|---|
| 07A-01 | High | Flaky/harness | Fixed global option ownership and added permanent adversarial-order command |
| 07A-02 | High | Missing/production defect | Corrupted queued webhook envelopes now fail before transport or logging |
| 07A-03 | Medium | Missing/production defect | Malformed or expired operation-lock state is reclaimed safely |
| 07A-04 | High | Misleading | Activation source assertions replaced with executable shim tests |
| 07A-05 | High | Harness | Added push, pull-request, and manual GitHub quality workflow; corrected PHP-floor lock resolution |
| 07A-06 | Medium | Missing/harness | Cron fake now models failure and argument-sensitive deduplication |
| 07A-07 | Medium | Missing | Added exact password-length and independent complexity boundaries |
| 07A-08 | Medium | Mis-layered | Retained as real WordPress runtime handoff |
| 07A-09 | Low | Mis-layered | Static asset assertions retained with browser-runtime limitation documented |

## Change Record

### 07A-01 - Test Isolation

- Layer: PHPUnit shim harness.
- Red: fixed-random produced one failure; reverse order produced one failure and
  one error.
- Action: authentication and webhook settings-page fixtures now initialize the
  global options they consume.
- Green: normal, reverse, and fixed-random suites pass.

### 07A-02 - Queued Webhook Contract

- Layer: external-service contract unit test.
- Red: a queued envelope containing only `{"unexpected":"data"}` was accepted,
  sent, and returned `true` without a stable delivery header.
- Action: queued envelopes require a string URL, the `account.created` event,
  a nonempty event ID, and array user/site snapshots before transport.
- Green: malformed data returns `alynt_ag_webhook_invalid_envelope`, with zero
  remote requests and zero delivery-log writes.

### 07A-03 - Operation-Lock Recovery

- Layer: deterministic pure/shim unit test.
- Red: malformed option state returned `alynt_ag_operation_locked` permanently.
- Action: only a structurally valid, unexpired owner record may retain a lock;
  malformed and expired records are deleted before atomic reacquisition.
- Green: privacy, contention, stale recovery, malformed recovery, ownership,
  and TTL-boundary tests pass.

### 07A-04 - Activation Evidence

- Layer: lifecycle shim test with real production classes.
- Action: replaced source-string claims with executable network rejection,
  failed-install rollback, and successful single-site initialization tests.
- Green: settings, schema stamp, six table definitions, retention schedule,
  rollback, and rewrite-flush outcomes are asserted.

### 07A-05 And 07A-06 - CI And Scheduling

- Layer: harness and contract tests.
- Action: Cron stubs now support forced failure and exact hook/argument identity.
  Retry tests cover queue failure, deduplication, retry ceiling, immutable
  envelope preservation, and diagnostics logging.
- Action: `.github/workflows/quality.yml` runs on all pushes, pull requests, and
  manual dispatch for PHP 7.4 and 8.3.
- Red: the first remote matrix run failed dependency installation because the
  lock had selected `doctrine/instantiator 2.1.0`, which requires PHP 8.4.
- Action: Composer resolves the development lock against PHP 7.4 and selected
  `doctrine/instantiator 1.5.0`, compatible with the declared matrix floor.
- Red: fresh checkouts reached asset tests before ignored production assets
  were built, and PHP 7.4 required explicit reflection accessibility in tests.
- Action: CI builds first, verifies all four generated assets are nonempty, and
  test-only reflection helpers now run consistently across the PHP matrix.
- Red: the remote npm audit received a new high-severity `shell-quote`
  advisory through an unused `concurrently` development dependency.
- Action: removed the unused dependency and its transitive package subtree.

### 07A-07 - Password Boundaries

- Layer: deterministic unit test.
- Action: data-provider cases independently reject 11 characters and missing
  uppercase, lowercase, number, or symbol requirements. An exact 12-character
  compliant password is accepted.

### Admin Security-Negative Matrix

Ten sensitive admin handlers now run through unauthorized and invalid-nonce
cases. Each case asserts that mail, webhooks, database writes, settings writes,
diagnostics changes, and redirects do not occur before the guard succeeds.

## Runtime Handoffs

The final consolidated WordPress component-testing workflow must still verify:

1. Activation, schema installation, upgrade idempotency, and rollback using a
   real isolated WordPress database.
2. Operation-lock contention and stale recovery through real atomic WordPress
   option writes rather than in-memory shims.
3. WP-Cron event identity, duplicate suppression, retry timing, and processing.
4. Capability and nonce enforcement through authenticated WordPress requests.
5. Rendered CSS, JavaScript, keyboard, responsive, and browser behavior.
6. Disposable external-provider and webhook behavior without production data.

Prompt `13-SECURITY_AUDIT_PROMPT.md` remains the final whole-plugin security
review and must run after prompts 08 through 12.

## Validation Evidence

- PHPUnit normal order: 526 tests, 3,675 assertions.
- PHPUnit reverse order: 526 tests, 3,675 assertions.
- PHPUnit fixed-random seed `20260720`: 526 tests, 3,675 assertions.
- PHPCS/WPCS and PHP compatibility lint: passed.
- Production asset build: passed; all four ignored build outputs are nonempty.
- npm high-severity audit: zero vulnerabilities.
- Composer advisory audit: no advisories.
- Composer dependency resolution is pinned to the PHP 7.4 support floor.
- PHPUnit warnings, risky, skipped, and incomplete tests are configured to fail.
- `git diff --check`: passed.

Prompt 07A is complete. Prompt 08 Uninstall Review is next.
