<?php

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileSupport\MobileSupportApiService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 12:00:00'));

    config([
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_support_api.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_support_api.revoked_tokens',
    ]);

    Http::preventStrayRequests();

    app(AccessTokenService::class)->put('support-api-access-token', CarbonImmutable::now()->addMinutes(15));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('mobile support api service calls ticket endpoints', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-001/messages' => Http::response(mobileSupportApiServiceEnvelope([
            'ticket' => ['id' => 'ticket-001', 'messages_count' => 2],
        ]), 201),
        'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-001' => Http::response(mobileSupportApiServiceEnvelope([
            'ticket' => ['id' => 'ticket-001', 'subject' => 'Need help'],
        ])),
        'https://api-admin.example.test/api/v1/mobile/support/tickets' => Http::response(mobileSupportApiServiceEnvelope([
            'ticket' => ['id' => 'ticket-001', 'subject' => 'Need help'],
        ]), 201),
        'https://api-admin.example.test/api/v1/mobile/support/tickets*' => Http::response(mobileSupportApiServiceEnvelope([
            'tickets' => [
                ['id' => 'ticket-001', 'subject' => 'Need help'],
            ],
        ])),
    ]);

    $service = app(MobileSupportApiService::class);

    expect($service->listTickets(['status' => 'open'])['tickets'][0]['id'])->toBe('ticket-001')
        ->and($service->createTicket(['subject' => 'Need help', 'body' => 'Hello'])['id'])->toBe('ticket-001')
        ->and($service->showTicket('ticket-001')['subject'])->toBe('Need help')
        ->and($service->addMessage('ticket-001', ['body' => 'More context'])['messages_count'])->toBe(2);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/support/tickets?status=open'
        && $request->hasHeader('Authorization', 'Bearer support-api-access-token'));
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/support/tickets');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-001');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-001/messages');
});

test('mobile support api service requires an access token', function (): void {
    app(AccessTokenService::class)->forget();

    Http::fake();

    app(MobileSupportApiService::class)->listTickets();
})->throws(MobileApiException::class, 'A valid mobile session is required.');

/**
 * @param  array<string, mixed>  $data
 * @return array<string, mixed>
 */
function mobileSupportApiServiceEnvelope(array $data): array
{
    return [
        'success' => true,
        'data' => $data,
        'meta' => [
            'api_version' => 'v1',
            'server_time' => '2026-06-26T12:00:00+00:00',
        ],
    ];
}
