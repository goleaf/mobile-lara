<?php

use App\Livewire\Mobile\EditProfile;
use App\Livewire\Mobile\Profile;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\MobileSessionService;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    config([
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.session_key' => 'testing.mobile_auth.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_auth.revoked_tokens',
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    $mobileProfileLocalDatabasePath = storage_path('framework/testing/mobile-profile-policy.sqlite');

    if (File::exists($mobileProfileLocalDatabasePath)) {
        File::delete($mobileProfileLocalDatabasePath);
    }
});

test('profile page renders authenticated account overview and shortcuts', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('avatars/current.jpg', 'avatar');

    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
        'phone' => '+370 600 11111',
        'bio' => 'Mobile field operator',
        'avatar_path' => 'avatars/current.jpg',
    ]);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->assertSet('displayName', 'Taylor Mobile')
        ->assertSet('email', 'taylor@example.test')
        ->assertSet('avatarUrl', Storage::disk('public')->url('avatars/current.jpg'))
        ->assertSet('phone', '+370 600 11111')
        ->assertSet('bio', 'Mobile field operator')
        ->assertSet('avatarInitials', 'TM')
        ->assertSet('accountStatus', 'Verified')
        ->assertSee('Taylor Mobile')
        ->assertSee('taylor@example.test')
        ->assertSee('+370 600 11111')
        ->assertSee('Mobile field operator')
        ->assertSee('Verified')
        ->assertSee('Edit profile')
        ->assertSee('Share profile')
        ->assertSee('Security')
        ->assertSee('Notifications')
        ->assertSee('Logout')
        ->assertSee(route('mobile.profile.edit'), false)
        ->assertSee(route('mobile.settings.security'), false)
        ->assertSee(route('mobile.settings.notifications'), false);
});

test('profile share action is hidden and blocked by disabled share policy', function (): void {
    migrateMobileProfileLocalDatabase();

    app(SettingsRepository::class)->cacheBootstrapContext(mobileProfilePolicyBootstrapEnvelope([
        'native_share' => mobileProfilePolicyFeature(
            enabled: false,
            state: 'hidden',
            message: 'Profile sharing is disabled by admin policy.',
        ),
    ]));

    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
    ]);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->assertDontSee('Share profile')
        ->assertDontSee('wire:click="shareProfile"', false)
        ->call('shareProfile')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Share unavailable'
                && ($params['message'] ?? null) === 'Profile sharing is disabled by admin policy.';
        });
});

test('profile share button reports browser fallback outside NativePHP', function (): void {
    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
    ]);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->call('shareProfile')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Share unavailable'
                && ($params['message'] ?? null) === 'Native URL sharing is unavailable in this browser runtime.';
        });
});

test('edit profile screen renders editable fields and saves valid details', function (): void {
    Storage::fake('public');

    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
    ]);

    $avatar = UploadedFile::fake()->image('avatar.png', 256, 256);

    app(AccessTokenService::class)->put('profile-edit-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response(mobileProfileApiEnvelope(
            name: 'Updated Person',
            email: 'taylor@example.test',
            avatarPath: 'avatars/api-updated-profile.png',
            username: 'updated.person',
            phone: '+370 600 00000',
            bio: 'Mobile profile owner',
            location: 'Vilnius',
            website: 'https://example.test',
        )),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->assertSet('name', 'Taylor Mobile')
        ->assertSet('username', 'taylor')
        ->assertSet('avatarInitials', 'TM')
        ->assertSee('Saving profile...')
        ->assertSee('Edit profile')
        ->assertSee('Profile photo')
        ->assertSee('Take photo')
        ->assertSee('Gallery')
        ->assertSee('Name')
        ->assertSee('Username')
        ->assertSee('Phone')
        ->assertSee('Bio')
        ->assertSee('Location')
        ->assertSee('Website')
        ->set('name', 'Updated Person')
        ->set('username', 'updated.person')
        ->set('phone', '+370 600 00000')
        ->set('bio', 'Mobile profile owner')
        ->set('location', 'Vilnius')
        ->set('website', 'https://example.test')
        ->set('avatar', $avatar)
        ->call('saveProfile')
        ->assertSet('name', 'Updated Person')
        ->assertSet('username', 'updated.person')
        ->assertSet('phone', '+370 600 00000')
        ->assertSet('bio', 'Mobile profile owner')
        ->assertSet('location', 'Vilnius')
        ->assertSet('website', 'https://example.test')
        ->assertSet('avatarInitials', 'UP')
        ->assertSet('avatarUploadName', 'avatar.png')
        ->assertSet('successMessage', 'Profile details and avatar saved with API.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Profile saved'
                && ($params['message'] ?? null) === 'Profile details and avatar saved with API.';
        })
        ->assertSee('Mobile profile owner')
        ->assertSee('Avatar ready: avatar.png');

    $user->refresh();

    expect($user->name)->toBe('Updated Person')
        ->and($user->username)->toBe('updated.person')
        ->and($user->phone)->toBe('+370 600 00000')
        ->and($user->bio)->toBe('Mobile profile owner')
        ->and($user->location)->toBe('Vilnius')
        ->and($user->website)->toBe('https://example.test')
        ->and($user->avatar_path)->toBe('avatars/api-updated-profile.png');

    Storage::disk('public')->assertExists('avatars/api-updated-profile.png');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-edit-access-token')
        && str_contains($request->body(), 'name="name"')
        && str_contains($request->body(), 'Updated Person')
        && str_contains($request->body(), 'name="username"')
        && str_contains($request->body(), 'updated.person')
        && str_contains($request->body(), 'name="phone"')
        && str_contains($request->body(), '+370 600 00000')
        && str_contains($request->body(), 'name="bio"')
        && str_contains($request->body(), 'Mobile profile owner')
        && str_contains($request->body(), 'name="location"')
        && str_contains($request->body(), 'Vilnius')
        && str_contains($request->body(), 'name="website"')
        && str_contains($request->body(), 'https://example.test'));
});

