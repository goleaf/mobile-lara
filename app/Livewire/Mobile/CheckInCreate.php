<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalMediaItem;
use App\Services\MobileLocal\CheckInRepository;
use App\Services\MobileLocal\MediaItemRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create check-in')]
class CheckInCreate extends Component
{
    use DispatchesToasts;

    public ?string $latitude = null;

    public ?string $longitude = null;

    public ?string $accuracy = null;

    public string $note = '';

    public ?string $photoId = null;

    public ?string $storageError = null;

    private CheckInRepository $checkIns;

    private MediaItemRepository $mediaItems;

    public function boot(CheckInRepository $checkIns, MediaItemRepository $mediaItems): void
    {
        $this->checkIns = $checkIns;
        $this->mediaItems = $mediaItems;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $userId = Auth::id();

        if (! is_int($userId)) {
            $this->toastError('Sign in before saving a local check-in.', 'Check-in unavailable');

            return;
        }

        try {
            $photoId = $this->validatedPhotoId();

            if ($this->getErrorBag()->any()) {
                return;
            }

            $this->checkIns->record(
                userId: $userId,
                latitude: (float) $validated['latitude'],
                longitude: (float) $validated['longitude'],
                accuracy: $validated['accuracy'] === null || $validated['accuracy'] === '' ? null : (float) $validated['accuracy'],
                note: $validated['note'],
                photoId: $photoId,
            );
        } catch (QueryException) {
            $this->storageError = 'Check-in storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Check-in not saved');

            return;
        }

        $this->toastSuccess('Check-in saved locally and queued for sync.', 'Check-in saved');
        $this->redirectRoute('mobile.check-ins.index', navigate: true);
    }

    public function render(): View
    {
        try {
            $photoOptions = $this->photoOptions();
            $storageAvailable = true;
        } catch (QueryException) {
            $photoOptions = [];
            $storageAvailable = false;
        }

        return view('livewire.mobile.check-in-create', [
            'photoOptions' => $photoOptions,
            'storageAvailable' => $storageAvailable,
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'note' => ['nullable', 'string', 'max:1000'],
            'photoId' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'photoId' => 'photo',
        ];
    }

    private function validatedPhotoId(): ?int
    {
        if ($this->photoId === null || trim($this->photoId) === '') {
            return null;
        }

        $photoId = (int) $this->photoId;
        $exists = MobileLocalMediaItem::query()
            ->images()
            ->whereKey($photoId)
            ->exists();

        if (! $exists) {
            $this->addError('photoId', 'Select a local image that exists on this device.');

            return null;
        }

        return $photoId;
    }

    /**
     * @return array<string, string>
     */
    private function photoOptions(): array
    {
        return $this->mediaItems
            ->recent(limit: 30, type: MobileLocalMediaItem::TYPE_IMAGE)
            ->mapWithKeys(fn (MobileLocalMediaItem $mediaItem): array => [
                (string) $mediaItem->getKey() => $mediaItem->displayName(),
            ])
            ->all();
    }
}
