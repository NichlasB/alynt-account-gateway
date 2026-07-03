# Alynt Account Gateway Design Handoff

## Source

- Export received: `C:\Users\Captain\Desktop\# Alynt Account Gateway Design System__---__## Design Tokens__### Color Palette__ Token _ Value _ Usage ___----.zip`
- Extracted prototype: `Alynt Account Gateway.dc.html`
- Bundled references:
  - `uploads\Login Form.jpg`
  - `uploads\Register screen.jpg`
  - `uploads\Lost Password Form.jpg`

The export is a design prototype, not production code. Use it as the visual and interaction source for the WordPress templates, CSS, and settings mappings.

The palette and typography in this handoff are default starter values only. Alynt Account Gateway must remain brand-agnostic out of the box: colors, logo, background image, instruction copy, and font stacks are settings-driven and can be changed per site.

## Design Direction

- Warm, calm, branded account gateway.
- Website-agnostic by default, with store logo, store name, colors, and media configurable from plugin settings.
- Avoid standard WordPress login visuals during normal enabled frontend use.
- Keep the experience compact and form-first. The product is the account action, not a marketing landing page.

## Design Tokens

| Token | Value | Usage |
| --- | --- | --- |
| Graphite | `#281408` | Default body text, headings, dark focus outlines |
| Mineral Green | `#3B5249` | Default primary buttons, links, success accents |
| Pearl Bush | `#EAE4D6` | Default page background |
| Grain Brown | `#E1CDB5` | Default instruction and notice boxes |
| Terracotta | `#B3492E` | Default error borders, icons, and text |
| White | `#FFFFFF` | Default card surfaces and button text |

Recommended CSS custom properties:

```css
.alynt-account-gateway {
	--agw-color-text: #281408;
	--agw-color-primary: #3B5249;
	--agw-color-background: #EAE4D6;
	--agw-color-notice: #E1CDB5;
	--agw-color-error: #B3492E;
	--agw-color-surface: #FFFFFF;
	--agw-button-background: #3B5249;
	--agw-button-text: #FFFFFF;
	--agw-font-heading: Georgia, serif;
	--agw-font-body: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}
```

Map plugin settings into these custom properties at render time:

- Primary/accent color can override `--agw-color-primary`.
- Button background can override the primary button background independently.
- Button text color can override the primary button text independently.
- Text, background, surface, notice, and error colors should use settings-backed defaults.

## Typography

- Prototype headings: `Fraunces, Georgia, serif`
- Prototype body, labels, inputs, buttons: `Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`
- Production default headings: `Georgia, serif`
- Production default body, labels, inputs, buttons: `-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`
- Page title: 28 to 32px, weight 600.
- Form title: 26px, weight 600.
- Section heading: 20 to 22px, weight 600.
- Body and inputs: 15 to 16px.
- Helper/meta text: 12 to 13px.

Implementation note: do not hard-depend on remote font loading. Use settings-backed font-family stacks with graceful fallback, and consider an optional "load gateway fonts" setting later if local/legal font loading becomes important.

## Layout

### Desktop

- Breakpoint: 800px.
- Above 800px, use a two-column split screen.
- Left panel: media/background area, approximately 44% width.
- Right panel: centered form card.
- Outer frame may use up to 16px radius in preview/dashboard contexts.
- Form card max width: 420px.
- Form card padding: 40px 36px on desktop.
- Card background: white.
- Card radius: 8px.
- Card shadow:

```css
0 1px 2px rgba(40, 20, 8, .05),
0 16px 32px -16px rgba(40, 20, 8, .14)
```

### Mobile

- Below 800px, collapse to one column.
- Hide the media panel.
- Form card becomes full-width page content.
- Reduce side padding to about 24px.
- First/last name and other two-column field rows stack vertically.
- Preserve readable type sizes rather than scaling typography with viewport width.

## Media Panel

- The prototype uses a soft abstract botanical panel, not a product photo.
- Production should support the plugin's one global background image setting.
- If no image is configured, use a neutral generated pattern/gradient based on the token palette.
- Recommended uploaded image guidance for settings UI: at least 1600px wide, portrait or flexible crop, with important details away from the center split edge.

## Component Inventory

