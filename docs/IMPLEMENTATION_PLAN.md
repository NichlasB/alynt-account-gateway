# Alynt Account Gateway Implementation Plan

## Status

- Current phase: v0.1.20 webhook signing
- Target path: `C:\Development\WordPress\Plugins\alynt-account-gateway`
- Plugin status: v0.1.19 is the current public baseline after GitHub release and Alynt Plugin Updater verification.
- Frontend output default: Disabled
- Distribution: Alynt-distributed plugin with GitHub updater compatibility

## v0.1.20 Small Release Cycle

### Scope

- [x] Start the next integration-hardening slice from the released `master` baseline.
- [x] Add an optional webhook signing secret setting on the Webhooks tab.
- [x] Sign webhook request bodies with timestamped HMAC headers when a signing secret is configured.
- [x] Add focused coverage for unsigned and signed webhook dispatch behavior.
- [ ] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [ ] Publish the final `v0.1.20` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.20` from clean `master` after the `v0.1.19` release merge.
- Added an optional `webhook_signing_secret` setting on the Webhooks tab, defaulting to empty so existing integrations remain unsigned.
- Added signed webhook headers when a secret is configured: `X-Alynt-AG-Event`, `X-Alynt-AG-Time`, `X-Alynt-AG-Version`, and `X-Alynt-AG-Signature` using HMAC-SHA256 over `{timestamp}.{event}.{json_body}`.
- Added focused PHPUnit coverage for webhook signing defaults/sanitization, unsigned dispatch, and signed dispatch. Targeted `WebhookDispatcherTest|SettingsSchemaTest` passed with 27 tests and 131 assertions.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` writes 382 strings, `npm.cmd run lint`, `npm.cmd test` passes with 164 tests and 680 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.20-branch-qa-20260705-000212\alynt-account-gateway-v0.1.20-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.19` metadata, and signing setting/header code present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.19`; verified installed signing setting/method/header markers and safe intercepted signed test dispatch. The `account.created.test` signature matched the exact intercepted body, the log row recorded HTTP `202` success, and temporary settings/log artifacts were cleaned up.

### Guardrails

- Do not change the existing account-created payload shape, event names, test-send behavior, webhook URL policy, logging retention, registration flow, email behavior, frontend routes, dashboard rendering, WooCommerce delegation, or provider verification behavior.
- Keep signing optional and disabled by default so existing webhook consumers continue working until a site owner configures a shared secret.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates signed test webhook dispatch and Webhooks tab rendering.
- [ ] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.19 Small Release Cycle

### Scope

- [x] Start the next product-polish slice from the released `master` baseline.
- [x] Add Webhooks tab tools for sending an admin-triggered account-created test webhook to the configured destination.
- [x] Add a recent webhook deliveries table on the Webhooks tab using plugin-owned webhook log metadata.
- [x] Add focused coverage for test webhook dispatch behavior where practical.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.19` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.19` from clean `master` after the `v0.1.18` release merge.
- Added `ALYNT_AG_Webhook_Dispatcher::dispatch_account_created_test()` so admins can send an explicit `account.created.test` event through the saved account-created webhook URL without changing the normal account-created payload path.
- Added Webhooks tab tools: a nonce-protected `Send Test Webhook` action and a recent webhook deliveries table showing event, destination host, HTTP status, result, error, and timestamp.
- Added focused PHPUnit coverage for the test dispatch path. Verified targeted `WebhookDispatcherTest` plus full `npm.cmd test` passed with 162 tests and 671 assertions.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.19-branch-qa-20260704-233152\alynt-account-gateway-v0.1.19-branch-qa.zip`; inspected 46 packaged files, no dev/source entries, no backslash zip entries, header/constant still `0.1.18` as expected before release bump.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.18`; verified active plugin, installed method/action/table strings, and safe intercepted test dispatch logging `account.created.test` with HTTP `202`, success `1`, and no external network call.
- Browser-smoked Plugin Tester Webhooks tab through temporary Novamira admin access: `Webhook Tools`, `Send Test Webhook`, `Recent Webhook Deliveries`, disabled send button, and missing-URL helper text rendered correctly.
- Bumped release-candidate metadata to `0.1.19` across the plugin header/constant, npm metadata, readme, sample test, and changelog.
- Regenerated `languages/alynt-account-gateway.pot` with 381 strings and `0.1.19` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 162 tests and 671 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.19-20260704-233844\alynt-account-gateway-v0.1.19.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, and `0.1.19` header/constant/readme/POT metadata.
- Installed the local `0.1.19` package on LocalWP Plugin Tester; verified active header and loaded constant are `0.1.19`, webhook test method/action/table strings are present, and safe intercepted test dispatch logs `account.created.test` with HTTP `202` and success `1` without external network calls.
- Published GitHub release `v0.1.19`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, and `0.1.19` header/constant/readme metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.18` to `0.1.19`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.19` header/constant, webhook test method/action/table strings present, and no remaining update offer.

### Guardrails

- Do not change normal account-created webhook behavior, payload shape for real account creation events, registration flow, email behavior, frontend output, routes, dashboard rendering, WooCommerce delegation, provider verification behavior, or existing webhook retention defaults.
- Keep this cycle focused on admin webhook observability and an explicit test-send action.
- Defer final `0.1.19` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Webhooks tab test-send and recent delivery table.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.18 Small Release Cycle

### Scope

