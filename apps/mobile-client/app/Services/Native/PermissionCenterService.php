<?php

namespace App\Services\Native;

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Services\MobileAccess\MobileAccessPolicy;
use Illuminate\Support\Str;
use Native\Mobile\Biometrics;
use Nativephp\AllPermissionHandler\AllPermissionHandler;
use Nativephp\AllPermissionHandler\Enums\PermissionStatus;
use Throwable;

final class PermissionCenterService
{
    /**
     * @var array<string, array{label: string, permission: string, feature: string, ability?: string, badge: string, description: string, explanation: string, request_label: string, driver: string}>
     */
    private const RUNTIME_PERMISSIONS = [
        'camera' => [
            'label' => 'Camera',
            'permission' => 'camera',
            'feature' => 'native_camera',
            'badge' => 'Media',
            'description' => 'Required for photo capture, video capture, avatar updates, and QR scanner previews.',
            'explanation' => 'The app asks for camera access only when a camera-based workflow needs it.',
            'request_label' => 'Request camera permission',
            'driver' => 'AllPermissionHandler.Camera',
        ],
        'microphone' => [
            'label' => 'Microphone',
            'permission' => 'microphone',
            'feature' => 'native_microphone',
            'badge' => 'Audio',
            'description' => 'Required for voice notes and video capture with sound.',
            'explanation' => 'The microphone prompt is separate from camera access and can be recovered from OS settings after denial.',
            'request_label' => 'Request microphone permission',
            'driver' => 'AllPermissionHandler.Microphone',
        ],
        'location' => [
            'label' => 'Location',
            'permission' => 'locationWhenInUse',
            'feature' => 'native_location',
            'badge' => 'Location',
            'description' => 'Required for check-ins, nearby workflows, and location-aware sync context.',
            'explanation' => 'Location is requested for foreground use only and should stay optional until a feature needs a position fix.',
            'request_label' => 'Request location permission',
            'driver' => 'AllPermissionHandler.LocationWhenInUse',
        ],
        'notifications' => [
            'label' => 'Notifications',
            'permission' => 'notification',
            'feature' => 'notifications',
            'ability' => 'notifications.view',
            'badge' => 'Alerts',
            'description' => 'Required for notification previews, reminders, and future push enrollment.',
            'explanation' => 'Notifications can be denied independently from local inbox storage; use settings recovery if the prompt cannot be shown again.',
            'request_label' => 'Request notification permission',
            'driver' => 'AllPermissionHandler.Notification',
        ],
        'files' => [
            'label' => 'Storage/files',
            'permission' => 'storage',
            'feature' => 'native_files',
            'badge' => 'Storage',
            'description' => 'Required for importing, exporting, sharing, and managing local app files.',
            'explanation' => 'The app can use its local sandbox without broad storage access; OS storage permission is only needed for provider-level file access.',
            'request_label' => 'Request storage permission',
            'driver' => 'AllPermissionHandler.Storage',
        ],
    ];

    public function __construct(
        private readonly AllPermissionHandler $permissions,
        private readonly Biometrics $biometrics,
        private readonly CameraService $cameras,
        private readonly AudioRecordingService $audioRecordings,
        private readonly LocationService $locations,
        private readonly FileService $files,
        private readonly MobileNetworkState $network,
        private readonly SystemService $systems,
        private readonly MobileAccessPolicy $accessPolicy,
    ) {}

    /**
     * @return list<array{
     *     key: string,
     *     label: string,
     *     status: string,
     *     status_variant: string,
     *     badge: string,
     *     description: string,
     *     explanation: string,
     *     request_label: string,
     *     can_request: bool,
     *     recovery_label: string,
     *     recovery_note: string,
     *     details: list<array{label: string, value: string}>
     * }>
     */
    public function permissions(): array
    {
        return [
            $this->runtimePermissionRow('camera'),
            $this->runtimePermissionRow('microphone'),
            $this->runtimePermissionRow('location'),
            $this->runtimePermissionRow('notifications'),
            $this->biometricRow(),
            $this->runtimePermissionRow('files'),
            $this->networkRow(),
        ];
    }

    /**
     * @return array{success: bool, key: string, label: string, status: string, message: string}
     */
    public function request(string $key): array
    {
        $key = $this->normalizeKey($key);

        return match ($key) {
            'biometrics' => $this->requestBiometrics(),
            'network' => $this->refreshNetworkStatus(),
            default => $this->requestRuntimePermission($key),
        };
    }

