<?php

use App\Livewire\Mobile\Settings\Account;
use App\Livewire\Mobile\Settings\Appearance;
use App\Livewire\Mobile\Settings\Developer;
use App\Livewire\Mobile\Settings\Legal;
use App\Livewire\Mobile\Settings\Notifications;
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
    'sync' => [Sync::class, 'Sync settings'],
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
        ->assertSee('Media capture')
        ->assertSee(route('mobile.media.capture'), false)
        ->assertSee('Media gallery')
        ->assertSee(route('mobile.media.gallery'), false)
        ->assertSee('Voice notes')
        ->assertSee(route('mobile.voice-notes'), false)
        ->assertSee('File manager')
        ->assertSee(route('mobile.files'), false)
        ->assertSee('QR/barcode scanner')
        ->assertSee(route('mobile.scanner'), false)
        ->assertSee('Location check-in')
        ->assertSee(route('mobile.location.check-in'), false)
        ->assertSee('Check-in history')
        ->assertSee(route('mobile.check-ins.index'), false)
        ->assertSee('Tailwind check')
        ->assertSee(route('dev.tailwind'), false);

    Livewire::test(Sync::class)
        ->assertSee('Conflict inbox')
        ->assertSee(route('mobile.conflicts.index'), false);

    Livewire::test(Storage::class)
        ->assertSee('Storage overview')
        ->assertSee('Local database size')
        ->assertSee('Clear cache')
        ->assertSee(route('mobile.settings'), false);
});
