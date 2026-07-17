# Operations Guide

This guide is for site owners and administrators operating Alynt Account Gateway on WordPress sites. It focuses on safe installation, updates, rollback, emergency recovery, support boundaries, and launch acceptance.

## Supported Environment

Minimum supported runtime:

- WordPress 6.0 or newer.
- PHP 7.4 or newer.
- WooCommerce is optional. WooCommerce account takeover requires WooCommerce to be installed and active.
- Alynt Plugin Updater is the supported private-update path for Alynt-distributed releases.

Recommended runtime for production:

- A currently supported WordPress release.
- PHP 8.1 or newer where the host and plugin stack support it.
- Current stable WooCommerce when WooCommerce takeover is enabled.
- A staging site that mirrors production plugins, theme, caching, SMTP, security, and WooCommerce extensions.
- Modern evergreen browsers for admin configuration and customer-facing account screens.

## Installation

1. Create a file and database restore point before installing on an existing site.
2. Download the release ZIP from the private Alynt distribution channel or GitHub release asset.
3. In WordPress admin, go to Plugins -> Add New -> Upload Plugin.
4. Upload the ZIP and activate Alynt Account Gateway.
5. Open Settings -> Account Gateway.
6. Keep Frontend Output disabled while configuring.
7. Configure routes, branding, screen copy, legal paths, email templates, retention, integrations, dashboard, and WooCommerce takeover.
8. Use Gateway Screen Preview and email test-send tools before enabling public output.
9. Enable Frontend Output only after the site-owner acceptance checklist passes.

Fresh installs are intentionally conservative. Frontend Output and public registration are disabled by default.

## Initial Configuration Order

Use this order on a new site:

1. General: confirm Frontend Output remains disabled.
2. URLs & Redirects: set login path, account action base, after-login redirect, and confirm emergency access.
3. Branding & Layout: upload logo, set logo width, choose background image, colors, buttons, and typography.
4. Screen Copy: review every login, registration, password, logout, disabled, and invalid-link message.
5. Registration: decide whether public account creation is enabled. Confirm username format, token expiry, Terms path, and Privacy path.
6. Security & Spam: configure Turnstile and/or Reoon before enabling public registration.
7. Emails: review rich templates, sender expectations, disable switches, and test recipient. Send test emails.
8. Dashboard: configure dashboard state and custom links.
9. WooCommerce: enable takeover only after the WooCommerce account page and endpoints are ready.
10. Webhooks: configure account-created webhook URL and signing secret only when the receiver is ready.
11. Privacy & Data: review retention windows and privacy/exporter/eraser expectations.
12. Advanced / Tools: review diagnostics, import/export, reset, and compatibility warnings.

## Updating

Preferred update path:

1. Confirm the current site has a recent restore point.
2. Review the release notes and any migration notes.
3. Update on staging first using Alynt Plugin Updater or the release ZIP.
4. Verify the installed plugin version.
5. Confirm settings remained intact.
6. Smoke test `/login/`, `/account?action=lostpassword`, `/account?action=register`, `/account?action=logout`, `/my-account/`, and any enabled WooCommerce account endpoints.
7. Confirm no native WordPress account screen appears in normal customer journeys.
8. Repeat the update on production only after staging passes.

If an update changes account routes, email behavior, dashboard rendering, WooCommerce delegation, privacy behavior, or integration behavior, perform a broader staging acceptance pass before production.

## Rollback

Use rollback when an update causes a customer-facing defect, authentication issue, account email issue, WooCommerce account issue, privacy issue, or integration failure.

Preferred rollback order:

1. Disable Frontend Output if wp-admin is reachable.
2. If needed, deactivate Alynt Account Gateway to restore native WordPress behavior.
3. Restore the previous plugin ZIP through Plugins -> Add New -> Upload Plugin if the site remains stable.
4. If database schema or settings may have changed, restore the pre-update file and database restore point.
5. Clear page cache, object cache, CDN cache, and browser cache where applicable.
6. Recheck login, password reset, registration, dashboard, WooCommerce account endpoints, email sending, and webhooks.

Do not uninstall as a rollback method unless the intent is to remove plugin-owned settings and tables. Uninstall is destructive for plugin-owned data by design.

## Frontend Output Staging

Frontend Output is the master public-output switch. While disabled:

- Public gateway routes do not replace native/account behavior.
- Settings can be configured safely before public launch.
- Gateway Screen Preview can show saved screen states without enabling public output.
- Role policies and customer wp-admin blocking are not applied by Alynt Account Gateway.

