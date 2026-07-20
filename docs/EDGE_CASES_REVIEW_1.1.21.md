# Alynt Account Gateway Edge Cases Review

## Scope

This document records both phases of `07-EDGE_CASES_REVIEW_PROMPT.md`
against the immutable public v1.1.21 baseline. Approved corrective work belongs
to a separately approved v1.1.22 candidate. No package, release, site, or
production operation was performed during this workflow.

Phase 1 inventoried 26 scenarios: 8 high, 14 medium, 4 low, and no critical
finding. Phase 2 used four explicit treatments: Prevent, Detect and Handle,
Recover, or Accept.

## Implemented Treatments

### Prevent

- Short-lived, privacy-preserving operation locks use atomic `add_option()`
  creation to serialize rate-limit counter updates and same-email public
  pending-registration creation. Stale locks can be reclaimed, ownership is
  checked before release, and uninstall removes lock options.
- Registration and set-password submit buttons are server-usable before
  JavaScript runs. JavaScript still disables invalid forms after initialization,
  while HTML constraints and server validation protect no-script submissions.
- Numeric settings now clamp to schema-owned minimums and maximums. Dashboard
  custom links are capped at 100 and each link is capped at 20 role entries.
- Generated usernames remain within WordPress's 60-character limit even after
  collision suffixes. Password length uses UTF-8-aware server counting when
  available and code-point counting in the browser.
- Reoon responses must use HTTP 200 and a documented status. `valid` and `safe`
  pass, blocked and flagged statuses retain their policy behavior, and unknown
  response-contract values fail closed.
- Network-wide multisite activation is rejected with a clear per-site
  activation instruction. A failed database installation now rolls back a
  newly created settings option and aborts activation instead of presenting a
  partially initialized plugin as healthy.

### Detect And Handle

- A queued account-created webhook now snapshots the destination, full user and
  site payload, creation timestamp, signing/debug policy, and stable event ID.
  Retries reuse that immutable envelope and emit `X-Alynt-AG-Delivery`, avoiding
  configuration drift, user deletion drift, and duplicate downstream event
  identity.
- Transport and non-2xx webhook failures continue to receive two bounded
  retries. A successful receiver response is treated as delivery success even
  if local delivery-log persistence subsequently fails; that secondary failure
  is sent to diagnostics instead of encouraging an unsafe duplicate delivery.
- Existing malformed JSON, provider transport failures, expired tokens,
  zero-row state transitions, privacy query failures, and bounded cron
  scheduling failures retain their previously reviewed error paths.

### Recover

- An expired operation lock is deleted and atomically reacquired on the next
  matching request.
- Webhook retries preserve event identity and the original destination rather
  than rebuilding an event from current settings.
- Existing registration rollback, confirmation idempotency, privacy batching,
  bounded retention continuations, and settings-preservation paths remain the
  recovery mechanisms for their respective failures.

## Accepted Or Deferred Scenarios

The following are not release blockers for the v1.1.22 candidate:

- Turnstile and Reoon remain synchronous and sequential. Registration decisions
  require configured provider outcomes before applying the selected policy;
  parallel provider transport remains a separate architecture project.
- Delivery timing depends on WP-Cron. Sites using `DISABLE_WP_CRON` must provide
  a real cron runner. The bounded queue does not add a custom daemon or replay UI.
- Deactivation clears pending plugin cron hooks. A delivery queued immediately
  before intentional deactivation may therefore be abandoned; preserving or
  replaying jobs across inactive code requires a durable queue product decision.
- Concurrent administrators can overwrite settings saved from stale tabs. This
  is standard WordPress options behavior; introducing record versioning would
  alter the settings contract.
- Saved media, legal-path, and navigation-menu references can become stale after
  external deletion. Current renderers degrade to absent/fallback output.
- Rich email content has no plugin-specific character ceiling beyond host and
  WordPress request limits. It is administrator-only, sanitized, and not a
  public ingestion surface.
- User deletion can leave retained audit/verification history until configured
  retention or personal-data erasure runs. Prompt 08 and the later privacy and
  security reviews remain responsible for validating final lifecycle policy.
- Compatibility warnings identify competing login, routing, and WooCommerce
  hooks but cannot safely resolve arbitrary third-party ordering conflicts.
