# Production Rollout Playbook

Use this playbook when Alynt Account Gateway is ready to move from an accepted staging configuration to a specific production WordPress site. Complete a fresh copy for each production rollout.

This document is an operational checklist. It does not authorize production changes. Every production write, deployment, settings change, cache purge, or rollback requires the site-specific approval defined by the operating team.

See `OPERATIONS.md` for detailed configuration, update, emergency-access, support-boundary, and rollback guidance.

## Rollout Record

Complete before scheduling work:

| Field | Value |
| --- | --- |
| Production site | Pending |
| Staging site | Pending |
| Planned plugin version | Pending |
| Current production version | Pending |
| Release URL | Pending |
| Release ZIP SHA-256 | Pending |
| Planned date and maintenance window | Pending |
| Rollout operator | Pending |
| Approval owner | Pending |
| Technical observer | Pending |
| Rollback decision owner | Pending |
| File restore point | Pending |
| Database restore point | Pending |
| Previous plugin ZIP and SHA-256 | Pending |
| Settings export or fingerprint | Pending |
| Active-plugin state or fingerprint | Pending |
| Cache/CDN owners | Pending |
| SMTP owner | Pending |
| Support contact during monitoring | Pending |

Do not record passwords, API keys, cookies, webhook secrets, emergency bypass keys, private customer data, or production database credentials in this file.

## Scope And Change Freeze

- [ ] Confirm the exact production site, WordPress path, and deployment method.
- [ ] Confirm the exact release tag and public/private release asset.
- [ ] Record all plugin, theme, WordPress, PHP, WooCommerce, and relevant extension versions.
- [ ] Freeze unrelated production changes for the rollout window.
- [ ] List overlapping login, registration, force-login, security, SMTP, caching, WooCommerce account, and redirect plugins.
- [ ] Confirm which system will own public login and account routes after launch.
- [ ] Record any approved exceptions or deferred findings.
- [ ] Confirm production HBF or any other site is outside scope unless named explicitly in the completed rollout record.

## Required Approval Gates

### Gate A: Preflight Approval

Required before changing production:

- [ ] Target and deployment method confirmed.
- [ ] Restore points completed and independently verified.
- [ ] Previous plugin ZIP and rollback procedure available.
- [ ] Staging acceptance completed against the intended production release.
- [ ] Emergency administrator access tested.
- [ ] Monitoring and support coverage confirmed.
- [ ] Approval owner authorizes the production change.

Decision: `GO / NO-GO`

Approved by: `Pending`

Date and time: `Pending`

### Gate B: Public Handover Approval

Required before enabling Frontend Output or transferring public account routes:

- [ ] Installed production version and file integrity verified.
- [ ] Settings and active-plugin state preserved.
- [ ] Administrator access remains available.
- [ ] Preview-only and non-public checks pass.
- [ ] Cache behavior is understood.
- [ ] Approval owner authorizes public handover.

Decision: `GO / NO-GO`

Approved by: `Pending`

Date and time: `Pending`

### Gate C: Closeout Approval

Required before ending the maintenance window:

- [ ] Critical account journeys pass.
- [ ] No critical or high-severity defect is open.
- [ ] Monitoring signals are normal.
- [ ] Temporary credentials, users, orders, files, and evidence are removed.
- [ ] Rollback is no longer required.

Decision: `ACCEPT / ROLLBACK / EXTEND MONITORING`

Approved by: `Pending`

Date and time: `Pending`

## Preflight

### Release And Environment

- [ ] Verify the release asset checksum and runtime-only package contents.
- [ ] Confirm plugin metadata matches the intended version.
- [ ] Confirm WordPress, PHP, WooCommerce, browser, and updater compatibility.
- [ ] Confirm sufficient disk space and writable WordPress plugin/update directories.
- [ ] Confirm production plugin ownership and permissions match the site user.
- [ ] Capture current plugin status, active-plugin order, settings fingerprint, database schema version, and scheduled events.
- [ ] Confirm Alynt Plugin Updater detects the intended release when that update path is used.

### Recovery

