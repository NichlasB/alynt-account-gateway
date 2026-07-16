# Alynt Account Gateway v1.0 Readiness Plan

## Status

- Current phase: Phase 2 is active. Public `v0.1.111` is updater-verified on `hbf-staging`, route/native-screen leakage checks passed, the first registration-start POST regression found in Phase 2 was fixed, and the confirmation-first registration happy path completed: pending registration, consent, email confirmation, set-password, delayed WordPress user creation, generated username, and email-only login all passed. Disposable registration artifacts were cleaned up, invalid login passed, invalid set-password token handling stayed branded, registration-disabled behavior passed, lost-password/reset-password/logout happy paths passed, role access/admin-toolbar behavior passed for administrator, shop manager, and customer, and rate-limit/password-policy failure states passed. Permanent webhook receiver configuration, full frontend Turnstile challenge success, remaining expired-token/pending-account/emergency-bypass states, and WooCommerce/customer dashboard journey acceptance remain gated.
- Product baseline: `v0.1.111`, released, public-asset verified, and updater-verified on production-like staging.
- Release goal: `v1.0.0`.
- Frontend output default: Disabled.
- Distribution: Alynt-distributed plugin with GitHub updater compatibility.
- Acceptance target: `hbf-staging` at `https://staging.handcraftedbotanicalformulas.com` in `live-only` operating mode. The production HBF site is explicitly excluded.

## Purpose

This is the living production-acceptance roadmap for Alynt Account Gateway. The completed implementation plan records the released `v0.1.98` product baseline. This plan determines whether that product is ready to be called `v1.0.0` on real WordPress and WooCommerce sites.

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

- [ ] Verify email-only login with valid, invalid, rate-limited, and inactive/pending account states.
- [ ] Verify login redirects preserve only safe destinations and do not create loops.
- [x] Verify public registration is unavailable while account creation is disabled.
- [x] Verify the full confirmation-first registration flow when account creation is enabled.
- [x] Confirm no WordPress user is created before email confirmation and password completion.
- [ ] Verify pending-registration expiry, invalid tokens, used tokens, and resend behavior.
- [x] Verify generated usernames follow the configured pattern while login remains email-only.
- [x] Verify password creation requires matching fields and the configured minimum policy.
- [ ] Verify password strength feedback is understandable and accessible.
- [x] Verify lost-password, reset-password, password-changed, and invalid/expired reset-link states.
- [x] Verify logout confirmation, cancellation, successful logout, and safe redirect behavior.
- [x] Verify administrators and shop managers retain intended wp-admin and toolbar access.
- [x] Verify other roles are redirected away from wp-admin without redirect loops.
- [ ] Verify the emergency bypass exposes only native login and never authenticates a visitor.
- [ ] Verify all public account routes avoid the native WordPress shell except through deliberate bypass.
- [ ] Verify frontend output can be disabled to restore native behavior without losing settings.

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

- [ ] Verify Turnstile success, failure, expiry, replay, and server-side validation behavior.
- [ ] Verify Reoon valid, invalid, disposable, catch-all, role-account, and unknown outcomes against the configured policy.
- [ ] Verify registration can proceed when either configured protection succeeds, according to site policy.
- [ ] Verify provider outages and timeouts fail safely with useful administrator diagnostics.
- [ ] Verify registration, login, reset, resend, and provider rate limits under repeated requests.
- [ ] Verify lockout and resend-throttling feedback does not disclose whether an account exists.
- [ ] Verify manual-review decisions, audit records, and retention behavior where enabled.
- [ ] Verify webhook delivery fires only when the account is created.
- [ ] Verify the webhook includes the intended full user fields without secrets or password data.
- [ ] Verify signing headers, receiver validation, failure metadata, and debug-payload controls.
- [ ] Verify credentials and bypass keys are redacted from diagnostics, exports, logs, and screenshots.
- [ ] Rotate the emergency bypass key and integration test secrets after acceptance.

## Phase 5: Dashboard And WooCommerce Acceptance

- [ ] Verify the custom dashboard disabled and enabled states.
- [ ] Verify customer greeting uses first name with the neutral fallback.
- [ ] Verify custom dashboard links, icons, ordering, role visibility, and new-tab settings.
- [ ] Verify WooCommerce takeover disabled and enabled states.
- [ ] Verify dashboard overview and navigation with no orders and with representative orders.
- [ ] Verify order list, pagination, order details, and available order actions.
- [ ] Verify downloads with empty, available, expired, and limited-download states.
- [ ] Verify billing and shipping address view/edit flows and validation errors.
- [ ] Verify account-details editing, email changes, and password changes.
- [ ] Verify saved payment-method list, add, delete, and default-method flows where supported.
- [ ] Verify delegated WooCommerce notices, forms, nonces, and errors remain functional.
- [ ] Verify unavailable WooCommerce endpoint guidance and recovery links.
- [ ] Verify shop-manager administration remains available while customer wp-admin access remains blocked.
- [ ] Confirm checkout, payment, subscription, membership, or other extension behavior used by the target site is not disrupted.

## Phase 6: Compatibility And Experience Matrix

- [ ] Test the minimum supported WordPress and PHP versions.
- [ ] Test the current supported WordPress, PHP, and WooCommerce versions.
- [ ] Test at least one default WordPress theme and the target site's production theme/builder.
- [ ] Test with representative caching, security, SMTP, and WooCommerce extension combinations.
- [ ] Verify login, registration, reset, logout, dashboard, and WooCommerce routes at mobile and desktop widths.
- [ ] Verify the 800px gateway layout boundary and narrow admin settings layouts.
- [ ] Verify keyboard-only navigation, visible focus, error association, live regions, and password controls.
- [ ] Verify zoom, reflow, high contrast, reduced motion, and resilient color contrast.
- [ ] Verify RTL layout and at least one translated locale across frontend and admin screens.
- [ ] Verify no unexpected remote font, tracking, or third-party request is introduced by default.
- [ ] Verify browser console, PHP logs, diagnostics, and network responses contain no plugin-caused errors.

### Compatibility Matrix

| Environment | WordPress | PHP | WooCommerce | Theme / Builder | Locale / RTL | Status | Evidence |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Minimum supported |  |  |  |  |  | Pending |  |
| Current supported |  |  |  |  |  | Pending |  |
| Target staging |  |  |  |  |  | Pending |  |

## Phase 7: Privacy, Data, And Lifecycle Acceptance

- [ ] Confirm the data inventory for pending registrations, consent, verification, webhooks, diagnostics, and audit records.
- [ ] Verify data minimization and redaction in settings, logs, exports, webhooks, and support evidence.
- [ ] Verify Terms and Privacy consent capture and the site's legal copy ownership.
- [ ] Verify WordPress personal-data exporter output for plugin-owned records.
- [ ] Verify WordPress personal-data eraser behavior and documented exceptions.
- [ ] Verify configured retention cleanup schedules and manual cleanup controls.
- [ ] Verify webhook payload-body storage remains disabled unless debugging is deliberately enabled.
- [ ] Verify disabling or uninstalling the plugin does not remove WordPress users, WooCommerce orders, or unrelated media.
- [ ] Verify uninstall removes only documented plugin-owned options, tables, scheduled hooks, and transient data.
- [ ] Review GDPR-facing documentation with the site owner or qualified adviser where required.

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