Recommended staging workflow:

1. Configure with Frontend Output disabled.
2. Use preview tools and email test-send.
3. Confirm emergency bypass access.
4. Confirm overlapping login/account plugins are deactivated or configured not to conflict.
5. Enable Frontend Output on staging.
6. Run public route, registration, reset-password, logout, dashboard, WooCommerce, Turnstile/Reoon, email, webhook, and privacy smoke tests.
7. Only then schedule production handover.

## Emergency Disable

If customers cannot log in, reset passwords, register, or access their account area:

1. Try Settings -> Account Gateway -> General -> disable Frontend Output.
2. If the settings screen is unavailable, deactivate Alynt Account Gateway from Plugins.
3. If wp-admin redirects prevent access, use the emergency bypass URL to reach native `wp-login.php`.
4. If wp-admin is unavailable, use WP-CLI or hosting file manager/SFTP to deactivate the plugin.
5. Clear relevant caches.
6. Test native login and password reset.
7. Record the symptom, exact URL, browser, user role, timestamp, and recent changes before re-enabling.

Deactivation preserves plugin settings and custom tables. Uninstall removes plugin-owned settings and tables.

## Emergency Bypass

The emergency bypass is a secret query parameter for native login access:

```text
wp-login.php?alynt_ag_bypass={key}
```

Purpose:

- Bypass the branded login redirect.
- Show native WordPress login.
- Help administrators regain access when frontend routing is misconfigured.

Limitations:

- It does not authenticate anyone.
- It does not grant admin access.
- It does not bypass WordPress passwords, roles, 2FA, hosting firewalls, security plugins, or IP restrictions.
- It should not be shared in tickets, screenshots, chat logs, or public docs.

Rotation:

- Rotate the key after sharing it with a support person.
- Rotate the key after a suspected leak.
- Store it only in the site owner's password manager or secure operational vault.

## Account Creation And Username Policy

Public account creation is disabled by default.

When enabled:

- Registration collects first name, last name, email, and Terms/Privacy acceptance.
- A pending registration is created before a WordPress user exists.
- The user must confirm email and set a valid password before the WordPress user is created.
- Passwords must satisfy the configured strength policy.
- Usernames are generated from the configured username format.
- Customers log in with email.

Use Terms and Privacy paths that belong to the site. Do not rely on the plugin to create or approve legal copy.

## Email Operations

The Emails tab provides rich-text templates, preview, and test-send.

Operational notes:

- Test-send uses the configured test recipient.
- Subjects and preheaders are plain text.
- Email body templates support WordPress-safe formatting through the Visual/Text editor.
- Password reset, password changed, welcome/account-created, and email-change templates should be mailbox-tested before launch.
- Some WordPress profile email-change requests are plain text because WordPress exposes only that body through core filters.
- SMTP delivery, DNS authentication, SPF, DKIM, DMARC, bounce handling, and mailbox reputation are owned by the site and SMTP provider.

## Turnstile, Reoon, Webhook, SMTP, And DNS Boundaries

Alynt Account Gateway integrates with services configured by the site owner. It does not replace provider setup, terms review, DNS configuration, or data-processing agreements.

Cloudflare Turnstile:

- Requires a site key and secret key.
- Tokens are verified server-side when Turnstile is configured.
- The site owner is responsible for Cloudflare account configuration and provider terms.

Reoon Email Verifier:

- Requires a Reoon API key.
- Email addresses submitted during registration may be sent to Reoon when configured.
- The site owner chooses whether flagged statuses are allowed or blocked.

Webhooks:

- Account-created webhooks fire only after a confirmed registration creates the WordPress user.
- Payloads include account-created user and site fields.
- Optional HMAC signing should be configured when the receiver supports signature verification.
- Delivery logs store metadata by default. Payload bodies are stored only when Debug Payload Logging is enabled.

SMTP and DNS:

- SMTP delivery is owned by the site's mail stack.
- DNS authentication records are owned by the domain/DNS operator.
- The plugin can render and request account emails, but it does not guarantee inbox placement.

## Dashboard And WooCommerce

The custom dashboard is optional.

When WooCommerce takeover is enabled:

- Standard WooCommerce account endpoints are delegated to WooCommerce.
- Orders, downloads, addresses, account details, payment methods, logout, and plugin-added menu endpoints should be tested on staging.
- Payment, shipping, tax, subscription, membership, points, affiliate, download, and CRM extensions may add account endpoints or notices that require staging review.
- Checkout remains WooCommerce-owned.

