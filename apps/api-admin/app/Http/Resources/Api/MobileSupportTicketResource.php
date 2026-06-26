<?php

namespace App\Http\Resources\Api;

use App\Models\MobileSupportMessage;
use App\Models\MobileSupportTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class MobileSupportTicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MobileSupportTicket $ticket */
        $ticket = $this->resource;

        return [
            'id' => $ticket->public_id,
            'tenant_id' => $ticket->tenant?->public_id,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'category' => $ticket->category,
            'source' => $ticket->source,
            'assignment' => [
                'assigned' => $ticket->assignedAgent !== null,
                'agent' => $ticket->assignedAgent === null ? null : [
                    'id' => $ticket->assignedAgent->id,
                    'name' => $ticket->assignedAgent->name,
                ],
            ],
            'support_context' => $ticket->support_context ?? [],
            'messages_count' => (int) ($ticket->messages_count ?? 0),
            'messages' => $ticket->relationLoaded('messages')
                ? $ticket->messages->map(fn (MobileSupportMessage $message): array => MobileSupportMessageResource::make($message)->resolve($request))->values()->all()
                : [],
            'last_message_at' => $ticket->last_message_at?->toIso8601String(),
            'closed_at' => $ticket->closed_at?->toIso8601String(),
            'created_at' => $ticket->created_at?->toIso8601String(),
            'updated_at' => $ticket->updated_at?->toIso8601String(),
            'allowed_actions' => $this->allowedActions($request, $ticket),
        ];
    }

    /**
     * @return array{view: bool, add_message: bool, attach_metadata: bool, attach_diagnostics: bool}
     */
    private function allowedActions(Request $request, MobileSupportTicket $ticket): array
    {
        $permissions = $request->attributes->get('mobile_tenant_permissions');
        $permissions = is_array($permissions) ? $permissions : [];
        $canCreate = Arr::get($permissions, 'abilities.support.create') === true;

        return [
            'view' => Arr::get($permissions, 'abilities.support.view') === true,
            'add_message' => $canCreate && $ticket->acceptsUserMessages(),
            'attach_metadata' => $canCreate && $ticket->acceptsUserMessages(),
            'attach_diagnostics' => Arr::get($permissions, 'abilities.diagnostics.view') === true && $ticket->acceptsUserMessages(),
        ];
    }
}
