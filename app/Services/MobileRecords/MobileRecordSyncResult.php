<?php

namespace App\Services\MobileRecords;

use App\Models\MobileLocalRecord;

final class MobileRecordSyncResult
{
    public function __construct(
        public readonly MobileLocalRecord $record,
        public readonly bool $synced,
        public readonly ?string $message = null,
    ) {}

    public function failed(): bool
    {
        return ! $this->synced && is_string($this->message) && trim($this->message) !== '';
    }
}
