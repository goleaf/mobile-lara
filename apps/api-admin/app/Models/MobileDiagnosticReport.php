<?php

namespace App\Models;

use Database\Factories\MobileDiagnosticReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'user_id',
    'mobile_device_session_id',
    'public_id',
    'app_version',
    'api_base_url',
    'support_ticket_id',
    'redactions_applied',
    'snapshot',
    'failed_sync_actions_count',
    'generated_at',
    'received_at',
])]
final class MobileDiagnosticReport extends Model
{
    /** @use HasFactory<MobileDiagnosticReportFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    public const SELECT_COLUMNS = [
        'id',
        'tenant_id',
        'user_id',
        'mobile_device_session_id',
        'public_id',
        'app_version',
        'api_base_url',
        'support_ticket_id',
        'redactions_applied',
        'snapshot',
        'failed_sync_actions_count',
        'generated_at',
        'received_at',
        'created_at',
        'updated_at',
    ];

    protected static function booted(): void
    {
        self::creating(function (MobileDiagnosticReport $report): void {
            if (! is_string($report->public_id) || trim($report->public_id) === '') {
                $report->public_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'redactions_applied' => 'array',
            'snapshot' => 'array',
            'failed_sync_actions_count' => 'integer',
            'generated_at' => 'immutable_datetime',
            'received_at' => 'immutable_datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deviceSession(): BelongsTo
    {
        return $this->belongsTo(MobileDeviceSession::class, 'mobile_device_session_id');
    }

    /**
     * @param  Builder<MobileDiagnosticReport>  $query
     * @return Builder<MobileDiagnosticReport>
     */
    public function scopeForAdminIndex(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug,status,subscription_state',
                'user:id,name',
                'deviceSession:id,user_id,device_name,platform,app_version,status,last_seen_at',
            ])
            ->orderByDesc('received_at')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<MobileDiagnosticReport>  $query
     * @return Builder<MobileDiagnosticReport>
     */
    public function scopeForAdminDetail(Builder $query): Builder
    {
        return $query
            ->select(self::SELECT_COLUMNS)
            ->with([
                'tenant:id,public_id,name,slug,status,subscription_state',
                'user:id,name',
                'deviceSession:id,user_id,device_name,platform,app_version,status,last_seen_at',
            ]);
    }

    /**
     * @param  Builder<MobileDiagnosticReport>  $query
     * @return Builder<MobileDiagnosticReport>
     */
    public function scopeMatchingAdminSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            $query
                ->where('public_id', 'like', '%'.$search.'%')
                ->orWhere('app_version', 'like', '%'.$search.'%')
                ->orWhere('api_base_url', 'like', '%'.$search.'%')
                ->orWhere('support_ticket_id', 'like', '%'.$search.'%')
                ->orWhereHas('tenant', function (Builder $query) use ($search): void {
                    $query
                        ->where('public_id', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%');
                })
                ->orWhereHas('user', function (Builder $query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('deviceSession', function (Builder $query) use ($search): void {
                    $query
                        ->where('device_name', 'like', '%'.$search.'%')
                        ->orWhere('platform', 'like', '%'.$search.'%')
                        ->orWhere('app_version', 'like', '%'.$search.'%');
                });
        });
    }
}
