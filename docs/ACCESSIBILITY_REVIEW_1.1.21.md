# Alynt Account Gateway Accessibility Review 1.1.21

## Boundary

This Prompt 10 review evaluates the post-refactor `v1.1.21` codebase against
WCAG 2.1 Level AA patterns. Corrective work remains on the review branch and
targets a separately approved `v1.1.22` candidate. No WordPress site, release,
package, database, or saved setting was changed.

The review covered 64 UI-facing source files: 35 PHP renderers, 15 JavaScript
modules, and 14 CSS files. It included gateway forms and state screens, the
dashboard and WooCommerce wrapper, admin settings and tools, tables, media
controls, custom-link editing, menus, live regions, keyboard behavior, focus,
responsive reflow, reduced motion, forced colors, and RTL-sensitive styles.

## Summary

```text
Files reviewed: 64
Accessibility issues: 7
Critical (blocks usage): 0
Major (significant barrier): 3
Minor (inconvenience): 4
Fixed: 7
Accepted: 0
Deferred code findings: 0
```

## Critical Issues

None found.

## Major Issues

### Dynamic Dashboard-Link Editing

```text
ISSUE: Adding or removing a custom dashboard-link row changed the interface
without announcing the result or moving focus to a predictable control.
FILE: assets/src/admin/modules/dashboard-links.js
WCAG: 2.4.3 Focus Order; 4.1.3 Status Messages
IMPACT: Keyboard and screen-reader users could lose their working position or
miss that the requested row operation completed.
FIX: Added an atomic polite status region, localized add/remove messages, focus
on the new label field after addition, and focus on the nearest remaining row
or Add Dashboard Link button after removal.
```

Repeated rows now expose `role="group"` with an associated heading, and each
remove button has the explicit accessible name `Remove dashboard link`.

### Off-Canvas Dialog Isolation

```text
ISSUE: Keyboard focus was trapped correctly, but background dashboard content
remained available to virtual-cursor and other assistive-technology navigation.
FILE: assets/src/frontend/modules/offcanvas.js
WCAG: 2.4.3 Focus Order; 4.1.2 Name, Role, Value
IMPACT: Screen-reader users could navigate outside the modal account menu while
it was presented as an aria-modal dialog.
FIX: Set sibling dashboard regions inert while the dialog is open and remove
only inert state introduced by the plugin when it closes.
```

The existing first-focus behavior, Tab and Shift+Tab loop, Escape close,
`aria-expanded` updates, and trigger-focus restoration remain intact.

### Admin Data-Table Semantics

```text
ISSUE: Non-layout admin tables did not consistently expose an accessible name,
and three tables omitted scope="col" on column headers.
FILE: admin/settings-page/*.php
WCAG: 1.3.1 Info and Relationships; 2.4.6 Headings and Labels
IMPACT: Screen-reader users received less context when entering operational,
security, compatibility, diagnostics, and webhook tables.
FIX: Added translated aria-label values to every non-layout data table and
scope="col" to every column header. The WordPress settings form table remains
role="presentation" because it is layout, not tabular data.
```

## Minor Issues

### Current Settings Tab

```text
ISSUE: The selected tab was visually styled but not programmatically identified.
FILE: admin/settings-page/class-page-shell.php
WCAG: 1.3.1 Info and Relationships
IMPACT: Screen-reader users had to infer the current settings section.
FIX: Added aria-current="page" to the active settings-tab link.
```

### New-Window Navigation

```text
ISSUE: Gateway preview, off-canvas, and footer menu links could open a new tab
without consistently announcing that behavior.
FILE: admin/settings-page/class-settings-tools.php;
includes/services/class-dashboard-navigation-renderer.php;
includes/services/class-offcanvas-menu-walker.php
WCAG: 3.2.5 Change on Request
IMPACT: Screen-reader users could experience an unexpected context change.
FIX: Added translated new-tab announcements and merged noopener/noreferrer with
any existing rel value.
```

### Media Control Context And Status

