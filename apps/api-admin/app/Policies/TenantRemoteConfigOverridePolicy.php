<?php

namespace App\Policies;

use App\Models\TenantRemoteConfigOverride;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class TenantRemoteConfigOverridePolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, TenantRemoteConfigOverride $tenantRemoteConfigOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, TenantRemoteConfigOverride $tenantRemoteConfigOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, TenantRemoteConfigOverride $tenantRemoteConfigOverride): bool
    {
        return false;
    }

    public function restore(User $user, TenantRemoteConfigOverride $tenantRemoteConfigOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, TenantRemoteConfigOverride $tenantRemoteConfigOverride): bool
    {
        return false;
    }
}
