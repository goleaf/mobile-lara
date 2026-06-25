# Mobile App Audit

Audit date: 2026-06-25

Scope: inspection only. This document records the current Laravel, frontend, NativePHP, and MCP status without changing application logic.

## Executive Summary

- The project is a minimal Laravel application with one public web route, SQLite-backed app services, Blade rendering, Vite, and Tailwind CSS.
- NativePHP Mobile is installed and configured for both platforms, with a generated native wrapper tracked through NativePHP files and the generated `nativephp/` directory ignored.
- NativePHP has 18 registered installed plugins covering browser, camera, device, dialog, file, microphone, network, share, system, permissions, fullscreen, loaders, splash screen, in-app update, in-app reviews, screenshot blocking, double-back-close, and locales.
- Livewire is not installed. The current UI is plain Blade.
- Tailwind CSS is installed through the Vite plugin.
- Premium/private NativePHP packages such as biometrics, geolocation, scanner, secure storage, background tasks, and local notifications are not installed because marketplace repository credentials are not configured in this checkout.
- Native build tooling is incomplete on this machine: Java and CocoaPods are present, but Xcode, Android Studio, and Gradle were not detected by `php artisan native:debug`.

## Project Structure Snapshot

- Laravel app code: `app/Models/User.php`, `app/Providers/AppServiceProvider.php`, and `app/Providers/NativeServiceProvider.php`.
- Routing: `routes/web.php` currently exposes only `GET /`.
- Blade UI: `resources/views/welcome.blade.php`.
- NativePHP config: `config/nativephp.php`, `nativephp.lock`, `native`, `database/nativephp.sqlite`, and the generated ignored `nativephp/` directory.
- Frontend tooling: `resources/css/app.scss`, `resources/js/app.js`, `vite.config.js`, `package.json`, and `package-lock.json`.
- Database: SQLite app database, default Laravel migrations for users/cache/jobs, factories, and seeders.
- Tests: default Pest feature/unit tests plus `tests/Feature/NativePhpConfigurationTest.php`.
- MCP/tooling: `.mcp.json`, `.codex/config.toml`, `.cursor/mcp.json`, `opencode.json`, `.codebase-memory.json`, `.cbmignore`, and `boost.json`.

## Installed Versions

| Package / Runtime | Current status | Source checked |
| --- | --- | --- |
| PHP | 8.5 / host PHP 8.5.7 in NativePHP debug | Laravel Boost, `native:debug` |
| Laravel Framework | 13.17.0 | Laravel Boost, `php artisan --version`, Composer |
| Livewire | Not installed | Composer installed package list |
| Tailwind CSS | 4.3.1 | Laravel Boost, `npm ls tailwindcss` |
| `@tailwindcss/postcss` | 4.3.1 | `npm ls @tailwindcss/postcss` |
| `sass-embedded` | 1.100.0 | `npm ls sass-embedded` |
| Vite | 8.1.0 | `npm ls vite` |
| NativePHP Mobile | 3.3.6 | Composer, `php artisan native:debug` |
| Embedded NativePHP PHP | 8.5.7 | `php artisan native:debug` |
| Laravel Boost | 2.4.10 | Laravel Boost application info |
| Laravel MCP | 0.8.1 | Laravel Boost application info |
| Pest | 4.7.3 | Laravel Boost application info |
| PHPUnit | 12.5.29 | Laravel Boost application info |

## NativePHP Status

NativePHP Mobile is installed through Composer and the project has already been initialized for mobile. NativePHP's generated native application directory is present as an ignored/generated artifact, which matches the NativePHP documentation guidance that the generated `nativephp/` folder is ephemeral and should not be committed as application source.

Current `config/nativephp.php` highlights:

- App ID: `dev.andrejprus.mobilelara` by default.
- Deep link scheme: `mobilelara` by default.
- Deep link host: `mobile-lara.test` by default.
- Start URL: `/`.
- Runtime mode: `persistent`.
- iOS permission strings are configured for camera, microphone, photo library, and adding to photo library.
- Android theme primary color is configured as `#04ABA6`.

`App\Providers\NativeServiceProvider::plugins()` registers all currently installed NativePHP plugins.

## Installed NativePHP Plugins

| Plugin | Version | Current role |
| --- | --- | --- |
| `nativephp/mobile` | 3.3.6 | NativePHP Mobile runtime |
| `nativephp/mobile-browser` | 1.0.1 | In-app browser bridge |
| `nativephp/mobile-camera` | 1.0.3 | Camera bridge |
| `nativephp/mobile-device` | 1.0.2 | Device info, battery, flashlight, and basic vibration bridge |
| `nativephp/mobile-dialog` | 1.0.1 | Native dialog bridge |
| `nativephp/mobile-file` | 1.0.1 | File picker / file bridge |
| `nativephp/mobile-microphone` | 1.0.1 | Microphone recording bridge |
| `nativephp/mobile-network` | 1.0.1 | Network status bridge |
| `nativephp/mobile-share` | 1.0.1 | Native share bridge |
| `nativephp/mobile-system` | 1.0.2 | System bridge |
| `bhargavdetroja/nativephp-all-permission-handle` | 1.0.2 | Permission request/status bridge |
| `codingwithrk/double-back-to-close` | 1.0.0 | Android-style double-back close behavior |
| `codingwithrk/no-screenshot` | 1.0.0 | Screenshot prevention/detection bridge |
| `developernauts/nativephp-mobile-locales` | 1.0.1 | Locale integration |
| `kevinbatdorf/nativephp-fullscreen` | 0.1.0 | Fullscreen bridge |
| `mobikul/mobikul_loader` | 1.0.2 | Native loader bridge |
| `s2br/nativephp-mobile-splashscreen` | 1.3.0 | Splash screen integration |
| `wilsonatb/in-app-update` | 1.0.1 | Android in-app update bridge |
| `wilsonatb/nativephp-in-app-reviews` | 1.0.4 | Native in-app review bridge |

