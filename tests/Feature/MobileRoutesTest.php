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
    'dashboard' => ['mobile.dashboard', Dashboard::class, 'Dashboard'],
    'profile' => ['mobile.profile', Profile::class, 'Profile'],
    'settings' => ['mobile.settings', Settings::class, 'Settings'],
    'notifications' => ['mobile.notifications', Notifications::class, 'Notifications'],
    'search' => ['mobile.search', Search::class, 'Search'],
    'debug' => ['mobile.debug', Debug::class, 'Debug'],
]);
