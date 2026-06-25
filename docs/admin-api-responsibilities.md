# Admin/API Responsibilities

Updated: 2026-06-25

This document defines the logical responsibilities of the Admin/API system in Mobile Lara. It explains what the SaaS control plane owns, why it owns it, how that authority relates to the mobile client, and which risks the responsibility model prevents. It is documentation only and does not define database fields, migrations, controllers, components, policies, jobs, services, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Documentation-First Architecture](documentation-first-architecture.md), [API-First Principles](api-first-principles.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), and [Mobile Version Control Logic](mobile-version-control-logic.md): Admin/API owns authority, API is the trusted contract, mobile owns local execution, stakeholder value is made operable through server-side responsibility, admin controls are scoped and auditable, feature flags, remote config, and mobile version policy are resolved server-side, and every feature/control/risk is documented before implementation.

## Responsibility Statement

The Admin/API system is the authoritative SaaS control plane.

It owns all decisions that affect tenant access, user authority, permissions, API contracts, feature availability, remote configuration, mobile app-version policy, notifications, billing/subscription logic, support operations, reporting, audit history, conflict decisions, and security enforcement.

The mobile client may consume, cache, display, and act on Admin/API decisions, but it must not become the place where those decisions are created or trusted.

## Core Responsibility Principles

1. **Authority stays server-side** - Any business-sensitive decision belongs to Admin/API, not mobile-local state.
2. **The API is the contract** - Mobile receives policy, data, allowed actions, and failure states through explicit API responses.
3. **Admin changes must be explainable** - Controls that affect mobile behavior need scope, reason, visibility, support context, and audit expectations.
4. **Tenant isolation is the operating boundary** - Every responsibility below must respect tenant scope and role scope.
5. **Mobile gets outcomes, not internals** - Mobile should receive allowed, denied, blocked, deprecated, pending, conflict, retry, or contact-support states, not raw admin machinery.
6. **Operations close the loop** - Feature flags, version rules, notifications, billing, support, reports, audit, and conflict decisions must feed each other so the product can be operated as SaaS.

## Responsibility Map

| Responsibility | Admin/API owns | Mobile receives |
| --- | --- | --- |
| Tenant management | Tenant lifecycle, tenant settings, tenant plan state, tenant isolation, tenant status. | Current tenant context, allowed tenant choices, tenant labels, tenant-blocked state. |
| Users and permissions | User lifecycle, invitations, suspension, roles, permission checks, least privilege, account-state restrictions. | Capability state, profile/session state, invitation/suspension/pre-login flow state. |
| Admin panel | Operational control surfaces for platform, tenant, support, billing, reports, rollout, and security roles. | No admin panel authority; only user-facing outcomes. |
| API contracts | Versioned request/response behavior, validation rules, authorization, resources, errors, rate limits, idempotency. | Shaped payloads, allowed actions, explicit errors, retry/conflict/version states. |
| Feature control | Global, tenant, plan, role, user, device, version, cohort, and emergency feature gates. | Enabled, disabled, blocked, deprecated, update-required, or unavailable states. |
| Remote configuration | Config schema, config version, tenant scope, rollout scope, safe defaults, rollback expectations. | Cached config copy, config version, refresh state, UI behavior controlled by server policy. |
| Mobile version rules | Supported, recommended, deprecated, blocked, internal-only, and minimum API contract policy. | Update prompts, reduced capability, blocked mode, version warning, release notes if exposed. |
| Notification orchestration | Templates, channels, targeting, quiet hours, delivery policy, escalation, delivery visibility. | Device registration outcome, received notification display, safe local notification history. |
| Billing/subscription logic | Plans, quotas, entitlements, trials, renewals, invoices, restrictions, failed-payment outcomes. | Allowed/denied capability state, quota warning, contact admin/support or upgrade messaging. |
| Support operations | Case lifecycle, safe diagnostics, escalation, config/version/sync context, support visibility. | Support request UI, safe diagnostic submission, retry/config-refresh instructions. |
| Reporting | Report definitions, aggregation, tenant scope, role scope, exports, operational dashboards. | Personal/task/workflow summaries only when API grants them. |
| Audit history | Server-trusted record of sensitive admin changes and accepted sensitive mobile-originated events. | Local activity hints only; no server-trusted audit authority. |
| Conflict decisions | Sync acceptance, rejection, transformation, conflict reasons, resolution options, retry windows. | Pending, synced, conflict, failed, blocked, or retry-later states and resolution UI. |
| Security enforcement | Authentication, authorization, tenant scope, token revocation, device trust, rate limits, secrets policy, forced logout. | Secure access state, blocked/logout state, device warning, permission-denied state. |

