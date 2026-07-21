# Alynt Account Gateway Internationalization Review

## Summary

- Files reviewed: 140 runtime files (125 PHP and 15 JavaScript).
- Unique catalog strings: 1,149.
- Untranslatable user-facing strings found: 3; all corrected.
- Text-domain issues: 0.
- Pluralization issues: 10; all corrected.
- Placeholder entries without translator guidance: 0.
- Bundled production translations: None; the plugin ships a POT template.

## Hardcoded Strings Corrected

### Admin Media Dialog

- File: `assets/src/admin/modules/media.js`.
- Strings: `Select Image` and `Use Image` fallback values.
- Context: WordPress media-frame title and primary action.
- Correction: JavaScript now relies on the translated `alyntAgAdmin` values
  localized by `admin/class-admin.php`, with an empty defensive fallback.

### Typography Status

- File: `assets/src/admin/modules/typography.js`.
- String: `Current pairing:` fallback value.
- Context: Live typography-preset status.
- Correction: JavaScript now relies on the translated
  `data-status-prefix` value rendered by PHP.

No hardcoded shopper-facing validation, alert, confirmation, loading, or
status strings remain in the source JavaScript modules.

## Pluralization Corrections

Count-aware handling was added for:

- ignored settings keys after import;
- resend cooldown minutes;
- available download counts;
- active registration, resend, login, and password-reset buckets;
- configured attempts and rate-limit window minutes; and
- password requirements met in the frontend JavaScript status.

PHP count-dependent strings use `_n()`. The browser-side password status
selects between two translated templates supplied through
`wp_localize_script()` because its count is computed in JavaScript.

## Text Domain And Loading

- Text domain: `alynt-account-gateway`.
- Plugin header domain and `ALYNT_AG_TEXT_DOMAIN` match the plugin slug.
- Domain path: `/languages`.
- `ALYNT_AG_I18n` loads the domain on `plugins_loaded` from the plugin language
  directory.
- No missing, misspelled, or variable text domains were found.

## Catalog Tooling

The repeatable `scripts/make-pot.mjs` generator now preserves:

- singular translation calls;
- `_n()` plural entries;
- `_x()` context entries;
- `_nx()` contextual plural entries; and
- nearby `translators:` comments.

The regenerated POT contains 1,149 entries, 32 translator-comment references,
and nine plural entries. No catalog message contains embedded HTML or an HTTP
URL, and every placeholder-bearing entry has translator guidance.

## Existing Runtime Evidence

The earlier v1 readiness pass used temporary Spanish LTR and Arabic RTL QA
translations to verify frontend and admin text-domain loading, document
direction, RTL layout, LTR email fields, and absence of frontend horizontal
overflow. Those temporary language files were removed afterward and are not
presented as bundled translations.

## Deferred Boundary

Permanent translated `.po`/`.mo` files remain a localization-content and
translation-maintenance decision. Prompt 09 verifies that the source and POT
are ready for translators; it does not claim professional translation quality
for languages the plugin does not ship.

## Validation

- Normal, reverse-order, and fixed-random PHPUnit runs each pass with 534 tests
  and 3,730 assertions.
- PHPCS passes with the WordPress and PHP 7.4 compatibility standards.
- Production asset build and JavaScript syntax checks pass.
- npm and Composer security audits report no known vulnerabilities.
- Composer configuration, project-wide PHP syntax, and Git diff hygiene pass.
