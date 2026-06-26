# Search Logic

Updated: 2026-06-26

This document defines search logic for the Mobile Lara SaaS system. It explains
local search behavior, API search behavior, recent searches, saved filters,
filtering principles, sorting principles, scan-to-search behavior, offline
search limitations, and privacy and tenant isolation in search. It is
documentation only and does not define database structure, database fields,
migrations, indexes, seeders, routes, controllers, Livewire components,
Filament resources, NativePHP plugins, policies, gates, middleware, jobs,
services, local storage schemas, API endpoints, UI components, CSS,
JavaScript, search-engine configuration, queues, or application logic.

Use this document with [Product Principles](product-principles.md), [Two-System
Boundary Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Offline-First Principles](offline-first-principles.md),
[Offline UX Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), [Records/Content
Module Logic](records-content-module-logic.md), [Mobile Dashboard Logic](mobile-dashboard-logic.md),
[Mobile Settings Logic](mobile-settings-logic.md), [Mobile Permission
Logic](mobile-permission-logic.md), [Mobile App Lock Principles](mobile-app-lock-principles.md),
[Forms And Drafts Logic](forms-drafts-logic.md), [Notifications
Logic](notifications-logic.md), and [API v1 Records Contract](../contracts/api/v1-records.md):
search is a tenant-scoped discovery capability, and Admin/API remains
authoritative for what can be searched, filtered, sorted, returned, cached,
scanned, saved, shared, audited, retained, notified, or hidden.

Forms And Drafts Logic is defined in `forms-drafts-logic.md`:
search forms, saved filter forms, and draft-discovery behavior must stay simple,
validated, autosave-aware where useful, offline-draft safe, API-submitted when
needed, admin-controlled, and explicit about local-save versus server-accepted
state so user work is protected without bypassing authority.

Notifications Logic is defined in `notifications-logic.md`:
notification inbox search, notification deep-link discovery, and notification
preference lookup must remain tenant-scoped, permission-aware, privacy-safe,
offline-limited, and API-authoritative before showing counts, snippets, or
destinations.

Support System Logic is defined in `support-system-logic.md`:
support requests, support messages, attachments, tenant context, support-agent
visibility, audit, notifications, and offline support drafts must remain
tenant-scoped, least-privilege, privacy-safe, and Admin/API-authoritative.

Billing And Plan Logic is defined in `billing-and-plan-logic.md`:
plan-based access, trial behavior, active/expired/suspended subscription states,
plan limits, feature-flag entitlement ceilings, mobile unavailable-feature states,
and manual admin billing controls must remain Admin/API-authoritative,
tenant-scoped, auditable, and provider-neutral.

Reporting Logic is defined in `reporting-logic.md`:
admin measurements, tenant-admin measurements, mobile-visible summaries,
privacy boundaries, date ranges, exports, feature usage, sync health,
notification, support, and billing reports must remain tenant-scoped,
permission-aware, privacy-safe, auditable, and Admin/API-authoritative.

Native Feature Strategy is defined in `native-feature-strategy.md`:
NativePHP capability use, logical service boundaries, browser/development
fallbacks, permission education, admin feature-flag control, native failure
UX, and offline sync behavior must remain feature-scoped, tenant-safe,
privacy-aware, fallback-safe, and Admin/API-authoritative.

Camera And Media Logic is defined in `camera-media-logic.md`:
photo capture, media selection, media preview, record/support attachments,
offline media storage, upload queues, feature-flag control, permission
denial, size limits, and privacy behavior must remain tenant-scoped,
permission-aware, fallback-safe, queue-safe, privacy-safe, and
Admin/API-authoritative.

Scanner Logic is defined in `scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Voice Note Logic is defined in `voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

## Search Statement

Search helps mobile and admin users find tenant-scoped work quickly without
weakening access control.

Search is not a separate authority layer. It is a discovery layer over content,
records, activity, settings, support context, notifications, reports, or other
modules that are already allowed by tenant, role, permission, feature flag,
subscription, app-version, maintenance, privacy, sync, and audit rules.

Product rule: if a user cannot view an item through normal API access, search
must not reveal that item, its title, its count, its metadata, its existence,
or any useful hint that it exists.

