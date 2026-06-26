<?php

namespace App\Http\Requests\Api\V1\Mobile;

use Illuminate\Validation\Rule;

final class SyncPushRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'client_batch_id' => ['nullable', 'string', 'max:120'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*' => ['required', 'array:client_intent_id,idempotency_key,collection,action,record_id,base_sync_version,payload,queued_at'],
            'items.*.client_intent_id' => ['required', 'string', 'max:120'],
            'items.*.idempotency_key' => ['required', 'string', 'max:160'],
            'items.*.collection' => ['required', 'string', Rule::in(['records'])],
            'items.*.action' => ['required', 'string', Rule::in(['create', 'update', 'archive', 'restore', 'delete'])],
            'items.*.record_id' => ['nullable', 'string', 'max:120'],
            'items.*.base_sync_version' => ['nullable', 'string', 'max:120'],
            'items.*.payload' => ['nullable', 'array'],
            'items.*.payload.title' => ['sometimes', 'required', 'string', 'max:160'],
            'items.*.payload.description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'items.*.payload.status' => ['sometimes', 'string', Rule::in(['draft', 'active', 'review', 'done'])],
            'items.*.payload.priority' => ['sometimes', 'string', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'items.*.payload.metadata' => ['sometimes', 'nullable', 'array'],
            'items.*.payload.category_id' => ['sometimes', 'nullable', 'string', 'max:120'],
            'items.*.payload.category' => ['sometimes', 'nullable', 'array:name,color,description'],
            'items.*.payload.category.name' => ['required_with:items.*.payload.category', 'string', 'max:80'],
            'items.*.payload.category.color' => ['nullable', 'string', 'max:32'],
            'items.*.payload.category.description' => ['nullable', 'string', 'max:500'],
            'items.*.payload.tags' => ['sometimes', 'array', 'max:10'],
            'items.*.payload.tags.*' => ['string', 'max:60'],
            'items.*.payload.note' => ['sometimes', 'nullable', 'string', 'max:3000'],
            'items.*.payload.attachments' => ['sometimes', 'array', 'max:5'],
            'items.*.payload.attachments.*' => ['array:local_id,file_name,mime_type,size_bytes,metadata'],
            'items.*.payload.attachments.*.local_id' => ['nullable', 'string', 'max:120'],
            'items.*.payload.attachments.*.file_name' => ['required_with:items.*.payload.attachments', 'string', 'max:180'],
            'items.*.payload.attachments.*.mime_type' => ['nullable', 'string', 'max:120'],
            'items.*.payload.attachments.*.size_bytes' => ['nullable', 'integer', 'min:0', 'max:52428800'],
            'items.*.payload.attachments.*.metadata' => ['nullable', 'array'],
            'items.*.queued_at' => ['nullable', 'date'],
        ];
    }
}