test('edit profile hydrates saved api profile details from the local mirror', function (): void {
    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
        'username' => 'taylor.saved',
        'phone' => '+370 600 22222',
        'bio' => 'Saved API profile',
        'location' => 'Kaunas',
        'website' => 'https://saved.example.test',
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->assertSet('name', 'Taylor Mobile')
        ->assertSet('username', 'taylor.saved')
        ->assertSet('phone', '+370 600 22222')
        ->assertSet('bio', 'Saved API profile')
        ->assertSet('location', 'Kaunas')
        ->assertSet('website', 'https://saved.example.test');
});

test('edit profile screen removes a saved avatar on save', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('avatars/current.jpg', 'avatar');

    $user = User::factory()->create([
        'avatar_path' => 'avatars/current.jpg',
    ]);

    app(AccessTokenService::class)->put('profile-remove-avatar-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response(mobileProfileApiEnvelope(
            name: (string) $user->name,
            email: (string) $user->email,
            avatarPath: null,
        )),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->assertSet('savedAvatarPath', 'avatars/current.jpg')
        ->assertSee('Remove avatar')
        ->call('removeAvatar')
        ->assertSet('avatarMarkedForRemoval', true)
        ->assertSet('nativeAvatarStatus', 'Current avatar will be removed when you save.')
        ->assertSee('Avatar marked for removal. Save profile to apply.')
        ->call('saveProfile')
        ->assertSet('savedAvatarPath', null)
        ->assertSet('avatarMarkedForRemoval', false)
        ->assertSet('successMessage', 'Profile details and avatar saved with API.');

    expect($user->refresh()->avatar_path)->toBeNull();

    Storage::disk('public')->assertMissing('avatars/current.jpg');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-remove-avatar-token')
        && $request['remove_avatar'] === true);
});

test('edit profile syncs account details through api when access token exists', function (): void {
    Storage::fake('public');

    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
    ]);

    app(AccessTokenService::class)->put('profile-update-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => 123,
                    'name' => 'API Updated Person',
                    'email' => 'taylor@example.test',
                    'username' => 'api.updated',
                    'phone' => '+370 600 55555',
                    'bio' => 'Updated through API.',
                    'location' => 'Riga',
                    'website' => 'https://api.example.test',
                    'email_verified_at' => '2026-06-25T12:00:00+00:00',
                ],
                'session' => ['id' => 99, 'status' => 'active'],
                'next_bootstrap_required' => true,
            ],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('name', 'API Updated Person')
        ->set('username', 'api.updated')
        ->set('phone', '+370 600 55555')
        ->set('bio', 'Updated through API.')
        ->set('location', 'Riga')
        ->set('website', 'https://api.example.test')
        ->call('saveProfile')
        ->assertSet('username', 'api.updated')
        ->assertSet('phone', '+370 600 55555')
        ->assertSet('bio', 'Updated through API.')
        ->assertSet('location', 'Riga')
        ->assertSet('website', 'https://api.example.test')
        ->assertSet('successMessage', 'Profile details saved with API.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Profile saved'
                && ($params['message'] ?? null) === 'Profile details saved with API.';
        });

    $user->refresh();

    expect($user->name)->toBe('API Updated Person')
        ->and($user->username)->toBe('api.updated')
        ->and($user->phone)->toBe('+370 600 55555')
        ->and($user->bio)->toBe('Updated through API.')
        ->and($user->location)->toBe('Riga')
        ->and($user->website)->toBe('https://api.example.test');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-update-access-token')
        && $request['name'] === 'API Updated Person'
        && $request['username'] === 'api.updated'
        && $request['phone'] === '+370 600 55555'
        && $request['bio'] === 'Updated through API.'
        && $request['location'] === 'Riga'
        && $request['website'] === 'https://api.example.test');
});