- [x] Start the next product-polish slice from the released `master` baseline.
- [x] Replace the raw custom dashboard links JSON field with a repeatable admin editor for label, URL, icon, ordering, role visibility, and open-in-new-tab behavior while preserving the existing `dashboard_custom_links` storage format.
- [x] Add focused coverage for custom dashboard link sanitization/serialization where practical.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.18` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.18` from clean `master` after the `v0.1.17` release merge.
- Added a repeatable Dashboard settings editor for custom dashboard links with label, URL, icon, order, open-in-new-tab, and role-visibility controls, plus a raw JSON fallback panel that preserves the existing `dashboard_custom_links` storage format.
- Added backend sanitization for dashboard links so saved/imported JSON is normalized to known fields and incomplete rows are skipped.
- Added focused `SettingsSchemaTest` coverage for custom dashboard link JSON sanitization.
- Verified `php -l` for the touched PHP files; `npm.cmd run build` passes; `npm.cmd run lint` passes; full `npm.cmd test` passes with 161 tests and 665 assertions; `npm.cmd run make-pot` writes 365 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.18-branch-qa-20260704-230747\alynt-account-gateway-v0.1.18-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built admin assets, and `0.1.17` header/constant metadata as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.17` header/constant, built admin asset present, settings page editor markup present, and dashboard-link schema type present.
- Browser-tested the Dashboard settings editor in wp-admin with Playwright: added a `QA Support Portal` link through the repeatable editor, set `/support/`, `help` icon, order `12`, new-tab behavior, and `customer` role visibility, then saved successfully and confirmed the stored JSON.
- Server-side rendered the dashboard for a temporary customer user and confirmed the custom link appears with the normalized site URL, new-tab accessible text, and `help` icon class. Removed the temporary user and restored Plugin Tester dashboard links to `[]` after QA.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, and `/my-account/`; public gateway routes rendered branded output with expected screen markers, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed temporary branch-QA ZIP artifacts from Plugin Tester uploads, including the duplicate-path artifact created by the temporary upload endpoint.
- Bumped release-candidate metadata to `0.1.18` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 161 tests and 665 assertions, `npm.cmd run make-pot` writes 365 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.18-20260704-231643\alynt-account-gateway-v0.1.18.zip`; verified built admin assets and dashboard-link editor runtime files are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.18`.
- Published GitHub release `v0.1.18`, verified the remote tag points to release commit `a7b2965`, downloaded the public release asset, and verified the downloaded package has 46 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built admin CSS/JS assets, dashboard-link editor runtime files, and `0.1.18` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.17` to `0.1.18`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.18` header/constant, dashboard-link editor markup/schema present, built admin JS/CSS contain the dashboard-link editor markers, custom dashboard links restored to `[]`, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, and `/my-account/`; public gateway routes rendered branded output with expected screen markers, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change frontend dashboard link rendering, WooCommerce dashboard delegation, dashboard default links, dashboard link visibility rules, URL normalization behavior, routes, query parameters, auth flow, registration flow, email behavior, webhook behavior, provider verification behavior, frontend class names, or design-token names.
- Keep this cycle focused on the admin editing experience and compatible dashboard-link persistence.
- Defer final `0.1.18` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Dashboard settings editor and representative dashboard output after saving custom links.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.17 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract full gateway document rendering and admin preview rendering out of the frontend hook/controller class without changing document markup, body class, page title behavior, dashboard-vs-auth shell selection, preview screen normalization, routes, redirects, logout handling, or admin preview compatibility.
- [x] Add focused test coverage around the extracted frontend document renderer service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.17` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.17` from clean `master` after the `v0.1.16` release merge.
- Extracted full gateway document rendering into `ALYNT_AG_Frontend_Document_Renderer`, including status/no-cache headers, HTML document wrapper, document title lookup, `wp_head()`/`wp_footer()` placement, dashboard-vs-auth shell selection, admin preview screen normalization, set-password preview rendering, and screen-title lookup.
- Kept `ALYNT_AG_Frontend` hook registration, frontend asset enqueueing, route detection, native login redirect behavior, emergency bypass handling, URL filters, wp-admin blocking, logout confirmation execution, current-path calculation, and public admin-preview title wrapper intact while delegating document/preview rendering to the new service.
- Added focused `FrontendDocumentRendererTest` coverage for full auth document output, dashboard document output with current-path propagation, unknown preview fallback, set-password preview output, renderer title lookup, and the preserved `ALYNT_AG_Frontend::get_screen_title()` wrapper used by admin preview.
- Added test bootstrap shims for `status_header()`, `nocache_headers()`, `language_attributes()`, `wp_head()`, and `wp_footer()` so document rendering can be verified without a full WordPress runtime.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendDocumentRendererTest` passes with 6 tests and 16 assertions; full `npm.cmd test` passes with 160 tests and 657 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings with no string changes; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.17-branch-qa-20260704-224135\alynt-account-gateway-v0.1.17-branch-qa.zip`; verified the new frontend document renderer service, runtime plugin files, built frontend/admin assets, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.16` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.16` header/constant, `ALYNT_AG_Frontend_Document_Renderer` file/class loaded in a fresh request, preview rendering works for login and set-password, the full document wrapper renders, and the preserved admin-preview title wrapper returns `Create Account`.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with the expected body class, frontend JS assets, and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed the branch-QA zip from Plugin Tester uploads after smoke verification.
- Bumped release-candidate metadata to `0.1.17` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 160 tests and 657 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.17-20260704-224614\alynt-account-gateway-v0.1.17.zip`; verified built frontend/admin assets and the new frontend document renderer service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.17`.
- Published GitHub release `v0.1.17`, corrected the release target/tag to the `0.1.17` release commit, replaced the initially stale release asset, downloaded the public release asset, and verified the downloaded package has 46 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend document renderer service, and `0.1.17` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.16` to `0.1.17`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.17` header/constant, `ALYNT_AG_Frontend_Document_Renderer` file/class loaded, admin-preview title wrapper intact, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with the expected body class, frontend JS assets, and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change rendered gateway copy, document structure, body class, page title mapping, routes, query parameters, redirects, emergency bypass behavior, wp-admin blocking, logout behavior, registration request handling, login/lost-password/set-password behavior, email behavior, WooCommerce dashboard behavior, dashboard output, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or dashboard link normalization behavior.
- Keep this cycle focused on document and preview rendering; leave request routing, auth services, registration storage, provider verification, email delivery, webhook behavior, and WooCommerce endpoint delegation untouched.
- Defer final `0.1.17` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the document renderer extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.16 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the generic branded gateway shell and auth screen dispatch out of the large frontend renderer class without changing shell markup, branding output, media panel output, screen copy, routes, query parameters, nonce/action names, password preview behavior, dashboard behavior, or request handling.
- [x] Add focused test coverage around the extracted frontend gateway shell service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.16` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.16` from clean `master` after the `v0.1.15` release merge.
- Extracted the branded auth shell into `ALYNT_AG_Frontend_Gateway_Shell`, including shell wrapper markup, inline branding style output, media panel rendering, brand block rendering, auth screen dispatch, and the admin set-password preview shell.
- Kept `ALYNT_AG_Frontend` request flow, frontend asset enqueueing, route detection, native login redirect behavior, emergency bypass handling, URL filters, logout confirmation handling, dashboard rendering, and document title behavior intact while delegating non-dashboard auth shell output to the new service.
- Removed now-unused private frontend wrapper methods for auth screen rendering, branding helper access, path comparison, and resend-error message lookup.
- Added focused `FrontendGatewayShellTest` coverage for shell wrapper output, branding/media insertion, screen dispatch across login/register/lost-password/set-password/logout/state fallbacks, unknown-screen fallback, and set-password preview form rendering.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendGatewayShellTest` passes with 9 tests and 18 assertions; full `npm.cmd test` passes with 154 tests and 641 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings with no string changes; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.16-branch-qa-20260704-220428\alynt-account-gateway-v0.1.16-branch-qa.zip`; verified the new frontend gateway shell service, runtime plugin files, built frontend/admin assets, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.15` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.15` header/constant, `ALYNT_AG_Frontend_Gateway_Shell` file/class loaded, and server-side gateway shell rendering includes the branded gateway wrapper, login screen marker, set-password preview marker, preview user hidden field, and no native login shell marker.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed the branch-QA zip from Plugin Tester uploads after smoke verification.
- Bumped release-candidate metadata to `0.1.16` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 154 tests and 641 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.16-20260704-221009\alynt-account-gateway-v0.1.16.zip`; verified built frontend/admin assets and the new frontend gateway shell service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.16`.
- Published GitHub release `v0.1.16`, downloaded the public release asset, and verified the downloaded package has 45 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend gateway shell service, and `0.1.16` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.15` to `0.1.16`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.16` header/constant, `ALYNT_AG_Frontend_Gateway_Shell` file/class loaded, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, login/lost-password/set-password/logout behavior, email behavior, WooCommerce dashboard behavior, dashboard output, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or dashboard link normalization behavior.
- Keep this cycle focused on generic auth shell rendering and auth screen dispatch; leave request routing, dashboard rendering, auth services, registration storage, provider verification, email delivery, webhook behavior, and WooCommerce endpoint delegation untouched.
- Defer final `0.1.16` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the gateway shell extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.15 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the frontend dashboard shell and dashboard content renderer out of the large frontend renderer class without changing dashboard copy, links, logout URL behavior, WooCommerce takeover warning, endpoint content delegation, external-link accessibility text, or dashboard classes.
- [x] Add focused test coverage around the extracted frontend dashboard screen service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.15` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.15` from clean `master` after the `v0.1.14` release merge.
- Extracted dashboard shell and dashboard content rendering into `ALYNT_AG_Frontend_Dashboard_Screen`.
- Kept `ALYNT_AG_Frontend` request flow, dashboard route detection, login-required dashboard redirect, logout handling, current-path calculation, and preview entry point intact while delegating dashboard shell markup, brand block rendering, dashboard hero output, dashboard links, WooCommerce unavailable warning, and WooCommerce endpoint content rendering to the new service.
- Added focused `FrontendDashboardScreenTest` coverage for dashboard shell output, brand/logout rendering, dashboard hero/user metadata, dashboard links including external-link accessibility text, WooCommerce unavailable warning, WooCommerce endpoint content rendering, and endpoint fallback copy.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendDashboardScreenTest` passes with 4 tests and 20 assertions; full `npm.cmd test` passes with 145 tests and 623 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.15-branch-qa-20260704-213541\alynt-account-gateway-v0.1.15-branch-qa.zip`; verified the new frontend dashboard screen service, runtime plugin files, built frontend/admin assets, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.14` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.14` header/constant, `ALYNT_AG_Frontend_Dashboard_Screen` file/class loaded, and dashboard shell rendering includes the dashboard shell, hero, manage-account links, logout link, WooCommerce content section, and no native login shell.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed the branch-QA zip from Plugin Tester uploads after smoke verification.
- Bumped release-candidate metadata to `0.1.15` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 145 tests and 623 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.15-20260704-214210\alynt-account-gateway-v0.1.15.zip`; verified built frontend/admin assets and the new frontend dashboard screen service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.15`.
- Published GitHub release `v0.1.15`, downloaded the public release asset, and verified the downloaded package has 53 archive entries including directories, 43 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend dashboard screen service, and `0.1.15` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.14` to `0.1.15`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.15` header/constant, `ALYNT_AG_Frontend_Dashboard_Screen` file/class loaded, dashboard shell rendering succeeds, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, login/lost-password/set-password behavior, email behavior, WooCommerce endpoint behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or dashboard link normalization behavior.
- Keep this cycle focused on dashboard rendering; leave dashboard data/link rules, WooCommerce endpoint routing, WooCommerce action delegation, auth flow, and admin settings untouched.
- Defer final `0.1.15` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the dashboard screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.14 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the set-password screen renderer and shared password form out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, token/key validation routing, password strength markup, password requirements, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend set-password screen service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.14` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.14` from clean `master` after the `v0.1.13` release merge.
- Extracted set-password routing and shared password form rendering into `ALYNT_AG_Frontend_Setpassword_Screen`.
- Kept `ALYNT_AG_Frontend` request flow, gateway shell, and admin preview wrapper intact while delegating pending-registration token handling, native password-reset key handling, invalid-link fallback routing, lost-password fallback routing, password error display, password requirements, and password-strength markup to the new service.
- Added focused `FrontendSetpasswordScreenTest` coverage for default password form output, error accessibility state, pending-registration token form output, native reset-key form output, invalid registration-token fallback, and invalid native reset-key fallback.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendSetpasswordScreenTest` passes with 6 tests and 46 assertions; full `npm.cmd test` passes with 141 tests and 603 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.14-branch-qa-20260704-205911\alynt-account-gateway-v0.1.14-branch-qa.zip`; verified the new frontend set-password screen service, runtime plugin files, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.13` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.13` header/constant, `ALYNT_AG_Frontend_Setpassword_Screen` file/class loaded, and the new service file present in the installed plugin copy.
- Browser-smoked the branch-QA installed Plugin Tester copy with system Chrome at `/account?action=setpassword&key=...&login=...`, `/account?action=setpassword`, and `/login`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved the set-password form, invalid-link fallback, and login control route. A 390px viewport pass confirmed no horizontal overflow, hidden media panel, and stable password form/card widths.
- Removed the temporary branch-QA reset user and uploaded branch-QA zip from Plugin Tester after smoke verification.
- Bumped release-candidate metadata to `0.1.14` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 141 tests and 603 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.14-20260704-210740\alynt-account-gateway-v0.1.14.zip`; verified built frontend/admin assets and the new frontend set-password screen service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.14`.
- Published GitHub release `v0.1.14`, downloaded the public release asset, and verified the downloaded package has 52 archive entries including directories, 42 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend set-password screen service, and `0.1.14` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.13` to `0.1.14`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.14` header/constant, `ALYNT_AG_Frontend_Setpassword_Screen` file/class loaded, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/account?action=setpassword&key=...&login=...`, `/account?action=setpassword`, and `/login`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend JS assets, and preserved expected set-password form, invalid-link fallback, and login control states. Temporary release-smoke users were removed after verification.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, login/lost-password behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or password-strength UI behavior.
- Keep this cycle focused on set-password rendering; leave password reset request handling, pending-registration storage, email confirmation creation, Turnstile/Reoon validation, webhook behavior, and WooCommerce dashboard behavior untouched.
- Defer final `0.1.14` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the set-password screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.13 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the registration screen renderer out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, terms/privacy links, verification slot output, registration-success handling, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend registration screen service.
- [ ] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.13` release.

### Progress Notes

- Started `v0.1.13` from clean `master` after the `v0.1.12` release merge.
- Extracted registration rendering into `ALYNT_AG_Frontend_Register_Screen`.
- Kept `ALYNT_AG_Frontend` request flow and wrapper method intact while delegating registration markup, read-only success/error display, terms/privacy links, and verification-slot rendering to the new service.
- Added focused `FrontendRegisterScreenTest` coverage for default form output, nonce field output, terms/privacy links, disabled submit state, registration-sent success state, registration error accessibility state, and Turnstile slot output.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendRegisterScreenTest` passes with 4 tests and 33 assertions; full `npm.cmd test` passes with 135 tests and 557 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.13-branch-qa-20260704\alynt-account-gateway-v0.1.13-branch-qa.zip`; verified built frontend/admin assets, the new frontend registration screen service, and the previously extracted frontend login/lost-password/logout/state services are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.12` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.12` header/constant, `ALYNT_AG_Frontend_Register_Screen` file/class loaded, and registration rendering includes the default form, nonce, terms/privacy links, placeholder verification slot, registration-sent success state, registration error state, and Turnstile widget slot when a site key is configured.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/account?action=register`, `/account?action=register&registration_sent=1`, and `/account?action=register&registration_error=terms_required`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected registration default/success/error states. A 390px viewport pass confirmed the single-column layout, hidden media panel, no horizontal overflow, and stable field/button widths.
- Removed the branch-QA zip from Plugin Tester uploads.
- Bumped release-candidate metadata to `0.1.13` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 135 tests and 557 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.13-20260704\alynt-account-gateway-v0.1.13.zip`; verified built frontend/admin assets and the new frontend registration screen service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.13`.
- Published GitHub release `v0.1.13`, downloaded the public release asset, and verified the downloaded package has 51 archive entries including directories, 41 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend registration screen service, and `0.1.13` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.12` to `0.1.13`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.13` header/constant, `ALYNT_AG_Frontend_Register_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/account?action=register`, `/account?action=register&registration_sent=1`, and `/account?action=register&registration_error=terms_required`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected registration default/success/error states.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, or provider verification behavior.
- Keep this cycle focused on registration screen rendering; leave pending-registration storage, email confirmation, set-password, Turnstile/Reoon validation, and resend-confirmation flows untouched.
- Defer final `0.1.13` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the registration screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.12 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the login screen renderer out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, redirect handling, routes, password toggle markup, status handling, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend login screen service.
- [ ] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.12` release.

### Progress Notes

- Started `v0.1.12` from clean `master` after the `v0.1.11` release merge.
- Extracted login rendering into `ALYNT_AG_Frontend_Login_Screen`.
- Kept `ALYNT_AG_Frontend` request flow and wrapper method intact while delegating login markup and read-only status/error display to the new service.
- Added focused `FrontendLoginScreenTest` coverage for default form output, nonce field output, account links, password toggle markup, success states, redirect preservation, and login error accessibility state.
- Verified `php -l` for the new service and test file, targeted `FrontendLoginScreenTest` passes with 3 tests and 24 assertions, `npm.cmd run lint` passes, and `git diff --check` passes.
- Verified the full local gate: `npm.cmd test` passes with 131 tests and 524 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.12-branch-qa-20260704-193414\alynt-account-gateway-v0.1.12-branch-qa.zip`; verified built frontend/admin assets, the new frontend login screen service, the frontend lost-password screen service, and the frontend logout-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.11` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.11` header/constant, `ALYNT_AG_Frontend_Login_Screen` file/class loaded in a fresh request, and login rendering includes the title, form action, nonce, email/password fields, registration link, and lost-password link.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/login?registration_complete=1&password_reset=1&redirect_to=...`, `/login?login_error=alynt_ag_rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved login default/success/error states.
- Removed the branch-QA zip from Plugin Tester uploads.
- Bumped release-candidate metadata to `0.1.12` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 131 tests and 524 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.12-20260704-193759\alynt-account-gateway-v0.1.12.zip`; verified built frontend/admin assets and the new frontend login screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.12`.
- Published GitHub release `v0.1.12`, downloaded the public release asset, and verified the downloaded package has 50 archive entries including directories, 40 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend/admin CSS/JS assets, the new frontend login screen service, and `0.1.12` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.11` to `0.1.12`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.12` header/constant, `ALYNT_AG_Frontend_Login_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/login?registration_complete=1&password_reset=1&redirect_to=...`, `/login?login_error=alynt_ag_rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected screen states.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, or auth-service messages.
- Keep this cycle focused on login screen rendering; leave login request handling and other auth screens untouched.
- Defer final `0.1.12` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the login screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.11 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the lost-password screen renderer out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, redirect behavior, routes, status handling, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend lost-password screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.11` release.

