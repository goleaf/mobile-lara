# Changelog

Final Consistency Review is defined in `docs/final-consistency-review.md`:
all SaaS idea documentation must preserve API-only mobile authority,
admin-controlled configurable features, separated feature flags and remote
config, tenant isolation, clear offline behavior, permission-aware
NativePHP features, logical billing and plan limits, privacy-safe support,
tenant-bound reports, docs-only planning language, no database-field
definitions, and consistent terminology.

Final Optimized SaaS Blueprint is defined in `docs/final-optimized-saas-blueprint.md`:
this is the main planning document for product vision, system architecture,
Admin/API logic, mobile-client logic, API principles, tenant principles,
permissions, feature flags, remote config, offline sync, NativePHP features,
notifications, billing, support, reporting, security, release,
and future module expansion principles.

All notable changes to Mobile Lara will be documented in this file.

Scanner Logic is defined in `docs/scanner-logic.md`:
QR/barcode scan-to-search, scan-to-create, scan-to-validate, scan history,
offline scanning, invalid scan handling, duplicate scan handling, admin
feature flags, and camera/permission dependency behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear,
duplicate-safe, privacy-safe, and Admin/API-authoritative.

Geolocation Logic is defined in `docs/geolocation-logic.md`:
check-ins, location-attached records, accuracy display, permission
explanation, offline location behavior, privacy boundaries, admin feature
flags, user-facing location understanding, and never-collect rules must
remain tenant-scoped, permission-aware, fallback-safe, offline-clear,
privacy-safe, purpose-limited, and Admin/API-authoritative.

Device, Network, And Diagnostics Logic is defined in `docs/device-network-diagnostics-logic.md`:
device information use, network status use, offline detection, diagnostics
export, support troubleshooting context, diagnostics redaction, admin mobile
device visibility, and user-controlled diagnostics sharing must remain
tenant-scoped, permission-aware, support-scoped, privacy-safe, redacted,
audit-ready, and Admin/API-authoritative.

Module Selection Principles are defined in `docs/module-selection-principles.md`:
optional industry modules such as field service, logistics, ecommerce,
booking, education, events, support, community/messaging, reports, and AI
assistant must be tenant-enabled, plan-controlled, permission-aware,
mobile-hidden when unavailable, feature-flag-safe, documented before
implementation, and Admin/API-authoritative.

Field Service Logic is defined in `docs/field-service-logic.md`:
work order lifecycle, technician mobile flow, check-in/check-out, photos,
notes, future signatures, offline behavior, admin dispatch/control, and
report visibility must remain tenant-enabled, plan-controlled,
permission-aware, offline-clear, evidence-safe, privacy-safe, auditable,
and Admin/API-authoritative.

Booking Logic is defined in `docs/booking-logic.md`:
service selection, availability logic, booking requests, confirmation,
cancellation, reschedule, reminders, admin schedule control, tenant rules,
and mobile offline limitations must remain tenant-enabled, plan-controlled,
permission-aware, availability-safe, schedule-conflict-safe, reminder-safe,
offline-limited, privacy-safe, auditable, and Admin/API-authoritative.

Commerce Logic is defined in `docs/commerce-logic.md`:
catalog browsing, cart behavior, checkout principles, hosted payment
boundaries, order lifecycle, invoice/receipt principles, subscription
upsell, admin product/control, and mobile offline limitations must remain
tenant-enabled, plan-controlled, permission-aware, price-safe,
inventory-safe, hosted-payment-safe, offline-limited, privacy-safe,
auditable, and Admin/API-authoritative.

Messaging And Community Logic is defined in `docs/messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `docs/ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

Acceptance Principles are defined in `docs/acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `docs/risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Testing Strategy Principles are defined in `docs/testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Release And Versioning Principles are defined in `docs/release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `docs/documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Feature Dependency Map is defined in `docs/feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

Logistics Delivery Logic is defined in `docs/logistics-delivery-logic.md`:
delivery job lifecycle, pickup flow, drop-off flow, proof of delivery,
scan validation, location check-in, failed delivery reasons, offline
behavior, and admin monitoring must remain tenant-enabled, plan-controlled,
permission-aware, scan-safe, location-purpose-limited, offline-clear,
privacy-safe, auditable, and Admin/API-authoritative.

