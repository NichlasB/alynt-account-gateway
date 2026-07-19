# Alynt Account Gateway File Structure Review

Date: 2026-07-19

## Scope And Disposition

This is Phase 1 of the `02-FILE_STRUCTURE_REVIEW_PROMPT.md` workflow. It is a
read-only inventory and refactoring plan. No structural refactoring is included
in v1.1.12.

The toolkit thresholds are:

- PHP: split files over 300 lines.
- JavaScript: modularize files over 250 lines.
- CSS: split files over 500 lines by component or section.

The v1.1.7 pre-release revalidation recorded these oversized files as known,
non-blocking structural debt. That decision avoided a high-blast-radius
architecture change inside a release gate, but it did not make the files
compliant with the structure-review thresholds.

## Summary Statistics

- Code files analyzed: 92
- PHP files: 88
- JavaScript files: 2
- CSS files: 2
- Files over their applicable limit: 23
- Oversized production files: 14
- Oversized test and test-support files: 9
- Methods initially flagged above 50 lines: 85
- Methods initially flagged above 4 parameters: 12
- Refactoring complexity: Complex

The method counts are first-pass structural signals. Exact executable line
counts, nesting depth, and cyclomatic complexity must be confirmed for each
extraction before Phase 2.

## Oversized Production Files

| File | Lines | Limit | Primary concerns |
|---|---:|---:|---|
| `admin/class-settings-page.php` | 5,489 | 300 | Settings UI, guidance, readiness, security operations, email/webhook tools, diagnostics, previews, import/export, action handlers |
| `assets/src/frontend/style.css` | 2,198 | 500 | Gateway shell/forms, dashboard modules, WooCommerce normalization, off-canvas UI, responsive and forced-color rules |
| `includes/services/class-frontend-dashboard-screen.php` | 983 | 300 | Dashboard shell, menus, endpoint affordances, overview, orders, downloads, addresses, account details, payment methods |
| `includes/services/class-registration-service.php` | 974 | 300 | Request orchestration, anti-spam, pending registration lifecycle, confirmation, account creation, webhook/email delivery |
| `includes/class-settings-schema.php` | 940 | 300 | Tab/schema definition, defaults, import/export, sanitization, dashboard links, WooCommerce visibility |
| `assets/src/admin/style.css` | 862 | 500 | Settings tabs, fields, guidance, security status, tools, responsive admin behavior |
| `includes/services/class-woocommerce-integration.php` | 736 | 300 | Menu visibility, endpoint routing, form delegation, account data adapters, URL/icon helpers |
| `includes/services/class-email-template-service.php` | 733 | 300 | Template registry, WordPress email filters, token generation, transport, HTML/plain rendering |
| `public/class-frontend.php` | 608 | 300 | Public hooks, access control, route interception, preview routing, URL filters, diagnostics, document rendering |
| `includes/services/class-auth-service.php` | 537 | 300 | Login, lost password, reset, rate limiting, redirects, diagnostics, messages |
| `assets/src/admin/index.js` | 494 | 250 | Save-state tracking, typography, colors, media, dashboard-link editor |
| `includes/services/class-compatibility-warnings.php` | 450 | 300 | Known-plugin detection, hook inspection, callback normalization, warning generation |
| `includes/services/class-privacy-service.php` | 332 | 300 | Privacy text, consent storage, export, erasure |
| `assets/src/frontend/index.js` | 317 | 250 | Password visibility/policy, registration readiness, Turnstile sizing, off-canvas navigation |

## Oversized Test And Test-Support Files

| File | Lines | Limit | Recommended boundary |
|---|---:|---:|---|
| `tests/SettingsPageSecurityStatusTest.php` | 1,629 | 300 | Split by provider status, abuse signals, rate limits, pending registrations, and review actions |
| `tests/bootstrap.php` | 1,136 | 300 | Separate WordPress stubs by API area and retain one bootstrap loader |
| `tests/FrontendDashboardScreenTest.php` | 980 | 300 | Split by shell/navigation and individual dashboard module |
| `tests/WooCommerceIntegrationTest.php` | 571 | 300 | Split menu/routing tests from endpoint data-adapter tests |
| `tests/RegistrationServiceTest.php` | 510 | 300 | Split protection/pending lifecycle from completion/delivery tests |
| `tests/AuthServiceTest.php` | 458 | 300 | Split login/redirect tests from password recovery tests |
| `tests/SettingsSchemaTest.php` | 446 | 300 | Split schema/default tests from sanitization/import tests |
| `tests/EmailTemplateServiceTest.php` | 408 | 300 | Split WordPress filter tests from rendering/token tests |
| `tests/FrontendRoutingTest.php` | 326 | 300 | Split native-login interception from gateway route rendering |

