# Scanner Logic

Updated: 2026-06-26

Geolocation Logic is defined in `geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Voice Note Logic is defined in `voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

This document defines QR/barcode scanner logic for the Mobile Lara NativePHP
client. It explains scan-to-search, scan-to-create, scan-to-validate, scan
history principles, offline scanning, invalid scan behavior, duplicate scan
behavior, admin control through feature flags, and permission plus camera
dependency principles. It is documentation only and does not define database
structure, database fields, migrations, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, plugin manifests, policies,
gates, middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, queue workers, scanner providers, barcode
libraries, or application logic.

Use this document with [Product Principles](product-principles.md),
[Documentation-First Architecture](documentation-first-architecture.md),
[Two-System Boundary Logic](two-system-boundary.md), [API-First
Principles](api-first-principles.md), [Admin/API
Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Role And Permission
Logic](role-permission-logic.md), [Feature Flag Logic](feature-flag-logic.md),
[Remote Configuration Logic](remote-configuration-logic.md), [Mobile Version
Control Logic](mobile-version-control-logic.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Admin Safety
Principles](admin-safety-principles.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Mobile Permission Logic](mobile-permission-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Camera And Media
Logic](camera-media-logic.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle Logic](sync-lifecycle-logic.md),
[Conflict Resolution Logic](conflict-resolution-logic.md), [Records/Content
Module Logic](records-content-module-logic.md), [Search
Logic](search-logic.md), [Forms And Drafts Logic](forms-drafts-logic.md),
[Support System Logic](support-system-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), and [Reporting Logic](reporting-logic.md):
scanner workflows are tenant-scoped native-assisted input workflows, while
Admin/API remains authoritative for feature eligibility, code meaning, search
results, creation acceptance, validation decisions, duplicate handling,
privacy, audit, reporting, support visibility, and sync truth.

## Scanner Statement

The scanner turns a visual code into mobile input. It does not turn that input
into trusted tenant data by itself.

QR codes and barcodes are useful because they reduce typing, help field users
find records quickly, speed up create flows, and let workers validate assets,
tickets, jobs, inventory, check-ins, support cases, or tenant-specific items.
They are also risky because a scanned value may be stale, copied, malformed,
from another tenant, expired, duplicated, malicious, or too sensitive to store
raw.

Product rule: a scanned QR/barcode value is untrusted local input until the
API resolves it for the current tenant, user, feature flag, permission, plan,
app version, and sync state. Mobile may capture, parse, display, cache, queue,
or retry scanner intent, but Admin/API decides whether the scan is meaningful,
allowed, accepted, auditable, reportable, supportable, or rejected.

## Goals

Scanner logic should:

- Let users search by scanning a QR code, barcode, label, ticket, asset tag,
  job code, record code, inventory code, or tenant-specific identifier.
- Let users start create flows from a scanned code only when admin policy
  allows that workflow.
- Let users validate a scanned code against server authority or a clearly
  labeled offline cache.
- Keep scan history useful without storing unnecessary sensitive values.
- Support offline scanning where the value can be safely captured, matched
  against tenant-local cache, or queued for later API resolution.
- Explain invalid, unsupported, expired, revoked, wrong-tenant, and unreadable
  scans without leaking sensitive existence information.
- Prevent duplicate local actions and duplicate server records caused by
  repeated scans.
- Give admins feature-flag and remote-config control over scanner visibility,
  supported workflows, allowed formats, offline limits, duplicate behavior,
  manual fallback, and rollout.
- Separate camera permission, scanner capability availability, and SaaS
  authorization in mobile UX.
- Keep scanner behavior API-first, tenant-scoped, privacy-safe, auditable, and
  compatible with sync and conflict principles.

Scanner logic should not:

- Treat device camera permission as product permission.
- Treat a successful native scan as server acceptance.
- Let mobile infer cross-tenant ownership from a scanned value.
- Create records, mark validations complete, or reveal hidden records without
  API authority.
- Store raw scan values longer than policy allows.
- Log raw QR/barcode values in diagnostics, support notes, analytics, or
  reports unless explicitly documented and privacy-approved.
