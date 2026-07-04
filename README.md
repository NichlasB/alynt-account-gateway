# Alynt Account Gateway

Alynt Account Gateway provides a branded account experience for WordPress login, registration, password management, customer dashboards, account emails, WooCommerce account handling, privacy exports, and account-related integrations.

This is private Alynt-distributed software. The plugin header includes `GitHub Plugin URI: NichlasB/alynt-account-gateway` for Alynt Plugin Updater compatibility.

## Features

- Branded replacements for login, registration, lost password, reset password, set password, and logout confirmation screens.
- Configurable login path, account action base, after-login redirect, and emergency bypass key for direct `wp-login.php` access.
- Frontend output disabled by default so site owners can configure settings before public changes appear.
- Brand controls for logo, logo max width, background image, colors, button colors, and font stacks.
- Screen-specific instruction copy for account gateway forms and states.
- Confirmation-first registration flow that creates a WordPress user only after the visitor confirms email and sets a valid password.
- Email-only login support with generated usernames for created accounts.
- Password validation requiring at least 12 characters with uppercase, lowercase, number, and symbol characters.
- Optional Cloudflare Turnstile and Reoon Email Verifier registration protection, plus transient-backed rate limiting.
- Branded HTML account email templates with preview and test-send tools.
- Custom full-page dashboard with optional WooCommerce My Account takeover that delegates standard endpoints to WooCommerce.
- Custom dashboard links with labels, icons, ordering, role visibility, and new-tab support.
- Account-created webhook delivery with metadata logging and optional debug payload logging.
- Privacy exporter/eraser support for plugin-owned account, consent, verification, webhook, and audit records.
- Scheduled retention cleanup for logs and diagnostics.
- Admin diagnostics with redacted context, recent event display, CSV export, and clear controls.
- Multilingual-ready strings and generated POT file in `languages/`.

## Requirements

- WordPress 6.0 or newer.
- PHP 7.4 or newer.
- WooCommerce is optional and only required for WooCommerce account takeover.
- Cloudflare Turnstile and Reoon integrations require site-owned API credentials.

## Installation

1. Download the release zip from the private Alynt distribution channel.
2. Install it through WordPress admin under Plugins -> Add New -> Upload Plugin.
3. Activate Alynt Account Gateway.
4. Open Settings -> Account Gateway and configure the plugin before enabling frontend output.

## Basic Configuration

Recommended first pass:

1. Set the Login URL Path and Account Action Base.
2. Confirm the After Login Redirect.
3. Upload the brand logo and optional gateway background image.
4. Review colors, button colors, and font stacks.
5. Configure Terms and Privacy relative paths.
6. Decide whether public registration should be enabled.
7. Configure Turnstile or Reoon before enabling registration on public sites.
8. Preview/test account emails.
9. Enable the dashboard and WooCommerce takeover only after confirming account pages locally.
10. Enable Frontend Output when the configuration is ready.

## Security Notes

- The emergency bypass only restores access to the native WordPress login screen; it does not authenticate anyone or grant admin access.
- Administrators and shop managers may access `wp-admin`; other users are redirected to the configured account destination.
- Turnstile tokens are verified server-side when Turnstile is configured.
- Webhook payload bodies are stored only when Debug Payload Logging is enabled.
- Secrets must be stored in plugin settings, not in code or documentation.

## Development

```powershell
npm install
php ./composer.phar install
npm run build
npm run lint
npm test
```

If Composer is available globally, `composer install` can be used instead of `php ./composer.phar install`.

## Documentation

- `docs/IMPLEMENTATION_PLAN.md`
- `docs/SCAFFOLD_MASTER_PROMPT.md`
- `docs/SETTINGS.md`
- `docs/HOOKS.md`

## License

GPL-2.0-or-later.
