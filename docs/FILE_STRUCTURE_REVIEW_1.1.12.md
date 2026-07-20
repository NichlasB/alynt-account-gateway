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

Increment 8 began from released v1.1.20 after the completed v1.1.18
settings-schema worktree was retired. The current settings page remains 5,492
lines with 136 methods and no mutable instance state. The approved execution
keeps `ALYNT_AG_Settings_Page` as the public WordPress hook/callback facade and
uses composition behind a focused internal method registry. Stateless
collaborators will own the page shell, fields, guidance/readiness, security
status and review surfaces, email/webhook tools, diagnostics, settings
transfer, gateway preview, and action logic. Every production PHP file must
finish at or below 300 lines without changing public callbacks, markup, copy,
actions, nonces, redirects, provider behavior, or persistent settings.

The extraction is implemented for v1.1.21. The facade is 238 lines and keeps
the exact established 17 public methods. Thirty focused behavior
collaborators, the shared component base, and the registry are all below 300
lines; the largest is 277 lines. AST comparison against released v1.1.20
confirms all 136 baseline signatures and method bodies remain present with no
missing, extra, or changed operations.

The test suite now passes at 424 tests and 3,121 assertions. New guardrails
lock the facade API, 133 extracted method owners, production loader order, and
line thresholds. Build, stable 1,104-string POT generation, PHPCS, all PHP and
JavaScript syntax checks, npm and Composer security audits, and diff-integrity
checks pass. The exact 116-file package was inspected and installed on LocalWP
Plugin Tester, where all installed files byte-matched the candidate and the
full settings surface passed authenticated browser acceptance without changing
activation or persistent settings. Increment 8 is complete, v1.1.21 is
published, and its public updater path is verified end to end.

Each increment requires the full test suite, PHPCS, build, exact-package
inspection, Plugin Tester acceptance, and a small maintenance release. Do not
perform the entire sequence in one branch or release.

## Phase 2 Progress

### v1.1.14 Inventory Refresh

Phase 2 began from clean pushed checkpoint
`36ec9eccc321a9af5326d64e78ea5545c412581d` after the v1.1.14 release,
updater verification, and accepted production synchronization.

The refreshed inventory contains 97 code files after excluding dependencies,
generated assets, build output, work directories, and repository metadata.
Twenty-three files exceed the review thresholds: 14 production files and nine
test/support files. The production priorities remain materially unchanged from
the v1.1.12 review.

### Increment 1: Test Infrastructure

Status: complete.

- Locked the baseline at 386 tests and 2,438 assertions.
- Reduced `tests/bootstrap.php` from 1,175 lines to an ordered loader and
  shared-state initializer.
- Extracted the database fixture, production test loader, and focused core,
  WooCommerce, media, HTTP, routing, authentication/user, sanitization, and
  options/hooks stub modules.
- Preserved declaration and loading order.
- Kept each extracted support file below the 300-line PHP review threshold.
- Split all eight oversized test classes into behavior-specific PHPUnit
  classes backed by shared abstract support cases.
- Extracted the large verification-activity expectation catalog into a
  dedicated test fixture.
- Reduced every PHP file under `tests/` to 300 lines or fewer.
- Passed full PHPCS, PHP syntax, frontend/admin build, POT generation, npm
  high-severity audit, Composer advisory audit, `git diff --check`, and the
  unchanged 386-test/2,438-assertion suite without warnings or deprecations.

This increment changes only tests and planning documentation, both of which are
excluded from the public runtime package. It is therefore retained as a pushed
development checkpoint rather than a version-only public plugin release. The
next increment begins source asset modularization and will complete package and
Plugin Tester acceptance before requesting maintenance-release approval.

### Increment 2: Source Asset Modules

Status: complete; released and updater-verified as v1.1.15.

- Locked the v1.1.14 compiled asset lengths and SHA-256 fingerprints before
  changing source organization.
- Split frontend CSS into seven ordered modules and admin CSS into five ordered
  modules while keeping each module at or below 500 lines.
