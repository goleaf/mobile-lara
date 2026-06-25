<?php

namespace App\Services\MobileLocal;

use Carbon\CarbonImmutable;

final readonly class MobileLocalHealthReport
{
    public function __construct(
        public bool $ok,
        public string $connection,
        public string $databasePath,
        public string $migrationPath,
        public string $message,
        public CarbonImmutable $checkedAt,
    ) {}

    /**
     * @return array{
     *     ok: bool,
     *     connection: string,
     *     database_path: string,
     *     migration_path: string,
     *     message: string,
     *     checked_at: string
     * }
     */
    public function toArray(): array
    {
        return [
            'ok' => $this->ok,
            'connection' => $this->connection,
            'database_path' => $this->databasePath,
            'migration_path' => $this->migrationPath,
            'message' => $this->message,
            'checked_at' => $this->checkedAt->toIso8601String(),
        ];
    }
}
