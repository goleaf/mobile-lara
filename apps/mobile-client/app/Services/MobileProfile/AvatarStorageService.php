<?php

namespace App\Services\MobileProfile;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AvatarStorageService
{
    private const DISK = 'public';

    private const DIRECTORY = 'avatars';

    private const MAX_BYTES = 2_097_152;

    /**
     * @var array<string, string>
     */
    private const EXTENSIONS_BY_MIME = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function storeUploaded(UploadedFile $avatar, ?string $previousPath = null, bool $deletePrevious = true): string
    {
        $path = $avatar->store(path: self::DIRECTORY, options: self::DISK);

        if (! is_string($path)) {
            throw ValidationException::withMessages([
                'avatar' => 'The avatar could not be stored. Try another image.',
            ]);
        }

        if ($deletePrevious) {
            $this->delete($previousPath, except: $path);
        }

        return $path;
    }

    public function storeNativePath(string $sourcePath, ?string $mimeType = null): string
    {
        $path = $this->normalizeNativePath($sourcePath);

        if (! is_file($path) || ! is_readable($path)) {
            throw ValidationException::withMessages([
                'avatar' => 'The native avatar image could not be read.',
            ]);
        }

        $size = FileFacade::size($path);

        if ($size === false || $size > self::MAX_BYTES) {
            throw ValidationException::withMessages([
                'avatar' => 'The avatar may not be greater than 2 MB.',
            ]);
        }

        $detectedMime = $mimeType ?: FileFacade::mimeType($path);
        $extension = is_string($detectedMime) ? self::EXTENSIONS_BY_MIME[$detectedMime] ?? null : null;

        if ($extension === null) {
            throw ValidationException::withMessages([
                'avatar' => 'The avatar must be a JPG, PNG, or WebP image.',
            ]);
        }

        $storedPath = Storage::disk(self::DISK)->putFileAs(
            self::DIRECTORY,
            new File($path),
            Str::uuid()->toString().'.'.$extension,
        );

        if (! is_string($storedPath)) {
            throw ValidationException::withMessages([
                'avatar' => 'The native avatar image could not be stored.',
            ]);
        }

        return $storedPath;
    }

    public function delete(?string $path, ?string $except = null): void
    {
        if (! is_string($path) || $path === $except || ! Str::startsWith($path, self::DIRECTORY.'/')) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }

    public function url(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        return Storage::disk(self::DISK)->url($path);
    }

    public function absolutePath(?string $path): ?string
    {
        if (! is_string($path) || ! Str::startsWith($path, self::DIRECTORY.'/')) {
            return null;
        }

        $absolutePath = Storage::disk(self::DISK)->path($path);

        return is_file($absolutePath) && is_readable($absolutePath) ? $absolutePath : null;
    }

    public function copyWithinDisk(?string $sourcePath, ?string $targetPath): bool
    {
        if (
            ! is_string($sourcePath)
            || ! is_string($targetPath)
            || $sourcePath === $targetPath
            || ! Str::startsWith($sourcePath, self::DIRECTORY.'/')
            || ! Str::startsWith($targetPath, self::DIRECTORY.'/')
            || ! Storage::disk(self::DISK)->exists($sourcePath)
        ) {
            return false;
        }

        return Storage::disk(self::DISK)->copy($sourcePath, $targetPath);
    }

    private function normalizeNativePath(string $path): string
    {
        $path = trim($path);

        if (Str::startsWith($path, 'file://')) {
            return rawurldecode((string) parse_url($path, PHP_URL_PATH));
        }

        return $path;
    }
}
