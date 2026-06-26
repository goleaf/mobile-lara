# Commerce Logic

Updated: 2026-06-26

This document defines commerce module logic for Mobile Lara. It explains
catalog browsing, cart behavior, checkout principles, hosted payment
principles, order lifecycle, invoice and receipt principles, subscription
upsell principles, admin product/control principles, and mobile offline
limitations. It is documentation only and does not define database structure,
database fields, migrations, seeders, routes, controllers, Livewire
components, Filament resources, NativePHP plugins, plugin manifests, policies,
gates, middleware, jobs, services, local storage schemas, API endpoints, UI
components, CSS, JavaScript, queue workers, payment code, payment-provider
integration, webhook handling, invoice generation implementation, report
builders, dashboards, or application logic.

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
Logic](mobile-permission-logic.md), [Native Feature
Strategy](native-feature-strategy.md), [Records/Content Module
Logic](records-content-module-logic.md), [Search Logic](search-logic.md),
[Forms And Drafts Logic](forms-drafts-logic.md), [Notifications
Logic](notifications-logic.md), [Support System
Logic](support-system-logic.md), [Reporting Logic](reporting-logic.md),
[Device, Network, And Diagnostics
Logic](device-network-diagnostics-logic.md), [Field Service
Logic](field-service-logic.md), [Logistics Delivery
Logic](logistics-delivery-logic.md), and [Booking
Logic](booking-logic.md): commerce is an optional industry module that turns
tenant-scoped catalog, cart, checkout, order, receipt, subscription upsell,
and admin product-control work into mobile-visible workflows, while Admin/API
remains authoritative for catalog truth, price truth, inventory truth, cart
acceptance, checkout eligibility, hosted payment handoff, order state,
invoice/receipt state, subscription entitlements, reports, audit, support,
billing, feature flags, and sync decisions.

## Commerce Statement

The commerce module helps tenants sell, reserve, quote, invoice, upsell, or
fulfill products and services through a controlled mobile experience. Admins
control the catalog, pricing posture, inventory rules, checkout eligibility,
order state, subscription upsells, invoices, receipts, reports, support, and
policy. Mobile users browse, search, compare, add to cart, review a server
quote, use a hosted payment handoff when required, and track order outcomes.

The product goal is not to make the mobile client a payment system, inventory
authority, pricing engine, or standalone store backend. The goal is to give
mobile users a simple NativePHP commerce flow while Admin/API controls
commercial truth, tenant policy, plan access, permissions, hosted payment
handoff, reports, support, audit, and conflict decisions.

Product rule: mobile may present catalog data, preserve a local cart draft,
show cached order summaries, open a hosted payment flow when API allows it,
and display receipt/invoice summaries, but a cart is not trusted, checkout is
not final, payment is not accepted, an order is not placed, inventory is not
reserved, and a receipt or invoice is not official until Admin/API accepts the
state.

## Goals

Commerce logic should:

- Let admins control product visibility, pricing posture, inventory posture,
  plan limits, tenant rules, product availability, checkout eligibility,
  subscription upsells, reporting, support, and audit through Admin/API
  authority.
- Let mobile users browse only tenant-enabled, plan-allowed,
  permission-allowed, feature-flag-enabled, app-version-safe, and
  currently visible commerce content.
- Let mobile users understand product availability, price freshness, stock
  posture, cart changes, checkout status, payment handoff, order state,
  invoice/receipt state, subscription upsell state, and offline limits.
- Keep price, inventory, discount, tax, shipping, payment, order, invoice,
  receipt, refund, and subscription entitlement truth in Admin/API.
- Use hosted payment principles so mobile never captures, stores, processes,
  or logs payment-card or payment-secret data.
- Protect tenant boundaries, customer data, order history, invoices, receipts,
  support context, diagnostics, and reports.
- Keep mobile offline behavior useful for browsing and drafting without
  pretending stale commercial data is final.

Commerce logic should not:

- Define payment code or payment-provider integration.
- Let mobile store card data, payment secrets, provider tokens, provider event
  payloads, or raw payment failure internals.
