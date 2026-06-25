<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalHealthCheck;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Throwable;

final class MobileLocalStorageHealthCheck
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    public function run(?string $value = null): MobileLocalHealthReport
    {
        $connection = $this->mobileLocalDatabase->connection();
        $databasePath = $this->mobileLocalDatabase->path();
        $migrationPath = $this->mobileLocalDatabase->migrationPath();
        $checkedAt = CarbonImmutable::now();

        try {
            $this->mobileLocalDatabase->ensureFileExists();

            $checkValue = $value ?: Str::uuid()->toString();
            $healthKey = (string) config('mobile_local.health.key', 'nativephp-mobile-local-storage');

            $record = MobileLocalHealthCheck::query()->updateOrCreate(
                ['check_key' => $healthKey],
                [
                    'check_value' => $checkValue,
                    'checked_at' => $checkedAt,
                ],
            );

            $freshRecord = MobileLocalHealthCheck::query()
                ->whereKey($record->getKey())
                ->first();

            if ($freshRecord?->check_value !== $checkValue) {
                return new MobileLocalHealthReport(
                    ok: false,
                    connection: $connection,
                    databasePath: $databasePath,
                    migrationPath: $migrationPath,
                    message: 'Mobile local SQLite health check wrote data but could not read the same value back.',
                    checkedAt: $checkedAt,
                );
            }

            return new MobileLocalHealthReport(
                ok: true,
                connection: $connection,
                databasePath: $databasePath,
                migrationPath: $migrationPath,
                message: 'Mobile local SQLite storage can write and read data.',
                checkedAt: $checkedAt,
            );
        } catch (Throwable $exception) {
            return new MobileLocalHealthReport(
                ok: false,
                connection: $connection,
                databasePath: $databasePath,
                migrationPath: $migrationPath,
                message: $exception->getMessage(),
                checkedAt: $checkedAt,
            );
        }
    }
}
