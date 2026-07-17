# Privacy And GDPR Review Guide

This guide summarizes the privacy-facing behavior of Alynt Account Gateway for site-owner review. It is an operational documentation aid, not legal advice. Site owners remain responsible for deciding lawful bases, privacy notice wording, processor contracts, data-transfer safeguards, retention policy, and data-subject request handling for their own sites.

Official reference points:

- European Commission: controller and processor roles: https://commission.europa.eu/law/law-topic/data-protection/information-business-and-organisations/obligations/controllerprocessor/what-data-controller-or-data-processor_en
- European Commission: using processors on an organisation's behalf: https://commission.europa.eu/law/law-topic/data-protection/information-business-and-organisations/obligations/controllerprocessor/can-someone-else-process-data-my-organisations-behalf_en
- European Commission: individual GDPR rights: https://commission.europa.eu/law/law-topic/data-protection/information-individuals_en
- ICO: storage limitation and retention guidance: https://ico.org.uk/for-organisations/uk-gdpr-guidance-and-resources/data-protection-principles/a-guide-to-the-data-protection-principles/storage-limitation/
- ICO: right to erasure guidance: https://ico.org.uk/for-organisations/uk-gdpr-guidance-and-resources/individual-rights/individual-rights/right-to-erasure/

## Role Boundaries

The site owner normally determines why and how account data is collected, used, retained, and shared. In GDPR terms, that generally makes the site owner the controller for account-gateway use.

Alynt Account Gateway is installed software. It stores data in the WordPress database controlled by the site owner. The plugin does not make Alynt a processor merely by being installed. Alynt may become involved in processing only if support, hosting, managed services, remote logging, repository access, or other services involve access to personal data.

Third-party services configured by the site owner may act as processors or independent controllers depending on their terms and the site owner's use. Review at least:

- Web host and database host.
- SMTP or transactional email provider.
- Cloudflare Turnstile.
- Reoon Email Verifier.
- Webhook receiver or automation platform.
- WooCommerce payment, shipping, tax, analytics, CRM, and fulfillment integrations.
- Backup, security, monitoring, logging, and analytics tools.

## Default Privacy-Safe Posture

Fresh installs are intentionally conservative:

- Frontend Output is disabled by default.
- Public registration is disabled by default.
- Cloudflare Turnstile, Reoon, and webhooks are unconfigured by default.
- Debug payload logging is disabled by default.
- Diagnostics are disabled by default.
- The registration flow creates no WordPress user until email confirmation and password setup complete.
- Registration confirmation tokens are stored as one-way hashes; raw tokens exist only for link delivery.
- Registration consent records do not store IP addresses by default.
- Portable settings exports omit secret credentials, email-recipient settings, and site-specific media IDs.

## Plugin-Owned Data Inventory

| Store | Typical data | Default retention / removal |
| --- | --- | --- |
| `alynt_ag_settings` option | Routes, branding references, email templates, provider credentials, webhook settings, retention settings | Kept while installed; removed by uninstall |
| `alynt_ag_db_version` option | Plugin schema version | Kept while installed; removed by uninstall |
| Pending registrations table | Email, first name, last name, optional created user ID, token hash, status, timestamps | Expired rows removed by retention cleanup; table removed by uninstall |
| Consent records table | Email, user ID when available, Terms path, Privacy path, context, plugin version, settings hash, timestamp | Default 365 days; table removed by uninstall |
| Verification logs table | Email, provider, result/status, blocked flag, optional review decision, timestamps | Default 30 days; table removed by uninstall |
| Webhook logs table | Event, user ID, destination host, response metadata, retry count, sanitized error, optional debug payload | Successful rows default 7 days; failed rows default 30 days; table removed by uninstall |
| Audit logs table | Acting user ID, action, recursively redacted context, timestamp | Default 180 days; table removed by uninstall |
| Diagnostics logs table | Severity, category, event code, summary, recursively redacted context, correlation ID, timestamp | Disabled by default; default 30 days when enabled; table removed by uninstall |
| Rate-limit transients | HMAC-derived bucket keys and aggregate lockout metadata | Expires by action window; plugin-owned transient rows removed by uninstall |

