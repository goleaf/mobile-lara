<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalAttachment;
use App\Models\MobileLocalMediaItem;
use App\Models\MobileLocalRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

final class AttachmentRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @return Collection<int, MobileLocalAttachment>
     */
    public function forRecord(MobileLocalRecord $record, int $limit = 50): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalAttachment::query()
            ->forRecord($record)
            ->with('mediaItem')
            ->listOrder()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalMediaItem>
     */
    public function availableMediaItems(int $limit = 8): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalMediaItem::query()
            ->galleryOrder()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @throws ModelNotFoundException<MobileLocalAttachment>
     */
    public function findForRecord(MobileLocalRecord $record, int|string $attachmentId): MobileLocalAttachment
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalAttachment::query()
            ->select(MobileLocalAttachment::SELECT_COLUMNS)
            ->with('mediaItem')
            ->forRecord($record)
            ->whereKey($attachmentId)
            ->firstOrFail();
    }

    /**
     * @throws ModelNotFoundException<MobileLocalMediaItem>
     */
    public function findMediaItem(int|string $mediaItemId): MobileLocalMediaItem
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalMediaItem::query()
            ->select(MobileLocalMediaItem::SELECT_COLUMNS)
            ->whereKey($mediaItemId)
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function attachFile(
        MobileLocalRecord $record,
        string $path,
        ?string $name = null,
        ?string $mime = null,
        ?string $type = null,
        ?int $size = null,
        ?string $caption = null,
        array $metadata = [],
    ): MobileLocalAttachment {
        $this->mobileLocalDatabase->ensureFileExists();

        $path = $this->normalizePath($path);

        return MobileLocalAttachment::query()->create([
            'record_id' => $record->getKey(),
            'media_item_id' => null,
            'path' => $path,
            'name' => $this->nullableText($name),
            'mime' => $this->nullableText($mime),
            'type' => $this->normalizeType($type, $mime, $path),
            'size' => $this->normalizeSize($size),
            'caption' => $this->nullableText($caption),
            'sync_status' => MobileLocalAttachment::SYNC_PENDING,
            'upload_status' => MobileLocalAttachment::UPLOAD_QUEUED,
            'metadata' => $metadata,
        ]);
    }

    public function linkMediaItem(MobileLocalRecord $record, MobileLocalMediaItem $mediaItem): MobileLocalAttachment
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalAttachment::query()->create([
            'record_id' => $record->getKey(),
            'media_item_id' => $mediaItem->getKey(),
            'path' => $mediaItem->path,
            'name' => $mediaItem->displayName(),
            'mime' => $mediaItem->mime,
            'type' => $this->normalizeType($mediaItem->type, $mediaItem->mime, $mediaItem->path),
            'size' => $mediaItem->size,
            'caption' => $mediaItem->caption,
            'sync_status' => MobileLocalAttachment::SYNC_PENDING,
            'upload_status' => MobileLocalAttachment::UPLOAD_QUEUED,
            'metadata' => [
                'source' => 'media_item',
            ],
        ]);
    }

    public function delete(MobileLocalAttachment $attachment): bool
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $attachment->forceFill([
            'sync_status' => MobileLocalAttachment::SYNC_PENDING,
            'upload_status' => MobileLocalAttachment::UPLOAD_QUEUED,
        ])->save();

        return (bool) $attachment->delete();
    }

    public function normalizeType(?string $type, ?string $mime, string $path): string
    {
        $type = Str::of((string) $type)->lower()->trim()->toString();
        $mime = Str::of((string) $mime)->lower()->trim()->toString();

        if (in_array($type, MobileLocalAttachment::TYPES, true)) {
            return $type;
        }

        if (str_starts_with($mime, 'image/')) {
            return MobileLocalAttachment::TYPE_IMAGE;
        }

        if (str_starts_with($mime, 'video/')) {
            return MobileLocalAttachment::TYPE_VIDEO;
        }

        if (str_starts_with($mime, 'audio/')) {
            return MobileLocalAttachment::TYPE_AUDIO;
        }

        return match (Str::of(pathinfo($path, PATHINFO_EXTENSION))->lower()->toString()) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif' => MobileLocalAttachment::TYPE_IMAGE,
            'mp4', 'mov', 'm4v', 'webm', 'avi' => MobileLocalAttachment::TYPE_VIDEO,
            'mp3', 'm4a', 'wav', 'aac', 'ogg' => MobileLocalAttachment::TYPE_AUDIO,
            default => MobileLocalAttachment::TYPE_FILE,
        };
    }

    private function normalizePath(string $path): string
    {
        return Str::of($path)
            ->squish()
            ->limit(500, '')
            ->toString();
    }

    private function nullableText(?string $text, int $limit = 255): ?string
    {
        $text = is_string($text) ? trim($text) : '';

        return $text === '' ? null : Str::of($text)->limit($limit, '')->toString();
    }

    private function normalizeSize(?int $size): ?int
    {
        return is_int($size) && $size >= 0 ? $size : null;
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
