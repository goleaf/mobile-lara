<?php

use App\Livewire\Mobile\Settings\Account;
use App\Livewire\Mobile\Settings\Appearance;
use App\Livewire\Mobile\Settings\Developer;
use App\Livewire\Mobile\Settings\Legal;
use App\Livewire\Mobile\Settings\Notifications;
use App\Livewire\Mobile\Settings\Permissions;
use App\Livewire\Mobile\Settings\Security;
use App\Livewire\Mobile\Settings\Storage;
use App\Livewire\Mobile\Settings\Support;
use App\Livewire\Mobile\Settings\Sync;
use Livewire\Livewire;

test('mobile settings section pages render shared placeholder structure', function (string $component, string $title): void {
    Livewire::test($component)
        ->assertSee($title)
        ->assertSee('Current status')
        ->assertSee('Section route ready')
        ->assertSee('Entries')
        ->assertSee(route('mobile.settings'), false);
})->with([
    'account' => [Account::class, 'Account settings'],
    'security' => [Security::class, 'Security settings'],
    'notifications' => [Notifications::class, 'Notification settings'],
    'appearance' => [Appearance::class, 'Appearance settings'],
    'storage' => [Storage::class, 'Storage settings'],
    'sync' => [Sync::class, 'Sync settings'],
    'permissions' => [Permissions::class, 'Permission settings'],
    'support' => [Support::class, 'Support settings'],
    'legal' => [Legal::class, 'Legal settings'],
    'developer' => [Developer::class, 'Developer settings'],
]);

test('settings section pages include connected and placeholder entries', function (): void {
    Livewire::test(Account::class)
        ->assertSee('Profile details')
        ->assertSee(route('mobile.profile'), false)
        ->assertSee('Delete account')
        ->assertSee(route('mobile.account.delete'), false);

    Livewire::test(Appearance::class)
        ->assertSee('Theme mode')
        ->assertSee('Placeholder');

    Livewire::test(Developer::class)
        ->assertSee('Mobile debug')
        ->assertSee(route('mobile.debug'), false)
        ->assertSee('Tailwind check')
        ->assertSee(route('dev.tailwind'), false);
});
