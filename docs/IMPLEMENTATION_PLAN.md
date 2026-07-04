# Alynt Account Gateway Implementation Plan

## Status

- Current phase: Plugin Tester browser QA completed; release-readiness hardening pending
- Target path: `C:\Development\WordPress\Plugins\alynt-account-gateway`
- Plugin status: Initial scaffold and observability foundation committed in Git checkpoint `c0daf48`
- Frontend output default: Disabled
- Distribution: Private Alynt-distributed plugin with GitHub updater compatibility

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
- [ ] Add import/export settings JSON.
- [ ] Add per-tab restore defaults.
- [ ] Add gateway screen preview mode while frontend output is disabled.
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
- [ ] Add compatibility warnings for plugins that also modify login, registration, account pages, security redirects, or WooCommerce account endpoints.

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
- [ ] Run full pre-release workflow sequence before release.

## Test Plan

- [ ] Unit test settings schema, defaults, sanitization, and cross-tab save protection.
- [ ] Unit test URL routing and frontend-output master switch.
- [ ] Unit test emergency bypass behavior.
- [ ] Unit test role access and admin-toolbar rules.
- [ ] Unit test email-only login behavior.
- [ ] Unit test pending-registration lifecycle and expiry.
- [ ] Unit test password policy and confirmation matching.
- [ ] Unit test username generation and collision handling.
- [ ] Unit test Reoon policy mapping.
- [ ] Unit test Turnstile verification handling.
- [x] Unit test webhook payload construction and metadata logging.
- [ ] Unit test retention cleanup.
- [ ] Unit test uninstall cleanup.
- [x] Browser/manual QA login, lost password, set password, registration, logout confirmation, disabled registration, and invalid/expired link screens.
- [x] Browser/manual QA desktop and mobile responsive behavior.
- [x] Browser/manual QA keyboard-only flow and focus management.
- [ ] Browser/manual QA email preview and test-send.
- [x] Browser/manual QA WooCommerce dashboard delegation.
- [x] Verify `npm run build`.
- [x] Verify `npm run lint`.
- [x] Verify `npm test`.
- [x] Verify `npm run make-pot`.
- [ ] Verify generated release zip through GitHub release workflow.

## Release Gates

- [ ] Frontend output remains disabled by default on fresh install.
- [ ] Emergency bypass opens native login only and never authenticates users.
- [x] No standard WordPress core account screen is exposed during normal enabled frontend use.
- [ ] Registration creates no WordPress user until email confirmation and password setup are complete.
- [x] WooCommerce account features remain usable when the custom dashboard is enabled.
- [ ] Accessibility acceptance criteria pass.
- [ ] Multilingual/i18n acceptance criteria pass.
- [ ] Privacy exporter/eraser and retention controls are present.
- [ ] Alynt Plugin Updater compatibility is verified with a release asset.

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
- Completed the initial scaffold, initialized Git, installed dependencies, and verified build/lint/test/audit.
- Added scaffold master prompt artifact for the initial plugin foundation.
- Created initial implementation plan from approved product-planning decisions.
