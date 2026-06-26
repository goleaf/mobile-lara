<?php

namespace App\Actions\Notifications;

use App\Models\MobileDeviceSession;
use App\Models\MobilePushToken;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class RegisterMobilePushTokenAction
{
    public function __construct(private readonly MobileAuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, Tenant $tenant, User $user, Request $request): MobilePushToken
    {
        $session = $request->attributes->get('mobile_device_session');
        $session = $session instanceof MobileDeviceSession ? $session : null;
        $rawToken = trim((string) $data['token']);
        $tokenHash = hash('sha256', $rawToken);
        $pushToken = MobilePushToken::query()
            ->where('tenant_id', $tenant->id)
            ->where('token_hash', $tokenHash)
            ->first();

        $attributes = [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'mobile_device_session_id' => $session?->id,
            'token_hash' => $tokenHash,
            'token_preview' => $this->preview($rawToken),
            'provider' => (string) $data['provider'],
            'platform' => (string) $data['platform'],
            'device_id' => $this->nullableString($data['device_id'] ?? null),
            'app_version' => $this->nullableString($data['app_version'] ?? null) ?? $this->nullableString($request->header('X-Mobile-App-Version')),
            'metadata' => $this->metadata($data),
            'last_registered_at' => now(),
            'revoked_at' => null,
        ];

        if ($pushToken instanceof MobilePushToken) {
            $pushToken->fill($attributes)->save();
        } else {
            $pushToken = MobilePushToken::query()->create($attributes);
        }

        $this->audit->record('mobile_push_token_registered', $request, $user, $session, metadata: [
            'tenant_public_id' => $tenant->public_id,
            'push_token_id' => $pushToken->public_id,
            'provider' => $pushToken->provider,
            'platform' => $pushToken->platform,
            'token_preview' => $pushToken->token_preview,
        ]);

        return $pushToken;
    }

    private function preview(string $token): string
    {
        $start = Str::of($token)->substr(0, 6)->toString();
        $end = Str::of($token)->substr(-4)->toString();

        return "{$start}...{$end}";
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function metadata(array $data): array
    {
        $metadata = Arr::get($data, 'metadata');

        return is_array($metadata) ? $metadata : [];
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
