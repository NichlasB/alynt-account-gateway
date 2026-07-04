# Changelog

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
