<?php

namespace App\Enums;

enum TenantUserStatus: string
{
    case Invited = 'invited';
    case Active = 'active';
    case Suspended = 'suspended';

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
