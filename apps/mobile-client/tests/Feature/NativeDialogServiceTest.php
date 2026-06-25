<?php

use App\Services\Native\NativeDialogService;

test('alert returns native dialog payload without dispatching in browser context', function (): void {
    $payload = app(NativeDialogService::class)->alert(
        title: 'Native alert',
        message: 'Hello from tests.',
        buttons: ['OK'],
        id: 'test-alert',
    );

    expect($payload)
        ->toMatchArray([
            'type' => 'alert',
            'id' => 'test-alert',
            'title' => 'Native alert',
            'message' => 'Hello from tests.',
            'buttons' => ['OK'],
            'native_method' => 'Dialog.Alert',
            'dispatched' => function_exists('nativephp_call'),
        ]);
});

test('confirm maps to alert buttons with cancel first', function (): void {
    $payload = app(NativeDialogService::class)->confirm(
        title: 'Continue?',
        message: 'Confirm the action.',
        confirmLabel: 'Continue',
        cancelLabel: 'Cancel',
        id: 'test-confirm',
    );

    expect($payload['type'])->toBe('confirm')
        ->and($payload['id'])->toBe('test-confirm')
        ->and($payload['buttons'])->toBe(['Cancel', 'Continue'])
        ->and($payload['native_method'])->toBe('Dialog.Alert');
});

test('prompt records fallback metadata for unsupported native input', function (): void {
    $payload = app(NativeDialogService::class)->prompt(
        title: 'Prompt',
        message: 'Enter value.',
        defaultValue: 'Demo',
        submitLabel: 'Use',
        cancelLabel: 'Cancel',
        id: 'test-prompt',
    );

    expect($payload['type'])->toBe('prompt')
        ->and($payload['id'])->toBe('test-prompt')
        ->and($payload['buttons'])->toBe(['Cancel', 'Use'])
        ->and($payload['default_value'])->toBe('Demo')
        ->and($payload['native_input_supported'])->toBeFalse()
        ->and($payload['message'])->toContain('Default: Demo');
});

test('toast normalizes duration and snackbar uses the toast bridge', function (): void {
    $dialogs = app(NativeDialogService::class);

    $toastPayload = $dialogs->toast('Saved.', 'instant');
    $snackbarPayload = $dialogs->snackbar('Queued.', 'short');

    expect($toastPayload)
        ->toMatchArray([
            'type' => 'toast',
            'message' => 'Saved.',
            'duration' => 'long',
            'native_method' => 'Dialog.Toast',
            'dispatched' => function_exists('nativephp_call'),
        ]);

    expect($snackbarPayload)
        ->toMatchArray([
            'type' => 'snackbar',
            'message' => 'Queued.',
            'duration' => 'short',
            'native_method' => 'Dialog.Toast',
            'dispatched' => function_exists('nativephp_call'),
        ]);
});