## Recommended Production Splits

### `admin/class-settings-page.php`

Complexity: Complex.

Current responsibilities:

1. Registers the page, settings, fields, and admin action hooks.
2. Renders the settings shell, controls, field help, and tab guidance.
3. Produces setup-readiness and compatibility reports.
4. Produces security status, provider health, abuse, rate-limit, pending
   registration, and manual-review interfaces.
5. Produces email, webhook, diagnostics, preview, import/export, and reset tools.
6. Handles every settings-page form action and records settings changes.

Dependencies include `ALYNT_AG_Settings_Schema`, database and diagnostics APIs,
provider clients, email templates, webhook dispatch, WooCommerce integration,
dashboard helpers, frontend preview rendering, and compatibility warnings. It is
constructed by `ALYNT_AG_Admin`.

Recommended staged composition:

1. `admin/class-settings-page.php`
   - Keep registration, the page shell, tab routing, and collaborator
     orchestration.
2. `admin/class-settings-field-renderer.php`
   - Move generic fields, typography, media, navigation menu, dashboard links,
     help text, and direction/select helpers.
3. `admin/class-settings-guidance.php`
   - Move tab guidance, readiness checks, readiness counts, and tab URLs.
4. `admin/class-security-status-page.php`
   - Move security status rendering and read-only signal aggregation.
5. `admin/class-security-review-actions.php`
   - Move review queue rendering, decisions, provider tests, and related action
     handlers.
6. `admin/class-email-tools.php`
   - Move email reference, token reference, preview, and test-send actions.
7. `admin/class-webhook-tools.php`
   - Move webhook tools, delivery summaries, logs, and test action.
8. `admin/class-diagnostics-tools.php`
   - Move operational snapshots, export, clear, and compatibility warnings.
9. `admin/class-settings-transfer.php`
   - Move settings import, export, and restore-tab-default actions.
10. `admin/class-gateway-preview-tools.php`
    - Move preview screen mapping, request handling, and asset loading.

Use composition rather than traits. These responsibilities have distinct
dependencies and are not cross-class code reuse candidates.

### `includes/class-settings-schema.php`

Recommended split:

- `class-settings-schema.php`: schema facade and tab/key lookup.
- `class-settings-defaults.php`: defaults and per-tab defaults.
- `class-settings-sanitizer.php`: scalar and structured sanitization.
- `class-settings-transfer-schema.php`: portable export/import inspection.

The 526-line `schema()` method should be replaced by tab-specific schema
providers so adding one setting does not require editing a single monolithic
array.

### `includes/services/class-registration-service.php`

Recommended split:

- Registration controller: request dispatch and user-facing error mapping.
- Registration protection service: Turnstile, Reoon, rate limits, terms, and
  verification logging.
- Pending registration repository/service: create, find, renew, resend,
  confirm, and token hashing.
- Account creation service: password validation, username generation, user
  creation, consent attachment, email, and webhook completion.

These services should receive collaborators through constructors. Avoid moving
database behavior into traits.

### `includes/services/class-frontend-dashboard-screen.php`

Recommended split:

- Dashboard shell/navigation renderer.
- WooCommerce endpoint guidance/affordance renderer.
- Overview renderer.
- Recent orders renderer.
- Downloads renderer.
- Addresses renderer.
- Account details renderer.
- Payment methods renderer.

The existing class can remain a facade that delegates each module to a focused
renderer, preserving its current public API.

### `includes/services/class-woocommerce-integration.php`

Recommended split:

- Account menu policy and visibility.
- Endpoint routing/delegation.
- Customer account data provider for orders, downloads, addresses, account
  details, and payment methods.
- Endpoint URL and icon helpers.

Retain WooCommerce-sensitive writes in native WooCommerce handlers.

### `includes/services/class-email-template-service.php`

Recommended split:

- Template registry and token catalog.
- WordPress notification filter adapter.
- Token resolver.
- HTML/plain renderer.
- Mail transport wrapper.

Keep the existing service as the hook-registration facade so public filters and
test seams remain stable.

### `public/class-frontend.php`

Recommended split:

- Frontend hook registrar.
- Admin/native-login access controller.
- Gateway request router.
- WordPress auth URL filter adapter.
- Preview controller.
- Routing diagnostics context builder.

