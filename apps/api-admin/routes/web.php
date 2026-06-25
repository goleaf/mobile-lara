<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Livewire\Admin\AppVersionPolicies;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\FeatureFlags;
use App\Livewire\Admin\RemoteConfigs;
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
                    Route::livewire('/mobile/features', FeatureFlags::class)->name('mobile.features');
                    Route::livewire('/mobile/config', RemoteConfigs::class)->name('mobile.config');
                    Route::livewire('/mobile/app-versions', AppVersionPolicies::class)->name('mobile.app-versions');
                    Route::post('/logout', LogoutController::class)->name('logout');
                });
            });
    });
