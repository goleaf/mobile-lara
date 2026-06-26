<?php

namespace App\Http\Requests\Api\V1\Mobile;

final class SyncAcknowledgeRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'acknowledgements' => ['required', 'array', 'min:1', 'max:100'],
            'acknowledgements.*' => ['required', 'array:sync_event_id,client_intent_id'],
            'acknowledgements.*.sync_event_id' => ['required', 'string', 'max:120'],
            'acknowledgements.*.client_intent_id' => ['nullable', 'string', 'max:120'],
            'last_cursor' => ['nullable', 'date'],
        ];
    }
}