test('edit profile syncs uploaded avatar through api when access token exists', function (): void {
    Storage::fake('public');

    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
    ]);

    app(AccessTokenService::class)->put('profile-avatar-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => 123,
                    'name' => 'Avatar Person',
                    'email' => 'taylor@example.test',
                    'avatar_path' => 'avatars/api-avatar.png',
                    'avatar_url' => 'https://api-admin.example.test/storage/avatars/api-avatar.png',
                    'email_verified_at' => '2026-06-25T12:00:00+00:00',
                ],
                'session' => ['id' => 99, 'status' => 'active'],
                'next_bootstrap_required' => true,
            ],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('name', 'Avatar Person')
        ->set('avatar', UploadedFile::fake()->image('avatar.png', 256, 256))
        ->call('saveProfile')
        ->assertSet('successMessage', 'Profile details and avatar saved with API.')
        ->assertSet('savedAvatarPath', 'avatars/api-avatar.png')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Profile saved'
                && ($params['message'] ?? null) === 'Profile details and avatar saved with API.';
        });

    expect($user->refresh()->avatar_path)->toBe('avatars/api-avatar.png');

    Storage::disk('public')->assertExists('avatars/api-avatar.png');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-avatar-access-token'));
});

test('edit profile does not update local mirror when api rejects profile save', function (): void {
    Storage::fake('public');

    $user = User::factory()->create([
        'name' => 'Original Person',
        'email' => 'original@example.test',
        'username' => 'original.person',
        'phone' => '+370 600 33333',
    ]);

    app(AccessTokenService::class)->put('profile-failure-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response([
            'success' => false,
            'error' => [
                'code' => 'profile_update_failed',
                'message' => 'The profile update was rejected by the API.',
                'category' => 'validation',
                'next_action' => 'fix_form',
            ],
            'meta' => ['api_version' => 'v1'],
        ], 422),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('name', 'Rejected Person')
        ->set('username', 'rejected.person')
        ->set('phone', '+370 600 44444')
        ->call('saveProfile')
        ->assertSet('successMessage', null)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Profile save blocked';
        });

    $user->refresh();

    expect($user->name)->toBe('Original Person')
        ->and($user->username)->toBe('original.person')
        ->and($user->phone)->toBe('+370 600 33333');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-failure-token'));
});

test('native camera photo can be previewed and saved as avatar', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('native-camera.jpg', 256, 256);
    $operationId = 'native-camera-avatar';

    app(AccessTokenService::class)->put('profile-native-camera-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response(mobileProfileApiEnvelope(
            name: (string) $user->name,
            email: (string) $user->email,
            avatarPath: 'avatars/api-native-camera.jpg',
        )),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('pendingNativeOperationId', $operationId)
        ->call('handleNativePhotoTaken', $photo->getRealPath(), 'image/jpeg', $operationId)
        ->assertSet('avatarUploadName', 'Camera photo')
        ->assertSet('pendingNativeOperationId', null)
        ->assertSet('nativeAvatarStatus', 'Camera photo ready to preview and save.')
        ->assertSee('Avatar ready: Camera photo')
        ->call('saveProfile')
        ->assertSet('savedAvatarPath', 'avatars/api-native-camera.jpg')
        ->assertSet('successMessage', 'Profile details and avatar saved with API.');

    $avatarPath = $user->refresh()->avatar_path;

    expect($avatarPath)->toBe('avatars/api-native-camera.jpg');

    Storage::disk('public')->assertExists((string) $avatarPath);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-native-camera-token'));
});

