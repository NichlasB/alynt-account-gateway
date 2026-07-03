# Settings Reference

Settings are stored in the `alynt_ag_settings` option and defined in `ALYNT_AG_Settings_Schema`.

## Defaults

- `frontend_enabled`: `false`
- `login_path`: `/login`
- `account_action_base`: `/account`
- `after_login_redirect`: `/my-account/`
- `registration_enabled`: `false`
- `registration_token_hours`: `24`
- `username_format`: `@User_{first_name}_{last_name}`
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
- `login_intro_text`: configurable login instruction text
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
- `woocommerce_takeover`: `false`
- `account_created_webhook`: empty
- `debug_payload_logging`: `false`
- `diagnostics_enabled`: `false`
- `diagnostics_min_level`: `warning`
- `diagnostics_retention`: `30`
- `success_log_retention`: `7`
- `failed_log_retention`: `30`
- `verification_log_retention`: `30`

## Notes

Secrets must never be committed to documentation. Store Turnstile, Reoon, webhook, and bypass secrets only in plugin settings.

Email templates support token placeholders such as `{{site_name}}`, `{{first_name}}`, `{{last_name}}`, `{{user_email}}`, `{{confirmation_url}}`, `{{reset_url}}`, `{{change_email_url}}`, and `{{expiry_hours}}`.

The `email_change_confirmation_*` template is used for the post-change email notification and as the plain-text body for WordPress's pending profile email-change request. WordPress exposes only the body for the pending request through `new_user_email_content`, so that specific core email cannot use the branded HTML wrapper until the plugin replaces the full sender flow.

The `account_created_webhook` setting sends an `account.created` JSON payload after a confirmed registration creates the WordPress user. Webhook logs store destination host, HTTP status, success state, retry count, error message, and timestamp by default. Full payload bodies are stored only when `debug_payload_logging` is enabled.

## Diagnostics

Diagnostics live under `Advanced / Tools`.

- Disabled by default.
- Stored in the plugin-owned `alynt_ag_diagnostics_logs` table.
- Events below the configured minimum level are ignored.
- Context values are redacted before storage and export.
- Admins can view recent events, export a CSV, and clear diagnostics events.
- Scheduled retention cleanup removes diagnostics events older than `diagnostics_retention` days.