- Ask for camera or scanner permission when the scanner feature is disabled,
  hidden, unlicensed, unsupported, blocked by app version, blocked by tenant
  state, blocked by user permission, or unavailable in maintenance mode.
- Define scanner plugins, provider payloads, database tables, queue schemas,
  endpoints, or code in this document.

## Ownership And Authority

| Concern | Admin/API owns | Mobile client owns |
| --- | --- | --- |
| Scanner availability | Whether scanner, scan-to-search, scan-to-create, scan-to-validate, offline scanning, scan history, and manual fallback are enabled for each global rollout, tenant, plan, role, user, app version, platform, and maintenance state. | Showing only eligible scanner entry points, hiding or explaining disabled states, and avoiding native permission prompts when scanner workflows are unavailable. |
| Code meaning | Which formats, prefixes, payload types, tenants, records, external references, or validation targets are recognized. | Capturing the value, normalizing obvious local input safely, and sending or queueing scanner intent without inventing meaning. |
| Search authority | Searchable targets, permission-filtered results, result counts, privacy boundaries, and no-match behavior. | Using scanner input to start local or API search only inside the active tenant and visible feature scope. |
| Create authority | Whether a scanned value may start a create flow, prefill a draft, select a template, link an item, or create a new server record. | Showing the user what will be created, preserving drafts, and submitting through API or sync only when allowed. |
| Validation authority | Whether a code is valid, expired, revoked, already used, duplicate, wrong tenant, blocked, or accepted. | Presenting pending, preliminary, valid, invalid, duplicate, failed, or needs-review states without turning stale local checks into final truth. |
| History and audit | Authoritative scan events, validation outcomes, reportable usage, support-visible context, retention, and audit history. | Local recent-scan UX, safe clear behavior, tenant-scoped cache separation, and minimal diagnostics. |
| Offline behavior | Which scanner workflows may run offline, queue limits, replay windows, duplicate rules, conflict rules, and emergency stop behavior. | Local capture, local cache lookup, queued scanner intent, pending labels, retry UX, and cleanup when tenant/session policy requires. |
| Privacy and security | Data classification, storage policy, log policy, support visibility, diagnostics limits, rate limits, abuse handling, and tenant isolation. | Treating scan values as untrusted, minimizing local storage, avoiding raw-value exposure, and helping users recover safely. |

The mobile client makes scanning feel fast. Admin/API decides what the scan
means.

## Scanner Capability Model

Scanner capability is a native-assisted capture capability, usually dependent
on camera hardware, camera permission, supported platform behavior, and an
available scanner plugin or fallback.

Capability principles:

- Scanner availability should be resolved from API context, feature flags,
  app-version rules, tenant state, plan entitlement, device support, and native
  permission status.
- Native scan completion should be treated as an asynchronous result that may
  arrive after the user navigates, cancels, loses connectivity, switches
  tenant, locks the app, or loses permission.
- A scanner result should be translated into stable product outcomes such as
  canceled, unreadable, unsupported format, permission denied, disabled by
  admin, blocked by plan, requires update, offline pending, found, not found,
  duplicate, valid, invalid, expired, revoked, wrong tenant, needs review,
  accepted, or rejected.
- Browser and development fallback may support manual entry, mock scan values,
  or disabled scanner states, but must not pretend to prove native camera or
  scanner behavior.
- Scanner flows should work from clear entry points instead of hidden global
  camera access.
- The scanner should never be the only recovery path where manual entry is
  allowed by product policy.
- Scanner UX should remain simple: scan, show what was read, show what it can
  do, and ask for confirmation before a write-like action.

The product should document scanner behavior by workflow, not by plugin. "Scan
record label to search" and "scan ticket to validate entry" are product
workflows. The scanner plugin is an implementation detail.

## Scan-To-Search

Scan-to-search uses a scanned value to find allowed tenant content faster than
typing.

Scan-to-search principles:

- Scan-to-search should be available only when search, scanner, the target
  module, current tenant, user permission, subscription, app version, and
  platform support allow it.
- The scanned value should become a search input, identifier lookup, or filter
  suggestion. It should not bypass search authorization.
- API search is authoritative when online. Local search may help offline, but
  it can search only safe cached tenant data and should label results as cached
  or stale where useful.
