<?php

namespace App\Providers;

use App\Contracts\MobileAuth\MobileTokenStore;
use App\Contracts\MobileLocal\MobileNetworkState;
use App\Contracts\Native\LocalNotificationDriver;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileAuth\NativeSecureMobileTokenStore;
use App\Services\MobileAuth\SessionMobileTokenStore;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\NativeMobileNetworkState;
use App\Services\Native\LocalNotifications\NativePhpLocalNotificationDriver;
use App\Services\Native\LocalNotifications\PlaceholderLocalNotificationDriver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MobileTokenStore::class, function () {
            return match (config('mobile_auth.storage.driver', 'native_secure_storage')) {
                'native_secure_storage', 'nativephp', 'secure_storage' => $this->app->make(NativeSecureMobileTokenStore::class),
                'session' => $this->app->make(SessionMobileTokenStore::class),
                default => throw new InvalidArgumentException('Unsupported mobile auth token store driver.'),
            };
        });

        $this->app->bind(MobileNetworkState::class, NativeMobileNetworkState::class);

        $this->app->bind(LocalNotificationDriver::class, function () {
            $driver = (string) config('mobile_notifications.driver', 'auto');

            return match ($driver) {
                'auto' => $this->localNotificationAutoDriver(),
                'native', 'nativephp' => $this->app->make(NativePhpLocalNotificationDriver::class),
                'placeholder', 'local' => $this->app->make(PlaceholderLocalNotificationDriver::class),
                default => throw new InvalidArgumentException('Unsupported local notification driver.'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MobileLocalDatabase $mobileLocalDatabase): void
    {
        $mobileLocalDatabase->ensureFileExists();

        View::composer('components.mobile.bottom-navigation', function ($view): void {
            $view->with('items', $this->app->make(MobileAccessPolicy::class)->primaryNavigationItems());
        });
    }

    private function localNotificationAutoDriver(): LocalNotificationDriver
    {
        $nativeDriver = $this->app->make(NativePhpLocalNotificationDriver::class);

        if ($nativeDriver->pluginIsInstalled()) {
            return $nativeDriver;
        }

        return $this->app->make(PlaceholderLocalNotificationDriver::class);
    }
}
