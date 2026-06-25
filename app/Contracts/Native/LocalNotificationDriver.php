<?php

namespace App\Contracts\Native;

use Carbon\CarbonInterface;

interface LocalNotificationDriver
{
    public function driverName(): string;

    public function isNative(): bool;

    public function isAvailable(): bool;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function schedule(
        string $id,
        string $title,
        string $body,
        CarbonInterface $scheduledAt,
        string $type,
        array $data = [],
        ?string $deepLink = null,
    ): array;

    /**
     * @return array<string, mixed>
     */
    public function cancel(string $id): array;

    /**
     * @return array<string, mixed>
     */
    public function listScheduled(int $limit = 50): array;

    /**
     * @return array<string, mixed>
     */
    public function testNotification(?string $id = null): array;
}
