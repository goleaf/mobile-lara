<?php

use App\Livewire\Mobile\AccountDeletion;
use App\Livewire\Mobile\ActivityFeed;
use App\Livewire\Mobile\AppUnlock;
use App\Livewire\Mobile\CheckInCreate;
use App\Livewire\Mobile\CheckInHistory;
use App\Livewire\Mobile\Conflicts\ConflictDetail;
use App\Livewire\Mobile\Conflicts\ConflictList;
use App\Livewire\Mobile\ConsentAcceptance;
use App\Livewire\Mobile\ConsentHistory;
use App\Livewire\Mobile\Create;
use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\Debug;
use App\Livewire\Mobile\EditProfile;
use App\Livewire\Mobile\EmailVerification;
use App\Livewire\Mobile\FileManager;
use App\Livewire\Mobile\ForceUpdate;
use App\Livewire\Mobile\ForgotPassword;
use App\Livewire\Mobile\LocationCheckIn;
use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Maintenance;
use App\Livewire\Mobile\MediaCapture;
use App\Livewire\Mobile\MediaGallery;
use App\Livewire\Mobile\Notifications;
use App\Livewire\Mobile\PinChange;
use App\Livewire\Mobile\PinConfirm;
use App\Livewire\Mobile\PinCreate;
use App\Livewire\Mobile\PinRemove;
use App\Livewire\Mobile\PrivacyPolicy;
use App\Livewire\Mobile\Profile;
use App\Livewire\Mobile\RecordCategories;
use App\Livewire\Mobile\RecordCreate;
use App\Livewire\Mobile\RecordDetail;
use App\Livewire\Mobile\RecordEdit;
use App\Livewire\Mobile\Records;
use App\Livewire\Mobile\Register;
use App\Livewire\Mobile\ResetPassword;
use App\Livewire\Mobile\ScanHistory;
use App\Livewire\Mobile\ScannerDemo;
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
use App\Livewire\Mobile\Settings\Workspace as SettingsWorkspace;
use App\Livewire\Mobile\SupportTicketCreate;
use App\Livewire\Mobile\SupportTicketDetail;
use App\Livewire\Mobile\SupportTickets;
use App\Livewire\Mobile\TermsOfService;
use App\Livewire\Mobile\VoiceNotes;
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
            Route::livewire('/update-required', ForceUpdate::class)->name('update-required');
            Route::livewire('/maintenance', Maintenance::class)->name('maintenance');

            Route::middleware(['mobile.unlock'])->group(function (): void {
                Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
                Route::livewire('/create', Create::class)->name('create');
                Route::livewire('/profile', Profile::class)->name('profile');
                Route::livewire('/profile/edit', EditProfile::class)->name('profile.edit');
                Route::livewire('/settings', Settings::class)->name('settings');
                Route::livewire('/settings/account', SettingsAccount::class)->name('settings.account');
                Route::livewire('/settings/workspace', SettingsWorkspace::class)->name('settings.workspace');
                Route::livewire('/settings/security', SettingsSecurity::class)->name('settings.security');
                Route::livewire('/settings/notifications', SettingsNotifications::class)->name('settings.notifications');
                Route::livewire('/settings/appearance', SettingsAppearance::class)->name('settings.appearance');
                Route::livewire('/settings/storage', SettingsStorage::class)->name('settings.storage');
                Route::livewire('/settings/sync', SettingsSync::class)->name('settings.sync');
                Route::livewire('/settings/permissions', SettingsPermissions::class)->name('settings.permissions');
                Route::livewire('/settings/support', SettingsSupport::class)->name('settings.support');
                Route::livewire('/settings/legal', SettingsLegal::class)->name('settings.legal');
                Route::livewire('/settings/developer', SettingsDeveloper::class)->name('settings.developer');
                Route::livewire('/support', SupportTickets::class)
                    ->middleware('mobile.feature:support,support.view')
                    ->name('support.index');
                Route::livewire('/support/create', SupportTicketCreate::class)
                    ->middleware('mobile.feature:support,support.create')
                    ->name('support.create');
                Route::livewire('/support/{ticket}', SupportTicketDetail::class)
                    ->middleware('mobile.feature:support,support.view')
                    ->name('support.show');
                Route::livewire('/sessions', Sessions::class)->name('sessions');
                Route::livewire('/account/delete', AccountDeletion::class)->name('account.delete');
                Route::livewire('/activity', ActivityFeed::class)->name('activity');
                Route::livewire('/notifications', Notifications::class)
                    ->middleware('mobile.feature:notifications,notifications.view')
                    ->name('notifications');
                Route::livewire('/search', Search::class)->name('search');
                Route::livewire('/sync/conflicts', ConflictList::class)
                    ->middleware('mobile.feature:offline_sync,sync.view')
                    ->name('conflicts.index');
                Route::livewire('/sync/conflicts/{offlineAction}', ConflictDetail::class)
                    ->middleware('mobile.feature:offline_sync,sync.view')
                    ->name('conflicts.show');
                Route::livewire('/media-capture', MediaCapture::class)
                    ->middleware('mobile.feature:native_camera')
                    ->name('media.capture');
                Route::livewire('/media-gallery', MediaGallery::class)
                    ->middleware('mobile.feature:native_files')
                    ->name('media.gallery');
                Route::livewire('/voice-notes', VoiceNotes::class)
                    ->middleware('mobile.feature:native_microphone')
                    ->name('voice-notes');
                Route::livewire('/files', FileManager::class)
                    ->middleware('mobile.feature:native_files')
                    ->name('files');
                Route::livewire('/records', Records::class)
                    ->middleware('mobile.feature:records,records.view')
                    ->name('records.index');
                Route::livewire('/records/categories', RecordCategories::class)
                    ->middleware('mobile.feature:records,records.view')
                    ->name('records.categories');
                Route::livewire('/records/create', RecordCreate::class)
                    ->middleware('mobile.feature:records,records.create')
                    ->name('records.create');
                Route::livewire('/records/{record}', RecordDetail::class)
                    ->middleware('mobile.feature:records,records.view')
                    ->name('records.show');
                Route::livewire('/records/{record}/edit', RecordEdit::class)
                    ->middleware('mobile.feature:records,records.update')
                    ->name('records.edit');
                Route::livewire('/scanner', ScannerDemo::class)
                    ->middleware('mobile.feature:native_scanner')
                    ->name('scanner');
                Route::livewire('/scan-history', ScanHistory::class)
                    ->middleware('mobile.feature:native_scanner')
                    ->name('scan-history');
                Route::livewire('/location-check-in', LocationCheckIn::class)
                    ->middleware('mobile.feature:native_location')
                    ->name('location.check-in');
                Route::livewire('/check-ins', CheckInHistory::class)
                    ->middleware('mobile.feature:native_location')
                    ->name('check-ins.index');
                Route::livewire('/check-ins/create', CheckInCreate::class)
                    ->middleware('mobile.feature:native_location')
                    ->name('check-ins.create');
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
