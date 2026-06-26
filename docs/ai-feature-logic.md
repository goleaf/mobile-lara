# AI Feature Logic

Acceptance Principles are defined in `acceptance-principles.md`:
every feature must document purpose, admin control, mobile behavior,
API dependency, offline behavior, permission behavior, feature flag
behavior, tenant behavior, error behavior, security behavior, and
documentation requirements before implementation planning begins.

Risk Map is defined in `risk-map.md`:
API dependency, offline sync, tenant isolation, mobile secure storage,
NativePHP plugin availability, app store releases, forced updates,
feature flag mistakes, billing restrictions, admin misconfiguration,
support access, privacy, and data conflicts must document prevention
principles and documentation requirements before implementation.

Testing Strategy Principles are defined in `testing-strategy-principles.md`:
future tests for API contracts, admin controls, mobile feature visibility,
permissions, feature flags, remote config, authentication, tenant isolation,
offline sync, conflict behavior, native feature fallbacks, notification flows,
billing rules, and app version rules must map to documented authority, risk,
and user-visible behavior before implementation.

Release And Versioning Principles are defined in `release-versioning-principles.md`:
API versioning, mobile app versioning, admin releases, feature rollout,
rollback, app store release, forced update, documentation update, and Git
change-history decisions must preserve documented authority, compatibility,
rollback, support, audit, and user-visible behavior before release.

Documentation Audit is defined in `documentation-audit.md`:
project documentation for two-system architecture, Admin/API authority, mobile
client execution, API-first communication, feature flags, remote config,
tenancy, permissions, offline sync, NativePHP features, notifications, billing,
support, reports, security, risks, and release principles must use consistent
authority language and resolve contradictions before implementation.

Updated: 2026-06-26

This document defines AI feature logic for Mobile Lara as an optional future
module. It explains AI assistant purpose, summarization, categorization, smart
suggestions, moderation assistance, report generation assistance, admin control
of AI features, tenant opt-in principles, privacy principles, and human-review
principles. It is documentation only and does not define database structure,
database fields, migrations, indexes, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, plugin manifests, policies,
gates, middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, queues, prompts, prompt templates, vector stores,
embeddings, model providers, provider integrations, provider configuration,
tool-calling integrations, eval pipelines, billing-provider implementation, or
application logic.

Use this document with [Module Selection
Principles](module-selection-principles.md), [Product
Principles](product-principles.md), [Documentation-First
Architecture](documentation-first-architecture.md), [Two-System Boundary
Logic](two-system-boundary.md), [API-First Principles](api-first-principles.md),
[Admin/API Responsibilities](admin-api-responsibilities.md), [Mobile Client
Responsibilities](mobile-client-responsibilities.md), [Admin Control Center
Logic](admin-control-center-logic.md), [Feature Flag
Logic](feature-flag-logic.md), [Remote Configuration
Logic](remote-configuration-logic.md), [Billing And Plan
Logic](billing-and-plan-logic.md), [Role And Permission
Logic](role-permission-logic.md), [Data Privacy
Principles](data-privacy-principles.md), [Audit Logic](audit-logic.md),
[Tenant Lifecycle Logic](tenant-lifecycle-logic.md), [Tenant Admin
Logic](tenant-admin-logic.md), [Multi-Tenant Mobile
Logic](multi-tenant-mobile-logic.md), [Offline-First
Principles](offline-first-principles.md), [Offline UX
Logic](offline-ux-logic.md), [Sync Lifecycle
Logic](sync-lifecycle-logic.md), [Conflict Resolution
Logic](conflict-resolution-logic.md), [Mobile UX
Principles](mobile-ux-principles.md), [Mobile App Shell
Logic](mobile-app-shell-logic.md), [Mobile Dashboard
Logic](mobile-dashboard-logic.md), [Mobile Settings
Logic](mobile-settings-logic.md), [Mobile Permission
Logic](mobile-permission-logic.md), [Mobile App Lock
Principles](mobile-app-lock-principles.md), [Native Feature
Strategy](native-feature-strategy.md), [Records/Content Module
Logic](records-content-module-logic.md), [Search Logic](search-logic.md),
[Forms And Drafts Logic](forms-drafts-logic.md), [Notifications
Logic](notifications-logic.md), [Support System
Logic](support-system-logic.md), [Reporting Logic](reporting-logic.md),
[Messaging And Community Logic](messaging-community-logic.md), [Camera And
Media Logic](camera-media-logic.md), [Voice Note
Logic](voice-note-logic.md), [Device, Network, And Diagnostics
Logic](device-network-diagnostics-logic.md), [Field Service
Logic](field-service-logic.md), [Logistics Delivery
Logic](logistics-delivery-logic.md), [Booking Logic](booking-logic.md), and
[Commerce Logic](commerce-logic.md): AI is an optional assistive module, while
Admin/API remains authoritative for AI availability, tenant opt-in, plan
entitlement, feature flags, allowed data sources, permission boundaries, prompt
scope, provider-neutral policy, cost limits, rate limits, audit, retention,
human review, privacy, support visibility, reporting, and final decisions.

