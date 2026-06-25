<?php

namespace Database\Factories;

use App\Models\MobileLocalAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalAttachment>
 */
class MobileLocalAttachmentFactory extends Factory
{
    protected $model = MobileLocalAttachment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'record_id' => $this->faker->numberBetween(1, 20),
            'media_item_id' => null,
            'path' => '/tmp/mobile-attachments/'.$this->faker->uuid().'.jpg',
            'name' => $this->faker->words(2, true).'.jpg',
            'mime' => 'image/jpeg',
            'type' => MobileLocalAttachment::TYPE_IMAGE,
            'size' => $this->faker->numberBetween(25_000, 4_000_000),
            'caption' => $this->faker->sentence(),
            'sync_status' => MobileLocalAttachment::SYNC_PENDING,
            'upload_status' => MobileLocalAttachment::UPLOAD_QUEUED,
            'metadata' => [],
            'deleted_at' => null,
        ];
    }

    public function file(): static
    {
        return $this->state(fn (): array => [
            'path' => '/tmp/mobile-attachments/'.$this->faker->uuid().'.pdf',
            'name' => $this->faker->words(2, true).'.pdf',
            'mime' => 'application/pdf',
            'type' => MobileLocalAttachment::TYPE_FILE,
        ]);
    }

    public function synced(): static
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalAttachment::SYNC_SYNCED,
            'upload_status' => MobileLocalAttachment::UPLOAD_UPLOADED,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalAttachment::SYNC_FAILED,
            'upload_status' => MobileLocalAttachment::UPLOAD_FAILED,
            'metadata' => [
                'last_error' => 'Attachment upload failed.',
            ],
        ]);
    }
}
