# Alynt Account Gateway Implementation Plan

## Status

- Current phase: v0.1.42 Registration flow visibility shipped; selecting next slice
- Target path: `C:\Development\WordPress\Plugins\alynt-account-gateway`
- Plugin status: v0.1.42 is the current public baseline after GitHub release and Alynt Plugin Updater verification.
- Frontend output default: Disabled
- Distribution: Alynt-distributed plugin with GitHub updater compatibility

## Remaining Product Slices

- [x] Settings readiness and onboarding checks: show whether required URL, registration, protection, branding, email, dashboard, WooCommerce, webhook, privacy, and frontend-output prerequisites are ready before site owners enable public output.
- [ ] Real-world WooCommerce dashboard polish: improve branded empty states, endpoint affordances, customer account copy, delegated WooCommerce form styling, order/address/payment-method edge states, and WooCommerce unavailable guidance.
- [x] Settings UX refinement: improve setup grouping, tab-level guidance, validation hints, admin notices, and safe defaults for first-time configuration.
- [x] Email template editor polish: add richer token browsing, per-template reset guidance, preview/test-send ergonomics, and clearer plain-text/core-email limitations.
- [ ] Security and anti-spam hardening: improve Reoon policy visibility, provider failure feedback, registration abuse logs, lockout visibility, resend throttling UX, and optional manual-review decisions.
- [ ] Accessibility, RTL, and multilingual QA pass: verify keyboard flow, focus states, ARIA messaging, contrast resilience, RTL layout behavior, and translation coverage across frontend/admin screens.
- [ ] Frontend visual QA and theme compatibility: smoke common themes, mobile/desktop breakpoints, high-contrast settings, and CSS interference around the gateway shell.
- [ ] Admin observability: add clearer diagnostics for auth redirects, blocked wp-admin access, provider verification failures, registration failures, email sends, and webhook failures.
- [ ] Import/export/reset experience: strengthen preset export/import, tab-level restore guidance, import validation, and configuration portability.
- [ ] Uninstall and data cleanup coverage: add explicit uninstall tests and verify plugin-owned tables/options/scheduled hooks cleanup policy.

## v0.1.42 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add read-only Registration Flow Signals to the Security tab using existing verification activity rows.
- [x] Summarize recent consent-related blocks, pending-record or confirmation-email failures, password setup blocks, and successful confirmation resends without changing public registration behavior.
- [x] Keep changes scoped to admin visibility with no settings schema, frontend routing, provider verification policy, rate-limit enforcement, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for registration-flow signal counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.42` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.42` from clean `master` after the `v0.1.41` release merge.
- Added a Registration Flow Signals summary above the Security tab verification activity table.
- The summary derives counts from existing recent `registration_flow` rows and separates consent blocks, registration system failures, password setup blocks, and successful confirmation resends.
- Added focused tests for registration-flow signal counts and rendered guidance copy.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 10 tests and 148 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 752 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 212 tests and 1116 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.42-branch-qa-20260705-231528\alynt-account-gateway-v0.1.42-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.41` metadata, Registration Flow Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA runtime package into the LocalWP Plugin Tester plugin directory over active `0.1.41`. Fresh runtime smoke confirmed active pre-bump header `0.1.41` and loaded constant `0.1.41`, Registration Flow Signals renderer and CSS are present, seeded consent/system/password/resend registration-flow rows render the new flow copy alongside Provider Health Signals and Rate Limit Pressure, and temporary verification rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.42` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 752 strings, PHP syntax checks for the main plugin file and touched settings page, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 212 tests and 1116 assertions, and `git diff --check` all passed.
- Created final local package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.42-20260706-114930\alynt-account-gateway-v0.1.42.zip`; verified 45 runtime file entries, no directory entries, no backslash entries, no dev entries, `0.1.42` plugin/readme/POT metadata, and Registration Flow Signals markers present. Installed the final package on LocalWP Plugin Tester through WordPress upgrader classes and confirmed active header `0.1.42`, loaded constant `0.1.42`, Registration Flow Signals rendering, Provider Health Signals, Rate Limit Pressure, and zero temporary QA rows remaining after cleanup.
- Published GitHub release `v0.1.42`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public `alynt-account-gateway-v0.1.42.zip` asset, verified runtime-only packaging and `0.1.42` metadata, downgraded LocalWP Plugin Tester to the public `v0.1.41` asset, confirmed Alynt Plugin Updater detected `0.1.41` to `0.1.42`, upgraded from the `v0.1.42` GitHub release asset, and verified final Plugin Tester state: active `0.1.42`, no remaining update, Registration Flow Signals render after upgrade, and zero temporary QA rows remaining.

### Guardrails

- Do not change registration validation, confirmation email sending, password rules, pending registration storage, provider API calls, provider status interpretation, Reoon flagged-status policy, public frontend error messages, rate-limit thresholds, transient keying, login behavior, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only registration-flow visibility using existing plugin-owned verification activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Registration Flow Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.41 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add read-only Provider Health Signals to the Security tab using existing verification activity rows.
- [x] Summarize recent Turnstile challenge rejections, Turnstile configuration/connectivity failures, Reoon email-quality blocks, and Reoon provider failures without changing provider policy or public responses.
- [x] Keep changes scoped to admin visibility with no settings schema, frontend routing, provider verification policy, rate-limit enforcement, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for provider-health counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.41` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.41` from clean `master` after the `v0.1.40` release merge.
- Added a Provider Health Signals summary above the Security tab verification activity table.
- The summary derives counts from existing recent verification rows and separates Turnstile challenge rejections, Turnstile configuration/connectivity failures, Reoon email-quality blocks, and Reoon provider failures.
- Added focused tests for provider-health item counts and rendered guidance copy.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 9 tests and 127 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 742 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 211 tests and 1095 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.41-branch-qa-20260705-222244\alynt-account-gateway-v0.1.41-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.40` metadata, Provider Health Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.40` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.40`, Provider Health Signals renderer and CSS are present, seeded Turnstile/Reoon provider-health rows render the new health copy plus Rate Limit Pressure, temporary verification rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Bumped release-candidate metadata to `0.1.41` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 742 strings, PHP syntax checks for the main plugin file and touched settings page, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 211 tests and 1095 assertions, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.41-20260705-222832\alynt-account-gateway-v0.1.41.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.41` header/constant/readme/POT metadata, Provider Health Signals renderer, and built admin CSS present.
- Installed the `0.1.41` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.41` and loaded constant `0.1.41`. Runtime smoke confirmed Provider Health Signals render with Turnstile/Reoon challenge, connectivity, email-block, and provider-failure cards; temporary verification rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Published GitHub release `v0.1.41`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.41`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.41.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.41` metadata, Provider Health Signals renderer, and built admin CSS present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.40` release asset, confirming runtime `0.1.40` with Provider Health Signals markers absent, clearing updater scanner/release caches, running a fresh Alynt Plugin Updater check to discover `0.1.41`, confirming the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming final active runtime `0.1.41` with no update remaining, and re-smoking Provider Health Signals output after the updater install. Temporary verification rows and uploaded QA artifacts were cleaned up after verification.

### Guardrails

- Do not change provider API calls, provider status interpretation, Reoon flagged-status policy, public frontend error messages, rate-limit thresholds, transient keying, login or registration behavior, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only provider-health visibility using existing plugin-owned verification activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Provider Health Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.40 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a read-only Rate Limit Pressure summary to the Security tab using existing verification activity rows.
- [x] Summarize recent registration, confirmation resend, login, and password-reset rate-limit blocks without changing enforcement thresholds or public responses.
- [x] Keep changes scoped to admin visibility with no settings schema, rate-limit storage, frontend routing, provider verification policy, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for pressure summary counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.40` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.40` from clean `master` after the `v0.1.39` release merge.
- Added a Rate Limit Pressure summary above the existing Recent Registration Verification Activity table.
- The summary derives counts from existing recent `rate_limit` verification rows and shows separate cards for Registration, Confirmation Resends, Login, and Password Reset.
- Added small admin CSS rules for the summary heading and card headings, then rebuilt the admin asset bundle.
- Verified focused checks: PHP syntax passes for the touched admin settings page, focused `SettingsPageSecurityStatusTest` passes with 8 tests and 110 assertions, `npm.cmd run build` passes, and `npm.cmd run lint` passes.
- Verified broader local checks: `npm.cmd run make-pot` writes 732 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 210 tests and 1078 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.40-branch-qa-20260705-204413\alynt-account-gateway-v0.1.40-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.39` metadata, Rate Limit Pressure renderer, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.39` through WordPress upgrader classes. Fresh runtime smoke confirmed active header and loaded constant remain pre-bump `0.1.39`, Rate Limit Pressure renderer and CSS are present, seeded registration/resend/login/password-reset rate-limit rows render the new pressure copy plus existing table rows, and temporary verification rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.40` across the plugin header/constant, npm metadata, readme, sample test, changelog, POT, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 732 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 210 tests and 1078 assertions, PHP syntax check for the main plugin file, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.40-20260705-204748\alynt-account-gateway-v0.1.40.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.40` header/constant/readme/POT metadata, Rate Limit Pressure renderer, and built admin CSS present.
- Installed the `0.1.40` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.40` and loaded constant `0.1.40`. Runtime smoke confirmed Rate Limit Pressure renderer and CSS are present, seeded registration/resend/login/password-reset rate-limit rows render the new pressure copy, and temporary verification rows were cleaned up after QA.
- Published GitHub release `v0.1.40`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.40`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.40.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.40` metadata, Rate Limit Pressure renderer, and built admin CSS present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.39` release asset, confirming runtime `0.1.39` with Rate Limit Pressure markers absent, clearing updater scanner/release caches, detecting the available `0.1.40` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming final active runtime `0.1.40` with no update remaining, and re-smoking Rate Limit Pressure output after the updater install. Temporary verification rows were cleaned up after verification.

### Guardrails

- Do not change rate-limit thresholds, transient keying, public frontend rate-limit messages, login or registration behavior, provider verification policy, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only rate-limit visibility using existing plugin-owned verification activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Rate Limit Pressure summary on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.39 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a configurable Reoon flagged-status policy with a safe default that continues to allow and log catch-all, role account, unknown, and inbox-full statuses.
- [x] Allow stricter sites to block flagged Reoon statuses before account creation while preserving the original Reoon status in admin-visible activity logs.
- [x] Update the Security tab policy cards and activity guidance so admins can distinguish always-blocked Reoon statuses from configurable flagged statuses.
- [x] Add frontend-safe customer messaging for blocked flagged Reoon statuses without exposing provider internals.
- [x] Add focused coverage for setting defaults/sanitization, flagged-policy behavior, frontend messages, and Security tab guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.39` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.39` from clean `master` after the `v0.1.38` release merge.
- Added `reoon_flagged_policy` on the Security tab with `allow` as the default and `block` as the stricter option.
- Added generic schema-backed select rendering and option sanitization while preserving normal secret/API-key sanitization.
- Added strict flagged-status blocking through the registration protection service, returning `alynt_ag_reoon_flagged_blocked` and logging compact statuses such as `role_account_flagged_blocked`.
- Split the Security tab Reoon policy visibility into `Reoon Blocked Statuses` and `Reoon Flagged Statuses`, with guidance that changes based on the configured flagged-status policy.
- Added frontend-safe copy for blocked flagged Reoon statuses and admin guidance for `*_flagged_blocked` verification activity rows.
- Verified focused checks: PHP syntax passes for touched settings, admin, registration, and frontend message files; focused `SettingsSchemaTest` passes with 20 tests and 90 assertions; focused `RegistrationServiceTest` passes with 24 tests and 98 assertions; focused `FrontendMessagesTest` passes with 5 tests and 16 assertions; focused `SettingsPageSecurityStatusTest` passes with 7 tests and 95 assertions; and `npm.cmd run lint` passes.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 725 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 209 tests and 1063 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.39-branch-qa-20260705-201831\alynt-account-gateway-v0.1.39-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.38` metadata, Reoon flagged-policy setting, blocked flagged status handling, Security tab policy cards, frontend-safe blocked message, built assets, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.38` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.38`. Runtime smoke confirmed default flagged policy `allow`, select sanitization for `block` and invalid values, frontend-safe blocked flagged message, simulated Reoon `role_account` blocking with `alynt_ag_reoon_flagged_blocked`, verification activity status `role_account_flagged_blocked`, Security tab blocked-policy copy, and `*_flagged_blocked` admin guidance. Temporary verification rows and uploaded ZIPs were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.39` across the plugin header/constant, npm metadata, readme, sample test, changelog, POT, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 725 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 209 tests and 1063 assertions, PHP syntax check for the main plugin file, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.39-20260705-202439\alynt-account-gateway-v0.1.39.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.39` header/constant/readme/POT metadata, Reoon flagged-policy setting, blocked flagged status handling, Security tab policy cards, frontend-safe blocked message, and built assets present.
- Installed the `0.1.39` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.39` and loaded constant `0.1.39`. Runtime smoke confirmed default flagged policy `allow`, select sanitization for `block` and invalid values, frontend-safe blocked flagged message, simulated Reoon `role_account` blocking with `alynt_ag_reoon_flagged_blocked`, verification activity status `role_account_flagged_blocked`, Security tab blocked-policy copy, and `*_flagged_blocked` admin guidance. Temporary verification rows were cleaned up after QA.
- Published GitHub release `v0.1.39`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.39`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.39.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.39` metadata, Reoon flagged-policy setting, blocked flagged status handling, Security tab policy cards, frontend-safe blocked message, and built assets present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.38` release asset, confirming runtime `0.1.38` with Reoon flagged-policy markers absent, clearing updater scanner/release caches, detecting the available `0.1.39` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming final active runtime `0.1.39` with no update remaining, and re-smoking blocked flagged Reoon behavior plus Security tab guidance after the updater install. Temporary verification rows were cleaned up after verification.

