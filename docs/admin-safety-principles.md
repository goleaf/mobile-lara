# Admin Safety Principles

Updated: 2026-06-26

This document defines admin safety principles for Mobile Lara. It explains which admin actions are dangerous, which actions need confirmation, which actions need audit history, which actions should show impact before saving, how admins should preview mobile impact, how rollback should work, and how tenant-specific changes should be isolated. It is documentation only and does not define database structure, database fields, migrations, routes, controllers, Livewire components, Filament resources, policies, jobs, services, providers, or application logic.

Use this document with [Product Vision](product-vision.md), [Product Positioning](product-positioning.md), [Core Product Principles](product-principles.md), [Target User Roles](user-roles.md), [SaaS Value Map](saas-value-map.md), [Two-System Boundary Logic](two-system-boundary.md), [Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client Responsibilities](mobile-client-responsibilities.md), [Mobile UX Principles](mobile-ux-principles.md), [API-First Principles](api-first-principles.md), [Documentation-First Architecture](documentation-first-architecture.md), [Admin Control Center Logic](admin-control-center-logic.md), [Feature Flag Logic](feature-flag-logic.md), [Remote Configuration Logic](remote-configuration-logic.md), and [Mobile Version Control Logic](mobile-version-control-logic.md): admin safety defines how high-impact controls are confirmed, previewed, audited, isolated, and rolled back before they affect tenants, mobile users, support, billing, reports, sync, or NativePHP UX.

## Safety Statement

Admin controls are powerful because the Admin/API system is the source of SaaS authority.

That power creates risk. A single admin decision can block tenant access, revoke user ability, hide mobile features, change offline behavior, force updates, enter maintenance mode, send notifications, alter billing access, expose reports, change support state, or affect sync replay.

Product rule: dangerous admin actions must be intentional, explainable, scoped, confirmed, auditable, previewable where useful, reversible where possible, and tenant-isolated by default.

Mobile should never discover dangerous changes by surprise. The API should resolve a safe mobile outcome, and the admin surface should show the expected mobile effect before the change is saved.

## Admin Safety Decision Contract

Every dangerous admin control should be documented before implementation because admin mistakes can affect many users quickly.

| Decision area | Principle | Required outcome |
| --- | --- | --- |
| Dangerous actions | Any action that changes access, authority, safety, billing, version policy, sync behavior, notification reach, report visibility, data lifecycle, support recovery, or tenant isolation is dangerous. | The action has an owner, scope, reason, affected area, expected mobile/API outcome, and risk level before implementation planning. |
| Confirmation | Dangerous, destructive, broad, irreversible, security-sensitive, billing-impacting, mobile-blocking, cross-tenant, or platform-wide actions need explicit confirmation. | The admin sees who/what will be affected, what mobile will show, whether queued/offline work changes, what support should expect, and whether rollback is available before saving. |
| Audit history | Sensitive actions need a durable record of who changed what, why, where, when, from which scope, and with what expected effect. | Support, security, billing, and platform operators can reconstruct the decision without relying on memory or hidden UI state. |
| Impact before save | Admins should see the blast radius before changing controls that affect tenants, users, roles, plans, versions, devices, features, sync, notifications, reports, billing, or support. | The admin makes a product decision with visible consequences instead of toggling unknown internals. |
| Mobile preview | Admins should preview the resolved mobile outcome, not raw flags, config, or policy layers. | The preview shows what a guest, invited user, suspended user, active mobile user, stale app, offline app, and affected tenant would experience. |
| Rollback | Reversible actions should have a rollback path before activation; irreversible actions should say so clearly. | Admins know how to restore the previous state, limit damage, notify support, and protect queued/offline mobile work. |
| Tenant isolation | Tenant-specific changes must stay inside the tenant unless explicitly platform-wide. | No tenant change leaks data, reports, notifications, support context, billing behavior, or feature access across tenant boundaries. |

This contract is intentionally principle-level. It does not create audit tables, confirmation dialogs, preview services, policies, queues, events, Livewire components, Filament resources, API endpoints, migrations, or application logic.

## Dangerous Admin Actions

An admin action is dangerous when it can deny access, grant authority, change money-related behavior, modify security posture, alter mobile behavior, trigger communication, expose data, affect offline work, or change tenant boundaries.

Dangerous actions include:

