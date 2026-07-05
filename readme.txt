=== Alynt Account Gateway ===
Contributors: alynt
Tags: login, registration, account, woocommerce, dashboard
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.1.22
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A branded account gateway for WordPress login, registration, password flows, emails, dashboards, WooCommerce account handling, privacy tooling, and integrations.

== Description ==

Alynt Account Gateway is private Alynt-distributed software for replacing bare WordPress account screens with a branded, configurable account experience.

Frontend output is disabled by default so site owners can configure URL paths, branding, screen copy, registration behavior, emails, integrations, and dashboard behavior before public changes appear.

Core features include:

* Branded login, registration, lost password, reset password, set password, and logout confirmation screens.
* Configurable login path, account action base, after-login redirect, and emergency bypass key.
* Setup readiness checks before public frontend output is enabled.
* Confirmation-first registration that creates the WordPress user only after email confirmation and password setup.
* Optional Cloudflare Turnstile and Reoon Email Verifier support.
* Rate limiting for account gateway actions.
* Branded account emails with preview and test-send tools.
* Optional custom dashboard and WooCommerce My Account takeover.
* Custom dashboard links with icons, ordering, role visibility, and new-tab behavior.
* Account-created webhook delivery with optional request signing and admin delivery summaries.
* Privacy exporter/eraser integration and retention cleanup.
* Multilingual-ready strings and generated POT file.

== Installation ==

1. Upload the private Alynt release zip through Plugins -> Add New -> Upload Plugin.
2. Activate Alynt Account Gateway.
3. Configure Settings -> Account Gateway.
4. Enable Frontend Output only after confirming the settings.

== Changelog ==

= 0.1.22 =

* Added a General tab setup readiness panel with advisory checks before enabling public frontend output.

= 0.1.21 =

* Added Webhooks tab delivery summary, signature verification guidance, and expandable delivery metadata for recent webhook logs.

= 0.1.20 =

* Added optional HMAC request signing headers for outgoing account-created webhooks.

= 0.1.19 =

* Added Webhooks tab tools for sending an explicit account-created test webhook and reviewing recent webhook delivery metadata.

= 0.1.18 =

* Added a repeatable custom dashboard links editor for label, URL, icon, order, role visibility, and new-tab behavior while preserving the existing JSON storage format.
* Sanitized custom dashboard links into a known JSON shape so imported or hand-edited settings skip incomplete rows and discard unsupported fields.

= 0.1.17 =

* Extracted full gateway document rendering and admin preview rendering into a dedicated document renderer service without changing document markup, body class, title behavior, dashboard-vs-auth shell selection, routes, redirects, logout handling, or admin preview compatibility.
* Added focused frontend document renderer tests for full document output, preview normalization, set-password preview rendering, dashboard path propagation, title lookup, and the preserved admin-preview title wrapper.

= 0.1.16 =

* Extracted the generic branded gateway shell and auth screen dispatch into a dedicated gateway shell service without changing shell markup, branding output, media panel output, routes, query parameters, password preview behavior, dashboard behavior, or request handling.
* Added focused frontend gateway shell tests for shell output, branding/media insertion, auth screen dispatch, unknown-screen fallback, and set-password preview rendering.

= 0.1.15 =

* Extracted frontend dashboard shell and dashboard content rendering into a dedicated dashboard screen service without changing dashboard copy, links, logout URL behavior, WooCommerce takeover warning, endpoint content delegation, external-link accessibility text, or dashboard classes.
* Added focused frontend dashboard screen service tests for shell output, brand/logout rendering, dashboard hero metadata, dashboard links, WooCommerce warning, endpoint content rendering, and endpoint fallback copy.

= 0.1.14 =

* Extracted set-password screen routing and shared password form rendering into a dedicated set-password screen service without changing copy, form fields, nonce/action names, query parameters, token/key validation routing, password strength markup, password requirements, or accessibility attributes.
* Added focused frontend set-password screen service tests for default password form output, error accessibility state, pending-registration token form output, native reset-key form output, invalid registration-token fallback, and invalid native reset-key fallback.

= 0.1.13 =

* Extracted registration screen rendering into a dedicated registration screen service without changing copy, form fields, nonce/action names, query parameters, terms/privacy links, verification slot output, registration-success handling, or accessibility attributes.
* Added focused frontend registration screen service tests for default form output, nonce field output, terms/privacy links, disabled submit state, registration-sent success state, registration error accessibility state, and Turnstile slot output.

= 0.1.12 =

* Extracted login screen rendering into a dedicated login screen service without changing copy, form fields, nonce/action names, query parameters, redirect handling, routes, password toggle markup, status handling, or accessibility attributes.
* Added focused frontend login screen service tests for default form output, nonce field output, account links, password toggle markup, success states, redirect preservation, and login error accessibility state.

= 0.1.11 =

* Extracted lost-password screen rendering into a dedicated lost-password screen service without changing copy, form fields, nonce/action names, query parameters, redirect behavior, routes, status handling, or accessibility attributes.
* Added focused frontend lost-password screen service tests for default form output, nonce field output, request error state, forced invalid-token error state, reset-sent success state, and back-to-login behavior.

= 0.1.10 =

* Extracted logout confirmation screen rendering into a dedicated logout-screen service without changing copy, nonce/action names, query parameters, redirect behavior, routes, button classes, or notice behavior.
* Added focused frontend logout-screen service tests for notice output, nonce-protected logout URL, cancel URL, action button classes, and empty-notice suppression.

= 0.1.9 =

* Extracted registration-disabled and invalid-link screen rendering into a dedicated state-screen service without changing copy, resend form fields, nonce/action names, query handling, routes, or accessibility attributes.
* Added focused frontend state-screen service tests for registration-disabled output, invalid-link resend defaults, confirmation-resent status, resend error state, nonce field output, and accessibility attributes.

= 0.1.8 =

* Extracted shared frontend notice and verification-slot rendering into a dedicated component service without changing markup, copy, accessibility attributes, Turnstile output, or empty-copy behavior.
* Added focused frontend component service tests for empty notice suppression, formatted notice output, verification placeholder output, and Turnstile widget output.

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
