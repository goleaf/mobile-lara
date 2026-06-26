<?php

use App\Livewire\Mobile\SupportTicketCreate;
use App\Livewire\Mobile\SupportTicketDetail;
use App\Livewire\Mobile\SupportTickets;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-support-screens.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_support_screens.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_support_screens.revoked_tokens',
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    Http::preventStrayRequests();

    app(AccessTokenService::class)->put('support-screen-access-token', CarbonImmutable::now()->addMinutes(15));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('support ticket list renders requester scoped tickets from the mobile api', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/support/tickets*' => Http::response(mobileSupportScreensEnvelope([
            'tickets' => [
                mobileSupportScreensTicket([
                    'id' => 'ticket-sync-001',
                    'subject' => 'Sync is stuck',
                    'status' => 'open',
                    'priority' => 'high',
                    'messages_count' => 2,
                ]),
                mobileSupportScreensTicket([
                    'id' => 'ticket-billing-002',
                    'subject' => 'Billing receipt',
                    'status' => 'waiting_on_user',
                    'priority' => 'normal',
                ]),
            ],
        ])),
    ]);

    Livewire::test(SupportTickets::class)
        ->assertSee('Support tickets')
        ->assertSee('Sync is stuck')
        ->assertSee('Billing receipt')
        ->assertSee('2 shown')
        ->call('setStatus', 'open')
        ->assertSet('status', 'open')
        ->set('search', 'sync')
        ->call('loadTickets')
        ->assertSee('Sync is stuck');

    Http::assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer support-screen-access-token')
        && $request->method() === 'GET'
        && str_starts_with($request->url(), 'https://api-admin.example.test/api/v1/mobile/support/tickets'));
});

test('support ticket create posts to the mobile api and redirects to detail', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/support/tickets' => Http::response(mobileSupportScreensEnvelope([
            'ticket' => mobileSupportScreensTicket([
                'id' => 'ticket-new-001',
                'subject' => 'Need help with sync',
                'messages_count' => 1,
            ]),
        ]), 201),
    ]);

    Livewire::test(SupportTicketCreate::class)
        ->set('subject', 'Need help with sync')
        ->set('body', 'My latest record changes are not uploading.')
        ->set('priority', 'high')
        ->set('category', 'sync')
        ->set('diagnosticReportId', 'diagnostic-001')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('mobile.support.show', ['ticket' => 'ticket-new-001']));

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/support/tickets'
        && $request->data()['subject'] === 'Need help with sync'
        && $request->data()['body'] === 'My latest record changes are not uploading.'
        && $request->data()['priority'] === 'high'
        && $request->data()['category'] === 'sync'
        && $request->data()['diagnostic_report_id'] === 'diagnostic-001');
});

test('support ticket detail renders messages and posts replies through the mobile api', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-sync-001/messages' => Http::response(mobileSupportScreensEnvelope([
            'ticket' => mobileSupportScreensTicket([
                'id' => 'ticket-sync-001',
                'subject' => 'Sync is stuck',
                'messages_count' => 2,
                'messages' => [
                    mobileSupportScreensMessage('Initial help request.'),
                    mobileSupportScreensMessage('Here is more context.'),
                ],
            ]),
        ]), 201),
        'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-sync-001' => Http::response(mobileSupportScreensEnvelope([
            'ticket' => mobileSupportScreensTicket([
                'id' => 'ticket-sync-001',
                'subject' => 'Sync is stuck',
                'messages_count' => 1,
                'messages' => [
                    mobileSupportScreensMessage('Initial help request.'),
                ],
            ]),
        ])),
    ]);

    Livewire::test(SupportTicketDetail::class, ['ticket' => 'ticket-sync-001'])
        ->assertSee('Sync is stuck')
        ->assertSee('Initial help request.')
        ->set('messageBody', 'Here is more context.')
        ->call('sendMessage')
        ->assertHasNoErrors()
        ->assertSet('messageBody', '')
        ->assertSee('Here is more context.');

    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-sync-001/messages'
        && $request->data()['body'] === 'Here is more context.');
});

test('support screens fail closed when cached admin policy disables support', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileSupportScreensBootstrapEnvelope([
        'support' => mobileSupportScreensFeature(
            enabled: false,
            state: 'disabled',
            message: 'Support tickets are disabled by admin policy.',
        ),
    ], abilities: [
        'support' => ['view' => true, 'create' => true],
    ]));

    Http::fake();

    Livewire::test(SupportTickets::class)
        ->assertSee('Support disabled')
        ->assertSee('Support tickets are disabled by admin policy.')
        ->assertDontSee('wire:click="loadTickets"', false);

    Livewire::test(SupportTicketCreate::class)
        ->assertSee('Support disabled')
        ->set('subject', 'Need help')
        ->set('body', 'This should not be sent.')
        ->call('submit')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Ticket not created'
                && ($params['message'] ?? null) === 'Support tickets are disabled by admin policy.';
        });

    Http::assertNothingSent();
});

