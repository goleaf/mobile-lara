<?php

namespace App\Actions\Records;

use App\Models\RecordActivity;
use App\Models\RecordAttachment;
use App\Models\RecordCategory;
use App\Models\RecordNote;
use App\Models\RecordTag;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class SaveTenantRecordAction
{
    public function __construct(private readonly MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, Tenant $tenant, User $user, Request $request): TenantRecord
    {
        return DB::transaction(function () use ($data, $tenant, $user, $request): TenantRecord {
            $record = TenantRecord::query()->create([
                'tenant_id' => $tenant->id,
                'created_by_user_id' => $user->id,
                'updated_by_user_id' => $user->id,
                'record_category_id' => $this->categoryId($data, $tenant),
                'title' => (string) $data['title'],
                'description' => $this->nullableString($data['description'] ?? null),
                'status' => (string) ($data['status'] ?? TenantRecord::STATUS_ACTIVE),
                'priority' => (string) ($data['priority'] ?? TenantRecord::PRIORITY_NORMAL),
                'metadata' => is_array($data['metadata'] ?? null) ? $data['metadata'] : [],
                'sync_version' => (string) Str::uuid(),
            ]);

            $this->syncTags($record, $data, $tenant);
            $this->createNote($record, $data, $user);
            $this->createAttachments($record, $data, $user);
            $this->recordActivity($record, $user, 'record.created', 'Record created.', ['source' => 'mobile_api']);
            $this->audit->record('mobile_record_created', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
                'tenant_public_id' => $tenant->public_id,
                'record_public_id' => $record->public_id,
            ]);

            return $this->freshRecord($record);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TenantRecord $record, array $data, Tenant $tenant, User $user, Request $request): TenantRecord
    {
        return DB::transaction(function () use ($record, $data, $tenant, $user, $request): TenantRecord {
            $attributes = [
                'updated_by_user_id' => $user->id,
                'sync_version' => (string) Str::uuid(),
            ];

            foreach (['title', 'description', 'status', 'priority', 'metadata'] as $field) {
                if (array_key_exists($field, $data)) {
                    $attributes[$field] = $data[$field];
                }
            }

            if (array_key_exists('category_id', $data) || array_key_exists('category', $data)) {
                $attributes['record_category_id'] = $this->categoryId($data, $tenant);
            }

            $record->fill($attributes)->save();
            $this->syncTags($record, $data, $tenant);
            $this->createNote($record, $data, $user);
            $this->createAttachments($record, $data, $user);
            $this->recordActivity($record, $user, 'record.updated', 'Record updated.', ['fields' => array_keys($data)]);
            $this->audit->record('mobile_record_updated', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
                'tenant_public_id' => $tenant->public_id,
                'record_public_id' => $record->public_id,
                'fields' => array_keys($data),
            ]);

            return $this->freshRecord($record);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function categoryId(array $data, Tenant $tenant): ?int
    {
        $categoryId = $this->nullableString($data['category_id'] ?? null);

        if ($categoryId !== null) {
            return RecordCategory::query()
                ->forTenant($tenant)
                ->where('public_id', $categoryId)
                ->value('id');
        }

        $category = is_array($data['category'] ?? null) ? $data['category'] : null;
        $name = $this->nullableString($category['name'] ?? null);

        if ($name === null) {
            return null;
        }

        return RecordCategory::query()->firstOrCreate([
            'tenant_id' => $tenant->id,
            'slug' => Str::slug($name),
        ], [
            'name' => $name,
            'color' => $this->nullableString($category['color'] ?? null),
            'description' => $this->nullableString($category['description'] ?? null),
            'is_active' => true,
        ])->id;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncTags(TenantRecord $record, array $data, Tenant $tenant): void
    {
        if (! array_key_exists('tags', $data)) {
            return;
        }

        $tagIds = collect(is_array($data['tags']) ? $data['tags'] : [])
            ->map(fn (mixed $tag): ?string => $this->nullableString($tag))
            ->filter()
            ->unique()
            ->map(function (string $name) use ($tenant): int {
                return RecordTag::query()->firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'slug' => Str::slug($name),
                ], [
                    'name' => Str::title($name),
                ])->id;
            })
            ->values()
            ->all();

        $record->tags()->sync($tagIds);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createNote(TenantRecord $record, array $data, User $user): void
    {
        $body = $this->nullableString($data['note'] ?? null);

        if ($body === null) {
            return;
        }

        RecordNote::query()->create([
            'tenant_id' => $record->tenant_id,
            'tenant_record_id' => $record->id,
            'author_user_id' => $user->id,
            'body' => $body,
            'visibility' => 'tenant',
            'metadata' => ['source' => 'mobile_api'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createAttachments(TenantRecord $record, array $data, User $user): void
    {
        foreach (is_array($data['attachments'] ?? null) ? $data['attachments'] : [] as $attachment) {
            if (! is_array($attachment)) {
                continue;
            }

            RecordAttachment::query()->create([
                'tenant_id' => $record->tenant_id,
                'tenant_record_id' => $record->id,
                'uploaded_by_user_id' => $user->id,
                'local_id' => $this->nullableString($attachment['local_id'] ?? null),
                'file_name' => (string) $attachment['file_name'],
                'mime_type' => $this->nullableString($attachment['mime_type'] ?? null),
                'size_bytes' => (int) ($attachment['size_bytes'] ?? 0),
                'status' => 'metadata_only',
                'metadata' => is_array($attachment['metadata'] ?? null) ? $attachment['metadata'] : [],
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    private function recordActivity(TenantRecord $record, User $user, string $action, string $description, array $metadata): void
    {
        RecordActivity::query()->create([
            'tenant_id' => $record->tenant_id,
            'tenant_record_id' => $record->id,
            'actor_user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    private function freshRecord(TenantRecord $record): TenantRecord
    {
        return TenantRecord::query()
            ->forTenant($record->tenant_id)
            ->forMobileDetail()
            ->where('id', $record->id)
            ->firstOrFail();
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