- Split frontend JavaScript into five behavior-focused modules and admin
  JavaScript into five behavior-focused modules while keeping each module at
  or below 250 lines.
- Preserved the existing `assets/dist/frontend/index.*` and
  `assets/dist/admin/index.*` public build entry points.
- Rebuilt CSS is byte-identical to the v1.1.14 baseline. Rebuilt JavaScript has
  expected bundler-level byte differences after module extraction; entry
  points, initialization order, behavior markers, and runtime responsibilities
  remain covered.
- Added structural tests for ordered CSS imports, JavaScript module
  reachability, and module line limits.
- The focused quality run passes PHPCS, JavaScript syntax, lint, build, and the
  expanded 390-test/2,712-assertion suite.

The full audit set, exact-package inspection, and Plugin Tester browser
verification pass for the v1.1.15 candidate. The package contains 49 runtime
files and 43 syntax-clean PHP files, contains no development/source files or
backslash paths, and has SHA-256
`6F36E2930E1B26773991DA39607ECF328035FFEE6224B8E0086D86BBEAC0B625`.
All installed files byte-match the package. Settings, activation order, and
schema fingerprints remain unchanged. Browser acceptance covered the five
admin JavaScript responsibilities, responsive admin tabs, desktop/mobile
gateway output, password visibility, lost password, and disabled registration
without browser warnings or errors.