### Progress Notes

- Started `v0.1.11` from clean `master` after the `v0.1.10` release merge.
- Extracted lost-password rendering into `ALYNT_AG_Frontend_Lostpassword_Screen`.
- Kept `ALYNT_AG_Frontend` request flow and wrapper method intact while delegating lost-password markup and read-only status/error display to the new service.
- Added focused `FrontendLostpasswordScreenTest` coverage for default form output, nonce field output, request error state, forced invalid-token error state, reset-sent success state, and back-to-login behavior.
- Verified `php -l` for the new service and test file, targeted `FrontendLostpasswordScreenTest` passes with 4 tests and 22 assertions, `npm.cmd run lint` passes, and `git diff --check` passes.
- Verified the full local gate: `npm.cmd test` passes with 128 tests and 500 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.11-branch-qa-20260704-191248\alynt-account-gateway-v0.1.11-branch-qa.zip`; verified built frontend/admin assets, the new frontend lost-password screen service, the frontend logout-screen service, and the frontend state-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.10` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.10` header/constant, `ALYNT_AG_Frontend_Lostpassword_Screen` file/class loaded in a fresh request, and lost-password rendering includes the title, form action, nonce, email field, and back-to-login link.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=lostpassword&reset_error=alynt_ag_rate_limited`, `/account?action=lostpassword&reset_sent=1`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved lost-password default/error/success states.
- Removed the branch-QA zip from Plugin Tester uploads.
- Bumped release-candidate metadata to `0.1.11` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 128 tests and 500 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.11-20260704-191621\alynt-account-gateway-v0.1.11.zip`; verified built frontend/admin assets and the new frontend lost-password screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.11`.
- Published GitHub release `v0.1.11`, downloaded the public release asset, and verified the downloaded package has 49 archive entries including directories, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend/admin CSS/JS assets, the new frontend lost-password screen service, and `0.1.11` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.10` to `0.1.11`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.11` header/constant, `ALYNT_AG_Frontend_Lostpassword_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=lostpassword&reset_error=alynt_ag_rate_limited`, `/account?action=lostpassword&reset_sent=1`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected screen states.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, or auth-service messages.
- Keep this cycle focused on lost-password screen rendering; leave password-reset request handling and set-password flows untouched.
- Defer final `0.1.11` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the lost-password screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.10 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the logout confirmation screen renderer out of the large frontend renderer class without changing copy, nonce/action names, query parameters, redirect behavior, routes, button classes, or notice behavior.
- [x] Add focused test coverage around the extracted frontend logout-screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.10` release.

### Progress Notes

- Started `v0.1.10` from clean `master` after the `v0.1.9` release merge.
- Extracted logout confirmation rendering into `ALYNT_AG_Frontend_Logout_Screen`.
- Kept `ALYNT_AG_Frontend` request handling and wrapper method intact while delegating logout confirmation markup to the new service.
- Added focused `FrontendLogoutScreenTest` coverage for the notice, nonce-protected logout URL, cancel URL, action button classes, and empty-notice suppression.
- Verified `php -l` for the new service and test file, targeted `FrontendLogoutScreenTest` passes with 2 tests and 11 assertions, `npm.cmd run lint` passes, and `git diff --check` passes.
- Verified the full local gate: `npm.cmd test` passes with 124 tests and 478 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.10-branch-qa-20260704-183635\alynt-account-gateway-v0.1.10-branch-qa.zip`; verified built frontend/admin assets, the new frontend logout-screen service, the existing frontend state-screen service, and the frontend component service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.9` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.9` header/constant, `ALYNT_AG_Frontend_Logout_Screen` file/class loaded in a fresh request, and logout confirmation rendering includes the title, notice, nonce-protected confirm URL, and cancel action.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=logout`, `/account?action=invalidlink`, `/account?action=lostpassword`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, and included frontend CSS/JS assets.
- Removed the branch-QA zip from Plugin Tester uploads and cleaned up the misplaced temporary upload folder created by the failed upload-link attempt.
- Bumped release-candidate metadata to `0.1.10` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 124 tests and 478 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.10-20260704-184128\alynt-account-gateway-v0.1.10.zip`; verified built frontend/admin assets and the new frontend logout-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.10`.
- Published GitHub release `v0.1.10`, downloaded the public release asset, and verified the downloaded package has 48 archive entries including directories, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend/admin CSS/JS assets, the new frontend logout-screen service, and `0.1.10` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.9` to `0.1.10`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.10` header/constant, `ALYNT_AG_Frontend_Logout_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=logout`, `/account?action=invalidlink`, `/account?action=lostpassword`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected screen titles.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, or form action names.
- Keep this cycle focused on logout confirmation rendering; leave confirmed logout request handling inside the main frontend controller.
- Defer final `0.1.10` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the logout-screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## Locked Decisions

- Plugin title: Alynt Account Gateway
- Plugin slug / text domain: `alynt-account-gateway`
- Development prefix: `alynt_ag_`
- Initial version: `0.1.0`
- GitHub Plugin URI: `NichlasB/alynt-account-gateway`
- Account action base default: `/account`
- Login URL default: `/login`
- Public account creation default: Disabled
- Pending registration token expiry: 24 hours
- Customer login method: Email only
- Emergency bypass: Yes, via generated secret query key on `wp-login.php`
- `wp-admin` access: Administrators and shop managers only
- Admin toolbar: Visible for administrators and shop managers only
- Gateway background image: One global image
- WooCommerce dashboard mode: Custom branded UI that delegates sensitive actions to WooCommerce
- Webhook event for v1: Account created
- Webhook payload: Full user fields
- Webhook logging default: Response metadata only, not full request payload bodies
- Turnstile and Reoon mode: Optional, with default protection setting of `Turnstile or Reoon`
- Email editor: Rich template editor with preview and test-send
- Terms/privacy links: Relative URL paths configured manually, such as `/terms/` or `/legal/privacy/`
- Multilingual support: Required for v1

## v0.1.9 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the registration-disabled and invalid-link screen renderers out of the large frontend renderer class without changing copy, form fields, nonce/action names, query handling, routes, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend state-screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.9` release.

