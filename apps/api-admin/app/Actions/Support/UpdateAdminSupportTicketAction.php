<?php

namespace App\Actions\Support;

use App\Models\MobileSupportTicket;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateAdminSupportTicketAction
{
    /**
     * @param  array{status: string, priority: string, assigned_user_id: int|null}  $data
     */
    public function handle(MobileSupportTicket $ticket, User $admin, array $data): MobileSupportTicket
    {
        return DB::transaction(function () use ($ticket, $admin, $data): MobileSupportTicket {
            $before = [
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'assigned_user_id' => $ticket->assigned_user_id,
                'closed_at' => $ticket->closed_at?->toIso8601String(),
            ];

            $closedAt = in_array($data['status'], [
                MobileSupportTicket::STATUS_RESOLVED,
                MobileSupportTicket::STATUS_CLOSED,
            ], true) ? ($ticket->closed_at ?? now()) : null;

            $ticket->forceFill([
                'status' => $data['status'],
                'priority' => $data['priority'],
                'assigned_user_id' => $data['assigned_user_id'],
                'closed_at' => $closedAt,
            ])->save();

            $ticket->loadMissing('tenant');

            SecurityAuditEvent::query()->create([
                'user_id' => $admin->id,
                'event' => 'admin_support_ticket_updated',
                'severity' => 'info',
                'metadata' => [
                    'tenant_public_id' => $ticket->tenant?->public_id,
                    'support_ticket_id' => $ticket->public_id,
                    'before' => $before,
                    'after' => [
                        'status' => $ticket->status,
                        'priority' => $ticket->priority,
                        'assigned_user_id' => $ticket->assigned_user_id,
                        'closed_at' => $ticket->closed_at?->toIso8601String(),
                    ],
                ],
            ]);

            return MobileSupportTicket::query()
                ->forAdminDetail()
                ->findOrFail($ticket->id);
        });
    }
}
