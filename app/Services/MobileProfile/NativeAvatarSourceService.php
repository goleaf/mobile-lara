<?php

namespace App\Services\MobileProfile;

use Native\Mobile\Facades\Camera;

final class NativeAvatarSourceService
{
    public function isAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    public function startCamera(string $id): bool
    {
        if (! $this->isAvailable()) {
            return false;
        }

        return Camera::getPhoto([
            'quality' => 90,
        ])->id($id)->remember()->start();
    }

    public function startGallery(string $id): bool
    {
        if (! $this->isAvailable()) {
            return false;
        }

        return Camera::pickImages('image', false, 1)
            ->id($id)
            ->remember()
            ->start();
    }
}