- Let mobile decide price, tax, discount, stock, inventory reservation,
  fulfillment, payment acceptance, refund, invoice issuance, subscription
  entitlement, or order state.
- Treat cached catalog, cached price, cached inventory, local cart, local
  checkout screen, redirect result, or push notification as commercial truth.
- Expose one tenant's products, carts, orders, invoices, receipts, customers,
  reports, support cases, or diagnostics to another tenant.
- Define product tables, cart tables, order tables, checkout endpoints,
  payment webhooks, invoice templates, tax integrations, shipping
  integrations, or code in this document.

## Commerce Meaning

Commerce represents tenant-scoped commercial intent and commercial outcomes.
It may involve physical goods, digital products, services, bookings,
deliveries, subscriptions, add-ons, upgrades, invoices, receipts, discounts,
taxes, fulfillment, support, and reporting.

Admin/API owns authoritative commerce state. Mobile owns local presentation,
search and browsing UX, cart draft UX, hosted payment handoff presentation,
safe cached order display, offline labels, and user feedback.

Commerce should be understood through:

- **Tenant context**: which tenant owns the catalog, products, pricing,
  inventory, orders, receipts, reports, support, and billing rules.
- **Catalog context**: which products, collections, categories, variants, or
  services are visible to the current tenant, plan, role, user, feature flag,
  and app version.
- **Pricing context**: current price, currency, discounts, taxes, fees,
  shipping, subscription effect, and quote freshness.
- **Inventory context**: stock posture, reservation posture, fulfillment
  limits, backorder state, unavailable state, or service capacity posture.
- **Cart context**: local draft items, server quote, validation state,
  warnings, required changes, and checkout readiness.
- **Checkout context**: identity, address, delivery/pickup/digital mode,
  terms, hosted payment eligibility, payment handoff, and confirmation.
- **Order context**: order state, fulfillment state, cancellation/refund/return
  posture, receipt/invoice state, support, and audit.
- **Mobile context**: cache freshness, offline state, pending drafts,
  unavailable actions, safe deep links, and current API authority.
- **Admin context**: product controls, pricing controls, inventory controls,
  order controls, subscription upsells, reporting, support, audit, and risk.

## Catalog Browsing

Catalog browsing helps users find allowed products or services without making
mobile the catalog authority.

Catalog browsing should:

1. Resolve active tenant context before showing products.
2. Show only catalog items allowed by tenant state, plan, role, permission,
   feature flag, app version, remote config, maintenance state, and product
   visibility policy.
3. Support mobile-safe browsing by category, collection, search, filter,
   sort, recent items, saved filters, or scan-to-search where documented.
4. Show product summaries, detail summaries, images, variants, availability
   posture, price labels, and action states only when API allows.
5. Clearly label unavailable, hidden, plan-blocked, permission-blocked,
   out-of-stock, request-only, subscription-required, coming-soon,
   discontinued, maintenance-blocked, or offline-stale states.
6. Avoid displaying exact stock, internal cost, supplier data, private
   customer pricing, or restricted product metadata unless the role allows it.
7. Treat cached product data as display cache, not purchase authority.

Catalog principles:

- Admin/API owns catalog truth and product visibility.
- Mobile may cache catalog summaries where policy allows, but cache freshness
  must be visible when it affects decisions.
- Search, filter, and sort should preserve tenant isolation and permission
  boundaries.
- Product images and media should follow camera/media and privacy principles
  when uploaded or shown.
- Product labels, categories, merchandising, and unavailable-state copy may be
  remotely configurable, but the resolved catalog state must come from API.
- Catalog deep links must re-check current API authority before showing
  protected product detail.

## Cart Behavior

A cart is a user draft for commercial intent. It is not an order, invoice,
reservation, payment, or stock hold until Admin/API accepts the relevant
state.

Cart behavior should:

1. Let users add, remove, change quantity, select variant/options, save for
   later, or clear items when the module, tenant, plan, role, feature flag,
   permission, app version, and product state allow it.
2. Preserve local cart draft state where safe and tenant-scoped.
3. Request an API quote or validation before checkout.
4. Show item-level warnings for unavailable products, changed price, changed
   inventory, changed option, minimum/maximum quantity, plan limit, permission
   block, subscription requirement, or tenant rule.