    /**
     * @return array{
     *     key: string,
     *     label: string,
     *     status: string,
     *     status_variant: string,
     *     badge: string,
     *     description: string,
     *     explanation: string,
     *     request_label: string,
     *     can_request: bool,
     *     recovery_label: string,
     *     recovery_note: string,
     *     details: list<array{label: string, value: string}>
     * }
     */
    private function runtimePermissionRow(string $key): array
    {
        $definition = self::RUNTIME_PERMISSIONS[$key];
        $nativeRuntimeAvailable = $this->systems->nativeRuntimeAvailable();
        $decision = $this->policyDecision($key);

        if (! $decision['allowed']) {
            return [
                'key' => $key,
                'label' => $definition['label'],
                'status' => 'Blocked by policy',
                'status_variant' => 'warning',
                'badge' => $definition['badge'],
                'description' => $definition['description'],
                'explanation' => $decision['message'],
                'request_label' => 'Disabled by policy',
                'can_request' => false,
                'recovery_label' => $this->systems->platformSettingsLabel(),
                'recovery_note' => 'Admin/API policy must enable this feature before a native permission prompt is useful.',
                'details' => $this->policyDetails($decision, $this->runtimePermissionDetails($key, $definition)),
            ];
        }

        $status = $this->checkedStatus($definition['permission']);

        return [
            'key' => $key,
            'label' => $definition['label'],
            'status' => $status instanceof PermissionStatus
                ? $this->statusLabel($status)
                : ($nativeRuntimeAvailable ? 'Unknown' : 'Browser fallback'),
            'status_variant' => $status instanceof PermissionStatus
                ? $this->statusVariant($status)
                : ($nativeRuntimeAvailable ? 'neutral' : 'warning'),
            'badge' => $definition['badge'],
            'description' => $definition['description'],
            'explanation' => $definition['explanation'],
            'request_label' => $definition['request_label'],
            'can_request' => true,
            'recovery_label' => $this->systems->platformSettingsLabel(),
            'recovery_note' => $this->systems->permissionRecoveryDescription(),
            'details' => $this->runtimePermissionDetails($key, $definition),
        ];
    }

    /**
     * @return array{
     *     key: string,
     *     label: string,
     *     status: string,
     *     status_variant: string,
     *     badge: string,
     *     description: string,
     *     explanation: string,
     *     request_label: string,
     *     can_request: bool,
     *     recovery_label: string,
     *     recovery_note: string,
     *     details: list<array{label: string, value: string}>
     * }
     */
    private function biometricRow(): array
    {
        $nativeRuntimeAvailable = $this->systems->nativeRuntimeAvailable();
        $decision = $this->policyDecision('biometrics');

        if (! $decision['allowed']) {
            return [
                'key' => 'biometrics',
                'label' => 'Biometrics',
                'status' => 'Blocked by policy',
                'status_variant' => 'warning',
                'badge' => 'Secure',
                'description' => 'Required for Face ID, Touch ID, fingerprint, and protected app unlock confirmation.',
                'explanation' => $decision['message'],
                'request_label' => 'Disabled by policy',
                'can_request' => false,
                'recovery_label' => $this->systems->platformSettingsLabel(),
                'recovery_note' => 'Admin/API policy must enable biometric unlock before the native prompt is useful.',
                'details' => $this->policyDetails($decision, [
                    $this->detail('Driver', 'NativePHP Biometric.Prompt'),
                    $this->detail('Runtime', $nativeRuntimeAvailable ? 'NativePHP available' : 'Browser fallback'),
                    $this->detail('Android manifest toggle', $this->configToggleLabel(config('nativephp.permissions.biometric'))),
                ]),
            ];
        }

        return [
            'key' => 'biometrics',
            'label' => 'Biometrics',
            'status' => $nativeRuntimeAvailable ? 'Prompt available' : 'Browser fallback',
            'status_variant' => $nativeRuntimeAvailable ? 'success' : 'warning',
            'badge' => 'Secure',
            'description' => 'Required for Face ID, Touch ID, fingerprint, and protected app unlock confirmation.',
            'explanation' => 'Biometric unlock uses the device security prompt and falls back to PIN or normal authentication when unavailable.',
            'request_label' => 'Test biometric prompt',
            'can_request' => true,
            'recovery_label' => $this->systems->platformSettingsLabel(),
            'recovery_note' => 'Biometric enrollment and recovery are controlled by the device security settings.',
            'details' => [
                $this->detail('Driver', 'NativePHP Biometric.Prompt'),
                $this->detail('Runtime', $nativeRuntimeAvailable ? 'NativePHP available' : 'Browser fallback'),
                $this->detail('Android manifest toggle', $this->configToggleLabel(config('nativephp.permissions.biometric'))),
            ],
        ];
    }

