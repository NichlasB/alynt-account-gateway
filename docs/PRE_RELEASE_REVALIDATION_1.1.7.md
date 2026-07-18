# Alynt Account Gateway v1.1.7 Pre-Release Revalidation

Date: 2026-07-18

## Scope

This record covers the ordered `wp-plugin-toolkit` pre-release sequence run against the v1.1.6 public baseline, the resulting v1.1.7 maintenance candidate, and exact-package acceptance on LocalWP Plugin Tester. Production deployment remains governed by the production rollout playbook.

## Workflow Results

| Workflow | Result | Disposition |
|---|---|---|
| 01 Code Cleanup | Clean | No production debug, temporary, backup, or commented-out code defects found. |
| 02 File Structure | Complete with deferred debt | Oversized controllers, services, and stylesheets are recorded for a dedicated structural project; no release-gate refactor was justified. |
| 03 Error Handling | Clean | Registration, email, provider, webhook, import, and admin actions return or display bounded failures. |
| 04 WordPress Practices | Clean | Prefixing, hooks, nonces, capabilities, assets, database APIs, and plugin metadata passed review. |
| 05 Database | Fixed | Added the missing `token_hash` index and bumped the idempotent schema version to `0.1.5`. |
| 06 Performance | Fixed | Confirmation lookup now uses an indexed column; no material N+1, unbounded frontend query, or unconditional asset issue was found. |
| 07 Edge Cases | Fixed | Public webhook requests now use WordPress safe URL validation; explicit local-development destinations retain their existing behavior. |
| 07A Adversarial Tests | Clean | Full isolated suite passes with 355 tests and 2342 assertions; focused regression coverage was added for all candidate fixes. |
| 08 Uninstall | Clean | Plugin options, six custom tables, rate-limit transients, and the retention cron hook are removed. |
| 09 Internationalization | Fixed | Password requirement states and summaries are now localized rather than hardcoded in JavaScript. |
| 10 Accessibility | Fixed | Localized accessible names and live password-policy summaries remain available in normal and preview asset paths. |
| 11 Code Quality | Clean | PHPCS/WPCS, PHPUnit, and asset build pass. |
| 12 Documentation | Fixed | Added the rollout playbook, restored the missing v1.1.6 changelog entry, and documented this revalidation. |
| 13 Security | Clean after fixes | Safe webhook transport, input/output handling, SQL preparation, authorization, nonces, dependency advisories, and dangerous-function scans passed. |

## Verification

- Focused regression suite: 21 tests, 135 assertions.
- Full PHPUnit suite: 355 tests, 2342 assertions.
- PHPCS/WPCS: pass.
- Frontend/admin asset build: pass.
- npm audit at high severity: zero vulnerabilities.
- Composer audit: no security advisories.
- Diff whitespace check: pass; only the repository's expected `readme.txt` line-ending notice was reported.

## Exact-Package WordPress Acceptance

Target:

- Site key: `plugin-tester`
- Mode: `local-only`
- Site: LocalWP Plugin Tester at `plugin-tester.local`
- WordPress: `7.0.2`
- PHP: `8.5.1`
- Installed baseline: active v1.1.6
- Novamira MCP: unavailable to the agent; Playwright MCP and the normal LocalWP workflow were used

Package:

- Corrected candidate: `alynt-account-gateway-v1.1.7.zip`
- Runtime files: 47
- SHA-256: `FF9275437605C612B89231FA0EF95557B1EFF14A9C2A551A0AFF7E432292AEA5`
- Archive entry separators: 47 forward-slash entries, zero backslash entries
- Installed runtime tree: byte-for-byte match with the inspected package staging tree

The first Windows-built QA archive was rejected by WordPress while unpacking `alynt-account-gateway\assets\`. Inspection found 49 backslash archive entries and zero forward-slash entries. Its rejected SHA-256 was `0EC9D14635120B814EC9D734D19F69E78B4CD4DDECC4B538CEB47DB2063E5734`. The GitHub release workflow packages on Ubuntu with `zip`, so this was a local QA-builder defect rather than a production workflow defect. Rebuilding the same runtime tree with normalized forward-slash entry names resolved the failure and allowed the native WordPress overwrite flow to complete.

Acceptance results:

- WordPress recognized the update from v1.1.6 to v1.1.7 and reported a successful replacement.
- The plugin remained active in the exact same serialized `active_plugins` position.
- Saved plugin settings remained byte-equivalent after normalizing only the intended database schema value.
- Schema upgraded from `0.1.4` to `0.1.5`.
- The `token_hash` index exists on `wp_alynt_ag_pending_registrations`.
- The Advanced / Tools settings screen and Gateway Screen Preview loaded without browser console errors.
- The Set Password preview updated all six accessible requirement states, produced the localized `6 of 6 requirements met` summary, and enabled submission for a compliant matching password.
- An isolated `.local` test receiver returned HTTP 200 through the packaged dispatcher. The delivery log stored response metadata and no payload, matching the privacy default. Unit coverage separately confirms public HTTPS destinations use `wp_safe_remote_post()`.
- The LocalWP homepage returned HTTP 200 after update and cleanup.
- The disposable administrator, webhook log row, receiver, browser session, and WordPress upgrader remnants were removed.

## Deferred Non-Blocking Work

### Structural decomposition

The toolkit's conservative size thresholds identify the settings controller, frontend stylesheet, dashboard renderer, registration service, settings schema, admin stylesheet, WooCommerce integration, email service, frontend controller, authentication service, compatibility warnings service, privacy service, and both JavaScript entry points as decomposition candidates.

These are established, tested modules with cross-cutting rendering and compatibility responsibilities. Splitting them inside a pre-release maintenance cycle would increase regression risk without correcting a current production defect. Treat this as a dedicated architecture project with incremental extractions and runtime acceptance.

### PHPDoc `@since` coverage

The codebase documents classes and public methods with descriptions, parameter types, and return contracts but does not use per-symbol `@since` tags. Adding them across the entire project is a documentation consistency project, not a production readiness blocker.

### Multisite certification

The plugin uses site-prefixed tables and WordPress APIs, but network activation, cross-site uninstall, and per-blog lifecycle behavior were not certified. Do not claim multisite support until a dedicated multisite test matrix passes.

## Release Outcome

- Release: [Alynt Account Gateway v1.1.7](https://github.com/NichlasB/alynt-account-gateway/releases/tag/v1.1.7)
- Source commit: `a3912e0`
- Build Release workflow: `29661956615`, passed
- Public ZIP entries: 57
- Public runtime files: 47
- Packaged PHP files: 41, all syntax-clean
- Public SHA-256: `BE25A519FAA4CBE00D5B3B3E777F0E581EDC16BE3E80CC0DC4642B2FAB10EE42`
- Archive structure: one `alynt-account-gateway` root, zero backslash paths, zero development-file leakage

Plugin Tester updater verification restored the public v1.1.6 package, forced a fresh Alynt Plugin Updater check, received the v1.1.7 offer, and installed the exact public GitHub asset through WordPress's Plugins screen. The final 47-file installed tree byte-matches the public ZIP. Activation order and plugin settings were preserved, schema `0.1.5` and the `token_hash` index are present, a second fresh check reports v1.1.7 up to date, and the LocalWP homepage returns HTTP 200.

The disposable administrator, browser session, downloaded downgrade package, extracted inspection files, and WordPress upgrader remnants were removed. No staging or production site was part of this release verification.
