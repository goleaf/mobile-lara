<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\Native\FileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('File manager')]
final class FileManager extends Component
{
    use DispatchesToasts;
    use WithFileUploads;

    public string $filePath = 'notes/demo.txt';

    public string $fileContents = "Hello from the NativePHP file manager.\n";

    public string $copyTo = 'copies/demo-copy.txt';

    public string $moveTo = 'archive/demo-moved.txt';

    public string $importDirectory = 'imports';

    public mixed $importUpload = null;

    public ?string $selectedPath = null;

    public ?string $lastOperationMessage = null;

    public string $lastOperationStatus = 'info';

    private FileService $files;

    public function boot(FileService $files): void
    {
        $this->files = $files;
    }

    public function writeCurrentFile(): void
    {
        $this->validate([
            'filePath' => ['required', 'string', 'max:180'],
            'fileContents' => ['nullable', 'string', 'max:262144'],
        ]);

        $result = $this->files->write($this->filePath, $this->fileContents);
        $this->selectedPath = ($result['success'] ?? false) ? (string) ($result['path'] ?? $this->filePath) : $this->selectedPath;
        $this->applyResult($result, 'File saved', 'File write failed');
    }

    public function readFile(string $path): void
    {
        $result = $this->files->read($path);

        if ($result['success'] ?? false) {
            $this->selectedPath = (string) ($result['path'] ?? $path);
            $this->filePath = $this->selectedPath;
            $this->fileContents = (string) ($result['contents'] ?? '');
        }

        $this->applyResult($result, 'File loaded', 'File read failed');
    }

    public function readCurrentFile(): void
    {
        $this->validate([
            'filePath' => ['required', 'string', 'max:180'],
        ]);

        $this->readFile($this->filePath);
    }

    public function copyCurrentFile(): void
    {
        $this->validate([
            'filePath' => ['required', 'string', 'max:180'],
            'copyTo' => ['required', 'string', 'max:180'],
        ]);

        $result = $this->files->copy($this->filePath, $this->copyTo);
        $this->applyResult($result, 'File copied', 'File copy failed');
    }

    public function moveCurrentFile(): void
    {
        $this->validate([
            'filePath' => ['required', 'string', 'max:180'],
            'moveTo' => ['required', 'string', 'max:180'],
        ]);

        $result = $this->files->move($this->filePath, $this->moveTo);

        if ($result['success'] ?? false) {
            $this->filePath = (string) ($result['destination'] ?? $this->moveTo);
            $this->selectedPath = $this->filePath;
        }

        $this->applyResult($result, 'File moved', 'File move failed');
    }

    public function deleteFile(string $path): void
    {
        $result = $this->files->delete($path);

        if (($result['success'] ?? false) && $this->selectedPath === ($result['path'] ?? $path)) {
            $this->selectedPath = null;
        }

        $this->applyResult($result, 'File deleted', 'File delete failed');
    }

    public function importFile(): void
    {
        $this->validate([
            'importDirectory' => ['required', 'string', 'max:120'],
            'importUpload' => ['required', 'file', 'max:10240'],
        ]);

        if (! $this->importUpload instanceof UploadedFile) {
            $this->applyResult([
                'success' => false,
                'operation' => 'import',
                'message' => 'Choose a file before importing.',
            ], 'File imported', 'File import failed');

            return;
        }

        $result = $this->files->import($this->importUpload, $this->importDirectory);

        if ($result['success'] ?? false) {
            $this->filePath = (string) ($result['path'] ?? $this->filePath);
            $this->selectedPath = $this->filePath;
            $this->importUpload = null;
        }

        $this->applyResult($result, 'File imported', 'File import failed');
    }

    public function exportFile(?string $path = null): void
    {
        $result = $this->files->export($path ?: $this->filePath);
        $this->applyResult($result, 'File exported', 'File export failed');
    }

    public function shareFile(?string $path = null): void
    {
        $result = $this->files->share($path ?: $this->filePath);
        $this->applyResult($result, 'File sharing', 'File share unavailable');
    }

    public function refreshFiles(): void
    {
        //
    }

    public function render(): View
    {
        return view('livewire.mobile.file-manager', [
            'capabilities' => $this->files->capabilities(),
            'fileRows' => $this->files->listFiles(),
            'snapshot' => $this->files->snapshot(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function applyResult(array $result, string $successTitle, string $failureTitle): void
    {
        $success = (bool) ($result['success'] ?? false);
        $message = (string) ($result['message'] ?? ($success ? 'Operation completed.' : 'Operation failed.'));

        $this->lastOperationMessage = $message;
        $this->lastOperationStatus = $success ? 'success' : 'error';

        if ($success) {
            $this->toastSuccess($message, $successTitle);

            return;
        }

        $this->toastWarning($message, $failureTitle);
    }
}
