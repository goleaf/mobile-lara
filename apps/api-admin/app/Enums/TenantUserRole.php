<?php

namespace App\Enums;

enum TenantUserRole: string
{
    case TenantAdmin = 'tenant_admin';
    case TenantManager = 'tenant_manager';
    case SupportAgent = 'support_agent';
    case BillingManager = 'billing_manager';
    case MobileUser = 'mobile_user';

    public function label(): string
    {
        return match ($this) {
            self::TenantAdmin => 'Tenant admin',
            self::TenantManager => 'Tenant manager',
            self::SupportAgent => 'Support agent',
            self::BillingManager => 'Billing manager',
            self::MobileUser => 'Mobile user',
        };
    }
}
