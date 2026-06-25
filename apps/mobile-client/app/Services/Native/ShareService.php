<?php

namespace App\Services\Native;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use Native\Mobile\Share;
use Throwable;

final class ShareService
{
    public function __construct(
        private readonly Share $share,
        private readonly Filesystem $files,
    ) {}

    public function nativeRuntimeAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    /**
     * @return list<array{key: string, label: string, description: string, supported: bool, driver: string}>
     */
    public function capabilities(): array
    {
        $nativeAvailable = $this->nativeRuntimeAvailable();

        return [
            [
                'key' => 'text',
                'label' => 'Text',
                'description' => 'Open the native share sheet with plain text content.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Share.Url',
            ],
            [
                'key' => 'url',
                'label' => 'URL',
                'description' => 'Open the native share sheet with a title, message, and absolute URL.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Share.Url',
            ],
            [
                'key' => 'file',
                'label' => 'File',
                'description' => 'Open the native share sheet for a readable local file.',
                'supported' => $nativeAvailable,
                'driver' => 'NativePHP Share.File',
            ],
        ];
    }

    /**
     * @return array{success: bool, operation: string, message: string, driver?: string, title?: string}
     */
    public function shareText(string $title, string $text): array
    {
        try {
            $title = $this->cleanTitle($title);
            $text = $this->cleanText($text);

            if (! $this->nativeRuntimeAvailable()) {
                return $this->result(false, 'share_text', 'Native text sharing is unavailable in this browser runtime.', [
                    'driver' => 'native',
                    'title' => $title,
                ]);
            }

            $this->share->url($title, $text, '');

            return $this->result(true, 'share_text', 'Native share sheet opened.', [
                'driver' => 'native',
                'title' => $title,
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'share_text', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, driver?: string, title?: string, url?: string}
     */
    public function shareUrl(string $title, string $text, string $url): array
    {
        try {
            $title = $this->cleanTitle($title);
            $text = $this->cleanText($text);
            $url = trim($url);

            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                throw new InvalidArgumentException('Share URL must be a valid absolute URL.');
            }

            if (! $this->nativeRuntimeAvailable()) {
                return $this->result(false, 'share_url', 'Native URL sharing is unavailable in this browser runtime.', [
                    'driver' => 'native',
                    'title' => $title,
                    'url' => $url,
                ]);
            }

            $this->share->url($title, $text, $url);

            return $this->result(true, 'share_url', 'Native share sheet opened.', [
                'driver' => 'native',
                'title' => $title,
                'url' => $url,
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'share_url', $exception->getMessage());
        }
    }

    /**
     * @return array{success: bool, operation: string, message: string, driver?: string, title?: string, file_path?: string}
     */
    public function shareFile(string $title, string $text, string $filePath): array
    {
        try {
            $title = $this->cleanTitle($title);
            $text = $this->cleanText($text);
            $filePath = $this->readableFilePath($filePath);

            if (! $this->nativeRuntimeAvailable()) {
                return $this->result(false, 'share_file', 'Native file sharing is unavailable in this browser runtime.', [
                    'driver' => 'native',
                    'title' => $title,
                    'file_path' => $filePath,
                ]);
            }

            $this->share->file($title, $text, $filePath);

            return $this->result(true, 'share_file', 'Native share sheet opened.', [
                'driver' => 'native',
                'title' => $title,
                'file_path' => $filePath,
            ]);
        } catch (Throwable $exception) {
            return $this->result(false, 'share_file', $exception->getMessage());
        }
    }

    public function fileCanBeShared(string $filePath): bool
    {
        $filePath = trim($filePath);

        return $filePath !== ''
            && $this->files->exists($filePath)
            && $this->files->isFile($filePath);
    }

    private function cleanTitle(string $title): string
    {
        $title = trim($title);

        if ($title === '') {
            throw new InvalidArgumentException('Share title is required.');
        }

        return $title;
    }

    private function cleanText(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            throw new InvalidArgumentException('Share text is required.');
        }

        return $text;
    }

    private function readableFilePath(string $filePath): string
    {
        $filePath = trim($filePath);

        if ($filePath === '') {
            throw new InvalidArgumentException('Share file path is required.');
        }

        if (! $this->files->exists($filePath)) {
            throw new InvalidArgumentException('Share file does not exist.');
        }

        if (! $this->files->isFile($filePath)) {
            throw new InvalidArgumentException('Share path is not a file.');
        }

        return $filePath;
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
