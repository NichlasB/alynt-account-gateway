# Alynt Account Gateway v1.0 Readiness Plan

## Status

- Current phase: Phase 7 privacy, data, and lifecycle acceptance is in progress. The data inventory and source-level minimization/redaction slice is released as public `v0.1.120`, installed through Alynt Plugin Updater on `hbf-staging`, and ready for runtime exporter/eraser acceptance. Phase 5 dashboard/WooCommerce acceptance is complete, and Phase 6 compatibility acceptance passed. The production-like staging stack is WordPress `7.0.1`, PHP `8.2.32`, WooCommerce `10.9.4`, Blocksy child theme, `en_US` LTR, Redis/FluentSMTP/security plugin stack, PayPal/NMI gateways, and USPS shipping. The local default-theme smoke used Plugin Tester with Twenty Twenty-Five.
- Product baseline: `v0.1.120`, released, public-asset verified, and updater-verified on production-like staging.
- Release goal: `v1.0.0`.
- Frontend output default: Disabled.
- Distribution: Alynt-distributed plugin with GitHub updater compatibility.
- Acceptance target: `hbf-staging` at `https://staging.handcraftedbotanicalformulas.com` in `live-only` operating mode. The production HBF site is explicitly excluded.

## Purpose

This is the living production-acceptance roadmap for Alynt Account Gateway. The implementation plan records the released product history and current candidate work. This plan determines whether the plugin is ready to be called `v1.0.0` on real WordPress and WooCommerce sites.

Readiness work should prioritize runtime evidence, configuration safety, compatibility, documentation, and operational recovery. New product features belong in a separately approved roadmap unless they are required to resolve a release-blocking defect found during acceptance testing.

## Readiness Principles

- Test the exact packaged build that would be released.
- Use a production-like staging site before any live-site rollout.
- Keep Frontend Output disabled until configuration and readiness checks pass, then enable it only through an approved handover window with rollback evidence.
- Record versions, settings fingerprints, package hashes, browser evidence, and cleanup results.
- Use site-owned test credentials and disposable test accounts; never commit secrets or personal data.
- Preserve a restore point before package installation, configuration import, or transactional testing.
- Treat native WordPress account-screen leakage, authentication bypass, account-creation defects, sensitive-data exposure, and destructive cleanup behavior as release blockers.
- Require explicit release approval before commit, tag, push, or publication of `v1.0.0`.

## Out Of Scope

- New account, dashboard, email, integration, or WooCommerce features that are not required to fix an acceptance defect.
- Site-specific visual redesign beyond validating the plugin's existing brand controls.
- Replacing site-owned SMTP, DNS, consent-management, firewall, backup, or monitoring services.
- Live-site writes before the WordPress site-operations confirmation and approval gates are satisfied.

## Phase 0: Acceptance Environment

- [x] Confirm the staging site shorthand, domain, filesystem path, and operating mode.
- [x] Confirm whether a separate live target exists and keep it out of scope until staging passes.
- [x] Record WordPress, PHP, WooCommerce, theme, mail-provider, and relevant security/caching plugin versions.
- [x] Create and verify a restore point for files and database.
- [x] Record the starting Alynt Account Gateway version, activation state, settings fingerprint, and frontend-output state.
- [x] Define disposable administrator, shop-manager, customer, and pending-registration test identities.
- [x] Define cleanup steps for test users, orders, emails, logs, webhook records, uploads, and temporary helpers.
- [x] Store integration credentials outside the repository and confirm that evidence will be redacted.
- [x] Agree on browser viewport, language, RTL, assistive-technology, and transactional test coverage.

### Environment Evidence

| Item | Value | Evidence | Status |
| --- | --- | --- | --- |
| Staging target | `hbf-staging`; `https://staging.handcraftedbotanicalformulas.com`; `live-only` mode | Site Operations registry, HTTP, and WP-CLI | Confirmed |
| Production exclusion | Production HBF site is not an acceptance target | User confirmation dated 2026-07-15 | Confirmed |
| Restore point | Locked, server-local WPvivid full backup; task `wpvivid-d6e76efae4340`; 680,105,457 bytes; SHA-256 `BE3835C433B6150C7A901CDE036C691E9F2E948F4F4F21206AB2930594A1D432` | WPvivid completed state, manifest/hash comparison, outer and six nested ZIP integrity checks, SQL-entry and package-metadata validation, and Drime non-transfer checks | Verified |
| WordPress / PHP | WordPress `7.0.1`; PHP `8.2.32` | WP-CLI on target | Confirmed |
| WooCommerce / theme | WooCommerce `10.9.4`; Blocksy child theme with Blocksy `2.1.48`; Blocksy Companion Pro `2.1.49`; HPOS disabled | WP-CLI on target | Confirmed |
| Mail / cache / security | FluentSMTP `2.2.95`; Redis Cache `2.8.0`; BBQ Pro `3.9`; Blackhole for Bad Bots `3.8.2`; WP fail2ban `5.4.1`; Nginx Helper `9.9.10` | Active-plugin inventory on target | Confirmed |
| Installed gateway baseline | Alynt Account Gateway is not installed or active | WP-CLI plugin query on target | Confirmed absent |
| Settings fingerprint | Not applicable until installation; no Alynt Account Gateway options were present to fingerprint | WP-CLI plugin query and read-only baseline | Confirmed not applicable |
| Current account-route owner | WP Custom Login Manager `1.2.0`, with Force Login `5.6.3` also active | Active-plugin inventory, source inspection, HTTP, and browser evidence | Handover required |

### Acceptance Controls

- Test identities use the `alynt_ag_v1_qa_` prefix and must never reuse or modify an existing customer, administrator, shop manager, order, or registration record.
- Non-mail role checks use disposable administrator, shop-manager, and customer users. Real email-flow checks use site-owned acceptance mailbox aliases supplied outside the repository; reserved or invented addresses are not used for deliverability claims.
- Pending registrations and representative WooCommerce orders receive the same QA prefix or marker plus a run identifier so cleanup can be deterministic.
- Cleanup covers prefixed users, QA-marked orders, pending-registration records, plugin-owned logs, webhook records, diagnostics, test messages, uploaded packages, temporary helpers, and rotated acceptance secrets. Existing records are out of bounds.
- Evidence may contain aggregate counts, version numbers, redacted settings, request outcomes, hashes, and screenshots. It must not retain credentials, bypass keys, cookies, private customer data, email bodies containing personal data, or full webhook payloads.
- Baseline viewports are `390x844`, `800x900`, and `1440x1000`. Later acceptance adds keyboard-only checks, visible-focus review, Windows NVDA checks, `en_US`, one translated LTR locale, one RTL locale, and the browser matrix defined in Phase 6.
- Transactional testing starts with empty/new-customer states and then uses only disposable QA orders. Existing production-clone orders remain read-only.
- The target contains approximately 9,214 users and 13,698 legacy order posts. This production-data-clone scale makes strict namespacing, redaction, and cleanup mandatory.

### Route Handover Constraint

WP Custom Login Manager currently owns the site's `/login/` route, registration, email verification, Reoon and Turnstile integration, direct `wp-login.php` handling, account emails, and WooCommerce redirects. Force Login also participates in public access and redirect behavior. Alynt Account Gateway must therefore not be enabled beside the incumbent route owner without a controlled handover.

The Phase 1 handover sequence is: preserve a redacted incumbent-settings snapshot; install and configure Alynt Account Gateway with Frontend Output disabled; verify emergency access; approve the route switch; deactivate or reconfigure the overlapping route owner during a controlled window; test the gateway routes; and retain a rollback path that restores the incumbent plugin state.

### Read-Only Baseline Findings

- Public route behavior: `/` redirects to native login through Force Login; `/wp-login.php` redirects to `/login/`; `/login/` returns `200`; `/my-account/` redirects to the branded login route.
- The current login screen has no horizontal overflow at `390px` or `800px` and exposes email, password, remember-me, registration, and lost-password controls.
- The incumbent login JavaScript throws `TypeError: this.initKeyboardNavigation is not a function`. This predates Alynt Account Gateway installation and remains a handover baseline issue.
- WP-CLI emits a pre-existing early text-domain loading notice for `wp-custom-login-manager` on each bootstrap.
- Brizy, Brizy Pro, and Presto Player Pro had unrelated updates available during baseline capture. They are recorded as environmental drift and must not be updated as part of gateway acceptance without a separate decision.

## Phase 1: Production-Like Configuration

- [x] Install the current public release asset through the supported updater or package-install path.
- [x] Verify Frontend Output remains disabled during initial configuration.
- [x] Configure and verify login path, account action base, after-login redirect, and emergency bypass.
- [x] Configure logo, logo width, background image, colors, button colors, and typography using representative branding.
- [x] Visually review saved gateway screen states at `390x844`, `800x900`, and `1440x1000` while public output remains disabled.
- [x] Configure screen-specific welcome and instruction copy.
- [x] Configure relative Terms and Privacy paths and verify both destinations.
- [x] Configure account-creation policy and confirm the default disabled behavior before deliberate enablement.
- [x] Configure email templates, enable/disable switches, sender expectations, and test recipient.
- [x] Configure dashboard, custom links, role visibility, icons, ordering, and new-tab behavior.
- [x] Configure WooCommerce takeover only when the site's account page and endpoints are ready.
- [x] Configure Turnstile, Reoon, and webhook credentials only where acceptance requires them.
- [x] Review the plugin readiness summary and record every remaining release-blocking item.
- [x] Export a redacted configuration snapshot for recovery and portability testing.
- [x] Obtain approval before enabling Frontend Output on staging.

### Phase 1 Installation Evidence

| Item | Value | Evidence | Status |
| --- | --- | --- | --- |
| Incumbent snapshot | 45 `wpclm_*` options; SHA-256 `843FED4664D3A24EDA34119C2D3CDFDA2834EE5A7C0DAAEBF7E74C38E7E3757C` | Redacted local snapshot with configured-state, length, and hashes for sensitive values | Preserved |
| Release package | Public `v0.1.98`; 157,635 bytes; SHA-256 `63A5EAF0F573874E9D06AF8BDF819B989310DE388EE6639358D25218EFDF0585` | GitHub release metadata, local hash, server-side hash, and WP-CLI install | Verified |
| Activation state | Active; 45 runtime files | WP-CLI plugin status and filesystem inventory | Verified |
| Frontend safety state | Frontend Output, registration, dashboard, and WooCommerce takeover disabled | Persisted activation settings and browser/HTTP checks | Verified |
| Gateway settings | 77 keys; SHA-256 `659358CFB69611AEC993E124F2CBCD91F2D81C352F0BC18478789B486EAA2802`; redacted SHA-256 `C24BC7484BA1CAC9DBF25E262A49CF7CC8654B2348E293CB3BB706577C0B4AE0` | Redacted activation snapshot | Recorded |
| Plugin data | Six plugin tables created and empty; retention cleanup scheduled | Database counts and cron-state check | Verified |
| Incumbent preservation | WP Custom Login Manager and Force Login remain active; incumbent fingerprint unchanged | Pre/post activation option hashes and plugin status | Verified |
| Public behavior | Existing `/login/`, `/wp-login.php`, `/account`, and `/my-account/` behavior unchanged; zero gateway assets on login | HTTP, Playwright DOM/assets, screenshot, and PHP-log review | Verified |
| Disabled-output role policy | Administrator, shop-manager, and customer behavior was identical with the gateway inactive and active because the existing site stack applies equivalent policies | Disposable authenticated role comparison, cleanup verification, and `PHASE_1_ROLE_POLICY_TEST.json` | Site delta absent; product defect confirmed |

The generated emergency bypass value was confirmed present but was not printed or stored in evidence. Turnstile, Reoon, and webhook credentials remain unconfigured in Alynt Account Gateway. No account, order, email, webhook, or gateway log record was created.

### Phase 1 P1-001 Maintenance Release

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.99` | Prepared locally |
| Behavior correction | Admin-bar filtering passes through unchanged and customer wp-admin blocking returns without redirect when Frontend Output is disabled | Implemented |
| Regression coverage | Added direct disabled-toggle cases while retaining enabled customer, administrator, and shop-manager policy coverage | Verified |
| Focused tests | 9 tests, 42 assertions | Passed |
| Full tests | 285 tests, 1,900 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | 108 project PHP files outside dependencies | Passed |
| Dependency audits | npm audit: zero vulnerabilities; Composer audit: no advisories | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.99` metadata | Passed |
| Candidate package | 45 runtime files; one plugin root; zero backslash entries; zero development leaks; aligned header, constant, stable tag, and POT metadata; one updater header; both guards present | Verified |
| Candidate SHA-256 | `55B989352D638BC18C4C35A167F10CE69ECAEFEAC09CBCD486EA877CF72323BB` | Recorded |
| Installed-package smoke | Exact candidate installed on local-only Plugin Tester; administrator, shop-manager, and customer toolbar values passed through unchanged and wp-admin blocking returned without redirect while Frontend Output was disabled | Passed |
| Plugin Tester restoration | Public `0.1.98` restored active at its original position with the original 45-file and settings fingerprints, disabled toggles, scheduled retention, zero QA users, and zero upload artifacts | Verified |
| Release commits | Release commit `8abe7f1`; merge commit `0271789` | Pushed to GitHub |
| GitHub release | [`v0.1.99`](https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.99) | Published |
| Build Release workflow | Run `29450943872`; build completed in 17 seconds with no substantive warning, deprecation, or error signal | Passed |
| Public release asset | `alynt-account-gateway-v0.1.99.zip`; 157,682 bytes; 45 runtime files plus 10 directory entries; one plugin root; zero backslash entries; zero development leaks; aligned metadata; one updater header; both guards present | Verified |
| Public asset SHA-256 | `F87B79060043C6995A1FCD3ABE01C00BB410BF1A580B15D0B6D74F551D97BE4A` | Matches GitHub digest |
| Staging updater | Alynt Plugin Updater `1.1.1` offered and installed the exact public `v0.1.99` asset on `hbf-staging` | Passed |
| Installed public-package identity | 45 files; installed and public-ZIP file-map SHA-256 `611E3571D88086F6F6D220B320E095F1F713C390D7C4DA520264858B6595DEE2` | Exact match |
| Disabled-output staging retest | Administrator, shop-manager, and customer toolbar values passed through unchanged and wp-admin policy evaluation returned without redirect | Passed |
| Staging preservation and cleanup | Active at original position `4`; settings fingerprint unchanged; six tables empty; retention scheduled; incumbent routes unchanged; zero QA users; no remaining offer | Verified |

The maintenance release is published, its public package is verified, and its updater installation and disabled-output role-policy retest have passed on `hbf-staging`. `P1-001` is closed. Representative configuration is recorded below, with Frontend Output still disabled and route handover still behind an explicit approval gate.

### Phase 1 Representative Configuration Evidence

| Item | Result | Status |
| --- | --- | --- |
| Safety state | Frontend Output, registration, dashboard, and WooCommerce takeover remain disabled | Verified |
| Settings | 77 keys; SHA-256 `4BC5E9FEACE6CC434831606259E6BA8DB019343620D3D47B7D50F5883D44E180`; all secret fields preserved | Recorded |
| Branding | Existing HBF logo and pomegranate background, 150px logo width, HBF palette, Noto Serif headings, and DM Sans body text | Configured |
| Routes and copy | `/login/`, `/account`, `/my-account/`, HBF instructions, 24-hour registration expiry, and customer username format | Configured |
| Legal destinations | Saved settings use `/legal/terms/` and `/legal/privacy/`; both map to published custom `legal` posts. Anonymous requests remain intercepted by Force Login on staging by explicit site-owner decision. | Configured; staging access constraint accepted |
| Email templates | Five rich-text templates render valid HTML and plain text with no unresolved tokens. A redacted site-owned test recipient is configured, and all five supported templates returned sent through `wp_mail()` while Frontend Output and registration stayed disabled. | Preview and site mail handoff passed; mailbox evidence pending |
| Dashboard | FAQ and Contact links configured with icons, ordering, customer/subscriber visibility, and same-tab behavior | Configured; dashboard disabled |
| Integrations | Either-provider mode, quick Reoon mode, and flagged-result allow policy are saved. The test recipient, Turnstile keys, Reoon API key, webhook URL, and signing secret are empty. | Configuration required before live provider, delivery, and webhook acceptance |
| Gateway previews | Eight screen states render the HBF logo and no WordPress logo; 24 visual captures across `390x844`, `800x900`, and `1440x1000` show no horizontal overflow or obvious overlap. The authenticated compact-code preview endpoint renders standalone gateway markup on staging. | Structural, fallback visual, and live preview route checks passed |
| Public behavior | Incumbent routes unchanged; zero Account Gateway assets on `/login/` | Verified |
| Data and cleanup | Six tables empty; retention scheduled; zero QA users | Verified |
| Redacted export | Non-secret settings and validation evidence stored locally; media IDs documented as site-specific | Verified |

### Phase 1 P1-003 Maintenance Candidate

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.100` | Prepared locally |
| Behavior correction | Gateway Screen Preview buttons now use the settings-page admin route `options-general.php?page=alynt-account-gateway&alynt_ag_preview=1`; the legacy `admin-post.php` handler remains registered | Implemented |
| Regression coverage | Added tests for settings-page preview route registration and generated Preview button URLs | Verified |
| Focused tests | 3 tests, 15 assertions | Passed |
| Full tests | 287 tests, 1,908 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | Changed PHP files | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.100` metadata | Passed |
| Publication state | Not committed, tagged, pushed, published, or installed | Waiting for release approval |

### Phase 1 P1-003 Corrective Candidate

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.101` | Prepared after `0.1.100` staging retest |
| Staging retest finding | `0.1.100` installed through Alynt Plugin Updater, but authenticated settings-page preview requests still redirected before standalone preview output rendered | Corrective change required |
| Behavior correction | Gateway Screen Preview buttons now use authenticated `admin-ajax.php?action=alynt_ag_preview_gateway`; settings-page and legacy admin-post handlers remain registered as compatibility fallbacks | Implemented |
| Regression coverage | Added tests for `wp_ajax_alynt_ag_preview_gateway` registration and generated admin AJAX Preview button URLs | Verified |
| Focused tests | 3 tests, 16 assertions | Passed |
| Full tests | 287 tests, 1,909 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | Changed PHP files | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.101` metadata | Passed |
| Publication state | Local corrective candidate prepared after release approval; public release and staging updater retest in progress | Active |

