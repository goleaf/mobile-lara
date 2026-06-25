<?php

namespace App\Livewire\Concerns;

trait DispatchesToasts
{
    /**
     * @param  array<string, mixed>  $actionPayload
     */
    protected function toast(
        string $message,
        string $type = 'info',
        ?string $title = null,
        ?string $actionLabel = null,
        ?string $actionEvent = null,
        array $actionPayload = [],
        bool $persistent = false,
        int $duration = 5000,
        bool $dismissesOnAction = true,
    ): void {
        $this->dispatch(
            'mobile-toast',
            type: $type,
            title: $title,
            message: $message,
            actionLabel: $actionLabel,
            actionEvent: $actionEvent,
            actionPayload: $actionPayload,
            persistent: $persistent,
            duration: $duration,
            dismissesOnAction: $dismissesOnAction,
        );
    }

    protected function toastSuccess(string $message, ?string $title = 'Success', int $duration = 5000): void
    {
        $this->toast(
            message: $message,
            type: 'success',
            title: $title,
            duration: $duration,
        );
    }

    protected function toastError(string $message, ?string $title = 'Error', bool $persistent = true): void
    {
        $this->toast(
            message: $message,
            type: 'error',
            title: $title,
            persistent: $persistent,
        );
    }

    protected function toastWarning(string $message, ?string $title = 'Warning', int $duration = 7000): void
    {
        $this->toast(
            message: $message,
            type: 'warning',
            title: $title,
            duration: $duration,
        );
    }

    protected function toastInfo(string $message, ?string $title = 'Info', int $duration = 5000): void
    {
        $this->toast(
            message: $message,
            type: 'info',
            title: $title,
            duration: $duration,
        );
    }
}
