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
- `dashboard_enabled`: `false`
- `woocommerce_takeover`: `false`
- `debug_payload_logging`: `false`
- `diagnostics_enabled`: `false`
- `diagnostics_min_level`: `warning`
- `diagnostics_retention`: `30`
- `success_log_retention`: `7`
- `failed_log_retention`: `30`
- `verification_log_retention`: `30`

## Notes

Secrets must never be committed to documentation. Store Turnstile, Reoon, webhook, and bypass secrets only in plugin settings.

## Diagnostics

Diagnostics live under `Advanced / Tools`.

- Disabled by default.
- Stored in the plugin-owned `alynt_ag_diagnostics_logs` table.
- Events below the configured minimum level are ignored.
- Context values are redacted before storage and export.
- Admins can view recent events, export a CSV, and clear diagnostics events.
- Scheduled retention cleanup removes diagnostics events older than `diagnostics_retention` days.
