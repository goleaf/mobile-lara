<?php

namespace App\Services\MobileLocal;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class MobileLocalFileStorage
{
    public function __construct(private readonly Filesystem $files) {}

    /**
     * @return array{path: string, name: string, mime: string|null, size: int|null}
     */
    public function storeUploaded(UploadedFile $file, string $bucket): array
    {
        $sourcePath = $file->getRealPath() ?: $file->getPathname();

        if (! is_string($sourcePath) || ! $this->files->isFile($sourcePath)) {
            throw new InvalidArgumentException('Uploaded file is no longer available.');
        }

        return $this->copyIntoBucket(
            sourcePath: $sourcePath,
            bucket: $bucket,
            fileName: $file->getClientOriginalName(),
            mime: $file->getMimeType(),
        );
    }

    /**
     * @return array{path: string, name: string, mime: string|null, size: int|null}|null
     */
    public function storeExistingPath(string $sourcePath, string $bucket, ?string $mime = null): ?array
    {
        $sourcePath = $this->normalizePath($sourcePath);

        if (! $this->files->isFile($sourcePath) || ! $this->files->isReadable($sourcePath)) {
            return null;
        }

        return $this->copyIntoBucket(
            sourcePath: $sourcePath,
            bucket: $bucket,
            fileName: basename($sourcePath),
            mime: $mime,
        );
    }

    /**
     * @return array{path: string, name: string, mime: string|null, size: int|null}
     */
    private function copyIntoBucket(string $sourcePath, string $bucket, string $fileName, ?string $mime): array
    {
        $rootPath = $this->bucketPath($bucket);
        $safeName = $this->safeFileName($fileName);
        $targetPath = $this->uniquePath($rootPath, $safeName);

        $this->files->ensureDirectoryExists(dirname($targetPath));

        if (! $this->files->copy($sourcePath, $targetPath)) {
            throw new InvalidArgumentException('The selected file could not be saved locally.');
        }

        return [
            'path' => $targetPath,
            'name' => basename($targetPath),
            'mime' => $mime ?: ($this->files->mimeType($targetPath) ?: null),
            'size' => $this->files->size($targetPath),
        ];
    }

    private function bucketPath(string $bucket): string
    {
        $configKey = match ($bucket) {
            'attachments' => 'mobile_local.storage.attachment_path',
            'media' => 'mobile_local.storage.media_path',
            default => throw new InvalidArgumentException('Unsupported local file bucket.'),
        };

        return rtrim((string) config($configKey), DIRECTORY_SEPARATOR);
    }

    private function safeFileName(string $fileName): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $safeBaseName = Str::of($baseName)->ascii()->slug('-')->limit(80, '')->toString() ?: 'file';
        $safeExtension = Str::of($extension)->lower()->replaceMatches('/[^a-z0-9]/', '')->toString();

        return $safeExtension === '' ? $safeBaseName : "{$safeBaseName}.{$safeExtension}";
    }

    private function uniquePath(string $rootPath, string $fileName): string
    {
        $candidate = $rootPath.DIRECTORY_SEPARATOR.$fileName;

        if (! $this->files->exists($candidate)) {
            return $candidate;
        }

        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        for ($counter = 2; $counter <= 999; $counter++) {
            $candidate = $rootPath.DIRECTORY_SEPARATOR.$baseName.'-'.$counter.($extension ? ".{$extension}" : '');

            if (! $this->files->exists($candidate)) {
                return $candidate;
            }
        }

        throw new InvalidArgumentException('Unable to generate a unique local file name.');
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);

        if (Str::startsWith($path, 'file://')) {
            return rawurldecode((string) parse_url($path, PHP_URL_PATH));
        }

        return $path;
    }
}