### Progress Notes

- Started `v0.1.9` from `master` after the `v0.1.8` release merge.
- Extracted registration-disabled and invalid-link screen rendering into `ALYNT_AG_Frontend_State_Screens`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating the extracted auth-state screens to the new service.
- Added focused `FrontendStateScreensTest` coverage for registration-disabled output, invalid-link resend form defaults, confirmation-resent success state, resend error state, nonce field output, and accessibility attributes.
- Added a PHPUnit bootstrap stub for `wp_nonce_field()` so extracted state-screen tests can verify nonce field names without loading WordPress admin helpers.
- Verified `php -l` for the new service and test file, `npm.cmd test` passes with 122 tests and 467 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.9-branch-qa-20260704-181637\alynt-account-gateway-v0.1.9-branch-qa.zip`; verified built frontend assets, the new frontend state-screen service, and the frontend component service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.8` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.8` header/constant, `ALYNT_AG_Frontend_State_Screens` file/class loaded, and registration-disabled rendering works in a fresh request.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=invalidlink`, `/account?action=invalidlink&confirmation_resent=1&resend_error=rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved invalid-link resend form/status behavior.
- Bumped release-candidate metadata to `0.1.9` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 122 tests and 467 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.9-20260704-182053\alynt-account-gateway-v0.1.9.zip`; verified built frontend assets and the new frontend state-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.9`.
- Published GitHub release `v0.1.9`, downloaded the public release asset, and verified the downloaded package has 37 runtime entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend CSS/JS assets, the new frontend state-screen service, and `0.1.9` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.8` to `0.1.9`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.9` header/constant, `ALYNT_AG_Frontend_State_Screens` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=invalidlink`, `/account?action=invalidlink&confirmation_resent=1&resend_error=rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved invalid-link resend form/status behavior.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, or form action names.
- Keep this cycle focused on the two simplest auth-state screens before extracting larger login/register/lost-password forms.
- Defer final `0.1.9` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the state-screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.8 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract shared frontend notice and verification-slot rendering out of the large frontend renderer class without changing markup, copy, accessibility attributes, Turnstile site-key output, or empty-copy behavior.
- [x] Add focused test coverage around the extracted frontend component service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.8` release.

### Progress Notes

- Started `v0.1.8` from `master` after the `v0.1.7` release merge.
- Extracted reusable notice rendering and registration verification-slot rendering into `ALYNT_AG_Frontend_Components`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating shared component rendering to the new service.
- Added focused `FrontendComponentsTest` coverage for empty notice suppression, paragraph formatting, default verification placeholder output, and Turnstile widget output with accessible label and configured site key.
- Added PHPUnit bootstrap stubs for `esc_html_e()` and `esc_attr_e()` so extracted component tests can exercise WordPress-style echo escaping.
- Verified `php -l` for the new service and test file, `npm.cmd test` passes with 119 tests and 447 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.8-branch-qa-20260704-175610\alynt-account-gateway-v0.1.8-branch-qa.zip`; verified built frontend assets, the new frontend components service, and the frontend branding service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.7` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.7` header/constant, `ALYNT_AG_Frontend_Components` file/class loaded, and verification placeholder rendering works in a fresh request.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, preserved form notice output, and preserved the registration verification slot.
- Bumped release-candidate metadata to `0.1.8` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 119 tests and 447 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.8-20260704-175944\alynt-account-gateway-v0.1.8.zip`; verified built frontend assets and the new frontend components service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.8`.
- Published GitHub release `v0.1.8`, downloaded the public release asset, and verified the downloaded package has 36 runtime entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend CSS/JS assets, the new frontend components service, and `0.1.8` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.7` to `0.1.8`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.8` header/constant, `ALYNT_AG_Frontend_Components` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, preserved form notice output, and preserved the registration verification slot.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, or frontend class names.
- Keep this cycle focused on shared frontend form components before attempting a larger screen-renderer split.
- Defer final `0.1.8` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the shared-component extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.7 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Preserve the existing `AI_CODING_RULES.md` housekeeping rename as a separate checkpoint.
- [x] Extract frontend branding/media/style rendering out of the large frontend renderer class without changing markup, design tokens, image handling, logo sizing, or fallback store-name behavior.
- [x] Add focused test coverage around the extracted frontend branding service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.7` release.

