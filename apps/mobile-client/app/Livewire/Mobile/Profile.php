<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\MobileAuthApiService;
use App\Services\MobileAuth\MobileSessionService;
use App\Services\MobileProfile\AvatarStorageService;
use App\Services\Native\ShareService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile')]
class Profile extends Component
{
    use DispatchesToasts;

    public string $displayName = 'Mobile Lara';

    public string $email = 'mobile@example.test';

    public string $phone = 'Not added';

    public string $bio = 'Local mobile account';

    public string $accountStatus = 'Active';

    public string $avatarInitials = 'ML';

    public ?string $avatarUrl = null;

    public bool $isEditingProfile = false;

    public bool $hasNetworkError = false;

    public bool $hasProfile = true;

    protected MobileSessionService $mobileSessions;

    protected MobileAuthApiService $authApi;

    protected AvatarStorageService $avatarStorage;

    protected ShareService $shares;

    public function boot(
        MobileSessionService $mobileSessions,
        MobileAuthApiService $authApi,
        AvatarStorageService $avatarStorage,
        ShareService $shares,
    ): void {
        $this->mobileSessions = $mobileSessions;
        $this->authApi = $authApi;
        $this->avatarStorage = $avatarStorage;
        $this->shares = $shares;
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            $this->refreshDerivedProfileState();

            return;
        }

        $this->displayName = (string) $user->name;
        $this->email = (string) $user->email;
        $this->accountStatus = $user->email_verified_at === null ? 'Active' : 'Verified';
        $this->avatarUrl = $this->avatarStorage->url($user->avatar_path);

        $this->refreshDerivedProfileState();
    }

    public function editProfile(): void
    {
        $this->isEditingProfile = true;
    }

    public function cancelProfileEdit(): void
    {
        $this->isEditingProfile = false;
        $this->refreshDerivedProfileState();
    }

    public function saveProfile(): void
    {
        $this->hasNetworkError = false;
        $this->hasProfile = true;
        $this->isEditingProfile = false;
        $this->refreshDerivedProfileState();
    }

    public function shareProfile(): void
    {
        $this->refreshDerivedProfileState();

        $result = $this->shares->shareUrl(
            title: "Profile: {$this->displayName}",
            text: implode(PHP_EOL, [
                $this->displayName,
                $this->email,
                $this->bio,
            ]),
            url: route('mobile.profile'),
        );

        $this->toastForShareResult($result, 'Profile shared', 'Share unavailable');
    }

    public function retryProfile(): void
    {
        $this->hasNetworkError = false;
        $this->hasProfile = true;
        $this->refreshDerivedProfileState();
    }

    public function logout(): void
    {
        try {
            $this->authApi->logout();
        } catch (MobileApiException) {
        }

        $this->mobileSessions->logoutCurrentSession();

        $this->redirect(route('mobile.login'), true);
    }

    public function render(): View
    {
        return view('livewire.mobile.profile', [
            'profileRows' => $this->profileRows(),
            'profileShortcuts' => $this->profileShortcuts(),
        ]);
    }

    private function refreshDerivedProfileState(): void
    {
        $this->displayName = trim($this->displayName) === '' ? 'Mobile Lara' : trim($this->displayName);
        $this->email = trim($this->email) === '' ? 'mobile@example.test' : trim($this->email);
        $this->phone = trim($this->phone) === '' ? 'Not added' : trim($this->phone);
        $this->bio = trim($this->bio) === '' ? 'Local mobile account' : trim($this->bio);
        $this->avatarInitials = $this->initials($this->displayName);
    }

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    private function profileRows(): array
    {
        return [
            [
                'key' => 'email',
                'label' => 'Email',
                'value' => $this->email,
            ],
            [
                'key' => 'phone',
                'label' => 'Phone',
                'value' => $this->phone,
            ],
            [
                'key' => 'status',
                'label' => 'Account status',
                'value' => $this->accountStatus,
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string, description: string, route: string, badge: string}>
     */
    private function profileShortcuts(): array
    {
        return [
            [
                'key' => 'security',
                'label' => 'Security',
                'description' => 'PIN, biometrics, app unlock, and sessions.',
                'route' => 'mobile.settings.security',
                'badge' => 'Secure',
            ],
            [
                'key' => 'notifications',
                'label' => 'Notifications',
                'description' => 'Push, in-app alerts, and notification preferences.',
                'route' => 'mobile.settings.notifications',
                'badge' => 'Alerts',
            ],
        ];
    }

    private function initials(string $name): string
    {
        $words = Str::of($name)
            ->replaceMatches('/[^\pL\pN\s]+/u', ' ')
            ->squish()
            ->explode(' ')
            ->filter();

        $initials = $words
            ->take(2)
            ->map(static fn (string $word): string => Str::of($word)->substr(0, 1)->upper()->toString())
            ->implode('');

        return $initials === '' ? 'ML' : $initials;
    }

    /**
     * @param  array{success: bool, message: string}  $result
     */
    private function toastForShareResult(array $result, string $successTitle, string $failureTitle): void
    {
        if ($result['success']) {
            $this->toastSuccess($result['message'], $successTitle);

            return;
        }

        $this->toastWarning($result['message'], $failureTitle);
    }
}