- A scan must not reveal whether a hidden, cross-tenant, disabled, archived,
  restricted, or unauthorized item exists.
- No-match behavior should be user-friendly but privacy-safe. It may say that
  no available result was found, not that a protected item exists elsewhere.
- Scan-to-search should preserve the scanned value only as long as needed for
  the search, recent-scan policy, or support-safe troubleshooting.
- Scan-to-search should allow manual correction or manual entry where the
  product allows it.
- Repeated scans of the same value should not create duplicate recent-search
  rows or duplicate noisy activity.
- Search filters, sorting, saved filters, and recent searches still follow the
  search logic document.

When local and API results disagree, API results win. Mobile can explain that
offline results were limited and refreshed after reconnection.

## Scan-To-Create

Scan-to-create uses a scanned value to start or prefill a creation workflow.

Scan-to-create principles:

- Scan-to-create should be enabled only for workflows where admins explicitly
  allow scanner-assisted creation.
- A scan may prefill a draft, choose a record type, link a known external code,
  select a tenant-safe template, attach a label to a new item, or start a
  support/request flow.
- Mobile should show what will be created or prefilled before the user submits.
- The user should confirm write-like outcomes. A scan alone should not silently
  create server content.
- The API owns final create acceptance, validation, duplicate detection,
  permission checks, plan limits, audit meaning, and conflict decisions.
- Offline scan-to-create may create a local draft or queued create intent only
  when offline policy allows it.
- A queued create intent should stay clearly pending until API acknowledgement.
- If a scanned code already belongs to an existing item, the duplicate behavior
  should guide the user to open, link, merge, retry, or abandon according to
  policy. Mobile should not invent a duplicate record.
- If the target module is disabled, plan-blocked, permission-blocked, or
  maintenance-blocked, scan-to-create should not request camera permission and
  should not preserve a create intent.

Scan-to-create is a convenience for entering data. It is not a shortcut around
forms, validation, permissions, billing, or API acceptance.

## Scan-To-Validate

Scan-to-validate uses a scanned value to check whether an item, ticket, asset,
job, inventory object, record, credential, or tenant-specific code is valid for
a specific purpose.

Scan-to-validate principles:

- Validation purpose must be clear before scanning. The same code may have
  different meaning for search, create, check-in, access, inventory, support,
  or audit workflows.
- Admin/API decides whether validation is allowed, which codes are valid, how
  stale data is handled, and what outcome should be recorded.
- Online validation should return mobile-safe outcomes such as accepted,
  rejected, expired, revoked, already used, duplicate, wrong tenant, not
  available, needs review, permission denied, feature disabled, or retry later.
- Offline validation should be clearly labeled as local, cached, preliminary,
  or pending unless the documented workflow permits final offline decisions.
- A stale positive local match should not be presented as final server success.
- A stale negative local miss should not permanently reject a user or item when
  the API has not been reached.
- Validation should be idempotent where repeated scans can happen. Replaying a
  validation should not double-count, double-charge, double-check-in, or
  double-complete work.
- Validation failures should be calm, specific enough to guide the user, and
  privacy-safe enough to avoid revealing protected records.
- Sensitive validation screens should respect app lock and session revocation.

Scan-to-validate is often higher risk than scan-to-search. It should have
stronger audit, retry, duplicate, and offline rules before implementation.

## Scan History Principles

Scan history can make repeated work faster, but it can also expose sensitive
values. History should therefore be minimal, scoped, and purposeful.

History principles:

- Local scan history is a mobile UX aid, not the authoritative audit trail.
- Authoritative scan events, validation outcomes, and reportable usage belong
  to Admin/API when the API accepts or records them.
- Local history should be scoped to the current user, tenant, device, app lock,
  session, and workflow.
- Local history should not appear across tenants.
- Local history should clear, hide, or become inaccessible on logout,
  logout-all-devices, tenant switch, app lock, session revocation, tenant
  suspension, user suspension, forced update, or privacy retention policy where
  required.
- History should store the minimum useful representation: workflow, safe label,
  type/category, status, and time-like meaning. Raw scan values should be
  avoided when a safe label, masked value, hash-like reference, or server
  identifier is enough.
- Users should be able to clear local history where policy allows.
- Support diagnostics should describe scanner state and recent outcome classes
  without exposing raw values by default.
