# Alynt Account Gateway v1.0 Readiness Plan

## Status

- Current phase: Readiness plan created; fully validated v0.1.98 pre-readiness settings UX polish awaits release approval before acceptance target selection.
- Product baseline: `v0.1.97`, released and verified through Alynt Plugin Updater.
- Release goal: `v1.0.0`.
- Frontend output default: Disabled.
- Distribution: Alynt-distributed plugin with GitHub updater compatibility.
- Acceptance target: To be confirmed through the WordPress site-operations workflow before testing begins.

## Purpose

This is the living production-acceptance roadmap for Alynt Account Gateway. The implementation plan tracks one approved pre-readiness settings UX polish slice before this plan begins. This plan then determines whether the resulting product is ready to be called `v1.0.0` on real WordPress and WooCommerce sites.

Readiness work should prioritize runtime evidence, configuration safety, compatibility, documentation, and operational recovery. New product features belong in a separately approved roadmap unless they are required to resolve a release-blocking defect found during acceptance testing.

## Readiness Principles

- Test the exact packaged build that would be released.
- Use a production-like staging site before any live-site rollout.
- Keep Frontend Output disabled until configuration and readiness checks pass.
- Record versions, settings fingerprints, package hashes, browser evidence, and cleanup results.
- Use site-owned test credentials and disposable test accounts; never commit secrets or personal data.
- Preserve a restore point before package installation, configuration import, or transactional testing.
- Treat native WordPress account-screen leakage, authentication bypass, account-creation defects, sensitive-data exposure, and destructive cleanup behavior as release blockers.
- Require explicit release approval before commit, tag, push, or publication of `v1.0.0`.

## Out Of Scope

- New account, dashboard, email, integration, or WooCommerce features that are not required to fix an acceptance defect.
- Site-specific visual redesign beyond validating the plugin's existing brand controls.
- Replacing site-owned SMTP, DNS, consent-management, firewall, backup, or monitoring services.
- Live-site writes before the WordPress site-operations confirmation and approval gates are satisfied.

## Phase 0: Acceptance Environment

- [ ] Confirm the staging site shorthand, domain, filesystem path, and operating mode.
- [ ] Confirm whether a separate live target exists and keep it out of scope until staging passes.
- [ ] Record WordPress, PHP, WooCommerce, theme, mail-provider, and relevant security/caching plugin versions.
- [ ] Create and verify a restore point for files and database.
- [ ] Record the starting Alynt Account Gateway version, activation state, settings fingerprint, and frontend-output state.
- [ ] Define disposable administrator, shop-manager, customer, and pending-registration test identities.
- [ ] Define cleanup steps for test users, orders, emails, logs, webhook records, uploads, and temporary helpers.
- [ ] Store integration credentials outside the repository and confirm that evidence will be redacted.
- [ ] Agree on browser viewport, language, RTL, assistive-technology, and transactional test coverage.

### Environment Evidence

| Item | Value | Evidence | Status |
| --- | --- | --- | --- |
| Staging target | To be confirmed |  | Pending |
| Restore point |  |  | Pending |
| WordPress / PHP |  |  | Pending |
| WooCommerce / theme |  |  | Pending |
| Installed gateway baseline |  |  | Pending |
| Settings fingerprint |  |  | Pending |

## Phase 1: Production-Like Configuration

- [ ] Install the current public release asset through the supported updater or package-install path.
- [ ] Verify Frontend Output remains disabled during initial configuration.
- [ ] Configure and verify login path, account action base, after-login redirect, and emergency bypass.
- [ ] Configure logo, logo width, background image, colors, button colors, and typography using representative branding.
- [ ] Configure screen-specific welcome and instruction copy.
- [ ] Configure relative Terms and Privacy paths and verify both destinations.
- [ ] Configure account-creation policy and confirm the default disabled behavior before deliberate enablement.
- [ ] Configure email templates, enable/disable switches, sender expectations, and test recipient.
- [ ] Configure dashboard, custom links, role visibility, icons, ordering, and new-tab behavior.
- [ ] Configure WooCommerce takeover only when the site's account page and endpoints are ready.
- [ ] Configure Turnstile, Reoon, and webhook credentials only where acceptance requires them.
- [ ] Review the plugin readiness summary and resolve every release-blocking item.
- [ ] Export a redacted configuration snapshot for recovery and portability testing.
- [ ] Obtain approval before enabling Frontend Output on staging.

