<?php

namespace App\Policies;

use App\Models\MobileSupportTicket;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class MobileSupportTicketPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, MobileSupportTicket $mobileSupportTicket): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MobileSupportTicket $mobileSupportTicket): bool
    {
        return $this->platformAdmin($user);
    }

    public function delete(User $user, MobileSupportTicket $mobileSupportTicket): bool
    {
        return false;
    }

    public function restore(User $user, MobileSupportTicket $mobileSupportTicket): bool
    {
        return false;
    }

    public function forceDelete(User $user, MobileSupportTicket $mobileSupportTicket): bool
    {
        return false;
    }
}
