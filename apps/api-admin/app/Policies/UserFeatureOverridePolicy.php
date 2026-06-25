<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserFeatureOverride;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class UserFeatureOverridePolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, UserFeatureOverride $userFeatureOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function update(User $user, UserFeatureOverride $userFeatureOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, UserFeatureOverride $userFeatureOverride): bool
    {
        return false;
    }

    public function restore(User $user, UserFeatureOverride $userFeatureOverride): bool
    {
        return $this->platformAdmin($user);
    }

    public function forceDelete(User $user, UserFeatureOverride $userFeatureOverride): bool
    {
        return false;
    }
}
