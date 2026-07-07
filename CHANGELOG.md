# Changelog

## Unreleased

## 0.1.83 - 2026-07-07

### Fixed

- Fixed Provider Failure Triage latest-seen metadata rendering in the Security tab.

## 0.1.82 - 2026-07-07

### Changed

- Added latest-seen timestamps to provider failure triage cards in the Security tab.

## 0.1.81 - 2026-07-07

### Changed

- Changed password requirement checklist state from current-page semantics to checkbox-style ria-checked semantics for assistive technology.

## 0.1.80 - 2026-07-07

### Changed

- Added explicit shell direction attributes for auth gateway and dashboard surfaces to improve RTL resilience.

## 0.1.79 - 2026-07-07

### Changed

- Added Security tab next-step triage guidance for recent verification activity rows.

## 0.1.78 - 2026-07-07

### Changed

- Improved blocked wp-admin access diagnostics with privacy-safe request path, method, and query-key context plus clearer Security tab guidance.

## 0.1.77 - 2026-07-07

### Changed

- Associated frontend instruction notices with login, registration, password, and invalid-link forms through form-level `aria-describedby` relationships.

## 0.1.76 - 2026-07-07

### Changed

- Added current-page semantics to dashboard account links for screen reader and keyboard navigation.

## 0.1.75 - 2026-07-07

### Changed

- Added a read-only manual-review decision playbook for Reoon flagged email statuses in the Security tab.

## 0.1.74 - 2026-07-07

### Changed

- Improved resend-throttle accessibility by associating cooldown guidance with the rate-limited confirmation resend form.

## 0.1.73 - 2026-07-07

### Changed

- Added a Reoon policy visibility table separating always-blocked statuses from configurable flagged statuses.

## 0.1.72 - 2026-07-07

### Changed

- Added LTR direction hints to machine-readable admin settings fields for RTL and multilingual admin environments.

## 0.1.71 - 2026-07-07

### Changed

- Added synchronized `aria-disabled` state for the set-password submit button while password requirements are unmet.

## 0.1.70 - 2026-07-07

### Changed

- Added a Security tab launch decision summary for public registration readiness, anti-spam coverage, consent links, flagged email policy, and launch diagnostics evidence.

## 0.1.69 - 2026-07-07

### Changed

- Added privacy-preserving active rate-limit bucket visibility to the Security settings panel.

## 0.1.68 - 2026-07-07

### Changed

- Added stronger status semantics to the password strength live region.

## 0.1.67 - 2026-07-07

### Changed

- Added LTR direction hints to branded password fields for RTL language resilience.

## 0.1.66 - 2026-07-06

### Changed

- Added LTR direction hints to branded auth email fields for RTL language resilience.

## 0.1.65 - 2026-07-06

### Changed

- Added scoped dashboard form-control CSS guardrails to reduce theme interference with delegated account controls.

## 0.1.64 - 2026-07-06

### Changed

- Added scoped gateway form-control CSS guardrails to reduce theme interference with fields and buttons.

## 0.1.63 - 2026-07-06

### Changed

- Added accessible password visibility controls to set-password fields and explicit controlled-field relationships for password toggles.

## 0.1.62 - 2026-07-06

### Changed

- Replaced the remaining left-specific frontend resend-guidance indentation with RTL-safe logical CSS.

## 0.1.61 - 2026-07-06

### Changed

- Replaced left-specific admin panel accents with RTL-safe logical inline-start CSS.

## 0.1.60 - 2026-07-06

### Added

- Added privacy-conscious branded auth diagnostics and Security tab Gateway Auth Signals.

## 0.1.59 - 2026-07-06

### Added

- Added frontend focus-visible and high-contrast forced-colors CSS guardrails.

## 0.1.58 - 2026-07-06

### Added

- Added a Security tab Manual Review Queue for Reoon flagged registration results.

## 0.1.57 - 2026-07-06

### Changed

- Strengthened uninstall cleanup coverage and documented the plugin-owned data cleanup policy.

## 0.1.56 - 2026-07-06

### Changed

- Updated the GitHub release workflow to use `softprops/action-gh-release@v3`.

## 0.1.55 - 2026-07-06

### Added

- Added safer settings import validation, clearer import notices, and configuration portability guidance.

## 0.1.54 - 2026-07-06