- [ ] Create file and database restore points immediately before rollout.
- [ ] Verify restore-point timestamps and scope.
- [ ] Verify the previous plugin ZIP can be installed.
- [ ] Confirm WP-CLI, hosting control panel, SFTP/file manager, or another emergency deactivation path.
- [ ] Test the emergency bypass without recording its key in evidence.
- [ ] Confirm at least one administrator can reach native login and wp-admin.

### Configuration

- [ ] Keep Frontend Output disabled during installation and configuration checks.
- [ ] Verify login path, account action base, after-login redirect, and logout behavior.
- [ ] Verify Terms and Privacy paths.
- [ ] Confirm public account creation is intentionally enabled or disabled.
- [ ] Review branding, typography, colors, images, logo sizing, and screen copy.
- [ ] Review dashboard modules, custom links, navigation menus, role visibility, and WooCommerce takeover.
- [ ] Review email templates, sender details, disable switches, and test recipient.
- [ ] Review retention, diagnostics, privacy exporter/eraser, and uninstall expectations.
- [ ] Confirm secrets are present where required without exposing them in evidence.

### External Services

- [ ] Send account-email tests to a real mailbox and review desktop/mobile rendering.
- [ ] Confirm SMTP acceptance, SPF, DKIM, DMARC, and bounce ownership.
- [ ] Test Turnstile server-side verification when enabled.
- [ ] Test Reoon behavior for accepted, rejected, catch-all, role-account, and unknown responses as configured.
- [ ] Test the account-created webhook receiver and HMAC verification when enabled.
- [ ] Confirm third-party data-processing and legal obligations have been reviewed.

## Installation Or Update

Record timestamps and results for every action.

1. Put support and monitoring owners on notice.
2. Confirm Gate A approval.
3. Install or update the exact approved release through Alynt Plugin Updater or the approved release ZIP.
4. Verify the installed version, active status, active-plugin order, settings fingerprint, schema version, and scheduled events.
5. Confirm no stale update offer remains.
6. Confirm the plugin directory contains runtime files only and has correct ownership.
7. Clear only the caches required for validation.
8. Keep Frontend Output disabled until the private checks below pass.

## Private Acceptance Before Handover

- [ ] Open every Gateway Screen Preview state.
- [ ] Preview every account-email template.
- [ ] Send required email tests.
- [ ] Verify diagnostics and logs contain no unexpected critical errors.
- [ ] Confirm wp-admin access for administrators and shop managers.
- [ ] Confirm customer wp-admin blocking is inactive while Frontend Output is disabled.
- [ ] Confirm import/export, retention, privacy, and webhook tools are in the expected state.
- [ ] Confirm WooCommerce endpoints and payment-provider dependencies are available.
- [ ] Confirm no overlapping plugin is already claiming the intended public routes.
- [ ] Obtain Gate B approval.

## Public Handover

1. Disable or reconfigure overlapping route owners according to the approved plan.
2. Enable Frontend Output.
3. Clear relevant page, object, CDN, and browser caches.
4. Run the acceptance matrix immediately.

## Production Acceptance Matrix

Record URL, role, browser/device, expected result, actual result, timestamp, and evidence reference.

### Anonymous

- [ ] Login screen.
- [ ] Lost-password request.
- [ ] Reset-password link and set-password form.
- [ ] Registration disabled state or enabled registration flow.
- [ ] Terms and Privacy links.
- [ ] Invalid, expired, reused, and malformed confirmation/reset links.
- [ ] Logout confirmation entry behavior.
- [ ] No standard journey reaches an unbranded native WordPress screen.

### Customer

- [ ] Email-only login.
- [ ] After-login redirect.
- [ ] Dashboard overview.
- [ ] Orders and order details.
- [ ] Downloads.
- [ ] Billing and shipping addresses.
- [ ] Payment methods.
- [ ] Account details and password change.
- [ ] Custom dashboard links, role visibility, footer menu, and off-canvas navigation.
- [ ] Logout and post-logout destination.
- [ ] wp-admin blocking and admin-bar hiding.

### Staff

- [ ] Administrator login and wp-admin access.
- [ ] Shop-manager login, wp-admin access, and admin toolbar.
- [ ] Emergency bypass reaches native login without authenticating the visitor.
- [ ] Settings, preview, diagnostics, retention, webhook, and email tools remain accessible to authorized roles only.