    /**
     * @return array{
     *     key: string,
     *     label: string,
     *     status: string,
     *     status_variant: string,
     *     badge: string,
     *     description: string,
     *     explanation: string,
     *     request_label: string,
     *     can_request: bool,
     *     recovery_label: string,
     *     recovery_note: string,
     *     details: list<array{label: string, value: string}>
     * }
     */
    private function networkRow(): array
    {
        $status = $this->network->status();

        return [
            'key' => 'network',
            'label' => 'Network status',
            'status' => $status->stateLabel(),
            'status_variant' => $status->variant(),
            'badge' => 'Connectivity',
            'description' => 'Required for sync, API calls, remote auth, and remote device-session checks.',
            'explanation' => 'Network state does not use an OS permission prompt; the button refreshes the detected connection state.',
            'request_label' => 'Refresh network status',
            'can_request' => true,
            'recovery_label' => $this->systems->platformSettingsLabel(),
            'recovery_note' => 'Use system settings to recover Wi-Fi, cellular data, VPN, or low-data-mode restrictions.',
            'details' => [
                $this->detail('Connection', $status->connectionTypeLabel()),
                $this->detail('Metered', $status->meteredLabel()),
                $this->detail('Constraint', $status->constrainedLabel()),
                $this->detail('Source', $status->sourceLabel()),
            ],
        ];
    }

    /**
     * @param  array{label: string, permission: string, feature: string, ability?: string, badge: string, description: string, explanation: string, request_label: string, driver: string}  $definition
     * @return list<array{label: string, value: string}>
     */
    private function runtimePermissionDetails(string $key, array $definition): array
    {
        return [
            $this->detail('Permission key', $definition['permission']),
            $this->detail('Driver', $definition['driver']),
            $this->detail('Runtime', $this->systems->nativeRuntimeAvailable() ? 'NativePHP available' : 'Browser fallback'),
            $this->detail('Feature bridge', $this->featureBridgeLabel($key)),
        ];
    }

    /**
     * @return array{success: bool, key: string, label: string, status: string, message: string}
     */
    private function requestRuntimePermission(string $key): array
    {
        $definition = self::RUNTIME_PERMISSIONS[$key] ?? null;

        if (! is_array($definition)) {
            return $this->result(false, $key, 'Unknown permission', 'Unknown', 'Unknown permission center item.');
        }

        $decision = $this->policyDecision($key);

        if (! $decision['allowed']) {
            return $this->result(false, $key, $definition['label'], 'Blocked by policy', $decision['message']);
        }

        if (! $this->nativePermissionHandlerAvailable()) {
            return $this->result(
                false,
                $key,
                $definition['label'],
                'Browser fallback',
                "{$definition['label']} permission requests are unavailable in this browser runtime.",
            );
        }

        try {
            $status = $this->permissions->request($definition['permission']);
        } catch (Throwable) {
            return $this->result(
                false,
                $key,
                $definition['label'],
                'Unknown',
                "Unable to request {$definition['label']} permission.",
            );
        }

        return $this->result(
            $this->statusAllowsFeatureUse($status),
            $key,
            $definition['label'],
            $this->statusLabel($status),
            $this->requestMessage($definition['label'], $status),
        );
    }

    /**
     * @return array{success: bool, key: string, label: string, status: string, message: string}
     */
    private function requestBiometrics(): array
    {
        $decision = $this->policyDecision('biometrics');

        if (! $decision['allowed']) {
            return $this->result(false, 'biometrics', 'Biometrics', 'Blocked by policy', $decision['message']);
        }

        if (! $this->systems->nativeRuntimeAvailable()) {
            return $this->result(
                false,
                'biometrics',
                'Biometrics',
                'Browser fallback',
                'Biometric prompts are unavailable in this browser runtime.',
            );
        }

        try {
            $started = $this->biometrics
                ->prompt()
                ->id((string) Str::uuid())
                ->remember()
                ->prompt();
        } catch (Throwable) {
            $started = false;
        }

        return $this->result(
            $started,
            'biometrics',
            'Biometrics',
            $started ? 'Prompt started' : 'Unavailable',
            $started ? 'Biometric prompt started.' : 'Unable to start the biometric prompt.',
        );
    }

    /**
     * @return array{success: bool, key: string, label: string, status: string, message: string}
     */
    private function refreshNetworkStatus(): array
    {
        $status = $this->network->status();

        return $this->result(
            true,
            'network',
            'Network status',
            $status->stateLabel(),
            'Network status refreshed: '.$status->summary().'.',
        );
    }

