<?php

namespace App\Services\Native;

use Illuminate\Support\Str;
use Native\Mobile\Facades\Dialog as NativeDialog;

final class NativeDialogService
{
    /**
     * @return array{
     *     type: 'alert',
     *     id: string,
     *     title: string,
     *     message: string,
     *     buttons: list<string>,
     *     native_method: 'Dialog.Alert',
     *     dispatched: bool
     * }
     */
    public function alert(string $title, string $message, array $buttons = ['OK'], ?string $id = null): array
    {
        return $this->showAlert(
            type: 'alert',
            title: $title,
            message: $message,
            buttons: $buttons,
            id: $id,
        );
    }

    /**
     * @return array{
     *     type: 'confirm',
     *     id: string,
     *     title: string,
     *     message: string,
     *     buttons: list<string>,
     *     native_method: 'Dialog.Alert',
     *     dispatched: bool
     * }
     */
    public function confirm(
        string $title,
        string $message,
        string $confirmLabel = 'Confirm',
        string $cancelLabel = 'Cancel',
        ?string $id = null,
    ): array {
        return $this->showAlert(
            type: 'confirm',
            title: $title,
            message: $message,
            buttons: [$cancelLabel, $confirmLabel],
            id: $id,
        );
    }

    /**
     * NativePHP Mobile Dialog 1.x does not expose text-input prompts yet, so
     * this records prompt metadata while using a native alert when available.
     *
     * @return array{
     *     type: 'prompt',
     *     id: string,
     *     title: string,
     *     message: string,
     *     buttons: list<string>,
     *     default_value: string,
     *     native_input_supported: false,
     *     native_method: 'Dialog.Alert',
     *     dispatched: bool
     * }
     */
    public function prompt(
        string $title,
        string $message,
        string $defaultValue = '',
        string $submitLabel = 'Submit',
        string $cancelLabel = 'Cancel',
        ?string $id = null,
    ): array {
        $payload = $this->showAlert(
            type: 'prompt',
            title: $title,
            message: $defaultValue === '' ? $message : "{$message}\n\nDefault: {$defaultValue}",
            buttons: [$cancelLabel, $submitLabel],
            id: $id,
        );

        return [
            ...$payload,
            'default_value' => $defaultValue,
            'native_input_supported' => false,
        ];
    }

    /**
     * @return array{
     *     type: 'toast',
     *     message: string,
     *     duration: 'short'|'long',
     *     native_method: 'Dialog.Toast',
     *     dispatched: bool
     * }
     */
    public function toast(string $message, string $duration = 'long'): array
    {
        $normalizedDuration = $this->normalizeDuration($duration);
        $dispatched = $this->canDispatchNativeDialog();

        if ($dispatched) {
            NativeDialog::toast($message, $normalizedDuration);
        }

        return [
            'type' => 'toast',
            'message' => $message,
            'duration' => $normalizedDuration,
            'native_method' => 'Dialog.Toast',
            'dispatched' => $dispatched,
        ];
    }

    /**
     * NativePHP Mobile maps toast notifications to an Android snackbar style
     * notification, so this method keeps app code expressive while sharing the
     * same native bridge call.
     *
     * @return array{
     *     type: 'snackbar',
     *     message: string,
     *     duration: 'short'|'long',
     *     native_method: 'Dialog.Toast',
     *     dispatched: bool
     * }
     */
    public function snackbar(string $message, string $duration = 'long'): array
    {
        $payload = $this->toast($message, $duration);

        return [
            ...$payload,
            'type' => 'snackbar',
        ];
    }

    /**
     * @param  list<string>|array<int|string, mixed>  $buttons
     * @return array{
     *     type: 'alert'|'confirm'|'prompt',
     *     id: string,
     *     title: string,
     *     message: string,
     *     buttons: list<string>,
     *     native_method: 'Dialog.Alert',
     *     dispatched: bool
     * }
     */
    private function showAlert(string $type, string $title, string $message, array $buttons, ?string $id): array
    {
        $dialogId = $id ?: $this->dialogId($type);
        $normalizedButtons = $this->normalizeButtons($buttons);
        $dispatched = $this->canDispatchNativeDialog();

        if ($dispatched) {
            NativeDialog::alert($title, $message, $normalizedButtons)
                ->id($dialogId)
                ->show();
        }

        return [
            'type' => $type,
            'id' => $dialogId,
            'title' => $title,
            'message' => $message,
            'buttons' => $normalizedButtons,
            'native_method' => 'Dialog.Alert',
            'dispatched' => $dispatched,
        ];
    }

    private function canDispatchNativeDialog(): bool
    {
        return function_exists('nativephp_call');
    }

    private function dialogId(string $type): string
    {
        return "mobile-{$type}-".Str::uuid()->toString();
    }

    /**
     * @param  list<string>|array<int|string, mixed>  $buttons
     * @return list<string>
     */
    private function normalizeButtons(array $buttons): array
    {
        $normalizedButtons = [];

        foreach ($buttons as $button) {
            if (! is_scalar($button)) {
                continue;
            }

            $label = trim((string) $button);

            if ($label !== '') {
                $normalizedButtons[] = $label;
            }
        }

        return $normalizedButtons === [] ? ['OK'] : $normalizedButtons;
    }

    private function normalizeDuration(string $duration): string
    {
        return in_array($duration, ['short', 'long'], true) ? $duration : 'long';
    }
}
