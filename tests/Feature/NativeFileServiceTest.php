<?php

use App\Services\Native\FileService;
use App\Services\Native\ShareService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as FileFacade;
use Native\Mobile\File as NativeFile;
use Native\Mobile\Share;

beforeEach(function (): void {
    $this->fileManagerPath = storage_path('framework/testing/native-file-service/files');
    $this->fileExportPath = storage_path('framework/testing/native-file-service/exports');

    FileFacade::deleteDirectory(storage_path('framework/testing/native-file-service'));

    config([
        'mobile_local.storage.file_manager_path' => $this->fileManagerPath,
        'mobile_local.storage.file_export_path' => $this->fileExportPath,
        'mobile_local.storage.file_preview_bytes' => 65536,
    ]);
});

afterEach(function (): void {
    FileFacade::deleteDirectory(storage_path('framework/testing/native-file-service'));
});

test('file service reads writes copies moves deletes imports and exports with local fallback', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $service = new FileService(
            new Filesystem,
            new NativeFileServiceFakeFile,
            new ShareService(new NativeFileServiceFakeShare, new Filesystem),
        );

        expect($service->nativeRuntimeAvailable())->toBeFalse()
            ->and($service->write('notes/source.txt', 'Hello files'))->toMatchArray([
                'success' => true,
                'operation' => 'write',
                'path' => 'notes/source.txt',
                'driver' => 'local',
            ])
            ->and($service->read('notes/source.txt'))->toMatchArray([
                'success' => true,
                'operation' => 'read',
                'contents' => 'Hello files',
            ])
            ->and($service->copy('notes/source.txt', 'copies/source-copy.txt'))->toMatchArray([
                'success' => true,
                'operation' => 'copy',
                'destination' => 'copies/source-copy.txt',
                'driver' => 'local',
            ])
            ->and(FileFacade::exists($this->fileManagerPath.'/copies/source-copy.txt'))->toBeTrue()
            ->and($service->move('copies/source-copy.txt', 'archive/source-moved.txt'))->toMatchArray([
                'success' => true,
                'operation' => 'move',
                'destination' => 'archive/source-moved.txt',
                'driver' => 'local',
            ])
            ->and(FileFacade::missing($this->fileManagerPath.'/copies/source-copy.txt'))->toBeTrue()
            ->and(FileFacade::exists($this->fileManagerPath.'/archive/source-moved.txt'))->toBeTrue()
            ->and($service->export('notes/source.txt'))->toMatchArray([
                'success' => true,
                'operation' => 'export',
                'driver' => 'local',
            ])
            ->and(FileFacade::exists($this->fileExportPath.'/source.txt'))->toBeTrue()
            ->and($service->import(UploadedFile::fake()->create('Report Final.txt', 1, 'text/plain'), 'docs'))->toMatchArray([
                'success' => true,
                'operation' => 'import',
                'path' => 'docs/report-final.txt',
            ])
            ->and(FileFacade::exists($this->fileManagerPath.'/docs/report-final.txt'))->toBeTrue()
            ->and($service->delete('notes/source.txt'))->toMatchArray([
                'success' => true,
                'operation' => 'delete',
                'path' => 'notes/source.txt',
            ])
            ->and(FileFacade::missing($this->fileManagerPath.'/notes/source.txt'))->toBeTrue()
            ->and($service->write('../secret.txt', 'nope'))->toMatchArray([
                'success' => false,
                'operation' => 'write',
                'message' => 'Path contains unsupported characters.',
            ]);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('file service uses native copy move export and share when runtime is available', function (): void {
    config(['nativephp-internal.running' => true]);

    $nativeFile = new NativeFileServiceFakeFile;
    $share = new NativeFileServiceFakeShare;
    $service = new FileService(new Filesystem, $nativeFile, new ShareService($share, new Filesystem));

    $service->write('notes/native.txt', 'Native bridge');

    expect($service->copy('notes/native.txt', 'copies/native-copy.txt'))->toMatchArray([
        'success' => true,
        'driver' => 'native',
    ])
        ->and($nativeFile->copies)->toHaveCount(1)
        ->and($service->move('copies/native-copy.txt', 'archive/native-moved.txt'))->toMatchArray([
            'success' => true,
            'driver' => 'native',
        ])
        ->and($nativeFile->moves)->toHaveCount(1)
        ->and($service->export('archive/native-moved.txt'))->toMatchArray([
            'success' => true,
            'driver' => 'native',
        ])
        ->and($nativeFile->copies)->toHaveCount(2)
        ->and($service->share('archive/native-moved.txt'))->toMatchArray([
            'success' => true,
            'operation' => 'share',
            'driver' => 'native',
        ])
        ->and($share->sharedFiles)->toHaveCount(1)
        ->and($share->sharedFiles[0]['filePath'])->toBe($this->fileManagerPath.'/archive/native-moved.txt');
});

final class NativeFileServiceFakeFile extends NativeFile
{
    /**
     * @var list<array{from: string, to: string}>
     */
    public array $copies = [];

    /**
     * @var list<array{from: string, to: string}>
     */
    public array $moves = [];

    public function copy(string $from, string $to): bool
    {
        $this->copies[] = ['from' => $from, 'to' => $to];
        FileFacade::ensureDirectoryExists(dirname($to));

        return FileFacade::copy($from, $to);
    }

    public function move(string $from, string $to): bool
    {
        $this->moves[] = ['from' => $from, 'to' => $to];
        FileFacade::ensureDirectoryExists(dirname($to));

        return FileFacade::move($from, $to);
    }
}

final class NativeFileServiceFakeShare extends Share
{
    /**
     * @var list<array{title: string, text: string, filePath: string}>
     */
    public array $sharedFiles = [];

    public function file(string $title, string $text, string $filePath): void
    {
        $this->sharedFiles[] = [
            'title' => $title,
            'text' => $text,
            'filePath' => $filePath,
        ];
    }
}
