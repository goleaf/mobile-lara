<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AuthorizesPlatformAdmins
{
    private function platformAdmin(User $user): bool
    {
        return $user->is_platform_admin === true;
    }
}
