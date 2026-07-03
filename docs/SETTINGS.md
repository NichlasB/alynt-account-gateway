# Settings Reference

Settings are stored in the `alynt_ag_settings` option and defined in `ALYNT_AG_Settings_Schema`.

## Defaults

- `frontend_enabled`: `false`
- `login_path`: `/login`
- `account_action_base`: `/account`
- `after_login_redirect`: `/my-account/`
- `registration_enabled`: `false`
- `registration_token_hours`: `24`
- `protection_mode`: `turnstile_or_reoon`
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