WordPress users, WooCommerce customers, WooCommerce orders, payment data, shipping data, media-library files, and non-plugin options are owned by WordPress, WooCommerce, or other site components. They are not removed by disabling, deactivating, or uninstalling Alynt Account Gateway.

## Privacy Notice Checklist

Before enabling public frontend output or registration, review whether the site privacy notice explains:

- What account data is collected during registration, login, password reset, dashboard use, and WooCommerce account management.
- That registration may require email confirmation before the WordPress user account is created.
- The purposes for processing account data, security logs, verification logs, webhook deliveries, diagnostics, and consent records.
- Which third-party services may receive account or verification data, including SMTP, Turnstile, Reoon, webhook receivers, payment/shipping providers, and hosting/security services.
- How long pending registrations, consent records, verification logs, webhook logs, audit logs, diagnostics, and WooCommerce/account records are retained.
- Which rights users can exercise and how they can contact the site owner.
- Any international-transfer or processor-contract information required for the configured providers.

## Data-Subject Request Support

Alynt Account Gateway registers WordPress personal-data exporter and eraser callbacks for plugin-owned records.

The exporter can return plugin-owned pending-registration, consent, verification, webhook metadata, and audit records associated with the requested email or user ID.

The eraser removes plugin-owned pending-registration, consent, verification, webhook metadata, and audit records associated with the requested email or user ID where those records are owned by the plugin.

The eraser intentionally does not delete:

- WordPress users.
- WooCommerce orders.
- WooCommerce customer/account records.
- Media-library files.
- Records owned by payment, shipping, email, analytics, security, backup, or webhook receiver systems.

Handle those records through WordPress, WooCommerce, the relevant plugin/service, or the service provider's own data-subject request process.

## Retention Review

Default retention values are:

- Successful webhook metadata: 7 days.
- Failed webhook metadata: 30 days.
- Verification logs: 30 days.
- Diagnostics logs: 30 days when diagnostics are enabled.
- Consent records: 365 days.
- Audit logs: 180 days.

Site owners should adjust retention to match their purposes, legal obligations, fraud-prevention needs, customer-support requirements, and local policy. The storage-limitation principle requires being able to justify why personal data is retained for the chosen period.

## Third-Party Sharing Review

When integrations are enabled:

- Cloudflare Turnstile receives verification data needed to validate challenges.
- Reoon receives email addresses submitted for email verification.
- SMTP providers receive account email content and recipients.
- Webhook receivers receive account-created payloads when account-created webhooks are configured.
- WooCommerce extensions may receive customer, order, payment, shipping, tax, analytics, CRM, or fulfillment data depending on the enabled extensions.

Keep webhook debug payload logging disabled unless actively diagnosing a delivery issue. If debug payload logging is enabled, treat stored webhook payloads as potentially personal data and reduce retention accordingly.

## Operational Checklist

Before public launch:

- Confirm Frontend Output is still disabled while configuring.
- Set Terms and Privacy paths to site-owned legal pages.
- Confirm registration is intentionally enabled or disabled.
- Configure Turnstile and/or Reoon only after provider terms and data-processing responsibilities are reviewed.
- Configure SMTP and webhook receivers only after processor/recipient responsibilities are reviewed.
- Review retention settings on the Privacy & Data tab.
- Send test emails only to approved test recipients.
- Keep evidence redacted: no secrets, cookies, full webhook payloads, private customer data, or full personal email bodies.

After launch:

- Periodically review retention windows and provider list.
- Run WordPress exporter/eraser workflows when handling data-subject requests.
- Clear diagnostics after support investigations.
- Disable debug payload logging after webhook troubleshooting.
- Revisit privacy notice wording when enabling registration, Turnstile, Reoon, webhooks, WooCommerce takeover, diagnostics, or new WooCommerce extensions.
