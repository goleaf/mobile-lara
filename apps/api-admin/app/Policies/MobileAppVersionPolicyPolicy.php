<?php

namespace App\Policies;

use App\Models\MobileAppVersionPolicy;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class MobileAppVersionPolicyPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, MobileAppVersionPolicy $mobileAppVersionPolicy): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, MobileAppVersionPolicy $mobileAppVersionPolicy): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, MobileAppVersionPolicy $mobileAppVersionPolicy): bool
    {
        return false;
    }

    public function restore(User $user, MobileAppVersionPolicy $mobileAppVersionPolicy): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, MobileAppVersionPolicy $mobileAppVersionPolicy): bool
    {
        return false;
    }
}