- Split screen shell: media panel plus centered form card.
- Form card: white surface, 8px radius, restrained shadow, 420px max width.
- Brand block: uploaded logo plus optional store/site name.
- Instruction box: Grain Brown surface for orienting copy on each screen.
- Text field: visible label, soft background, 1px border, 2px Mineral focus ring, `aria-invalid` on error.
- Password field: show/hide toggle with at least 44px hit target.
- Checkbox with label: remember me and terms/privacy agreement.
- Primary button: full width, default/hover/loading/disabled states.
- Secondary button: outline style, used for cancel/destructive escape actions.
- Underlined text link: back to login, create account, forgot password.
- Inline field error: icon plus text under the field; never color alone.
- Form-level banner: error and success variants, using `role="alert"` and `role="status"` respectively.
- Password strength meter: 4-segment bar plus text label and requirements checklist.
- Verification widget slot: reserved block for Turnstile/Reoon/anti-spam messaging.

## Screen Inventory

Implement the following branded screens under the shared shell:

1. Login
2. Registration
3. Lost password
4. Set new password
5. Logout confirmation
6. Registration disabled
7. Invalid or expired link

The prototype includes state variations for default, submitting, error, success, ready/valid, confirmed, and requested states. Production should represent these with server-rendered state classes/data attributes plus minimal JavaScript where needed.

## Screen Notes

### Login

- Heading: "Log In"
- Instruction copy should be configurable.
- Email field only for username/login.
- Password field with show/hide toggle.
- Remember me checkbox.
- Full-width primary "Log In" button.
- Links: "Create Account" and "Forgot Password?"
- Error state should include a form-level alert and inline field hint without revealing whether the email exists.

### Registration

- Heading: "Create Account"
- Highlighted instruction box explains confirmation email and spam folder.
- First name and last name side by side on desktop, stacked on mobile.
- Email field full width.
- Terms/privacy agreement checkbox with configured relative URL links.
- Verification slot below terms.
- Full-width "Create Account" button disabled until required conditions are met.
- Bottom link: "Back to Login"
- Success state should explain that a confirmation email was sent, without creating a WordPress user yet.

### Lost Password

- Heading: "Reset Password"
- Instruction copy explains that a reset link will be sent.
- Email field.
- Full-width primary "Reset Password" button.
- Bottom link: "Back to Login"
- Success and error copy should avoid account enumeration.

### Set New Password

- Heading: "Set New Password"
- Password and confirm password fields, both with show/hide toggles.
- Strength meter with 4 segments.
- Requirements checklist:
  - At least 12 characters
  - Uppercase letter
  - Lowercase letter
  - Number
  - Special symbol
  - Passwords match
- Full-width "Save Password" button disabled until requirements pass.
- Success state offers "Continue to Login".

### Logout Confirmation

- Heading: "Log Out"
- Instruction copy confirms intent.
- Primary "Log Out" button.
- Secondary "Cancel" button.
- Confirmed state says the user has been logged out and offers "Log In Again".

### Registration Disabled

- Heading: "Registration Unavailable"
- No form.
- Explain that new account registration is currently unavailable.
- Single "Back to Login" action.

### Invalid Or Expired Link

- Heading: "Link Expired"
- Explain that the confirmation/reset link is invalid or expired.
- Email field to request a new link when relevant.
- Primary "Send New Link" action.
- Bottom link: "Back to Login"

## Accessibility Requirements

- Use semantic forms, labels, and native submit buttons.
- Use `aria-invalid` and `aria-describedby` for fields with errors.
- Use `role="alert"` for errors and `role="status"` with `aria-live="polite"` for success messages.
- Do not communicate state with color only.
- Keep interactive targets at least 44px where practical.
- Make password visibility toggles keyboard accessible and screen-reader labeled.
- Ensure focus styles are visible against all configured colors.
- Keep all strings translatable.
- Ensure RTL-safe layout declarations where possible.

## WordPress Implementation Rules

- Wrap all public markup in a namespaced root, such as `.alynt-account-gateway`.
- Prefix component classes with `agw-`.
- Do not ship prototype inline styles as production markup.
- Use template partials for shared shell, brand block, notices, fields, buttons, and links.
- Drive screen/state with data attributes, for example:

```html
<div class="alynt-account-gateway" data-agw-screen="login" data-agw-state="error">
```

- Preserve WordPress nonces, reset keys, redirects, and native authentication/password APIs.
- Use plugin routes as the outer experience, but delegate sensitive account actions to WordPress/WooCommerce APIs.
- Keep frontend output disabled by default until the user enables it in settings.

## Production Deviations From Prototype

- Replace editable placeholder logo/name with uploaded logo and site/store name settings.
- Replace static verification placeholder with actual Turnstile output when configured and server-side validation on submit.
- Replace fake navigation/state controls with real route/action handling.
- Replace inline SVG icons with a small vetted icon set or accessible inline SVG partials.
- Avoid `color-mix()` as the only color mechanism unless fallback colors are defined.
- Avoid remote image/font dependencies in the default plugin output.
