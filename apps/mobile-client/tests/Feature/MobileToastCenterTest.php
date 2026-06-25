<?php

use App\Livewire\Mobile\ToastCenter;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('toast center renders success error warning and info notifications', function (string $type, string $title, string $message, string $class, string $role): void {
    Livewire::test(ToastCenter::class)
        ->dispatch('mobile-toast', type: $type, title: $title, message: $message)
        ->assertSee($title)
        ->assertSee($message)
        ->assertSee($class, false)
        ->assertSee('role="'.$role.'"', false);
})->with([
    'success' => ['success', 'Saved', 'Local changes saved.', 'border-emerald-200', 'status'],
    'error' => ['error', 'Failed', 'Sync failed.', 'border-red-200', 'alert'],
    'warning' => ['warning', 'Check this', 'Storage fallback active.', 'border-amber-200', 'status'],
    'info' => ['info', 'Queued', 'Refresh queued.', 'border-sky-200', 'status'],
]);

test('toast center accepts the legacy toast event and variant argument', function (): void {
    Livewire::test(ToastCenter::class)
        ->dispatch('toast', variant: 'warning', title: 'Legacy event', message: 'Still works.')
        ->assertSee('Legacy event')
        ->assertSee('Still works.')
        ->assertSee('border-amber-200', false);
});

test('toast center supports action buttons and dispatches the configured action event', function (): void {
    $component = Livewire::test(ToastCenter::class)
        ->dispatch(
            'mobile-toast',
            type: 'info',
            title: 'Undo available',
            message: 'A sync was queued.',
            actionLabel: 'Undo',
            actionEvent: 'debug-toast-action',
            actionPayload: ['status' => 'Undo action received.'],
            persistent: true,
        )
        ->assertSee('Undo available')
        ->assertSee('Undo');

    $toastId = $component->instance()->toasts[0]['id'];

    $component
        ->call('runAction', $toastId)
        ->assertDispatched('debug-toast-action', status: 'Undo action received.');

    expect($component->instance()->toasts)->toBeEmpty();
});

test('toast center can keep an action toast visible after the action runs', function (): void {
    $component = Livewire::test(ToastCenter::class)
        ->dispatch(
            'mobile-toast',
            message: 'Action stays visible.',
            actionLabel: 'Review',
            actionEvent: 'toast-reviewed',
            persistent: true,
            dismissesOnAction: false,
        );

    $toastId = $component->instance()->toasts[0]['id'];

    $component
        ->call('runAction', $toastId)
        ->assertDispatched('toast-reviewed');

    expect($component->instance()->toasts)->toHaveCount(1);
});

test('toast center dismisses notifications manually', function (): void {
    $component = Livewire::test(ToastCenter::class)
        ->dispatch('mobile-toast', message: 'Dismiss me.');

    $toastId = $component->instance()->toasts[0]['id'];

    $component
        ->call('dismiss', $toastId)
        ->assertDontSee('Dismiss me.');

    expect($component->instance()->toasts)->toBeEmpty();
});

test('toast center auto dismisses expired notifications and keeps persistent ones', function (): void {
    $start = Carbon::parse('2026-06-25 12:00:00');

    Carbon::setTestNow($start);

    $component = Livewire::test(ToastCenter::class)
        ->dispatch('mobile-toast', message: 'Gone soon.', duration: 1000)
        ->dispatch('mobile-toast', message: 'Stays visible.', persistent: true)
        ->assertSee('Gone soon.')
        ->assertSee('Stays visible.');

    Carbon::setTestNow($start->copy()->addSeconds(2));

    $component
        ->call('pruneExpiredToasts')
        ->assertDontSee('Gone soon.')
        ->assertSee('Stays visible.');

    Carbon::setTestNow();
});

test('toast center limits visible notifications and ignores blank messages', function (): void {
    $component = Livewire::test(ToastCenter::class)
        ->dispatch('mobile-toast', message: 'First')
        ->dispatch('mobile-toast', message: 'Second')
        ->dispatch('mobile-toast', message: 'Third')
        ->dispatch('mobile-toast', message: 'Fourth')
        ->dispatch('mobile-toast', message: 'Fifth')
        ->dispatch('mobile-toast', message: '   ');

    expect($component->instance()->toasts)
        ->toHaveCount(4)
        ->and(array_column($component->instance()->toasts, 'message'))
        ->toBe(['Second', 'Third', 'Fourth', 'Fifth']);
});
