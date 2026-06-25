<?php

namespace App\Actions\MobileAuth;

use Illuminate\Contracts\Auth\Authenticatable;

final class RequestAccountDeletionAction
{
    /**
     * @return array{
     *     status: string,
     *     message: string,
     *     server_endpoint: string,
     *     confirmed_by: string,
     *     user_id: string|null
     * }
     */
    public function handle(?Authenticatable $user, string $confirmedBy): array
    {
        return [
            'status' => 'placeholder',
            'message' => 'Account deletion API placeholder reached. No account has been deleted yet.',
            'server_endpoint' => 'DELETE /api/mobile/account',
            'confirmed_by' => $confirmedBy,
            'user_id' => is_null($user) ? null : (string) $user->getAuthIdentifier(),
        ];
    }
}
