<?php

namespace App\Enums;

enum MobileAppVersionState: string
{
    case Current = 'current';
    case Supported = 'supported';
    case OptionalUpdate = 'optional_update';
    case RecommendedUpdate = 'recommended_update';
    case Deprecated = 'deprecated';
    case ForceUpdate = 'force_update';
    case Blocked = 'blocked';
    case Maintenance = 'maintenance';
    case InternalOnly = 'internal_only';
    case StaleClient = 'stale_client';

    public function blocksNormalUse(): bool
    {
        return in_array($this, [
            self::ForceUpdate,
            self::Blocked,
            self::Maintenance,
            self::InternalOnly,
            self::StaleClient,
        ], true);
    }
}
