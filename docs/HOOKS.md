# Hooks Reference

This document tracks public and integration-facing hooks used by Alynt Account Gateway.

## Actions

- `alynt_ag_retention_cleanup`: Runs scheduled cleanup for expired plugin-owned records.
- `woocommerce_account_{endpoint}_endpoint`: Fired by WooCommerce and delegated by the branded dashboard for standard and plugin-added My Account endpoints when WooCommerce takeover is enabled.

## WordPress Filters Used

- `login_url`: Points login links to the configured branded login path when frontend output is enabled.
- `lostpassword_url`: Points password reset links to the configured account action base.
- `register_url`: Points registration links to the configured account action base.
- `logout_url`: Points logout links to the branded logout confirmation screen.
- `authenticate`: Enforces email-only login behavior and login rate limiting.
- `retrieve_password_message`: Replaces the password reset email body with the configured template.
- `retrieve_password_title`: Replaces the password reset email subject with the configured template.
- `password_change_email`: Replaces or disables the password changed email.
- `wp_new_user_notification_email`: Replaces or disables the account-created welcome email.
- `send_password_change_email`: Disables the password changed email when configured.
- `send_email_change_email`: Disables the email change confirmation email when configured.
- `new_user_email_content`: Applies the configured email-change confirmation body to WordPress pending email-change requests.
- `woocommerce_account_menu_items`: Preserves WooCommerce account endpoints while allowing branded dashboard presentation.
- `wp_privacy_personal_data_exporters`: Registers the account gateway personal data exporter.
- `wp_privacy_personal_data_erasers`: Registers the account gateway personal data eraser.

## Plugin-Owned Events

The account-created webhook is configured through settings rather than a PHP action. It fires an `account.created` JSON payload after a confirmed registration creates the WordPress user.
