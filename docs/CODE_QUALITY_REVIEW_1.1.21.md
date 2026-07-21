# Alynt Account Gateway Code Quality Review

## Scope

This document records `11-CODE_QUALITY_REVIEW_PROMPT.md` against the immutable
public v1.1.21 baseline. The review covered 125 production PHP files and 15
source JavaScript files. Corrective work belongs to a separately approved
v1.1.22 candidate. No package, release, site, database, or deployment operation
was performed.

## Summary

```text
Files reviewed: 140
Issues found: 3
Issues auto-fixed: 3
Issues requiring manual decision: 0
Lint status: fixed and clean
Test execution status: clean
Test command and discovered count: npm test -- --do-not-cache-result; 543 tests
Build status: clean
07A evidence: complete
```

The final normal, reverse, and fixed-random PHPUnit runs each passed with 543
tests and 3,883 assertions. Prompt 07A evidence remains current in
`docs/ADVERSARIAL_TEST_SUITE_REVIEW_1.1.21.md`.

## Issues Found

### CQ-01 - Repeated Security Signal Cards

```text
TYPE: Duplicate
FILE: admin/settings-page/class-security-signal-renderer-a.php:160
ISSUE: The same escaped count-based status-card template appeared in eleven
       render paths across six settings-page components.
ACTION: Refactored into render_security_signal_cards(). The shared renderer
        preserves status, label, count, message, and optional latest-seen
        metadata output.
```

Focused coverage verifies escaped attributes and text, optional metadata, and
the existing provider-triage output contract.

### CQ-02 - Duplicate Preview Asset Ownership

```text
TYPE: Duplicate
FILE: admin/settings-page/class-gateway-preview.php:126
ISSUE: The legacy admin preview duplicated frontend style, script,
       localization, and Turnstile enqueue behavior.
ACTION: Removed the duplicate loader and delegated to
        ALYNT_AG_Frontend::enqueue_preview_assets().
```

The production frontend asset service remains the single owner of preview and
public gateway asset labels and provider-loading decisions.

### CQ-03 - Duplicate WooCommerce Menu Merge

```text
TYPE: Duplicate
FILE: includes/services/class-woocommerce-integration.php:271
ISSUE: The compatibility facade repeated its navigation collaborator's
       standard/custom account-menu merge algorithm.
ACTION: Kept the established private compatibility shim but delegated its
        implementation to ALYNT_AG_WooCommerce_Navigation.
```

The existing routing regression continues to verify required item restoration,
custom endpoint placement, sanitization behavior, and logout ordering through
the compatibility shim.

## Duplicate Code Report

```text
DUPLICATE FOUND:
- Location 1: admin/settings-page/class-security-signal-renderer-a.php
- Location 2: admin/settings-page/class-security-signal-renderer-b.php
- Location 3: admin/settings-page/class-security-review-queue.php
- Location 4: admin/settings-page/class-security-rate-limits.php
- Location 5: admin/settings-page/class-security-pending.php
- Location 6: admin/settings-page/class-security-failure-triage.php
RECOMMENDATION: Completed by extracting render_security_signal_cards().

DUPLICATE FOUND:
- Location 1: admin/settings-page/class-gateway-preview.php
- Location 2: includes/services/class-frontend-assets.php
RECOMMENDATION: Completed by delegating preview loading to the frontend asset
                owner through the existing frontend facade.

DUPLICATE FOUND:
- Location 1: includes/services/class-woocommerce-integration.php
- Location 2: includes/services/class-woocommerce-navigation.php
RECOMMENDATION: Completed by retaining one algorithm in the navigation
                collaborator and one facade delegation shim.
```

The normalized five-line scan also reported HTML closing sequences, table-loop
scaffolding, WordPress document-shell setup, and form submission state changes.
Those were not treated as defects because their surrounding semantics, owners,
or lifecycle contexts differ.

## Reviewed Code-Smell Exceptions

- The mechanical scan found 45 methods longer than 50 lines. These match the
  documented file-structure review set: declarative settings/guidance catalogs,
  cohesive HTML templates, database DDL, and compatibility-sensitive
  transactions. No new mixed-responsibility method was found.
- Fifteen production methods have more than four parameters. Required
  WordPress callback signatures, constructor dependency seams, and established
  transaction or logging contracts were retained to avoid compatibility churn.
- `$data` is confined to exporter payload collections. `$result` identifies an
  immediate typed operation outcome. Neither naming pattern obscures a durable
  domain concept.
- WordPress globals are used at native `$wpdb`, `$wp`, `$wp_filter`, and
  `$wp_version` integration boundaries. No plugin-owned mutable global state was
  found in production code.
- No production TODO, FIXME, HACK, debug console, `var_dump()`, `print_r()`, or
  direct `error_log()` remnant was found.

## Validation

- Focused regression set: 34 tests and 230 assertions.
- PHPUnit normal order: 543 tests and 3,883 assertions.
- PHPUnit reverse order: 543 tests and 3,883 assertions.
- PHPUnit fixed-random seed `20260720`: 543 tests and 3,883 assertions.
- `npm run lint`: passed with no PHPCS/WPCS or PHP-compatibility violation.
- `npm run build`: passed.
- `npm run make-pot`: passed with 1,166 strings.
- PHP syntax: 235 files passed.
- JavaScript/MJS syntax: 17 files passed.
- `npm audit --audit-level=high`: zero vulnerabilities.
- Composer validation: valid.
- Composer advisory audit: no advisories.
- Production PHP, source JavaScript, and source CSS ceilings: passed.
- `git diff --check`: passed.

## Outcome

Prompt 11 is complete. The three verified quality issues are fixed, no manual
decision remains, and all configured final quality gates pass. Prompt 12,
Documentation Review, is next. Prompt 13 remains the final whole-plugin
security audit.
