# Scripts

This directory is reserved for root-level monorepo helper scripts.

Scripts must support the Admin Control Center planning boundary in
`../docs/admin-control-center-logic.md`: verification should prove documented
tenant, user, role, permission, feature, config, version, maintenance, force
update, sync, notification, report, billing, and support controls without
creating undocumented application logic.

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
