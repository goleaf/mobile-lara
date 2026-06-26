<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Livewire\Admin\AppVersionPolicies;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\FeatureFlags;
use App\Livewire\Admin\MobileDiagnosticReports;
use App\Livewire\Admin\MobileSyncEvents;
use App\Livewire\Admin\RemoteConfigs;
use App\Livewire\Admin\TenantFeatureOverrides;
use App\Livewire\Admin\TenantRemoteConfigOverrides;
use App\Livewire\Admin\Tenants;
use App\Livewire\Admin\UserFeatureOverrides;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->group(function (): void {
        Route::redirect('/', '/admin/dashboard')->name('home');
        Route::redirect('/login', '/admin/login')->name('login');

        Route::prefix('admin')
            ->name('admin.')
            ->group(function (): void {
                Route::middleware('guest')->group(function (): void {
                    Route::get('/login', [LoginController::class, 'show'])->name('login');
                    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
                });

                Route::middleware(['auth', 'admin.platform'])->group(function (): void {
                    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
                    Route::livewire('/tenants', Tenants::class)->name('tenants');
                    Route::livewire('/mobile/features', FeatureFlags::class)->name('mobile.features');
                    Route::livewire('/mobile/feature-overrides', TenantFeatureOverrides::class)->name('mobile.feature-overrides');
                    Route::livewire('/mobile/user-feature-overrides', UserFeatureOverrides::class)->name('mobile.user-feature-overrides');
                    Route::livewire('/mobile/config', RemoteConfigs::class)->name('mobile.config');
                    Route::livewire('/mobile/tenant-config', TenantRemoteConfigOverrides::class)->name('mobile.tenant-config');
                    Route::livewire('/mobile/app-versions', AppVersionPolicies::class)->name('mobile.app-versions');
                    Route::livewire('/mobile/diagnostics', MobileDiagnosticReports::class)->name('mobile.diagnostics');
                    Route::livewire('/mobile/sync', MobileSyncEvents::class)->name('mobile.sync');
                    Route::post('/logout', LogoutController::class)->name('logout');
                });
            });
    });