## AI Feature Statement

AI features exist to assist users and admins with understanding, summarizing,
organizing, drafting, suggesting, moderating, and reporting. AI must not become
the system of record, a permission authority, a tenant authority, a billing
authority, a moderation authority, a legal authority, or a replacement for
human judgment where risk is meaningful.

The product goal is not to add AI everywhere. The goal is to add AI only where
it reduces effort, improves clarity, helps users recover context, or assists
admins with review while preserving tenant isolation, permission boundaries,
privacy, auditability, opt-in, and human control.

Product rule: AI output is advisory. Admin/API owns the source data, allowed
context, feature eligibility, prompt boundaries, result acceptance, audit,
retention, and final action. Mobile may present AI suggestions, summaries, and
drafts, but the user or authorized admin must decide whether to accept, edit,
ignore, report, or escalate the result.

## Goals

AI feature logic should:

- Make repetitive interpretation work faster without bypassing Admin/API.
- Help mobile users understand permitted records, messages, notifications,
  support cases, sync state, forms, or reports with clear uncertainty labels.
- Help admins review support, moderation, reports, configuration impact, and
  tenant operations without exposing data beyond role scope.
- Keep every AI capability tenant-enabled, plan-controlled, feature-flagged,
  permission-aware, app-version-safe, opt-in, auditable, and revocable.
- Treat user prompts, retrieved context, model output, provider responses, and
  generated suggestions as untrusted until validated, reviewed, and accepted.
- Minimize data sent to any AI capability and avoid sensitive data by default.
- Require human review for decisions that affect users, billing, permissions,
  moderation, support outcomes, tenant status, records, reports, or external
  communication.

AI feature logic should not:

- Define provider logic, provider selection, provider credentials, model names,
  prompt templates, embeddings, vector storage, tool execution, or code.
- Let AI bypass API, permissions, tenant boundaries, feature flags, plan
  limits, app-version policy, moderation policy, or privacy policy.
- Treat AI output as trusted truth, authoritative classification, final
  moderation, final report, final support answer, final billing answer, or
  final system action.
- Send secrets, tokens, passwords, PINs, payment secrets, private keys,
  biometric data, raw diagnostics, unrelated tenant data, or broad exports to
  AI context.
- Let mobile run AI features while offline unless the behavior is explicitly
  documented as local-only and non-authoritative.
- Hide AI involvement from users or admins where it affects trust, privacy,
  decisions, support, moderation, or reports.

## AI Assistant Purpose

The AI assistant is an optional helper surface. It should answer only within
documented product boundaries, current tenant context, current user
permissions, enabled features, tenant opt-in, and allowed data sources.

The assistant may help with:

- Explaining current app state, such as offline, sync, feature-disabled,
  permission-blocked, maintenance, forced-update, or support-needed states.
- Finding relevant permitted records, messages, support cases, notifications,
  settings, reports, or help content.
- Summarizing allowed content into short mobile-friendly explanations.
- Drafting user-editable replies, support messages, notes, or report text.
- Suggesting next actions that are available through normal UI and API rules.
- Helping admins understand configuration impact, moderation queues, support
  context, reporting questions, or feature rollout risk.

Assistant principles:

- AI assistant output is guidance, not command execution.
- Every assistant answer should be scoped to the current tenant and role.
- The assistant should say when it lacks permission, lacks context, or cannot
  answer safely.
- The assistant should not invent tenant policy, billing rules, permissions,
  app-version requirements, legal advice, medical advice, security decisions,
  or compliance outcomes.
- The assistant should not expose hidden reasoning, secret instructions,
  provider details, system prompts, or internal risk labels to users.

## Summarization

Summarization reduces reading effort by creating shorter explanations of
permitted content. It is useful for support cases, long records, activity
history, message threads, reports, sync conflicts, notifications, and admin
impact previews.

Summarization should:

1. Use only content the current user or admin is allowed to access.
2. Preserve tenant scope and record/module scope.
3. Label the summary as AI-assisted where trust matters.
4. Preserve links back to source content where possible.
5. Avoid summarizing hidden, restricted, deleted, sealed, legal-hold,
   cross-tenant, or unsupported content.
6. Avoid turning uncertain or incomplete content into a confident conclusion.
7. Require human review before a summary is sent to another user, used in a
   support decision, used in moderation, used in billing, or used in reports.

Summarization principles:

- A summary is not source truth.
- Users should be able to inspect the original permitted content.
- Admin/API owns summary eligibility, source selection, retention, audit, and
  whether the summary may be stored.
- Mobile may show cached summaries only with freshness and source-context
  labels.

## Categorization

Categorization helps organize content into labels, types, priorities,
sentiment, severity, themes, topics, or queues. It can assist support routing,
record organization, message moderation, report grouping, notification
triage, or admin review.

Categorization should:

1. Be treated as a suggestion until accepted by a user, admin, moderator,
   support agent, or documented automated policy.
2. Keep the current tenant, role, module, plan, and feature boundaries.
3. Explain confidence and uncertainty in user-friendly terms where useful.
4. Allow correction, override, or dismissal.
5. Avoid protected-class, discriminatory, sensitive, or legally risky
   inferences unless explicitly documented, lawful, necessary, reviewed, and
   privacy-approved.
6. Be audited when accepted categories affect routing, visibility, reports,
   moderation, support priority, billing operations, or user outcomes.

Categorization principles:

- AI may propose categories; Admin/API decides what categories exist and what
  accepted categories mean.
- Accepted categories should be traceable to source context and reviewer
  action where risk matters.
- Mobile should never silently reclassify server data based on local AI output.

## Smart Suggestions

Smart suggestions help users decide what to do next without removing their
agency. Suggestions may include next actions, draft replies, recommended
filters, likely support steps, missing form fields, relevant records, possible
conflict resolutions, or admin impact warnings.

Smart suggestions should:

1. Suggest only actions the current user can actually perform.
2. Make it clear that suggestions are optional.
3. Never execute destructive, billing, permission, tenant lifecycle,
   moderation, export, notification, support-resolution, or data-deletion
   actions without explicit human confirmation.
4. Prefer low-risk helpfulness: explain, draft, prefill, recommend, or link.
5. Avoid dark patterns, pressure, misleading certainty, or hidden upsell
   logic.
6. Respect remote config, feature flags, rate limits, cost limits, and tenant
   opt-in.

Suggestion principles:

- AI should make the normal workflow easier, not create hidden workflows.
- Accepted suggestions should pass the same validation and authorization as
  manually entered input.
- Rejected or ignored suggestions should not penalize the user.
- Suggestions should be unavailable or clearly disabled when offline unless
  documented as local-only and safe.

## Moderation Assistance

AI may help moderators identify, triage, summarize, or prioritize potentially
unsafe content. It must not be the final moderation authority unless a future
policy explicitly documents a narrow, low-risk automated action.

Moderation assistance may help with:

- Flagging likely spam, harassment, abuse, unsafe content, impersonation,
  privacy leakage, or policy violations.
- Summarizing report context for moderators.
- Grouping duplicate reports.
- Suggesting moderation categories.
- Highlighting content that needs urgent human review.
- Drafting moderator notes or user-facing explanations for human editing.

Moderation assistance principles:

- Human review is required for punitive, visibility-changing, account-changing,
  tenant-changing, legal, or high-impact moderation outcomes.
