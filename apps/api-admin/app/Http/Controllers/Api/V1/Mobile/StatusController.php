<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class StatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return MobileApiResponse::success([
            'service' => 'api-admin',
            'authority' => 'admin_api',
            'mobile_api' => 'v1',
            'status' => 'ok',
        ], [
            'next_contract' => 'v1-bootstrap',
        ]);
    }
}