## Tenant Management

Admin/API owns tenant management because tenant scope is the commercial, security, reporting, support, billing, and configuration boundary of the SaaS product.

Principles:

- Tenants are server-authoritative workspaces.
- Tenant status can enable, limit, suspend, or block access.
- Tenant settings can differ without forking the mobile app.
- Tenant identifiers supplied by mobile are claims to verify, not authority to trust.
- Reports, support, notifications, billing, and sync decisions must be tenant-scoped.

Mobile may show tenant context and tenant-switching UI when allowed, but Admin/API decides which tenants exist, which tenants a user can access, and which tenant policies apply.

## Users And Permissions

Admin/API owns users and permissions because visibility and control must be enforced by the system of authority.

Principles:

- User lifecycle includes invitation, activation, authentication, suspension, reactivation, and removal.
- Permission checks happen server-side for every protected read, write, sync replay, support action, report, and admin action.
- Roles are least-privilege bundles, not UI labels.
- Invited, suspended, and guest/pre-login states override normal role capability.
- Mobile can render API-provided capability state, but cannot grant or infer permissions.

Mobile should receive clear states such as allowed, denied, suspended, invited, expired, blocked, or verification-required.

## Admin Panel

Admin/API owns the admin panel because SaaS operations need a scoped, auditable, role-aware control surface.

Principles:

- The admin panel is an operations surface, not a marketing page.
- Every admin control should document its mobile effect before implementation.
- Admin controls should be scoped by platform, tenant, role, user, device, feature, version, billing, support, report, or sync policy.
- Sensitive controls should be reversible where possible and auditable by actor, scope, reason, old value, and new value.
- Support and billing users should get job-specific controls, not broad platform authority.
- Admin panel visibility is not enough; Admin/API policy still enforces every action.

The mobile client should never expose admin controls. It receives the operational outcome of those controls through API responses.

The detailed control checklist for admin panel behavior lives in [Admin Control Center Logic](admin-control-center-logic.md).

## API Contracts

Admin/API owns API contracts because the API is the durable boundary between SaaS authority and mobile execution.

Principles:

- API behavior should be explicit, version-aware, and additive where possible.
- Requests are validated at the boundary.
- Authorization is checked server-side.
- Responses are shaped for mobile needs rather than exposing internal models directly.
- Error categories should be predictable: validation, unauthorized, forbidden, conflict, stale client, maintenance, rate limited, retry later, and server error.
- Replayable writes need idempotency rules.
- List responses need pagination or other bounded access patterns before they become large.

Mobile should treat the API as the source of allowed actions, data shape, sync behavior, and failure meaning.

## Feature Control

Admin/API owns feature control because SaaS features must be enabled, disabled, rolled out, limited, and reversed without app-store releases.

Principles:

- Feature control can be global, tenant-scoped, plan-scoped, role-scoped, user-scoped, device-scoped, version-scoped, or cohort-scoped.
- A feature is not ready if it exists only as a mobile screen.
- Every feature needs enable, disable, blocked, deprecated, support, report, audit, and rollback thinking.
- Emergency disablement must be server-authoritative.
- Feature state should be explainable to support and safe to report.
- Important mobile features should follow [Feature Flag Logic](feature-flag-logic.md): global, tenant, plan, role, permission, user, app-version, device, cohort, maintenance, emergency, and offline decisions resolve to mobile-safe states.

Mobile renders feature state; it does not decide feature availability.

## Remote Configuration

Admin/API owns remote configuration because runtime behavior must stay governable while mobile clients may be stale or offline.

Principles:

- Remote config should have scope, version, compatibility expectations, safe defaults, and rollback expectations.
- Remote config should follow [Remote Configuration Logic](remote-configuration-logic.md): safe runtime behavior is configurable, mobile receives resolved values, cache rules are explicit, tenant overrides stay bounded, and missing/invalid config fails safely.
- Config can control copy, limits, workflow options, native permission purpose text, offline eligibility, sync behavior, notification behavior, and support instructions.
- Config changes that affect business behavior should be auditable and support-visible.
- Mobile may cache config but must refresh it when online and respect version policy.

