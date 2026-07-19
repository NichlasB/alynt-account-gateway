# Settings Reference

Settings are stored in the `alynt_ag_settings` option and defined in `ALYNT_AG_Settings_Schema`.

## Defaults

- `frontend_enabled`: `false`
- `login_path`: `/login`
- `account_action_base`: `/account`
- `after_login_redirect`: `/my-account/`
- `administrator_after_login_redirect`: `/wp-admin/`
- `shop_manager_after_login_redirect`: `/wp-admin/`

Role-aware login redirects are used only when the login request does not contain a safe explicit internal `redirect_to` destination. Administrators and WooCommerce shop managers use their dedicated paths; customers and other roles use `after_login_redirect`. External and authentication-surface destinations remain rejected.
- `emergency_bypass_key`: generated secret on activation/default creation
- `registration_enabled`: `false`
- `registration_token_hours`: `24`
- `username_format`: `@User_{first_name}_{last_name}`
- `terms_path`: `/legal/terms/`
- `privacy_path`: `/legal/privacy/`
- `brand_logo_id`: `0`
- `brand_logo_max_width`: `220`
- `background_image_id`: `0`
- `primary_color`: `#3B5249`
- `accent_color`: `#E1CDB5`
- `text_color`: `#281408`
- `page_background_color`: `#EAE4D6`
- `surface_color`: `#FFFFFF`
- `error_color`: `#B3492E`
- `button_background_color`: `#3B5249`
- `button_text_color`: `#ffffff`
- `heading_font_family`: `Georgia, serif`
- `body_font_family`: `-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`
- `login_intro_text`: `Welcome back. Log in to manage your orders and account details.`
- `register_intro_text`: configurable registration instruction text
- `lostpassword_intro_text`: configurable lost-password instruction text
- `setpassword_intro_text`: configurable set-password instruction text
- `logout_intro_text`: configurable logout confirmation instruction text
- `registration_disabled_text`: configurable disabled-registration text
- `invalid_link_text`: configurable invalid/expired-link text
- `protection_mode`: `turnstile_or_reoon`
- `turnstile_site_key`: empty
- `turnstile_secret_key`: empty
- `reoon_api_key`: empty
- `reoon_mode`: `quick`
- `registration_rate_limit_count`: `5`
- `registration_rate_limit_window`: `60`
- `resend_confirmation_rate_limit_count`: `5`
- `resend_confirmation_rate_limit_window`: `60`
- `login_rate_limit_count`: `10`
- `login_rate_limit_window`: `15`
- `lostpassword_rate_limit_count`: `5`
- `lostpassword_rate_limit_window`: `60`
- `email_registration_confirmation_subject`: tokenized subject for registration confirmation email
- `email_registration_confirmation_preheader`: tokenized preheader for registration confirmation email
- `email_registration_confirmation_body`: tokenized body for registration confirmation email
- `email_password_reset_subject`: tokenized subject for password reset email
- `email_password_reset_preheader`: tokenized preheader for password reset email
- `email_password_reset_body`: tokenized body for password reset email
- `email_password_changed_disabled`: `false`
- `email_password_changed_subject`: tokenized subject for password changed email
- `email_password_changed_preheader`: tokenized preheader for password changed email
- `email_password_changed_body`: tokenized body for password changed email
- `email_new_user_welcome_disabled`: `false`
- `email_new_user_welcome_subject`: tokenized subject for account-created welcome email
- `email_new_user_welcome_preheader`: tokenized preheader for account-created welcome email
- `email_new_user_welcome_body`: tokenized body for account-created welcome email
- `email_change_confirmation_disabled`: `false`
- `email_change_confirmation_subject`: tokenized subject for email-change notification/confirmation email
- `email_change_confirmation_preheader`: tokenized preheader for email-change notification/confirmation email
- `email_change_confirmation_body`: tokenized body for email-change notification/confirmation email
- `email_test_recipient`: empty
- `dashboard_enabled`: `false`
- `dashboard_custom_links`: `[]`
- `woocommerce_takeover`: `false`
- `account_created_webhook`: empty
- `webhook_signing_secret`: empty
- `debug_payload_logging`: `false`
- `diagnostics_enabled`: `false`
- `diagnostics_min_level`: `warning`
- `diagnostics_retention`: `30`
- `success_log_retention`: `7`
- `failed_log_retention`: `30`
- `verification_log_retention`: `30`
- `consent_record_retention`: `365`
- `audit_log_retention`: `180`