### Guardrails

- Do not change frontend routes, Reoon request payloads, Turnstile behavior, rate-limit thresholds, registration success behavior, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep the default Reoon flagged-status behavior permissive and admin-visible: allow flagged statuses unless the site explicitly changes the new setting to block.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates flagged-policy settings, blocked flagged status logging, frontend-safe copy, and Security tab guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.38 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add clearer frontend copy for resend-confirmation throttling while keeping public resend outcomes neutral.
- [x] Log successful confirmation resends for existing pending registrations as admin-visible `registration_flow` activity without logging missing-pending neutral outcomes.
- [x] Add Security tab pending-registration next-step guidance for pending, email-confirmed, expired, and completed records.
- [x] Improve Security tab guidance for resend-confirmation rate-limit blocks.
- [x] Keep changes scoped to resend/expiry visibility with no settings schema, frontend route, pending-registration table schema, provider verification policy, rate-limit thresholds, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for resend messages, resend success activity, resend throttle guidance, and pending-registration next-step guidance.
- [x] Run package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.38` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.38` from clean `master` after the `v0.1.37` release merge.
- Added frontend copy for resend throttling: "Too many confirmation email requests. Please wait a moment and try again."
- Added admin-visible `registration_flow` / `confirmation_resent` logging only when a real pending registration is renewed and the confirmation email send succeeds.
- Added a Next Step column to Recent Pending Registrations so admins can distinguish waiting-for-confirmation, email-confirmed password setup, expired-link resend, and completed-account states.
- Updated Security tab guidance for `resend_confirmation_rate_limited` rows so admins know the customer should wait for the configured resend window before retrying.
- Verified focused checks: PHP syntax passes for touched frontend, registration, and admin files; focused `FrontendMessagesTest` passes with 5 tests and 15 assertions; focused `RegistrationServiceTest` passes with 21 tests and 89 assertions; focused `SettingsPageSecurityStatusTest` passes with 6 tests and 90 assertions; and `npm.cmd run lint` passes.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 713 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 204 tests and 1045 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.38-branch-qa-20260705-193210\alynt-account-gateway-v0.1.38-branch-qa-wp.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.37` metadata, resend throttle copy, `confirmation_resent` logging/guidance, pending-registration Next Step guidance, built assets, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.37` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.37`, and the new markers are present. Runtime smoke confirmed the resend-throttle frontend message, inserted disposable `confirmation_resent` and `resend_confirmation_rate_limited` activity rows plus pending and expired pending-registration rows, authenticated admin HTML smoke confirmed the Security tab renders resend throttle guidance, confirmation resent guidance, Next Step guidance, masked pending/expired emails, and no fatal/critical error output. Temporary activity rows, pending-registration rows, upload ZIPs, and an initial duplicated upload artifact were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.38` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 713 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 204 tests and 1045 assertions, and PHP syntax check for the main plugin file all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.38-20260705-194205\alynt-account-gateway-v0.1.38.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.38` header/constant/readme/POT metadata, resend throttle copy, `confirmation_resent` logging/guidance, pending-registration Next Step guidance, built assets, and POT strings present.
- Installed the `0.1.38` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.38`, loaded constant `0.1.38`, and the new markers are present. Runtime smoke confirmed the resend-throttle frontend message, inserted disposable `confirmation_resent` and `resend_confirmation_rate_limited` activity rows plus pending and expired pending-registration rows, authenticated admin HTML smoke confirmed the Security tab renders resend throttle guidance, confirmation resent guidance, Next Step guidance, masked pending/expired emails, and no fatal/critical error output. Temporary activity rows, pending-registration rows, and upload ZIPs were cleaned up after QA.
- Published GitHub release `v0.1.38`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.38`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.38.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.38` metadata, resend throttle copy, `confirmation_resent` logging/guidance, pending-registration Next Step guidance, built assets, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.37` release asset, confirming runtime `0.1.37` with the new resend copy and Next Step markers absent, clearing updater caches, detecting the available `0.1.38` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.38` with no update remaining, and re-smoking resend throttle copy plus Security tab guidance after the updater install. Temporary activity rows, pending-registration rows, and upload artifacts were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, pending-registration table schema, provider verification behavior, provider request payloads, Reoon/Turnstile policy decisions, rate-limit thresholds, registration success behavior, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep public resend-confirmation success responses neutral and do not expose whether a pending registration exists.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates resend throttle copy, resend success activity, and pending-registration next-step guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.35 Small Release Cycle

