# Alynt Account Gateway File Structure Review 1.1.21

## Scope

This document records both phases of `02-FILE_STRUCTURE_REVIEW_PROMPT.md`
against the released `v1.1.21` baseline. Phase 1 was read-only. Phase 2 was
explicitly approved and completed on the isolated
`prerelease/v1.1.21-revalidation` branch. No WordPress site, release tag, or
published asset was changed.

## Phase 1 Summary

- Initial inventory: 237 tracked PHP, JavaScript/MJS, and CSS files in the
  reviewed source set: 209 PHP, 14 JavaScript/MJS, and 14 CSS.
- Files over a hard limit: 3.
- Mechanically flagged callables: 109. This count included declarative schema
  providers, translated guidance catalogs, templates, tests, required
  WordPress callback signatures, and genuine orchestration hotspots.
- Mixed-concern facades requiring structural work: compatibility warnings,
  registration service, and frontend gateway.
- Complexity assessment: Complex.

The approved order was:

1. Extract the compatibility registry and hook inspector.
2. Decompose the registration facade.
3. Decompose the frontend facade.
4. Refactor priority PHP and JavaScript callable hotspots.
5. Extend focused tests.
6. Run the complete source gate.

## Phase 2 Changes

### Compatibility Collaborators

- Added `ALYNT_AG_Compatibility_Registry` for known-plugin declarations.
- Added `ALYNT_AG_Compatibility_Hook_Inspector` for callback inspection.
- Reduced `ALYNT_AG_Compatibility_Warnings` to a coordinating facade.
- Preserved optional collaborator injection and public behavior.
- Checkpoint: `42da35c`.

### Registration Facade

- Extracted lifecycle, protection, and credential facade traits.
- Reduced `ALYNT_AG_Registration_Service` to an 84-line coordinator.
- Preserved all 27 public methods and existing collaborators.
- Checkpoint: `34559c8`.

### Frontend Facade

- Added request-context, URL-adapter, access-controller, and gateway-controller
  collaborators.
- Reduced `public/class-frontend.php` from 608 lines to 233 lines.
- Preserved public callbacks, routes, access rules, and optional collaborator
  injection.
- Checkpoint: `57e0495`.

### JavaScript Workflows

- Decomposed dashboard-link event binding and serialization setup.
- Decomposed typography preset state and application.
- Decomposed TinyMCE/native-field dirty-state tracking.
- Decomposed password-policy scoring, requirement rendering, messaging, and
  validity updates.
- Kept the existing five admin and five frontend module boundaries.
- All source modules remain at or below 250 lines.
- Checkpoint: `fa3d42e`.

### Settings Rendering

- Replaced the long admin-notice branch chain with safe notice definitions and
  focused render helpers.
- Split core field rendering into scalar, color, rich-text, select, and
  text/secret helpers.
- Consolidated private renderer arguments into one internal context array.
- Added focused notice and field-renderer tests plus missing WordPress test
  stubs.
- Checkpoints: `ff2e34c`, `7a547aa`.

### Registration, Privacy, And Email

- Split registration request dispatch from start-registration context,
  validation, pending-record creation, and delivery handling.
- Split privacy export retrieval from consent, pending-registration,
  verification, and webhook formatting.
- Added an injected `ALYNT_AG_Email_Html_Renderer` for theme derivation and
  client-safe header, content, and footer composition.
- Split registration completion into user creation, pending-record
  finalization, consent attachment, and non-blocking integrations.
- Checkpoints: `fe763e8`, `47aa613`, `7ebc4c3`.

## Final Structure

- Tracked repository files: 276.
- Tracked PHP files: 222.
- Tracked JavaScript/MJS files: 14.
- Tracked CSS files: 14.
- PHP files over 300 lines: 0.
- Source JavaScript files over 250 lines: 0.
- Source CSS files over 500 lines: 0.
- Undefined loader or collaborator references: 0.
- New circular dependency or public API break: none found.

## Reviewed Callable Exceptions

The final mechanical scan still reports 41 production methods longer than 50
lines. They were reviewed rather than split automatically because the
50-line rule is advisory, while the file ceilings are hard limits.

### Declarative Catalogs

Retained as single-purpose data providers:

- `ALYNT_AG_Settings_Definition_Security_Email::fields()`
- `ALYNT_AG_Settings_Definition_Core::fields()`
- `ALYNT_AG_Settings_Definition_Account_Data::fields()`
- settings-tab guidance, verification guidance, failure-triage items,
  diagnostics snapshot items, readiness checks, rate-limit items, compatibility
  plugin declarations, field help, and dashboard endpoint affordances

Splitting these arrays would scatter one schema or guidance catalog across
artificial methods without reducing runtime branching.

### Cohesive Templates

Retained where one method renders one accessible screen, panel, row, or
WooCommerce data view:

- settings page shell and admin status panels
- login, registration, set-password, dashboard, and invalid-link screens
- diagnostics, email, webhook, pending-registration, and review interfaces
- order, download, address, and payment-method views

These methods are long primarily because of readable HTML/PHP templates.
Their state preparation is already delegated to focused services.

### Cohesive Infrastructure And Transactions

Retained with existing tests and stable signatures:

- database installation DDL
- retention cleanup
- branded login and lost-password transactions
- pending-registration persistence
- WooCommerce customer-data queries
- settings import

These are candidates for later incremental extraction only when a behavioral
change or a dedicated test seam requires it. Splitting them during this
pre-release pass would increase migration, authentication, or compatibility
risk without resolving an oversized class.

## Validation

- Focused compatibility checkpoint: 9 tests, 30 assertions.
- Focused registration facade checkpoint: 74 tests, 464 assertions.
- Focused frontend checkpoint: 26 tests, 130 assertions.
- Focused JavaScript source checkpoint: 8 tests, 173 assertions.
- Focused settings-rendering checkpoint: 14 tests, 52 assertions.
- Focused registration/privacy checkpoint: 47 tests, 255 assertions.
- Focused email checkpoint: 33 tests, 229 assertions.
- Focused registration-completion checkpoint: 13 tests, 123 assertions.
- `npm run build`: passed.
- `npm run make-pot`: passed with 1,104 strings.
- `npm run lint`: passed.
- Full PHPUnit: 438 tests, 3,212 assertions.
- PHP syntax: 222 tracked files passed.
- JavaScript/MJS syntax: 14 tracked files passed.
- `npm audit --audit-level=high`: 0 vulnerabilities.
- Composer advisory audit: no advisories.
- `git diff --check`: passed.
- Source file ceilings: passed with zero over-limit files.

## Outcome

Phase 2 is complete. The approved structural boundaries are in place, all hard
file limits pass, priority callable hotspots were decomposed, and reviewed
declarative/template exceptions remain explicit. Any later correction will
target `v1.1.22`; the published `v1.1.21` tag and asset remain immutable.

The next ordered workflow is `03-ERROR_HANDLING_REVIEW_PROMPT.md` Phase 1,
which remains approval-gated.
