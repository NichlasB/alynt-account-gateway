# Changelog

## Unreleased

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
