<?php

namespace App\Livewire\Concerns;

use App\Services\MobileAccess\MobileAccessPolicy;

trait GuardsMobileRecordActions
{
    protected MobileAccessPolicy $mobileAccessPolicy;

    /**
     * @return array{create: bool, update: bool, archive: bool, delete: bool}
     */
    protected function recordActionPermissions(): array
    {
        return [
            'create' => $this->recordActionAllowed('records.create'),
            'update' => $this->recordActionAllowed('records.update'),
            'archive' => $this->recordActionAllowed('records.archive'),
            'delete' => $this->recordActionAllowed('records.delete'),
        ];
    }

    protected function recordActionAllowed(string $permission): bool
    {
        return $this->mobileAccessPolicy->allows('records', $permission);
    }

    protected function recordActionDenied(string $permission, string $title): bool
    {
        $decision = $this->mobileAccessPolicy->decision('records', $permission);

        if ($decision['allowed']) {
            return false;
        }

        $this->toastWarning($decision['message'], $title);

        return true;
    }
}
