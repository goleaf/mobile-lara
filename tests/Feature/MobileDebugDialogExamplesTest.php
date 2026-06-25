<?php

use App\Livewire\Mobile\Debug;
use Livewire\Livewire;

test('debug screen renders native dialog examples', function (): void {
    Livewire::test(Debug::class)
        ->assertSee('Runtime')
        ->assertSee('Native dialogs')
        ->assertSee('Livewire toasts')
        ->assertSee('Prompt default value')
        ->assertSee('Alert')
        ->assertSee('Confirm')
        ->assertSee('Prompt')
        ->assertSee('Toast')
        ->assertSee('Snackbar')
        ->assertSee('Success')
        ->assertSee('Error')
        ->assertSee('Warning')
        ->assertSee('Info')
        ->assertSee('Action')
        ->assertSee('Persistent');
});

test('debug dialog actions update the last payload', function (string $action, string $type, string $status): void {
    $component = Livewire::test(Debug::class)
        ->call($action)
        ->assertSet('dialogStatus', $status)
        ->assertSee($status)
        ->assertSee('Last payload');

    expect($component->instance()->dialogResult)
        ->toBeArray()
        ->and($component->instance()->dialogResult['type'])->toBe($type)
        ->and($component->instance()->dialogResult['dispatched'])->toBe(function_exists('nativephp_call'));
})->with([
    'alert' => ['showAlertExample', 'alert', 'Alert dialog requested.'],
    'confirm' => ['showConfirmExample', 'confirm', 'Confirm dialog requested.'],
    'prompt' => ['showPromptExample', 'prompt', 'Prompt fallback requested.'],
    'toast' => ['showToastExample', 'toast', 'Toast notification requested.'],
    'snackbar' => ['showSnackbarExample', 'snackbar', 'Snackbar notification requested.'],
]);

test('debug prompt example validates the default value', function (): void {
    Livewire::test(Debug::class)
        ->set('promptValue', str_repeat('x', 81))
        ->call('showPromptExample')
        ->assertHasErrors(['promptValue' => 'max']);
});

test('debug toast examples dispatch mobile toast events', function (string $action, string $type, bool $persistent): void {
    Livewire::test(Debug::class)
        ->call($action)
        ->assertDispatched('mobile-toast', function (string $event, array $params) use ($type, $persistent): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === $type
                && ($params['persistent'] ?? null) === $persistent
                && is_string($params['message'] ?? null);
        });
})->with([
    'success' => ['showSuccessToastExample', 'success', false],
    'error' => ['showErrorToastExample', 'error', true],
    'warning' => ['showWarningToastExample', 'warning', false],
    'info' => ['showInfoToastExample', 'info', false],
    'persistent' => ['showPersistentToastExample', 'warning', true],
]);

test('debug action toast dispatches action metadata and receives action events', function (): void {
    Livewire::test(Debug::class)
        ->call('showActionToastExample')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'info'
                && ($params['actionLabel'] ?? null) === 'Undo'
                && ($params['actionEvent'] ?? null) === 'debug-toast-action'
                && ($params['persistent'] ?? null) === true;
        })
        ->dispatch('debug-toast-action', status: 'Undo action received.')
        ->assertSet('toastActionStatus', 'Undo action received.');
});
