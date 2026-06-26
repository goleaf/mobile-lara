<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileRecordActions;
use App\Models\MobileLocalRecord;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileLocal\RecordRepository;
use App\Services\MobileRecords\MobileRecordSyncResult;
use App\Services\MobileRecords\MobileRecordSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit record')]
class RecordEdit extends Component
{
    use DispatchesToasts;
    use GuardsMobileRecordActions;

    public MobileLocalRecord $record;

    public string $title = '';

    public string $description = '';

    public string $status = MobileLocalRecord::STATUS_DRAFT;

    public string $priority = MobileLocalRecord::PRIORITY_NORMAL;

    public string $categoryId = '';

    public ?int $userId = null;

    public string $dueAt = '';

    public string $tags = '';

    public string $notes = '';

    public string $locationName = '';

    public string $latitude = '';

    public string $longitude = '';

    public ?string $storageError = null;

    private RecordRepository $records;

    private MobileRecordSyncService $recordSync;

    public function boot(
        RecordRepository $records,
        MobileAccessPolicy $mobileAccessPolicy,
        MobileRecordSyncService $recordSync,
    ): void {
        $this->records = $records;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
        $this->recordSync = $recordSync;
    }

    public function mount(MobileLocalRecord $record): void
    {
        $this->record = $record->loadMissing('tagModels');
        $this->title = $record->title;
        $this->description = (string) $record->description;
        $this->status = $record->status;
        $this->priority = $record->priority;
        $this->categoryId = $record->category_id !== null ? (string) $record->category_id : '';
        $this->userId = $record->user_id;
        $this->dueAt = $record->due_at?->format('Y-m-d\TH:i') ?? '';
        $this->tags = implode(', ', $record->tagList());
        $this->notes = (string) $record->notesText();

        $location = $record->metadataValue('location', []);

        if (is_array($location)) {
            $this->locationName = is_string($location['label'] ?? null) ? $location['label'] : '';
            $this->latitude = is_numeric($location['latitude'] ?? null) ? (string) $location['latitude'] : '';
            $this->longitude = is_numeric($location['longitude'] ?? null) ? (string) $location['longitude'] : '';
        }
    }

    public function save(): void
    {
        $this->persistRecord(
            status: $this->status,
            submitMode: 'updated',
            successMessage: 'Record updated locally.',
            successTitle: 'Record updated',
        );
    }

    public function saveAsDraft(): void
    {
        $this->persistRecord(
            status: MobileLocalRecord::STATUS_DRAFT,
            submitMode: 'draft',
            successMessage: 'Record saved as a local draft.',
            successTitle: 'Draft saved',
        );
    }

    public function archiveRecord(): void
    {
        if ($this->recordActionDenied('records.archive', 'Record not archived')) {
            return;
        }

        try {
            $this->record = $this->records->archive($this->record);
        } catch (QueryException) {
            $this->storageError = 'Record storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Record not archived');

            return;
        }

        $syncResult = $this->recordSync->archive($this->record);
        $this->record = $syncResult->record;

