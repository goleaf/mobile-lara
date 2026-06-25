<?php

namespace App\Policies;

use App\Models\TenantFeatureOverride;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class TenantFeatureOverridePolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, TenantFeatureOverride $tenantFeatureOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, TenantFeatureOverride $tenantFeatureOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, TenantFeatureOverride $tenantFeatureOverride): bool
    {
        return false;
    }

    public function restore(User $user, TenantFeatureOverride $tenantFeatureOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, TenantFeatureOverride $tenantFeatureOverride): bool
    {
        return false;
    }
}