## Search Goals

Search should make common mobile work faster.

Primary goals:

- Help users find records, drafts, recent work, supportable items, and allowed
  tenant content with minimal typing.
- Let users narrow results through understandable filters and predictable
  sorting.
- Support local search over safe cached data when offline.
- Support API search for current, complete, permission-filtered results when
  online.
- Support scan-to-search where scanning a code, label, QR value, or barcode is
  faster than typing.
- Preserve tenant isolation and privacy even when the query, cache, or device
  state is stale.

Non-goals:

- Search must not bypass API authorization.
- Search must not create cross-tenant discovery.
- Search must not become a hidden reporting engine.
- Search must not expose private fields only because they are indexed or
  cached.
- Search must not store sensitive search terms longer than policy allows.
- Search must not treat local results as server-trusted when offline.

## Ownership And Authority

| Concern | Admin/API authority | Mobile responsibility |
| --- | --- | --- |
| Search availability | Decide whether search is enabled globally, by tenant, by plan, by role, by user, by app version, or by module. | Show or hide search entry points from resolved API context and feature flags. |
| Searchable content | Decide which modules and content types are searchable. | Search only cached or API-returned content that is visible to the current user. |
| Access control | Enforce tenant, role, permission, subscription, feature, and lifecycle boundaries. | Treat API-denied, stale, or disabled search states as final and explain them safely. |
| Local search | Define what can be cached and searched offline. | Search safe tenant-scoped cache, local drafts, and pending local work without implying completeness. |
| API search | Return current, authorized, predictable, tenant-scoped results. | Request search through API only and render returned results without inventing hidden matches. |
| Recent searches | Define retention, privacy, clear rules, and whether recents can sync. | Store only allowed recent-search context and clear it when tenant/session policy requires. |
| Saved filters | Define whether filters are private, tenant-shared, admin-provided, plan-limited, or disabled. | Apply available filters locally or through API according to online/offline state. |
| Scan-to-search | Decide whether scanning is enabled and which scan formats map to search. | Explain permission purpose, scan safely, validate locally, and search only allowed scopes. |
| Privacy | Define what query/result metadata can be logged, audited, diagnosed, exported, or deleted. | Avoid leaking search terms through UI, local storage, logs, diagnostics, or cross-tenant state. |

## Local Search Behavior

Local search is an offline-capable convenience over data already present on the
device.

Local search may include:

- Cached records and content that the current user was allowed to view.
- Current tenant metadata that is safe for offline display.
- Local drafts and queued actions created on the device.
- Recently opened items that remain allowed by cached context.
- Attachment metadata that is safe to show locally.
- Tags, categories, status labels, and other safe local filter context.

Local search must remain limited:

- It searches only what is present in the current tenant cache.
- It cannot discover content that has never been synced to the device.
- It cannot confirm that results are current.
- It cannot prove that an item still exists on the server.
- It cannot reveal disabled modules or server-only content.
- It cannot override a cached block such as suspended tenant, locked app,
  revoked session, forced update, or maintenance mode.

Local results should be labeled by state where useful:

- Cached result.
- Local draft.
- Pending sync.
- Sync failed.
- Conflict.
- Stale or last synced.
- Unavailable until online.

Local search should prefer speed, clarity, and safe degradation. It should not
try to mimic every API search behavior if doing so would create confusing or
unsafe results.

## API Search Behavior

API search is the authoritative online search path.

API search should:

- Run only through authenticated, tenant-scoped API communication.
- Validate the query, filters, sorting, pagination, and module scope before
  searching.
- Apply permissions before results are counted, ranked, grouped, or returned.
- Apply feature flags and plan limits before exposing module-specific results.
- Return predictable result shapes and user-friendly mobile errors.
- Prefer deterministic pagination and stable result ordering.
- Explain unavailable search states without exposing sensitive internals.
- Support sync-aware states where relevant, such as conflict, pending, archived,
  deleted, or unavailable content.

API search should not:

- Return results from another tenant.
- Return hidden records as partial snippets.
- Return total counts that reveal inaccessible data.
- Return raw internal scoring details.
- Depend on mobile-provided tenant, permission, or feature authority.
- Treat scanned values, saved filters, or recent terms as trusted input.

When API search and local search disagree, API search wins. Mobile may preserve
the user's local query and pending context, but it must update the result state
to match the server response once online.

## Query Behavior

Search queries should be simple and forgiving for mobile users.

Query principles:

- Trim accidental whitespace.
- Treat casing consistently.
- Handle short queries through predictable rules.
- Avoid search behavior that changes unexpectedly between screens.
- Avoid returning overwhelming results for broad queries.
- Avoid accepting unlimited input length.
- Avoid exposing raw validation details.
- Avoid using query text as a permission or tenant boundary.

Mobile should help users recover from poor queries:

- Empty query should show either safe defaults, recent context, or no-query
  state depending on the screen purpose.
- Too-short query should explain that more input may be needed.
- Unsupported characters or formats should be handled with a friendly message.
- Very broad query should encourage filters rather than dumping excessive
  results.
- Failed query should preserve user input when safe so the user can retry.

## Recent Searches

Recent searches help users repeat common work without typing.

Recent-search principles:

- Recent searches should be scoped by tenant and user context.
- Recents should never appear across tenants.
- Recents should be cleared on logout when policy requires.
- Recents should be cleared or hidden when app lock, server revocation,
  tenant suspension, or privacy policy requires.
- Recents should store the minimum useful information, normally query text,
  selected filters, selected module, and timestamp-like meaning.
- Recents should avoid storing result payloads unless explicitly allowed as
  cached search results.
- Sensitive recent terms should be avoidable, clearable, and excluded from
  diagnostics by default.
- Admin should be able to control whether recent searches are enabled.

Recent searches may be local-only or API-backed depending on product policy.
Local-only recents are simpler and privacy-friendly. API-backed recents may
support multi-device continuity, but they require stronger consent, retention,
deletion, tenant isolation, and support visibility rules.

## Saved Filters

Saved filters let users or admins preserve common search setups.

Saved-filter types:

- Private user filters: visible only to the current user in the current tenant.
- Tenant filters: shared with allowed users inside one tenant.
- Admin-provided filters: curated by tenant admins or platform admins for
  standard workflows.
- System filters: product-defined filters such as active, archived, pending
  sync, assigned to me, or needs review.
- Temporary filters: current screen state that is not saved.

Saved-filter principles:

- A saved filter stores search intent, not access authority.
- Applying a saved filter must still pass current API permissions and feature
  flags.
- Shared filters require API confirmation before becoming trusted.
- Disabled modules should hide or disable filters that depend on them.
- Deleted tags, categories, statuses, users, or module states should degrade
  safely with clear messaging.
- Offline use of saved filters should apply only to local cache and should
  say when server confirmation is required.
- Admin-provided filters should explain ownership and impact before changes.

Saved filters should reduce repeated work, not obscure active constraints. The
mobile UI should make active filters easy to see, adjust, and clear.

## Filtering Principles

Filters narrow results by explicit, understandable conditions.

Filter principles:

- Filters must be permission-aware.
- Filters must be tenant-scoped.
- Filters must be feature-aware.
- Filters must be compatible with offline limitations.
- Filters should compose predictably.
- Filters should be easy to clear.
- Filters should not silently expand access.
- Filters should not reveal values the user cannot otherwise see.
- Filters should use admin-controlled vocabularies where the product defines
  tags, categories, statuses, priorities, teams, or workflows.

Common filter concepts may include:

- Module or content type.
- Record status.
- Category.
- Tag.
- Assignment or ownership context.
- Created, updated, synced, or due timing.
- Attachment presence.
- Draft, pending sync, failed sync, or conflict state.
- Archived, restored, or deleted lifecycle state where permission allows.
- Current tenant, when the user has more than one tenant.

Filtering should separate user intent from server authority. Mobile may let the
user choose filters locally, but the API decides which filters are allowed and
which matching results are visible.

## Sorting Principles

Sorting decides result order; it must be predictable enough that users can
trust what they see.

Sort principles:

- Supported sort options should be explicit.
- Default sorting should be stable and explainable.
- API search should return the accepted sort behavior.
- Local search should use the closest safe local sort when offline.
- Search relevance should not hide permission or tenant boundaries.
- Sorting should not depend on inaccessible fields.
- Sorting should not change unexpectedly because a module is disabled.
- Pagination should remain stable enough that users do not see duplicate or
  missing results during normal browsing.

Common sort concepts may include:

- Relevance when a text query is present.
- Most recently updated.
- Most recently created.
- Due or priority order where the module supports it.
- Alphabetical order for names or labels.
- Status or category order where admin-defined workflow requires it.
- Nearest local match for scanned values.

When relevance is used, users should still understand why a result appeared.
When relevance is unavailable offline, the app should prefer a simpler
deterministic local sort over pretending to have server ranking.

## Scan-To-Search Behavior

Scan-to-search lets a mobile user scan a QR code, barcode, label, asset code,
ticket code, or similar value and use it as a search query or lookup hint.

Scan-to-search principles:

- Scanning must be feature-flag controlled.
- Scanning must respect tenant, role, permission, subscription, app-version,
  maintenance, and device permission state.
- The app should explain why scanning is needed before requesting camera or
  scanner permission.
- Disabled scanning features should not request scanner or camera permission.
- A scanned value is untrusted input.
- A scanned value should be normalized and confirmed before it triggers
  sensitive behavior.
- Scanning should search only the current tenant unless API-confirmed
  tenant-switch or deep-link rules explicitly allow another path.
- Scanning should never execute arbitrary commands, admin actions, scripts, or
  hidden configuration.
- Scanning should not reveal whether a code belongs to another tenant.

Online scan-to-search:

- Mobile scans the value and asks the API for an authorized result.
- API validates the scanned value and current context.
- API returns either an allowed result, a safe no-result state, a permission
  denial, a feature-disabled state, or a user-friendly validation error.
- Mobile renders the API outcome without exposing internal matching logic.

Offline scan-to-search:

- Mobile may search safe local cache for the scanned value.
- Mobile must label local-only matches as limited or cached.
- Mobile must queue no server decision merely because a scan occurred.
- Mobile must wait for API confirmation before opening protected server-only
  content, switching tenants, resolving identity, or accepting a workflow
  action.

## Offline Search Limitations

Offline search is useful, but it is not complete.

Offline search can:

- Search safe cached content for the current tenant.
- Search local drafts and pending actions.
- Search safe locally cached tags, categories, statuses, and recent items.
- Apply saved filters to local cache when their dependencies are available.
- Preserve a query so the user can retry online.
- Show last-known results with clear stale-state messaging.

Offline search cannot:

- Find content that is not cached.
- Confirm current permissions or feature flags.
- Confirm that a tenant is still active.
- Confirm billing, suspension, deletion, or restore state.
- Confirm latest record status, conflicts, or attachment availability.
- Search server-only reports or support data.
- Resolve cross-device changes.
- Return authoritative result counts.
- Reveal data that was never allowed for offline cache.

If offline search would create false confidence, mobile should show a limited
state instead of pretending results are complete.

## Privacy And Tenant Isolation

Search is privacy-sensitive because queries can reveal intent, customer names,
codes, locations, support issues, or private workflow details.

Privacy principles:

- Search is always scoped to the active tenant context.
- Search results must be filtered before display, count, grouping, or ranking.
- Search terms should not appear in logs, diagnostics, analytics, support
  views, or audit trails unless policy explicitly allows the specific purpose.
- If search metadata is audited, audit should favor action meaning over raw
  sensitive text.
- Recent searches and saved filters must not leak between tenants.
- Local search cache must be separated by tenant.
- App lock, logout, server revocation, tenant suspension, and local storage
  clearing must protect search history and cached results.
- Support agents should see only search context required to help, not private
  query history by default.
- Data export and deletion policies must include search history, saved filters,
  and cached search data if those are retained.

External search services, if ever introduced, require a separate documented
privacy and tenant-isolation decision before implementation. That decision must
explain what data leaves the system, how tenant boundaries are enforced, how
deletion is honored, and how support/compliance reviews understand the risk.

