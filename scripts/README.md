# Scripts

This directory is reserved for root-level monorepo helper scripts.

Product Vision is defined in `../docs/product-vision.md`: scripts may support
verification, but they must preserve the two-system product idea and must not
create hidden authority outside Admin/API.

Product Positioning is defined in `../docs/product-positioning.md`: scripts
should verify the combined SaaS control center and mobile platform posture,
not create side channels around it.

Core Product Principles are defined in `../docs/product-principles.md`: scripts
may verify admin authority, API-first behavior, tenant isolation, security,
offline state, and documentation coverage, but must not create application
logic.

Scripts must support the Admin Control Center planning boundary in
`../docs/admin-control-center-logic.md`: verification should prove documented
tenant, user, role, permission, feature, config, version, maintenance, force
update, sync, notification, report, billing, and support controls without
creating undocumented application logic.

Scripts must also respect Feature Flag Logic in
`../docs/feature-flag-logic.md`: any future verification helper should check
resolved mobile-safe feature states and avoid creating hidden feature authority
outside the documented Admin/API path.

Scripts must also respect Remote Configuration Logic in
`../docs/remote-configuration-logic.md`: any future helper should verify
documented config contracts, freshness, fallback, and invalid-config behavior
without creating runtime authority outside Admin/API.

Scripts must also respect Mobile Version Control Logic in
`../docs/mobile-version-control-logic.md`: any future helper should verify
documented minimum-version, optional-update, force-update, maintenance, store
link, update-message, and stale-client behavior without creating runtime
authority outside Admin/API.

Do not add custom verification scripts when a normal project command already
proves the behavior. Prefer the real commands:

```bash
composer install
npm install
npm run build
php artisan test --compact
vendor/bin/pint --dirty --format agent
php artisan route:list
php artisan native:plugin:validate
```

When the monorepo apps are split into `apps/api-admin` and
`apps/mobile-client`, scripts here may coordinate per-app checks without
duplicating the actual Laravel/Pest/Vite commands.
