=== Alynt Account Gateway ===
Contributors: alynt
Tags: login, registration, account, woocommerce, dashboard
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A branded account gateway for WordPress login, registration, password flows, emails, dashboards, WooCommerce account handling, privacy tooling, and integrations.

== Description ==

Alynt Account Gateway is private Alynt-distributed software for replacing bare WordPress account screens with a branded, configurable account experience.

Frontend output is disabled by default so site owners can configure URL paths, branding, screen copy, registration behavior, emails, integrations, and dashboard behavior before public changes appear.

Core features include:

* Branded login, registration, lost password, reset password, set password, and logout confirmation screens.
* Configurable login path, account action base, after-login redirect, and emergency bypass key.
* Confirmation-first registration that creates the WordPress user only after email confirmation and password setup.
* Optional Cloudflare Turnstile and Reoon Email Verifier support.
* Rate limiting for account gateway actions.
* Branded account emails with preview and test-send tools.
* Optional custom dashboard and WooCommerce My Account takeover.
* Custom dashboard links with icons, ordering, role visibility, and new-tab behavior.
* Account-created webhook delivery.
* Privacy exporter/eraser integration and retention cleanup.
* Multilingual-ready strings and generated POT file.

== Installation ==

1. Upload the private Alynt release zip through Plugins -> Add New -> Upload Plugin.
2. Activate Alynt Account Gateway.
3. Configure Settings -> Account Gateway.
4. Enable Frontend Output only after confirming the settings.

== Changelog ==

= 0.1.0 =
* Added branded account gateway screens and configurable account paths.
* Added confirmation-first registration, password validation, registration protection, rate limiting, emails, dashboard, WooCommerce takeover, webhooks, privacy tools, diagnostics, and updater metadata.
* Fixed admin media preview DOM handling and uninstall cleanup for scheduled hooks and rate-limit transients.
