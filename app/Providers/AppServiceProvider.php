<?php

namespace App\Providers;

use App\Contracts\MobileAuth\MobileTokenStore;
use App\Contracts\MobileLocal\MobileNetworkState;
use App\Services\MobileAuth\NativeSecureMobileTokenStore;
use App\Services\MobileAuth\SessionMobileTokenStore;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\NativeMobileNetworkState;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(MobileLocalDatabase $mobileLocalDatabase): void
    {
        $mobileLocalDatabase->ensureFileExists();
    }
}