### Phase 1 P1-003 Second Corrective Candidate

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.102` | Prepared after `0.1.101` staging retest |
| Staging retest finding | `0.1.101` installed through Alynt Plugin Updater and `admin-ajax.php` was reachable, but the preview action still redirected before standalone preview output rendered | Corrective change required |
| Behavior correction | Standalone preview output now prints only the plugin preview CSS/JS handles instead of running broad `wp_head()` and `wp_footer()` hooks | Implemented |
| Rationale | The preview document does not need full site head/footer hook execution; isolating output avoids redirect-heavy theme/plugin hooks while preserving saved-setting previews | Recorded |
| Focused tests | 3 tests, 16 assertions | Passed |
| Full tests | 287 tests, 1,909 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | Changed PHP files | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.102` metadata | Passed |
| Publication state | Local second corrective candidate prepared after release approval; public release and staging updater retest in progress | Active |

### Phase 1 P1-003 Final Corrective Candidate

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.103` | Prepared after `0.1.102` staging retest |
| Diagnostic finding | Temporary staging-only MU trace showed WP Custom Login Manager redirecting preview requests to `/wp-admin/` at `plugins_loaded`, before ACG admin/admin-ajax handlers could run | Root cause identified |
| Behavior correction | Gateway Screen Preview buttons now use a nonce-protected front-end endpoint guarded by `manage_options` | Implemented |
| Rationale | The endpoint stays admin-only and nonce-protected while avoiding wp-admin transports that incumbent redirect plugins can preempt | Recorded |
| Focused tests | 3 tests, 16 assertions | Passed |
| Full tests | 287 tests, 1,909 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | Changed PHP files | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.103` metadata | Passed |
| Publication state | Local final corrective candidate prepared after release approval; public release and staging updater retest in progress | Active |

### Phase 1 P1-003 URL Code Corrective Candidate

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.104` | Prepared after `0.1.103` staging retest |
| Staging retest finding | The front-end preview endpoint still redirected because WP Custom Login Manager redirects logged-in administrators when the request URI contains `login`; the login preview URL contained `alynt_ag_preview_gateway=login` | Root cause refined |
| Behavior correction | Gateway Screen Preview URLs now use compact screen codes, e.g. `alynt_ag_preview_gateway=1&alynt_ag_preview_screen=l` | Implemented |
| Rationale | Compact codes keep previews admin-only and nonce-protected while avoiding incumbent substring-based login redirects | Recorded |
| Focused tests | 3 tests, 18 assertions | Passed |
| Full tests | 287 tests, 1,911 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | Changed PHP files | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.104` metadata | Passed |
| Publication state | Public `v0.1.104` released, inspected, installed through Alynt Plugin Updater, and retested on `hbf-staging` | Passed |
| Public asset | 45 runtime files, 0 development files, SHA-256 `A88D61F830A9C909A2ABCAB0BE569B23ED3555AB954144D3CB8ED2B2B65E60E3` | Verified |
| Staging retest | Compact-code login preview URL returned `HTTP/1.1 200 OK`, zero redirects, gateway markup and login copy present, no wp-admin dashboard body | Passed |
| Cleanup | Temporary MU trace, helper files, and temporary admin session tokens removed | Completed |
| Finding status | `P1-003` | Closed |

Representative configuration is complete for the inputs currently available. This statement captured the pre-handover state; later Phase 1 evidence supersedes it for current staging status. Before route handover, mailbox-side email evidence, provider configuration and behavior, webhook configuration and delivery, and route-switch approval remained open.

### Phase 1 Integration Gate

| Item | Result | Status |
| --- | --- | --- |
| Turnstile | Site key and secret key are saved in Alynt Account Gateway on `hbf-staging`; direct invalid-token server-side verification returns `alynt_ag_turnstile_failed` with provider error `invalid-input-response` | Configured; negative server-side check passed |
| Reoon | API key is saved in Alynt Account Gateway on `hbf-staging`; quick-mode policy check returned `valid` and unblocked under the configured `turnstile_or_reoon` mode | Configured; valid-path check passed |
| Account-created webhook | Temporary receiver URL and generated staging signing secret were saved for acceptance; `account.created.test` delivered HTTP 200 with HMAC signature header present; temporary receiver and saved temporary webhook settings were then removed | Temporary delivery passed; permanent receiver still needed |
| Current safety state | Frontend Output and registration remain disabled, debug payload logging remains disabled, and no stale temporary webhook URL remains saved | Safe configuration state preserved |
| Cleanup | Disposable QA user was deleted, temporary helper files were removed locally and remotely, and the temporary external receiver token was deleted after evidence capture | Completed |

### Phase 1 Integration Acceptance

| Item | Result | Status |
| --- | --- | --- |
| Redacted settings status | Turnstile site key, Turnstile secret key, and Reoon API key are present. Temporary webhook URL and signing secret were present during the acceptance dispatch, then cleared after the temporary receiver was deleted. Frontend Output and registration are disabled. Debug payload logging is disabled. | Passed |
| Provider policy | Direct invalid Turnstile token failed server-side, Reoon quick-mode check returned `valid`, and the configured `turnstile_or_reoon` registration policy returned pass because one configured provider succeeded | Passed |
| Verification logs | Latest acceptance rows recorded `turnstile` blocked with `alynt_ag_turnstile_failed` and `reoon` unblocked with `valid` | Passed |
| Webhook dispatch | Built-in `account.created.test` dispatch returned success to a temporary HTTPS receiver | Passed |
| Webhook delivery log | Latest webhook log records `account.created.test`, destination host `webhook.site`, HTTP 200, success `1`, and no stored payload body | Passed |
| External receiver | Temporary receiver observed one POST with JSON content type, `X-Alynt-AG-Event: account.created.test`, and an HMAC signature header; raw payload and signature were not retained in evidence | Passed |
| Cleanup | Temporary receiver token was deleted; temporary webhook URL/signing secret were cleared from staging settings; disposable QA user and temporary helper files were removed | Passed |

### Phase 1 Route Handover Preflight

| Item | Result | Status |
| --- | --- | --- |
| Installed gateway | Alynt Account Gateway is active on `hbf-staging` at `v0.1.107` during preflight; later handover evidence advances the installed version to `v0.1.110` | Verified; superseded by handover evidence |
| Incumbent route owner | WP Custom Login Manager `1.2.0` remains active and continues to own `/login/` before handover | Verified |
| Force Login | Force Login is active as plugin slug `wp-force-login` at `5.6.3` | Verified |
| Current public route behavior | `/login/` returns HTTP 200; `/account?action=lostpassword` redirects to native `wp-login.php` through the incumbent/Force Login stack before Alynt Account Gateway owns the route | Baseline preserved |
| Configured routes | `login_path` is `/login/`, `account_action_base` is `/account`, and `after_login_redirect` is `/my-account/` | Configured |
| Safety toggles | `frontend_enabled`, `registration_enabled`, `dashboard_enabled`, and `woocommerce_takeover` remain false-style stored values | Safe state preserved |
| Emergency bypass | Emergency bypass key is present; runtime source and tests confirm `wp-login.php?alynt_ag_bypass={key}` bypasses only Alynt Account Gateway's native-login redirect and does not authenticate the visitor | Source/test verified; live behavior waits for frontend handover |
| Provider credentials | Turnstile site key, Turnstile secret key, and Reoon API key remain present | Configured |
| Webhook cleanup correction | A follow-up preflight found the current schema key `account_created_webhook` still held the temporary receiver value after the earlier legacy-key cleanup. The current schema webhook URL, signing secret, and debug payload setting now resolve to zero-byte values. | Corrected |
| Next gate | Enabling Frontend Output, enabling registration, and changing/deactivating incumbent route ownership required explicit route-handover approval and rollback steps; approval was later granted and acceptance is recorded below | Completed |

### Phase 1 Route Handover Acceptance

| Item | Result | Status |
| --- | --- | --- |
| Approval and scope | Route handover was approved for `hbf-staging` only. The production HBF site remained excluded. | Passed |
| Compatibility release | Public `v0.1.108` added a Force Login compatibility bypass for only the configured login and account-action routes, and only while Frontend Output is enabled. Legal pages and the dashboard remain protected by Force Login. | Released and updater-installed |
| Canonical redirect release | Public `v0.1.109` moved gateway rendering ahead of WordPress canonical redirects so `/login/` renders the gateway instead of redirecting to `/`. | Released and updater-installed |
| Turnstile script release | Public `v0.1.110` loads the Cloudflare Turnstile API without a WordPress version query, removing the provider console warning on a fresh registration-page load. | Released and updater-installed |
| Package and release checks | `v0.1.110` passed the full PHPUnit suite with 292 tests and 1,932 assertions, PHPCS, build, POT regeneration, PHP syntax checks, and public ZIP inspection. The public runtime ZIP contains 45 runtime files and no development files; SHA-256 `99C6648853AAEDD1BC79B01ED97DBB77082B0F0AB33B98114A23DFEADF397C13`. | Passed |
| Installed state | Alynt Account Gateway is active at `0.1.110`; WP Custom Login Manager is inactive; Force Login remains active at `5.6.3`; Frontend Output and registration are enabled for acceptance. | Verified |
| Webhook state | The current schema webhook URL and signing secret resolve to empty values after temporary receiver cleanup. Debug payload logging is disabled. | Verified |
| Login route | `/login/` returns HTTP 200 with Alynt Account Gateway markup and no WP Custom Login Manager markup. | Passed |
| Account action routes | `/account?action=lostpassword` and `/account?action=register` return HTTP 200 with Alynt Account Gateway forms and no native WordPress shell. | Passed |
| Native login redirect | `/wp-login.php` redirects to the configured branded login route. | Passed |
| Emergency bypass | `wp-login.php?alynt_ag_bypass={redacted}` returns the native WordPress login form without authenticating the visitor and without Alynt Account Gateway form markup. | Passed |
| Account redirect | `/my-account/` redirects unauthenticated visitors to the branded login route with a safe `redirect_to` value. | Passed |
| Staging-only Force Login boundary | `/legal/terms/` and `/legal/privacy/` continue to redirect to the branded login route under Force Login, matching the approved staging-only constraint. | Passed |
| Browser smoke | Fresh browser checks confirmed Alynt gateway body/shell/form output for login, lost-password, and registration. Registration shows the Turnstile widget, uses the clean Cloudflare API URL, and produced no fresh console warnings or errors. | Passed |
| Cleanup | Temporary updater helper files were removed. No webhook receiver, payload body, bypass key, or credential evidence was retained in the repository. | Passed |

Post-handover route acceptance is complete for `hbf-staging`. Full form submission, real Turnstile challenge success, permanent webhook delivery, and WooCommerce/customer dashboard journeys remain in later phases.
### Phase 1 Email, Provider, And Webhook Acceptance Attempt

| Item | Result | Status |
| --- | --- | --- |
| Temporary helper | Local WP-CLI helper linted, copied to `/tmp`, executed once under the site user, and removed from the server afterward | Completed |
| Safety state | Frontend Output and registration remained disabled | Preserved |
| Email test sends | All five template sends returned `alynt_ag_invalid_email_recipient` because `email_test_recipient` is empty | Blocked by missing configuration |
| Provider check | Registration protection returned pass because no Turnstile or Reoon credentials are saved, so no provider checks ran | Blocked by missing configuration |
| Webhook test | Disposable QA user was created and deleted; test dispatch returned `alynt_ag_webhook_missing_url`; zero webhook log rows were deleted because no delivery was attempted | Blocked by missing configuration |
| Cleanup | Remote helper removed; disposable QA user deleted; no email, provider, or webhook acceptance evidence was produced | Verified |

### Phase 1 Email Acceptance

| Item | Result | Status |
| --- | --- | --- |
| Test recipient | Site-owned test recipient saved in Account Gateway settings; recipient value kept out of repository evidence | Configured |
| Temporary helper | Local WP-CLI helper linted, copied to `/tmp`, executed once under the site user, and removed locally and from the server afterward | Completed |
| Safety state | Frontend Output and registration remained disabled | Preserved |
| Registration confirmation email | `wp_mail()` returned sent | Passed |
| Password reset email | `wp_mail()` returned sent | Passed |
| Password changed email | `wp_mail()` returned sent | Passed |
| Account-created welcome email | `wp_mail()` returned sent | Passed |
| Email-change confirmation email | `wp_mail()` returned sent | Passed |
| Summary | Five sent, zero failed | Site mail handoff passed |
| Resend after SMTP simulation note | After the first run was observed as simulated by the mail stack, all five supported templates were resent. Each returned sent through `wp_mail()` with Frontend Output and registration still disabled. | Passed |
| Mailbox rendering | User confirmed receipt after `v0.1.105`; the brand logo rendered centered and proportionate in the real mailbox. Body text readability should be improved in a follow-up typography correction. | Logo passed; see `P1-005` |

### Phase 1 P1-004 Email Logo Corrective Candidate

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.105` | Released |
| Defect | Account-email logo rendered at the raw source image size in at least one mailbox client despite the previous CSS-only max-width | Confirmed |
| Behavior correction | Email logo output now honors `brand_logo_max_width`, clamps email logo width between 80px and 220px, and emits both a `width` attribute and inline `width`, `max-width`, `height`, `display`, and border-reset styles | Implemented |
| Regression coverage | Added email renderer coverage for explicit logo `width` attribute and inline constrained dimensions | Verified |
| Focused tests | 18 tests, 129 assertions | Passed |
| Full tests | 288 tests, 1,915 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | 83 project PHP files outside dependencies | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.105` metadata | Passed |
| Publication state | `v0.1.105` committed, tagged, pushed, published, public ZIP inspected, installed on `hbf-staging` through Alynt Plugin Updater, all five account-email templates resent through the site mail path, and mailbox screenshot accepted by the product owner | Closed |

### Phase 1 P1-005 Email Body Typography Candidate

| Item | Result | Status |
| --- | --- | --- |
| Candidate version | `0.1.107` | Prepared locally |
| Finding | Real mailbox review showed the corrected logo, but account-email body text still felt too small on larger screens | Confirmed |
| First attempt | `v0.1.106` relied on wrapper-level media queries and was released, updater-installed, and resent; real mailbox comparison showed no visible paragraph-size change | Failed; superseded |
| Behavior correction | Account email body text now uses inline 20px paragraph and list-item sizing with responsive 16px mobile and 18px tablet overrides as progressive enhancement for clients that honor embedded CSS | Implemented |
| Cleanup | Removed the ineffective desktop-size media query as the primary mechanism and moved reliable sizing to generated copy elements | Completed |
| Regression coverage | Added email renderer coverage for inline paragraph/list-item sizing, the `agw-email-body` wrapper, and responsive mobile/tablet overrides | Verified |
| Focused tests | 19 tests, 136 assertions | Passed |
| Full tests | 289 tests, 1,922 assertions | Passed |
| Coding standards | Full PHPCS/WPCS project scan | Passed |
| PHP syntax | Project PHP syntax sweep outside dependencies | Passed |
| Build and translations | Production assets rebuilt; POT regenerated with 997 strings and `0.1.107` metadata | Passed |
| Publication state | `v0.1.107` committed, tagged, pushed, published, public ZIP inspected, installed on `hbf-staging` through Alynt Plugin Updater, all five account-email templates resent through the site mail path, and mailbox screenshot accepted by the product owner on mobile and desktop | Closed |

## Phase 2: Core Account Acceptance

- [x] Verify email-only login with valid, invalid, rate-limited, and pending account states; document inactive-account scope.
- [x] Verify login redirects preserve only safe destinations and do not create loops.
- [x] Verify public registration is unavailable while account creation is disabled.
- [x] Verify the full confirmation-first registration flow when account creation is enabled.
- [x] Confirm no WordPress user is created before email confirmation and password completion.
- [x] Verify pending-registration expiry, invalid tokens, used tokens, and resend behavior.
- [x] Verify generated usernames follow the configured pattern while login remains email-only.
- [x] Verify password creation requires matching fields and the configured minimum policy.
- [x] Verify password strength feedback is understandable and accessible.
- [x] Verify lost-password, reset-password, password-changed, and invalid/expired reset-link states.
- [x] Verify logout confirmation, cancellation, successful logout, and safe redirect behavior.
- [x] Verify administrators and shop managers retain intended wp-admin and toolbar access.
- [x] Verify other roles are redirected away from wp-admin without redirect loops.
- [x] Verify the emergency bypass exposes only native login and never authenticates a visitor.
- [x] Verify all public account routes avoid the native WordPress shell except through deliberate bypass.
- [x] Verify frontend output can be disabled to restore native behavior without losing settings.

### Phase 2 Initial Account Acceptance Evidence

| Item | Result | Status |
| --- | --- | --- |
| Installed gateway | Alynt Account Gateway is active on `hbf-staging` at `v0.1.111` after updater installation from the public release asset | Verified |
| Public route shell | `/login/`, `/account?action=lostpassword`, `/account?action=register`, and `/account?action=logout` render Alynt Account Gateway markup and return zero native WordPress or WP Custom Login Manager markers | Passed |
| Native redirects | `/wp-login.php` redirects to the branded login route, `/my-account/` redirects to branded login with `redirect_to`, and unauthenticated `/wp-admin/` redirects to the configured after-login destination | Passed |
| Regression found | The first disposable registration-start POST on `v0.1.110` returned `200 OK` and created zero pending/consent rows because gateway rendering ran before auth/registration POST handlers | Confirmed |
| Corrective release | Public `v0.1.111` moves auth and registration POST handlers to `template_redirect` priority `0`, before the gateway renderer at priority `1`, while preserving the canonical redirect fix | Released and updater-installed |
| Release checks | `v0.1.111` passed focused routing tests, full PHPUnit with 293 tests and 1,937 assertions, PHPCS, build, POT regeneration, PHP syntax checks for 108 files, and public ZIP inspection | Passed |
| Public package | `alynt-account-gateway-v0.1.111.zip`; one root item; 45 runtime files; zero development files; SHA-256 `45386200DB17501C006CBF9F17EDBCACA4A8EE1765CD9156DAFB9EDFB1085621` | Verified |
| Updater install | Alynt Plugin Updater refreshed the managed release metadata and installed `0.1.110 -> 0.1.111` on `hbf-staging` | Passed |
| Registration start | A fresh disposable registration POST redirected to `/account?action=register&registration_sent=1`; a follow-up run used the reachable acceptance mailbox `ai@mailastic.com` after the original plus-address mailbox did not receive the message | Passed |
| Provider policy | With the configured `turnstile_or_reoon` policy, the registration start logged Turnstile as blocked because no challenge token was submitted and Reoon as `valid` and unblocked, allowing the flow because one configured provider succeeded | Passed |
| Pending state | Pending registration and consent counts now include the reachable `ai@mailastic.com` run; no WordPress user exists for that email before confirmation/password completion | Passed |
| Completion state | Confirmation-link set-password completion is waiting on the `ai@mailastic.com` confirmation link and has not yet been executed | Pending user action |

