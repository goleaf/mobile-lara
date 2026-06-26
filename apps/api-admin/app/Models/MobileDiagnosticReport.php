<?php

namespace App\Models;

use Database\Factories\MobileDiagnosticReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
}
