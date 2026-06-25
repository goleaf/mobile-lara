<?php

use App\Support\Api\MobileApiResponse;

test('mobile api error envelope is predictable', function (): void {
    $response = MobileApiResponse::error(
        code: 'maintenance',
        message: 'Mobile API is temporarily unavailable.',
        category: 'maintenance',
        nextAction: 'retry_later',
        status: 503,
    );

    $payload = $response->getData(true);

    expect($response->getStatusCode())->toBe(503)
        ->and($payload)->toMatchArray([
            'success' => false,
            'error' => [
                'code' => 'maintenance',
                'message' => 'Mobile API is temporarily unavailable.',
                'category' => 'maintenance',
                'next_action' => 'retry_later',
            ],
        ])
        ->and($payload['meta']['api_version'])->toBe('v1')
        ->and($payload['meta'])->toHaveKey('server_time');
});
