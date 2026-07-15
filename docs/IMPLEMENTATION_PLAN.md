# Alynt Account Gateway Implementation Plan

## Status

- Current phase: v0.1.98 pre-readiness settings UX polish fully validated; awaiting release approval
- Target path: `C:\Development\WordPress\Plugins\alynt-account-gateway`
- Plugin status: v0.1.97 is the current public baseline after GitHub release, public asset inspection, and Alynt Plugin Updater verification.
- Frontend output default: Disabled
- Distribution: Alynt-distributed plugin with GitHub updater compatibility
- Next roadmap: Complete and release the v0.1.98 polish slice, then begin production acceptance and `v1.0.0` preparation in [`V1_READINESS_PLAN.md`](V1_READINESS_PLAN.md).

## v0.1.98 Pre-Readiness Settings UX Polish

### Scope

- [x] Start from the released and updater-verified `v0.1.97` baseline.
- [x] Pair every saved hex-color field with an accessible native color picker and live swatch while retaining the text field as the single persisted value.
- [x] Add bidirectional picker/text synchronization, uppercase normalization, invalid-value feedback, focus styling, and source-level regression coverage.
- [x] Expand heading and body font-stack guidance with concrete Blocksy-loaded Google Font examples and clarify that the plugin does not load fonts.
- [x] Remove the downloads reference from the default login instruction text.
- [x] Change the default Terms path to `/legal/terms/` without overwriting existing saved settings.
- [x] Regenerate translations and pass the production build, PHPCS, full PHPUnit suite, npm audit, and whitespace checks.
- [x] Build and inspect the release candidate, complete focused Plugin Tester browser QA, and restore the test site.
- [ ] Publish the approved release and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Implemented on isolated branch `release/0.1.98`; the main checkout remains untouched.
- Color fields now use an unnamed native picker as a modern live swatch beside the existing named hex input. JavaScript keeps both controls synchronized without introducing another saved setting or a third-party dependency.
- Font guidance now demonstrates `"Poppins", Arial, sans-serif` and `"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif` when those fonts are already loaded by Blocksy.
- Updated schema defaults and settings documentation for `/legal/terms/` and `Welcome back. Log in to manage your orders and account details.` Existing saved settings remain unchanged.
- Local validation passes: production build; POT generation (`997 strings`); PHPCS; `283 tests, 1897 assertions`; npm audit (`0 vulnerabilities`); and `git diff --check`.
- Built and inspected portable branch package `alynt-account-gateway-v0.1.97-branch-qa-v0.1.98.zip`: 45 runtime files, one expected plugin root, zero backslash entries, zero development/source entries, compiled picker synchronization and styling present, font guidance and updated defaults present, and SHA-256 `9AEDCB5AAD00D1ECB3D011138D594F27F59B6EBCBA7FACA957A005AB3CA7E5F8`.
- Novamira MCP is not exposed in this side conversation; the configured Playwright MCP is available for focused browser QA after the required LocalWP target confirmation.
- Installed the inspected branch package through WordPress's replace-current upload flow on confirmed local-only Plugin Tester. The plugin remained active with 45 runtime files and the exact pre-QA settings fingerprint `2d0e919d2f2bc08590b34fcf6ffc6fdc24ebd8e97b6b778f0e67326636226a8e`.
- Packaged-runtime browser QA passed for all eight color controls: each picker is an unnamed accessible native color input with a 64-by-40-pixel live swatch, one named hex input, picker-to-text and text-to-picker synchronization, uppercase normalization, invalid-value `aria-invalid` feedback, and restored valid state. Both Blocksy font-stack examples rendered correctly.
- Responsive QA passed at 1440 pixels and 390 pixels. All color controls remained within the viewport at mobile width with zero document overflow, and packaged admin CSS/JavaScript loaded with HTTP 200.
- Restored the exact public `0.1.97` package through WordPress, returning the installed-copy fingerprint to `C0B1F4393139AF8C924B472E75E8E1CEC524FCC7EE78C77E2035DF066F563721`. Activation and settings hashes match the baseline, both disposable administrators were deleted, the browser was closed, and no upgrade artifacts remain.
- Bumped release-candidate metadata to `0.1.98` across the plugin header/constant, npm metadata, WordPress stable tag and changelog, README, version assertion, and generated POT header.
- Final release validation passes: production build; POT generation (`997 strings`); PHPCS; `283 tests, 1897 assertions`; npm audit (`0 vulnerabilities`); Composer audit (no advisories); PHP syntax; metadata alignment; and `git diff --check`.
- Built and inspected final package `alynt-account-gateway-v0.1.98.zip`: 45 runtime files, one expected plugin root, zero backslash entries, zero development/source entries, aligned header/constant/stable-tag/POT `0.1.98` metadata, exactly one updater header, all color-picker/font-guidance/default markers present, and SHA-256 `92E99C606A59DAB138512E85E0B455688CDDBAA5C5ECADC355C576B52651D75B`.
- Installed that exact final package through WordPress's replace-current flow. Plugin Tester reported active header/constant `0.1.98`, 45 runtime files, all eight unnamed pickers and named hex inputs, rendered font guidance, and an unchanged settings fingerprint.
- Restored public `0.1.97` after final-package smoke. The exact original installed-copy fingerprint, active-plugin hash, and settings hash match the baseline; zero disposable users and zero upgrade artifacts remain, and the browser session is closed.

### Guardrails

- Preserve existing saved color values, import/export behavior, sanitization, frontend CSS variables, and brand-agnostic defaults.
- Do not load remote fonts, add tracking, or add a JavaScript color-picker dependency.
- Treat the changed Terms path and login instruction as defaults for fresh/default-restored configuration only; do not migrate established sites automatically.

### Completion Gate

- [x] Picker changes update the hex field immediately, and valid typed hex values update the swatch.
- [x] Each color retains exactly one named persisted input with an accessible picker label and keyboard focus treatment.
- [x] Font-stack guidance gives copy-ready examples and accurately explains font loading ownership.
- [ ] Public updater verification passes after release approval and publication.

## v0.1.97 Brand-Agnostic Typography Presets

### Scope

- [x] Start from the released and updater-verified `v0.1.96` baseline.
- [x] Add neutral local/system font-pairing presets to Branding without loading remote fonts.
- [x] Keep the existing heading and body CSS font-stack fields as the saved source of truth so custom stacks, imports, and established sites remain compatible.
- [x] Add an immediate accessible admin preview and a clear Custom state when either stack is edited manually.
- [x] Add focused PHP, admin JavaScript, and admin CSS regression coverage.
- [x] Run build, lint, full tests, POT generation, audits, package inspection, and Plugin Tester browser QA.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started from clean, released, and updater-verified `v0.1.96` on branch `release/0.1.97`.
- The existing Branding tab already stores sanitized heading and body CSS font stacks, but requires site owners to know CSS syntax. This slice will add a convenience layer over those existing values rather than changing their schema or storage contract.
- Added Classic Contrast, Modern Sans, Editorial Serif, and Clear Humanist pairings using only local/system font stacks. The non-persistent selector writes the existing heading/body inputs, while direct input switches the selector to Custom without replacing either value.
- Added an inline preview with a polite current-pairing status, explicit no-remote-font guidance, and a no-JavaScript fallback that keeps the existing custom fields available.
- Focused typography/PHP/JavaScript/CSS coverage passes within the full suite: `276 tests, 1854 assertions`; PHPCS, build, POT generation (`996 strings`), npm audit (`0 vulnerabilities`), Composer audit (no advisories), and `git diff --check` pass.
- Built and inspected portable branch package `alynt-account-gateway-v0.1.96-branch-qa-typography-portable.zip`: 45 runtime files, one expected plugin root, zero backslash entries, zero development/source entries, correct `assets/dist` layout, pre-bump `0.1.96` metadata, and all typography PHP/compiled JS/compiled CSS markers present. SHA-256: `965821F9D690D0EF845A1257548DCBA1C1EEBD47BE3E19DDDF90930A0F3343D6`.
- Local-only runtime target is Plugin Tester at `http://plugin-tester.local/`; Novamira MCP is not exposed in the active tool list, so Playwright MCP is the browser verification path after the required target confirmation.
- Installed the verified portable branch package through WordPress's replace-current upload flow. All four presets updated both existing stack fields and the live preview immediately; direct heading-stack input switched to Custom without overwriting the body stack; Modern Sans persisted across save and reload.
- Browser QA passed at 1440px and 390px for typography-control containment, preview layout, and field sizing. The 390px page-level 17px overflow was traced to WordPress admin, Brizy notice, and toolbar elements outside Alynt Account Gateway; all typography controls remained within the viewport.
- Network and console review found no Alynt Account Gateway remote font dependency or runtime error. The page's Google Figtree request, React DevTools warning, and SureForms analytics stylesheet 404 came from other installed admin assets.
- Restored the exact pre-QA settings fingerprint `cb1ce5869e6723144852b720fcbeee191dcf61cc6193e4b29af0f51f4e669940`, returned Alynt Account Gateway to its original inactive state, deleted the temporary administrator and helper, and closed the browser session.
- Bumped release-candidate metadata to `0.1.97` across the plugin header/constant, npm metadata, WordPress readme, sample version assertion, changelog, and README feature/configuration summaries.
- Final release validation passes: production build; POT generation (`996 strings`); PHPCS; `276 tests, 1854 assertions`; npm audit (`0 vulnerabilities`); Composer audit (no advisories); and `git diff --check`.
- Built and inspected final package `alynt-account-gateway-v0.1.97.zip`: one expected plugin root, 45 runtime files, zero backslash entries, zero development/source entries, aligned header/constant/stable-tag/POT `0.1.97` metadata, exactly one updater header, and all typography PHP/compiled JS/compiled CSS markers present. SHA-256: `DAA9663261995978BF5B8365802B7D7DAD43A4E937BB55C208616CC3DCDC8CF5`.
- Installed that exact final package on local-only Plugin Tester through WordPress's replace-current upload flow. WordPress reported `0.1.97`; the packaged admin screen exposed all five preset states, local/system guidance, preview, and immediate Modern Sans updates from compiled assets.
- Returned Alynt Account Gateway to its pre-test inactive state with the exact original settings fingerprint, deleted the final temporary administrator and helper, and closed the browser session. The only console error remained the unrelated pre-existing SureForms analytics stylesheet 404.
- Published GitHub release `v0.1.97` after merging release commit `a670771` into `master` as `e87d5ed`; Build Release workflow run `29360699226` completed successfully and attached the generated runtime ZIP.
- Downloaded and inspected the exact public asset `alynt-account-gateway-v0.1.97.zip`: one expected plugin root, 45 runtime files, zero backslash entries, zero development/source entries, aligned `0.1.97` metadata, exactly one updater header, and all typography PHP/compiled JavaScript/compiled CSS markers present. SHA-256: `83B201EFD9492D965862BAC74C8CA3776832EDC1964907710934576748354625`.
- On restored local-only Plugin Tester, Alynt Plugin Updater `1.1.1` discovered Alynt Account Gateway `0.1.97` over active installed `0.1.93`; the standard WordPress update flow completed successfully and no gateway update remained after a forced refresh.
- Updater-installed runtime verification found active header `0.1.97`, 45 runtime files, zero development files, and all compiled typography markers. Branding browser smoke confirmed all five preset states, the preview, and no-remote-font guidance with zero console errors.
- Preserved both plugins' restored active state and the exact settings fingerprint `2d0e919d2f2bc08590b34fcf6ffc6fdc24ebd8e97b6b778f0e67326636226a8e`, deleted the temporary administrator and helper, and closed the browser session.

### Guardrails

- Use only local/system font stacks; do not add Google Fonts, remote requests, bundled font files, tracking, licensing prompts, or new privacy dependencies.
- Preserve current default stacks, saved custom stacks, import/export behavior, frontend CSS variables, design tokens, screen layout, and frontend-output defaults.
- Keep preset selection as an admin convenience that writes the existing heading/body stack fields; do not create a second competing typography source of truth.

### Completion Gate

- [x] Selecting a preset updates both saved font-stack fields and the preview without a page reload.
- [x] Manually editing either font stack switches the selector to Custom without overwriting the entered value.
- [x] Existing custom stacks and imported settings remain unchanged until an administrator deliberately selects a preset and saves.
- [x] Full validation, final package inspection, and public updater verification pass.

## v0.1.96 Email Reconciliation Stability Audit

### Scope

- [x] Start from the released and updater-verified `v0.1.95` baseline.
- [x] Reproduce the delayed TinyMCE undo and canceled-navigation sequence against the public installed package.
- [x] Keep restored Email settings clean after editor events settle and after a native leave-page dialog is canceled.
- [x] Add focused regression coverage for any reproducible stability gap without broadening the saved-settings boundary.
- [x] Run build, lint, full tests, POT generation, audits, package inspection, and focused Plugin Tester browser QA.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started from clean, released, and updater-verified `v0.1.95` on branch `release/0.1.96`.
- Local-only target remains Plugin Tester at `http://plugin-tester.local/`; Novamira MCP is not exposed in the active tool list, so focused Playwright and LocalWP runtime checks remain the verification path.
- Public-package reproduction confirmed Visual undo remains clean after delayed editor events, while a canceled native leave-page prompt synchronizes all five hidden editor textareas from saved plain paragraphs to equivalent HTML and prevents an ordinary restored field from returning clean.
- Reconciliation now reads canonical TinyMCE content while an editor is visible and raw textarea content in Code mode, keeping representation-only hidden-textarea synchronization outside the dirty comparison without weakening real editor-change detection.
- Initial branch QA confirmed the canceled-navigation restoration fix, then exposed that Visual and Code modes still needed one shared representation. Code and Visual values are now normalized through WordPress `wpautop` plus TinyMCE's DOM parser/serializer before comparison so equivalent formatting is stable across mode switches.
- Focused source coverage passes with `1 test, 36 assertions`; build, PHPCS, and `git diff --check` pass after the canonical editor-value implementation.
- Built and inspected same-version branch package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.96-branch-qa-20260714-193559\alynt-account-gateway-v0.1.95-branch-qa-v0.1.96.zip`: 45 runtime files, zero backslash entries, zero development/source entries, canonical Visual/Code normalization markers present, and SHA-256 `807BA49677D2849F25008CB400435E7491CFF8EA8CFE1AAA2D61F3F87108A524`.
- Installed the branch package over public `v0.1.95` through WordPress `Plugin_Upgrader`; the plugin remained active with 45 runtime files and the settings fingerprint unchanged.
- Playwright branch QA passed for Code-only startup, Code edit/restoration, standalone recipient independence, lazy initialization of all five Visual editors, canceled-navigation restoration, delayed Visual undo, clean Visual/Code mode switching, real-difference prompting, restored disabled/ARIA states, and zero console errors.
- Bumped release-candidate metadata to `0.1.96` across the plugin header/constant, npm metadata, readme stable tag/changelog, sample version assertion, changelog, README feature summary, and generated POT.
- Final release validation passed: build; POT generation (`984 strings`); PHPCS; `271 tests, 1817 assertions`; npm audit (`0 vulnerabilities`); Composer audit (no advisories); and `git diff --check`.
- Built and inspected final package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.96-20260714-194326\alynt-account-gateway-v0.1.96.zip`: 45 runtime files, zero backslash entries, one expected plugin root, zero development/source entries, aligned header/constant/stable-tag/POT `0.1.96` metadata, exactly one updater header, canonical Visual/Code normalization markers, and existing leave-page/action-state guards present. SHA-256: `BFCF0B37321B92C3B37BC83BCF3C88E22C6630B694FC0ABDB7DA63A07CA15393`.
- Installed that exact final package on local-only Plugin Tester through WordPress `Plugin_Upgrader`; final-package state was active `0.1.96` with 45 runtime files, canonical normalization markers present, and unchanged settings fingerprint.
- Final-package Playwright smoke verified canceled-navigation restoration, delayed Visual undo, Code edit/restoration, clean mode switching, native dirty-state prompting, restored disabled/ARIA states, and zero console errors.
- Restored Plugin Tester to published public `v0.1.95`; the plugin is active with 45 runtime files, the settings fingerprint remains `2d0e919d2f2bc08590b34fcf6ffc6fdc24ebd8e97b6b778f0e67326636226a8e`, and the temporary administrator and verifier scripts were removed.
- Published GitHub release `v0.1.96` after merging release commit `2ae0e73` into `master` as `226bca4`; Build Release workflow run `29355773892` completed successfully and attached the generated runtime ZIP.
- Downloaded and inspected the exact public asset `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.96-public-20260714\alynt-account-gateway-v0.1.96.zip`: one expected plugin root, 45 runtime files, zero development/source entries, aligned `0.1.96` metadata, canonical editor normalization markers present, and SHA-256 `91F07C8105AB164D0BC27EAFA52046DFC425495EFCA7274943B78662E77CC8C8`.
- Activated Alynt Plugin Updater on local-only Plugin Tester, forced an update check, confirmed WordPress offered `v0.1.96` over installed public `v0.1.95`, and completed the update through the standard WordPress update action. WordPress then reported Alynt Account Gateway `v0.1.96`.
- Post-updater Playwright smoke passed for ordinary-field dirty detection/restoration, disabled and `aria-disabled` email action states, clean Visual/Code mode switching, and TinyMCE edit/undo reconciliation. The only console error was an unrelated pre-existing SureForms analytics stylesheet `404`.
- Restored Alynt Account Gateway and Alynt Plugin Updater to their pre-test inactive state, deleted the temporary administrator, removed the local verifier helper, and closed the browser session. Plugin Tester retains the public `v0.1.96` package for the next updater cycle.

### Guardrails

- Keep this slice limited to Email-tab client-side save-state reconciliation and its tests unless the runtime evidence identifies a different root cause.
- Do not change persisted settings, template sanitization, preview/test-send endpoints, email delivery, account-email triggers, frontend auth flows, registration, dashboard/WooCommerce behavior, or updater behavior.
- Preserve the standalone one-off test recipient outside the saved-settings dirty boundary.

### Completion Gate

- [x] Exact ordinary-field restoration remains clean before and after canceling a native leave-page prompt.
- [x] Visual editor undo remains clean after delayed editor events settle.
- [x] Real differences still show the warning, disable both email actions, and prompt before navigation.
- [x] Full validation, final package inspection, and public updater verification pass.

## v0.1.95 Email Dirty-State Reconciliation

### Scope

- [x] Start from the released and updater-verified `v0.1.94` baseline.
- [x] Snapshot the saved Email settings form and treat it as dirty only while its current serialized values differ.
- [x] Restore the clean state when ordinary fields or Visual/Text editor content return exactly to their initial values.
- [x] Hide the warning, remove the dirty class, and re-enable Preview Email and Send Test Email when the form becomes clean again.
- [x] Preserve clean editor mode switching, standalone test-recipient independence, valid-save behavior, and native leave-page protection for real unsaved differences.
- [x] Add focused regression coverage and run build, lint, full tests, POT generation, audits, package inspection, and Plugin Tester browser QA.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started from clean, released, and updater-verified `v0.1.94` on branch `release/0.1.95`.
- Replaced touch-only dirty tracking with a serialized snapshot of the saved Email settings form and symmetric dirty/clean rendering for the warning, form class, native disabled state, and `aria-disabled` state.
- TinyMCE change/input/undo/redo events now save their editor content before reconciliation, allowing Visual undo and exact restoration to return clean.
- Plugin Tester QA exposed WordPress Visual-editor HTML normalization after the initial page snapshot. The implementation now updates only the initializing editor field's baseline after TinyMCE settles, preserving unrelated unsaved fields while preventing normalization false positives.
- Focused source coverage passed with `1 test, 25 assertions`; full branch validation passed with build, PHPCS, `271 tests, 1806 assertions`, npm audit (`0 vulnerabilities`), Composer audit (no advisories), and `git diff --check`.
- Playwright on local-only Plugin Tester verified ordinary field change/restoration, Code editor change/restoration, Visual edit/undo, all-five-editor initialization, lazy Visual initialization while another field remained dirty, clean mode switching, standalone test-recipient independence, real-difference navigation prompting, cancel behavior, restored action/ARIA states, zero overflow at `390px`, and zero console errors.
- Inspected branch package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.95-branch-qa-20260714-172446\alynt-account-gateway-v0.1.94-branch-qa-dirty-reconciliation.zip`: 45 runtime files, zero backslash entries, zero development/source entries, compiled form snapshot, settled per-editor baseline, clean-state restoration, and leave-page guards present, SHA-256 `6EB3A768A5511D08BA10BFBBBA0FD65A888C672CF02C0275B31FD9EE3F1CBB5D`.
- Restored Plugin Tester to the published `v0.1.94` asset after QA; the plugin remains active, the settings fingerprint is unchanged, and the temporary administrator and helper scripts were removed.
- Bumped final release metadata to `0.1.95` across the plugin header/constant, npm metadata, readme stable tag/changelog, sample version assertion, changelog, README feature summary, and generated POT.
- Final release validation passed: build; POT generation (`984 strings`); PHPCS; `271 tests, 1806 assertions`; npm audit (`0 vulnerabilities`); Composer audit (no advisories); and `git diff --check`.
- Built and inspected final package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.95-20260714-174026\alynt-account-gateway-v0.1.95.zip`: 45 runtime files, zero backslash entries, zero development/source entries, aligned header/constant/stable-tag/POT `0.1.95` metadata, exactly one updater header, compiled form snapshot, settled per-editor baseline, clean-state restoration, and leave-page guards present, SHA-256 `30254339B8546A52322E0B8B3A793EB5EF9A30FBAEAF3A05A2A79E7EA900C4FA`.
- Merged release commit `dcc36c9` through merge commit `4961b07`, tagged `v0.1.95`, and published GitHub release `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.95`; Build Release workflow run `29346729310` completed successfully.
- Downloaded and inspected public asset `alynt-account-gateway-v0.1.95.zip`: 55 ZIP entries representing 45 runtime files, zero backslash entries, one expected plugin root, zero development/source files, aligned `0.1.95` metadata, exactly one updater header, and compiled dirty-state reconciliation guards present. SHA-256 `0D5C5E01514C504B513D9CA43B7064233EF2A72FD699DFD6AA6A1EDFBB7F4740` matches GitHub's published digest.
- Alynt Plugin Updater on local-only Plugin Tester performed a forced fresh release check from public `0.1.94`, reported public `0.1.95`, and populated WordPress's normal plugin-update transient with package URL `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.95/alynt-account-gateway-v0.1.95.zip`.
- WordPress `Plugin_Upgrader::upgrade()` installed that public package. The headless updater left the plugin inactive after replacement, so the prior active state was restored explicitly; final state is active at `0.1.95`, 45 runtime files, exactly one updater header, unchanged settings fingerprint `2d0e919d2f2bc08590b34fcf6ffc6fdc24ebd8e97b6b778f0e67326636226a8e`, compiled reconciliation guards present, and no update remaining.
- Post-updater Playwright smoke verified all five editors, clean/enabled initial state, ordinary field change/restoration, Visual edit/undo, standalone test-recipient independence, native leave-page confirmation for a real difference, clean reload after discarding the QA edit, and zero console errors. The temporary administrator and verifier scripts were removed.

### Guardrails

- Compare only the saved Email settings form; do not include the standalone one-off test recipient or email action forms.
- Keep reconciliation entirely client-side and do not autosave, mutate persisted settings, or send draft templates to preview/test endpoints.
- Preserve WordPress-native Visual/Text editing, template sanitization, sample tokens, mail-provider delegation, and all actual account-email triggers.

### Completion Gate

- [x] Changing and exactly restoring an ordinary saved field returns to the clean state without a navigation prompt.
- [x] Visual/Text editor changes and undo/restoration reconcile accurately without mode-switch false positives.
- [x] Real unsaved differences still show the warning, disable both email actions, and prompt before navigation.
- [x] Full validation, final package inspection, and public updater verification pass.

## v0.1.94 Unsaved Email Navigation Guard

### Scope

- [x] Start from the released and updater-verified `v0.1.93` baseline.
- [x] Prompt before leaving the settings page when tracked Email tab settings have unsaved changes.
- [x] Keep Code/Visual mode switching clean and preserve the existing ordinary-field and TinyMCE change detection.
- [x] Suppress the leave-page prompt during a legitimate Save Settings submission.
- [x] Keep the standalone test-recipient field independent from the saved-settings dirty state.
- [x] Expand the accessible save-state notice so leaving-page risk is clear without relying on browser-dialog copy.
- [x] Add focused regression coverage and run build, lint, full tests, POT generation, audits, package inspection, and Plugin Tester browser QA.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started from clean, released, and updater-verified `v0.1.93` on branch `release/0.1.94`.
- Added an Email-settings-scoped native `beforeunload` guard that activates only after the existing saved-settings dirty detector runs and clears during a valid Save Settings submission.
- Expanded the accessible save-state notice to explain that unsaved changes must be saved before previewing, test sending, or leaving the page.
- Focused coverage now asserts the leave-page handler, browser event contract, save-submission bypass, and revised warning copy.
- Full validation passed: build; POT generation (`984 strings`); PHPCS; `271 tests, 1796 assertions`; npm audit (`0 vulnerabilities`); Composer audit (no advisories); and `git diff --check`.
- Playwright on local-only Plugin Tester verified clean tab navigation, clean Code/Visual mode switches, dirty saved recipient and subject fields, dirty Code and Visual editor content, cancel/accept behavior, browser Back protection, no prompt during Save Settings, a clean post-save reload, and zero console errors.
- The standalone one-off Send Test Email recipient remained independent: editing it left the warning hidden and both email actions enabled, then tab navigation completed without a prompt.
- At `390px`, the warning and both email actions stayed within the viewport with zero horizontal overflow.
- Inspected release-style branch package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.94-branch-qa-final-20260714-161406\alynt-account-gateway-v0.1.93-branch-qa-navigation-guard-portable.zip`: 45 runtime files, zero backslash entries, zero development/source entries, aligned `0.1.93` baseline metadata, compiled leave-page/save-bypass guards, revised PHP/POT copy, and SHA-256 `7D2B5E36DCDABAEB6660F72DA96A5AEDD961F2BC96247F5BCDA92EAF9F3AD790`.
- Restored Plugin Tester to the published `v0.1.93` asset after QA; the plugin remains active and both temporary administrator accounts and local helper scripts were removed.
- Bumped final release metadata to `0.1.94` across the plugin header/constant, npm metadata, readme stable tag/changelog, sample version assertion, changelog, README feature summary, and generated POT.
- Final release validation passed: build; POT generation (`984 strings`); PHPCS; `271 tests, 1796 assertions`; npm audit (`0 vulnerabilities`); Composer audit (no advisories); and `git diff --check`.
- Built and inspected final package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.94-20260714-161851\alynt-account-gateway-v0.1.94.zip`: 45 runtime files, zero backslash entries, zero development/source entries, aligned header/constant/stable-tag/POT `0.1.94` metadata, exactly one updater header, compiled leave-page/save-bypass guards, revised PHP/POT copy, and SHA-256 `2676E6EB9FB4B1919D2E474FCB5CE98B2BBFA73CA0201056A45FEF07DE7A445F`.
- Merged release commit `e740535` through merge commit `2dde4b1`, tagged `v0.1.94`, and published GitHub release `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.94`; Build Release workflow run `29340949616` completed successfully.
- Downloaded and inspected public asset `alynt-account-gateway-v0.1.94.zip`: 55 ZIP entries representing 45 runtime files, zero backslash entries, zero unexpected roots, zero development/source files, aligned `0.1.94` metadata, exactly one updater header, compiled leave-page/save-bypass guards, revised PHP/POT copy, and SHA-256 `9BA9317CED3C4FBC02AAE2D3356917233D9FFECB312B40FDA7303EE38C181558` matching GitHub's published digest.
- Alynt Plugin Updater on local-only Plugin Tester performed a fresh release check, detected public `0.1.93` to `0.1.94`, and populated WordPress's update transient with package URL `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.94/alynt-account-gateway-v0.1.94.zip`.
- WordPress `Plugin_Upgrader::upgrade()` installed that public package. The headless updater left the plugin inactive after replacement, so the prior active state was restored explicitly; final state is active at `0.1.94`, 45 runtime files, unchanged settings fingerprint, compiled guard present, and no update remaining.
- Post-updater Playwright smoke verified the clean initial state, revised notice copy, dirty warning and disabled actions after a tracked edit, native tab-navigation confirmation, and zero console errors. The temporary administrator and all local verifier scripts were removed.

### Guardrails

- Use the browser's standard `beforeunload` confirmation; do not create a custom modal that can conflict with WordPress navigation or accessibility behavior.
- Keep the guard scoped to the Email settings form and activate it only after a real tracked change.
- Preserve preview/test-send disabling, WordPress-native Visual/Text editing, saved email content, template sanitization, and all mail-provider behavior.

### Completion Gate

- [x] Dirty ordinary and Visual/Text email edits prompt before tab, browser, or external navigation.
- [x] Canceling navigation preserves the dirty state and disabled email actions.
- [x] Saving settings completes without a leave-page prompt and reloads into a clean state.
- [x] Clean pages, editor mode switches, and standalone test-recipient edits do not prompt.
- [x] Full validation, final package inspection, and public updater verification pass.

## v0.1.93 Email Editor Save-State Guard

### Scope

- [x] Start from the released and updater-verified `v0.1.92` baseline.
- [x] Detect unsaved changes to Email tab settings, including WordPress Visual-editor changes.
- [x] Show an accessible status notice explaining that preview and test-send tools use saved settings.
- [x] Disable Preview Email and Send Test Email while tracked email settings are unsaved, without treating the standalone test-recipient field as a saved setting.
- [x] Add focused regression coverage for the settings-page hooks, admin JavaScript behavior markers, and notice styling.
- [x] Run build, lint, full tests, POT generation, audits, package inspection, and Plugin Tester browser QA.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started from clean, released, and updater-verified `v0.1.92` on branch `release/0.1.93`.
- Added Email-tab-only data hooks, an initially hidden `role="status"`/`aria-live="polite"` warning, and guards around both saved-settings email actions.
- Added admin behavior that tracks ordinary settings fields plus all five WordPress TinyMCE editors, reveals the warning after a real edit, and disables Preview Email and Send Test Email with native and ARIA disabled states.
- Kept the standalone test-recipient field independent because that field intentionally does not update the saved default recipient.
- Plugin Tester QA caught and corrected a mode-switch edge case: synthetic textarea/editor initialization events emitted while returning from Code to Visual mode no longer create a false unsaved state.
- Focused validation passed with `8 tests, 65 assertions`; full validation passed with build, POT generation (`984 strings`), PHPCS, `271 tests, 1791 assertions`, npm audit (`0 vulnerabilities`), and Composer audit (no advisories).
- Installed a same-version branch-QA package on local-only Plugin Tester through WordPress `Plugin_Upgrader`; the plugin remained active at `0.1.92` and exposed the compiled save-state and synthetic-event guards.
- Playwright verified clean Code/Visual mode switching, real Visual and Code edits, ordinary field edits, all five TinyMCE bindings, standalone recipient independence, saved-settings reload behavior, accessible warning semantics, disabled action states, and zero overflow at `390px`. Original email settings remained unchanged and temporary site artifacts were removed.
- Inspected current release-style branch package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.93-branch-qa-20260714-141207\alynt-account-gateway-v0.1.92-branch-qa-email-save-state.zip`: 45 runtime files, zero backslash entries, zero development/source entries, translated warning strings and compiled save-state guards present, SHA-256 `C33F3600864243F575DE3FBC95AC9EBE700690164305DA743BB0E5110E8F5EE6`.
- Bumped final release metadata to `0.1.93` across the plugin header/constant, npm metadata, readme stable tag/changelog, sample version assertion, changelog, README feature summary, and generated POT.
- Final release validation passed: build; POT generation (`984 strings`); PHPCS; `271 tests, 1791 assertions`; npm audit (`0 vulnerabilities`); and Composer audit (no advisories).
- Built and inspected final package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.93-20260714-141503\alynt-account-gateway-v0.1.93.zip` with SHA-256 `B6A7C84F010B578BBF1246CB9388BE7B988CA9BC0C9DE0C3B2D317F412D68C33`: 45 runtime files, zero backslash entries, zero development/source entries, aligned header/constant/stable-tag/POT metadata, exactly one updater header, and all save-state, synthetic-event, and translation markers present.
- Installed that exact final package on Plugin Tester through WordPress `Plugin_Upgrader`; the plugin remained active at header/constant `0.1.93` with 45 runtime files. Final Playwright smoke verified clean/enabled initial state, visible warning and disabled actions after a real edit, clean restored state after reload, original subject retained, zero document overflow, and zero console errors. Temporary installer artifacts were removed.
- Published GitHub release `v0.1.93` at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.93`; Build Release workflow run `29332973058` completed successfully.
- Downloaded and inspected public asset `alynt-account-gateway-v0.1.93.zip` as 55 ZIP entries representing 45 runtime files, with zero backslash entries, zero unexpected roots, zero development/source files, aligned `0.1.93` metadata, exactly one updater header, all save-state/synthetic-event/translation markers present, and SHA-256 `160E9F3F5CDB4962F09DF77190870B4578685911D2C7EBC358C579A6BCE12D94` matching GitHub's published digest.
- Alynt Plugin Updater on local-only Plugin Tester refreshed its repository cache, detected public `0.1.92` to `0.1.93`, and populated WordPress's normal update transient with package URL `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.93/alynt-account-gateway-v0.1.93.zip`.
- WordPress `Plugin_Upgrader::upgrade()` installed the public asset with no update remaining. The headless `Automatic_Upgrader_Skin` harness left the plugin inactive after replacement, so the prior active state was restored explicitly; final state is active header/constant `0.1.93`, 45 runtime files, unchanged settings fingerprint, compiled guards present, and no update remaining.
- Post-updater Playwright smoke verified five email editors, clean/enabled initial state, visible warning and disabled actions after a real edit, original subject restored after reload, zero overflow, and zero console errors. Temporary updater artifacts were removed. WordPress emitted the existing non-blocking `str_ends_with()` null deprecation from `wp-includes/load.php:1031` during the headless activation request.

### Guardrails

- Keep this behavior scoped to the Email settings tab and its existing preview/test-send tools.
- Preserve WordPress-native Visual/Text editing, template sanitization, sample tokens, mail-provider delegation, and all actual account-email triggers.
- Do not autosave, submit draft editor content to preview endpoints, or change the saved default test recipient from the standalone test-send form.

### Completion Gate

- [x] Ordinary email settings and all five Visual/Text body editors reliably enter the unsaved state.
- [x] Saving and reloading restores the clean state and re-enables preview/test-send actions.
- [x] The warning is keyboard/screen-reader accessible and the controls remain coherent at admin breakpoints.
- [x] Full local validation and final package inspection pass.
- [x] Public release asset installs through Alynt Plugin Updater with no update remaining.

## Remaining Product Slices

- Tracker audit after `v0.1.97`: all planned product slices are complete, and historical unchecked release gates were reconciled against their recorded validation and updater evidence.
- [x] Brand-agnostic typography setup: add system-font pairing presets and an admin preview while preserving custom stacks and avoiding remote font dependencies.
- [x] Settings readiness and onboarding checks: show whether required URL, registration, protection, branding, email, dashboard, WooCommerce, webhook, privacy, and frontend-output prerequisites are ready before site owners enable public output.
- [x] Real-world WooCommerce dashboard polish: improve branded empty states, endpoint affordances, customer account copy, delegated WooCommerce form styling, order/address/payment-method edge states, and WooCommerce unavailable guidance.
- [x] Settings UX refinement: improve setup grouping, tab-level guidance, validation hints, admin notices, and safe defaults for first-time configuration.
- [x] Email template editor polish: add richer token browsing, per-template reset guidance, preview/test-send ergonomics, and clearer plain-text/core-email limitations.
- [x] Security and anti-spam hardening: improve Reoon policy visibility, provider failure feedback, registration abuse logs, lockout visibility, resend throttling UX, and optional manual-review decisions.
- [x] Accessibility, RTL, and multilingual QA pass: verify keyboard flow, focus states, ARIA messaging, contrast resilience, RTL layout behavior, and translation coverage across frontend/admin screens.
- [x] Frontend visual QA and theme compatibility: smoke common themes, mobile/desktop breakpoints, high-contrast settings, and CSS interference around the gateway shell.
- [x] Admin observability: add clearer diagnostics for auth redirects, blocked wp-admin access, branded auth outcomes, provider verification failures, registration failures, email sends, and webhook failures.
- [x] Import/export/reset experience: strengthen preset export/import, tab-level restore guidance, import validation, and configuration portability.
- [x] Uninstall and data cleanup coverage: add explicit uninstall tests and verify plugin-owned tables/options/scheduled hooks cleanup policy.

## v0.1.91 Small Release Cycle

### Scope

- [x] Raise all explicitly sized frontend gateway and dashboard text to a minimum of 16px.
- [x] Set gateway notices, auth inputs, dashboard inputs, checkboxes, links, back links, and buttons to 18px.
- [x] Keep the logout action labeled `Log Out` while changing the screen heading to `Confirm Logout`.
- [x] Use the current user's stored first name in the dashboard greeting, with a neutral translated fallback instead of a username.
- [x] Make the settings tabs wrap as independent controls so additional rows keep coherent borders and active-state styling.
- [x] Add focused regression coverage for typography thresholds, requested 18px controls, logout copy, first-name greeting, and responsive tab CSS.
- [x] Run build, lint, full tests, POT generation, audit, and diff checks.
- [x] Install and browser-test the release candidate on LocalWP Plugin Tester at frontend and admin breakpoint widths.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started the v0.1.91 maintenance slice from clean, updater-verified `v0.1.90`.
- Raised every explicit frontend gateway/dashboard pixel font size to at least 16px and set notices, auth and WooCommerce account inputs, checkbox copy, gateway links, back links, and buttons to 18px.
- Increased the password-field end padding to preserve clearance for the now-16px password visibility control.
- Changed the logout heading to `Confirm Logout` while preserving the `Log Out` action label and behavior.
- Changed the dashboard greeting from `display_name` to the stored `first_name`, with translated `there` fallback for legacy accounts that have no first name.
- Reworked the settings tab wrapper as a wrapping flex layout with standalone borders and an inset active indicator so wrapped rows remain visually coherent.
- Focused validation passed: `25 tests, 260 assertions`; both touched PHP services passed syntax checks.
- Full repository validation passed: `npm run build`; `npm run make-pot` (`981 strings`); `npm run lint`; `npm test -- --do-not-cache-result` (`264 tests, 1740 assertions`); `npm audit --audit-level=moderate` (`0 vulnerabilities`); and `git diff --check` with only the existing POT line-ending normalization warning.
- Built and inspected branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.90-branch-qa-20260713-220553\alynt-account-gateway-v0.1.90-branch-qa-forward-slash.zip`: 45 runtime files, zero backslash entries, zero dev/source entries, all typography/tab/copy/name markers present, and SHA-256 `653A2C03BA4D4A52880C74EC6F43F18B3FCF5C099859F8842BB5BD8240D62723`.
- Created pre-install restore archive `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.90-branch-qa-20260713-220553\plugin-tester-acg-v0.1.90-before.zip`.
- Installed the branch-QA package through WordPress `Plugin_Upgrader`; the plugin remained active at `0.1.90`, all installed typography/tab/copy/name markers were verified, and the temporary installer/package web artifacts were removed.
- Novamira MCP became available and created a temporary one-time browser access exchange for the configured Playwright MCP; no WordPress credentials were requested, exposed, or changed.
- Playwright verified the settings tabs at `1768`, `1440`, `1200`, `1024`, `800`, and `640px`: tabs wrapped into one, two, or three coherent rows as space required, with zero overlap, zero horizontal document overflow, 16px labels, intact standalone borders, and the active inset indicator retained.
- Playwright verified all eight saved-setting previews (login, registration, lost password, set password, logout, registration disabled, invalid link, and dashboard) at `390x844` and `1440x1000`: all rendered the branded gateway, had zero horizontal overflow, zero clipped controls, no visible text below 16px, and every requested notice/input/checkbox/link/button/back-link group present computed to 18px.
- Auth previews hid the media panel at `390px` and displayed the two-column media/form layout at `1440px`. Logout rendered `Confirm Logout` above the separate `Log Out` action. Dashboard rendered `Welcome, Alynt` from stored `first_name=Alynt` while the account login/display name remained `@user_alynt`.
- Browser evidence was retained as `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.90-branch-qa-20260713-220553\acg-v0.1.91-admin-tabs-1440.png` and `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.90-branch-qa-20260713-220553\acg-v0.1.91-logout-1440.png`. The only console warning was WordPress core's development React Refresh shim notice; there were no plugin/runtime console errors.
- Bumped final release metadata to `0.1.91` across the plugin header/constant, npm metadata, readme stable tag/changelog, sample version assertion, changelog, and generated POT.
- Final release validation passed: `npm run build`; `npm run make-pot` (`981 strings`); `npm run lint`; `npm test -- --do-not-cache-result` (`264 tests, 1740 assertions`); `npm audit --audit-level=moderate` (`0 vulnerabilities`); and `composer audit` (no advisories).
- Built final package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.91-20260714-121408\alynt-account-gateway-v0.1.91.zip` with SHA-256 `B48682889762913BDD878590DC551E8D81ABAA2D89D3A7771F39BFF32309882B`. Inspection found 45 runtime files, zero backslash entries, zero unexpected roots, zero development/source entries, aligned `0.1.91` header/constant/stable-tag/POT metadata, exactly one updater header, and all typography, responsive-tab, first-name greeting, and logout-copy markers present.
- Published GitHub release `v0.1.91`; Build Release workflow run `29327498012` completed successfully.
- Downloaded and inspected public asset `alynt-account-gateway-v0.1.91.zip` as 55 ZIP entries representing 45 runtime files, with zero backslash entries, zero unexpected roots, zero development/source files, aligned `0.1.91` metadata, exactly one updater header, all release markers present, and SHA-256 `9fe2d3fc5482040de020da25b0da07045cb5ca82cf63b0fc7978e79969e33e68` matching GitHub's published digest.
- Alynt Plugin Updater on local-only `plugin-tester.local` detected `0.1.90` to `0.1.91` from the public GitHub release asset URL and installed it through WordPress `Plugin_Upgrader`. Final fresh runtime state: active header/constant `0.1.91`, 45 runtime files, no development files, one updater header, all typography/tab/greeting/logout markers present, and no update remaining.

### Guardrails

- Keep frontend typography changes scoped to `.alynt-ag-gateway` and admin navigation changes scoped to `.alynt-ag-admin`.
- Preserve auth, registration, password, logout action, dashboard/WooCommerce delegation, saved settings, provider decisions, diagnostics, privacy, database schema, and updater behavior.

### Completion Gate

- [x] Focused and full local validation pass.
- [x] Plugin Tester frontend and admin responsive browser checks pass without overflow, clipped controls, or broken tab states.
- [x] Final package inspection passes.
- [x] Public release asset installs through Alynt Plugin Updater with no update remaining.

## v0.1.92 Rich-Text Email Editor Slice

### Scope

- [x] Start from the released and updater-verified `v0.1.91` baseline.
- [x] Replace the five email body textareas with WordPress-native visual editors that retain Visual and Text modes.
- [x] Support safely sanitized headings, bold, italic, links, blockquotes, and ordered/unordered lists in branded HTML emails.
- [x] Preserve template tokens in rich content and keep template-specific action buttons generated from their URL tokens.
- [x] Render approved body HTML instead of displaying tags as text, while stripping unsafe markup and preserving the existing plain-text fallback.
- [x] Keep subjects and preheaders as plain-text fields and keep the subject as the branded email's primary heading.
- [x] Preserve preview and real test-send tools for all five templates, with clear saved-settings behavior and sample token data.
- [x] Add focused coverage for editor rendering, safe HTML storage/output, unsafe markup removal, token replacement inside formatted content, and plain-text conversion.
- [x] Run build, lint, full tests, POT generation, audits, package inspection, Plugin Tester email previews, and controlled test sends.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started from clean, released, and updater-verified `v0.1.91` on branch `release/0.1.92`.
- Added a dedicated `rich_text` settings type only to the five email body fields; frontend screen-copy textareas and subject/preheader fields remain unchanged.
- Added WordPress-native `wp_editor()` instances with Visual and Text modes, format selection, bold, italic, lists, blockquotes, alignment, links, undo/redo, and Quicktags; media buttons and drag/drop uploads are disabled.
- Changed branded HTML rendering to retain `wp_kses_post()`-approved markup instead of escaping tags as text. Token values are escaped before HTML insertion, including URL-specific escaping for action URL tokens, and the plain-text fallback still strips formatting and appends the generated action URL.
- Added focused schema, settings-page, and email-renderer coverage for all five rich-text field types, editor configuration, safe formatting, unsafe markup removal, token injection prevention, token replacement, and plain-text output.
- Focused validation passed: PHP syntax for all touched PHP files; PHPCS; and `SettingsSchemaTest`, `SettingsPageEmailToolsTest`, and `EmailTemplateServiceTest` (`24 tests, 111 assertions`).
- Full branch validation passed: `npm run build`; `npm run make-pot` (`982 strings`); `npm run lint`; `npm test -- --do-not-cache-result` (`269 tests, 1772 assertions`); `npm audit --audit-level=moderate` (`0 vulnerabilities`); and `composer audit` (no advisories).
- Built and inspected branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.92-branch-qa-20260714-131848\alynt-account-gateway-v0.1.91-branch-qa-rich-text.zip` with SHA-256 `C537C3F3B510128B67933F88639DB7B0AA70DB9B9D43EB862E1B2CFDA9184567`: 45 runtime files, zero backslash entries, zero development/source entries, and all rich-text editor, sanitization, renderer, and token-escaping markers present.
- Installed the same-version branch-QA package on local-only Plugin Tester through WordPress `Plugin_Upgrader`; the plugin remained active with 45 runtime files and the installed copy exposed the rich-text schema, native editor, safe HTML renderer, and HTML token-escaping implementation.
- Playwright verified five WordPress-native editors with Visual and Code modes, Heading 1 through Heading 6, bold, italic, ordered/unordered lists, blockquotes, alignment, links, undo/redo, and no media-upload controls. Editor geometry had zero document/editor overflow at `1440`, `1024`, `782`, and `390px` widths.
- Saved a formatted password-reset body through the real settings form and confirmed the heading, strong/emphasis, link, blockquote, list, and reset token survived sanitization. The browser preview rendered those elements, replaced sample tokens, and retained the generated Reset Password action button and fallback URL.
- A read-only malicious-render probe retained approved headings/emphasis while stripping scripts, iframes, event-handler attributes, and JavaScript URLs. Its plain fallback contained no HTML tags and retained the action URL.
- SureMails simulation mode was enabled only for controlled QA. All five template sends succeeded to the reserved `alynt-ag-v0192-qa@example.test` address and were logged as simulated with no external delivery; the formatted password-reset HTML was present in its log. SureMails simulation and the exact original password-reset body were then restored, and temporary QA packages were removed from Plugin Tester.
- Browser evidence was retained as `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.92-branch-qa-20260714-131848\acg-v0.1.92-rich-email-editor.png` and `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.92-branch-qa-20260714-131848\acg-v0.1.92-rich-email-preview.png`. There were no browser console errors; the only warning was WordPress core's development React Refresh shim notice.
- Bumped final release metadata to `0.1.92` across the plugin header/constant, npm metadata, readme stable tag/changelog, sample version assertion, changelog, and generated POT.
- Final release validation passed: `npm run build`; `npm run make-pot` (`982 strings`); `npm run lint`; `npm test -- --do-not-cache-result` (`269 tests, 1772 assertions`); `npm audit --audit-level=moderate` (`0 vulnerabilities`); and `composer audit` (no advisories).
- Built final package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.92-20260714-134045\alynt-account-gateway-v0.1.92.zip` with SHA-256 `BB7827FCC8AC2EEAFA09A3B362828A47C3D51201A666468D75510DA0D1DB1630`. Inspection found 45 runtime files, zero backslash entries, zero unexpected roots, zero development/source leaks, aligned `0.1.92` header/constant/stable-tag/POT metadata, exactly one updater header, and all native-editor, sanitization, safe-renderer, and HTML-token-escaping markers present.
- Published GitHub release `v0.1.92` at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.92`; Build Release workflow run `29329920602` completed successfully.
- Downloaded and inspected public asset `alynt-account-gateway-v0.1.92.zip` as 55 ZIP entries representing 45 runtime files, with zero backslash entries, zero unexpected roots, zero development/source leaks, aligned `0.1.92` metadata, exactly one updater header, all rich-text release markers present, and SHA-256 `a11f15128664bcb2a775b88bbce74cdd3005642ef257f00732c23043ab9a6361` matching GitHub's published digest.
- Alynt Plugin Updater on local-only `plugin-tester.local` detected `0.1.91` to `0.1.92` from the public GitHub release asset URL. WordPress populated its normal plugin-update transient and installed the release through `Plugin_Upgrader::upgrade()`; final fresh runtime state is active header/constant `0.1.92`, 45 runtime files, no development files, all five rich-text schema keys, native editor/safe renderer/token escaping present, restored email settings, SureMails simulation disabled, and no update remaining.
- Post-updater Playwright smoke confirmed the public installed copy renders five Visual/Code editors, no media controls, no document or editor overflow at `1440px`, the original password-reset body restored, and no console errors. WordPress emitted the same non-blocking `str_ends_with()` null deprecation from `wp-includes/load.php:1031` during `Automatic_Upgrader_Skin`; the update itself completed successfully.

### Guardrails

- Use WordPress-native editor and sanitization APIs; do not introduce a standalone editor dependency.
- Do not allow scripts, iframes, forms, event-handler attributes, or other executable email-body markup.
- Keep media uploads and arbitrary template-shell editing out of this first rich-text slice.
- Preserve existing branded wrapper, logo, colors, action buttons, account-email triggers, disable toggles, registration flow, and mail-provider delegation.
- Preserve the WordPress core pending profile email-change request's documented plain-text limitation.

### Completion Gate

- [x] All five body fields provide keyboard-accessible Visual and Text editing modes.
- [x] Supported formatting survives save, preview, and delivered HTML test email without unsafe markup.
- [x] Plain-text rendering remains readable and includes the applicable action URL.
- [x] Full local validation and final package inspection pass.
- [x] Public release asset installs through Alynt Plugin Updater with no update remaining.

## v0.1.90 Small Release Cycle

### Scope

- [x] Finish the Admin observability slice from the updater-verified `v0.1.89` baseline.
- [x] Audit existing diagnostics, verification-log, webhook-log, and Security tab coverage before adding UI.
- [x] Add an Advanced Tools operational snapshot that summarizes redirects/admin blocks, branded auth outcomes, provider verification failures, registration flow failures, account email failures, and webhook delivery failures.
- [x] Keep raw diagnostics export/recent-event behavior available while making support triage faster.
- [x] Preserve auth behavior, registration outcomes, provider policy decisions, webhook delivery behavior, privacy retention behavior, frontend output, dashboard/WooCommerce behavior, and updater behavior.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.90` from clean `master` after updater-verified `v0.1.89`.
- Audit confirmed the Security tab already surfaced most individual signals, while Advanced Tools still required reading raw diagnostics rows to understand account-gateway health.
- Added `Operational Snapshot` to Advanced Tools, backed by the existing diagnostics log, verification log, and webhook log helpers.
- The snapshot groups the admin observability concerns into six operator cards: `Redirects and Admin Blocks`, `Branded Auth Outcomes`, `Provider Verification Failures`, `Registration Flow Failures`, `Account Email Failures`, and `Webhook Delivery Failures`.
- Focused validation passed: `SettingsPageSettingsToolsTest` (`2 tests, 15 assertions`).
- Release-candidate validation passed before version bump: `npm run build`; `npm run make-pot` (`980 strings`); `npm run lint`; `npm audit --audit-level=moderate` (`0 vulnerabilities`); and `npm test -- --do-not-cache-result` (`261 tests, 1647 assertions`).
- Release-candidate validation passed after version bump: `npm run build`; `npm run make-pot` (`980 strings`); `npm run lint`; `npm audit --audit-level=moderate` (`0 vulnerabilities`); `npm test -- --do-not-cache-result` (`261 tests, 1647 assertions`); and `git diff --check` with only line-ending normalization warnings for `readme.txt` and `languages/alynt-account-gateway.pot`.
- Local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.90-20260712-175245\alynt-account-gateway-v0.1.90.zip` and inspected as 45 runtime files, no source/dev package files, `0.1.90` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, and the new Advanced Tools operational snapshot strings present, with SHA-256 `04E7089B074AB9DEDA20DD3E6873F04848BF78644ABF6DFD0ED9061103E0F5B7`.
- GitHub release `v0.1.90` was published at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.90`; release workflow run `29199160372` completed successfully.
- Public release asset `alynt-account-gateway-v0.1.90.zip` was downloaded and inspected as 45 files, with no development directories, `0.1.90` header/constant/stable tag, the Advanced Tools operational snapshot strings, and exactly one `GitHub Plugin URI` updater header, with SHA-256 `776864CEBFE24CF8921BF82445A67567BE07CEACDC28B6716719A2E3D3BF62F9`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from public `v0.1.89` to public `v0.1.90`, detecting package URL `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.90/alynt-account-gateway-v0.1.90.zip`, upgrading through WordPress `Plugin_Upgrader`, reactivating ACG, confirming active `0.1.90`, the new operational snapshot source string, and no remaining update.
- The temporary updater smoke script was removed from the LocalWP webroot and work folder after verification. Novamira MCP was not exposed in the active tool list.

### Guardrails

- Keep observability changes read-only/admin-only. Do not change public auth, registration, provider decisions, email/webhook sending behavior, storage schema, retention cleanup, frontend rendering, dashboard/WooCommerce behavior, privacy behavior, or updater metadata beyond the release version.

### Completion Gate

- [x] Existing observability coverage was audited before adding UI.
- [x] Advanced Tools now has a support-oriented diagnostics snapshot for the named product concerns.
- [x] Focused and full local validation pass.
- [x] Final package inspection passes.
- [x] Plugin Tester installed-package smoke passes.
- [x] Public release asset installs through Alynt Plugin Updater with no update remaining.

## v0.1.89 Small Release Cycle

### Scope

- [x] Finish the Frontend visual QA and theme compatibility slice from the updater-verified `v0.1.88` baseline.
- [x] Run current gateway screens through mobile, breakpoint-edge, and desktop browser geometry checks using Playwright MCP.
- [x] Make the single-column breakpoint inclusive through `800px` so fractional CSS viewport calculations do not leave a nominal `799px` viewport in two-column mode.
- [x] Add higher-specificity, gateway-scoped form, control, and link constraints that neutralize theme-injected minimum widths, margins, and shadows without affecting page elements outside the gateway.
- [x] Recheck forced-colors, reduced-motion, focus visibility, frontend asset loading, native-login avoidance, clipped controls, and console errors.
- [x] Preserve screen copy, routes, submitted fields, registration/login/password behavior, saved settings, dashboard/WooCommerce delegation, provider verification, rate limits, diagnostics, privacy behavior, database schema, and updater behavior.
- [x] Run focused tests, build, lint, full tests, audit, POT generation, package inspection, and final Plugin Tester browser smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.89` from clean `master` after updater-verified `v0.1.88`.
- Initial Playwright matrix covered login, registration, registration error, lost password, invalid set-password, invalid-link resend throttle, and logout screens at `360x800`, `390x844`, `799x900`, `800x900`, and `1440x1000`; all screens used the branded gateway, loaded plugin CSS, avoided native WordPress login, and had no baseline horizontal overflow or clipped controls.
- The matrix exposed a nominal `799px` viewport where `(max-width: 799px)` did not match because of fractional browser viewport evaluation, leaving the media panel visible and the shell in two-column mode.
- A theme-interference test using ordinary input/button/form/link selectors exposed `181px` login overflow and `229px` registration overflow on a `390px` viewport because injected minimum widths, margins, and shadows could outrank the existing scoped resets.
- Changed the breakpoint to `(max-width: 800px)` and added higher-specificity gateway constraints for form paragraphs/fieldsets, inputs, password toggles, action buttons, and links.
- Post-fix Playwright injection checks passed at `390`, `799`, `800`, `801`, and `1440px`: zero overflow, single-column through `800px`, two-column at `801px`, zeroed input/button minimum widths and margins, and removed injected shadows.
- Forced-colors and reduced-motion runtime checks passed with system foreground/background colors, visible focused-input outline, hidden decorative pattern, and no browser console errors.
- Focused validation passed: `FrontendCssSourceTest` (`7 tests, 62 assertions`) and `SampleTest` (`1 test, 2 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`966 strings`); `npm audit --audit-level=moderate` (`0 vulnerabilities`); and `npm test -- --do-not-cache-result` (`260 tests, 1638 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.89-20260712-172537\alynt-account-gateway-v0.1.89.zip` and inspected as 45 runtime files, no source/dev package files, `0.1.89` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, compiled `800px` breakpoint, input/button/link guardrails, and forced-colors support present, with SHA-256 `E089ABD2BC2A2F14F8CE1BE2237CE9661F4FAE1780B78A04F8245B7E44AA323F`.
- Plugin Tester installed-package smoke passed on the local-only `plugin-tester.local` site: active `0.1.89`, 45 runtime files, no development files, exactly one updater header, compiled `800px` breakpoint, input/button/link guardrails, and forced-colors support present.
- Final Playwright matrix against the installed release candidate covered seven gateway states at `390`, `799`, `800`, `801`, and `1440px` for 35 combinations with zero failures, zero horizontal overflow, zero clipped controls, single-column layout through `800px`, two-column layout from `801px`, branded output, and no native-login fallback while QA output was enabled.
- Installed-package theme-interference injection passed on mobile login and registration with zero overflow/clipping, `0px` control minimum widths and margins, removed injected shadows, and zeroed link margins. Desktop login and mobile registration screenshots were captured, and no browser console errors were reported.
- Plugin Tester settings were restored to their exact pre-QA values after browser testing; `/login` again followed the restored frontend-disabled behavior to native `wp-login.php`, and temporary QA/install scripts and packages were removed. Novamira MCP was not exposed in the active tool list.
- GitHub release `v0.1.89` was published at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.89`; release workflow run `29198485615` completed successfully.
- Public release asset `alynt-account-gateway-v0.1.89.zip` was downloaded and inspected as 45 files, with no development directories, `0.1.89` header/constant/stable tag, compiled inclusive `800px` breakpoint, no stale `799px` breakpoint, input/button/link guardrails, forced-colors support, and exactly one `GitHub Plugin URI` updater header, with SHA-256 `D7C24C5579253ED0A967C65EF89165AF030A496A25813D32AAA9DDEA1E13A0B2`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to public `v0.1.88`, detecting `0.1.88` -> `0.1.89` with package URL `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.89/alynt-account-gateway-v0.1.89.zip`, upgrading through WordPress `Plugin_Upgrader`, reactivating ACG, confirming active `0.1.89`, the inclusive breakpoint, all guardrails, 45 runtime files, no development files, exactly one updater header, and no remaining update.
- The temporary updater smoke script and public downgrade package were removed from the LocalWP webroot after verification. Novamira MCP was not exposed in the active tool list.

### Guardrails

- Keep CSS changes scoped to `.alynt-ag-gateway`; do not alter WordPress theme elements outside the gateway, screen content, form semantics, routes, auth/registration behavior, settings, provider decisions, diagnostics, emails, dashboard/WooCommerce delegation, privacy behavior, database schema, or updater metadata beyond the release version.

### Completion Gate

- [x] Browser evidence covers public gateway screens across mobile, breakpoint-edge, and desktop viewports.
- [x] Theme-interference injection no longer causes horizontal overflow or distorted control constraints.
- [x] Forced-colors, reduced-motion, focus visibility, branded routing, and console checks pass.
- [x] Full local validation and package inspection pass.
- [x] Final installed-package Playwright matrix passes on Plugin Tester.
- [x] Public release asset installs through Alynt Plugin Updater with no update remaining.

## v0.1.88 Small Release Cycle

### Scope

- [x] Finish the actionable manual-review portion of the Security and anti-spam hardening slice from the updater-verified `v0.1.87` baseline.
- [x] Add admin-only review decisions for allowed flagged Reoon verification rows without changing registration outcomes or the site-wide Reoon policy.
- [x] Persist review decision, reviewer ID, and review timestamp on the plugin-owned verification log and exclude resolved rows from unresolved queue counts.
- [x] Record a privacy-conscious audit event containing the verification log ID and decision, not the registrant email address.
- [x] Protect review writes with `manage_options`, a row-specific nonce, a strict decision allowlist, and eligibility revalidation against the stored provider/status/block state.
- [x] Preserve registration, account creation, provider API calls, Reoon allow/block behavior, rate-limit thresholds, frontend output, emails, dashboard/WooCommerce behavior, privacy erasure, retention cleanup, and updater behavior.
- [x] Run focused tests, build, lint, full tests, audit, POT generation, package inspection, and Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.88` from clean `master` after updater-verified `v0.1.87`.
- Added `legitimate` and `monitor` review decisions for allowed Reoon statuses ending in `_flagged`; blocked rows and non-Reoon rows remain ineligible.
- Added verification-log review metadata and bumped the plugin database schema to `0.1.4`; existing installs migrate through the established `dbDelta()` upgrade path.
- Added unresolved-only Manual Review Queue counts, per-row review controls, recorded-decision display, and success/failure admin notices.
- Added focused coverage for schema markers, queue resolution, review rendering, secure persistence, first-write-only decisions, audit logging, privacy export, and rejection of blocked or unknown decisions.
- Focused validation passed: PHP syntax for touched PHP files; `SettingsPageSecurityStatusTest` (`29 tests, 421 assertions`); `DatabaseReviewSchemaTest` (`1 test, 6 assertions`); `PrivacyServiceTest` (`5 tests, 31 assertions`); and `SampleTest` (`1 test, 2 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`966 strings`); `npm audit --audit-level=moderate` (`0 vulnerabilities`); and `npm test -- --do-not-cache-result` (`260 tests, 1633 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.88-20260712-164250\alynt-account-gateway-v0.1.88.zip` and inspected as 45 runtime files, no source/dev package files, `0.1.88` header/constant/stable tag, database schema `0.1.4`, exactly one `GitHub Plugin URI` updater header, review handler/immutable-decision/schema/privacy-export markers present, compiled review-control CSS present, and SHA-256 `2BBF8A090F765F91EE4E4CC06DBFE7EB7111E571560A4EFD2B5A149B9A1FB8D4`.
- Plugin Tester smoke passed on the local-only `plugin-tester.local` site: the installed release candidate remained active at header/constant/stable tag `0.1.88`, migrated the verification log table to schema `0.1.4`, added all three review columns, recorded a `monitor` decision with reviewer/timestamp, rejected a second attempt to rewrite that decision as `legitimate`, changed the unresolved queue count from `1` to `0`, wrote an audit event without the test email address, retained exactly one updater header, and removed the temporary verification/audit rows and web artifacts. Novamira MCP was not exposed in the active tool list.
- GitHub release `v0.1.88` was published at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.88`; release workflow run `29197208069` completed successfully.
- Public release asset `alynt-account-gateway-v0.1.88.zip` was downloaded and inspected as 45 files, with no development directories, `0.1.88` header/constant/stable tag, database schema `0.1.4`, exactly one `GitHub Plugin URI` updater header, review handler/immutable-decision/schema/privacy-export markers present, compiled review-control CSS present, and SHA-256 `DB6ADDDB617B1AC8F5F0C54F86FACAD9D9B9D7E286CD57AFA6C435A45D1BE7F2`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to public `v0.1.87`, detecting `0.1.87` -> `0.1.88` with package URL `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.88/alynt-account-gateway-v0.1.88.zip`, upgrading through WordPress `Plugin_Upgrader`, reactivating ACG, confirming active `0.1.88`, schema `0.1.4`, all review markers, 45 runtime files, no development files, exactly one updater header, and no remaining update.
- The temporary updater smoke script and public downgrade package were removed from the LocalWP webroot after verification. Novamira MCP was not exposed in the active tool list.

### Guardrails

- Do not alter public registration outcomes, account creation, Reoon provider requests, Reoon status classification, the configured allow/block policy, rate-limit enforcement, frontend copy, email delivery, webhook payloads, dashboard/WooCommerce behavior, privacy erasure, retention windows, or updater behavior.

### Completion Gate

- [x] Review decisions are limited to allowed flagged Reoon rows and known decision values.
- [x] Review writes are capability- and nonce-protected and produce a redacted audit event.
- [x] Resolved rows no longer contribute to unresolved manual-review queue counts.
- [x] Full local validation and package inspection pass.
- [x] Plugin Tester schema migration and review-action smoke pass.
- [x] Public release asset installs through Alynt Plugin Updater with no update remaining.

## v0.1.87 Small Release Cycle

### Scope

- [x] Continue the Accessibility, RTL, and multilingual QA pass from the released `v0.1.86` baseline.
- [x] Localize the password visibility status labels in standalone admin gateway previews so previewed password toggles do not fall back to hard-coded English strings.
- [x] Preserve behavior: no visible frontend copy changes, layout changes, route handling changes, saved settings changes, database schema changes, registration flow changes, provider policy changes, rate-limit changes, email delivery changes, dashboard endpoint changes, WooCommerce action delegation changes, diagnostics storage changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.87` from clean `master` after updater-verified `v0.1.86`.
- Added `passwordVisible` and `passwordHidden` localized labels to the standalone admin gateway preview asset localization path.
- Added focused coverage for admin preview password visibility status labels.
- Initial focused validation passed: PHP syntax for touched admin/test/plugin files; focused `SettingsPagePreviewAssetsTest` (`1 test, 7 assertions`), `FrontendAssetsTest` (`3 tests, 20 assertions`), and `SampleTest` (`1 test, 2 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`955 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`256 tests, 1604 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.87-20260712-132754\alynt-account-gateway-v0.1.87.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.87` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, preview password visibility status label markers present, and SHA-256 `29E879E1F986A6B485EF0C06655CB123083D7C646D2E2F9C9208463752EA2A61`.
- Initial Plugin Tester smoke was blocked because `http://plugin-tester.local/` did not respond on port 80 during the first local-only smoke attempt. The temporary web smoke script was removed from the LocalWP webroot after the failed connection.
- Plugin Tester package smoke passed after the LocalWP site was available again: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.87`, stable tag `0.1.87`, exactly one `GitHub Plugin URI` updater header, preview password visibility status label markers present, 45 runtime files, no source/dev package files, and the temporary web smoke script was removed.
- GitHub release `v0.1.87` was published at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.87`; the release workflow run `29195911082` completed successfully.
- Public release asset `alynt-account-gateway-v0.1.87.zip` was downloaded and inspected as 45 files, with no development directories, `0.1.87` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, preview password visibility status label markers present, and SHA-256 `23B02E471564E055B07E852C2CEFA11E727E645E83CF603ADB1354E7E49CC9A`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.86` asset, detecting `0.1.86` -> `0.1.87` with package URL `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.87/alynt-account-gateway-v0.1.87.zip`, upgrading through WordPress `Plugin_Upgrader`, reactivating ACG, confirming active `0.1.87`, confirming both preview password status labels, and confirming no remaining update.
- Temporary updater smoke script and downgrade package were removed from the LocalWP webroot after verification. Novamira MCP was not exposed in the active tool list.

### Guardrails

- Do not change visible frontend copy, layout, auth routes, frontend output gating, saved settings, database schema, registration flow, provider verification decisions, rate-limit enforcement, email delivery, dashboard/WooCommerce endpoint behavior, diagnostics storage schema, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover standalone admin preview password visibility status labels.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Local package inspection validates installed-package markers.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public GitHub release asset inspection passes.
- [x] Alynt Plugin Updater detects and installs `v0.1.87` from public `v0.1.86` on Plugin Tester with no update remaining.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.86 Small Release Cycle

### Scope

- [x] Continue the Accessibility, RTL, and multilingual QA pass from the released `v0.1.85` baseline.
- [x] Add screen-reader status updates for password visibility toggles on the login and set-password screens.
- [x] Localize hidden status labels for the visible/hidden password state.
- [x] Preserve behavior: no visible copy changes, layout changes, route handling changes, saved settings changes, database schema changes, registration flow changes, provider policy changes, rate-limit changes, email delivery changes, dashboard endpoint changes, WooCommerce action delegation changes, diagnostics storage changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.86` from clean `master` after updater-verified `v0.1.85`.
- Added hidden polite atomic status regions beside password visibility toggles on the login form and both set-password password fields.
- Updated frontend password-toggle JavaScript to announce `Password is visible.` and `Password is hidden.` through the hidden status region after each toggle.
- Added localized frontend labels for the password visibility status messages.
- Added focused coverage for the localized labels, JS status update behavior, login screen status region, and set-password status regions.
- Initial focused validation passed: PHP syntax for touched frontend service files and focused `FrontendJsSourceTest` (`3 tests, 11 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`955 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`255 tests, 1597 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.86-20260707-233439\alynt-account-gateway-v0.1.86.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.86` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, password visibility status marker checks present, and SHA-256 `F530E829F8C6073122F9052BF81C2DE21E1C3CC35822F9CB3814B4481FC8EBC7`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP and then running a settled-state verification: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.86`, stable tag `0.1.86`, exactly one `GitHub Plugin URI` updater header, password visibility status marker checks present, 45 runtime files, no source/dev package files, Novamira MCP was not exposed in the active tool list, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.86`; Build Release workflow run `28929563916` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.86.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.86` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, password visibility status marker checks present, and SHA-256 `A08D2248C02C106901876B82B55BB8ECF6A88D9F66B72D5BBFFEAA5686A9DF0E`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.85` asset, clearing Alynt Plugin Updater scanner/release cache, seeding release data through the updater's force-fresh check path, confirming Alynt Plugin Updater detected `0.1.85` -> `0.1.86` with the public `v0.1.86` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.86`, stable tag `0.1.86`, exactly one `GitHub Plugin URI` updater header, password visibility status marker checks present, no remaining update, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change visible frontend copy, layout, auth routes, frontend output gating, saved settings, database schema, registration flow, provider verification decisions, rate-limit enforcement, email delivery, dashboard/WooCommerce endpoint behavior, diagnostics storage schema, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover password visibility screen-reader status updates.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.85 Small Release Cycle

### Scope

- [x] Continue the Accessibility, RTL, and multilingual QA pass from the released `v0.1.84` baseline.
- [x] Add explicit, consistent live-region semantics to auth feedback and dashboard fallback states.
- [x] Give redirect-landed success messages stable IDs and atomic polite status announcements.
- [x] Give redirect-landed error messages explicit assertive atomic alert announcements.
- [x] Preserve behavior: no copy changes, route handling changes, saved settings changes, database schema changes, registration flow changes, provider policy changes, rate-limit changes, email delivery changes, dashboard endpoint changes, WooCommerce action delegation changes, diagnostics storage changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.85` from clean `master` after updater-verified `v0.1.84`.
- Added stable IDs and `aria-live`/`aria-atomic` semantics for login success states, login errors, lost-password sent/errors, registration sent/errors, set-password errors, invalid-link resend success/errors, verification placeholder status, WooCommerce unavailable alert, and WooCommerce endpoint fallback status.
- Included login success IDs in the login form `aria-describedby` chain when success messages are present after redirects.
- Added focused frontend screen coverage for polite atomic success/status regions, assertive atomic error alerts, verification placeholder status semantics, and dashboard fallback live regions.
- Initial focused validation passed: PHP syntax for touched frontend service files and focused `FrontendLoginScreenTest` plus `SampleTest` (`3 tests, 33 assertions`). The full suite below covered all touched frontend tests.
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`953 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`254 tests, 1588 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.85-20260707-193237\alynt-account-gateway-v0.1.85.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.85` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, live-region marker checks present, and SHA-256 `3DC494F8E3B8FD29185C1FCF4633C9091E74973B40B2338C35B3A634CAE433F9`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.85`, main file header/constant `0.1.85`, stable tag `0.1.85`, exactly one `GitHub Plugin URI` updater header, live-region marker checks present, 45 runtime files, no source/dev package files, Novamira MCP was not exposed in the active tool list, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.85`; Build Release workflow run `28886658745` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.85.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.85` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, live-region marker checks present, and SHA-256 `452D8FFD192645CA3CEAE2AE9696ED5DE5FD5F6615B6F0F230BA5F27149A2844`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.84` asset, clearing Alynt Plugin Updater scanner/release cache after the deliberate downgrade, confirming Alynt Plugin Updater detected `0.1.84` -> `0.1.85` with the public `v0.1.85` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.85`, stable tag `0.1.85`, exactly one `GitHub Plugin URI` updater header, live-region marker checks present, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change frontend copy, auth routes, frontend output gating, saved settings, database schema, registration flow, provider verification decisions, rate-limit enforcement, email delivery, dashboard/WooCommerce endpoint behavior, diagnostics storage schema, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover frontend live-region accessibility semantics.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.84 Small Release Cycle

### Scope

- [x] Continue the Frontend visual QA and theme compatibility pass from the released `v0.1.83` baseline.
- [x] Add CSS guardrails that make auth gateway form controls, buttons, links, and WooCommerce dashboard controls more resilient against theme-injected margins, shadows, text transforms, letter spacing, line height, and grid overflow.
- [x] Extend box-sizing coverage to the gateway root and pseudo-elements.
- [x] Preserve behavior: no route handling changes, saved settings changes, database schema changes, registration flow changes, provider policy changes, rate-limit changes, email delivery changes, dashboard endpoint changes, WooCommerce action delegation changes, diagnostics storage changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.84` from clean `master` after updater-verified `v0.1.83`.
- Added frontend CSS normalization for auth form paragraphs/fieldsets, inputs, password toggles, checkboxes, buttons, links, and WooCommerce dashboard form controls so common theme styles are less likely to distort the gateway shell.
- Added source-level CSS tests covering pseudo-element box-sizing, form margin resets, input/button shadow and margin resets, link wrapping, text-transform resets, dashboard input min-width protection, and dashboard button normalization.
- Initial focused validation passed: PHP syntax for touched PHP files and focused `FrontendCssSourceTest` plus `SampleTest` (`7 tests, 57 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`953 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`254 tests, 1579 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.84-20260707-191051\alynt-account-gateway-v0.1.84.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.84` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, compiled CSS guardrail markers present, and SHA-256 `9C4150E3F7B61C0482AE98C5BC0EC610ECE172805CA97433EEADEFCE264B80DD`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.84`, main file header/constant `0.1.84`, stable tag `0.1.84`, exactly one `GitHub Plugin URI` updater header, compiled CSS guardrail markers present, 45 runtime files, no source/dev package files, Novamira MCP was not exposed in the active tool list, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.84`; Build Release workflow run `28885170977` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.84.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.84` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, compiled CSS guardrail markers present, and SHA-256 `327DC37895AD786870344B183DAE0554962F58E5D875679719831E04CFA452D2`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.83` asset, clearing Alynt Plugin Updater scanner/release cache after the deliberate downgrade, confirming Alynt Plugin Updater detected `0.1.83` -> `0.1.84` with the public `v0.1.84` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.84`, stable tag `0.1.84`, exactly one `GitHub Plugin URI` updater header, compiled CSS guardrail markers present, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change auth routes, frontend output gating, saved settings, database schema, registration flow, provider verification decisions, rate-limit enforcement, public messages, email delivery, dashboard/WooCommerce endpoint behavior, diagnostics storage schema, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover frontend theme-compatibility CSS guardrails.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.83 Small Release Cycle

### Scope

- [x] Continue the Security tab admin observability and accessibility/multilingual QA pass from the released `v0.1.82` baseline.
- [x] Render the already-computed Provider Failure Triage `Latest seen` metadata on the Provider Failure Triage cards.
- [x] Remove the misplaced dormant latest-seen metadata block from unrelated Security tab signal cards.
- [x] Preserve behavior: no provider policy changes, rate-limit threshold changes, registration flow changes, saved settings changes, database schema changes, frontend copy changes, public response changes, diagnostics storage changes, email delivery changes, dashboard/WooCommerce behavior changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.83` from clean `master` after updater-verified `v0.1.82`.
- Fixed the Provider Failure Triage renderer so cards now show `Latest seen` metadata when matching recent verification logs include timestamps.
- Added focused render coverage so the Security tab output must include latest-seen metadata for Turnstile/Reoon provider failures.
- Initial focused validation passed: PHP syntax for edited admin/test files; focused `SettingsPageSecurityStatusTest` (`26 tests, 402 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`953 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`254 tests, 1569 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.83-20260707-190000\alynt-account-gateway-v0.1.83.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.83` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, Provider Failure Triage marker present, Provider Failure Triage latest-render marker present, and SHA-256 `6EC6482D858723CE72CE27E8BD3334F8BA5086F3548AD172C9BF520B82393B6A`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.83`, main file constant is `0.1.83`, stable tag `0.1.83`, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, Provider Failure Triage marker present, runtime Provider Failure Triage render includes latest-seen metadata, 45 runtime files, no source/dev package files, Novamira MCP was not exposed in the active tool list, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.83`; Build Release workflow run `28883922595` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.83.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.83` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, Provider Failure Triage marker present, Provider Failure Triage latest-render marker present, and SHA-256 `5B76703E99FB479620FFE4ECD05242B4563B0F9782D7E4A1BD3F90990D04F3A2`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.82` asset, clearing Alynt Plugin Updater scanner/release cache after the deliberate downgrade, confirming Alynt Plugin Updater detected `0.1.82` -> `0.1.83` with the public `v0.1.83` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.83`, stable tag `0.1.83`, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, Provider Failure Triage marker present, Provider Failure Triage latest-render marker present, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change provider verification decisions, Reoon flagged-status policy behavior, Turnstile verification behavior, rate-limit enforcement, registration flow, public messages, saved settings, database schema, diagnostics storage schema, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover rendered Provider Failure Triage latest-seen metadata.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.82 Small Release Cycle

### Scope

- [x] Continue security and anti-spam hardening plus admin observability from the released `v0.1.81` baseline.
- [x] Add privacy-safe latest-seen timestamps to Security tab Provider Failure Triage cards.
- [x] Cover Turnstile and Reoon configuration/connectivity/response failure recency without exposing email addresses, tokens, IP addresses, raw API responses, or query values.
- [x] Preserve behavior: no provider policy changes, rate-limit threshold changes, registration flow changes, saved settings changes, database schema changes, frontend copy changes, public response changes, diagnostics storage changes, email delivery changes, dashboard/WooCommerce behavior changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.82` from clean `master` after updater-verified `v0.1.81`.
- Added `Latest seen` metadata to the existing Security tab Provider Failure Triage cards when matching recent verification logs include timestamps.
- Added latest matching timestamp selection for Turnstile configuration, Turnstile connectivity, Turnstile challenge rejection, Reoon configuration, Reoon connectivity, and Reoon unexpected response failures.
- Initial focused validation passed: PHP syntax for edited admin/test files; focused `SettingsPageSecurityStatusTest` (`25 tests, 396 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`953 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`253 tests, 1563 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.82-20260707-182055\alynt-account-gateway-v0.1.82.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.82` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, latest-seen helper marker present, Provider Failure Triage marker present, and SHA-256 `EA1E39867BE727898B80AB97FB58DF4327542ABD4135E2FB8B049602FE6E5DEE`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.82`, main file constant is `0.1.82`, stable tag `0.1.82`, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, latest-seen helper marker present, Provider Failure Triage marker present, 45 runtime files, no source/dev package files, Novamira MCP was not exposed in the active tool list, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.82`; Build Release workflow run `28882675381` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.82.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.82` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, latest-seen helper marker present, Provider Failure Triage marker present, and SHA-256 `603D4251C4E23C68D11301A1074C2A68D03B17A7FC15813A5FFF556585764A19`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.81` asset, clearing Alynt Plugin Updater scanner/release cache after the deliberate downgrade, confirming Alynt Plugin Updater detected `0.1.81` -> `0.1.82` with the public `v0.1.82` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.82`, stable tag `0.1.82`, exactly one `GitHub Plugin URI` updater header, `Latest seen` marker present, latest-seen helper marker present, Provider Failure Triage marker present, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change provider verification decisions, Reoon flagged-status policy behavior, Turnstile verification behavior, rate-limit enforcement, registration flow, public messages, saved settings, database schema, diagnostics storage schema, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover provider failure latest-seen timestamp selection.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.81 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA pass from the released `v0.1.80` baseline.
- [x] Change set-password requirement checklist state from `aria-current` to checkbox-style `aria-checked` semantics.
- [x] Render each password requirement as a non-interactive disabled checkbox status item with an initial unchecked state.
- [x] Cover rendered requirement semantics and frontend JavaScript state updates with focused tests.
- [x] Preserve behavior: no password policy changes, validation threshold changes, form submission changes, saved settings changes, frontend routing changes, visual CSS changes, provider verification changes, diagnostics changes, email delivery changes, dashboard/WooCommerce behavior changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.81` from clean `master` after updater-verified `v0.1.80`.
- Updated set-password password requirement rows to render as `role="checkbox" aria-checked="false" aria-disabled="true"` status items.
- Updated frontend password policy JavaScript to synchronize requirement state with `aria-checked` instead of `aria-current`.
- Initial focused validation passed: PHP syntax for edited runtime/test files; focused `FrontendSetpasswordScreenTest` (`6 tests, 58 assertions`) and `FrontendJsSourceTest` (`2 tests, 6 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`952 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`253 tests, 1557 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.81-20260707-180328\alynt-account-gateway-v0.1.81.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.81` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, requirement checkbox marker present, built frontend JavaScript `aria-checked` marker present, built frontend JavaScript `aria-current` marker absent, and SHA-256 `30602B7B98D7ADAD40D25E31AD5E0A31E2D947BAFDC3303DA773B27081BD8257`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.81`, stable tag `0.1.81`, exactly one `GitHub Plugin URI` updater header, requirement checkbox marker present, built frontend JavaScript `aria-checked` marker present, built frontend JavaScript `aria-current` marker absent, 45 runtime files, no source/dev package files, Novamira MCP was not exposed in the active tool list, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.81`; Build Release workflow run `28880752152` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.81.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.81` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, requirement checkbox marker present, built frontend JavaScript `aria-checked` marker present, built frontend JavaScript `aria-current` marker absent, and SHA-256 `C40D58A23F25CD1D05A12D1394C878D250C8863608B6F34C7854D471F88C7BFE`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.80` asset, clearing Alynt Plugin Updater scanner/release cache after the deliberate downgrade, confirming Alynt Plugin Updater detected `0.1.80` -> `0.1.81` with the public `v0.1.81` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying the final active plugin as `0.1.81` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.81`, stable tag `0.1.81`, exactly one `GitHub Plugin URI` updater header, requirement checkbox marker present, built frontend JavaScript `aria-checked` marker present, built frontend JavaScript `aria-current` marker absent, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change password requirements, password reset or registration completion behavior, public copy, visible styling, saved settings, frontend routes, provider verification, rate limits, diagnostics, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover rendered requirement checkbox semantics and JavaScript `aria-checked` synchronization.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.80 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA pass from the released `v0.1.79` baseline.
- [x] Add explicit `dir="ltr"` / `dir="rtl"` attributes to auth gateway shell, set-password preview shell, and dashboard shell containers.
- [x] Cover default LTR and site RTL output with focused frontend shell and dashboard tests.
- [x] Preserve behavior: no route changes, saved settings changes, visual CSS changes, screen copy changes beyond release notes, field-level direction changes, registration flow changes, provider verification changes, rate-limit changes, diagnostics changes, email delivery changes, dashboard/WooCommerce behavior changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.80` from clean `master` after updater-verified `v0.1.79`.
- Added shell-level direction attributes based on `is_rtl()` to the reusable auth gateway shell, set-password preview shell, and frontend dashboard shell.
- Added a test-controlled `is_rtl()` stub and focused frontend coverage. Initial validation passed: PHP syntax for edited runtime/test files; focused `FrontendGatewayShellTest` (`10 tests, 24 assertions`) and `FrontendDashboardScreenTest` (`11 tests, 79 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`952 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`252 tests, 1551 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.80-20260707-173212\alynt-account-gateway-v0.1.80.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.80` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, auth shell direction marker present, dashboard shell direction marker present, RTL helper marker present, and SHA-256 `ACF650694E4130B58D0AEA75E71DF4E00028804EB84C65A0EC5D7E03C788195E`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.80`, fresh-request loaded constant `0.1.80`, stable tag `0.1.80`, exactly one `GitHub Plugin URI` updater header, auth shell direction marker present, dashboard shell direction marker present, RTL helper marker present, 45 runtime files, no source/dev package files, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.80`; Build Release workflow run `28879457171` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.80.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.80` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, auth shell direction marker present, dashboard shell direction marker present, RTL helper marker present, and SHA-256 `C74E066242C4D7AE799127C4EE8CB454271B4EAF0533B110B0A4010A81DFF892`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.79` asset, clearing Alynt Plugin Updater scanner/release cache after the deliberate downgrade, confirming Alynt Plugin Updater detected `0.1.79` -> `0.1.80` with the public `v0.1.80` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying the final active plugin as `0.1.80` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.80`, stable tag `0.1.80`, exactly one `GitHub Plugin URI` updater header, auth shell direction marker present, dashboard shell direction marker present, RTL helper marker present, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change frontend routing, saved settings, visual styling, user-facing account flow behavior, field-level `dir="ltr"` handling for email/password/path-like values, provider verification, rate limits, diagnostics, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover LTR/RTL shell direction output for auth, set-password preview, and dashboard shells.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.79 Small Release Cycle

### Scope

- [x] Continue security and anti-spam hardening from the released `v0.1.78` baseline.
- [x] Add privacy-safe next-step triage guidance to Recent Registration Verification Activity rows in the Security tab.
- [x] Cover Reoon flagged/blocked/provider-error rows, Turnstile challenge/config/connectivity rows, rate-limit rows, registration-flow rows, and generic fallback rows without exposing full email addresses or query values.
- [x] Preserve behavior: no provider policy changes, rate-limit threshold changes, registration flow changes, saved settings changes, database schema changes, frontend copy changes, public response changes, diagnostics behavior changes, email delivery changes, dashboard/WooCommerce changes, privacy cleanup changes, or updater behavior changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.79` from clean `master` after updater-verified `v0.1.78`.
- Added a `Next Step` column to the Security tab Recent Registration Verification Activity table using a new row-level helper that derives support/admin triage guidance from existing provider, status, and blocked fields only.
- Added focused `SettingsPageSecurityStatusTest` coverage. Initial validation passed: PHP syntax for the edited admin and test files; focused `SettingsPageSecurityStatusTest` (`25 tests, 390 assertions`).
- Release validation passed: `npm run build`; `npm run lint`; `npm run make-pot` (`952 strings`); `npm audit --audit-level=moderate`; focused `SettingsPageSecurityStatusTest` (`25 tests, 390 assertions`); and `npm test -- --do-not-cache-result` (`250 tests, 1542 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.79-20260707-165757\alynt-account-gateway-v0.1.79.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.79` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Security tab `Next Step` column marker present, next-step helper marker present, Reoon next-step triage marker present, and SHA-256 `A652466963BC8FAC7795A1A1BDD7621E900A762E02FC4E83BFEFD509AB5B153C`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.79`, fresh-request loaded constant `0.1.79`, stable tag `0.1.79`, exactly one `GitHub Plugin URI` updater header, Security tab `Next Step` column marker present, next-step helper marker present, Reoon next-step triage marker present, 45 runtime files, no source/dev package files, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.79`; Build Release workflow run `28877775027` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.79.zip` was downloaded from GitHub and inspected as 55 entries, 45 files, 10 directory entries, no backslash entries, no dev entries, `0.1.79` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Security tab `Next Step` column marker present, next-step helper marker present, Reoon next-step triage marker present, and SHA-256 `B37126AB8C89FA3DA217B42CCB5AAC3FB1A0734CF91ECF95AD0E33970D0DCA1F`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.78` asset, confirming Alynt Plugin Updater detected `0.1.78` -> `0.1.79` with the public `v0.1.79` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying the final active plugin as `0.1.79` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, header/constant `0.1.79`, stable tag `0.1.79`, exactly one `GitHub Plugin URI` updater header, Security tab `Next Step` column marker present, next-step helper marker present, Reoon next-step triage marker present, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change provider verification decisions, Reoon flagged-status policy behavior, Turnstile verification behavior, rate-limit enforcement, registration flow, public messages, saved settings, database schema, diagnostics storage, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover Security tab next-step triage rows and fallback messages.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.78 Small Release Cycle

### Scope

- [x] Continue admin observability from the released `v0.1.77` baseline.
- [x] Enrich blocked `wp-admin` access diagnostics with privacy-safe request path, request method, and query-key names.
- [x] Surface the latest blocked admin path, destination path, and query-key names in Security tab Access Control Signals when diagnostics context is available.
- [x] Preserve existing access-control behavior: no role/capability changes, redirect changes, frontend-output changes, saved settings changes, diagnostics storage schema changes, or updater metadata changes beyond the release version.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.78` from clean `master` after updater-verified `v0.1.77`.
- Added privacy-safe blocked-admin diagnostics context for request path, request method, and query-key names while continuing to omit query values.
- Added Security tab Access Control Signals detail for the latest blocked-admin event when diagnostics rows include request context.
- Added focused `FrontendRoutingTest` and `SettingsPageSecurityStatusTest` coverage. Initial validation passed: PHP syntax for edited runtime/admin/test files; focused `FrontendRoutingTest` (`7 tests, 39 assertions`) and `SettingsPageSecurityStatusTest` (`24 tests, 372 assertions`).
- Release validation passed: PHP syntax for edited runtime/admin/test files; `npm run build`; `npm run lint`; `npm run make-pot` (`930 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`249 tests, 1524 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.78-20260707-152329\alynt-account-gateway-v0.1.78.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.78` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, blocked-admin request-path/query-key markers present, Security tab latest-blocked-path/query-key markers present, and SHA-256 `383C9B1CD0A2D0D3A10AA72EEB5C8B73595F6D3196058F3D094DB1BC26D2F6D7`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader` under LocalWP web PHP: active plugin option contains `alynt-account-gateway/alynt-account-gateway.php`, `get_plugins()` reports `0.1.78`, loaded constant `0.1.78`, main file header/constant are `0.1.78`, stable tag `0.1.78`, exactly one `GitHub Plugin URI` updater header, blocked-admin request-path/query-key markers present, Security tab latest-blocked-path/query-key markers present, 45 runtime files, no source/dev package files, and temporary web smoke scripts were removed.
- Published GitHub release `v0.1.78`; Build Release workflow run `28870776144` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.78.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.78` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, blocked-admin request-path/query-key markers present, Security tab latest-blocked-path/query-key markers present, and SHA-256 `8B4BE8F4547E4CCF68C016F4EC9BFAA4F6552AC85C5AC936EC1409C2B6EB6651`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.77` asset, confirming Alynt Plugin Updater detected `0.1.77` -> `0.1.78` with the public `v0.1.78` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.78` header/constant, stable tag `0.1.78`, exactly one `GitHub Plugin URI` updater header, blocked-admin request-path/query-key markers present, Security tab latest-blocked-path/query-key markers present, 45 runtime files, no source/dev package files, and no temporary web verifier scripts remaining.

### Guardrails

- Do not change who can access `wp-admin`, where blocked users are redirected, admin toolbar policy, login routing, public frontend behavior, saved settings, database schema, provider verification, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover privacy-safe blocked-admin diagnostics and Security tab summary detail.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.77 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA pass from the released `v0.1.76` baseline.
- [x] Associate configurable instruction notices with their frontend account forms through form-level `aria-describedby` relationships.
- [x] Cover login, lost password, registration, set-password, registration-disabled, invalid-link, and logout notice IDs without changing visible copy or behavior.
- [x] Keep field-level error descriptions intact while adding form-level context for assistive technology.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.77` from clean `master` after updater-verified `v0.1.76`.
- Added reusable frontend component helpers for notice presence checks and sanitized `aria-describedby` attribute generation.
- Added stable IDs to configurable instruction notices and associated them with the relevant frontend forms.
- Preserved existing field-level error relationships for invalid fields and password requirement/status descriptions.
- Added focused frontend component and screen coverage. Initial validation passed: PHP syntax for the edited frontend services; focused `FrontendComponentsTest` (`6 tests, 16 assertions`), `FrontendLoginScreenTest` (`3 tests, 29 assertions`), `FrontendRegisterScreenTest` (`4 tests, 35 assertions`), `FrontendLostpasswordScreenTest` (`4 tests, 25 assertions`), `FrontendSetpasswordScreenTest` (`6 tests, 55 assertions`), and `FrontendStateScreensTest` (`4 tests, 30 assertions`).
- Release validation passed: PHP syntax for edited runtime and focused test files; `npm run build`; `npm run lint`; `npm run make-pot` (`927 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`249 tests, 1517 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.77-20260707-145711\alynt-account-gateway-v0.1.77.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.77` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, frontend `describedby_attribute` helper marker present, login/register/set-password/invalid-link instruction markers present, and SHA-256 `30281485EF9D21E7D1C8A8303F266AE622990F0CF247944C633C29F13F81498B`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin upgraded from `0.1.76` to `0.1.77`, fresh-request loaded constant `0.1.77`, stable tag `0.1.77`, exactly one `GitHub Plugin URI` updater header, frontend `describedby_attribute` helper marker present, login/register/set-password/invalid-link instruction markers present, 45 runtime files, no source/dev package files, and Novamira MCP was available for verification.
- Published GitHub release `v0.1.77`; Build Release workflow run `28868430529` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.77.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.77` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, frontend `describedby_attribute` helper marker present, login/register/set-password/invalid-link instruction markers present, and SHA-256 `35E82DEFDA65985195D7E6518206790A4381567A944F2529FAA9AACB5B1C809A`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.76` asset, confirming Alynt Plugin Updater detected `0.1.76` -> `0.1.77` with the public `v0.1.77` GitHub release asset URL, upgrading through WordPress `Plugin_Upgrader`, and verifying the final active plugin as `0.1.77` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.77` header/constant, stable tag `0.1.77`, exactly one `GitHub Plugin URI` updater header, frontend `describedby_attribute` helper marker present, login/register/set-password/invalid-link instruction markers present, 45 runtime files, and no source/dev package files.

### Guardrails

- Do not change frontend routing, saved settings, visual styles, screen copy, registration flow, password policy, provider verification, rate limits, diagnostics, email delivery, privacy cleanup, dashboard/WooCommerce behavior, or updater metadata beyond the release version.

### Completion Gate

- [x] Focused tests cover instruction notice IDs and form-level accessible descriptions.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.76 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA pass from the released `v0.1.75` baseline.
- [x] Add current-page semantics to dashboard account links so assistive technology can identify the active account area.
- [x] Keep matching path-only and behavior-neutral: no route changes, saved settings changes, WooCommerce endpoint changes, visual style changes, email behavior, provider verification, rate limits, diagnostics, privacy cleanup, or updater metadata changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.76` from clean `master` after updater-verified `v0.1.75`.
- Added `aria-current="page"` to matching dashboard account links and account section shortcuts.
- Added focused `FrontendDashboardScreenTest` coverage. Initial validation passed: PHP syntax and focused test (`10 tests, 76 assertions`).
- Release validation passed: PHP syntax for the edited dashboard screen and focused test; `npm run build`; focused `FrontendDashboardScreenTest` (`10 tests, 76 assertions`); `npm run lint`; `npm run make-pot` (`927 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`247 tests, 1503 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.76-20260707-141800\alynt-account-gateway-v0.1.76.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.76` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, dashboard `aria-current` marker present, dashboard current-link helper marker present, and SHA-256 `830D5113E064C648DF5F9DAA1A58F486D2E0FA6E90EB33E23AB8264BC5E1046C`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.76` header/constant, stable tag `0.1.76`, exactly one `GitHub Plugin URI` updater header, dashboard `aria-current` marker present, dashboard current-link helper marker present, 45 runtime files, no source/dev package files, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.76`; Build Release workflow run `28866477048` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.76.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.76` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, dashboard `aria-current` marker present, dashboard current-link helper marker present, and SHA-256 `BDA78E595BDC87C7C33118CC3071F5E3D15083ABE3E5DC366DC5BEB4B98136FA`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.75` asset, forcing update detection to `0.1.75` -> `0.1.76`, upgrading through the public `v0.1.76` GitHub release asset URL from the updater transient, and verifying the final active plugin as `0.1.76` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.76` header/constant, stable tag `0.1.76`, exactly one `GitHub Plugin URI` updater header, dashboard `aria-current` marker present, dashboard current-link helper marker present, 45 runtime files, and no source/dev package files.

### Guardrails

- Do not change dashboard routes, URL generation, custom-link settings, WooCommerce endpoint detection/delegation, frontend-output gating, saved settings, screen copy beyond release notes, provider verification, rate limits, diagnostics events, email delivery, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover current-page semantics for dashboard account links.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.75 Small Release Cycle

### Scope

- [x] Continue security and anti-spam hardening from released `v0.1.74` baseline.
- [x] Add a read-only manual-review decision playbook for Reoon flagged email statuses in the Security tab.
- [x] Cover role-account, catch-all, unknown/inbox-full, and always-blocked status families with default decision, tighten-when, and review-first guidance.
- [x] Keep behavior unchanged: no Reoon policy logic, registration flow, saved settings, settings schema/defaults, provider verification, rate limits, diagnostics, frontend output, dashboard/WooCommerce, privacy cleanup, or updater metadata changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.75` from clean `master` after updater-verified `v0.1.74`.
- Added a manual-review decision playbook below the Security tab Manual Review Queue using a read-only admin table.
- Added focused `SettingsPageSecurityStatusTest` coverage. Initial validation passed: PHP syntax and focused test (`24 tests, 370 assertions`).
- Release validation passed: PHP syntax for the main plugin, edited settings page, and focused test; `npm run build`; focused `SettingsPageSecurityStatusTest` (`24 tests, 370 assertions`); `npm run lint`; `npm run make-pot` (`927 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`246 tests, 1500 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.75-20260707-135027\alynt-account-gateway-v0.1.75.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.75` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, manual-review playbook marker present, manual-review helper marker present, and SHA-256 `5E5753E14604F214C0191D8C20E81EC04FCA37C5C7B1163A083436126642B9D3`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.75` header/constant, stable tag `0.1.75`, exactly one `GitHub Plugin URI` updater header, manual-review playbook marker present, manual-review helper marker present, 45 runtime files, no source/dev package files, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.75`; Build Release workflow run `28864644735` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.75.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.75` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, manual-review playbook marker present, manual-review helper marker present, and SHA-256 `4DE0E9ACB8B5007C4CC9807AB4BBCDF9C3F25910C4C14802A3DE41B77B9C007E`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.74` asset, forcing update detection to `0.1.74` -> `0.1.75`, upgrading through the public `v0.1.75` GitHub release asset URL from the updater transient, and verifying the final active plugin as `0.1.75` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.75` header/constant, stable tag `0.1.75`, exactly one `GitHub Plugin URI` updater header, manual-review playbook marker present, manual-review helper marker present, 45 runtime files, and no source/dev package files.

### Guardrails

- Do not change Reoon verification behavior, flagged-status policy logic, registration flow, settings keys/types/defaults/sanitization, provider error handling, rate limits, diagnostics events, frontend output, emails, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover rendered manual-review playbook and decision rows.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.74 Small Release Cycle

### Scope

- [x] Continue security and anti-spam hardening from released `v0.1.73` baseline.
- [x] Improve resend-throttle UX accessibility by associating cooldown guidance with the rate-limited resend form and email field.
- [x] Keep behavior unchanged: no rate-limit thresholds, resend flow, token handling, email delivery, settings schema/defaults, diagnostics, dashboard/WooCommerce, privacy cleanup, or updater metadata changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.74` from clean `master` after updater-verified `v0.1.73`.
- Added an `id` to the resend cooldown guidance and included it in `aria-describedby` when the confirmation resend form is rate-limited.
- Added focused `FrontendStateScreensTest` coverage. Initial validation passed: PHP syntax and focused test (`4 tests, 28 assertions`).
- Release validation passed: PHP syntax for the main plugin, edited frontend state screen, and focused test; `npm run build`; focused `FrontendStateScreensTest` (`4 tests, 28 assertions`); `npm run lint`; `npm run make-pot` (`905 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`245 tests, 1482 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.74-20260707-132848\alynt-account-gateway-v0.1.74.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.74` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, resend guidance ID marker present, resend describedby logic marker present, and SHA-256 `2EC8C86F3BA8F1BE23E9EA1ABCA0CCCE6F7881A12F9A29F67BDB7565ABB84D01`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.74` header/constant, stable tag `0.1.74`, exactly one `GitHub Plugin URI` updater header, resend guidance ID marker present, resend describedby logic marker present, 45 runtime files, no source/dev package files, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.74`; Build Release workflow run `28863485457` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.74.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.74` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, resend guidance ID marker present, resend describedby logic marker present, and SHA-256 `14A438028F31F90524656D276CDA99AFF2FD95221A82DB7EB414E8C9B8802F0D`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.73` asset, forcing update detection to `0.1.73` -> `0.1.74`, upgrading through the public `v0.1.74` GitHub release asset URL from the updater transient, and verifying the final active plugin as `0.1.74` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.74` header/constant, stable tag `0.1.74`, exactly one `GitHub Plugin URI` updater header, resend guidance ID marker present, resend describedby logic marker present, 45 runtime files, and no source/dev package files.

### Guardrails

- Do not change resend rate-limit thresholds, bucket keys, cooldown behavior, pending-registration lookup, token handling, email delivery, saved settings, settings schema/defaults, diagnostics logging, frontend routes beyond accessibility attributes, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.

### Completion Gate

- [x] Focused tests cover rate-limited resend guidance association.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.73 Small Release Cycle

### Scope

- [x] Continue security and anti-spam hardening from released `v0.1.72` baseline.
- [x] Add read-only Reoon policy visibility table separating always-blocked statuses from configurable flagged statuses.
- [x] Show current registration treatment for configurable flagged statuses based on selected policy.
- [x] Keep behavior read-only/admin-only; no saved setting/schema/provider/runtime changes.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.73` from clean `master` after updater-verified `v0.1.72`.
- Added a Reoon policy visibility table in the Security tab with always-blocked and configurable flagged status rows.
- Added focused `SettingsPageSecurityStatusTest` coverage. Initial validation passed: PHP syntax and focused test (`23 tests, 352 assertions`).
- Release validation passed: PHP syntax for the main plugin, edited settings page, and focused test; `npm run build`; focused `SettingsPageSecurityStatusTest` (`23 tests, 352 assertions`); `npm run lint`; `npm run make-pot` (`905 strings`); `npm audit --audit-level=moderate`; and `npm test -- --do-not-cache-result` (`245 tests, 1481 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.73-20260707-124727\alynt-account-gateway-v0.1.73.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.73` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Reoon policy table marker present, Reoon policy helper marker present, and SHA-256 `2DE01E9FBC52CC3B9F4A161BEEA48FC0C99CDDFA4C4CEB31F22A0E3A822BF06A`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.73` header/constant, stable tag `0.1.73`, exactly one `GitHub Plugin URI` updater header, Reoon policy table marker present, Reoon policy helper marker present, 45 runtime files, no source/dev package files, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.73`; Build Release workflow run `28862080044` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.73.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.73` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Reoon policy table marker present, Reoon policy helper marker present, and SHA-256 `F1E2437BDA83767E65C16905CCBA3D3E781301A51E1EB873DB10A53F3BC2629B`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.72` asset, forcing update detection to `0.1.72` -> `0.1.73`, upgrading through the public `v0.1.73` GitHub release asset URL from the updater transient, and verifying the final active plugin as `0.1.73` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.73` header/constant, stable tag `0.1.73`, exactly one `GitHub Plugin URI` updater header, Reoon policy table marker present, Reoon policy helper marker present, 45 runtime files, and no source/dev package files.

### Guardrails

- Do not change settings keys/types/defaults/sanitization, registration flow, Reoon verification policy, provider error handling, rate limits, diagnostics events, frontend output, emails, dashboard/WooCommerce, privacy cleanup, or updater metadata.

### Completion Gate

- [x] Focused tests cover Reoon policy table and allow/block treatment rows.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.72 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA pass from the released `v0.1.71` baseline.
- [x] Add explicit LTR direction hints to machine-readable admin settings inputs, including relative paths, webhook URLs, secrets, font stacks, username format, and Turnstile site key values.
- [x] Preserve normal prose fields, including email subject/preheader/body settings, so translatable site copy follows the admin language direction.
- [x] Keep the slice admin-output-only: do not change saved settings, sanitization, settings schema, frontend routes, frontend rendering, provider verification, rate limits, diagnostics logging, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.72` from clean `master` after the updater-verified `v0.1.71` release.
- Added `dir="ltr"` to shared admin text/password rendering for machine-readable settings fields while leaving ordinary prose string fields direction-neutral.
- Added focused `SettingsPageFieldHelpTest` coverage for relative path, webhook URL, signing secret, and prose-subject negative cases. Initial validation passed: PHP syntax for the edited settings page and test, and focused `SettingsPageFieldHelpTest` (`5 tests, 16 assertions`).
- Release validation passed: PHP syntax for the main plugin, edited settings page, and focused test, `npm run build`, focused `SettingsPageFieldHelpTest` (`5 tests, 16 assertions`), `npm run lint`, `npm run make-pot` (`896 strings`), `npm audit --audit-level=moderate`, and `npm test -- --do-not-cache-result` (`244 tests, 1466 assertions`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.72-20260707-123052\alynt-account-gateway-v0.1.72.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.72` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, admin field-direction helper marker present, LTR direction marker present, and SHA-256 `F81D045E32EDCA3552A8AD916CAE8D6D18B1F1F87CA6E5160354A7BB2AA1A302`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.72` header/constant, stable tag `0.1.72`, exactly one `GitHub Plugin URI` updater header, admin field-direction helper marker present, LTR direction marker present, expected machine-readable field rules present, docs/tests/source/package files excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.72`; Build Release workflow run `28859768133` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.72.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.72` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, admin field-direction helper marker present, LTR direction marker present, and SHA-256 `F4CA57FD16D660CE20199228D398AB5C8EF32014BD9AB81C9A4E3F89B787E0A1`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.71` asset, forcing update detection to `0.1.71` -> `0.1.72`, upgrading through the public `v0.1.72` GitHub release asset URL, and verifying the final active plugin as `0.1.72` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.72` header/constant, stable tag `0.1.72`, exactly one `GitHub Plugin URI` updater header, admin field-direction helper marker present, LTR direction marker present, and no source/dev package files.

### Guardrails

- Do not change settings keys, settings types, sanitization rules, default values, saved option payloads, tab placement, frontend output, public routes, provider behavior, diagnostics events, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater metadata.
- Keep the change limited to admin field direction attributes and focused coverage.

### Completion Gate

- [x] Focused tests cover LTR hints for machine-readable admin fields and avoid applying LTR to normal prose string fields.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.71 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA pass from the released `v0.1.70` baseline.
- [x] Add explicit `aria-disabled="true"` to the initially disabled set-password submit button.
- [x] Keep set-password JavaScript synchronized so `aria-disabled` changes to `false` only when the password requirements and confirmation match pass.
- [x] Keep the slice accessibility-only: do not change password requirements, validation rules, submitted field names, token handling, registration creation, password reset behavior, Reoon/Turnstile checks, rate limits, diagnostics logging, saved settings, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.71` from clean `master` after the updater-verified `v0.1.70` release.
- Added `aria-disabled="true"` to the set-password submit button's initial disabled state and synchronized `aria-disabled` with password validity in the frontend password-policy JavaScript.
- Added focused frontend coverage for the rendered set-password submit state and frontend JavaScript source guardrail. Initial validation passed: PHP syntax for the edited renderer and tests, focused `FrontendSetpasswordScreenTest` (`6 tests, 53 assertions`), and focused `FrontendJsSourceTest` (`1 test, 3 assertions`).
- Release validation passed: PHP syntax for the main plugin, edited renderer, and tests, `npm run build`, focused `FrontendSetpasswordScreenTest` (`6 tests, 53 assertions`), focused `FrontendJsSourceTest` (`1 test, 3 assertions`), `npm run make-pot` (`896 strings`), `npm run lint`, `npm test -- --do-not-cache-result` (`243 tests, 1462 assertions`), and `npm audit --audit-level=moderate`.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.71-20260707-120830\alynt-account-gateway-v0.1.71.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.71` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, set-password submit `aria-disabled` marker present, built frontend JavaScript `aria-disabled` synchronization marker present, and SHA-256 `0C821E29761D465C756BD4BFB2A076A23D45043D3BC25331C68C3E321D6DEB1A`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.71` header/constant, stable tag `0.1.71`, exactly one `GitHub Plugin URI` updater header in the main plugin file, set-password submit `aria-disabled` marker present, built frontend JavaScript `aria-disabled` synchronization marker present, docs/tests/source package files excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.71`; Build Release workflow run `28858660563` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.71.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.71` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, set-password submit `aria-disabled` marker present, built frontend JavaScript `aria-disabled` synchronization marker present, and SHA-256 `7A701FF774B6F3415F37ACB1791E8FE449E6D0ECA66CB7A48A5DE43FA5566743`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.70` asset, forcing update detection to `0.1.70` -> `0.1.71`, upgrading through the public `v0.1.71` GitHub release asset URL, and verifying the final active plugin as `0.1.71` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.71` header/constant, stable tag `0.1.71`, exactly one `GitHub Plugin URI` updater header, set-password submit `aria-disabled` marker present, built frontend JavaScript `aria-disabled` synchronization marker present, and no source/dev package files.

### Guardrails

- Do not alter password complexity requirements, password mismatch behavior, native reset-key validation, pending-registration token validation, account creation, frontend routes, submitted field names, nonce names, provider verification, rate limits, diagnostics events, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater metadata.
- Keep the JavaScript change scoped to button state semantics.

### Completion Gate

- [x] Focused frontend tests cover the set-password submit `aria-disabled` state and JavaScript synchronization marker.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.70 Small Release Cycle

### Scope

- [x] Continue the security and anti-spam hardening slice from the released `v0.1.69` baseline.
- [x] Add an admin-only Security tab launch decision summary that turns existing settings into public-registration readiness guidance.
- [x] Cover public registration state, anti-spam provider coverage, Terms/Privacy consent links, Reoon flagged-email policy, and diagnostics launch evidence.
- [x] Keep the slice read-only and guidance-only: do not add saved settings, change registration flow behavior, provider verification logic, rate-limit enforcement, diagnostics event names, frontend routes, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.70` from clean `master` after the updater-verified `v0.1.69` release.
- Added a Launch Decision Summary to the Security tab so administrators can see pre-launch blockers and review items before enabling public registration.
- Added focused `SettingsPageSecurityStatusTest` coverage for default pre-launch warnings, launch-ready configuration, and rendered Security tab markers. Initial validation passed: PHP syntax for the edited settings page and focused `SettingsPageSecurityStatusTest` (`22 tests, 337 assertions`).
- Release validation passed: PHP syntax for the main plugin and edited settings page, `npm run build`, focused `SettingsPageSecurityStatusTest` (`22 tests, 337 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`242 tests, 1459 assertions`), `npm audit --audit-level=moderate`, and `npm run make-pot` (`896 strings`).
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.70-20260707-114240\alynt-account-gateway-v0.1.70.zip` and inspected as 45 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.70` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Launch Decision Summary marker present, built admin CSS launch marker present, source assets excluded, and SHA-256 `6297F9BBEDC32511BF61914F72FF20B3B40A4BDC7BA582BE8F127E8C6D287C02`.
- Plugin Tester package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.70` header/constant, exactly one `GitHub Plugin URI` updater header, Launch Decision Summary and Anti-Spam Coverage markers present, built admin CSS launch marker present, docs/tests/source package files excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.70`; Build Release workflow run `28857639977` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.70.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.70` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Launch Decision Summary marker present, Anti-Spam Coverage marker present, built admin CSS launch marker present, and SHA-256 `B55431F15D4B9FDD1A344998B2A1C7582C7AB7C2B6A48F07628594E83BE1DF77`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.69` asset, forcing update detection to `0.1.69` -> `0.1.70`, upgrading through the public `v0.1.70` GitHub release asset URL, and verifying the final active plugin as `0.1.70` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.70` header/constant, exactly one `GitHub Plugin URI` updater header, Launch Decision Summary and Anti-Spam Coverage markers present, built admin CSS launch marker present, and no source/dev package files.

### Guardrails

- Do not change authentication, registration creation, confirmation resend, Reoon, Turnstile, rate-limit, diagnostics, email, dashboard, WooCommerce, privacy, uninstall, or updater runtime behavior in this slice.
- Do not store new data or introduce new settings.
- Keep the new output admin-only and read-only.

### Completion Gate

- [x] Focused tests cover the launch decision summary helper and rendered markers.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.69 Small Release Cycle

### Scope

- [x] Continue the security and anti-spam hardening slice from the released `v0.1.68` baseline.
- [x] Add privacy-preserving active rate-limit metadata so administrators can see current lockout pressure without exposing submitted identifiers or IP addresses.
- [x] Show active rate-limit bucket and lockout counts in the Security settings panel alongside recent verification-log pressure.
- [x] Extend uninstall cleanup to remove the new metadata transient rows.
- [x] Keep the slice observability-only: do not change rate-limit thresholds, bucket identity, submitted field names, frontend routes, provider verification, registration creation, diagnostics event codes, saved setting keys, email delivery, dashboard/WooCommerce behavior, privacy exporter behavior, or updater behavior.
- [x] Run build, focused tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.69` from clean `master` after the updater-verified `v0.1.68` release.
- Added metadata transients for rate-limit buckets containing only action, count, limit, locked state, and expiry. Submitted emails/usernames and IP addresses remain excluded from the metadata.
- Added an Active Rate Limit Buckets section to the Security settings panel so administrators can distinguish recent logged rate-limit blocks from lockouts that are still active inside the configured windows.
- Initial validation passed: PHP syntax for the edited rate limiter, settings page, and uninstall script, plus focused `RateLimiterTest`, `SettingsPageSecurityStatusTest`, and `CleanupLifecycleTest` coverage (`27 tests, 354 assertions`).
- Release validation passed: `npm run build`, PHP syntax for the main plugin, edited rate limiter, settings page, and uninstall script, focused security/cleanup tests (`27 tests, 354 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`240 tests, 1432 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`880 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on generated/metadata files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.69-20260707-111615\alynt-account-gateway-v0.1.69.zip` and inspected as 46 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.69` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Active Rate Limit Buckets UI marker present, rate-limit metadata transient marker present, metadata uninstall cleanup marker present, source assets excluded, and SHA-256 `1D96C30971E8AADEC68AB8611C70998191FBDCD4870A4225B7C9D4B91EDAB699`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.69` header/constant, exactly one `GitHub Plugin URI` updater header, Active Rate Limit Buckets UI marker present, metadata transient and cleanup markers present, source assets excluded, a throwaway login bucket produced locked metadata without storing the submitted email or IP address, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.69`; Build Release workflow run `28855833215` passed and produced the public asset.
- Public release asset `alynt-account-gateway-v0.1.69.zip` was downloaded from GitHub and inspected as 55 entries, 10 directory entries, no backslash entries, no dev entries, `0.1.69` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, Active Rate Limit Buckets UI marker present, rate-limit metadata transient marker present, metadata uninstall cleanup marker present, and SHA-256 `96D6B58FA941BC61AD509B3ED8DFCB3C15D7640CF73C1005760E10258C8327A6`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.68` asset, forcing update detection to `0.1.68` -> `0.1.69`, upgrading through the public `v0.1.69` GitHub release asset URL, and verifying the final active plugin as `0.1.69` with no remaining update.
- Post-updater Plugin Tester verification confirmed the installed public package: active plugin, `0.1.69` header/constant, exactly one `GitHub Plugin URI` updater header, Active Rate Limit Buckets UI marker present, metadata transient and cleanup markers present, no source/dev package files, and a throwaway login bucket produced locked metadata without storing the submitted email or IP address.

### Guardrails

- Do not store submitted identifiers, IP addresses, raw user agents, request bodies, or provider payloads in the new rate-limit metadata.
- Do not change rate-limit enforcement semantics, limits, windows, bucket hash inputs, public error messages, provider verification policy, registration flow, login flow, password-reset flow, or updater metadata in this slice.

### Completion Gate

- [x] Focused tests cover privacy-preserving metadata, active lockout admin summary, and uninstall cleanup.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.68 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA sub-slice from the released `v0.1.67` baseline.
- [x] Add stronger status semantics to the password strength live region so strength-message updates are announced more reliably.
- [x] Add focused frontend screen coverage for the live-region semantics.
- [x] Keep the slice markup-only: do not change authentication decisions, submitted field names, validation, password policy, password visibility JavaScript, registration creation, provider verification, rate-limit enforcement, diagnostics logging, saved settings, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused frontend screen tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.68` from clean `master` after the updater-verified `v0.1.67` release.
- Added `role="status"` and `aria-atomic="true"` to the password strength live region while preserving the existing `aria-live="polite"` behavior and JavaScript update flow.
- Added focused `FrontendSetpasswordScreenTest` coverage for the strengthened live-region semantics. Initial validation passed: PHP syntax for the edited set-password renderer, focused `FrontendSetpasswordScreenTest` (`6 tests, 53 assertions`), and `npm run build`.
- Release validation passed: `npm run build`, PHP syntax for the main plugin and edited set-password renderer, focused `FrontendSetpasswordScreenTest` (`6 tests, 53 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`238 tests, 1406 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on generated/metadata files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.68-20260707-002935\alynt-account-gateway-v0.1.68.zip` and inspected as 46 runtime files, no directory entries, no backslash entries, no dev entries, `0.1.68` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, password strength `role="status" aria-live="polite" aria-atomic="true"` marker present, source assets excluded, and SHA-256 `7D556F4B02F85ABCD2A0C6044D7453C609645E9FC4E27FE58441E797BB742FA1`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.68` header/constant, exactly one `GitHub Plugin URI` updater header, password strength live-region marker present, source assets excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.68`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public asset as 55 entries with no dev entries, `0.1.68` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, password strength live-region marker present, source assets excluded, and SHA-256 `7E11A2018610F9170C09E570AC682ECBFFE9F353F2C19B888AD576B6EA7BB7A6`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.67` release asset, forcing Alynt Plugin Updater to detect `0.1.67` to `0.1.68`, running the WordPress plugin upgrader against the public `v0.1.68` asset URL, and confirming final active state `0.1.68` with no remaining update.

### Guardrails

- Do not alter PHP business logic, submitted form names, frontend routes, form actions, nonce names, password validation decisions, password strength requirements, Reoon/Turnstile checks, rate-limit buckets, diagnostics events, saved setting keys, data retention, privacy cleanup, WooCommerce delegated forms, or updater metadata in this slice.
- Keep the markup change scoped to the password strength live region.

### Completion Gate

- [x] Frontend screen tests cover password strength live-region status semantics.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markup markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.67 Small Release Cycle

### Scope

- [x] Continue the accessibility, RTL, and multilingual QA sub-slice from the released `v0.1.66` baseline.
- [x] Add LTR direction hints to branded password fields so symbol-heavy passwords remain readable on RTL sites.
- [x] Cover login current-password, set-password new-password, and set-password confirmation fields.
- [x] Add focused frontend screen coverage for the LTR password-field guardrails.
- [x] Keep the slice markup-only: do not change authentication decisions, submitted field names, validation, password policy, password visibility JavaScript, registration creation, provider verification, rate-limit enforcement, diagnostics logging, saved settings, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused frontend screen tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.67` from clean `master` after the updater-verified `v0.1.66` release.
- Added `dir="ltr"` to branded password inputs on the login and set-password screens while preserving password-manager autocomplete values and password-toggle control relationships.
- Added focused frontend screen assertions for the LTR password-field guardrails. Initial validation passed: PHP syntax for the two edited screen renderers, focused frontend screen tests (`9 tests, 79 assertions`), and `npm run build`.
- Release validation passed: `npm run build`, PHP syntax for the main plugin and two edited frontend screen renderers, focused frontend screen tests (`9 tests, 79 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`238 tests, 1405 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on generated/metadata files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.67-20260707-001250\alynt-account-gateway-v0.1.67.zip` and inspected as 46 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.67` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, login/set-password/confirm-password `dir="ltr"` markers present, source assets excluded, and SHA-256 `B878F25377939B2C08BD18299E2FBE5073D5907D8058C467837DF2BA9E67F466`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.67` header/constant, exactly one `GitHub Plugin URI` updater header, login/set-password/confirm-password `dir="ltr"` markers present, source assets excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.67`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public asset as 55 entries with no dev entries, `0.1.67` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, login/set-password/confirm-password `dir="ltr"` markers present, source assets excluded, and SHA-256 `7073930FB374DDB650D1AFF9B139B9FE9D259BE6B1D14F154B50CFB0C9C0840B`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.66` release asset, forcing a fresh updater check that detected `0.1.66` to `0.1.67`, running the WordPress plugin upgrader against the public `v0.1.67` asset, and confirming final active state `0.1.67` with no remaining update.

### Guardrails

- Do not alter PHP business logic, submitted form names, frontend routes, form actions, nonce names, password validation decisions, password strength requirements, Reoon/Turnstile checks, rate-limit buckets, diagnostics events, saved setting keys, data retention, privacy cleanup, WooCommerce delegated forms, or updater metadata in this slice.
- Keep the markup change scoped to branded password inputs.

### Completion Gate

- [x] Frontend screen tests cover LTR direction hints on branded password fields.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markup markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.66 Small Release Cycle

### Scope

- [x] Start the accessibility, RTL, and multilingual QA sub-slice from the released `v0.1.65` baseline.
- [x] Add LTR direction hints to branded auth email-address fields so email addresses remain readable on RTL sites.
- [x] Cover login, registration, lost-password, and invalid-link confirmation-resend email fields.
- [x] Add focused frontend screen coverage for the LTR email-field guardrails.
- [x] Keep the slice markup-only: do not change authentication decisions, submitted field names, validation, registration creation, provider verification, rate-limit enforcement, diagnostics logging, saved settings, email delivery, dashboard/WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused frontend screen tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.66` from clean `master` after the updater-verified `v0.1.65` release.
- Added `dir="ltr"` to branded auth email inputs on login, registration, lost-password, and invalid-link confirmation-resend screens.
- Added focused frontend screen assertions for the LTR email-field guardrails. Initial validation passed: PHP syntax for the four edited screen renderers, focused frontend screen tests (`15 tests, 109 assertions`), and `npm run build`.
- Release validation passed: `npm run build`, PHP syntax for the main plugin and four edited frontend screen renderers, focused frontend screen tests (`15 tests, 109 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`238 tests, 1402 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on generated/metadata files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.66-20260706-234844\alynt-account-gateway-v0.1.66.zip` and inspected as 46 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.66` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, login/register/lost-password/invalid-link email `dir="ltr"` markers present, source assets excluded, and SHA-256 `7E1BE217922C9F7472274D85BAF754D0EF074D7857C537B33954B75A3BB249C9`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.66` header/constant, exactly one `GitHub Plugin URI` updater header, login/register/lost-password/invalid-link email `dir="ltr"` markers present, source assets excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.66`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public asset as 55 entries with no dev entries, `0.1.66` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, login/register/lost-password/invalid-link email `dir="ltr"` markers present, source assets excluded, and SHA-256 `4CAA38860D721A2B24F8602457983C9F03E79542A218C4DEC2662ED23484735D`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.65` release asset, forcing a fresh updater check that detected `0.1.65` to `0.1.66`, running the WordPress plugin upgrader against the public `v0.1.66` asset, and confirming final active state `0.1.66` with no remaining update.

### Guardrails

- Do not alter PHP business logic, submitted form names, frontend routes, form actions, nonce names, validation decisions, Reoon/Turnstile checks, rate-limit buckets, diagnostics events, saved setting keys, data retention, privacy cleanup, WooCommerce delegated forms, or updater metadata in this slice.
- Keep the markup change scoped to branded auth email inputs.

### Completion Gate

- [x] Frontend screen tests cover LTR direction hints on branded auth email fields.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package markup markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.65 Small Release Cycle

### Scope

- [x] Start the frontend visual QA and theme compatibility sub-slice from the released `v0.1.64` baseline.
- [x] Add scoped dashboard and delegated WooCommerce form-control CSS guardrails that reduce browser/theme interference with account fields and buttons.
- [x] Preserve native select, checkbox, and radio control behavior while normalizing text-like inputs, textareas, and dashboard action buttons.
- [x] Add focused frontend CSS source coverage for the dashboard guardrails.
- [x] Keep the slice CSS-only: do not change markup, JavaScript behavior, authentication decisions, password policy validation, registration creation, provider verification, saved settings, diagnostics logging, admin UI behavior, WooCommerce delegated form behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused frontend CSS tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.65` from clean `master` after the updater-verified `v0.1.64` release.
- Added `appearance: none` to dashboard text-like inputs, textareas, and delegated account action buttons, plus a `max-width: 100%` guardrail on dashboard buttons, so browser or theme form styling is less likely to distort the branded dashboard area.
- Preserved native select arrows and checkbox/radio platform controls by keeping select out of the appearance reset and adding an explicit `appearance: auto` reset for checkbox/radio controls.
- Added focused `FrontendCssSourceTest` coverage for the new scoped dashboard form-control guardrails. Initial validation passed: focused `FrontendCssSourceTest` (`7 tests, 47 assertions`) and `npm run build`.
- Release validation passed: `npm run build`, PHP syntax for the main plugin and updated frontend CSS test, focused `FrontendCssSourceTest` (`7 tests, 47 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`238 tests, 1399 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on generated/metadata files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.65-20260706-232116\alynt-account-gateway-v0.1.65.zip` and inspected as 46 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.65` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, compiled dashboard form-control guardrails present, source CSS excluded, and SHA-256 `C65A024539F106B2870C96D5AB2ADF3E104B2E7C6F7998F2F3E12A91B3AFDEF7`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.65` header/constant, exactly one `GitHub Plugin URI` updater header, compiled frontend CSS includes dashboard input/textarea, checkbox/radio, and button appearance guardrails, dashboard button `max-width:100%` is present, source CSS is excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.65`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public asset as 55 entries with no dev entries, `0.1.65` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, compiled dashboard form-control guardrails present, source CSS excluded, and SHA-256 `C7942F21E1FA01F4AA0BEB3F3E0B6C384A9356C979D77AA518ECF4029CD85475`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.64` release asset, forcing a fresh updater check that detected `0.1.64` to `0.1.65`, running the WordPress plugin upgrader against the public `v0.1.65` asset, and confirming final active state `0.1.65` with no remaining update.

### Guardrails

- Do not alter PHP runtime behavior, translated strings outside necessary metadata/plan changes, frontend route handling, saved setting keys, diagnostics event names, provider API behavior, data retention, privacy cleanup, WooCommerce delegated form behavior, or updater metadata in this slice.
- Keep the CSS changes scoped to branded dashboard and delegated account form controls and buttons.

### Completion Gate

- [x] Frontend CSS tests cover scoped dashboard form-control theme-compatibility guardrails.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package CSS markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.64 Small Release Cycle

### Scope

- [x] Start the frontend visual QA and theme compatibility sub-slice from the released `v0.1.63` baseline.
- [x] Add scoped gateway form-control CSS guardrails that reduce browser/theme interference with branded auth fields and buttons.
- [x] Add focused frontend CSS source coverage for the guardrails.
- [x] Keep the slice CSS-only: do not change markup, JavaScript behavior, authentication decisions, password policy validation, registration creation, provider verification, saved settings, diagnostics logging, admin UI behavior, WooCommerce delegated form behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused frontend CSS tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.64` from clean `master` after the updater-verified `v0.1.63` release.
- Added `appearance: none` to branded gateway text inputs, password-toggle buttons, and gateway buttons, plus a `max-width: 100%` guardrail on gateway text inputs, so native browser or theme form-control styling is less likely to distort gateway forms.
- Added focused `FrontendCssSourceTest` coverage for the new scoped form-control guardrails. Initial validation passed: focused `FrontendCssSourceTest` (`6 tests, 42 assertions`) and `npm run build`.
- Release validation passed: `npm run build`, PHP syntax for the main plugin and updated frontend CSS test, focused `FrontendCssSourceTest` (`6 tests, 42 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`237 tests, 1394 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on generated/metadata files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.64-20260706-230345\alynt-account-gateway-v0.1.64.zip` and inspected as 46 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.64` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, compiled frontend CSS form-control guardrails present, source CSS excluded, and SHA-256 `4879E3F4E5230F00B066C3133076BC2DCF4147863FF520DD78075CD35564F603`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.64` header/constant, exactly one `GitHub Plugin URI` updater header, compiled frontend CSS includes `appearance:none` guardrails for gateway inputs, password toggles, and buttons, gateway input `max-width:100%` is present, source CSS is excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.64`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public asset as 55 entries with no dev entries, `0.1.64` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, compiled frontend CSS form-control guardrails present, source CSS excluded, and SHA-256 `3B2F91EB4881415BE14125FBE7310BEC4D27C6734CC104819A2A8E6F5F4C8F32`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.63` release asset, forcing a fresh updater check that detected `0.1.63` to `0.1.64`, running the WordPress plugin upgrader against the public `v0.1.64` asset, and confirming final active state `0.1.64` with no remaining update.

### Guardrails

- Do not alter PHP runtime behavior, translated strings outside necessary metadata/plan changes, frontend route handling, saved setting keys, diagnostics event names, provider API behavior, data retention, privacy cleanup, WooCommerce delegated forms, or updater metadata in this slice.
- Keep the CSS changes scoped to branded gateway auth form controls and buttons.

### Completion Gate

- [x] Frontend CSS tests cover scoped form-control theme-compatibility guardrails.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package CSS markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.63 Small Release Cycle

### Scope

- [x] Start the next accessibility, RTL, and multilingual QA sub-slice from the released `v0.1.62` baseline.
- [x] Add accessible password visibility controls to set-password password and confirmation fields.
- [x] Add explicit `aria-controls` relationships to password visibility toggles so assistive technology can identify the controlled field.
- [x] Keep the slice frontend-output-only: do not change authentication decisions, password policy validation, reset-token validation, registration creation, provider verification, saved settings, diagnostics logging, admin UI behavior, WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused frontend screen tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.63` from clean `master` after the updater-verified `v0.1.62` release.
- Added `aria-controls` to the login password visibility toggle and updated frontend JavaScript to prefer the explicitly controlled input before falling back to the closest password wrapper.
- Added password visibility toggles for both set-password fields while preserving existing password policy, strength meter, and error-describedby relationships.
- Added focused frontend screen coverage for the login toggle control relationship and the two set-password visibility toggles. Initial validation passed: PHP syntax for the edited frontend screen files, focused `FrontendLoginScreenTest` plus `FrontendSetpasswordScreenTest` (`9 tests, 75 assertions`), and `npm run build`.
- Release validation passed: `npm run build`, PHP syntax for the main plugin and edited frontend screen files, focused `FrontendLoginScreenTest` plus `FrontendSetpasswordScreenTest` (`9 tests, 75 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`236 tests, 1389 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on generated/metadata files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.63-20260706-225202\alynt-account-gateway-v0.1.63.zip` and inspected as 46 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.63` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, login password toggle `aria-controls` present, two set-password toggle controls present, compiled frontend JS controlled-input lookup present, source JS excluded, and SHA-256 `C2152966F6186903D0DA59BC72A1DF2FC385CE06243C972D632A67A43FB0FF00`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.63` header/constant, exactly one `GitHub Plugin URI` updater header, login password toggle `aria-controls` present, two set-password toggle controls present, compiled frontend JS controlled-input lookup present, source JS excluded, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.63`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public asset as 55 entries with no dev entries, `0.1.63` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, login password toggle `aria-controls` present, two set-password toggle controls present, compiled frontend JS controlled-input lookup present, source JS excluded, and SHA-256 `2272F6D9A08454D9206D0A80A8AF8C1FFDAB054962500ED67FAE1277CB51CDCF`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.62` release asset, forcing a fresh updater check that detected `0.1.62` to `0.1.63`, running the WordPress plugin upgrader against the public `v0.1.63` asset, and confirming final active state `0.1.63` with no remaining update.

### Guardrails

- Do not alter credential handling, submitted password validation, generated usernames, registration tokens, password-reset keys, email sending, Reoon/Turnstile checks, rate limits, dashboard/WooCommerce rendering, data retention, or updater metadata in this slice.
- Keep the behavior limited to frontend password-field visibility controls and accessible control relationships.

### Completion Gate

- [x] Focused frontend screen tests cover the password toggle control relationships.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package password toggle markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.62 Small Release Cycle

### Scope

- [x] Start the next accessibility, RTL, and multilingual QA sub-slice from the released `v0.1.61` baseline.
- [x] Convert the remaining frontend left-specific resend-guidance list indentation to a logical inline-start property so the gateway helper text behaves correctly in RTL languages.
- [x] Add focused frontend CSS source coverage for the logical resend-guidance indentation guardrail.
- [x] Keep the slice visual/frontend-CSS-only: do not change settings schema, saved values, authentication, registration, provider verification, diagnostics logging, admin UI behavior, WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused frontend CSS tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.62` from clean `master` after the updater-verified `v0.1.61` release.
- Converted `.agw-resend-guidance ul` from `padding-left: 20px` to `padding-inline-start: 20px` so helper-list indentation follows writing direction.
- Added focused `FrontendCssSourceTest` coverage for the logical resend-guidance indentation and absence of the old left-specific padding. Initial validation passed: PHP syntax for the updated test, focused `FrontendCssSourceTest` (`5 tests, 37 assertions`), and `npm run build`.
- Release validation passed: `npm run build`, PHP syntax for the main plugin and updated frontend CSS test, focused `FrontendCssSourceTest` (`5 tests, 37 assertions`), `npm run lint`, `npm test -- --do-not-cache-result` (`236 tests, 1384 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on the POT and readme files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.62-20260706-222325\alynt-account-gateway-v0.1.62.zip` and inspected as 46 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.62` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, frontend RTL resend-guidance logical CSS marker present, old left-specific resend-guidance marker absent, source CSS excluded, and SHA-256 `8079AE312A660C7CE92B35DDDCAA7FA10716F42DF52D9C18FB101606C14251FC`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.62` header/constant, exactly one `GitHub Plugin URI` updater header, compiled frontend CSS includes `padding-inline-start`, old `padding-left` resend-guidance indentation is absent, source CSS is excluded from the installed package, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.62`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public asset as 55 entries with no dev entries, `0.1.62` header/constant/stable tag, exactly one `GitHub Plugin URI` updater header, frontend RTL resend-guidance logical CSS marker present, old left-specific resend-guidance marker absent, source CSS excluded, and SHA-256 `61667E6D91AD3B60CCB9CF9DCF09A7B28BEF67EEC257C1D3C4BD26E7E298B6CE`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.61` release asset, forcing a fresh updater check that detected `0.1.61` to `0.1.62`, running the WordPress plugin upgrader against the public `v0.1.62` asset, and confirming final active state `0.1.62` with no remaining update.

### Guardrails

- Do not alter PHP runtime behavior, translated strings outside necessary metadata/plan changes, frontend route handling, saved setting keys, diagnostics event names, provider API behavior, data retention, privacy cleanup, or updater metadata in this slice.
- Keep the CSS change scoped to frontend gateway resend-guidance RTL resilience.

### Completion Gate

- [x] Frontend CSS tests cover logical inline-start indentation for resend guidance.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the installed-package frontend CSS marker.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.61 Small Release Cycle

### Scope

- [x] Start the next accessibility, RTL, and multilingual QA sub-slice from the released `v0.1.60` baseline.
- [x] Convert admin settings guidance, readiness, security, and Reoon policy panels from left-specific visual affordances to logical inline-start CSS so the admin UI behaves correctly in RTL languages.
- [x] Add focused admin CSS source coverage for logical inline-start guardrails.
- [x] Keep the slice visual/admin-CSS-only: do not change settings schema, saved values, authentication, registration, provider verification, diagnostics logging, frontend output, WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, focused admin CSS tests, lint, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.61` from clean `master` after the updater-verified `v0.1.60` release.
- Converted admin settings guidance, readiness cards, security notices/cards, and Reoon policy guidance from left-specific CSS to logical inline-start properties for RTL resilience.
- Added focused `AdminCssSourceTest` coverage for logical inline-start markers and absence of the old left-specific panel accents. Initial validation passed: PHP syntax for the new test, focused `AdminCssSourceTest` (`2 tests, 15 assertions`), and `npm run build`.
- Release validation passed: `npm run build`, focused `AdminCssSourceTest` (`2 tests, 15 assertions`), PHP syntax for the main plugin and new test file, `npm run lint`, `npm test -- --do-not-cache-result` (`235 tests, 1380 assertions`), `npm audit --audit-level=moderate`, `npm run make-pot` (`874 strings`), and whitespace check. The only diff-check notes were expected line-ending normalization warnings on the implementation plan and POT files.
- Final local release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.61-20260706-215727\alynt-account-gateway-v0.1.61.zip` and inspected as 46 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.61` header/constant/stable tag, exactly one updater header, admin RTL logical CSS markers present, old left panel accents absent, and SHA-256 `D18B58580497B9D3AAA20E7995B57C9322311E933FD7CBB20A66B48DE0634C95`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after installing the local package through WordPress `Plugin_Upgrader`: active plugin, `0.1.61` header/constant, compiled admin CSS includes `border-inline-start`, `border-inline-start-width`, and `inset-inline-start`, old left panel accents are absent, source CSS is excluded from the installed package, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.61`, confirmed the Build Release workflow completed successfully, downloaded and inspected `alynt-account-gateway-v0.1.61.zip` as 55 entries with no dev entries, `0.1.61` header/constant/stable tag, exactly one updater header, admin RTL logical CSS markers present, old left panel accents absent, and SHA-256 `27E25E99F597FC010C099CA723E9280F94F1E0C8EFDF84F98C97529B4EAD99F4`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.60` release asset, forcing a fresh updater check that detected `0.1.60` to `0.1.61`, running the WordPress plugin upgrader against the public `v0.1.61` asset, and confirming final active state `0.1.61` with no remaining update.

### Guardrails

- Do not alter PHP runtime behavior, translated strings outside necessary test/plan coverage, frontend route handling, saved setting keys, diagnostics event names, provider API behavior, data retention, privacy cleanup, or updater metadata in this slice.
- Keep the CSS changes scoped to admin settings layout resilience for RTL and multilingual admin environments.

### Completion Gate

- [x] Admin CSS tests cover logical inline-start guardrails for guidance cards, readiness cards, security notices/cards, and Reoon policy guidance.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the installed-package admin CSS markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.60 Small Release Cycle

### Scope

- [x] Start the next admin-observability sub-slice from the released `v0.1.59` baseline.
- [x] Add privacy-conscious diagnostics events for branded login success/failure, login rate-limit blocks, neutral password-reset requests, password-reset delivery failures, reset completion failures, and reset completions.
- [x] Add a Security tab Gateway Auth Signals panel that summarizes recent branded login and password-reset outcomes from diagnostics logs.
- [x] Keep the slice observational only: do not change authentication decisions, reset-token validation, redirect destinations, email templates, registration behavior, WooCommerce behavior, provider verification, saved settings, privacy cleanup, or updater behavior.
- [x] Run build, lint, focused auth/settings tests, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.60` from clean `master` after the updater-verified `v0.1.59` release.
- Added branded auth diagnostics that store event codes, reasons, booleans, destination paths, and user IDs where appropriate while avoiding submitted email and password values.
- Added Gateway Auth Signals to the Security tab so administrators can see recent branded login failures/successes and password-reset pressure/issues without reading raw diagnostics rows.
- Added focused auth-service and settings-page coverage. Initial validation passed: PHP syntax for edited runtime files and focused `AuthServiceTest` plus `SettingsPageSecurityStatusTest` (`15 tests, 68 assertions`).
- Release validation passed: `npm run build`, `npm run make-pot` (`874 strings`), PHP syntax for the main plugin and edited runtime files, `npm run lint`, `npm test -- --do-not-cache-result` (`233 tests, 1365 assertions`), `npm audit --audit-level=moderate`, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.60-20260706-211701\alynt-account-gateway-v0.1.60.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.60` header/constant/stable tag, exactly one updater header, branded login/reset diagnostics present, Gateway Auth Signals panel present, grouped diagnostics counter present, and SHA-256 `8C5FC10EFB9AB0E45EC34B11917E9ADB8CF42E5F1D3AA061080B346899633D59`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.60` header/constant, branded login success/failure diagnostics present, branded password-reset request/completion diagnostics present, Gateway Auth Signals panel present, grouped diagnostics counter present, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.60`, confirmed the Build Release workflow completed successfully, downloaded and inspected `alynt-account-gateway-v0.1.60.zip` as 55 entries with no dev entries, `0.1.60` header/constant/stable tag, exactly one updater header, branded login/reset diagnostics present, Gateway Auth Signals panel present, grouped diagnostics counter present, and SHA-256 `F9A83462E3113190CDD87600137437C8E9ACBA697A3A4BCD260A9795461D1532`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.59` release asset, forcing a fresh updater check that detected `0.1.59` to `0.1.60`, running the WordPress plugin upgrader against the public `v0.1.60` asset, and confirming final active state `0.1.60` with no remaining update.

### Guardrails

- Do not alter login credentials, password-reset tokens, neutral reset responses, account creation, email rendering, Reoon/Turnstile checks, rate-limit thresholds, dashboard/WooCommerce rendering, data retention, or updater metadata in this slice.
- Keep diagnostics privacy-conscious: do not store submitted passwords, submitted email addresses, raw reset keys, cookies, nonces, or provider secrets.

### Completion Gate

- [x] Focused tests cover branded auth diagnostics and Security tab auth signal counts.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package diagnostics markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.59 Small Release Cycle

### Scope

- [x] Start the next accessibility, RTL, and multilingual QA sub-slice from the released `v0.1.58` baseline.
- [x] Add frontend `:focus-visible` guardrails for gateway forms, password toggles, links, dashboard cards, dashboard actions, and delegated WooCommerce controls.
- [x] Add forced-colors/high-contrast CSS support so gateway and dashboard surfaces use OS system colors in high-contrast mode.
- [x] Keep the slice visual/accessibility-only: do not change authentication, registration, dashboard routing, WooCommerce endpoint delegation, settings schema, provider behavior, diagnostics logging, privacy cleanup, or updater behavior.
- [x] Run build, lint, focused frontend CSS tests, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.59` from clean `master` after the updater-verified `v0.1.58` release.
- Added explicit `:focus-visible` outlines for public gateway controls and dashboard/WooCommerce delegated controls.
- Added a `forced-colors: active` media block that maps frontend gateway colors to system colors such as `Canvas`, `CanvasText`, `ButtonFace`, `ButtonText`, `Field`, `FieldText`, `LinkText`, and `Highlight`.
- Added frontend CSS source coverage for focus-visible selectors and forced-colors system-color guardrails. Initial validation passed: focused `FrontendCssSourceTest` (`4 tests, 33 assertions`) and `npm run build`.
- Release validation passed: `npm run build`, `npm run make-pot` (`853 strings`), PHP syntax for the main plugin, `npm run lint`, `npm test -- --do-not-cache-result` (`228 tests, 1307 assertions`), `npm audit --audit-level=moderate`, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.59-20260706-205216\alynt-account-gateway-v0.1.59.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.59` header/constant/stable tag, exactly one updater header, forced-colors CSS present, focus-visible CSS present, system colors present, and SHA-256 `9CE54EADCC0B5B86DB0F195BFCAF842CA3E4E57FD25390EF559718741AE35CDB`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.59` header/constant, compiled frontend CSS includes focus-visible selectors, forced-colors media support, and system color markers (`CanvasText`, `ButtonFace`, `Highlight`), and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.59`, confirmed the Build Release workflow completed successfully, downloaded and inspected `alynt-account-gateway-v0.1.59.zip` as 55 entries with no dev entries, `0.1.59` header/constant/stable tag, exactly one updater header, focus-visible CSS present, forced-colors CSS present, system colors present, and SHA-256 `ED29C060CB5275BBAF09A1F8D8E1A18E689A78E96B6DE9614DA9024CDB97E0B5`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.58` release asset, forcing a fresh updater check that detected `0.1.58` to `0.1.59`, running the WordPress plugin upgrader against the public `v0.1.59` asset, and confirming final active state `0.1.59` with no remaining update.

### Guardrails

- Do not alter frontend routes, form POST handling, account creation, Reoon/Turnstile verification, rate-limit enforcement, dashboard endpoint delegation, saved setting keys, diagnostics/event logging, data retention, privacy cleanup, or updater metadata in this slice.
- Keep this release focused on contrast resilience and keyboard-focus visibility in the existing frontend CSS.

### Completion Gate

- [x] Frontend CSS tests cover focus-visible and forced-colors guardrails.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the installed-package CSS markers.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.58 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening sub-slice from the released `v0.1.57` baseline.
- [x] Add a Security tab Manual Review Queue that summarizes Reoon flagged results allowed by policy.
- [x] Separate allowed flagged results, role-account reviews, catch-all/unknown/inbox-full reviews, and blocked flagged results.
- [x] Keep the slice informational only: do not change registration flow, Reoon provider decisions, rate-limit enforcement, provider API calls, data retention, frontend output, WooCommerce behavior, privacy cleanup, or updater behavior.
- [x] Run build, lint, focused security settings tests, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.58` from clean `master` after the updater-verified `v0.1.57` release.
- Added a Manual Review Queue to Recent Registration Verification Activity so allowed-but-flagged Reoon statuses are visible before the detailed masked log table.
- Added focused helper coverage for allowed flagged results, role-account reviews, catch-all/unknown/inbox-full reviews, and blocked flagged results.
- Initial validation passed: PHP syntax for the settings page and focused `SettingsPageSecurityStatusTest` (`18 tests, 275 assertions`).
- Release validation passed: `npm run build`, `npm run make-pot` (`853 strings`), PHP syntax for the main plugin and settings page, `npm run lint`, `npm test -- --do-not-cache-result` (`226 tests, 1290 assertions`), `npm audit --audit-level=moderate`, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.58-20260706-203912\alynt-account-gateway-v0.1.58.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.58` header/constant/stable tag, exactly one updater header, Manual Review Queue renderer present, helper present, and SHA-256 `D906690852CF62E3AEECAA7C0203C032EC7C733A2B759D6FD5033A8D11809B4B`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.58` header/constant, Manual Review Queue renderer/helper present, synthetic review counts matched expected allowed flagged, role-account, catch-all/unknown, and blocked flagged buckets, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.58`, confirmed the Build Release workflow completed successfully, downloaded and inspected `alynt-account-gateway-v0.1.58.zip` as 55 entries with no dev entries, `0.1.58` header/constant/stable tag, exactly one updater header, Manual Review Queue renderer present, helper present, and SHA-256 `1D9E496332F1F5C8ABC5AD39657948A75136539FE5A8BD631AB1095281D30E46`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.57` release asset, confirming updater detection for `0.1.57` to `0.1.58`, running the WordPress plugin upgrader against the public `v0.1.58` asset, and confirming final active state `0.1.58` with no remaining update.

### Guardrails

- Do not alter saved setting keys, registration account creation, pending-registration storage, Reoon/Turnstile API behavior, rate-limit buckets, diagnostics retention, webhook dispatch, dashboard/WooCommerce rendering, privacy cleanup, or updater metadata in this slice.
- Keep this release focused on admin visibility for existing Reoon manual-review evidence.

### Completion Gate

- [x] Security settings tests cover the Manual Review Queue counts and rendered guidance.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the installed-package Manual Review Queue.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.57 Small Release Cycle

### Scope

- [x] Start the uninstall and data cleanup coverage slice from the released `v0.1.56` baseline.
- [x] Make uninstall table cleanup use the shared database table registry to reduce drift between install and uninstall behavior.
- [x] Keep uninstall self-contained with a fallback table list if the database registry file is unavailable.
- [x] Strengthen cleanup tests for registry-matched table drops and rate-limit transient timeout cleanup.
- [x] Add readme uninstall policy copy that clarifies plugin-owned data is removed while WordPress users, WooCommerce orders, media-library files, and non-plugin data are preserved.
- [x] Run build, lint, focused cleanup tests, full tests, audit, POT generation, and package inspection.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.57` from clean `master` after the updater-verified `v0.1.56` release.
- Updated `uninstall.php` to load `ALYNT_AG_Database::tables()` for plugin-owned table cleanup, while preserving a hardcoded fallback for uninstall safety.
- Added cleanup lifecycle coverage proving uninstall drops exactly the registered plugin-owned tables and deletes both rate-limit transient and timeout option rows.
- Added readme uninstall policy text describing which plugin-owned data is removed and which site data is not removed.
- Initial validation passed: PHP syntax for `uninstall.php` and `CleanupLifecycleTest`, plus focused `CleanupLifecycleTest` (`4 tests, 25 assertions`).
- Release validation passed: `npm run build`, `npm run make-pot` (`842 strings`), PHP syntax for the main plugin and uninstall file, `npm run lint`, `npm test -- --do-not-cache-result` (`225 tests, 1274 assertions`), `npm audit --audit-level=moderate`, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.57-20260706-181945\alynt-account-gateway-v0.1.57.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.57` header/constant/stable tag, exactly one updater header, uninstall registry cleanup present, readme uninstall policy present, and SHA-256 `1F842B96823DA32641496130A6566686DCB267D84AB22CE96A9C03EDFF083AA8`.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.57` header/constant, readme uninstall policy present, uninstall registry cleanup present, uninstall fallback present, and uploaded sandbox artifacts were cleaned.
- Published GitHub release `v0.1.57`, confirmed the Build Release workflow completed successfully, downloaded and inspected `alynt-account-gateway-v0.1.57.zip` as 55 entries with no dev entries, `0.1.57` header/constant/stable tag, uninstall registry cleanup present, readme uninstall policy present, and SHA-256 `D616A8340435FA3ABEE54F1A44A652DD317265AF813618D2CF7CC07BB7FC2E0C`.
- Verified Alynt Plugin Updater end to end on the local-only `plugin-tester.local` site by downgrading to the public `v0.1.56` release asset, confirming updater detection for `0.1.56` to `0.1.57`, running the WordPress plugin upgrader against the public `v0.1.57` asset, and confirming final active state `0.1.57` with no remaining update.

### Guardrails

- Do not change runtime settings, frontend output, authentication flow, WooCommerce behavior, user creation, data retention intervals, updater behavior, or package exclusions in this slice.
- Keep this release focused on uninstall cleanup reliability, test coverage, and user-facing cleanup policy clarity.

### Completion Gate

- [x] Cleanup lifecycle tests cover registry-matched table drops and rate-limit transient timeout cleanup.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package metadata and cleanup policy presence.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.56 Maintenance Release Cycle

### Scope

- [x] Start a tiny maintenance release from the released `v0.1.55` baseline.
- [x] Update the GitHub release workflow from `softprops/action-gh-release@v2` to `softprops/action-gh-release@v3`.
- [x] Verify the `softprops/action-gh-release@v3` tag exists before publishing.
- [x] Bump release metadata to `0.1.56` so Alynt Plugin Updater can offer the maintenance release.
- [x] Run build, lint, tests, audit, POT generation, and package inspection.
- [x] Publish release and verify the workflow warning is gone.
- [x] Complete public asset and Alynt Plugin Updater verification.

### Progress Notes

- Started `v0.1.56` from `master` after the updater-verified `v0.1.55` release. The only pre-existing local diff was the intended release workflow change from `softprops/action-gh-release@v2` to `softprops/action-gh-release@v3`.
- Verified `softprops/action-gh-release@v3` exists through the GitHub API before keeping the workflow change.
- Bumped release metadata to `0.1.56` across the plugin header/constant, npm metadata, readme, changelog, and sample test.
- Release validation passed: PHP syntax for the main plugin and settings page, `npm run build`, `npm run make-pot` (`842 strings`), `npm run lint`, `npm test -- --do-not-cache-result` (`224 tests, 1272 assertions`), `npm audit --audit-level=moderate`, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.56-20260706-180423\alynt-account-gateway-v0.1.56.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.56` header/constant/stable tag, exactly one updater header, and SHA-256 `45974A425515008E295E32473191722E98FC89401A4D30DABFB2E488E4206843`.
- GitHub `v0.1.56` release was created at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.56`; release workflow `28812730329` passed, and the previous Node.js 20 deprecation annotation was absent from the workflow watch output after switching to `softprops/action-gh-release@v3`.
- Public `v0.1.56` asset downloaded to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.56-public\alynt-account-gateway-v0.1.56.zip` and inspected as 55 runtime entries, wrapped main file, no backslash entries, no dev entries, `0.1.56` header/constant/stable tag, and exactly one updater header. Public asset SHA-256: `432408242B808B1602D60B40237C3957DFA69C55673E11221D8DC2E15B826980`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from public `0.1.55`: fresh updater check found `0.1.56`, update response used `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.56/alynt-account-gateway-v0.1.56.zip`, WordPress `Plugin_Upgrader` installed the public release asset successfully, a fresh final check showed active `0.1.56`, no update remained pending, and uploaded test artifacts were cleaned. As in the prior non-browser verification, the direct upgrader skin left the plugin inactive immediately after update, so the installed copy was reactivated before the final active/no-update check.

### Guardrails

- Do not change plugin runtime behavior, settings, frontend output, authentication flow, WooCommerce behavior, updater metadata shape, package exclusions, or product features in this maintenance release.
- Keep this release focused on removing the GitHub Actions Node.js 20 deprecation annotation from future release builds.

### Completion Gate

- [x] Workflow references `softprops/action-gh-release@v3`.
- [x] Release workflow completes without the prior Node.js 20 deprecation annotation.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.55 Small Release Cycle

### Scope

- [x] Start the import/export/reset experience slice from the released `v0.1.54` baseline.
- [x] Add dry-run settings import inspection so JSON files can be validated before saving.
- [x] Improve admin import outcomes for invalid JSON, missing recognized settings, unreadable uploads, and imports with ignored unknown keys.
- [x] Add configuration portability guidance that explains what settings exports include, what they do not include, and when tab-level restore is the safer option.
- [x] Run build, lint, focused settings tests, full tests, audit, and POT generation.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.55` from clean `master` after the updater-verified `v0.1.54` release merge.
- Added `ALYNT_AG_Settings_Schema::inspect_import_package()` to report source metadata, recognized setting keys, ignored unknown keys, and clear validation errors without saving options.
- Updated the settings import handler to use the import inspection before saving, log recognized/ignored key metadata, and redirect to specific admin notices for invalid JSON, empty imports, unreadable uploads, and imports with ignored keys.
- Added Advanced Tools portability guidance covering settings-only exports, excluded media/users/diagnostics/webhook logs/pending registrations, JSON validation, schema sanitization, ignored unknown keys, and tab-level restore use.
- Initial validation passed: PHP syntax for touched PHP files, focused `SettingsSchemaTest` plus `SettingsPageSettingsToolsTest` (`22 tests, 100 assertions`), full tests (`224 tests, 1272 assertions`), lint, and whitespace check.
- Release-script validation passed: `npm run build`, `npm run make-pot` (`842 strings`), `npm run lint`, `npm test -- --do-not-cache-result` (`224 tests, 1272 assertions`), `npm audit --audit-level=moderate`, and whitespace check. The only diff-check note was the expected POT line-ending normalization warning.
- Branch-QA package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.55-branch-qa-20260706-173314\alynt-account-gateway-v0.1.55-branch-qa.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, pre-bump `0.1.54` header/constant, exactly one updater header, and import inspector/admin notice/portability guidance/POT strings present.
- Plugin Tester branch smoke passed on the local-only `plugin-tester.local` site: installed package active with pre-bump `0.1.54` header/constant, import inspector and ignored-key notice present in installed files, Advanced Tools settings import/export guidance rendered, export/import controls rendered, POT included the guidance string, dry-run import inspection reported one recognized and one ignored smoke key, and uploaded sandbox artifacts were cleaned.
- Release metadata bumped to `0.1.55`, POT regenerated (`842 strings`), and release validation passed: PHP syntax for the main plugin and settings page, build, lint, full tests (`224 tests, 1272 assertions`), npm audit, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.55-20260706-174103\alynt-account-gateway-v0.1.55.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.55` header/constant/stable tag, exactly one updater header, and import inspector/admin notice/portability guidance/POT strings present.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.55` header/constant, Advanced Tools settings import/export guidance rendered, export/import controls rendered, POT included the guidance string, dry-run import inspection reported one recognized and one ignored smoke key, and uploaded sandbox artifacts were cleaned.
- GitHub `v0.1.55` release was created at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.55`; release workflow `28811631216` passed with the known non-blocking Node.js 20 deprecation annotation from `softprops/action-gh-release@v2`.
- Public `v0.1.55` asset downloaded to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.55-public\alynt-account-gateway-v0.1.55.zip` and inspected as 55 runtime entries, wrapped main file, no backslash entries, no dev entries, `0.1.55` header/constant/stable tag, exactly one updater header, and import inspector/admin notice/portability guidance/POT strings present. Public asset SHA-256: `A11B738EC8C478B6BB1D67BE9494D7F545766BFD1D9DF6B78AE554A1DE9E370A`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from public `0.1.54`: fresh updater check found `0.1.55`, update response used `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.55/alynt-account-gateway-v0.1.55.zip`, WordPress `Plugin_Upgrader` installed the public release asset successfully, a fresh final check showed active `0.1.55`, no update remained pending, and uploaded test artifacts were cleaned. The non-browser upgrader skin used during verification left the plugin inactive immediately after update, so the installed copy was reactivated before the final active/no-update check.

### Guardrails

- Do not change exported setting keys, importer sanitization behavior, saved option names, media handling, pending registration storage, diagnostics retention, webhook logs, WordPress users, frontend output, authentication flow, WooCommerce behavior, or updater behavior in this slice.
- Keep this release focused on making existing configuration portability safer and more explainable.

### Completion Gate

- [x] Schema tests cover dry-run import inspection and invalid JSON handling.
- [x] Admin settings tools tests cover portability guidance.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates installed-package import/export/reset guidance.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.54 Small Release Cycle

### Scope

- [x] Start the next admin observability/security hardening slice from the released `master` baseline.
- [x] Clarify that access-control, gateway-routing, welcome-email failure, and webhook-dispatch security signals depend on diagnostics being enabled.
- [x] Keep this slice scoped to admin guidance and tests; do not change diagnostics logging, retention, security counters, rate-limit enforcement, provider decisions, registration flow, email delivery, webhooks, dashboard rendering, WooCommerce behavior, privacy cleanup, updater behavior, or default frontend-output disabled behavior.
- [x] Run build, lint, focused settings tests, full tests, audit, and POT generation.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.54` from clean `master` after the updater-verified `v0.1.53` release merge.
- Added a diagnostics-disabled note above the diagnostics-dependent Security tab signal groups. The note explains that access-control, gateway-routing, welcome-email failure, and webhook-dispatch signals only show complete evidence while diagnostics are enabled in Advanced Tools.
- Added focused settings-page coverage for the diagnostics-disabled notice and the diagnostics-enabled omission path. Initial validation passed: PHP syntax for the settings page and focused `SettingsPageSecurityStatusTest` (`17 tests, 259 assertions`).
- Branch implementation validation passed: build, POT generation (`834 strings`), PHP syntax for the settings page, lint, focused `SettingsPageSecurityStatusTest` (`17 tests, 259 assertions`), full tests (`221 tests, 1256 assertions`), npm audit, and whitespace check. The only diff-check note was the expected POT line-ending normalization warning.
- Branch-QA package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.54-branch-qa-20260706-164658\alynt-account-gateway-v0.1.54-branch-qa.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, pre-bump `0.1.53` header/constant, exactly one updater header, and diagnostics notice renderer/built admin CSS/POT strings present.
- Plugin Tester branch smoke passed on the local-only `plugin-tester.local` site: installed package active with pre-bump `0.1.53` header/constant, diagnostics notice renderer/built admin CSS/POT strings present, diagnostics-disabled render showed the new note while keeping Access Control, Gateway Routing, and Account Delivery sections visible, diagnostics-enabled render omitted the note, and uploaded test artifacts were cleaned.
- Release metadata bumped to `0.1.54`, POT regenerated (`834 strings`), and release validation passed: PHP syntax for the main plugin and settings page, build, lint, full tests (`221 tests, 1256 assertions`), npm audit, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.54-20260706-165032\alynt-account-gateway-v0.1.54.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.54` header/constant/stable tag, exactly one updater header, and diagnostics notice renderer/built admin CSS/POT strings present.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.54` header/constant, diagnostics-disabled render showed the new note while keeping Access Control, Gateway Routing, and Account Delivery sections visible, diagnostics-enabled render omitted the note, and uploaded test artifacts were cleaned.
- GitHub `v0.1.54` release was created at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.54`; release workflow `28809614194` passed with only the non-blocking Node.js 20 deprecation annotation from `softprops/action-gh-release@v2`.
- Public `v0.1.54` asset downloaded to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.54-public\alynt-account-gateway-v0.1.54.zip` and inspected as 55 runtime entries, wrapped main file, no backslash entries, no dev entries, `0.1.54` header/constant/stable tag, exactly one updater header, and diagnostics notice renderer/built admin CSS/POT strings present. Public asset SHA-256: `52FBF7A6FA57B73582BD15A6FD5EB3EAD1ED6DCE07CF458F34CDB140EDBCBF11`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from public `0.1.53`: updater found `0.1.54`, update response used `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.54/alynt-account-gateway-v0.1.54.zip`, `Plugin_Upgrader->upgrade()` installed successfully, fresh runtime showed active `0.1.54`, no update remained pending, diagnostics-disabled render showed the new note, diagnostics-enabled render omitted it, and uploaded test artifacts were cleaned.

### Guardrails

- Do not alter diagnostics capture, log retention, event schemas, rate-limit behavior, provider API behavior, registration storage, token expiry, email delivery, webhook dispatch, dashboard/WooCommerce rendering, saved settings keys, privacy cleanup, or updater metadata in this slice.
- Keep this release focused on making existing admin signal limitations visible when diagnostics are disabled.

### Completion Gate

- [x] Admin settings tests cover disabled/enabled diagnostics notice behavior.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the installed-package diagnostics notice.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.53 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Improve invalid-link resend throttling UX so customers understand the cooldown, newest-link behavior, and inbox checks when confirmation email resend requests are rate-limited.
- [x] Keep this slice scoped to frontend resend guidance and tests; do not change rate-limit enforcement, token expiry, resend email delivery, registration storage, provider enforcement, dashboard output, WooCommerce behavior, webhooks, privacy cleanup, updater behavior, or default frontend-output disabled behavior.
- [x] Run focused frontend state screen tests, build, lint, full tests, audit, and POT generation.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.53` from clean `master` after the updater-verified `v0.1.52` release merge.
- Began the resend throttling UX slice by targeting the invalid-link resend screen, where the real `alynt_ag_rate_limited` error can currently appear without cooldown-specific guidance.
- Added rate-limit-specific invalid-link resend guidance that shows the configured resend window, reminds customers to use the newest confirmation email, and suggests checking spam/promotions/filtered inbox folders. The public error message remains neutral and does not confirm whether an email address has a pending registration.
- Added focused frontend renderer coverage for showing throttle guidance only on `alynt_ag_rate_limited`, plus message-catalog coverage for the updated resend error copy.
- Branch implementation validation passed: PHP syntax for the changed frontend services, focused `FrontendStateScreensTest` (`4 tests, 26 assertions`), build, POT generation (`832 strings`), lint, full tests (`220 tests, 1251 assertions`), npm audit, and whitespace check. The only diff-check note was the expected POT line-ending normalization warning.
- Branch-QA package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.53-branch-qa-20260706-162220\alynt-account-gateway-v0.1.53-branch-qa.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, pre-bump `0.1.52` header/constant, exactly one updater header, and resend guidance renderer/message/built CSS/POT strings present.
- Plugin Tester branch smoke passed on the local-only `plugin-tester.local` site: installed package active with pre-bump `0.1.52` header/constant, resend guidance renderer/message/built CSS/POT strings present, direct installed-renderer smoke confirmed the rate-limited invalid-link screen shows the cooldown, newest-link, inbox-check, and ARIA guidance, non-rate-limited resend errors omit the cooldown panel, and uploaded test artifacts were cleaned.
- Release metadata bumped to `0.1.53`, POT regenerated (`832 strings`), and release validation passed: PHP syntax for the main plugin and frontend state screen service, build, lint, full tests (`220 tests, 1251 assertions`), npm audit, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.53-20260706-162635\alynt-account-gateway-v0.1.53.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.53` header/constant/stable tag, exactly one updater header, and resend guidance renderer/message/built CSS/POT strings present.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.53` header/constant, rate-limited invalid-link resend screen shows cooldown, newest-link, inbox-check, and ARIA guidance, non-rate-limited resend errors omit the cooldown panel, and uploaded test artifacts were cleaned.
- GitHub `v0.1.53` release was created at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.53`; release workflow `28807333580` passed with only the non-blocking Node.js 20 deprecation annotation from `softprops/action-gh-release@v2`.
- Public `v0.1.53` asset downloaded to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.53-public\alynt-account-gateway-v0.1.53.zip` and inspected as 55 runtime entries, wrapped main file, no backslash entries, no dev entries, `0.1.53` header/constant/stable tag, exactly one updater header, and resend guidance renderer/message/built CSS/POT strings present. Public asset SHA-256: `A8E0A82CA20AF84D45E7AD0361F633B8C200F3723E1D1A0F800A2237FE26C757`.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from public `0.1.52`: updater found `0.1.53`, update response used `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.53/alynt-account-gateway-v0.1.53.zip`, `Plugin_Upgrader->upgrade()` installed successfully, fresh runtime showed active `0.1.53`, no update remained pending, the rate-limited invalid-link resend screen rendered cooldown/newest-link/inbox-check/ARIA guidance, non-rate-limited resend errors omitted the cooldown panel, and uploaded test artifacts were cleaned.

### Guardrails

- Do not alter rate-limit buckets, limits, transient keys, registration creation, pending registration token expiry, confirmation email sending, auth routes, provider API behavior, dashboard/WooCommerce rendering, webhook dispatch, saved settings keys, privacy cleanup, or updater metadata in this slice.
- Keep public messaging privacy-preserving; do not confirm whether a submitted email address belongs to a pending registration.

### Completion Gate

- [x] Frontend renderer tests cover rate-limited resend guidance and non-rate-limited resend errors.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the installed-package resend guidance.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.52 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Improve provider failure feedback in the Security & Spam admin tab by separating configuration gaps, connectivity failures, unexpected provider responses, and Turnstile challenge rejections.
- [x] Keep this slice scoped to admin observability/guidance; do not change provider enforcement, registration flow, saved settings schema, account creation, email sending, webhooks, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup, updater behavior, or default frontend-output disabled behavior.
- [x] Run build, lint, test, audit, and POT generation.
- [x] Package and run Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.52` from clean `master` after the updater-verified `v0.1.51` release merge.
- Added a Provider Failure Triage section under Recent Registration Verification Activity. The section breaks recent Turnstile/Reoon provider errors into concrete next-check cards for Turnstile configuration, Turnstile connectivity, Turnstile challenge rejections, Reoon configuration, Reoon connectivity, and unexpected Reoon responses.
- Added focused settings-page coverage for the new triage counts/statuses and rendered guidance. Initial validation passed: PHP syntax for the settings page and focused `SettingsPageSecurityStatusTest` (`16 tests, 254 assertions`).
- Branch implementation validation passed: build, POT generation (`828 strings`), PHP syntax for the settings page, lint, focused `SettingsPageSecurityStatusTest` (`16 tests, 254 assertions`), full tests (`219 tests, 1245 assertions`), npm audit, and whitespace check. The only diff-check note was the expected POT line-ending normalization warning.
- Branch-QA package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.52-branch-qa-20260706-154547\alynt-account-gateway-v0.1.52-branch-qa.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, pre-bump `0.1.51` header/constant/stable tag/POT metadata, exactly one updater header, and Provider Failure Triage renderer/CSS/POT strings present.
- Plugin Tester branch smoke passed on the local-only `plugin-tester.local` site: installed package active, pre-bump `0.1.51` header/constant confirmed, Provider Failure Triage rendered from the installed package, per-provider action/warning counts were correct for synthetic Turnstile/Reoon failures, compiled admin CSS and POT strings were present, and uploaded test artifacts were cleaned.
- Release metadata bumped to `0.1.52`, POT regenerated (`828 strings`), and release validation passed: PHP syntax for the main plugin and settings page, build, lint, full tests (`219 tests, 1245 assertions`), npm audit, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.52-20260706-155035\alynt-account-gateway-v0.1.52.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.52` header/constant/stable tag/POT metadata, exactly one updater header, and Provider Failure Triage renderer/CSS/POT strings present.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.52` header/constant, GitHub updater header, Provider Failure Triage rendered from the installed package, per-provider action/warning counts were correct for synthetic Turnstile/Reoon failures, compiled admin CSS and POT strings were present, and uploaded test artifacts were cleaned.
- GitHub `v0.1.52` release was created at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.52`; release workflow `28804664511` passed with only the non-blocking Node.js 20 deprecation annotation from `softprops/action-gh-release@v2`.
- Public `v0.1.52` asset downloaded to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.52-public\alynt-account-gateway-v0.1.52.zip` and inspected as 45 runtime files, wrapped main file, no backslash entries, no dev entries, `0.1.52` header/constant/stable tag/POT metadata, exactly one updater header, and Provider Failure Triage renderer/CSS/POT strings present.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from public `v0.1.51`: updater found `0.1.52`, update response used `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.52/alynt-account-gateway-v0.1.52.zip`, `Plugin_Upgrader->upgrade()` installed successfully, fresh runtime showed active `0.1.52`, no update remained pending, Provider Failure Triage rendered, compiled admin CSS was present, and uploaded test artifacts were cleaned.

### Guardrails

- Do not alter Turnstile or Reoon API calls, provider pass/block decisions, rate-limit enforcement, registration storage, token expiry, resend behavior, auth routes, frontend copy, dashboard output, WooCommerce behavior, email delivery, webhook dispatch, saved settings keys, privacy cleanup, or updater metadata in this slice.
- Keep this release focused on clearer admin diagnosis when configured anti-spam providers fail, reject, or return unexpected results.

### Completion Gate

- [x] Admin settings tests cover provider failure triage.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Provider Failure Triage guidance in the installed package.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.51 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Close the completed WooCommerce polish product slice in the plan based on shipped overview, delegated CSS, endpoint guidance, next-step affordances, unavailable fallback, shortcut actions, and updater-verified release evidence.
- [x] Improve Reoon flagged email policy visibility in the Security & Spam admin tab so site owners understand the recommended stance for catch-all, role-account, and unknown email verdicts.
- [x] Keep this slice scoped to admin guidance/visibility; do not change provider enforcement, registration flow, saved settings schema, account creation, email sending, webhooks, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup, updater behavior, or default frontend-output disabled behavior.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke.
- [x] Publish release and complete updater verification.

### Progress Notes

- Started `v0.1.51` from clean `master` after the `v0.1.50` release merge.
- Marked the broad WooCommerce dashboard polish product slice complete because the remaining listed outcomes have shipped across the WooCommerce overview, delegated form styling, endpoint guidance, edge-state affordance panels, unavailable fallback, endpoint shortcut actions, and final updater-verified baseline.
- Added a Reoon Flagged Status Guidance panel to the Security & Spam status area. The panel shows the current flagged-status policy, recommends allow-and-log for most stores, explains when blocking is appropriate, and points owners to Recent Registration Verification Activity for masked review.
- Added focused settings-page coverage for the new allow-and-log and blocking policy guidance, plus scoped admin CSS for the guide panel.
- Branch implementation validation passed: PHP syntax for the settings page, focused `SettingsPageSecurityStatusTest` (`15 tests, 228 assertions`), build, POT generation (`814 strings`), lint, full tests (`218 tests, 1219 assertions`), npm audit, and whitespace check. The only diff-check note was the expected POT LF-to-CRLF normalization warning.
- Release metadata bumped to `0.1.51`, POT regenerated (`814 strings`), and release validation passed: PHP syntax for the main plugin and settings page, build, lint, full tests (`218 tests, 1219 assertions`), npm audit, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.51-20260706-160415\alynt-account-gateway-v0.1.51.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.51` header/constant/stable tag/POT metadata, exactly one `GitHub Plugin URI: NichlasB/alynt-account-gateway` header, and Reoon guide renderer/CSS/POT strings present.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.51` header/constant, GitHub updater header, allow-and-log Reoon guide, block-policy Reoon guide, and compiled admin CSS validated. Uploaded test artifact was cleaned from the Novamira doubled-path upload location.
- GitHub `v0.1.51` release was created at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.51`; release workflow `28797680641` passed with only the non-blocking Node.js 20 deprecation annotation from `softprops/action-gh-release@v2`.
- Public `v0.1.51` asset downloaded to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.51-public\alynt-account-gateway-v0.1.51.zip` and inspected as 45 runtime files, wrapped main file, no backslash entries, no dev entries, `0.1.51` header/constant/stable tag/POT metadata, exactly one updater header, and Reoon guide renderer/CSS/POT strings present.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from public `v0.1.50`: updater found `0.1.51`, update response used `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.51/alynt-account-gateway-v0.1.51.zip`, `Plugin_Upgrader->upgrade()` installed successfully, fresh runtime showed active `0.1.51`, no update remained pending, the Reoon guidance and compiled CSS rendered, and uploaded test artifacts were cleaned.

### Guardrails

- Do not alter Reoon API calls, provider pass/block decisions, rate-limit enforcement, registration storage, token expiry, resend behavior, auth routes, frontend copy, dashboard output, WooCommerce behavior, email delivery, webhook dispatch, saved settings keys, privacy cleanup, or updater metadata in this slice.
- Keep this release focused on clearer admin visibility and recommended operating guidance for existing Reoon policy settings.

### Completion Gate

- [x] Admin settings tests cover the new Reoon policy guidance.
- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security & Spam guidance in the installed package.
- [x] Public release asset is installed through Alynt Plugin Updater.

## v0.1.50 Small Release Cycle

### Scope

- [x] Add the required `GitHub Plugin URI` plugin header so Alynt Plugin Updater can discover Alynt Account Gateway.
- [x] Keep the correction limited to updater metadata and release bookkeeping.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke.
- [x] Complete updater verification.
- [x] Publish the final `v0.1.50` release asset and verify the Alynt Plugin Updater path end to end from a header-bearing installed baseline.

### Progress Notes

- Started `v0.1.50` after `v0.1.49` public release creation when Plugin Tester updater verification showed Alynt Plugin Updater could not discover the installed ACG package without the `GitHub Plugin URI` header.
- Added `GitHub Plugin URI: NichlasB/alynt-account-gateway` to the main plugin header and bumped release metadata to `0.1.50`.
- Release validation passed: PHP syntax for the main plugin and dashboard screen, build, POT generation (`806 strings`), lint, full tests (`218 tests, 1212 assertions`), npm audit, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.50-20260706-154323\alynt-account-gateway-v0.1.50.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.50` header/constant/stable tag/POT metadata, exactly one `GitHub Plugin URI: NichlasB/alynt-account-gateway` header, and shortcut code/CSS/strings present.
- Plugin Tester local package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.50` header/constant, GitHub updater header, order-detail shortcuts, compiled CSS, and delegated endpoint output validated. Uploaded test artifact was cleaned from the Novamira doubled-path upload location.
- GitHub `v0.1.50` release was created at `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.50`; the release workflow passed with only the non-blocking Node.js 20 deprecation annotation from `softprops/action-gh-release@v2`.
- Public `v0.1.50` asset downloaded to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.50-public\alynt-account-gateway-v0.1.50.zip` and inspected as 45 runtime files, wrapped main file, no backslash entries, no dev entries, `0.1.50` header/constant/stable tag/POT metadata, exactly one updater header, and shortcut code/CSS present.
- Alynt Plugin Updater verification passed on the local-only `plugin-tester.local` site from a controlled header-bearing `0.1.49` test baseline: updater scanner found `NichlasB/alynt-account-gateway`, forced release check found `0.1.50`, update response used `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.50/alynt-account-gateway-v0.1.50.zip`, `Plugin_Upgrader->upgrade()` installed successfully, fresh runtime showed active `0.1.50`, no update remained pending, shortcut UI still rendered, and uploaded test artifacts were cleaned.

### Guardrails

- Do not change dashboard UI behavior, WooCommerce endpoint handling, auth, registration, emails, settings schema, saved data, privacy cleanup, or frontend-output defaults in this corrective release.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates `0.1.50` installs with updater metadata present.
- [x] Alynt Plugin Updater discovers and installs the public `v0.1.50` asset from GitHub.

## v0.1.49 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add compact account section shortcut actions for standard WooCommerce endpoints.
- [x] Cover orders, order details, downloads, addresses, account details, saved payment methods, add payment method, delete payment method, and set-default payment method flows.
- [x] Keep changes scoped to frontend navigation affordances; do not change endpoint routing, WooCommerce action delegation, dashboard settings, saved data, auth, registration, emails, updater behavior, or default frontend-output disabled behavior.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.49` release asset.
- [x] Verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.49` from clean `master` after the `v0.1.48` release merge.
- Added a compact WooCommerce account section shortcut nav above endpoint guidance so customers can move between related account tasks without returning to the dashboard grid.
- Added frontend styling for the shortcut row using existing dashboard colors, focus treatment, compact sizing, and wrapping behavior.
- Added focused dashboard renderer coverage for orders, payment methods, order-details shortcuts, and custom endpoint exclusion.
- Branch implementation validation passed: PHP syntax for the dashboard screen, focused dashboard tests (`9 tests, 73 assertions`), build, POT generation (`806 strings`), lint, full tests (`218 tests, 1211 assertions`), npm audit, and whitespace check. The only diff-check note was the expected POT LF-to-CRLF normalization warning.
- Branch-QA package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.49-branch-qa-20260706-152637\alynt-account-gateway-v0.1.49-branch-qa.zip` and inspected as wrapped, dev-free, backslash-free, pre-bump `0.1.48` metadata, with endpoint actions, shortcut aria label, shortcut CSS, and translated action strings present.
- Plugin Tester branch smoke passed on the local-only `plugin-tester.local` site: installed package active, pre-bump `0.1.48` header/constant confirmed, order-detail shortcuts, payment-method shortcuts, compiled CSS, delegated endpoint output, and custom endpoint no-shortcut behavior validated. Uploaded test artifact was cleaned from the Novamira doubled-path upload location.
- Release metadata bumped to `0.1.49`, POT regenerated (`806 strings`), and release validation passed: PHP syntax for the main plugin and dashboard screen, build, lint, full tests (`218 tests, 1211 assertions`), npm audit, and whitespace check. The only diff-check notes were expected line-ending normalization warnings on metadata/POT files.
- Final release package built at `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.49-20260706-153426\alynt-account-gateway-v0.1.49.zip` and inspected as 45 runtime files, wrapped main file, no directory entries, no backslash entries, no dev entries, `0.1.49` header/constant/stable tag/POT metadata, and shortcut code/CSS/strings present.
- Plugin Tester final package smoke passed on the local-only `plugin-tester.local` site after a fresh request: active plugin, `0.1.49` header/constant, order-detail shortcuts, payment-method shortcuts, compiled CSS, delegated endpoint output, and custom endpoint no-shortcut behavior validated. Uploaded test artifact was cleaned from the Novamira doubled-path upload location.
- GitHub `v0.1.49` release was created and the release workflow passed. Public asset inspection passed, but final updater verification exposed that the ACG package lacked the `GitHub Plugin URI` header required by Alynt Plugin Updater's scanner, so updater discovery could not find the installed plugin. This is being corrected in `v0.1.50`.

### Guardrails

- Do not change WooCommerce endpoint resolution, WooCommerce action names, account menu links, dashboard settings, saved settings schema, account creation, auth routing, email sending, webhook behavior, privacy cleanup, or default frontend-output disabled behavior.
- Keep this cycle focused on lightweight account section navigation affordances for delegated WooCommerce endpoints.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates WooCommerce endpoint shortcut actions.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.48 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Replace the bare unavailable WooCommerce endpoint message with a branded dashboard fallback panel.
- [x] Include practical recovery links back to the account dashboard and account details.
- [x] Keep changes scoped to frontend presentation when WooCommerce does not render endpoint content; do not change endpoint routing, WooCommerce action delegation, dashboard settings, saved data, auth, registration, emails, updater behavior, or default frontend-output disabled behavior.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks through final release package validation.
- [x] Publish the final `v0.1.48` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.48` from clean `master` after the `v0.1.47` release merge.
- Added a branded WooCommerce endpoint unavailable fallback with status semantics, endpoint-specific copy, and links to the account dashboard and account details.
- Added frontend styling for the unavailable fallback panel using existing dashboard surface, button, typography, and responsive patterns.
- Added focused dashboard renderer coverage for the new fallback copy, status role, recovery links, and removal of the old bare fallback paragraph.
- Verified branch implementation checks before the release metadata bump: PHP syntax passes for the touched dashboard screen, focused `FrontendDashboardScreenTest` passes with 8 tests and 60 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 801 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 217 tests and 1198 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` reports only the expected POT line-ending normalization warning.
- Created wrapped branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.48-branch-qa-20260706-150425\alynt-account-gateway-v0.1.48-branch-qa.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, pre-bump `0.1.47` metadata, WooCommerce endpoint fallback renderer, built frontend CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.47` through WordPress upgrader classes. Fresh runtime smoke confirmed active pre-bump header `0.1.47` and loaded constant `0.1.47`, rendered a WooCommerce delegated endpoint with no WooCommerce output, validated the fallback panel class, status role, endpoint-specific copy, dashboard/account recovery links, old bare fallback removal, and built CSS presence, then removed uploaded QA artifacts from the LocalWP filesystem.
- Bumped release metadata to `0.1.48` in the plugin header, version constant, package metadata, readme stable tag/changelog, CHANGELOG, sample version assertion, and POT metadata.
- Verified release-candidate checks after the metadata bump: PHP syntax passes for `alynt-account-gateway.php` and the touched dashboard screen, `npm.cmd run build` passes, `npm.cmd run lint` passes, full `npm.cmd test` passes with 217 tests and 1198 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` reports only expected line-ending normalization warnings for CHANGELOG/POT.
- Created final wrapped package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.48-20260706-150945\alynt-account-gateway-v0.1.48.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, no root-level plugin main file, wrapped `0.1.48` plugin header/constant/readme/POT metadata, WooCommerce endpoint fallback renderer, built frontend CSS, and POT strings present.
- Installed the final package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime smoke confirmed active header `0.1.48` and loaded constant `0.1.48`, rendered a WooCommerce delegated endpoint with no WooCommerce output, validated the fallback panel class, status role, endpoint-specific copy, dashboard/account recovery links, old bare fallback removal, and built CSS presence, then removed uploaded final package artifacts from the LocalWP filesystem.
- Published GitHub release `v0.1.48`; release workflow `28794115778` completed successfully. The workflow emitted a non-blocking Node 20 deprecation warning for `softprops/action-gh-release@v2` while GitHub forced Node 24.
- Downloaded and inspected public release asset `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.48-public\alynt-account-gateway-v0.1.48.zip`; verified 45 runtime file entries, 10 harmless directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, wrapped plugin main file, `0.1.48` plugin header/constant/readme/POT metadata, WooCommerce endpoint fallback renderer, built frontend CSS, and POT strings present.
- Verified Alynt Plugin Updater end to end on LocalWP Plugin Tester: downgraded to public `0.1.47`, confirmed fallback renderer absent and prior Pending Registration Lifecycle Signals present, force-refreshed updater data to detect public `0.1.48` from `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.48/alynt-account-gateway-v0.1.48.zip`, upgraded through WordPress `Plugin_Upgrader`, reactivated ACG after the programmatic upgrade, confirmed active `0.1.48`, confirmed no update remains available, reran the fallback smoke, and removed uploaded downgrade artifacts.

### Guardrails

- Do not change WooCommerce endpoint resolution, WooCommerce action names, account menu links, dashboard settings, saved settings schema, account creation, auth routing, email sending, webhook behavior, privacy cleanup, or default frontend-output disabled behavior.
- Keep this cycle focused on making unavailable delegated WooCommerce endpoint content feel intentional and recoverable.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the WooCommerce endpoint fallback panel.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.47 Small Release Cycle

### Scope

- [x] Start the next admin observability and registration lifecycle slice from the released `master` baseline.
- [x] Add read-only Pending Registration Lifecycle Signals to the Security tab using existing pending registration rows.
- [x] Summarize recent pending, email-confirmed-but-not-completed, expired, and completed pending registration records.
- [x] Keep changes scoped to admin visibility with no pending registration storage, token expiry, resend behavior, account creation, email sending, saved settings schema, dashboard, WooCommerce, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for lifecycle signal counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks through final release package validation.
- [x] Publish the final `v0.1.47` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.47` from clean `master` after the `v0.1.46` release merge.
- Added a Pending Registration Lifecycle Signals summary above the Security tab pending registrations table.
- The summary derives lifecycle counts from existing `pending_registrations` rows and reuses the same status resolution used by the table.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 15 tests and 221 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 796 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 217 tests and 1189 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.47-branch-qa-20260706-143540\alynt-account-gateway-v0.1.47-branch-qa.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, pre-bump `0.1.46` metadata, Pending Registration Lifecycle Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA runtime package on LocalWP Plugin Tester over active `0.1.46` through WordPress upgrader classes. Fresh runtime smoke confirmed active pre-bump header `0.1.46` and loaded constant `0.1.46`, inserted 4 temporary pending registration rows, validated Pending Registration Lifecycle Signals render with pending, email-confirmed, expired, and completed guidance, validated table statuses, cleaned up all 4 temporary rows, confirmed 0 remaining QA rows, and removed uploaded QA artifacts from the LocalWP filesystem.
- Bumped release metadata to `0.1.47` in the plugin header, version constant, package metadata, readme stable tag/changelog, CHANGELOG, sample version assertion, and POT metadata.
- Verified final release checks after the metadata bump: PHP syntax passes for `alynt-account-gateway.php` and `admin/class-settings-page.php`, `npm.cmd run build` passes, `npm.cmd run lint` passes, full `npm.cmd test` passes with 217 tests and 1189 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` reports only expected line-ending normalization warnings for CHANGELOG/POT.
- Corrected the final release ZIP to use the standard WordPress plugin-folder wrapper after local upgrader testing showed rootless packages can update subfolders while leaving the active main plugin file behind. Final wrapped package: `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.47-wrapped-20260706-144422\alynt-account-gateway-v0.1.47.zip`.
- Verified the wrapped final package has 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, no root-level plugin main file, wrapped `0.1.47` plugin header/constant/readme/POT metadata, Pending Registration Lifecycle Signals renderer, built admin CSS, and POT strings present.
- Installed the wrapped final package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime smoke confirmed active header `0.1.47` and loaded constant `0.1.47`, inserted 4 temporary pending registration rows, validated lifecycle counts/copy, table status labels, next-step copy, masked email output, deleted all 4 temporary rows, confirmed 0 remaining QA rows, and removed uploaded package artifacts from the LocalWP filesystem.
- Published GitHub release `v0.1.47`; release workflow `28792707446` completed successfully. The workflow emitted a non-blocking Node 20 deprecation warning for `softprops/action-gh-release@v2` while GitHub forced Node 24.
- Downloaded and inspected public release asset `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.47-public\alynt-account-gateway-v0.1.47.zip`; verified 45 runtime file entries, 10 harmless directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, wrapped plugin main file, `0.1.47` plugin header/constant/readme/POT metadata, Pending Registration Lifecycle Signals renderer, built admin CSS, and POT strings present.
- Verified Alynt Plugin Updater end to end on LocalWP Plugin Tester: downgraded to public `0.1.46`, confirmed lifecycle renderer absent and prior Registration Abuse Signals present, force-refreshed updater data to detect public `0.1.47` from `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.47/alynt-account-gateway-v0.1.47.zip`, upgraded through WordPress `Plugin_Upgrader`, reactivated ACG after the programmatic upgrade, confirmed active `0.1.47`, confirmed no update remains available, reran the lifecycle smoke with 4 temporary pending registration rows, deleted all 4 rows, confirmed 0 remaining QA rows, and removed uploaded downgrade artifacts.

### Guardrails

- Do not change account creation, pending registration storage, token expiry, resend behavior, email sending, verification logging, diagnostics logging, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only lifecycle visibility using existing plugin-owned pending registration records.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Pending Registration Lifecycle Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.46 Small Release Cycle

### Scope

- [x] Start the next admin observability and security-hardening slice from the released `master` baseline.
- [x] Add read-only Registration Abuse Signals to the Security tab using existing verification log rows.
- [x] Summarize recent registration rate-limit blocks, confirmation resend rate-limit blocks, Reoon flagged email blocks, and account setup friction blocks.
- [x] Keep changes scoped to admin visibility with no registration flow, provider verification, rate-limit enforcement, diagnostics logging, saved settings schema, dashboard, WooCommerce, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for abuse signal counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.46` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.46` from clean `master` after the `v0.1.45` release merge.
- Added a Registration Abuse Signals summary above the Security tab verification activity table.
- The summary derives abuse and friction counts from existing `verification_logs` rows without introducing new tables, settings, logging events, or behavior changes.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 14 tests and 204 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 786 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 216 tests and 1172 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.46-branch-qa-20260706-135635\alynt-account-gateway-v0.1.46-branch-qa.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, pre-bump `0.1.45` metadata, Registration Abuse Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA runtime package on LocalWP Plugin Tester over active `0.1.45` through WordPress upgrader classes. Fresh runtime smoke confirmed active pre-bump header `0.1.45` and loaded constant `0.1.45`, inserted 5 temporary verification rows, validated Registration Abuse Signals render with registration rate-limit, resend rate-limit, flagged email block, and setup friction guidance, cleaned up all 5 temporary rows, confirmed 0 remaining QA rows, and removed uploaded QA artifacts from the LocalWP filesystem.
- Bumped release metadata to `0.1.46`, regenerated POT output with 786 strings, and re-ran release-candidate checks: PHP syntax for the plugin header and settings page, `npm.cmd run build`, `npm.cmd run lint`, full `npm.cmd test` with 216 tests and 1172 assertions, `npm.cmd audit --audit-level=moderate` with 0 vulnerabilities, and `git diff --check` with only expected Windows line-ending warnings.
- Created final local package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.46-20260706-140154\alynt-account-gateway-v0.1.46.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, header/constant/readme/POT metadata at `0.1.46`, Registration Abuse Signals renderer, built admin CSS, and POT strings present.
- Installed the final local package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime smoke confirmed active header `0.1.46` and loaded constant `0.1.46`, inserted 4 temporary verification rows, validated Registration Abuse Signals render with registration rate-limit, resend rate-limit, flagged email block, and setup friction guidance, cleaned up all 4 temporary rows, confirmed 0 remaining QA rows, and removed uploaded QA artifacts from the LocalWP filesystem.
- Published GitHub release `v0.1.46`; release workflow `28790175619` passed and uploaded `alynt-account-gateway-v0.1.46.zip`.
- Downloaded the public `v0.1.46` release asset to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.46-public\alynt-account-gateway-v0.1.46.zip`; verified 45 runtime file entries, 10 directory entries from the workflow-built archive, no backslash archive entries, no dev/source/test/docs/build-tooling files, header/constant/readme/POT metadata at `0.1.46`, Registration Abuse Signals renderer, built admin CSS, and POT strings present.
- Downgraded LocalWP Plugin Tester to the public `v0.1.45` asset, confirmed active header and loaded constant `0.1.45`, confirmed Registration Abuse Signals absent and Account Delivery Signals still present, then verified Alynt Plugin Updater detected `0.1.46` from `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.46/alynt-account-gateway-v0.1.46.zip`.
- Upgraded LocalWP Plugin Tester through Alynt Plugin Updater from `0.1.45` to `0.1.46`. Fresh runtime smoke confirmed active header `0.1.46`, loaded constant `0.1.46`, no remaining update available, inserted 4 temporary verification rows, validated Registration Abuse Signals render with registration rate-limit, resend rate-limit, flagged email block, and setup friction guidance, cleaned up all 4 temporary rows, confirmed 0 remaining QA rows, and removed uploaded downgrade artifacts from the LocalWP filesystem.

### Guardrails

- Do not change account creation, provider verification, rate-limit enforcement, email sending, webhook dispatch, diagnostics log writes, verification log writes, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only visibility using existing plugin-owned verification activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Registration Abuse Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.45 Small Release Cycle

### Scope

- [x] Start the next admin observability slice from the released `master` baseline.
- [x] Add read-only Account Delivery Signals to the Security tab using existing external diagnostics and webhook delivery log rows.
- [x] Summarize recent account-created welcome email failures, account-created webhook dispatch failures, and failed webhook delivery rows.
- [x] Keep changes scoped to admin visibility with no mail sending, webhook dispatch, webhook signing, delivery logging, diagnostics logging, saved settings schema, dashboard, WooCommerce, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for delivery signal counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.45` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.45` from clean `master` after the `v0.1.44` release merge.
- Added an Account Delivery Signals summary above the Security tab verification activity table.
- The summary derives account email and account webhook failure counts from existing `external_api` diagnostics events and failed webhook delivery counts from the existing webhook log table.
- Added focused tests for delivery signal counts and rendered guidance copy.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 13 tests and 187 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 776 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 215 tests and 1155 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.45-branch-qa-20260706-132412\alynt-account-gateway-v0.1.45-branch-qa.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, pre-bump `0.1.44` metadata, Account Delivery Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA runtime package on LocalWP Plugin Tester over active `0.1.44` through WordPress upgrader classes. Fresh runtime smoke confirmed active pre-bump header `0.1.44` and loaded constant `0.1.44`, Account Delivery Signals render with welcome email, account webhook, and failed webhook delivery guidance, temporary diagnostics and webhook log rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Bumped release metadata to `0.1.45`, regenerated POT output with 776 strings, and re-ran release-candidate checks: PHP syntax for the plugin header and settings page, `npm.cmd run build`, `npm.cmd run lint`, full `npm.cmd test` with 215 tests and 1155 assertions, `npm.cmd audit --audit-level=moderate` with 0 vulnerabilities, and `git diff --check` with only expected Windows line-ending warnings.
- Created final local package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.45-20260706-133053\alynt-account-gateway-v0.1.45.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, header/constant/readme/POT metadata at `0.1.45`, Account Delivery Signals renderer, built admin CSS, and POT strings present.
- Installed the final local package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime smoke confirmed active header `0.1.45` and loaded constant `0.1.45`, Account Delivery Signals render with welcome email, account webhook, and failed webhook delivery guidance, temporary diagnostics and webhook log rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Published GitHub release `v0.1.45`; release workflow `28788624191` passed and uploaded `alynt-account-gateway-v0.1.45.zip`.
- Downloaded the public `v0.1.45` release asset to `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.45-public\alynt-account-gateway-v0.1.45.zip`; verified 45 runtime file entries, 10 directory entries from the workflow-built archive, no backslash archive entries, no dev/source/test/docs/build-tooling files, header/constant/readme/POT metadata at `0.1.45`, Account Delivery Signals renderer, built admin CSS with the delivery selector, and POT strings present.
- Downgraded LocalWP Plugin Tester to the public `v0.1.44` asset, confirmed active header and loaded constant `0.1.44`, confirmed Account Delivery Signals absent and Gateway Routing Signals still present, then verified Alynt Plugin Updater detected `0.1.45` from `https://github.com/NichlasB/alynt-account-gateway/releases/download/v0.1.45/alynt-account-gateway-v0.1.45.zip`.
- Upgraded LocalWP Plugin Tester through Alynt Plugin Updater from `0.1.44` to `0.1.45`. Fresh runtime smoke confirmed active header `0.1.45`, loaded constant `0.1.45`, no remaining update available, Account Delivery Signals render with welcome email, account webhook, and failed webhook delivery guidance, temporary diagnostics and webhook log rows were cleaned up after QA, and uploaded downgrade artifacts were removed from the LocalWP filesystem.

### Guardrails

- Do not change account creation, account emails, email template rendering, email send behavior, webhook payloads, webhook dispatch, webhook signing, delivery log writes, diagnostics log writes, retry behavior, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only account delivery visibility using existing plugin-owned diagnostics and webhook delivery activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Account Delivery Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.44 Small Release Cycle

### Scope

- [x] Start the next admin observability slice from the released `master` baseline.
- [x] Add read-only Gateway Routing Signals to the Security tab using existing security diagnostics rows.
- [x] Summarize recent native `wp-login.php` redirects, reset-link redirects where `key` and `login` were preserved, and redirects where `redirect_to` was preserved.
- [x] Keep changes scoped to admin visibility with no public login routing, password reset, redirect preservation, diagnostics logging, role access, toolbar, saved settings schema, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for routing signal counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.44` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.44` from clean `master` after the `v0.1.43` release merge.
- Added a Gateway Routing Signals summary above the Security tab verification activity table.
- The summary derives counts from existing `native_login_redirected` diagnostics rows and the stored `preserved_query_keys` context.
- Added focused tests for routing signal counts and rendered guidance copy.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 12 tests and 174 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 768 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 214 tests and 1142 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.44-branch-qa-20260706-130719\alynt-account-gateway-v0.1.44-branch-qa.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build-tooling files, pre-bump `0.1.43` metadata, Gateway Routing Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA runtime package on LocalWP Plugin Tester over active `0.1.43` through WordPress upgrader classes. Fresh runtime smoke confirmed active pre-bump header `0.1.43` and loaded constant `0.1.43`, Gateway Routing Signals render with native login redirect, reset-link redirect, and redirect-to preserved guidance alongside Access Control Signals, temporary diagnostics rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Bumped release-candidate metadata to `0.1.44` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run make-pot` with 768 strings, PHP syntax checks for the main plugin file and touched settings page, `npm.cmd run build`, `npm.cmd run lint`, full `npm.cmd test` with 214 tests and 1142 assertions, `npm.cmd audit --audit-level=moderate`, and `git diff --check` all passed.
- Created final local package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.44-20260706-131227\alynt-account-gateway-v0.1.44.zip`; verified 45 runtime file entries, no directory entries, no backslash entries, no dev entries, `0.1.44` plugin/readme/POT metadata, and Gateway Routing Signals markers present. Installed the final package on LocalWP Plugin Tester through WordPress upgrader classes and confirmed fresh runtime active header `0.1.44`, loaded constant `0.1.44`, Gateway Routing Signals rendering, Access Control Signals still rendering, and zero temporary diagnostics rows remaining after cleanup.
- Published GitHub release `v0.1.44`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public `alynt-account-gateway-v0.1.44.zip` asset, verified runtime-only packaging and `0.1.44` metadata, downgraded LocalWP Plugin Tester to the public `v0.1.43` asset, confirmed Alynt Plugin Updater detected `0.1.43` to `0.1.44`, upgraded from the `v0.1.44` GitHub release asset, and verified final Plugin Tester state: active `0.1.44`, no remaining update, Gateway Routing Signals render after upgrade, and zero temporary diagnostics rows remaining.

### Guardrails

- Do not change authentication, login redirect, native `wp-login.php` redirect handling, password reset key handling, redirect destination preservation, blocked admin access, role/capability, toolbar, diagnostics logging, rate-limit threshold, transient keying, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only gateway routing visibility using existing plugin-owned diagnostics activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Gateway Routing Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.43 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add read-only Access Control Signals to the Security tab using existing verification and diagnostics activity rows.
- [x] Summarize recent login lockouts, password-reset lockouts, and blocked `wp-admin` access without changing public login, password-reset, admin redirect, role access, diagnostics, rate-limit, or toolbar behavior.
- [x] Keep changes scoped to admin visibility with no settings schema, frontend routing, login/auth handling, rate-limit enforcement, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for access-control signal counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.43` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.43` from clean `master` after the `v0.1.42` release merge.
- Added an Access Control Signals summary above the Security tab verification activity table.
- The summary derives login and password-reset lockout counts from existing `rate_limit` verification rows and blocked `wp-admin` access counts from existing security diagnostics events.
- Added focused tests for access-control signal counts and rendered guidance copy. PHP syntax checks pass for the touched settings page and focused `SettingsPageSecurityStatusTest` passes with 11 tests and 161 assertions.
- Verified local checks before the release metadata bump: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 760 strings, `npm.cmd run lint` passes after assignment-alignment cleanup, full `npm.cmd test` passes with 213 tests and 1129 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.43-branch-qa-20260706-122725\alynt-account-gateway-v0.1.43-branch-qa.zip`; verified 45 runtime file entries, no directory entries, no backslash archive entries, no dev/source/test/docs/build files, pre-bump `0.1.42` metadata, Access Control Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA runtime package on LocalWP Plugin Tester over active `0.1.42` through WordPress upgrader classes. Fresh runtime smoke confirmed active pre-bump header `0.1.42` and loaded constant `0.1.42`, Access Control Signals render with login lockout, password-reset lockout, and blocked-admin-access guidance alongside Rate Limit Pressure and Registration Flow Signals, temporary verification/diagnostics rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Bumped release-candidate metadata to `0.1.43` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run make-pot` with 760 strings, PHP syntax checks for the main plugin file and touched settings page, `npm.cmd run build`, `npm.cmd run lint`, full `npm.cmd test` with 213 tests and 1129 assertions, `npm.cmd audit --audit-level=moderate`, and `git diff --check` all passed.
- Created final local package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.43-20260706-123355\alynt-account-gateway-v0.1.43.zip`; verified 45 runtime file entries, no directory entries, no backslash entries, no dev entries, `0.1.43` plugin/readme/POT metadata, and Access Control Signals markers present. Installed the final package on LocalWP Plugin Tester through WordPress upgrader classes and confirmed active header `0.1.43`, loaded constant `0.1.43`, Access Control Signals rendering, and zero temporary verification/diagnostics rows remaining after cleanup.
- Published GitHub release `v0.1.43`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public `alynt-account-gateway-v0.1.43.zip` asset, verified runtime-only packaging and `0.1.43` metadata, downgraded LocalWP Plugin Tester to the public `v0.1.42` asset, confirmed Alynt Plugin Updater detected `0.1.42` to `0.1.43`, upgraded from the `v0.1.43` GitHub release asset, and verified final Plugin Tester state: active `0.1.43`, no remaining update, Access Control Signals render after upgrade, and zero temporary verification/diagnostics rows remaining.

### Guardrails

- Do not change authentication, login redirect, password reset, blocked admin access, role/capability, diagnostics logging, rate-limit threshold, transient keying, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only access-control visibility using existing plugin-owned verification and diagnostics activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Access Control Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.42 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add read-only Registration Flow Signals to the Security tab using existing verification activity rows.
- [x] Summarize recent consent-related blocks, pending-record or confirmation-email failures, password setup blocks, and successful confirmation resends without changing public registration behavior.
- [x] Keep changes scoped to admin visibility with no settings schema, frontend routing, provider verification policy, rate-limit enforcement, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for registration-flow signal counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.42` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.42` from clean `master` after the `v0.1.41` release merge.
- Added a Registration Flow Signals summary above the Security tab verification activity table.
- The summary derives counts from existing recent `registration_flow` rows and separates consent blocks, registration system failures, password setup blocks, and successful confirmation resends.
- Added focused tests for registration-flow signal counts and rendered guidance copy.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 10 tests and 148 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 752 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 212 tests and 1116 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.42-branch-qa-20260705-231528\alynt-account-gateway-v0.1.42-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.41` metadata, Registration Flow Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA runtime package into the LocalWP Plugin Tester plugin directory over active `0.1.41`. Fresh runtime smoke confirmed active pre-bump header `0.1.41` and loaded constant `0.1.41`, Registration Flow Signals renderer and CSS are present, seeded consent/system/password/resend registration-flow rows render the new flow copy alongside Provider Health Signals and Rate Limit Pressure, and temporary verification rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.42` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 752 strings, PHP syntax checks for the main plugin file and touched settings page, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 212 tests and 1116 assertions, and `git diff --check` all passed.
- Created final local package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.42-20260706-114930\alynt-account-gateway-v0.1.42.zip`; verified 45 runtime file entries, no directory entries, no backslash entries, no dev entries, `0.1.42` plugin/readme/POT metadata, and Registration Flow Signals markers present. Installed the final package on LocalWP Plugin Tester through WordPress upgrader classes and confirmed active header `0.1.42`, loaded constant `0.1.42`, Registration Flow Signals rendering, Provider Health Signals, Rate Limit Pressure, and zero temporary QA rows remaining after cleanup.
- Published GitHub release `v0.1.42`, confirmed the Build Release workflow completed successfully, downloaded and inspected the public `alynt-account-gateway-v0.1.42.zip` asset, verified runtime-only packaging and `0.1.42` metadata, downgraded LocalWP Plugin Tester to the public `v0.1.41` asset, confirmed Alynt Plugin Updater detected `0.1.41` to `0.1.42`, upgraded from the `v0.1.42` GitHub release asset, and verified final Plugin Tester state: active `0.1.42`, no remaining update, Registration Flow Signals render after upgrade, and zero temporary QA rows remaining.

### Guardrails

- Do not change registration validation, confirmation email sending, password rules, pending registration storage, provider API calls, provider status interpretation, Reoon flagged-status policy, public frontend error messages, rate-limit thresholds, transient keying, login behavior, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only registration-flow visibility using existing plugin-owned verification activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Registration Flow Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.41 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add read-only Provider Health Signals to the Security tab using existing verification activity rows.
- [x] Summarize recent Turnstile challenge rejections, Turnstile configuration/connectivity failures, Reoon email-quality blocks, and Reoon provider failures without changing provider policy or public responses.
- [x] Keep changes scoped to admin visibility with no settings schema, frontend routing, provider verification policy, rate-limit enforcement, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for provider-health counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.41` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.41` from clean `master` after the `v0.1.40` release merge.
- Added a Provider Health Signals summary above the Security tab verification activity table.
- The summary derives counts from existing recent verification rows and separates Turnstile challenge rejections, Turnstile configuration/connectivity failures, Reoon email-quality blocks, and Reoon provider failures.
- Added focused tests for provider-health item counts and rendered guidance copy.
- Verified local checks before the release metadata bump: PHP syntax passes for the touched settings page, focused `SettingsPageSecurityStatusTest` passes with 9 tests and 127 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 742 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 211 tests and 1095 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.41-branch-qa-20260705-222244\alynt-account-gateway-v0.1.41-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.40` metadata, Provider Health Signals renderer, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.40` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.40`, Provider Health Signals renderer and CSS are present, seeded Turnstile/Reoon provider-health rows render the new health copy plus Rate Limit Pressure, temporary verification rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Bumped release-candidate metadata to `0.1.41` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 742 strings, PHP syntax checks for the main plugin file and touched settings page, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 211 tests and 1095 assertions, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.41-20260705-222832\alynt-account-gateway-v0.1.41.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.41` header/constant/readme/POT metadata, Provider Health Signals renderer, and built admin CSS present.
- Installed the `0.1.41` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.41` and loaded constant `0.1.41`. Runtime smoke confirmed Provider Health Signals render with Turnstile/Reoon challenge, connectivity, email-block, and provider-failure cards; temporary verification rows were cleaned up after QA, and uploaded QA artifacts were removed from the LocalWP filesystem.
- Published GitHub release `v0.1.41`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.41`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.41.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.41` metadata, Provider Health Signals renderer, and built admin CSS present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.40` release asset, confirming runtime `0.1.40` with Provider Health Signals markers absent, clearing updater scanner/release caches, running a fresh Alynt Plugin Updater check to discover `0.1.41`, confirming the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming final active runtime `0.1.41` with no update remaining, and re-smoking Provider Health Signals output after the updater install. Temporary verification rows and uploaded QA artifacts were cleaned up after verification.

### Guardrails

- Do not change provider API calls, provider status interpretation, Reoon flagged-status policy, public frontend error messages, rate-limit thresholds, transient keying, login or registration behavior, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only provider-health visibility using existing plugin-owned verification activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Provider Health Signals on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.40 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a read-only Rate Limit Pressure summary to the Security tab using existing verification activity rows.
- [x] Summarize recent registration, confirmation resend, login, and password-reset rate-limit blocks without changing enforcement thresholds or public responses.
- [x] Keep changes scoped to admin visibility with no settings schema, rate-limit storage, frontend routing, provider verification policy, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for pressure summary counts and rendered admin copy.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.40` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.40` from clean `master` after the `v0.1.39` release merge.
- Added a Rate Limit Pressure summary above the existing Recent Registration Verification Activity table.
- The summary derives counts from existing recent `rate_limit` verification rows and shows separate cards for Registration, Confirmation Resends, Login, and Password Reset.
- Added small admin CSS rules for the summary heading and card headings, then rebuilt the admin asset bundle.
- Verified focused checks: PHP syntax passes for the touched admin settings page, focused `SettingsPageSecurityStatusTest` passes with 8 tests and 110 assertions, `npm.cmd run build` passes, and `npm.cmd run lint` passes.
- Verified broader local checks: `npm.cmd run make-pot` writes 732 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 210 tests and 1078 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.40-branch-qa-20260705-204413\alynt-account-gateway-v0.1.40-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.39` metadata, Rate Limit Pressure renderer, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.39` through WordPress upgrader classes. Fresh runtime smoke confirmed active header and loaded constant remain pre-bump `0.1.39`, Rate Limit Pressure renderer and CSS are present, seeded registration/resend/login/password-reset rate-limit rows render the new pressure copy plus existing table rows, and temporary verification rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.40` across the plugin header/constant, npm metadata, readme, sample test, changelog, POT, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 732 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 210 tests and 1078 assertions, PHP syntax check for the main plugin file, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.40-20260705-204748\alynt-account-gateway-v0.1.40.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.40` header/constant/readme/POT metadata, Rate Limit Pressure renderer, and built admin CSS present.
- Installed the `0.1.40` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.40` and loaded constant `0.1.40`. Runtime smoke confirmed Rate Limit Pressure renderer and CSS are present, seeded registration/resend/login/password-reset rate-limit rows render the new pressure copy, and temporary verification rows were cleaned up after QA.
- Published GitHub release `v0.1.40`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.40`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.40.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.40` metadata, Rate Limit Pressure renderer, and built admin CSS present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.39` release asset, confirming runtime `0.1.39` with Rate Limit Pressure markers absent, clearing updater scanner/release caches, detecting the available `0.1.40` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming final active runtime `0.1.40` with no update remaining, and re-smoking Rate Limit Pressure output after the updater install. Temporary verification rows were cleaned up after verification.

### Guardrails

- Do not change rate-limit thresholds, transient keying, public frontend rate-limit messages, login or registration behavior, provider verification policy, saved settings schema, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-only rate-limit visibility using existing plugin-owned verification activity.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Rate Limit Pressure summary on the Security tab.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.39 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a configurable Reoon flagged-status policy with a safe default that continues to allow and log catch-all, role account, unknown, and inbox-full statuses.
- [x] Allow stricter sites to block flagged Reoon statuses before account creation while preserving the original Reoon status in admin-visible activity logs.
- [x] Update the Security tab policy cards and activity guidance so admins can distinguish always-blocked Reoon statuses from configurable flagged statuses.
- [x] Add frontend-safe customer messaging for blocked flagged Reoon statuses without exposing provider internals.
- [x] Add focused coverage for setting defaults/sanitization, flagged-policy behavior, frontend messages, and Security tab guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.39` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.39` from clean `master` after the `v0.1.38` release merge.
- Added `reoon_flagged_policy` on the Security tab with `allow` as the default and `block` as the stricter option.
- Added generic schema-backed select rendering and option sanitization while preserving normal secret/API-key sanitization.
- Added strict flagged-status blocking through the registration protection service, returning `alynt_ag_reoon_flagged_blocked` and logging compact statuses such as `role_account_flagged_blocked`.
- Split the Security tab Reoon policy visibility into `Reoon Blocked Statuses` and `Reoon Flagged Statuses`, with guidance that changes based on the configured flagged-status policy.
- Added frontend-safe copy for blocked flagged Reoon statuses and admin guidance for `*_flagged_blocked` verification activity rows.
- Verified focused checks: PHP syntax passes for touched settings, admin, registration, and frontend message files; focused `SettingsSchemaTest` passes with 20 tests and 90 assertions; focused `RegistrationServiceTest` passes with 24 tests and 98 assertions; focused `FrontendMessagesTest` passes with 5 tests and 16 assertions; focused `SettingsPageSecurityStatusTest` passes with 7 tests and 95 assertions; and `npm.cmd run lint` passes.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 725 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 209 tests and 1063 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.39-branch-qa-20260705-201831\alynt-account-gateway-v0.1.39-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.38` metadata, Reoon flagged-policy setting, blocked flagged status handling, Security tab policy cards, frontend-safe blocked message, built assets, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.38` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.38`. Runtime smoke confirmed default flagged policy `allow`, select sanitization for `block` and invalid values, frontend-safe blocked flagged message, simulated Reoon `role_account` blocking with `alynt_ag_reoon_flagged_blocked`, verification activity status `role_account_flagged_blocked`, Security tab blocked-policy copy, and `*_flagged_blocked` admin guidance. Temporary verification rows and uploaded ZIPs were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.39` across the plugin header/constant, npm metadata, readme, sample test, changelog, POT, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 725 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 209 tests and 1063 assertions, PHP syntax check for the main plugin file, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.39-20260705-202439\alynt-account-gateway-v0.1.39.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.39` header/constant/readme/POT metadata, Reoon flagged-policy setting, blocked flagged status handling, Security tab policy cards, frontend-safe blocked message, and built assets present.
- Installed the `0.1.39` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.39` and loaded constant `0.1.39`. Runtime smoke confirmed default flagged policy `allow`, select sanitization for `block` and invalid values, frontend-safe blocked flagged message, simulated Reoon `role_account` blocking with `alynt_ag_reoon_flagged_blocked`, verification activity status `role_account_flagged_blocked`, Security tab blocked-policy copy, and `*_flagged_blocked` admin guidance. Temporary verification rows were cleaned up after QA.
- Published GitHub release `v0.1.39`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.39`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.39.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.39` metadata, Reoon flagged-policy setting, blocked flagged status handling, Security tab policy cards, frontend-safe blocked message, and built assets present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.38` release asset, confirming runtime `0.1.38` with Reoon flagged-policy markers absent, clearing updater scanner/release caches, detecting the available `0.1.39` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming final active runtime `0.1.39` with no update remaining, and re-smoking blocked flagged Reoon behavior plus Security tab guidance after the updater install. Temporary verification rows were cleaned up after verification.

### Guardrails

- Do not change frontend routes, Reoon request payloads, Turnstile behavior, rate-limit thresholds, registration success behavior, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep the default Reoon flagged-status behavior permissive and admin-visible: allow flagged statuses unless the site explicitly changes the new setting to block.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates flagged-policy settings, blocked flagged status logging, frontend-safe copy, and Security tab guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.38 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add clearer frontend copy for resend-confirmation throttling while keeping public resend outcomes neutral.
- [x] Log successful confirmation resends for existing pending registrations as admin-visible `registration_flow` activity without logging missing-pending neutral outcomes.
- [x] Add Security tab pending-registration next-step guidance for pending, email-confirmed, expired, and completed records.
- [x] Improve Security tab guidance for resend-confirmation rate-limit blocks.
- [x] Keep changes scoped to resend/expiry visibility with no settings schema, frontend route, pending-registration table schema, provider verification policy, rate-limit thresholds, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for resend messages, resend success activity, resend throttle guidance, and pending-registration next-step guidance.
- [x] Run package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.38` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.38` from clean `master` after the `v0.1.37` release merge.
- Added frontend copy for resend throttling: "Too many confirmation email requests. Please wait a moment and try again."
- Added admin-visible `registration_flow` / `confirmation_resent` logging only when a real pending registration is renewed and the confirmation email send succeeds.
- Added a Next Step column to Recent Pending Registrations so admins can distinguish waiting-for-confirmation, email-confirmed password setup, expired-link resend, and completed-account states.
- Updated Security tab guidance for `resend_confirmation_rate_limited` rows so admins know the customer should wait for the configured resend window before retrying.
- Verified focused checks: PHP syntax passes for touched frontend, registration, and admin files; focused `FrontendMessagesTest` passes with 5 tests and 15 assertions; focused `RegistrationServiceTest` passes with 21 tests and 89 assertions; focused `SettingsPageSecurityStatusTest` passes with 6 tests and 90 assertions; and `npm.cmd run lint` passes.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 713 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 204 tests and 1045 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.38-branch-qa-20260705-193210\alynt-account-gateway-v0.1.38-branch-qa-wp.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.37` metadata, resend throttle copy, `confirmation_resent` logging/guidance, pending-registration Next Step guidance, built assets, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.37` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.37`, and the new markers are present. Runtime smoke confirmed the resend-throttle frontend message, inserted disposable `confirmation_resent` and `resend_confirmation_rate_limited` activity rows plus pending and expired pending-registration rows, authenticated admin HTML smoke confirmed the Security tab renders resend throttle guidance, confirmation resent guidance, Next Step guidance, masked pending/expired emails, and no fatal/critical error output. Temporary activity rows, pending-registration rows, upload ZIPs, and an initial duplicated upload artifact were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.38` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 713 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 204 tests and 1045 assertions, and PHP syntax check for the main plugin file all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.38-20260705-194205\alynt-account-gateway-v0.1.38.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.38` header/constant/readme/POT metadata, resend throttle copy, `confirmation_resent` logging/guidance, pending-registration Next Step guidance, built assets, and POT strings present.
- Installed the `0.1.38` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.38`, loaded constant `0.1.38`, and the new markers are present. Runtime smoke confirmed the resend-throttle frontend message, inserted disposable `confirmation_resent` and `resend_confirmation_rate_limited` activity rows plus pending and expired pending-registration rows, authenticated admin HTML smoke confirmed the Security tab renders resend throttle guidance, confirmation resent guidance, Next Step guidance, masked pending/expired emails, and no fatal/critical error output. Temporary activity rows, pending-registration rows, and upload ZIPs were cleaned up after QA.
- Published GitHub release `v0.1.38`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.38`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.38.zip` verified with 45 runtime file entries plus 10 directory entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.38` metadata, resend throttle copy, `confirmation_resent` logging/guidance, pending-registration Next Step guidance, built assets, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.37` release asset, confirming runtime `0.1.37` with the new resend copy and Next Step markers absent, clearing updater caches, detecting the available `0.1.38` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.38` with no update remaining, and re-smoking resend throttle copy plus Security tab guidance after the updater install. Temporary activity rows, pending-registration rows, and upload artifacts were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, pending-registration table schema, provider verification behavior, provider request payloads, Reoon/Turnstile policy decisions, rate-limit thresholds, registration success behavior, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep public resend-confirmation success responses neutral and do not expose whether a pending registration exists.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates resend throttle copy, resend success activity, and pending-registration next-step guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.35 Small Release Cycle

### Scope

- [x] Start the next admin observability slice from the released `master` baseline.
- [x] Log native `wp-login.php` redirect decisions into the existing diagnostics table when diagnostics are enabled.
- [x] Log blocked `wp-admin` access for non-privileged users into the existing diagnostics table when diagnostics are enabled.
- [x] Keep changes scoped to diagnostics evidence with no settings schema, frontend route, redirect destination, capability, toolbar, login, registration, dashboard, WooCommerce, webhook, email, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage proving diagnostics rows are written without storing raw login or redirect query values.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.35` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.35` from clean `master` after the `v0.1.34` release merge.
- Added diagnostics events for `native_login_redirected` and `wp_admin_access_blocked` using the existing diagnostics settings gate and custom diagnostics table.
- Kept diagnostics context privacy-conscious by recording action, destination path, preserved query argument names, request method, and user id when available, without storing raw login, key, or redirect query values.
- Added focused `FrontendRoutingTest` coverage for native-login redirect diagnostics and blocked wp-admin diagnostics.
- Verified initial local checks: PHP syntax passes for touched frontend/test files, focused `FrontendRoutingTest` passes with 7 tests and 34 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 689 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 199 tests and 994 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.35-branch-qa-20260705-172521\alynt-account-gateway-v0.1.35-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.34` metadata, routing diagnostics PHP, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.34` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.34`, and both routing diagnostics event codes are present. Runtime HTTP smoke enabled diagnostics temporarily, triggered a native `wp-login.php` redirect and blocked subscriber `wp-admin` access, confirmed `native_login_redirected` and `wp_admin_access_blocked` diagnostics rows were written without raw login email or full redirect URL values, and authenticated admin HTML smoke confirmed the Advanced / Tools diagnostics panel renders both event codes with no fatal/critical error output. Temporary settings, subscriber user, diagnostics rows, cookie state, and upload artifacts were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.35` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 689 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 199 tests and 994 assertions, PHP syntax checks for the main plugin and frontend class, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.35-20260705-173449\alynt-account-gateway-v0.1.35.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.35` header/constant/readme/POT metadata, routing diagnostics PHP, built admin CSS, and POT strings present.
- Installed the `0.1.35` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.35`, loaded constant `0.1.35`, and both diagnostics helpers present. Runtime HTTP smoke temporarily enabled diagnostics, triggered a native `wp-login.php` lost-password redirect and blocked subscriber `wp-admin` access, confirmed exactly the expected diagnostics events were written without raw login email or full redirect URL values, and authenticated admin HTML smoke confirmed the Advanced / Tools diagnostics panel renders both event codes with no fatal/critical error output. Temporary settings, subscriber user, diagnostics rows, cookie state, and upload artifacts were cleaned up after QA.
- Published GitHub release `v0.1.35`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.35`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.35.zip` verified with 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.35` metadata, routing diagnostics PHP, built admin CSS, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.34` release asset, confirming runtime `0.1.34` with routing diagnostics absent, clearing updater caches, detecting the available `0.1.35` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.35` with no update remaining, and re-smoking the two diagnostics events after the updater install. Temporary settings, subscriber user, diagnostics rows, cookie state, and uploaded downgrade artifact were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, redirect destinations, emergency bypass behavior, wp-admin capability checks, toolbar behavior, login behavior, registration flow, provider verification behavior, rate-limit enforcement, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, email delivery behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on diagnostics-only observability for account routing decisions.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates diagnostics events for native login redirects and blocked wp-admin access.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.36 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Log registration-flow failures into the existing verification activity table with `registration_flow` as the provider.
- [x] Add Security tab guidance for registration-flow failures such as missing terms consent, pending-registration storage failure, consent-record storage failure, confirmation-email failure, password mismatch, password-strength failure, and email becoming unavailable during account creation.
- [x] Keep changes scoped to admin-visible activity evidence with no settings schema, frontend routing, registration success path, provider verification policy, rate-limit thresholds, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for registration-flow activity rows and Security tab guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.36` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.36` from clean `master` after the `v0.1.35` release merge.
- Added reusable `registration_flow` activity logging to the existing plugin-owned verification log table.
- Added logging at blocked or failed registration-flow outcomes where a valid submitted email is available, including terms consent, pending registration storage, consent storage, confirmation email delivery, password validation, email availability during account creation, and user creation errors.
- Added Security tab provider labeling and guidance for `registration_flow` rows.
- Verified focused checks: PHP syntax passes for touched registration/admin files, focused `RegistrationServiceTest` passes with 20 tests and 83 assertions, and focused `SettingsPageSecurityStatusTest` passes with 6 tests and 71 assertions.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 699 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 202 tests and 1013 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.36-branch-qa-20260705-182425\alynt-account-gateway-v0.1.36-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.35` metadata, registration-flow logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.35` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.35`, and the registration-flow logger and Security tab guidance are present. Runtime smoke wrote a disposable `registration_flow` / `terms_required` activity row through the service, authenticated admin HTML smoke confirmed the Security tab renders the masked email, `Registration Flow` provider label, `terms_required` status, and terms-consent guidance with no fatal/critical error output. Temporary activity row, cookie state, and upload artifact were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.36` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 699 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 202 tests and 1013 assertions, PHP syntax checks for the main plugin, registration service, and admin settings page, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.36-20260705-182920\alynt-account-gateway-v0.1.36.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.36` header/constant/readme/POT metadata, registration-flow logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Installed the `0.1.36` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.36`, loaded constant `0.1.36`, and the registration-flow logger and Security tab guidance are present. Runtime smoke wrote a disposable `registration_flow` / `terms_required` activity row through the service, authenticated admin HTML smoke confirmed the Security tab renders the masked email, `Registration Flow` provider label, `terms_required` status, and terms-consent guidance with no fatal/critical error output. Temporary activity row, cookie state, and upload artifact were cleaned up after QA.
- Published GitHub release `v0.1.36`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.36`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.36.zip` verified with 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.36` metadata, registration-flow logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.35` release asset, confirming runtime `0.1.35` with registration-flow logging absent, clearing updater caches, detecting the available `0.1.36` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.36` with no update remaining, and re-smoking the `registration_flow` activity row plus Security tab guidance after the updater install. Temporary activity row, cookie state, and uploaded downgrade artifact were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, registration success behavior, provider policy decisions, rate-limit thresholds, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin-visible registration-flow activity evidence.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates `registration_flow` activity rows and Security tab guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.37 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add frontend-safe registration messages for Reoon and Turnstile provider failures without exposing provider internals or sensitive configuration details.
- [x] Improve Security tab guidance so Reoon and Turnstile failures distinguish policy blocks, missing configuration, provider connectivity, invalid provider responses, and failed customer challenges.
- [x] Keep changes scoped to copy/guidance only with no settings schema, frontend routing, registration success path, provider request payloads, provider policy decisions, rate-limit thresholds, dashboard, WooCommerce, webhook, email template, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for frontend provider messages and Security tab provider guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.37` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.37` from clean `master` after the `v0.1.36` release merge.
- Added frontend-safe registration messages for `alynt_ag_reoon_blocked`, `alynt_ag_reoon_missing`, `alynt_ag_reoon_request_failed`, `alynt_ag_reoon_invalid_response`, `alynt_ag_turnstile_failed`, `alynt_ag_turnstile_missing`, and `alynt_ag_turnstile_request_failed`.
- Updated Security tab provider guidance so admins get clearer next actions for missing Reoon keys, Reoon connectivity failures, unexpected Reoon responses, Turnstile challenge rejection, missing Turnstile keys, and Cloudflare verification connectivity failures.
- Verified focused checks: PHP syntax passes for touched frontend/admin files, focused `FrontendMessagesTest` passes with 5 tests and 15 assertions, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 81 assertions, and `npm.cmd run lint` passes after automatic array-alignment cleanup.
- Verified broader local checks: `npm.cmd run build` passes, `npm.cmd run make-pot` writes 706 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 203 tests and 1030 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.37-branch-qa-20260705-184835\alynt-account-gateway-v0.1.37-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.36` metadata, frontend-safe provider messages, Security tab provider guidance, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.36` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.36`, and the provider message/guidance markers are present. Runtime smoke confirmed frontend-safe Reoon and Turnstile registration messages through the message service, inserted disposable Reoon and Turnstile provider failure rows, and authenticated admin HTML smoke confirmed the Security tab renders the new guidance with masked emails and no fatal/critical error output. Temporary activity rows, cookie state, and upload artifact were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.37` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Re-ran release-candidate validation after the metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` with 706 strings, `npm.cmd run lint`, `npm.cmd audit --audit-level=moderate`, full `npm.cmd test` with 203 tests and 1030 assertions, PHP syntax checks for the main plugin, frontend messages, and admin settings page, and `git diff --check` all passed.
- Created release-candidate package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.37-20260705-185509\alynt-account-gateway-v0.1.37.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.37` header/constant/readme/POT metadata, frontend-safe provider messages, Security tab provider guidance, built admin CSS, and POT strings present.
- Installed the `0.1.37` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header `0.1.37`, loaded constant `0.1.37`, and the provider message/guidance markers are present. Runtime smoke confirmed frontend-safe Reoon and Turnstile registration messages through the message service, inserted disposable Reoon and Turnstile provider failure rows, and authenticated admin HTML smoke confirmed the Security tab renders the new guidance with masked emails and no fatal/critical error output. Temporary activity rows, cookie state, and upload artifact were cleaned up after QA.
- Published GitHub release `v0.1.37`: `https://github.com/NichlasB/alynt-account-gateway/releases/tag/v0.1.37`. The Build Release workflow completed successfully, and the public asset `alynt-account-gateway-v0.1.37.zip` verified with 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.37` metadata, frontend-safe provider messages, Security tab provider guidance, built admin CSS, and POT strings present.
- Verified the Alynt Plugin Updater path end to end on LocalWP Plugin Tester by downgrading to the public `v0.1.36` release asset, confirming runtime `0.1.36` with the new provider message markers absent, clearing updater caches, detecting the available `0.1.37` update from the updater-discovered GitHub package URL, installing that package through WordPress upgrader classes, confirming runtime `0.1.37` with no update remaining, and re-smoking frontend-safe provider messages plus Security tab guidance after the updater install. Temporary activity rows, cookie state, and uploaded downgrade artifact were cleaned up after verification.

### Guardrails

- Do not change saved settings schema, frontend routes, provider validation behavior, provider request payloads, Reoon/Turnstile policy decisions, rate-limit thresholds, registration success behavior, email template content, webhook dispatch behavior, dashboard rendering, WooCommerce endpoint delegation, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on provider failure feedback and admin guidance copy.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates frontend-safe provider messages and Security tab guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.34 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Log blocked login and password-reset rate-limit outcomes into the existing verification activity table.
- [x] Add admin guidance for `login_rate_limited` and `lostpassword_rate_limited` activity rows.
- [x] Keep changes scoped to auth-side rate-limit evidence and admin visibility with no settings schema, frontend routing, authentication success/failure behavior, reset email behavior, rate-limit thresholds, dashboard, WooCommerce, webhook, privacy cleanup, or default frontend-output behavior changes.
- [x] Add focused coverage for auth-side rate-limit logging and rendered guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.34` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.34` from clean `master` after the `v0.1.33` release merge.
- Reused the existing plugin-owned `verification_logs` table instead of adding a schema migration.
- Added blocked login and password-reset throttles to the shared Security tab activity stream with `rate_limit` as the provider and `login_rate_limited` / `lostpassword_rate_limited` statuses.
- Added Guidance column messages for blocked login attempts and blocked password-reset requests.
- Added focused `AuthServiceTest` coverage proving both auth limiter buckets write blocked activity rows, plus `SettingsPageSecurityStatusTest` coverage for the rendered Security tab guidance.
- Verified initial local checks: PHP syntax passes for touched PHP files, focused `AuthServiceTest` passes with 11 tests and 31 assertions, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 67 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 687 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 197 tests and 974 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.34-branch-qa-20260705-165919\alynt-account-gateway-v0.1.34-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.33` metadata, auth rate-limit logging PHP, Security tab guidance PHP, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.33` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.33`, auth rate-limit logging code and Security tab guidance code are present. Runtime smoke triggered unique login and password-reset limiter blocks, confirmed `login_rate_limited` and `lostpassword_rate_limited` rows were written to the verification log table, and authenticated HTTP smoke confirmed the Security tab returns both statuses and both guidance messages with no fatal/critical error output. Temporary QA rows and upload artifacts were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.34` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 687 strings and `0.1.34` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 197 tests and 974 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, PHP syntax passes, and `git diff --check` passes with only line-ending warnings for generated/package metadata files.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.34-20260705-170558\alynt-account-gateway-v0.1.34.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.34` header/constant/readme/POT metadata, auth rate-limit logging PHP, Security tab guidance PHP, built admin CSS, and POT present.
- Installed the local `0.1.34` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.34`, auth rate-limit logging code and Security tab guidance code are present. Runtime smoke triggered unique login and password-reset limiter blocks, confirmed `login_rate_limited` and `lostpassword_rate_limited` rows were written to the verification log table, and authenticated HTTP smoke confirmed the Security tab returns both statuses and both guidance messages with no fatal/critical error output. Temporary QA rows and staged upload ZIP were cleaned up after QA.
- Published GitHub release `v0.1.34`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs files, `0.1.34` header/constant/readme/POT metadata, auth rate-limit logging PHP, Security tab guidance PHP, built admin CSS, and POT present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.33` to `0.1.34`, then installed it through the WordPress plugin update path using the updater-discovered GitHub release ZIP URL. Final fresh runtime state: active `0.1.34` header/constant, auth rate-limit logging PHP and Security tab guidance PHP present, Alynt Plugin Updater reports current/new `0.1.34` with no update available, and authenticated HTTP smoke confirmed the Security tab returns `login_rate_limited`, `lostpassword_rate_limited`, both guidance messages, and no fatal/critical error output. Temporary QA rows and staged upload ZIP were cleaned up after QA.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication success/failure behavior, password reset email behavior, registration flow, provider verification behavior, rate-limit enforcement thresholds, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on auth-side rate-limit evidence and read-only admin activity guidance only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders auth rate-limit activity guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.33 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add admin-readable guidance to Recent Registration Verification Activity rows so raw provider/status codes explain what happened.
- [x] Cover passed, flagged, blocked, Turnstile failure, and rate-limit outcomes without changing registration behavior or stored verification data.
- [x] Keep changes scoped to admin visibility with no schema, settings, registration flow, provider verification, rate-limit enforcement, email delivery, webhook, frontend, dashboard, WooCommerce, privacy retention, or default frontend-output behavior changes.
- [x] Add focused coverage for rendered provider guidance.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.33` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.33` from clean `master` after the `v0.1.32` release merge.
- Added a Guidance column to the Security tab Recent Registration Verification Activity table. The guidance explains accepted Reoon emails, flagged Reoon statuses, Reoon policy blocks, Turnstile failures, and registration/confirmation resend rate-limit blocks.
- Added focused `SettingsPageSecurityStatusTest` coverage for passed, flagged, blocked, Turnstile failed, and rate-limited guidance output.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 61 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 685 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 197 tests and 960 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.33-branch-qa-20260705-162731\alynt-account-gateway-v0.1.33-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.32` metadata, verification guidance PHP, built admin CSS, and POT present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.32` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.32`, the verification guidance code and built admin CSS are present. Seeded five temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Guidance column, Reoon accepted/flagged/blocked explanations, Turnstile failed explanation, rate-limit explanation, masked email rows, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.33` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 685 strings and `0.1.33` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 197 tests and 960 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, PHP syntax passes, and `git diff --check` passes with only line-ending warnings for generated/package metadata files.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.33-20260705-163414\alynt-account-gateway-v0.1.33.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.33` header/constant/readme/POT metadata, verification guidance PHP, built admin CSS, and POT present.
- Installed the local `0.1.33` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.33`, the verification guidance code and built admin CSS are present. Seeded five temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Guidance column, Reoon accepted/flagged/blocked explanations, Turnstile failed explanation, rate-limit explanation, masked email rows, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Published GitHub release `v0.1.33`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs files, `0.1.33` header/constant/readme/POT metadata, verification guidance PHP, built admin CSS, and POT present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.32` to `0.1.33`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.33` header/constant, verification guidance PHP and built admin CSS present, no remaining update offer, Alynt Plugin Updater reports current/new `0.1.33`, and authenticated HTTP smoke confirmed the Security tab returns the Guidance column, Reoon accepted/flagged/blocked explanations, Turnstile failed explanation, rate-limit explanation, masked email rows, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, provider verification behavior, rate-limit enforcement, pending-registration persistence behavior, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only provider/status guidance in the Security tab only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders verification guidance.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.32 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a read-only Security tab panel for recent pending registration records using the existing plugin-owned pending registration table.
- [x] Mask email addresses in the admin panel and show compact status labels for pending, email-confirmed, completed, and expired records.
- [x] Keep changes scoped to admin visibility with no schema, registration flow, provider verification, rate-limit enforcement, email delivery, webhook, frontend, dashboard, WooCommerce, privacy retention, or default frontend-output behavior changes.
- [x] Add focused coverage for empty and populated pending-registration output.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.32` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.32` from clean `master` after the `v0.1.31` release merge.
- Added a Recent Pending Registrations table to the Security tab after the verification activity table. The table reads existing pending registration records, masks email addresses, and shows status, user id, created, confirmed, and expiry fields.
- Added derived Expired status output for pending or email-confirmed rows whose expiry timestamp has passed, without mutating stored registration records.
- Added focused `SettingsPageSecurityStatusTest` coverage for empty pending-registration output plus masked pending, email-confirmed, completed, and expired rows.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `SettingsPageSecurityStatusTest` passes with 6 tests and 49 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 667 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 197 tests and 948 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.32-branch-qa-20260705-155937\alynt-account-gateway-v0.1.32-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, pre-bump `0.1.31` metadata, pending-registration panel PHP, built admin CSS, and POT present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.31` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.31`, the pending-registration panel code and built admin CSS are present. Seeded two temporary pending-registration rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Pending Registrations table, masked pending and expired email rows, Pending and Expired statuses, existing verification activity, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.32` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 667 strings and `0.1.32` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 197 tests and 948 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, PHP syntax passes, and `git diff --check` passes with only line-ending warnings for generated/package metadata files.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.32-20260705-160828\alynt-account-gateway-v0.1.32.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs/build files, `0.1.32` header/constant/readme/POT metadata, pending-registration panel PHP, built admin CSS, and POT present.
- Installed the local `0.1.32` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.32`, the pending-registration panel code and built admin CSS are present. Seeded two temporary pending-registration rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Pending Registrations table, masked pending and expired email rows, Pending and Expired statuses, existing verification activity, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.
- Published GitHub release `v0.1.32`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor/docs files, `0.1.32` header/constant/readme/POT metadata, pending-registration panel PHP, built admin CSS, and POT present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.31` to `0.1.32`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.32` header/constant, pending-registration panel PHP and built admin CSS present, no remaining update offer, Alynt Plugin Updater reports current/new `0.1.32`, and authenticated HTTP smoke confirmed the Security tab returns the Recent Pending Registrations table, masked pending/expired email rows, Pending and Expired statuses, existing verification activity, admin CSS, and no fatal/critical error output. Seeded QA rows were cleaned up after QA.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, pending-registration persistence behavior, provider verification behavior, rate-limit enforcement, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only pending-registration visibility in the Security tab only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders pending-registration visibility.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.31 Small Release Cycle

### Scope

- [x] Start the next security and anti-spam hardening slice from the released `master` baseline.
- [x] Log registration provider outcomes into the existing verification log table.
- [x] Log registration and confirmation-resend rate-limit blocks into the existing verification log table.
- [x] Add a read-only Security tab activity table for recent provider/rate-limit outcomes with masked email addresses.
- [x] Keep changes scoped to security evidence and admin visibility with no schema, retention, privacy exporter/eraser, registration flow, provider verification, rate-limit enforcement, frontend, dashboard, WooCommerce, webhook, or email delivery behavior changes.
- [x] Add focused coverage for passed, blocked, flagged, and rate-limited activity plus the rendered Security tab activity table.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.31` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.31` from clean `master` after the `v0.1.30` release merge.
- Reused the existing plugin-owned `verification_logs` table instead of adding a schema migration.
- Added registration verification logging for Turnstile and Reoon provider outcomes. Successful checks store compact statuses such as `passed`, `safe`, or `role_account_flagged`; provider errors store their sanitized error code and mark the row blocked.
- Added blocked registration and confirmation-resend throttles to the same activity stream with `rate_limit` as the provider and bucket-specific statuses.
- Added a Recent Registration Verification Activity table to the Security tab. The table masks email addresses, labels providers, shows outcome codes, and distinguishes Passed from Blocked decisions.
- Added focused `RegistrationServiceTest` coverage for logged safe, blocked, flagged, and rate-limited outcomes, plus `SettingsPageSecurityStatusTest` coverage for empty and populated activity output.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 657 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 195 tests and 933 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.31-branch-qa-20260705-151743\alynt-account-gateway-v0.1.31-branch-qa.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, pre-bump `0.1.30` metadata, registration verification logging PHP, activity panel PHP, and built admin activity CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.30` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.30`, registration verification logging PHP, activity panel PHP, and built admin activity CSS are present. Seeded two temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Registration Verification Activity table, masked email rows, Reoon Email Verifier and Rate Limit labels, `safe` and `registration_rate_limited` outcomes, Passed and Blocked decisions, admin CSS, and no fatal/critical error output. Temporary package, seeded QA rows, and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.31` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 657 strings and `0.1.31` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 195 tests and 933 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.31-20260705-152211\alynt-account-gateway-v0.1.31.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, `0.1.31` header/constant/readme/POT metadata, registration verification logging PHP, activity panel PHP, and built admin activity CSS present.
- Installed the local `0.1.31` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.31`, registration verification logging PHP, activity panel PHP, and built admin activity CSS are present. Seeded two temporary verification-log rows and authenticated HTTP smoke confirmed the Security tab returns the Recent Registration Verification Activity table, masked email rows, Reoon Email Verifier and Rate Limit labels, `safe` and `registration_rate_limited` outcomes, Passed and Blocked decisions, admin CSS, and no fatal/critical error output. Temporary package, seeded QA rows, and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.31`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.31` header/constant/readme metadata, registration verification logging PHP, activity panel PHP, and built admin activity CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.30` to `0.1.31`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.31` header/constant, registration verification logging PHP, activity panel PHP, built admin activity CSS present, no remaining update offer, and authenticated HTTP smoke confirmed the Security tab returns the Recent Registration Verification Activity table, masked email rows, Reoon Email Verifier and Rate Limit labels, `safe` and `registration_rate_limited` outcomes, Passed and Blocked decisions, admin CSS, and no fatal/critical error output.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, provider verification behavior, rate-limit enforcement, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on registration security evidence, rate-limit visibility, and read-only admin activity output only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders recent verification activity and rate-limit evidence.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.30 Small Release Cycle

### Scope

- [x] Start the security and anti-spam hardening slice from the released `master` baseline.
- [x] Add a read-only Security tab status panel for provider readiness, Reoon policy visibility, and rate-limit posture.
- [x] Keep changes admin-only with no registration flow, provider verification, rate-limit enforcement, settings schema, frontend, dashboard, WooCommerce, webhook, privacy, or email behavior changes.
- [x] Add focused coverage for missing-provider guidance, configured-provider guidance, Reoon default policy wording, and configured rate-limit values.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.30` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.30` from clean `master` after the `v0.1.29` release merge.
- Added a Security And Spam Status panel on the Security tab after the settings form so saved provider fields remain the primary editing surface.
- Added provider readiness cards for protection mode, Turnstile, Reoon Email Verifier, and the default Reoon policy. The policy message documents that invalid, disabled, disposable, and spamtrap statuses are blocked while catch-all, role account, unknown, and inbox-full statuses are allowed but flagged.
- Added rate-limit posture cards for registration, confirmation resend, login, and password reset windows.
- Added focused `SettingsPageSecurityStatusTest` coverage for missing providers, fully configured providers, Reoon policy wording, and configured rate-limit values.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 649 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 192 tests and 901 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.30-branch-qa-20260705-143958\alynt-account-gateway-v0.1.30-branch-qa.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, pre-bump `0.1.29` metadata, security status PHP, Reoon policy PHP, and built admin security CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.29` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.29`, security status PHP and built admin CSS are present. Authenticated HTTP smoke confirmed the Security tab returns the Security And Spam Status panel, provider readiness, protection mode, Turnstile, Reoon Email Verifier, Reoon Default Policy, registration/password-reset rate-limit cards, admin CSS, and no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.30` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 649 strings and `0.1.30` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 192 tests and 901 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.30-20260705-144650\alynt-account-gateway-v0.1.30.zip`; verified 46 runtime file entries, no backslash archive entries, no dev/source/test/vendor/build files, `0.1.30` header/constant/readme/POT metadata, security status PHP, Reoon policy PHP, and built admin security CSS present.
- Installed the local `0.1.30` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.30`, security status PHP and built admin CSS are present. Authenticated HTTP smoke confirmed the Security tab returns the Security And Spam Status panel, provider readiness, protection mode, Turnstile, Reoon Email Verifier, Reoon Default Policy, registration/password-reset rate-limit cards, admin CSS, and no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.30`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.30` header/constant/readme metadata, security status PHP, Reoon policy PHP, and built admin security CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.29` to `0.1.30`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.30` header/constant, security status PHP and built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed the Security tab returns the Security And Spam Status panel, provider readiness, protection mode, Turnstile, Reoon Email Verifier, Reoon Default Policy, registration/password-reset rate-limit cards, admin CSS, and no fatal/critical error output.

### Guardrails

- Do not change saved settings schema, frontend routing, authentication behavior, registration flow, provider verification behavior, rate-limit enforcement, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only Security tab status guidance and styling only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Security tab renders provider readiness, Reoon policy visibility, and rate-limit posture.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.29 Small Release Cycle

### Scope

- [x] Start the email template editor polish slice from the released `master` baseline.
- [x] Add richer token browsing for all email template tokens.
- [x] Add per-template guidance for purpose, action tokens, and disabled-email caveats.
- [x] Improve preview/test-send ergonomics with clearer descriptions and accessible form help.
- [x] Keep changes admin-only/read-only with no email delivery behavior, template storage, frontend, registration, provider, dashboard, WooCommerce, webhook, or privacy behavior changes.
- [x] Add focused coverage for token reference metadata and rendered email tools.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.29` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.29` from clean `master` after the `v0.1.28` release merge.
- Added reusable `ALYNT_AG_Email_Template_Service::token_reference()` metadata for every preview token.
- Expanded the Emails tab tools with a Template Reference panel, Available Template Tokens panel, sample token values, plain-text/core-email caveat copy, and clearer preview/test-send descriptions.
- Added `aria-describedby` help for preview template selection, test template selection, and the test recipient input.
- Added focused `EmailTemplateServiceTest` coverage for token reference metadata and `SettingsPageEmailToolsTest` coverage for template action-token guidance and rendered email tools.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 626 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 189 tests and 879 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Confirmed the built admin CSS contains the new email-tool styling.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.29-branch-qa-20260705-135904\alynt-account-gateway-v0.1.29-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.28` metadata, token reference PHP, template reference PHP, email tools markup, and built admin email-tool CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.28` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.28`, token reference PHP, template reference PHP, email tools markup, and built admin CSS are present. Authenticated HTTP smoke confirmed the Emails tab returns `200`, loads the admin CSS asset, renders the Template Reference and Available Template Tokens panels, shows action-token examples, includes linked preview/test-send `aria-describedby` help, and shows no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.29` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 626 strings and `0.1.29` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 189 tests and 879 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.29-20260705-140305\alynt-account-gateway-v0.1.29.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.29` header/constant/readme/POT metadata, token reference PHP, template reference PHP, email tools markup, and built admin email-tool CSS present.
- Installed the local `0.1.29` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.29`, token reference PHP, template reference PHP, email tools markup, and built admin CSS are present. Authenticated HTTP smoke confirmed the Emails tab returns `200`, loads the admin CSS asset, renders the Template Reference and Available Template Tokens panels, shows action-token examples, includes linked preview/test-send `aria-describedby` help, and shows no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.29`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.29` header/constant/readme metadata, token reference PHP, template reference PHP, email tools markup, and built admin email-tool CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.28` to `0.1.29`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.29` header/constant, token reference PHP, template reference PHP, email tools markup, built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed the Emails tab returns `200`, loads the admin CSS asset, renders the Template Reference and Available Template Tokens panels, shows action-token examples, includes linked preview/test-send `aria-describedby` help, and shows no fatal/critical error output.

### Guardrails

- Do not change saved settings schema, default email copy, token replacement behavior, email delivery behavior, email disable toggles, frontend routing, registration flow, provider verification behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, or default frontend-output disabled behavior.
- Keep this cycle focused on admin email editor guidance, token browsing, and preview/test-send ergonomics only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Emails tab renders template reference, token reference, and accessible preview/test-send help.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.28 Small Release Cycle

### Scope

- [x] Start the next settings UX refinement slice from the released `master` baseline.
- [x] Add field-level help text for high-impact Account Gateway settings.
- [x] Add `aria-describedby` linkage for native settings inputs that have help text.
- [x] Keep help advisory/read-only with no settings storage, routing, provider, email, dashboard, WooCommerce, webhook, privacy, or frontend behavior changes.
- [x] Add focused coverage for the help map, rendered help output, native input `aria-describedby`, and no-op missing help.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.28` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.28` from clean `master` after the `v0.1.27` release merge.
- Added reusable field-level help text under high-impact settings across General, URLs, Branding, Registration, Security, Emails, Dashboard, WooCommerce, Webhooks, Privacy, and Advanced / Tools.
- Added `aria-describedby` attributes for native input, textarea, select, checkbox, email, number, color, secret, and text controls when field help is available.
- Added focused `SettingsPageFieldHelpTest` coverage for high-impact help text, rendered text-field help, rendered boolean-field help, and missing-setting no-op output.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 592 strings, `npm.cmd run lint` passes after PHPCBF alignment cleanup, full `npm.cmd test` passes with 186 tests and 832 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Confirmed the built admin CSS contains the field-help styling.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.28-branch-qa-20260705-133906\alynt-account-gateway-v0.1.28-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.27` metadata, field-help PHP helpers, `aria-describedby` support, and built admin field-help CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.27` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.27`, field-help PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, URLs, Registration, and WooCommerce settings tabs return `200`, load the admin CSS asset, render expected helper text, and include linked `aria-describedby` attributes. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.28` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 592 strings and `0.1.28` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 186 tests and 832 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.28-20260705-134350\alynt-account-gateway-v0.1.28.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.28` header/constant/readme/POT metadata, field-help PHP helpers, `aria-describedby` support, and built admin field-help CSS present.
- Installed the local `0.1.28` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.28`, field-help PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, URLs, Registration, and WooCommerce settings tabs return `200`, load the admin CSS asset, show expected helper text, include linked `aria-describedby` attributes, and show no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.28`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.28` header/constant/readme metadata, field-help PHP helpers, `aria-describedby` support, and built admin field-help CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.27` to `0.1.28`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.28` header/constant, field-help PHP helpers and built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed General, URLs, Registration, and WooCommerce settings tabs return `200`, load the admin CSS asset, show expected helper text, include linked `aria-describedby` attributes, and show no fatal/critical error output.

### Guardrails

- Do not change frontend routing, authentication behavior, registration flow, provider verification behavior, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only field-level admin help text and styling only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative settings fields render help text and linked descriptions.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.27 Small Release Cycle

### Scope

- [x] Start the next settings UX refinement slice from the released `master` baseline.
- [x] Add read-only tab-level guidance panels across all settings tabs.
- [x] Keep guidance advisory only with no settings storage, routing, provider, email, dashboard, WooCommerce, webhook, privacy, or frontend behavior changes.
- [x] Add focused coverage for complete tab guidance, registration-to-security handoff, and invalid-tab fallback.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.27` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.27` from clean `master` after the `v0.1.26` release merge.
- Added a read-only settings guidance panel beneath the tab navigation. Each tab now shows a concise focus statement, three setup prompts, and an optional related-tab action for the next natural configuration area.
- Added focused `SettingsPageTabGuidanceTest` coverage for one guidance entry per registered settings tab, Registration tab guidance linking to Security, and invalid tab fallback to General guidance.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 544 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 182 tests and 820 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Confirmed the built admin CSS contains the tab guidance styles and mobile single-column fallback.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.27-branch-qa-20260705-131253\alynt-account-gateway-v0.1.27-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.26` metadata, tab guidance PHP, and built admin guidance CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.26` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.26`, tab guidance PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, Registration, WooCommerce, and Advanced / Tools tabs return `200`, render the guidance panel, load the admin CSS asset, show expected guidance copy, and show no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.27` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 544 strings and `0.1.27` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 182 tests and 820 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.27-20260705-131836\alynt-account-gateway-v0.1.27.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.27` header/constant/readme/POT metadata, tab guidance PHP, and built admin guidance CSS present.
- Installed the local `0.1.27` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.27`, tab guidance PHP and built admin CSS are present, and authenticated HTTP smoke confirmed General, Registration, WooCommerce, and Advanced / Tools tabs return `200`, render the guidance panel, load the admin CSS asset, show expected guidance copy, and show no fatal/critical error output. Temporary package and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.27`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.27` header/constant/readme metadata, tab guidance PHP, and built admin guidance CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.26` to `0.1.27`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.27` header/constant, tab guidance PHP and built admin CSS present, no remaining update offer, and authenticated HTTP smoke confirmed General, Registration, WooCommerce, and Advanced / Tools tabs return `200`, render the guidance panel, load the admin CSS asset, show expected guidance copy, and show no fatal/critical error output.

### Guardrails

- Do not change frontend routing, authentication behavior, registration flow, provider verification behavior, email delivery behavior, dashboard rendering, WooCommerce endpoint delegation, webhook dispatch behavior, privacy cleanup behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on read-only admin setup guidance and styling only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative settings tabs render the new guidance panel.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.26 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add scoped frontend CSS for delegated WooCommerce notices, forms, fieldsets, required labels, buttons, and payment-method containers inside branded dashboard content.
- [x] Keep changes presentation-only and preserve WooCommerce endpoint handlers, forms, submissions, and sensitive account flows.
- [x] Add focused CSS source coverage for key scoped WooCommerce selectors and mobile single-column fallback.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.26` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.26` from clean `master` after the `v0.1.25` release merge.
- Added dashboard-scoped CSS polish for WooCommerce notices, validation/error boxes, address/account form rows, fieldsets, required markers, submit buttons, and payment-method containers so delegated WooCommerce screens better match the branded dashboard shell without replacing WooCommerce logic.
- Added focused source-level CSS coverage to keep the WooCommerce selectors scoped to `.agw-dashboard-content` and preserve the mobile single-column fallback for address/account form grids.
- Verified initial local checks: PHP syntax passes for the new CSS source test, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 474 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 179 tests and 764 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only the existing POT line-ending warning.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.26-branch-qa-20260705-123438\alynt-account-gateway-v0.1.26-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, and no dev/source/test/vendor files.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.25` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.25`, built frontend CSS is present, and delegated WooCommerce CSS selectors for notices, account forms, payment methods, and mobile fallback are present.
- Authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/edit-address/`, `/my-account/edit-account/`, and `/my-account/payment-methods/` return `200`, render the branded dashboard and delegated content shell, load the frontend CSS asset, match expected endpoint copy, and show no fatal/critical error output. Temporary upload artifacts and curl cookie state were cleaned up after QA.
- Bumped release-candidate metadata to `0.1.26` across the plugin header/constant, npm metadata, readme, sample test, changelog, and implementation plan.
- Regenerated `languages/alynt-account-gateway.pot` with 474 strings and `0.1.26` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 179 tests and 764 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes with only POT/readme line-ending warnings.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.26-20260705-124007\alynt-account-gateway-v0.1.26.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.26` header/constant/readme/POT metadata, and built delegated WooCommerce CSS present.
- Installed the local `0.1.26` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.26`, built delegated WooCommerce CSS selectors are present, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/edit-address/`, `/my-account/edit-account/`, and `/my-account/payment-methods/` return `200`, render the branded dashboard and delegated content shell, load the frontend CSS asset, match expected endpoint copy, and show no fatal/critical error output. Temporary upload artifacts and curl cookie state were cleaned up after QA.
- Published GitHub release `v0.1.26`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.26` header/constant/readme metadata, and built delegated WooCommerce CSS selectors present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.25` to `0.1.26`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.26` header/constant, delegated WooCommerce CSS selectors present, no remaining update offer, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/edit-address/`, `/my-account/edit-account/`, and `/my-account/payment-methods/` return `200`, render the branded dashboard and delegated content shell, load the frontend CSS asset, match expected endpoint copy, and show no fatal/critical error output.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, WooCommerce form handlers, WooCommerce account data submission, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on scoped delegated-content presentation only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative delegated WooCommerce account endpoints.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.25 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add branded next-step panels for standard WooCommerce account endpoint edge states while preserving delegated WooCommerce endpoint content.
- [x] Keep custom/plugin-added WooCommerce endpoints free of plugin-authored affordance assumptions.
- [x] Add focused coverage for orders, downloads, payment methods, and custom endpoint restraint.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.25` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.25` from clean `master` after the `v0.1.24` release merge.
- Added contextual affordance panels above delegated WooCommerce endpoint content for orders, downloads, addresses, account details, and payment-methods pages. The panels point customers toward safe account next steps without taking over WooCommerce forms, tables, or endpoint handlers.
- Added focused dashboard screen coverage for Orders edge-state affordance, Downloads edge-state affordance, Payment Methods add-method affordance, and skipping affordances for plugin-added/custom endpoints.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `FrontendDashboardScreenTest` passes with 8 tests and 51 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 474 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 177 tests and 748 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.25-branch-qa-20260705-115634\alynt-account-gateway-v0.1.25-branch-qa.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, pre-bump `0.1.24` metadata, affordance renderer, and built affordance CSS present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.24` through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant remain pre-bump `0.1.24`, the affordance renderer and built affordance CSS are present, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/downloads/`, and `/my-account/payment-methods/` return `200` with the expected affordance panel text and no fatal output.
- Bumped release-candidate metadata to `0.1.25` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 474 strings and `0.1.25` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 177 tests and 748 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.25-20260705-120515\alynt-account-gateway-v0.1.25.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.25` header/constant/readme/POT metadata, affordance renderer, and built affordance CSS present.
- Installed the local `0.1.25` package on LocalWP Plugin Tester through WordPress upgrader classes. Fresh runtime verification confirmed active header and loaded constant are `0.1.25`, affordance PHP and built affordance CSS are present, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/downloads/`, and `/my-account/payment-methods/` return `200` with the expected affordance panel text and no fatal output.
- Published GitHub release `v0.1.25`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.25` header/constant/readme metadata, affordance renderer, and built affordance CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.24` to `0.1.25`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.25` header/constant, affordance PHP and built affordance CSS present, checked plugin version `0.1.25`, no remaining update offer, and authenticated HTTP smoke confirmed `/my-account/orders/`, `/my-account/downloads/`, and `/my-account/payment-methods/` return `200` with the expected affordance panel text and no fatal output.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, WooCommerce menu/link generation, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on endpoint edge-state help and presentation only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates endpoint affordance panels on representative WooCommerce endpoints.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.24 Small Release Cycle

### Scope

- [x] Start the next WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add branded guidance copy above delegated WooCommerce endpoint content for standard account areas.
- [x] Keep custom/plugin-added WooCommerce endpoints free of plugin-authored guidance assumptions.
- [x] Add focused coverage for endpoint guidance and custom endpoint restraint.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.24` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.24` from clean `master` after the `v0.1.23` release merge.
- Added endpoint-specific guidance for WooCommerce orders, order details, downloads, addresses, account details, and payment-method flows while preserving WooCommerce endpoint delegation.
- Added focused dashboard screen coverage for Orders guidance, Payment Methods guidance, and skipping guidance for plugin-added/custom endpoints.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused `FrontendDashboardScreenTest` passes with 7 tests and 37 assertions, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 460 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 176 tests and 734 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.24-branch-qa-20260705-112041\alynt-account-gateway-v0.1.24-branch-qa.zip`; verified the main plugin file and built frontend assets are included, and dev/source/test/vendor files are excluded.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.23` by replacing the local-only plugin files from the verified package after WP-CLI was unavailable. Verified the active plugin remains pre-bump `0.1.23`, installed files include endpoint guidance PHP and built CSS, and Plugin Tester settings already have frontend output, dashboard, and WooCommerce takeover enabled.
- Smoked Plugin Tester through WordPress rendering and authenticated HTTP checks: `/my-account/orders/` and `/my-account/payment-methods/` returned `200` and rendered the expected guidance text; direct renderer checks confirmed Orders guidance, Payment Methods guidance, delegated content shells, and no plugin-authored guidance for a custom endpoint.
- Bumped release-candidate metadata to `0.1.24` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 460 strings and `0.1.24` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 176 tests and 734 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.24-20260705-113710\alynt-account-gateway-v0.1.24.zip`; verified 45 runtime file entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.24` header/constant/readme/POT metadata, endpoint guidance renderer, and built frontend assets present.
- Installed the local `0.1.24` package on LocalWP Plugin Tester through WordPress upgrader classes after WP-CLI was unavailable. Fresh runtime verification confirmed active header and loaded constant are `0.1.24`, endpoint guidance PHP and built guidance CSS are present, and authenticated HTTP smoke confirmed `/my-account/orders/` and `/my-account/payment-methods/` return `200` with the expected guidance text.
- Published GitHub release `v0.1.24`, confirmed the Build Release workflow completed successfully, downloaded the public release asset, and verified 55 runtime entries, no backslash archive entries, no dev/source/test/vendor files, `0.1.24` header/constant/readme metadata, endpoint guidance renderer, and built guidance CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.23` to `0.1.24`, then installed it through the WordPress plugin update path from the GitHub release ZIP URL. Final fresh runtime state: active `0.1.24` header/constant, endpoint guidance PHP and built guidance CSS present, checked plugin version `0.1.24`, no remaining update offer, and authenticated HTTP smoke confirmed `/my-account/orders/` and `/my-account/payment-methods/` return `200` with the expected guidance text.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, WooCommerce menu/link generation, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on endpoint affordance copy and presentation only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates endpoint guidance on representative WooCommerce endpoints.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.23 Small Release Cycle

### Scope

- [x] Start the WooCommerce dashboard polish slice from the released `master` baseline.
- [x] Add a branded WooCommerce customer overview on the base dashboard when takeover is enabled and WooCommerce is available.
- [x] Keep WooCommerce endpoint pages delegated to WooCommerce handlers.
- [x] Add focused coverage for overview rendering and configured endpoint URLs.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.23` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.23` from clean `master` after the `v0.1.22` release merge.
- Added a WooCommerce-only dashboard overview on the base account page with branded customer-account copy and quick links for orders, addresses, and account details.
- Added a public WooCommerce endpoint URL helper so dashboard overview links follow the configured account base path.
- Verified initial local checks: PHP syntax passes for touched PHP/test files, focused dashboard and WooCommerce tests pass, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 448 strings, `npm.cmd run lint` passes, full `npm.cmd test` passes with 174 tests and 724 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.23-branch-qa-20260705-105241\alynt-account-gateway-v0.1.23-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.22` metadata, dashboard overview renderer, WooCommerce endpoint URL helper, built frontend overview CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.22` through the WordPress upgrader path. Verified active header and loaded constant remain pre-bump `0.1.22`, installed files include the dashboard overview renderer and endpoint URL helper, and built frontend CSS contains overview styles.
- Authenticated-smoked Plugin Tester with a temporary Novamira admin access session and curl cookie jar after the Playwright MCP browser backend closed: `/my-account/` rendered the branded dashboard overview, customer copy, orders/addresses/account quick links, and no endpoint content shell; `/my-account/orders/` rendered the branded dashboard shell, no overview, the delegated endpoint content shell, and the Orders content title. Restored the previous Plugin Tester settings and cleaned temporary upload artifacts after QA.
- Bumped release-candidate metadata to `0.1.23` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 448 strings and `0.1.23` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 174 tests and 724 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.23-20260705-110650\alynt-account-gateway-v0.1.23.zip`; verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.23` header/constant/readme/POT metadata, dashboard overview renderer, endpoint URL helper, built frontend overview CSS, and POT strings present.
- Installed the local `0.1.23` package on LocalWP Plugin Tester through the WordPress upgrader path. Fresh runtime verification confirmed active header and loaded constant are `0.1.23`, dashboard overview renderer and built overview CSS are present, and temporary upload artifacts were cleaned up.
- Published GitHub release `v0.1.23`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.23` header/constant/readme metadata, dashboard overview renderer, and built frontend overview CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.22` to `0.1.23`, then installed it through the WordPress upgrader path. Final fresh runtime state: active `0.1.23` header/constant, dashboard overview renderer and built overview CSS present, checked plugin version `0.1.23`, no remaining update offer, and temporary upload artifacts cleaned up.

### Guardrails

- Do not change frontend routing, authentication behavior, WooCommerce endpoint delegation, registration flow, email behavior, webhook behavior, provider verification behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep this cycle focused on customer-facing dashboard polish and small, testable WooCommerce affordances.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the branded WooCommerce dashboard overview and representative endpoint delegation.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.22 Small Release Cycle

### Scope

- [x] Start the next settings UX slice from the released `master` baseline.
- [x] Add a read-only setup readiness panel on the General tab.
- [x] Summarize critical setup checks before frontend output is enabled.
- [x] Surface warnings for public registration without Turnstile/Reoon, missing Terms/Privacy paths, missing email test recipient, dashboard/WooCommerce takeover gaps, and webhook signing gaps where applicable.
- [x] Add focused coverage for readiness check classification and panel output.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.22` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.22` from clean `master` after the `v0.1.21` release merge.
- Added a read-only General tab Setup Readiness panel with action/review/ready counts and tab links for frontend output, gateway URLs, emergency access, branding, public registration, email testing, dashboard, WooCommerce takeover, webhook signing, and privacy retention checks.
- Added focused `SettingsPageReadinessTest` coverage for safe default classification, public registration without provider warning, WooCommerce takeover dependency, and rendered panel summary/link output.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` writes 439 strings, `npm.cmd run lint`, `npm.cmd test` passes with 172 tests and 715 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.22-branch-qa-20260705-103309\alynt-account-gateway-v0.1.22-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.21` metadata, readiness panel code, built admin CSS, and POT strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.21` through the WordPress upgrader path. Verified active header and loaded constant remain pre-bump `0.1.21`, installed admin file includes readiness panel/check code, and built admin CSS contains readiness styles.
- Browser-smoked Plugin Tester General tab through temporary Novamira admin access using Playwright with the system Edge channel: one setup readiness panel, ten check rows, action/review/ready summary text, key check labels, and `Open Setting` links rendered correctly.
- Bumped release-candidate metadata to `0.1.22` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 439 strings and `0.1.22` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 172 tests and 715 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.22-20260705-103755\alynt-account-gateway-v0.1.22.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, `0.1.22` header/constant/readme/POT metadata, readiness panel code, and built readiness CSS present.
- Installed the local `0.1.22` package on LocalWP Plugin Tester through the WordPress upgrader path. Fresh runtime verification confirmed active header and loaded constant are `0.1.22`, readiness panel code and built readiness CSS are present, and temporary upload artifacts were cleaned up.
- Published GitHub release `v0.1.22`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.22` header/constant/readme metadata, readiness panel code, and built readiness CSS present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.21` to `0.1.22`, then installed it through the WordPress upgrader path. Final fresh runtime state: active `0.1.22` header/constant, readiness panel code and built readiness CSS present, checked plugin version `0.1.22`, no remaining update offer, and temporary upload artifacts cleaned up.

### Guardrails

- Do not change frontend routing, authentication behavior, registration flow, email delivery behavior, provider verification behavior, WooCommerce endpoint delegation, webhook dispatch behavior, settings storage shape, or default frontend-output disabled behavior.
- Keep readiness checks advisory/read-only and admin-only.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the General tab readiness panel.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.21 Small Release Cycle

### Scope

- [x] Start the next admin UX slice from the released `master` baseline.
- [x] Add a Webhooks tab delivery summary based on the most recent webhook log row.
- [x] Add signature verification guidance that reflects whether webhook signing is configured.
- [x] Add expandable delivery metadata for recent webhook log rows without changing dispatch behavior or log retention.
- [x] Add focused coverage where practical.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.21` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.21` from clean `master` after the `v0.1.20` release merge.
- Added Webhooks tab delivery summary, signing status, signature verification reference, and expandable per-row delivery details without changing webhook dispatch behavior.
- Added focused admin webhook UX coverage for summary copy, signed/unsigned guidance, expanded row metadata, and invalid timestamp fallback. Verified targeted `SettingsPageWebhookUxTest` passed with 4 tests and 16 assertions.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` writes 398 strings, `npm.cmd run lint`, `npm.cmd test` passes with 168 tests and 696 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.21-branch-qa-20260705-100422\alynt-account-gateway-v0.1.21-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.20` metadata, and webhook delivery UX strings present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.20` through the WordPress upgrader path. Verified active header and loaded constant remain pre-bump `0.1.20`, installed admin file includes delivery summary/signature reference/details strings, and temporary settings/uploads were cleaned up.
- Browser-smoked Plugin Tester Webhooks tab through temporary Novamira admin access using Playwright with the system Edge channel: `Webhook Tools`, `Delivery Status:`, signing-enabled guidance, `Signature Verification Reference`, `Recent Webhook Deliveries`, `Details`, `View`, event text, and destination text rendered correctly.
- Bumped release-candidate metadata to `0.1.21` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 398 strings and `0.1.21` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 168 tests and 696 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.21-20260705-101155\alynt-account-gateway-v0.1.21.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, `0.1.21` header/constant/readme/POT metadata, and webhook delivery UX strings present.
- Installed the local `0.1.21` package on LocalWP Plugin Tester through the WordPress upgrader path. Fresh runtime verification confirmed active header and loaded constant are `0.1.21`, delivery summary/signature reference/details strings are present, and temporary upload artifacts were cleaned up.
- Published GitHub release `v0.1.21`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.21` header/constant/readme metadata, and webhook delivery UX strings present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.20` to `0.1.21`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.21` header/constant, delivery summary/signature reference/details strings present, and no remaining update offer.

### Guardrails

- Do not change webhook dispatch behavior, signing algorithm, payload shape, event names, URL policy, log retention, registration flow, email behavior, frontend routes, dashboard rendering, WooCommerce delegation, or provider verification behavior.
- Keep the slice admin-only and read-only except for the existing test webhook action.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates Webhooks tab rendering and recent delivery metadata.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.20 Small Release Cycle

### Scope

- [x] Start the next integration-hardening slice from the released `master` baseline.
- [x] Add an optional webhook signing secret setting on the Webhooks tab.
- [x] Sign webhook request bodies with timestamped HMAC headers when a signing secret is configured.
- [x] Add focused coverage for unsigned and signed webhook dispatch behavior.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.20` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.20` from clean `master` after the `v0.1.19` release merge.
- Added an optional `webhook_signing_secret` setting on the Webhooks tab, defaulting to empty so existing integrations remain unsigned.
- Added signed webhook headers when a secret is configured: `X-Alynt-AG-Event`, `X-Alynt-AG-Time`, `X-Alynt-AG-Version`, and `X-Alynt-AG-Signature` using HMAC-SHA256 over `{timestamp}.{event}.{json_body}`.
- Added focused PHPUnit coverage for webhook signing defaults/sanitization, unsigned dispatch, and signed dispatch. Targeted `WebhookDispatcherTest|SettingsSchemaTest` passed with 27 tests and 131 assertions.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run make-pot` writes 382 strings, `npm.cmd run lint`, `npm.cmd test` passes with 164 tests and 680 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.20-branch-qa-20260705-000212\alynt-account-gateway-v0.1.20-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, pre-bump `0.1.19` metadata, and signing setting/header code present.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.19`; verified installed signing setting/method/header markers and safe intercepted signed test dispatch. The `account.created.test` signature matched the exact intercepted body, the log row recorded HTTP `202` success, and temporary settings/log artifacts were cleaned up.
- Bumped release-candidate metadata to `0.1.20` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT.
- Regenerated `languages/alynt-account-gateway.pot` with 382 strings and `0.1.20` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 164 tests and 680 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.20-20260705-000519\alynt-account-gateway-v0.1.20.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, `0.1.20` header/constant/readme/POT metadata, and signing setting/header code present.
- Installed the local `0.1.20` package on LocalWP Plugin Tester; verified active header and loaded constant are `0.1.20`, signing setting/header markers are present, and safe intercepted signed test dispatch produced a matching signature and HTTP `202` success log row without external network calls.
- Published GitHub release `v0.1.20`, re-uploaded the inspected release asset to ensure `CHANGELOG.md` parity, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, `0.1.20` header/constant/readme metadata, and signing setting/header code present.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.19` to `0.1.20`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.20` header/constant, signing setting/header markers present, and no remaining update offer.

### Guardrails

- Do not change the existing account-created payload shape, event names, test-send behavior, webhook URL policy, logging retention, registration flow, email behavior, frontend routes, dashboard rendering, WooCommerce delegation, or provider verification behavior.
- Keep signing optional and disabled by default so existing webhook consumers continue working until a site owner configures a shared secret.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates signed test webhook dispatch and Webhooks tab rendering.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.19 Small Release Cycle

### Scope

- [x] Start the next product-polish slice from the released `master` baseline.
- [x] Add Webhooks tab tools for sending an admin-triggered account-created test webhook to the configured destination.
- [x] Add a recent webhook deliveries table on the Webhooks tab using plugin-owned webhook log metadata.
- [x] Add focused coverage for test webhook dispatch behavior where practical.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.19` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.19` from clean `master` after the `v0.1.18` release merge.
- Added `ALYNT_AG_Webhook_Dispatcher::dispatch_account_created_test()` so admins can send an explicit `account.created.test` event through the saved account-created webhook URL without changing the normal account-created payload path.
- Added Webhooks tab tools: a nonce-protected `Send Test Webhook` action and a recent webhook deliveries table showing event, destination host, HTTP status, result, error, and timestamp.
- Added focused PHPUnit coverage for the test dispatch path. Verified targeted `WebhookDispatcherTest` plus full `npm.cmd test` passed with 162 tests and 671 assertions.
- Verified branch checks before metadata bump: `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.19-branch-qa-20260704-233152\alynt-account-gateway-v0.1.19-branch-qa.zip`; inspected 46 packaged files, no dev/source entries, no backslash zip entries, header/constant still `0.1.18` as expected before release bump.
- Installed the branch-QA package on LocalWP Plugin Tester over active `0.1.18`; verified active plugin, installed method/action/table strings, and safe intercepted test dispatch logging `account.created.test` with HTTP `202`, success `1`, and no external network call.
- Browser-smoked Plugin Tester Webhooks tab through temporary Novamira admin access: `Webhook Tools`, `Send Test Webhook`, `Recent Webhook Deliveries`, disabled send button, and missing-URL helper text rendered correctly.
- Bumped release-candidate metadata to `0.1.19` across the plugin header/constant, npm metadata, readme, sample test, and changelog.
- Regenerated `languages/alynt-account-gateway.pot` with 381 strings and `0.1.19` project metadata. Verified release-candidate `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 162 tests and 671 assertions, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.19-20260704-233844\alynt-account-gateway-v0.1.19.zip`; verified 46 runtime files, no backslash archive entries, no missing runtime files, no dev/source/test/docs/rules/package/vendor files, and `0.1.19` header/constant/readme/POT metadata.
- Installed the local `0.1.19` package on LocalWP Plugin Tester; verified active header and loaded constant are `0.1.19`, webhook test method/action/table strings are present, and safe intercepted test dispatch logs `account.created.test` with HTTP `202` and success `1` without external network calls.
- Published GitHub release `v0.1.19`, downloaded the public release asset, and verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, and `0.1.19` header/constant/readme metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.18` to `0.1.19`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.19` header/constant, webhook test method/action/table strings present, and no remaining update offer.

### Guardrails

- Do not change normal account-created webhook behavior, payload shape for real account creation events, registration flow, email behavior, frontend output, routes, dashboard rendering, WooCommerce delegation, provider verification behavior, or existing webhook retention defaults.
- Keep this cycle focused on admin webhook observability and an explicit test-send action.
- Defer final `0.1.19` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Webhooks tab test-send and recent delivery table.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.18 Small Release Cycle

### Scope

- [x] Start the next product-polish slice from the released `master` baseline.
- [x] Replace the raw custom dashboard links JSON field with a repeatable admin editor for label, URL, icon, ordering, role visibility, and open-in-new-tab behavior while preserving the existing `dashboard_custom_links` storage format.
- [x] Add focused coverage for custom dashboard link sanitization/serialization where practical.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.18` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.18` from clean `master` after the `v0.1.17` release merge.
- Added a repeatable Dashboard settings editor for custom dashboard links with label, URL, icon, order, open-in-new-tab, and role-visibility controls, plus a raw JSON fallback panel that preserves the existing `dashboard_custom_links` storage format.
- Added backend sanitization for dashboard links so saved/imported JSON is normalized to known fields and incomplete rows are skipped.
- Added focused `SettingsSchemaTest` coverage for custom dashboard link JSON sanitization.
- Verified `php -l` for the touched PHP files; `npm.cmd run build` passes; `npm.cmd run lint` passes; full `npm.cmd test` passes with 161 tests and 665 assertions; `npm.cmd run make-pot` writes 365 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.18-branch-qa-20260704-230747\alynt-account-gateway-v0.1.18-branch-qa.zip`; verified 46 runtime files, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built admin assets, and `0.1.17` header/constant metadata as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.17` header/constant, built admin asset present, settings page editor markup present, and dashboard-link schema type present.
- Browser-tested the Dashboard settings editor in wp-admin with Playwright: added a `QA Support Portal` link through the repeatable editor, set `/support/`, `help` icon, order `12`, new-tab behavior, and `customer` role visibility, then saved successfully and confirmed the stored JSON.
- Server-side rendered the dashboard for a temporary customer user and confirmed the custom link appears with the normalized site URL, new-tab accessible text, and `help` icon class. Removed the temporary user and restored Plugin Tester dashboard links to `[]` after QA.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, and `/my-account/`; public gateway routes rendered branded output with expected screen markers, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed temporary branch-QA ZIP artifacts from Plugin Tester uploads, including the duplicate-path artifact created by the temporary upload endpoint.
- Bumped release-candidate metadata to `0.1.18` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 161 tests and 665 assertions, `npm.cmd run make-pot` writes 365 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.18-20260704-231643\alynt-account-gateway-v0.1.18.zip`; verified built admin assets and dashboard-link editor runtime files are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.18`.
- Published GitHub release `v0.1.18`, verified the remote tag points to release commit `a7b2965`, downloaded the public release asset, and verified the downloaded package has 46 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built admin CSS/JS assets, dashboard-link editor runtime files, and `0.1.18` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.17` to `0.1.18`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.18` header/constant, dashboard-link editor markup/schema present, built admin JS/CSS contain the dashboard-link editor markers, custom dashboard links restored to `[]`, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, and `/my-account/`; public gateway routes rendered branded output with expected screen markers, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change frontend dashboard link rendering, WooCommerce dashboard delegation, dashboard default links, dashboard link visibility rules, URL normalization behavior, routes, query parameters, auth flow, registration flow, email behavior, webhook behavior, provider verification behavior, frontend class names, or design-token names.
- Keep this cycle focused on the admin editing experience and compatible dashboard-link persistence.
- Defer final `0.1.18` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates the Dashboard settings editor and representative dashboard output after saving custom links.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.17 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract full gateway document rendering and admin preview rendering out of the frontend hook/controller class without changing document markup, body class, page title behavior, dashboard-vs-auth shell selection, preview screen normalization, routes, redirects, logout handling, or admin preview compatibility.
- [x] Add focused test coverage around the extracted frontend document renderer service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.17` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.17` from clean `master` after the `v0.1.16` release merge.
- Extracted full gateway document rendering into `ALYNT_AG_Frontend_Document_Renderer`, including status/no-cache headers, HTML document wrapper, document title lookup, `wp_head()`/`wp_footer()` placement, dashboard-vs-auth shell selection, admin preview screen normalization, set-password preview rendering, and screen-title lookup.
- Kept `ALYNT_AG_Frontend` hook registration, frontend asset enqueueing, route detection, native login redirect behavior, emergency bypass handling, URL filters, wp-admin blocking, logout confirmation execution, current-path calculation, and public admin-preview title wrapper intact while delegating document/preview rendering to the new service.
- Added focused `FrontendDocumentRendererTest` coverage for full auth document output, dashboard document output with current-path propagation, unknown preview fallback, set-password preview output, renderer title lookup, and the preserved `ALYNT_AG_Frontend::get_screen_title()` wrapper used by admin preview.
- Added test bootstrap shims for `status_header()`, `nocache_headers()`, `language_attributes()`, `wp_head()`, and `wp_footer()` so document rendering can be verified without a full WordPress runtime.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendDocumentRendererTest` passes with 6 tests and 16 assertions; full `npm.cmd test` passes with 160 tests and 657 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings with no string changes; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.17-branch-qa-20260704-224135\alynt-account-gateway-v0.1.17-branch-qa.zip`; verified the new frontend document renderer service, runtime plugin files, built frontend/admin assets, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.16` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.16` header/constant, `ALYNT_AG_Frontend_Document_Renderer` file/class loaded in a fresh request, preview rendering works for login and set-password, the full document wrapper renders, and the preserved admin-preview title wrapper returns `Create Account`.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with the expected body class, frontend JS assets, and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed the branch-QA zip from Plugin Tester uploads after smoke verification.
- Bumped release-candidate metadata to `0.1.17` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 160 tests and 657 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.17-20260704-224614\alynt-account-gateway-v0.1.17.zip`; verified built frontend/admin assets and the new frontend document renderer service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.17`.
- Published GitHub release `v0.1.17`, corrected the release target/tag to the `0.1.17` release commit, replaced the initially stale release asset, downloaded the public release asset, and verified the downloaded package has 46 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend document renderer service, and `0.1.17` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.16` to `0.1.17`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.17` header/constant, `ALYNT_AG_Frontend_Document_Renderer` file/class loaded, admin-preview title wrapper intact, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with the expected body class, frontend JS assets, and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change rendered gateway copy, document structure, body class, page title mapping, routes, query parameters, redirects, emergency bypass behavior, wp-admin blocking, logout behavior, registration request handling, login/lost-password/set-password behavior, email behavior, WooCommerce dashboard behavior, dashboard output, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or dashboard link normalization behavior.
- Keep this cycle focused on document and preview rendering; leave request routing, auth services, registration storage, provider verification, email delivery, webhook behavior, and WooCommerce endpoint delegation untouched.
- Defer final `0.1.17` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the document renderer extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.16 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the generic branded gateway shell and auth screen dispatch out of the large frontend renderer class without changing shell markup, branding output, media panel output, screen copy, routes, query parameters, nonce/action names, password preview behavior, dashboard behavior, or request handling.
- [x] Add focused test coverage around the extracted frontend gateway shell service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.16` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.16` from clean `master` after the `v0.1.15` release merge.
- Extracted the branded auth shell into `ALYNT_AG_Frontend_Gateway_Shell`, including shell wrapper markup, inline branding style output, media panel rendering, brand block rendering, auth screen dispatch, and the admin set-password preview shell.
- Kept `ALYNT_AG_Frontend` request flow, frontend asset enqueueing, route detection, native login redirect behavior, emergency bypass handling, URL filters, logout confirmation handling, dashboard rendering, and document title behavior intact while delegating non-dashboard auth shell output to the new service.
- Removed now-unused private frontend wrapper methods for auth screen rendering, branding helper access, path comparison, and resend-error message lookup.
- Added focused `FrontendGatewayShellTest` coverage for shell wrapper output, branding/media insertion, screen dispatch across login/register/lost-password/set-password/logout/state fallbacks, unknown-screen fallback, and set-password preview form rendering.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendGatewayShellTest` passes with 9 tests and 18 assertions; full `npm.cmd test` passes with 154 tests and 641 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings with no string changes; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.16-branch-qa-20260704-220428\alynt-account-gateway-v0.1.16-branch-qa.zip`; verified the new frontend gateway shell service, runtime plugin files, built frontend/admin assets, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.15` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.15` header/constant, `ALYNT_AG_Frontend_Gateway_Shell` file/class loaded, and server-side gateway shell rendering includes the branded gateway wrapper, login screen marker, set-password preview marker, preview user hidden field, and no native login shell marker.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed the branch-QA zip from Plugin Tester uploads after smoke verification.
- Bumped release-candidate metadata to `0.1.16` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 154 tests and 641 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.16-20260704-221009\alynt-account-gateway-v0.1.16.zip`; verified built frontend/admin assets and the new frontend gateway shell service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.16`.
- Published GitHub release `v0.1.16`, downloaded the public release asset, and verified the downloaded package has 45 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend gateway shell service, and `0.1.16` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.15` to `0.1.16`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.16` header/constant, `ALYNT_AG_Frontend_Gateway_Shell` file/class loaded, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, login/lost-password/set-password/logout behavior, email behavior, WooCommerce dashboard behavior, dashboard output, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or dashboard link normalization behavior.
- Keep this cycle focused on generic auth shell rendering and auth screen dispatch; leave request routing, dashboard rendering, auth services, registration storage, provider verification, email delivery, webhook behavior, and WooCommerce endpoint delegation untouched.
- Defer final `0.1.16` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the gateway shell extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.15 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the frontend dashboard shell and dashboard content renderer out of the large frontend renderer class without changing dashboard copy, links, logout URL behavior, WooCommerce takeover warning, endpoint content delegation, external-link accessibility text, or dashboard classes.
- [x] Add focused test coverage around the extracted frontend dashboard screen service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.15` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.15` from clean `master` after the `v0.1.14` release merge.
- Extracted dashboard shell and dashboard content rendering into `ALYNT_AG_Frontend_Dashboard_Screen`.
- Kept `ALYNT_AG_Frontend` request flow, dashboard route detection, login-required dashboard redirect, logout handling, current-path calculation, and preview entry point intact while delegating dashboard shell markup, brand block rendering, dashboard hero output, dashboard links, WooCommerce unavailable warning, and WooCommerce endpoint content rendering to the new service.
- Added focused `FrontendDashboardScreenTest` coverage for dashboard shell output, brand/logout rendering, dashboard hero/user metadata, dashboard links including external-link accessibility text, WooCommerce unavailable warning, WooCommerce endpoint content rendering, and endpoint fallback copy.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendDashboardScreenTest` passes with 4 tests and 20 assertions; full `npm.cmd test` passes with 145 tests and 623 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.15-branch-qa-20260704-213541\alynt-account-gateway-v0.1.15-branch-qa.zip`; verified the new frontend dashboard screen service, runtime plugin files, built frontend/admin assets, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.14` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.14` header/constant, `ALYNT_AG_Frontend_Dashboard_Screen` file/class loaded, and dashboard shell rendering includes the dashboard shell, hero, manage-account links, logout link, WooCommerce content section, and no native login shell.
- HTTP-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.
- Removed the branch-QA zip from Plugin Tester uploads after smoke verification.
- Bumped release-candidate metadata to `0.1.15` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 145 tests and 623 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.15-20260704-214210\alynt-account-gateway-v0.1.15.zip`; verified built frontend/admin assets and the new frontend dashboard screen service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.15`.
- Published GitHub release `v0.1.15`, downloaded the public release asset, and verified the downloaded package has 53 archive entries including directories, 43 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend dashboard screen service, and `0.1.15` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.14` to `0.1.15`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.15` header/constant, `ALYNT_AG_Frontend_Dashboard_Screen` file/class loaded, dashboard shell rendering succeeds, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=invalidlink`, and `/my-account/`; public gateway routes rendered branded output with frontend JS assets and no native login shell, and logged-out dashboard access redirected to `/login?redirect_to=...`.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, login/lost-password/set-password behavior, email behavior, WooCommerce endpoint behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or dashboard link normalization behavior.
- Keep this cycle focused on dashboard rendering; leave dashboard data/link rules, WooCommerce endpoint routing, WooCommerce action delegation, auth flow, and admin settings untouched.
- Defer final `0.1.15` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the dashboard screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.14 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the set-password screen renderer and shared password form out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, token/key validation routing, password strength markup, password requirements, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend set-password screen service.
- [x] Run branch-QA package and Plugin Tester smoke checks before final release metadata bump.
- [x] Publish the final `v0.1.14` release asset and verify the Alynt Plugin Updater path end to end.

### Progress Notes

- Started `v0.1.14` from clean `master` after the `v0.1.13` release merge.
- Extracted set-password routing and shared password form rendering into `ALYNT_AG_Frontend_Setpassword_Screen`.
- Kept `ALYNT_AG_Frontend` request flow, gateway shell, and admin preview wrapper intact while delegating pending-registration token handling, native password-reset key handling, invalid-link fallback routing, lost-password fallback routing, password error display, password requirements, and password-strength markup to the new service.
- Added focused `FrontendSetpasswordScreenTest` coverage for default password form output, error accessibility state, pending-registration token form output, native reset-key form output, invalid registration-token fallback, and invalid native reset-key fallback.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendSetpasswordScreenTest` passes with 6 tests and 46 assertions; full `npm.cmd test` passes with 141 tests and 603 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.14-branch-qa-20260704-205911\alynt-account-gateway-v0.1.14-branch-qa.zip`; verified the new frontend set-password screen service, runtime plugin files, and WordPress-compatible archive paths are included, dev/source/test/docs/rules/package/vendor files are excluded, and the package header/constant report `0.1.13` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.13` header/constant, `ALYNT_AG_Frontend_Setpassword_Screen` file/class loaded, and the new service file present in the installed plugin copy.
- Browser-smoked the branch-QA installed Plugin Tester copy with system Chrome at `/account?action=setpassword&key=...&login=...`, `/account?action=setpassword`, and `/login`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved the set-password form, invalid-link fallback, and login control route. A 390px viewport pass confirmed no horizontal overflow, hidden media panel, and stable password form/card widths.
- Removed the temporary branch-QA reset user and uploaded branch-QA zip from Plugin Tester after smoke verification.
- Bumped release-candidate metadata to `0.1.14` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 141 tests and 603 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.14-20260704-210740\alynt-account-gateway-v0.1.14.zip`; verified built frontend/admin assets and the new frontend set-password screen service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.14`.
- Published GitHub release `v0.1.14`, downloaded the public release asset, and verified the downloaded package has 52 archive entries including directories, 42 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend set-password screen service, and `0.1.14` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.13` to `0.1.14`, then installed it through the WordPress upgrader path. Final server-side state: active `0.1.14` header/constant, `ALYNT_AG_Frontend_Setpassword_Screen` file/class loaded, and no remaining update offer.
- HTTP-smoked the release-installed Plugin Tester copy at `/account?action=setpassword&key=...&login=...`, `/account?action=setpassword`, and `/login`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend JS assets, and preserved expected set-password form, invalid-link fallback, and login control states. Temporary release-smoke users were removed after verification.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, login/lost-password behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, provider verification behavior, password policy, or password-strength UI behavior.
- Keep this cycle focused on set-password rendering; leave password reset request handling, pending-registration storage, email confirmation creation, Turnstile/Reoon validation, webhook behavior, and WooCommerce dashboard behavior untouched.
- Defer final `0.1.14` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the set-password screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.13 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the registration screen renderer out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, terms/privacy links, verification slot output, registration-success handling, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend registration screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.13` release.

### Progress Notes

- Started `v0.1.13` from clean `master` after the `v0.1.12` release merge.
- Extracted registration rendering into `ALYNT_AG_Frontend_Register_Screen`.
- Kept `ALYNT_AG_Frontend` request flow and wrapper method intact while delegating registration markup, read-only success/error display, terms/privacy links, and verification-slot rendering to the new service.
- Added focused `FrontendRegisterScreenTest` coverage for default form output, nonce field output, terms/privacy links, disabled submit state, registration-sent success state, registration error accessibility state, and Turnstile slot output.
- Verified `php -l` for the new service, test file, and frontend class; targeted `FrontendRegisterScreenTest` passes with 4 tests and 33 assertions; full `npm.cmd test` passes with 135 tests and 557 assertions; `npm.cmd run lint` passes; `npm.cmd run build` passes; `npm.cmd run make-pot` writes 344 strings; `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities; and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.13-branch-qa-20260704\alynt-account-gateway-v0.1.13-branch-qa.zip`; verified built frontend/admin assets, the new frontend registration screen service, and the previously extracted frontend login/lost-password/logout/state services are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.12` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.12` header/constant, `ALYNT_AG_Frontend_Register_Screen` file/class loaded, and registration rendering includes the default form, nonce, terms/privacy links, placeholder verification slot, registration-sent success state, registration error state, and Turnstile widget slot when a site key is configured.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/account?action=register`, `/account?action=register&registration_sent=1`, and `/account?action=register&registration_error=terms_required`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected registration default/success/error states. A 390px viewport pass confirmed the single-column layout, hidden media panel, no horizontal overflow, and stable field/button widths.
- Removed the branch-QA zip from Plugin Tester uploads.
- Bumped release-candidate metadata to `0.1.13` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 135 tests and 557 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.13-20260704\alynt-account-gateway-v0.1.13.zip`; verified built frontend/admin assets and the new frontend registration screen service are included, dev/source/test/docs/rules/package/vendor files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.13`.
- Published GitHub release `v0.1.13`, downloaded the public release asset, and verified the downloaded package has 51 archive entries including directories, 41 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package/vendor files, built frontend/admin CSS/JS assets, the new frontend registration screen service, and `0.1.13` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.12` to `0.1.13`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.13` header/constant, `ALYNT_AG_Frontend_Register_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/account?action=register`, `/account?action=register&registration_sent=1`, and `/account?action=register&registration_error=terms_required`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected registration default/success/error states.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration request handling, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, or provider verification behavior.
- Keep this cycle focused on registration screen rendering; leave pending-registration storage, email confirmation, set-password, Turnstile/Reoon validation, and resend-confirmation flows untouched.
- Defer final `0.1.13` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the registration screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.12 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the login screen renderer out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, redirect handling, routes, password toggle markup, status handling, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend login screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.12` release.

### Progress Notes

- Started `v0.1.12` from clean `master` after the `v0.1.11` release merge.
- Extracted login rendering into `ALYNT_AG_Frontend_Login_Screen`.
- Kept `ALYNT_AG_Frontend` request flow and wrapper method intact while delegating login markup and read-only status/error display to the new service.
- Added focused `FrontendLoginScreenTest` coverage for default form output, nonce field output, account links, password toggle markup, success states, redirect preservation, and login error accessibility state.
- Verified `php -l` for the new service and test file, targeted `FrontendLoginScreenTest` passes with 3 tests and 24 assertions, `npm.cmd run lint` passes, and `git diff --check` passes.
- Verified the full local gate: `npm.cmd test` passes with 131 tests and 524 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.12-branch-qa-20260704-193414\alynt-account-gateway-v0.1.12-branch-qa.zip`; verified built frontend/admin assets, the new frontend login screen service, the frontend lost-password screen service, and the frontend logout-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.11` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.11` header/constant, `ALYNT_AG_Frontend_Login_Screen` file/class loaded in a fresh request, and login rendering includes the title, form action, nonce, email/password fields, registration link, and lost-password link.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/login?registration_complete=1&password_reset=1&redirect_to=...`, `/login?login_error=alynt_ag_rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved login default/success/error states.
- Removed the branch-QA zip from Plugin Tester uploads.
- Bumped release-candidate metadata to `0.1.12` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 131 tests and 524 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.12-20260704-193759\alynt-account-gateway-v0.1.12.zip`; verified built frontend/admin assets and the new frontend login screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.12`.
- Published GitHub release `v0.1.12`, downloaded the public release asset, and verified the downloaded package has 50 archive entries including directories, 40 runtime file entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend/admin CSS/JS assets, the new frontend login screen service, and `0.1.12` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.11` to `0.1.12`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.12` header/constant, `ALYNT_AG_Frontend_Login_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/login?registration_complete=1&password_reset=1&redirect_to=...`, `/login?login_error=alynt_ag_rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected screen states.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, or auth-service messages.
- Keep this cycle focused on login screen rendering; leave login request handling and other auth screens untouched.
- Defer final `0.1.12` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the login screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.11 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the lost-password screen renderer out of the large frontend renderer class without changing copy, form fields, nonce/action names, query parameters, redirect behavior, routes, status handling, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend lost-password screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.11` release.

### Progress Notes

- Started `v0.1.11` from clean `master` after the `v0.1.10` release merge.
- Extracted lost-password rendering into `ALYNT_AG_Frontend_Lostpassword_Screen`.
- Kept `ALYNT_AG_Frontend` request flow and wrapper method intact while delegating lost-password markup and read-only status/error display to the new service.
- Added focused `FrontendLostpasswordScreenTest` coverage for default form output, nonce field output, request error state, forced invalid-token error state, reset-sent success state, and back-to-login behavior.
- Verified `php -l` for the new service and test file, targeted `FrontendLostpasswordScreenTest` passes with 4 tests and 22 assertions, `npm.cmd run lint` passes, and `git diff --check` passes.
- Verified the full local gate: `npm.cmd test` passes with 128 tests and 500 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.11-branch-qa-20260704-191248\alynt-account-gateway-v0.1.11-branch-qa.zip`; verified built frontend/admin assets, the new frontend lost-password screen service, the frontend logout-screen service, and the frontend state-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.10` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.10` header/constant, `ALYNT_AG_Frontend_Lostpassword_Screen` file/class loaded in a fresh request, and lost-password rendering includes the title, form action, nonce, email field, and back-to-login link.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=lostpassword&reset_error=alynt_ag_rate_limited`, `/account?action=lostpassword&reset_sent=1`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved lost-password default/error/success states.
- Removed the branch-QA zip from Plugin Tester uploads.
- Bumped release-candidate metadata to `0.1.11` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 128 tests and 500 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.11-20260704-191621\alynt-account-gateway-v0.1.11.zip`; verified built frontend/admin assets and the new frontend lost-password screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.11`.
- Published GitHub release `v0.1.11`, downloaded the public release asset, and verified the downloaded package has 49 archive entries including directories, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend/admin CSS/JS assets, the new frontend lost-password screen service, and `0.1.11` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.10` to `0.1.11`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.11` header/constant, `ALYNT_AG_Frontend_Lostpassword_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=lostpassword`, `/account?action=lostpassword&reset_error=alynt_ag_rate_limited`, `/account?action=lostpassword&reset_sent=1`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected screen states.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, form action names, or auth-service messages.
- Keep this cycle focused on lost-password screen rendering; leave password-reset request handling and set-password flows untouched.
- Defer final `0.1.11` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the lost-password screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.10 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the logout confirmation screen renderer out of the large frontend renderer class without changing copy, nonce/action names, query parameters, redirect behavior, routes, button classes, or notice behavior.
- [x] Add focused test coverage around the extracted frontend logout-screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.10` release.

### Progress Notes

- Started `v0.1.10` from clean `master` after the `v0.1.9` release merge.
- Extracted logout confirmation rendering into `ALYNT_AG_Frontend_Logout_Screen`.
- Kept `ALYNT_AG_Frontend` request handling and wrapper method intact while delegating logout confirmation markup to the new service.
- Added focused `FrontendLogoutScreenTest` coverage for the notice, nonce-protected logout URL, cancel URL, action button classes, and empty-notice suppression.
- Verified `php -l` for the new service and test file, targeted `FrontendLogoutScreenTest` passes with 2 tests and 11 assertions, `npm.cmd run lint` passes, and `git diff --check` passes.
- Verified the full local gate: `npm.cmd test` passes with 124 tests and 478 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.10-branch-qa-20260704-183635\alynt-account-gateway-v0.1.10-branch-qa.zip`; verified built frontend/admin assets, the new frontend logout-screen service, the existing frontend state-screen service, and the frontend component service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.9` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.9` header/constant, `ALYNT_AG_Frontend_Logout_Screen` file/class loaded in a fresh request, and logout confirmation rendering includes the title, notice, nonce-protected confirm URL, and cancel action.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=logout`, `/account?action=invalidlink`, `/account?action=lostpassword`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, and included frontend CSS/JS assets.
- Removed the branch-QA zip from Plugin Tester uploads and cleaned up the misplaced temporary upload folder created by the failed upload-link attempt.
- Bumped release-candidate metadata to `0.1.10` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 124 tests and 478 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.10-20260704-184128\alynt-account-gateway-v0.1.10.zip`; verified built frontend/admin assets and the new frontend logout-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.10`.
- Published GitHub release `v0.1.10`, downloaded the public release asset, and verified the downloaded package has 48 archive entries including directories, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend/admin CSS/JS assets, the new frontend logout-screen service, and `0.1.10` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.9` to `0.1.10`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.10` header/constant, `ALYNT_AG_Frontend_Logout_Screen` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=logout`, `/account?action=invalidlink`, `/account?action=lostpassword`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved expected screen titles.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, or form action names.
- Keep this cycle focused on logout confirmation rendering; leave confirmed logout request handling inside the main frontend controller.
- Defer final `0.1.10` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the logout-screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## Locked Decisions

- Plugin title: Alynt Account Gateway
- Plugin slug / text domain: `alynt-account-gateway`
- Development prefix: `alynt_ag_`
- Initial version: `0.1.0`
- GitHub Plugin URI: `NichlasB/alynt-account-gateway`
- Account action base default: `/account`
- Login URL default: `/login`
- Public account creation default: Disabled
- Pending registration token expiry: 24 hours
- Customer login method: Email only
- Emergency bypass: Yes, via generated secret query key on `wp-login.php`
- `wp-admin` access: Administrators and shop managers only
- Admin toolbar: Visible for administrators and shop managers only
- Gateway background image: One global image
- WooCommerce dashboard mode: Custom branded UI that delegates sensitive actions to WooCommerce
- Webhook event for v1: Account created
- Webhook payload: Full user fields
- Webhook logging default: Response metadata only, not full request payload bodies
- Turnstile and Reoon mode: Optional, with default protection setting of `Turnstile or Reoon`
- Email editor: Rich template editor with preview and test-send
- Terms/privacy links: Relative URL paths configured manually, such as `/terms/` or `/legal/privacy/`
- Multilingual support: Required for v1

## v0.1.9 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract the registration-disabled and invalid-link screen renderers out of the large frontend renderer class without changing copy, form fields, nonce/action names, query handling, routes, or accessibility attributes.
- [x] Add focused test coverage around the extracted frontend state-screen service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.9` release.

### Progress Notes

- Started `v0.1.9` from `master` after the `v0.1.8` release merge.
- Extracted registration-disabled and invalid-link screen rendering into `ALYNT_AG_Frontend_State_Screens`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating the extracted auth-state screens to the new service.
- Added focused `FrontendStateScreensTest` coverage for registration-disabled output, invalid-link resend form defaults, confirmation-resent success state, resend error state, nonce field output, and accessibility attributes.
- Added a PHPUnit bootstrap stub for `wp_nonce_field()` so extracted state-screen tests can verify nonce field names without loading WordPress admin helpers.
- Verified `php -l` for the new service and test file, `npm.cmd test` passes with 122 tests and 467 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.9-branch-qa-20260704-181637\alynt-account-gateway-v0.1.9-branch-qa.zip`; verified built frontend assets, the new frontend state-screen service, and the frontend component service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.8` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.8` header/constant, `ALYNT_AG_Frontend_State_Screens` file/class loaded, and registration-disabled rendering works in a fresh request.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=invalidlink`, `/account?action=invalidlink&confirmation_resent=1&resend_error=rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved invalid-link resend form/status behavior.
- Bumped release-candidate metadata to `0.1.9` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 122 tests and 467 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.9-20260704-182053\alynt-account-gateway-v0.1.9.zip`; verified built frontend assets and the new frontend state-screen service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.9`.
- Published GitHub release `v0.1.9`, downloaded the public release asset, and verified the downloaded package has 37 runtime entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend CSS/JS assets, the new frontend state-screen service, and `0.1.9` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.8` to `0.1.9`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.9` header/constant, `ALYNT_AG_Frontend_State_Screens` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=invalidlink`, `/account?action=invalidlink&confirmation_resent=1&resend_error=rate_limited`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and preserved invalid-link resend form/status behavior.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, frontend class names, nonce names, or form action names.
- Keep this cycle focused on the two simplest auth-state screens before extracting larger login/register/lost-password forms.
- Defer final `0.1.9` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the state-screen extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.8 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract shared frontend notice and verification-slot rendering out of the large frontend renderer class without changing markup, copy, accessibility attributes, Turnstile site-key output, or empty-copy behavior.
- [x] Add focused test coverage around the extracted frontend component service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.8` release.

### Progress Notes

- Started `v0.1.8` from `master` after the `v0.1.7` release merge.
- Extracted reusable notice rendering and registration verification-slot rendering into `ALYNT_AG_Frontend_Components`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating shared component rendering to the new service.
- Added focused `FrontendComponentsTest` coverage for empty notice suppression, paragraph formatting, default verification placeholder output, and Turnstile widget output with accessible label and configured site key.
- Added PHPUnit bootstrap stubs for `esc_html_e()` and `esc_attr_e()` so extracted component tests can exercise WordPress-style echo escaping.
- Verified `php -l` for the new service and test file, `npm.cmd test` passes with 119 tests and 447 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.8-branch-qa-20260704-175610\alynt-account-gateway-v0.1.8-branch-qa.zip`; verified built frontend assets, the new frontend components service, and the frontend branding service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.7` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.7` header/constant, `ALYNT_AG_Frontend_Components` file/class loaded, and verification placeholder rendering works in a fresh request.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, preserved form notice output, and preserved the registration verification slot.
- Bumped release-candidate metadata to `0.1.8` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 119 tests and 447 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.8-20260704-175944\alynt-account-gateway-v0.1.8.zip`; verified built frontend assets and the new frontend components service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.8`.
- Published GitHub release `v0.1.8`, downloaded the public release asset, and verified the downloaded package has 36 runtime entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend CSS/JS assets, the new frontend components service, and `0.1.8` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.7` to `0.1.8`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.8` header/constant, `ALYNT_AG_Frontend_Components` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, preserved form notice output, and preserved the registration verification slot.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, design-token names, or frontend class names.
- Keep this cycle focused on shared frontend form components before attempting a larger screen-renderer split.
- Defer final `0.1.8` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the shared-component extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.7 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Preserve the existing `AI_CODING_RULES.md` housekeeping rename as a separate checkpoint.
- [x] Extract frontend branding/media/style rendering out of the large frontend renderer class without changing markup, design tokens, image handling, logo sizing, or fallback store-name behavior.
- [x] Add focused test coverage around the extracted frontend branding service.
- [x] Run build, lint, test, audit, POT, package, and Plugin Tester smoke checks as appropriate for the final `0.1.7` release.

### Progress Notes

- Started `v0.1.7` from `master` after the `v0.1.6` release merge.
- Preserved the already-present `.windsurfrules` to `AI_CODING_RULES.md` housekeeping rename in commit `c1b0b63` before beginning the code slice.
- Extracted inline design-token style generation, left media-panel rendering, and logo/store-name rendering into `ALYNT_AG_Frontend_Branding`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating branding/media/style decisions to the new service.
- Added focused `FrontendBrandingTest` coverage for configured design tokens, empty-value skipping, media pattern fallback, configured background image output, store-name fallback, logo URL output, and logo max-width clamping.
- Verified `php -l` for the new service and test file, `npm.cmd test` passes with 115 tests and 435 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.7-branch-qa-20260704-173636\alynt-account-gateway-v0.1.7-branch-qa.zip`; verified built frontend assets, the new frontend branding service, and the frontend asset service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.6` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.6` header/constant, `ALYNT_AG_Frontend_Branding` file/class loaded, and style-token generation works from `ALYNT_AG_Settings_Schema::defaults()`.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and included the brand block plus design-token style output.
- Bumped release-candidate metadata to `0.1.7` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 115 tests and 435 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.7-20260704-174141\alynt-account-gateway-v0.1.7.zip`; verified built frontend assets and the new frontend branding service are included, dev/source/test/docs/rules/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.7`.
- Published GitHub release `v0.1.7`, downloaded the public release asset, and verified the downloaded package has 35 runtime entries, no backslash archive entries, no dev/source/test/docs/rules/package files, built frontend CSS/JS assets, the new frontend branding service, and `0.1.7` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.6` to `0.1.7`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.7` header/constant, `ALYNT_AG_Frontend_Branding` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, included frontend CSS/JS assets, and included the brand block plus design-token style output.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, asset handles/URLs, or design-token names.
- Keep this cycle focused on one structural extraction from the frontend renderer.
- Defer final `0.1.7` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the branding/media/style extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.6 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract frontend asset enqueue logic out of the large frontend renderer class without changing when assets load.
- [x] Add focused test coverage around the extracted frontend asset service.
- [x] Run installed Plugin Tester smoke checks for representative gateway routes after packaging.
- [x] Re-run package/update checks as appropriate for the final `0.1.6` release.

### Progress Notes

- Started `v0.1.6` from `master` after the `v0.1.5` release merge.
- Extracted frontend stylesheet/script enqueueing, localized password-toggle labels, and Turnstile script enqueueing into `ALYNT_AG_Frontend_Assets`.
- Kept `ALYNT_AG_Frontend::enqueue_assets()` as the public hook target while delegating asset decisions to the new service.
- Added focused `FrontendAssetsTest` coverage for frontend-output/screen gating, frontend CSS/JS enqueueing, localized labels, and Turnstile loading only on configured registration screens.
- Added lightweight PHPUnit bootstrap stubs to record enqueued styles, scripts, and localized script data.
- Verified `npm.cmd test` passes with 110 tests and 420 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.6-branch-qa-20260704-165504\alynt-account-gateway-v0.1.6-branch-qa.zip`; verified built frontend assets and the frontend asset, route, and message services are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.5` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.5` header/constant, `ALYNT_AG_Frontend_Assets` file/class loaded, frontend style/script queue on gateway screens, and Turnstile script queues on a configured registration screen.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, and included the frontend CSS/JS assets.
- Bumped release-candidate metadata to `0.1.6` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test` passes with 110 tests and 420 assertions, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.6-20260704-171356\alynt-account-gateway-v0.1.6.zip`; verified built frontend assets and the frontend asset, route, and message services are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.6`.
- Published GitHub release `v0.1.6`, downloaded the public release asset, and verified the downloaded package has 34 runtime entries, no backslash archive entries, no dev/source/test/docs/package files, built frontend CSS/JS assets, the extracted frontend asset service, and `0.1.6` header/constant metadata.
- Verified Alynt Plugin Updater on LocalWP Plugin Tester detected the public GitHub release asset as an update from installed `0.1.5` to `0.1.6`, then installed it through the WordPress Plugins screen update path. Final server-side state: active `0.1.6` header/constant, `ALYNT_AG_Frontend_Assets` file/class loaded, and no remaining update offer.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens, avoided the native WordPress login shell, and included the frontend CSS/JS assets.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, WooCommerce dashboard behavior, or asset handles/URLs.
- Keep this cycle focused on one structural extraction from the frontend renderer.
- Defer final `0.1.6` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the asset-service extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.5 Small Release Cycle

### Scope

- [x] Start the next low-risk structural slice from the released `master` baseline.
- [x] Extract frontend URL and screen-routing helpers out of the large frontend renderer class without changing public routes or query handling.
- [x] Add focused test coverage around the extracted frontend route service.
- [x] Run installed Plugin Tester smoke checks for representative gateway routes after packaging.
- [x] Re-run package/update checks as appropriate for the final `0.1.5` release.

### Progress Notes

- Started `v0.1.5` from `master` after the `v0.1.4` release merge.
- Extracted branded action URL construction, login/lost-password/register/logout URL helpers, current relative path handling, path matching, and gateway screen resolution into `ALYNT_AG_Frontend_Routes`.
- Kept `ALYNT_AG_Frontend` wrapper methods for internal compatibility while delegating route decisions to the new service.
- Added focused `FrontendRoutesTest` coverage for known and fallback action URLs, redirect/nonce query preservation, enabled and disabled registration screen routing, dashboard and non-gateway path routing, WooCommerce takeover endpoint routing, and trailing-slash-insensitive path matching.
- Verified `npm.cmd test` passes with 107 tests and 402 assertions, `npm.cmd run lint` passes, `npm.cmd run build` passes, `npm.cmd run make-pot` writes 344 strings, `npm.cmd audit --audit-level=moderate` reports 0 vulnerabilities, and `git diff --check` passes.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.5-branch-qa-20260704-162630\alynt-account-gateway-v0.1.5-branch-qa.zip`; verified built frontend assets, the message catalog, and the route service are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.4` as expected before the final release bump.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes. Verified final installed state: active `0.1.4` header/constant, `ALYNT_AG_Frontend_Routes` file/class loaded, and route helpers resolve register/lost-password URLs and the register screen.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.
- Bumped release-candidate metadata to `0.1.5` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.5-20260704-163228\alynt-account-gateway-v0.1.5.zip`; verified built assets, the message catalog, and the route service are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.5`.
- Published GitHub release `v0.1.5`, downloaded `alynt-account-gateway-v0.1.5.zip` from the release, and verified the public asset reports `0.1.5`, includes built assets, the message catalog, and the route service, and excludes development/source files.
- Confirmed Alynt Plugin Updater detected `0.1.4` to `0.1.5`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.5` GitHub release asset, and verified final Plugin Tester state: active `0.1.5`, no remaining update.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.

### Guardrails

- Do not change rendered gateway copy, routes, query parameters, redirect behavior, registration behavior, email behavior, or WooCommerce dashboard behavior.
- Keep this cycle focused on one structural extraction from the frontend renderer.
- Defer final `0.1.5` metadata bump, release asset publication, and Alynt Plugin Updater verification until branch QA is complete.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the route-service extraction.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.4 Small Release Cycle

### Scope

- [x] Reconcile stale implementation-plan checklist items that were completed during `v0.1.2` email QA and release verification.
- [x] Start a low-risk structural refactor by extracting frontend message/catalog lookup out of the large frontend renderer class without changing public copy or behavior.
- [x] Add or preserve focused test coverage around the extracted message catalog.
- [x] Re-run build, lint, tests, POT, and package/update checks as appropriate for the final `0.1.4` release.

### Progress Notes

- Started `v0.1.4` from `master` after the `v0.1.3` release merge. Reconciled stale plan checkboxes: profile email-change request suppression and email preview/test-send QA were completed during the `v0.1.2` cycle and release notes.
- Extracted frontend gateway title and error-message lookup into `ALYNT_AG_Frontend_Messages`, keeping the existing `ALYNT_AG_Frontend::get_screen_title()` public wrapper for admin preview compatibility and preserving rendered copy/fallback behavior.
- Added focused `FrontendMessagesTest` coverage for screen-title, registration-error, resend-error, and password-error mappings and fallback messages.
- Created local branch-QA package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.4-branch-qa-20260704-155405\alynt-account-gateway-v0.1.4-branch-qa-wp.zip`; verified built frontend assets and the message catalog file are included, dev/source/test/docs/package files are excluded, and archive entries use WordPress-compatible forward-slash paths.
- Installed the branch-QA package on LocalWP Plugin Tester through WordPress upgrader classes after the browser upload path returned a blank update response and WP-CLI was unavailable. Verified final installed state: active `0.1.3` header/constant, `ALYNT_AG_Frontend_Messages` file/class loaded, and `register` title resolves to `Create Account`.
- Browser-smoked the branch-QA installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.
- Bumped release-candidate metadata to `0.1.4` across the plugin header/constant, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.4-20260704-160614\alynt-account-gateway-v0.1.4.zip`; verified built assets and the message catalog are included, dev/source/test/docs/package files are excluded, archive entries use WordPress-compatible forward-slash paths, and the package header/constant report `0.1.4`.
- Published GitHub release `v0.1.4`, downloaded `alynt-account-gateway-v0.1.4.zip` from the release, and verified the public asset reports `0.1.4`, includes built assets and the message catalog, and excludes development/source files.
- Confirmed Alynt Plugin Updater detected `0.1.3` to `0.1.4`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.4` GitHub release asset, and verified final Plugin Tester state: active `0.1.4`, no remaining update.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.

### Guardrails

- Do not change rendered gateway copy, routes, form behavior, email behavior, registration behavior, or WooCommerce dashboard behavior.
- Keep this cycle focused on documentation reconciliation and one small structure improvement.
- Defer broad class splitting until the extracted seams have tests and release evidence.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Plugin Tester smoke validates representative gateway routes after the refactor.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.3 Small Release Cycle

### Scope

- [x] Re-audit registration flow behavior against the approved confirmation-first, email-only, terms-consent, password-policy, and spam-prevention requirements.
- [x] Add or refine focused unit coverage around registration validation, pending-registration state, account-created webhook payload/logging, or protection-mode decisions where practical.
- [x] Browser/manual QA the installed Plugin Tester registration path, including disabled registration, enabled registration form gating, pending confirmation, set-password completion, and email-only login after account creation.
- [x] Verify account-created webhook behavior in a local-safe way without sending real customer data to an external service.
- [x] Refresh docs/changelog/POT if implementation changes registration, webhook, or user-facing behavior.
- [x] Re-run Plugin Tester smoke checks and verify Alynt Plugin Updater detects and installs `0.1.3` from the GitHub release asset.

### Progress Notes

- Registration service audit confirmed the existing flow keeps account creation behind email confirmation and password validation.
- Added focused PHPUnit coverage for the confirmed pending-registration completion path: WordPress user creation, profile update, pending-row `account_created` status, consent attachment, welcome email hook, and account-created webhook hook.
- Plugin Tester QA covered disabled registration, responsive registration layout, required-field and terms gating, simulated confirmation email delivery, set-password strength/match gating, account creation, email-only login, branded dashboard redirect, no customer admin toolbar, and customer `wp-admin` redirect.
- Account-created webhook QA used a local `pre_http_request` intercept against `http://127.0.0.1/alynt-local-webhook-capture`; no external request was sent, the payload contained full user/site fields, and a successful `202` webhook log row was written.
- No runtime code or user-facing string changes were needed during the Plugin Tester QA pass, so POT generation was not required for this checkpoint.
- Bumped release-candidate metadata to `0.1.3` across the plugin header/constant, database schema version, npm metadata, readme, changelog, sample test, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.3-20260704-153148\alynt-account-gateway-v0.1.3.zip`; verified built assets are included, dev/source/test/docs/package files are excluded, and the package header/constant report `0.1.3`.
- Published GitHub release `v0.1.3`, confirmed the Build Release workflow completed successfully, downloaded `alynt-account-gateway-v0.1.3.zip`, and verified the package reports `0.1.3` while excluding development/source files.
- Confirmed Alynt Plugin Updater detected `0.1.2` to `0.1.3`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.3` GitHub release asset, and verified final Plugin Tester state: active `0.1.3`, no remaining update.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, and `/my-account/`; all selected routes rendered branded gateway screens and avoided the native WordPress login shell.

### Guardrails

- Keep the cycle small and registration-focused; do not redesign the gateway screens.
- Keep public account creation disabled by default.
- Do not weaken the confirmation-first account creation contract.
- Do not send real webhook payloads to third-party services during QA.
- Preserve email-only login and generated-username behavior.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Local release-style zip excludes source/dev files and includes built assets.
- [x] Plugin Tester validates the selected registration and webhook behavior.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.2 Small Release Cycle

### Scope

- [x] Browser/manual QA email preview and test-send on LocalWP Plugin Tester.
- [x] Add or refine focused test coverage around email preview/test-send handlers where practical.
- [x] Evaluate and, if safe, implement the remaining profile email-change request suppression strategy for the existing disable toggle.
- [x] Refresh docs/changelog/POT if implementation changes account email behavior or user-facing strings.
- [x] Re-run Plugin Tester smoke checks for email tools and a light account-gateway regression pass.
- [x] Verify Alynt Plugin Updater detects and installs `0.1.2` from the GitHub release asset.

### Guardrails

- Keep the cycle small; do not rework the whole email editor.
- Keep frontend output disabled by default on fresh install.
- Preserve the existing branded email templates, tokens, and preview/test-send UI unless a QA finding requires a narrow fix.
- Do not suppress WordPress core security/account emails unless the behavior can be verified safely and documented clearly.
- Keep release packaging exclusions aligned with the existing GitHub release workflow.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Local release-style zip excludes source/dev files and includes built assets.
- [x] Plugin Tester validates email preview/test-send and the selected email-change behavior.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## v0.1.1 Small Release Cycle

### Scope

- [x] Add settings import/export JSON for all plugin-owned settings.
- [x] Add per-tab restore defaults with confirmation and diagnostics logging.
- [x] Add gateway screen preview mode while frontend output is disabled.
- [x] Add compatibility warnings for plugins that commonly modify login, registration, account pages, security redirects, or WooCommerce account endpoints.
- [x] Add focused unit coverage for settings schema defaults/sanitization, frontend-output routing, emergency bypass behavior, role access/admin toolbar rules, password policy matching, retention cleanup, and uninstall cleanup where practical.
- [x] Re-run Plugin Tester smoke checks after the release package is built.
- [x] Verify Alynt Plugin Updater detects and installs `0.1.1` from the GitHub release asset.

### Guardrails

- Keep frontend output disabled by default on fresh install.
- Do not change the public registration confirmation-first contract.
- Do not rework the dashboard architecture unless a small compatibility warning requires a narrow hook change.
- Prefer incremental tests and admin polish over broad refactors.
- Leave large-file refactors for a later structural release unless a v0.1.1 task directly forces a split.

### Completion Gate

- [x] Build, lint, test, audit, and POT generation pass.
- [x] Local release-style zip excludes source/dev files and includes built assets.
- [x] Plugin Tester validates admin settings actions, preview mode, gateway routes, and WooCommerce dashboard takeover.
- [x] GitHub release asset is installed through Alynt Plugin Updater.

## Implementation Phases

### Phase 1 - Scaffold And Baseline Tooling

- [x] Scaffold plugin in the existing empty target folder.
- [x] Initialize Git repository.
- [x] Add `AI_CODING_RULES.md`.
- [x] Add main plugin file with plugin header, version constant, text domain, and GitHub Plugin URI.
- [x] Add loader, activator, deactivator, i18n, and core plugin bootstrap classes.
- [x] Add Composer tooling for PHPCS/WPCS and PHPUnit/Brain Monkey.
- [x] Add npm/esbuild tooling for admin and frontend assets.
- [x] Add GitHub Actions release workflow for Alynt Plugin Updater compatibility.
- [x] Add `README.md`, `readme.txt`, `CHANGELOG.md`, `docs/SETTINGS.md`, `docs/HOOKS.md`, and `uninstall.php`.

### Phase 2 - Settings, Admin UI, And Observability

- [x] Add tabbed admin settings page.
- [x] Add central settings schema with defaults, sanitization, and cross-tab save protection.
- [x] Add tabs: General, URLs & Redirects, Branding & Layout, Screen Copy, Registration, Security & Spam, Emails, Dashboard, WooCommerce, Webhooks, Privacy & Data, Advanced/Tools.
- [x] Add import/export settings JSON.
- [x] Add per-tab restore defaults.
- [x] Add gateway screen preview mode while frontend output is disabled.
- [x] Add diagnostics and privacy-conscious logs.
- [x] Add settings-change audit entries.
- [x] Add retention cleanup for plugin-owned logs and pending records.

### Phase 3 - Account Routing And Gateway Screens

- [x] Implement frontend-output master switch.
- [x] Route `/login` to the branded login screen.
- [x] Route `/account?action=lostpassword`, `/account?action=register`, `/account?action=rp`, and `/account?action=logout` to branded screens.
- [x] Replace public `wp-login.php` links with branded routes when frontend output is enabled.
- [x] Add emergency bypass URL that opens native `wp-login.php` without authenticating or bypassing 2FA/security plugins.
- [x] Block `wp-admin` for roles other than administrators and shop managers.
- [x] Remove admin toolbar for roles other than administrators and shop managers.
- [x] Build responsive split-screen gateway template with one global background image.
- [x] Implement frontend templates from `docs/DESIGN_HANDOFF.md`.
- [x] Add logo upload and max-width control.
- [x] Add color controls for primary color, accent color, text, page background, surface, error, button background, and button text.
- [x] Add font stack controls for heading and body typography.
- [x] Add per-screen instruction/welcome text.
- [x] Add disabled-registration and invalid/expired-link branded states.

### Phase 4 - Registration, Passwords, And Spam Protection

- [x] Implement pending registration records.
- [x] Send registration confirmation email before creating a WordPress user.
- [x] Create WordPress user only after confirmation link and valid password setup.
- [x] Store first name, last name, email, generated username, and chosen password during final account creation only.
- [x] Add configurable username format with unique generated username collision handling.
- [x] Add password strength meter and matching validation.
- [x] Enforce minimum 12 characters, uppercase, lowercase, number, and special symbol.
- [x] Add terms/privacy agreement checkbox with relative URL path links.
- [x] Add Turnstile client and required server-side validation when enabled.
- [x] Add Reoon Email Verifier client and policy mapping.
- [x] Default Reoon policy: block invalid, disabled, disposable, and spamtrap; allow but flag catch-all, role-account, unknown, and inbox-full.
- [x] Add rate limits for registration, resend confirmation, login, and password reset flows.
- [x] Add neutral resend-confirmation handling for invalid/expired registration links.
- [x] Avoid account enumeration in public registration and resend-confirmation outcomes.
- [x] Avoid account enumeration in login and password-reset request messages.
- [x] Add branded WordPress password-reset key validation and password update flow for native reset links.

### Phase 5 - Emails And Webhooks

- [x] Add rich email template editor foundation.
- [x] Add template preview and test-send.
- [x] Add branded HTML wrapper, logo, colors, buttons, and plain-text fallback.
- [x] Add templates for password reset, password changed, registration confirmation/welcome, and email-change confirmation.
- [x] Wire disable toggles for password changed and email-change notification emails.
- [x] Wire branded overrides for native password reset, password changed, and email-change notification emails.
- [x] Wire the WordPress profile email-change request body through `new_user_email_content` as a branded plain-text template.
- [x] Add account-created welcome email and disable-toggle behavior.
- [x] Evaluate a safe replacement strategy if the disable toggle must suppress the profile email-change request email itself.
- [x] Add account-created webhook dispatcher.
- [x] Send full user fields in the account-created webhook payload.
- [x] Store webhook response metadata by default.
- [x] Store full payload bodies only when debug payload logging is enabled.
- [x] Retain successful webhook metadata for 7 days and failed webhook metadata for 30 days by default.

### Phase 6 - Dashboard And WooCommerce

- [x] Add optional custom full-page account dashboard.
- [x] Add custom dashboard links with icons, ordering, role visibility, and open-in-new-tab.
- [x] Detect WooCommerce availability.
- [x] Allow custom dashboard to take over WooCommerce My Account when enabled.
- [x] Delegate sensitive WooCommerce actions to native WooCommerce handlers/endpoints.
- [x] Preserve orders, downloads, addresses, payment methods, account details, and logout through standard WooCommerce endpoints.
- [x] Discover and preserve plugin-added WooCommerce account endpoints.
- [x] Add compatibility warnings for plugins that also modify login, registration, account pages, security redirects, or WooCommerce account endpoints.

### Phase 7 - Privacy, Accessibility, I18n, And Release Readiness

- [x] Add WordPress privacy policy text.
- [x] Add personal data exporter and eraser support.
- [x] Add retention settings for verification logs, webhook logs, consent records, and audit entries.
- [x] Store consent record with terms/privacy URLs, timestamp, and policy/version context.
- [x] Avoid storing IP by default unless explicitly enabled.
- [x] Ensure visible labels, keyboard operation, focus states, inline validation, `aria-invalid`, and live-region messages.
- [x] Add responsive CSS guardrails down to 320px.
- [x] Ensure frontend account-gateway strings are translatable and localize frontend JS labels through WordPress.
- [x] Generate POT file.
- [x] Ensure RTL-safe CSS for frontend gateway surfaces.
- [x] Run pre-release workflow sequence through cleanup, structure, error handling, WP practices, database, performance, edge cases, uninstall, i18n, accessibility, code quality, documentation, and security review.

## Test Plan

- [x] Unit test settings schema, defaults, sanitization, and cross-tab save protection.
- [x] Unit test URL routing and frontend-output master switch.
- [x] Unit test emergency bypass behavior.
- [x] Unit test role access and admin-toolbar rules.
- [x] Unit test email-only login behavior.
- [x] Unit test pending-registration lifecycle and expiry.
- [x] Unit test password policy and confirmation matching.
- [x] Unit test username generation and collision handling.
- [x] Unit test Reoon policy mapping.
- [x] Unit test Turnstile verification handling.
- [x] Unit test webhook payload construction and metadata logging.
- [x] Unit test retention cleanup.
- [x] Unit test uninstall cleanup.
- [x] Browser/manual QA login, lost password, set password, registration, logout confirmation, disabled registration, and invalid/expired link screens.
- [x] Browser/manual QA desktop and mobile responsive behavior.
- [x] Browser/manual QA keyboard-only flow and focus management.
- [x] Browser/manual QA email preview and test-send.
- [x] Browser/manual QA WooCommerce dashboard delegation.
- [x] Verify `npm run build`.
- [x] Verify `npm run lint`.
- [x] Verify `npm test`.
- [x] Verify `npm run make-pot`.
- [x] Verify PHP syntax across runtime and test PHP files.
- [x] Verify `npm audit --audit-level=high`.
- [x] Verify `composer audit`.
- [x] Verify release-style zip locally with GitHub workflow exclusions.
- [x] Verify generated release zip through GitHub release workflow.
- [x] Verify install/update from the GitHub release asset through Alynt Plugin Updater on LocalWP Plugin Tester.

## Release Gates

- [x] Frontend output remains disabled by default on fresh install.
- [x] Emergency bypass opens native login only and never authenticates users.
- [x] No standard WordPress core account screen is exposed during normal enabled frontend use.
- [x] Registration creates no WordPress user until email confirmation and password setup are complete.
- [x] WooCommerce account features remain usable when the custom dashboard is enabled.
- [x] Accessibility acceptance criteria pass for implemented gateway/dashboard surfaces.
- [x] Multilingual/i18n acceptance criteria pass for implemented strings and generated POT.
- [x] Privacy exporter/eraser and retention controls are present.
- [x] Alynt Plugin Updater compatibility is verified end to end by updating the LocalWP Plugin Tester install from a GitHub release asset.

## Pre-Release Audit Notes

### 2026-07-04

- Completed pre-release review sequence `01` through `13` from the wp-plugin-toolkit.
- Fixed release hygiene issue where `AI_CODING_RULES.md` would have been included in the GitHub release zip.
- Fixed admin media preview DOM handling by replacing `innerHTML` with explicit image node creation.
- Fixed uninstall cleanup coverage for the scheduled retention hook and transient-backed rate-limit buckets.
- Added HTTPS enforcement for public account-created webhook URLs while allowing local development hosts (`localhost`, `127.0.0.1`, `::1`, and `.local`).
- Updated README, `readme.txt`, changelog, settings docs, and hooks docs to reflect the implemented feature set instead of scaffold status.
- Regenerated `languages/alynt-account-gateway.pot` after the new webhook security string.
- Verified local release-style zip excludes source assets, dev dependencies, tests, docs, scripts, GitHub metadata, maps, Composer/npm files, and editor rules.
- Structural debt remains: `public/class-frontend.php`, `includes/services/class-registration-service.php`, and several admin/settings/template classes are larger than ideal. They are intentionally left intact for this release pass because splitting them now would be a high-blast-radius refactor after browser QA.
- Published GitHub release `v0.1.0`, confirmed the Build Release workflow completed successfully, downloaded `alynt-account-gateway-v0.1.0.zip`, and verified the package contains the plugin runtime files and built assets while excluding development files.
- Made the GitHub repository public for release delivery, forced the LocalWP Plugin Tester installed copy to `0.0.9`, confirmed Alynt Plugin Updater detected `0.0.9` to `0.1.0`, installed from the `alynt-account-gateway-v0.1.0.zip` release asset, and verified the active Plugin Tester copy returned to `0.1.0`.
- Browser-smoked the release-installed Plugin Tester copy at `/login`, `/account?action=register`, `/account?action=lostpassword`, and `/my-account/` after the updater install.
- Remaining release decisions: optionally add uninstall-specific unit coverage before the next release.
- Installed the local `alynt-account-gateway-v0.1.1.zip` package on LocalWP Plugin Tester, verified the active plugin reports `0.1.1`, browser-smoked Advanced / Tools compatibility warnings, gateway preview mode, `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, `/my-account/`, and WooCommerce account endpoints for orders, downloads, addresses, payment methods, and account details.
- Published GitHub release `v0.1.1`, confirmed the Build Release workflow completed successfully, downloaded and inspected `alynt-account-gateway-v0.1.1.zip`, verified the package reports `0.1.1` and excludes development/source files, then confirmed Alynt Plugin Updater detected `0.1.0` to `0.1.1` and the WordPress Plugins screen `update now` action installed the GitHub release asset. Final Plugin Tester state: active `0.1.1`, no remaining update, settings intact.
- Started v0.1.2 email QA on LocalWP Plugin Tester. Verified the Emails tab exposes all five templates, preview renders successful branded HTML for registration confirmation, password reset, password changed, account-created welcome, and email-change confirmation, and the test-send form redirects with `email_test_sent`. SureMails was temporarily switched to simulation mode for the send test, logged `Reset your password for Plugin Tester` to `alynt-ag-v012-qa@example.test` as simulated, and was restored to its prior non-simulation setting.
- Added focused email tooling coverage for preview-token rendering across every supported template and test-send rejection paths for invalid recipients and unknown templates. Verified `npm.cmd test` passes with 94 tests and 354 assertions.
- Implemented the remaining profile email-change request suppression for the existing email-change disable toggle. WordPress core sends this pending request through a direct `wp_mail()` call after `new_user_email_content`, so the plugin now marks that exact request when disabled, short-circuits it through `pre_wp_mail`, and removes the pending `_new_email` marker to avoid an impossible confirmation state. Verified `npm.cmd test` passes with 96 tests and 359 assertions, and `npm.cmd run lint` passes.
- Refreshed docs, changelog, and `languages/alynt-account-gateway.pot` after the email-change behavior update. No new translatable strings were added; POT changes were source-reference/date metadata.
- Bumped release candidate metadata to `0.1.2` across the plugin header/constant, npm metadata, readme, sample test, changelog, and POT. Verified `npm.cmd run build`, `npm.cmd run lint`, `npm.cmd test`, `npm.cmd run make-pot`, `npm.cmd audit --audit-level=moderate`, and `git diff --check`.
- Created local release-style package `C:\Users\Captain\Documents\AI Workflows\work\acg-v0.1.2-20260704-143449\alynt-account-gateway-v0.1.2.zip`; verified built assets are included, dev/source/test/docs/package files are excluded, and the package header/constant report `0.1.2`.
- Installed the local `0.1.2` package on LocalWP Plugin Tester, verified active header and loaded constant are `0.1.2`, browser-smoked `/login`, `/account?action=register`, `/account?action=lostpassword`, `/account?action=logout`, `/my-account/`, `/my-account/orders/`, and `/my-account/edit-account/`, and verified no native WordPress login shell appears on gateway routes.
- Re-ran installed-copy email QA on Plugin Tester: all five admin previews returned branded HTML, test-send logged `Confirm your email address for Plugin Tester` to `alynt-ag-v012-smoke@example.test` as simulated through SureMails, SureMails simulation was restored to `no`, and the email-change suppression path returned `false` through `pre_wp_mail` while clearing `_new_email` and restoring the original setting.
- Published GitHub release `v0.1.2`, confirmed the Build Release workflow completed successfully, and verified the attached `alynt-account-gateway-v0.1.2.zip` release asset. Downgraded LocalWP Plugin Tester to the public `v0.1.1` release asset, confirmed Alynt Plugin Updater detected `0.1.1` to `0.1.2`, used the WordPress Plugins screen `update now` path to download and install from the `v0.1.2` GitHub release asset, and verified final Plugin Tester state: active `0.1.2`, no remaining update.

## Workflow Notes

- Use `C:\Users\Captain\Documents\AI Workflows\Toolkits\wp-plugin-toolkit\START_HERE_MASTER_WORKFLOW.md` as the router for plugin work.
- Scaffold/observability checkpoint commit: `c0daf48` (`Scaffold account gateway foundation`).
- Design workflow Phase 1 has been completed using the supplied login/register/lost-password screenshots as visual references.
- Design export received and distilled into `docs/DESIGN_HANDOFF.md`; use it as the implementation source for frontend gateway templates.
- Next toolkit step before scaffold: use `d1-setup/ai-plugin-setup-reference.md` Section 2 to create the scaffold master prompt.
- After scaffold, route to `@ADD_OBSERVABILITY_TOOLING_PROMPT.md run` before heavy feature work.
- After each major feature, run the feature review sequence: light review, bloat/structure review, UI/UX review, and security review.
- Before release, run pre-release prompts `@01` through `@13` in filename order, keeping security last.
- Do not update `PRE_RELEASE_CHECKLIST.md` unless a supported toolkit workflow completes successfully.

## Scaffold Prompt

- [x] Created `docs/SCAFFOLD_MASTER_PROMPT.md` from the approved product plan and toolkit scaffold guidance.

## Change Log

### 2026-07-03

- Committed scaffold and observability foundation as checkpoint `c0daf48`.
- Added design workflow gate before account routing and gateway screen implementation.
- Captured the Claude design export as a durable implementation handoff in `docs/DESIGN_HANDOFF.md`.
- Clarified that the design palette and fonts are default starter values only; production output is brand-agnostic and settings-driven.
- Implemented the first Phase 3 frontend foundation: branded route detection, native login redirect with emergency bypass, URL filters, settings-backed design tokens, responsive gateway shell, screen templates, logout confirmation handling, and password visibility toggle.
- Added WordPress media-library controls for the brand logo and gateway background image, plus configurable instruction text for each gateway screen.
- Added the Phase 4 pending-registration foundation: frontend registration submission handling, hashed confirmation tokens, 24-hour configurable expiry, confirmation email delivery, email-confirmed pending state, and token helper tests.
- Added final pending-registration account creation: set-password POST handling, password confirmation/policy validation, generated username creation, WordPress user creation, profile name persistence, pending record consumption, and DB schema upgrade check.
- Added client-side password policy UX for set-password: live strength bars, translated status text, requirement states, password-match feedback, and disabled submit until the configured v1 policy is satisfied.
- Added server-side terms/privacy acceptance validation for registration submissions, matching the required frontend checkbox and relative path links.
- Added Turnstile/Reoon registration protection: Turnstile widget rendering, server-side Siteverify validation, Reoon single-email verification, default OR policy when both providers are configured, and provider interpretation tests.
- Added transient-backed rate limiting for registration, resend confirmation, login, and password reset buckets with privacy-preserving hashed keys.
- Added expired-link recovery: invalid/expired registration links can request a new confirmation email, pending tokens are renewed without creating users, resend attempts use their own rate bucket, and public responses stay neutral when no pending registration exists.
- Added branded auth POST handling for login and password-reset requests so failed submissions return to gateway screens with neutral public messages instead of native WordPress screens.
- Added branded native password-reset completion: WordPress reset links with `key` and `login` now render the gateway set-password screen, validate through WordPress reset-key APIs, enforce the v1 password policy, and redirect to branded login after success.
- Added Phase 5 email foundation: editable template settings for account emails, branded HTML/plain rendering, preview and test-send admin tools, and registration confirmation emails routed through the renderer.
- Added native email overrides for WordPress password reset, password changed, and email-change notification emails, including branded HTML output, gateway reset links, and disable toggles for password/email change notifications.
- Added observability tooling: diagnostics settings, structured logs, health/recent-event UI, export/clear actions, retention cleanup, and redaction tests.
- Added frontend accessibility/i18n hardening: server error IDs and `aria-describedby` wiring, translated password-toggle JavaScript labels, new-tab screen-reader text, Turnstile/verification semantics, RTL-safe frontend CSS, 320px responsive guardrails, repeatable POT generation tooling, and regenerated the plugin POT file.
- Ran LocalWP Plugin Tester browser QA with Playwright/Chrome across public gateway routes, authenticated dashboard flow, non-admin `wp-admin` redirect, logout confirmation, native `wp-login.php` redirect, 320px responsive behavior, keyboard tab order, registration disabled state, registration submit gating, and pending-registration set-password completion.
- Fixed QA findings: successful login with no submitted `redirect_to` now redirects to the configured dashboard instead of preserving the underlying 404 response, and the registration submit button now remains disabled until required fields, valid email, and terms acceptance are complete.
- Installed and activated WooCommerce on LocalWP Plugin Tester, enabled the account gateway WooCommerce takeover, and browser-tested the custom dashboard plus native Orders, Downloads, Addresses, Payment Methods, and Account Details endpoint delegation.
- Fixed WooCommerce QA finding: required standard account facilities such as Payment Methods are restored in the custom dashboard navigation when WooCommerce omits them from its menu helper on a minimal store.
- Completed pre-release cleanup/security/documentation/package pass: hardened webhook URL scheme policy, expanded uninstall cleanup, removed admin preview `innerHTML`, refreshed docs/readme/changelog/hooks/settings notes, regenerated POT, fixed release zip exclusions, and verified build/lint/tests/audits/package locally.
- Completed the initial scaffold, initialized Git, installed dependencies, and verified build/lint/test/audit.
- Added scaffold master prompt artifact for the initial plugin foundation.
- Created initial implementation plan from approved product-planning decisions.