### Progress Notes

- Started `v0.1.7` from `master` after the `v0.1.6` release merge.
- Preserved the already-present `.windsurfrules` to `AI_CODING_RULES.md` housekeeping rename in commit `c1b0b63` before beginning the code slice.
- Extracted inline design-token style generation, left media-panel rendering, and logo/store-name rendering into `ALYNT_AG_Frontend_Branding`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating branding/media/style decisions to the new service.
- Added focused `FrontendBrandingTest` coverage for configured design tokens, empty-value skipping, media pattern fallback, configured background image output, store-name fallback, logo URL output, and logo max-width clamping.
- Verified `php -l` for the new service and test file, `npm.cmd test` passes with 115 tests and 435 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.7-branch-qa-20260704-173636\alynt-account-gateway-v0.1.7-branch-qa.zip`; verified built frontend assets, the new frontend branding service, and the frontend asset service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.6` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.6` header/constant, `ALYNT_AG_Frontend_Branding` file/class loaded, and style-token generation works from `ALYNT_AG_Settings_Schema::defaults()`.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and included the brand block plus design-token style output.
- Bumped release-candidate metadata to `0.1.7` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 115 tests and 435 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.7-20260704-174141\alynt-account-gateway-v0.1.7.zip`; verified built frontend assets and the new frontend branding service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.7`.
- Published GitHub release `v0.1.7`, downloaded the public release asset, and verified the downloaded package has 35 runtime entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend CSS/JS assets, the new frontend branding service, and `0.1.7` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.6` to `0.1.7`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.7` header/constant, `ALYNT_AG_Frontend_Branding` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and included the brand block plus design-token style output.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, or design-token names.
- Keep this cycle focused on one structural extraction from the frontend renderer.
- Defer final `0.1.7` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the branding/media/style extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.6 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract frontend asset enqueue logic out of the large frontend renderer class without changing when assets load.
- [x] Add focused test coverage around the extracted frontend asset service.
- [x] Run installed Plugin Tester smoke checks for representative gateway routes after packaging.
- [x] Re-run package/update checks as appropriate for the final `0.1.6` release.

### Progress Notes

- Started `v0.1.6` from `master` after the `v0.1.5` release merge.
- Extracted frontend stylesheet/script enqueueing, localized password-toggle labels, and Turnstile script enqueueing into `ALYNT_AG_Frontend_Assets`.
- Kept `ALYNT_AG_Frontend::enqueue_assets()` as the public hook target while delegating asset decisions to the new service.
- Added focused `FrontendAssetsTest` coverage for frontend-output/screen gating, frontend CSS/JS enqueueing, localized labels, and Turnstile loading only on configured registration screens.
- Added lightweight PHPUnit bootstrap stubs to record enqueued styles, scripts, and localized script data.
- Verified `npm.cmd test` passes with 110 tests and 420 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.6-branch-qa-20260704-165504\alynt-account-gateway-v0.1.6-branch-qa.zip`; verified built frontend assets and the frontend asset, route, and message services are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.5` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.5` header/constant, `ALYNT_AG_Frontend_Assets` file/class loaded, frontend style/script queue on gateway screens, and Turnstile script queues on a configured registration screen.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, and included the frontend CSS/JS assets.
- Bumped release-candidate metadata to `0.1.6` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 110 tests and 420 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.6-20260704-171356\alynt-account-gateway-v0.1.6.zip`; verified built frontend assets and the frontend asset, route, and message services are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.6`.
- Published GitHub release `v0.1.6`, downloaded the public release asset, and verified the downloaded package has 34 runtime entries, no backslash archive entries, no dev/source/test/docs/package files, built frontend CSS/JS assets, the extracted frontend asset service, and `0.1.6` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.5` to `0.1.6`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.6` header/constant, `ALYNT_AG_Frontend_Assets` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, and included the frontend CSS/JS assets.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, or asset handles/URLs.
- Keep this cycle focused on one structural extraction from the frontend renderer.
- Defer final `0.1.6` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the asset-service extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.5 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract frontend URL and screen-routing helpers out of the large frontend renderer class without changing public routes or query handling.
- [x] Add focused test coverage around the extracted frontend route service.
- [x] Run installed Plugin Tester smoke checks for representative gateway routes after packaging.
- [x] Re-run package/update checks as appropriate for the final `0.1.5` release.

### Progress Notes

- Started `v0.1.5` from `master` after the `v0.1.4` release merge.
- Extracted branded action URL construction, login/lost-password/register/logout URL helpers, current relative path handling, path matching, and gateway screen resolution into `ALYNT_AG_Frontend_Routes`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating route decisions to the new service.
- Added focused `FrontendRoutesTest` coverage for known and fallback action URLs, redirect/nonce query preservation, enabled and disabled registration screen routing, dashboard and non-gateway path routing, WooCommerce takeover endpoint routing, and trailing-slash-insensitive path matching.
- Verified `npm.cmd test` passes with 107 tests and 402 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.5-branch-qa-20260704-162630\alynt-account-gateway-v0.1.5-branch-qa.zip`; verified built frontend assets, the message catalog, and the route service are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.4` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.4` header/constant, `ALYNT_AG_Frontend_Routes` file/class loaded, and route helpers resolve register/lost-password URLs and the register screen.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.
- Bumped release-candidate metadata to `0.1.5` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.5-20260704-163228\alynt-account-gateway-v0.1.5.zip`; verified built assets, the message catalog, and the route service are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.5`.
- Published GitHub release `v0.1.5`, downloaded `alynt-account-gateway-v0.1.5.zip` from the release, and verified the public asset reports `0.1.5`, includes built assets, the message catalog, and the route service, and excludes development/source files.
- Confirmed Alynt Plugin Updater detected `0.1.4` to `0.1.5`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.5` GitHub release asset, and verified final Plugin Tester state: active `0.1.5`, no remaining update.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, or WooCommerce dashboard behavior.
- Keep this cycle focused on one structural extraction from the frontend renderer.
- Defer final `0.1.5` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the route-service extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.4 Small Release Cycle

### Scope

- [x] Reconcile stale implementation-plan checklist items that were completed during `v0.1.2` email QA and release verification.
- [x] Start a low-risk structural refactor by extracting frontend message/catalog lookup out of the large frontend renderer class without changing public copy or behavior.
- [x] Add or preserve focused test coverage around the extracted message catalog.
- [x] Re-run build, lint, tests, POT, and package/update checks as appropriate for the final `0.1.4` release.

### Progress Notes

- Started `v0.1.4` from `master` after the `v0.1.3` release merge. Reconciled stale plan checkboxes: profile email-change request suppression and email preview/test-send QA were completed during the `v0.1.2` cycle and release notes.
- Extracted frontend gateway title and error-message lookup into `ALYNT_AG_Frontend_Messages`, keeping the existing `ALYNT_AG_Frontend::get_screen_title()` public wrapper for admin preview compatibility and preserving rendered copy/fallback behavior.
- Added focused `FrontendMessagesTest` coverage for screen-title, registration-error, resend-error, and password-error mappings and fallback messages.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.4-branch-qa-20260704-155405\alynt-account-gateway-v0.1.4-branch-qa-wp.zip`; verified built frontend assets and the message catalog file are included, dev/source/test/docs/package files are excluded, and archive entries use WordPress-compatible forward-slash paths.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes after the browser upload path returned a blank update response and WP-CLI was unavailable. Verified final installed state: active `0.1.3` header/constant, `ALYNT_AG_Frontend_Messages` file/class loaded, and `register` title resolves to `Create Account`.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.
- Bumped release-candidate metadata to `0.1.4` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.4-20260704-160614\alynt-account-gateway-v0.1.4.zip`; verified built assets and the message catalog are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.4`.
- Published GitHub release `v0.1.4`, downloaded `alynt-account-gateway-v0.1.4.zip` from the release, and verified the public asset reports `0.1.4`, includes built assets and the message catalog, and excludes development/source files.
- Confirmed Alynt Plugin Updater detected `0.1.3` to `0.1.4`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.4` GitHub release asset, and verified final Plugin Tester state: active `0.1.4`, no remaining update.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.

