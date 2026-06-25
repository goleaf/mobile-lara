<?php

namespace App\Livewire\Mobile\Settings;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\Native\PermissionCenterService;
use App\Services\Native\SystemService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Permissions center')]
final class Permissions extends Component
{
    use DispatchesToasts;

    public ?string $permissionStatus = null;

    public ?string $permissionError = null;

    public ?string $lastPermissionTarget = null;

    public ?string $lastPermissionStatus = null;

    public ?string $settingsStatus = null;

    public ?string $settingsError = null;

    public ?string $lastRecoveryTarget = null;

    private PermissionCenterService $permissionCenter;

    private SystemService $systems;

    public function boot(PermissionCenterService $permissionCenter, SystemService $systems): void
    {
        $this->permissionCenter = $permissionCenter;
        $this->systems = $systems;
    }

    public function requestPermission(string $permissionKey): void
    {
        $this->permissionStatus = null;
        $this->permissionError = null;
        $this->lastPermissionTarget = null;
        $this->lastPermissionStatus = null;

        $result = $this->permissionCenter->request($permissionKey);
        $this->lastPermissionTarget = $result['label'];
        $this->lastPermissionStatus = $result['status'];

        if ($result['success']) {
            $this->permissionStatus = $result['message'];
            $this->toastSuccess($result['message'], 'Permission request');

            return;
        }

        $this->permissionError = $result['message'];
        $this->toastWarning($result['message'], 'Permission unavailable');
    }

    public function openAppSettings(?string $permissionKey = null): void
    {
        $this->settingsStatus = null;
        $this->settingsError = null;
        $this->lastRecoveryTarget = $this->permissionLabel($permissionKey);

        $result = $this->systems->openAppSettings();
        $message = $this->lastRecoveryTarget === null
            ? $result['message']
            : "{$result['message']} Recovery target: {$this->lastRecoveryTarget}.";

        if ($result['success']) {
            $this->settingsStatus = $message;
            $this->toastSuccess($message, 'Settings opened');

            return;
        }

        $this->settingsError = $message;
        $this->toastWarning($message, 'Native settings unavailable');
    }

    public function render(): View
    {
        return view('livewire.mobile.settings.permissions', [
            'permissionRows' => $this->permissionCenter->permissions(),
            'platformRows' => $this->systems->platformHelperRows(),
            'systemSnapshot' => $this->systems->snapshot(),
        ]);
    }

    private function permissionLabel(?string $permissionKey): ?string
    {
        if (! is_string($permissionKey) || trim($permissionKey) === '') {
            return null;
        }

        foreach ($this->permissionCenter->permissions() as $permission) {
            if ($permission['key'] === $permissionKey) {
                return $permission['label'];
            }
        }

        return null;
    }
}
