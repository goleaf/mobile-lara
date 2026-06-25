<?php

namespace App\Contracts\MobileLocal;

use App\Services\MobileLocal\MobileNetworkStatus;

interface MobileNetworkState
{
    public function isAvailable(): bool;

    public function status(): MobileNetworkStatus;
}
