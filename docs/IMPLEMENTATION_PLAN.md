# Alynt Account Gateway Implementation Plan

## Status

- Current phase: v0.1.2 released from branch `release/0.1.2`; ready to merge to `master` and start the next small release cycle.
- Target path: `C:\Development\WordPress\Plugins\alynt-account-gateway`
- Plugin status: v0.1.2 is the current public baseline after GitHub release and Alynt Plugin Updater verification.
- Frontend output default: Disabled
- Distribution: Alynt-distributed plugin with GitHub updater compatibility

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
- [x] Add `.windsurfrules`.
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
- [ ] Evaluate a safe replacement strategy if the disable toggle must suppress the profile email-change request email itself.
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
- [ ] Browser/manual QA email preview and test-send.
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
- Fixed release hygiene issue where `.windsurfrules` would have been included in the GitHub release zip.
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
- Next toolkit step before scaffold: use `d1-setup/windsurf-wp-config.md` Section 2 to create the scaffold master prompt.
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
