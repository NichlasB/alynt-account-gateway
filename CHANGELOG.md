# Changelog

## Unreleased

## 0.1.11 - 2026-07-04

### Changed

- Extracted lost-password screen rendering into a dedicated lost-password screen service without changing copy, form fields, nonce/action names, query parameters, redirect behavior, routes, status handling, or accessibility attributes.

### Added

- Focused frontend lost-password screen service tests for default form output, nonce field output, request error state, forced invalid-token error state, reset-sent success state, and back-to-login behavior.

## 0.1.10 - 2026-07-04

### Changed

- Extracted logout confirmation screen rendering into a dedicated logout-screen service without changing copy, nonce/action names, query parameters, redirect behavior, routes, button classes, or notice behavior.

### Added

- Focused frontend logout-screen service tests for notice output, nonce-protected logout URL, cancel URL, action button classes, and empty-notice suppression.

## 0.1.9 - 2026-07-04

### Changed

- Extracted registration-disabled and invalid-link screen rendering into a dedicated state-screen service without changing copy, resend form fields, nonce/action names, query handling, routes, or accessibility attributes.

### Added

- Focused frontend state-screen service tests for registration-disabled output, invalid-link resend defaults, confirmation-resent status, resend error state, nonce field output, and accessibility attributes.

## 0.1.8 - 2026-07-04

### Changed

- Extracted shared frontend notice and verification-slot rendering into a dedicated component service without changing markup, copy, accessibility attributes, Turnstile output, or empty-copy behavior.

### Added

- Focused frontend component service tests for empty notice suppression, formatted notice output, verification placeholder output, and Turnstile widget output.

## 0.1.7 - 2026-07-04

### Changed

- Extracted frontend branding, media-panel, and design-token rendering into a dedicated branding service without changing rendered markup, design-token names, logo sizing, or fallback behavior.
- Renamed the local coding-rules reference from `.windsurfrules` to `AI_CODING_RULES.md` and kept release packaging exclusions current.

### Added

- Focused frontend branding service tests for design tokens, media image/pattern output, store-name fallback, logo output, and logo width clamping.

## 0.1.6 - 2026-07-04

### Changed

- Extracted frontend asset enqueueing into a dedicated asset service without changing asset handles, URLs, labels, or Turnstile loading rules.

### Added

- Focused frontend asset service tests for frontend-output gating, CSS/JS enqueueing, localized labels, and Turnstile registration-screen loading.

## 0.1.5 - 2026-07-04

### Changed

- Extracted frontend URL and screen-routing helpers into a dedicated route service without changing public routes or query handling.

### Added

- Focused frontend route service tests for action URLs, query preservation, screen routing, WooCommerce takeover routing, and path matching.

## 0.1.4 - 2026-07-04

### Changed

- Reconciled completed implementation-plan checklist items for email preview/test-send QA and profile email-change request suppression.
- Extracted frontend gateway title and error-message lookup into a dedicated message catalog service without changing rendered copy or behavior.

### Added

- Focused frontend message catalog tests for known mappings and fallback messages.

## 0.1.3 - 2026-07-04

### Added

- Focused registration completion coverage for the confirmed pending account creation path.

### Verified

- Registration flow and spam-prevention QA.
- Local-safe account-created webhook verification.

## 0.1.2 - 2026-07-04

### Added

- Focused email tooling test coverage for preview rendering and test-send validation.

### Fixed

- The email-change disable toggle now also suppresses WordPress pending profile email-change request emails and clears the pending marker so users are not left waiting for a disabled confirmation email.

### Verified

- Email preview and test-send QA on LocalWP Plugin Tester.

## 0.1.1 - 2026-07-04

### Added

- Settings import/export JSON for all plugin-owned settings.
- Per-tab restore defaults with confirmation and diagnostics logging.
- Gateway screen preview mode while frontend output is disabled.
- Compatibility warnings for login, registration, redirect/security, account-page, and WooCommerce account endpoint overlaps.
- Focused unit coverage for frontend routing, emergency bypass behavior, role/admin-bar access rules, email-only login, retention cleanup, deactivation cleanup, and uninstall cleanup.

## 0.1.0 - 2026-07-04

### Added

- Branded account gateway screens for login, registration, lost password, reset password, set password, and logout confirmation.
- Configurable login path, account action base, after-login redirect, and emergency bypass key.
- Frontend output safety toggle, disabled by default.
- Brand settings for logo, logo max width, gateway background image, colors, button colors, and font stacks.
- Screen copy settings for login, registration, lost password, set password, logout, disabled registration, and invalid links.
- Confirmation-first registration flow with pending-token storage, email confirmation, password setup, and user creation after confirmation.
- Email-only login with generated username support.
- Password complexity checks for minimum length, uppercase, lowercase, number, and symbol characters.
- Cloudflare Turnstile and Reoon Email Verifier integration points for registration protection.
- Rate limiting for registration, confirmation resend, login, and lost password actions.
- Branded account email templates, email preview, and test-send tooling.
- Optional custom customer dashboard and WooCommerce My Account takeover.
- WooCommerce endpoint delegation for standard account facilities, including orders, downloads, addresses, payment methods, and account details.
- Custom dashboard links with icons, ordering, role visibility, and new-tab behavior.
- Account-created webhook delivery and webhook log storage.
- Privacy exporter and eraser callbacks for plugin-owned account gateway data.
- Consent, audit, verification, webhook, diagnostics, and pending registration tables with scheduled retention cleanup.
- Admin diagnostics with redacted context, recent event display, CSV export, and clear controls.
- Multilingual-ready strings and POT generation tooling.
- Private GitHub release workflow and plugin header metadata for Alynt Plugin Updater compatibility.

### Changed

- Updated release documentation from scaffold status to the current implemented feature set.

### Fixed

- Removed `innerHTML` usage from the admin media preview UI.
- Expanded uninstall cleanup to clear the retention hook and transient-backed rate-limit buckets.
