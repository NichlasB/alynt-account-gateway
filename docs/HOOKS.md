# Hooks Reference

This document distinguishes Alynt Account Gateway extension points from WordPress and WooCommerce hooks that the plugin consumes. WordPress/WooCommerce callback signatures remain authoritative in their upstream documentation.

## Alynt Filters

### `alynt_ag_user_can_access_wp_admin`

Allows a trusted integration to extend the default administrator and WooCommerce shop-manager wp-admin policy for a narrowly scoped custom operator role.

| Parameter | Type | Description |
| --- | --- | --- |
| `$allowed` | `bool` | Whether the default Account Gateway policy allows access. |
| `$user` | `WP_User` | Current authenticated user. |

### `alynt_ag_default_login_redirect_url`

Adjusts the safe same-site fallback URL used after login. The result is normalized through Account Gateway's return-destination policy before use.

| Parameter | Type | Description |
| --- | --- | --- |
| `$default` | `string` | Default same-site URL for the user's role. |
| `$settings` | `array` | Active Account Gateway settings. |
| `$user` | `WP_User|null` | Authenticated user, when available. |

These filters are intended to be used together. An integration that grants a custom role wp-admin access should also provide a role-appropriate default destination and retain its own capability checks and admin-menu restrictions.

### `alynt_ag_is_trusted_proxy`

Determines whether the immediate `REMOTE_ADDR` peer is a trusted reverse proxy. By default no peer is trusted, so forwarded headers are ignored.

**Parameters**

| Parameter | Type | Description |
| --- | --- | --- |
| `$is_trusted` | `bool` | Whether the peer is trusted; defaults to `false`. |
| `$remote_addr` | `string` | Validated immediate-peer IP address. |

```php
add_filter(
	'alynt_ag_is_trusted_proxy',
	static function ( $is_trusted, $remote_addr ) {
		return '203.0.113.10' === $remote_addr;
	},
	10,
	2
);
```

Only trust IP addresses that are controlled by the site's reverse-proxy provider. Trusting all peers would allow clients to spoof rate-limit identity through forwarded headers.

### `alynt_ag_trusted_proxy_headers`

Adjusts the ordered forwarded-header server variables considered after `alynt_ag_is_trusted_proxy` returns true. Alynt Account Gateway accepts only `HTTP_CF_CONNECTING_IP` and `HTTP_X_FORWARDED_FOR` from this filter.

**Parameters**

| Parameter | Type | Description |
| --- | --- | --- |
| `$headers` | `string[]` | Ordered supported server-variable names. |
| `$remote_addr` | `string` | Validated immediate-peer IP address. |

```php
add_filter(
	'alynt_ag_trusted_proxy_headers',
	static function ( $headers ) {
		return array( 'HTTP_CF_CONNECTING_IP' );
	}
);
```

## Scheduled Actions

These are internal scheduled actions, not general integration events. Do not invoke them from a request or use them to replace the retention scheduler.

### `alynt_ag_retention_cleanup`

Runs daily after activation and removes expired plugin-owned pending registrations, logs, consent records, audit data, and diagnostics according to the active retention settings. It has no parameters.

### `alynt_ag_retention_cleanup_continue`

Runs a bounded continuation one minute later when a retention pass reaches its batch limit. It has no parameters.

### `alynt_ag_deliver_account_created_webhook`

Internal asynchronous delivery action for a queued `account.created` webhook. It receives the user ID and payload. This action is scheduled by the registration flow and must not be used as a replacement webhook API.

### `alynt_ag_retry_account_created_webhook`

Internal asynchronous retry action for a failed account-created delivery. It receives the user ID, payload, and retry count. The dispatcher allows at most two retries.

## WooCommerce Delegation

When WooCommerce takeover is enabled, the branded dashboard delegates the current endpoint to WooCommerce using the native dynamic action:

```php
do_action( 'woocommerce_account_' . sanitize_key( $endpoint ) . '_endpoint', $value );
```

The resulting action name is `woocommerce_account_{endpoint}_endpoint`; it receives the endpoint value as its single parameter. Extensions that already register native WooCommerce My Account endpoint renderers continue to run inside the branded dashboard. This is WooCommerce's extension point, not an Alynt-owned hook.

## WordPress And WooCommerce Hooks Consumed

The plugin registers callbacks on the following upstream hooks. They are listed to make integration review easier; they are not invitations to depend on Alynt callback order.

| Hook | Alynt Account Gateway use |
| --- | --- |
| `login_url`, `lostpassword_url`, `register_url`, `logout_url` | Routes standard account links to configured branded routes when frontend output is enabled. |
| `authenticate`, `lostpassword_post` | Enforces email-only login behavior and rate limits login/password-recovery attempts. |
| `retrieve_password_notification_email`, `retrieve_password_title`, `retrieve_password_message` | Produces the configured password-reset email. |
| `send_password_change_email`, `password_change_email` | Produces or suppresses password-changed email. |
| `send_email_change_email`, `email_change_email`, `new_user_email_content`, `pre_wp_mail` | Produces or suppresses email-change messages and handles the core pending-change edge case. |
| `wp_new_user_notification_email` | Produces or suppresses the account-created welcome email. |
| `show_admin_bar`, `admin_init`, `login_init`, `template_redirect` | Applies frontend account routing, access policy, and dashboard presentation. |
| `woocommerce_account_menu_items` | Builds the branded WooCommerce navigation from native account endpoints. |
| `wp_privacy_personal_data_exporters`, `wp_privacy_personal_data_erasers` | Registers plugin-owned data exporter and eraser callbacks. |
| `v_forcelogin_bypass` | Keeps configured staging-only Force Login scenarios compatible with public account routes. |

## Webhook Contract

The plugin does not publish a `do_action()` event for webhooks. When `account_created_webhook` is configured, it sends one signed-or-unsigned `account.created` HTTP JSON request after confirmed registration creates the user. See [Settings](SETTINGS.md#webhooks) and [Privacy and GDPR](PRIVACY_AND_GDPR.md) for payload logging and retention behavior.
