# Alynt Account Gateway Implementation Plan

## Status

- Current phase: Observability complete / ready for account routing feature work
- Target path: `C:\Development\WordPress\Plugins\alynt-account-gateway`
- Plugin status: Initial scaffold exists and Git repository is initialized
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

- [ ] Implement frontend-output master switch.
- [ ] Route `/login` to the branded login screen.
- [ ] Route `/account?action=lostpassword`, `/account?action=register`, `/account?action=rp`, and `/account?action=logout` to branded screens.
- [ ] Replace public `wp-login.php` links with branded routes when frontend output is enabled.
- [ ] Add emergency bypass URL that opens native `wp-login.php` without authenticating or bypassing 2FA/security plugins.
- [ ] Block `wp-admin` for roles other than administrators and shop managers.
- [ ] Remove admin toolbar for roles other than administrators and shop managers.
- [ ] Build responsive split-screen gateway template with one global background image.
- [ ] Add logo upload and max-width control.
- [ ] Add color controls for primary color, accent color, button background, and button text.
- [ ] Add per-screen instruction/welcome text.
- [ ] Add disabled-registration and invalid/expired-link branded states.

### Phase 4 - Registration, Passwords, And Spam Protection

- [ ] Implement pending registration records.
- [ ] Send registration confirmation email before creating a WordPress user.
- [ ] Create WordPress user only after confirmation link and valid password setup.
- [ ] Store first name, last name, email, generated username, and chosen password during final account creation only.
- [ ] Add password strength meter and matching validation.
- [ ] Enforce minimum 12 characters, uppercase, lowercase, number, and special symbol.
- [ ] Add terms/privacy agreement checkbox with relative URL path links.
- [ ] Add Turnstile client and required server-side validation when enabled.
- [ ] Add Reoon Email Verifier client and policy mapping.
- [ ] Default Reoon policy: block invalid, disabled, disposable, and spamtrap; allow but flag catch-all, role-account, unknown, and inbox-full.
- [ ] Add rate limits for registration, resend confirmation, login, and password reset flows.
- [ ] Avoid account enumeration in login and password-reset messages.

### Phase 5 - Emails And Webhooks

- [ ] Add rich email template editor.
- [ ] Add template preview and test-send.
- [ ] Add branded HTML wrapper, logo, colors, buttons, and plain-text fallback.
- [ ] Add templates for password reset, password changed, registration confirmation/welcome, and email-change confirmation.
- [ ] Add disable toggles for password changed, new user welcome, and email address change confirmation emails.
- [ ] Add account-created webhook dispatcher.
- [ ] Send full user fields in the account-created webhook payload.
- [ ] Store webhook response metadata by default.
- [ ] Store full payload bodies only when debug payload logging is enabled.
- [ ] Retain successful webhook metadata for 7 days and failed webhook metadata for 30 days by default.

### Phase 6 - Dashboard And WooCommerce

- [ ] Add optional custom full-page account dashboard.
- [ ] Add custom dashboard links with icons, ordering, role visibility, and open-in-new-tab.
- [ ] Detect WooCommerce availability.
- [ ] Allow custom dashboard to take over WooCommerce My Account when enabled.
- [ ] Delegate sensitive WooCommerce actions to native WooCommerce handlers/endpoints.
- [ ] Preserve orders, downloads, addresses, payment methods, account details, logout, and discoverable plugin-added endpoints.
- [ ] Add compatibility warnings for plugins that also modify login, registration, account pages, security redirects, or WooCommerce account endpoints.

### Phase 7 - Privacy, Accessibility, I18n, And Release Readiness

- [ ] Add WordPress privacy policy text.
- [ ] Add personal data exporter and eraser support.
- [ ] Add retention settings for pending registrations, verification logs, webhook logs, consent records, and audit entries.
- [ ] Store consent record with terms/privacy URLs, timestamp, and policy/version context.
- [ ] Avoid storing IP by default unless explicitly enabled.
- [ ] Ensure visible labels, keyboard operation, focus states, inline validation, `aria-invalid`, and live-region messages.
- [ ] Ensure responsive behavior down to 320px.
- [ ] Ensure all user-facing strings are translatable.
- [ ] Generate POT file.
- [ ] Ensure RTL-safe CSS.
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
- [ ] Unit test webhook payload construction and metadata logging.
- [ ] Unit test retention cleanup.
- [ ] Unit test uninstall cleanup.
- [ ] Browser/manual QA login, lost password, set password, registration, logout confirmation, disabled registration, and invalid/expired link screens.
- [ ] Browser/manual QA desktop and mobile responsive behavior.
- [ ] Browser/manual QA keyboard-only flow and focus management.
- [ ] Browser/manual QA email preview and test-send.
- [ ] Browser/manual QA WooCommerce dashboard delegation.
- [ ] Verify `npm run build`.
- [ ] Verify `npm run lint`.
- [ ] Verify `npm test`.
- [ ] Verify generated release zip through GitHub release workflow.

## Release Gates

- [ ] Frontend output remains disabled by default on fresh install.
- [ ] Emergency bypass opens native login only and never authenticates users.
- [ ] No standard WordPress core account screen is exposed during normal enabled frontend use.
- [ ] Registration creates no WordPress user until email confirmation and password setup are complete.
- [ ] WooCommerce account features remain usable when the custom dashboard is enabled.
- [ ] Accessibility acceptance criteria pass.
- [ ] Multilingual/i18n acceptance criteria pass.
- [ ] Privacy exporter/eraser and retention controls are present.
- [ ] Alynt Plugin Updater compatibility is verified with a release asset.

## Workflow Notes

- Use `C:\Users\Captain\Documents\AI Workflows\Toolkits\wp-plugin-toolkit\START_HERE_MASTER_WORKFLOW.md` as the router for plugin work.
- Next toolkit step before scaffold: use `d1-setup/windsurf-wp-config.md` Section 2 to create the scaffold master prompt.
- After scaffold, route to `@ADD_OBSERVABILITY_TOOLING_PROMPT.md run` before heavy feature work.
- After each major feature, run the feature review sequence: light review, bloat/structure review, UI/UX review, and security review.
- Before release, run pre-release prompts `@01` through `@13` in filename order, keeping security last.
- Do not update `PRE_RELEASE_CHECKLIST.md` unless a supported toolkit workflow completes successfully.

## Scaffold Prompt

- [x] Created `docs/SCAFFOLD_MASTER_PROMPT.md` from the approved product plan and toolkit scaffold guidance.

## Change Log

### 2026-07-03

- Added observability tooling: diagnostics settings, structured logs, health/recent-event UI, export/clear actions, retention cleanup, and redaction tests.
- Completed the initial scaffold, initialized Git, installed dependencies, and verified build/lint/test/audit.
- Added scaffold master prompt artifact for the initial plugin foundation.
- Created initial implementation plan from approved product-planning decisions.