Voice Note Logic is defined in `docs/voice-note-logic.md`:
recording, pausing, resuming, local saving, record/support attachments,
optional future transcription, offline upload queues, microphone-permission
denial, admin feature flags, privacy, and retention behavior must remain
tenant-scoped, permission-aware, fallback-safe, offline-clear, queue-safe,
privacy-safe, retention-aware, and Admin/API-authoritative.

## Unreleased

### Added

- Added Final Consistency Review documentation for API-only mobile authority,
  admin-controlled configurable features, feature-flag and remote-config
  separation, tenant isolation, offline behavior, NativePHP permission
  awareness, billing/plan limits, privacy-safe support, tenant-safe reports,
  docs-only language, no database-field definitions, and terminology.
- Added Final Optimized SaaS Blueprint as the main planning document for
  product vision, system architecture, Admin/API logic, mobile-client logic,
  API principles, tenant principles, permissions, feature flags, remote config,
  offline sync, NativePHP features, notifications, billing, support,
  reporting, security, release, and future module expansion.
- Added Feature Dependency Map documentation for major feature dependencies on
  authentication, tenant context, permissions, feature flags, remote config,
  API availability, offline cache, NativePHP permissions, subscription plan,
  and admin settings.
- Added Documentation Audit documentation for consistent two-system
  architecture, Admin/API authority, mobile-client execution, API-first
  behavior, feature flags, remote config, tenancy, permissions, offline sync,
  NativePHP feature principles, notifications, billing, support, reports,
  security, risks, release principles, and contradiction-resolution rules.
- Added Release And Versioning Principles documentation for API versioning,
  mobile app versioning, admin release process, feature rollout, rollback,
  app store release, forced update, documentation update requirements, Git
  commit discipline, and change-history principles.
- Added Testing Strategy Principles documentation for future API contract,
  admin control, mobile feature visibility, permission, feature flag, remote
  config, authentication, tenant isolation, offline sync, conflict behavior,
  NativePHP fallback, notification, billing, and app-version test coverage.
- Added Risk Map documentation for API dependency, offline sync, tenant
  isolation, mobile secure storage, NativePHP plugin availability, app store
  releases, forced updates, feature flag mistakes, billing restrictions, admin
  misconfiguration, support access, privacy, data conflicts, prevention
  principles, and documentation requirements.
- Added Acceptance Principles documentation for project-wide feature readiness
  across purpose, admin control, mobile behavior, API dependency, offline
  behavior, permission behavior, feature flags, tenant behavior, error
  behavior, security behavior, documentation requirements, review flow, and
  acceptance matrix.
- Added AI Feature Logic documentation for optional future AI assistant
  purpose, summarization, categorization, smart suggestions, moderation
  assistance, report generation assistance, admin AI controls, tenant opt-in,
  privacy, human-review principles, risks, and readiness checks.
- Added Messaging And Community Logic documentation for conversation behavior,
  support chat behavior, message attachments, moderation, reports/abuse flow,
  notification behavior, offline message drafts, admin visibility boundaries,
  privacy principles, risks, and readiness checks.
- Added Commerce Logic documentation for catalog browsing, cart behavior,
  checkout principles, hosted payment boundaries, order lifecycle,
  invoice/receipt principles, subscription upsell principles, admin
  product/control principles, mobile offline limitations, privacy, risks, and
  readiness checks.
- Added Booking Logic documentation for service selection, availability logic,
  booking requests, confirmations, cancellations, reschedules, reminders,
  admin schedule control, tenant rules, mobile offline limitations, privacy,
  risks, and readiness checks.
- Added Logistics Delivery Logic documentation for delivery job lifecycle,
  pickup flow, drop-off flow, proof of delivery, scan validation, location
  check-in, failed delivery reasons, offline behavior, admin monitoring,
  privacy, risks, and readiness checks.
- Added Field Service Logic documentation for work order lifecycle,
  technician mobile flow, check-in/check-out, photos, notes, future
  signatures, offline behavior, admin dispatch/control principles, report
  visibility, privacy, risks, and readiness checks.
- Added Module Selection Principles documentation for optional industry
  modules, tenant enablement, plan control, mobile unavailable states,
  documentation-before-implementation rules, module-specific risks, rollout,
  rollback, and retirement principles.
- Added Device, Network, And Diagnostics Logic documentation for device
  information use, network status use, offline detection, diagnostics export,
  support troubleshooting context, diagnostics redaction, admin mobile-device
  visibility, and user-controlled diagnostics sharing.
