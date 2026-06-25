<?php

namespace App\Enums;

enum MobilePermission: string
{
    case TenantView = 'tenant.view';
    case TenantSwitch = 'tenant.switch';
    case TenantUsersView = 'tenant.users.view';
    case TenantUsersManage = 'tenant.users.manage';
    case RecordsView = 'records.view';
    case RecordsCreate = 'records.create';
    case RecordsUpdate = 'records.update';
    case RecordsArchive = 'records.archive';
    case RecordsDelete = 'records.delete';
    case RecordsAttachmentsManage = 'records.attachments.manage';
    case SyncView = 'sync.view';
    case SyncRun = 'sync.run';
    case SyncResolveConflicts = 'sync.conflicts.resolve';
    case NotificationsView = 'notifications.view';
    case NotificationsManagePreferences = 'notifications.preferences.manage';
    case SupportView = 'support.view';
    case SupportCreate = 'support.create';
    case SupportManage = 'support.manage';
    case ReportsView = 'reports.view';
    case BillingView = 'billing.view';
    case BillingManage = 'billing.manage';
    case DiagnosticsView = 'diagnostics.view';

    /**
     * @return array<int, self>
     */
    public static function forRole(TenantUserRole $role): array
    {
        return match ($role) {
            TenantUserRole::TenantAdmin => [
                self::TenantView,
                self::TenantSwitch,
                self::TenantUsersView,
                self::TenantUsersManage,
                self::RecordsView,
                self::RecordsCreate,
                self::RecordsUpdate,
                self::RecordsArchive,
                self::RecordsDelete,
                self::RecordsAttachmentsManage,
                self::SyncView,
                self::SyncRun,
                self::SyncResolveConflicts,
                self::NotificationsView,
                self::NotificationsManagePreferences,
                self::SupportView,
                self::SupportCreate,
                self::ReportsView,
                self::DiagnosticsView,
            ],
            TenantUserRole::TenantManager => [
                self::TenantView,
                self::TenantSwitch,
                self::TenantUsersView,
                self::RecordsView,
                self::RecordsCreate,
                self::RecordsUpdate,
                self::RecordsArchive,
                self::RecordsAttachmentsManage,
                self::SyncView,
                self::SyncRun,
                self::NotificationsView,
                self::SupportView,
                self::SupportCreate,
                self::ReportsView,
                self::DiagnosticsView,
            ],
            TenantUserRole::SupportAgent => [
                self::TenantView,
                self::TenantSwitch,
                self::SupportView,
                self::SupportManage,
                self::SyncView,
                self::DiagnosticsView,
            ],
            TenantUserRole::BillingManager => [
                self::TenantView,
                self::TenantSwitch,
                self::BillingView,
                self::BillingManage,
                self::DiagnosticsView,
            ],
            TenantUserRole::MobileUser => [
                self::TenantView,
                self::TenantSwitch,
                self::RecordsView,
                self::RecordsCreate,
                self::RecordsUpdate,
                self::RecordsAttachmentsManage,
                self::SyncView,
                self::SyncRun,
                self::NotificationsView,
                self::SupportView,
                self::SupportCreate,
                self::DiagnosticsView,
            ],
        };
    }

    public function category(): string
    {
        return str($this->value)->before('.')->toString();
    }
}