### Scope

- [x] Start the next admin observability slice from the released `master` baseline.
- [x] Log native `wp-login.php` redirect decisions into the existing diagnostics table when diagnostics are enabled.
- [x] Log blocked `wp-admin` access for non-privileged users into the existing diagnostics table when diagnostics are enabled.
- [x] Keep changes scoped to diagnostics evidence with no settings schema, frontend route, redirect destination, capability, toolbar, login, registration, dashboard, WooCommerce, webhook, email, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage proving diagnostics rows are written without storing raw login or redirect query values.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.35` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.35` from clean `master` after the `v0.1.34` release merge.
- Added diagnostics events for `native_login_redirected` and `wp_admin_access_blocked` using the existing diagnostics settings gate and custom diagnostics table.
- Kept diagnostics context privacy-conscious by recording action, destination path, preserved query argument names, request method, and user id when available, without storing raw login, key, or redirect query values.
- Added focused `FrontendRoutingTest` coverage for native-login redirect diagnostics and blocked wp-admin diagnostics.
- Verified initial local checks: PHP syntax passes for touched frontend/test files, focused `FrontendRoutingTest` passes with 7 tests and 34 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 689 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 199 tests and 994 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.35-branch-qa-20260705-172521\alynt-account-gateway-v0.1.35-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.34` metadata, routing diagnostics PHP, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.34` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.34`, and both routing diagnostics event codes are present. Runtime HTTP smoke enabled diagnostics temporarily, triggered a native `wp-login.php` redirect and blocked subscriber `wp-admin` access, confirmed `native_login_redirected` and `wp_admin_access_blocked` diagnostics rows were written without raw login email or full redirect URL values, and authenticated admin HTML smoke confirmed the Advanced / Tools diagnostics panel renders both event codes with no fatal/critical error output. Temporary settings, subscriber user, diagnostics rows, cookie state, and upload artifacts were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.35` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 689 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 199 tests and 994 assertions, PHP syntax checks for the main plugin and frontend class, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.35-20260705-173449\alynt-account-gateway-v0.1.35.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.35` header/constant/readme/POT metadata, routing diagnostics PHP, built admin CSS, and POT strings present.
- Installed the `0.1.35` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.35`, loaded constant `0.1.35`, and both diagnostics helpers present. Runtime HTTP smoke temporarily enabled diagnostics, triggered a native `wp-login.php` lost-password redirect and blocked subscriber `wp-admin` access, confirmed exactly the expected diagnostics events were written without raw login email or full redirect URL values, and authenticated admin HTML smoke confirmed the Advanced / Tools diagnostics panel renders both event codes with no fatal/critical error output. Temporary settings, subscriber user, diagnostics rows, cookie state, and upload artifacts were cleaned up after QA.
- Published GitHub release `v0.1.35`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.35`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.35.zip` verified with 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.35` metadata, routing diagnostics PHP, built admin CSS, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.34` release asset, confirming runtime `0.1.34` with routing diagnostics absent, clearing updater caches, detecting the available `0.1.35` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.35` with no update remaining, and re-smoking the two diagnostics events after the updater install. Temporary settings, subscriber user, diagnostics rows, cookie state, and uploaded downgrade artifact were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, redirect destinations, emergency bypass behavior, wp-admin capability checks, toolbar behavior, login behavior, registration flow, provider verification behavior, rate-limit enforcement, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, email delivery behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on diagnostics-only observability for account routing decisions.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates diagnostics events for native login redirects and blocked wp-admin access.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.36 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Log registration-flow failures into the existing verification activity table with `registration_flow` as the provider.
- [x] Add Security tab guidance for registration-flow failures such as missing terms consent, pending-registration storage failure, consent-record storage failure, confirmation-email failure, password mismatch, password-strength failure, and email becoming unavailable during account creation.
- [x] Keep changes scoped to admin-visible activity evidence with no settings schema, frontend routing, registration success path, provider verification policy, rate-limit thresholds, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for registration-flow activity rows and Security tab guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.36` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.36` from clean `master` after the `v0.1.35` release merge.
- Added reusable `registration_flow` activity logging to the existing plugin-owned verification log table.
- Added logging at blocked or failed registration-flow outcomes where a valid submitted email is available, including terms consent, pending registration storage, consent storage, confirmation email delivery, password validation, email availability during account creation, and user creation errors.
- Added Security tab provider labeling and guidance for `registration_flow` rows.
- Verified focused checks: PHP syntax passes for touched registration/admin files, focused `RegistrationServiceTest` passes with 20 tests and 83 assertions, and focused `SettingsPageSecurityStatusTest` passes with 6 tests and 71 assertions.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 699 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 202 tests and 1013 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.36-branch-qa-20260705-182425\alynt-account-gateway-v0.1.36-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.35` metadata, registration-flow logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.35` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.35`, and the registration-flow logger and Security tab guidance are present. Runtime smoke wrote a disposable `registration_flow` / `terms_required` activity row through the service, authenticated admin HTML smoke confirmed the Security tab renders the masked email, `Registration Flow` provider label, `terms_required` status, and terms-consent guidance with no fatal/critical error output. Temporary activity row, cookie state, and upload artifact were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.36` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 699 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 202 tests and 1013 assertions, PHP syntax checks for the main plugin, registration service, and admin settings page, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.36-20260705-182920\alynt-account-gateway-v0.1.36.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.36` header/constant/readme/POT metadata, registration-flow logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Installed the `0.1.36` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.36`, loaded constant `0.1.36`, and the registration-flow logger and Security tab guidance are present. Runtime smoke wrote a disposable `registration_flow` / `terms_required` activity row through the service, authenticated admin HTML smoke confirmed the Security tab renders the masked email, `Registration Flow` provider label, `terms_required` status, and terms-consent guidance with no fatal/critical error output. Temporary activity row, cookie state, and upload artifact were cleaned up after QA.
- Published GitHub release `v0.1.36`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.36`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.36.zip` verified with 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.36` metadata, registration-flow logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.35` release asset, confirming runtime `0.1.35` with registration-flow logging absent, clearing updater caches, detecting the available `0.1.36` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.36` with no update remaining, and re-smoking the `registration_flow` activity row plus Security tab guidance after the updater install. Temporary activity row, cookie state, and uploaded downgrade artifact were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, registration success behavior, provider policy decisions, rate-limit thresholds, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-visible registration-flow activity evidence.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates `registration_flow` activity rows and Security tab guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.37 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add frontend-safe registration messages for Reoon and Turnstile provider failures without exposing provider internals or sensitive configuration details.
- [x] Improve Security tab guidance so Reoon and Turnstile failures distinguish policy blocks, missing configuration, provider connectivity, invalid provider responses, and failed customer challenges.
- [x] Keep changes scoped to copy/guidance only with no settings schema, frontend routing, registration success path, provider request payloads, provider policy decisions, rate-limit thresholds, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for frontend provider messages and Security tab provider guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.37` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.37` from clean `master` after the `v0.1.36` release merge.
- Added frontend-safe registration messages for `alynt_ag_reoon_blocked`, `alynt_ag_reoon_missing`, `alynt_ag_reoon_request_failed`, `alynt_ag_reoon_invalid_response`, `alynt_ag_turnstile_failed`, `alynt_ag_turnstile_missing`, and `alynt_ag_turnstile_request_failed`.
- Updated Security tab provider guidance so admins get clearer next actions for missing Reoon keys, Reoon connectivity failures, unexpected Reoon responses, Turnstile challenge rejection, missing Turnstile keys, and Cloudflare verification connectivity failures.
- Verified focused checks: PHP syntax passes for touched frontend/admin files, focused `FrontendMessagesTest` passes with 5 tests and 15 assertions, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 81 assertions, and `npm.cmd run lint` passes after automatic array-alignment cleanup.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 706 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 203 tests and 1030 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.37-branch-qa-20260705-184835\alynt-account-gateway-v0.1.37-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.36` metadata, frontend-safe provider messages, Security tab provider guidance, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.36` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.36`, and the provider message/guidance markers are present. Runtime smoke confirmed frontend-safe Reoon and Turnstile registration messages through the message service, inserted disposable Reoon and Turnstile provider failure rows, and authenticated admin HTML smoke confirmed the Security tab renders the new guidance with masked emails and no fatal/critical error output. Temporary activity rows, cookie state, and upload artifact were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.37` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 706 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 203 tests and 1030 assertions, PHP syntax checks for the main plugin, frontend messages, and admin settings page, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.37-20260705-185509\alynt-account-gateway-v0.1.37.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.37` header/constant/readme/POT metadata, frontend-safe provider messages, Security tab provider guidance, built admin CSS, and POT strings present.
- Installed the `0.1.37` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.37`, loaded constant `0.1.37`, and the provider message/guidance markers are present. Runtime smoke confirmed frontend-safe Reoon and Turnstile registration messages through the message service, inserted disposable Reoon and Turnstile provider failure rows, and authenticated admin HTML smoke confirmed the Security tab renders the new guidance with masked emails and no fatal/critical error output. Temporary activity rows, cookie state, and upload artifact were cleaned up after QA.
- Published GitHub release `v0.1.37`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.37`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.37.zip` verified with 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.37` metadata, frontend-safe provider messages, Security tab provider guidance, built admin CSS, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.36` release asset, confirming runtime `0.1.36` with the new provider message markers absent, clearing updater caches, detecting the available `0.1.37` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.37` with no update remaining, and re-smoking frontend-safe provider messages plus Security tab guidance after the updater install. Temporary activity rows, cookie state, and uploaded downgrade artifact were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, provider validation behavior, provider request payloads, Reoon/Turnstile policy decisions, rate-limit thresholds, registration success behavior, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on provider failure feedback and admin guidance copy.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates frontend-safe provider messages and Security tab guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.34 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Log blocked login and password-reset rate-limit outcomes into the existing verification activity table.
- [x] Add admin guidance for `login_rate_limited` and `lostpassword_rate_limited` activity rows.
- [x] Keep changes scoped to auth-side rate-limit evidence and admin visibility with no settings schema, frontend routing, authentication success/failure behavior, reset email behavior, rate-limit thresholds, dashboard, WooCommerce, webhook, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for auth-side rate-limit logging and rendered guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.34` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.34` from clean `master` after the `v0.1.33` release merge.
- Reused the existing plugin-owned `verification_logs` table instead of adding a schema migration.
- Added blocked login and password-reset throttles to the shared Security tab activity stream with `rate_limit` as the provider and `login_rate_limited` / `lostpassword_rate_limited` statuses.
- Added Guidance column messages for blocked login attempts and blocked password-reset requests.
- Added focused `AuthServiceTest` coverage proving both auth limiter buckets write blocked activity rows, plus `SettingsPageSecurityStatusTest` coverage for the rendered Security tab guidance.
- Verified initial local checks: PHP syntax passes for touched PHP files, focused `AuthServiceTest` passes with 11 tests and 31 assertions, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 67 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 687 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 197 tests and 974 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.34-branch-qa-20260705-165919\alynt-account-gateway-v0.1.34-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.33` metadata, auth rate-limit logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.33` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.33`, auth rate-limit logging code and Security tab guidance code are present. Runtime smoke triggered unique login and password-reset limiter blocks, confirmed `login_rate_limited` and `lostpassword_rate_limited` rows were written to the verification log table, and authenticated HTTP smoke confirmed the Security tab returns both statuses and both guidance messages with no fatal/critical error output. Temporary QA rows and upload artifacts were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.34` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 687 strings and `0.1.34` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 197 tests and 974 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, PHP syntax passes, and `git diff --check` passes with only line-ending warnings for generated/package metadata files.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.34-20260705-170558\alynt-account-gateway-v0.1.34.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.34` header/constant/readme/POT metadata, auth rate-limit logging PHP, Security tab guidance PHP, built admin CSS, and POT present.
- Installed the local `0.1.34` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.34`, auth rate-limit logging code and Security tab guidance code are present. Runtime smoke triggered unique login and password-reset limiter blocks, confirmed `login_rate_limited` and `lostpassword_rate_limited` rows were written to the verification log table, and authenticated HTTP smoke confirmed the Security tab returns both statuses and both guidance messages with no fatal/critical error output. Temporary QA rows and staged upload ZIP were cleaned up after QA.
- Published GitHub release `v0.1.34`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs files, `0.1.34` header/constant/readme/POT metadata, auth rate-limit logging PHP, Security tab guidance PHP, built admin CSS, and POT present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.33` to `0.1.34`, then installed it through the WordPress plugin update path using the updater-discovered GitHub release ZIP URL. Final fresh runtime state: active `0.1.34` header/constant, auth rate-limit logging PHP and Security tab guidance PHP present, Alynt Plugin Updater reports current/new `0.1.34` with no update available, and authenticated HTTP smoke confirmed the Security tab returns `login_rate_limited`, `lostpassword_rate_limited`, both guidance messages, and no fatal/critical error output. Temporary QA rows and staged upload ZIP were cleaned up after QA.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication success/failure behavior, password reset email behavior, registration flow, provider verification behavior, rate-limit enforcement thresholds, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on auth-side rate-limit evidence and read-only admin activity guidance only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders auth rate-limit activity guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.33 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add admin-readable guidance to Recent Registration Verification Activity rows so raw provider/status codes explain what happened.
- [x] Cover passed, flagged, blocked, Turnstile failure, and rate-limit outcomes without changing registration behavior or stored verification data.
- [x] Keep changes scoped to admin visibility with no schema, settings, registration flow, provider verification, rate-limit enforcement, email delivery, webhook, frontend, dashboard, WooCommerce, privacy retention, or default frontend-output behavior changes.
- [x] Add focused coverage for rendered provider guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.33` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.33` from clean `master` after the `v0.1.32` release merge.
- Added a Guidance column to the Security tab Recent Registration Verification Activity table. The guidance explains accepted Reoon emails, flagged Reoon statuses, Reoon policy blocks, Turnstile failures, and registration/confirmation resend rate-limit blocks.
- Added focused `SettingsPageSecurityStatusTest` coverage for passed, flagged, blocked, Turnstile failed, and rate-limited guidance output.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 61 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 685 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 197 tests and 960 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.33-branch-qa-20260705-162731\alynt-account-gateway-v0.1.33-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.32` metadata, verification guidance PHP, built admin CSS, and POT present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.32` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.32`, the verification guidance code and built admin CSS are present. Seeded five temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Guidance column, Reoon accepted/flagged/blocked explanations, Turnstile failed explanation, rate-limit explanation, masked email rows, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.33` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 685 strings and `0.1.33` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 197 tests and 960 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, PHP syntax passes, and `git diff --check` passes with only line-ending warnings for generated/package metadata files.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.33-20260705-163414\alynt-account-gateway-v0.1.33.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.33` header/constant/readme/POT metadata, verification guidance PHP, built admin CSS, and POT present.
- Installed the local `0.1.33` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.33`, the verification guidance code and built admin CSS are present. Seeded five temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Guidance column, Reoon accepted/flagged/blocked explanations, Turnstile failed explanation, rate-limit explanation, masked email rows, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Published GitHub release `v0.1.33`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs files, `0.1.33` header/constant/readme/POT metadata, verification guidance PHP, built admin CSS, and POT present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.32` to `0.1.33`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.33` header/constant, verification guidance PHP and built admin CSS present, no remaining update offer, Alynt Plugin Updater reports current/new `0.1.33`, and authenticated HTTP smoke confirmed the Security tab returns the Guidance column, Reoon accepted/flagged/blocked explanations, Turnstile failed explanation, rate-limit explanation, masked email rows, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, provider verification behavior, rate-limit enforcement, pending-registration persistence behavior, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only provider/status guidance in the Security tab only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders verification guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.32 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a read-only Security tab panel for recent pending registration records using the existing plugin-owned pending registration table.
- [x] Mask email addresses in the admin panel and show compact status labels for pending, email-confirmed, completed, and expired records.
- [x] Keep changes scoped to admin visibility with no schema, registration flow, provider verification, rate-limit enforcement, email delivery, webhook, frontend, dashboard, WooCommerce, privacy retention, or default frontend-output behavior changes.
- [x] Add focused coverage for empty and populated pending-registration output.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.32` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.32` from clean `master` after the `v0.1.31` release merge.
- Added a Recent Pending Registrations table to the Security tab after the verification activity table. The table reads existing pending registration records, masks email addresses, and shows status, user id, created, confirmed, and expiry fields.
- Added derived Expired status output for pending or email-confirmed rows whose expiry timestamp has passed, without mutating stored registration records.
- Added focused `SettingsPageSecurityStatusTest` coverage for empty pending-registration output plus masked pending, email-confirmed, completed, and expired rows.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 49 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 667 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 197 tests and 948 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.32-branch-qa-20260705-155937\alynt-account-gateway-v0.1.32-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.31` metadata, pending-registration panel PHP, built admin CSS, and POT present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.31` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.31`, the pending-registration panel code and built admin CSS are present. Seeded two temporary pending-registration rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Pending Registrations table, masked pending and expired email rows, Pending and Expired statuses, existing verification activity, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.32` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 667 strings and `0.1.32` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 197 tests and 948 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, PHP syntax passes, and `git diff --check` passes with only line-ending warnings for generated/package metadata files.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.32-20260705-160828\alynt-account-gateway-v0.1.32.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.32` header/constant/readme/POT metadata, pending-registration panel PHP, built admin CSS, and POT present.
- Installed the local `0.1.32` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.32`, the pending-registration panel code and built admin CSS are present. Seeded two temporary pending-registration rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Pending Registrations table, masked pending and expired email rows, Pending and Expired statuses, existing verification activity, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Published GitHub release `v0.1.32`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs files, `0.1.32` header/constant/readme/POT metadata, pending-registration panel PHP, built admin CSS, and POT present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.31` to `0.1.32`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.32` header/constant, pending-registration panel PHP and built admin CSS present, no remaining update offer, Alynt Plugin Updater reports current/new `0.1.32`, and authenticated HTTP smoke confirmed the Security tab returns the Recent Pending Registrations table, masked pending/expired email rows, Pending and Expired statuses, existing verification activity, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, pending-registration persistence behavior, provider verification behavior, rate-limit enforcement, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only pending-registration visibility in the Security tab only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders pending-registration visibility.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.31 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Log registration provider outcomes into the existing verification log table.
- [x] Log registration and confirmation-resend rate-limit blocks into the existing verification log table.
- [x] Add a read-only Security tab activity table for recent provider/rate-limit outcomes with masked email addresses.
- [x] Keep changes scoped to security evidence and admin visibility with no schema, retention, privacy exporter/eraser, registration flow, provider verification, rate-limit enforcement, frontend, dashboard, WooCommerce, webhook, or email delivery behavior changes.
- [x] Add focused coverage for passed, blocked, flagged, and rate-limited activity plus the rendered Security tab activity table.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.31` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.31` from clean `master` after the `v0.1.30` release merge.
- Reused the existing plugin-owned `verification_logs` table instead of adding a schema migration.
- Added registration verification logging for Turnstile and Reoon provider outcomes. Successful checks store compact statuses such as `passed`, `safe`, or `role_account_flagged`; provider errors store their sanitized error code and mark the row blocked.
- Added blocked registration and confirmation-resend throttles to the same activity stream with `rate_limit` as the provider and bucket-specific statuses.
- Added a Recent Registration Verification Activity table to the Security tab. The table masks email addresses, labels providers, shows outcome codes, and distinguishes Passed from Blocked decisions.
- Added focused `RegistrationServiceTest` coverage for logged safe, blocked, flagged, and rate-limited outcomes, plus `SettingsPageSecurityStatusTest` coverage for empty and populated activity output.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 657 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 195 tests and 933 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.31-branch-qa-20260705-151743\alynt-account-gateway-v0.1.31-branch-qa.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, pre-bump `0.1.30` metadata, registration verification logging PHP, activity panel PHP, and built admin activity CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.30` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.30`, registration verification logging PHP, activity panel PHP, and built admin activity CSS are present. Seeded two temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Registration Verification Activity table, masked email rows, Reoon Email Verifier and Rate Limit labels, `safe` and `registration_rate_limited` outcomes, Passed and Blocked decisions, admin CSS, and no fatal/critical error output. Temporary package, seeded QA rows, and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.31` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 657 strings and `0.1.31` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 195 tests and 933 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.31-20260705-152211\alynt-account-gateway-v0.1.31.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, `0.1.31` header/constant/readme/POT metadata, registration verification logging PHP, activity panel PHP, and built admin activity CSS present.
- Installed the local `0.1.31` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.31`, registration verification logging PHP, activity panel PHP, and built admin activity CSS are present. Seeded two temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Registration Verification Activity table, masked email rows, Reoon Email Verifier and Rate Limit labels, `safe` and `registration_rate_limited` outcomes, Passed and Blocked decisions, admin CSS, and no fatal/critical error output. Temporary package, seeded QA rows, and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.31`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.31` header/constant/readme metadata, registration verification logging PHP, activity panel PHP, and built admin activity CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.30` to `0.1.31`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.31` header/constant, registration verification logging PHP, activity panel PHP, built admin activity CSS present, no remaining update offer, and authenticated HTTP smoke confirmed the Security tab returns the Recent Registration Verification Activity table, masked email rows, Reoon Email Verifier and Rate Limit labels, `safe` and `registration_rate_limited` outcomes, Passed and Blocked decisions, admin CSS, and no fatal/critical error output.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, provider verification behavior, rate-limit enforcement, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on registration security evidence, rate-limit visibility, and read-only admin activity output only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders recent verification activity and rate-limit evidence.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.30 Small Release Cycle

