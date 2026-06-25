<?php

namespace App\Livewire\Mobile\Settings;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\Native\SystemService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Permission settings')]
final class Permissions extends Component
{
    use DispatchesToasts;

    public ?string $settingsStatus = null;

    public ?string $settingsError = null;

    public ?string $lastRecoveryTarget = null;

    private SystemService $systems;

    public function boot(SystemService $systems): void
    {
        $this->systems = $systems;
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
            'platformRows' => $this->systems->platformHelperRows(),
            'permissionRecoveryLinks' => $this->systems->permissionRecoveryLinks(),
            'systemSnapshot' => $this->systems->snapshot(),
        ]);
    }

    private function permissionLabel(?string $permissionKey): ?string
    {
        if (! is_string($permissionKey) || trim($permissionKey) === '') {
            return null;
        }

        foreach ($this->systems->permissionRecoveryLinks() as $link) {
            if ($link['key'] === $permissionKey) {
                return $link['label'];
            }
        }

        return null;
    }
}
