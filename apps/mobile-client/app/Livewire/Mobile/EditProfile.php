<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\User;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\MobileApiSessionBridge;
use App\Services\MobileAuth\MobileAuthApiService;
use App\Services\MobileProfile\AvatarStorageService;
use App\Services\MobileProfile\NativeAvatarSourceService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Camera\PermissionDenied;
use Native\Mobile\Events\Camera\PhotoCancelled;
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Events\Gallery\MediaSelected;
use Throwable;

#[Title('Edit profile')]
final class EditProfile extends Component
{
    use DispatchesToasts;
    use WithFileUploads;

    public string $name = '';

    public string $username = '';

    public string $phone = '';

    public string $bio = '';

    public string $location = '';

    public string $website = '';

    public mixed $avatar = null;

    public ?string $avatarUploadName = null;

    public ?string $successMessage = null;

    public string $avatarInitials = 'ML';

    public ?string $savedAvatarPath = null;

    public ?string $savedAvatarUrl = null;

    public ?string $pendingNativeAvatarPath = null;

    public ?string $pendingNativeAvatarUrl = null;

    public ?string $nativeAvatarStatus = null;

    public ?string $pendingNativeOperationId = null;

    public bool $avatarMarkedForRemoval = false;

    protected AvatarStorageService $avatarStorage;

    protected NativeAvatarSourceService $nativeAvatars;

    protected MobileAuthApiService $authApi;

    protected MobileApiSessionBridge $apiSessions;

