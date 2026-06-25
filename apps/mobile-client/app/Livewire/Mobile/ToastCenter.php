<?php

namespace App\Livewire\Mobile;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

final class ToastCenter extends Component
{
    private const MAX_VISIBLE_TOASTS = 4;

    /**
     * @var list<array{
     *     id: string,
     *     type: string,
     *     title: string|null,
     *     message: string,
     *     action_label: string|null,
     *     action_event: string|null,
     *     action_payload: array<string, mixed>,
     *     persistent: bool,
     *     dismisses_on_action: bool,
     *     expires_at: int|null,
     *     created_at: int
     * }>
     */
    public array $toasts = [];

    #[On('mobile-toast')]
    #[On('toast')]
    public function notify(
        string $message,
        ?string $title = null,
        string $type = 'info',
        ?string $variant = null,
        ?string $actionLabel = null,
        ?string $actionEvent = null,
        array $actionPayload = [],
        bool $persistent = false,
        int $duration = 5000,
        bool $dismissesOnAction = true,
    ): void {
        $message = trim($message);

        if ($message === '') {
            return;
        }

        $toastType = $this->normalizeType($variant ?: $type);
        $durationInSeconds = $this->durationInSeconds($duration);
        $createdAt = now()->timestamp;

        $this->toasts[] = [
            'id' => 'toast-'.Str::uuid()->toString(),
            'type' => $toastType,
            'title' => $this->normalizeNullableText($title),
            'message' => $message,
            'action_label' => $this->normalizeNullableText($actionLabel),
            'action_event' => $this->normalizeNullableText($actionEvent),
            'action_payload' => $this->normalizePayload($actionPayload),
            'persistent' => $persistent,
            'dismisses_on_action' => $dismissesOnAction,
            'expires_at' => $persistent ? null : $createdAt + $durationInSeconds,
            'created_at' => $createdAt,
        ];

        $this->pruneExpiredToasts();
        $this->toasts = array_slice($this->toasts, -self::MAX_VISIBLE_TOASTS);
    }

    public function dismiss(string $id): void
    {
        $this->toasts = array_values(array_filter(
            $this->toasts,
            static fn (array $toast): bool => $toast['id'] !== $id,
        ));
    }

    public function runAction(string $id): void
    {
        $toast = $this->findToast($id);

        if ($toast === null || ! is_string($toast['action_event']) || $toast['action_event'] === '') {
            return;
        }

        $this->dispatch($toast['action_event'], ...$toast['action_payload']);

        if ($toast['dismisses_on_action']) {
            $this->dismiss($id);
        }
    }

    public function pruneExpiredToasts(): void
    {
        $currentTimestamp = now()->timestamp;

        $this->toasts = array_values(array_filter(
            $this->toasts,
            static fn (array $toast): bool => $toast['expires_at'] === null || $toast['expires_at'] > $currentTimestamp,
        ));
    }

    public function render(): View
    {
        return view('livewire.mobile.toast-center', [
            'toastRows' => $this->toastRows(),
            'hasAutoDismissToasts' => $this->hasAutoDismissToasts(),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findToast(string $id): ?array
    {
        foreach ($this->toasts as $toast) {
            if ($toast['id'] === $id) {
                return $toast;
            }
        }

        return null;
    }

    private function normalizeType(string $type): string
    {
        return in_array($type, ['success', 'error', 'warning', 'info'], true) ? $type : 'info';
    }

    private function normalizeNullableText(?string $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private function durationInSeconds(int $duration): int
    {
        return max(1, (int) ceil(max(1, $duration) / 1000));
    }

    /**
     * @param  array<mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(array $payload): array
    {
        $normalizedPayload = [];

        foreach ($payload as $key => $value) {
            if (! is_string($key) || $key === '') {
                continue;
            }

            $normalizedPayload[$key] = $value;
        }

        return $normalizedPayload;
    }

    /**
     * @return list<array{
     *     id: string,
     *     type: string,
     *     title: string|null,
     *     message: string,
     *     action_label: string|null,
     *     action_event: string|null,
     *     action_payload: array<string, mixed>,
     *     persistent: bool,
     *     dismisses_on_action: bool,
     *     expires_at: int|null,
     *     created_at: int,
     *     role: string,
     *     marker: string,
     *     wrapper_classes: string,
     *     marker_classes: string,
     *     action_classes: string
     * }>
     */
    private function toastRows(): array
    {
        return array_map(
            fn (array $toast): array => [
                ...$toast,
                ...$this->toastPresentation($toast['type']),
            ],
            $this->toasts,
        );
    }

    private function hasAutoDismissToasts(): bool
    {
        foreach ($this->toasts as $toast) {
            if ($toast['expires_at'] !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{role: string, marker: string, wrapper_classes: string, marker_classes: string, action_classes: string}
     */
    private function toastPresentation(string $type): array
    {
        return [
            'success' => [
                'role' => 'status',
                'marker' => 'S',
                'wrapper_classes' => 'border-emerald-200 bg-emerald-50 text-emerald-950 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-100',
                'marker_classes' => 'bg-emerald-500 text-white dark:bg-emerald-300 dark:text-emerald-950',
                'action_classes' => 'border-emerald-200 bg-white text-emerald-800 hover:bg-emerald-100 dark:border-emerald-400/30 dark:bg-emerald-400/10 dark:text-emerald-100 dark:hover:bg-emerald-400/20',
            ],
            'error' => [
                'role' => 'alert',
                'marker' => 'E',
                'wrapper_classes' => 'border-red-200 bg-red-50 text-red-950 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-100',
                'marker_classes' => 'bg-red-600 text-white dark:bg-red-300 dark:text-red-950',
                'action_classes' => 'border-red-200 bg-white text-red-800 hover:bg-red-100 dark:border-red-400/30 dark:bg-red-400/10 dark:text-red-100 dark:hover:bg-red-400/20',
            ],
            'warning' => [
                'role' => 'status',
                'marker' => 'W',
                'wrapper_classes' => 'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-300/30 dark:bg-amber-300/10 dark:text-amber-100',
                'marker_classes' => 'bg-amber-500 text-amber-950 dark:bg-amber-300 dark:text-amber-950',
                'action_classes' => 'border-amber-200 bg-white text-amber-900 hover:bg-amber-100 dark:border-amber-300/30 dark:bg-amber-300/10 dark:text-amber-100 dark:hover:bg-amber-300/20',
            ],
            'info' => [
                'role' => 'status',
                'marker' => 'I',
                'wrapper_classes' => 'border-sky-200 bg-sky-50 text-sky-950 dark:border-sky-400/30 dark:bg-sky-400/10 dark:text-sky-100',
                'marker_classes' => 'bg-sky-500 text-white dark:bg-sky-300 dark:text-sky-950',
                'action_classes' => 'border-sky-200 bg-white text-sky-800 hover:bg-sky-100 dark:border-sky-400/30 dark:bg-sky-400/10 dark:text-sky-100 dark:hover:bg-sky-400/20',
            ],
        ][$type] ?? [
            'role' => 'status',
            'marker' => 'I',
            'wrapper_classes' => 'border-app-line bg-app-surface text-app-ink dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100',
            'marker_classes' => 'bg-app-accent text-app-accent-ink dark:bg-emerald-300 dark:text-zinc-950',
            'action_classes' => 'border-app-line bg-white text-app-ink hover:bg-app-bg dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:bg-zinc-900',
        ];
    }
}