- Duplicate local history should collapse repeated scans where useful.
- Failed, invalid, and denied scans should not be retained longer than needed
  for user recovery or privacy-approved diagnostics.

The safest scan history is useful enough to help the user continue work and
small enough that losing the device does not expose tenant secrets.

## Offline Scanning

Offline scanning lets users keep moving when the network is unavailable, but it
must make the difference between local work and server truth obvious.

Offline scanning may allow:

- Capturing a scan value for later resolution.
- Matching a value against safe current-tenant local cache.
- Opening a cached result that the user was already allowed to view.
- Prefilling a local draft from the scanned value.
- Creating a queued scan-to-create or scan-to-validate intent when policy
  allows it.
- Adding a pending history item with a clear offline state.
- Retrying queued scanner intent when connectivity returns.

Offline scanning must wait for online API access when:

- The scan requires permission, tenant, subscription, version, or feature
  confirmation that is not safely cached.
- The scan would create, update, validate, archive, delete, charge, complete,
  check in, or otherwise change authoritative server state.
- The scan needs current uniqueness or duplicate resolution.
- The scan targets a protected record not available in local cache.
- The scan could reveal sensitive existence information.
- The tenant or user state is suspended, revoked, unknown, or too stale.

Offline UX principles:

- Offline scanner states should say what is saved locally, what is pending, and
  what still needs sync.
- Pending scanner actions should be visible in sync status where relevant.
- Users should be able to cancel queued scanner actions before upload when
  policy allows.
- If sync later rejects an offline scan, mobile should preserve enough local
  context for correction without implying the rejected action succeeded.
- Admin controls should define offline limits, replay windows, allowed scanner
  workflows, duplicate policy, and emergency disablement behavior.

Offline scanning is a resilience feature. It should not become an alternative
authority path.

## Invalid Scan Behavior

Invalid scans are normal. The app should make recovery clear without blaming
the user or exposing sensitive system details.

Invalid scan categories include:

- Unreadable image or camera failure.
- Unsupported code type.
- Malformed payload.
- Unknown prefix or unsupported workflow.
- Wrong tenant or unavailable tenant context.
- Expired, revoked, already used, blocked, or disabled code.
- No available result for the current user.
- Code exists but is not visible to the current user.
- Feature disabled by admin.
- Plan or subscription limit reached.
- App version too old.
- Maintenance mode or force update.
- Offline and cannot resolve.
- Rate limited or abuse-protected state.
- Tampered, suspicious, or unsafe content.

Invalid scan principles:

- The message should explain the next safe action: retry, clean the camera,
  enter manually, change tenant, go online, update the app, contact support, or
  ask an admin.
- The app should preserve manual entry when the product allows it.
- The app should not reveal whether a hidden item exists in another tenant or
  behind a permission boundary.
- The app should avoid raw technical payload display unless the user explicitly
  needs it and policy allows it.
- Suspicious scans should avoid local history retention except for safe audit
  or support-approved summaries.
- Repeated invalid scans may trigger rate limits, calmer messaging, or support
  guidance through API policy.
- Invalid scan handling should be consistent across search, create, and
  validate flows, with stronger controls for write-like or security-sensitive
  validation.

An invalid scan state should feel like a recoverable workflow problem, not an
app crash.

## Duplicate Scan Behavior

Duplicate scans can happen because users scan the same label repeatedly, the
camera emits multiple events, offline queues replay, or multiple users scan the
same item.

Duplicate categories include:

- Same value scanned multiple times in one scanner session.
- Same value scanned again from local recent history.
- Same scan-to-create draft already exists locally.
- Same queued scanner intent is already pending.
- Same server record already exists.
- Same validation was already accepted.
- Same validation is already pending from another device or user.
- Same value maps to multiple allowed records and needs user choice.

Duplicate principles:

- Mobile should collapse accidental repeated native events in the same session
  where the product meaning is identical.
- Mobile should not create duplicate drafts or queued writes without an
  explicit user choice and policy allowance.
- API decides whether a duplicate is harmless, blocked, mergeable, needs user
  choice, or needs admin/support review.
- Duplicate scan-to-search should usually open or refresh the existing result
  instead of creating noise.