- Reporter identity, hidden content, moderator notes, risk scores, and internal
  labels must remain protected.
- AI flags should be auditable and correctable.
- False positives and false negatives should be expected and handled.
- AI moderation should not expose internal detection logic to users in a way
  that enables abuse.

## Report Generation Assistance

AI may assist admins with report interpretation and draft report narratives.
It should not create official numbers, invent metrics, bypass privacy rules,
or replace reporting definitions.

Report generation assistance may help with:

- Drafting plain-language explanations of existing report data.
- Summarizing trends already visible to the current admin.
- Suggesting possible questions to investigate.
- Highlighting incomplete data, stale data, missing filters, or risky
  interpretation.
- Drafting export descriptions, executive summaries, support notes, or
  operational observations for human review.

Report assistance principles:

- Admin/API owns report definitions, filters, metrics, aggregation meaning,
  privacy boundaries, date ranges, exports, audit, and retention.
- AI may explain a report; it must not become the report source.
- AI-generated report text should include uncertainty and source context.
- Human review is required before report text is exported, shared, used for
  billing, used for compliance, or used for tenant/account decisions.

## Admin Control Of AI Features

AI features are high-impact controls because they affect privacy, cost,
trust, support, moderation, reporting, and user behavior.

Admins should control:

- Whether AI is globally available, unavailable, beta, suspended, deprecated,
  or retired.
- Which tenants, plans, roles, users, modules, app versions, and device
  contexts may use AI features.
- Which AI capabilities are enabled: assistant, summarization, categorization,
  smart suggestions, moderation assistance, report assistance, draft help, or
  search assistance.
- Which data sources are allowed per capability.
- Which sensitive fields must be excluded.
- Whether outputs may be stored, cached, exported, audited, or used only
  ephemerally.
- Rate limits, cost limits, daily quotas, tenant quotas, abuse limits, and
  emergency disable behavior.
- Human-review requirements for high-risk outputs.
- User-facing disclosure, tenant-facing disclosure, support visibility, and
  admin impact previews.

Admin control principles:

- AI defaults should be off or limited until tenant opt-in and policy are
  documented.
- Dangerous AI setting changes should require confirmation, impact preview,
  audit history, rollback thinking, and tenant isolation.
- Tenant admins may control AI only when platform policy delegates that
  control.
- Support agents should not enable AI for a tenant unless they have explicit
  permission.
- Billing users should see entitlement and cost posture, not private prompts or
  outputs unless explicitly allowed.

## Tenant Opt-In Principles

Tenant opt-in protects trust and makes AI a deliberate product choice.

Tenant opt-in should:

1. Explain what AI capabilities are enabled.
2. Explain what tenant data sources may be used.
3. Explain what data is excluded.
4. Explain whether outputs are stored, cached, retained, audited, or used only
   ephemerally.
5. Explain who can use AI and who can see outputs.
6. Explain human-review requirements.
7. Explain cost, plan, quota, and rate-limit posture.
8. Explain how to disable AI and what happens to existing AI outputs.

Opt-in principles:

- Tenant opt-in should not override platform safety policy.
- Platform policy may disable AI globally or per tenant even if a tenant opted
  in.
- Plan entitlement may make AI unavailable, limited, trial-only, or paid, but
  plan access alone is not consent.
- Users should receive appropriate disclosure when they interact with AI
  output or AI-assisted workflows.
- Opt-out should be respected and auditable.

## Privacy Principles

AI privacy rules must be stricter than ordinary feature rules because model
context can combine data in unexpected ways and output can be persuasive even
when wrong.

Privacy principles:

- Send the minimum necessary context.
- Use tenant-scoped, permission-scoped, purpose-scoped context only.
- Exclude secrets, tokens, passwords, PINs, private keys, payment secrets,
  biometric data, raw diagnostics, hidden moderation data, unrelated tenant
  data, unrelated user data, and unsupported exports by default.
- Treat prompts, context, retrieved snippets, model outputs, provider
  responses, evaluation notes, and accepted AI results as sensitive data.
