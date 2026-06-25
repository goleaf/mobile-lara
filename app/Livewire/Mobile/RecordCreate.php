<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\RecordRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create record')]
class RecordCreate extends Component
{
    use DispatchesToasts;

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

    public function boot(RecordRepository $records): void
    {
        $this->records = $records;
    }

    public function save(): void
    {
        $this->submitOffline();
    }

    public function saveDraft(): void
    {
        $this->persistRecord(
            status: MobileLocalRecord::STATUS_DRAFT,
            submitMode: 'draft',
            successMessage: 'Draft saved locally.',
            successTitle: 'Draft saved',
        );
    }

    public function submitOffline(): void
    {
        $this->persistRecord(
            status: MobileLocalRecord::STATUS_ACTIVE,
            submitMode: 'offline_submit',
            successMessage: 'Record queued locally for offline sync.',
            successTitle: 'Record saved offline',
        );
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
        return view('livewire.mobile.record-create', [
            'attachmentPlaceholder' => $this->attachmentPlaceholder(),
            'categoryOptions' => $this->records->categoryOptions(),
            'priorityOptions' => $this->records->priorityOptions(),
            'storageAvailable' => $this->storageError === null,
            'tagPickerContext' => $this->tagPickerContext(),
            'tagValues' => $this->tagList($this->tags),
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
        $this->status = $status;
        $validated = $this->validate();

        try {
            $record = $this->records->create(
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

        $this->toastSuccess($successMessage, $successTitle);
        $this->redirectRoute('mobile.records.show', ['record' => $record], navigate: true);
    }

    /**
     * @return array{title: string, description: string, badge: string}
     */
    private function attachmentPlaceholder(): array
    {
        return [
            'title' => 'Attachments placeholder',
            'description' => 'Camera, gallery, scanner, and file attachments will be linked after native capture flows are connected.',
            'badge' => 'Ready for media',
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function metadataForSubmission(array $validated, string $submitMode): array
    {
        $metadata = [
            'category_label' => $this->records->categoryLabel((int) $validated['categoryId']),
            'submit_mode' => $submitMode,
            'offline_ready' => true,
            'attachments' => [
                'status' => 'placeholder',
                'count' => 0,
                'message' => 'No local attachments have been linked yet.',
            ],
        ];

        $location = $this->locationMetadata($validated);

        if ($location !== []) {
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
        return 'record-create';
    }
}