The current frontend routes, assets, and document renderer are already separate
collaborators and provide a useful model for this decomposition.

### `includes/services/class-auth-service.php`

Recommended split:

- Login handler and redirect policy.
- Password-recovery handler.
- Authentication throttling and diagnostics adapter.
- Error-message catalog.

Preserve current nonce, safe-redirect, and rate-limit behavior with focused
behavior-locking tests before extraction.

### `includes/services/class-compatibility-warnings.php`

Recommended split:

- Known-plugin compatibility registry.
- Hook callback inspector.
- Warning formatter/deduplicator.

### `includes/services/class-privacy-service.php`

Recommended split:

- Privacy integration registrar and policy text.
- Consent recorder.
- Personal-data exporter.
- Personal-data eraser.

This is the lowest-risk PHP extraction candidate and is suitable for the first
structural maintenance increment.

## Recommended Asset Splits

### Frontend CSS

Create source partials for:

- Tokens, reset, typography, and accessibility.
- Gateway shell, card, notices, and forms.
- Dashboard shell/navigation.
- Dashboard modules.
- WooCommerce delegated content.
- Off-canvas and footer menus.
- Responsive behavior.
- Forced-colors behavior.

Continue producing one minified frontend asset so runtime request count and
enqueue behavior do not change.

### Admin CSS

Create source partials for:

- Settings shell/tabs.
- Fields and repeaters.
- Guidance/readiness.
- Security status and review tools.
- Email/webhook/diagnostics tools.
- Responsive and accessibility behavior.

### Frontend JavaScript

Create modules for:

- Password controls and password policy.
- Registration form readiness.
- Turnstile widget preparation.
- Off-canvas navigation and submenu disclosure.

### Admin JavaScript

Create modules for:

- Email editor save state.
- Typography presets.
- Color controls.
- Media controls.
- Dashboard-link repeater.

The build should continue emitting the existing `assets/dist` entry points.

## Complex Method Priorities

First-priority extractions:

- `ALYNT_AG_Settings_Schema::schema()` at approximately 526 lines.
- `ALYNT_AG_Settings_Page::render_field()` at approximately 167 lines.
- `ALYNT_AG_Settings_Page::settings_tab_guidance()` at approximately 141 lines.
- `ALYNT_AG_Settings_Page::render_admin_notice()` at approximately 137 lines.
- `ALYNT_AG_Privacy_Service::export_personal_data()` at approximately 121
  lines.
- `ALYNT_AG_Settings_Page::security_provider_failure_triage_items()` at
  approximately 117 lines.
- `ALYNT_AG_Frontend_Dashboard_Screen::render_dashboard_screen()` at
  approximately 108 lines.
- `ALYNT_AG_Registration_Service::maybe_handle_registration_request()` at
  approximately 96 lines.
- `ALYNT_AG_Registration_Service::complete_pending_registration()` at
  approximately 95 lines.
- `ALYNT_AG_Email_Template_Service::render_html()` has approximately 97 lines
  and six parameters; introduce a render-context value object or normalized
  context array before splitting markup assembly.

## Dependency And Naming Findings

- No confirmed circular class dependency was found in the loader/orchestrator
  path.
- `ALYNT_AG_Settings_Page` is the clearest god-class candidate and has the
  broadest dependency surface.
- Several services instantiate collaborators internally. Constructor injection
  should be introduced only where it makes extraction and isolated testing
  materially clearer.
- Class names and `class-{name}.php` filenames are consistent with the current
  non-namespaced WordPress convention.
- Templates are mostly separated into frontend renderer classes; the settings
  page remains the principal place where rendering, queries, and actions are
  mixed.
- Traits are not recommended as the primary remedy. Composition produces
  clearer ownership and dependency boundaries for the identified concerns.

## Phase 2 Sequence

1. Split test bootstrap stubs and oversized test classes without runtime
   changes.
2. Split frontend/admin CSS and JavaScript source modules while preserving the
   exact built entry points.
3. Extract privacy exporter/eraser collaborators as the lowest-risk PHP
   runtime increment.
4. Extract dashboard module renderers behind the existing dashboard facade.
5. Split settings schema/defaults/sanitization.
6. Split registration and authentication services with behavior-locking tests.
7. Split WooCommerce and email services.
8. Decompose the settings page last, using the collaborators established by
   earlier increments.

Each increment requires the full test suite, PHPCS, build, exact-package
inspection, Plugin Tester acceptance, and a small maintenance release. Do not
perform the entire sequence in one branch or release.