Release approval was granted and
[GitHub v1.1.15](https://github.com/NichlasB/alynt-account-gateway/releases/tag/v1.1.15)
was published from merge commit `446f975`. Build Release run `29704646229`
completed successfully. The public package contains 49 runtime files and 43
syntax-clean PHP files, contains no development/source files or backslash
archive paths, and has SHA-256
`241A6BD3276303771D63BD5CFF349C6D8709E85A92385F6AB75D775A30B8FFA9`.

Alynt Plugin Updater detected the public `1.1.14 -> 1.1.15` update on LocalWP
Plugin Tester and WordPress installed the GitHub release asset through the
native Plugins screen. All 49 installed files byte-match the public package,
Account Gateway remains active, no update remains, and the activation,
settings, and database-version fingerprints are unchanged. The homepage and
`/login/` return HTTP 200, browser acceptance found no errors, the temporary
authentication helper was removed, and the QA session was logged out. No
staging or production site was touched.

Increment 3 may now begin with privacy exporter/eraser extraction.

### Increment 3: Privacy Exporter And Eraser Collaborators

Status: complete; released and updater-verified as v1.1.16.

- Kept `ALYNT_AG_Privacy_Service` as the stable WordPress callback and
  registration-consent facade.
- Extracted exporter queries and record formatting into
  `ALYNT_AG_Privacy_Exporter`.
- Extracted plugin-owned personal-data deletion into
  `ALYNT_AG_Privacy_Eraser`.
- Preserved callback signatures, pagination arguments, query conditions,
  exported fields, deletion paths, and response structures.
- Added injection-based regression coverage for callback ownership and
  collaborator delegation.
- Reduced the privacy facade from 333 to 220 lines. The exporter is 164 lines
  and the eraser is 54 lines.

The focused privacy suite and the full quality gates pass. PHPUnit now reports
392 tests and 2,718 assertions. PHPCS, project-wide PHP syntax, source
JavaScript syntax, frontend/admin build, POT generation, npm audit, Composer
audit, and `git diff --check` all pass.

The inspected v1.1.16 candidate contains 51 runtime files and 45 syntax-clean
PHP files, contains no development files or backslash archive paths, and has
SHA-256
`B4985AF9B34C510E2642A0F0A893BEED43882EADAACFAFDFC04D623D8DFFB6B7`.
LocalWP Plugin Tester installed that exact package over active v1.1.15; all 51
installed files byte-match, and activation, settings, and schema fingerprints
remain unchanged.

Installed-copy acceptance verified both callbacks through WordPress's native
privacy filter registrations. The exporter retained the privacy-service facade
callback and returned the expected consent, pending registration, verification,
and webhook groups. The eraser retained the facade callback and removed all
disposable consent, pending registration, verification, webhook, and audit
records. The disposable subscriber, temporary callback harnesses, and all QA
rows were removed. The homepage and branded login return HTTP 200 without
browser errors. No staging or production site was touched.

Release approval was granted and
[GitHub v1.1.16](https://github.com/NichlasB/alynt-account-gateway/releases/tag/v1.1.16)
was published from merge commit `6274b0b`. Build Release run `29727428023`
completed successfully. The public ZIP contains 51 runtime files and 45
syntax-clean PHP files, contains no development files or backslash archive
paths, and has SHA-256
`793B3C8CBF96583D2745A4639331C92AC23D14F00FA52C6907F0EF8AF8BCD49D`.

Alynt Plugin Updater detected the public `1.1.15 -> 1.1.16` update on LocalWP
Plugin Tester, and WordPress installed that public asset through its native
upgrader. All 51 installed files byte-match the public ZIP, Account Gateway
remains active, no update remains, and activation, settings, and schema
fingerprints are unchanged. The homepage and branded login return HTTP 200,
and browser acceptance found no warnings or errors. No staging or production
site was touched.

Increment 4 may now begin with dashboard module renderer extraction.

### Increment 4: Dashboard Renderer Collaborators

Status: complete; released and updater-verified as v1.1.17.

- Kept `ALYNT_AG_Frontend_Dashboard_Screen` as the stable public facade with
  its established first three constructor arguments and two public render
  methods.
- Extracted dashboard actions and optional menus into a navigation renderer.
- Extracted WooCommerce endpoint metadata and endpoint content rendering into
  separate collaborators.
- Extracted overview, recent-order, and download modules into a commerce
  renderer.
- Extracted address, account-detail, and payment-method modules into an
  account renderer.
- Preserved WooCommerce delegation, visibility checks, endpoint context,
  markup classes, copy, and navigation behavior.
- Reduced the dashboard facade from 983 to 208 lines. Every extracted
  collaborator is 241 lines or fewer.
- Added regression coverage for navigation order, dashboard module delegation,
  exact endpoint context, and the 300-line structural threshold.

The full quality gates pass. PHPUnit now reports 396 tests and 2,743
assertions. PHPCS, project-wide PHP syntax, source JavaScript syntax,
frontend/admin build, POT generation, npm audit, Composer audit, and
`git diff --check` all pass.

The inspected v1.1.17 candidate contains 56 runtime files and 50 syntax-clean
PHP files under one plugin root, contains no development files or backslash
archive paths, and has SHA-256
`0ADF44A4005D09A4F2FBDBBDB206816FC86C963A4E97BE1C8E98C1BCC89545BD`.
LocalWP Plugin Tester installed that exact candidate over active v1.1.16; all
56 installed files byte-match the package, the plugin remains active at
v1.1.17, and activation and settings fingerprints remain unchanged.

Authenticated desktop acceptance covered every extracted dashboard renderer,
including the dashboard overview, commerce, address, account-detail, payment,
navigation, and endpoint responsibilities. Orders and Payment Methods
preserved native WooCommerce delegation and empty-state output. Payment
Methods also passed at a 390x844 mobile viewport with no browser warnings,
errors, or failed dynamic requests. The disposable customer and authenticated
session were removed, the homepage and branded login return HTTP 200, and no
staging or production site was touched.

Release approval was granted and
[GitHub v1.1.17](https://github.com/NichlasB/alynt-account-gateway/releases/tag/v1.1.17)
was published from merge commit `8fc92ee`. Build Release run `29730827502`
completed successfully. The public ZIP contains 56 runtime files and 50
syntax-clean PHP files, contains no development files or backslash archive
paths, and has SHA-256
`F6E31A2B541FD4F7811AC6B8002A07C5A54AB3DD2B798BCA64BE71E81DB407FE`.
It matches the accepted candidate byte-for-byte except for expected
`readme.txt` line-ending normalization.

Alynt Plugin Updater's manual check detected the public `1.1.16 -> 1.1.17`
update on LocalWP Plugin Tester, and WordPress installed the GitHub release
asset through the native Plugins screen. All 56 installed files byte-match
the public ZIP, the plugin remains active at v1.1.17, activation and settings
fingerprints are unchanged, and no update remains.

Post-update authenticated dashboard smoke acceptance passed without plugin
errors or failed dynamic requests. Only pre-existing WordPress
development-mode React and jQuery Migrate warnings were observed. The
disposable administrator and browser session were removed, the homepage and
branded login return HTTP 200, and no staging or production site was touched.

The next Phase 2 increment may now split settings schema, defaults, and
sanitization.

### Increment 5: Settings Definition, Defaults, And Sanitization

Status: implementation, v1.1.18 candidate acceptance, publication, and public
updater verification complete.

- Kept `ALYNT_AG_Settings_Schema` as the stable public static facade with all
  twelve established public methods.
- Split the ordered field catalog into core, security/email, and account/data
  definition providers behind one definition aggregator.
- Extracted default derivation, tab restoration, and stored-value merging into
  a defaults collaborator.
- Extracted schema filtering and type-specific value handling into a sanitizer
  collaborator.
- Preserved exact tab, schema, and defaults fingerprints from v1.1.17.
- Preserved import/export behavior, field order and metadata, generated
  bypass defaults, stored-value merging, and sanitized output.
- Reduced the settings facade from 952 to 213 lines. Every extracted
  collaborator is 236 lines or fewer.
- Added regression coverage for the public facade contract, exact
  fingerprints, provider aggregation, collaborator equivalence, and the
  300-line structural threshold.

The full quality gates pass. PHPUnit reports 401 tests and 2,773 assertions.
PHPCS, project-wide PHP syntax, source JavaScript syntax, frontend/admin build,
POT generation, npm audit, Composer audit, and `git diff --check` all pass.

The inspected v1.1.18 candidate contains 62 runtime files and 56 syntax-clean
PHP files under one plugin root, excludes development files, uses portable
forward-slash archive paths, and has SHA-256
`6C5043A6F064D670B1896DB13592CFBEB9A5FA34504B3B86BDF26A73F1CC5D9D`.
LocalWP Plugin Tester installed that exact package over active v1.1.17. Every
installed file byte-matches the package, Account Gateway remains active at
v1.1.18, saved settings and active-plugin fingerprints are unchanged, and the
database version remains `0.1.6`.

Installed-copy verification preserved the exact 86-field schema and ordered
tabs fingerprints, and the facade/defaults collaborator outputs match after
normalizing the intentionally generated emergency bypass key. Browser
acceptance loaded all 12 settings tabs and exercised representative core,
security, email, and tools screens. Tabs wrapped without overlap at 1100px and
782px, dynamic requests succeeded, and no plugin browser errors were observed.
The disposable administrator, browser session, and inspection helper were
removed; public local routes return HTTP 200. No staging or production site
was touched. The candidate is ready for explicit v1.1.18 release approval.

Release approval was granted and GitHub v1.1.18 was published from merge
commit `bd220e5`. Build Release run `29737008196` completed successfully. The
public package contains the same 62 runtime files as the accepted candidate,
has 56 syntax-clean PHP files, excludes development files, uses portable
archive paths, and has SHA-256
`847F2FC3E6A460C98BF66E6060316034F099291853F0614BD15FCE042D4F5679`.
Only `readme.txt` differs from the candidate at the byte level due to expected
checkout line-ending normalization; normalized content is identical.

Alynt Plugin Updater detected the public v1.1.17 to v1.1.18 update on LocalWP
Plugin Tester, and WordPress installed the GitHub asset through the native
Plugins screen. All 62 installed files byte-match the public ZIP, activation
and settings fingerprints remain unchanged, database version `0.1.6` is
preserved, and a fresh updater check reports no remaining update. Post-update
settings smoke acceptance passed and cleanup removed all disposable release
artifacts. Increment 5 is closed.

### Increment 6: Registration And Authentication Services

Status: implementation, collaboration-contract coverage, full gates, candidate
acceptance, v1.1.19 publication, and public updater verification complete.

The registration service currently contains 1,033 lines and exposes 27 public
methods across request routing, protection providers, activity logging,
pending-record persistence, confirmation resend, token handling, account
completion, delivery, username generation, and password policy. The
authentication service contains 532 lines and exposes 12 public methods across
request routing, rate limiting, diagnostics, public messages, password reset,
and role-aware redirects.

Both existing classes will remain the public facades. Their first constructor
arguments and established public methods will be preserved. Collaborators that
orchestrate multi-step flows will call override-sensitive methods through the
facade instead of calling sibling collaborators directly. This maintains the
subclass behavior already exercised by registration completion, resend, and
set-password tests.

The planned boundaries are:

- registration request handling;
- registration protection and activity logging;
- pending-registration persistence and confirmation lifecycle;
- registration email/webhook delivery;
- registration completion plus credential and token policy;
- authentication request handling;
- authentication rate-limit/activity logging;
- authentication public messages;
- authentication password reset; and
- authentication role-aware redirects.

The completed extraction uses eight registration collaborators and five
authentication collaborators behind the two unchanged public facades. A shared
collaborator base forwards override-sensitive calls through each facade so
existing subclass behavior remains available.

Every extracted collaborator is at or below 300 lines.
`class-registration-service.php` is now 386 lines, down from 1,033, and
`class-auth-service.php` is now 185 lines, down from 532. New tests lock the
public APIs, constructor seams, collaborator delegation, override-sensitive
paths, loader order, and structural thresholds. The full suite currently passes
at 410 tests and 2,853 assertions. The v1.1.19 source also passes build, stable
1,104-string POT generation, full PHPCS, all-file PHP and JavaScript syntax,
npm audit with zero vulnerabilities, and diff-integrity checks. Exact-package
inspection found 76 runtime files and 70 syntax-clean PHP files under one
plugin root, no development or backslash entries, and SHA-256
`31C6DE7EFA4A80A9E1DF829AFDD690A3DE7C59574731619B62E05CD64507444F`.

WordPress's native upload-and-replace flow installed the exact candidate over
active v1.1.18 on local-only Plugin Tester. All 76 installed files byte-match
the package with no extras, and the plugin remains active at v1.1.19. Browser
acceptance covered settings, logout, login, lost-password,
registration-disabled, invalid set-password, and same-site redirect handling
without console errors or horizontal overflow at desktop and 390px. Cleanup
removed all temporary helpers, the browser session, the delayed disposable
administrator, and upgrade artifacts. Established settings and active-plugin
fingerprints remain exact, and database version 0.1.6 is unchanged. The
candidate is ready for explicit v1.1.19 release approval.

Release approval was granted and GitHub v1.1.19 was published from merge
commit `bfe0a45`. Build Release run `29741445139` completed successfully. The
public ZIP contains 76 runtime files and 70 syntax-clean PHP files, has no
development or backslash entries, and has SHA-256
`C137ABB90A2726FF1221345DEA43AE8CBB82E211407CA96E4E8157FFD3373BEB`.
Its file set and normalized content match the accepted candidate; only expected
`readme.txt` checkout line endings differ at the byte level.

Alynt Plugin Updater detected and installed the public v1.1.18 to v1.1.19
update on LocalWP Plugin Tester. All 76 installed files byte-match the public
asset, Account Gateway remains active at v1.1.19, established settings and
active-plugin fingerprints remain exact, and database version 0.1.6 is
unchanged. A fresh updater check reports v1.1.19 up to date. Final cleanup
removed the authenticated browser session and helper, left no disposable
candidate users or upgrade artifacts, and confirmed home, login, and
lost-password return HTTP 200. Increment 6 is closed.

### Increment 7: WooCommerce And Email Services

Status: implementation, collaboration-contract coverage, full source gates,
exact-package inspection, LocalWP Plugin Tester acceptance, v1.1.20 release,
and public-updater verification complete.

The WooCommerce integration contains 736 lines and exposes 22 public methods.
It combines plugin detection and takeover state with dashboard navigation,
endpoint routing and native form handling, endpoint rendering, customer-data
normalization, and account URL generation. The email template service contains
733 lines and exposes 18 public methods. It combines template and token
metadata, HTML and plain-text rendering, delivery, WordPress core email
filters, and mutable profile-email suppression state.

Both existing classes will remain the public facades. Their established public
methods, hook callbacks, and default construction behavior will be preserved.
New optional collaborator injection seams may be used by focused tests, while
ordinary no-argument construction remains unchanged. Override-sensitive
collaborator calls will continue through the public facade.

The planned WooCommerce boundaries are:

- account navigation, visibility, icons, and URLs;
- endpoint routing and native account-form processing;
- endpoint rendering; and
- customer account, order, download, payment, and address data.

The planned email boundaries are:

- template and token metadata;
- HTML and plain-text rendering;
- email sending; and
- WordPress core email filters and profile-email suppression state.

Every extracted collaborator must remain at or below 300 lines. Tests must lock
the public APIs, optional constructor seams, delegation, hook registration,
override-sensitive paths, loader order, and structural thresholds. Full build,
POT, PHPCS, PHP and JavaScript syntax, dependency audit, PHPUnit, package
inspection, and LocalWP Plugin Tester acceptance must pass before v1.1.20 is
presented for separate release approval.

The completed extraction uses four WooCommerce collaborators and four email
collaborators behind the two public facades. Both facades and every collaborator
remain at or below 300 lines. New tests lock exact public APIs, optional
constructor injection, delegation, subclass overrides, hook registration,
loader order, and structural thresholds. The full suite passes at 420 tests and
2,911 assertions. Build, stable 1,104-string POT generation, full PHPCS,
all-file PHP and JavaScript syntax, npm audit with zero vulnerabilities,
Composer audit with no advisories, and diff-integrity checks also pass.

The exact release-style package contains 84 runtime files, no development
files, and has SHA-256
`9D3EF6DD59FB8D669DAD26A1536F838DC4F5EAB7DDC3F6D1FB330B2FC543FF47`.
LocalWP Plugin Tester installed that exact candidate over active v1.1.19, and
all 84 installed files byte-match the package. The plugin remains active at
v1.1.20 with unchanged activation, settings, and database-version
fingerprints.

Authenticated browser acceptance covered the WooCommerce settings tab,
dashboard overview, every customer account endpoint, native account-form
fields, email editors, registration-confirmation preview, local test-send
handling, and responsive Payment Methods and email-preview states. No plugin
browser errors, failed dynamic requests, horizontal overflow, or new PHP log
entries were found. Cleanup removed the disposable administrator, browser
session, preview tab, and in-place rollback directory; the complete v1.1.19
rollback ZIP remains local. Novamira MCP was unavailable, so the accepted
normal LocalWP workflow was used. No staging or production site was touched.

Release approval was granted and GitHub v1.1.20 was published from merge
commit `5f32c1b`. Build Release run `29753307309` passed. The public ZIP
contains 84 runtime files, no development files, and has SHA-256
`414A4DDDF90A074F043EB1278A7174B1306DE37B923BF1E2FF5AB14AD6D11635`.
Its file set and normalized content match the accepted candidate; only
`readme.txt` differs byte-for-byte because of expected line-ending
normalization.

Alynt Plugin Updater detected the public 1.1.19 to 1.1.20 update on LocalWP
Plugin Tester, and WordPress installed the GitHub asset through the native
Plugins screen. All 84 installed files byte-match the public ZIP. The plugin
remains active, settings and database fingerprints are unchanged, and a fresh
updater check reports v1.1.20 up to date. Post-update Emails-tab, Payment
Methods, homepage, and branded-login smoke checks passed. Cleanup removed the
disposable release administrator, browser session, duplicate rollback
directory, and upgrade artifacts.
