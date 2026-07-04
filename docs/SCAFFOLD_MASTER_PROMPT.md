# Alynt Account Gateway Scaffold Master Prompt

Use this prompt to scaffold the initial plugin foundation in:

```text
C:\Development\WordPress\Plugins\alynt-account-gateway
```

Do not deploy to LocalWP or any live site during scaffold. Do not enable frontend output by default.

```markdown
I'm creating a WordPress plugin with the following details:

**Plugin Name:** Alynt Account Gateway
**Plugin Slug:** alynt-account-gateway
**Plugin Purpose:** Provide a branded account gateway experience for login, registration, password management, account emails, redirects, frontend account dashboards, WooCommerce customer account handling, integrations, and frontend output controls.
**Development Prefix:** alynt_ag_
**Text Domain:** alynt-account-gateway
**Initial Version:** 0.1.0
**Minimum PHP Version:** 7.4
**Target WordPress Version:** 6.0+
**GitHub Plugin URI:** NichlasB/alynt-account-gateway
**Target Folder:** C:\Development\WordPress\Plugins\alynt-account-gateway

**Core Product Rules:**
- Frontend output must be disabled by default.
- The plugin must provide preview tools so settings can be configured before public behavior changes.
- Customers must never see unbranded WordPress core account screens during normal enabled frontend use.
- Native WordPress login must remain reachable through an emergency bypass URL, but the bypass must never authenticate users or bypass 2FA/security plugins.
- Customer login is email-only.
- Public account creation is disabled by default.
- Pending registration confirmation tokens expire after 24 hours.
- WordPress users are created only after email confirmation and valid password setup.
- `wp-admin` and the admin toolbar are available only to administrators and shop managers.
- The plugin is privately distributed through Alynt Plugin Updater via GitHub releases.

**Core Features:**
- Customizable login URL, default `/login`.
- Customizable account action base, default `/account`.
- Branded screens for login, lost password, set password, registration, logout confirmation, disabled registration, and invalid/expired registration links.
- Responsive frontend gateway layout: one column below 800px, two columns above 800px.
- One global gateway background image.
- Logo upload with max-width control.
- Color controls for primary color, accent color, button background, and button text.
- Per-screen welcome/instruction text.
- Registration flow with first name, last name, email, terms/privacy agreement, email confirmation, and password setup.
- Password policy: minimum 12 characters, uppercase, lowercase, number, and special symbol, with password strength meter and confirm-password validation.
- Optional Cloudflare Turnstile integration with server-side validation.
- Optional Reoon Email Verifier integration.
- Default Reoon policy: block invalid, disabled, disposable, and spamtrap; allow but flag catch-all, role-account, unknown, and inbox-full.
- Rich branded email template editor with preview and test-send.
- Email template overrides for password reset, password changed, registration confirmation/welcome, and email-change confirmation.
- Optional disable toggles for password changed, new user welcome, and email address change confirmation emails.
- Account-created webhook with full user fields.
- Webhook logging stores response metadata by default; full payload bodies are stored only when debug payload logging is enabled.
- Optional custom full-page dashboard.
- Custom dashboard links with icons, ordering, role visibility, and open-in-new-tab.
- WooCommerce-aware custom dashboard that delegates sensitive actions to WooCommerce handlers/endpoints.
- Privacy tools: privacy policy text, personal data exporter/eraser, retention controls, and consent record.
- Multilingual support for v1.

**Architecture Choices:**
- [x] Tabbed settings UI
- [x] REST API endpoints or AJAX endpoints for admin-only actions such as preview, test-send, import/export, and retention tools
- [ ] Custom Post Types
- [ ] Frontend shortcodes/blocks
- [x] Custom database tables for pending registrations, webhook logs, verification logs, consent records, and audit entries where options/user meta are not appropriate
- [x] WooCommerce integration layer that is inactive unless WooCommerce is available

**Admin Settings Tabs:**
- General
- URLs & Redirects
- Branding & Layout
- Screen Copy
- Registration
- Security & Spam
- Emails
- Dashboard
- WooCommerce
- Webhooks
- Privacy & Data
- Advanced / Tools

**Settings Requirements:**
- Use one prefixed settings option with a central schema.
- Include defaults, sanitization, validation, tab ownership, and help text for every setting.
- Protect settings on other tabs from being wiped when saving one tab.
- Include settings import/export JSON.
- Include per-tab restore defaults.
- Include a setup/status checklist before enabling frontend output.
- Store secrets such as Turnstile secret key, Reoon API key, webhook signing secret, and emergency bypass key securely in plugin options, never in documentation.

**Data Storage Requirements:**
- Use activation-time database table creation with migrations for plugin-owned records that need retention, search, status, or cleanup.
- Pending registrations must store hashed confirmation tokens, not raw tokens.
- Do not store chosen passwords in pending registration records.
- Store webhook response metadata by default.
- Store full webhook payload bodies only when debug payload logging is enabled.
- Successful webhook metadata default retention: 7 days.
- Failed webhook metadata default retention: 30 days.
- Pending registration default retention: 24 hours plus cleanup buffer.
- Verification log default retention: 30 days.
- Avoid storing IP addresses by default unless explicitly enabled.

**Frontend Requirements:**
- Namespace all frontend classes with the plugin slug prefix.
- Avoid global CSS resets.
- Use semantic HTML.
- Every form control must have an accessible label.
- Inline validation must not clear user input.
- Invalid fields must use `aria-invalid`.
- Dynamic status messages must use live regions.
- Focus indicators must remain visible.
- Forms must be keyboard usable.
- Layout must be usable down to 320px.
- Respect `prefers-reduced-motion`.
- Use the attached screenshots only as visual inspiration, not as hardcoded brand content.

**WooCommerce Requirements:**
- Detect WooCommerce before registering WooCommerce-specific behavior.
- If enabled, allow the custom dashboard to take over WooCommerce My Account presentation.
- Delegate sensitive actions such as orders, addresses, payment methods, account details, downloads, and logout to WooCommerce handlers/endpoints.
- Discover plugin-added WooCommerce account endpoints where practical.
- Show compatibility warnings for plugins that also modify login, registration, account pages, security redirects, or WooCommerce account endpoints.

**Security Requirements:**
- Nonces for every admin form, AJAX, REST mutation, preview action, import/export action, and test-send action.
- Capability checks for all admin actions.
- Sanitize every input and escape every output.
- Use `$wpdb->prepare()` for every query with dynamic values.
- Hash confirmation tokens and webhook signing secrets where appropriate.
- Avoid account enumeration in login and password-reset messages.
- Add rate limits for registration, resend confirmation, login, and password reset flows.
- Turnstile tokens must be verified server-side when Turnstile is enabled.
- Do not use `eval()`, `extract()` with untrusted input, or dynamic includes from user input.

**Privacy And GDPR-Oriented Requirements:**
- Add WordPress privacy policy text using core privacy hooks.
- Add exporter and eraser support for pending registrations, verification logs, webhook logs, consent records, and audit entries.
- Add retention controls and scheduled cleanup.
- Store consent record with terms/privacy relative URLs, timestamp, and policy/settings version context.
- Keep default logging privacy-light.
- Document third-party processing for Cloudflare Turnstile, Reoon Email Verifier, and outgoing webhooks.

**Internationalization Requirements:**
- Wrap every user-facing string in WordPress i18n functions.
- Use text domain `alynt-account-gateway`.
- Generate `languages/alynt-account-gateway.pot`.
- Avoid hardcoded English-only output in templates.
- Keep CSS RTL-safe.

**Deployment:**
- No staging or live deployment during scaffold.
- Create `deploy.example.sh` only as a placeholder template.
- Keep local `deploy.sh` ignored.

**Setup Instructions:**
1. Create complete folder structure per `AI_CODING_RULES.md`.
2. Generate main plugin file with proper header:
   - `Plugin Name: Alynt Account Gateway`
   - `Version: 0.1.0`
   - `Author: Alynt`
   - `Text Domain: alynt-account-gateway`
   - `GitHub Plugin URI: NichlasB/alynt-account-gateway`
3. Create plugin bootstrap, loader, activator, deactivator, i18n, and uninstall structure.
4. Create admin settings foundation with tab controller and placeholder tabs.
5. Create frontend gateway foundation with placeholder templates only; frontend output must remain disabled by default.
6. Create database/migration foundation for future plugin-owned records.
7. Create diagnostics/logging foundation with privacy-conscious defaults.
8. Create service placeholders for email templates, registration, Turnstile, Reoon, webhooks, dashboard, WooCommerce, privacy, and retention cleanup.
9. Set up Composer with WordPress Coding Standards and PHPUnit/Brain Monkey.
10. Set up package.json with esbuild scripts for admin and frontend assets.
11. Create build script in `/scripts/build.mjs`.
12. Create `.phpcs.xml`.
13. Create `phpunit.xml` or `phpunit.xml.dist`.
14. Create `/tests/` with bootstrap that loads both Composer and the plugin loader.
15. Preserve existing `docs/IMPLEMENTATION_PLAN.md`.
16. Create or update `docs/SETTINGS.md`, `docs/HOOKS.md`, `README.md`, `readme.txt`, `CHANGELOG.md`, `.gitignore`, `.gitattributes`, and `.github/workflows/build-release.yml`.
17. Initialize Git only after files are generated, unless the repository already exists.

**After Creating Files:**
1. Detect platform and use Windows-compatible commands.
2. If Composer is unavailable globally, use project-local `composer.phar` when possible.
3. Install Composer dependencies.
4. Install npm dependencies.
5. Run `npm run build`.
6. Run `npm run lint`.
7. Run `npm test`.
8. Fix scaffold-level errors only.
9. Do not implement full feature behavior beyond safe foundations and placeholders during scaffold unless explicitly requested.
10. Update `docs/IMPLEMENTATION_PLAN.md` Phase 1 checkboxes that are truly complete.
11. Report remaining issues and the next recommended toolkit prompt.

Follow all rules in `AI_CODING_RULES.md` exactly. This is production code.
```

