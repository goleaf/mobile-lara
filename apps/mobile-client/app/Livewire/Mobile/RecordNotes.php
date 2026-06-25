<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalNote;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\NoteRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Livewire\Component;

class RecordNotes extends Component
{
    use DispatchesToasts;

    public MobileLocalRecord $record;

    public string $body = '';

    public ?int $editingNoteId = null;

    public string $editingBody = '';

    public ?string $storageError = null;

    private NoteRepository $notes;

    public function boot(NoteRepository $notes): void
    {
        $this->notes = $notes;
    }

    public function mount(MobileLocalRecord $record): void
    {
        $this->record = $record;
    }

    public function createNote(): void
    {
        $validated = $this->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $this->notes->create(
                record: $this->record,
                body: $validated['body'],
            );
        } catch (QueryException) {
            $this->storageError = 'Note storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Note not saved');

            return;
        }

        $this->body = '';
        $this->resetValidation('body');
        $this->toastSuccess('Note saved locally and marked pending sync.', 'Note saved');
    }

    public function startEditingNote(int $noteId): void
    {
        try {
            $note = $this->notes->findForRecord($this->record, $noteId);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Note is no longer available on this device.', 'Edit unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Note storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Edit unavailable');

            return;
        }

        $this->editingNoteId = $note->id;
        $this->editingBody = $note->body;
        $this->resetValidation('editingBody');
    }

    public function updateNote(): void
    {
        $validated = $this->validate([
            'editingNoteId' => ['required', 'integer'],
            'editingBody' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $note = $this->notes->findForRecord($this->record, (int) $validated['editingNoteId']);
            $this->notes->update($note, $validated['editingBody']);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Note is no longer available on this device.', 'Update unavailable');
            $this->cancelEditing();

            return;
        } catch (QueryException) {
            $this->storageError = 'Note storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Note not updated');

            return;
        }

        $this->cancelEditing();
        $this->toastSuccess('Note updated locally and marked pending sync.', 'Note updated');
    }

    public function cancelEditing(): void
    {
        $this->editingNoteId = null;
        $this->editingBody = '';
        $this->resetValidation('editingBody');
    }

    public function deleteNote(int $noteId): void
    {
        try {
            $note = $this->notes->findForRecord($this->record, $noteId);
            $deleted = $this->notes->delete($note);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Note is no longer available on this device.', 'Delete unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Note storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Delete unavailable');

            return;
        }

        if (! $deleted) {
            $this->toastWarning('Note could not be deleted from this device.', 'Delete unavailable');

            return;
        }

        if ($this->editingNoteId === $noteId) {
            $this->cancelEditing();
        }

        $this->toastSuccess('Note deleted locally and marked pending sync.', 'Note deleted');
    }

    public function refreshNotes(): void
    {
        $this->storageError = null;
    }

    public function render(): View
    {
        try {
            $notes = $this->notes->forRecord($this->record);
            $storageAvailable = true;
        } catch (QueryException) {
            $notes = new Collection;
            $storageAvailable = false;
        }

        return view('livewire.mobile.record-notes', [
            'failedCount' => $notes->where('sync_status', MobileLocalNote::SYNC_FAILED)->count(),
            'notes' => $notes,
            'noteCount' => $notes->count(),
            'pendingCount' => $notes->where('sync_status', MobileLocalNote::SYNC_PENDING)->count(),
            'storageAvailable' => $storageAvailable && $this->storageError === null,
        ]);
    }
}
