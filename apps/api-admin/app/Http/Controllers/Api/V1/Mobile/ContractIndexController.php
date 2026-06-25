<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileContractRegistry;
use Illuminate\Http\JsonResponse;

final class ContractIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $catalogue = MobileContractRegistry::catalogue();

        return MobileApiResponse::success($catalogue, [
            'contract_count' => count($catalogue['contracts']),
            'next_contract' => 'v1-auth',
        ]);
    }
}