`php artisan native:plugin:validate` passed for the installed plugins, with non-fatal warnings for `developernauts/nativephp-mobile-locales` and `s2br/nativephp-mobile-splashscreen` because they do not expose bridge functions or native code directories. That looks consistent with hook/config-style plugins, but should be checked again when their features are actively used.

## Missing Packages And Access Blocks

The packages below are not installed in this checkout.

### First-Party NativePHP Premium / Private Packages

| Package | Status | Next action |
| --- | --- | --- |
| `nativephp/mobile-biometrics` | Not installed | Requires NativePHP marketplace/private Composer access. |
| `nativephp/mobile-geolocation` | Not installed | Requires NativePHP marketplace/private Composer access. |
| `nativephp/mobile-scanner` | Not installed | Requires NativePHP marketplace/private Composer access. |
| `nativephp/mobile-secure-storage` | Not installed | Requires NativePHP marketplace/private Composer access. |
| `nativephp/mobile-background-tasks` | Not installed | Requires NativePHP marketplace/private Composer access. |
| `nativephp/mobile-local-notifications` | Not installed | Requires NativePHP marketplace/private Composer access. |

Optional first-party marketplace packages to evaluate later:

- `nativephp/mobile-firebase` for push notifications and Firebase-backed capabilities.

### Community Marketplace Packages

| Capability | Package seen in marketplace | Current status |
| --- | --- | --- |
| Pro vibration / haptics | `jvdluk/pro-vibration` | Not installed. Basic `Device.Vibrate` is already available through `nativephp/mobile-device`. |
| Contacts | `srwiez/nativephp-mobile-contacts` | Not installed. |
| NFC | `weswecan/nfc` | Not installed. |
| Calendar | `srwiez/nativephp-mobile-calendar` | Not installed. |
| Screenshot capture | `srwiez/nativephp-mobile-screenshots` | Not installed. Screenshot prevention/control is already installed through `codingwithrk/no-screenshot`. |

### App Stack Packages

| Package | Status | Recommendation |
| --- | --- | --- |
| `livewire/livewire` | Not installed | Decide whether the first mobile UX needs Livewire 4 interactivity or should remain Blade-only for the first slice. |

## Current Risks And Gaps

- The application does not yet have a real mobile product workflow. It only exposes the default root route and Blade welcome view.
- Native permissions are configured broadly, but no app screens currently explain or exercise the related capabilities.
- Premium NativePHP repository credentials are not configured. Do not commit marketplace tokens, license keys, API keys, or Composer auth secrets.
- Xcode, Android Studio, and Gradle were not detected. iOS and Android builds should be treated as blocked until local tooling is installed.
- Native plugin registration exists, but feature-level verification still needs simulator/device runs after each capability is wired into the UI.
- Livewire is absent, so there are no Livewire components, tests, or hydration concerns yet.
- Any future query/data work must continue to follow the project rule of Eloquent-only queries, no raw SQL, no queries in Blade, and eager-loaded data flow from controller/action to view.

## Next Implementation Steps

1. Define the first real mobile app slice: for example authentication, dashboard, camera capture, scanner flow, secure settings, offline location flow, or notifications.
2. Decide whether to install Livewire 4 for the mobile UI or keep the initial version Blade-only.
3. Install missing native build tools: Xcode, Android Studio/SDK, and Gradle. Then rerun `php artisan native:debug`.
4. Configure NativePHP marketplace credentials outside git only if premium packages are required:

   ```bash
   composer config repositories.nativephp-plugins composer https://plugins.nativephp.com
   composer config --auth http-basic.plugins.nativephp.com <email> <license-key>
   ```

5. Install only the premium/community plugin packages that are needed by the chosen first slice.
6. Rebuild native artifacts after any plugin or NativePHP config change:

   ```bash
   php artisan native:install both --no-interaction
   php artisan native:plugin:list
   php artisan native:plugin:validate
   ```

7. Build the first mobile route/view using named routes, Blade SSR, controller/action-driven data loading, and tests.
8. Tighten permission copy in `config/nativephp.php` once the exact user-facing purpose of each permission is known.
9. Add focused tests for NativePHP configuration, plugin registration, and the first mobile workflow.
10. Run verification before release work:

    ```bash
    php artisan test --compact
    npm run build
    php artisan native:debug
    ```

11. Prepare release assets later: icons, splash assets, Android signing, iOS team/app identifiers, store metadata, and environment-specific config.

## Verification Commands Used

```bash
php artisan --version
php artisan about --only=environment,cache,database,drivers,queues,mail
php artisan route:list --except-vendor
php artisan native:debug --no-interaction
php artisan native:plugin:list
php artisan native:plugin:validate
composer show --format=json
composer show livewire/livewire --all
npm ls tailwindcss @tailwindcss/postcss sass-embedded vite --depth=0
git status --short --branch
```

## Sources

- NativePHP Mobile installation documentation: https://nativephp.com/docs/mobile/3/getting-started/installation
- NativePHP plugin usage documentation: https://nativephp.com/docs/mobile/3/plugins/using-plugins
- NativePHP marketplace catalogue: https://nativephp.com/plugins/marketplace
- NativePHP first-party marketplace catalogue: https://nativephp.com/plugins/marketplace?author=974
- Laravel package discovery documentation: https://github.com/laravel/docs/blob/13.x/packages.md#package-discovery
- Laravel configuration and environment documentation: https://github.com/laravel/docs/blob/13.x/configuration.md