Remote configuration is not a loophole for unreviewed business logic. It is a controlled way to adjust behavior within documented boundaries.

## Mobile Version Rules

Admin/API owns mobile version rules because old clients can call stale contracts or show stale capability assumptions.

Principles:

- Version policy can mark builds as supported, recommended update, deprecated, blocked, or internal-only.
- Version policy should follow [Mobile Version Control Logic](mobile-version-control-logic.md): minimum supported versions, optional updates, forced updates, maintenance state, outdated responses, store links, update messages, support context, audit, rollback, and old-version protection are documented before implementation.
- Version rules can differ by platform, tenant, rollout cohort, feature risk, or incident state.
- API contracts should deprecate before removal when feasible.
- Version policy should protect security, billing, sync, and feature-control assumptions.
- Support should be able to see which version policy applied.

Mobile reports its version and follows the returned policy. It does not decide whether it is still safe to operate.

## Notification Orchestration

Admin/API owns notification orchestration because messages affect operations, support, billing, rollout, security, and user attention.

Principles:

- Admin/API controls templates, channels, targeting, quiet hours, priority, escalation, and delivery rules.
- Notification eligibility should respect tenant, role, feature, billing, version, device, and user preference boundaries.
- Delivery events should become support/reporting context where appropriate.
- Notifications should be queued or otherwise orchestrated so slow channels do not define the product experience.
- Mobile-local notification history is a user convenience, not delivery truth.

Mobile handles device permission prompts, received message display, and safe local history.

## Billing And Subscription Logic

Admin/API owns billing/subscription logic because commercial state changes what tenants can use.

Principles:

- Plans, trials, quotas, invoices, failed-payment rules, renewals, restrictions, and entitlements belong server-side.
- Billing logic should map to feature availability through API-enforced entitlement outcomes.
- Mobile users should see clear product state, not payment-provider internals.
- Support and billing roles need explainable entitlement context.
- Offline replay must re-check current entitlement and quota state before server acceptance.

Mobile receives allowed, blocked, quota-warning, contact-admin, contact-support, or upgrade/contact-sales outcomes.

## Support Operations

Admin/API owns support operations because support needs safe context without broad tenant exposure.

Principles:

- Support cases belong to the server-authoritative case timeline.
- Safe diagnostics should be defined by policy, not arbitrary mobile uploads.
- Support visibility should be case-scoped, tenant-scoped, and role-scoped.
- Support context should include relevant app version, config version, feature state, sync state, notification state, and recent safe errors.
- Support actions should be auditable when they affect users, tenants, devices, config, sync, or security.

Mobile can create support requests and submit safe diagnostics, but Admin/API owns case authority and support visibility.

## Reporting

Admin/API owns reporting because reports can expose cross-user, tenant, billing, support, and security context.

Principles:

- Reports must be scoped by tenant, role, support case, billing purpose, aggregation level, and export permission.
- Reports should measure product health: adoption, active devices, sync health, feature rollout, notification health, support load, billing posture, and security events.
- Mobile-facing summaries should be personal or workflow-scoped unless API grants broader access.
- Support and billing reports should expose only what those jobs require.
- Reports should help operate the SaaS, not leak data.

Mobile may display safe summaries; Admin/API decides report definition, scope, and exportability.

## Audit History

Admin/API owns audit history because audit must be trusted after disputes, incidents, support escalations, and compliance reviews.

Principles:

- Sensitive admin changes need actor, scope, old value, new value, timestamp, reason, and affected area.
- Accepted sensitive mobile-originated events can become server audit events after API validation.
- Local mobile activity history is not trusted audit history.
- Audit visibility should be role-scoped and tenant-scoped.
- Audit events should help explain feature, config, version, billing, support, sync, and security outcomes.

Mobile can show local activity hints, but only Admin/API creates server-trusted audit history.

## Conflict Decisions

Admin/API owns conflict decisions because conflict outcomes determine whether local intent becomes server truth.

Principles:

- Offline writes replay as intents, not facts.
- API decides whether an intent is accepted, transformed, rejected, duplicated, stale, unauthorized, out-of-policy, or conflicted.
- Conflict reasons should be explicit and support-visible when they affect business data.
- Conflict options should be safe: retry, edit, discard, request support, or wait for admin policy.
- Billing, permissions, tenant state, app version, and feature flags can all change while mobile is offline and must be rechecked.

