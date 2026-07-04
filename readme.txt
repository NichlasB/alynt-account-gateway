=== Alynt Account Gateway ===
Contributors: alynt
Tags: login, registration, account, woocommerce, dashboard
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.1.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A branded account gateway for WordPress login, registration, password flows, emails, dashboards, WooCommerce account handling, privacy tooling, and integrations.

== Description ==

Alynt Account Gateway is private Alynt-distributed software for replacing bare WordPress account screens with a branded, configurable account experience.

Frontend output is disabled by default so site owners can configure URL paths, branding, screen copy, registration behavior, emails, integrations, and dashboard behavior before public changes appear.

Core features include:

* Branded login, registration, lost password, reset password, set password, and logout confirmation screens.
* Configurable login path, account action base, after-login redirect, and emergency bypass key.
* Confirmation-first registration that creates the WordPress user only after email confirmation and password setup.
* Optional Cloudflare Turnstile and Reoon Email Verifier support.
* Rate limiting for account gateway actions.
* Branded account emails with preview and test-send tools.
* Optional custom dashboard and WooCommerce My Account takeover.
* Custom dashboard links with icons, ordering, role visibility, and new-tab behavior.
* Account-created webhook delivery.
* Privacy exporter/eraser integration and retention cleanup.
* Multilingual-ready strings and generated POT file.

== Installation ==

1. Upload the private Alynt release zip through Plugins -> Add New -> Upload Plugin.
2. Activate Alynt Account Gateway.
3. Configure Settings -> Account Gateway.
4. Enable Frontend Output only after confirming the settings.

== Changelog ==

= 0.1.7 =

* Extracted frontend branding, media-panel, and design-token rendering into a dedicated branding service without changing rendered markup, design-token names, logo sizing, or fallback behavior.
* Renamed the local coding-rules reference from `.windsurfrules` to `AI_CODING_RULES.md` and kept release packaging exclusions current.
* Added focused frontend branding service tests for design tokens, media image/pattern output, store-name fallback, logo output, and logo width clamping.

= 0.1.6 =

* Extracted frontend asset enqueueing into a dedicated asset service without changing asset handles, URLs, labels, or Turnstile loading rules.
* Added focused frontend asset service tests for frontend-output gating, CSS/JS enqueueing, localized labels, and Turnstile registration-screen loading.

= 0.1.5 =

* Extracted frontend URL and screen-routing helpers into a dedicated route service without changing public routes or query handling.
* Added focused frontend route service tests for action URLs, query preservation, screen routing, WooCommerce takeover routing, and path matching.

= 0.1.4 =

* Reconciled completed implementation-plan checklist items for email preview/test-send QA and profile email-change request suppression.
* Extracted frontend gateway title and error-message lookup into a dedicated message catalog service without changing rendered copy or behavior.
* Added focused frontend message catalog tests for known mappings and fallback messages.

= 0.1.3 =
* Added focused registration completion coverage for the confirmed pending account creation path.
* Verified registration flow, spam-prevention behavior, and local-safe account-created webhook dispatch on LocalWP Plugin Tester.

= 0.1.2 =
* Verified email preview and test-send tooling on LocalWP Plugin Tester.
* Added focused email tooling test coverage for preview rendering and test-send validation.
* Fixed the email-change disable toggle so it also suppresses WordPress pending profile email-change request emails and clears the pending marker.

= 0.1.1 =
* Added settings import/export JSON, per-tab restore defaults, and gateway preview mode.
* Added compatibility warnings for login, registration, redirects, account pages, and WooCommerce account endpoint overlaps.
* Added focused release-gate unit coverage for frontend routing, emergency bypass, role access, email-only login, retention, deactivation, and uninstall cleanup.

= 0.1.0 =
* Added branded account gateway screens and configurable account paths.
* Added confirmation-first registration, password validation, registration protection, rate limiting, emails, dashboard, WooCommerce takeover, webhooks, privacy tools, diagnostics, and updater metadata.
* Fixed admin media preview DOM handling and uninstall cleanup for scheduled hooks and rate-limit transients.
