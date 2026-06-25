# NativePHP Runbook

Updated: 2026-06-25

This Laravel app is initialized for NativePHP Mobile. NativePHP v3 uses the unified `NATIVEPHP_APP_ID` value for both the iOS bundle identifier and Android application ID, so the iOS and Android placeholder keys below should stay in sync unless NativePHP adds separate platform identifiers later.

## Current Placeholders

| Setting | Value |
| --- | --- |
| App name | `Mobile Lara` |
| NativePHP app ID | `com.example.mobilelara` |
| iOS bundle identifier placeholder | `com.example.mobilelara` |
| Android package placeholder | `com.example.mobilelara` |
| App icon source | `public/icon.png` |
| Deep link scheme | `mobilelara` |
| Deep link host | `mobile-lara.test` |
| Start URL | `/` |

Replace `com.example.mobilelara` with a real reverse-domain identifier before signing or publishing the app.

## Icon Placeholder

NativePHP expects one source icon at:

```text
public/icon.png
```

The placeholder in this repo is a generated 1024x1024 PNG with no transparency. Replace it with a production icon before release. NativePHP will resize it for iOS and Android during installation/build steps.

## Initialize NativePHP

Run this after changing NativePHP config, plugins, app IDs, or icon assets:

```bash
php artisan native:install both --no-interaction
```

For platform-specific regeneration:

```bash
php artisan native:install ios --no-interaction
php artisan native:install android --no-interaction
```

The generated `nativephp/` directory is an ephemeral build artifact. NativePHP may delete and rebuild it during install/upgrade commands.

## Verify Local Tooling

```bash
php artisan native:debug --no-interaction
php artisan native:plugin:validate
```

Current local status at the time of this runbook:

- Java: present.
- CocoaPods: present.
- Xcode: not detected.
- Android Studio: not detected.
- Gradle: not detected.

iOS simulator runs require Xcode and installed simulators. Android emulator runs require Android Studio/SDK, an emulator image, and Gradle available to NativePHP.

## Run On iOS Simulator

```bash
php artisan native:run ios
```

To target a specific simulator after NativePHP shows available devices:

```bash
php artisan native:run ios <simulator-udid>
```

Useful development flags:

```bash
php artisan native:run ios --watch
php artisan native:run ios --start-url=/dev/tailwind
```

## Run On Android Emulator

```bash
php artisan native:run android
```

To target a specific emulator/device:

```bash
php artisan native:run android <device-udid>
```

Useful development flags:

```bash
php artisan native:run android --watch
php artisan native:run android --start-url=/dev/tailwind
```

## Open Native Projects

```bash
php artisan native:open ios
php artisan native:open android
```

## Test Without Building

NativePHP Jump can serve the app for device testing without compiling a native build:

```bash
php artisan native:jump
```

Use this when you want a quick device smoke test before setting up full simulator/emulator build tooling.

## Pre-Run Checklist

```bash
npm run build
php artisan test --compact
php artisan native:plugin:validate
```

Run `php artisan native:install both --no-interaction` again after replacing `public/icon.png`, changing app identifiers, changing NativePHP permissions, or adding/removing NativePHP plugins.

## References

- NativePHP installation: https://nativephp.com/docs/mobile/3/getting-started/installation
- NativePHP command reference: https://nativephp.com/docs/mobile/3/getting-started/commands
- NativePHP app icons: https://nativephp.com/docs/mobile/3/the-basics/app-icon