5. Distinguish local cart draft from server-validated cart, quoted cart,
   checkout-ready cart, payment-pending cart, and rejected cart.
6. Prevent duplicate checkout attempts, double taps, retry loops, and stale
   cart submission confusion.
7. Clear or lock cart data on logout, tenant switch, revocation, tenant
   suspension, app lock policy, or privacy policy as required.

Cart principles:

- Mobile can help the user assemble intent, but Admin/API owns cart
  acceptance.
- Prices, taxes, shipping, discounts, promotions, inventory, eligibility, and
  plan effects must be revalidated through API before checkout.
- Cart data should be separated by tenant and user context.
- Mobile should never let a cart from one tenant bleed into another tenant.
- Local cart drafts should survive recoverable connection changes but not
  bypass server denial.
- Sensitive cart contents should not appear in diagnostics, support, logs, or
  notifications unless explicitly allowed.

## Checkout Principles

Checkout converts a cart draft into a server-reviewed commercial action. It
is high-risk because it can affect money, inventory, fulfillment, invoices,
receipts, subscriptions, customer commitments, and support.

Checkout should:

1. Reconfirm tenant, user, role, permission, plan, feature flag, app version,
   maintenance, product visibility, price, inventory, tax, shipping, discount,
   and terms through API.
2. Present a clear server quote before the user commits.
3. Explain whether checkout creates an order, request, quote, invoice, payment
   handoff, subscription upgrade, or pending approval.
4. Require explicit user confirmation before starting a payment or placing an
   order.
5. Use mobile-friendly errors for changed price, unavailable item, changed
   shipping, expired quote, invalid promotion, plan limit, billing block,
   payment unavailable, maintenance, permission denial, or offline state.
6. Rate-limit and protect sensitive checkout attempts.
7. Preserve recoverable user input without storing payment data or provider
   secrets.

Checkout principles:

- Admin/API owns checkout eligibility and quote acceptance.
- Mobile should never calculate final totals as authority.
- Mobile should never place an order while offline.
- Checkout should be idempotent at the API boundary to protect against
  duplicate attempts.
- The user should understand whether they are paying now, requesting an
  invoice, upgrading a subscription, submitting a quote request, or creating a
  pending order.
- Checkout outcome should not depend only on browser/native redirect state.
  API must confirm the official state after any hosted payment or external
  handoff.

## Hosted Payment Principle

Hosted payment keeps sensitive payment handling outside the mobile client and
outside normal product logic. This document does not choose, design, or
implement a payment provider.

Hosted payment principles:

- Admin/API owns payment-provider selection, payment session creation,
  provider secrets, payment event reconciliation, payment audit, and provider
  error handling outside this document.
- Mobile should receive only safe hosted-payment outcomes, such as continue to
  hosted payment, payment pending, payment confirmed, payment failed, payment
  cancelled, payment expired, payment unavailable, or contact support.
- Mobile must never collect, store, process, log, cache, or transmit raw card
  data, bank data, payment secrets, provider secret keys, webhook payloads,
  or full provider error internals.
- A hosted payment redirect, native browser return, deep link, or user-facing
  success page is not payment truth. Admin/API confirmation is required.
- Payment failures should be user-friendly without exposing provider internals
  or sensitive fraud/risk details.
- Payment handoff should respect feature flags, plan limits, tenant state,
  app-version policy, maintenance mode, role permission, and support policy.
- If a hosted payment flow cannot safely resume, mobile should show a pending
  or support-needed state instead of guessing.
- Refunds, disputes, chargebacks, saved payment methods, wallet payments,
  deposits, split payments, taxes, and provider-specific compliance require
  separate documentation before implementation.

## Order Lifecycle

The lifecycle should describe business meaning, not implementation status
values. A tenant may later customize labels through remote config, but the
core meaning should remain consistent.

