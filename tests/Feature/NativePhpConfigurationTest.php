<?php

use App\Providers\NativeServiceProvider;
use Codingwithrk\DoubleBackToClose\DoubleBackToCloseServiceProvider;
use Codingwithrk\NoScreenshot\NoScreenshotServiceProvider;
use Developernauts\NativephpMobileLocales\NativephpMobileLocalesServiceProvider;
use KevinBatdorf\Fullscreen\FullscreenServiceProvider;
use MobikulLoader\MobikulLoaderServiceProvider;
use Native\Mobile\Providers\BrowserServiceProvider;
use Native\Mobile\Providers\CameraServiceProvider;
use Native\Mobile\Providers\DeviceServiceProvider;
use Native\Mobile\Providers\DialogServiceProvider;
use Native\Mobile\Providers\FileServiceProvider;
use Native\Mobile\Providers\MicrophoneServiceProvider;
use Native\Mobile\Providers\NetworkServiceProvider;
use Native\Mobile\Providers\ShareServiceProvider;
use Native\Mobile\Providers\SystemServiceProvider;
use Nativephp\AllPermissionHandler\AllPermissionHandlerServiceProvider;
use Nativephp\InAppReviews\InAppReviewsServiceProvider;
use S2BR\MobileSplashscreen\MobileSplashscreenServiceProvider;
use Wilsonatb\InAppUpdate\InAppUpdateServiceProvider;

test('nativephp mobile is configured for this app', function () {
    expect(config('nativephp'))
        ->toMatchArray([
            'version' => '1.0.0',
            'version_code' => '1',
            'app_id' => 'dev.andrejprus.mobilelara',
            'deeplink_scheme' => 'mobilelara',
            'deeplink_host' => 'mobile-lara.test',
            'start_url' => '/',
        ])
        ->and(config('nativephp.runtime.mode'))->toBe('persistent')
        ->and(config('nativephp.android.theme.color_primary'))->toBe('#04ABA6')
        ->and(config('nativephp.permissions'))->toHaveKeys([
            'NSCameraUsageDescription',
            'NSMicrophoneUsageDescription',
            'NSPhotoLibraryUsageDescription',
            'NSPhotoLibraryAddUsageDescription',
        ]);
});

test('native service provider registers installed mobile plugins', function () {
    $provider = new NativeServiceProvider(app());

    expect($provider->plugins())->toContain(
        AllPermissionHandlerServiceProvider::class,
        DoubleBackToCloseServiceProvider::class,
        NoScreenshotServiceProvider::class,
        NativephpMobileLocalesServiceProvider::class,
        FullscreenServiceProvider::class,
        MobikulLoaderServiceProvider::class,
        BrowserServiceProvider::class,
        CameraServiceProvider::class,
        DeviceServiceProvider::class,
        DialogServiceProvider::class,
        FileServiceProvider::class,
        MicrophoneServiceProvider::class,
        NetworkServiceProvider::class,
        ShareServiceProvider::class,
        SystemServiceProvider::class,
        MobileSplashscreenServiceProvider::class,
        InAppUpdateServiceProvider::class,
        InAppReviewsServiceProvider::class,
    );
});
