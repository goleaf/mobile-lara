<?php

namespace App\Livewire\Mobile;

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileFeatureActions;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileDiagnostics\MobileDiagnosticsReportBuilder;
use App\Services\Native\BrowserService;
use App\Services\Native\DeviceService;
use App\Services\Native\LocalNotifications\LocalNotificationService;
use App\Services\Native\NativeDialogService;
use App\Services\Native\ShareService;
use Composer\InstalledVersions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Camera\PermissionDenied;
use Native\Mobile\Events\Camera\PhotoCancelled;
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Events\PushNotification\TokenGenerated;
use Native\Mobile\Facades\Camera;
use Native\Mobile\SecureStorage;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Developer Debug')]
class Debug extends Component
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $dialogResult = null;

    public ?string $dialogStatus = null;

    public ?string $toastActionStatus = null;

    public ?string $storageStatus = null;

    public ?string $cameraStatus = null;

    public ?string $notificationStatus = null;

    public ?string $flashlightStatus = null;

    public ?string $vibrationStatus = null;

    public ?string $hapticStatus = null;

    public ?string $browserStatus = null;

    public ?string $shareStatus = null;

    public ?string $diagnosticsStatus = null;

    public ?string $pendingCameraTestId = null;

    public ?string $pendingNotificationTestId = null;

    #[Validate('nullable|string|max:80')]
    public string $promptValue = 'Demo value';

    private NativeDialogService $dialogs;

    private MobileNetworkState $networkState;

    private DeviceService $devices;

    private BrowserService $browsers;

    private ShareService $shares;

    private LocalNotificationService $localNotifications;

    private MobileDiagnosticsReportBuilder $diagnostics;

    public function boot(
        NativeDialogService $dialogs,
        MobileNetworkState $networkState,
        DeviceService $devices,
        BrowserService $browsers,
        ShareService $shares,
        LocalNotificationService $localNotifications,
        MobileDiagnosticsReportBuilder $diagnostics,
        MobileAccessPolicy $mobileAccessPolicy,
    ): void {
        $this->dialogs = $dialogs;
        $this->networkState = $networkState;
        $this->devices = $devices;
        $this->browsers = $browsers;
        $this->shares = $shares;
        $this->localNotifications = $localNotifications;
        $this->diagnostics = $diagnostics;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
    }

    public function showAlertExample(): void
    {
        if ($this->nativeDebugActionDenied('native_dialogs', 'Dialog unavailable', 'dialog')) {
            return;
        }

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
        if ($this->nativeDebugActionDenied('native_dialogs', 'Dialog unavailable', 'dialog')) {
            return;
        }

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
        if ($this->nativeDebugActionDenied('native_dialogs', 'Dialog unavailable', 'dialog')) {
            return;
        }

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
        if ($this->nativeDebugActionDenied('native_dialogs', 'Dialog unavailable', 'dialog')) {
            return;
        }

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
        if ($this->nativeDebugActionDenied('native_dialogs', 'Dialog unavailable', 'dialog')) {
            return;
        }

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

    public function testStorageExample(): void
    {
        if ($this->nativeDebugActionDenied('native_secure_storage', 'Storage unavailable', 'storage')) {
            return;
        }

        if (! $this->nativeBridgeIsAvailable()) {
            $this->storageStatus = 'Native secure storage is unavailable in this browser runtime.';
            $this->toastWarning($this->storageStatus, 'Storage fallback active');

            return;
        }

        $key = 'debug.storage.'.Str::uuid()->toString();
        $value = 'debug-'.Str::random(12);
        $secureStorage = new SecureStorage;

        $stored = $secureStorage->set($key, $value);
        $readValue = $secureStorage->get($key);
        $deleted = $secureStorage->delete($key);

        if ($stored && is_string($readValue) && hash_equals($value, $readValue) && $deleted) {
            $this->storageStatus = 'Native secure storage write/read/delete passed.';
            $this->toastSuccess($this->storageStatus, 'Storage OK');

            return;
        }

        $this->storageStatus = 'Native secure storage test failed.';
        $this->toastError($this->storageStatus, 'Storage failed');
    }

    public function testCameraExample(): void
    {
        if ($this->nativeDebugActionDenied('native_camera', 'Camera unavailable', 'camera')) {
            return;
        }

        if (! $this->nativeBridgeIsAvailable()) {
            $this->cameraStatus = 'Native camera is unavailable in this browser runtime.';
            $this->toastInfo($this->cameraStatus, 'Camera fallback active');

            return;
        }

        $testId = 'debug-camera-'.Str::uuid()->toString();
        $this->pendingCameraTestId = $testId;

        $started = Camera::getPhoto(['quality' => 80])
            ->id($testId)
            ->remember()
            ->start();

        if (! $started) {
            $this->pendingCameraTestId = null;
            $this->cameraStatus = 'Unable to open the native camera.';
            $this->toastError($this->cameraStatus, 'Camera failed');

            return;
        }

        $this->cameraStatus = 'Native camera opened. Capture or cancel on the device.';
        $this->toastInfo($this->cameraStatus, 'Camera opened');
    }

    public function testNotificationsExample(): void
    {
        if ($this->nativeDebugActionDenied('notifications', 'Notifications unavailable', 'notifications', 'notifications.view')) {
            return;
        }

        $testId = 'debug-notifications-'.Str::uuid()->toString();
        $result = $this->localNotifications->testNotification($testId);

        $notification = $result['notification'] ?? null;
        $this->pendingNotificationTestId = is_array($notification) && is_string($notification['id'] ?? null)
            ? $notification['id']
            : $testId;
        $this->notificationStatus = (string) $result['message'];

        if (($result['success'] ?? false) === true) {
            $this->toastSuccess($this->notificationStatus, 'Notifications OK');

            return;
        }

        $this->toastWarning($this->notificationStatus, 'Notifications fallback active');
    }

    public function testFlashlightExample(): void
    {
        if ($this->nativeDebugActionDenied('native_device', 'Device unavailable', 'flashlight')) {
            return;
        }

        $result = $this->devices->toggleFlashlight();
        $this->flashlightStatus = $result['message'];

        if ($result['success']) {
            $this->toastSuccess($this->flashlightStatus, 'Flashlight OK');

            return;
        }

        $this->toastWarning($this->flashlightStatus, 'Flashlight unavailable');
    }

    public function testVibrationExample(): void
    {
        if ($this->nativeDebugActionDenied('native_device', 'Device unavailable', 'vibration')) {
            return;
        }

        $result = $this->devices->vibrate();
        $this->vibrationStatus = $result['message'];

        if ($result['success']) {
            $this->toastSuccess($this->vibrationStatus, 'Vibration OK');

            return;
        }

        $this->toastWarning($this->vibrationStatus, 'Vibration unavailable');
    }

    public function testHapticsExample(): void
    {
        if ($this->nativeDebugActionDenied('native_device', 'Device unavailable', 'haptics')) {
            return;
        }

        $result = $this->devices->hapticFeedback();
        $this->hapticStatus = $result['message'];

        if ($result['success']) {
            $this->toastSuccess($this->hapticStatus, 'Haptics OK');

            return;
        }

        $this->toastWarning($this->hapticStatus, 'Haptics unavailable');
    }

    public function shareDebugSnapshot(): void
    {
        if ($this->nativeDebugActionDenied('native_share', 'Share unavailable', 'share')) {
            return;
        }

        $result = $this->shares->shareText(
            title: 'Mobile Lara diagnostics JSON',
            text: $this->diagnostics->toJson(),
        );

        $this->rememberShareResult($result, 'Debug shared', 'Share unavailable');
    }

    public function exportDiagnosticsJson(): StreamedResponse
    {
        $this->diagnosticsStatus = 'Diagnostics JSON export prepared.';
        $json = $this->diagnostics->toJson();

        return response()->streamDownload(
            static function () use ($json): void {
                echo $json;
            },
            'mobile-lara-diagnostics.json',
            ['Content-Type' => 'application/json']
        );
    }

    public function shareReportPlaceholder(): void
    {
        if ($this->nativeDebugActionDenied('native_share', 'Share unavailable', 'share')) {
            return;
        }

        $result = $this->shares->shareUrl(
            title: 'Mobile Lara report placeholder',
            text: 'Share this placeholder report link while the server-side report workflow is being connected.',
            url: route('mobile.debug'),
        );

        $this->rememberShareResult($result, 'Report shared', 'Share unavailable');
    }

    public function openExternalBrowserExample(): void
    {
        if ($this->nativeDebugActionDenied('native_browser', 'Browser unavailable', 'browser')) {
            return;
        }

        $result = $this->browsers->openExternalUrl((string) config('mobile_browser.links.external_url'));

        $this->rememberBrowserResult($result, 'Browser opened', 'Browser unavailable');
    }

    public function openInAppBrowserExample(): void
    {
        if ($this->nativeDebugActionDenied('native_browser', 'Browser unavailable', 'browser')) {
            return;
        }

        $result = $this->browsers->openInAppUrl((string) config('mobile_browser.links.in_app_url'));

        $this->rememberBrowserResult($result, 'Browser opened', 'Browser unavailable');
    }

    public function openOAuthBrowserExample(): void
    {
        if ($this->nativeDebugActionDenied('native_browser', 'Browser unavailable', 'browser')) {
            return;
        }

        $result = $this->browsers->openOAuthUrl((string) config('mobile_browser.links.oauth_url'));

        $this->rememberBrowserResult($result, 'Browser opened', 'Browser unavailable');
    }

    public function openPrivacyPolicyBrowserExample(): void
    {
        if ($this->nativeDebugActionDenied('native_browser', 'Browser unavailable', 'browser')) {
            return;
        }

        $result = $this->browsers->openPrivacyPolicy();

        $this->rememberBrowserResult($result, 'Privacy opened', 'Browser unavailable');
    }

    public function openSupportCenterBrowserExample(): void
    {
        if ($this->nativeDebugActionDenied('native_browser', 'Browser unavailable', 'browser')) {
            return;
        }

        $result = $this->browsers->openSupportCenter();

        $this->rememberBrowserResult($result, 'Support opened', 'Browser unavailable');
    }

    public function openBillingPortalPlaceholderExample(): void
    {
        if ($this->nativeDebugActionDenied('native_browser', 'Browser unavailable', 'browser')) {
            return;
        }

        $result = $this->browsers->openBillingPortalPlaceholder();

        $this->rememberBrowserResult($result, 'Billing opened', 'Browser unavailable');
    }

    #[OnNative(PhotoTaken::class)]
    public function handleDebugPhotoTaken(string $path, string $mimeType = 'image/jpeg', ?string $id = null): void
    {
        if (! $this->matchesPendingCameraTest($id)) {
            return;
        }

        if ($this->nativeDebugActionDenied('native_camera', 'Camera unavailable', 'camera')) {
            $this->pendingCameraTestId = null;

            return;
        }

        $this->pendingCameraTestId = null;
        $this->cameraStatus = 'Camera returned '.basename($path)." ({$mimeType}).";
        $this->toastSuccess($this->cameraStatus, 'Camera OK');
    }

    #[OnNative(PhotoCancelled::class)]
    public function handleDebugPhotoCancelled(bool $cancelled = true, ?string $id = null): void
    {
        if (! $cancelled || ! $this->matchesPendingCameraTest($id)) {
            return;
        }

        $this->pendingCameraTestId = null;
        $this->cameraStatus = 'Camera test cancelled.';
        $this->toastInfo($this->cameraStatus, 'Camera cancelled');
    }

    #[OnNative(PermissionDenied::class)]
    public function handleDebugPermissionDenied(string $action, ?string $id = null): void
    {
        if (! $this->matchesPendingCameraTest($id)) {
            return;
        }

        $this->pendingCameraTestId = null;
        $this->cameraStatus = "Native {$action} permission denied.";
        $this->toastError($this->cameraStatus, 'Permission denied');
    }

    #[OnNative(TokenGenerated::class)]
    public function handleDebugPushTokenGenerated(string $token, ?string $id = null): void
    {
        if (! $this->matchesPendingNotificationTest($id)) {
            return;
        }

        if ($this->nativeDebugActionDenied('notifications', 'Notifications unavailable', 'notifications', 'notifications.view')) {
            $this->pendingNotificationTestId = null;

            return;
        }

        $this->pendingNotificationTestId = null;
        $this->notificationStatus = 'Push token generated: '.$this->shortToken($token).'.';
        $this->toastSuccess($this->notificationStatus, 'Notifications OK');
    }

    #[On('debug-toast-action')]
    public function recordToastAction(string $status): void
    {
        $this->toastActionStatus = $status;
    }

    public function render(): View
    {
        return view('livewire.mobile.debug', [
            'browserActions' => $this->browserActions(),
            'debugRows' => $this->debugRows(),
            'diagnosticsRows' => $this->diagnostics->summaryRows(),
            'dialogActions' => $this->dialogActions(),
            'dialogResultRows' => $this->dialogResultRows(),
            'shareActions' => $this->shareActions(),
            'testActions' => $this->testActions(),
            'testStatusRows' => $this->testStatusRows(),
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
        $networkStatus = $this->networkState->status();
        $deviceSnapshot = $this->devices->snapshot();
        $notificationCapabilities = $this->localNotifications->capabilities();

        return [
            [
                'key' => 'app-version',
                'label' => 'App version',
                'value' => $deviceSnapshot['app_version'],
            ],
            [
                'key' => 'laravel-version',
                'label' => 'Laravel version',
                'value' => app()->version(),
            ],
            [
                'key' => 'livewire',
                'label' => 'Livewire',
                'value' => '4.x',
            ],
            [
                'key' => 'nativephp-status',
                'label' => 'NativePHP status',
                'value' => $this->nativePhpStatus(),
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
                'key' => 'device-model',
                'label' => 'Device model',
                'value' => $deviceSnapshot['device_model'],
            ],
            [
                'key' => 'os-version',
                'label' => 'OS version',
                'value' => $deviceSnapshot['os_version'],
            ],
            [
                'key' => 'battery-status',
                'label' => 'Battery status',
                'value' => $deviceSnapshot['battery_status'],
            ],
            [
                'key' => 'charging-status',
                'label' => 'Charging status',
                'value' => $deviceSnapshot['charging_status'],
            ],
            [
                'key' => 'network-status',
                'label' => 'Network status',
                'value' => $networkStatus->stateLabel(),
            ],
            [
                'key' => 'network-type',
                'label' => 'Connection type',
                'value' => $networkStatus->connectionTypeLabel(),
            ],
            [
                'key' => 'network-metered',
                'label' => 'Metered connection',
                'value' => $networkStatus->meteredLabel(),
            ],
            [
                'key' => 'network-source',
                'label' => 'Network source',
                'value' => $networkStatus->sourceLabel(),
            ],
            [
                'key' => 'database-path',
                'label' => 'Database path placeholder',
                'value' => $this->databasePath(),
            ],
            [
                'key' => 'queue-status',
                'label' => 'Queue status placeholder',
                'value' => $this->queueStatus(),
            ],
            [
                'key' => 'local-notification-driver',
                'label' => 'Local notification driver',
                'value' => $notificationCapabilities['native']
                    ? "NativePHP ({$notificationCapabilities['driver']})"
                    : "Placeholder ({$notificationCapabilities['driver']})",
            ],
        ];
    }

    /**
     * @return list<array{label: string, action: string, variant: string}>
     */
    private function dialogActions(): array
    {
        return $this->filterNativeDebugActions([
            [
                'label' => 'Alert',
                'action' => 'showAlertExample',
                'variant' => 'primary',
                'feature' => 'native_dialogs',
            ],
            [
                'label' => 'Confirm',
                'action' => 'showConfirmExample',
                'variant' => 'secondary',
                'feature' => 'native_dialogs',
            ],
            [
                'label' => 'Prompt',
                'action' => 'showPromptExample',
                'variant' => 'secondary',
                'feature' => 'native_dialogs',
            ],
            [
                'label' => 'Toast',
                'action' => 'showToastExample',
                'variant' => 'accent',
                'feature' => 'native_dialogs',
            ],
            [
                'label' => 'Snackbar',
                'action' => 'showSnackbarExample',
                'variant' => 'ghost',
                'feature' => 'native_dialogs',
            ],
        ]);
    }

    /**
     * @return list<array{label: string, action: string, variant: string}>
     */
    private function testActions(): array
    {
        return $this->filterNativeDebugActions([
            [
                'label' => 'Test dialogs',
                'action' => 'showAlertExample',
                'variant' => 'primary',
                'feature' => 'native_dialogs',
            ],
            [
                'label' => 'Test storage',
                'action' => 'testStorageExample',
                'variant' => 'secondary',
                'feature' => 'native_secure_storage',
            ],
            [
                'label' => 'Test camera',
                'action' => 'testCameraExample',
                'variant' => 'secondary',
                'feature' => 'native_camera',
            ],
            [
                'label' => 'Test notifications',
                'action' => 'testNotificationsExample',
                'variant' => 'accent',
                'feature' => 'notifications',
                'permission' => 'notifications.view',
            ],
            [
                'label' => 'Test flashlight',
                'action' => 'testFlashlightExample',
                'variant' => 'secondary',
                'feature' => 'native_device',
            ],
            [
                'label' => 'Test vibration',
                'action' => 'testVibrationExample',
                'variant' => 'secondary',
                'feature' => 'native_device',
            ],
            [
                'label' => 'Test haptics',
                'action' => 'testHapticsExample',
                'variant' => 'ghost',
                'feature' => 'native_device',
            ],
        ]);
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
     * @return list<array{label: string, action: string, variant: string}>
     */
    private function browserActions(): array
    {
        return $this->filterNativeDebugActions([
            [
                'label' => 'External URL',
                'action' => 'openExternalBrowserExample',
                'variant' => 'primary',
                'feature' => 'native_browser',
            ],
            [
                'label' => 'In-app link',
                'action' => 'openInAppBrowserExample',
                'variant' => 'secondary',
                'feature' => 'native_browser',
            ],
            [
                'label' => 'OAuth link',
                'action' => 'openOAuthBrowserExample',
                'variant' => 'accent',
                'feature' => 'native_browser',
            ],
            [
                'label' => 'Privacy policy',
                'action' => 'openPrivacyPolicyBrowserExample',
                'variant' => 'secondary',
                'feature' => 'native_browser',
            ],
            [
                'label' => 'Support center',
                'action' => 'openSupportCenterBrowserExample',
                'variant' => 'secondary',
                'feature' => 'native_browser',
            ],
            [
                'label' => 'Billing portal',
                'action' => 'openBillingPortalPlaceholderExample',
                'variant' => 'ghost',
                'feature' => 'native_browser',
            ],
        ]);
    }

    /**
     * @return list<array{label: string, action: string, variant: string}>
     */
    private function shareActions(): array
    {
        return $this->filterNativeDebugActions([
            [
                'label' => 'Share debug snapshot',
                'action' => 'shareDebugSnapshot',
                'variant' => 'primary',
                'feature' => 'native_share',
            ],
            [
                'label' => 'Share report placeholder',
                'action' => 'shareReportPlaceholder',
                'variant' => 'secondary',
                'feature' => 'native_share',
            ],
        ]);
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

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    private function testStatusRows(): array
    {
        $rows = [];

        foreach ([
            'storage' => ['Storage', $this->storageStatus],
            'camera' => ['Camera', $this->cameraStatus],
            'notifications' => ['Notifications', $this->notificationStatus],
            'flashlight' => ['Flashlight', $this->flashlightStatus],
            'vibration' => ['Vibration', $this->vibrationStatus],
            'haptics' => ['Haptics', $this->hapticStatus],
            'browser' => ['Browser', $this->browserStatus],
            'share' => ['Share', $this->shareStatus],
            'diagnostics' => ['Diagnostics', $this->diagnosticsStatus],
        ] as $key => [$label, $value]) {
            if (! is_string($value) || $value === '') {
                continue;
            }

            $rows[] = [
                'key' => $key,
                'label' => $label,
                'value' => $value,
            ];
        }

        return $rows;
    }

    /**
     * @param  array{success: bool, message: string}  $result
     */
    private function rememberBrowserResult(array $result, string $successTitle, string $failureTitle): void
    {
        $this->browserStatus = $result['message'];

        if ($result['success']) {
            $this->toastSuccess($result['message'], $successTitle);

            return;
        }

        $this->toastWarning($result['message'], $failureTitle);
    }

    /**
     * @param  array{success: bool, message: string}  $result
     */
    private function rememberShareResult(array $result, string $successTitle, string $failureTitle): void
    {
        $this->shareStatus = $result['message'];

        if ($result['success']) {
            $this->toastSuccess($result['message'], $successTitle);

            return;
        }

        $this->toastWarning($result['message'], $failureTitle);
    }

    private function nativeBridgeIsAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    private function nativePhpStatus(): string
    {
        $version = InstalledVersions::isInstalled('nativephp/mobile')
            ? (string) InstalledVersions::getPrettyVersion('nativephp/mobile')
            : 'installed';

        if (config('nativephp-internal.running') === true) {
            return "Native runtime active ({$version})";
        }

        if (getenv('JUMP_BRIDGE_PORT') !== false) {
            return "Jump bridge configured ({$version})";
        }

        return function_exists('nativephp_call')
            ? "Browser fallback, bridge helper loaded ({$version})"
            : "Browser fallback ({$version})";
    }

    private function databasePath(): string
    {
        $connection = (string) config('database.default', 'sqlite');
        $database = config("database.connections.{$connection}.database");

        if (! is_scalar($database) || trim((string) $database) === '') {
            return "{$connection}: path placeholder not configured";
        }

        return "{$connection}: ".(string) $database;
    }

    private function queueStatus(): string
    {
        $connection = (string) config('queue.default', 'sync');
        $queue = (string) config("queue.connections.{$connection}.queue", 'default');

        return "{$connection} connection, {$queue} queue";
    }

    private function matchesPendingCameraTest(?string $id): bool
    {
        return is_string($id)
            && is_string($this->pendingCameraTestId)
            && hash_equals($this->pendingCameraTestId, $id);
    }

    private function matchesPendingNotificationTest(?string $id): bool
    {
        return is_string($id)
            && is_string($this->pendingNotificationTestId)
            && hash_equals($this->pendingNotificationTestId, $id);
    }

    private function shortToken(string $token): string
    {
        $token = trim($token);

        if (Str::length($token) <= 12) {
            return $token;
        }

        return Str::substr($token, 0, 6).'...'.Str::substr($token, -4);
    }

    /**
     * @param  list<array{label: string, action: string, variant: string, feature?: string, permission?: string}>  $actions
     * @return list<array{label: string, action: string, variant: string}>
     */
    private function filterNativeDebugActions(array $actions): array
    {
        return array_values(array_map(
            static fn (array $action): array => [
                'label' => $action['label'],
                'action' => $action['action'],
                'variant' => $action['variant'],
            ],
            array_filter(
                $actions,
                fn (array $action): bool => $this->mobileFeatureAllowed(
                    (string) ($action['feature'] ?? 'settings'),
                    is_string($action['permission'] ?? null) ? $action['permission'] : null,
                ),
            ),
        ));
    }

    private function nativeDebugActionDenied(string $feature, string $title, string $statusKey, ?string $permission = null): bool
    {
        $decision = $this->mobileFeatureDecision($feature, $permission);

        if ($decision['allowed']) {
            return false;
        }

        $this->setNativeDebugStatus($statusKey, $decision['message']);
        $this->toastWarning($decision['message'], $title);

        return true;
    }

    private function setNativeDebugStatus(string $statusKey, string $message): void
    {
        match ($statusKey) {
            'browser' => $this->browserStatus = $message,
            'camera' => $this->cameraStatus = $message,
            'dialog' => $this->dialogStatus = $message,
            'flashlight' => $this->flashlightStatus = $message,
            'haptics' => $this->hapticStatus = $message,
            'notifications' => $this->notificationStatus = $message,
            'share' => $this->shareStatus = $message,
            'storage' => $this->storageStatus = $message,
            'vibration' => $this->vibrationStatus = $message,
            default => null,
        };
    }
}
