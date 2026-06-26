<?php

namespace Database\Factories;

use App\Models\MobileDeviceSession;
use App\Models\MobileDiagnosticReport;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileDiagnosticReport>
 */
class MobileDiagnosticReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'mobile_device_session_id' => MobileDeviceSession::factory(),
            'app_version' => '1.0.0',
            'api_base_url' => 'https://api-admin.test/api/v1/mobile',
            'support_ticket_id' => null,
            'redactions_applied' => ['tokens', 'headers', 'payloads'],
            'snapshot' => [
                'app' => ['app_version' => '1.0.0'],
                'network' => ['state' => 'Online'],
                'sync' => ['failed_actions' => 0],
            ],
            'failed_sync_actions_count' => 0,
            'generated_at' => now(),
            'received_at' => now(),
        ];
    }
}