### Added

- Added Security tab guidance for diagnostics-dependent access, routing, email, and webhook signals.

## 0.1.53 - 2026-07-06

### Added

- Added clearer invalid-link resend throttling guidance for confirmation email cooldowns.

## 0.1.52 - 2026-07-06

### Added

- Added Security & Spam Provider Failure Triage guidance for Turnstile and Reoon configuration, connectivity, challenge, and response issues.

## 0.1.51 - 2026-07-06

### Added

- Added Security & Spam guidance for Reoon flagged-status policy decisions.

## 0.1.50 - 2026-07-06

### Added

- Added GitHub updater metadata so Alynt Plugin Updater can discover the plugin.

## 0.1.49 - 2026-07-06

### Added

- Added WooCommerce account endpoint shortcut actions for related customer account tasks.

## 0.1.48 - 2026-07-06

### Added

- Added a branded WooCommerce endpoint unavailable fallback panel with recovery links for delegated account sections.

## 0.1.47 - 2026-07-06

### Added

- Added Security tab Pending Registration Lifecycle Signals for pending, confirmed, expired, and completed registration records.

## 0.1.46 - 2026-07-06

### Added

- Added Security tab Registration Abuse Signals for recent registration throttles, resend throttles, flagged email blocks, and account setup friction.

## 0.1.45 - 2026-07-06

### Added

- Added Security tab Account Delivery Signals for recent welcome email failures, account webhook failures, and failed webhook deliveries.

## 0.1.44 - 2026-07-06

### Added

- Added Security tab Gateway Routing Signals for recent native login redirects, reset-link redirects, and preserved redirect destinations.

## 0.1.43 - 2026-07-06

### Added

- Added Security tab Access Control Signals for recent login lockouts, password-reset lockouts, and blocked wp-admin access diagnostics.

## 0.1.42 - 2026-07-06

### Added

- Added Security tab Registration Flow Signals for recent consent blocks, registration system failures, password setup blocks, and confirmation resends.

## 0.1.41 - 2026-07-05

### Added

- Added Security tab Provider Health Signals for recent Turnstile and Reoon challenge, connectivity, configuration, response, and email-quality blocks.

## 0.1.40 - 2026-07-05

### Added

- Added Security tab Rate Limit Pressure cards for recent registration, confirmation resend, login, and password-reset blocks.

## 0.1.39 - 2026-07-05

### Added

- Added configurable Reoon flagged-status policy with Security tab guidance and stricter blocking support for catch-all, role account, unknown, and inbox-full statuses.

## 0.1.38 - 2026-07-05

### Added

- Added pending-registration resend and expiry visibility with clearer resend throttling copy, confirmation resent activity logging, and Security tab next-step guidance.

## 0.1.37 - 2026-07-05

### Added

- Added frontend-safe provider failure messages and clearer Security tab guidance for Reoon and Turnstile.

## 0.1.36 - 2026-07-05

### Added

- Added Security tab activity logging and guidance for blocked registration-flow outcomes.

## 0.1.35 - 2026-07-05

### Added

- Added diagnostics events for native login redirects and blocked wp-admin access when diagnostics are enabled.

## 0.1.34 - 2026-07-05

### Added

- Added auth-side rate-limit activity logging for blocked login and password-reset attempts on the Security tab.

## 0.1.33 - 2026-07-05

### Added

- Added admin-readable guidance for recent verification activity outcomes on the Security tab.

## 0.1.32 - 2026-07-05

### Added

- Added read-only pending registration visibility on the Security tab with masked email rows and status labels.

## 0.1.31 - 2026-07-05

### Added

- Added registration security activity logging for provider outcomes and rate-limit blocks, plus a masked recent activity table on the Security tab.

## 0.1.30 - 2026-07-05

### Added

- Added Security tab status guidance for anti-spam provider readiness, Reoon policy visibility, and rate-limit posture.

## 0.1.29 - 2026-07-05

### Added

- Added email template editor guidance with token browsing, per-template action notes, and clearer preview/test-send help.

## 0.1.28 - 2026-07-05

### Added

- Added field-level help text and accessible descriptions for high-impact Account Gateway settings.

## 0.1.27 - 2026-07-05

### Added

- Added read-only settings tab guidance panels with setup prompts and related-tab actions across all Account Gateway settings tabs.

