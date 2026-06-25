<?php

namespace App\Enums;

enum TenantStatus: string
{
    case Active = 'active';
    case Onboarding = 'onboarding';
    case Limited = 'limited';
    case Suspended = 'suspended';
    case Disabled = 'disabled';
    case Archived = 'archived';
    case Maintenance = 'maintenance';

    public function isMobileSwitchable(): bool
    {
        return in_array($this, [
            self::Active,
            self::Onboarding,
            self::Limited,
        ], true);
    }
}
