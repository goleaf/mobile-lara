<?php

namespace App\Policies;

use App\Models\MobileSyncEvent;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class MobileSyncEventPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, MobileSyncEvent $mobileSyncEvent): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MobileSyncEvent $mobileSyncEvent): bool
    {
        return false;
    }

    public function delete(User $user, MobileSyncEvent $mobileSyncEvent): bool
    {
        return false;
    }

    public function restore(User $user, MobileSyncEvent $mobileSyncEvent): bool
    {
        return false;
    }

    public function forceDelete(User $user, MobileSyncEvent $mobileSyncEvent): bool
    {
        return false;
    }
}