### Scope

- [x] Start the security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a read-only Security tab status panel for provider readiness, Reoon policy visibility, and rate-limit posture.
- [x] Keep changes admin-only with no registration flow, provider verification, rate-limit enforcement, settings schema, frontend, dashboard, WooCommerce, webhook, privacy, or email behavior changes.
- [x] Add focused coverage for missing-provider guidance, configured-provider guidance, Reoon default policy wording, and configured rate-limit values.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.30` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.30` from clean `master` after the `v0.1.29` release merge.
- Added a Security And Spam Status panel on the Security tab after the settings form so saved provider fields remain the primary editing surface.
- Added provider readiness cards for protection mode, Turnstile, Reoon Email Verifier, and the default Reoon policy. The policy message documents that invalid, disabled, disposable, and spamtrap statuses are blocked while catch-all, role account, unknown, and inbox-full statuses are allowed but flagged.
- Added rate-limit posture cards for registration, confirmation resend, login, and password reset windows.
- Added focused `SettingsPageSecurityStatusTest` coverage for missing providers, fully configured providers, Reoon policy wording, and configured rate-limit values.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 649 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 192 tests and 901 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.30-branch-qa-20260705-143958\alynt-account-gateway-v0.1.30-branch-qa.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, pre-bump `0.1.29` metadata, security status PHP, Reoon policy PHP, and built admin security CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.29` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.29`, security status PHP and built admin CSS are present. Authenticated HTTP smoke confirmed the Security tab returns the Security And Spam Status panel, provider readiness, protection mode, Turnstile, Reoon Email Verifier, Reoon Default Policy, registration/password-reset rate-limit cards, admin CSS, and no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.30` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 649 strings and `0.1.30` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 192 tests and 901 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.30-20260705-144650\alynt-account-gateway-v0.1.30.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, `0.1.30` header/constant/readme/POT metadata, security status PHP, Reoon policy PHP, and built admin security CSS present.
- Installed the local `0.1.30` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.30`, security status PHP and built admin CSS are present. Authenticated HTTP smoke confirmed the Security tab returns the Security And Spam Status panel, provider readiness, protection mode, Turnstile, Reoon Email Verifier, Reoon Default Policy, registration/password-reset rate-limit cards, admin CSS, and no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.30`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.30` header/constant/readme metadata, security status PHP, Reoon policy PHP, and built admin security CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.29` to `0.1.30`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.30` header/constant, security status PHP and built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed the Security tab returns the Security And Spam Status panel, provider readiness, protection mode, Turnstile, Reoon Email Verifier, Reoon Default Policy, registration/password-reset rate-limit cards, admin CSS, and no fatal/critical error output.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, provider verification behavior, rate-limit enforcement, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only Security tab status guidance and styling only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders provider readiness, Reoon policy visibility, and rate-limit posture.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.29 Small Release Cycle

