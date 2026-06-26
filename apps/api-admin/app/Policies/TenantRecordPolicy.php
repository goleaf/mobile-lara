<?php

namespace App\Policies;

use App\Models\TenantRecord;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class TenantRecordPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, TenantRecord $tenantRecord): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, TenantRecord $tenantRecord): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, TenantRecord $tenantRecord): bool
    {
        return false;
    }

    public function restore(User $user, TenantRecord $tenantRecord): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, TenantRecord $tenantRecord): bool
    {
        return false;
    }
}
