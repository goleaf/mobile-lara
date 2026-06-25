# Target User Roles

Updated: 2026-06-25

This document defines the main logical user roles for Mobile Lara. It describes responsibilities, limitations, and what each role should be able to see or control. It is documentation only and does not define database fields, migrations, seeders, policies, permissions, controllers, Livewire components, or application logic.

## Role Model Principles

Roles are product boundaries, not only labels.

- Role and permission decisions must follow [Documentation-First Architecture](documentation-first-architecture.md): every permission documents who controls it, who can use it, what it exposes, how mobile receives it, and which risk or audit expectation applies.
- Role and permission decisions must follow [Admin Control Center Logic](admin-control-center-logic.md): every role-sensitive control has a named scope, mobile effect, API context, audit expectation, support meaning, and offline behavior.
- Role and permission decisions must follow [Feature Flag Logic](feature-flag-logic.md): role and user-level feature access can refine availability only inside global, tenant, plan, version, permission, and safety boundaries.
- Role and permission decisions must follow [Remote Configuration Logic](remote-configuration-logic.md): role or user presentation config can change safe UX behavior but cannot grant authority, billing access, tenant access, or permissions.
- Role and permission decisions must follow [Mobile Version Control Logic](mobile-version-control-logic.md): support, release, internal, invited, suspended, and guest states may affect version visibility, update prompts, maintenance access, and blocked old-version behavior without bypassing API authority.
- Every role must be enforced by the Admin/API system.
- The mobile client must receive role-derived capability state through the API.
- Roles should follow least privilege.
- Tenant-scoped roles must never cross tenant boundaries.
- Suspended and invited states restrict access even if the person would otherwise have a role.
- Support and billing roles can see operational context only for their job.
- UI visibility is never authorization; server-side policy remains final.

Roles define authority and visibility. The [SaaS Value Map](saas-value-map.md) defines product value. A stakeholder may receive value from a feature without receiving direct control over it; for example, a tenant business benefits from reports and offline sync, while the tenant admin or support team may be the role that actually sees the management surface.

Roles also depend on [Two-System Boundary Logic](two-system-boundary.md). Admin/API enforces role authority, while mobile only renders API-derived capability state and account-state restrictions.

Roles also depend on [API-First Principles](api-first-principles.md). Role and account-state behavior must reach mobile through predictable API context, permissions, feature state, errors, and tenant-scoped responses.

Roles also depend on [Admin/API Responsibilities](admin-api-responsibilities.md). Responsibility ownership explains which control-plane areas each role may operate, observe, or consume as API-derived mobile state.

Roles also depend on [Mobile Client Responsibilities](mobile-client-responsibilities.md). Mobile responsibilities explain how mobile users, invited users, suspended users, and guest/pre-login users experience local UX, session, cache, drafts, sync state, device permissions, and feature visibility without gaining authority.

## Role Summary

| Role | Scope | Primary purpose |
| --- | --- | --- |
| Platform owner | Whole SaaS | Owns business, strategy, highest-level platform control, and ultimate accountability. |
| Super admin | Whole SaaS | Operates the platform with broad administrative authority under owner policy. |
| Tenant admin | One tenant | Controls tenant users, settings, enabled modules, and tenant operations. |
| Tenant manager | One tenant or tenant unit | Manages day-to-day workflows and teams inside configured limits. |
| Support agent | Platform support scope | Helps users and tenants using safe diagnostics and support tools. |
| Billing manager | Platform or tenant billing scope | Manages plans, invoices, billing state, quotas, and entitlement context. |
| Mobile user | Tenant/mobile scope | Performs allowed work in the NativePHP mobile client. |
| Invited user | Pending tenant/user scope | Has an invitation but has not completed activation. |
| Suspended user | Previously valid scope | Is blocked from normal access until reactivated. |
| Guest/pre-login user | Public/pre-auth scope | Can access only public or authentication-related flows. |

## Platform Owner

The platform owner is the highest business authority for the SaaS.

Responsibilities:

- Define product direction, commercial model, platform-level policies, and operating principles.
- Appoint or remove super admins.
- Approve dangerous global controls such as platform-wide maintenance, global feature kill switches, production billing provider changes, and data-retention policy changes.
- Own legal, compliance, privacy, and business-risk accountability.

Limitations:

- Should not perform routine tenant support or day-to-day tenant management.
- Should not bypass audit, approval, or reason-capture for sensitive changes.
- Should not use owner authority as a substitute for tenant-scoped roles.

Should see/control:

- Global tenant list and platform health.
- Global feature flags, app-version policy, billing posture, risk dashboards, and audit summaries.
- Super admin assignments and highest-risk platform settings.
- Cross-tenant reports in aggregated or authorized operational form.