| Stage | Business meaning | Admin/API authority | Mobile behavior |
| --- | --- | --- | --- |
| Cart draft | User is assembling commercial intent. | Owns whether draft carts are allowed, which items are valid, and when a draft must be cleared. | Saves local cart intent where safe and labels it as not checked out. |
| Server quote | API reviewed cart for price, inventory, eligibility, and terms. | Owns quote freshness, totals, taxes, fees, shipping, discounts, and checkout readiness. | Shows quote, warnings, expiration, and next action. |
| Checkout started | User committed to begin checkout or hosted payment. | Owns checkout session, idempotency, hosted payment handoff, and audit. | Shows processing or hosted-payment state without treating it as paid. |
| Payment pending | Payment or payment-like confirmation is in progress or awaiting reconciliation. | Owns official payment state and risk review. | Shows pending state and avoids duplicate orders. |
| Order requested | Order was submitted but requires approval, payment, inventory allocation, or review. | Owns validation, acceptance, rejection, support routing, and audit. | Shows pending/requested state with safe recovery. |
| Order confirmed | Order is accepted as official. | Owns order truth, inventory effect, fulfillment start, receipt/invoice behavior, notifications, and audit. | Shows confirmed order summary and allowed next actions. |
| Processing | Tenant is preparing the order. | Owns processing rules, staff/admin visibility, cancellation eligibility, and support state. | Shows progress only where role and tenant policy allow. |
| Fulfilled | Product/service was provided, shipped, delivered, picked up, activated, or completed. | Owns fulfillment truth, reports, receipt/invoice state, and support visibility. | Shows fulfilled/completed summary where permitted. |
| Cancelled | Order is no longer active before or during fulfillment according to policy. | Owns cancellation eligibility, inventory release, payment/refund implications, notifications, and audit. | Shows cancelled summary or recovery path. |
| Payment failed | Payment did not complete or could not be confirmed. | Owns failure classification, retry eligibility, fraud/risk privacy, and support routing. | Shows safe retry/support options without exposing provider internals. |
| Refunded or credited | Money or account credit was returned or adjusted. | Owns refund/credit decision, provider reconciliation, invoice/receipt updates, reports, and audit. | Shows role-safe refund/credit summary only after API confirmation. |
| Returned | Customer or tenant initiated return workflow. | Owns return eligibility, inspection, restock, refund/credit, support, and audit. | Shows return state and instructions where permitted. |
| Archived | Order is historical and available only through role/report/support rules. | Owns retention, export, legal/support access, and report visibility. | Shows role-safe historical summaries or nothing. |

Lifecycle transitions should be audited when they affect money, inventory,
tax, fulfillment, subscription entitlement, invoices, receipts, reports,
support, compliance, or tenant/customer commitments.

## Invoice And Receipt Principles

Invoices and receipts explain commercial obligations and outcomes. They should
be official only when Admin/API accepts them.

Invoice principles:

- An invoice represents a requested or owed amount, terms, due state, tenant
  identity, customer identity, line summary, and payment instructions where
  policy allows.
- Admin/API owns invoice issuance, numbering, due state, voiding, adjustment,
  export, retention, support visibility, and audit.
- Mobile may show invoice summaries, due state, download/open actions, and
  hosted-payment handoff when API allows.
- Mobile should not create official invoices offline.
- Invoice details should avoid exposing tax, address, customer, or line-item
  data to roles that should not see them.

Receipt principles:

- A receipt represents accepted payment or accepted commercial settlement.
- Admin/API owns receipt issuance, payment confirmation, receipt numbering,
  provider reconciliation, export, retention, support visibility, and audit.
- Mobile may show receipt summaries, download/open actions, and support links
  only after API confirmation.
- Mobile should not treat redirect success, local checkout screen, cached
  state, or push notification as receipt truth.
- Receipts should never include sensitive payment secrets or full provider
  details.

Shared principles:

- Invoice and receipt documents should be tenant-scoped, permission-aware,
  privacy-safe, auditable, and retention-aware.
- If downloadable documents exist later, access should re-check API authority
  and avoid long-lived public links unless separately documented.
- Support should see only invoice/receipt context needed to resolve a case.
- Exports should be scoped, logged, and role-controlled.

## Subscription Upsell Principles