### Scope

- [x] Start the email template editor polish slice from the released `master` baseline.
- [x] Add richer token browsing for all email template tokens.
- [x] Add per-template guidance for purpose, action tokens, and disabled-email caveats.
- [x] Improve preview/test-send ergonomics with clearer descriptions and accessible form help.
- [x] Keep changes admin-only/read-only with no email delivery behavior, template storage, frontend, registration, provider, dashboard, WooCommerce, webhook, or privacy behavior changes.
- [x] Add focused coverage for token reference metadata and rendered email tools.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.29` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.29` from clean `master` after the `v0.1.28` release merge.
- Added reusable `ALYNT_AG_Email_Template_Service::token_reference()` metadata for every preview token.
- Expanded the Emails tab tools with a Template Reference panel, Available Template Tokens panel, sample token values, plain-text/core-email caveat copy, and clearer preview/test-send descriptions.
- Added `aria-describedby` help for preview template selection, test template selection, and the test recipient input.
- Added focused `EmailTemplateServiceTest` coverage for token reference metadata and `SettingsPageEmailToolsTest` coverage for template action-token guidance and rendered email tools.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 626 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 189 tests and 879 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Confirmed the built admin CSS contains the new email-tool styling.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.29-branch-qa-20260705-135904\alynt-account-gateway-v0.1.29-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.28` metadata, token reference PHP, template reference PHP, email tools markup, and built admin email-tool CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.28` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.28`, token reference PHP, template reference PHP, email tools markup, and built admin CSS are present. Authenticated HTTP smoke confirmed the Emails tab returns `200`, loads the admin CSS asset, renders the Template Reference and Available Template Tokens panels, shows action-token examples, includes linked preview/test-send `aria-describedby` help, and shows no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.29` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 626 strings and `0.1.29` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 189 tests and 879 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.29-20260705-140305\alynt-account-gateway-v0.1.29.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.29` header/constant/readme/POT metadata, token reference PHP, template reference PHP, email tools markup, and built admin email-tool CSS present.
- Installed the local `0.1.29` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.29`, token reference PHP, template reference PHP, email tools markup, and built admin CSS are present. Authenticated HTTP smoke confirmed the Emails tab returns `200`, loads the admin CSS asset, renders the Template Reference and Available Template Tokens panels, shows action-token examples, includes linked preview/test-send `aria-describedby` help, and shows no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.29`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.29` header/constant/readme metadata, token reference PHP, template reference PHP, email tools markup, and built admin email-tool CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.28` to `0.1.29`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.29` header/constant, token reference PHP, template reference PHP, email tools markup, built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed the Emails tab returns `200`, loads the admin CSS asset, renders the Template Reference and Available Template Tokens panels, shows action-token examples, includes linked preview/test-send `aria-describedby` help, and shows no fatal/critical error output.

