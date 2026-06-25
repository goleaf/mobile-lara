<?php

use App\Livewire\Mobile\CheckInCreate;
use App\Livewire\Mobile\CheckInHistory;
use App\Models\MobileLocalCheckIn;
use App\Models\MobileLocalMediaItem;
use App\Models\User;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-check-ins.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('check-in history renders local rows and summary metrics for the current user', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $photo = MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/check-ins/site-photo.jpg',
        'type' => MobileLocalMediaItem::TYPE_IMAGE,
    ]);

    MobileLocalCheckIn::factory()->create([
        'user_id' => $user->id,
        'latitude' => 54.687157,
        'longitude' => 25.279652,
        'accuracy' => 8.5,
        'note' => 'Customer site arrival',
        'photo_id' => $photo->id,
        'sync_status' => MobileLocalCheckIn::SYNC_PENDING,
        'created_at' => CarbonImmutable::now(),
    ]);

    MobileLocalCheckIn::factory()->synced()->create([
        'user_id' => $user->id,
        'latitude' => 54.680000,
        'longitude' => 25.270000,
        'note' => 'Synced visit',
        'created_at' => CarbonImmutable::now()->subMinute(),
    ]);

    MobileLocalCheckIn::factory()->failed()->create([
        'user_id' => User::factory()->create()->id,
        'note' => 'Other user row',
        'created_at' => CarbonImmutable::now()->subMinutes(2),
    ]);

    Livewire::test(CheckInHistory::class)
        ->assertSee('Check-in history')
        ->assertSee('History summary')
        ->assertSee('2 shown')
        ->assertSee('54.6871570, 25.2796520')
        ->assertSee('8.50 m')
        ->assertSee('Customer site arrival')
        ->assertSee('site-photo.jpg')
        ->assertDontSee('Other user row')
        ->call('setFilter', 'synced')
        ->assertSet('filter', 'synced')
        ->assertSee('Synced visit')
        ->assertDontSee('Customer site arrival')
        ->call('setFilter', 'unknown')
        ->assertSet('filter', 'all')
        ->assertSee('Customer site arrival');
});

test('check-in history renders an empty state without local rows', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(CheckInHistory::class)
        ->assertSee('No check-ins')
        ->assertSee('0 shown');
});

test('create check-in screen saves a pending local check-in', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $photo = MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/check-ins/new-photo.jpg',
        'type' => MobileLocalMediaItem::TYPE_IMAGE,
    ]);

    Livewire::test(CheckInCreate::class)
        ->assertSee('Create check-in')
        ->assertSee('Location details')
        ->set('latitude', '54.687157')
        ->set('longitude', '25.279652')
        ->set('accuracy', '6.54')
        ->set('note', 'Saved from Livewire form')
        ->set('photoId', (string) $photo->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('mobile.check-ins.index'));

    $checkIn = MobileLocalCheckIn::query()->first();

    expect($checkIn)->not->toBeNull()
        ->and($checkIn?->user_id)->toBe($user->id)
        ->and($checkIn?->latitude)->toBe(54.687157)
        ->and($checkIn?->longitude)->toBe(25.279652)
        ->and($checkIn?->accuracy)->toBe(6.54)
        ->and($checkIn?->note)->toBe('Saved from Livewire form')
        ->and($checkIn?->photo_id)->toBe($photo->id)
        ->and($checkIn?->sync_status)->toBe(MobileLocalCheckIn::SYNC_PENDING);
});

test('create check-in screen validates coordinates and local photo selection', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(CheckInCreate::class)
        ->set('latitude', '120')
        ->set('longitude', '200')
        ->set('accuracy', '-1')
        ->set('photoId', '999')
        ->call('save')
        ->assertHasErrors(['latitude', 'longitude', 'accuracy'])
        ->set('latitude', '54.687157')
        ->set('longitude', '25.279652')
        ->set('accuracy', '8')
        ->call('save')
        ->assertHasErrors(['photoId']);
});
