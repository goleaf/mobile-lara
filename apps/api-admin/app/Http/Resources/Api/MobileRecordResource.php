<?php

namespace App\Http\Resources\Api;

use App\Models\RecordActivity;
use App\Models\RecordAttachment;
use App\Models\RecordCategory;
use App\Models\RecordNote;
use App\Models\RecordTag;
use App\Models\TenantRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class MobileRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var TenantRecord $record */
        $record = $this->resource;

        return [
            'id' => $record->public_id,
            'tenant_id' => $record->tenant?->public_id,
            'title' => $record->title,
            'description' => $record->description,
            'status' => $record->status,
            'priority' => $record->priority,
            'category' => $this->categoryPayload($record->category),
            'tags' => $record->relationLoaded('tags')
                ? $record->tags->map(fn (RecordTag $tag): array => $this->tagPayload($tag))->values()->all()
                : [],
            'notes_count' => (int) ($record->notes_count ?? 0),
            'attachments_count' => (int) ($record->attachments_count ?? 0),
            'activities_count' => (int) ($record->activities_count ?? 0),
            'notes' => $record->relationLoaded('notes')
                ? $record->notes->map(fn (RecordNote $note): array => $this->notePayload($note))->values()->all()
                : [],
            'attachments' => $record->relationLoaded('attachments')
                ? $record->attachments->map(fn (RecordAttachment $attachment): array => $this->attachmentPayload($attachment))->values()->all()
                : [],
            'activity' => $record->relationLoaded('activities')
                ? $record->activities->map(fn (RecordActivity $activity): array => $this->activityPayload($activity))->values()->all()
                : [],
            'archived' => $record->isArchived(),
            'archived_at' => $record->archived_at?->toIso8601String(),
            'updated_at' => $record->updated_at?->toIso8601String(),
            'created_at' => $record->created_at?->toIso8601String(),
            'sync_version' => $record->sync_version,
            'actions' => $this->actions($request, $record),
        ];
    }

    private function categoryPayload(?RecordCategory $category): ?array
    {
        if (! $category instanceof RecordCategory) {
            return null;
        }

        return [
            'id' => $category->public_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'color' => $category->color,
        ];
    }

    private function tagPayload(RecordTag $tag): array
    {
        return [
            'id' => $tag->public_id,
            'name' => $tag->name,
            'slug' => $tag->slug,
            'color' => $tag->color,
        ];
    }

    private function notePayload(RecordNote $note): array
    {
        return [
            'id' => $note->public_id,
            'body' => $note->body,
            'visibility' => $note->visibility,
            'author' => $note->author === null ? null : [
                'id' => $note->author->id,
                'name' => $note->author->name,
            ],
            'created_at' => $note->created_at?->toIso8601String(),
        ];
    }

    private function attachmentPayload(RecordAttachment $attachment): array
    {
        return [
            'id' => $attachment->public_id,
            'local_id' => $attachment->local_id,
            'file_name' => $attachment->file_name,
            'mime_type' => $attachment->mime_type,
            'size_bytes' => $attachment->size_bytes,
            'status' => $attachment->status,
            'metadata' => $attachment->metadata ?? [],
            'created_at' => $attachment->created_at?->toIso8601String(),
        ];
    }

    private function activityPayload(RecordActivity $activity): array
    {
        return [
            'action' => $activity->action,
            'description' => $activity->description,
            'metadata' => $activity->metadata ?? [],
            'actor' => $activity->actor === null ? null : [
                'id' => $activity->actor->id,
                'name' => $activity->actor->name,
            ],
            'created_at' => $activity->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array{view: bool, update: bool, archive: bool, restore: bool, delete: bool, attachments_manage: bool}
     */
    private function actions(Request $request, TenantRecord $record): array
    {
        $permissions = $request->attributes->get('mobile_record_permissions');
        $permissions = is_array($permissions) ? $permissions : [];

        return [
            'view' => Arr::get($permissions, 'abilities.records.view') === true,
            'update' => Arr::get($permissions, 'abilities.records.update') === true && ! $record->isArchived(),
            'archive' => Arr::get($permissions, 'abilities.records.archive') === true && ! $record->isArchived(),
            'restore' => Arr::get($permissions, 'abilities.records.archive') === true && $record->isArchived(),
            'delete' => Arr::get($permissions, 'abilities.records.delete') === true,
            'attachments_manage' => Arr::get($permissions, 'abilities.records.attachments.manage') === true && ! $record->isArchived(),
        ];
    }
}