## Feature Flags And Remote Config

Search behavior should be controlled by admin policy.

Feature flags may control:

- Whether search exists on mobile.
- Which modules are searchable.
- Whether local search is enabled.
- Whether API search is enabled.
- Whether scan-to-search is enabled.
- Whether recent searches are enabled.
- Whether saved filters are enabled.
- Whether tenant-shared filters are enabled.
- Whether archived or deleted content can be searched.
- Whether attachment metadata can appear in results.
- Whether offline search is allowed for a tenant or plan.

Remote configuration may control safe behavior such as:

- Minimum query length.
- Maximum query length.
- Debounce or wait behavior in principle.
- Default sort.
- Available sort choices.
- Available filter groups.
- Result page size.
- Recent-search retention window.
- Offline search stale-state messaging.
- Scan formats allowed by policy.

Mobile must treat missing, invalid, stale, or unsupported search config as a
reason to fall back to safe defaults or disable risky search behavior until API
confirmation returns.

## Admin Visibility And Control

Admins should understand what search affects before changing it.

Admin control principles:

- Platform admins may define global search policy.
- Tenant admins may manage tenant-scoped search behavior only when delegated.
- Admins should see which modules and users are affected by a search setting.
- Disabling a searchable module should explain the mobile impact.
- Changing filters or sort defaults should explain affected workflows.
- Enabling offline search should explain cache, privacy, and stale-data impact.
- Enabling scan-to-search should explain native permission and privacy impact.
- Shared saved filters should have clear ownership and rollback expectations.
- Dangerous search changes should be audited where they affect access,
  privacy, cache retention, or tenant visibility.

Admins should not use search controls to bypass normal permissions. Admin
search views may have broader visibility only when authorized, audited,
tenant-scoped, and clear about support/compliance purpose.

## Result States

Search should use clear states so users understand outcomes.

Recommended result states:

- Ready: search is available and waiting for input or filters.
- Searching: search is in progress.
- Results: authorized matches are available.
- No results: no authorized matches were found.
- Limited offline results: only local cache was searched.
- Too broad: the query needs filters or more detail.
- Invalid query: the query cannot be searched safely.
- Feature disabled: search or a search capability is off.
- Permission denied: the user cannot search that scope.
- Tenant unavailable: tenant state prevents search.
- Maintenance or forced update: app policy blocks search.
- Sync required: server confirmation is needed.
- Error: search failed without revealing internals.

No-result messaging must avoid confirming whether inaccessible content exists.
"Nothing available for your current access" is safer than "record exists but
you cannot view it."

## Risks

Search risks to record before implementation:

- Cross-tenant leakage through cache, recents, saved filters, counts, snippets,
  ranking, or diagnostics.
- Permission leakage through result totals, suggestions, autocomplete, or
  scan-to-search no-result differences.
- Stale offline results causing users to act on old information.
- Sensitive query terms stored too long on the device.
- Saved filters outliving the feature, role, tag, category, or status they
  depend on.
- Scan values being treated as trusted commands or tenant switches.
- Search performance creating slow mobile screens or API load.
- Relevance disagreements making users distrust the result order.
- Admin changes breaking mobile workflows without preview or rollback.

## Acceptance Questions

Before implementing any search behavior, the team should answer:

- What exact module or content type is searchable?
- Is search local, API-backed, or both?
- What can the current user discover through search?
- What must never appear in search results?
- Which filters and sorts are allowed?
- What happens when the device is offline?
- Are recent searches stored, and where?
- Are saved filters private, tenant-shared, admin-provided, or disabled?
- Does scan-to-search require native permissions?
- What feature flags or remote config control the behavior?
- What is audited, logged, retained, exported, or deleted?
- What does support see when helping a user with search?
- How does the behavior fail closed for suspended users, suspended tenants,
  forced updates, revoked sessions, and disabled features?

## Success Standard

Search is successful when users can find allowed tenant work quickly, understand
the difference between local and API-authoritative results, recover from
offline or invalid states, reuse safe searches and filters, scan when it helps,
and trust that search never leaks data across tenants, permissions, privacy
boundaries, or stale mobile cache.