- Added Voice Note Logic documentation for recording, pausing, resuming, local
  saving, record/support attachments, optional future transcription, offline
  upload queues, microphone-permission denial, admin feature-flag control,
  privacy boundaries, and retention principles.
- Added Geolocation Logic documentation for check-ins, location-attached
  records, accuracy display, permission explanation, offline location behavior,
  privacy boundaries, admin feature-flag control, user-facing location
  understanding, and never-collect rules.
- Added Scanner Logic documentation for QR/barcode scan-to-search,
  scan-to-create, scan-to-validate, scan history, offline scanning, invalid
  scan behavior, duplicate scan behavior, admin feature-flag control, and
  camera permission dependency principles.
- Added Camera And Media Logic documentation for taking photos, choosing media,
  previewing media, attaching media to records or support, offline media
  storage, upload queues, admin feature-flag control, permission denial
  behavior, media size limits, and privacy-safe tenant boundaries.
- Added Native Feature Strategy documentation for NativePHP capability
  boundaries, logical service wrapping principles, browser/development
  fallbacks, permission education, admin feature-flag control, native failure
  UX, native diagnostics, and offline sync behavior.
- Added Reporting Logic documentation for admin measurements, tenant-admin
  measurements, mobile-visible summaries, report privacy boundaries, date
  ranges, exports, feature usage reporting, sync health reporting,
  notification reporting, support reporting, billing reporting, and
  Admin/API-authoritative reporting boundaries.
- Added Billing And Plan Logic documentation for plan-based access, trial
  behavior, active/expired/suspended subscription states, plan limits,
  feature-flag entitlement ceilings, mobile unavailable-feature states, manual
  admin billing controls, and provider-neutral billing boundaries.
- Added Support System Logic documentation for mobile support requests,
  admin/support review, support messages, attachments, tenant context, support
  access limits, audit, notifications, and offline support drafts.
- Added mobile API-derived access policy that reads cached bootstrap feature,
  permission, subscription, maintenance, notification, and sync outcomes to
  gate primary navigation, dashboard/create/search shortcuts, and direct module
  routes for records, notifications, sync conflicts, NativePHP media/files,
  scanner, and location screens.
- Added API-derived policy checks to the mobile permissions center so disabled
  NativePHP features show blocked states and do not request camera,
  microphone, location, notification, file, or biometric prompts.
- Added API-derived policy checks to mobile record create, update, archive,
  restore, delete, and bulk mutation actions so denied cached bootstrap
  permissions hide local controls and stop direct Livewire calls before SQLite
  writes.
- Added API-derived policy checks to record attachment management and sharing
  so disabled attachment permissions or native share features hide controls and
  stop direct Livewire calls before local writes or native handoff.
- Added API-derived policy checks to mobile voice-note recording, native
  microphone callbacks, local save/delete mutations, and upload queue
  placeholders so disabled microphone or sync policy stops direct Livewire
  calls before local SQLite writes, file deletes, or offline queue writes.
- Added API-derived policy checks to NativePHP location permission/check-in
  actions, location callbacks, check-in creation forms, and check-in history
  shortcuts so disabled location or sync policy stops direct Livewire calls
  before native location handoff or local check-in writes.
- Added API-derived policy checks to mobile media capture and file manager
  actions so disabled camera, file, or share features hide controls and stop
  direct Livewire calls before native media handoff, local file writes, file
  deletes, exports, imports, or native share handoff.
- Added API-derived policy checks to profile, record-detail, and media-gallery
  share actions so disabled native share policy hides user-facing controls and
  blocks direct Livewire calls before native share handoff.
- Added API-derived policy checks to scanner capture and saved scan-history
  mutations so disabled scanner policy hides scan controls and stops direct
  Livewire calls before local scan-history writes or deletes.
- Added API-derived policy checks to the mobile notification inbox so disabled
  notification policy hides cached inbox rows and stops direct Livewire calls
  before local read/open/read-all mutations.
- Added API-derived policy checks to manual sync and conflict resolution so
  disabled sync policy hides resolution controls and stops direct Livewire
  calls before local sync timestamps or conflict queue statuses change.
- Added cached sync-policy enforcement inside the offline-first queue service
  so lower-level queue calls cannot write replay intents when offline sync is
  disabled for the current workspace.