### Phase 2 Registration Completion Evidence

| Item | Result | Status |
| --- | --- | --- |
| Confirmation link | The real confirmation link delivered to `ai@mailastic.com` opened the branded set-password screen with Alynt Account Gateway shell/form markers, both password fields, a completion nonce, and zero native WordPress or WP Custom Login Manager markers | Passed |
| Set-password completion | A one-off compliant password was submitted through the branded set-password form and redirected to `/login/?registration_complete=1` | Passed |
| Delayed account creation | No WordPress user existed for `ai@mailastic.com` before confirmation/password completion; after set-password completion, the WordPress user was created | Passed |
| User fields | Created user ID `9253`; email `ai@mailastic.com`; first name `Alynt`; last name `GatewayQAAI...`; role `subscriber` | Passed |
| Generated username | The generated username used the configured customer pattern: `@Customer_Alynt_GatewayQAAI...` | Passed |
| Pending registration state | Latest `ai@mailastic.com` pending registration row moved to `account_created`, stores user ID `9253`, and records a confirmation timestamp | Passed |
| Consent attachment | Latest consent record for `ai@mailastic.com` is attached to user ID `9253` with registration context | Passed |
| Webhook state | Webhook log count did not increase because no permanent webhook receiver is configured | Expected |
| Email-only login | Branded login accepted `ai@mailastic.com` plus the chosen password, set a WordPress logged-in cookie, and redirected to `/my-account/` | Passed |
| Temporary password handling | The generated acceptance password was stored only in a local temp file for the login check and removed after the login verification | Completed |
| Cleanup state | The disposable user and plugin-owned test rows remain present temporarily so the next Phase 2 checks can reuse or intentionally clean the created account with evidence | Deferred |

### Phase 2 Cleanup And Negative-State Evidence

| Item | Result | Status |
| --- | --- | --- |
| Cleanup inventory | Disposable user `9253`, one completed `ai@mailastic.com` pending/consent pair, and one stale undeliverable plus-address pending/consent pair were identified before cleanup | Verified |
| Cleanup action | Disposable user `9253` was deleted. The two known Phase 2 pending-registration rows and two matching consent rows were deleted through a temporary `wp eval-file` helper, which was removed locally and remotely after use | Completed |
| Cleanup verification | `ai@mailastic.com` no longer resolves to a WordPress user; pending-registration count is `0`; consent-record count is `0`; Frontend Output and registration remain enabled | Passed |
| Invalid login | Branded login with a nonexistent email and wrong password redirected to `/login/?login_error=failed` | Passed |
| Invalid set-password token | A bogus set-password token rendered a branded invalid/expired confirmation state with no password fields and zero native WordPress or WP Custom Login Manager markers | Passed |
| Registration disabled | Registration was temporarily disabled, `/account?action=register` returned the branded registration-disabled state, and registration was restored immediately afterward | Passed |
| Remaining account negatives | Rate limits, inactive/pending account login, expired token, used token, password-policy failure, reset-password, and logout edge states remain for later Phase 2 slices | Pending |

### Phase 2 Password Reset And Logout Evidence

| Item | Result | Status |
| --- | --- | --- |
| Disposable user | Created subscriber user `9254` with login `alynt_ag_reset_qa` and email `ai@mailastic.com` for reset/logout acceptance | Created |
| Lost-password request | Branded lost-password form accepted `ai@mailastic.com` and redirected to `/account?action=lostpassword&reset_sent=1` | Passed |
| Stale reset link | The first mailbox reset link rendered the branded invalid/expired state with zero password fields and zero native WordPress or WP Custom Login Manager markers after WordPress no longer had the matching key | Passed |
| Fresh reset link | A second reset request produced a fresh WordPress reset key, and the newest link rendered the branded `Set New Password` form with two password fields, reset action, nonce, and zero native/incumbent markers | Passed |
| Reset completion | Submitting a compliant new password redirected to `/login/?password_reset=1` | Passed |
| Password invalidation | The old password redirected to `/login/?login_error=failed` with no logged-in cookie | Passed |
| Reset login | The new password logged in by email, set a logged-in cookie, and redirected to `/my-account/` | Passed |
| Logout confirmation | Branded logout screen rendered with confirm/cancel actions and zero native/incumbent markers | Passed |
| Logout cancel | Cancel returned to `/my-account/` while the user remained logged in | Passed |
| Logout confirm | Confirmed logout used the nonce URL, redirected to `/login/`, removed the logged-in cookie, and made `/my-account/` redirect back to branded login | Passed |
| Cleanup | Deleted disposable user `9254`; removed local temporary old/new password files; verified `ai@mailastic.com` no longer resolves to a WordPress user | Completed |
| Remaining account negatives | Rate limits, inactive/pending account login, expired/used registration token states, password-policy failure states, and role-access edge cases remain for later Phase 2 slices | Pending |

### Phase 2 Role Access And Toolbar Evidence

| Item | Result | Status |
| --- | --- | --- |
| Disposable users | Created administrator `9255`, shop manager `9256`, and customer `9257` with `alynt_ag_role_qa_` identifiers for role-access testing | Created |
| Branded login | All three roles logged in by email through the branded login form, received a logged-in cookie, and redirected to `/my-account/` | Passed |
| Administrator access | Administrator reached `/wp-admin/` with HTTP 200 and saw the frontend admin toolbar | Passed |
| Shop manager access | Shop manager reached `/wp-admin/` with HTTP 200 and saw the frontend admin toolbar | Passed |
| Customer access | Customer was redirected from `/wp-admin/` to `/my-account/` and did not see the frontend admin toolbar | Passed |
| Native leakage | Logged-in frontend checks for all three roles returned zero native WordPress login or WP Custom Login Manager markers | Passed |
| Cleanup | Deleted users `9255`, `9256`, and `9257`; removed the local temporary role-test credential file; verified zero `alynt_ag_role_qa_` users remain | Completed |

### Phase 2 Rate Limit And Password Policy Evidence

| Item | Result | Status |
| --- | --- | --- |
| Baseline settings | Recorded original staging rate-limit values before testing: registration `5/60`, resend `5/60`, login `10/15`, and lost-password `5/60` | Verified |
| Temporary test thresholds | Set all four buckets to `1` attempt for a `1` minute window, then restored the original values immediately after the rate-limit checks | Restored |
| Login rate limit | Two branded login POSTs with the same disposable email produced a normal failed-login redirect first and a second redirect with `login_error=alynt_ag_rate_limited` | Passed |
| Lost-password rate limit | Two branded lost-password POSTs with the same disposable email produced a neutral reset-sent redirect first and a second redirect with `reset_error=alynt_ag_rate_limited` | Passed |
| Registration rate limit | Two branded registration POSTs with the same disposable email produced a registration-sent redirect first and a second redirect with `registration_error=alynt_ag_rate_limited` | Passed |
| Confirmation resend rate limit | Two branded resend-confirmation POSTs with the same disposable email produced a confirmation-resent redirect first and a second redirect with `resend_error=alynt_ag_rate_limited` | Passed |
| Password length failure | Disposable pending registration set-password POST with a short password redirected with `password_error=alynt_ag_password_length`, stayed branded, showed the password error region, and returned zero native markers | Passed |
| Password complexity failure | Disposable pending registration set-password POST without required character classes redirected with `password_error=alynt_ag_password_complexity`, stayed branded, showed the password error region, and returned zero native markers | Passed |
| Password mismatch failure | Disposable pending registration set-password POST with mismatched compliant passwords redirected with `password_error=password_mismatch`, stayed branded, showed the password error region, and returned zero native markers | Passed |
| Account creation guard | The disposable password-policy pending registration never created a WordPress user while the password submissions failed | Passed |
| Cleanup | Removed disposable `alynt_ag_rate_` and `alynt_ag_policy_` rows from plugin-owned pending, consent, and verification tables; removed temporary local and remote helper files | Completed |

### Phase 2 Pending Account And Token State Evidence

| Item | Result | Status |
| --- | --- | --- |
| Disposable state setup | Created one pending-only registration, one expired pending registration, and one consumed-token registration using disposable `alynt_ag_` identifiers | Created |
| Pending-email login | Branded login for the pending-only email redirected to `/login/?login_error=failed`, showed the branded error region, returned zero native markers, and did not create a WordPress user | Passed |
| Expired confirmation token | The expired confirmation link rendered the branded invalid/expired state with HTTP 200, no password fields, and zero native WordPress or WP Custom Login Manager markers | Passed |
| Used confirmation token | The consumed confirmation link rendered the branded invalid/expired state with HTTP 200, no password fields, and zero native WordPress or WP Custom Login Manager markers | Passed |
| Used-token account guard | The consumed-token setup created one disposable user to represent a real completed registration; that user was deleted after the token-state check | Completed |
| Cleanup | Removed disposable `alynt_ag_pending_`, `alynt_ag_expired_`, and `alynt_ag_used_` rows from plugin-owned pending, consent, and verification tables; removed temporary local and remote helper files | Completed |

### Phase 2 Pending Registration Resend Evidence

| Item | Result | Status |
| --- | --- | --- |
| Disposable setup | Created pending registration `8` for disposable email `alynt_ag_resend_...@mailastic.com` through the plugin registration service; no WordPress user existed and one consent record was present | Created |
| Invalid-link screen | `/account?action=invalidlink` returned HTTP 200 with Alynt shell markers, the resend form, and zero native WordPress markers | Passed |
| Resend POST | Public resend form submitted `alynt_ag_action=resend_confirmation`, nonce, and disposable email; handler returned HTTP 302 to `/account?action=invalidlink&confirmation_resent=1` | Passed |
| Success state | The confirmation-resent URL rendered HTTP 200 with Alynt shell markers, the success status region, and zero native WordPress markers | Passed |
| Pending renewal | Pending registration stayed `pending`; token hash, `created_at`, and `expires_at` changed to fresh values; `confirmed_at` remained null | Passed |
| Logging and account guard | One `confirmation_resent` verification log was recorded; no WordPress user was created for the disposable email | Passed |
| Cleanup | Removed the disposable pending registration, consent record, verification log, and local/remote helper files; post-cleanup snapshot returned zero rows and no user | Completed |

### Phase 2 Emergency Bypass Evidence

| Item | Result | Status |
| --- | --- | --- |
| Secret handling | The bypass key was read only into a server-side shell variable, was not printed, and was not stored in repository evidence | Protected |
| Normal native login | Anonymous `/wp-login.php` returned HTTP 302 and redirected to the configured branded `/login/` route | Passed |
| Bypass native login | Anonymous `wp-login.php` with the emergency bypass query returned HTTP 200 with native `loginform` and `wp-submit` markers, no redirect, and no Alynt Account Gateway form markup | Passed |
| No authentication | The bypass response did not set a `wordpress_logged_in` cookie | Passed |
| Admin still protected | Reusing the bypass visit cookie jar against `/wp-admin/` returned a redirect and no dashboard/admin-bar markers | Passed |
| Cleanup | Temporary bypass-check helper was removed from `/tmp` on staging and from the local workflow work folder | Completed |

### Phase 2 Frontend Output Safety Switch Evidence

| Item | Result | Status |
| --- | --- | --- |
| Baseline enabled state | With Frontend Output enabled, `/login/`, `/account?action=lostpassword`, and `/account?action=register` rendered Alynt Account Gateway markup, while `/wp-login.php` redirected to branded `/login/` | Passed |
| Disabled setting | `frontend_enabled` was temporarily changed from `1` to `0`; `registration_enabled` remained `1` | Verified |
| Disabled public routes | `/login/`, `/account?action=lostpassword`, and `/account?action=register` returned no Alynt Account Gateway markup while Frontend Output was disabled | Passed |
| Disabled native login | `/wp-login.php` returned native WordPress login markup while Frontend Output was disabled | Passed |
| Restored setting | `frontend_enabled` was restored to `1`; `registration_enabled` remained `1` | Restored |
| Restored branded routes | `/login/`, `/account?action=lostpassword`, and `/account?action=register` again rendered Alynt Account Gateway markup, and `/wp-login.php` again redirected to branded `/login/` | Passed |
| Settings retention | The safety-switch test changed only the Frontend Output toggle and preserved the registration toggle and route ownership after restoration | Passed |

### Phase 2 Inactive Account Scope Evidence

| Item | Result | Status |
| --- | --- | --- |
| Source inspection | Alynt Account Gateway delegates completed-account login to `wp_signon()` and does not define a separate inactive customer/account status model | Verified |
| WordPress-core simulation | A disposable subscriber was created and its `wp_users.user_status` value was set to `1` to test whether core treats that field as inactive | Simulated |
| Login behavior | Branded email-only login with the correct password succeeded, set a logged-in cookie, and redirected to `/my-account/`; this confirms `user_status = 1` is not an inactive-account control in this environment | Verified |
| Scope decision | Inactive-account blocking is not a v1 Alynt Account Gateway feature unless a separate account-approval, membership, commerce, or LMS integration defines an authoritative inactive state | Documented |
| Cleanup | Deleted disposable user `9259` and removed temporary local and remote helper files | Completed |

### Phase 2 Safe Redirect Matrix Evidence

| Item | Result | Status |
| --- | --- | --- |
| Initial matrix finding | `v0.1.111` preserved safe internal redirects and rejected external redirects, but a valid login submitted with `redirect_to=/login/` returned the logged-in user to the branded login form | Confirmed |
| Corrective release | Public `v0.1.112` rejects post-login destinations that point to the configured branded login path, configured account action base, or native `wp-login.php`, falling back to the configured after-login URL | Released and updater-installed |
| Release checks | Focused auth tests passed with 17 tests and 72 assertions; full PHPUnit passed with 295 tests and 1,941 assertions; PHPCS, PHP syntax sweep for 108 files, npm audit, Composer audit, build, POT regeneration, diff check, and public ZIP inspection passed | Passed |
| Public package | `alynt-account-gateway-v0.1.112.zip`; one root item; 45 runtime files; zero development files; SHA-256 `1E53D6E0ED7CA39DA113E87BA0B93511F8AFB7DFA85349606C4A1DE1B4AA5579` | Verified |
| Updater install | Alynt Plugin Updater refreshed managed release metadata and installed `0.1.111 -> 0.1.112` on `hbf-staging`; installed plugin reports `0.1.112`, remains active, and contains 45 runtime files with no development files | Passed |
| Valid safe redirect | Branded email login with `redirect_to=/my-account/` returned `302` to `/my-account/` and set a logged-in cookie | Passed |
| Unsafe external redirect | Branded email login with an external `redirect_to` returned `302` to the configured `/my-account/` fallback and set a logged-in cookie | Passed |
| Auth-surface redirect fallback | Branded email login with `redirect_to` set to `/login/`, `/account?action=lostpassword`, or `/wp-login.php` returned `302` to `/my-account/` and set a logged-in cookie | Passed |
| Anonymous redirect chains | Anonymous `/my-account/` and `/wp-login.php` each resolved in one redirect to a branded account route; anonymous `/account?action=lostpassword` rendered directly with zero redirects | Passed |
| Cleanup | Disposable redirect-test user `9260` and temporary local/remote helper files were removed | Completed |

### Phase 2 Public Route Shell Evidence

| Item | Result | Status |
| --- | --- | --- |
| Installed state | Alynt Account Gateway `0.1.112` was active on `hbf-staging`; Frontend Output and registration were enabled | Verified |
| Login route states | `/login/`, failed-login, registration-complete, and password-reset status URLs returned HTTP 200 with Alynt shell markers and zero native WordPress login, WordPress-logo, or incumbent WP Custom Login Manager markers | Passed |
| Account action routes | `/account?action=lostpassword`, reset-sent state, `/account?action=register`, registration-sent state, invalid registration set-password token, invalid reset-key set-password state, `/account?action=logout`, and an unknown account action all returned HTTP 200 with Alynt shell markers and zero native/incumbent markers | Passed |
| Native route handoff | `/wp-login.php`, `/wp-login.php?action=lostpassword`, `/wp-login.php?action=register`, and `/wp-login.php?action=logout` each resolved in one redirect to the corresponding branded Alynt route with Alynt shell markers and zero native/incumbent markers | Passed |
| Emergency bypass exception | The emergency bypass key was read only into a server-side variable and was not printed. `wp-login.php` with the bypass returned HTTP 200 with native login markers, no Alynt shell markers, and no logged-in cookie | Passed |
| Cleanup | Temporary bypass-control helper was removed from `/tmp` on staging and from the local workflow work folder | Completed |

### Phase 2 Password Strength Feedback Accessibility Evidence

| Item | Result | Status |
| --- | --- | --- |
| Initial finding | Set-password requirement items used disabled checkbox semantics for read-only progress criteria, which made the feedback less clear for assistive technology | Confirmed |
| Corrective release | Public `v0.1.113` replaces requirement checkbox state with readable requirement labels and live progress-count status text | Released and updater-installed |
| Release checks | Full PHPUnit passed with 295 tests and 1,947 assertions; PHPCS, PHP syntax sweep for 108 files, npm audit, Composer audit, build, POT regeneration, diff check, and public ZIP inspection passed | Passed |
| Public package | `alynt-account-gateway-v0.1.113.zip`; one root item; 45 runtime files; zero development files; SHA-256 `077179E9BE379B915574BA726E7B567BBF578F7B8D39B17A2FC859DBCB1B11EC` | Verified |
| Updater install | Alynt Plugin Updater refreshed managed release metadata and installed `0.1.112 -> 0.1.113` on `hbf-staging`; installed plugin reports `0.1.113`, remains active, and contains 45 runtime files with no development files | Passed |
| Set-password screen | A disposable pending registration rendered the branded set-password screen with HTTP 200, Alynt shell markers, the password form, a polite live status region, six requirement labels, and six initial `Not met` labels | Passed |
| Removed semantics | The rendered set-password screen no longer includes requirement `role="checkbox"` or `aria-checked` markers, and no native WordPress login markers appeared | Passed |
| Cleanup | Disposable pending-registration and consent records were deleted; temporary local and remote helper files were removed | Completed |

