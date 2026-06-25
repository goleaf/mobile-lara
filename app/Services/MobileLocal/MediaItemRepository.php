<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalMediaItem;
use App\Models\MobileLocalRecord;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class MediaItemRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    public function record(
        string $path,
        string $type,
        ?string $mime = null,
        ?int $size = null,
        ?int $width = null,
        ?int $height = null,
        ?int $duration = null,
        ?string $caption = null,
        string $syncStatus = MobileLocalMediaItem::SYNC_PENDING,
        ?string $relatedEntityType = null,
        int|string|null $relatedEntityId = null,
        ?CarbonInterface $createdAt = null,
    ): MobileLocalMediaItem {
        $this->mobileLocalDatabase->ensureFileExists();

        $timestamp = $createdAt ?: CarbonImmutable::now();

        return MobileLocalMediaItem::query()->create([
            'path' => $path,
            'type' => $this->normalizeType($type, $mime, $path),
            'mime' => $mime,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'duration' => $duration,
            'caption' => $caption,
            'sync_status' => $syncStatus,
            'related_entity_type' => $relatedEntityType,
            'related_entity_id' => is_null($relatedEntityId) ? null : (string) $relatedEntityId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    /**
     * @return Collection<int, MobileLocalMediaItem>
     */
    public function recent(int $limit = 24, ?string $type = null, ?string $syncStatus = null): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $query = MobileLocalMediaItem::query()->galleryOrder();

        if (is_string($type) && $type !== '') {
            $query->forType($type);
        }

        if (is_string($syncStatus) && $syncStatus !== '') {
            $query->forSyncStatus($syncStatus);
        }

        return $query
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalMediaItem>
     */
    public function pendingSync(int $limit = 50): Collection
    {
        return $this->recent($limit, syncStatus: MobileLocalMediaItem::SYNC_PENDING);
    }

    /**
     * @return Collection<int, MobileLocalMediaItem>
     */
    public function forRecord(MobileLocalRecord $record, int $limit = 12): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalMediaItem::query()
            ->galleryOrder()
            ->forRelatedEntity(MobileLocalRecord::ENTITY_TYPE, $record->getKey())
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return array{total: int, images: int, videos: int, pending: int, failed: int}
     */
    public function counts(): array
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return [
            'total' => MobileLocalMediaItem::query()->count(),
            'images' => MobileLocalMediaItem::query()->images()->count(),
            'videos' => MobileLocalMediaItem::query()->videos()->count(),
            'pending' => MobileLocalMediaItem::query()->pendingSync()->count(),
            'failed' => MobileLocalMediaItem::query()->forSyncStatus(MobileLocalMediaItem::SYNC_FAILED)->count(),
        ];
    }

    public function markSynced(MobileLocalMediaItem $mediaItem): MobileLocalMediaItem
    {
        return $this->updateSyncStatus($mediaItem, MobileLocalMediaItem::SYNC_SYNCED);
    }

    public function markFailed(MobileLocalMediaItem $mediaItem): MobileLocalMediaItem
    {
        return $this->updateSyncStatus($mediaItem, MobileLocalMediaItem::SYNC_FAILED);
    }

    private function updateSyncStatus(MobileLocalMediaItem $mediaItem, string $syncStatus): MobileLocalMediaItem
    {
        $mediaItem->forceFill([
            'sync_status' => $syncStatus,
            'updated_at' => CarbonImmutable::now(),
        ])->save();

        return $this->find($mediaItem->getKey());
    }

    private function find(int|string $mediaItemId): MobileLocalMediaItem
    {
        return MobileLocalMediaItem::query()
            ->select(MobileLocalMediaItem::SELECT_COLUMNS)
            ->whereKey($mediaItemId)
            ->firstOrFail();
    }

    private function normalizeType(string $type, ?string $mime, string $path): string
    {
        $type = Str::of($type)->lower()->trim()->toString();
        $mime = Str::of((string) $mime)->lower()->trim()->toString();

        if (in_array($type, [
            MobileLocalMediaItem::TYPE_IMAGE,
            MobileLocalMediaItem::TYPE_VIDEO,
            MobileLocalMediaItem::TYPE_AUDIO,
        ], true)) {
            return $type;
        }

        if (str_starts_with($mime, 'image/')) {
            return MobileLocalMediaItem::TYPE_IMAGE;
        }

        if (str_starts_with($mime, 'video/')) {
            return MobileLocalMediaItem::TYPE_VIDEO;
        }

        if (str_starts_with($mime, 'audio/')) {
            return MobileLocalMediaItem::TYPE_AUDIO;
        }

        $extension = Str::of(pathinfo($path, PATHINFO_EXTENSION))->lower()->toString();

        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif' => MobileLocalMediaItem::TYPE_IMAGE,
            'mp4', 'mov', 'm4v', 'webm', 'avi' => MobileLocalMediaItem::TYPE_VIDEO,
            'mp3', 'm4a', 'wav', 'aac', 'ogg' => MobileLocalMediaItem::TYPE_AUDIO,
            default => MobileLocalMediaItem::TYPE_FILE,
        };
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
