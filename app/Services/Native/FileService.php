<?php

namespace App\Services\Native;

use Carbon\CarbonImmutable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Native\Mobile\File as NativeFile;
use Throwable;

final class FileService
{
    public function __construct(
        private readonly Filesystem $files,
        private readonly NativeFile $nativeFile,
        private readonly ShareService $shares,
    ) {}

    public function nativeRuntimeAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    public function rootPath(): string
    {
        return rtrim((string) config('mobile_local.storage.file_manager_path', storage_path('app/mobile/files')), DIRECTORY_SEPARATOR);
    }

    public function exportPath(): string
    {
        return rtrim((string) config('mobile_local.storage.file_export_path', storage_path('app/mobile/exports')), DIRECTORY_SEPARATOR);
    }

    /**
     * @return list<array{key: string, label: string, description: string, supported: bool, driver: string}>
     */
    public function capabilities(): array
    {
        $nativeAvailable = $this->nativeRuntimeAvailable();

        return [
            [
                'key' => 'read',
                'label' => 'Read',
                'description' => 'Preview text files inside the local app file sandbox.',
                'supported' => true,
                'driver' => 'Laravel filesystem',
            ],
            [
                'key' => 'write',
                'label' => 'Write',
                'description' => 'Create or update local text files for offline workflows.',
                'supported' => true,
                'driver' => 'Laravel filesystem',
            ],
            [
                'key' => 'copy',
                'label' => 'Copy',
                'description' => 'Copy files with NativePHP when available, with a local fallback.',
                'supported' => true,
                'driver' => $nativeAvailable ? 'NativePHP File.Copy' : 'Laravel filesystem',
            ],
            [
                'key' => 'move',
                'label' => 'Move',
                'description' => 'Move files with NativePHP when available, with a local fallback.',
                'supported' => true,
                'driver' => $nativeAvailable ? 'NativePHP File.Move' : 'Laravel filesystem',
            ],
            [
                'key' => 'delete',
                'label' => 'Delete',
                'description' => 'Delete local files from the app sandbox.',
                'supported' => true,
                'driver' => 'Laravel filesystem',
            ],
            [
                'key' => 'import',
                'label' => 'Import',
                'description' => 'Import a selected upload into the app file sandbox.',
                'supported' => true,
                'driver' => 'Livewire upload',
            ],
            [
                'key' => 'export',
                'label' => 'Export',
                'description' => 'Copy a sandbox file to the mobile export directory.',
                'supported' => true,
                'driver' => $nativeAvailable ? 'NativePHP File.Copy' : 'Laravel filesystem',
            ],
            [
                'key' => 'share',
                'label' => 'Share',
                'description' => 'Open the native share sheet for a local file.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Share.File',
            ],
        ];
    }

    /**
     * @return array{native_available: bool, root_path: string, export_path: string, file_count: int, total_size: int, total_size_label: string}
     */
    public function snapshot(): array
    {
        $files = $this->listFiles();
        $totalSize = (int) array_sum(array_column($files, 'size'));

        return [
            'native_available' => $this->nativeRuntimeAvailable(),
            'root_path' => $this->rootPath(),
            'export_path' => $this->exportPath(),
            'file_count' => count($files),
            'total_size' => $totalSize,
            'total_size_label' => Number::fileSize($totalSize),
        ];
    }

    /**
     * @return list<array{path: string, name: string, size: int, size_label: string, mime: string, modified_at: string|null}>
     */
    public function listFiles(int $limit = 50): array
    {
        $this->ensureDirectories();

        return collect($this->files->allFiles($this->rootPath()))
            ->sortBy(fn ($file): string => Str::lower($this->relativePathForAbsolute($file->getPathname())))
            ->take(max(1, min($limit, 200)))
            ->map(fn ($file): array => $this->fileRow($file->getPathname()))
            ->values()
            ->all();
    }

