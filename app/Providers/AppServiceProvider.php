<?php

namespace App\Providers;

use App\Contracts\MobileAuth\MobileTokenStore;
use App\Services\MobileAuth\NativeSecureMobileTokenStore;
use App\Services\MobileAuth\SessionMobileTokenStore;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