### Guardrails

- Do not change saved settings schema, default email copy, token replacement behavior, email delivery behavior, email disable toggles, frontend routing, registration flow, provider verification behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin email editor guidance, token browsing, and preview/test-send ergonomics only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Emails tab renders template reference, token reference, and accessible preview/test-send help.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.28 Small Release Cycle

### Scope

- [x] Start the next settings UX refinement slice from the released `master` baseline.
- [x] Add field-level help text for high-impact Account Gateway settings.
- [x] Add `aria-describedby` linkage for native settings inputs that have help text.
- [x] Keep help advisory/read-only with no settings storage, routing, provider, email, dashboard, WooCommerce, webhook, privacy, or frontend behavior changes.
- [x] Add focused coverage for the help map, rendered help output, native input `aria-describedby`, and no-op missing help.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.28` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.28` from clean `master` after the `v0.1.27` release merge.
- Added reusable field-level help text under high-impact settings across General, URLs, Branding, Registration, Security, Emails, Dashboard, WooCommerce, Webhooks, Privacy, and Advanced / Tools.
- Added `aria-describedby` attributes for native input, textarea, select, checkbox, email, number, color, secret, and text controls when field help is available.
- Added focused `SettingsPageFieldHelpTest` coverage for high-impact help text, rendered text-field help, rendered boolean-field help, and missing-setting no-op output.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 592 strings, `npm.cmd run lint` passes after PHPCBF alignment cleanup, full `npm.cmd test` passes with 186 tests and 832 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Confirmed the built admin CSS contains the field-help styling.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.28-branch-qa-20260705-133906\alynt-account-gateway-v0.1.28-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.27` metadata, field-help PHP helpers, `aria-describedby` support, and built admin field-help CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.27` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.27`, field-help PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, URLs, Registration, and WooCommerce settings tabs return `200`, load the admin CSS asset, render expected helper text, and include linked `aria-describedby` attributes. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.28` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 592 strings and `0.1.28` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 186 tests and 832 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.28-20260705-134350\alynt-account-gateway-v0.1.28.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.28` header/constant/readme/POT metadata, field-help PHP helpers, `aria-describedby` support, and built admin field-help CSS present.
- Installed the local `0.1.28` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.28`, field-help PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, URLs, Registration, and WooCommerce settings tabs return `200`, load the admin CSS asset, show expected helper text, include linked `aria-describedby` attributes, and show no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.28`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.28` header/constant/readme metadata, field-help PHP helpers, `aria-describedby` support, and built admin field-help CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.27` to `0.1.28`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.28` header/constant, field-help PHP helpers and built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed General, URLs, Registration, and WooCommerce settings tabs return `200`, load the admin CSS asset, show expected helper text, include linked `aria-describedby` attributes, and show no fatal/critical error output.

### Guardrails

- Do not change frontend routing, authentication behavior, registration flow, provider verification behavior, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only field-level admin help text and styling only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative settings fields render help text and linked descriptions.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.27 Small Release Cycle

### Scope

- [x] Start the next settings UX refinement slice from the released `master` baseline.
- [x] Add read-only tab-level guidance panels across all settings tabs.
- [x] Keep guidance advisory only with no settings storage, routing, provider, email, dashboard, WooCommerce, webhook, privacy, or frontend behavior changes.
- [x] Add focused coverage for complete tab guidance, registration-to-security handoff, and invalid-tab fallback.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.27` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.27` from clean `master` after the `v0.1.26` release merge.
- Added a read-only settings guidance panel beneath the tab navigation. Each tab now shows a concise focus statement, three setup prompts, and an optional related-tab action for the next natural configuration area.
- Added focused `SettingsPageTabGuidanceTest` coverage for one guidance entry per registered settings tab, Registration tab guidance linking to Security, and invalid tab fallback to General guidance.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 544 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 182 tests and 820 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Confirmed the built admin CSS contains the tab guidance styles and mobile single-column fallback.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.27-branch-qa-20260705-131253\alynt-account-gateway-v0.1.27-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.26` metadata, tab guidance PHP, and built admin guidance CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.26` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.26`, tab guidance PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, Registration, WooCommerce, and Advanced / Tools tabs return `200`, render the guidance panel, load the admin CSS asset, show expected guidance copy, and show no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.27` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 544 strings and `0.1.27` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 182 tests and 820 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.27-20260705-131836\alynt-account-gateway-v0.1.27.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.27` header/constant/readme/POT metadata, tab guidance PHP, and built admin guidance CSS present.
- Installed the local `0.1.27` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.27`, tab guidance PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, Registration, WooCommerce, and Advanced / Tools tabs return `200`, render the guidance panel, load the admin CSS asset, show expected guidance copy, and show no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.27`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.27` header/constant/readme metadata, tab guidance PHP, and built admin guidance CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.26` to `0.1.27`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.27` header/constant, tab guidance PHP and built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed General, Registration, WooCommerce, and Advanced / Tools tabs return `200`, render the guidance panel, load the admin CSS asset, show expected guidance copy, and show no fatal/critical error output.

### Guardrails