### Integrations

- [ ] Turnstile success, failure, expiry, and server-side rejection.
- [ ] Reoon configured policy behavior.
- [ ] Welcome/confirmation email.
- [ ] Password-reset email.
- [ ] Password-changed email when enabled.
- [ ] Email-change confirmation email when enabled.
- [ ] Account-created webhook delivery and signature.
- [ ] WooCommerce extension-added endpoints and notices.

### Responsive And Accessible Behavior

- [ ] Keyboard-only operation and visible focus.
- [ ] Screen-reader labels, errors, statuses, dialogs, and menu toggles.
- [ ] Mobile at 320px and representative phone sizes.
- [ ] Tablet and desktop layouts.
- [ ] Reduced-motion behavior.
- [ ] RTL and enabled production languages.
- [ ] Text zoom/reflow and contrast.

## Monitoring Window

Recommended minimum: one staffed business cycle after handover, adjusted for traffic and risk.

Monitor:

- Authentication, registration, password-reset, and email-change failures.
- PHP errors, WordPress debug logs, web-server errors, and plugin diagnostics.
- SMTP delivery, bounces, and mailbox complaints.
- Turnstile and Reoon failures or latency.
- Webhook failures, retries, and receiver errors.
- WooCommerce account, payment-method, address, order, and download failures.
- Support reports, unexpected redirects, and native WordPress screen exposure.
- Cache/CDN anomalies and elevated response times.

Do not enable debug payload logging merely for routine monitoring. If temporarily enabled for diagnosis, disable it and remove retained payloads when the investigation ends.

## Rollback Triggers

Rollback or disable Frontend Output immediately for:

- Authentication bypass, account takeover risk, or unauthorized wp-admin access.
- Administrator lockout without verified emergency recovery.
- Customers broadly unable to log in, reset passwords, register, or reach their accounts.
- Incorrect account creation, password, email, privacy, or webhook behavior with material user impact.
- Destructive data loss or corruption.
- Widespread WooCommerce account or payment-method failure.
- Sensitive data, secrets, or debug payload exposure.

The rollback owner may choose a narrower mitigation for an isolated, understood defect with a safe workaround.

## Rollback Procedure

1. Disable Frontend Output when wp-admin remains available.
2. If necessary, deactivate Alynt Account Gateway through wp-admin, WP-CLI, or the approved hosting/file-access path.
3. Restore the previous plugin ZIP.
4. Restore the database only when settings, schema, or data changed and the rollback owner approves it.
5. Restore the recorded active-plugin state when replacement changed ordering.
6. Clear relevant caches.
7. Verify native login, password reset, wp-admin access, WooCommerce My Account, and customer support paths.
8. Preserve sanitized diagnostics and timestamps needed for investigation.
9. Record the rollback reason, impact, mitigation, and corrective-release owner.

Do not uninstall the plugin as a rollback method unless destructive plugin-data removal is explicitly intended.

## Cleanup And Closeout

- [ ] Remove temporary users, registrations, orders, downloads, webhooks, files, packages, and credentials.
- [ ] Remove temporary bypass sharing and rotate the bypass key if it was disclosed.
- [ ] Disable temporary diagnostics or debug payload logging.
- [ ] Confirm caches are in their normal production state.
- [ ] Record final versions, settings fingerprint, active-plugin state, and site-health result.
- [ ] Record unresolved low/medium findings with owners and due dates.
- [ ] Obtain Gate C approval.
- [ ] Store the completed rollout record in the approved private operational location.
- [ ] Update release and support documentation without including secrets or customer data.

## Evidence Log

| Time | Action or check | Expected | Actual | Result | Evidence reference | Operator |
| --- | --- | --- | --- | --- | --- | --- |
| Pending | Pending | Pending | Pending | Pending | Pending | Pending |

## Findings And Decisions

| ID | Severity | Finding | Owner | Decision | Retest evidence | Status |
| --- | --- | --- | --- | --- | --- | --- |
| Pending | Pending | Pending | Pending | Pending | Pending | Pending |
