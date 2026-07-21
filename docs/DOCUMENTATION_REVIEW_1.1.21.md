# Alynt Account Gateway Documentation Review

## Scope

This document records `12-DOCUMENTATION_REVIEW_PROMPT.md` against the immutable public v1.1.21 baseline. The review covered the plugin header, root README and WordPress readme, changelog, settings and hooks references, the active operational/privacy documentation, and PHPDoc conventions in 125 production PHP files.

No package, release, WordPress site, database, or deployment operation was performed. Corrective documentation and regression-test work belongs to a later v1.1.22 candidate.

## Summary

```text
Documentation sources reviewed: 20
Persisted settings documented: 80 / 80
Plugin-owned public filters documented: 2 / 2
Scheduled action contracts documented: 4 / 4
Issues found: 4
Issues corrected: 4
Manual decisions required: 0
```

## Findings And Corrections

### DOC-01 - Incomplete Settings Reference

`docs/SETTINGS.md` was an informative defaults list, but did not document every schema key, type, sanitization rule, settings tab, or several newer dashboard and WooCommerce controls.

It is now a tabbed schema reference for all 80 persisted keys. A sanitization legend explains every stored type, while each row identifies the setting key, type, default, and owner-facing behavior. The document now covers dashboard menus, footer navigation, checkout and order-pay gates, WooCommerce navigation visibility, Reoon policy, diagnostics, all retention settings, and the emergency bypass key.

### DOC-02 - Ambiguous Hooks Reference

The previous hooks document mixed plugin extension points with upstream hooks the plugin consumes. That could lead integrators to rely on an Alynt callback order or treat internal scheduled actions as a general event API.

The replacement documents both plugin filters with parameters and examples, labels internal scheduled actions as internal, explains native WooCommerce endpoint delegation, and lists consumed upstream hooks separately. It also states plainly that webhook delivery is an HTTP integration contract, not a plugin-owned `do_action()` event.

### DOC-03 - Owner-Facing README Gaps

The root README had feature, requirements, installation, configuration, security, privacy, uninstall, development, and documentation sections, but no FAQ or explanation of safe Gateway Screen Preview use. The WordPress readme also lacked an FAQ and an Unreleased entry for the pending corrective candidate.

Both readmes now explain the non-authenticating emergency bypass, why disabled registration omits Create Account, dashboard/WooCommerce boundaries, safe email formatting, and the authenticated preview feature. The root documentation list now links to the settings, hooks, privacy, operations, rollout, readiness, and changelog records.

### DOC-04 - Changelog Context

The Markdown changelog used a correct heading structure but lacked its customary scope statement and did not record the pending documentation reconciliation.

It now declares that it records notable changes and contains an Unreleased Documentation entry. `readme.txt` contains the matching Unreleased summary without changing the published `1.1.21` stable tag.

## PHPDoc Review

All 125 production PHP source files contain the project `@package Alynt_Account_Gateway` header convention. Reviewed classes and public methods use concise purpose, parameter, and return documentation where their contracts need it; hook callbacks and the two public filters include contract-level documentation at their point of use.

The codebase has no per-symbol `@since` tags. Introducing dates for 536 public methods would require inventing history that the repository did not record. This review therefore retains the established accurate PHPDoc convention rather than fabricating lineage. Future newly introduced public extension APIs should include a truthful `@since` tag at creation time; this is documentation-debt policy work, not a release blocker.

## Regression Coverage

`DocumentationReviewTest` now asserts that:

- every key returned by `ALYNT_AG_Settings_Schema::schema()` has a settings-reference row;
- the two public proxy filters, retention action, webhook scheduler actions, and WooCommerce delegation contract remain described;
- README/readme FAQ and preview guidance, changelog Unreleased section, plugin header version, text domain, and WordPress stable tag remain aligned.

The focused test passes with 3 tests and 106 assertions.

## Full Validation

- `npm run build`: passed.
- `npm run make-pot`: passed with 1,166 strings.
- `npm run lint`: passed.
- PHP syntax: 236 files passed.
- JavaScript/MJS syntax: 17 files passed.
- `npm test`: passed with 546 tests and 3,989 assertions.
- `npm audit --audit-level=high`: zero vulnerabilities.
- Composer validation: valid.
- Composer advisory audit: no advisories.
- `git diff --check`: passed.

## Outcome

Prompt 12 is complete. Documentation now matches the settings schema and documented integration boundary, has regression coverage, and leaves no manual decision. Prompt 13, Security Audit, remains the final pre-release workflow.
