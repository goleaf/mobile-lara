<?php

namespace App\Policies;

use App\Models\MobileFeatureFlag;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class MobileFeatureFlagPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, MobileFeatureFlag $mobileFeatureFlag): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, MobileFeatureFlag $mobileFeatureFlag): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, MobileFeatureFlag $mobileFeatureFlag): bool
    {
        return false;
    }

    public function restore(User $user, MobileFeatureFlag $mobileFeatureFlag): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, MobileFeatureFlag $mobileFeatureFlag): bool
    {
        return false;
    }
}