## Phase 2: Core Account Acceptance

- [ ] Verify email-only login with valid, invalid, rate-limited, and inactive/pending account states.
- [ ] Verify login redirects preserve only safe destinations and do not create loops.
- [ ] Verify public registration is unavailable while account creation is disabled.
- [ ] Verify the full confirmation-first registration flow when account creation is enabled.
- [ ] Confirm no WordPress user is created before email confirmation and password completion.
- [ ] Verify pending-registration expiry, invalid tokens, used tokens, and resend behavior.
- [ ] Verify generated usernames follow the configured pattern while login remains email-only.
- [ ] Verify password creation requires matching fields and the configured minimum policy.
- [ ] Verify password strength feedback is understandable and accessible.
- [ ] Verify lost-password, reset-password, password-changed, and invalid/expired reset-link states.
- [ ] Verify logout confirmation, cancellation, successful logout, and safe redirect behavior.
- [ ] Verify administrators and shop managers retain intended wp-admin and toolbar access.
- [ ] Verify other roles are redirected away from wp-admin without redirect loops.
- [ ] Verify the emergency bypass exposes only native login and never authenticates a visitor.
- [ ] Verify all public account routes avoid the native WordPress shell except through deliberate bypass.
- [ ] Verify frontend output can be disabled to restore native behavior without losing settings.

## Phase 3: Email And Deliverability Acceptance

- [ ] Preview every supported account email using representative tokens and branding.
- [ ] Test-send every supported account email through the site's real mail-delivery path.
- [ ] Verify headings, bold text, links, lists, spacing, logo sizing, colors, and mobile rendering.
- [ ] Verify plain-text alternatives remain understandable and contain required links.
- [ ] Verify reset, welcome/confirmation, password-changed, and email-change links reach the correct branded state.
- [ ] Verify disabled email switches suppress only their intended messages.
- [ ] Verify WordPress or WooCommerce emails outside the plugin's ownership remain unaffected.
- [ ] Verify sender identity and reply behavior match the site's mail configuration.
- [ ] Confirm SPF, DKIM, DMARC, SMTP, and mailbox-placement responsibilities with the site owner.
- [ ] Confirm email previews, test sends, logs, and diagnostics do not expose secrets or unrelated customer data.
- [ ] Record representative desktop/mobile mailbox evidence without retaining personal data.

## Phase 4: Security And Integration Acceptance

- [ ] Verify Turnstile success, failure, expiry, replay, and server-side validation behavior.
- [ ] Verify Reoon valid, invalid, disposable, catch-all, role-account, and unknown outcomes against the configured policy.
- [ ] Verify registration can proceed when either configured protection succeeds, according to site policy.
- [ ] Verify provider outages and timeouts fail safely with useful administrator diagnostics.
- [ ] Verify registration, login, reset, resend, and provider rate limits under repeated requests.
- [ ] Verify lockout and resend-throttling feedback does not disclose whether an account exists.
- [ ] Verify manual-review decisions, audit records, and retention behavior where enabled.
- [ ] Verify webhook delivery fires only when the account is created.
- [ ] Verify the webhook includes the intended full user fields without secrets or password data.
- [ ] Verify signing headers, receiver validation, failure metadata, and debug-payload controls.
- [ ] Verify credentials and bypass keys are redacted from diagnostics, exports, logs, and screenshots.
- [ ] Rotate the emergency bypass key and integration test secrets after acceptance.

## Phase 5: Dashboard And WooCommerce Acceptance