    /**
     * @return array{success: bool, operation: string, message: string, path?: string, contents?: string, size?: int, driver?: string}
     */
    public function read(string $relativePath): array
    {
        try {
            $absolutePath = $this->absoluteExistingFilePath($relativePath);
            $size = $this->files->size($absolutePath);
            $maxBytes = (int) config('mobile_local.storage.file_preview_bytes', 65536);

            if ($size > $maxBytes) {
                return $this->result(false, 'read', 'File is too large to preview in the demo.', [
                    'path' => $this->normalizeRelativePath($relativePath),
                    'size' => $size,
                ]);
            }

            $contents = $this->files->get($absolutePath);

            if (! mb_check_encoding($contents, 'UTF-8')) {
                return $this->result(false, 'read', 'Only UTF-8 text files can be previewed in the demo.', [
                    'path' => $this->normalizeRelativePath($relativePath),
                    'size' => $size,
                ]);
            }

            return $this->result(true, 'read', 'File loaded.', [
                'path' => $this->normalizeRelativePath($relativePath),
                'contents' => $contents,
                'size' => $size,
                'driver' => 'local',
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'read', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, path?: string, size?: int, driver?: string}
     */
    public function write(string $relativePath, string $contents): array
    {
        try {
            $absolutePath = $this->absoluteWritablePath($relativePath);

            $this->files->ensureDirectoryExists(dirname($absolutePath));
            $this->files->put($absolutePath, $contents);

            return $this->result(true, 'write', 'File written locally.', [
                'path' => $this->normalizeRelativePath($relativePath),
                'size' => strlen($contents),
                'driver' => 'local',
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'write', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, source?: string, destination?: string, driver?: string}
     */
    public function copy(string $from, string $to): array
    {
        try {
            $sourcePath = $this->absoluteExistingFilePath($from);
            $destinationPath = $this->absoluteWritablePath($to);
            $this->files->ensureDirectoryExists(dirname($destinationPath));

            if ($this->nativeRuntimeAvailable() && $this->nativeFile->copy($sourcePath, $destinationPath)) {
                return $this->result(true, 'copy', 'File copied with NativePHP.', [
                    'source' => $this->normalizeRelativePath($from),
                    'destination' => $this->normalizeRelativePath($to),
                    'driver' => 'native',
                ]);
            }

            if ($this->files->copy($sourcePath, $destinationPath)) {
                return $this->result(true, 'copy', 'File copied locally.', [
                    'source' => $this->normalizeRelativePath($from),
                    'destination' => $this->normalizeRelativePath($to),
                    'driver' => 'local',
                ]);
            }

            return $this->result(false, 'copy', 'Unable to copy the file.');
        } catch (Throwable $exception) {
            return $this->result(false, 'copy', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, source?: string, destination?: string, driver?: string}
     */
    public function move(string $from, string $to): array
    {
        try {
            $sourcePath = $this->absoluteExistingFilePath($from);
            $destinationPath = $this->absoluteWritablePath($to);
            $this->files->ensureDirectoryExists(dirname($destinationPath));

            if ($this->nativeRuntimeAvailable() && $this->nativeFile->move($sourcePath, $destinationPath)) {
                return $this->result(true, 'move', 'File moved with NativePHP.', [
                    'source' => $this->normalizeRelativePath($from),
                    'destination' => $this->normalizeRelativePath($to),
                    'driver' => 'native',
                ]);
            }

            if ($this->files->move($sourcePath, $destinationPath)) {
                return $this->result(true, 'move', 'File moved locally.', [
                    'source' => $this->normalizeRelativePath($from),
                    'destination' => $this->normalizeRelativePath($to),
                    'driver' => 'local',
                ]);
            }

            return $this->result(false, 'move', 'Unable to move the file.');
        } catch (Throwable $exception) {
            return $this->result(false, 'move', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, path?: string, driver?: string}
     */
    public function delete(string $relativePath): array
    {
        try {
            $absolutePath = $this->absoluteExistingFilePath($relativePath);

            if (! $this->files->delete($absolutePath)) {
                return $this->result(false, 'delete', 'Unable to delete the file.');
            }

            return $this->result(true, 'delete', 'File deleted locally.', [
                'path' => $this->normalizeRelativePath($relativePath),
                'driver' => 'local',
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'delete', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, path?: string, size?: int, driver?: string}
     */
    public function import(UploadedFile $file, ?string $directory = null): array
    {
        try {
            $fileName = $this->sanitizeFileName($file->getClientOriginalName());
            $relativePath = $this->uniqueRelativePath($this->joinRelativePath($directory ?: 'imports', $fileName));
            $destinationPath = $this->absoluteWritablePath($relativePath);
            $sourcePath = $file->getRealPath() ?: $file->getPathname();

            if (! is_string($sourcePath) || $sourcePath === '' || ! $this->files->exists($sourcePath)) {
                return $this->result(false, 'import', 'Uploaded file is no longer available.');
            }

            $this->files->ensureDirectoryExists(dirname($destinationPath));
            $this->files->copy($sourcePath, $destinationPath);

            return $this->result(true, 'import', 'File imported locally.', [
                'path' => $relativePath,
                'size' => $this->files->size($destinationPath),
                'driver' => 'local',
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'import', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, path?: string, export_path?: string, driver?: string}
     */
    public function export(string $relativePath, ?string $exportName = null): array
    {
        try {
            $sourcePath = $this->absoluteExistingFilePath($relativePath);
            $exportFileName = $this->sanitizeFileName($exportName ?: basename($relativePath));
            $destinationPath = $this->uniqueAbsoluteExportPath($exportFileName);
            $this->files->ensureDirectoryExists(dirname($destinationPath));

            if ($this->nativeRuntimeAvailable() && $this->nativeFile->copy($sourcePath, $destinationPath)) {
                return $this->result(true, 'export', 'File exported with NativePHP.', [
                    'path' => $this->normalizeRelativePath($relativePath),
                    'export_path' => $destinationPath,
                    'driver' => 'native',
                ]);
            }

            if ($this->files->copy($sourcePath, $destinationPath)) {
                return $this->result(true, 'export', 'File exported locally.', [
                    'path' => $this->normalizeRelativePath($relativePath),
                    'export_path' => $destinationPath,
                    'driver' => 'local',
                ]);
            }

            return $this->result(false, 'export', 'Unable to export the file.');
        } catch (Throwable $exception) {
            return $this->result(false, 'export', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, path?: string, driver?: string}
     */
    public function share(string $relativePath, string $title = 'Mobile file', string $message = 'Shared from the mobile app.'): array
    {
        try {
            $absolutePath = $this->absoluteExistingFilePath($relativePath);

            if (! $this->nativeRuntimeAvailable()) {
                return $this->result(false, 'share', 'Native file sharing is unavailable in this browser runtime.', [
                    'path' => $this->normalizeRelativePath($relativePath),
                    'driver' => 'native',
                ]);
            }

            $shareResult = $this->shares->shareFile($title, $message, $absolutePath);

            if (! $shareResult['success']) {
                return $this->result(false, 'share', $shareResult['message'], [
                    'path' => $this->normalizeRelativePath($relativePath),
                    'driver' => $shareResult['driver'] ?? 'native',
                ]);
            }

            return $this->result(true, 'share', 'Native share sheet opened.', [
                'path' => $this->normalizeRelativePath($relativePath),
                'driver' => 'native',
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'share', $exception->getMessage());
        }
    }

    private function ensureDirectories(): void
    {
        $this->files->ensureDirectoryExists($this->rootPath());
        $this->files->ensureDirectoryExists($this->exportPath());
    }

    private function absoluteExistingFilePath(string $relativePath): string
    {
        $absolutePath = $this->absoluteWritablePath($relativePath);

        if (! $this->files->exists($absolutePath)) {
            throw new InvalidArgumentException('File does not exist.');
        }

        if (! $this->files->isFile($absolutePath)) {
            throw new InvalidArgumentException('Path is not a file.');
        }

        return $absolutePath;
    }

    private function absoluteWritablePath(string $relativePath): string
    {
        $this->ensureDirectories();

        return $this->rootPath().DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $this->normalizeRelativePath($relativePath));
    }

    private function normalizeRelativePath(string $relativePath): string
    {
        $path = str_replace('\\', '/', trim($relativePath));

        if ($path === '' || str_starts_with($path, '/') || str_contains($path, "\0")) {
            throw new InvalidArgumentException('Path must be a relative file path.');
        }

        $segments = [];

        foreach (explode('/', $path) as $segment) {
            $segment = trim($segment);

            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..' || ! preg_match('/^[A-Za-z0-9 ._()\\-]+$/', $segment)) {
                throw new InvalidArgumentException('Path contains unsupported characters.');
            }

            $segments[] = $segment;
        }

        if ($segments === []) {
            throw new InvalidArgumentException('Path must include a file name.');
        }

        return implode('/', $segments);
    }

    private function joinRelativePath(string $directory, string $fileName): string
    {
        return rtrim($directory, '/\\').'/'.$fileName;
    }

    private function sanitizeFileName(string $fileName): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $safeBaseName = Str::of($baseName)->ascii()->slug('-')->limit(80, '')->toString() ?: 'file';
        $safeExtension = Str::of($extension)->lower()->replaceMatches('/[^a-z0-9]/', '')->toString();

        return $safeExtension === '' ? $safeBaseName : "{$safeBaseName}.{$safeExtension}";
    }

    private function uniqueRelativePath(string $relativePath): string
    {
        $relativePath = $this->normalizeRelativePath($relativePath);

        if (! $this->files->exists($this->absoluteWritablePath($relativePath))) {
            return $relativePath;
        }

        $directory = pathinfo($relativePath, PATHINFO_DIRNAME);
        $baseName = pathinfo($relativePath, PATHINFO_FILENAME);
        $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
        $directory = $directory === '.' ? '' : "{$directory}/";

        for ($counter = 2; $counter <= 999; $counter++) {
            $candidate = $directory.$baseName.'-'.$counter.($extension ? ".{$extension}" : '');

            if (! $this->files->exists($this->absoluteWritablePath($candidate))) {
                return $candidate;
            }
        }

        throw new InvalidArgumentException('Unable to generate a unique file name.');
    }

    private function uniqueAbsoluteExportPath(string $fileName): string
    {
        $this->ensureDirectories();

        $fileName = $this->sanitizeFileName($fileName);
        $candidate = $this->exportPath().DIRECTORY_SEPARATOR.$fileName;

        if (! $this->files->exists($candidate)) {
            return $candidate;
        }

        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        for ($counter = 2; $counter <= 999; $counter++) {
            $candidate = $this->exportPath().DIRECTORY_SEPARATOR.$baseName.'-'.$counter.($extension ? ".{$extension}" : '');

            if (! $this->files->exists($candidate)) {
                return $candidate;
            }
        }

        throw new InvalidArgumentException('Unable to generate a unique export file name.');
    }

    /**
     * @return array{path: string, name: string, size: int, size_label: string, mime: string, modified_at: string|null}
     */
    private function fileRow(string $absolutePath): array
    {
        $modifiedAt = $this->files->lastModified($absolutePath);

        return [
            'path' => $this->relativePathForAbsolute($absolutePath),
            'name' => basename($absolutePath),
            'size' => $this->files->size($absolutePath),
            'size_label' => Number::fileSize($this->files->size($absolutePath)),
            'mime' => $this->files->mimeType($absolutePath) ?: 'application/octet-stream',
            'modified_at' => $modifiedAt ? CarbonImmutable::createFromTimestamp($modifiedAt)->diffForHumans() : null,
        ];
    }

    private function relativePathForAbsolute(string $absolutePath): string
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', ltrim(substr($absolutePath, strlen($this->rootPath())), DIRECTORY_SEPARATOR));
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function result(bool $success, string $operation, string $message, array $extra = []): array
    {
        return [
            'success' => $success,
            'operation' => $operation,
            'message' => $message,
            ...$extra,
        ];
    }
}
