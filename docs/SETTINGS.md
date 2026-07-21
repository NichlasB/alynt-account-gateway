# Settings Reference

Settings are stored in the `alynt_ag_settings` option. This reference is derived from `ALYNT_AG_Settings_Schema`; it records every persisted setting, its owning tab, default, storage type, and sanitization rule.

## Sanitization Legend

| Type | Sanitization / storage rule |
| --- | --- |
| `boolean` | Cast to a boolean value. |
| `integer` | Cast to an integer and clamped to the field's documented minimum and maximum. |
| `relative_path` | `sanitize_text_field()`, leading slash restored, query string removed. |
| `string` / `secret` | `sanitize_text_field()`; secret values must not be copied into exports or documentation. |
| `textarea` / `rich_text` | `wp_kses_post()`; the Visual/Text email editor retains safe post formatting only. |
| `color` | `sanitize_hex_color()`, otherwise stored empty. |
| `attachment_id` / `nav_menu` | Positive integer via `absint()`. |
| `css_font_family` | Text sanitization followed by a restricted font-stack character allowlist. |
| `email` | `sanitize_email()`. |
| `url` | `esc_url_raw()`. |
| `select` | Sanitized key restricted to the field's available options. |
| `dashboard_links` | Validated JSON/array, then each link is normalized and bounded. |
| `woocommerce_menu_visibility` | A bounded list of sanitized WooCommerce account endpoint keys. |

## General

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `frontend_enabled` | `boolean` | `false` | Enables the branded frontend gateway and URL overrides. Keep disabled while configuring a new site. |

## URLs & Redirects

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `login_path` | `relative_path` | `/login` | Branded login path. |
| `account_action_base` | `relative_path` | `/account` | Base path for account actions such as registration, password reset, and logout. |
| `after_login_redirect` | `relative_path` | `/my-account/` | Fallback post-login path for customers and other non-admin roles. |
| `administrator_after_login_redirect` | `relative_path` | `/wp-admin/` | Fallback post-login path for administrators. |
| `shop_manager_after_login_redirect` | `relative_path` | `/wp-admin/` | Fallback post-login path for WooCommerce shop managers. |

Safe internal `redirect_to` destinations take precedence over role-aware defaults. External and authentication-surface destinations are rejected.

## Branding & Layout

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `brand_logo_id` | `attachment_id` | `0` | Media Library attachment used as the gateway, dashboard, and email logo. |
| `brand_logo_max_width` | `integer` | `220` | Logo maximum width in pixels; constrained to `80` through `520`. |
| `primary_color` | `color` | `#3B5249` | Main brand color. |
| `accent_color` | `color` | `#E1CDB5` | Accent and highlighted-notice color. |
| `text_color` | `color` | `#281408` | Primary text color. |
| `page_background_color` | `color` | `#EAE4D6` | Gateway and dashboard page background color. |
| `surface_color` | `color` | `#FFFFFF` | Card and panel surface color. |
| `error_color` | `color` | `#B3492E` | Error and destructive-state color. |
| `button_background_color` | `color` | `#3B5249` | Primary button background color. |
| `button_text_color` | `color` | `#ffffff` | Primary button text color. |
| `background_image_id` | `attachment_id` | `0` | Optional desktop two-column gateway background image. A vertical image near `1280 x 1920` crops gracefully. |
| `heading_font_family` | `css_font_family` | `Georgia, serif` | Heading font stack. |
| `body_font_family` | `css_font_family` | `-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif` | Body font stack. |

## Screen Copy

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `login_intro_text` | `textarea` | Welcome-back message | Instruction text above the login form. |
| `register_intro_text` | `textarea` | Registration confirmation guidance | Instruction text above the registration form. |
| `lostpassword_intro_text` | `textarea` | Password-reset guidance | Instruction text above the lost-password form. |
| `setpassword_intro_text` | `textarea` | Choose-a-password guidance | Instruction text above the set-password form. |
| `logout_intro_text` | `textarea` | Logout confirmation question | Instruction text above the logout confirmation controls. |
| `registration_disabled_text` | `textarea` | Registration-unavailable message | Notice displayed when a visitor opens registration while public account creation is disabled. |
| `invalid_link_text` | `textarea` | Invalid-or-expired-link message | Notice displayed for expired or invalid confirmation links. |

## Registration

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `registration_enabled` | `boolean` | `false` | Allows public account creation. When disabled, registration links are omitted from the login screen. |
| `registration_token_hours` | `integer` | `24` | Pending-registration confirmation expiry in hours; constrained to `1` through `168`. |
| `username_format` | `string` | `@User_{first_name}_{last_name}` | Generated username pattern for confirmed registrations. |
| `terms_path` | `relative_path` | `/legal/terms/` | Terms link used in the registration consent text. |
| `privacy_path` | `relative_path` | `/legal/privacy/` | Privacy Policy link used in the registration consent text. |

