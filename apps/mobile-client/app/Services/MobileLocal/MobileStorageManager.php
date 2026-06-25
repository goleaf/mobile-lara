<?php

namespace App\Services\MobileLocal;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;

final class MobileStorageManager
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
        private readonly Filesystem $files,
    ) {}

    /**
     * @return array{
     *     local_database_size: string,
     *     file_cache_size: string,
     *     export_path: string
     * }
     */
    public function snapshot(): array
    {
        return [
            'local_database_size' => $this->localDatabaseSizeLabel(),
            'file_cache_size' => $this->fileCacheSizeLabel(),
            'export_path' => $this->exportPlaceholderPath(),
        ];
    }

    public function localDatabaseSizeLabel(): string
    {
        $databasePath = $this->mobileLocalDatabase->ensureFileExists();

        if ($databasePath === ':memory:') {
            return 'Memory only';
        }

        return $this->formatBytes($this->fileSize($databasePath));
    }

    public function fileCacheSizeLabel(): string
    {
        return $this->formatBytes($this->directorySize($this->fileCachePath()));
    }

    public function clearFileCache(): void
    {
        $path = $this->ensureCleanableDirectory($this->fileCachePath());

        $this->cleanCacheDirectory($path);
    }

    public function resetLocalData(): void
    {
        $connection = $this->mobileLocalDatabase->connection();
        $databasePath = $this->mobileLocalDatabase->path();

        DB::purge($connection);

        if ($databasePath !== ':memory:') {
            foreach ($this->databaseFiles($databasePath) as $file) {
                if ($this->files->exists($file)) {
                    $this->files->delete($file);
                }
            }
        }

        $this->mobileLocalDatabase->ensureFileExists();
        $this->migrateLocalDatabase($connection);

        DB::purge($connection);
        DB::reconnect($connection);
    }

    public function exportPlaceholderPath(): string
    {
        return (string) config('mobile_local.storage.export_path', storage_path('app/mobile/mobile-local-export.json'));
    }

    public function exportPlaceholderMessage(): string
    {
        return "Export local data placeholder prepared at {$this->exportPlaceholderPath()}.";
    }

    private function fileCachePath(): string
    {
        return (string) config('mobile_local.storage.file_cache_path', storage_path('framework/cache/data'));
    }

    private function fileSize(string $path): int
    {
        if (! $this->files->exists($path)) {
            return 0;
        }

        return $this->files->size($path);
    }

    private function directorySize(string $path): int
    {
        if (! $this->files->isDirectory($path)) {
            return 0;
        }

        return collect($this->files->allFiles($path))
            ->reject(static fn (SplFileInfo $file): bool => $file->getFilename() === '.gitignore')
            ->sum(static fn (SplFileInfo $file): int => $file->getSize());
    }

    private function ensureCleanableDirectory(string $path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }

        $realPath = realpath($path);
        $realStoragePath = realpath(storage_path());

        if (! is_string($realPath) || ! is_string($realStoragePath) || ! str_starts_with($realPath, $realStoragePath.DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('Configured cache path must be inside Laravel storage.');
        }

        return $realPath;
    }

    private function cleanCacheDirectory(string $path): void
    {
        foreach ($this->files->allFiles($path) as $file) {
            if ($file->getFilename() === '.gitignore') {
                continue;
            }

            $this->files->delete($file->getPathname());
        }
    }

    /**
     * @return list<string>
     */
    private function databaseFiles(string $databasePath): array
    {
        return [
            $databasePath,
            "{$databasePath}-wal",
            "{$databasePath}-shm",
            "{$databasePath}-journal",
        ];
    }

    private function migrateLocalDatabase(string $connection): void
    {
        $migrationPath = $this->mobileLocalDatabase->migrationPath();

        if (! $this->files->isDirectory($migrationPath)) {
            return;
        }

        $exitCode = Artisan::call('migrate', [
            '--database' => $connection,
            '--path' => $migrationPath,
            '--realpath' => true,
            '--force' => true,
        ]);

        if ($exitCode !== Command::SUCCESS) {
            throw new RuntimeException('Mobile local database migrations failed during reset.');
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return "{$bytes} B";
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;

        foreach ($units as $index => $unit) {
            $isLastUnit = $index === array_key_last($units);

            if ($value < 1024 || $isLastUnit) {
                return number_format($value, $value >= 10 ? 0 : 1).' '.$unit;
            }

            $value /= 1024;
        }

        return "{$bytes} B";
    }
}
