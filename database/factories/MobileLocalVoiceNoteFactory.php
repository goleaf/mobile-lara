<?php

namespace Database\Factories;

use App\Models\MobileLocalVoiceNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalVoiceNote>
 */
class MobileLocalVoiceNoteFactory extends Factory
{
    protected $model = MobileLocalVoiceNote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'local_file_path' => '/tmp/mobile-voice-notes/'.$this->faker->uuid().'.m4a',
            'duration' => $this->faker->numberBetween(5, 300),
            'transcript' => null,
            'sync_status' => MobileLocalVoiceNote::SYNC_PENDING,
            'related_entity_type' => null,
            'related_entity_id' => null,
        ];
    }

    public function withTranscript(): self
    {
        return $this->state(fn (): array => [
            'transcript' => $this->faker->sentence(12),
        ]);
    }

    public function synced(): self
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalVoiceNote::SYNC_SYNCED,
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalVoiceNote::SYNC_FAILED,
        ]);
    }
}
