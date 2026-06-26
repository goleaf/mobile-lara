<?php

namespace App\Actions\Support;

use App\Models\MobileDiagnosticReport;
use App\Models\MobileSupportMessage;
use App\Models\MobileSupportTicket;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class SaveMobileSupportTicketAction
{
    public function __construct(private readonly MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, Tenant $tenant, User $user, Request $request): MobileSupportTicket
    {
        return DB::transaction(function () use ($data, $tenant, $user, $request): MobileSupportTicket {
            $ticket = MobileSupportTicket::query()->create([
                'tenant_id' => $tenant->id,
                'requester_user_id' => $user->id,
                'subject' => (string) $data['subject'],
                'status' => MobileSupportTicket::STATUS_OPEN,
                'priority' => (string) ($data['priority'] ?? MobileSupportTicket::PRIORITY_NORMAL),
                'category' => $this->nullableString($data['category'] ?? null),
                'source' => 'mobile_api',
                'support_context' => is_array($data['support_context'] ?? null) ? $data['support_context'] : [],
                'last_message_at' => now(),
            ]);

            $message = $this->message($ticket, $tenant, $user, $data);
            $this->audit->record('mobile_support_ticket_created', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
                'tenant_public_id' => $tenant->public_id,
                'support_ticket_id' => $ticket->public_id,
                'support_message_id' => $message->public_id,
            ]);

            return $this->freshTicket($ticket, $tenant, $user);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function addMessage(MobileSupportTicket $ticket, array $data, Tenant $tenant, User $user, Request $request): MobileSupportTicket
    {
        return DB::transaction(function () use ($ticket, $data, $tenant, $user, $request): MobileSupportTicket {
            $message = $this->message($ticket, $tenant, $user, $data);
            $ticket->forceFill(['last_message_at' => now()])->save();
            $this->audit->record('mobile_support_message_created', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
                'tenant_public_id' => $tenant->public_id,
                'support_ticket_id' => $ticket->public_id,
                'support_message_id' => $message->public_id,
            ]);

            return $this->freshTicket($ticket, $tenant, $user);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function message(MobileSupportTicket $ticket, Tenant $tenant, User $user, array $data): MobileSupportMessage
    {
        return MobileSupportMessage::query()->create([
            'tenant_id' => $tenant->id,
            'mobile_support_ticket_id' => $ticket->id,
            'author_user_id' => $user->id,
            'body' => (string) $data['body'],
            'direction' => MobileSupportMessage::DIRECTION_USER,
            'visibility' => MobileSupportMessage::VISIBILITY_REQUESTER,
            'attachments' => $this->attachments($data),
            'diagnostic_report_id' => $this->diagnosticReportId($tenant, $user, $data['diagnostic_report_id'] ?? null),
            'metadata' => [
                'source' => 'mobile_api',
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    private function attachments(array $data): array
    {
        return collect(is_array($data['attachments'] ?? null) ? $data['attachments'] : [])
            ->filter(static fn (mixed $attachment): bool => is_array($attachment))
            ->map(function (array $attachment): array {
                return [
                    'local_id' => $this->nullableString($attachment['local_id'] ?? null),
                    'file_name' => (string) $attachment['file_name'],
                    'mime_type' => $this->nullableString($attachment['mime_type'] ?? null),
                    'size_bytes' => (int) ($attachment['size_bytes'] ?? 0),
                    'status' => 'metadata_only',
                    'metadata' => is_array($attachment['metadata'] ?? null) ? $attachment['metadata'] : [],
                ];
            })
            ->values()
            ->all();
    }

    private function diagnosticReportId(Tenant $tenant, User $user, mixed $value): ?string
    {
        $publicId = $this->nullableString($value);

        if ($publicId === null) {
            return null;
        }

        return MobileDiagnosticReport::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->where('public_id', $publicId)
            ->value('public_id');
    }

    private function freshTicket(MobileSupportTicket $ticket, Tenant $tenant, User $user): MobileSupportTicket
    {
        return MobileSupportTicket::query()
            ->forMobileDetail($tenant, $user)
            ->where('id', $ticket->id)
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
