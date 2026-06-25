# Mobile Stack

Updated: 2026-06-25

Scope: package installation and build configuration for the Laravel mobile stack. No Vue, React, Inertia, Ionic, or Capacitor packages were added.

## Installed Versions

| Package / tool | Version | Notes |
| --- | --- | --- |
| PHP | 8.5 / 8.5.7 host runtime | Laravel Boost reports PHP 8.5; NativePHP debug reports host PHP 8.5.7. |
| Laravel Framework | 13.17.0 | Existing application framework version. |
| NativePHP Mobile | 3.3.6 | Latest stable version resolved by Composer for the current Laravel/PHP constraints. |
| Livewire | 4.3.1 | Installed through Composer; Laravel package discovery is active. |
| Tailwind CSS | 4.3.1 | Installed through npm. |
| `@tailwindcss/vite` | 4.3.1 | Installed through npm and registered in `vite.config.js`. |
| Vite | 8.1.0 | Existing frontend build tool. |

## Configuration Status

- `nativephp/mobile` is installed and NativePHP's post-update installer ran through Composer.
- `config/nativephp.php` is present and the NativePHP service provider registers the installed mobile plugins.
- `livewire/livewire` is installed and discovered by Laravel.
- `config/livewire.php` has been published with Livewire 4 defaults.
- Tailwind CSS v4 is configured through `@tailwindcss/vite` in `vite.config.js`.
- `resources/css/app.css` uses Tailwind v4's CSS-first import:

  ```css
  @import 'tailwindcss';
  ```

- Tailwind source scanning includes Laravel pagination views, compiled Blade views, app Blade views, and app JavaScript files.

## Commands Used

```bash
composer require nativephp/mobile livewire/livewire --update-with-all-dependencies --no-interaction
php artisan livewire:config --no-interaction
npm install -D tailwindcss@latest @tailwindcss/vite@latest
php artisan optimize:clear --no-interaction
vendor/bin/pint --dirty --format agent
php artisan native:debug --no-interaction
php artisan native:plugin:validate
npm run build
php artisan test --compact
```

Supporting inspection commands:

```bash
composer show nativephp/mobile --all
composer show livewire/livewire --all
composer show --locked nativephp/mobile
composer show --locked livewire/livewire
npm view tailwindcss version
npm view @tailwindcss/vite version
npm ls tailwindcss @tailwindcss/vite vite --depth=0
npm ls vue react @inertiajs/vue3 @inertiajs/react @ionic/core @capacitor/core --depth=0
```

## Verification Results

- `vendor/bin/pint --dirty --format agent`: passed.
- `npm run build`: passed; Vite built the Tailwind assets successfully.
- `php artisan test --compact`: passed, 4 tests, 38 assertions.
- `php artisan native:debug --no-interaction`: passed and reports NativePHP Mobile 3.3.6.
- `php artisan native:plugin:validate`: completed for 18 plugins.
- Forbidden frontend/mobile packages check: `npm ls vue react @inertiajs/vue3 @inertiajs/react @ionic/core @capacitor/core --depth=0` returned empty.
- Composer check found no direct Inertia, Ionic, Capacitor, Vue, React.js, or React frontend packages.

## NativePHP Notes

Installed NativePHP plugin validation is healthy overall. Two existing plugins still report non-fatal validation warnings:

- `developernauts/nativephp-mobile-locales`: no bridge functions and no native code directories.
- `s2br/nativephp-mobile-splashscreen`: no bridge functions and no native code directories.

`php artisan native:debug` still reports missing local build tooling:

- Xcode: not found.
- Android Studio: not found.
- Gradle: not found.
- Java: present.
- CocoaPods: present.

Native device/simulator builds should wait until Xcode, Android Studio, and Gradle are installed.

## Source References

- NativePHP Mobile installation: https://nativephp.com/docs/mobile/3/getting-started/installation
- Livewire 4 installation: https://livewire.laravel.com/docs/4.x/installation
- Tailwind CSS with Laravel and Vite: https://tailwindcss.com/docs/installation/framework-guides/laravel/vite