test('native gallery image can be previewed and saved as avatar', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('native-gallery.png', 256, 256);
    $operationId = 'native-gallery-avatar';

    app(AccessTokenService::class)->put('profile-native-gallery-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response(mobileProfileApiEnvelope(
            name: (string) $user->name,
            email: (string) $user->email,
            avatarPath: 'avatars/api-native-gallery.png',
        )),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('pendingNativeOperationId', $operationId)
        ->call('handleNativeMediaSelected', true, [[
            'path' => $photo->getRealPath(),
            'mimeType' => 'image/png',
            'extension' => 'png',
            'type' => 'image',
        ]], 1, null, false, $operationId)
        ->assertSet('avatarUploadName', 'Gallery image')
        ->assertSet('pendingNativeOperationId', null)
        ->assertSet('nativeAvatarStatus', 'Gallery image ready to preview and save.')
        ->assertSee('Avatar ready: Gallery image')
        ->call('saveProfile')
        ->assertSet('savedAvatarPath', 'avatars/api-native-gallery.png')
        ->assertSet('successMessage', 'Profile details and avatar saved with API.');

    $avatarPath = $user->refresh()->avatar_path;

    expect($avatarPath)->toBe('avatars/api-native-gallery.png');

    Storage::disk('public')->assertExists((string) $avatarPath);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-native-gallery-token'));
});

test('native avatar controls fall back to browser upload outside NativePHP', function (): void {
    Livewire::test(EditProfile::class)
        ->call('takeAvatarPhoto')
        ->assertSet('pendingNativeOperationId', null)
        ->assertSet('nativeAvatarStatus', 'Native camera and gallery are available inside the NativePHP mobile app. Use the file picker in this browser.');
});

test('edit profile screen validates required fields formats and avatar type', function (): void {
    Livewire::test(EditProfile::class)
        ->set('name', '')
        ->set('username', 'bad username')
        ->set('phone', 'call me maybe')
        ->set('website', 'not-a-url')
        ->set('avatar', UploadedFile::fake()->create('avatar.txt', 4, 'text/plain'))
        ->call('saveProfile')
        ->assertHasErrors([
            'name' => 'required',
            'username' => 'regex',
            'phone' => 'regex',
            'website' => 'url',
            'avatar' => 'image',
        ]);
});

test('profile logout redirects and clears local mobile state', function (): void {
    $this->actingAs(User::factory()->create());

    app(AppUnlockStateService::class)->unlock();
    app(AccessTokenService::class)->put('profile-logout-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/logout' => Http::response([
            'success' => true,
            'data' => ['revoked' => true],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    session()->put(
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        CarbonImmutable::now()->toIso8601String(),
    );

    Livewire::test(Profile::class)
        ->call('logout')
        ->assertRedirect(route('mobile.login'));

    expect(session()->has(MobileSessionService::LAST_LOGIN_AT_SESSION_KEY))->toBeFalse()
        ->and(app(AccessTokenService::class)->get())->toBeNull()
        ->and(app(AppUnlockStateService::class)->isUnlocked())->toBeFalse();

    $this->assertGuest();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/logout'
        && $request->hasHeader('Authorization', 'Bearer profile-logout-access-token'));
});

/**
 * @return array<string, mixed>
 */
function mobileProfileApiEnvelope(
    string $name,
    string $email,
    ?string $avatarPath = null,
    ?string $username = null,
    ?string $phone = null,
    ?string $bio = null,
    ?string $location = null,
    ?string $website = null,
): array {
    return [
        'success' => true,
        'data' => [
            'user' => [
                'id' => 123,
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'phone' => $phone,
                'bio' => $bio,
                'location' => $location,
                'website' => $website,
                'avatar_path' => $avatarPath,
                'avatar_url' => is_string($avatarPath) ? "https://api-admin.example.test/storage/{$avatarPath}" : null,
                'email_verified_at' => '2026-06-25T12:00:00+00:00',
            ],
            'session' => ['id' => 99, 'status' => 'active'],
            'next_bootstrap_required' => true,
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}

function migrateMobileProfileLocalDatabase(): void
{
    $mobileProfileLocalDatabasePath = storage_path('framework/testing/mobile-profile-policy.sqlite');

    File::ensureDirectoryExists(dirname($mobileProfileLocalDatabasePath));

    if (File::exists($mobileProfileLocalDatabasePath)) {
        File::delete($mobileProfileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $mobileProfileLocalDatabasePath,
        'mobile_local.database' => $mobileProfileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
}

/**
 * @param  array<string, array<string, mixed>>  $features
 * @return array<string, mixed>
 */
function mobileProfilePolicyBootstrapEnvelope(array $features): array
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
                'abilities' => [],
                'ability_list' => [],
            ],
            'features' => [
                'version' => 'mobile-profile-policy-test',
                'items' => $features,
            ],
            'remote_config' => ['version' => 'mobile-profile-policy-test', 'values' => []],
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
            'bootstrap_version' => 'mobile-profile-policy-test',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileProfilePolicyFeature(bool $enabled, string $state, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $enabled ? null : 'feature_disabled_by_admin',
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'mobile_profile_policy_test',
    ];
}
