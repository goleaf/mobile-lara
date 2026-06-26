<?php

namespace App\Providers;

use App\Models\MobileAppVersionPolicy;
use App\Models\MobileFeatureFlag;
use App\Models\MobileRemoteConfig;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\TenantRemoteConfigOverride;
use App\Models\UserFeatureOverride;
use App\Policies\MobileAppVersionPolicyPolicy;
use App\Policies\MobileFeatureFlagPolicy;
use App\Policies\MobileRemoteConfigPolicy;
use App\Policies\TenantFeatureOverridePolicy;
use App\Policies\TenantPolicy;
use App\Policies\TenantRemoteConfigOverridePolicy;
use App\Policies\UserFeatureOverridePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(MobileFeatureFlag::class, MobileFeatureFlagPolicy::class);
        Gate::policy(TenantFeatureOverride::class, TenantFeatureOverridePolicy::class);
        Gate::policy(UserFeatureOverride::class, UserFeatureOverridePolicy::class);
        Gate::policy(MobileRemoteConfig::class, MobileRemoteConfigPolicy::class);
        Gate::policy(TenantRemoteConfigOverride::class, TenantRemoteConfigOverridePolicy::class);
        Gate::policy(MobileAppVersionPolicy::class, MobileAppVersionPolicyPolicy::class);
        Gate::policy(Tenant::class, TenantPolicy::class);
    }
}
