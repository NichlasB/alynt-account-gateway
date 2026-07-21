# Alynt Account Gateway Performance Review

## Scope

This document records both phases of `06-PERFORMANCE_REVIEW_PROMPT.md`
against the immutable public v1.1.21 baseline. Approved corrections belong to
the separately approved v1.1.22 candidate. No package, release, site, or
production operation was performed during this workflow.

The review covered 151 first-party runtime and source files: 122 PHP files, 15
JavaScript or MJS files, and 14 CSS files. It examined query shapes, custom
tables, external requests, asset registration, file loading, object creation,
admin memory bounds, background processing, and JavaScript behavior.

## Phase 1 Inventory

Ten performance findings were recorded:

- Critical: 0
- High: 4
- Medium: 5
- Low: 1

The high findings were unconditional admin class loading, a large eager
frontend renderer graph, eager settings-preview registry construction on every
admin request, and synchronous account-created webhook delivery. Medium
findings covered repeated settings metadata construction, unbounded settings
imports, unbounded rate-limit metadata inspection, sequential registration
provider checks, and complete WooCommerce download/token retrieval. The low
finding was two missing composite indexes for ordered admin reads.

No runtime N+1 query, global asset-loading problem, unminified production
asset, event-listener proliferation, resize/scroll hot loop, or avoidable
render-blocking script was found. Frontend and admin assets were already
route-scoped and small.

## Implemented Optimizations

### Request-Scoped Class Loading

**Issue:** `includes/class-loader.php` included every admin settings class on
public requests.

**Strategy:** Keep shared services and the frontend facade in the normal
runtime list, but append the 34 admin files only when `is_admin()` is true.
The test loader remains explicit so the test environment does not depend on
request context.

**Before:** 119 unconditional PHP includes measured approximately 696 KB of
first-party source.

**After:** Public requests load 87 listed files measuring 443,622 bytes. The 34
admin files, currently 261,960 bytes, are excluded from public requests. The
public count includes two small lazy-resolution traits introduced by this pass.

**Trade-off:** WordPress admin, AJAX, and admin-post requests still load the
complete admin set. This preserves hook registration and avoids a custom
autoloading layer.

### Lazy Frontend And WooCommerce Object Graphs

**Issue:** Constructing the frontend facade eagerly built the document
renderer, dashboard, every authentication screen, and multiple WooCommerce
collaborator trees before the request was known to need them.

**Strategy:** Store optional injected collaborators, create the gateway
controller only when a gateway operation runs, create only the selected
document branch, and resolve WooCommerce navigation, routing, rendering, and
customer-data collaborators independently. The dashboard service now reuses
the screen's WooCommerce integration instead of creating a duplicate.

**Before:** Ordinary requests paid for the complete gateway and commerce
renderer graph even when no gateway document was rendered.

**After:** Frontend construction retains the lightweight routes, assets,
access, and URL services. Authentication screens, dashboard services, and
WooCommerce feature collaborators are created only on their active path.

**Trade-off:** A few private resolver methods replace constructor-only object
assembly. Existing collaborator injection remains supported and is covered by
regression tests.

### Early Admin Preview Gate

**Issue:** The priority-one `admin_init` preview callback instantiated the
reflection-backed settings component registry before determining whether the
request was a gateway preview.

**Strategy:** Check the settings page and preview query markers in the facade
before resolving the registry. The component still performs capability and
nonce verification for matching requests.

**Before:** Every admin page could construct roughly 30 settings components
and their registry metadata.

**After:** Unrelated admin requests return before registry construction.

**Trade-off:** The facade and component intentionally perform separate route
checks; the outer check is a cheap performance gate and the inner check owns
authorization.

### Request-Local Settings Metadata Cache

**Issue:** The 86-field schema and defaults array were rebuilt repeatedly in a
single request.

**Strategy:** Cache schema and defaults in private static request-local
properties and pass prepared defaults into the settings merger.

**Before:** Each settings read could rebuild both immutable catalogs.

**After:** Each catalog is built at most once per PHP request.

**Trade-off:** Runtime mutation of the schema inside one request is not
supported. The schema is an internal immutable definition, so no invalidation
hook is required. Persistent/transient caching was intentionally avoided.

### Bounded Admin Memory

**Issue:** Settings import read an uploaded file without a plugin-level size
bound, and rate-limit observability could load every matching transient row.

**Strategy:** Reject settings imports over 1 MB before reading their contents.
Fetch at most 1,001 rate-limit metadata rows and return a visible error when
the 1,000-row reporting ceiling is exceeded.

