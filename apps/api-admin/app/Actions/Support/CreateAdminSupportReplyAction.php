<?php

namespace App\Actions\Support;

use App\Models\MobileSupportMessage;
use App\Models\MobileSupportTicket;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateAdminSupportReplyAction
{
    public function handle(MobileSupportTicket $ticket, User $admin, string $body): MobileSupportTicket
    {
        return DB::transaction(function () use ($ticket, $admin, $body): MobileSupportTicket {
            $ticket->loadMissing('tenant');

            $message = MobileSupportMessage::query()->create([
                'tenant_id' => $ticket->tenant_id,
                'mobile_support_ticket_id' => $ticket->id,
                'author_user_id' => $admin->id,
                'body' => trim($body),
                'direction' => MobileSupportMessage::DIRECTION_SUPPORT,
                'visibility' => MobileSupportMessage::VISIBILITY_REQUESTER,
                'attachments' => [],
                'metadata' => [
                    'source' => 'admin_support_queue',
                ],
            ]);

            $ticket->forceFill([
                'assigned_user_id' => $ticket->assigned_user_id ?? $admin->id,
                'last_message_at' => now(),
            ])->save();

            SecurityAuditEvent::query()->create([
                'user_id' => $admin->id,
                'event' => 'admin_support_reply_created',
                'severity' => 'info',
                'metadata' => [
                    'tenant_public_id' => $ticket->tenant?->public_id,
                    'support_ticket_id' => $ticket->public_id,
                    'support_message_id' => $message->public_id,
                    'visibility' => $message->visibility,
                ],
            ]);

            return MobileSupportTicket::query()
                ->forAdminDetail()
                ->findOrFail($ticket->id);
        });
    }
}