- Added API-derived policy checks to the developer debug native-action surface
  so disabled camera, share, notification, browser, device, dialog, or secure
  storage policy hides debug controls and blocks direct wrapper calls/callbacks.
- Added a policy-gated support-center browser action to mobile support
  settings so disabled native browser policy hides the recovery control and
  blocks direct Livewire calls before native browser handoff.
- Added audited Admin/API tenant management for platform admins, including
  tenant create/update controls for lifecycle status, subscription state, slug,
  and JSON settings.
- Added audited Admin/API tenant membership management so platform admins can
  assign existing users to tenant roles, rotate current tenant context, and
  review recent role/status changes from the tenant management screen.
- Added authenticated mobile tenant invitation endpoints for listing pending
  invitations, accepting into an active tenant membership, declining into a
  fail-closed membership state, returning refreshed tenant context, and
  auditing success or failed response attempts.
- Added mobile workspace invitation controls so the NativePHP client can check
  pending invitations, accept or decline through the API only, refresh
  bootstrap after server confirmation, and keep invitation responses out of
  offline-local authority.
- Added Notifications Logic documentation for admin-created notifications,
  system notifications, security notifications, reminder notifications, push
  principles, in-app inbox, read/unread behavior, deep links, preferences,
  offline behavior, and tenant or permission boundaries.
- Added Forms And Drafts Logic documentation for simple forms, multi-step
  forms, validation, autosave, offline drafts, online/offline submit behavior,
  submit feedback, admin-controlled form availability, and data-loss
  prevention.
- Added Search Logic documentation for local search, API search, recent
  searches, saved filters, filtering, sorting, scan-to-search, offline search
  limitations, privacy, tenant isolation, admin controls, feature flags, and
  safe result states.
- Added Records/Content Module Logic documentation for record meaning,
  lifecycle actions, notes, attachments, activity, tags, categories, status,
  offline behavior, sync requirements, admin controls, permissions, feature
  flags, audit, reporting, and support boundaries.
- Added Offline UX Logic documentation for offline banners, pending action
  indicators, disabled online-only actions, local drafts, retry behavior, sync
  success/failure feedback, saved-local versus synced status, and calm
  connection-loss behavior.
- Added Conflict Resolution Logic documentation for conflict causes,
  auto-resolution, user-choice recovery, admin/support review, mobile conflict
  UX, admin monitoring, audit, and data-loss prevention.
- Added Sync Lifecycle Logic documentation for bootstrap sync, pull changes,
  push local changes, retries, conflict detection, conflict resolution,
  acknowledgement, sync status communication, manual sync, background sync,
  and admin monitoring of sync health.
- Added Offline-First Principles documentation for offline mobile capability,
  online-only API authority, cache rules, never-cache rules, offline state
  messaging, queued action logic, pending-change UX, and admin-controlled
  offline limits.
- Added Multi-Tenant Mobile Logic documentation for multiple-tenant choice,
  remembered current tenant context, safe tenant switching, tenant-scoped cache,
  per-tenant permissions and feature flags, sync after switch, offline behavior,
  and logout tenant cleanup.
- Added Tenant Admin Logic documentation for tenant-scoped controls,
  platform-only boundaries, invitations, delegated mobile-feature management,
  reports, support, admin safety, and cross-tenant isolation.
- Added Tenant Lifecycle Logic documentation for tenant creation, onboarding,
  trial, active, suspended, archived, billing-blocked, deletion/requested
  deletion, restore, and mobile-visible tenant state behavior.
- Added Data Privacy Principles documentation for tenant isolation, least
  privilege, secure local mobile data, secure native storage, exports,
  deletion, support access, admin visibility, privacy-by-default behavior, and
  mobile diagnostics privacy limits.
- Added Audit Logic documentation for audited admin actions, security events,
  support and compliance history, mobile activity representation, audit
  questions, and protected audit data principles.
- Added Role And Permission Logic documentation for platform-level,
  tenant-level, admin-user, and mobile-user permissions, API access, mobile UI
  visibility, feature flag interaction, and suspended user or tenant behavior.
- Added Mobile App Lock Principles documentation for lock timing,
  sensitive-area confirmation, biometric unlock, PIN unlock, repeated failed
  unlock handling, logout behavior, admin-disabled biometrics, and offline
  cached-data protection.