        $this->toastForSyncResult($syncResult, 'Record archived locally.', 'Record archived');
    }

    public function restoreRecord(): void
    {
        if ($this->recordActionDenied('records.archive', 'Record not restored')) {
            return;
        }

        try {
            $this->record = $this->records->restore($this->record);
        } catch (QueryException) {
            $this->storageError = 'Record storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Record not restored');

            return;
        }

        $syncResult = $this->recordSync->restore($this->record);
        $this->record = $syncResult->record;

        $this->toastForSyncResult($syncResult, 'Record restored locally.', 'Record restored');
    }

    public function deleteRecord(): void
    {
        if ($this->recordActionDenied('records.delete', 'Record not deleted')) {
            return;
        }

        try {
            $syncResult = $this->recordSync->delete($this->record);

            if ($syncResult->failed()) {
                $this->toastWarning("API delete needs retry: {$syncResult->message}", 'Record not deleted');

                return;
            }

            $deleted = $this->records->delete($this->record);
        } catch (QueryException) {
            $this->storageError = 'Record storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Record not deleted');

            return;
        }

        if (! $deleted) {
            $this->toastError('The local record could not be deleted.', 'Record not deleted');

            return;
        }

        if ($syncResult->synced) {
            $this->toastSuccess('Record deleted through Admin/API and removed from this device.', 'Record deleted');
        } else {
            $this->toastWarning('Record removed from this device. API delete is pending for this local-only record.', 'Record deleted locally');
        }

        $this->redirectRoute('mobile.records.index', navigate: true);
    }

    #[On('tag-picker-updated')]
    public function updateTagsFromPicker(string $context, array $tags, array $slugs = []): void
    {
        if ($context !== $this->tagPickerContext()) {
            return;
        }

        $this->tags = implode(', ', $this->tagListFromArray($tags));
    }

    public function render(): View
    {
        return view('livewire.mobile.record-edit', [
            'attachmentPlaceholder' => $this->attachmentPlaceholder(),
            'categoryOptions' => $this->records->categoryOptions(),
            'statusOptions' => $this->records->statusOptions(),
            'priorityOptions' => $this->records->priorityOptions(),
            'storageAvailable' => $this->storageError === null,
            'tagPickerContext' => $this->tagPickerContext(),
            'tagValues' => $this->tagList($this->tags),
            'recordActionPermissions' => $this->recordActionPermissions(),
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(MobileLocalRecord::STATUSES)],
            'priority' => ['required', Rule::in(MobileLocalRecord::PRIORITIES)],
            'categoryId' => ['required', 'integer', Rule::in(array_keys($this->records->categoryOptions()))],
            'userId' => ['nullable', 'integer', 'min:1'],
            'dueAt' => ['nullable', 'date'],
            'tags' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'locationName' => ['nullable', 'string', 'max:160'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    private function persistRecord(string $status, string $submitMode, string $successMessage, string $successTitle): void
    {
        if ($this->recordActionDenied('records.update', 'Record not saved')) {
            return;
        }

        $this->status = $status;
        $validated = $this->validate();

        try {
            $this->record = $this->records->update(
                record: $this->record,
                title: $validated['title'],
                description: $validated['description'],
                status: $validated['status'],
                priority: $validated['priority'],
                categoryId: (int) $validated['categoryId'],
                userId: $validated['userId'],
                dueAt: $validated['dueAt'],
                tags: $this->tagList($validated['tags']),
                notes: $validated['notes'],
                metadata: $this->metadataForSubmission($validated, $submitMode),
            );
        } catch (QueryException) {
            $this->storageError = 'Record storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Record not saved');

            return;
        }

        $syncResult = $submitMode === 'draft' ? null : $this->recordSync->save($this->record);
        $this->record = $syncResult?->record ?? $this->record;

        $this->toastForSyncResult($syncResult, $successMessage, $successTitle);
        $this->redirectRoute('mobile.records.show', ['record' => $this->record], navigate: true);
    }

    private function toastForSyncResult(
        ?MobileRecordSyncResult $syncResult,
        string $fallbackMessage,
        string $fallbackTitle,
    ): void {
        if ($syncResult?->synced) {
            $this->toastSuccess('Record synced with Admin/API and cached locally.', 'Record synced');

            return;
        }

        if ($syncResult?->failed()) {
            $this->toastWarning("Saved locally. API sync needs retry: {$syncResult->message}", 'Record saved locally');

            return;
        }

        $this->toastSuccess($fallbackMessage, $fallbackTitle);
    }

    /**
     * @return array{title: string, description: string, badge: string}
     */
    private function attachmentPlaceholder(): array
    {
        return [
            'title' => 'Attachments placeholder',
            'description' => 'Camera, gallery, scanner, and file attachments will stay linked after native capture flows are connected.',
            'badge' => 'Preserved',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function metadataForSubmission(array $validated, string $submitMode): array
    {
        $metadata = $this->record->metadata;

        if (! is_array($metadata)) {
            $metadata = [];
        }

        $metadata['category_label'] = $this->records->categoryLabel((int) $validated['categoryId']);
        $metadata['submit_mode'] = $submitMode;
        $metadata['offline_ready'] = true;
        $metadata['attachments'] = is_array($metadata['attachments'] ?? null)
            ? $metadata['attachments']
            : [
                'status' => 'placeholder',
                'count' => 0,
                'message' => 'No local attachments have been linked yet.',
            ];

        $location = $this->locationMetadata($validated);

        if ($location === []) {
            unset($metadata['location']);
        } else {
            $metadata['location'] = $location;
        }

        return $metadata;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function locationMetadata(array $validated): array
    {
        $location = [];

        if (is_string($validated['locationName']) && trim($validated['locationName']) !== '') {
            $location['label'] = trim($validated['locationName']);
        }

        if (is_numeric($validated['latitude'])) {
            $location['latitude'] = (float) $validated['latitude'];
        }

        if (is_numeric($validated['longitude'])) {
            $location['longitude'] = (float) $validated['longitude'];
        }

        return $location;
    }

    /**
     * @return list<string>
     */
    private function tagList(string $tags): array
    {
        return str($tags)
            ->explode(',')
            ->map(fn (string $tag): string => trim($tag))
            ->filter(fn (string $tag): bool => $tag !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, mixed>  $tags
     * @return list<string>
     */
    private function tagListFromArray(array $tags): array
    {
        return collect($tags)
            ->filter(fn (mixed $tag): bool => is_string($tag))
            ->map(fn (string $tag): string => trim($tag))
            ->filter(fn (string $tag): bool => $tag !== '')
            ->values()
            ->all();
    }

    private function tagPickerContext(): string
    {
        return "record-edit-{$this->record->id}";
    }
}
