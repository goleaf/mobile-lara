<?php

namespace App\Http\Middleware;

use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\BiometricUnlockService;
use App\Services\MobileAuth\PinUnlockService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMobileUnlock
{
    public function __construct(
        private readonly AppUnlockStateService $unlockState,
        private readonly BiometricUnlockService $biometricUnlocks,
        private readonly PinUnlockService $pinUnlocks,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->biometricUnlocks->shouldRequireUnlock() && ! $this->pinUnlocks->shouldRequireUnlock()) {
            return $next($request);
        }

        $this->unlockState->rememberIntendedUrl($request->fullUrl());

        return redirect()->route('mobile.unlock');
    }
}
