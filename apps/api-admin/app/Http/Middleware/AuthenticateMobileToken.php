<?php

namespace App\Http\Middleware;

use App\Services\MobileAuth\MobileTokenAuthenticator;
use App\Support\Api\MobileApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticateMobileToken
{
    public function __construct(private MobileTokenAuthenticator $authenticator) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $this->authenticator->authenticateAccessToken($request->bearerToken());

        if ($accessToken === null) {
            return MobileApiResponse::error(
                code: 'unauthenticated',
                message: 'A valid mobile access token is required.',
                category: 'unauthenticated',
                nextAction: 'login',
                status: 401,
            );
        }

        $request->attributes->set('mobile_access_token', $accessToken);
        $request->attributes->set('mobile_device_session', $accessToken->deviceSession);
        $request->setUserResolver(fn () => $accessToken->user);

        return $next($request);
    }
}