- Duplicate scan-to-create should guide the user toward an existing draft,
  existing record, merge/link option, or explicit "create another" path only if
  allowed.
- Duplicate scan-to-validate should be idempotent and should not double-count
  a validation outcome.
- Offline duplicates should reconcile after sync, and any conflict should be
  shown through sync/conflict UX.
- Duplicate decisions that affect server truth should be audit-safe.

The user should never have to guess whether scanning twice created two server
actions.

## Admin Control Through Feature Flags

Scanner behavior should be controlled by feature flags and remote config
because scanner workflows can affect permissions, privacy, billing, support,
sync, reports, and operational risk.

Admin controls may govern:

- Scanner capability visibility.
- Scan-to-search availability.
- Scan-to-create availability.
- Scan-to-validate availability.
- Supported code types and workflow categories.
- Allowed tenant modules, record types, support categories, or validation
  purposes.
- Manual entry fallback.
- Offline scanning enablement.
- Offline queue limits and replay windows.
- Local scan history enablement and retention.
- Duplicate handling policy.
- Invalid scan messaging and support guidance.
- Required app version or platform support.
- Plan limits and entitlement ceilings.
- Role, permission, user, cohort, tenant, maintenance, or emergency overrides.
- Rate limiting or abuse-protection behavior.
- Reporting and audit visibility.

Admin UX principles:

- Admins should understand which mobile screens, permission prompts, offline
  queues, sync behavior, reports, support flows, and plan messages change when
  scanner flags change.
- Dangerous scanner changes should show impact before saving.
- Tenant-specific scanner changes should not affect other tenants.
- Emergency disablement should stop new scans, preserve pending safe local work
  according to policy, and explain what happens to queued scanner actions.
- Rollout should support global, tenant, cohort, role, and user-level control
  without letting mobile bypass API authority.

Feature flags make scanner rollout reversible and observable.

## Permission And Camera Dependency Principles

Scanner behavior depends on device capability, but device capability is not
SaaS permission.

Permission principles:

- Scanner workflows should explain why scanning is needed before the camera or
  scanner permission prompt appears.
- Camera permission should be requested just in time, when the user starts an
  enabled scanner workflow.
- Disabled scanner features should not request camera or scanner permission.
- The settings screen should show scanner feature state separately from camera
  permission state.
- A user may grant camera permission and still be blocked by tenant, role,
  feature flag, plan, app version, maintenance, or server revocation.
- A user may have scanner SaaS permission while device camera permission is
  denied, unavailable, permanently denied, or unsupported.
- Permission denial should offer recovery: retry explanation, open settings,
  use manual entry, use search, contact support, or ask an admin, depending on
  policy.
- Browser/development fallback should not ask for unavailable native scanner
  permission and should not be treated as proof of production scanning.
- Camera failure, low light, focus failure, plugin failure, revoked permission,
  and unavailable hardware should produce distinct user-safe states when useful.
- Scanner history and diagnostics should not store raw camera frames or raw
  scan values unless a future documented policy explicitly allows it.

Permission education should make scanning feel intentional. It should not feel
like the app is asking for camera access because the plugin exists.

## API And Sync Principles

Scanner workflows should use API-first communication for anything
authoritative.

API and sync principles:

- Mobile sends scanner intent through API or sync. It does not invent server
  meaning.
- API responses should be predictable and mobile-safe for found, not found,
  invalid, duplicate, permission denied, feature disabled, plan blocked,
  version blocked, maintenance, offline pending, conflict, accepted, rejected,
  and needs-review states.
- API errors should preserve user recovery without exposing raw internals.
- Scanner actions that can be replayed should be idempotent at the API
  boundary.
- Offline queued scanner actions should include enough context for safe
  reconciliation without trusting stale mobile authority.
- Conflict decisions belong to Admin/API, with mobile presenting the outcome
  and preserving user work where possible.
- Accepted scanner outcomes that matter to compliance, security, support,
  billing, reporting, or operations should be audit-safe.
- Tenant boundaries must be enforced before resolving, searching, creating, or
  validating a scanned value.

Scanner API behavior can be designed later. The principle is fixed now: scanner
input reaches authority only through API-controlled contracts.

