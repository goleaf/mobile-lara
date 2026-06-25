<?php

use App\Livewire\Mobile\FileManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->fileManagerPath = storage_path('framework/testing/mobile-file-manager/files');
    $this->fileExportPath = storage_path('framework/testing/mobile-file-manager/exports');

    File::deleteDirectory(storage_path('framework/testing/mobile-file-manager'));

    config([
        'nativephp-internal.running' => false,
        'mobile_local.storage.file_manager_path' => $this->fileManagerPath,
        'mobile_local.storage.file_export_path' => $this->fileExportPath,
        'mobile_local.storage.file_preview_bytes' => 65536,
    ]);
});

afterEach(function (): void {
    File::deleteDirectory(storage_path('framework/testing/mobile-file-manager'));
});

test('file manager screen renders file operations and fallback state', function (): void {
    Livewire::test(FileManager::class)
        ->assertSee('File manager')
        ->assertSee('File bridge')
        ->assertSee('Browser fallback active')
        ->assertSee('Editor')
        ->assertSee('Copy and move')
        ->assertSee('Import')
        ->assertSee('Capabilities')
        ->assertSee('Local files')
        ->assertSee('No local files');
});

test('file manager writes reads copies moves exports shares and deletes files', function (): void {
    Livewire::test(FileManager::class)
        ->set('filePath', 'notes/demo.txt')
        ->set('fileContents', 'Demo file contents')
        ->call('writeCurrentFile')
        ->assertSet('selectedPath', 'notes/demo.txt')
        ->assertSee('File written locally.')
        ->assertSee('demo.txt')
        ->call('readCurrentFile')
        ->assertSet('fileContents', 'Demo file contents')
        ->set('copyTo', 'copies/demo-copy.txt')
        ->call('copyCurrentFile')
        ->assertSee('File copied locally.')
        ->set('moveTo', 'archive/demo-moved.txt')
        ->call('moveCurrentFile')
        ->assertSet('filePath', 'archive/demo-moved.txt')
        ->assertSee('File moved locally.')
        ->call('exportFile')
        ->assertSee('File exported locally.')
        ->call('shareFile')
        ->assertSee('Native file sharing is unavailable in this browser runtime.')
        ->call('deleteFile', 'archive/demo-moved.txt')
        ->assertSee('File deleted locally.');

    expect(File::exists($this->fileExportPath.'/demo-moved.txt'))->toBeTrue()
        ->and(File::missing($this->fileManagerPath.'/archive/demo-moved.txt'))->toBeTrue();
});

test('file manager imports uploaded files into the local sandbox', function (): void {
    Livewire::test(FileManager::class)
        ->set('importDirectory', 'docs')
        ->set('importUpload', UploadedFile::fake()->create('Import Note.txt', 1, 'text/plain'))
        ->call('importFile')
        ->assertSet('filePath', 'docs/import-note.txt')
        ->assertSee('File imported locally.')
        ->assertSee('import-note.txt');

    expect(File::exists($this->fileManagerPath.'/docs/import-note.txt'))->toBeTrue();
});
