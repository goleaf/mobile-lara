<?php

namespace App\Livewire\Concerns;

use App\Services\MobileAccess\MobileAccessPolicy;

trait GuardsMobileFeatureActions
{
    protected MobileAccessPolicy $mobileAccessPolicy;

    protected function mobileFeatureAllowed(string $feature, ?string $permission = null): bool
    {
        return $this->mobileAccessPolicy->allows($feature, $permission);
    }

    /**
     * @return array{allowed: bool, feature: string, permission: string|null, reason: string|null, message: string, next_action: string|null, source: string}
     */
    protected function mobileFeatureDecision(string $feature, ?string $permission = null): array
    {
        return $this->mobileAccessPolicy->decision($feature, $permission);
    }

    protected function mobileFeatureDenied(string $feature, string $title, ?string $permission = null): bool
    {
        $decision = $this->mobileFeatureDecision($feature, $permission);

        if ($decision['allowed']) {
            return false;
        }

        $this->toastWarning($decision['message'], $title);

        return true;
    }
}