**Before:** Both operations were bounded only by host-level PHP and database
limits.

**After:** Import memory is capped by a 1 MB source file, and the rate-limit
summary has an explicit 1,000-row maximum without presenting truncated data as
complete.

**Trade-off:** An exceptionally large valid import must be reduced before use;
normal plugin exports are far below the ceiling. Extreme rate-limit pressure
shows an actionable error rather than a partial dashboard summary.

### Deferred Account-Created Webhook

**Issue:** Successful registration synchronously posted the account-created
webhook with a timeout of up to 10 seconds.

**Strategy:** Validate the destination and schedule a one-time WordPress Cron
delivery event. The existing direct dispatcher, HMAC signature, delivery log,
and two bounded retries remain unchanged. Deactivation clears initial and
retry hooks.

**Before:** A slow receiver could add up to the configured 10-second HTTP
timeout to the customer registration response.

**After:** Registration performs only destination validation and event
scheduling; remote delivery runs outside the customer request.

**Trade-off:** Delivery timing now depends on WP-Cron traffic and may be
delayed on low-traffic sites. Scheduling failure is returned and recorded as a
non-blocking diagnostic. The admin test-webhook action remains synchronous so
administrators receive an immediate result.

### Query-Matched Composite Indexes

**Issue:** Two ordered admin reads could require filesorts as their tables
grew.

**Strategy:** Add these indexes through the existing idempotent `dbDelta()`
upgrade path:

```sql
KEY created_at_id (created_at, id)
KEY category_created_at (category, created_at, id)
```

The first supports recent pending-registration ordering. The second supports
security diagnostics filtered by category and ordered by creation time and ID.
The schema version advances from `0.1.7` to `0.1.8`; no data transformation is
required.

**Trade-off:** Each table gains a small write and storage cost. Existing
single-column indexes remain because they serve other cleanup and lookup
shapes. Adding an index can briefly lock a large table depending on the host's
database engine.

## Accepted Constraints

### Registration Provider Latency

Turnstile and Reoon checks remain synchronous and sequential. Registration
must have both configured provider outcomes before it can apply the selected
"either provider may verify" policy and record independent diagnostics.
Parallel HTTP requests would require a new transport abstraction and would
make WordPress HTTP compatibility and provider-specific error handling less
predictable. Security correctness takes priority over speculative latency
savings here.

### WooCommerce Complete Collections

WooCommerce's public `wc_get_customer_available_downloads()` and
`WC_Payment_Tokens::get_customer_tokens()` APIs expose no supported limit or
pagination argument. The dashboard continues to delegate to those APIs so
extension compatibility and WooCommerce ownership are preserved. Replacing
them with direct database queries or a private cache would create correctness,
invalidation, and compatibility risk disproportionate to the expected
customer collection sizes.

### Welcome Email

The welcome email remains synchronous. WordPress mail submission is part of
the existing registration completion contract, while the less reliable remote
webhook is now deferred. A future queue for all transactional email would be a
separate product and operations decision rather than a narrow performance fix.

## Regression Coverage

Focused tests now lock:

- request-scoped admin loading;
- dormant frontend renderer and WooCommerce collaborators;
- early preview return without component-registry construction;
- 1 MB import and 1,000-row observability bounds;
- asynchronous initial webhook scheduling without network I/O;
- delivery and retry hook registration and deactivation cleanup; and
- schema version `0.1.8` with both composite indexes.

## Validation Evidence

- Production asset build: passed with no generated distribution change.
- POT generation: 1,142 strings, including the new bounded-import,
  rate-pressure, queue, and diagnostic wording.
- PHPCS/WPCS and PHP compatibility lint: passed.
- First-party PHP syntax: 228 files passed.
- JavaScript/MJS syntax: 17 files passed.
- PHPUnit: 480 tests and 3,407 assertions passed with no PHP deprecations.
- npm high-severity advisory audit: zero vulnerabilities.
- Composer advisory audit: no advisories.
- Source ceilings: 122 production PHP files at or below 300 lines, 15 source
  JavaScript/MJS files at or below 250 lines, and 14 source CSS files at or
  below 500 lines.
- `git diff --check`: passed.

## Outcome

All approved high-impact fixes and safe bounded optimizations are implemented.
The two accepted runtime constraints are documented with their compatibility
and security rationale. No release, package, updater, LocalWP, staging, or
production operation is part of this review.

Next ordered workflow: `07-EDGE_CASES_REVIEW_PROMPT.md` after the final
verification gate and documentation checkpoint.