- Added Authentication Principles documentation for API-only mobile login,
  secure token handling, refresh sessions, logout, logout-all-devices, tenant
  selection after login, session expiry, offline authenticated behavior, and
  server revocation.
- Added Mobile Permission Logic documentation for pre-permission education,
  camera, microphone, location, notifications, files, scanner, biometrics,
  secure storage, disabled-feature prompts, denied-permission recovery,
  feature flag effects, and settings status.
- Added Mobile Settings Logic documentation for account, tenant, security,
  notifications, sync, appearance, permissions, storage, support, legal, and
  diagnostics sections, including local control, Admin/API authority, and
  offline-disabled behavior.
- Added Mobile Dashboard Logic documentation for current user context, current
  tenant, enabled feature shortcuts, sync/offline status, unread notifications,
  recent activity, announcements, quick actions, and state-based dashboard
  changes.
- Added Mobile App Shell Logic documentation for welcome, authenticated,
  locked, offline, maintenance, forced update, tenant switching,
  sync-in-progress, permission-blocked, and feature-disabled NativePHP shell
  states.
- Added Mobile UX Principles documentation for mobile-first navigation, simple
  screens, loading/offline states, thumb-friendly controls, minimum typing,
  fast actions, admin-rule-based feature visibility, secure session behavior,
  and native permission education.
- Added Admin Safety Principles documentation for dangerous admin actions,
  confirmations, audit history, impact previews, mobile impact previews,
  rollback, and tenant-isolated changes.
- Clarified Mobile Version Control Logic with a decision contract for minimum
  supported versions, optional updates, forced updates, maintenance mode,
  outdated API responses, store links, update messages, and broken old-version
  protection.
- Clarified Remote Configuration Logic with a decision contract for
  configurable behavior, mobile receive/cache rules, offline handling,
  tenant overrides, safe admin changes, and missing or invalid config.
- Clarified Feature Flag Logic with a decision contract for controlled mobile
  feature availability, global/tenant/user priority, disabled mobile states,
  admin impact, safe rollout, and plan-limit behavior.
- Clarified Admin Control Center Logic with a control ownership contract for
  tenants, users, roles, permissions, mobile features, remote config, app
  versions, maintenance mode, force update, sync behavior, notifications,
  reports, billing, and support.
- Clarified Documentation-First Architecture with a documentation-first contract
  for pre-implementation feature decisions, admin mobile effects, mobile screen
  API dependencies, sync online/offline behavior, permission ownership, and
  risk records.
- Clarified API-First Principles with an API-first contract for API-only mobile
  communication, predictable responses, explicit mobile-feature API purpose,
  permissions/feature/config/version/user context, mobile-friendly errors,
  sync/conflict behavior, and tenant-boundary protection.
- Clarified Mobile Client Responsibilities with a mobile ownership contract for
  mobile UX, secure local session, cache, offline actions, NativePHP device
  features, navigation, permissions UX, sync status display, local drafts,
  local user feedback, and admin-rule-based feature visibility.
- Clarified Admin/API Responsibilities with a responsibility ownership contract
  for tenant management, users and permissions, admin panel, API contracts,
  feature control, remote configuration, mobile version rules, notifications,
  billing/subscription logic, support operations, reporting, audit history,
  conflict decisions, and security enforcement.
- Clarified Two-System Boundary Logic with an explicit boundary contract for
  Admin/API ownership, mobile ownership, API-only behavior, local cache,
  remote admin control, and offline behavior.
- Clarified the SaaS Value Map with a value delivery contract that connects
  stakeholders to admin control, mobile access, offline sync, notifications,
  reports, security, feature flags, API outcomes, boundaries, and proof
  signals.
- Clarified Target User Roles documentation so platform owner, super admin,
  tenant admin, tenant manager, support agent, billing manager, mobile user,
  invited user, suspended user, and guest/pre-login user responsibilities,
  limits, visibility, and control boundaries stay explicit.
- Added Target User Roles decision rules for account-state precedence,
  tenant-scoped visibility, platform exceptions, mobile API-derived capability,
  and job-scoped support/billing access.
- Clarified Core Product Principles documentation so admin authority, API-only
  mobile behavior, feature control, tenant isolation, useful offline behavior,
  secure defaults, API-first communication, simple mobile UX, documentation-first
  development, and modular expansion stay explicit across the Markdown corpus.