```text
ISSUE: Repeated Select Image and Remove buttons did not include their field
context, and selection/removal had no status announcement.
FILE: admin/settings-page/class-complex-fields.php;
assets/src/admin/modules/media.js
WCAG: 2.4.6 Headings and Labels; 4.1.3 Status Messages
IMPACT: Assistive-technology users could not easily distinguish logo and
background-image controls or confirm that a media change occurred.
FIX: Added field-specific accessible names, a polite atomic status region,
localized messages, and synchronized disabled/aria-disabled state.
```

### Decorative Dialog Backdrop

```text
ISSUE: The clickable visual backdrop was not explicitly hidden from assistive
technology even though the named Close account menu button provides the action.
FILE: includes/services/class-dashboard-navigation-renderer.php
WCAG: 1.1.1 Non-text Content
IMPACT: Some accessibility trees could expose a meaningless visual layer.
FIX: Added aria-hidden="true" to the backdrop while preserving pointer close.
```

## Keyboard Navigation Report

```text
COMPONENT: Gateway forms
RESULT: Native fields and buttons follow DOM order; labels are associated;
invalid fields receive focus after redirected errors; visible focus remains.

COMPONENT: Password controls
RESULT: Native buttons support Enter/Space, expose pressed state and controlled
input, announce visibility, and preserve policy status and invalid state.

COMPONENT: Dashboard off-canvas navigation
RESULT: Open moves focus into the dialog; Tab/Shift+Tab remain inside; Escape
closes; close returns focus; submenu buttons expose controls/expanded state;
background regions are inert while open.

COMPONENT: Admin dashboard-link editor
RESULT: Add moves focus to the new row; remove moves focus to the nearest
remaining row or add control; both operations announce completion.

COMPONENT: Native details, links, forms, and WordPress media modal
RESULT: Native keyboard behavior retained; no positive tabindex or custom
keyboard-only substitute was introduced.
```

## Color Contrast

No default-palette contrast failure was found.

```text
Text #281408 on surface #FFFFFF: 17.59:1
Text #281408 on page background #EAE4D6: 13.88:1
Text #281408 on accent #E1CDB5: 11.39:1
Button text #FFFFFF on button background #3B5249: 8.44:1
Required normal-text ratio: 4.5:1
Required UI/large-text ratio: 3:1
```

Site administrators can configure these colors. Existing field help explicitly
requires checking text/background pairs; the plugin does not silently rewrite a
site's chosen brand colors.

## Existing Controls Confirmed

- One page-level H1 and logical section headings on rendered plugin screens.
- Semantic `main`, `nav`, `aside`, header, footer, and section elements.
- Explicit labels or accessible names for rendered controls.
- Required attributes plus textual instructions and associated error messages.
- Assertive atomic alerts for errors and polite atomic status regions for
  successful or dynamic updates.
- Decorative SVGs and preview images hidden or supplied with empty alt text;
  informative brand logos have site-name alternatives.
- Consistent `:focus-visible` styling, forced-colors rules, and no blanket focus
  outline removal.
- Responsive grids, wrapping controls, bounded media, and horizontal containment
  for unavoidable WooCommerce/admin data surfaces.
- `prefers-reduced-motion` suppression for plugin animations and transitions.
- `dir` output, logical properties, and existing RTL regression coverage.

## Verification

- `npm.cmd run build`: pass.
- `npm.cmd run make-pot`: pass, 1,166 strings.
- `npm.cmd run lint`: pass.
- PHP syntax across first-party PHP: pass.
- JavaScript syntax across source modules and scripts: pass.
- PHPUnit normal order: 542 tests, 3,885 assertions, pass.
- PHPUnit reverse order: 542 tests, 3,885 assertions, pass.
- PHPUnit fixed-random order, seed `20260720`: 542 tests, 3,885 assertions,
  pass.
- `npm.cmd audit --audit-level=high`: zero vulnerabilities.
- Composer validation: pass.
- Composer advisory audit: no advisories.
- `git diff --check`: pass.

## Residual Acceptance Boundary

No release-blocking code finding remains from Prompt 10. Automated source and
renderer coverage cannot replace human assistive-technology acceptance. The
consolidated post-Prompt-13 workflow therefore retains keyboard-only, zoom,
forced-colors, reduced-motion, RTL, browser accessibility-tree, and practical
screen-reader checks on an explicitly approved disposable WordPress target.