## Security & Spam

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `protection_mode` | `select` | `turnstile_or_reoon` | Requires either configured provider to pass, or every configured provider when set to `turnstile_and_reoon`. |
| `turnstile_site_key` | `string` | empty | Cloudflare Turnstile site key. |
| `turnstile_secret_key` | `secret` | empty | Cloudflare Turnstile secret key. |
| `reoon_api_key` | `secret` | empty | Reoon Email Verifier API key. |
| `reoon_mode` | `select` | `quick` | Reoon verification mode: `quick` or `power`. |
| `reoon_flagged_policy` | `select` | `allow` | Whether flagged Reoon statuses are logged and allowed, or blocked. |
| `registration_rate_limit_count` | `integer` | `5` | Registration attempts permitted per registration window; constrained to `1` through `1000`. |
| `registration_rate_limit_window` | `integer` | `60` | Registration rate-limit window in minutes; constrained to `1` through `10080`. |
| `resend_confirmation_rate_limit_count` | `integer` | `5` | Confirmation resend attempts per resend window; constrained to `1` through `1000`. |
| `resend_confirmation_rate_limit_window` | `integer` | `60` | Confirmation resend rate-limit window in minutes; constrained to `1` through `10080`. |
| `login_rate_limit_count` | `integer` | `10` | Login attempts permitted per login window; constrained to `1` through `1000`. |
| `login_rate_limit_window` | `integer` | `15` | Login rate-limit window in minutes; constrained to `1` through `10080`. |
| `lostpassword_rate_limit_count` | `integer` | `5` | Password-reset attempts per reset window; constrained to `1` through `1000`. |
| `lostpassword_rate_limit_window` | `integer` | `60` | Password-reset rate-limit window in minutes; constrained to `1` through `10080`. |

## Emails

Every email template has a plain-text subject and preheader plus a `rich_text` body. Available tokens include `{{site_name}}`, `{{first_name}}`, `{{last_name}}`, `{{user_email}}`, `{{confirmation_url}}`, `{{reset_url}}`, `{{change_email_url}}`, and `{{expiry_hours}}` when applicable. The email body supports safe headings, bold/italic text, links, blockquotes, and lists. The branded wrapper and action-button behavior are intentionally not editable.

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `email_registration_confirmation_subject` | `string` | Confirmation subject | Subject for the pending-registration confirmation email. |
| `email_registration_confirmation_preheader` | `string` | Confirmation preheader | Preheader for the pending-registration confirmation email. |
| `email_registration_confirmation_body` | `rich_text` | Confirmation body | Body for the pending-registration confirmation email. |
| `email_password_reset_subject` | `string` | Password reset subject | Subject for password reset messages. |
| `email_password_reset_preheader` | `string` | Password reset preheader | Preheader for password reset messages. |
| `email_password_reset_body` | `rich_text` | Password reset body | Body for password reset messages. |
| `email_password_changed_disabled` | `boolean` | `false` | Suppresses password-changed notifications when enabled. |
| `email_password_changed_subject` | `string` | Password changed subject | Subject for password-changed notifications. |
| `email_password_changed_preheader` | `string` | Password changed preheader | Preheader for password-changed notifications. |
| `email_password_changed_body` | `rich_text` | Password changed body | Body for password-changed notifications. |
| `email_new_user_welcome_disabled` | `boolean` | `false` | Suppresses account-created welcome messages when enabled. |
| `email_new_user_welcome_subject` | `string` | Account-created welcome subject | Subject for the account-created welcome email. |
| `email_new_user_welcome_preheader` | `string` | Account-created welcome preheader | Preheader for the account-created welcome email. |
| `email_new_user_welcome_body` | `rich_text` | Account-created welcome body | Body for the account-created welcome email. |
| `email_change_confirmation_disabled` | `boolean` | `false` | Suppresses post-change notifications and pending profile email-change requests when enabled. |
| `email_change_confirmation_subject` | `string` | Email-change subject | Subject for email-change confirmation/notification messages. |
| `email_change_confirmation_preheader` | `string` | Email-change preheader | Preheader for email-change confirmation/notification messages. |
| `email_change_confirmation_body` | `rich_text` | Email-change body | Body for email-change confirmation/notification messages. |
| `email_test_recipient` | `email` | empty | Recipient used by the administrator test-send tool. |

WordPress exposes only a plain-text body for its pending profile email-change request. The plugin uses the configured body there but cannot apply the branded HTML wrapper. Disabling that template also clears the pending `_new_email` marker so a user is not left waiting for an email that will not be sent.

