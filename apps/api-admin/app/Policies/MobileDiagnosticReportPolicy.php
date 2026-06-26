<?php

namespace App\Policies;

use App\Models\MobileDiagnosticReport;
use App\Models\User;
use App\Policies\Concerns\AuthorizesPlatformAdmins;

final class MobileDiagnosticReportPolicy
{
    use AuthorizesPlatformAdmins;

    public function viewAny(User $user): bool
    {
        return $this->platformAdmin($user);
    }

    public function view(User $user, MobileDiagnosticReport $mobileDiagnosticReport): bool
    {
        return $this->platformAdmin($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MobileDiagnosticReport $mobileDiagnosticReport): bool
    {
        return false;
    }

    public function delete(User $user, MobileDiagnosticReport $mobileDiagnosticReport): bool
    {
        return false;
    }

    public function restore(User $user, MobileDiagnosticReport $mobileDiagnosticReport): bool
    {
        return false;
    }

    public function forceDelete(User $user, MobileDiagnosticReport $mobileDiagnosticReport): bool
    {
        return false;
    }
}
