<?php

use App\Http\Controllers\Api\V1\Mobile\AppVersionController;
use App\Http\Controllers\Api\V1\Mobile\Auth\CurrentUserController;
use App\Http\Controllers\Api\V1\Mobile\Auth\LoginController;
use App\Http\Controllers\Api\V1\Mobile\Auth\LogoutAllDevicesController;
use App\Http\Controllers\Api\V1\Mobile\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Mobile\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Mobile\Auth\RefreshTokenController;
use App\Http\Controllers\Api\V1\Mobile\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Mobile\BootstrapController;
use App\Http\Controllers\Api\V1\Mobile\ConfigController;
use App\Http\Controllers\Api\V1\Mobile\ContractIndexController;
use App\Http\Controllers\Api\V1\Mobile\FeatureIndexController;
use App\Http\Controllers\Api\V1\Mobile\StatusController;
use App\Http\Controllers\Api\V1\Mobile\Tenants\SwitchTenantController;
use App\Http\Controllers\Api\V1\Mobile\Tenants\TenantIndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::prefix('mobile')
            ->name('mobile.')
            ->group(function (): void {
                Route::get('/status', StatusController::class)->name('status');
                Route::get('/contracts', ContractIndexController::class)->name('contracts.index');
                Route::get('/app-version', AppVersionController::class)->name('app-version');

                Route::middleware('mobile.auth')->group(function (): void {
                    Route::get('/bootstrap', BootstrapController::class)->name('bootstrap');
                    Route::get('/config', ConfigController::class)->name('config');
                    Route::get('/features', FeatureIndexController::class)->name('features.index');
                    Route::get('/tenants', TenantIndexController::class)->name('tenants.index');
                    Route::post('/tenants/current', SwitchTenantController::class)->name('tenants.current');
                });

                Route::prefix('auth')
                    ->name('auth.')
                    ->group(function (): void {
                        Route::post('/login', LoginController::class)->name('login');
                        Route::post('/register', RegisterController::class)->name('register');
                        Route::post('/refresh', RefreshTokenController::class)->name('refresh');

                        Route::middleware('mobile.auth')->group(function (): void {
                            Route::post('/logout', LogoutController::class)->name('logout');
                            Route::post('/logout-all', LogoutAllDevicesController::class)->name('logout-all');
                            Route::get('/user', CurrentUserController::class)->name('user');
                            Route::patch('/profile', ProfileController::class)->name('profile');
                        });
                    });
            });
    });