Subscription upsell connects commerce behavior to SaaS plan access. Upsells
should be clear, respectful, permission-aware, and Admin/API-controlled.

Upsell may appear when:

- A tenant reaches a plan limit.
- A product or commerce feature requires a higher plan.
- A subscription add-on is needed.
- A trial is ending.
- A tenant is billing-blocked, expired, or suspended.
- A user attempts to access a premium module, report, support tier, storage
  amount, notification capacity, sync capacity, or NativePHP capability.

Upsell principles:

- Admin/API owns plan entitlement, subscription state, upgrade eligibility,
  downgrade rules, trial conversion, add-ons, manual overrides, and billing
  support.
- Mobile should show upsell states only when the current user may see them.
- Mobile users who cannot manage billing should receive contact-admin or
  limited-access messaging, not payment prompts.
- Tenant admins and billing managers may see upgrade/contact/support guidance
  where policy allows.
- Upsell should not use dark patterns, hide current limits, or imply that
  payment will bypass permission, tenant lifecycle, security, or feature flags.
- Hosted payment or upgrade handoff requires API confirmation and separate
  provider-neutral billing documentation before implementation.
- Subscription upsell should preserve tenant isolation and never reveal other
  tenants' plan/pricing context.

## Admin Product And Control Principles

Admin product controls should be scoped, auditable, reversible where possible,
and understandable before saving.

Admins may control:

- Commerce module enablement by platform, plan, tenant, role, cohort, app
  version, feature flag, and maintenance state.
- Product catalog visibility, status, merchandising, categories, collections,
  variants, labels, and unavailable states.
- Pricing posture, discount posture, promotion posture, tax/shipping display
  posture, and quote freshness rules without exposing provider internals.
- Inventory posture, stock display posture, reservation posture, backorder
  posture, fulfillment posture, and unavailable states.
- Cart behavior, checkout eligibility, quote expiration, duplicate-submission
  protection, and hosted payment handoff availability.
- Order lifecycle visibility, cancellation policy, return policy, refund/credit
  posture, invoice/receipt visibility, support routing, and reporting.
- Subscription upsell messaging, plan-limit messaging, contact-admin behavior,
  and billing manager visibility.
- Offline catalog cache limits, cart draft limits, stale-price warnings, and
  blocked offline actions.

Dangerous controls need confirmation and impact preview, especially:

- Publishing, unpublishing, retiring, or hiding products.
- Changing price, discount, tax, shipping, inventory, or checkout policy.
- Enabling hosted payment handoff.
- Disabling checkout or commerce for a tenant.
- Cancelling, refunding, crediting, or force-changing orders.
- Changing invoice/receipt visibility or retention.
- Changing subscription upsell paths or plan limits.
- Exporting customer, order, invoice, receipt, or payment-adjacent reports.
- Granting support/admin visibility into sensitive order or billing context.

## Mobile Offline Limitations

Commerce is highly online-dependent because price, inventory, discount,
shipping, tax, payment, order, invoice, receipt, and subscription state must
be current.

Mobile may cache:

- Tenant-scoped catalog summaries.
- Product detail summaries where policy allows.
- Product media references where policy allows.
- Local cart drafts.
- Recently viewed products.
- Safe order summaries.
- Safe invoice/receipt summaries.
- Plan/upsell labels and blocked-state messages within freshness rules.
- Feature flags, remote config, permissions, plan outcomes, and app-version
  outcomes within documented freshness rules.
- Sync status and conflict explanations.

Mobile should not cache:

- Cross-tenant catalog, cart, order, invoice, receipt, customer, support,
  report, or diagnostics data.
- Final price, tax, shipping, discount, inventory, payment, refund, invoice,
  receipt, subscription, or order truth.
- Raw payment data, provider secrets, provider tokens, provider payloads, or
  full provider failure details.
- Sensitive customer data, private pricing, internal cost, supplier data, or
  restricted order notes longer than policy allows.

Offline mobile may:

- Browse cached catalog summaries with stale labels.
- Search/filter cached catalog content where policy allows.
- Save or edit a local cart draft.
- View cached order, invoice, or receipt summaries where privacy allows.
- Draft a support request about an order.
- Show subscription upsell/help text already confirmed by API, with stale
  labels where needed.