## Dashboard

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `dashboard_enabled` | `boolean` | `false` | Enables the custom full-page account dashboard. |
| `dashboard_custom_links` | `dashboard_links` | `[]` | JSON list of custom account links with label, URL, icon, order, role visibility, and target. |
| `dashboard_offcanvas_enabled` | `boolean` | `false` | Enables the optional dashboard slide-out navigation panel. |
| `dashboard_offcanvas_menu_id` | `nav_menu` | `0` | WordPress navigation menu selected for the dashboard slide-out panel. |
| `dashboard_footer_menu_enabled` | `boolean` | `false` | Enables the optional dashboard footer navigation. |
| `dashboard_footer_menu_id` | `nav_menu` | `0` | WordPress navigation menu selected for the dashboard footer. |

## WooCommerce

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `woocommerce_takeover` | `boolean` | `false` | Renders eligible WooCommerce My Account routes inside the branded dashboard. Requires active WooCommerce and the custom dashboard. |
| `woocommerce_require_login_checkout` | `boolean` | `false` | Redirects logged-out checkout visitors to the branded login page and returns them after authentication. |
| `woocommerce_require_login_order_pay` | `boolean` | `false` | Applies the checkout authentication gate to order-payment links separately. |
| `woocommerce_hidden_menu_items` | `woocommerce_menu_visibility` | `[]` | WooCommerce account endpoint keys hidden from branded dashboard navigation. |

## Webhooks

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `account_created_webhook` | `url` | empty | HTTPS endpoint that receives `account.created` after a confirmed registration creates the user. |
| `webhook_signing_secret` | `secret` | empty | Shared secret for the `sha256=` HMAC signature. |
| `debug_payload_logging` | `boolean` | `false` | Stores full payload bodies for diagnostic use. Leave disabled in normal operation. |

Public, staging, and production webhook destinations must use HTTPS and are sent through WordPress SSRF-safe URL validation. HTTP loopback and `.local` destinations are accepted only when `wp_get_environment_type()` reports `local`, preserving LocalWP testing without opening the exception on deployed sites.

When a signing secret is configured, delivery includes `X-Alynt-AG-Event`, `X-Alynt-AG-Time`, `X-Alynt-AG-Version`, and `X-Alynt-AG-Signature`. The signature covers `{timestamp}.{event}.{json_body}`. Normal logs retain only metadata such as destination host, status, retry count, and error message.

## Privacy & Data

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `success_log_retention` | `integer` | `7` | Successful webhook-log retention in days; constrained to `1` through `3650`. |
| `failed_log_retention` | `integer` | `30` | Failed webhook-log retention in days; constrained to `1` through `3650`. |
| `verification_log_retention` | `integer` | `30` | Verification-log retention in days; constrained to `1` through `3650`. |
| `consent_record_retention` | `integer` | `365` | Registration-consent record retention in days; constrained to `1` through `3650`. |
| `audit_log_retention` | `integer` | `180` | Audit-log retention in days; constrained to `1` through `3650`. |

Consent records omit IP addresses by default. They include email, user ID when available, terms and privacy paths, consent context, plugin version, settings hash, and timestamp. The plugin registers WordPress personal-data exporter and eraser callbacks for plugin-owned pending registrations, consent records, verification logs, webhook metadata, and audit entries.

## Advanced / Tools

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `emergency_bypass_key` | `secret` | generated on activation/default creation | Allows `wp-login.php?alynt_ag_bypass={key}` to bypass the branded screen redirect. It never authenticates a visitor or grants admin access. |
| `diagnostics_enabled` | `boolean` | `false` | Enables plugin diagnostics collection. |
| `diagnostics_min_level` | `select` | `warning` | Lowest retained diagnostic level. |
| `diagnostics_retention` | `integer` | `30` | Diagnostic-record retention in days; constrained to `1` through `3650`. |

Diagnostics are stored in the plugin-owned `alynt_ag_diagnostics_logs` table. Context is redacted before storage and export. Administrators can view recent events, export CSV, clear entries, and rely on scheduled retention cleanup.

## Operational Notes

- The General tab includes a read-only Setup Readiness panel covering frontend output, URLs, emergency access, branding, registration, email testing, dashboard configuration, WooCommerce takeover, webhook signing, and retention windows.
- Plugin settings exports intentionally omit secrets, email recipients, Media Library attachment IDs, and navigation menu IDs because those values do not transfer safely between sites.
- Review [Operations](OPERATIONS.md), [Privacy and GDPR](PRIVACY_AND_GDPR.md), and [Production Rollout Playbook](PRODUCTION_ROLLOUT_PLAYBOOK.md) before enabling public registration or changing a production account surface.
