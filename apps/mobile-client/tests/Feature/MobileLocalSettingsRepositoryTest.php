<?php

use App\Models\MobileLocalSetting;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-settings.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_local.settings' => [
            'key' => 'default',
            'theme' => 'light',
            'language' => 'en',
            'notification_preferences' => [
                'push_enabled' => true,
                'email_enabled' => false,
            ],
            'sync_settings' => [
                'auto_sync_enabled' => true,
                'wifi_only' => false,
            ],
        ],
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

test('settings repository creates the default local settings row', function (): void {
    $settings = app(SettingsRepository::class)->get();

    expect($settings)->toBeInstanceOf(MobileLocalSetting::class)
        ->and($settings->settings_key)->toBe('default')
        ->and($settings->theme)->toBe('light')
        ->and($settings->language)->toBe('en')
        ->and($settings->notification_preferences)->toBe([
            'push_enabled' => true,
            'email_enabled' => false,
        ])
        ->and($settings->sync_settings)->toBe([
            'auto_sync_enabled' => true,
            'wifi_only' => false,
        ])
        ->and($settings->biometric_enabled)->toBeFalse()
        ->and($settings->pin_enabled)->toBeFalse()
        ->and($settings->last_sync_at)->toBeNull()
        ->and(MobileLocalSetting::query()->count())->toBe(1);
});

test('settings repository updates all local settings fields', function (): void {
    $settings = app(SettingsRepository::class)->update([
        'theme' => 'system',
        'language' => 'lt',
        'notification_preferences' => [
            'push_enabled' => false,
            'sms_enabled' => true,
        ],
        'sync_settings' => [
            'auto_sync_enabled' => false,
            'wifi_only' => true,
        ],
        'bootstrap_context' => [
            'success' => true,
            'data' => ['features' => ['version' => 'foundation-1']],
        ],
        'bootstrap_cached_at' => CarbonImmutable::parse('2026-06-25 13:30:00'),
        'biometric_enabled' => true,
        'pin_enabled' => true,
        'last_sync_at' => CarbonImmutable::parse('2026-06-25 13:45:00'),
    ]);

    expect($settings->theme)->toBe('light')
        ->and($settings->language)->toBe('lt')
        ->and($settings->notification_preferences)->toBe([
            'push_enabled' => false,
            'sms_enabled' => true,
        ])
        ->and($settings->sync_settings)->toBe([
            'auto_sync_enabled' => false,
            'wifi_only' => true,
        ])
        ->and($settings->bootstrap_context)->toBe([
            'success' => true,
            'data' => ['features' => ['version' => 'foundation-1']],
        ])
        ->and($settings->bootstrap_cached_at?->toDateTimeString())->toBe('2026-06-25 13:30:00')
        ->and($settings->biometric_enabled)->toBeTrue()
        ->and($settings->pin_enabled)->toBeTrue()
        ->and($settings->last_sync_at?->toDateTimeString())->toBe('2026-06-25 13:45:00')
        ->and(MobileLocalSetting::query()->count())->toBe(1);
});

test('settings repository exposes focused mutators for mobile settings', function (): void {
    $repository = app(SettingsRepository::class);

    $repository->setTheme('light');
    $repository->setLanguage('ru');
    $repository->mergeNotificationPreferences(['email_enabled' => true]);
    $repository->mergeSyncSettings(['wifi_only' => true]);
    $repository->cacheBootstrapContext(['success' => true, 'data' => ['user' => ['id' => 1]]]);
    $repository->setBiometricEnabled(true);
    $repository->setPinEnabled(true);

    $settings = $repository->markSynced();

    expect($settings->theme)->toBe('light')
        ->and($settings->language)->toBe('ru')
        ->and($settings->notification_preferences)->toBe([
            'push_enabled' => true,
            'email_enabled' => true,
        ])
        ->and($settings->sync_settings)->toBe([
            'auto_sync_enabled' => true,
            'wifi_only' => true,
        ])
        ->and($settings->bootstrap_context)->toBe(['success' => true, 'data' => ['user' => ['id' => 1]]])
        ->and($settings->bootstrap_cached_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($settings->biometric_enabled)->toBeTrue()
        ->and($settings->pin_enabled)->toBeTrue()
        ->and($settings->last_sync_at?->equalTo(CarbonImmutable::now()))->toBeTrue();
});