Offline mobile must not:

- Confirm price, inventory, tax, shipping, discount, payment, or subscription
  entitlement.
- Start or complete checkout.
- Start hosted payment.
- Place an order.
- Reserve inventory.
- Issue, void, or pay an invoice.
- Generate or confirm a receipt.
- Cancel, refund, credit, or return an order as official.
- Bypass tenant rules, plan limits, feature flags, permissions, app-version
  rules, maintenance, or server revocation.

Offline principles:

- Commerce actions that involve money, inventory, fulfillment, subscription
  access, invoice state, or receipt state must wait for online API access.
- Local carts should be protected from data loss but clearly labeled as drafts.
- If a cached product, price, promotion, shipping option, tax rule, inventory
  state, or plan entitlement changes before checkout, mobile should show a
  conflict or quote update instead of silently proceeding.
- If tenant, plan, feature flag, permission, app version, product, or order
  state changes while offline, reconnect should fail closed where commercial
  risk exists.

## Conflict Scenarios

Commerce conflicts are expected when users browse cached data, prices change,
stock changes, checkout expires, or payment state needs reconciliation.

Potential conflicts include:

- Product was visible on mobile but hidden before checkout.
- Price changed after the cart draft was created.
- Promotion expired or no longer applies.
- Inventory became unavailable or quantity changed.
- Shipping/delivery/pickup option changed.
- Tax or fee posture changed.
- Plan limit or subscription state changed.
- Checkout quote expired.
- Hosted payment was cancelled, failed, expired, or pending reconciliation.
- Order was cancelled, refunded, fulfilled, returned, or adjusted by admin.
- Invoice was voided, paid, expired, or adjusted.
- Tenant entered suspended, archived, billing-blocked, deletion-requested, or
  maintenance state.

Conflict principles:

- Mobile should preserve user intent where safe and explain what changed.
- API/Admin authority decides whether a conflict can auto-resolve, needs user
  choice, or requires admin/support review.
- Price, inventory, payment, invoice, receipt, refund, and subscription
  conflicts should never be silently accepted from cached mobile data.
- Conflict messages should be useful without leaking restricted stock, cost,
  fraud/risk, customer, provider, or cross-tenant information.
- Conflict decisions should be audited when they affect money, inventory,
  fulfillment, subscriptions, reports, support, or compliance.

## Mobile UX Principles

Commerce UX should feel simple, trustworthy, and honest about freshness.

Mobile should:

- Show catalog, product, cart, checkout, payment handoff, order, invoice,
  receipt, and upsell states in plain language.
- Keep browse/search/filter/cart actions fast and thumb-friendly.
- Show stale catalog, stale price, stale cart, offline, quote expired,
  payment pending, order pending, and support-needed states clearly.
- Avoid admin language and provider-specific payment language.
- Confirm destructive or money-adjacent actions before submission.
- Avoid duplicate checkout attempts with stable pending indicators.
- Provide support/contact-admin paths where checkout, payment, subscription,
  order, invoice, or receipt state is blocked.
- Hide or explain commerce entry points blocked by tenant, plan, permission,
  feature flag, app version, maintenance, or offline state.

Mobile should not:

- Imply that cached prices or inventory are guaranteed.
- Ask for payment details inside the mobile app.
- Show provider internals, fraud/risk details, or raw payment errors.
- Let notifications or deep links bypass current API checks.
- Let upsell prompts pressure users who cannot make billing decisions.

## Privacy And Security Principles

Commerce data may reveal customer identity, purchase intent, order history,
business pricing, inventory, fulfillment, invoice data, receipts, subscription
state, and support context. It should be treated as sensitive tenant data.

Privacy and security principles:

- Tenant isolation applies to catalog, cart, orders, invoices, receipts,
  customers, support, diagnostics, reports, audit, and billing context.
- Least privilege applies to platform admins, tenant admins, managers, support
  agents, billing users, and mobile users.
- Payment data should stay in hosted payment surfaces and Admin/API provider
  reconciliation, never in mobile-local storage.
