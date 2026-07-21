# Alynt Account Gateway v1.1.22 Candidate End-to-End Acceptance

## Scope

This record covers the consolidated LocalWP acceptance matrix for the
post-v1.1.21 corrective source tree. The exact candidate is commit `99ecfb9`
on `prerelease/v1.1.21-revalidation`. Its public metadata intentionally remains
at v1.1.21 until a separately approved release-preparation step.

- Target: `plugin-tester local-only`
- WordPress: `7.0.2`
- PHP: `8.5.1`
- WooCommerce: active
- Live and staging sites: excluded
- Public release, tag, and updater operations: excluded

Novamira MCP tools were registered, but the LocalWP endpoint did not return
within two bounded attempts. The approved fallback used LocalWP MariaDB,
LocalWP's PHP runtime with its generated `php.ini`, and Playwright.

## Candidate And Rollback

- Source build: passed
- PHPUnit: `547` tests, `3,995` assertions
- Runtime files: `131`
- Development files: `0`
- Candidate SHA-256:
  `BC2C2FD92DC39E9DE6DD14B1D743217769C69FE3CC2BB00534C3F4EDC6226779`
- Installed/source hash mismatches: `0`
- Rollback: private v1.1.21 installed-copy ZIP retained outside the repository

An initial internal-package copy included the Git worktree `.git` pointer
because Windows treated it as a file rather than a directory. Preflight caught
it before runtime acceptance. The pointer was removed from both the package and
LocalWP copy, the ZIP was rebuilt, and the final package inspection included
hidden files. The final installed copy contains no `.git` pointer.

## Migration And Preservation

The installed v1.1.21 baseline stored schema version `0.1.6`. The first public
request against the candidate migrated it to `0.1.8`. The settings option,
active-plugin state, and all pre-existing plugin-owned rows were preserved.

The exact pre-test settings fingerprint was restored after temporary
registration acceptance:

```text
6b1588760a275e2c5369dc37458b8012
```

## Admin Matrix

All 12 settings tabs were loaded at the WordPress `782px` breakpoint:

```text
General
URLs & Redirects
Branding & Layout
Screen Copy
Registration
Security & Spam
Emails
Dashboard
WooCommerce
Webhooks
Privacy & Data
Advanced / Tools
```

Every tab returned HTTP `200`, rendered its controls, retained all 12 tab
links, showed no fatal output, and measured zero horizontal overflow.

## Public Gateway Matrix

At `390x844`, the following logged-out routes rendered the branded gateway
with expected headings and zero horizontal overflow:

| Route | Expected state |
| --- | --- |
| `/login` | Log In |
| `/account?action=lostpassword` | Reset Password |
| `/account?action=register` | Registration Unavailable |
| `/account?action=logout` | Confirm Logout |
| `/account?action=setpassword` | Link Expired |
| `/my-account/` | Branded login with a same-site return destination |
| `/wp-login.php` | Redirect to branded login |

Login, lost password, registration-unavailable, logout, and expired-link states
also passed at `1440x1000`. The `799px` check hid the media panel and the `801px`
check displayed it; neither viewport overflowed.

Failed login retained the email field, cleared the password field, and rendered
the neutral accessible error. Unknown-address password reset rendered the
neutral `Check Your Email` state.

## Account And WooCommerce Matrix

A disposable customer completed email-only login and reached the branded
dashboard. The dashboard greeted the customer by first name and rendered with
no admin toolbar. These routes returned HTTP `200`, stayed in the custom
dashboard shell, and showed no overflow:

```text
/my-account/
/my-account/orders/
/my-account/downloads/
/my-account/edit-address/
/my-account/payment-methods/
/my-account/edit-account/
```

Customer access to `/wp-admin/` redirected to `/my-account/`. The disposable
administrator reached the native WordPress dashboard as expected.

## Registration Matrix

Registration was enabled only for the bounded acceptance flow. The pass
verified:

1. Required first name, last name, email, and terms controls.
2. Disabled submission until valid required data and consent were present.
3. Pending-registration and consent persistence without early user creation.
4. Branded confirmation email generation through the configured local mail
   stack.
5. Confirmation-link rendering of the branded Set New Password form.
6. Strong matching password submission.
7. WordPress user creation only after confirmation and password setup.
8. Generated username `@User_Alynt_Pending`.
9. Successful email-only login to the mobile dashboard.

Registration was disabled again after the flow.

## Email, Privacy, And Retention

- Registration-confirmation preview rendered branded content with `20px`
  desktop paragraph text for primary content, `16px` footer text, and no
  horizontal overflow.
- Test send returned the success notice and produced a sent local mail-log row.
- The WordPress privacy exporter returned two plugin-owned data groups and
  completed in one page.
- The privacy eraser removed the disposable account's plugin-owned data with no
  retained items.
- Retention cleanup removed an expired diagnostics fixture, preserved a fresh
  control fixture, and returned success.

## Cleanup And Final State

Cleanup removed three disposable users, their Account Gateway rows, their
local mail rows, all matching rate-limit transients, temporary PHP helpers,
temporary database dumps, and browser sessions.

Final reconciliation:

| State | Final value |
| --- | ---: |
| Original users | `2` |
| Pending registrations | `0` |
| Webhook logs | `0` |
| Verification logs | `0` |
| Consent records | `0` |
| Existing audit logs | `8` |
| Diagnostics logs | `0` |
| QA mail rows | `0` |
| QA rate-limit options | `0` |
| Installed runtime files | `131` |
| Installed hash mismatches | `0` |

The browser console recorded no errors. Its only warning was WordPress's
development React-refresh shim. A first command-line bootstrap omitted
LocalWP's generated PHP configuration and produced a missing-`mysqli` fatal in
the local PHP log. The command was rerun with LocalWP's `php.ini` and succeeded;
this was an execution-environment mistake, not an Account Gateway runtime
failure.

## Result

The exact `99ecfb9` source candidate passes the consolidated LocalWP acceptance
matrix. No acceptance defect remains open. The next approval-controlled step is
v1.1.22 release preparation: align version metadata and changelog, rerun final
source and package gates, and present the release candidate for publication
approval.
