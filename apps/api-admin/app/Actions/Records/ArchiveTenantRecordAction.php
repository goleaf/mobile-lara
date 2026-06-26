<?php

namespace App\Actions\Records;

use App\Models\RecordActivity;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class ArchiveTenantRecordAction
{
    public function __construct(private readonly MobileAuditLogger $audit) {}

    public function archive(TenantRecord $record, Tenant $tenant, User $user, Request $request): TenantRecord
    {
        $record->forceFill([
            'archived_at' => Carbon::now(),
            'updated_by_user_id' => $user->id,
            'sync_version' => (string) Str::uuid(),
        ])->save();

        RecordActivity::query()->create([
            'tenant_id' => $tenant->id,
            'tenant_record_id' => $record->id,
            'actor_user_id' => $user->id,
            'action' => 'record.archived',
            'description' => 'Record archived.',
            'metadata' => ['source' => 'mobile_api'],
        ]);

        $this->audit->record('mobile_record_archived', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
            'tenant_public_id' => $tenant->public_id,
            'record_public_id' => $record->public_id,
        ]);

        return TenantRecord::query()
            ->forTenant($tenant)
            ->forMobileDetail()
            ->where('id', $record->id)
            ->firstOrFail();
    }
}