- Mobile diagnostics should not expose cart contents, private pricing, order
  lines, invoice details, receipt details, provider payloads, payment secrets,
  customer private data, or subscription internals.
- Notifications should avoid sensitive order, payment, invoice, receipt, or
  subscription details on lock screen or shared-device surfaces.
- Support access should be case-scoped and auditable.
- Reports and exports should prefer aggregate trends unless role, policy, and
  audit allow detailed commerce data.
- Suspended users, suspended tenants, billing-blocked tenants, revoked devices,
  maintenance mode, and forced updates should fail closed.

## Reporting Principles

Commerce reporting should help tenants understand business health without
turning reports into unrestricted customer, payment, or order browsing.

Reports may summarize:

- Product visibility and usage.
- Catalog browsing and search trends.
- Cart creation and abandonment.
- Checkout start, success, cancellation, failure, and support-needed states.
- Order requested, confirmed, processing, fulfilled, cancelled, returned,
  refunded, credited, archived, and disputed states where documented.
- Invoice and receipt state.
- Subscription upsell impressions and conversions where permitted.
- Plan-limit commerce blocks.
- Offline cart drafts, stale quote conflicts, and sync/checkout failures.
- Support volume tied to commerce flows.

Reporting should:

- Keep tenant reports inside tenant boundaries.
- Show mobile users only personal or role-safe commerce summaries.
- Show tenant admins operational outcomes for their tenant.
- Show billing/operations users commercial outcomes without unnecessary
  private item, customer, payment, or provider detail.
- Show platform admins cross-tenant health only when role and policy allow it.
- Keep exports scoped, auditable, privacy-aware, and retention-aligned.

## Support Principles

Support workflows should help resolve commerce issues without granting broad
access to sensitive customer or payment-adjacent data.

Support may need to understand:

- Which tenant, product, cart, order, invoice, receipt, user, device class,
  app version, feature flag, plan limit, hosted-payment state, notification,
  or sync state was involved.
- Whether a cart was local draft, quoted, checkout-ready, payment-pending,
  confirmed, rejected, conflicted, expired, or support-needed.
- Whether price, inventory, promotion, shipping, tax, plan, permission,
  feature flag, app version, tenant state, or offline state blocked the flow.

Support should not receive unrestricted access to payment details, provider
payloads, fraud/risk details, customer private data, private pricing, invoice
details, receipt details, order notes, or diagnostics unless the support role,
tenant policy, privacy policy, and audit rules allow it.

## Rollout And Rollback Principles

Commerce should be introduced gradually because it touches catalog, price,
inventory, checkout, hosted payment handoff, orders, invoices, receipts,
subscription upsells, reports, support, privacy, and tenant operations.

Rollout principles:

- Start with documentation, pilot tenants, feature flags, limited products,
  read-only catalog, and quote-only or request-only flows before hosted
  payment.
- Resolve commerce availability through platform catalog, plan entitlement,
  tenant enablement, permissions, feature flags, remote config, app version,
  device support, and offline policy.
- Add carts, checkout, hosted payment handoff, order tracking, invoices,
  receipts, subscription upsells, reports, and support visibility in controlled
  stages.
- Review privacy, support, reporting, audit, and tenant-rule impact before
  expanding.

Rollback principles:

- Emergency disable should hide commerce entry points, stop new checkout,
  preserve local cart drafts according to policy, and explain what will happen
  when online.
- Admins should understand pending carts, payment-pending sessions, orders,
  invoices, receipts, upsell prompts, reports, and support cases before
  disabling a tenant or commerce workflow.
- Rollback should not silently delete local cart drafts or confirmed order
  summaries.
- Rollback should not expose cross-tenant data or bypass API authority.

## Risks

Key risks:

- Cached price or inventory is mistaken for purchase truth.
- Mobile accidentally handles payment data or provider secrets.
- Hosted payment redirect is treated as final payment truth without API
  reconciliation.
- Duplicate checkout attempts create duplicate orders.
- Admin product, price, inventory, or checkout changes affect users without
  impact preview.
