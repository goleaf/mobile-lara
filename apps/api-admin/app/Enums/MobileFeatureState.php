<?php

namespace App\Enums;

enum MobileFeatureState: string
{
    case Hidden = 'hidden';
    case Visible = 'visible';
    case Disabled = 'disabled';
    case Blocked = 'blocked';
    case Beta = 'beta';
    case Deprecated = 'deprecated';
    case UpdateRequired = 'update_required';
    case OfflineLimited = 'offline_limited';
    case EmergencyDisabled = 'emergency_disabled';

    public function isVisible(): bool
    {
        return $this !== self::Hidden;
    }

    public function isEnabled(): bool
    {
        return in_array($this, [
            self::Visible,
            self::Beta,
            self::Deprecated,
            self::OfflineLimited,
        ], true);
    }
}