## Security, Privacy, And Tenant Isolation

Scanned codes may contain identifiers, tokens, URLs, personal data, secrets, or
tenant-specific values. Treat them as sensitive until classified.

Security and privacy principles:

- Treat every scanned value as untrusted input.
- Do not execute scanned URLs or commands automatically.
- Do not use scanned values as tenant authority.
- Do not expose raw scanned values in logs, reports, support diagnostics,
  analytics, crash output, or audit summaries unless a documented privacy rule
  allows it.
- Do not keep scan values across tenants.
- Do not retain raw values after logout, revocation, app lock, retention
  expiry, or tenant switch when policy forbids it.
- Protect offline scan cache through local storage and app lock rules.
- Limit no-match and invalid messaging so it cannot be used to enumerate
  records or tenants.
- Apply rate limits or abuse controls when scan attempts become suspicious or
  excessive.
- Keep support access least-privilege. Support should see outcome class and
  tenant context before raw values, and raw values only when policy explicitly
  allows.

Scanner UX is fast, but scanner data still deserves the same tenant and privacy
boundaries as manually typed data.

## Reporting, Support, And Audit

Scanner reporting should explain feature health and workflow value without
turning raw scan values into broad analytics data.

Reporting and support principles:

- Admin reports may show scanner usage, adoption, failure categories,
  duplicate categories, offline pending counts, validation outcomes, and sync
  health when tenant and privacy rules allow.
- Tenant admins should see scanner information only for their tenant and only
  where their role permits it.
- Mobile users may see their own recent scan state, pending actions, failures,
  and successful outcomes where useful.
- Support agents should see enough safe scanner context to diagnose feature
  problems: feature enabled state, app version, permission state, device class,
  offline status, outcome category, and tenant context.
- Support should not see private raw values, camera frames, or unrelated tenant
  data by default.
- Audit history should help answer who scanned, for what workflow, in which
  tenant, what outcome occurred, whether it was offline, whether it replayed,
  whether it was duplicate, and what authority accepted or rejected it.
- Audit records should protect sensitive values through minimization, masking,
  or controlled access.

Scanner data should help operators improve the product without becoming a
privacy leak.

## Risks

Scanner implementation should not begin until these risks are documented for
the target workflow:

- Camera permission confusion: users may grant camera access but still lack
  product permission.
- Cross-tenant leakage: a code from one tenant may be scanned inside another
  tenant context.
- Sensitive payload exposure: QR codes may include secrets, tokens, URLs, or
  personal data.
- Duplicate writes: repeated camera events or offline replay may create
  duplicate records or validations.
- Stale offline truth: cached validation may be wrong after server changes.
- Enumeration: invalid or no-match messaging may reveal protected content.
- App-version drift: old clients may parse or submit scanner payloads
  incorrectly.
- Support overexposure: diagnostics may accidentally include raw scan values.
- Plugin variance: scanner behavior may differ by platform, device, lighting,
  camera quality, or plugin version.
- Recovery gaps: users may be stuck if camera permission is denied and manual
  entry is not available.

Each scanner workflow should define its own risk controls before code is
written.

## Implementation Readiness Checklist

Before implementing a scanner workflow, documentation should answer:

- What problem does this scanner workflow solve?
- Is the workflow scan-to-search, scan-to-create, scan-to-validate, or another
  documented scanner purpose?
- Which admin feature flags, remote config values, plan limits, permissions,
  roles, app versions, platforms, and tenant states control it?
- What does the mobile app show before requesting camera permission?
- What happens when scanner, camera, permission, API, or sync is unavailable?
- What is the manual fallback, if any?
- What is safe to cache locally?
- What must never be cached?
- What can happen offline?
- What must wait for online API authority?
- How are invalid, no-match, wrong-tenant, expired, revoked, and unsupported
  scans shown?
- How are duplicate scans prevented, merged, blocked, or reviewed?
- What scan history is stored, for how long, and who can clear it?
- What support diagnostics are safe?
- What audit and reporting questions should the scanner workflow answer?
- How is user work protected if the app locks, tenant switches, user logs out,
  session is revoked, or app version becomes unsupported?

If a scanner workflow cannot answer those questions in documentation, it is not
ready for implementation.