## Phase 3: Email And Deliverability Acceptance

- [ ] Preview every supported account email using representative tokens and branding.
- [ ] Test-send every supported account email through the site's real mail-delivery path.
- [ ] Verify headings, bold text, links, lists, spacing, logo sizing, colors, and mobile rendering.
- [ ] Verify plain-text alternatives remain understandable and contain required links.
- [ ] Verify reset, welcome/confirmation, password-changed, and email-change links reach the correct branded state.
- [ ] Verify disabled email switches suppress only their intended messages.
- [ ] Verify WordPress or WooCommerce emails outside the plugin's ownership remain unaffected.
- [ ] Verify sender identity and reply behavior match the site's mail configuration.
- [ ] Confirm SPF, DKIM, DMARC, SMTP, and mailbox-placement responsibilities with the site owner.
- [ ] Confirm email previews, test sends, logs, and diagnostics do not expose secrets or unrelated customer data.
- [ ] Record representative desktop/mobile mailbox evidence without retaining personal data.

## Phase 4: Security And Integration Acceptance

- [x] Verify Turnstile success, failure, expiry, replay, and server-side validation behavior.
- [x] Verify Reoon valid, invalid, disposable, catch-all, role-account, and unknown outcomes against the configured policy.
- [x] Verify registration can proceed when either configured protection succeeds, according to site policy.
- [x] Verify provider outages and timeouts fail safely with useful administrator diagnostics.
- [ ] Verify registration, login, reset, resend, and provider rate limits under repeated requests.
- [ ] Verify lockout and resend-throttling feedback does not disclose whether an account exists.
- [ ] Verify manual-review decisions, audit records, and retention behavior where enabled.
- [x] Verify webhook delivery fires only when the account is created.
- [x] Verify the webhook includes the intended full user fields without secrets or password data.
- [x] Verify signing headers, receiver validation, failure metadata, and debug-payload controls.
- [ ] Verify credentials and bypass keys are redacted from diagnostics, exports, logs, and screenshots.
- [ ] Rotate the emergency bypass key and integration test secrets after acceptance.

### Phase 4 Turnstile Browser Success Evidence

| Item | Result | Status |
| --- | --- | --- |
| Installed state | Alynt Account Gateway `0.1.113` was active on `hbf-staging`; Frontend Output and registration were enabled; protection mode was `turnstile_or_reoon` with Reoon quick mode | Verified |
| Browser token | The public registration page loaded the Cloudflare Turnstile widget and produced a non-empty browser response token before form submission | Passed |
| Registration submission | A disposable registration for `alynt_ag_turnstile_...@mailastic.com` submitted through the public browser form and redirected to `/account?action=register&registration_sent=1` | Passed |
| Server-side Turnstile result | Plugin-owned verification logs recorded provider `turnstile`, status `passed`, and `blocked=0` for the disposable registration | Passed |
| Provider policy context | The same submission also logged Reoon `valid` and `blocked=0`, so the registration succeeded with both configured providers passing under the either-provider policy | Passed |
| Account-creation guard | The browser registration created a pending registration and consent record, but no WordPress user before email confirmation/password completion | Passed |
| Cleanup | The disposable pending registration, consent record, and verification logs were deleted; no matching WordPress user remained; temporary local and remote helpers were removed | Completed |
| Remaining Turnstile coverage | Invalid-token, replay, and expiry behavior are now verified in the later Turnstile server-side validation evidence. Provider outage/timeout and Turnstile-only registration policy remain for separate Phase 4 slices. | Superseded |

### Phase 4 Turnstile Server-Side Validation Evidence

| Item | Result | Status |
| --- | --- | --- |
| Fresh token first use | A real browser-generated Turnstile token was copied to staging without printing it and verified through `ALYNT_AG_Turnstile_Client`; first use returned pass | Passed |
| Replay behavior | The same token was immediately verified a second time and returned `alynt_ag_turnstile_failed` instead of passing | Passed |
| Invalid token behavior | A deliberately invalid token returned `alynt_ag_turnstile_failed` instead of passing | Passed |
| Expiry behavior | A separate unused browser token was held for 310 seconds, then verified server-side and returned `alynt_ag_turnstile_failed` instead of passing | Passed |
| Secret handling | Turnstile tokens were saved only as temporary local and remote files, were not printed in repository evidence, and were deleted after verification | Passed |
| Cleanup | Temporary local token files, remote token files, and local/remote verifier helpers were removed; no account, pending-registration, consent, verification-log, or webhook data was created by these direct client checks | Completed |

### Phase 4 Reoon Policy Matrix Evidence

| Item | Result | Status |
| --- | --- | --- |
| Configured state | Reoon API key was present on `hbf-staging`; configured mode was `quick`; configured flagged policy was `allow` | Verified |
| Valid outcomes | Synthetic `valid` and power-mode `safe` statuses passed under both `allow` and `block` flagged policies | Passed |
| Hard-block outcomes | Synthetic `invalid`, `disabled`, `disposable`, and `spamtrap` statuses blocked under both flagged-policy settings with `alynt_ag_reoon_blocked` | Passed |
| Flagged outcomes allowed | Synthetic `catch_all`, `role_account`, `unknown`, and `inbox_full` statuses passed and remained flagged under the configured `allow` policy | Passed |
| Flagged outcomes blocked | The same flagged statuses blocked with `alynt_ag_reoon_flagged_blocked` when the flagged policy was switched to `block` in the isolated matrix | Passed |
| Live API spot checks | The configured Reoon API returned unblocked `valid` for a masked Mailastic acceptance address, blocked the masked `.invalid` address, and blocked the masked Mailinator disposable address | Passed |
| Data and cleanup | The matrix helper called the provider/client directly without creating account, pending-registration, consent, verification-log, or webhook rows; temporary local and remote helpers were removed | Completed |

### Phase 4 Provider Combination Policy Evidence

| Item | Result | Status |
| --- | --- | --- |
| Test method | Official Cloudflare Turnstile test credentials were used only in an isolated settings copy to force pass/fail states; saved staging settings were not changed | Verified |
| Current site policy | Saved staging policy remained `turnstile_or_reoon`; Reoon mode remained `quick`; Reoon API key remained present | Verified |
| Either-provider pass: Turnstile only | In `turnstile_or_reoon`, Turnstile passed while Reoon blocked a disposable address; overall protection passed | Passed |
| Either-provider pass: Reoon only | In `turnstile_or_reoon`, Turnstile failed while Reoon returned `valid`; overall protection passed | Passed |
| Either-provider fail: both providers | In `turnstile_or_reoon`, Turnstile failed and Reoon blocked a disposable address; overall protection failed | Passed |
| All-provider pass | In `turnstile_and_reoon`, Turnstile passed and Reoon returned `valid`; overall protection passed | Passed |
| All-provider fail: Reoon required | In `turnstile_and_reoon`, Turnstile passed but Reoon blocked a disposable address; overall protection failed | Passed |
| All-provider fail: Turnstile required | In `turnstile_and_reoon`, Turnstile failed while Reoon would have passed; overall protection failed before Reoon was checked | Passed |
| Data and cleanup | The helper created only temporary verification-log rows, deleted all 11 rows it created, and post-cleanup counts for the test prefix returned zero pending, consent, and verification rows | Completed |

### Phase 4 Provider Outage And Timeout Evidence

| Item | Result | Status |
| --- | --- | --- |
| Test method | Provider HTTP timeouts were simulated with a temporary `pre_http_request` filter; saved staging credentials and settings were not changed | Verified |
| Current site policy | Saved staging policy remained `turnstile_or_reoon`; Reoon mode remained `quick`; Alynt Account Gateway remained active at `0.1.113` | Verified |
| Turnstile timeout in either-provider mode | In `turnstile_or_reoon`, Turnstile timeout logged `alynt_ag_turnstile_request_failed` and Reoon returned `valid`; overall protection passed | Passed |
| Reoon timeout in either-provider mode | In `turnstile_or_reoon`, Turnstile passed and Reoon timeout logged `alynt_ag_reoon_request_failed`; overall protection passed | Passed |
| Both providers timeout in either-provider mode | In `turnstile_or_reoon`, both provider timeouts were logged and overall protection failed | Passed |
| Turnstile timeout in all-provider mode | In `turnstile_and_reoon`, Turnstile timeout logged `alynt_ag_turnstile_request_failed` and overall protection failed before checking Reoon | Passed |
| Reoon timeout in all-provider mode | In `turnstile_and_reoon`, Turnstile passed, Reoon timeout logged `alynt_ag_reoon_request_failed`, and overall protection failed | Passed |
| Diagnostics | Temporary verification logs exposed provider-specific compact statuses for administrator review without secrets or provider payload bodies | Passed |
| Data and cleanup | The helper created only temporary verification-log rows, deleted all 9 rows it created, and post-cleanup counts for the test prefix returned zero pending, consent, and verification rows | Completed |

### Phase 4 Webhook Receiver Acceptance Evidence

| Item | Result | Status |
| --- | --- | --- |
| Receiver | A temporary webhook.site HTTPS receiver was created for acceptance and deleted after testing | Completed |
| Settings scope | The receiver URL and signing secret were used only through temporary in-memory settings copies; saved staging webhook URL and signing secret remained empty after testing | Verified |
| Pending-registration guard | Creating a disposable pending registration did not create a webhook delivery log | Passed |
| Account-created delivery | Completing the disposable registration created WordPress user `9261` and triggered exactly one `account.created` webhook delivery log | Passed |
| Delivery metadata | The plugin logged destination host `webhook.site`, HTTP `200`, success `1`, event `account.created`, and no error message | Passed |
| Default payload logging | Webhook log payload storage remained `null` with debug payload logging disabled | Passed |
| Receiver payload | The receiver captured a JSON `account.created` payload with full user fields, site name, and site URL | Passed |
| Payload safety | Local receiver verification found no `password` or `secret` fields in the raw JSON body | Passed |
| HMAC headers | A second signed dispatch sent `X-Alynt-AG-Event`, `X-Alynt-AG-Time`, `X-Alynt-AG-Version`, and `X-Alynt-AG-Signature` headers | Passed |
| Receiver HMAC validation | The captured signature matched `sha256=HMAC_SHA256({time}.{event}.{raw_json_body}, signing_secret)` using a temporary secret file that was not printed | Passed |
| Cleanup | Disposable user `9261`, pending row, consent row, two webhook logs, temporary receiver token, temp secrets, request captures, and local/remote helpers were removed; post-cleanup counts returned zero | Completed |

## Phase 5: Dashboard And WooCommerce Acceptance

- [x] Verify the custom dashboard disabled and enabled states.
- [x] Verify customer greeting uses first name with the neutral fallback.
- [x] Verify custom dashboard links, icons, ordering, role visibility, and new-tab settings.
- [x] Verify WooCommerce takeover disabled and enabled states.
- [x] Verify dashboard overview and navigation with no orders.
- [x] Verify dashboard overview and navigation with representative orders.
- [x] Verify order list, pagination, order details, and available order actions.
- [x] Verify downloads with empty, available, expired, and limited-download states.
- [x] Verify billing and shipping address view/edit flows and validation errors.
- [x] Verify account-details name/display editing and validation errors.
- [x] Verify account email changes and password changes.
- [x] Verify saved payment-method list, add, delete, and default-method flows where supported.
- [x] Verify delegated WooCommerce notices, forms, nonces, and errors remain functional for address and account-details forms.
- [x] Verify unavailable WooCommerce endpoint guidance and recovery links.
- [x] Verify shop-manager administration remains available while customer wp-admin access remains blocked.
- [x] Confirm checkout, payment, subscription, membership, or other extension behavior used by the target site is not disrupted.

### Phase 5 Dashboard And WooCommerce Route Smoke Evidence

| Item | Result | Status |
| --- | --- | --- |
| Baseline state | Alynt Account Gateway `0.1.113` and WooCommerce `10.9.4` were active on `hbf-staging`; Frontend Output was enabled; saved dashboard and WooCommerce takeover switches were disabled | Verified |
| In-memory routing matrix | Disabled dashboard returned no gateway screen for `/my-account/`; dashboard enabled routed `/my-account/`; WooCommerce endpoint routes such as `/my-account/orders/` routed only when takeover was enabled | Passed |
| In-memory dashboard render | Disposable customer and administrator users were created and deleted; customer output included `Welcome, AlyntDash`, the customer email, WooCommerce overview, logout, orders, addresses, account details, FAQ, and Contact; administrator output excluded the customer/subscriber-only custom links | Passed |
| Public dashboard route | Temporarily enabled dashboard/takeover and added one temporary customer-only new-tab custom link; browser login as disposable customer landed on `/my-account/` with the branded dashboard, first-name greeting, WooCommerce overview, account navigation, configured custom links, and screen-reader text for the new-tab link | Passed |
| Public WooCommerce endpoints | Browser checks confirmed `/my-account/orders/` rendered branded shell, Account section shortcuts, Order History guidance, WooCommerce native empty-orders notice, and Browse products action; `/my-account/edit-address/` rendered WooCommerce address content; `/my-account/edit-account/` rendered WooCommerce first name, last name, display name, email, password-change fields, and Save changes button | Passed |
| Cleanup and restore | Original `alynt_ag_settings` were restored exactly from snapshot; disposable user `9264` was deleted; temporary snapshot options and remote helpers were removed; final baseline again showed dashboard and takeover disabled with the two original custom links only | Completed |
| Remaining coverage | All required Phase 5 coverage is complete | Complete |

### Phase 5 Representative Orders Evidence

| Item | Result | Status |
| --- | --- | --- |
| Test data | Created disposable customer `9265`, private temporary product `22286`, and 18 disposable WooCommerce orders with processing, completed, and pending-payment statuses | Created and removed |
| Temporary settings | Dashboard and WooCommerce takeover were enabled only for the browser smoke window from a settings snapshot | Verified |
| Dashboard overview | Browser login as the disposable customer landed on `/my-account/` with `Welcome, AlyntOrders`, WooCommerce customer overview, View Orders/Manage Addresses/Account Details shortcuts, and configured custom links | Passed |
| Order list | `/my-account/orders/` rendered inside the branded dashboard shell with WooCommerce table columns for Order, Date, Status, Total, and Actions; rows showed pending-payment, processing, and completed orders with `$12.34 for 1 item` totals | Passed |
| Available order actions | Pending-payment orders exposed native WooCommerce Pay, View, and Cancel actions; processing/completed orders exposed View actions | Passed |
| Order details | `/my-account/view-order/22304/` rendered inside the branded dashboard shell with Back to orders/Manage addresses shortcuts, Order Details guidance, order status/date, product line, subtotal, total, billing address, and Pay/Cancel actions | Passed |
| Pagination | The first orders page exposed `Next` to `/my-account/orders/2/`; page 2 rendered the remaining orders and exposed `Previous` back to page 1 | Passed |
| Cleanup and restore | Original settings were restored; orders `22287`-`22304`, product `22286`, user `9265`, temp snapshot options, and remote helpers were removed; final read-only check returned zero matching QA users/products/order markers and dashboard/takeover disabled | Completed |
| Remaining coverage | All required Phase 5 coverage is complete | Complete |

### Phase 5 Downloads Evidence

| Item | Result | Status |
| --- | --- | --- |
| Empty state | Disposable customer `9272` had no download permissions; `/my-account/downloads/` rendered in the branded dashboard shell with Downloads guidance, helpful next-step panel, WooCommerce `No downloads available yet.` notice, and Browse products action | Passed |
| Test data | Disposable customer `9273`, products `22305`-`22307`, orders `22308`-`22310`, and three download permission rows represented available unlimited, limited remaining, and expired download states | Created and removed |
| Approved-directory handling | WooCommerce `10.9.4` enforced approved download directories; the temporary uploads subdirectory was enabled only during the test and the original `wc_product_download_directories` table was restored from snapshot afterward | Verified |
| Available downloads | WooCommerce's own `wc_get_customer_available_downloads()` returned the unlimited and limited downloads; browser `/my-account/downloads/` rendered a native WooCommerce downloads table inside the branded shell | Passed |
| Limited download | The limited download displayed `1` in the Downloads remaining column | Passed |
| Unlimited download | The available unlimited download displayed `∞` in the Downloads remaining column and `Never` for expiry | Passed |
| Expired permission | The expired permission row remained present in setup evidence but was excluded from WooCommerce's available-downloads API and did not appear in the frontend table | Passed |
| Cleanup and restore | Original settings and approved-directory rows were restored; users `9272` and `9273`, products `22305`-`22307`, orders `22308`-`22310`, three permission rows, temp options, remote helpers, and upload files were removed; final read-only check returned zero QA rows/files and dashboard/takeover disabled | Completed |
| Remaining coverage | All required Phase 5 coverage is complete | Complete |

### Phase 5 Address And Account Form Evidence

| Item | Result | Status |
| --- | --- | --- |
| Initial address POST finding | Billing/shipping address forms and account-details forms rendered in the branded dashboard shell, but WooCommerce form handlers normally ran after the gateway renderer exited. Billing saves did not persist before the corrective staging patch. | Found |
| Corrective source change | Added early WooCommerce My Account POST delegation for address and account-details forms while preserving the gateway renderer at `template_redirect` priority `1`; primed the `edit-address` query var for billing/shipping POSTs; rendered WooCommerce notices before delegated endpoint content so validation errors are visible and cleared. | Released in `v0.1.114` |
| Billing address save | Browser login as disposable customer `9274`; `/my-account/edit-address/billing/` saved `AlyntBilling FixedQA`, `505 Fixed Billing Road`, and redirected to the branded address overview; WP-CLI user meta confirmed persistence. | Passed |
| Billing validation | Empty required billing first name did not overwrite the saved billing first name; after the notice-rendering fix, WooCommerce validation notices render inside the branded dashboard and clear on the next request. | Passed |
| Shipping address save | After clearing stale notices, `/my-account/edit-address/shipping/` saved `AlyntShipping FinalQA`, `1212 Final Shipping Road`, and `shipping_phone=5553332222`; WP-CLI user meta confirmed persistence. | Passed |
| Account-details validation | Empty display name on `/my-account/edit-account/` rendered the WooCommerce `Display name is a required field.` error inside the branded shell and kept the existing display name. | Passed |
| Account-details save | Valid first name, last name, and display-name update saved `AlyntAccount FormsQA`; WP-CLI confirmed user display name and first/last name meta. Email and password changes were intentionally left for a separate slice. | Passed |
| Cleanup and restore | Original settings were restored; disposable user `9274`, temporary snapshot/debug options, and local/remote helpers were removed; final compact cleanup check returned dashboard/takeover disabled, zero QA users, and zero temporary options. | Completed |
| Remaining coverage | All required Phase 5 coverage is complete | Complete |