    private function checkedStatus(string $permission): ?PermissionStatus
    {
        if (! $this->nativePermissionHandlerAvailable()) {
            return null;
        }

        try {
            return $this->permissions->check($permission);
        } catch (Throwable) {
            return null;
        }
    }

    private function nativePermissionHandlerAvailable(): bool
    {
        return $this->systems->nativeRuntimeAvailable()
            && class_exists(AllPermissionHandler::class);
    }

    private function featureBridgeLabel(string $key): string
    {
        return match ($key) {
            'camera' => $this->cameras->isAvailable() ? 'Native camera available' : 'Native camera unavailable',
            'microphone' => $this->audioRecordings->isAvailable() ? 'Native microphone available' : 'Native microphone unavailable',
            'location' => $this->locations->isAvailable() ? 'Native geolocation available' : 'Native geolocation unavailable',
            'notifications' => $this->configToggleLabel(config('nativephp.permissions.push_notifications')),
            'files' => $this->files->rootPath(),
            default => 'Not configured',
        };
    }

    /**
     * @return array{allowed: bool, feature: string, permission: string|null, reason: string|null, message: string, next_action: string|null, source: string}
     */
    private function policyDecision(string $key): array
    {
        $definition = self::RUNTIME_PERMISSIONS[$key] ?? null;

        if (is_array($definition)) {
            return $this->accessPolicy->decision(
                (string) $definition['feature'],
                is_string($definition['ability'] ?? null) ? $definition['ability'] : null,
            );
        }

        if ($key === 'biometrics') {
            return $this->accessPolicy->decision('native_biometrics');
        }

        return $this->accessPolicy->decision('settings');
    }

    /**
     * @param  array{allowed: bool, feature: string, permission: string|null, reason: string|null, message: string, next_action: string|null, source: string}  $decision
     * @param  list<array{label: string, value: string}>  $details
     * @return list<array{label: string, value: string}>
     */
    private function policyDetails(array $decision, array $details): array
    {
        return array_merge($details, [
            $this->detail('Policy source', $decision['source']),
            $this->detail('Policy feature', $decision['feature']),
            $this->detail('Policy reason', $decision['reason'] ?? 'not specified'),
        ]);
    }

    private function statusLabel(PermissionStatus $status): string
    {
        return match ($status) {
            PermissionStatus::Granted => 'Granted',
            PermissionStatus::Denied => 'Denied',
            PermissionStatus::Restricted => 'Restricted',
            PermissionStatus::Limited => 'Limited',
            PermissionStatus::PermanentlyDenied => 'Needs settings',
            PermissionStatus::Provisional => 'Provisional',
        };
    }

    private function statusVariant(PermissionStatus $status): string
    {
        return match ($status) {
            PermissionStatus::Granted => 'success',
            PermissionStatus::Limited, PermissionStatus::Provisional => 'accent',
            PermissionStatus::Denied => 'warning',
            PermissionStatus::Restricted, PermissionStatus::PermanentlyDenied => 'danger',
        };
    }

    private function statusAllowsFeatureUse(PermissionStatus $status): bool
    {
        return in_array($status, [
            PermissionStatus::Granted,
            PermissionStatus::Limited,
            PermissionStatus::Provisional,
        ], true);
    }

    private function requestMessage(string $label, PermissionStatus $status): string
    {
        return match ($status) {
            PermissionStatus::Granted => "{$label} permission is granted.",
            PermissionStatus::Denied => "{$label} permission was denied.",
            PermissionStatus::Restricted => "{$label} permission is restricted by the operating system.",
            PermissionStatus::Limited => "{$label} permission is limited; the feature can continue with reduced access.",
            PermissionStatus::PermanentlyDenied => "{$label} permission is permanently denied. Use app settings to recover it.",
            PermissionStatus::Provisional => "{$label} permission is provisional.",
        };
    }

    /**
     * @return array{success: bool, key: string, label: string, status: string, message: string}
     */
    private function result(bool $success, string $key, string $label, string $status, string $message): array
    {
        return [
            'success' => $success,
            'key' => $key,
            'label' => $label,
            'status' => $status,
            'message' => $message,
        ];
    }

    /**
     * @return array{label: string, value: string}
     */
    private function detail(string $label, string $value): array
    {
        return [
            'label' => $label,
            'value' => $value,
        ];
    }

    private function normalizeKey(string $key): string
    {
        $key = str($key)
            ->trim()
            ->lower()
            ->toString();

        return match ($key) {
            'storage', 'storage/files' => 'files',
            default => $key,
        };
    }

    private function configToggleLabel(mixed $value): string
    {
        return match ($value) {
            true => 'Enabled',
            false => 'Disabled',
            default => 'Not configured',
        };
    }
}
