<?php

namespace App\Http\Middleware;

use App\Services\MobileAccess\MobileAccessPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileFeatureAccess
{
    public function __construct(private readonly MobileAccessPolicy $accessPolicy) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature, ?string $permission = null): Response
    {
        $decision = $this->accessPolicy->decision($feature, $permission);

        if ($decision['allowed']) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(Response::HTTP_FORBIDDEN, $decision['message']);
        }

        if ($request->routeIs('mobile.dashboard')) {
            abort(Response::HTTP_FORBIDDEN, $decision['message']);
        }

        session()->flash('mobile_policy_denial', $decision['message']);
        session()->flash('mobile_policy_denial_reason', $decision['reason']);

        return redirect()->route('mobile.dashboard');
    }
}