- Clarified Product Positioning documentation so the SaaS control center,
  mobile workforce/client platform, API-first system, offline-capable mobile
  system, feature-controlled platform, tenant-based product, and web-only vs
  mobile-only tradeoffs stay explicit across the Markdown corpus.
- Clarified Product Vision documentation so the SaaS problem, admin users,
  mobile users, two-system need, admin-controlled mobile behavior,
  NativePHP + Livewire rationale, and SaaS scalability logic stay explicit.
- Added Mobile Version Control Logic documentation for minimum supported
  versions, optional updates, forced updates, maintenance mode, outdated app
  behavior, store links, update messages, and protection from broken old
  versions.
- Created the implementation status checklist that maps all documented SaaS,
  API/admin, mobile, NativePHP, offline/sync, support, billing, reports, and
  quality-loop requirements to current implementation state.
- Added Phase 1 monorepo boundary documentation for `apps/api-admin`,
  `apps/mobile-client`, API contracts, root scripts, and remaining tasks.
- Added Admin Control Center logic documentation for scoped, authorized,
  auditable control of tenants, users, roles, permissions, mobile features,
  remote config, app versions, maintenance, force update, sync, notifications,
  reports, billing, and support.
- Added Feature Flag Logic documentation for global, tenant, plan, role,
  permission, user, app-version, device, cohort, maintenance, and emergency
  feature decisions.
- Added Remote Configuration Logic documentation for configurable behavior,
  mobile receive/cache rules, offline behavior, tenant overrides, safe admin
  changes, and missing or invalid config handling.
- Scaffolded `apps/api-admin` as a Laravel 13 API/admin app with Livewire,
  Blade, Tailwind, a versioned mobile status endpoint, shared mobile API
  response envelopes, focused Pest coverage, and verified frontend build.
- Copied the verified root NativePHP mobile client into `apps/mobile-client`
  as a standalone Laravel app with Livewire routes, NativePHP config, local
  SQLite infrastructure, mobile UI surfaces, tests, frontend build, and plugin
  validation.
- Added v1 mobile API contract documentation for auth, bootstrap, tenancy,
  features, remote config, app version/maintenance, records, sync,
  notifications, support, billing, reports, and diagnostics, plus an
  implemented contract catalogue endpoint at `GET /api/v1/mobile/contracts`.
- Implemented the API/admin mobile authentication foundation with registration,
  login, refresh-token rotation, logout, logout-all-devices, current-user, and
  profile endpoints, hashed token persistence, device sessions, validation
  envelopes, and security audit events.
- Added API/admin session authentication for platform-admin users, protected the
  admin dashboard, added admin login/logout routes and Blade view, and audited
  admin login/logout attempts.
- Added the mobile client auth API service boundary with configurable API base
  URL/timeouts, standard JSON/error handling, device context payloads,
  secure-token-store persistence, and focused coverage for login, register,
  refresh, current user, profile update, logout, and logout-all calls.
- Rewired mobile login, register, profile update, profile logout, sessions
  logout, and sessions logout-all Livewire flows to the mobile auth API service
  while keeping local Laravel sessions presentation-only.
- Implemented the first mobile bootstrap endpoint and mobile bootstrap service,
  returning authenticated user/device context plus explicit foundation defaults
  for pending SaaS modules and caching the envelope in mobile-local settings.
- Added the API/admin tenancy foundation with tenant and tenant-membership
  schema, public tenant IDs, mobile tenant list/switch endpoints, bootstrap
  tenant context, switch denial handling, and tenant switch audit events.
- Added the mobile tenant workspace foundation with cached bootstrap tenant
  context, an authenticated tenant API service, workspace settings UI, manual
  context refresh, tenant switching through API/admin, and focused coverage for
  routes, settings, API calls, and cache refresh behavior.
- Added the API/admin mobile permission foundation with a role-derived ability
  registry, nested bootstrap permission payloads, fail-closed invited/suspended
  membership behavior, and focused bootstrap permission coverage.
- Added the API/admin feature flag foundation with global defaults, tenant and
  user overrides, resolved `/features` API output, bootstrap integration,
  permission-gated feature states, and focused resolution-order coverage.
- Added the API/admin feature flag control page for audited global mobile
  defaults, including admin navigation, search, create/update validation, and
  focused Livewire coverage.
- Enforced feature minimum app-version gates so otherwise-enabled mobile
  features return `update_required` with an `update_app` action when the
  reported app build is too old.
