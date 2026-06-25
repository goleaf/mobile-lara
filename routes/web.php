<?php

use App\Livewire\Mobile\AppUnlock;
use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\Debug;
use App\Livewire\Mobile\EmailVerification;
use App\Livewire\Mobile\ForgotPassword;
use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Notifications;
use App\Livewire\Mobile\PinChange;
use App\Livewire\Mobile\PinConfirm;
use App\Livewire\Mobile\PinCreate;
use App\Livewire\Mobile\PinRemove;
use App\Livewire\Mobile\Profile;
use App\Livewire\Mobile\Register;
use App\Livewire\Mobile\ResetPassword;
use App\Livewire\Mobile\Search;
use App\Livewire\Mobile\Settings;
use App\Livewire\Mobile\Welcome;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('')
    ->name('mobile.')
    ->group(function (): void {
        Route::livewire('/', Welcome::class)->name('welcome');
        Route::livewire('/login', Login::class)->name('login');
        Route::livewire('/register', Register::class)->name('register');
        Route::livewire('/forgot-password', ForgotPassword::class)->name('password.request');
        Route::livewire('/reset-password/{token?}', ResetPassword::class)->name('password.reset');
        Route::livewire('/email/verify', EmailVerification::class)->name('verification.notice');
        Route::livewire('/unlock', AppUnlock::class)->name('unlock');
        Route::livewire('/pin/create', PinCreate::class)->name('pin.create');
        Route::livewire('/pin/confirm', PinConfirm::class)->name('pin.confirm');

        Route::middleware(['mobile.unlock'])->group(function (): void {
            Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
            Route::livewire('/profile', Profile::class)->name('profile');
            Route::livewire('/settings', Settings::class)->name('settings');
            Route::livewire('/notifications', Notifications::class)->name('notifications');
            Route::livewire('/search', Search::class)->name('search');
            Route::livewire('/debug', Debug::class)->name('debug');
            Route::livewire('/pin/change', PinChange::class)->name('pin.change');
            Route::livewire('/pin/remove', PinRemove::class)->name('pin.remove');
        });
    });

Route::middleware(['web'])
    ->prefix('dev')
    ->name('dev.')
    ->group(function (): void {
        Route::view('/tailwind', 'mobile.tailwind-test')->name('tailwind');
    });
