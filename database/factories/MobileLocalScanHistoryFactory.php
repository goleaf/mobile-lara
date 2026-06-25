<?php

namespace Database\Factories;

use App\Models\MobileLocalScanHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalScanHistory>
 */
class MobileLocalScanHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'scan_type' => MobileLocalScanHistory::TYPE_QR,
            'raw_value' => fake()->url(),
            'parsed_value' => [
                'type' => 'url',
                'value' => fake()->url(),
            ],
            'action_result' => 'Stored locally from scanner.',
            'status' => MobileLocalScanHistory::STATUS_CAPTURED,
            'created_at' => now(),
        ];
    }

    public function barcode(): self
    {
        return $this->state(fn (): array => [
            'scan_type' => 'ean13',
            'raw_value' => fake()->ean13(),
            'parsed_value' => [
                'type' => 'number',
                'value' => fake()->ean13(),
            ],
        ]);
    }

    public function actioned(): self
    {
        return $this->state(fn (): array => [
            'status' => MobileLocalScanHistory::STATUS_ACTIONED,
            'action_result' => 'Opened scanned URL.',
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (): array => [
            'status' => MobileLocalScanHistory::STATUS_FAILED,
            'action_result' => 'Unable to process scanned value.',
        ]);
    }

    public function ignored(): self
    {
        return $this->state(fn (): array => [
            'status' => MobileLocalScanHistory::STATUS_IGNORED,
            'action_result' => 'Scan was ignored by the user.',
        ]);
    }
}
