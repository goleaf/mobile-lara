<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalMediaItem;
use App\Services\MobileLocal\MediaItemRepository;
use App\Services\Native\ShareService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Media gallery')]
final class MediaGallery extends Component
{
    use DispatchesToasts;

    private const FILTER_ALL = 'all';

    private const FILTER_IMAGES = 'images';

    private const FILTER_VIDEOS = 'videos';

    private const FILTER_PENDING = 'pending';

    private const FILTER_FAILED = 'failed';

    /**
     * @var list<string>
     */
    private const FILTERS = [
        self::FILTER_ALL,
        self::FILTER_IMAGES,
        self::FILTER_VIDEOS,
        self::FILTER_PENDING,
        self::FILTER_FAILED,
    ];

    public int $limit = 24;

    public string $filter = self::FILTER_ALL;

    private MediaItemRepository $mediaItems;

    private ShareService $shares;

    public function boot(MediaItemRepository $mediaItems, ShareService $shares): void
    {
        $this->mediaItems = $mediaItems;
        $this->shares = $shares;
    }

    public function mount(int $limit = 24, string $filter = self::FILTER_ALL): void
    {
        $this->limit = max(1, min($limit, 100));
        $this->filter = $this->validFilter($filter);
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $this->validFilter($filter);
    }

    public function refreshGallery(): void
    {
        //
    }

    public function shareMediaItem(int $mediaItemId): void
    {
        try {
            $mediaItem = MobileLocalMediaItem::query()
                ->select(MobileLocalMediaItem::SELECT_COLUMNS)
                ->whereKey($mediaItemId)
                ->first();
        } catch (QueryException) {
            $this->toastWarning('Media storage is unavailable. Run the local mobile migrations first.', 'Share unavailable');

            return;
        }

        if ($mediaItem === null) {
            $this->toastWarning('Media item is no longer available on this device.', 'Share unavailable');

            return;
        }

        $result = $this->shares->fileCanBeShared($mediaItem->path)
            ? $this->shares->shareFile($mediaItem->displayName(), $this->mediaShareText($mediaItem), $mediaItem->path)
            : $this->shares->shareText($mediaItem->displayName(), $this->mediaShareText($mediaItem));

        $this->toastForShareResult($result, 'Media shared', 'Share unavailable');
    }

    public function render(): View
    {
        try {
            $stats = $this->mediaItems->counts();
            $mediaItems = $this->mediaItems->recent(
                limit: $this->limit,
                type: $this->typeFilter(),
                syncStatus: $this->syncStatusFilter(),
            );
            $storageAvailable = true;
        } catch (QueryException) {
            $stats = [
                'total' => 0,
                'images' => 0,
                'videos' => 0,
                'pending' => 0,
                'failed' => 0,
            ];
            $mediaItems = new Collection;
            $storageAvailable = false;
        }

        return view('livewire.mobile.media-gallery', [
            'filters' => $this->filters($stats),
            'galleryCount' => $mediaItems->count(),
            'mediaItems' => $mediaItems,
            'metrics' => $this->metrics($stats),
            'storageAvailable' => $storageAvailable,
        ]);
    }

    /**
     * @param  array{total: int, images: int, videos: int, pending: int, failed: int}  $stats
     * @return list<array{key: string, label: string, count: int, active: bool}>
     */
    private function filters(array $stats): array
    {
        return [
            [
                'key' => self::FILTER_ALL,
                'label' => 'All',
                'count' => $stats['total'],
                'active' => $this->filter === self::FILTER_ALL,
            ],
            [
                'key' => self::FILTER_IMAGES,
                'label' => 'Images',
                'count' => $stats['images'],
                'active' => $this->filter === self::FILTER_IMAGES,
            ],
            [
                'key' => self::FILTER_VIDEOS,
                'label' => 'Videos',
                'count' => $stats['videos'],
                'active' => $this->filter === self::FILTER_VIDEOS,
            ],
            [
                'key' => self::FILTER_PENDING,
                'label' => 'Pending',
                'count' => $stats['pending'],
                'active' => $this->filter === self::FILTER_PENDING,
            ],
            [
                'key' => self::FILTER_FAILED,
                'label' => 'Failed',
                'count' => $stats['failed'],
                'active' => $this->filter === self::FILTER_FAILED,
            ],
        ];
    }

    /**
     * @param  array{total: int, images: int, videos: int, pending: int, failed: int}  $stats
     * @return list<array{label: string, value: int, description: string}>
     */
    private function metrics(array $stats): array
    {
        return [
            [
                'label' => 'Total',
                'value' => $stats['total'],
                'description' => 'Stored media',
            ],
            [
                'label' => 'Images',
                'value' => $stats['images'],
                'description' => 'Photo assets',
            ],
            [
                'label' => 'Videos',
                'value' => $stats['videos'],
                'description' => 'Video assets',
            ],
            [
                'label' => 'Pending',
                'value' => $stats['pending'],
                'description' => 'Awaiting sync',
            ],
        ];
    }

    private function typeFilter(): ?string
    {
        return match ($this->filter) {
            self::FILTER_IMAGES => MobileLocalMediaItem::TYPE_IMAGE,
            self::FILTER_VIDEOS => MobileLocalMediaItem::TYPE_VIDEO,
            default => null,
        };
    }

    private function syncStatusFilter(): ?string
    {
        return match ($this->filter) {
            self::FILTER_PENDING => MobileLocalMediaItem::SYNC_PENDING,
            self::FILTER_FAILED => MobileLocalMediaItem::SYNC_FAILED,
            default => null,
        };
    }

    private function validFilter(string $filter): string
    {
        return in_array($filter, self::FILTERS, true) ? $filter : self::FILTER_ALL;
    }

    private function mediaShareText(MobileLocalMediaItem $mediaItem): string
    {
        return collect([
            $mediaItem->displayName(),
            'Type: '.$mediaItem->type,
            'MIME: '.($mediaItem->mime ?: 'unknown'),
            'Sync: '.$mediaItem->sync_status,
            'Path: '.$mediaItem->path,
            $mediaItem->caption ? 'Caption: '.$mediaItem->caption : null,
        ])
            ->filter()
            ->implode(PHP_EOL);
    }

    /**
     * @param  array{success: bool, message: string}  $result
     */
    private function toastForShareResult(array $result, string $successTitle, string $failureTitle): void
    {
        if ($result['success']) {
            $this->toastSuccess($result['message'], $successTitle);

            return;
        }

        $this->toastWarning($result['message'], $failureTitle);
    }
}