    public function boot(
        AvatarStorageService $avatarStorage,
        NativeAvatarSourceService $nativeAvatars,
        MobileAuthApiService $authApi,
        MobileApiSessionBridge $apiSessions,
    ): void {
        $this->avatarStorage = $avatarStorage;
        $this->nativeAvatars = $nativeAvatars;
        $this->authApi = $authApi;
        $this->apiSessions = $apiSessions;
    }

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user instanceof User ? (string) $user->name : 'Mobile Lara';
        $this->username = $user instanceof User && is_string($user->username) && trim($user->username) !== ''
            ? trim($user->username)
            : $this->defaultUsername($user instanceof User ? (string) $user->email : $this->name);
        $this->phone = $user instanceof User && is_string($user->phone) ? trim($user->phone) : '';
        $this->bio = $user instanceof User && is_string($user->bio) ? trim($user->bio) : '';
        $this->location = $user instanceof User && is_string($user->location) ? trim($user->location) : '';
        $this->website = $user instanceof User && is_string($user->website) ? trim($user->website) : '';
        $this->savedAvatarPath = $user instanceof User ? $user->avatar_path : null;
        $this->savedAvatarUrl = $this->avatarStorage->url($this->savedAvatarPath);
        $this->refreshAvatarInitials();
    }

    public function updated(string $property): void
    {
        if ($property === 'avatar') {
            $this->clearPendingNativeAvatar();
            $this->avatarMarkedForRemoval = false;
            $this->avatarUploadName = $this->avatar instanceof UploadedFile
                ? $this->avatar->getClientOriginalName()
                : null;
            $this->nativeAvatarStatus = $this->avatarUploadName === null
                ? null
                : 'Selected image ready to preview and save.';
        }

        if (array_key_exists($property, $this->rules())) {
            $this->validateOnly($property);
        }

        if ($property === 'name') {
            $this->refreshAvatarInitials();
        }
    }

    public function saveProfile(): void
    {
        $validated = $this->validate();

        $this->name = trim((string) $validated['name']);
        $this->username = Str::lower(trim((string) $validated['username']));
        $this->phone = trim((string) ($validated['phone'] ?? ''));
        $this->bio = trim((string) ($validated['bio'] ?? ''));
        $this->location = trim((string) ($validated['location'] ?? ''));
        $this->website = trim((string) ($validated['website'] ?? ''));
        $this->refreshAvatarInitials();

        $user = Auth::user();
        $previousAvatarPath = $user instanceof User ? $user->avatar_path : $this->savedAvatarPath;
        $avatarPath = $previousAvatarPath;
        $avatarPathForApi = null;
        $removeAvatarInApi = false;
        $avatarChanged = false;
        $pendingNativeAvatarUsed = false;
        $stagedUploadedAvatarPath = null;

        if ($this->avatar instanceof UploadedFile) {
            $avatarPath = $this->avatarStorage->storeUploaded($this->avatar, deletePrevious: false);
            $stagedUploadedAvatarPath = $avatarPath;
            $avatarPathForApi = $avatarPath;
            $this->avatarUploadName = $this->avatar->getClientOriginalName();
            $avatarChanged = true;
        } elseif (is_string($this->pendingNativeAvatarPath)) {
            $avatarPath = $this->pendingNativeAvatarPath;
            $avatarPathForApi = $avatarPath;
            $avatarChanged = true;
            $pendingNativeAvatarUsed = true;
        } elseif ($this->avatarMarkedForRemoval) {
            $avatarPath = null;
            $removeAvatarInApi = true;
            $this->avatarUploadName = null;
            $avatarChanged = true;
        }

        $syncResult = $this->syncProfileWithApi($avatarPathForApi, $removeAvatarInApi);
        $syncedWithApi = $syncResult['synced'];

        if (! $syncedWithApi) {
            $this->avatarStorage->delete($stagedUploadedAvatarPath);
            $this->successMessage = null;
            $message = is_string($syncResult['message'] ?? null)
                ? $syncResult['message']
                : 'The mobile API did not accept the profile update.';
            $this->toastWarning("Profile was not saved because API sync failed: {$message}", 'Profile save blocked');

            return;
        }

        if (array_key_exists('avatar_path', $syncResult)) {
            $apiAvatarPath = $syncResult['avatar_path'];

            if (is_string($apiAvatarPath) && $apiAvatarPath !== $avatarPath) {
                $this->avatarStorage->copyWithinDisk($avatarPath, $apiAvatarPath);
                $this->avatarStorage->delete($avatarPath);
            }

            $avatarPath = $apiAvatarPath;
        }

        $this->applySyncedProfile(is_array($syncResult['profile'] ?? null) ? $syncResult['profile'] : []);

        if ($avatarChanged) {
            $this->avatarStorage->delete($previousAvatarPath, except: $avatarPath);
        }

        if ($pendingNativeAvatarUsed) {
            $this->clearPendingNativeAvatar(except: $avatarPath);
        }

        if ($user instanceof User) {
            $user->name = $this->name;
            $user->username = $this->username;
            $user->phone = $this->phone === '' ? null : $this->phone;
            $user->bio = $this->bio === '' ? null : $this->bio;
            $user->location = $this->location === '' ? null : $this->location;
            $user->website = $this->website === '' ? null : $this->website;
            $user->avatar_path = $avatarPath;
            $user->save();
        }

        $this->savedAvatarPath = $avatarPath;
        $this->savedAvatarUrl = $this->avatarStorage->url($avatarPath);
        $this->avatar = null;
        $this->avatarMarkedForRemoval = false;
        $this->nativeAvatarStatus = $avatarChanged
            ? 'Avatar saved to the mobile profile.'
            : $this->nativeAvatarStatus;

        $this->successMessage = $avatarChanged
            ? 'Profile details and avatar saved with API.'
            : 'Profile details saved with API.';
        $this->toastSuccess($this->successMessage, 'Profile saved');
    }

    public function takeAvatarPhoto(): void
    {
        $this->startNativeAvatarSelection('camera');
    }

    public function chooseAvatarFromGallery(): void
    {
        $this->startNativeAvatarSelection('gallery');
    }

    public function removeAvatar(): void
    {
        $this->resetValidation('avatar');
        $this->avatar = null;
        $this->avatarUploadName = null;
        $this->clearPendingNativeAvatar();

        if (is_string($this->savedAvatarPath)) {
            $this->avatarMarkedForRemoval = true;
            $this->nativeAvatarStatus = 'Current avatar will be removed when you save.';
            $this->toastWarning($this->nativeAvatarStatus, 'Avatar pending removal');

            return;
        }

        $this->avatarMarkedForRemoval = false;
        $this->nativeAvatarStatus = 'Selected avatar removed.';
        $this->toastInfo($this->nativeAvatarStatus, 'Avatar cleared');
    }

    #[OnNative(PhotoTaken::class)]
    public function handleNativePhotoTaken(string $path, string $mimeType = 'image/jpeg', ?string $id = null): void
    {
        if (! $this->matchesPendingNativeOperation($id)) {
            return;
        }

        $this->acceptNativeAvatar($path, $mimeType, 'Camera photo');
    }

    #[OnNative(PhotoCancelled::class)]
    public function handleNativePhotoCancelled(bool $cancelled = true, ?string $id = null): void
    {
        if (! $cancelled || ! $this->matchesPendingNativeOperation($id)) {
            return;
        }

        $this->pendingNativeOperationId = null;
        $this->nativeAvatarStatus = 'Camera capture cancelled.';
        $this->toastInfo($this->nativeAvatarStatus, 'Camera cancelled');
    }

    #[OnNative(MediaSelected::class)]
    public function handleNativeMediaSelected(
        bool $success,
        array $files = [],
        int $count = 0,
        ?string $error = null,
        bool $cancelled = false,
        ?string $id = null,
    ): void {
        if (! $this->matchesPendingNativeOperation($id)) {
            return;
        }

        if (! $success || $cancelled || $count < 1 || $files === []) {
            $this->pendingNativeOperationId = null;
            $this->nativeAvatarStatus = $cancelled
                ? 'Gallery selection cancelled.'
                : ($error ?: 'No gallery image was selected.');
            $this->toastInfo($this->nativeAvatarStatus, 'Gallery closed');

            return;
        }

        $file = $files[0];

        if (! is_array($file) || ($file['type'] ?? null) !== 'image' || ! is_string($file['path'] ?? null)) {
            $this->pendingNativeOperationId = null;
            $this->nativeAvatarStatus = 'Choose an image file for your avatar.';
            $this->addError('avatar', $this->nativeAvatarStatus);
            $this->toastError($this->nativeAvatarStatus, 'Avatar not selected');

            return;
        }

        $this->acceptNativeAvatar(
            $file['path'],
            is_string($file['mimeType'] ?? null) ? $file['mimeType'] : null,
            'Gallery image',
        );
    }

    #[OnNative(PermissionDenied::class)]
    public function handleNativePermissionDenied(string $action, ?string $id = null): void
    {
        if (! $this->matchesPendingNativeOperation($id)) {
            return;
        }

        $this->pendingNativeOperationId = null;
        $this->nativeAvatarStatus = "Native {$action} permission was denied.";
        $this->addError('avatar', $this->nativeAvatarStatus);
        $this->toastError($this->nativeAvatarStatus, 'Permission denied');
    }

    public function render(): View
    {
        return view('livewire.mobile.edit-profile', [
            'avatarPreviewUrl' => $this->avatarPreviewUrl(),
            'canRemoveAvatar' => $this->canRemoveAvatar(),
            'nativeAvatarAvailable' => $this->nativeAvatars->isAvailable(),
        ]);
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'phone' => ['nullable', 'string', 'max:32', 'regex:/^[0-9+\-\s().]*$/'],
            'bio' => ['nullable', 'string', 'max:280'],
            'location' => ['nullable', 'string', 'max:80'],
            'website' => ['nullable', 'url', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
        ];
    }

    private function startNativeAvatarSelection(string $source): void
    {
        $this->resetValidation('avatar');

        if (! $this->nativeAvatars->isAvailable()) {
            $this->nativeAvatarStatus = 'Native camera and gallery are available inside the NativePHP mobile app. Use the file picker in this browser.';
            $this->toastInfo($this->nativeAvatarStatus, 'Native source unavailable');

            return;
        }

        $id = Str::uuid()->toString();
        $this->pendingNativeOperationId = $id;

        $started = $source === 'camera'
            ? $this->nativeAvatars->startCamera($id)
            : $this->nativeAvatars->startGallery($id);

        if (! $started) {
            $this->pendingNativeOperationId = null;
            $this->nativeAvatarStatus = $source === 'camera'
                ? 'Unable to open the native camera.'
                : 'Unable to open the native gallery.';
            $this->addError('avatar', $this->nativeAvatarStatus);
            $this->toastError($this->nativeAvatarStatus, 'Native source failed');

            return;
        }

        $this->nativeAvatarStatus = $source === 'camera'
            ? 'Camera opened. Take a photo, then return here to preview it.'
            : 'Gallery opened. Choose a photo, then return here to preview it.';
        $this->toastInfo($this->nativeAvatarStatus, $source === 'camera' ? 'Camera opened' : 'Gallery opened');
    }

    private function acceptNativeAvatar(string $path, ?string $mimeType, string $label): void
    {
        try {
            $this->clearPendingNativeAvatar();
            $storedPath = $this->avatarStorage->storeNativePath($path, $mimeType);
        } catch (ValidationException $exception) {
            $this->pendingNativeOperationId = null;
            $message = (string) (collect($exception->errors())->flatten()->first() ?? 'The avatar could not be loaded.');
            $this->addError('avatar', $message);
            $this->nativeAvatarStatus = $message;
            $this->toastError($message, 'Avatar not ready');

            return;
        }

        $this->avatar = null;
        $this->avatarUploadName = $label;
        $this->avatarMarkedForRemoval = false;
        $this->pendingNativeOperationId = null;
        $this->pendingNativeAvatarPath = $storedPath;
        $this->pendingNativeAvatarUrl = $this->avatarStorage->url($storedPath);
        $this->nativeAvatarStatus = "{$label} ready to preview and save.";
        $this->toastSuccess($this->nativeAvatarStatus, 'Avatar ready');
    }

    private function avatarPreviewUrl(): ?string
    {
        if ($this->avatarMarkedForRemoval) {
            return null;
        }

        if ($this->avatar instanceof UploadedFile) {
            try {
                return $this->avatar->temporaryUrl();
            } catch (Throwable) {
                return null;
            }
        }

        if (is_string($this->pendingNativeAvatarUrl)) {
            return $this->pendingNativeAvatarUrl;
        }

        return $this->savedAvatarUrl;
    }

    private function canRemoveAvatar(): bool
    {
        return $this->avatar instanceof UploadedFile
            || is_string($this->pendingNativeAvatarPath)
            || (is_string($this->savedAvatarPath) && ! $this->avatarMarkedForRemoval);
    }

    private function clearPendingNativeAvatar(?string $except = null): void
    {
        $this->avatarStorage->delete($this->pendingNativeAvatarPath, except: $except);
        $this->pendingNativeAvatarPath = null;
        $this->pendingNativeAvatarUrl = null;
    }

    private function matchesPendingNativeOperation(?string $id): bool
    {
        if (! is_string($this->pendingNativeOperationId) || ! is_string($id)) {
            return false;
        }

        return hash_equals($this->pendingNativeOperationId, $id);
    }

    private function defaultUsername(string $source): string
    {
        $base = Str::contains($source, '@') ? Str::before($source, '@') : $source;
        $username = Str::of($base)
            ->lower()
            ->replaceMatches('/[^a-z0-9._-]+/', '_')
            ->trim('._-')
            ->limit(30, '')
            ->toString();

        return $username === '' ? 'mobile_user' : $username;
    }

    private function refreshAvatarInitials(): void
    {
        $initials = Str::of($this->name)
            ->replaceMatches('/[^\pL\pN\s]+/u', ' ')
            ->squish()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(static fn (string $word): string => Str::of($word)->substr(0, 1)->upper()->toString())
            ->implode('');

        $this->avatarInitials = $initials === '' ? 'ML' : $initials;
    }

    /**
     * @return array{synced: bool, avatar_path?: string|null, profile?: array<string, mixed>, message?: string}
     */
    private function syncProfileWithApi(?string $avatarPath = null, bool $removeAvatar = false): array
    {
        try {
            $envelope = $this->authApi->updateProfile(
                $this->profileApiPayload(),
                $this->avatarStorage->absolutePath($avatarPath),
                $removeAvatar,
            );
            $this->apiSessions->syncUser($envelope);

            $payload = $envelope['data']['user'] ?? [];
            $result = ['synced' => true, 'profile' => is_array($payload) ? $payload : []];

            if (is_array($payload) && array_key_exists('avatar_path', $payload)) {
                $apiAvatarPath = $payload['avatar_path'];
                $result['avatar_path'] = is_string($apiAvatarPath) && trim($apiAvatarPath) !== ''
                    ? trim($apiAvatarPath)
                    : null;
            }

            return $result;
        } catch (MobileApiException $exception) {
            return ['synced' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @return array{name: string, username: string, phone: string|null, bio: string|null, location: string|null, website: string|null}
     */
    private function profileApiPayload(): array
    {
        return [
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->nullableProfileValue($this->phone),
            'bio' => $this->nullableProfileValue($this->bio),
            'location' => $this->nullableProfileValue($this->location),
            'website' => $this->nullableProfileValue($this->website),
        ];
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    private function applySyncedProfile(array $profile): void
    {
        $this->name = $this->syncedProfileString($profile, 'name', $this->name, allowEmpty: false);
        $this->username = Str::lower($this->syncedProfileString($profile, 'username', $this->username, allowEmpty: false));
        $this->phone = $this->syncedProfileString($profile, 'phone', $this->phone);
        $this->bio = $this->syncedProfileString($profile, 'bio', $this->bio);
        $this->location = $this->syncedProfileString($profile, 'location', $this->location);
        $this->website = $this->syncedProfileString($profile, 'website', $this->website);
        $this->refreshAvatarInitials();
    }

    private function nullableProfileValue(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    private function syncedProfileString(array $profile, string $key, string $fallback, bool $allowEmpty = true): string
    {
        if (! array_key_exists($key, $profile)) {
            return $fallback;
        }

        $value = $profile[$key];

        if (! is_string($value)) {
            return $allowEmpty ? '' : $fallback;
        }

        $value = trim($value);

        if ($value === '' && ! $allowEmpty) {
            return $fallback;
        }

        return $value;
    }
}