- [ ] Verify the custom dashboard disabled and enabled states.
- [ ] Verify customer greeting uses first name with the neutral fallback.
- [ ] Verify custom dashboard links, icons, ordering, role visibility, and new-tab settings.
- [ ] Verify WooCommerce takeover disabled and enabled states.
- [ ] Verify dashboard overview and navigation with no orders and with representative orders.
- [ ] Verify order list, pagination, order details, and available order actions.
- [ ] Verify downloads with empty, available, expired, and limited-download states.
- [ ] Verify billing and shipping address view/edit flows and validation errors.
- [ ] Verify account-details editing, email changes, and password changes.
- [ ] Verify saved payment-method list, add, delete, and default-method flows where supported.
- [ ] Verify delegated WooCommerce notices, forms, nonces, and errors remain functional.
- [ ] Verify unavailable WooCommerce endpoint guidance and recovery links.
- [ ] Verify shop-manager administration remains available while customer wp-admin access remains blocked.
- [ ] Confirm checkout, payment, subscription, membership, or other extension behavior used by the target site is not disrupted.

## Phase 6: Compatibility And Experience Matrix

- [ ] Test the minimum supported WordPress and PHP versions.
- [ ] Test the current supported WordPress, PHP, and WooCommerce versions.
- [ ] Test at least one default WordPress theme and the target site's production theme/builder.
- [ ] Test with representative caching, security, SMTP, and WooCommerce extension combinations.
- [ ] Verify login, registration, reset, logout, dashboard, and WooCommerce routes at mobile and desktop widths.
- [ ] Verify the 800px gateway layout boundary and narrow admin settings layouts.
- [ ] Verify keyboard-only navigation, visible focus, error association, live regions, and password controls.
- [ ] Verify zoom, reflow, high contrast, reduced motion, and resilient color contrast.
- [ ] Verify RTL layout and at least one translated locale across frontend and admin screens.
- [ ] Verify no unexpected remote font, tracking, or third-party request is introduced by default.
- [ ] Verify browser console, PHP logs, diagnostics, and network responses contain no plugin-caused errors.

### Compatibility Matrix

| Environment | WordPress | PHP | WooCommerce | Theme / Builder | Locale / RTL | Status | Evidence |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Minimum supported |  |  |  |  |  | Pending |  |
| Current supported |  |  |  |  |  | Pending |  |
| Target staging |  |  |  |  |  | Pending |  |

## Phase 7: Privacy, Data, And Lifecycle Acceptance

- [ ] Confirm the data inventory for pending registrations, consent, verification, webhooks, diagnostics, and audit records.
- [ ] Verify data minimization and redaction in settings, logs, exports, webhooks, and support evidence.
- [ ] Verify Terms and Privacy consent capture and the site's legal copy ownership.
- [ ] Verify WordPress personal-data exporter output for plugin-owned records.
- [ ] Verify WordPress personal-data eraser behavior and documented exceptions.
- [ ] Verify configured retention cleanup schedules and manual cleanup controls.
- [ ] Verify webhook payload-body storage remains disabled unless debugging is deliberately enabled.
- [ ] Verify disabling or uninstalling the plugin does not remove WordPress users, WooCommerce orders, or unrelated media.
- [ ] Verify uninstall removes only documented plugin-owned options, tables, scheduled hooks, and transient data.
- [ ] Review GDPR-facing documentation with the site owner or qualified adviser where required.

## Phase 8: Documentation And Operations

- [ ] Document installation, update, rollback, activation, and initial configuration.
- [ ] Document frontend-output staging and emergency-disable procedures.
- [ ] Document emergency bypass purpose, storage, rotation, and recovery limitations.
- [ ] Document account-creation, username-generation, email, dashboard, and WooCommerce settings.
- [ ] Document Turnstile, Reoon, webhook, SMTP, and DNS ownership boundaries.
- [ ] Document preview, test-send, diagnostics, import/export, reset, retention, and privacy tools.
- [ ] Document known limitations, extension interactions, and support boundaries.
- [ ] Create a site-owner acceptance checklist using non-technical language.
- [ ] Define supported WordPress, PHP, WooCommerce, browser, and updater versions.
- [ ] Define semantic-versioning, backward-compatibility, migration, and deprecation policy.
- [ ] Define defect severity, support response, security-reporting, and release rollback procedures.
- [ ] Confirm the README, settings reference, hooks reference, changelog, POT, and release notes are current.

