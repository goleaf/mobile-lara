<?php

use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileNotifications\MobileNotificationsApiService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 12:00:00'));

    config([
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_notifications_api.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_notifications_api.revoked_tokens',
    ]);

    Http::preventStrayRequests();

    app(AccessTokenService::class)->put('notifications-api-access-token', CarbonImmutable::now()->addMinutes(15));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('mobile notifications api service calls inbox and mutation endpoints', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/notifications/notification-001/read' => Http::response(mobileNotificationsApiServiceEnvelope([
            'notification' => ['id' => 'notification-001', 'read_at' => '2026-06-26T12:00:00+00:00'],
            'unread_count' => 0,
        ])),
        'https://api-admin.example.test/api/v1/mobile/notifications/read-all' => Http::response(mobileNotificationsApiServiceEnvelope([
            'updated_count' => 3,
            'unread_count' => 0,
        ])),
        'https://api-admin.example.test/api/v1/mobile/notifications/notification-001' => Http::response(mobileNotificationsApiServiceEnvelope([
            'deleted' => true,
            'notification_id' => 'notification-001',
        ])),
        'https://api-admin.example.test/api/v1/mobile/notifications/push-tokens' => Http::response(mobileNotificationsApiServiceEnvelope([
            'push_token' => ['id' => 'push-token-001'],
        ]), 201),
        'https://api-admin.example.test/api/v1/mobile/notifications/push-tokens/push-token-001' => Http::response(mobileNotificationsApiServiceEnvelope([
            'revoked' => true,
            'push_token_id' => 'push-token-001',
        ])),
        'https://api-admin.example.test/api/v1/mobile/notifications*' => Http::response(mobileNotificationsApiServiceEnvelope([
            'notifications' => [
                ['id' => 'notification-001', 'title' => 'Hello'],
            ],
            'unread_count' => 1,
        ])),
    ]);

    $service = app(MobileNotificationsApiService::class);

    expect($service->list(['state' => 'unread'])['unread_count'])->toBe(1)
        ->and($service->markRead('notification-001')['unread_count'])->toBe(0)
        ->and($service->markAllRead()['updated_count'])->toBe(3)
        ->and($service->delete('notification-001')['deleted'])->toBeTrue()
        ->and($service->registerPushToken(['token' => 'token-value', 'provider' => 'apns', 'platform' => 'ios'])['push_token']['id'])->toBe('push-token-001')
        ->and($service->revokePushToken('push-token-001')['revoked'])->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications?state=unread'
        && $request->hasHeader('Authorization', 'Bearer notifications-api-access-token'));
    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications/notification-001/read');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications/read-all');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications/notification-001');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications/push-tokens');
    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications/push-tokens/push-token-001');
});

test('mobile notifications api service requires an access token', function (): void {
    app(AccessTokenService::class)->forget();

    Http::fake();

    app(MobileNotificationsApiService::class)->list();
})->throws(MobileApiException::class, 'A valid mobile session is required.');

/**
 * @param  array<string, mixed>  $data
 * @return array<string, mixed>
 */
function mobileNotificationsApiServiceEnvelope(array $data): array
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
