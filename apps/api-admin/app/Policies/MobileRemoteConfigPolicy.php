<?php

namespace App\Policies;

use App\Models\MobileRemoteConfig;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class MobileRemoteConfigPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, MobileRemoteConfig $mobileRemoteConfig): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, MobileRemoteConfig $mobileRemoteConfig): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, MobileRemoteConfig $mobileRemoteConfig): bool
    {
        return false;
    }

    public function restore(User $user, MobileRemoteConfig $mobileRemoteConfig): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, MobileRemoteConfig $mobileRemoteConfig): bool
    {
        return false;
    }
}