### Guardrails

- Do not change rendered gateway copy, routes, form behavior, email behavior, registration behavior, or WooCommerce dashboard behavior.
- Keep this cycle focused on documentation reconciliation and one small structure improvement.
- Defer broad class splitting until the extracted seams have tests and release evidence.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the refactor.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.3 Small Release Cycle

### Scope

- [x] Re-audit registration flow behavior against the approved confirmation-first, email-only, terms-consent, password-policy, and spam-prevention requirements.
- [x] Add or refine focused unit coverage around registration validation, pending-registration state, account-created webhook payload/logging, or protection-mode decisions where practical.
- [x] Browser/manual QA the installed Plugin Tester registration path, including disabled registration, enabled registration form gating, pending confirmation, set-password completion, and email-only login after account creation.
- [x] Verify account-created webhook behavior in a local-safe way without sending real customer data to an external service.
- [x] Refresh docs/changelog/POT if implementation changes registration, webhook, or user-facing behavior.
- [x] Re-run Plugin Tester smoke checks and verify Alynt Plugin Updater detects and installs `0.1.3` from the GitHub release asset.

### Progress Notes

- Registration service audit confirmed the existing flow keeps account creation behind email confirmation and password validation.
- Added focused PHPUnit coverage for the confirmed pending-registration completion path: WordPress user creation, profile update, pending-row `account_created` status, consent attachment, welcome email hook, and account-created webhook hook.
- Plugin Tester QA covered disabled registration, responsive registration layout, required-field and terms gating, simulated confirmation email delivery, set-password strength/match gating, account creation, email-only login, branded dashboard redirect, no customer admin toolbar, and customer `wp-admin` redirect.
- Account-created webhook QA used a local `pre_http_request` intercept against `http://127.0.0.1/alynt-local-webhook-capture`; no external request was sent, the payload contained full user/site fields, and a successful `202` webhook log row was written.
- No runtime code or user-facing string changes were needed during the Plugin Tester QA pass, so POT generation was not required for this checkpoint.
- Bumped release-candidate metadata to `0.1.3` across the plugin header/constant, database schema version, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.3-20260704-153148\alynt-account-gateway-v0.1.3.zip`; verified built assets are included, dev/source/test/docs/package files are excluded, and the package header/constant report `0.1.3`.
- Published GitHub release `v0.1.3`, confirmed the Build Release workflow completed successfully, downloaded `alynt-account-gateway-v0.1.3.zip`, and verified the package reports `0.1.3` while excluding development/source files.
- Confirmed Alynt Plugin Updater detected `0.1.2` to `0.1.3`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.3` GitHub release asset, and verified final Plugin Tester state: active `0.1.3`, no remaining update.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.

### Guardrails

- Keep the cycle small and registration-focused; do not redesign the gateway screens.
- Keep public account creation disabled by default.
- Do not weaken the confirmation-first account creation contract.
- Do not send real webhook payloads to third-party services during QA.
- Preserve email-only login and generated-username behavior.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Local release-style zip excludes source/dev files and includes built assets.
- [x] Plugin Tester validates the selected registration and webhook behavior.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.2 Small Release Cycle

### Scope

- [x] Browser/manual QA email preview and test-send on LocalWP Plugin Tester.
- [x] Add or refine focused test coverage around email preview/test-send handlers where practical.
- [x] Evaluate and, if safe, implement the remaining profile email-change request suppression strategy for the existing disable toggle.
- [x] Refresh docs/changelog/POT if implementation changes account email behavior or user-facing strings.
- [x] Re-run Plugin Tester smoke checks for email tools and a light account-gateway regression pass.
- [x] Verify Alynt Plugin Updater detects and installs `0.1.2` from the GitHub release asset.

### Guardrails

- Keep the cycle small; do not rework the whole email editor.
- Keep frontend output disabled by default on fresh install.
- Preserve the existing branded email templates, tokens, and preview/test-send UI unless a QA finding requires a narrow fix.
- Do not suppress WordPress core security/account emails unless the behavior can be verified safely and documented clearly.
- Keep release packaging exclusions aligned with the existing GitHub release workflow.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Local release-style zip excludes source/dev files and includes built assets.
- [x] Plugin Tester validates email preview/test-send and the selected email-change behavior.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.1 Small Release Cycle

### Scope

- [x] Add settings import/export JSON for all plugin-owned settings.
- [x] Add per-tab restore defaults with confirmation and diagnostics logging.
- [x] Add gateway screen preview mode while frontend output is disabled.
- [x] Add compatibility warnings for plugins that commonly modify login, registration, account pages, security redirects, or WooCommerce account endpoints.
- [x] Add focused unit coverage for settings schema defaults/sanitization, frontend-output routing, emergency bypass behavior, role access/admin toolbar rules, password policy matching, retention cleanup, and uninstall cleanup where practical.
- [x] Re-run Plugin Tester smoke checks after the release package is built.
- [x] Verify Alynt Plugin Updater detects and installs `0.1.1` from the GitHub release asset.

### Guardrails

- Keep frontend output disabled by default on fresh install.
- Do not change the public registration confirmation-first contract.
- Do not rework the dashboard architecture unless a small compatibility warning requires a narrow hook change.
- Prefer incremental tests and admin polish over broad refactors.
- Leave large-file refactors for a later structural release unless a v0.1.1 task directly forces a split.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Local release-style zip excludes source/dev files and includes built assets.
- [x] Plugin Tester validates admin settings actions, preview mode, gateway routes, and WooCommerce dashboard takeover.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## Implementation Phases

### Phase 1 - Scaffold And Baseline Tooling

- [x] Scaffold plugin in the existing empty target folder.
- [x] Initialize Git repository.
- [x] Add `AI_CODING_RULES.md`.
- [x] Add main plugin file with plugin header, version constant, text domain, and GitHub Plugin URI.
- [x] Add loader, activator, deactivator, i18n, and core plugin bootstrap classes.
- [x] Add Composer tooling for PHPCS/WPCS and PHPUnit/Brain Monkey.
- [x] Add npm/esbuild tooling for admin and frontend assets.
- [x] Add GitHub Actions release workflow for Alynt Plugin Updater compatibility.
- [x] Add `README.md`, `readme.txt`, `CHANGELOG.md`, `docs/SETTINGS.md`, `docs/HOOKS.md`, and `uninstall.php`.

### Phase 2 - Settings, Admin UI, And Observability

- [x] Add tabbed admin settings page.
- [x] Add central settings schema with defaults, sanitization, and cross-tab save protection.
- [x] Add tabs: General, URLs & Redirects, Branding & Layout, Screen Copy, Registration, Security & Spam, Emails, Dashboard, WooCommerce, Webhooks, Privacy & Data, Advanced/Tools.
- [x] Add import/export settings JSON.
- [x] Add per-tab restore defaults.
- [x] Add gateway screen preview mode while frontend output is disabled.
- [x] Add diagnostics and privacy-conscious logs.
- [x] Add settings-change audit entries.
- [x] Add retention cleanup for plugin-owned logs and pending records.

### Phase 3 - Account Routing And Gateway Screens

- [x] Implement frontend-output master switch.
- [x] Route `/login` to the branded login screen.
- [x] Route `/account?action=lostpassword`, `/account?action=register`, `/account?action=rp`, and `/account?action=logout` to branded screens.
- [x] Replace public `wp-login.php` links with branded routes when frontend output is enabled.
- [x] Add emergency bypass URL that opens native `wp-login.php` without authenticating or bypassing 2FA/security plugins.
- [x] Block `wp-admin` for roles other than administrators and shop managers.
- [x] Remove admin toolbar for roles other than administrators and shop managers.
- [x] Build responsive split-screen gateway template with one global background image.
- [x] Implement frontend templates from `docs/DESIGN_HANDOFF.md`.
- [x] Add logo upload and max-width control.
- [x] Add color controls for primary color, accent color, text, page background, surface, error, button background, and button text.
- [x] Add font stack controls for heading and body typography.
- [x] Add per-screen instruction/welcome text.
- [x] Add disabled-registration and invalid/expired-link branded states.