Known limitation:

- Standalone add-payment-method behavior depends on the enabled payment gateway support for that flow.

## Tools And Data Operations

Gateway Screen Preview:

- Lets administrators preview saved account screens while Frontend Output is disabled.
- Is admin-only and nonce-protected.

Email preview and test-send:

- Shows rendered HTML/plain output.
- Sends test messages to the configured test recipient.

Diagnostics:

- Disabled by default.
- Redacts context before storage.
- Can be exported or cleared by administrators.
- Should be cleared after support investigations when no longer needed.

Import/export and reset tools:

- Portable exports omit secret credentials, test email recipient, and site-specific media IDs.
- Reset tools should be used only after saving a configuration backup.

Retention and privacy tools:

- Retention cleanup removes expired plugin-owned logs and records.
- WordPress exporter/eraser callbacks cover plugin-owned records.
- WordPress users, WooCommerce records, media, and third-party provider records are handled by their owning systems.

## Site-Owner Acceptance Checklist

Before enabling production Frontend Output:

- A restore point exists and was verified.
- Emergency bypass works and is stored securely.
- Login, lost password, set password, registration, logout, dashboard, and WooCommerce account screens were tested on staging.
- No standard customer journey reaches an unbranded native WordPress account screen.
- Terms and Privacy links are correct.
- Account creation is intentionally enabled or disabled.
- Turnstile and/or Reoon are configured when public registration is enabled.
- Account emails were previewed and test-sent to a real mailbox.
- SMTP/DNS deliverability is accepted by the site owner.
- Webhook receiver and signing are tested if webhooks are enabled.
- Dashboard custom links have correct labels, URLs, icons, ordering, roles, and new-tab behavior.
- WooCommerce orders, downloads, addresses, account details, and payment methods work when takeover is enabled.
- Retention settings are reviewed.
- Privacy/GDPR review guide has been reviewed with a qualified adviser where required.
- Rollback steps are understood by the person performing launch.

## Known Extension Interactions

Review and test any plugin that controls:

- Login, registration, password reset, email verification, or `wp-login.php` redirects.
- Force-login or private-site behavior.
- Account pages, WooCommerce My Account endpoints, or dashboard redirects.
- SMTP, account emails, transactional emails, or template rendering.
- Security firewalls, 2FA, rate limits, bot protection, or CAPTCHA.
- Caching, CDN, page optimization, script deferral, or HTML minification.
- WooCommerce payment, shipping, tax, subscriptions, memberships, downloads, or CRM workflows.

Only one plugin should own public account routes at a time.

## Versioning And Compatibility Policy

Before `1.0.0`, `0.x` releases may include product refinements and compatibility corrections. Each public release should still preserve existing settings and document behavior changes.

After `1.0.0`:

- Patch releases fix defects and low-risk compatibility issues.
- Minor releases add backward-compatible features or settings.
- Major releases may remove deprecated behavior or require migration steps.

Deprecations should be documented before removal whenever practical. Settings migrations should preserve existing site behavior by default.

## Defect Severity And Support Response

Critical:

- Authentication bypass.
- Admin access exposure.
- Password reset or registration account-takeover risk.
- Sensitive data exposure.
- Destructive data-loss behavior.
- Complete lockout with no emergency access.

High:

- Customers cannot log in, reset passwords, register, or access WooCommerce account features.
- Account emails or webhooks fail in a way that blocks core account flows.
- Privacy exporter/eraser behavior is materially incorrect.

Medium:

- Important settings, preview, diagnostics, or dashboard behavior fails but has a workaround.
- A supported integration is degraded.

Low:

- Copy, layout, accessibility polish, non-blocking admin UI issues, or documentation gaps.

Report security issues privately. Do not include secrets, passwords, cookies, bypass keys, full webhook payloads, or private customer data in issue reports.

## Release Rollback Procedure

For release managers:

1. Confirm the current release tag, ZIP SHA-256, installed version, settings hash, active-plugin state, and restore point.
2. If the release is already public, decide whether to unpublish, supersede, or publish a corrective release.
3. Restore the prior release ZIP and database snapshot if needed.
4. Verify public account routes, emergency bypass, email, registration, dashboard, WooCommerce, privacy tools, and updater state.
5. Document the rollback reason, affected versions, mitigation, and next corrective release plan.
