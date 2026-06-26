<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class TenantPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return false;
    }

    public function restore(User $user, Tenant $tenant): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, Tenant $tenant): bool
    {
        return false;
    }
}
