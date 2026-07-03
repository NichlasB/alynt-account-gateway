# WordPress Plugin Development Rules

## Core Standards

- Follow WordPress Coding Standards for all PHP code.
- Use PHP 7.4+ compatible syntax.
- Use prefixed class names and functions based on `alynt_ag_`.
- Keep frontend output disabled by default.
- Keep files focused and split large classes before they become difficult to review.

## Security

- Verify nonces for every admin form, AJAX action, REST mutation, preview action, import/export action, and test-send action.
- Check capabilities for every admin-only operation.
- Sanitize all input before storage.
- Escape all output by context.
- Use `$wpdb->prepare()` for all dynamic database queries.
- Never store raw confirmation tokens.
- Never store chosen passwords in pending registration records.
- Never use `eval()`, unsafe dynamic includes, or `extract()` with untrusted data.

## Frontend

- Namespace all CSS classes with `alynt-ag-`.
- Do not reset global theme styles.
- Use semantic HTML and accessible labels.
- Preserve keyboard access and visible focus states.
- Use inline validation with `aria-invalid` and live-region status messages.
- Support responsive layouts down to 320px.

## Workflow

- Keep `docs/IMPLEMENTATION_PLAN.md` current as work progresses.
- Use the plugin toolkit feature review sequence after major feature implementation.
- Do not deploy to LocalWP or live sites unless a site-specific workflow explicitly asks for it.
