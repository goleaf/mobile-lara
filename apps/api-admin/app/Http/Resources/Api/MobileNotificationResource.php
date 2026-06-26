<?php

namespace App\Http\Resources\Api;

use App\Models\MobileNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileNotificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MobileNotification $notification */
        $notification = $this->resource;
        $isMutable = $notification->isUserScoped();

        return [
            'id' => $notification->public_id,
            'tenant_id' => $notification->tenant?->public_id,
            'type' => $notification->type,
            'title' => $notification->title,
            'body' => $notification->body,
            'data' => $notification->data ?? [],
            'deep_link' => $notification->deep_link,
            'source' => $notification->source,
            'delivery_status' => $notification->delivery_status,
            'read_at' => $notification->read_at?->toIso8601String(),
            'opened_at' => $notification->opened_at?->toIso8601String(),
            'sent_at' => $notification->sent_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
            'actions' => [
                'mark_read' => $isMutable && $notification->read_at === null,
                'open' => $notification->deep_link !== null,
                'delete' => $isMutable,
            ],
        ];
    }
}
