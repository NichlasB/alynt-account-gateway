=== Alynt Account Gateway ===
Contributors: alynt
Tags: login, registration, account, woocommerce, dashboard
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.1.65
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
* Branded WooCommerce customer dashboard overview with quick links for orders, addresses, and account details.
* Custom dashboard links with icons, ordering, role visibility, and new-tab behavior.
* Account-created webhook delivery with optional request signing and admin delivery summaries.
* Privacy exporter/eraser integration and retention cleanup.
* Multilingual-ready strings and generated POT file.

== Installation ==

1. Upload the private Alynt release zip through Plugins -> Add New -> Upload Plugin.
2. Activate Alynt Account Gateway.
3. Configure Settings -> Account Gateway.
4. Enable Frontend Output only after confirming the settings.

== Uninstall ==

Uninstalling the plugin removes plugin-owned settings, the stored database schema version, the retention cleanup schedule, rate-limit transients, and plugin-owned account gateway tables. WordPress users, WooCommerce orders, media-library files, and non-plugin data are not removed.

== Changelog ==

= 0.1.65 =

* Added scoped dashboard form-control CSS guardrails to reduce theme interference with delegated account controls.

= 0.1.64 =

* Added scoped gateway form-control CSS guardrails to reduce theme interference with fields and buttons.

= 0.1.63 =

* Added accessible password visibility controls to set-password fields and explicit controlled-field relationships for password toggles.

= 0.1.62 =

* Replaced the remaining left-specific frontend resend-guidance indentation with RTL-safe logical CSS.

= 0.1.61 =

* Replaced left-specific admin panel accents with RTL-safe logical inline-start CSS.

= 0.1.60 =

* Added privacy-conscious branded auth diagnostics and Security tab Gateway Auth Signals.

= 0.1.59 =

* Added frontend focus-visible and high-contrast forced-colors CSS guardrails.

= 0.1.58 =

* Added a Security tab Manual Review Queue for Reoon flagged registration results.

= 0.1.57 =

* Strengthened uninstall cleanup coverage and documented the plugin-owned data cleanup policy.

= 0.1.56 =

* Updated the GitHub release workflow to use `softprops/action-gh-release@v3`.

= 0.1.55 =

* Added safer settings import validation, clearer import notices, and configuration portability guidance.

= 0.1.54 =

* Added Security tab guidance for diagnostics-dependent access, routing, email, and webhook signals.

= 0.1.53 =

* Added clearer invalid-link resend throttling guidance for confirmation email cooldowns.

= 0.1.52 =

* Added Security & Spam Provider Failure Triage guidance for Turnstile and Reoon configuration, connectivity, challenge, and response issues.

= 0.1.51 =

* Added Security & Spam guidance for Reoon flagged-status policy decisions.

= 0.1.50 =

* Added GitHub updater metadata so Alynt Plugin Updater can discover the plugin.

= 0.1.49 =

* Added WooCommerce account endpoint shortcut actions for related customer account tasks.

= 0.1.48 =

* Added a branded WooCommerce endpoint unavailable fallback panel with recovery links for delegated account sections.

= 0.1.47 =

* Added Security tab Pending Registration Lifecycle Signals for pending, confirmed, expired, and completed registration records.

= 0.1.46 =

* Added Security tab Registration Abuse Signals for recent registration throttles, resend throttles, flagged email blocks, and account setup friction.

= 0.1.45 =

* Added Security tab Account Delivery Signals for recent welcome email failures, account webhook failures, and failed webhook deliveries.

= 0.1.44 =

* Added Security tab Gateway Routing Signals for recent native login redirects, reset-link redirects, and preserved redirect destinations.

= 0.1.43 =

* Added Security tab Access Control Signals for recent login lockouts, password-reset lockouts, and blocked wp-admin access diagnostics.

= 0.1.42 =

* Added Security tab Registration Flow Signals for recent consent blocks, registration system failures, password setup blocks, and confirmation resends.

= 0.1.41 =

* Added Security tab Provider Health Signals for recent Turnstile and Reoon challenge, connectivity, configuration, response, and email-quality blocks.

= 0.1.40 =

* Added Security tab Rate Limit Pressure cards for recent registration, confirmation resend, login, and password-reset blocks.

= 0.1.39 =

* Added configurable Reoon flagged-status policy with Security tab guidance and stricter blocking support for catch-all, role account, unknown, and inbox-full statuses.

= 0.1.38 =

* Added pending-registration resend and expiry visibility with clearer resend throttling copy, confirmation resent activity logging, and Security tab next-step guidance.

= 0.1.37 =

* Added frontend-safe provider failure messages and clearer Security tab guidance for Reoon and Turnstile.

= 0.1.36 =

* Added Security tab activity logging and guidance for blocked registration-flow outcomes.

= 0.1.35 =

* Added diagnostics events for native login redirects and blocked wp-admin access when diagnostics are enabled.

= 0.1.34 =

* Added auth-side rate-limit activity logging for blocked login and password-reset attempts on the Security tab.

= 0.1.33 =

* Added admin-readable guidance for recent verification activity outcomes on the Security tab.

= 0.1.32 =

* Added read-only pending registration visibility on the Security tab with masked email rows and status labels.

= 0.1.31 =

* Added registration security activity logging for provider outcomes and rate-limit blocks, plus a masked recent activity table on the Security tab.

= 0.1.30 =

* Added Security tab status guidance for anti-spam provider readiness, Reoon policy visibility, and rate-limit posture.

= 0.1.29 =

* Added email template editor guidance with token browsing, per-template action notes, and clearer preview/test-send help.

= 0.1.28 =

* Added field-level help text and accessible descriptions for high-impact Account Gateway settings.

= 0.1.27 =

* Added read-only settings tab guidance panels with setup prompts and related-tab actions across all Account Gateway settings tabs.

= 0.1.26 =

* Added scoped presentation polish for delegated WooCommerce account notices, forms, fieldsets, buttons, and payment-method containers inside the branded dashboard.

= 0.1.25 =

* Added branded next-step panels for WooCommerce account endpoint edge states on orders, downloads, addresses, account details, and payment-methods pages.

= 0.1.24 =

* Added branded guidance copy above delegated WooCommerce account endpoint content for orders, order details, downloads, addresses, account details, and payment-method flows.

= 0.1.23 =

* Added a branded WooCommerce customer overview on the base account dashboard with quick links for orders, addresses, and account details.

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