Mobile presents pending, synced, conflict, failed, blocked, or retry-later state. It does not decide final conflict truth.

## Security Enforcement

Admin/API owns security enforcement because security cannot depend on mobile UI, cached flags, or local device state.

Principles:

- Authentication, authorization, tenant scope, token revocation, forced logout, device trust, rate limits, and secrets policy belong server-side.
- Every protected API and admin action needs authorization.
- Sensitive data should be excluded from responses unless explicitly needed and allowed.
- Mobile-provided IDs, roles, tenant IDs, feature flags, plan labels, and device claims are untrusted input.
- Support, billing, reports, and audit need least-privilege visibility.
- Security failures should return clear but non-leaking outcomes.

Mobile can help users understand secure state, but it does not enforce SaaS security.

## Relationship To Mobile Client

The mobile client is a consumer and executor of Admin/API decisions.

The API contract model is defined in [API-First Principles](api-first-principles.md).

The companion local-execution model is defined in [Mobile Client Responsibilities](mobile-client-responsibilities.md).

Mobile may:

- Display capability state.
- Cache safe server-confirmed data.
- Store drafts.
- Queue allowed offline intents.
- Use NativePHP capabilities when server policy allows.
- Send safe diagnostics.
- Show support, billing, version, feature, and sync outcomes in user-friendly language.

Mobile must not:

- Decide tenant access.
- Grant permissions.
- Enforce billing.
- Define feature availability.
- Define remote config.
- Override app-version policy.
- Define notification targeting.
- Own support case state.
- Produce trusted reports.
- Produce trusted audit history.
- Decide final conflict outcomes.
- Enforce SaaS security.

## Responsibility Checklist

Use this checklist before planning a future Admin/API slice.

| Question | Required answer |
| --- | --- |
| Which responsibility area owns this? | One or more Admin/API responsibility areas are named. |
| Which tenant scope applies? | Tenant, role, support, billing, report, or platform scope is explicit. |
| What does mobile receive? | A clear outcome, state, or shaped API response, not raw authority. |
| What must be API-enforced? | Validation, authorization, entitlement, feature state, version policy, sync, or support policy is explicit. |
| What can be remote-configured? | Safe runtime variation is named, with scope and fallback. |
| What is auditable? | Sensitive changes and accepted mobile-originated events have an audit expectation. |
| What can support explain? | Support-visible context is named without overexposing tenant data. |
| What can billing affect? | Entitlement, quota, plan, restriction, or renewal outcome is explicit. |
| What happens offline? | Replay, conflict, stale, blocked, or retry-later behavior is defined. |
| What security boundary applies? | Authentication, authorization, tenant scope, rate limit, secret, or data exposure boundary is clear. |

## Risks

| Risk | Responsibility response |
| --- | --- |
| Admin/API becomes a generic admin dashboard | Keep responsibility tied to mobile control, SaaS operations, tenant safety, and API authority. |
| Mobile duplicates Admin/API rules | Mobile receives outcomes and state; Admin/API keeps the rules. |
| API contracts become accidental | Document request, response, error, version, idempotency, and deprecation expectations before implementation. |
| Feature flags become hidden behavior | Require owner, scope, audit, support explanation, reportability, and rollback. |
| Remote config becomes unreviewed logic | Keep config scoped, versioned, compatible, support-visible, and auditable when behavior-changing. |
| Billing leaks into mobile complexity | Mobile shows entitlement outcomes; Admin/API owns billing logic. |
| Support sees too much | Support visibility stays case-scoped, tenant-scoped, and diagnostic-safe. |
| Reports leak data | Admin/API owns report scope, aggregation, export, and role visibility. |
| Audit is incomplete | Sensitive changes and accepted sensitive mobile-originated events need server-trusted history. |
| Conflict handling is inconsistent | Admin/API owns conflict categories, reasons, options, and support visibility. |
| Security becomes UI-only | Server authorization, tenant scope, device trust, and rate limits remain final. |

## Success Test

The Admin/API responsibility model is successful when every mobile capability can be traced back to a server-owned tenant rule, permission rule, feature rule, config rule, version rule, notification rule, billing rule, support rule, report rule, audit rule, conflict rule, and security rule where applicable, and the mobile client receives only the outcome it needs to present or execute the workflow safely.