### Phase 5 Delegated Form Corrective Release Evidence

| Item | Result | Status |
| --- | --- | --- |
| Corrective release | Public `v0.1.114` adds early WooCommerce address/account form POST delegation and delegated WooCommerce notice output/clearing for branded My Account endpoints | Released |
| Build Release workflow | GitHub Build Release run `29563678035` completed successfully for release `v0.1.114` | Passed |
| Public package | `alynt-account-gateway-v0.1.114.zip`; one root item; 45 runtime files; zero development files; SHA-256 `1847E922AB8D6C50E34C02991294818383E5ED48E4D5EA535E7EA89748EAD174` | Verified |
| Updater detection | Alynt Plugin Updater fresh check detected `0.1.113 -> 0.1.114` and resolved the public release asset URL | Passed |
| Updater install | WordPress/Alynt Plugin Updater installed `0.1.113 -> 0.1.114` from the GitHub release asset on `hbf-staging`; installed header and `ALYNT_AG_VERSION` both report `0.1.114`; no remaining update offer | Passed |
| Installed package shape | Installed copy contains 45 runtime files with no development files; patched WooCommerce integration file is syntax-clean | Verified |
| Post-updater smoke | Disposable customer `9275` logged in through `/login/`; billing address save persisted; account-details display-name validation rendered in the branded shell; valid account-details save persisted | Passed |
| Cleanup and restore | Original settings were restored; disposable user `9275`, temporary updater/smoke helpers, and smoke snapshot option were removed; final compact cleanup check returned `0.1.114`, dashboard/takeover disabled, zero QA users, and no snapshot | Completed |

### Phase 5 Account Email And Password Evidence

| Item | Result | Status |
| --- | --- | --- |
| Baseline | Alynt Account Gateway `0.1.114` and WooCommerce `10.9.4` were active on `hbf-staging`; dashboard and WooCommerce takeover were temporarily enabled from a settings snapshot for one disposable customer. | Verified |
| Invalid email guard | Browser Account Details rejected an invalid email format without changing WordPress `user_email` or WooCommerce `billing_email`; WP-CLI confirmed both values stayed at the original disposable address. | Passed |
| Valid email change | Browser Account Details saved a new email address; the branded dashboard header and form reflected the new address; WP-CLI confirmed both WordPress `user_email` and WooCommerce `billing_email` updated. | Passed |
| Password mismatch | Browser Account Details submitted mismatched new password fields and rendered WooCommerce's `New passwords do not match.` validation notice inside the branded dashboard shell. | Passed |
| Password change | Browser Account Details accepted the current password plus matching new password fields, rendered `Account details changed successfully.`, then a full logout and email-only login with the changed email and new password landed back on the branded dashboard. | Passed |
| Logout wording spot-check | The branded logout confirmation screen used `Confirm Logout` as the screen title and `Log Out` for the action button, avoiding duplicated title/button wording. | Passed |
| Cleanup and restore | Original settings were restored; disposable user `9276`, temporary snapshot option, local helpers, and remote helpers were removed; final cleanup check returned `0.1.114`, zero matching QA users, and no helper files. | Completed |
| Remaining coverage | All required Phase 5 coverage is complete. | Complete |

### Phase 5 Saved Payment Methods Evidence

| Item | Result | Status |
| --- | --- | --- |
| Baseline | Alynt Account Gateway `0.1.114` and WooCommerce `10.9.4` were active on `hbf-staging`; enabled gateways were PayPal (`ppcp`) and NMI (`nmi`), neither of which advertised standalone add-payment-method/tokenization support. | Verified |
| Temporary data | One disposable customer and two WooCommerce card tokens were created under the enabled `nmi` gateway; dashboard and WooCommerce takeover were enabled only from a settings snapshot. | Created and removed |
| Saved-method list | Browser login through `/login/` rendered `/my-account/payment-methods/` inside the branded dashboard with WooCommerce's native table, Visa ending in `1111`, MasterCard ending in `4242`, expiry `12/30`, and native action links. | Passed |
| Set default | Browser `Make default` action on MasterCard ending in `4242` redirected back to the branded saved-methods page, showed WooCommerce's success notice, moved the `Make default` action to the old Visa token, and WP-CLI confirmed token `4242` became default. | Passed |
| Delete non-default | Browser `Delete` action removed the old Visa token, showed WooCommerce's `Payment method deleted.` notice inside the branded shell, and WP-CLI confirmed only token `4242` remained. | Passed |
| Add-payment-method availability | `/my-account/add-payment-method/` rendered inside the branded dashboard with Add Payment Method guidance and WooCommerce's provider message: `New payment methods can only be added during checkout. Please contact us if you require assistance.` No payment-provider settings were changed. | Passed as unsupported on this staging configuration |
| Delete default and empty state | Browser `Delete` action removed the remaining default token, rendered WooCommerce's `No saved methods found.` empty state inside the branded shell, and WP-CLI confirmed zero customer tokens remained. | Passed |
| Cleanup and restore | Original settings were restored; disposable user `9277`, temporary snapshot option, local helpers, and remote helpers were removed; final cleanup check returned `0.1.114`, zero matching QA users, and no helper files. | Completed |
| Remaining coverage | All required Phase 5 coverage is complete. | Complete |

### Phase 5 Unavailable Endpoint Fallback Evidence

| Item | Result | Status |
| --- | --- | --- |
| Baseline finding | Public `v0.1.115` installed through Alynt Plugin Updater, but a temporary empty WooCommerce endpoint rendered the branded dashboard shell, header, and sidebar without the intended fallback because WooCommerce emitted only an empty notices wrapper. | Found |
| Corrective source change | Public `v0.1.116` treats an empty WooCommerce notices wrapper as no delegated endpoint content, allowing the branded fallback to render; focused tests cover empty output, empty notices-wrapper output, and real endpoint-output passthrough. | Released |
| Public release | GitHub Build Release run `29569723527` completed successfully for release `v0.1.116`; public ZIP SHA-256 `3290CC8FBC132D6D76F8F59D78510B9B0480C28A5BC77154E789406796918B6E`, 45 runtime files, and zero development files. | Verified |
| Updater install | Alynt Plugin Updater installed `0.1.115 -> 0.1.116` on `hbf-staging`; installed header and `ALYNT_AG_VERSION` both report `0.1.116`; installed package has 45 runtime files and no development files. | Passed |
| Browser/HTTP acceptance | Disposable customer login through the branded login flow and authenticated request to `/my-account/alynt-empty-endpoint/` rendered `Alynt Empty Endpoint`, `Account section unavailable`, `This area is not ready yet`, the WooCommerce no-content message naming the endpoint, and the recovery links `Back to dashboard` and `Manage account details`. | Passed |
| Cleanup and restore | Original settings were restored; disposable user `9280`, temporary MU plugin, remote helpers, local helpers, cookies, and captured temporary HTML were removed; final cleanup check returned plugin `0.1.116`, zero matching QA users, and no helper leftovers. | Completed |

### Phase 5 Role/Admin Policy Recheck Evidence

| Item | Result | Status |
| --- | --- | --- |
| Baseline | Alynt Account Gateway `0.1.116` was active on `hbf-staging`; Frontend Output, dashboard, and WooCommerce takeover were temporarily enabled from a settings snapshot for three disposable role users. | Verified |
| Administrator access | Disposable administrator logged in through `/login/`, reached `/wp-admin/` at HTTP 200, saw the native admin dashboard, and retained the frontend admin toolbar. | Passed |
| Shop manager access | Disposable shop manager logged in through `/login/`, reached `/wp-admin/` at HTTP 200, saw the native admin dashboard, and retained the frontend admin toolbar through `manage_woocommerce`. | Passed |
| Customer blocking | Disposable customer logged in through `/login/`; `/wp-admin/` redirected to `/my-account/`, rendered the branded Account Dashboard with first-name greeting, and did not render frontend admin-bar markers. | Passed |
| Cleanup and restore | Original settings were restored; disposable users `9281`, `9282`, and `9283`, temporary remote helpers, local helpers, cookie jars, and captured HTML were removed; final cleanup check returned plugin `0.1.116`, zero matching role-test users, no snapshot option, and no helper leftovers. | Completed |

### Phase 5 Extension Compatibility Evidence

| Item | Result | Status |
| --- | --- | --- |
| Target extension inventory | Active staging stack included PayPal Payments (`pymntpl-paypal-woocommerce`), NMI (`wp-nmi-gateway-pci-woocommerce`), USPS shipping, Shipping Insurance Manager, WooCommerce PDF invoices, WooCustomizer, Kadence WooCommerce Email Designer, Automations, and multiple Alynt WooCommerce companion plugins. No WooCommerce Subscriptions or Memberships plugin was active in the target inventory. | Recorded |
| Temporary data | One disposable customer and one hidden physical product were created; Frontend Output, dashboard, and WooCommerce takeover were enabled only from an Account Gateway settings snapshot. | Created and removed |
| Gateway and shipping runtime | WooCommerce registered enabled PayPal (`ppcp`) and NMI (`nmi`) gateways; USPS was registered as a shipping method. Disabled gateway variants remained disabled and were not changed. | Verified |
| Cart smoke | Authenticated disposable customer added the hidden QA product through WooCommerce cart URL; cart rendered the product and checkout link without fatal or critical error output. | Passed |
| Checkout smoke | Checkout rendered the QA product, billing fields, payment container, PayPal method, NMI credit-card fields, USPS shipping rates, Shipping Insurance controls/assets, and PayPal frontend settings without Account Gateway taking over the checkout page and without fatal or critical error output. | Passed |
| Transaction boundary | No payment was attempted and no order was placed; the slice tested rendering/compatibility only. | Preserved |
| Cleanup and restore | Original settings were restored; disposable user `9284`, product `22315`, temporary remote helpers, local helpers, cookie jar, and captured HTML were removed; final cleanup check returned plugin `0.1.116`, zero matching QA users/products, no snapshot option, and no helper leftovers. | Completed |

## Phase 6: Compatibility And Experience Matrix

- [x] Test the minimum supported WordPress and PHP versions.
- [x] Test the current supported WordPress, PHP, and WooCommerce versions.
- [x] Test at least one default WordPress theme and the target site's production theme/builder.
- [x] Test with representative caching, security, SMTP, and WooCommerce extension combinations.
- [x] Verify login, registration, reset, logout, dashboard, and WooCommerce routes at mobile and desktop widths.
- [x] Verify the 800px gateway layout boundary and narrow admin settings layouts.
- [x] Verify keyboard-only navigation, visible focus, error association, live regions, and password controls.
- [x] Verify zoom, reflow, high contrast, reduced motion, and resilient color contrast.
- [x] Verify RTL layout and at least one translated locale across frontend and admin screens.
- [x] Verify no unexpected remote font, tracking, or third-party request is introduced by default.
- [x] Verify browser console, PHP logs, diagnostics, and network responses contain no plugin-caused errors.

### Compatibility Matrix

| Environment | WordPress | PHP | WooCommerce | Theme / Builder | Locale / RTL | Status | Evidence |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Minimum supported | `6.0` declared; post-6.0 API source review passed | `7.4` declared; PHPCompatibilityWP `testVersion=7.4-` passed | Optional integration; not required for support-floor install | Theme-independent static checks plus default-theme smoke below | `en_US` / LTR | Passed support-floor static compatibility | Phase 6 minimum-version and default-theme evidence |
| Current supported | `7.0.1` | `8.2.32` | `10.9.4` | Blocksy Child / Blocksy `2.1.48`; Brizy `2.8.16` and Brizy Pro `2.8.9` active | `en_US` / LTR | Passed target-current baseline | Phase 6 target-current environment evidence |
| Target staging | `7.0.1` | `8.2.32` | `10.9.4`; HPOS disabled | Blocksy Child / Blocksy `2.1.48`; Blocksy Companion Pro `2.1.49` | `en_US` / LTR baseline; `es_ES` / LTR QA; `ar` / RTL QA | Passed target-current baseline and locale QA | Phase 6 target-current and multilingual/RTL evidence |
| Local default-theme smoke | `7.0.1` | `8.5.1` | `10.9.4` active | Twenty Twenty-Five `1.5` | `en_US` / LTR | Passed gateway route smoke | Phase 6 minimum-version and default-theme evidence |

### Phase 6 Target-Current Environment Evidence

| Item | Result | Status |
| --- | --- | --- |
| Declared support floor | Plugin header and readme declare `Requires at least: 6.0` and `Requires PHP: 7.4`; stable tag and runtime version are `0.1.119`. | Recorded |
| Runtime versions | `hbf-staging` reports WordPress `7.0.1`, PHP `8.2.32`, MySQL/MariaDB `10.11.18`, WooCommerce `10.9.4`, and Alynt Account Gateway `0.1.119`. | Passed |
| Theme and locale | Active theme is Blocksy Child using parent Blocksy `2.1.48`; locale is `en_US`; RTL is false. No default Twenty Twenty-Four/Twenty Twenty-Five/Twenty Twenty-Six theme is installed on the target. | Recorded |
| WooCommerce mode | WooCommerce HPOS/custom orders table usage is disabled on the target. | Recorded |
| Cache/security/mail stack | Representative active stack includes Redis Object Cache `2.8.0`, Nginx Helper `9.9.10`, Force Login `5.6.3`, BBQ Pro `3.9`, Blackhole for Bad Bots `3.8.2`, WP fail2ban `5.4.1`, and FluentSMTP `2.2.95`. | Passed as target-current stack |
| Builder/account/payment/shipping stack | Representative active stack includes Blocksy Companion Pro `2.1.49`, Brizy `2.8.16`, Brizy Pro `2.8.9`, WooCommerce `10.9.4`, PayPal Payments `2.0.22`, NMI gateway `1.2.11`, USPS Shipping `5.5.8`, Shipping Insurance Manager `1.8`, WooCommerce PDF invoices `5.15.2`, and Alynt WooCommerce companion plugins. | Passed as target-current stack |
| Enabled payment and shipping runtime | Enabled gateways are PayPal (`ppcp`) and NMI (`nmi`); registered shipping methods include flat rate, free shipping, local pickup, and USPS. | Recorded |
| Scope boundary | Default-theme and support-floor compatibility were intentionally not claimed from the production-like staging pass because switching the live staging theme or changing WordPress/PHP versions would be disruptive; those were handled separately with static compatibility tooling and LocalWP Plugin Tester. | Preserved |

### Phase 6 Mobile/Desktop Route Matrix Evidence

