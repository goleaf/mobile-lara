<?php

namespace App\Models;

use Database\Factories\RecordAttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'tenant_record_id',
    'uploaded_by_user_id',
    'public_id',
    'local_id',
    'file_name',
    'mime_type',
    'size_bytes',
    'status',
    'metadata',
])]
class RecordAttachment extends Model
{
    /** @use HasFactory<RecordAttachmentFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        self::creating(function (RecordAttachment $attachment): void {
            if (! is_string($attachment->public_id) || trim($attachment->public_id) === '') {
                $attachment->public_id = (string) Str::uuid();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'size_bytes' => 'integer',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(TenantRecord::class, 'tenant_record_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