### Phase 4 - Registration, Passwords, And Spam Protection

- [x] Implement pending registration records.
- [x] Send registration confirmation email before creating a WordPress user.
- [x] Create WordPress user only after confirmation link and valid password setup.
- [x] Store first name, last name, email, generated username, and chosen password during final account creation only.
- [x] Add configurable username format with unique generated username collision handling.
- [x] Add password strength meter and matching validation.
- [x] Enforce minimum 12 characters, uppercase, lowercase, number, and special symbol.
- [x] Add terms/privacy agreement checkbox with relative URL path links.
- [x] Add Turnstile client and required server-side validation when enabled.
- [x] Add Reoon Email Verifier client and policy mapping.
- [x] Default Reoon policy: block invalid, disabled, disposable, and spamtrap; allow but flag catch-all, role-account, unknown, and inbox-full.
- [x] Add rate limits for registration, resend confirmation, login, and password reset flows.
- [x] Add neutral resend-confirmation handling for invalid/expired registration links.
- [x] Avoid account enumeration in public registration and resend-confirmation outcomes.
- [x] Avoid account enumeration in login and password-reset request messages.
- [x] Add branded WordPress password-reset key validation and password update flow for native reset links.

### Phase 5 - Emails And Webhooks

- [x] Add rich email template editor foundation.
- [x] Add template preview and test-send.
- [x] Add branded HTML wrapper, logo, colors, buttons, and plain-text fallback.
- [x] Add templates for password reset, password changed, registration confirmation/welcome, and email-change confirmation.
- [x] Wire disable toggles for password changed and email-change notification emails.
- [x] Wire branded overrides for native password reset, password changed, and email-change notification emails.
- [x] Wire the WordPress profile email-change request body through `new_user_email_content` as a branded plain-text template.
- [x] Add account-created welcome email and disable-toggle behavior.
- [x] Evaluate a safe replacement strategy if the disable toggle must suppress the profile email-change request email itself.
- [x] Add account-created webhook dispatcher.
- [x] Send full user fields in the account-created webhook payload.
- [x] Store webhook response metadata by default.
- [x] Store full payload bodies only when debug payload logging is enabled.
- [x] Retain successful webhook metadata for 7 days and failed webhook metadata for 30 days by default.

### Phase 6 - Dashboard And WooCommerce

- [x] Add optional custom full-page account dashboard.
- [x] Add custom dashboard links with icons, ordering, role visibility, and open-in-new-tab.
- [x] Detect WooCommerce availability.
- [x] Allow custom dashboard to take over WooCommerce My Account when enabled.
- [x] Delegate sensitive WooCommerce actions to native WooCommerce handlers/endpoints.
- [x] Preserve orders, downloads, addresses, payment methods, account details, and logout through standard WooCommerce endpoints.
- [x] Discover and preserve plugin-added WooCommerce account endpoints.
- [x] Add compatibility warnings for plugins that also modify login, registration, account pages, security redirects, or WooCommerce account endpoints.

### Phase 7 - Privacy, Accessibility, I18n, And Release Readiness

- [x] Add WordPress privacy policy text.
- [x] Add personal data exporter and eraser support.
- [x] Add retention settings for verification logs, webhook logs, consent records, and audit entries.
- [x] Store consent record with terms/privacy URLs, timestamp, and policy/version context.
- [x] Avoid storing IP by default unless explicitly enabled.
- [x] Ensure visible labels, keyboard operation, focus states, inline validation, `aria-invalid`, and live-region messages.
- [x] Add responsive CSS guardrails down to 320px.
- [x] Ensure frontend account-gateway strings are translatable and localize frontend JS labels through WordPress.
- [x] Generate POT file.
- [x] Ensure RTL-safe CSS for frontend gateway surfaces.
- [x] Run pre-release workflow sequence through cleanup, structure, error handling, WP practices, database, performance, edge cases, uninstall, i18n, accessibility, code quality, documentation, and security review.

## Test Plan

- [x] Unit test settings schema, defaults, sanitization, and cross-tab save protection.
- [x] Unit test URL routing and frontend-output master switch.
- [x] Unit test emergency bypass behavior.
- [x] Unit test role access and admin-toolbar rules.
- [x] Unit test email-only login behavior.
- [x] Unit test pending-registration lifecycle and expiry.
- [x] Unit test password policy and confirmation matching.
- [x] Unit test username generation and collision handling.
- [x] Unit test Reoon policy mapping.
- [x] Unit test Turnstile verification handling.
- [x] Unit test webhook payload construction and metadata logging.
- [x] Unit test retention cleanup.
- [x] Unit test uninstall cleanup.
- [x] Browser/manual QA login, lost password, set password, registration, logout confirmation, disabled registration, and invalid/expired link screens.
- [x] Browser/manual QA desktop and mobile responsive behavior.
- [x] Browser/manual QA keyboard-only flow and focus management.
- [x] Browser/manual QA email preview and test-send.
- [x] Browser/manual QA WooCommerce dashboard delegation.
- [x] Verify `npm run build`.
- [x] Verify `npm run lint`.
- [x] Verify `npm test`.
- [x] Verify `npm run make-pot`.
- [x] Verify PHP syntax across runtime and test PHP files.
- [x] Verify `npm audit --audit-level=high`.
- [x] Verify `composer audit`.
- [x] Verify release-style zip locally with GitHub workflow exclusions.
- [x] Verify generated release zip through GitHub release workflow.
- [x] Verify install/update from the GitHub release asset through Alynt Plugin Updater on LocalWP Plugin Tester.

## Release Gates

- [x] Frontend output remains disabled by default on fresh install.
- [x] Emergency bypass opens native login only and never authenticates users.
- [x] No standard WordPress core account screen is exposed during normal enabled frontend use.
- [x] Registration creates no WordPress user until email confirmation and password setup are complete.
- [x] WooCommerce account features remain usable when the custom dashboard is enabled.
- [x] Accessibility acceptance criteria pass for implemented gateway/dashboard surfaces.
- [x] Multilingual/i18n acceptance criteria pass for implemented strings and generated POT.
- [x] Privacy exporter/eraser and retention controls are present.
- [x] Alynt Plugin Updater compatibility is verified end to end by updating the LocalWP Plugin Tester install from a GitHub release asset.

## Pre-Release Audit Notes

### 2026-07-04

