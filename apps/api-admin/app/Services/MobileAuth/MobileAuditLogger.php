<?php

namespace App\Services\MobileAuth;

use App\Models\MobileDeviceSession;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use Illuminate\Http\Request;

final class MobileAuditLogger
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        string $event,
        Request $request,
        ?User $user = null,
        ?MobileDeviceSession $session = null,
        string $severity = 'info',
        array $metadata = [],
    ): SecurityAuditEvent {
        return SecurityAuditEvent::query()->create([
            'user_id' => $user?->id,
            'mobile_device_session_id' => $session?->id,
            'event' => $event,
            'severity' => $severity,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