## Notes

Secrets must never be committed to documentation. Store Turnstile, Reoon, webhook, and bypass secrets only in plugin settings.

The General tab includes a read-only Setup Readiness panel. It summarizes whether frontend output, gateway URLs, emergency access, branding, public registration, email testing, dashboard configuration, WooCommerce takeover, webhook signing, and retention windows are ready, need review, or need action before public launch.

Review `docs/OPERATIONS.md` for the recommended install, update, rollback, emergency-disable, staging, support-boundary, and production launch process.

Email templates support token placeholders such as `{{site_name}}`, `{{first_name}}`, `{{last_name}}`, `{{user_email}}`, `{{confirmation_url}}`, `{{reset_url}}`, `{{change_email_url}}`, and `{{expiry_hours}}`.

All five email body fields use the WordPress Visual and Text editor. Supported post-safe formatting includes headings, bold and italic emphasis, links, blockquotes, and ordered or unordered lists. Executable or unsafe markup is removed during settings sanitization and again before HTML rendering. Media uploads and arbitrary edits to the branded email wrapper are not exposed. Subjects and preheaders remain plain text, template-specific action buttons continue to use their URL tokens, and the plain-text fallback strips formatting while retaining the applicable action URL.

The `email_change_confirmation_*` template is used for the post-change email notification and as the plain-text body for WordPress's pending profile email-change request. WordPress exposes only the body for the pending request through `new_user_email_content`, so that specific core email cannot use the branded HTML wrapper. When `email_change_confirmation_disabled` is enabled, the plugin suppresses both the post-change notification and the pending profile email-change request, then clears the pending `_new_email` marker so users are not left waiting for a disabled confirmation email.

The `account_created_webhook` setting sends an `account.created` JSON payload after a confirmed registration creates the WordPress user. Webhook logs store destination host, HTTP status, success state, retry count, error message, and timestamp by default. Full payload bodies are stored only when `debug_payload_logging` is enabled.

When `webhook_signing_secret` is configured, outgoing webhooks include `X-Alynt-AG-Event`, `X-Alynt-AG-Time`, `X-Alynt-AG-Version`, and `X-Alynt-AG-Signature` headers. The signature is `sha256=` plus the HMAC-SHA256 of `{timestamp}.{event}.{json_body}` using the shared secret. Leave the secret empty to send unsigned webhooks for existing integrations.

The Webhooks settings tab shows the most recent delivery status, current signing status, a signature verification reference, and expandable metadata for recent webhook delivery logs.

Custom dashboard links are stored in `dashboard_custom_links` as a JSON array. Each link may define `label`, `url`, `icon`, `order`, `roles`, and `target`; relative URLs are resolved from the site home URL, empty roles are visible to all account users, and `_blank` targets receive safe new-tab behavior.

When `dashboard_enabled` and `woocommerce_takeover` are both enabled and WooCommerce is active, requests under the configured `after_login_redirect` path are rendered inside the branded dashboard. Standard WooCommerce account endpoints such as orders, downloads, addresses, account details, and payment methods are delegated to WooCommerce endpoint actions. Plugin-added account endpoints are discovered from WooCommerce account menu items and routed through the same endpoint action pattern.

Registration consent is stored without IP addresses by default. Consent records include email, user ID when available, terms path, privacy path, consent context, plugin version, settings hash, and timestamp. WordPress personal data exporter/eraser callbacks cover pending registrations, consent records, email verification logs, webhook metadata, and audit entries.

Review `docs/PRIVACY_AND_GDPR.md` before enabling public registration, Turnstile, Reoon, webhooks, diagnostics, or WooCommerce takeover on production sites. It summarizes plugin-owned records, default retention, data-subject request support, processor/third-party boundaries, and site-owner review responsibilities.

The emergency bypass key allows a site owner to visit `wp-login.php?alynt_ag_bypass={key}` when frontend output is enabled. It only bypasses the branded screen redirect and does not authenticate the visitor or grant admin access.

## Diagnostics

Diagnostics live under `Advanced / Tools`.

- Disabled by default.
- Stored in the plugin-owned `alynt_ag_diagnostics_logs` table.
- Events below the configured minimum level are ignored.
- Context values are redacted before storage and export.
- Admins can view recent events, export a CSV, and clear diagnostics events.
- Scheduled retention cleanup removes diagnostics events older than `diagnostics_retention` days.