- Keep AI data retention explicit and short by default where possible.
- Do not use tenant data to improve shared models unless explicit future
  policy, tenant consent, legal review, and product documentation allow it.
- Do not expose hidden prompts, internal policy, moderation risk scores,
  provider internals, or secret system instructions.
- Partition any future retrieval, embeddings, caches, logs, and diagnostics by
  tenant and permission boundary.
- Make diagnostics and support visibility redacted and purpose-limited.

## Human-Review Principles

Human review is required when AI output could materially affect a user,
tenant, admin decision, support result, moderation result, billing result,
report, export, security posture, privacy posture, or external communication.

Human review should be required before AI output:

- Changes a record, status, category, priority, permission, tenant setting,
  billing setting, feature flag, remote config, report, support case, or
  moderation outcome.
- Sends a notification, message, support reply, admin announcement, export, or
  external communication.
- Hides, removes, blocks, suspends, escalates, or penalizes a user or tenant.
- Produces a report narrative used for business, billing, compliance, legal,
  or tenant decisions.
- Resolves a sync conflict or discards user work.
- Handles high-risk content, sensitive data, protected classes, private
  diagnostics, legal topics, health topics, finance topics, or safety topics.

Human-review principles:

- The reviewer should see source context, AI output, confidence/uncertainty,
  risk labels where appropriate, and available actions.
- Review actions should be auditable.
- The user should be able to edit AI drafts before sending where they own the
  action.
- Admins should be able to override AI suggestions.
- Human review is not a ceremonial checkbox; it must allow real rejection,
  correction, escalation, or rollback.

## Mobile AI Behavior

Mobile AI should be simple, clear, and honest.

Mobile should:

1. Show AI entry points only when API says the feature is available.
2. Explain what context AI will use before requesting sensitive help.
3. Label AI-assisted content where trust matters.
4. Let users accept, edit, copy, retry, dismiss, report, or give feedback where
   appropriate.
5. Show clear loading, unavailable, rate-limited, plan-blocked,
   permission-blocked, feature-disabled, maintenance, and offline states.
6. Avoid asking for native permissions unless the enabled AI capability needs
   an attachment, voice note, media, file, diagnostics, or notification path.
7. Avoid storing AI prompts or outputs locally unless policy allows it.

Offline principles:

- AI features should be online-only by default.
- Offline mobile may preserve a user draft prompt or note only where policy
  allows.
- Offline mobile should not submit AI requests, infer authoritative results,
  or replay prompts across tenants without API revalidation.
- Cached AI output should show freshness, source context, and non-authority
  labels.

## API-First AI Principles

AI behavior must remain API-first even when mobile presents the assistant UI.

API-first AI principles:

- Mobile never calls an AI provider directly.
- API decides whether AI is available for the current tenant, user, plan,
  feature, app version, and capability.
- API decides allowed data sources and redaction policy.
- API returns predictable mobile-safe AI states and user-friendly errors.
- API validates AI outputs before they are accepted into any workflow.
- API protects tenant boundaries and permission boundaries server-side.
- API logs/audits AI actions according to risk, retention, support, and
  privacy policy.

## Reporting Principles

AI reporting should help platform and tenant admins understand usage, value,
cost, quality, risk, and support impact without exposing prompts or private
outputs broadly.

AI reports may measure:

- Feature usage by tenant, role, capability, module, time period, and app
  version.
- Opt-in, opt-out, disabled, blocked, and rate-limited states.
- Acceptance, edit, dismissal, retry, and feedback rates.
- Human-review queue volume and outcome categories.
- Moderation assistance volume and escalation trends.
- Report assistance usage and export-review status.
- Error, timeout, unavailable, rejected, and cost-limit states.
- Privacy or safety incident counts at an aggregate level.

AI reports should not:

- Expose raw prompts or outputs to broad audiences.
- Expose cross-tenant data or hidden source content.
- Treat AI usage volume as quality or productivity by itself.
- Expose provider internals, hidden prompts, risk scores, or private review
  notes except to explicitly authorized reviewers.

## Support Principles

Support teams may use AI only inside documented support boundaries.

Support AI may help:

- Summarize a case for the assigned support agent.
- Draft a reply for human editing.
- Suggest likely troubleshooting steps.
- Explain app version, sync, offline, tenant, feature flag, or permission
  context.
