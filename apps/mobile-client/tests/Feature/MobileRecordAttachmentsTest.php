<?php

use App\Livewire\Mobile\RecordAttachments;
use App\Models\MobileLocalAttachment;
use App\Models\MobileLocalMediaItem;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\AttachmentRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-record-attachments.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('attachment repository attaches files links media lists and soft deletes queued rows', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();
    $mediaItem = MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/mobile-media/receipt.jpg',
        'mime' => 'image/jpeg',
        'type' => MobileLocalMediaItem::TYPE_IMAGE,
        'size' => 125_000,
        'caption' => 'Receipt photo',
    ]);
    $repository = app(AttachmentRepository::class);

    $attachment = $repository->attachFile(
        record: $record,
        path: '  /tmp/mobile-attachments/manual.pdf  ',
        name: ' manual.pdf ',
        mime: 'application/pdf',
        type: MobileLocalAttachment::TYPE_FILE,
        size: 1_200,
        caption: ' Manual document ',
        metadata: ['source' => 'file_picker'],
    );

    $linked = $repository->linkMediaItem($record, $mediaItem);
    $attachments = $repository->forRecord($record);

    expect($attachment->record_id)->toBe($record->id)
        ->and($attachment->media_item_id)->toBeNull()
        ->and($attachment->path)->toBe('/tmp/mobile-attachments/manual.pdf')
        ->and($attachment->displayName())->toBe('manual.pdf')
        ->and($attachment->mime)->toBe('application/pdf')
        ->and($attachment->type)->toBe(MobileLocalAttachment::TYPE_FILE)
        ->and($attachment->size)->toBe(1_200)
        ->and($attachment->caption)->toBe('Manual document')
        ->and($attachment->metadata)->toBe(['source' => 'file_picker'])
        ->and($attachment->sync_status)->toBe(MobileLocalAttachment::SYNC_PENDING)
        ->and($attachment->upload_status)->toBe(MobileLocalAttachment::UPLOAD_QUEUED)
        ->and($linked->media_item_id)->toBe($mediaItem->id)
        ->and($linked->path)->toBe('/tmp/mobile-media/receipt.jpg')
        ->and($linked->displayName())->toBe('receipt.jpg')
        ->and($attachments)->toHaveCount(2)
        ->and($attachments->pluck('id')->contains($attachment->id))->toBeTrue()
        ->and($repository->delete($attachment))->toBeTrue()
        ->and(MobileLocalAttachment::query()->count())->toBe(1);

    $trashed = MobileLocalAttachment::withTrashed()->find($attachment->id);

    expect($trashed?->trashed())->toBeTrue()
        ->and($trashed?->sync_status)->toBe(MobileLocalAttachment::SYNC_PENDING)
        ->and($trashed?->upload_status)->toBe(MobileLocalAttachment::UPLOAD_QUEUED);
});

test('record attachments component creates links previews shares deletes and exposes upload queue placeholder', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();
    $mediaItem = MobileLocalMediaItem::factory()->create([
        'path' => '/tmp/mobile-media/field-photo.jpg',
        'type' => MobileLocalMediaItem::TYPE_IMAGE,
        'mime' => 'image/jpeg',
        'size' => 88_000,
        'caption' => 'Field photo',
    ]);

    Livewire::test(RecordAttachments::class, ['record' => $record])
        ->assertSee('Record attachments')
        ->assertSee('Attachment picker')
        ->assertSee('Media picker')
        ->assertSee('field-photo.jpg')
        ->assertSee('Upload queue placeholder')
        ->set('path', '/tmp/mobile-attachments/manual.pdf')
        ->set('name', 'manual.pdf')
        ->set('mime', 'application/pdf')
        ->set('type', MobileLocalAttachment::TYPE_FILE)
        ->set('size', '1200')
        ->set('caption', 'Manual document')
        ->call('createAttachment')
        ->assertHasNoErrors()
        ->assertSet('path', '')
        ->assertDispatched('mobile-toast')
        ->assertSee('manual.pdf')
        ->assertSee('Queued upload')
        ->assertSee('Pending sync')
        ->call('linkMediaItem', $mediaItem->id)
        ->assertDispatched('mobile-toast')
        ->assertSee('field-photo.jpg')
        ->call('previewAttachment', MobileLocalAttachment::query()->where('name', 'manual.pdf')->value('id'))
        ->assertSee('Attachment preview')
        ->assertSee('/tmp/mobile-attachments/manual.pdf')
        ->call('shareAttachment', MobileLocalAttachment::query()->where('name', 'manual.pdf')->value('id'))
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Share unavailable';
        })
        ->call('uploadQueuePlaceholder')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'info'
                && ($params['title'] ?? null) === 'Upload queue';
        })
        ->call('deleteAttachment', MobileLocalAttachment::query()->where('name', 'manual.pdf')->value('id'))
        ->assertDispatched('mobile-toast')
        ->assertDontSee('manual.pdf');

    expect(MobileLocalAttachment::withTrashed()->where('name', 'manual.pdf')->first()?->trashed())->toBeTrue();
});

test('record attachments component validates picker input before saving', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();

    Livewire::test(RecordAttachments::class, ['record' => $record])
        ->set('path', '')
        ->set('type', 'binary')
        ->set('size', '-1')
        ->call('createAttachment')
        ->assertHasErrors(['path', 'type', 'size']);
});
