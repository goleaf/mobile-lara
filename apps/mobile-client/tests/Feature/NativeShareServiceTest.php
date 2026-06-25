<?php

use App\Services\Native\ShareService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Native\Mobile\Share;

beforeEach(function (): void {
    $this->shareTestingPath = storage_path('framework/testing/native-share-service/share.txt');
    File::ensureDirectoryExists(dirname($this->shareTestingPath));
    File::put($this->shareTestingPath, 'Shared file');
});

afterEach(function (): void {
    File::deleteDirectory(storage_path('framework/testing/native-share-service'));
});

test('share service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $service = new ShareService(new NativeShareServiceFakeShare, new Filesystem);

        expect($service->nativeRuntimeAvailable())->toBeFalse()
            ->and($service->shareText('Debug', 'Runtime snapshot'))->toMatchArray([
                'success' => false,
                'operation' => 'share_text',
                'message' => 'Native text sharing is unavailable in this browser runtime.',
                'driver' => 'native',
            ])
            ->and($service->shareUrl('Debug', 'Runtime snapshot', 'https://example.test/debug'))->toMatchArray([
                'success' => false,
                'operation' => 'share_url',
                'message' => 'Native URL sharing is unavailable in this browser runtime.',
                'driver' => 'native',
            ])
            ->and($service->shareFile('Debug file', 'Runtime file', $this->shareTestingPath))->toMatchArray([
                'success' => false,
                'operation' => 'share_file',
                'message' => 'Native file sharing is unavailable in this browser runtime.',
                'driver' => 'native',
            ]);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('share service calls native text url and file channels when runtime is active', function (): void {
    config(['nativephp-internal.running' => true]);

    $nativeShare = new NativeShareServiceFakeShare;
    $service = new ShareService($nativeShare, new Filesystem);

    expect($service->shareText('Debug text', 'Runtime snapshot'))->toMatchArray([
        'success' => true,
        'operation' => 'share_text',
        'driver' => 'native',
    ])
        ->and($service->shareUrl('Debug URL', 'Runtime snapshot', 'https://example.test/debug'))->toMatchArray([
            'success' => true,
            'operation' => 'share_url',
            'url' => 'https://example.test/debug',
            'driver' => 'native',
        ])
        ->and($service->shareFile('Debug file', 'Runtime file', $this->shareTestingPath))->toMatchArray([
            'success' => true,
            'operation' => 'share_file',
            'file_path' => $this->shareTestingPath,
            'driver' => 'native',
        ])
        ->and($nativeShare->sharedUrls)->toHaveCount(2)
        ->and($nativeShare->sharedUrls[0])->toMatchArray([
            'title' => 'Debug text',
            'text' => 'Runtime snapshot',
            'url' => '',
        ])
        ->and($nativeShare->sharedUrls[1])->toMatchArray([
            'title' => 'Debug URL',
            'text' => 'Runtime snapshot',
            'url' => 'https://example.test/debug',
        ])
        ->and($nativeShare->sharedFiles)->toHaveCount(1)
        ->and($nativeShare->sharedFiles[0])->toMatchArray([
            'title' => 'Debug file',
            'text' => 'Runtime file',
            'filePath' => $this->shareTestingPath,
        ]);
});

test('share service validates url and file inputs before opening native share', function (): void {
    config(['nativephp-internal.running' => true]);

    $service = new ShareService(new NativeShareServiceFakeShare, new Filesystem);

    expect($service->shareUrl('Bad URL', 'Runtime snapshot', 'not-a-url'))->toMatchArray([
        'success' => false,
        'operation' => 'share_url',
        'message' => 'Share URL must be a valid absolute URL.',
    ])
        ->and($service->shareFile('Missing file', 'Runtime file', storage_path('missing-share.txt')))->toMatchArray([
            'success' => false,
            'operation' => 'share_file',
            'message' => 'Share file does not exist.',
        ])
        ->and($service->shareText('', 'Runtime snapshot'))->toMatchArray([
            'success' => false,
            'operation' => 'share_text',
            'message' => 'Share title is required.',
        ]);
});

final class NativeShareServiceFakeShare extends Share
{
    /**
     * @var list<array{title: string, text: string, url: string}>
     */
    public array $sharedUrls = [];

    /**
     * @var list<array{title: string, text: string, filePath: string}>
     */
    public array $sharedFiles = [];

    public function url(string $title, string $text, string $url): void
    {
        $this->sharedUrls[] = [
            'title' => $title,
            'text' => $text,
            'url' => $url,
        ];
    }

    public function file(string $title, string $text, string $filePath): void
    {
        $this->sharedFiles[] = [
            'title' => $title,
            'text' => $text,
            'filePath' => $filePath,
        ];
    }
}
