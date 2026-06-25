<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'settings_key',
    'theme',
    'language',
    'notification_preferences',
    'sync_settings',
    'biometric_enabled',
    'pin_enabled',
    'last_sync_at',
])]
class MobileLocalSetting extends Model
{
    public const DEFAULT_SETTINGS_KEY = 'default';

    public const THEME_SYSTEM = 'system';

    public const THEME_LIGHT = 'light';

    public const THEME_DARK = 'dark';

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'settings_key',
        'theme',
        'language',
        'notification_preferences',
        'sync_settings',
        'biometric_enabled',
        'pin_enabled',
        'last_sync_at',
        'created_at',
        'updated_at',
    ];

    protected $connection = 'mobile_local';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'settings_key' => self::DEFAULT_SETTINGS_KEY,
        'theme' => self::THEME_SYSTEM,
        'language' => 'en',
        'notification_preferences' => '{}',
        'sync_settings' => '{}',
        'biometric_enabled' => false,
        'pin_enabled' => false,
    ];

    public function scopeForKey(Builder $query, string $settingsKey): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->where('settings_key', $settingsKey);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notification_preferences' => 'array',
            'sync_settings' => 'array',
            'biometric_enabled' => 'boolean',
            'pin_enabled' => 'boolean',
            'last_sync_at' => 'immutable_datetime',
        ];
    }
}
