<?php

use App\Livewire\Mobile\AccountDeletion;
use App\Livewire\Mobile\AppUnlock;
use App\Livewire\Mobile\CheckInCreate;
use App\Livewire\Mobile\CheckInHistory;
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
use App\Livewire\Mobile\OfflineBanner;
use App\Livewire\Mobile\PinChange;
use App\Livewire\Mobile\PinConfirm;
use App\Livewire\Mobile\PinCreate;
use App\Livewire\Mobile\PinRemove;
use App\Livewire\Mobile\PrivacyPolicy;
use App\Livewire\Mobile\Profile;
use App\Livewire\Mobile\RecordCategories;
use App\Livewire\Mobile\RecordCreate;
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
use App\Livewire\Mobile\TermsOfService;
use App\Livewire\Mobile\ToastCenter;
use App\Livewire\Mobile\VoiceNotes;
use App\Livewire\Mobile\Welcome;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('public mobile routes render livewire components', function (string $route, string $component, string $pageTitle): void {
    $this->withoutVite();

    $this->get(route($route))
        ->assertOk()
        ->assertSeeLivewire($component)
        ->assertSee($pageTitle);
})->with([
    'welcome' => ['mobile.welcome', Welcome::class, 'Welcome'],
    'forgot password' => ['mobile.password.request', ForgotPassword::class, 'Forgot password'],
    'reset password' => ['mobile.password.reset', ResetPassword::class, 'Reset password'],
    'email verification' => ['mobile.verification.notice', EmailVerification::class, 'Verify email'],
    'terms' => ['mobile.terms', TermsOfService::class, 'Terms of Service'],
    'privacy' => ['mobile.privacy', PrivacyPolicy::class, 'Privacy Policy'],
    'consent accept' => ['mobile.consent.accept', ConsentAcceptance::class, 'Consent'],
    'consent history' => ['mobile.consent.history', ConsentHistory::class, 'Consent history'],
]);

test('guest mobile auth routes render livewire components', function (string $route, string $component, string $pageTitle): void {
    $this->withoutVite();

    $this->get(route($route))
        ->assertOk()
        ->assertSeeLivewire($component)
        ->assertSee($pageTitle);
})->with([
    'login' => ['mobile.login', Login::class, 'Login'],
    'register' => ['mobile.register', Register::class, 'Register'],
]);

test('protected mobile routes redirect guests to login', function (string $route): void {
    $this->get(route($route))
        ->assertRedirect(route('mobile.login'));
})->with([
    'unlock' => 'mobile.unlock',
    'pin create' => 'mobile.pin.create',
    'pin confirm' => 'mobile.pin.confirm',
    'update required' => 'mobile.update-required',
    'maintenance' => 'mobile.maintenance',
    'pin change' => 'mobile.pin.change',
    'pin remove' => 'mobile.pin.remove',
    'dashboard' => 'mobile.dashboard',
    'create' => 'mobile.create',
    'profile' => 'mobile.profile',
    'profile edit' => 'mobile.profile.edit',
    'settings' => 'mobile.settings',
    'settings account' => 'mobile.settings.account',
    'settings workspace' => 'mobile.settings.workspace',
    'settings security' => 'mobile.settings.security',
    'settings notifications' => 'mobile.settings.notifications',
    'settings appearance' => 'mobile.settings.appearance',
    'settings storage' => 'mobile.settings.storage',
    'settings sync' => 'mobile.settings.sync',
    'settings permissions' => 'mobile.settings.permissions',
    'settings support' => 'mobile.settings.support',
    'settings legal' => 'mobile.settings.legal',
    'settings developer' => 'mobile.settings.developer',
    'conflicts index' => 'mobile.conflicts.index',
    'sessions' => 'mobile.sessions',
    'account delete' => 'mobile.account.delete',
    'notifications' => 'mobile.notifications',
    'search' => 'mobile.search',
    'media capture' => 'mobile.media.capture',
    'media gallery' => 'mobile.media.gallery',
    'voice notes' => 'mobile.voice-notes',
    'files' => 'mobile.files',
    'records index' => 'mobile.records.index',
    'records categories' => 'mobile.records.categories',
    'records create' => 'mobile.records.create',
    'scanner' => 'mobile.scanner',
    'scan history' => 'mobile.scan-history',
    'location check-in' => 'mobile.location.check-in',
    'check-ins index' => 'mobile.check-ins.index',
    'check-ins create' => 'mobile.check-ins.create',
    'debug' => 'mobile.debug',
]);