- Categorize support cases for queue routing.

Support AI must not:

- Send replies without human review when the answer affects user access,
  billing, security, privacy, moderation, tenant status, or data changes.
- Reveal data outside the support case scope.
- Ask users for secrets or sensitive data.
- Override support agent limitations.
- Hide uncertainty from agents or users.

## Rollout And Rollback Principles

AI should roll out slower than ordinary UI features because it affects trust,
privacy, cost, moderation, reports, and support.

Rollout principles:

- Begin with provider-neutral product documentation, not provider integration.
- Start with low-risk capabilities such as draft assistance or summaries of
  already-visible content.
- Use platform gates, tenant opt-in, plan gates, role gates, feature flags,
  app-version gates, rate limits, cost limits, and pilot cohorts.
- Require admin impact preview before enabling AI: data sources, users,
  modules, costs, privacy, support, reports, moderation, and human-review
  load.
- Monitor feedback, corrections, rejected outputs, escalations, support load,
  privacy incidents, cost, latency, and blocked states.

Rollback principles:

- Emergency disable should stop new AI requests, hide AI entry points, preserve
  user drafts according to policy, and explain unavailable states.
- Disabling AI should not delete accepted user-owned content unless retention
  policy requires it.
- Stored AI outputs should follow documented retention, export, deletion, and
  audit policy.
- Rollback should not expose cross-tenant data or bypass API authority.

## Risks

Key risks:

- AI output is mistaken for truth.
- AI bypasses tenant, permission, feature, or plan boundaries.
- Sensitive data is sent to an AI provider or retained unexpectedly.
- Prompt injection or malicious content manipulates AI behavior.
- AI-generated reports overstate certainty or invent conclusions.
- AI categorization creates unfair, discriminatory, or unsupported outcomes.
- AI moderation creates harmful false positives or false negatives.
- AI suggestions execute risky actions without real confirmation.
- Tenant opt-in is unclear or bundled into plan access.
- Admins cannot audit what AI affected.
- Users cannot tell when AI assisted a result.

Risk controls:

- Keep Admin/API authoritative for availability, context, redaction, output
  validation, acceptance, audit, retention, and final decisions.
- Keep AI provider-neutral until implementation is explicitly approved later.
- Treat all prompts, source context, model output, and provider responses as
  untrusted.
- Minimize context and exclude sensitive data by default.
- Require human review for high-impact outcomes.
- Use feature flags, tenant opt-in, plan gates, role gates, rate limits, cost
  limits, app-version gates, audit, support visibility, and rollback controls.
- Document every AI capability before implementation.

## Readiness Checklist

Before implementing any AI behavior, the product documentation should answer:

- Which tenants and plans can use AI?
- Which AI capabilities are enabled: assistant, summarization, categorization,
  smart suggestions, moderation assistance, report assistance, draft help, or
  search assistance?
- Which roles can use each capability?
- Which tenant data sources are allowed and excluded?
- Which data is redacted before AI use?
- Which outputs are ephemeral, stored, cached, retained, exported, audited, or
  deleted?
- Which outputs require human review?
- Which mobile screens can show AI output?
- Which actions can accept AI output and which actions cannot?
- Which support, moderation, reporting, and billing users can see AI usage?
- Which rate limits, cost limits, and plan limits apply?
- What users and admins are told about AI involvement?
- How tenant opt-in and opt-out work?
- What happens when AI is disabled while drafts, summaries, review queues, or
  accepted outputs exist?

## Acceptance Principle

The AI module is ready for implementation only when the team can trace every AI
capability to:

- A documented tenant opt-in rule.
- A documented plan and feature flag rule.
- A documented role and permission rule.
- A documented API authority.
- A documented allowed-source rule.
- A documented redaction and privacy rule.
- A documented human-review rule.
- A documented output lifecycle rule.
- A documented audit and retention rule.
- A documented support visibility rule.
- A documented report meaning.
- A documented rollback rule.
- A documented statement that AI output is advisory, not trusted truth.

If any of those are unclear, the correct next step is more documentation, not
application code or provider integration.