- **Tenant lifecycle changes** - suspend, disable, archive, delete, merge, restore, limit, maintenance-state, plan change, support-tier change, or tenant ownership change.
- **User lifecycle changes** - invite, approve, suspend, reactivate, recover, revoke sessions, revoke devices, force password reset, change email identity, or remove a user from a tenant.
- **Role and permission changes** - grant admin access, revoke manager ability, change support visibility, change billing authority, assign high-risk permissions, or create broad permission bundles.
- **Feature control changes** - enable, disable, emergency-disable, deprecate, beta-release, plan-limit, version-gate, tenant-gate, or user-gate important mobile features.
- **Remote config changes** - change runtime copy, limits, workflow options, offline eligibility, sync thresholds, NativePHP permission text, maintenance messages, support prompts, notification behavior, version messaging, cache behavior, or fallback behavior.
- **Mobile version changes** - raise minimum supported version, force update, block versions, change store links, change update messages, enter maintenance, change release-channel rules, or revoke builds.
- **Sync behavior changes** - block replay, change conflict handling, change queue windows, change retry limits, accept or reject stale drafts, replay failed actions, or modify offline eligibility.
- **Notification changes** - broadcast to many users, target sensitive cohorts, escalate priority, bypass quiet hours, send billing/security/support notices, suppress notices, or change templates.
- **Billing and entitlement changes** - change plans, quotas, trials, renewals, failed-payment outcomes, entitlements, billing contacts, support tiers, or plan gates.
- **Support recovery changes** - impersonation, account recovery, diagnostic access, device/session recovery, manual state correction, case escalation, or emergency user unlock.
- **Report and export changes** - expose reports, export tenant data, change aggregation scope, reveal support/billing/security data, or broaden dashboard visibility.
- **Data lifecycle changes** - delete, purge, restore, archive, bulk update, retention change, legal hold change, import, merge, or irreversible cleanup.
- **Integration/provider changes** - push provider, billing provider, identity provider, storage provider, analytics provider, diagnostics provider, webhook target, or external API credential behavior.
- **API contract changes** - response-shape changes, error-code changes, stale-client behavior, bootstrap context changes, feature/config/version payload changes, or sync semantics that old mobile clients depend on.

Dangerous does not mean forbidden. It means the product must slow the admin down enough to understand the effect.

## Actions That Need Confirmation

Confirmation should be required when an admin action is destructive, irreversible, broad, security-sensitive, billing-impacting, mobile-blocking, notification-reaching, support-relevant, or cross-tenant.

Actions that need confirmation include:

- Suspending, disabling, archiving, deleting, restoring, or limiting a tenant.
- Suspending, reactivating, recovering, deleting, or removing a user.
- Revoking active sessions, tokens, trusted devices, or NativePHP device access.
- Granting or revoking roles and permissions that change tenant administration, billing, support, reports, exports, security, or mobile protected actions.
- Enabling, disabling, emergency-disabling, deprecating, or rolling out an important mobile feature.
- Publishing remote config that changes behavior, limits, offline eligibility, sync behavior, user-facing copy, maintenance messages, or support instructions.
- Raising minimum supported versions, forcing updates, blocking versions, revoking release channels, or changing store/update links.
- Starting, extending, narrowing, or ending platform, tenant, feature, API, sync, billing, notification, or support maintenance.
- Changing sync replay policy, conflict decision policy, queue acceptance, stale-data thresholds, retry windows, or offline draft acceptance.
- Sending notification broadcasts, security notices, billing notices, outage notices, support escalations, or high-priority alerts.
- Changing plan, quota, entitlement, subscription, trial, payment-failed, or support-tier behavior.
- Exporting reports, expanding report visibility, or revealing sensitive tenant, billing, support, security, or diagnostic data.
- Performing support recovery, impersonation-like access, manual account correction, device recovery, or emergency unblock.
- Deleting, purging, restoring, merging, importing, or bulk-changing operational data.
- Changing provider, integration, webhook, or API contract behavior that can affect mobile or tenant trust.

Confirmation should show:

- The acting admin and role.
- The action name and target.
- The scope: platform, tenant, user, role, plan, feature, version, device, cohort, report, support case, or sync area.
- The reason or ticket/case context.
- The affected tenants, users, roles, plans, app versions, devices, cohorts, features, screens, reports, notifications, billing surfaces, support queues, and sync queues where known.
- The resolved mobile effect: visible, hidden, disabled, blocked, deprecated, update-required, maintenance, offline-limited, draft-only, queueable, read-only, conflict, failed, retry-later, or contact-support.
- Whether the change applies immediately, later, gradually, or during maintenance.
- Whether the action is reversible, partially reversible, or irreversible.
- The rollback or recovery path.

## Actions That Need Audit History

Audit history is required for actions that affect authority, access, money, data, product behavior, support recovery, or operational safety.

Audit-worthy actions include:

- Tenant lifecycle, plan, status, settings, support tier, ownership, isolation, and maintenance changes.
- User invitation, activation, suspension, recovery, reactivation, removal, profile identity, session, token, and device changes.
- Role assignment, role definition, permission grant, permission revoke, report access, support access, billing access, and export access changes.
- Feature flag enablement, disablement, rollout, rollback, emergency disablement, plan limit, tenant override, user override, version gate, and cohort changes.
- Remote config publish, tenant override, validation failure, missing/invalid config recovery, rollback, and emergency config change.
- App-version policy, minimum version, optional update, forced update, blocked version, maintenance, store link, update message, release-channel, and rollback changes.
- Sync policy, replay, conflict decision, stale threshold, retry policy, queue block, offline eligibility, and manual correction changes.
- Notification template, target, broadcast, suppression, escalation, delivery-policy, quiet-hour, and high-priority changes.
- Billing plan, quota, entitlement, subscription, trial, invoice/payment state, support tier, and billing contact changes.
- Support case visibility, recovery action, diagnostic access, account/device recovery, and manual state correction.
- Report definition, export, visibility, aggregate scope, dashboard access, and data-retention changes.
- Integration provider, webhook, credential rotation event, external service policy, and API contract behavior changes.

Audit records should capture the principle-level context:

- Actor identity, role, tenant context, and admin surface.
- Target entity, target tenant, affected scope, and old/new meaning.
- Reason, ticket, support case, incident, release, or billing context.
- Time, request/session/device context, source IP class where appropriate, and approval/confirmation state.
- Expected mobile/API effect and user-facing state.
- Related feature/config/version/sync/notification/report/billing/support area.
- Rollback link, previous state, or reason rollback is impossible.

Audit history should be understandable by support and security reviewers without exposing tenant-private details to the wrong audience.

## Impact Before Saving

Dangerous actions should show impact before saving because admin users should not infer consequences from raw settings.

Impact previews should answer:

- Which tenants are affected.
- Which users, roles, account states, devices, app versions, plans, cohorts, or release channels are affected.
- Which mobile screens, navigation items, actions, NativePHP capabilities, offline queues, local drafts, sync flows, notifications, reports, support cases, or billing states are affected.
- Whether mobile state becomes visible, hidden, disabled, blocked, deprecated, update-required, maintenance-limited, offline-limited, draft-only, queueable, read-only, conflict, failed, retry-later, contact-admin, contact-support, or upgrade/contact-sales.
- Whether offline queued work will still replay, be blocked, be revalidated, become conflict, require update, or require support.
- Whether current API responses, feature flags, remote config, version rules, permissions, tenant status, billing entitlement, or maintenance state will change.
- Whether support, billing, reporting, or operations teams need visibility before activation.
- Whether the change can be rolled back, staged, delayed, or limited to a pilot tenant.

Impact preview should be product-language first. Admins need to understand behavior, not implementation internals.

## Mobile Impact Preview

Mobile impact preview should show the resolved mobile state after admin controls are applied.

The preview should not expose raw policy layers as if mobile decided them locally. It should show the API-derived outcome mobile will receive.

Useful preview personas and contexts include:

- Guest/pre-login user.
- Invited user.
- Suspended user.
- Active mobile user.
- Tenant manager with mobile access.
- Tenant admin viewing mobile-adjacent behavior where applicable.
- Billing-limited mobile user.
- Support-assisted user.
- User on an old supported app version.
- User on a deprecated or blocked app version.
- User offline with stale cached config.
- User with local drafts or queued actions.
- User inside a tenant-specific override.
- User outside the affected tenant.

Preview output should explain:

- Navigation visibility.
- Feature state.
- Permission outcome.
- Tenant/account status.
- Update prompt or force-update state.
- Maintenance state.
- Store/distribution link behavior.
- Offline eligibility.
- Draft and queue behavior.
- Sync replay outcome.
- Notification/message effect.
- User-facing copy category.
- Support next action.

Admin preview is not a substitute for API authority. It is a planning and safety tool that helps admins see the same product outcome the API will resolve for mobile.

## Rollback Principles

Rollback should be planned before a dangerous action is saved.

Rollback principles:

- Prefer reversible changes for feature flags, remote config, version rules, notification policy, maintenance state, sync policy, support visibility, report visibility, and plan gates.
- Record the previous effective state before changing a dangerous control.
- Record why the change happened and why rollback would be safe or unsafe.
- Distinguish reversible, partially reversible, and irreversible actions.
- Use staged rollout, pilot tenants, scheduled activation, grace periods, and version gates where they reduce risk.
- Keep emergency rollback available for broad feature/config/version/sync/notification incidents.
- Roll back within the same scope as the original change unless a wider incident requires platform-wide recovery.
- Revalidate mobile state after rollback through API context, not stale local assumptions.
- Treat queued offline actions carefully after rollback; replay should still recheck current tenant, permission, billing, feature, version, sync, and conflict policy.
- Notify support, billing, or affected tenant admins when rollback changes user-visible behavior.
- Audit rollback just like activation.