- Expired administrator nonces use the standard WordPress failure screen. Public
  gateway nonces retain branded recovery behavior.
- The check-then-schedule shape used by ordinary WordPress Cron can theoretically
  duplicate equivalent maintenance events under extreme concurrency. Handlers
  are bounded/idempotent, and a custom scheduler lock is disproportionate here.

## Phase 1 Scenario Disposition

| Scenario | Initial Risk | Treatment | Result |
|---|---:|---|---|
| Non-atomic transient rate-limit increment | High | Prevent | Atomic operation lock added |
| Registration/set-password require JavaScript to submit | High | Prevent | Progressive-enhancement fallback added |
| Webhook retries lack stable event identity | High | Prevent/Recover | Stable event ID and envelope added |
| Reoon undocumented statuses fail open | High | Prevent | Explicit documented-status allowlist added |
| Sequential provider latency | High | Accept | Security/compatibility constraint documented |
| Queued webhook reads changed settings/user data | High | Prevent | Immutable queue-time snapshot added |
| Network activation initializes only one site | High | Prevent | Network-wide activation rejected clearly |
| Activation ignores database install failure | High | Prevent | Activation now aborts on failure |
| Concurrent same-email pending replacement | Medium | Prevent | Public creation path serialized by email |
| Resend changes token before mail outcome | Medium | Accept | Existing bounded retry/recovery wording retained |
| Deactivation clears queued webhook jobs | Medium | Accept | Intentional lifecycle behavior documented |
| `DISABLE_WP_CRON` delays jobs | Medium | Accept | External cron requirement documented |
| No manual webhook replay after final retry | Medium | Deferred | Separate provider/admin UX slice |
| Successful webhook plus failed log reports failure | Medium | Detect/Handle | Receiver success now remains success |
| User deletion leaves retained logs | Medium | Deferred | Prompt 08/privacy/security lifecycle review |
| Corrupt saved setting types | Medium | Detect/Handle | Save/import normalization retained; deeper read repair deferred |
| Numeric settings have no upper bounds | Medium | Prevent | Schema min/max clamps added |
| Long names can exceed username limits | Medium | Prevent | Suffix-aware 60-character cap added |
| Unicode password counts differ | Medium | Prevent | Browser/server counting aligned |
| Unlimited dashboard links/roles | Medium | Prevent | 100-link/20-role bounds added |
| Concurrent settings edits lose updates | Medium | Accept | Standard WordPress last-write behavior |
| Third-party hook conflicts | Medium | Detect/Handle | Existing compatibility warnings retained |
| Stale media/menu/legal references | Low | Accept | Existing graceful fallback retained |
| Unlimited rich email length | Low | Accept | Administrator-only sanitized input |
| Cron check/schedule race | Low | Accept | Bounded idempotent handlers retained |
| Expired admin nonce uses `wp_die()` | Low | Accept | Standard WordPress admin behavior |

## Regression Coverage

Focused coverage now locks operation-lock contention, privacy-preserving bucket
keys, no-script-capable form markup, browser password counting, username length,
numeric field bounds, Reoon HTTP/status failure behavior, immutable webhook
delivery and retry identity, multisite/database activation guards, and uninstall
lock cleanup. Prompt `07A-ADVERSARIAL_TEST_SUITE_REVIEW_PROMPT.md` remains next
for systematic hostile and mutation-oriented test expansion.

## Validation Evidence

- Production asset build: passed.
- POT generation: 1,146 strings.
- PHPCS/WPCS and PHP compatibility lint: passed.
- PHP syntax: 230 tracked files passed.
- JavaScript/MJS syntax: 17 tracked files passed.
- PHPUnit: 487 tests, 3,443 assertions, no failures or deprecations.
- npm high-severity audit: zero vulnerabilities.
- Composer advisory audit: no advisories.
- Source ceilings: zero production PHP files over 300 lines, zero source
  JavaScript files over 250 lines, and zero source CSS files over 500 lines.
- `git diff --check`: passed.

Prompt 07 is complete. Prompt 07A is next. Publication, packaging, tagging,
deployment, and site acceptance remain separately approval-gated.