- Completed pre-release review sequence `01` through `13` from the wp-plugin-toolkit.
- Fixed release hygiene issue where `AI_CODING_RULES.md` would have been included in the GitHub release zip.
- Fixed admin media preview DOM handling by replacing `innerHTML` with explicit image node creation.
- Fixed uninstall cleanup coverage for the scheduled retention hook and transient-backed rate-limit buckets.
- Added HTTPS enforcement for public account-created webhook URLs while allowing local development hosts (`localhost`, `127.0.0.1`, `::1`, and `.local`).
- Updated README, `readme.txt`, changelog, settings docs, and hooks docs to reflect the implemented feature set instead of scaffold status.
- Regenerated `languages/alynt-account-gateway.pot` after the new webhook security string.
- Verified local release-style zip excludes source assets, dev dependencies, tests, docs, scripts, GitHub metadata, maps, Composer/npm files, and editor rules.
- Structural debt remains: `public/class-frontend.php`, `includes/services/class-registration-service.php`, and several admin/settings/template classes are larger than ideal. They are intentionally left intact for this release pass because splitting them now would be a high-blast-radius refactor after browser QA.
- Published GitHub release `v0.1.0`, confirmed the Build Release workflow completed successfully, downloaded `alynt-account-gateway-v0.1.0.zip`, and verified the package contains the plugin runtime files and built assets while excluding development files.
- Made the GitHub repository public for release delivery, forced the LocalWP Plugin Tester installed copy to `0.0.9`, confirmed Alynt Plugin Updater detected `0.0.9` to `0.1.0`, installed from the `alynt-account-gateway-v0.1.0.zip` release asset, and verified the active Plugin Tester copy returned to `0.1.0`.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, and `/my-account/` after the updater install.
- Remaining release decisions: optionally add uninstall-specific unit coverage before the next release.
- Installed the local `alynt-account-gateway-v0.1.1.zip` package on LocalWP Plugin Tester, verified the active plugin reports `0.1.1`, browser-smoked Advanced / Tools compatibility warnings, gateway preview mode, `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, `/my-account/`, and WooCommerce account endpoints for orders, downloads, addresses, payment methods, and account details.
- Published GitHub release `v0.1.1`, confirmed the Build Release workflow completed successfully, downloaded and inspected `alynt-account-gateway-v0.1.1.zip`, verified the package reports `0.1.1` and excludes development/source files, then confirmed Alynt Plugin Updater detected `0.1.0` to `0.1.1` and the WordPress Plugins screen `update now` action installed the GitHub release asset. Final Plugin Tester state: active `0.1.1`, no remaining update, settings intact.
- Started v0.1.2 email QA on LocalWP Plugin Tester. Verified the Emails tab exposes all five templates, preview renders successful branded HTML for registration confirmation, password reset, password changed, account-created welcome, and email-change confirmation, and the test-send form redirects with `email_test_sent`. SureMails was temporarily switched to simulation mode for the send test, logged `Reset your password for Plugin Tester` to `alynt-ag-v012-qa@example.test` as simulated, and was restored to its prior non-simulation setting.
- Added focused email tooling coverage for preview-token rendering across every supported template and test-send rejection paths for invalid recipients and unknown templates. Verified `npm.cmd test` passes with 94 tests and 354 assertions.
- Implemented the remaining profile email-change request suppression for the existing email-change disable toggle. WordPress core sends this pending request through a direct `wp_mail()` call after `new_user_email_content`, so the plugin now marks that exact request when disabled, short-circuits it through `pre_wp_mail`, and removes the pending `_new_email` marker to avoid an impossible confirmation state. Verified `npm.cmd test` passes with 96 tests and 359 assertions, and `npm.cmd run lint` passes.
- Refreshed docs, changelog, and `languages/alynt-account-gateway.pot` after the email-change behavior update. No new translatable strings were added; POT changes were source-reference/date metadata.
- Bumped release candidate metadata to `0.1.2` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.2-20260704-143449\alynt-account-gateway-v0.1.2.zip`; verified built assets are included, dev/source/test/docs/package files are excluded, and the package header/constant report `0.1.2`.
- Installed the local `0.1.2` package on LocalWP Plugin Tester, verified active header and loaded constant are `0.1.2`, browser-smoked `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, `/my-account/`, `/my-account/orders/`, and `/my-account/edit-account/`, and verified no native WordPress login shell appears on gateway routes.
- Re-ran installed-copy email QA on Plugin Tester: all five admin previews returned branded HTML, test-send logged `Confirm your email address for Plugin Tester` to `alynt-ag-v012-smoke@example.test` as simulated through SureMails, SureMails simulation was restored to `no`, and the email-change suppression path returned `false` through `pre_wp_mail` while clearing `_new_email` and restoring the original setting.
- Published GitHub release `v0.1.2`, confirmed the Build Release workflow completed successfully, and verified the attached `alynt-account-gateway-v0.1.2.zip` release asset. Downgraded LocalWP Plugin Tester to the public `v0.1.1` release asset, confirmed Alynt Plugin Updater detected `0.1.1` to `0.1.2`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.2` GitHub release asset, and verified final Plugin Tester state: active `0.1.2`, no remaining update.

## Workflow Notes

- Use `C:\Users\Captain\Documents\AI Workflows\Toolkits\wp-plugin-toolkit\START_HERE_MASTER_WORKFLOW.md` as the router for plugin work.
- Scaffold/observability checkpoint commit: `c0daf48` (`Scaffold account gateway foundation`).
- Design workflow Phase 1 has been completed using the supplied login/register/lost-password screenshots as visual references.
- Design export received and distilled into `docs/DESIGN_HANDOFF.md`; use it as the implementation source for frontend gateway templates.
- Next toolkit step before scaffold: use `d1-setup/ai-plugin-setup-reference.md` Section 2 to create the scaffold master prompt.
- After scaffold, route to `@ADD_OBSERVABILITY_TOOLING_PROMPT.md run` before heavy feature work.
- After each major feature, run the feature review sequence: light review, bloat/structure review, UI/UX review, and security review.
- Before release, run pre-release prompts `@01` through `@13` in filename order, keeping security last.
- Do not update `PRE_RELEASE_CHECKLIST.md` unless a supported toolkit workflow completes successfully.

## Scaffold Prompt

- [x] Created `docs/SCAFFOLD_MASTER_PROMPT.md` from the approved product plan and toolkit scaffold guidance.

## Change Log

### 2026-07-03

- Committed scaffold and observability foundation as checkpoint `c0daf48`.
- Added design workflow gate before account routing and gateway screen implementation.
- Captured the Claude design export as a durable implementation handoff in `docs/DESIGN_HANDOFF.md`.
- Clarified that the design palette and fonts are default starter values only; production output is brand-agnostic and settings-driven.
- Implemented the first Phase 3 frontend foundation: branded route detection, native login redirect with emergency bypass, URL filters, settings-backed design tokens, responsive gateway shell, screen templates, logout confirmation handling, and password visibility toggle.
- Added WordPress media-library controls for the brand logo and gateway background image, plus configurable instruction text for each gateway screen.
- Added the Phase 4 pending-registration foundation: frontend registration submission handling, hashed confirmation tokens, 24-hour configurable expiry, confirmation email delivery, email-confirmed pending state, and token helper tests.
- Added final pending-registration account creation: set-password POST handling, password confirmation/policy validation, generated username creation, WordPress user creation, profile name persistence, pending record consumption, and DB schema upgrade check.
- Added client-side password policy UX for set-password: live strength bars, translated status text, requirement states, password-match feedback, and disabled submit until the configured v1 policy is satisfied.
- Added server-side terms/privacy acceptance validation for registration submissions, matching the required frontend checkbox and relative path links.
- Added Turnstile/Reoon registration protection: Turnstile widget rendering, server-side Siteverify validation, Reoon single-email verification, default OR policy when both providers are configured, and provider interpretation tests.
- Added transient-backed rate limiting for registration, resend confirmation, login, and password reset buckets with privacy-preserving hashed keys.
- Added expired-link recovery: invalid/expired registration links can request a new confirmation email, pending tokens are renewed without creating users, resend attempts use their own rate bucket, and public responses stay neutral when no pending registration exists.
- Added branded auth POST handling for login and password-reset requests so failed submissions return to gateway screens with neutral public messages instead of native WordPress screens.
- Added branded native password-reset completion: WordPress reset links with `key` and `login` now render the gateway set-password screen, validate through WordPress reset-key APIs, enforce the v1 password policy, and redirect to branded login after success.
- Added Phase 5 email foundation: editable template settings for account emails, branded HTML/plain rendering, preview and test-send admin tools, and registration confirmation emails routed through the renderer.
- Added native email overrides for WordPress password reset, password changed, and email-change notification emails, including branded HTML output, gateway reset links, and disable toggles for password/email change notifications.
- Added observability tooling: diagnostics settings, structured logs, health/recent-event UI, export/clear actions, retention cleanup, and redaction tests.
- Added frontend accessibility/i18n hardening: server error IDs and `aria-describedby` wiring, translated password-toggle JavaScript labels, new-tab screen-reader text, Turnstile/verification semantics, RTL-safe frontend CSS, 320px responsive guardrails, repeatable POT generation tooling, and regenerated the plugin POT file.
- Ran LocalWP Plugin Tester browser QA with Playwright/Chrome across public gateway routes, authenticated dashboard flow, non-admin `wp-admin` redirect, logout confirmation, native `wp-login.php` redirect, 320px responsive behavior, keyboard tab order, registration disabled state, registration submit gating, and pending-registration set-password completion.
- Fixed QA findings: successful login with no submitted `redirect_to` now redirects to the configured dashboard instead of preserving the underlying 404 response, and the registration submit button now remains disabled until required fields, valid email, and terms acceptance are complete.
- Installed and activated WooCommerce on LocalWP Plugin Tester, enabled the account gateway WooCommerce takeover, and browser-tested the custom dashboard plus native Orders, Downloads, Addresses, Payment Methods, and Account Details endpoint delegation.
- Fixed WooCommerce QA finding: required standard account facilities such as Payment Methods are restored in the custom dashboard navigation when WooCommerce omits them from its menu helper on a minimal store.
- Completed pre-release cleanup/security/documentation/package pass: hardened webhook URL scheme policy, expanded uninstall cleanup, removed admin preview `innerHTML`, refreshed docs/readme/changelog/hooks/settings notes, regenerated POT, fixed release zip exclusions, and verified build/lint/tests/audits/package locally.
- Completed the initial scaffold, initialized Git, installed dependencies, and verified build/lint/test/audit.
- Added scaffold master prompt artifact for the initial plugin foundation.
- Created initial implementation plan from approved product-planning decisions.
