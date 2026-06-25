<?php

use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\Debug;
use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Notifications;
use App\Livewire\Mobile\Profile;
use App\Livewire\Mobile\Register;
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
        Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
        Route::livewire('/profile', Profile::class)->name('profile');
        Route::livewire('/settings', Settings::class)->name('settings');
        Route::livewire('/notifications', Notifications::class)->name('notifications');
        Route::livewire('/search', Search::class)->name('search');
        Route::livewire('/debug', Debug::class)->name('debug');
    });

Route::middleware(['web'])
    ->prefix('dev')
    ->name('dev.')
    ->group(function (): void {
        Route::view('/tailwind', 'mobile.tailwind-test')->name('tailwind');
    });
