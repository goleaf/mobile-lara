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

test('mobile routes render livewire components', function (string $route, string $component, string $pageTitle): void {
    $this->withoutVite();

    $this->get(route($route))
        ->assertOk()
        ->assertSeeLivewire($component)
        ->assertSee($pageTitle);
})->with([
    'welcome' => ['mobile.welcome', Welcome::class, 'Welcome'],
    'login' => ['mobile.login', Login::class, 'Login'],
    'register' => ['mobile.register', Register::class, 'Register'],
    'forgot password' => ['mobile.password.request', ForgotPassword::class, 'Forgot password'],
    'reset password' => ['mobile.password.reset', ResetPassword::class, 'Reset password'],
    'email verification' => ['mobile.verification.notice', EmailVerification::class, 'Verify email'],
    'unlock' => ['mobile.unlock', AppUnlock::class, 'Unlock app'],
    'pin create' => ['mobile.pin.create', PinCreate::class, 'Create PIN'],
    'pin confirm' => ['mobile.pin.confirm', PinConfirm::class, 'Confirm PIN'],
    'pin change' => ['mobile.pin.change', PinChange::class, 'Change PIN'],
    'pin remove' => ['mobile.pin.remove', PinRemove::class, 'Remove PIN'],
    'dashboard' => ['mobile.dashboard', Dashboard::class, 'Dashboard'],
    'profile' => ['mobile.profile', Profile::class, 'Profile'],
    'settings' => ['mobile.settings', Settings::class, 'Settings'],
    'notifications' => ['mobile.notifications', Notifications::class, 'Notifications'],
    'search' => ['mobile.search', Search::class, 'Search'],
    'debug' => ['mobile.debug', Debug::class, 'Debug'],
]);

test('mobile routes render the shared livewire app shell', function (): void {
    $this->withoutVite();

    $this->get(route('mobile.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('aria-label="Notifications"', false)
        ->assertSee('aria-label="Profile"', false)
        ->assertSee('aria-label="Primary tabs"', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('wire:key="mobile-tab-mobile.dashboard"', false)
        ->assertSee('safe-pt', false)
        ->assertSee('safe-pb', false);
});