- Do not change frontend routing, authentication behavior, registration flow, provider verification behavior, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only admin setup guidance and styling only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative settings tabs render the new guidance panel.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.26 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add scoped frontend CSS for delegated WooCommerce notices, forms, fieldsets, required labels, buttons, and payment-method containers inside branded dashboard content.
- [x] Keep changes presentation-only and preserve WooCommerce endpoint handlers, forms, submissions, and sensitive account flows.
- [x] Add focused CSS source coverage for key scoped WooCommerce selectors and mobile single-column fallback.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.26` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.26` from clean `master` after the `v0.1.25` release merge.
- Added dashboard-scoped CSS polish for WooCommerce notices, validation/error boxes, address/account form rows, fieldsets, required markers, submit buttons, and payment-method containers so delegated WooCommerce screens better match the branded dashboard shell without replacing WooCommerce logic.
- Added focused source-level CSS coverage to keep the WooCommerce selectors scoped to `.agw-dashboard-content` and preserve the mobile single-column fallback for address/account form grids.
- Verified initial local checks: PHP syntax passes for the new CSS source test, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 474 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 179 tests and 764 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.26-branch-qa-20260705-123438\alynt-account-gateway-v0.1.26-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, and no dev/source/test/vendor files.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.25` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.25`, built frontend CSS is present, and delegated WooCommerce CSS selectors for notices, account forms, payment methods, and mobile fallback are present.
- Authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/edit-address/`, `/my-account/edit-account/`, and `/my-account/payment-methods/` return `200`, render the branded dashboard and delegated content shell, load the frontend CSS asset, match expected endpoint copy, and show no fatal/critical error output. Temporary upload artifacts and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.26` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 474 strings and `0.1.26` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 179 tests and 764 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.26-20260705-124007\alynt-account-gateway-v0.1.26.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.26` header/constant/readme/POT metadata, and built delegated WooCommerce CSS present.
- Installed the local `0.1.26` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.26`, built delegated WooCommerce CSS selectors are present, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/edit-address/`, `/my-account/edit-account/`, and `/my-account/payment-methods/` return `200`, render the branded dashboard and delegated content shell, load the frontend CSS asset, match expected endpoint copy, and show no fatal/critical error output. Temporary upload artifacts and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.26`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.26` header/constant/readme metadata, and built delegated WooCommerce CSS selectors present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.25` to `0.1.26`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.26` header/constant, delegated WooCommerce CSS selectors present, no remaining update offer, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/edit-address/`, `/my-account/edit-account/`, and `/my-account/payment-methods/` return `200`, render the branded dashboard and delegated content shell, load the frontend CSS asset, match expected endpoint copy, and show no fatal/critical error output.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, WooCommerce form handlers, WooCommerce account data submission, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on scoped delegated-content presentation only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative delegated WooCommerce account endpoints.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.25 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add branded next-step panels for standard WooCommerce account endpoint edge states while preserving delegated WooCommerce endpoint content.
- [x] Keep custom/plugin-added WooCommerce endpoints free of plugin-authored affordance assumptions.
- [x] Add focused coverage for orders, downloads, payment methods, and custom endpoint restraint.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.25` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.25` from clean `master` after the `v0.1.24` release merge.
- Added contextual affordance panels above delegated WooCommerce endpoint content for orders, downloads, addresses, account details, and payment-methods pages. The panels point customers toward safe account next steps without taking over WooCommerce forms, tables, or endpoint handlers.
- Added focused dashboard screen coverage for Orders edge-state affordance, Downloads edge-state affordance, Payment Methods add-method affordance, and skipping affordances for plugin-added/custom endpoints.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `FrontendDashboardScreenTest` passes with 8 tests and 51 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 474 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 177 tests and 748 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.25-branch-qa-20260705-115634\alynt-account-gateway-v0.1.25-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.24` metadata, affordance renderer, and built affordance CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.24` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.24`, the affordance renderer and built affordance CSS are present, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/downloads/`, and `/my-account/payment-methods/` return `200` with the expected affordance panel text and no fatal output.
- Bumped release-candidate metadata to `0.1.25` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 474 strings and `0.1.25` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 177 tests and 748 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.25-20260705-120515\alynt-account-gateway-v0.1.25.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.25` header/constant/readme/POT metadata, affordance renderer, and built affordance CSS present.
- Installed the local `0.1.25` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.25`, affordance PHP and built affordance CSS are present, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/downloads/`, and `/my-account/payment-methods/` return `200` with the expected affordance panel text and no fatal output.
- Published GitHub release `v0.1.25`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.25` header/constant/readme metadata, affordance renderer, and built affordance CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.24` to `0.1.25`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.25` header/constant, affordance PHP and built affordance CSS present, checked plugin version `0.1.25`, no remaining update offer, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/downloads/`, and `/my-account/payment-methods/` return `200` with the expected affordance panel text and no fatal output.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, WooCommerce menu/link generation, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on endpoint edge-state help and presentation only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates endpoint affordance panels on representative WooCommerce endpoints.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.24 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add branded guidance copy above delegated WooCommerce endpoint content for standard account areas.
- [x] Keep custom/plugin-added WooCommerce endpoints free of plugin-authored guidance assumptions.
- [x] Add focused coverage for endpoint guidance and custom endpoint restraint.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.24` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.24` from clean `master` after the `v0.1.23` release merge.
- Added endpoint-specific guidance for WooCommerce orders, order details, downloads, addresses, account details, and payment-method flows while preserving WooCommerce endpoint delegation.
- Added focused dashboard screen coverage for Orders guidance, Payment Methods guidance, and skipping guidance for plugin-added/custom endpoints.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `FrontendDashboardScreenTest` passes with 7 tests and 37 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 460 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 176 tests and 734 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.24-branch-qa-20260705-112041\alynt-account-gateway-v0.1.24-branch-qa.zip`; verified the main plugin file and built frontend assets are included, and dev/source/test/vendor files are excluded.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.23` by replacing the local-only plugin files from the verified package after WP-CLI was unavailable. Verified the active plugin remains pre-bump `0.1.23`, installed files include endpoint guidance PHP and built CSS, and Plugin Tester settings already have frontend output, dashboard, and WooCommerce takeover enabled.
- Smoked Plugin Tester through WordPress rendering and authenticated HTTP checks: `/my-account/orders/` and `/my-account/payment-methods/` returned `200` and rendered the expected guidance text; direct renderer checks confirmed Orders guidance, Payment Methods guidance, delegated content shells, and no plugin-authored guidance for a custom endpoint.
- Bumped release-candidate metadata to `0.1.24` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 460 strings and `0.1.24` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 176 tests and 734 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.24-20260705-113710\alynt-account-gateway-v0.1.24.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.24` header/constant/readme/POT metadata, endpoint guidance renderer, and built frontend assets present.
- Installed the local `0.1.24` package on LocalWP Plugin Tester through WordPress upgrader classes after WP-CLI was unavailable. Fresh runtime verification confirmed active header and loaded constant are `0.1.24`, endpoint guidance PHP and built guidance CSS are present, and authenticated HTTP smoke confirmed `/my-account/orders/` and `/my-account/payment-methods/` return `200` with the expected guidance text.
- Published GitHub release `v0.1.24`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.24` header/constant/readme metadata, endpoint guidance renderer, and built guidance CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.23` to `0.1.24`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.24` header/constant, endpoint guidance PHP and built guidance CSS present, checked plugin version `0.1.24`, no remaining update offer, and authenticated HTTP smoke confirmed `/my-account/orders/` and `/my-account/payment-methods/` return `200` with the expected guidance text.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, WooCommerce menu/link generation, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on endpoint affordance copy and presentation only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates endpoint guidance on representative WooCommerce endpoints.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.23 Small Release Cycle

### Scope

- [x] Start the WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add a branded WooCommerce customer overview on the base dashboard when takeover is enabled and WooCommerce is available.
- [x] Keep WooCommerce endpoint pages delegated to WooCommerce handlers.
- [x] Add focused coverage for overview rendering and configured endpoint URLs.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.23` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.23` from clean `master` after the `v0.1.22` release merge.
- Added a WooCommerce-only dashboard overview on the base account page with branded customer-account copy and quick links for orders, addresses, and account details.
- Added a public WooCommerce endpoint URL helper so dashboard overview links follow the configured account base path.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused dashboard and WooCommerce tests pass, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 448 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 174 tests and 724 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.23-branch-qa-20260705-105241\alynt-account-gateway-v0.1.23-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.22` metadata, dashboard overview renderer, WooCommerce endpoint URL helper, built frontend overview CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.22` through the WordPress upgrader path. Verified active header and loaded constant remain pre-bump `0.1.22`, installed files include the dashboard overview renderer and endpoint URL helper, and built frontend CSS contains overview styles.
- Authenticated-smoked Plugin Tester with a temporary Novamira admin access session and curl cookie jar after the Playwright MCP browser backend closed: `/my-account/` rendered the branded dashboard overview, customer copy, orders/addresses/account quick links, and no endpoint content shell; `/my-account/orders/` rendered the branded dashboard shell, no overview, the delegated endpoint content shell, and the Orders content title. Restored the previous Plugin Tester settings and cleaned temporary upload artifacts after QA.
- Bumped release-candidate metadata to `0.1.23` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 448 strings and `0.1.23` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 174 tests and 724 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.23-20260705-110650\alynt-account-gateway-v0.1.23.zip`; verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.23` header/constant/readme/POT metadata, dashboard overview renderer, endpoint URL helper, built frontend overview CSS, and POT strings present.
- Installed the local `0.1.23` package on LocalWP Plugin Tester through the WordPress upgrader path. Fresh runtime verification confirmed active header and loaded constant are `0.1.23`, dashboard overview renderer and built overview CSS are present, and temporary upload artifacts were cleaned up.
- Published GitHub release `v0.1.23`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.23` header/constant/readme metadata, dashboard overview renderer, and built frontend overview CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.22` to `0.1.23`, then installed it through the WordPress upgrader path. Final fresh runtime state: active `0.1.23` header/constant, dashboard overview renderer and built overview CSS present, checked plugin version `0.1.23`, no remaining update offer, and temporary upload artifacts cleaned up.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on customer-facing dashboard polish and small, testable WooCommerce affordances.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the branded WooCommerce dashboard overview and representative endpoint delegation.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.22 Small Release Cycle

### Scope

- [x] Start the next settings UX slice from the released `master` baseline.
- [x] Add a read-only setup readiness panel on the General tab.
- [x] Summarize critical setup checks before frontend output is enabled.
- [x] Surface warnings for public registration without Turnstile/Reoon, missing Terms/Privacy paths, missing email test recipient, dashboard/WooCommerce takeover gaps, and webhook signing gaps where applicable.
- [x] Add focused coverage for readiness check classification and panel output.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.22` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.22` from clean `master` after the `v0.1.21` release merge.
- Added a read-only General tab Setup Readiness panel with action/review/ready counts and tab links for frontend output, gateway URLs, emergency access, branding, public registration, email testing, dashboard, WooCommerce takeover, webhook signing, and privacy retention checks.
- Added focused `SettingsPageReadinessTest` coverage for safe default classification, public registration without provider warning, WooCommerce takeover dependency, and rendered panel summary/link output.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` writes 439 strings, `npm.cmd run lint`, `npm.cmd test` passes with 172 tests and 715 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.22-branch-qa-20260705-103309\alynt-account-gateway-v0.1.22-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.21` metadata, readiness panel code, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.21` through the WordPress upgrader path. Verified active header and loaded constant remain pre-bump `0.1.21`, installed admin file includes readiness panel/check code, and built admin CSS contains readiness styles.
- Browser-smoked Plugin Tester General tab through temporary Novamira admin access using Playwright with the system Edge channel: one setup readiness panel, ten check rows, action/review/ready summary text, key check labels, and `Open Setting` links rendered correctly.
- Bumped release-candidate metadata to `0.1.22` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 439 strings and `0.1.22` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 172 tests and 715 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.22-20260705-103755\alynt-account-gateway-v0.1.22.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, `0.1.22` header/constant/readme/POT metadata, readiness panel code, and built readiness CSS present.
- Installed the local `0.1.22` package on LocalWP Plugin Tester through the WordPress upgrader path. Fresh runtime verification confirmed active header and loaded constant are `0.1.22`, readiness panel code and built readiness CSS are present, and temporary upload artifacts were cleaned up.
- Published GitHub release `v0.1.22`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.22` header/constant/readme metadata, readiness panel code, and built readiness CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.21` to `0.1.22`, then installed it through the WordPress upgrader path. Final fresh runtime state: active `0.1.22` header/constant, readiness panel code and built readiness CSS present, checked plugin version `0.1.22`, no remaining update offer, and temporary upload artifacts cleaned up.

