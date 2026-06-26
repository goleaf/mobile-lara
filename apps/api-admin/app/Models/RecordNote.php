<?php

namespace App\Models;

use Database\Factories\RecordNoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'tenant_record_id',
    'author_user_id',
    'public_id',
    'body',
    'visibility',
    'metadata',
])]
class RecordNote extends Model
{
    /** @use HasFactory<RecordNoteFactory> */
    use HasFactory;

    use SoftDeletes;

    protected static function booted(): void
    {
        self::creating(function (RecordNote $note): void {
            if (! is_string($note->public_id) || trim($note->public_id) === '') {
                $note->public_id = (string) Str::uuid();
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
            'deleted_at' => 'datetime',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