## Phase 9: v1.0 Release Candidate

- [ ] Resolve or formally defer every acceptance finding with owner, severity, rationale, and retest evidence.
- [ ] Confirm no critical or high-severity defects remain open.
- [ ] Confirm the repository starts from clean, released, and updater-verified `master`.
- [ ] Create the `release/1.0.0` branch only after acceptance scope is approved.
- [ ] Align plugin header, constant, npm metadata, stable tag, changelog, README, tests, and POT at `1.0.0`.
- [ ] Run production build, PHPCS, full PHPUnit suite, POT generation, npm audit, Composer audit, and diff checks.
- [ ] Build and inspect the runtime ZIP for one root, forward-slash entries, runtime-only contents, aligned metadata, and updater headers.
- [ ] Install the exact release candidate on the acceptance target and repeat critical account, email, integration, and WooCommerce smoke tests.
- [ ] Verify the settings fingerprint, activation state, test-data cleanup, and rollback package.
- [ ] Complete privacy and secret preflight before publication.
- [ ] Obtain explicit release approval.
- [ ] Commit, merge, tag, push, and publish the GitHub release.
- [ ] Inspect the generated public release asset and record its SHA-256 digest.
- [ ] Verify Alynt Plugin Updater discovers and installs public `v1.0.0` from the previous public baseline.
- [ ] Run post-updater browser and runtime smoke tests.
- [ ] Remove temporary users, orders, credentials, helper files, packages, and evidence containing sensitive data.
- [ ] Record release, workflow, public-asset, updater, cleanup, and final-site-state evidence in this plan.

## v1.0 Go / No-Go Gate

Release is approved only when all statements below are true:

- [ ] Every required phase above is complete or has an explicitly approved, non-blocking deferral.
- [ ] No critical or high-severity security, authentication, registration, email, data-loss, privacy, or WooCommerce defect remains open.
- [ ] No standard customer journey unexpectedly reaches an unbranded native WordPress account screen.
- [ ] Frontend Output can be disabled safely, and the emergency bypass behaves exactly as documented.
- [ ] Real email, protection-provider, webhook, and WooCommerce acceptance evidence is recorded.
- [ ] Minimum, current, and target compatibility environments pass their required scenarios.
- [ ] Accessibility, RTL, multilingual, responsive, and theme-compatibility evidence is complete.
- [ ] Privacy, retention, exporter, eraser, uninstall, backup, and rollback behavior is documented and verified.
- [ ] Site-owner documentation and support ownership are ready before publication.
- [ ] The exact public asset passes package inspection and Alynt Plugin Updater verification.
- [ ] Final release approval is recorded.

## Finding Register

| ID | Phase | Severity | Finding | Owner | Decision | Retest Evidence | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
|  |  |  |  |  |  |  |  |

Severity guidance:

- Critical: Authentication bypass, account takeover, secret/password exposure, destructive data loss, or equivalent immediate risk.
- High: Broken primary account journey, unsafe authorization, major privacy defect, or widespread WooCommerce failure.
- Medium: Important but recoverable workflow, compatibility, accessibility, or operational defect.
- Low: Minor presentation, copy, documentation, or narrow edge-case defect.

## Decision Log

| Date | Decision | Rationale | Evidence / Reference |
| --- | --- | --- | --- |
| 2026-07-14 | Keep v1.0 readiness separate from the completed implementation plan. | Preserves the implementation record and gives production acceptance a focused living roadmap. | `docs/IMPLEMENTATION_PLAN.md` completed at `v0.1.97`. |

## Progress Notes

- Created this production-readiness roadmap from the released and updater-verified `v0.1.97` baseline.
- The completed feature implementation history remains in `docs/IMPLEMENTATION_PLAN.md`.
- The next natural step is Phase 0: confirm a production-like staging target, operating mode, restore point, baseline versions, test identities, and evidence/cleanup rules.
