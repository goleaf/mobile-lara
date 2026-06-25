<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalSetting;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;

final class SettingsRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    public function get(): MobileLocalSetting
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $settings = MobileLocalSetting::query()
            ->forKey($this->settingsKey())
            ->first();

        if ($settings instanceof MobileLocalSetting) {
            return $settings;
        }

        return MobileLocalSetting::query()->create($this->defaultAttributes());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(array $attributes): MobileLocalSetting
    {
        $settings = $this->get();

        $settings->fill($this->settingsAttributes($attributes));
        $settings->save();

        return $this->get();
    }

    public function setTheme(string $theme): MobileLocalSetting
    {
        return $this->update(['theme' => $theme]);
    }

    public function setLanguage(string $language): MobileLocalSetting
    {
        return $this->update(['language' => $language]);
    }

    /**
     * @param  array<string, mixed>  $preferences
     */
    public function setNotificationPreferences(array $preferences): MobileLocalSetting
    {
        return $this->update(['notification_preferences' => $preferences]);
    }

    /**
     * @param  array<string, mixed>  $preferences
     */
    public function mergeNotificationPreferences(array $preferences): MobileLocalSetting
    {
        $currentPreferences = $this->get()->notification_preferences ?? [];

        return $this->setNotificationPreferences(array_replace($currentPreferences, $preferences));
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function setSyncSettings(array $settings): MobileLocalSetting
    {
        return $this->update(['sync_settings' => $settings]);
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function mergeSyncSettings(array $settings): MobileLocalSetting
    {
        $currentSettings = $this->get()->sync_settings ?? [];

        return $this->setSyncSettings(array_replace($currentSettings, $settings));
    }

    public function setBiometricEnabled(bool $enabled): MobileLocalSetting
    {
        return $this->update(['biometric_enabled' => $enabled]);
    }

    public function setPinEnabled(bool $enabled): MobileLocalSetting
    {
        return $this->update(['pin_enabled' => $enabled]);
    }

    public function markSynced(?CarbonInterface $syncedAt = null): MobileLocalSetting
    {
        return $this->update(['last_sync_at' => $syncedAt ?: CarbonImmutable::now()]);
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultAttributes(): array
    {
        return [
            'settings_key' => $this->settingsKey(),
            'theme' => (string) config('mobile_local.settings.theme', MobileLocalSetting::THEME_SYSTEM),
            'language' => (string) config('mobile_local.settings.language', config('app.locale', 'en')),
            'notification_preferences' => (array) config('mobile_local.settings.notification_preferences', []),
            'sync_settings' => (array) config('mobile_local.settings.sync_settings', []),
            'biometric_enabled' => false,
            'pin_enabled' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function settingsAttributes(array $attributes): array
    {
        return Arr::only($attributes, [
            'theme',
            'language',
            'notification_preferences',
            'sync_settings',
            'biometric_enabled',
            'pin_enabled',
            'last_sync_at',
        ]);
    }

    private function settingsKey(): string
    {
        return (string) config('mobile_local.settings.key', MobileLocalSetting::DEFAULT_SETTINGS_KEY);
    }
}