- Subscription upsells pressure users who cannot make billing decisions.
- Reports expose customer, order, invoice, receipt, or payment-adjacent data
  too broadly.
- Support receives more sensitive commerce data than needed.
- Offline cart drafts are confused with submitted orders.
- Plan, feature flag, or tenant lifecycle changes strand users with unclear
  blocked commerce states.

Risk controls:

- Keep Admin/API authoritative for catalog, price, inventory, cart validation,
  checkout, hosted payment handoff, order state, invoice/receipt state,
  subscription entitlements, reports, audit, billing, support, and conflict
  decisions.
- Keep mobile clear about local draft, quoted, checkout-ready, hosted-payment,
  pending, confirmed, failed, cancelled, refunded, returned, expired, and
  conflict states.
- Use tenant isolation, least privilege, feature flags, remote config,
  app-version gates, hosted payment boundaries, privacy rules, and audit
  history.
- Require impact preview and confirmation for dangerous admin commerce
  changes.
- Document every commerce workflow before implementation.

## Readiness Checklist

Before implementing commerce behavior, the product documentation should answer:

- Which tenants and plans can use the commerce module?
- Which products, categories, collections, services, variants, or add-ons are
  visible, purchasable, request-only, subscription-required, hidden, suspended,
  or retired?
- Which roles can browse, add to cart, checkout, approve, cancel, refund,
  return, export, report, support, or audit commerce behavior?
- Which pricing, discount, tax, shipping, inventory, and quote freshness rules
  apply?
- Which hosted payment principles apply and which provider details remain out
  of mobile documentation?
- Which order lifecycle states are user-facing, admin-facing, reportable,
  auditable, support-visible, or hidden?
- Which invoice and receipt rules apply?
- Which subscription upsell states are allowed and who may see them?
- Which cart actions can be drafted offline and which require online API
  access?
- How should mobile show stale catalog, stale price, cart warnings, checkout
  blockers, hosted-payment state, order state, invoice/receipt state,
  subscription upsell state, and offline limitations?
- How should admins preview mobile impact before product, price, inventory,
  checkout, invoice/receipt, or upsell changes?
- What data appears in reports, exports, diagnostics, support views,
  notifications, invoices, receipts, and audit history?
- What happens when a tenant, plan, feature flag, permission, app version,
  product, price, inventory, checkout, order, invoice, receipt, or hosted
  payment state changes while the device is offline?

## Acceptance Principle

The commerce module is ready for implementation only when the team can trace
every mobile commerce action to:

- A documented tenant and plan rule.
- A documented permission rule.
- A documented feature flag and remote config rule.
- A documented API authority.
- A documented price, inventory, and checkout outcome.
- A documented hosted payment boundary.
- A documented order lifecycle outcome.
- A documented invoice and receipt rule.
- A documented subscription upsell rule.
- A documented offline limitation.
- A documented privacy and retention boundary.
- A documented audit and reporting meaning.
- A documented support visibility rule.

Messaging And Community Logic is defined in `messaging-community-logic.md`:
conversation behavior, support chat behavior, message attachments,
moderation, reports/abuse flow, notification behavior, offline message
drafts, admin visibility boundaries, and privacy principles must remain
tenant-enabled, plan-controlled, permission-aware, moderation-ready,
abuse-report-safe, notification-safe, offline-draft-safe, privacy-safe,
auditable, and Admin/API-authoritative.

AI Feature Logic is defined in `ai-feature-logic.md`:
AI assistant purpose, summarization, categorization, smart suggestions,
moderation assistance, report generation assistance, admin AI controls,
tenant opt-in, privacy, and human-review principles must remain
tenant-enabled, plan-controlled, permission-aware, opt-in-only,
provider-neutral, human-reviewed, privacy-safe, audit-ready,
rate-limited, cost-aware, and Admin/API-authoritative.

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

Feature Dependency Map is defined in `feature-dependency-map.md`:
major features must document dependencies on authentication, tenant context,
permissions, feature flags, remote config, API availability, offline cache,
NativePHP permissions, subscription plan, and admin settings before
implementation planning or release decisions.

If any of those are unclear, the correct next step is more documentation, not
application code.
