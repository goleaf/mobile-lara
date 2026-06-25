<?php

namespace App\Services\MobileLocal;

use Illuminate\Filesystem\Filesystem;

final class MobileLocalDatabase
{
    public function __construct(
        private readonly Filesystem $files,
    ) {}

    public function connection(): string
    {
        return (string) config('mobile_local.connection', 'mobile_local');
    }

    public function path(): string
    {
        $connection = $this->connection();

        return (string) config("database.connections.{$connection}.database", config('mobile_local.database'));
    }

    public function migrationPath(): string
    {
        return (string) config('mobile_local.migrations.path', database_path('migrations/mobile-local'));
    }

    public function ensureFileExists(): string
    {
        $databasePath = $this->path();

        if ($databasePath === ':memory:') {
            return $databasePath;
        }

        $directory = dirname($databasePath);

        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if (! $this->files->exists($databasePath)) {
            $this->files->put($databasePath, '');
        }

        return $databasePath;
    }
}
