<?php

namespace Database\Factories;

use App\Models\MobileLocalMediaItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalMediaItem>
 */
class MobileLocalMediaItemFactory extends Factory
{
    protected $model = MobileLocalMediaItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => '/tmp/mobile-media/'.$this->faker->uuid().'.jpg',
            'type' => MobileLocalMediaItem::TYPE_IMAGE,
            'mime' => 'image/jpeg',
            'size' => $this->faker->numberBetween(25_000, 4_000_000),
            'width' => $this->faker->numberBetween(640, 2400),
            'height' => $this->faker->numberBetween(480, 1800),
            'duration' => null,
            'caption' => $this->faker->sentence(),
            'sync_status' => MobileLocalMediaItem::SYNC_PENDING,
            'related_entity_type' => null,
            'related_entity_id' => null,
        ];
    }

    public function video(): self
    {
        return $this->state(fn (): array => [
            'path' => '/tmp/mobile-media/'.$this->faker->uuid().'.mp4',
            'type' => MobileLocalMediaItem::TYPE_VIDEO,
            'mime' => 'video/mp4',
            'duration' => $this->faker->numberBetween(5, 180),
        ]);
    }

    public function synced(): self
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalMediaItem::SYNC_SYNCED,
        ]);
    }
}