- Added API/admin resource policies for current mobile control-plane resources,
  registered them explicitly, and wired Livewire edit/save/restore actions
  through policy authorization.
- Added the API/admin tenant feature override control page with tenant-scoped
  feature states, mobile impact preview, audited create/update, dashboard
  navigation, and restore-from-audit coverage.
- Added the API/admin user feature override control page with membership-safe
  user-specific feature states, mobile impact preview, audited create/update,
  dashboard navigation, and restore-from-audit coverage.
- Added the API/admin remote config foundation with global defaults,
  tenant-scoped overrides, resolved `/config` API output, bootstrap
  integration, freshness metadata, deterministic config versions, and focused
  contract coverage.
- Added the API/admin remote config control page with JSON-object validation,
  mobile impact preview, audited create/update, dashboard navigation, and
  restore-from-audit coverage.
- Added the API/admin tenant remote config override page with tenant-scoped
  JSON-object validation, mobile impact preview, audited create/update,
  dashboard navigation, and restore-from-audit coverage.
- Added a mobile remote config store/cache that reads cached bootstrap config
  without creating settings rows, validates URLs safely, falls back to bundled
  defaults, and applies config to dashboard widgets, sync settings, upload
  limits, app-lock copy, support links, and legal links.
- Added a mobile diagnostics report builder and debug-screen export/share flow
  that previews safe diagnostic categories, downloads
  `mobile-lara-diagnostics.json`, shares the same redacted JSON through the
  NativePHP share wrapper, and strips tokens, API credentials, bearer values,
  emails, queued payloads, and headers from support snapshots.
- Added the authenticated API diagnostics upload endpoint with current-tenant
  permission checks, server-side allowlisting/redaction, mobile diagnostic
  report persistence, standard mobile envelopes, and security audit history.
- Added the API/admin app version and maintenance foundation with
  minimum-version force updates, optional update states, public `/app-version`
  API output, bootstrap maintenance integration, and focused policy coverage.
- Added the API/admin records/content API foundation with tenant-scoped record
  persistence, category/tag resolution, note append, attachment metadata,
  activity timeline entries, security audit events, mobile list/detail/create/
  update/archive/restore endpoints, contract catalogue updates, and focused
  feature coverage.
- Added mobile records API and local-first sync services so record
  create/update/archive/restore actions write local SQLite first, attempt the
  Admin/API records contract when online and authenticated, store returned
  server IDs/sync versions in local metadata, preserve offline pending work,
  and record API failure retry context.
- Added the API/admin app version policy control page with confirmation,
  mobile impact preview, audited create/update, dashboard navigation, and
  restore-from-audit coverage.
- Extended app-version policy control with tenant and rollout-cohort scopes,
  trusted bootstrap precedence for tenant policies, public cohort checks through
  `X-Mobile-Cohort`, scoped audit snapshots, and focused resolver coverage.
- Added app-version range controls so admins can target scoped policies to
  specific reported build ranges, with deterministic resolver precedence,
  audited range snapshots, Livewire validation, and focused coverage.
- Added feature flag plan and device gates with global required-plan/device
  constraints, resolver-enforced blocked states, admin controls, audit
  snapshots, and focused API/bootstrap coverage.
- Added a fail-closed emergency feature gate so global, tenant, or user
  emergency-disabled states cannot be bypassed by lower-scope overrides.
- Added feature flag cohort gates with admin-managed allowed cohorts,
  resolver-enforced rollout blocking, API cohort context, and focused coverage.
- Added maintenance-aware feature gates so app-version maintenance policy blocks
  ordinary enabled features while keeping support behavior available.
- Added mobile app-version policy screens and dashboard banners so cached
  bootstrap force-update, optional-update, and maintenance states show
  Admin/API-provided messages, store/support/logout actions, retry timing, and
  policy refresh behavior before normal mobile navigation resumes.
- Added mobile subscription resolution from tenant subscription state/settings,
  `/api/v1/mobile/billing/subscription`, bootstrap subscription metadata, and
  subscription-backed feature plan gates.
- Added bootstrap notification policy resolution from tenant settings with
  quiet-hours metadata, push-registration hints, fail-closed no-tenant behavior,
  and notification policy version metadata.
- Added bootstrap sync policy resolution from tenant settings and remote config,
  with permission/subscription/maintenance gates, server-endpoint-pending
  metadata, and sync policy version metadata.
