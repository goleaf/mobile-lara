<?php

namespace App\Models;

use Database\Factories\MobileSupportMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'mobile_support_ticket_id',
    'author_user_id',
    'public_id',
    'body',
    'direction',
    'visibility',
    'attachments',
    'diagnostic_report_id',
    'metadata',
])]
final class MobileSupportMessage extends Model
{
    /** @use HasFactory<MobileSupportMessageFactory> */
    use HasFactory;

    public const DIRECTION_USER = 'user';

    public const DIRECTION_SUPPORT = 'support';

    public const VISIBILITY_REQUESTER = 'requester';

    public const VISIBILITY_INTERNAL = 'internal';

    protected static function booted(): void
    {
        self::creating(function (MobileSupportMessage $message): void {
            if (! is_string($message->public_id) || trim($message->public_id) === '') {
                $message->public_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'metadata' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(MobileSupportTicket::class, 'mobile_support_ticket_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