## 0.1.26 - 2026-07-05

### Added

- Added scoped presentation polish for delegated WooCommerce account notices, forms, fieldsets, buttons, and payment-method containers inside the branded dashboard.

## 0.1.25 - 2026-07-05

### Added

- Added branded next-step panels for WooCommerce account endpoint edge states on orders, downloads, addresses, account details, and payment-methods pages.

## 0.1.24 - 2026-07-05

### Added

- Added branded guidance copy above delegated WooCommerce account endpoint content for orders, order details, downloads, addresses, account details, and payment-method flows.

## 0.1.23 - 2026-07-05

### Added

- Added a branded WooCommerce customer overview on the base account dashboard with quick links for orders, addresses, and account details.

## 0.1.22 - 2026-07-05

### Added

- Added a General tab setup readiness panel with advisory checks before enabling public frontend output.

## 0.1.21 - 2026-07-05

### Added

- Added Webhooks tab delivery summary, signature verification guidance, and expandable delivery metadata for recent webhook logs.

## 0.1.20 - 2026-07-05

### Added

- Added optional HMAC request signing headers for outgoing account-created webhooks.

## 0.1.19 - 2026-07-04

### Added

- Added Webhooks tab tools for sending an explicit account-created test webhook and reviewing recent webhook delivery metadata.

## 0.1.18 - 2026-07-04

### Added

- Added a repeatable custom dashboard links editor for label, URL, icon, order, role visibility, and new-tab behavior while preserving the existing JSON storage format.

### Changed

- Sanitized custom dashboard links into a known JSON shape so imported or hand-edited settings skip incomplete rows and discard unsupported fields.

## 0.1.17 - 2026-07-04

### Changed

- Extracted full gateway document rendering and admin preview rendering into a dedicated document renderer service without changing document markup, body class, title behavior, dashboard-vs-auth shell selection, routes, redirects, logout handling, or admin preview compatibility.

### Added

- Focused frontend document renderer tests for full document output, preview normalization, set-password preview rendering, dashboard path propagation, title lookup, and the preserved admin-preview title wrapper.

## 0.1.16 - 2026-07-04

### Changed

- Extracted the generic branded gateway shell and auth screen dispatch into a dedicated gateway shell service without changing shell markup, branding output, media panel output, routes, query parameters, password preview behavior, dashboard behavior, or request handling.

### Added

- Focused frontend gateway shell service tests for shell output, branding/media insertion, auth screen dispatch, unknown-screen fallback, and set-password preview rendering.

## 0.1.15 - 2026-07-04

### Changed

- Extracted frontend dashboard shell and dashboard content rendering into a dedicated dashboard screen service without changing dashboard copy, links, logout URL behavior, WooCommerce takeover warning, endpoint content delegation, external-link accessibility text, or dashboard classes.

### Added

- Focused frontend dashboard screen service tests for shell output, brand/logout rendering, dashboard hero metadata, dashboard links, WooCommerce warning, endpoint content rendering, and endpoint fallback copy.

## 0.1.14 - 2026-07-04

### Changed

- Extracted set-password screen routing and shared password form rendering into a dedicated set-password screen service without changing copy, form fields, nonce/action names, query parameters, token/key validation routing, password strength markup, password requirements, or accessibility attributes.

### Added

- Focused frontend set-password screen service tests for default password form output, error accessibility state, pending-registration token form output, native reset-key form output, invalid registration-token fallback, and invalid native reset-key fallback.

## 0.1.13 - 2026-07-04

### Changed

- Extracted registration screen rendering into a dedicated registration screen service without changing copy, form fields, nonce/action names, query parameters, terms/privacy links, verification slot output, registration-success handling, or accessibility attributes.

### Added

- Focused frontend registration screen service tests for default form output, nonce field output, terms/privacy links, disabled submit state, registration-sent success state, registration error accessibility state, and Turnstile slot output.

## 0.1.12 - 2026-07-04

### Changed

- Extracted login screen rendering into a dedicated login screen service without changing copy, form fields, nonce/action names, query parameters, redirect handling, routes, password toggle markup, status handling, or accessibility attributes.

### Added

- Focused frontend login screen service tests for default form output, nonce field output, account links, password toggle markup, success states, redirect preservation, and login error accessibility state.

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
