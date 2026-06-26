<?php

namespace App\Http\Resources\Api;

use App\Models\MobileSupportMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileSupportMessageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MobileSupportMessage $message */
        $message = $this->resource;

        return [
            'id' => $message->public_id,
            'body' => $message->body,
            'direction' => $message->direction,
            'visibility' => $message->visibility,
            'attachments' => $message->attachments ?? [],
            'diagnostic_report_id' => $message->diagnostic_report_id,
            'author' => $message->author === null ? null : [
                'id' => $message->author->id,
                'name' => $message->author->name,
            ],
            'created_at' => $message->created_at?->toIso8601String(),
            'updated_at' => $message->updated_at?->toIso8601String(),
        ];
    }
}