test('protected mobile routes render for authenticated users', function (string $route, string $component, string $pageTitle): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    $this->get(route($route))
        ->assertOk()
        ->assertSeeLivewire($component)
        ->assertSee($pageTitle);
})->with([
    'unlock' => ['mobile.unlock', AppUnlock::class, 'Unlock app'],
    'pin create' => ['mobile.pin.create', PinCreate::class, 'Create PIN'],
    'pin confirm' => ['mobile.pin.confirm', PinConfirm::class, 'Confirm PIN'],
    'update required' => ['mobile.update-required', ForceUpdate::class, 'Update required'],
    'maintenance' => ['mobile.maintenance', Maintenance::class, 'Maintenance'],
    'pin change' => ['mobile.pin.change', PinChange::class, 'Change PIN'],
    'pin remove' => ['mobile.pin.remove', PinRemove::class, 'Remove PIN'],
    'dashboard' => ['mobile.dashboard', Dashboard::class, 'Dashboard'],
    'create' => ['mobile.create', Create::class, 'Create'],
    'profile' => ['mobile.profile', Profile::class, 'Profile'],
    'profile edit' => ['mobile.profile.edit', EditProfile::class, 'Edit profile'],
    'settings' => ['mobile.settings', Settings::class, 'Settings'],
    'settings account' => ['mobile.settings.account', SettingsAccount::class, 'Account settings'],
    'settings workspace' => ['mobile.settings.workspace', SettingsWorkspace::class, 'Workspace settings'],
    'settings security' => ['mobile.settings.security', SettingsSecurity::class, 'Security settings'],
    'settings notifications' => ['mobile.settings.notifications', SettingsNotifications::class, 'Notification settings'],
    'settings appearance' => ['mobile.settings.appearance', SettingsAppearance::class, 'Appearance settings'],
    'settings storage' => ['mobile.settings.storage', SettingsStorage::class, 'Storage settings'],
    'settings sync' => ['mobile.settings.sync', SettingsSync::class, 'Sync settings'],
    'settings permissions' => ['mobile.settings.permissions', SettingsPermissions::class, 'Permissions center'],
    'settings support' => ['mobile.settings.support', SettingsSupport::class, 'Support settings'],
    'settings legal' => ['mobile.settings.legal', SettingsLegal::class, 'Legal settings'],
    'settings developer' => ['mobile.settings.developer', SettingsDeveloper::class, 'Developer settings'],
    'conflicts index' => ['mobile.conflicts.index', ConflictList::class, 'Sync conflicts'],
    'sessions' => ['mobile.sessions', Sessions::class, 'Sessions'],
    'account delete' => ['mobile.account.delete', AccountDeletion::class, 'Delete account'],
    'notifications' => ['mobile.notifications', Notifications::class, 'Notifications'],
    'search' => ['mobile.search', Search::class, 'Search'],
    'media capture' => ['mobile.media.capture', MediaCapture::class, 'Media capture'],
    'media gallery' => ['mobile.media.gallery', MediaGallery::class, 'Media gallery'],
    'voice notes' => ['mobile.voice-notes', VoiceNotes::class, 'Voice notes'],
    'files' => ['mobile.files', FileManager::class, 'File manager'],
    'records index' => ['mobile.records.index', Records::class, 'Records'],
    'records categories' => ['mobile.records.categories', RecordCategories::class, 'Record categories'],
    'records create' => ['mobile.records.create', RecordCreate::class, 'Create record'],
    'scanner' => ['mobile.scanner', ScannerDemo::class, 'QR/barcode scanner'],
    'scan history' => ['mobile.scan-history', ScanHistory::class, 'Scan history'],
    'location check-in' => ['mobile.location.check-in', LocationCheckIn::class, 'Location check-in'],
    'check-ins index' => ['mobile.check-ins.index', CheckInHistory::class, 'Check-in history'],
    'check-ins create' => ['mobile.check-ins.create', CheckInCreate::class, 'Create check-in'],
    'debug' => ['mobile.debug', Debug::class, 'Debug'],
]);

test('authenticated users are redirected away from guest mobile auth routes', function (string $route): void {
    $this->actingAs(User::factory()->create());

    $this->get(route($route))
        ->assertRedirect(route('mobile.dashboard'));
})->with([
    'login' => 'mobile.login',
    'register' => 'mobile.register',
]);

test('mobile routes render the shared livewire app shell', function (): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    $this->get(route('mobile.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSeeLivewire(OfflineBanner::class)
        ->assertSeeLivewire(ToastCenter::class)
        ->assertSee('aria-label="Notifications"', false)
        ->assertSee('aria-label="Profile"', false)
        ->assertSee('aria-label="Primary tabs"', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('mobile-shell', false)
        ->assertSee('mobile-shell-header', false)
        ->assertSee('mobile-shell-footer', false)
        ->assertSee('wire:key="mobile-tab-mobile.dashboard"', false)
        ->assertSee('wire:key="mobile-tab-mobile.search"', false)
        ->assertSee('wire:key="mobile-tab-mobile.create"', false)
        ->assertSee('wire:key="mobile-tab-mobile.notifications"', false)
        ->assertSee('wire:key="mobile-tab-mobile.profile"', false)
        ->assertDontSee('wire:key="mobile-tab-mobile.settings"', false)
        ->assertSee('safe-pt', false)
        ->assertSee('safe-pb', false);
});
