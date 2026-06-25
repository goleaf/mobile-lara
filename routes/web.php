<?php

use App\Livewire\Mobile\AccountDeletion;
use App\Livewire\Mobile\ActivityFeed;
use App\Livewire\Mobile\AppUnlock;
use App\Livewire\Mobile\Conflicts\ConflictDetail;
use App\Livewire\Mobile\Conflicts\ConflictList;
use App\Livewire\Mobile\ConsentAcceptance;
use App\Livewire\Mobile\ConsentHistory;
use App\Livewire\Mobile\Create;
use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\Debug;
use App\Livewire\Mobile\EditProfile;
use App\Livewire\Mobile\EmailVerification;
use App\Livewire\Mobile\ForgotPassword;
use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Notifications;
use App\Livewire\Mobile\PinChange;
use App\Livewire\Mobile\PinConfirm;
use App\Livewire\Mobile\PinCreate;
use App\Livewire\Mobile\PinRemove;
use App\Livewire\Mobile\PrivacyPolicy;
use App\Livewire\Mobile\Profile;
use App\Livewire\Mobile\Register;
use App\Livewire\Mobile\ResetPassword;
use App\Livewire\Mobile\Search;
use App\Livewire\Mobile\Sessions;
use App\Livewire\Mobile\Settings;
use App\Livewire\Mobile\Settings\Account as SettingsAccount;
use App\Livewire\Mobile\Settings\Appearance as SettingsAppearance;
use App\Livewire\Mobile\Settings\Developer as SettingsDeveloper;
use App\Livewire\Mobile\Settings\Legal as SettingsLegal;
use App\Livewire\Mobile\Settings\Notifications as SettingsNotifications;
use App\Livewire\Mobile\Settings\Permissions as SettingsPermissions;
use App\Livewire\Mobile\Settings\Security as SettingsSecurity;
use App\Livewire\Mobile\Settings\Storage as SettingsStorage;
use App\Livewire\Mobile\Settings\Support as SettingsSupport;
use App\Livewire\Mobile\Settings\Sync as SettingsSync;
use App\Livewire\Mobile\TermsOfService;
use App\Livewire\Mobile\Welcome;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('')
    ->name('mobile.')
    ->group(function (): void {
        Route::livewire('/', Welcome::class)->name('welcome');
        Route::livewire('/forgot-password', ForgotPassword::class)->name('password.request');
        Route::livewire('/reset-password/{token?}', ResetPassword::class)->name('password.reset');
        Route::livewire('/email/verify', EmailVerification::class)->name('verification.notice');
        Route::livewire('/terms', TermsOfService::class)->name('terms');
        Route::livewire('/privacy', PrivacyPolicy::class)->name('privacy');
        Route::livewire('/consent', ConsentAcceptance::class)->name('consent.accept');
        Route::livewire('/consent/history', ConsentHistory::class)->name('consent.history');

        Route::middleware(['guest'])->group(function (): void {
            Route::livewire('/login', Login::class)->name('login');
            Route::livewire('/register', Register::class)->name('register');
        });

        Route::middleware(['auth'])->group(function (): void {
            Route::livewire('/unlock', AppUnlock::class)->name('unlock');
            Route::livewire('/pin/create', PinCreate::class)->name('pin.create');
            Route::livewire('/pin/confirm', PinConfirm::class)->name('pin.confirm');

            Route::middleware(['mobile.unlock'])->group(function (): void {
                Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
                Route::livewire('/create', Create::class)->name('create');
                Route::livewire('/profile', Profile::class)->name('profile');
                Route::livewire('/profile/edit', EditProfile::class)->name('profile.edit');
                Route::livewire('/settings', Settings::class)->name('settings');
                Route::livewire('/settings/account', SettingsAccount::class)->name('settings.account');
                Route::livewire('/settings/security', SettingsSecurity::class)->name('settings.security');
                Route::livewire('/settings/notifications', SettingsNotifications::class)->name('settings.notifications');
                Route::livewire('/settings/appearance', SettingsAppearance::class)->name('settings.appearance');
                Route::livewire('/settings/storage', SettingsStorage::class)->name('settings.storage');
                Route::livewire('/settings/sync', SettingsSync::class)->name('settings.sync');
                Route::livewire('/settings/permissions', SettingsPermissions::class)->name('settings.permissions');
                Route::livewire('/settings/support', SettingsSupport::class)->name('settings.support');
                Route::livewire('/settings/legal', SettingsLegal::class)->name('settings.legal');
                Route::livewire('/settings/developer', SettingsDeveloper::class)->name('settings.developer');
                Route::livewire('/sessions', Sessions::class)->name('sessions');
                Route::livewire('/account/delete', AccountDeletion::class)->name('account.delete');
                Route::livewire('/activity', ActivityFeed::class)->name('activity');
                Route::livewire('/notifications', Notifications::class)->name('notifications');
                Route::livewire('/search', Search::class)->name('search');
                Route::livewire('/sync/conflicts', ConflictList::class)->name('conflicts.index');
                Route::livewire('/sync/conflicts/{offlineAction}', ConflictDetail::class)->name('conflicts.show');
                Route::livewire('/debug', Debug::class)->name('debug');
                Route::livewire('/pin/change', PinChange::class)->name('pin.change');
                Route::livewire('/pin/remove', PinRemove::class)->name('pin.remove');
            });
        });
    });

Route::middleware(['web'])
    ->prefix('dev')
    ->name('dev.')
    ->group(function (): void {
        Route::view('/tailwind', 'mobile.tailwind-test')->name('tailwind');
    });