### Guardrails

- Do not change frontend routing, authentication behavior, registration flow, email delivery behavior, provider verification behavior, WooCommerce endpoint delegation, webhook dispatch behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep readiness checks advisory/read-only and admin-only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the General tab readiness panel.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.21 Small Release Cycle

### Scope

- [x] Start the next admin UX slice from the released `master` baseline.
- [x] Add a Webhooks tab delivery summary based on the most recent webhook log row.
- [x] Add signature verification guidance that reflects whether webhook signing is configured.
- [x] Add expandable delivery metadata for recent webhook log rows without changing dispatch behavior or log retention.
- [x] Add focused coverage where practical.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.21` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.21` from clean `master` after the `v0.1.20` release merge.
- Added Webhooks tab delivery summary, signing status, signature verification reference, and expandable per-row delivery details without changing webhook dispatch behavior.
- Added focused admin webhook UX coverage for summary copy, signed/unsigned guidance, expanded row metadata, and invalid timestamp fallback. Verified targeted `SettingsPageWebhookUxTest` passed with 4 tests and 16 assertions.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` writes 398 strings, `npm.cmd run lint`, `npm.cmd test` passes with 168 tests and 696 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.21-branch-qa-20260705-100422\alynt-account-gateway-v0.1.21-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.20` metadata, and webhook delivery UX strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.20` through the WordPress upgrader path. Verified active header and loaded constant remain pre-bump `0.1.20`, installed admin file includes delivery summary/signature reference/details strings, and temporary settings/uploads were cleaned up.
- Browser-smoked Plugin Tester Webhooks tab through temporary Novamira admin access using Playwright with the system Edge channel: `Webhook Tools`, `Delivery Status:`, signing-enabled guidance, `Signature Verification Reference`, `Recent Webhook Deliveries`, `Details`, `View`, event text, and destination text rendered correctly.
- Bumped release-candidate metadata to `0.1.21` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 398 strings and `0.1.21` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 168 tests and 696 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.21-20260705-101155\alynt-account-gateway-v0.1.21.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, `0.1.21` header/constant/readme/POT metadata, and webhook delivery UX strings present.
- Installed the local `0.1.21` package on LocalWP Plugin Tester through the WordPress upgrader path. Fresh runtime verification confirmed active header and loaded constant are `0.1.21`, delivery summary/signature reference/details strings are present, and temporary upload artifacts were cleaned up.
- Published GitHub release `v0.1.21`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.21` header/constant/readme metadata, and webhook delivery UX strings present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.20` to `0.1.21`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.21` header/constant, delivery summary/signature reference/details strings present, and no remaining update offer.

### Guardrails

- Do not change webhook dispatch behavior, signing algorithm, payload shape, event names, URL policy, log retention, registration flow, email behavior, frontend routes, dashboard rendering, WooCommerce delegation, or provider verification behavior.
- Keep the slice admin-only and read-only except for the existing test webhook action.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Webhooks tab rendering and recent delivery metadata.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.20 Small Release Cycle

### Scope

- [x] Start the next integration-hardening slice from the released `master` baseline.
- [x] Add an optional webhook signing secret setting on the Webhooks tab.
- [x] Sign webhook request bodies with timestamped HMAC headers when a signing secret is configured.
- [x] Add focused coverage for unsigned and signed webhook dispatch behavior.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.20` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.20` from clean `master` after the `v0.1.19` release merge.
- Added an optional `webhook_signing_secret` setting on the Webhooks tab, defaulting to empty so existing integrations remain unsigned.
- Added signed webhook headers when a secret is configured: `X-Alynt-AG-Event`, `X-Alynt-AG-Time`, `X-Alynt-AG-Version`, and `X-Alynt-AG-Signature` using HMAC-SHA256 over `{timestamp}.{event}.{json_body}`.
- Added focused PHPUnit coverage for webhook signing defaults/sanitization, unsigned dispatch, and signed dispatch. Targeted `WebhookDispatcherTest|SettingsSchemaTest` passed with 27 tests and 131 assertions.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` writes 382 strings, `npm.cmd run lint`, `npm.cmd test` passes with 164 tests and 680 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.20-branch-qa-20260705-000212\alynt-account-gateway-v0.1.20-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.19` metadata, and signing setting/header code present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.19`; verified installed signing setting/method/header markers and safe intercepted signed test dispatch. The `account.created.test` signature matched the exact intercepted body, the log row recorded HTTP `202` success, and temporary settings/log artifacts were cleaned up.
- Bumped release-candidate metadata to `0.1.20` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 382 strings and `0.1.20` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 164 tests and 680 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.20-20260705-000519\alynt-account-gateway-v0.1.20.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, `0.1.20` header/constant/readme/POT metadata, and signing setting/header code present.
- Installed the local `0.1.20` package on LocalWP Plugin Tester; verified active header and loaded constant are `0.1.20`, signing setting/header markers are present, and safe intercepted signed test dispatch produced a matching signature and HTTP `202` success log row without external network calls.
- Published GitHub release `v0.1.20`, re-uploaded the inspected release asset to ensure `CHANGELOG.md` parity, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.20` header/constant/readme metadata, and signing setting/header code present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.19` to `0.1.20`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.20` header/constant, signing setting/header markers present, and no remaining update offer.

### Guardrails

- Do not change the existing account-created payload shape, event names, test-send behavior, webhook URL policy, logging retention, registration flow, email behavior, frontend routes, dashboard rendering, WooCommerce delegation, or provider verification behavior.
- Keep signing optional and disabled by default so existing webhook consumers continue working until a site owner configures a shared secret.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates signed test webhook dispatch and Webhooks tab rendering.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

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
