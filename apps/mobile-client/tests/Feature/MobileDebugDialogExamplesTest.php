<?php

use App\Livewire\Mobile\Debug;
use App\Livewire\Mobile\NetworkStatus;
use Livewire\Livewire;

test('debug screen renders native dialog examples', function (): void {
    Livewire::test(Debug::class)
        ->assertSee('Developer Debug')
        ->assertSee('Runtime')
        ->assertSee('App version')
        ->assertSee('Laravel version')
        ->assertSee('NativePHP status')
        ->assertSee('Device model')
        ->assertSee('OS version')
        ->assertSee('Battery status')
        ->assertSee('Charging status')
        ->assertSee('Network status')
        ->assertSee('Connection type')
        ->assertSee('Metered connection')
        ->assertSee('Network source')
        ->assertSeeLivewire(NetworkStatus::class)
        ->assertSee('Database path placeholder')
        ->assertSee('Queue status placeholder')
        ->assertSee('Local notification driver')
        ->assertSee('Native tests')
        ->assertSee('Test dialogs')
        ->assertSee('Test storage')
        ->assertSee('Test camera')
        ->assertSee('Test notifications')
        ->assertSee('Test flashlight')
        ->assertSee('Test vibration')
        ->assertSee('Test haptics')
        ->assertSee('Native browser')
        ->assertSee('External URL')
        ->assertSee('In-app link')
        ->assertSee('OAuth link')
        ->assertSee('Privacy policy')
        ->assertSee('Support center')
        ->assertSee('Billing portal')
        ->assertSee('Native sharing')
        ->assertSee('Share debug snapshot')
        ->assertSee('Share report placeholder')
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

test('debug browser actions report browser fallback state', function (string $action, string $status): void {
    Livewire::test(Debug::class)
        ->call($action)
        ->assertSet('browserStatus', $status)
        ->assertSee($status)
        ->assertDispatched('mobile-toast', function (string $event, array $params) use ($status): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Browser unavailable'
                && ($params['message'] ?? null) === $status;
        });
})->with([
    'external url' => [
        'openExternalBrowserExample',
        'Native external browser is unavailable in this browser runtime.',
    ],
    'in-app url' => [
        'openInAppBrowserExample',
        'Native in-app browser is unavailable in this browser runtime.',
    ],
    'oauth url' => [
        'openOAuthBrowserExample',
        'Native OAuth browser is unavailable in this browser runtime.',
    ],
    'privacy policy' => [
        'openPrivacyPolicyBrowserExample',
        'Native in-app browser is unavailable in this browser runtime.',
    ],
    'support center' => [
        'openSupportCenterBrowserExample',
        'Native in-app browser is unavailable in this browser runtime.',
    ],
    'billing portal' => [
        'openBillingPortalPlaceholderExample',
        'Native external browser is unavailable in this browser runtime.',
    ],
]);

test('debug share actions report browser fallback state', function (string $action, string $status): void {
    Livewire::test(Debug::class)
        ->call($action)
        ->assertSet('shareStatus', $status)
        ->assertSee($status)
        ->assertDispatched('mobile-toast', function (string $event, array $params) use ($status): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Share unavailable'
                && ($params['message'] ?? null) === $status;
        });
})->with([
    'debug snapshot' => [
        'shareDebugSnapshot',
        'Native text sharing is unavailable in this browser runtime.',
    ],
    'report placeholder' => [
        'shareReportPlaceholder',
        'Native URL sharing is unavailable in this browser runtime.',
    ],
]);

test('debug native test buttons report browser fallback state', function (string $action, string $property, string $status): void {
    Livewire::test(Debug::class)
        ->call($action)
        ->assertSet($property, $status)
        ->assertSee($status);
})->with([
    'storage' => [
        'testStorageExample',
        'storageStatus',
        'Native secure storage is unavailable in this browser runtime.',
    ],
    'camera' => [
        'testCameraExample',
        'cameraStatus',
        'Native camera is unavailable in this browser runtime.',
    ],
    'flashlight' => [
        'testFlashlightExample',
        'flashlightStatus',
        'Flashlight is unavailable outside NativePHP runtime.',
    ],
    'vibration' => [
        'testVibrationExample',
        'vibrationStatus',
        'Vibration is unavailable outside NativePHP runtime.',
    ],
    'haptics' => [
        'testHapticsExample',
        'hapticStatus',
        'Haptic feedback is unavailable outside NativePHP runtime.',
    ],
]);

test('debug notification test uses the local notification abstraction', function (): void {
    $component = Livewire::test(Debug::class)
        ->call('testNotificationsExample');

    expect($component->instance()->notificationStatus)
        ->toBeString()
        ->not->toBe('');
});

test('debug native event callbacks update pending camera and notification statuses', function (): void {
    Livewire::test(Debug::class)
        ->set('pendingCameraTestId', 'debug-camera-test')
        ->call('handleDebugPhotoTaken', '/tmp/native-avatar.jpg', 'image/jpeg', 'debug-camera-test')
        ->assertSet('pendingCameraTestId', null)
        ->assertSet('cameraStatus', 'Camera returned native-avatar.jpg (image/jpeg).')
        ->set('pendingNotificationTestId', 'debug-push-test')
        ->call('handleDebugPushTokenGenerated', 'abcdef1234567890', 'debug-push-test')
        ->assertSet('pendingNotificationTestId', null)
        ->assertSet('notificationStatus', 'Push token generated: abcdef...7890.');
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