test('support routes render for authenticated users', function (string $route, string $component, string $pageTitle, array $parameters = []): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/support/tickets/ticket-route-001' => Http::response(mobileSupportScreensEnvelope([
            'ticket' => mobileSupportScreensTicket([
                'id' => 'ticket-route-001',
                'subject' => 'Route ticket',
            ]),
        ])),
        'https://api-admin.example.test/api/v1/mobile/support/tickets*' => Http::response(mobileSupportScreensEnvelope([
            'tickets' => [],
        ])),
    ]);

    $this->get(route($route, $parameters))
        ->assertOk()
        ->assertSeeLivewire($component)
        ->assertSee($pageTitle);
})->with([
    'support index' => ['mobile.support.index', SupportTickets::class, 'Support tickets', []],
    'support create' => ['mobile.support.create', SupportTicketCreate::class, 'Create support ticket', []],
    'support show' => ['mobile.support.show', SupportTicketDetail::class, 'Route ticket', ['ticket' => 'ticket-route-001']],
]);

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function mobileSupportScreensTicket(array $overrides = []): array
{
    return array_replace_recursive([
        'id' => 'ticket-001',
        'subject' => 'Need help',
        'status' => 'open',
        'priority' => 'normal',
        'category' => 'general',
        'source' => 'mobile',
        'assignment' => ['assigned' => false, 'agent' => null],
        'support_context' => [],
        'messages_count' => 0,
        'messages' => [],
        'last_message_at' => '2026-06-26T12:00:00+00:00',
        'closed_at' => null,
        'created_at' => '2026-06-26T12:00:00+00:00',
        'updated_at' => '2026-06-26T12:00:00+00:00',
        'allowed_actions' => [
            'view' => true,
            'add_message' => true,
            'attach_metadata' => true,
            'attach_diagnostics' => true,
        ],
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function mobileSupportScreensMessage(string $body): array
{
    return [
        'id' => fake()->uuid(),
        'body' => $body,
        'direction' => 'from_user',
        'visibility' => 'requester',
        'author' => ['id' => 123, 'name' => 'Mobile User'],
        'attachments' => [],
        'diagnostic_report_id' => null,
        'metadata' => [],
        'created_at' => '2026-06-26T12:00:00+00:00',
        'updated_at' => '2026-06-26T12:00:00+00:00',
    ];
}

/**
 * @param  array<string, mixed>  $data
 * @return array<string, mixed>
 */
function mobileSupportScreensEnvelope(array $data): array
{
    return [
        'success' => true,
        'data' => $data,
        'meta' => [
            'api_version' => 'v1',
            'support_version' => 'foundation-support-1',
            'server_time' => '2026-06-26T12:00:00+00:00',
        ],
    ];
}

/**
 * @param  array<string, array<string, mixed>>  $features
 * @param  array<string, array<string, bool>>  $abilities
 * @return array<string, mixed>
 */
function mobileSupportScreensBootstrapEnvelope(array $features = [], array $abilities = []): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => [
                'id' => 'tenant-001',
                'name' => 'North Field Team',
                'status' => 'active',
                'subscription_state' => 'active',
            ],
            'available_tenants' => [],
            'permissions' => [
                'status' => 'resolved',
                'roles' => [],
                'abilities' => $abilities,
                'ability_list' => mobileSupportScreensAbilityList($abilities),
            ],
            'features' => [
                'version' => 'support-policy',
                'items' => array_replace([
                    'support' => mobileSupportScreensFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'support-policy', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => true, 'reason' => null],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'support-policy',
            'server_time' => '2026-06-26T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileSupportScreensFeature(bool $enabled, string $state, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $enabled ? null : 'feature_disabled_by_admin',
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'test_policy',
    ];
}

/**
 * @param  array<string, array<string, bool>>  $abilities
 * @return list<string>
 */
function mobileSupportScreensAbilityList(array $abilities): array
{
    $abilityList = [];

    foreach ($abilities as $group => $items) {
        foreach ($items as $ability => $granted) {
            if ($granted) {
                $abilityList[] = $group.'.'.$ability;
            }
        }
    }

    return $abilityList;
}