Irreversible actions should be rare. If an action cannot be rolled back, the admin should see that before confirmation and should understand recovery alternatives such as restore, support case, export, re-invite, re-sync, or manual review.

## Tenant Isolation

Tenant-specific admin changes must be isolated by default.

Tenant isolation principles:

- A tenant-level change affects only that tenant unless the admin explicitly selects a platform-wide or multi-tenant scope.
- Tenant overrides cannot bypass global security blocks, platform emergency blocks, version blocks, plan ceilings, permission requirements, support visibility limits, or tenant-boundary protections.
- Tenant admins may control only tenant-scoped settings allowed by the platform.
- Tenant managers may see and operate only the tenant workflows their role permits.
- Support agents must see only the tenant, user, case, diagnostic, and recovery context their support role permits.
- Billing managers must see only billing and entitlement information within their allowed tenant or platform scope.
- Reports, exports, notifications, support cases, diagnostics, sync state, and audit history must not leak across tenants.
- Platform-wide changes need stronger confirmation than tenant-specific changes.
- Multi-tenant changes should show affected tenants individually where possible.
- Rollback of a tenant-specific change should restore only that tenant unless the original action was platform-wide.

Tenant isolation is a safety principle, not only a data model principle. It must shape admin copy, confirmation, preview, audit visibility, reporting, support, billing, and mobile outcomes.

## Confirmation Strength

Not every admin action needs the same friction.

Recommended confirmation levels:

| Level | When to use | Expected admin experience |
| --- | --- | --- |
| Informational save | Low-risk wording, presentation, or local tenant display settings with no authority change. | Normal save with clear summary. |
| Review before save | Config, feature, report, notification, or support changes with limited tenant impact. | Show impact summary and require explicit save. |
| Explicit confirmation | Access, billing, version, maintenance, sync, notification broadcast, report export, or support recovery changes. | Show impact, require reason, and require confirmation. |
| Strong confirmation | Destructive, irreversible, platform-wide, cross-tenant, force-update, emergency block, data lifecycle, or broad security changes. | Show blast radius, require reason, confirm scope, and make rollback/irreversibility explicit. |
| Approval or scheduled change | High-risk changes where separation of duties, rollout timing, incident response, or tenant communication matters. | Require documented approval or planned activation before effect. |

This table is a product principle. It does not require a specific UI pattern or persistence model.

## Admin Safety Checklist

Before implementing any dangerous admin control, documentation should answer:

- What action is being performed?
- Who may perform it?
- What scope applies?
- Why is it dangerous?
- What confirmation level applies?
- What audit history is needed?
- What should admins see before saving?
- What mobile state will users see?
- What API outcome carries that state?
- What happens to offline cache, drafts, and queued actions?
- What happens to support, billing, reports, notifications, and sync?
- Is the action reversible, partially reversible, or irreversible?
- What is the rollback or recovery path?
- How are tenant-specific changes isolated?
- What can go wrong?
- How will support explain the result?

If these answers are missing, the control is not ready for implementation planning.

## Risk Register

| Risk | Safety principle |
| --- | --- |
| Admin blocks too many users | Show impact before save, confirm broad scope, stage rollout, keep rollback ready, and expose support context. |
| Tenant change leaks outside tenant | Tenant isolation wins by default; platform-wide changes require explicit scope and stronger confirmation. |
| Mobile users see surprise behavior | Preview resolved mobile outcome and send behavior through predictable API responses. |
| Offline work becomes unsafe | Recheck queued actions during sync replay and show draft, blocked, conflict, failed, retry-later, or update-required states. |
| Feature/config changes become hidden authority | Document admin owner, API outcome, mobile effect, audit, support meaning, and rollback before implementation. |
| Forced update creates support surge | Preview affected versions/devices/tenants, use grace period where safe, and keep support messages ready. |
| Notification reaches wrong audience | Require targeting preview, tenant/role/version/permission checks, confirmation, and audit history. |
| Report/export exposes sensitive data | Require explicit scope, role/permission check, confirmation, audit, and tenant-safe visibility. |
| Rollback is assumed but impossible | Mark irreversible or partially reversible actions before confirmation and document recovery alternatives. |
| Support recovery becomes broad access | Keep support actions case-scoped, audited, reasoned, and tenant-safe. |

## Success Test

Admin safety is working when an authorized admin can answer these questions before saving a dangerous action:

- Who and what will this affect?
- What will mobile users see?
- What happens if a mobile user is offline?
- What happens to API, sync, support, billing, reports, notifications, and audit?
- Is this tenant-specific, platform-wide, or multi-tenant?
- Can this be rolled back?
- What will support say if users ask?

If the admin cannot answer those questions from the planned control experience and documentation, the action is too risky to implement as-is.
