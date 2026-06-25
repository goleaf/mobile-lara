<?php

use App\Livewire\Mobile\EditProfile;
use App\Livewire\Mobile\Profile;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\MobileSessionService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

test('profile page renders authenticated account overview and shortcuts', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('avatars/current.jpg', 'avatar');

    $user = User::factory()->create([
        'name' => 'Taylor Mobile',
        'email' => 'taylor@example.test',
        'avatar_path' => 'avatars/current.jpg',
    ]);

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->assertSet('displayName', 'Taylor Mobile')
        ->assertSet('email', 'taylor@example.test')
        ->assertSet('avatarUrl', Storage::disk('public')->url('avatars/current.jpg'))
        ->assertSet('phone', 'Not added')
        ->assertSet('bio', 'Local mobile account')
        ->assertSet('avatarInitials', 'TM')
        ->assertSet('accountStatus', 'Verified')
        ->assertSee('Taylor Mobile')
        ->assertSee('taylor@example.test')
        ->assertSee('Not added')
        ->assertSee('Local mobile account')
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
        ->assertSet('successMessage', 'Profile details and avatar saved locally.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Profile saved'
                && ($params['message'] ?? null) === 'Profile details and avatar saved locally.';
        })
        ->assertSee('Mobile profile owner')
        ->assertSee('Avatar ready: avatar.png');

    $user->refresh();

    expect($user->name)->toBe('Updated Person')
        ->and($user->avatar_path)->toBeString()
        ->and(Str::startsWith((string) $user->avatar_path, 'avatars/'))->toBeTrue();

    Storage::disk('public')->assertExists((string) $user->avatar_path);
});

test('edit profile screen removes a saved avatar on save', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('avatars/current.jpg', 'avatar');

    $user = User::factory()->create([
        'avatar_path' => 'avatars/current.jpg',
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
        ->assertSet('successMessage', 'Profile details and avatar saved locally.');

    expect($user->refresh()->avatar_path)->toBeNull();

    Storage::disk('public')->assertMissing('avatars/current.jpg');
});

test('edit profile syncs account name through api when access token exists', function (): void {
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
        ->call('saveProfile')
        ->assertSet('successMessage', 'Profile details saved with API.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Profile saved'
                && ($params['message'] ?? null) === 'Profile details saved with API.';
        });

    expect($user->refresh()->name)->toBe('API Updated Person');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-update-access-token')
        && $request['name'] === 'API Updated Person');
});

test('native camera photo can be previewed and saved as avatar', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('native-camera.jpg', 256, 256);
    $operationId = 'native-camera-avatar';

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('pendingNativeOperationId', $operationId)
        ->call('handleNativePhotoTaken', $photo->getRealPath(), 'image/jpeg', $operationId)
        ->assertSet('avatarUploadName', 'Camera photo')
        ->assertSet('pendingNativeOperationId', null)
        ->assertSet('nativeAvatarStatus', 'Camera photo ready to preview and save.')
        ->assertSee('Avatar ready: Camera photo')
        ->call('saveProfile')
        ->assertSet('savedAvatarPath', fn (?string $path): bool => is_string($path) && Str::startsWith($path, 'avatars/'))
        ->assertSet('successMessage', 'Profile details and avatar saved locally.');

    $avatarPath = $user->refresh()->avatar_path;

    expect($avatarPath)->toBeString()
        ->and(Str::startsWith((string) $avatarPath, 'avatars/'))->toBeTrue();

    Storage::disk('public')->assertExists((string) $avatarPath);
});

test('native gallery image can be previewed and saved as avatar', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('native-gallery.png', 256, 256);
    $operationId = 'native-gallery-avatar';

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
        ->assertSet('savedAvatarPath', fn (?string $path): bool => is_string($path) && Str::startsWith($path, 'avatars/'))
        ->assertSet('successMessage', 'Profile details and avatar saved locally.');

    $avatarPath = $user->refresh()->avatar_path;

    expect($avatarPath)->toBeString()
        ->and(Str::startsWith((string) $avatarPath, 'avatars/'))->toBeTrue();

    Storage::disk('public')->assertExists((string) $avatarPath);
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