Should not see/control by default:

- Tenant private content unless a policy, legal, security, or support reason requires it.
- Mobile user local-only data that has not been synced or submitted.

## Super Admin

The super admin is the highest operational role inside the Admin/API system.

Responsibilities:

- Operate the SaaS control center.
- Manage tenants, tenant admins, support visibility, app-version policy, remote config, feature flags, notifications, reports, and sync policy.
- Investigate platform incidents and coordinate rollbacks.
- Apply emergency controls when product safety or availability requires it.

Limitations:

- Must remain auditable for sensitive changes.
- Should not own business/legal decisions reserved for the platform owner.
- Should not directly perform tenant business workflows unless explicitly acting inside a tenant-scoped role.
- Should not access billing secrets, payment credentials, or private tenant content unless policy allows it.

Should see/control:

- Platform-wide configuration and operational dashboards.
- Tenants, users, devices, app versions, feature rollout state, support queues, and sync health.
- Tenant-level settings when needed for administration or support.

Should not see/control by default:

- Payment provider secrets.
- Unnecessary private tenant payloads.
- Mobile-local unsynced drafts.

## Tenant Admin

The tenant admin is the highest authority inside a tenant.

Responsibilities:

- Manage tenant users, invitations, roles, teams, and access.
- Configure tenant-level settings, enabled modules, notifications, and operational preferences within the plan.
- Review tenant reports, support cases, sync/conflict state, and device/user access for the tenant.
- Coordinate tenant onboarding and internal governance.

Limitations:

- Cannot access other tenants.
- Cannot override platform-owner or super-admin global policy.
- Cannot enable features not allowed by tenant plan, app-version policy, or platform rollout.
- Cannot bypass API authorization by changing mobile UI state.

Should see/control:

- Tenant dashboard, tenant users, tenant devices, tenant feature state, tenant reports, tenant support cases, tenant notification settings, and tenant sync/conflict status.
- Role assignment inside tenant-defined limits.
- Invitations and suspensions inside the tenant.

Should not see/control:

- Platform-wide settings.
- Other tenants.
- Billing provider internals unless also granted billing scope.
- Mobile-local unsynced drafts except where submitted through support diagnostics.

## Tenant Manager

The tenant manager handles day-to-day operations inside the tenant.

Responsibilities:

- Manage assigned teams, mobile users, work queues, records, reports, and operational status.
- Monitor workflow completion, conflicts, pending sync, and user/device readiness for their area.
- Help mobile users resolve routine operational issues.

Limitations:

- Cannot manage tenant billing, global tenant settings, platform policy, or high-risk feature controls.
- Cannot grant permissions beyond their own scope.
- Cannot access teams, records, users, or reports outside their assigned tenant/unit scope.
- Cannot reactivate suspended users unless tenant policy grants that ability.

Should see/control:

- Assigned tenant workflows, users, teams, operational reports, and relevant support/sync context.
- Feature state as applied to their team or workflows.
- Limited user management where delegated by tenant admin.

Should not see/control:

- Tenant-wide security settings unless delegated.
- Billing state beyond visible entitlement impact.
- Platform settings or other tenant data.

## Support Agent

The support agent helps platform and tenant users solve problems.

Responsibilities:

- Triage support requests.
- View safe diagnostic context: tenant, user, device, app version, feature flags, remote config version, sync status, and recent non-sensitive errors.
- Guide users through retries, config refreshes, version updates, and escalation paths.
- Escalate bugs, billing issues, security issues, and platform incidents.

Limitations:

- Cannot silently change tenant policy, billing, feature flags, or permissions unless explicitly granted.
- Cannot view secrets, payment credentials, private files, or mobile-local unsynced drafts by default.
- Cannot impersonate users without explicit audited policy.
- Must not use support access for tenant administration.

Should see/control:

- Support queue, ticket timeline, safe diagnostics, tenant context, app version, device state, sync/conflict summaries, and recent relevant admin/config changes.
- Support actions such as request logs, ask user to retry, mark ticket status, escalate, or trigger safe config refresh if policy allows.

Should not see/control:

- Broad tenant data unrelated to the support case.
- Payment secrets or private sensitive payloads.
- Global platform settings.

## Billing Manager

The billing manager handles commercial access and entitlement state.

Responsibilities:

- Manage plans, invoices, payment status, quotas, billing contacts, and entitlement outcomes.
- Coordinate failed payment restrictions, upgrades, downgrades, renewals, and account limitations.
- Explain billing-driven feature availability to tenant admins and support agents.

Limitations:

- Should not manage operational tenant workflows unless separately granted.
- Should not view mobile content or private tenant data unless needed for billing support and allowed by policy.
- Cannot override security, tenant isolation, or app-version policy.
- Cannot change payment provider credentials unless platform owner/super admin policy allows it.

Should see/control:

- Tenant billing state, plan, quotas, invoices, payment status, entitlement summary, and billing-related audit trail.
- Feature availability only as billing/entitlement outcome.

Should not see/control:

- Tenant operational records unrelated to billing.
- Mobile-local data.
- Platform security controls outside billing scope.

## Mobile User

The mobile user performs work in the NativePHP mobile client.

Responsibilities:

- Use mobile workflows allowed by tenant role, feature flags, app version, device policy, and offline policy.
- Complete assigned tasks, records, media capture, check-ins, notifications, or other enabled workflows.
- Resolve visible pending, failed, or conflict states when prompted.
- Request support when local or sync state blocks progress.

Limitations:

- Cannot access admin controls.
- Cannot choose their own tenant authority, permission state, billing state, feature flags, or app-version policy.
- Cannot make server-trusted changes without API confirmation.
- Cannot treat offline local state as final business truth.

Should see/control:

- Only mobile screens and actions allowed by server-provided policy.
- Their own profile/session/device state as allowed.
- Sync status, offline status, pending actions, conflicts, and feature-disabled explanations.

Should not see/control:

- Admin settings.
- Billing internals.
- Other users' private data unless a workflow grants it.
- Raw feature flag/config internals.

## Invited User

The invited user has been invited but has not completed activation.

Responsibilities:

- Accept or decline invitation.
- Complete required registration, verification, consent, and onboarding steps.
- Join only the tenant and role scope granted by the invitation.

Limitations:

- Cannot use normal admin or mobile workflows until activation is complete.
- Cannot change invitation scope.
- Cannot access tenant data before required verification/acceptance.
- Invitation may expire, be revoked, or be replaced.

Should see/control:

- Invitation details safe for pre-activation display: tenant name, inviter, role summary, expiration, and next step.
- Registration/login/verification flows.

Should not see/control:

- Tenant data, admin dashboards, mobile workflows, billing state, or support data before activation.

## Suspended User

The suspended user was previously valid but is blocked from normal access.

Responsibilities:

- Resolve account, security, billing, policy, or tenant-admin issue through the allowed recovery/support path.
- Stop using mobile/admin workflows until reactivated.

Limitations:

- Cannot access normal admin, API, or mobile workflows.
- Cannot sync new writes as trusted actions.
- Cannot accept new invitations into the same blocked context unless policy allows.
- Cannot use cached mobile UI as authority after suspension.

Should see/control:

- Minimal suspension notice.
- Allowed support/contact/recovery actions.
- Logout/session cleanup actions.

Should not see/control:

- Tenant data, mobile work queues, admin controls, billing management, reports, or support internals.

## Guest / Pre-Login User

The guest/pre-login user has no authenticated product identity yet.

Responsibilities:

- Register, log in, verify email or phone where required, reset password, accept invitation, or read public/legal information.
- Decide whether to continue into authenticated product flows.

Limitations:

- Cannot access tenant data, admin controls, mobile workflows, device trust state, reports, support case history, billing state, or feature-enabled app behavior.
- Cannot call authenticated API endpoints.
- Cannot rely on local mobile state as an authenticated session.

Should see/control:

- Login, registration, password reset, invitation acceptance, email verification, legal/privacy/support contact pages, and safe public status messages.

Should not see/control:

- Tenant-specific data or capabilities.
- Authenticated API responses.
- Feature flags beyond safe public/pre-login copy.

## Visibility And Control Matrix