| Item | Result | Status |
| --- | --- | --- |
| Setup | Public `v0.1.116` remained installed on `hbf-staging`; Frontend Output, registration, dashboard, and WooCommerce takeover were temporarily enabled from a settings snapshot for one disposable customer. | Completed |
| Viewports | Browser automation covered `390x844` mobile and `1440x1000` desktop widths. | Passed |
| Public auth routes | `/login/`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=setpassword`, and `/account?action=logout` returned HTTP 200, rendered the Alynt gateway marker, avoided native WordPress login/admin-bar output, and had no horizontal overflow. Mobile auth routes hid the split media panel; desktop auth routes displayed it. | Passed |
| Set-password validation | An initial stale reset key rendered the branded invalid/reset-request fallback; server-side validation returned `invalid_key`. A fresh disposable reset key then rendered the expected `Set New Password` screen with two password fields, password-strength UI, requirements list, and `Save Password` button at both mobile and desktop widths. | Passed |
| Dashboard and WooCommerce routes | Authenticated `/my-account/`, `/my-account/orders/`, `/my-account/edit-address/`, and `/my-account/edit-account/` returned HTTP 200, rendered the Alynt dashboard shell, avoided native login/admin-bar output for the customer, displayed expected dashboard/WooCommerce copy, and had no horizontal overflow. | Passed |
| Cleanup and restore | Original settings were restored; disposable user `9285`, temporary remote helpers, local helpers, snapshot option, and reset-key helpers were removed. Final cleanup returned plugin `0.1.116`, zero matching route-matrix QA users, no snapshot option, and no helper leftovers. | Completed |

### Phase 6 800px Boundary And Narrow Admin Layout Evidence

| Item | Result | Status |
| --- | --- | --- |
| Frontend gateway boundary | Public auth screens were checked at `799x900`, `800x900`, and `801x900` with Frontend Output, registration, dashboard, and WooCommerce takeover temporarily enabled from a settings snapshot. `799px` and `800px` used the single-column shell with media hidden; `801px` switched to the two-column grid with media visible. Login, registration, lost password, set password, and logout routes returned HTTP 200, rendered the Alynt gateway marker, avoided native WordPress login/admin-bar output, and had no horizontal overflow. | Passed |
| Logout copy correction check | The logout screen displayed title `Confirm Logout` and button `Log Out`, keeping title and primary action distinct. | Passed |
| Admin tabs | Authenticated settings tabs `general`, `webhooks`, `privacy`, and `advanced` were checked at `1024`, `960`, `800`, `640`, and `480` pixel widths. The tab wrapper used flex wrapping, tabs did not overlap, and no tab extended outside the viewport. | Passed |
| Narrow admin finding | At `480px`, the Webhooks tab produced page-level horizontal overflow because the plugin-owned `widefat` webhook log table extended beyond the viewport. | Finding `P6-001` |
| Corrective release | Public `v0.1.117` contains a scoped `@media (max-width: 640px)` rule that makes plugin admin `widefat` tables block-level and internally scrollable. The GitHub Build Release workflow passed; the public ZIP had 45 runtime files, zero development files, one plugin root, aligned `0.1.117` metadata, SHA-256 `AB97950E591E9BF571426757FAEA3A095932D48B6EEE5A3788ADB940162F0507`, and the packaged admin CSS fix. Alynt Plugin Updater detected `0.1.116 -> 0.1.117` and installed the public release asset on `hbf-staging`. | Released and updater-verified |
| Installed retest | The installed `v0.1.117` Webhooks settings tab at `480px` had no page-level horizontal overflow, retained wrapping tabs with no overlap or off-viewport tabs, and contained the webhook log table with `display: block` and `overflow-x: auto`. | Passed |
| Cleanup and restore | Original settings were restored; disposable users `9286` and `9287`, temporary remote helpers, local browser test state, and snapshot option were removed. Final cleanup returned plugin `0.1.116`, zero matching boundary QA users, no snapshot option, and no helper leftovers. | Completed |

### Phase 6 Keyboard, Focus, Live Region, And Password-Control Evidence

| Item | Result | Status |
| --- | --- | --- |
| Setup | Public `v0.1.117` remained installed on `hbf-staging`; Frontend Output, registration, dashboard, and WooCommerce takeover were temporarily enabled from a settings snapshot for one disposable administrator and one disposable customer. | Completed |
| Public screen focus | Login, lost-password, logout, and set-password screens at `390x844` and `1440x1000` exposed keyboard-reachable controls with visible focus and no native WordPress account markers. The set-password tab-order check wrapped to `body` after the final focusable control because the submit button is disabled until valid password input; actual password fields and visibility toggles retained visible focus. | Passed |
| Error association and live regions | Login, registration, lost-password, and set-password error states rendered alert/live-region notices. Login, registration, and lost-password invalid fields were associated with error text through `aria-describedby`; set-password rendered the alert state. | Passed |
| Password controls | Set-password controls exposed two visibility toggles with `aria-controls` and `aria-pressed`, a polite atomic strength status region, six readable requirement items, disabled submit state before valid input, and enabled submit state after a valid matching password. | Passed |
| Registration agreement-link finding | The registration Terms and Privacy Policy links inside `.agw-checkbox` only received the browser default dotted focus outline instead of the plugin's visible gateway focus treatment. | Finding `P6-002` |
| Corrective release | Public `v0.1.118` includes `.agw-checkbox a:focus` and `.agw-checkbox a:focus-visible` in the frontend focus selector groups, including the forced-colors block. The GitHub Build Release workflow passed; the public ZIP had one plugin root, 55 runtime entries, zero development files, aligned `0.1.118` metadata, SHA-256 `03B29C3DD8AA36DDBC50B13D639DEF16DB10B423659CB45DB278A6218BAE14AF`, and the packaged frontend CSS focus fix. Alynt Plugin Updater detected `0.1.117 -> 0.1.118` and installed the public release asset on `hbf-staging`. | Released and updater-verified |
| Installed retest | The installed `v0.1.118` registration screen at `390x844` and `1440x1000` rendered the Terms and Privacy Policy links with solid visible focus outlines from the packaged frontend CSS. | Passed |
| Cleanup and restore | Original test settings were restored before release; disposable users `9288` and `9289`, temporary remote helpers, local helper scripts, and snapshot option were removed. Final post-updater cleanup returned plugin `0.1.118`, zero matching accessibility QA users, no snapshot option, no helper leftovers, and no local release artifacts. | Completed |

### Phase 6 Zoom, Reflow, High-Contrast, And Reduced-Motion Evidence

| Item | Result | Status |
| --- | --- | --- |
| Setup | Public `v0.1.118` remained installed on `hbf-staging`; Frontend Output and registration were already enabled. The pass used read-only browser checks against public gateway auth screens without changing settings, users, orders, or plugin data. | Completed |
| 320px reflow | Login, lost-password, logout, and invalid-link screens at `320x1000` returned HTTP 200, rendered the Alynt gateway marker, avoided native WordPress account markers, hid the split media panel, and had no page-level horizontal overflow. | Passed |
| Registration reflow finding | The registration screen at `320x1000` produced page-level horizontal overflow because the Cloudflare Turnstile widget rendered at its normal 300px width inside a narrower verification slot. | Finding `P6-003` |
| High contrast | Login, registration, and lost-password screens were checked with browser `forced-colors: active`; the gateway remained branded-screen-owned, used system foreground/background/link colors, hid decorative media at mobile width, and had no horizontal overflow at `390x844`. | Passed |
| Reduced motion | Login, registration, and lost-password screens were checked with browser `prefers-reduced-motion: reduce`; forced-colors emulation was off, reduced-motion was active, transition and animation durations collapsed to near-zero timing, and there was no horizontal overflow at `390x844`. | Passed |
| Corrective release | Public `v0.1.119` marks plugin-owned Turnstile containers and the frontend bundle sets `data-size="compact"` only when the verification slot is under `300px` wide. The GitHub Build Release workflow passed; the public ZIP had one plugin root, 55 runtime entries, zero development files, aligned `0.1.119` metadata, SHA-256 `569B50E68A63254152AD708C9B8E7CCA3291A6CA3FC3A34DD95930085C51D814`, the packaged Turnstile marker, and bundled compact/normal sizing logic. Alynt Plugin Updater detected `0.1.118 -> 0.1.119` and installed the public release asset on `hbf-staging`. | Released and updater-verified |
| Installed retest | The installed `v0.1.119` registration screen at `320x1000` rendered the Alynt gateway marker, avoided native WordPress account markers, set the Turnstile widget to `data-size="compact"`, kept the verification slot inside the viewport, and had no page-level horizontal overflow. | Passed |

### Phase 6 Request, Console, Log, And Diagnostics Evidence

| Item | Result | Status |
| --- | --- | --- |
| Setup | Public `v0.1.119` remained installed and active on `hbf-staging`; Frontend Output and registration were enabled. The pass used read-only browser, WP-CLI, and server-log checks without changing settings, users, orders, plugin records, or site files. | Completed |
| Browser route sweep | Login, registration, lost-password, logout, and invalid-link routes returned HTTP 200, rendered the Alynt gateway shell, and avoided native WordPress login markers. No page-level JavaScript exception was raised by Account Gateway assets. | Passed |
| Account Gateway asset attribution | Static source review found no Account Gateway remote font, analytics, or tracking enqueue. The plugin enqueues local frontend/admin assets, Cloudflare Turnstile only on the registration screen when configured, and server-side Reoon/Turnstile verification only during registration validation. | Passed |
| Staging request attribution | Current staging pages load site-stack third-party assets from GetTerms, visitor tracking, Font Awesome CDNs, Bunny Fonts, and a browser-local AdGuard endpoint, plus Cloudflare Turnstile on the configured registration screen. These requests were not introduced by the plugin's default output and are attributed to existing site plugins/theme, browser environment, or the explicitly configured Turnstile provider. | Recorded |
| Console/network attribution | The repeated unauthenticated `wp-json/iawp/search` HTTP 401 console error is attributed to active Independent Analytics Pro. Registration also showed Cloudflare Turnstile challenge warnings/errors, including a provider challenge request failure in the test browser environment. No failing request was attributed to an Account Gateway local asset. | Passed with documented external noise |
| PHP and server logs | `wp-content/debug.log` had no recent output from the pass. The Nginx error log tail showed blocked external probes for sensitive files such as `.git`, `.env`, SQL dumps, and `wp-config` variants; no Account Gateway path, PHP warning, fatal, or notice appeared. | Passed |
| Plugin-owned records | Plugin-owned table counts after the pass: pending registrations `0`, consent records `0`, diagnostics logs `0`, verification logs `6`, webhook logs `1`, and audit logs `1`. The non-empty records are existing acceptance artifacts, and this read-only pass created no new plugin diagnostics rows. | Recorded |

### Phase 6 Multilingual And RTL Evidence

| Item | Result | Status |
| --- | --- | --- |
| Setup | Public `v0.1.119` remained installed and active on `hbf-staging`. WordPress `es_ES` and `ar` language packs were installed, temporary QA-only Account Gateway `.mo` files were placed in the plugin language paths, and a temporary MU plugin switched locale only for requests carrying the QA query parameter. The site baseline language stayed `en_US`. | Completed |
| Translated LTR frontend | Login, registration, lost-password, and invalid-link routes rendered Spanish QA translations for Account Gateway screen titles, returned HTTP 200, kept `lang="es"` and LTR direction, rendered the Alynt gateway shell, avoided native WordPress login markers, and had no horizontal overflow at `390x844`. | Passed |
| Translated LTR admin | The authenticated Account Gateway settings page rendered Spanish QA translations for the plugin title and Save Settings action, returned HTTP 200, and did not show the native login form. | Passed |
| RTL frontend | Login, registration, lost-password, and invalid-link routes rendered Arabic QA translations at `390x844` and `1440x1000`, returned HTTP 200, kept `lang="ar"`, set document and gateway direction to RTL, kept email fields LTR, rendered the Alynt gateway shell, avoided native WordPress login markers, and had no horizontal overflow. | Passed |
| RTL admin | The authenticated Account Gateway settings page rendered Arabic QA translations for the plugin title and Save Settings action, returned HTTP 200, exposed RTL markers, and did not show the native login form. | Passed |
| Scope boundary | This pass proves runtime textdomain loading and RTL resilience with temporary QA translations. It does not claim that Spanish or Arabic production translations ship with the plugin. Permanent bundled translations remain a separate localization-content decision. | Preserved |
| Cleanup and restore | The temporary MU plugin, temporary plugin/global `.mo` files, temporary remote helpers, local helper files, and disposable administrator `9290` were removed. Final checks returned `WPLANG` as `en_US`, Account Gateway active at `0.1.119`, zero matching locale QA users, and no QA locale files left on staging. | Completed |

### Phase 6 Minimum-Version And Default-Theme Evidence

| Item | Result | Status |
| --- | --- | --- |
| Support metadata | Plugin header and `readme.txt` declare `Requires at least: 6.0` and `Requires PHP: 7.4`; active development metadata is `0.1.119`. | Recorded |
| PHP support floor | The repository PHPCS configuration includes `PHPCompatibilityWP` with `testVersion=7.4-`; `php ./vendor/bin/phpcs --standard=.phpcs.xml .` and `npm run lint` completed with no findings. | Passed |
| WordPress support-floor review | Static source review checked for obvious post-6.0 WordPress APIs and PHP 8-only syntax patterns in runtime code; no matches were found. This is recorded as source/API compatibility evidence, not as a downgraded WordPress 6.0 runtime test. | Passed with scope note |
| Local default theme environment | LocalWP Plugin Tester was used as the isolated default-theme environment. Novamira reported WordPress `7.0.1`, PHP `8.5.1`, WooCommerce `10.9.4`, active theme Twenty Twenty-Five `1.5`, and Alynt Account Gateway `0.1.119` after the local-only plugin copy was updated from the current runtime files. | Recorded |
| Default-theme route smoke | With Frontend Output temporarily enabled on Plugin Tester, `/login`, `/account?action=lostpassword`, `/account?action=register`, `/account?action=invalidlink`, `/account?action=logout`, and `/account?action=setpassword` returned HTTP 200 with Alynt gateway markup, expected screen text, and no native WordPress login form markers. `/wp-login.php` returned HTTP 302 to `/login` without native-login content. | Passed |
| Cleanup and restore | Plugin Tester Frontend Output was restored to disabled, Twenty Twenty-Five remained active as it was before the pass, and the local Plugin Tester copy remained on `0.1.119` for future QA. A local backup of the prior `0.1.98` plugin folder was kept under `work/acg-plugin-tester-backup-v0.1.98`. | Completed |

## Phase 7: Privacy, Data, And Lifecycle Acceptance

- [x] Confirm the data inventory for pending registrations, consent, verification, webhooks, diagnostics, and audit records.
- [x] Verify data minimization and redaction in settings, logs, exports, webhooks, and support evidence.
- [ ] Verify Terms and Privacy consent capture and the site's legal copy ownership.
- [x] Verify WordPress personal-data exporter output for plugin-owned records.
- [x] Verify WordPress personal-data eraser behavior and documented exceptions.
- [ ] Verify configured retention cleanup schedules and manual cleanup controls.
- [ ] Verify webhook payload-body storage remains disabled unless debugging is deliberately enabled.
- [ ] Verify disabling or uninstalling the plugin does not remove WordPress users, WooCommerce orders, or unrelated media.
- [ ] Verify uninstall removes only documented plugin-owned options, tables, scheduled hooks, and transient data.
- [ ] Review GDPR-facing documentation with the site owner or qualified adviser where required.

### Phase 7 Data Inventory And Minimization Evidence

| Store | Data and ownership | Retention / removal | Export / erasure boundary |
| --- | --- | --- | --- |
| `alynt_ag_settings` option | Plugin configuration, including routes, branding references, email templates, provider credentials, webhook configuration, and retention values | Kept while installed; removed on uninstall | Portable JSON now omits schema fields typed as secret, email, or attachment ID; the WordPress personal-data exporter does not treat site configuration as customer data |
| `alynt_ag_db_version` option | Plugin schema version only | Kept while installed; removed on uninstall | No personal data |
| Pending registrations table | Email, first/last name, optional created user ID, one-way confirmation-token hash, lifecycle status, and timestamps | Expired rows removed by daily retention cleanup; uninstall drops the table | WordPress exporter returns the matching email's non-secret fields; eraser deletes matching rows |
| Verification logs table | Email, provider, result/status, blocked flag, manual review decision, reviewing administrator ID, and timestamps | Default 30 days; configurable daily cleanup; uninstall drops the table | WordPress exporter returns the subject's result and review outcome but omits the reviewing administrator ID; eraser deletes matching rows |
| Consent records table | User ID when available, email, Terms/Privacy paths, registration context, plugin version, settings hash, and timestamp | Default 365 days; configurable daily cleanup; uninstall drops the table | WordPress exporter returns the subject's legal paths, context, version, and timestamp; eraser deletes by email and by user ID |
| Webhook logs table | Event, user ID, destination host, response status/success, retry count, optional debug payload, sanitized error, and timestamp | Successful rows default to 7 days; failed rows default to 30 days; uninstall drops the table | WordPress exporter returns delivery metadata only; eraser deletes rows linked to the user ID |
| Audit logs table | Acting user ID, action, recursively redacted context, and timestamp | Default 180 days; configurable daily cleanup; uninstall drops the table | Eraser deletes rows linked to the user ID; exporter acceptance remains in the dedicated exporter slice |
| Diagnostics logs table | Severity, category, event code, summary, recursively redacted context, correlation ID, and timestamp | Disabled by default; default 30 days when enabled; manual clear and daily cleanup; uninstall drops the table | Support CSV is administrator-only; direct email keys are redacted before storage; personal-data exporter/eraser scope remains in the dedicated runtime slice |
| Rate-limit transients | HMAC bucket derived from action, normalized identifier, and source IP; separate aggregate metadata contains action/count/limit/window/lock state without raw identifiers | Expires with the configured action window; uninstall removes only `alynt_ag_rl_` and `alynt_ag_rl_meta_` transient families | Raw email/IP values are not stored; no personal-data export is produced for non-reversible bucket keys |
| Daily retention event | Scheduled hook name and next-run timestamp | Unschedule on deactivation; clear on uninstall | No personal data |
| WordPress users, WooCommerce data, and media | WordPress user/account records are created through core; WooCommerce owns orders/account data; plugin settings reference media attachment IDs | Not removed on disable, deactivation, or uninstall | Remain under WordPress/WooCommerce privacy and lifecycle ownership |

Additional source evidence:

- Registration confirmation tokens are stored only as password hashes; raw confirmation tokens exist only for link delivery.
- Registration consent deliberately stores no IP address.
- Admin registration tables mask email addresses, and webhook logs retain only the destination host rather than the full delivery URL.
- Webhook payload bodies remain `NULL` unless Debug Payload Logging is deliberately enabled; the default is disabled.
- Portable settings exports no longer carry the emergency bypass, Turnstile secret, Reoon API key, webhook signing secret, test recipient, logo ID, or background-image ID.
- The personal-data exporter now uses email-only consent lookup when no WordPress user exists, preventing an `OR user_id = 0` match from including unrelated pending registrants.
- Audit and diagnostics context redaction now masks `email`, `user_email`, and `email_address` recursively before storage.
- Local `v0.1.120` candidate validation passed the production build, POT generation (`1004 strings`), PHPCS, full PHPUnit (`299 tests, 1973 assertions`), npm audit, Composer audit, and diff checks.
- The inspected 45-file runtime package at `C:\Users\Captain\Desktop\alynt-account-gateway-0.1.120.zip` contains no development files or stale `build/` artifacts, aligns all `0.1.120` metadata, includes the three privacy-hardening markers, and has SHA-256 `7F405592AEF58CC336B22BCB8005027E6CBDB4818B819DF6F21B29CAE5B1ACE2`.
- Public `v0.1.120` release evidence: GitHub Build Release workflow `29583500288` passed, the public asset `alynt-account-gateway-v0.1.120.zip` contains 45 runtime files under one plugin root with no development files, all `0.1.120` metadata is aligned, and the public ZIP SHA-256 is `9CA485D6502820806A44D11C540621EBA07C1B74852D8470663A0AF863C5CB3B`.
- Updater evidence: Alynt Plugin Updater `1.1.1` force-refreshed managed release metadata and installed `0.1.119 -> 0.1.120` on `hbf-staging` from the public GitHub asset. The installed plugin remained active at `0.1.120`, `/account` returned HTTP 200, and non-secret settings plus active-plugin hashes matched the pre-update baselines.
- Runtime exporter evidence on `hbf-staging` used disposable run `20260717133148` against the registered WordPress privacy callbacks. The pending/non-user subject exported exactly the expected Account Gateway consent, pending registration, and email-verification groups, and it omitted an unrelated `user_id = 0` consent control row. The real disposable user subject exported Account Gateway consent and webhook delivery metadata and did not export audit-log context.
- Runtime eraser evidence for the same run passed: pending registration, verification, consent, webhook, and audit plugin-owned rows were removed as documented; the WordPress user was retained after the eraser to preserve WordPress ownership boundaries; and fixture cleanup then explicitly deleted the disposable user and unrelated control row. Follow-up cleanup verification reported zero matching pending, consent, verification, webhook, audit, and user records.

## Phase 8: Documentation And Operations

- [ ] Document installation, update, rollback, activation, and initial configuration.
- [ ] Document frontend-output staging and emergency-disable procedures.
- [ ] Document emergency bypass purpose, storage, rotation, and recovery limitations.
- [ ] Document account-creation, username-generation, email, dashboard, and WooCommerce settings.
- [ ] Document Turnstile, Reoon, webhook, SMTP, and DNS ownership boundaries.
- [ ] Document preview, test-send, diagnostics, import/export, reset, retention, and privacy tools.
- [ ] Document known limitations, extension interactions, and support boundaries.
- [ ] Create a site-owner acceptance checklist using non-technical language.
- [ ] Define supported WordPress, PHP, WooCommerce, browser, and updater versions.
- [ ] Define semantic-versioning, backward-compatibility, migration, and deprecation policy.
- [ ] Define defect severity, support response, security-reporting, and release rollback procedures.
- [ ] Confirm the README, settings reference, hooks reference, changelog, POT, and release notes are current.

## Phase 9: v1.0 Release Candidate

- [ ] Resolve or formally defer every acceptance finding with owner, severity, rationale, and retest evidence.
- [ ] Confirm no critical or high-severity defects remain open.
- [ ] Confirm the repository starts from clean, released, and updater-verified `master`.
- [ ] Create the `release/1.0.0` branch only after acceptance scope is approved.
- [ ] Align plugin header, constant, npm metadata, stable tag, changelog, README, tests, and POT at `1.0.0`.
- [ ] Run production build, PHPCS, full PHPUnit suite, POT generation, npm audit, Composer audit, and diff checks.
- [ ] Build and inspect the runtime ZIP for one root, forward-slash entries, runtime-only contents, aligned metadata, and updater headers.
- [ ] Install the exact release candidate on the acceptance target and repeat critical account, email, integration, and WooCommerce smoke tests.
- [ ] Verify the settings fingerprint, activation state, test-data cleanup, and rollback package.
- [ ] Complete privacy and secret preflight before publication.
- [ ] Obtain explicit release approval.
- [ ] Commit, merge, tag, push, and publish the GitHub release.
- [ ] Inspect the generated public release asset and record its SHA-256 digest.
- [ ] Verify Alynt Plugin Updater discovers and installs public `v1.0.0` from the previous public baseline.
- [ ] Run post-updater browser and runtime smoke tests.
- [ ] Remove temporary users, orders, credentials, helper files, packages, and evidence containing sensitive data.
- [ ] Record release, workflow, public-asset, updater, cleanup, and final-site-state evidence in this plan.

## v1.0 Go / No-Go Gate

Release is approved only when all statements below are true:

- [ ] Every required phase above is complete or has an explicitly approved, non-blocking deferral.
- [ ] No critical or high-severity security, authentication, registration, email, data-loss, privacy, or WooCommerce defect remains open.
- [ ] No standard customer journey unexpectedly reaches an unbranded native WordPress account screen.
- [ ] Frontend Output can be disabled safely, and the emergency bypass behaves exactly as documented.
- [ ] Real email, protection-provider, webhook, and WooCommerce acceptance evidence is recorded.
- [ ] Minimum, current, and target compatibility environments pass their required scenarios.
- [ ] Accessibility, RTL, multilingual, responsive, and theme-compatibility evidence is complete.
- [ ] Privacy, retention, exporter, eraser, uninstall, backup, and rollback behavior is documented and verified.
- [ ] Site-owner documentation and support ownership are ready before publication.
- [ ] The exact public asset passes package inspection and Alynt Plugin Updater verification.
- [ ] Final release approval is recorded.

## Finding Register

| ID | Phase | Severity | Finding | Owner | Decision | Retest Evidence | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `P0-001` | 0 / 1 | High | WP Custom Login Manager and Force Login overlapped Alynt Account Gateway routes and authentication redirects before handover. | Product + site owner | Closed for `hbf-staging` by controlled handover: ACG owns configured gateway routes, WP Custom Login Manager is inactive, Force Login remains active with a narrow route bypass, and the emergency bypass is verified. | `v0.1.108` Force Login compatibility, `v0.1.109` canonical redirect correction, `v0.1.110` Turnstile script cleanup, route checks, browser smoke, and updater installation evidence | Closed for staging |
| `P0-002` | 0 | Medium | The incumbent login page throws `this.initKeyboardNavigation is not a function`. | Incumbent plugin owner / site owner | Preserve as a pre-install baseline; verify it disappears with the controlled handover and does not recur in Alynt Account Gateway. | Browser console comparison after handover | Open baseline |
| `P0-003` | 0 | Low | WP-CLI reports early text-domain loading for `wp-custom-login-manager`. | Incumbent plugin owner / site owner | Preserve as a pre-install baseline and keep it out of Alynt Account Gateway defect attribution. | Post-handover WP-CLI comparison | Open baseline |
| `P0-004` | 0 | Low | Unrelated Brizy, Brizy Pro, and Presto Player Pro updates were available during baseline capture. | Site owner | Freeze unrelated updates during acceptance unless separately approved; record any unavoidable drift. | Version comparison before each acceptance run | Monitoring |
| `P0-005` | 0 / 1 | Low | The incumbent Turnstile script warned that its `onTurnstileReady` callback was unavailable at load time. | Incumbent plugin owner / site owner | Preserve as an incumbent-only baseline; zero Alynt Account Gateway assets were loaded when reproduced. ACG registration now loads Turnstile without a fresh console warning after `v0.1.110`. | Browser console comparison after handover | Closed baseline |
| `P1-001` | 1 | Medium | Customer wp-admin blocking and admin-bar filtering were registered while Frontend Output was disabled. The existing `hbf-staging` stack masked the behavior, producing no active/inactive runtime delta. | Product owner | Frontend Output is the master safety toggle; both policies now honor it in `v0.1.99`. | Disposable-role comparison, isolated regression tests, exact-package Plugin Tester smoke, public asset verification, Alynt Plugin Updater installation, and installed-copy staging role retest | Closed |
| `P1-002` | 1 | Medium | The configured `/terms/` and `/privacy/` paths did not resolve directly, and their redirected `/legal/terms/` and `/legal/privacy/` destinations were not anonymously reachable while Force Login owned public access. | Site owner | Use `/legal/terms/` and `/legal/privacy/` as the Account Gateway settings. Do not exempt them from Force Login on `hbf-staging`, because Force Login is staging-only. | Saved settings, published custom `legal` post mappings, and anonymous HTTP 302 confirmation | Closed for staging |
| `P1-003` | 1 | Medium | Gateway Screen Preview links on `hbf-staging` redirected to `/wp-admin/` instead of rendering the standalone preview page. | Product owner | Closed by `v0.1.104`: preview links use a nonce-protected front-end endpoint with compact screen codes to avoid incumbent login-redirect substring matching. | Public release inspection, Alynt Plugin Updater install, and authenticated compact-code preview retest returning `HTTP/1.1 200 OK` with zero redirects | Closed |
| `P1-004` | 1 / 3 | Medium | Account-email logo rendered at raw source-image scale in a real mailbox, overwhelming the email layout. | Product owner | Closed by `v0.1.105`: explicit email logo width attributes and inline constrained dimensions were released, installed, resent, and accepted in a real mailbox screenshot. | Local candidate tests, package inspection, updater install `0.1.104 -> 0.1.105`, installed-file verification, five resent account-email templates, and mailbox screenshot confirmation | Closed |
| `P1-005` | 1 / 3 | Low | Account-email body text felt too small during real mailbox review after logo correction. `v0.1.106` did not visibly change the real mailbox because the client ignored the wrapper/media-query approach. | Product owner | Closed by `v0.1.107`: inline paragraph/list-item body sizing was released, installed, resent, and accepted in real mailbox review on mobile and desktop. | Focused and full candidate tests, PHPCS, syntax sweep, build, POT regeneration, package inspection, updater install `0.1.106 -> 0.1.107`, installed-file verification, five resent account-email templates, and mailbox screenshot confirmation | Closed |
| `P2-001` | 2 | High | Branded auth and registration form POSTs were rendered before they could be processed after the `v0.1.109` canonical-redirect fix moved gateway rendering earlier. | Product owner | Closed by `v0.1.111`: auth and registration POST handlers now run at `template_redirect` priority `0`, ahead of gateway rendering at priority `1`, with regression coverage preserving both handler order and canonical redirect behavior. | Failed `v0.1.110` disposable registration POST with zero pending/consent rows; focused and full tests; PHPCS; build; POT; syntax sweep; public ZIP inspection; updater install `0.1.110 -> 0.1.111`; successful registration POST redirecting to `registration_sent=1` with pending/consent rows and zero created user accounts | Closed |
| `P2-002` | 2 | Medium | A validated same-site `redirect_to=/login/` target could send a successfully logged-in user back to the branded login form. | Product owner | Closed by `v0.1.112`: post-login destinations pointing to branded login, the account action base, or native `wp-login.php` now fall back to the configured after-login URL. | Initial redirect matrix finding; focused auth tests; full release checks; public ZIP inspection; Alynt Plugin Updater install `0.1.111 -> 0.1.112`; safe/external/auth-surface redirect matrix retest; disposable-user cleanup | Closed |
| `P2-003` | 2 | Medium | Set-password requirement feedback used disabled checkbox semantics for read-only password criteria. | Product owner | Closed by `v0.1.113`: requirement items now expose readable `Met` / `Not met` labels and the strength live region includes progress counts. | Source and renderer regression tests; full release checks; public ZIP inspection; Alynt Plugin Updater install `0.1.112 -> 0.1.113`; staging set-password screen retest with six labels, live status, and no checkbox/checked requirement semantics | Closed |
| `P5-001` | 5 | High | Delegated WooCommerce My Account address/account form POSTs could be rendered before WooCommerce processed them; WooCommerce notices were also not printed/cleared by the branded delegated endpoint renderer, allowing stale validation notices to block later saves. | Product owner | Closed by `v0.1.114`: WooCommerce address/account POST handlers now run before gateway rendering, billing/shipping endpoint values are primed for WooCommerce, and delegated endpoint notices render before endpoint content. | Failed billing save before patch; focused `WooCommerceIntegrationTest`; staging billing save/validation, shipping save after stale-notice clear, account-details validation/save; Build Release run `29563678035`; public ZIP inspection; updater install `0.1.113 -> 0.1.114`; post-updater billing/account smoke; cleanup verification | Closed |
| `P5-002` | 5 | Medium | Empty delegated WooCommerce account endpoints could render a blank branded content area because the renderer treated WooCommerce's empty notices wrapper as endpoint output. | Product owner | Closed by `v0.1.116`: empty delegated output and empty notices-wrapper output now fall through to the branded unavailable endpoint fallback. | Failed `v0.1.115` post-updater temporary endpoint smoke; focused `WooCommerceIntegrationTest`; Build Release run `29569723527`; public ZIP inspection; updater install `0.1.115 -> 0.1.116`; authenticated staging fallback acceptance; cleanup verification | Closed |
| `P6-001` | 6 | Low | The narrow Webhooks settings tab could create page-level horizontal overflow at `480px` because the plugin-owned `widefat` webhook log table exceeded the viewport. | Product owner | Closed by `v0.1.117`: plugin-owned admin `widefat` tables become internally scrollable at narrow widths instead of causing page-level overflow. | Initial `480px` Webhooks tab browser check; offender inspection identified the `widefat` table; exact-rule browser injection removed page-level overflow; local build, PHPCS, PHPUnit, POT, and diff checks passed; public ZIP inspection; Alynt Plugin Updater install `0.1.116 -> 0.1.117`; installed Webhooks `480px` retest | Closed |
| `P6-002` | 6 | Low | Registration Terms and Privacy Policy links inside the agreement checkbox used only the browser default dotted focus outline instead of the plugin's visible gateway focus treatment. | Product owner | Closed by `v0.1.118`: agreement links now receive the same normal, focus-visible, and forced-colors frontend focus treatment as other gateway links and controls. | Initial registration focus inspection at `390x844`; local source patch; build, PHPCS, PHPUnit, POT, and diff checks passed; browser injection of candidate rules; public ZIP inspection; Alynt Plugin Updater install `0.1.117 -> 0.1.118`; installed registration retest at `390x844` and `1440x1000` | Closed |
| `P6-003` | 6 | Low | At `320px`, the registration screen could create page-level horizontal overflow because Cloudflare Turnstile rendered the normal 300px widget inside a narrower verification slot. | Product owner | Closed by `v0.1.119`: plugin-owned Turnstile containers are marked and the frontend bundle sets `data-size="compact"` before Cloudflare renders when the verification slot is under `300px` wide. | Initial `320x1000` registration reflow check; offender inspection identified `.agw-verification-slot` / `.cf-turnstile`; local source patch; build, PHPCS, PHPUnit, POT, and diff checks passed; synthetic built-bundle browser check; public ZIP inspection; Alynt Plugin Updater install `0.1.118 -> 0.1.119`; installed registration retest at `320x1000`; clean reduced-motion retest | Closed |
| `P7-001` | 7 | High | A personal-data export for a valid email with no WordPress user could query consent rows with `email = requested OR user_id = 0`, allowing unrelated unattached consent records to enter the export. | Product owner | Closed by `v0.1.120`: user-ID matching is added only when the requested email resolves to an existing WordPress user; pending registrants use email-only lookup. | Source review, focused privacy exporter regression test, public ZIP inspection, Build Release workflow `29583500288`, and Alynt Plugin Updater install `0.1.119 -> 0.1.120` on `hbf-staging` | Closed |

Severity guidance:

- Critical: Authentication bypass, account takeover, secret/password exposure, destructive data loss, or equivalent immediate risk.
- High: Broken primary account journey, unsafe authorization, major privacy defect, or widespread WooCommerce failure.
- Medium: Important but recoverable workflow, compatibility, accessibility, or operational defect.
- Low: Minor presentation, copy, documentation, or narrow edge-case defect.

## Decision Log

| Date | Decision | Rationale | Evidence / Reference |
| --- | --- | --- | --- |
| 2026-07-14 | Keep v1.0 readiness separate from the completed implementation plan. | Preserves the implementation record and gives production acceptance a focused living roadmap. | `docs/IMPLEMENTATION_PLAN.md` completed at `v0.1.97`. |
| 2026-07-15 | Use `hbf-staging` in `live-only` operating mode for Phase 0 and exclude the production HBF site. | Provides production-like WooCommerce scale without authorizing production work. | User confirmation and Site Operations registry. |
| 2026-07-15 | Keep detailed operational evidence local-only and publish only redacted acceptance facts in this plan. | Protects SSH details, credentials, cookies, customer data, and test identities while retaining auditable release evidence. | Local Phase 0 evidence record and repository privacy rules. |
| 2026-07-15 | Require a controlled handover from WP Custom Login Manager and Force Login. | Both active plugins overlap gateway routes and authentication behavior, so concurrent public ownership would make testing unsafe and ambiguous. | Active-plugin inventory, source inspection, HTTP route checks, and browser baseline. |
| 2026-07-15 | Keep the Phase 0 restore point server-local instead of transferring the production-clone archive to Drime. | Avoids moving private customer and order data to an external destination before encryption, access, retention, and data-processing controls are separately verified. | User approval, private quarantine manifest, and Drime queue/registry checks. |
| 2026-07-15 | Install and activate the exact public `v0.1.98` package while retaining incumbent route ownership and disabled gateway output. | Establishes the production-like configuration baseline without switching public account journeys. | Package hashes, WP-CLI install/activation, settings fingerprints, empty-table counts, HTTP, Playwright, and log checks. |
| 2026-07-15 | Require customer wp-admin blocking and admin-bar filtering to honor Frontend Output. | The master toggle exists so a site can configure the plugin before it changes public/account behavior. HBF staging masks the issue through equivalent incumbent policy, but the plugin contract remains violated on an otherwise neutral stack. | `PHASE_1_ROLE_POLICY_TEST.json`, source inspection, and requirement 24. |
| 2026-07-16 | Use renderer-generated local HTML with inline staging assets and media for visual review when the authenticated admin preview route redirected to `/wp-admin/`. | Preserves disabled public output and allows responsive visual evidence to proceed, while keeping the broken admin preview route open as `P1-003`. | `visual-review/visual-review-results.json`, contact sheets, and authenticated browser redirect observation. |
| 2026-07-16 | Use `/legal/terms/` and `/legal/privacy/` for the staging Account Gateway legal links, without exempting those paths from Force Login. | The legal entries are published custom `legal` posts, and Force Login is intentionally staging-only, so anonymous interception is an accepted staging constraint rather than a plugin blocker. | Saved Account Gateway settings, `url-to-id` checks, published post metadata, and anonymous HTTP 302 responses. |
| 2026-07-16 | Complete the route handover on `hbf-staging` after releasing targeted compatibility fixes through `v0.1.110`. | Force Login needed a narrow gateway-route bypass, WordPress canonical redirects needed to yield to the gateway renderer, and the Turnstile API URL needed to avoid WordPress version query warnings before public route ownership could be accepted. | Public releases `v0.1.108`, `v0.1.109`, and `v0.1.110`; Alynt Plugin Updater installs; HTTP route checks; browser smoke evidence. |
| 2026-07-16 | Treat `v0.1.111` as a blocking Phase 2 corrective release before continuing full account-flow testing. | The first real registration-start POST proved that screen rendering could preempt form handlers, so completing Phase 2 on `v0.1.110` would test a broken submission path. | `P2-001` finding, hook-priority regression test, public release `v0.1.111`, updater install, and successful disposable registration-start retest. |
| 2026-07-16 | Treat `v0.1.112` as a Phase 2 redirect-safety corrective release before marking safe redirect handling complete. | The redirect matrix found same-site auth-surface destinations were host-valid but could return a logged-in user to account/login screens, which violates the no-loop account gateway goal. | `P2-002` finding, redirect regression tests, public release `v0.1.112`, updater install, and safe redirect matrix retest. |
| 2026-07-16 | Treat `v0.1.113` as a Phase 2 accessibility corrective release before marking password strength feedback complete. | The set-password requirements were visually useful but exposed read-only criteria as disabled checkboxes, so the accessibility acceptance item needed a focused correction and retest. | `P2-003` finding, requirement-label regression tests, public release `v0.1.113`, updater install, and staging set-password accessibility retest. |
| 2026-07-17 | Treat `v0.1.114` as a Phase 5 delegated WooCommerce form corrective release before continuing payment-method and extension compatibility coverage. | Real billing/account POST acceptance found the branded dashboard renderer could preempt WooCommerce form handlers and hide stale validation notices, which would break customer account management. | `P5-001` finding, focused tests, public release `v0.1.114`, updater install, and post-updater billing/account smoke. |
| 2026-07-17 | Treat `v0.1.116` as a Phase 5 unavailable-endpoint corrective release before closing WooCommerce fallback coverage. | `v0.1.115` added the branded fallback but real staging acceptance showed WooCommerce's empty notices wrapper was incorrectly counted as endpoint content, leaving a blank panel. | `P5-002` finding, focused tests, public release `v0.1.116`, updater install, and authenticated staging fallback acceptance. |

## Progress Notes

- Created this production-readiness roadmap from the released and updater-verified `v0.1.97` baseline, then advanced the product baseline to released and updater-verified `v0.1.98` before acceptance began.
- Public `v0.1.98` evidence: release commit `93fc9a4`, merge commit `1dbb058`, Build Release workflow run `29408445194`, public asset SHA-256 `63A5EAF0F573874E9D06AF8BDF819B989310DE388EE6639358D25218EFDF0585`, preserved Plugin Tester settings fingerprint, and no remaining update offer or QA artifacts.
- The completed feature implementation history remains in `docs/IMPLEMENTATION_PLAN.md`.
- Phase 0 selected and inventoried `hbf-staging`, explicitly excluded production, recorded the runtime and route-owner baseline, defined test identities, evidence rules, cleanup, viewports, accessibility/language coverage, and transactional boundaries, and captured responsive browser evidence without submitting forms or changing the site.
- The approved Phase 0 restore point completed as WPvivid task `wpvivid-d6e76efae4340`. Its locked 680,105,457-byte package contains database, themes, plugins, uploads, content, core, and package metadata; the outer package and all six nested component archives passed integrity checks, the database component contains SQL, and the package metadata is valid JSON.
- The package was moved immediately into a private server-local directory outside both the web root and Drime scan path. The source copy is absent, file and manifest permissions are `0600`, the directory is `0700`, the manifest hash matches the archive, and the task ID is absent from Drime queue, active-upload, uploaded, and failed registries. Drime automatic scanning remains enabled with its original behavior.
- Post-backup checks confirmed Alynt Account Gateway remains absent and the existing `/login/`, `/wp-login.php`, and `/my-account/` route behavior is unchanged. Production was not accessed.
- Phase 0 is complete.
- Phase 1 preserved a redacted 45-option incumbent snapshot, installed and activated the exact public `v0.1.98` asset, verified its safe activation defaults, recorded gateway settings fingerprints, confirmed six empty plugin tables and the retention schedule, and proved incumbent routes, settings, plugins, and login assets remained unchanged.
- The authenticated `P1-001` comparison created disposable administrator, shop-manager, and customer identities, measured toolbar and `/wp-admin/` behavior with the gateway inactive and active, and removed every identity afterward. The two matrices were identical because the existing site stack already applies equivalent policy; settings, plugin tables, cron, public routes, and activation state were preserved.
- Finding `P1-001` is confirmed as a product-contract defect despite the absent HBF-staging runtime delta. Frontend Output must govern customer wp-admin blocking and admin-bar filtering so disabled output truly produces no plugin-owned account-policy change.
- Local `v0.1.99` candidate work gates both policies on Frontend Output, adds direct regression coverage, passes 285 tests and 1,900 assertions, PHPCS, 108-file PHP syntax validation, npm and Composer audits, and build validation, and produces a verified 45-file runtime package with SHA-256 `55B989352D638BC18C4C35A167F10CE69ECAEFEAC09CBCD486EA877CF72323BB`.
- The exact candidate package passed an installed-copy smoke on local-only Plugin Tester for disposable administrator, shop-manager, and customer roles. The public `0.1.98` package, activation position, 45-file fingerprint, settings fingerprint, disabled toggles, and retention schedule were then restored exactly; zero QA users or upload artifacts remain.
- Public `v0.1.99` was published from release commit `8abe7f1` and merge commit `0271789`. Build Release run `29450943872` passed and attached a verified 45-runtime-file updater asset with SHA-256 `F87B79060043C6995A1FCD3ABE01C00BB410BF1A580B15D0B6D74F551D97BE4A`.
- Alynt Plugin Updater `1.1.1` offered and installed the exact public `v0.1.99` asset on `hbf-staging`. The installed 45-file fingerprint exactly matches the public ZIP, the original active position and settings fingerprint were preserved, all six plugin tables remain empty, the retention schedule remains present, incumbent public routes and assets remain unchanged, and no update offer remains.
- The installed-copy disabled-output retest passed for disposable administrator, shop-manager, and customer roles, with both toolbar input states preserved and wp-admin policy evaluation returning without redirect. Every QA identity was removed. `P1-001` is closed.
- Representative HBF branding, copy, routes, customer username format, rich email templates, retention settings, and dashboard links are configured while all public-output switches remain disabled. Eight saved preview states and five email previews render structurally, public routes remain unchanged, all six tables remain empty, and zero QA users remain.
- Finding `P1-002` is closed for staging: Account Gateway now saves `/legal/terms/` and `/legal/privacy/`, both paths map to published custom `legal` posts, and Force Login interception is accepted because it is staging-only. The first email/provider/webhook acceptance attempt produced no external acceptance evidence because the site-owned email test recipient, Turnstile keys, Reoon API key, webhook URL, and webhook signing secret were empty. The test recipient was then configured, and all five Account Gateway email templates were accepted by `wp_mail()` with Frontend Output and registration still disabled. After the first email run was observed as simulated by the mail stack, the five templates were resent successfully; mailbox-side evidence remains pending.
- Finding `P1-004` is closed: public `v0.1.105` constrains email logos with explicit width attributes and inline dimensions, adds regression coverage, passes focused and full PHPUnit, PHPCS, PHP syntax checks, build, and POT regeneration, has been installed on `hbf-staging` through Alynt Plugin Updater, resent all five account-email templates with `email_logo_max_width` set to 150px, and was accepted in real mailbox review.
- Finding `P1-005` records that the real mailbox render now has acceptable logo sizing but the email body copy should be more readable. Public `v0.1.106` added wrapper-level responsive body text sizing, was installed on `hbf-staging` through Alynt Plugin Updater, and resent all five account-email templates, but user-provided before/after screenshots showed no visible paragraph-size change. Local `v0.1.107` candidate work cleans up that ineffective approach and applies inline 20px sizing directly to generated paragraph/list-item body copy, with responsive 16px mobile and 18px tablet overrides retained only as progressive enhancement. Focused and full release checks passed; public `v0.1.107` was inspected, installed on `hbf-staging` through Alynt Plugin Updater, all five account-email templates were resent, and the product owner confirmed the result looks good on mobile and desktop. `P1-005` is closed.
- Visual review captured all eight gateway states across `390x844`, `800x900`, and `1440x1000` using current staging settings, installed frontend assets, and configured media while public output remained disabled. The fallback visual matrix passed for overflow, branding, WordPress-logo absence, and obvious layout overlap.
- Finding `P1-003` initially recorded that the actual Gateway Screen Preview admin-post route redirected to `/wp-admin/` in authenticated browser testing. The issue is now closed by the `v0.1.104` compact-code front-end preview endpoint; legal, email, and integration inputs remain before route handover. Route handover still requires a separate explicit approval.
- Local `v0.1.100` candidate work moves Gateway Screen Preview links to the settings-page admin route while retaining the legacy admin-post handler, adds focused route/link coverage, passes 287 tests and 1,908 assertions, PHPCS, changed-file PHP syntax validation, build validation, and POT regeneration. Release, updater installation, and staging retest are waiting for explicit approval.
- Public `v0.1.100` installed through Alynt Plugin Updater on `hbf-staging`, but authenticated settings-page preview requests still redirected before standalone preview output rendered. Local `v0.1.101` corrective candidate moves preview buttons to authenticated admin AJAX while preserving both fallback handlers; focused tests, full suite, PHPCS, syntax checks, build, and POT regeneration passed.
- Public `v0.1.101` installed through Alynt Plugin Updater on `hbf-staging`; admin AJAX was reachable, but the preview action still redirected before standalone output rendered. Local `v0.1.102` second corrective candidate isolates preview output from broad site head/footer hooks; focused tests, full suite, PHPCS, syntax checks, build, and POT regeneration passed.
- Temporary staging-only redirect tracing identified WP Custom Login Manager as the preempting redirect source at `plugins_loaded`. Local `v0.1.103` final corrective candidate moves preview links to a nonce-protected front-end endpoint guarded by `manage_options`; focused tests, full suite, PHPCS, syntax checks, build, and POT regeneration passed.
- Public `v0.1.103` installed through Alynt Plugin Updater on `hbf-staging`, but the login preview still redirected because the incumbent redirect plugin matches the substring `login` anywhere in the request URI. Local `v0.1.104` compact-code candidate removes screen names from preview URLs; focused tests, full suite, PHPCS, syntax checks, build, and POT regeneration passed.
- Public `v0.1.104` was published from release commit `f449fba`. Build Release run `29490491996` passed and attached a verified 45-runtime-file updater asset with SHA-256 `A88D61F830A9C909A2ABCAB0BE569B23ED3555AB954144D3CB8ED2B2B65E60E3`.
- Alynt Plugin Updater detected and installed `0.1.103 -> 0.1.104` on `hbf-staging`. The installed plugin is active at `0.1.104`, no update offer remains, no NULL activation placeholder appeared, and the compact-code authenticated login preview returned `HTTP/1.1 200 OK` with zero redirects and standalone gateway markup. Temporary trace files and admin session tokens were removed. `P1-003` is closed.

- Public `v0.1.108` was published and updater-installed on `hbf-staging` to add a narrow Force Login bypass for configured Alynt Account Gateway routes while Frontend Output is enabled. The first handover attempt proved `/account` action routes rendered correctly but exposed a canonical redirect issue for `/login/`, so the route switch was rolled back before the corrective release.
- Public `v0.1.109` was published and updater-installed on `hbf-staging` to render the gateway before WordPress canonical redirects. The controlled handover then succeeded: WP Custom Login Manager was deactivated, Frontend Output and registration were enabled, `/login/`, `/account?action=lostpassword`, and `/account?action=register` rendered Alynt Account Gateway, `/wp-login.php` redirected to the branded login route, the emergency bypass returned only native login, `/my-account/` redirected safely to branded login, and legal pages remained protected by Force Login.
- Public `v0.1.110` was published and updater-installed on `hbf-staging` to load the Cloudflare Turnstile API without a WordPress version query. Fresh browser smoke on the registration route showed the Turnstile widget, clean API URL, Alynt gateway shell, and no fresh console warnings or errors. Temporary helpers were removed, the temporary webhook receiver remains deleted, and current webhook settings are empty.
- Phase 2 began with route/native-screen leakage checks passing for login, lost-password, registration, and logout. The first disposable registration-start POST on `v0.1.110` exposed `P2-001`: gateway rendering ran before form handlers, returning `200 OK` and creating zero pending/consent rows. Public `v0.1.111` moves auth and registration handlers before gateway rendering, passed the release checks, was installed through Alynt Plugin Updater, and retested successfully: registration start now redirects to `registration_sent=1`, creates pending and consent records, logs Turnstile missing plus Reoon valid under the either-provider policy, and creates no WordPress user before confirmation/password completion. A follow-up run used `ai@mailastic.com` after the original plus-address mailbox did not receive the message; confirmation-link set-password completion is waiting on that reachable mailbox link.
- The `ai@mailastic.com` confirmation link completed the Phase 2 happy path: the branded set-password screen rendered without native/incumbent markers, a compliant password redirected to `/login/?registration_complete=1`, WordPress user `9253` was created only after password completion, first name, last name, email, subscriber role, generated username, pending-registration state, and consent attachment all matched expectations, and branded email-only login redirected to `/my-account/` with a logged-in cookie. The disposable user and related plugin rows remain temporarily for follow-up Phase 2 checks or a deliberate cleanup pass.
- The disposable Phase 2 registration artifacts were cleaned up: user `9253`, the completed `ai@mailastic.com` pending/consent rows, and the stale undeliverable plus-address pending/consent rows are gone. Negative checks passed for invalid login, invalid set-password token branded handling, and registration-disabled behavior, with Frontend Output and registration restored afterward. Remaining Phase 2 negative states include rate limits, inactive/pending account login, expired/used token, password-policy failure, reset-password, logout, and role-access edge cases.
- The Phase 2 password reset/logout slice passed: disposable user `9254` was created, branded lost-password request sent the reset email, stale reset link displayed the branded invalid state, fresh reset link rendered the branded password form, reset completion redirected to `/login/?password_reset=1`, old password failed, new password logged in by email and redirected to `/my-account/`, logout confirmation/cancel/confirm all behaved correctly, and the disposable user plus temporary password files were removed.
- The Phase 2 role-access slice passed: disposable administrator, shop-manager, and customer users all logged in by email through the branded form. Administrator and shop manager reached `/wp-admin/` and saw the toolbar, while the customer was redirected to `/my-account/` and did not see the toolbar. All disposable role users and local temp credentials were removed.
- The Phase 2 rate-limit and password-policy slice passed: registration, resend-confirmation, login, and lost-password buckets were temporarily lowered to `1/1`, each produced the expected branded rate-limit redirect on the second matching POST, and the original staging values were restored. A disposable pending registration then verified password length, complexity, and mismatch failures, each staying branded with zero native markers and no WordPress user creation. Disposable rows and temporary helper files were removed.
- The Phase 2 pending-account and token-state slice passed: a pending-only email could not log in and did not create a WordPress user, an expired confirmation link rendered the branded invalid/expired state without password fields or native markers, and a consumed confirmation link did the same after a real disposable registration completion. The disposable user, plugin-owned rows, and temporary helper files were removed.
- The Phase 2 pending-registration resend slice passed: a disposable pending registration rendered the branded invalid-link resend form, the public resend POST redirected to `confirmation_resent=1`, the success state stayed branded, the token hash and expiry timestamps renewed, one `confirmation_resent` verification log was recorded, no WordPress user was created, and all disposable rows/helpers were removed.
- The Phase 2 emergency-bypass slice passed: normal anonymous `/wp-login.php` redirected to branded `/login/`; the redacted emergency bypass returned only native WordPress login markup, no Alynt Account Gateway form markup, no logged-in cookie, and no `/wp-admin/` access. The bypass key was never printed or retained in evidence, and the temporary helper was removed.
- The Phase 2 Frontend Output safety-switch slice passed: disabling Frontend Output removed Alynt Account Gateway markup from the public gateway routes and restored native `/wp-login.php` output while preserving registration settings; restoring Frontend Output brought branded login, lost-password, registration, and native-login redirect behavior back.
- The Phase 2 inactive-account scope check clarified that WordPress core does not provide a meaningful inactive customer flag for Alynt Account Gateway to enforce: a disposable user with `user_status = 1` still logged in through `wp_signon()`. Inactive-account blocking is therefore documented as out of v1 scope unless a separate integration defines an authoritative inactive state. The disposable user and helpers were removed.
- The Phase 2 safe redirect matrix found `P2-002` on `v0.1.111`: a same-site `redirect_to=/login/` target could return a logged-in user to the branded login form. Public `v0.1.112` now rejects branded login, account-action, and native `wp-login.php` post-login destinations, falls back to `/my-account/`, passed focused and full release checks, was installed on `hbf-staging` through Alynt Plugin Updater, and passed the safe/internal, external/unsafe, auth-surface fallback, and anonymous-chain redirect matrix. The disposable redirect-test user and helpers were removed.
- The Phase 2 public route shell matrix passed on `v0.1.112`: login states, account action states, invalid set-password states, logout, unknown account action fallback, and native `wp-login.php` action handoffs all rendered the Alynt shell with zero native/incumbent markers. The deliberate emergency bypass remained the only native-login exception, did not render Alynt shell markup, and did not authenticate the visitor. Temporary helper files were removed.
- The Phase 2 password strength feedback accessibility slice found `P2-003`: read-only set-password requirements used disabled checkbox semantics. Public `v0.1.113` now exposes readable requirement labels, updates `aria-label` from `Not met` to `Met`, adds progress counts to the live strength status, passed full release checks, was installed on `hbf-staging` through Alynt Plugin Updater, and passed a staging set-password retest with six labels, live status, no checkbox/checked requirement semantics, and no native WordPress markers. Disposable pending/consent rows and helpers were removed.
- The Phase 4 Turnstile browser-success slice passed on `v0.1.113`: the public registration screen produced a real Cloudflare Turnstile response token in the browser, a disposable registration submitted successfully to `registration_sent=1`, plugin-owned verification logs recorded Turnstile `passed` with `blocked=0` and Reoon `valid` with `blocked=0`, no WordPress user was created before confirmation/password completion, and all disposable pending, consent, verification, and helper artifacts were removed. Token expiry, replay, provider outage/timeout, and Turnstile-only success remained for later Phase 4 slices at that point.
- The Phase 4 Turnstile server-side validation slice passed on `v0.1.113`: a fresh browser token passed on first use, immediate replay of that same token failed, a deliberately invalid token failed, and a separate unused token failed after a 310-second expiry wait. Temporary token files and verifier helpers were removed locally and remotely. The Turnstile success/failure/expiry/replay checklist item is now complete; provider outage/timeout and either-provider policy coverage remain separate Phase 4 work.
- The Phase 4 Reoon policy matrix slice passed on `v0.1.113`: valid/safe outcomes pass, invalid/disabled/disposable/spamtrap hard-block, and catch-all/role-account/unknown/inbox-full follow the configurable flagged policy. Live API spot checks confirmed valid, invalid, and disposable behavior using masked disposable test addresses. The helper called the provider/client directly and created no account or plugin-owned rows; temporary helpers were removed.
- The Phase 4 provider-combination policy slice passed on `v0.1.113`: in `turnstile_or_reoon`, either Turnstile pass/Reoon fail or Turnstile fail/Reoon pass allowed registration protection, while both providers failing blocked it. In `turnstile_and_reoon`, both providers had to pass; either provider failing blocked protection. Official Cloudflare Turnstile test credentials were used only in an isolated settings copy, saved staging settings were unchanged, all temporary verification rows were deleted, and cleanup counts returned zero.
- The Phase 4 provider outage/timeout slice passed on `v0.1.113`: simulated Turnstile and Reoon HTTP timeouts generated provider-specific request-failed statuses, `turnstile_or_reoon` allowed one provider outage when the other provider passed and blocked when both timed out, and `turnstile_and_reoon` blocked when either provider timed out. Saved staging settings were unchanged, temporary verification rows were deleted, and cleanup counts returned zero.
- The Phase 4 webhook receiver acceptance slice passed on `v0.1.113`: pending registration did not dispatch a webhook, account creation dispatched `account.created` to a temporary HTTPS receiver, delivery metadata logged HTTP 200/success without storing payload by default, receiver payload included expected user/site fields without password or secret data, HMAC signature validation passed against a temporary non-printed signing secret, saved staging webhook settings remained empty, and the disposable user, plugin rows, temp receiver, temp secrets, request captures, and helpers were removed.
