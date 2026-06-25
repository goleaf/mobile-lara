<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\Native\NativeDialogService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Debug')]
class Debug extends Component
{
    use DispatchesToasts;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $dialogResult = null;

    public ?string $dialogStatus = null;

    public ?string $toastActionStatus = null;

    #[Validate('nullable|string|max:80')]
    public string $promptValue = 'Demo value';

    private NativeDialogService $dialogs;

    public function boot(NativeDialogService $dialogs): void
    {
        $this->dialogs = $dialogs;
    }

    public function showAlertExample(): void
    {
        $this->rememberDialogResult(
            $this->dialogs->alert(
                title: 'Native alert',
                message: 'This alert is routed through the NativePHP dialog wrapper.',
                buttons: ['OK'],
                id: 'debug-alert',
            ),
            'Alert dialog requested.',
        );
    }

    public function showConfirmExample(): void
    {
        $this->rememberDialogResult(
            $this->dialogs->confirm(
                title: 'Confirm action',
                message: 'NativePHP will report which button was selected through its alert event flow.',
                confirmLabel: 'Continue',
                cancelLabel: 'Cancel',
                id: 'debug-confirm',
            ),
            'Confirm dialog requested.',
        );
    }

    public function showPromptExample(): void
    {
        $this->validateOnly('promptValue');

        $this->rememberDialogResult(
            $this->dialogs->prompt(
                title: 'Prompt fallback',
                message: 'Native text input is not exposed by the installed dialog package yet.',
                defaultValue: $this->promptValue,
                submitLabel: 'Use value',
                cancelLabel: 'Cancel',
                id: 'debug-prompt',
            ),
            'Prompt fallback requested.',
        );
    }

    public function showToastExample(): void
    {
        $this->rememberDialogResult(
            $this->dialogs->toast(
                message: 'Saved with NativePHP toast.',
                duration: 'short',
            ),
            'Toast notification requested.',
        );
    }

    public function showSnackbarExample(): void
    {
        $this->rememberDialogResult(
            $this->dialogs->snackbar(
                message: 'Background sync queued.',
                duration: 'long',
            ),
            'Snackbar notification requested.',
        );
    }

    public function showSuccessToastExample(): void
    {
        $this->toastSuccess('Dashboard draft saved locally.', 'Saved', 3000);
    }

    public function showErrorToastExample(): void
    {
        $this->toastError('Sync failed. Check the network state and try again.', 'Sync failed');
    }

    public function showWarningToastExample(): void
    {
        $this->toastWarning('Secure storage is using the browser fallback in this runtime.', 'Fallback active', 6000);
    }

    public function showInfoToastExample(): void
    {
        $this->toastInfo('Background refresh is queued for the next app resume.', 'Queued', 5000);
    }

    public function showActionToastExample(): void
    {
        $this->toast(
            message: 'A debug sync was queued.',
            type: 'info',
            title: 'Action available',
            actionLabel: 'Undo',
            actionEvent: 'debug-toast-action',
            actionPayload: ['status' => 'Undo action received.'],
            persistent: true,
        );
    }

    public function showPersistentToastExample(): void
    {
        $this->toast(
            message: 'This notification stays visible until it is dismissed.',
            type: 'warning',
            title: 'Persistent notice',
            persistent: true,
        );
    }

    #[On('debug-toast-action')]
    public function recordToastAction(string $status): void
    {
        $this->toastActionStatus = $status;
    }

    public function render(): View
    {
        return view('livewire.mobile.debug', [
            'debugRows' => $this->debugRows(),
            'dialogActions' => $this->dialogActions(),
            'dialogResultRows' => $this->dialogResultRows(),
            'toastActions' => $this->toastActions(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $dialogResult
     */
    private function rememberDialogResult(array $dialogResult, string $status): void
    {
        $this->dialogResult = $dialogResult;
        $this->dialogStatus = $status;
    }

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    private function debugRows(): array
    {
        return [
            [
                'key' => 'laravel',
                'label' => 'Laravel',
                'value' => app()->version(),
            ],
            [
                'key' => 'livewire',
                'label' => 'Livewire',
                'value' => '4.x',
            ],
            [
                'key' => 'nativephp-app-id',
                'label' => 'NativePHP app ID',
                'value' => (string) config('nativephp.app_id', 'Not configured'),
            ],
            [
                'key' => 'nativephp-start-url',
                'label' => 'NativePHP start URL',
                'value' => (string) config('nativephp.start_url', 'Not configured'),
            ],
            [
                'key' => 'native-bridge',
                'label' => 'Native bridge',
                'value' => function_exists('nativephp_call') ? 'Available' : 'Browser fallback',
            ],
        ];
    }

    /**
     * @return list<array{label: string, action: string, variant: string}>
     */
    private function dialogActions(): array
    {
        return [
            [
                'label' => 'Alert',
                'action' => 'showAlertExample',
                'variant' => 'primary',
            ],
            [
                'label' => 'Confirm',
                'action' => 'showConfirmExample',
                'variant' => 'secondary',
            ],
            [
                'label' => 'Prompt',
                'action' => 'showPromptExample',
                'variant' => 'secondary',
            ],
            [
                'label' => 'Toast',
                'action' => 'showToastExample',
                'variant' => 'accent',
            ],
            [
                'label' => 'Snackbar',
                'action' => 'showSnackbarExample',
                'variant' => 'ghost',
            ],
        ];
    }

    /**
     * @return list<array{label: string, action: string, variant: string}>
     */
    private function toastActions(): array
    {
        return [
            [
                'label' => 'Success',
                'action' => 'showSuccessToastExample',
                'variant' => 'accent',
            ],
            [
                'label' => 'Error',
                'action' => 'showErrorToastExample',
                'variant' => 'danger',
            ],
            [
                'label' => 'Warning',
                'action' => 'showWarningToastExample',
                'variant' => 'secondary',
            ],
            [
                'label' => 'Info',
                'action' => 'showInfoToastExample',
                'variant' => 'primary',
            ],
            [
                'label' => 'Action',
                'action' => 'showActionToastExample',
                'variant' => 'secondary',
            ],
            [
                'label' => 'Persistent',
                'action' => 'showPersistentToastExample',
                'variant' => 'ghost',
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    private function dialogResultRows(): array
    {
        if ($this->dialogResult === null) {
            return [];
        }

        $rows = [];

        foreach ($this->dialogResult as $key => $value) {
            $rows[] = [
                'key' => Str::slug((string) $key),
                'label' => Str::headline((string) $key),
                'value' => $this->displayValue($value),
            ];
        }

        return $rows;
    }

    private function displayValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return implode(', ', array_map(static fn (mixed $item): string => (string) $item, $value));
        }

        if ($value === null) {
            return 'None';
        }

        return (string) $value;
    }
}