| Area | Platform owner | Super admin | Tenant admin | Tenant manager | Support agent | Billing manager | Mobile user | Invited user | Suspended user | Guest/pre-login |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Global platform policy | Control | Operate | No | No | View only if needed | No | No | No | No | No |
| Tenant settings | View/control by policy | View/control by policy | Control own tenant | Limited | View for cases | Billing-related only | No | No | No | No |
| Users and roles | Control high-level | Control | Control own tenant | Limited | View for cases | Billing contacts only | Own profile | Invitation only | Recovery only | No |
| Billing and entitlements | Control policy | Operate by grant | View tenant outcome | View impact only | View support-safe outcome | Control billing scope | View allowed/blocked state | No | Recovery notice only | No |
| Feature flags/config | Control policy | Control | Tenant-scoped control | View/use allowed | View for cases | Entitlement impact | Render allowed state | No | No | No |
| App version/device policy | Control policy | Control | View/control own tenant by policy | View assigned scope | View for cases | No | View own device/update state | No | No | No |
| Support cases | Oversight | Oversight/control | Own tenant | Assigned scope | Control support workflow | Billing cases | Own cases | Invitation help only | Recovery case only | Public contact only |
| Reports | Global/aggregate | Global/tenant | Own tenant | Assigned scope | Case-related | Billing reports | Personal/task state only | No | No | No |
| Mobile workflows | No direct use by default | No direct use by default | Configure | Monitor/manage | Support context | Entitlement context | Use allowed workflows | No | No | No |
| Offline queue/sync | Oversight | Oversight/control policy | Own tenant view | Assigned scope | Case diagnostics | Entitlement impact | Own visible state | No | No trusted replay | No |
| API context | Policy context | Operate context | Tenant context | Assigned context | Case context | Billing context | User/mobile context | Activation context | Recovery context | Public/pre-login context |
| Admin/API responsibilities | Own/control by policy | Operate by grant | Tenant-scoped control | Assigned scope | Case/support view | Billing scope | API outcome only | Activation outcome only | Recovery outcome only | Public/pre-login outcome only |
| Mobile client responsibilities | No direct use by default | No direct use by default | Configure/observe outcomes | Observe assigned outcomes | Support-safe diagnostics | Entitlement outcome | Use local UX | Activation UX only | Recovery UX only | Public/pre-login UX only |

## Role Value Alignment

The value map should be used with this role model before feature planning:

| Value stakeholder | Closest role surface | Value boundary |
| --- | --- | --- |
| Platform owner | Platform owner and super admin | Strategic value and operational value are related, but owner accountability should not bypass super-admin audit controls. |
| Tenant business | Tenant admin and tenant manager | The business benefits from governed mobile operations, while tenant roles receive only the controls their job requires. |
| Tenant admin | Tenant admin | Tenant value is tenant-scoped and cannot override platform, plan, app-version, or security policy. |
| Mobile worker/client | Mobile user plus invited/suspended/pre-login states | Mobile value is simple allowed work, not access to admin machinery. |
| Support team | Support agent | Support value is safe diagnostics and case resolution, not broad tenant administration. |
| Billing/operations team | Billing manager | Billing value is entitlement and operational control, not tenant workflow ownership. |

## State Rules

Some entries are account states rather than full roles:

- Invited user is a pending state before activation.
- Suspended user is a blocked state after activation.
- Guest/pre-login user is unauthenticated state.

State restrictions override role permissions. For example, a tenant admin who is suspended should not keep tenant-admin access, and an invited support agent should not see support cases before activation.

## Boundaries

This role model does not implement:

- Role database records.
- Permission enums.
- Policies.
- Guards.
- Middleware.
- Invitations.
- Suspension logic.
- Tenant membership tables.
- Admin screens.
- Mobile screens.
- API endpoints.

Those belong in future implementation prompts with tests, migrations, policies, and acceptance criteria.

## Risks

| Risk | Product response |
| --- | --- |
| Platform owner and super admin blur together | Keep owner as business/accountability role and super admin as operational role. |
| Tenant manager becomes hidden tenant admin | Limit manager scope to assigned teams/workflows and avoid tenant-wide settings by default. |
| Permission ownership is undocumented | Use Documentation-First Architecture so every permission states who controls it, how mobile receives it, and what risk/audit expectations apply. |
| Support sees too much private data | Support gets safe diagnostics and case-scoped context, not broad tenant access. |
| Billing manager changes operational access | Billing controls entitlements and invoices, not day-to-day workflows. |
| Stakeholder value is confused with role authority | Use the SaaS value map to identify who benefits and this role model to decide who can see or control. |
| API context leaks role or tenant data | Use API-first principles so user, permission, feature, config, version, and tenant context are predictable and scoped. |
| Mobile infers role authority locally | Use the two-system boundary: mobile may cache role-derived capability state, but Admin/API must enforce final access. |
| Role grants do not map to responsibility ownership | Use Admin/API responsibilities to decide which control-plane area a role may operate, observe, or consume. |
| Mobile states become role authority | Use mobile-client responsibilities to keep UX, cache, drafts, sync, permissions prompts, and feature visibility as API-derived presentation. |
| Mobile user gains authority offline | Offline actions remain intents until API acceptance. |
| Invited or suspended states leak access | State restrictions override role permissions. |
| UI hiding becomes authorization | API and policies remain final authority. |

## Success Test

The role model is successful when each person can see and control only what their job requires, every stakeholder value is delivered through the right role surface, every sensitive action is server-authorized and auditable, tenant boundaries remain clear, and mobile capability state is derived from API-controlled role and account state.
