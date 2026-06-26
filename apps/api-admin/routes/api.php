<?php

use App\Http\Controllers\Api\V1\Mobile\AppVersionController;
use App\Http\Controllers\Api\V1\Mobile\Auth\CurrentUserController;
use App\Http\Controllers\Api\V1\Mobile\Auth\LoginController;
use App\Http\Controllers\Api\V1\Mobile\Auth\LogoutAllDevicesController;
use App\Http\Controllers\Api\V1\Mobile\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Mobile\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Mobile\Auth\RefreshTokenController;
use App\Http\Controllers\Api\V1\Mobile\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Mobile\Billing\SubscriptionController;
use App\Http\Controllers\Api\V1\Mobile\BootstrapController;
use App\Http\Controllers\Api\V1\Mobile\ConfigController;
use App\Http\Controllers\Api\V1\Mobile\ContractIndexController;
use App\Http\Controllers\Api\V1\Mobile\Diagnostics\DiagnosticsStoreController;
use App\Http\Controllers\Api\V1\Mobile\FeatureIndexController;
use App\Http\Controllers\Api\V1\Mobile\Notifications\NotificationDeleteController;
use App\Http\Controllers\Api\V1\Mobile\Notifications\NotificationIndexController;
use App\Http\Controllers\Api\V1\Mobile\Notifications\NotificationReadAllController;
use App\Http\Controllers\Api\V1\Mobile\Notifications\NotificationReadController;
use App\Http\Controllers\Api\V1\Mobile\Notifications\PushTokenDeleteController;
use App\Http\Controllers\Api\V1\Mobile\Notifications\PushTokenStoreController;
use App\Http\Controllers\Api\V1\Mobile\Records\RecordArchiveController;
use App\Http\Controllers\Api\V1\Mobile\Records\RecordIndexController;
use App\Http\Controllers\Api\V1\Mobile\Records\RecordRestoreController;
use App\Http\Controllers\Api\V1\Mobile\Records\RecordShowController;
use App\Http\Controllers\Api\V1\Mobile\Records\RecordStoreController;
use App\Http\Controllers\Api\V1\Mobile\Records\RecordUpdateController;
use App\Http\Controllers\Api\V1\Mobile\StatusController;
use App\Http\Controllers\Api\V1\Mobile\Support\SupportTicketIndexController;
use App\Http\Controllers\Api\V1\Mobile\Support\SupportTicketMessageStoreController;
use App\Http\Controllers\Api\V1\Mobile\Support\SupportTicketShowController;
use App\Http\Controllers\Api\V1\Mobile\Support\SupportTicketStoreController;
use App\Http\Controllers\Api\V1\Mobile\Sync\SyncAcknowledgeController;
use App\Http\Controllers\Api\V1\Mobile\Sync\SyncBootstrapController;
use App\Http\Controllers\Api\V1\Mobile\Sync\SyncPullController;
use App\Http\Controllers\Api\V1\Mobile\Sync\SyncPushController;
use App\Http\Controllers\Api\V1\Mobile\Tenants\AcceptTenantInvitationController;
use App\Http\Controllers\Api\V1\Mobile\Tenants\DeclineTenantInvitationController;
use App\Http\Controllers\Api\V1\Mobile\Tenants\SwitchTenantController;
use App\Http\Controllers\Api\V1\Mobile\Tenants\TenantIndexController;
use App\Http\Controllers\Api\V1\Mobile\Tenants\TenantInvitationIndexController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function (): void {
        Route::prefix('mobile')
            ->name('mobile.')
            ->group(function (): void {
                Route::get('/status', StatusController::class)->name('status');
                Route::get('/contracts', ContractIndexController::class)->name('contracts.index');
                Route::get('/app-version', AppVersionController::class)->name('app-version');

                Route::middleware('mobile.auth')->group(function (): void {
                    Route::get('/bootstrap', BootstrapController::class)->name('bootstrap');
                    Route::get('/config', ConfigController::class)->name('config');
                    Route::get('/features', FeatureIndexController::class)->name('features.index');
                    Route::get('/billing/subscription', SubscriptionController::class)->name('billing.subscription');
                    Route::post('/diagnostics', DiagnosticsStoreController::class)->name('diagnostics.store');
                    Route::get('/notifications', NotificationIndexController::class)->name('notifications.index');
                    Route::post('/notifications/push-tokens', PushTokenStoreController::class)->name('notifications.push-tokens.store');
                    Route::delete('/notifications/push-tokens/{token}', PushTokenDeleteController::class)->name('notifications.push-tokens.destroy');
                    Route::patch('/notifications/read-all', NotificationReadAllController::class)->name('notifications.read-all');
                    Route::patch('/notifications/{notification}/read', NotificationReadController::class)->name('notifications.read');
                    Route::delete('/notifications/{notification}', NotificationDeleteController::class)->name('notifications.destroy');
                    Route::get('/support/tickets', SupportTicketIndexController::class)->name('support.tickets.index');
                    Route::post('/support/tickets', SupportTicketStoreController::class)->name('support.tickets.store');
                    Route::get('/support/tickets/{ticket}', SupportTicketShowController::class)->name('support.tickets.show');
                    Route::post('/support/tickets/{ticket}/messages', SupportTicketMessageStoreController::class)->name('support.tickets.messages.store');
                    Route::get('/sync/bootstrap', SyncBootstrapController::class)->name('sync.bootstrap');
                    Route::get('/sync/pull', SyncPullController::class)->name('sync.pull');
                    Route::post('/sync/push', SyncPushController::class)->name('sync.push');
                    Route::post('/sync/acknowledge', SyncAcknowledgeController::class)->name('sync.acknowledge');
                    Route::get('/tenants', TenantIndexController::class)->name('tenants.index');
                    Route::post('/tenants/current', SwitchTenantController::class)->name('tenants.current');
                    Route::get('/tenants/invitations', TenantInvitationIndexController::class)->name('tenants.invitations.index');
                    Route::post('/tenants/invitations/{tenant}/accept', AcceptTenantInvitationController::class)->name('tenants.invitations.accept');
                    Route::post('/tenants/invitations/{tenant}/decline', DeclineTenantInvitationController::class)->name('tenants.invitations.decline');
                    Route::get('/records', RecordIndexController::class)->name('records.index');
                    Route::post('/records', RecordStoreController::class)->name('records.store');
                    Route::get('/records/{record}', RecordShowController::class)->name('records.show');
                    Route::patch('/records/{record}', RecordUpdateController::class)->name('records.update');
                    Route::delete('/records/{record}', RecordArchiveController::class)->name('records.archive');
                    Route::post('/records/{record}/restore', RecordRestoreController::class)->name('records.restore');
                });

                Route::prefix('auth')
                    ->name('auth.')
                    ->group(function (): void {
                        Route::post('/login', LoginController::class)->name('login');
                        Route::post('/register', RegisterController::class)->name('register');
                        Route::post('/refresh', RefreshTokenController::class)->name('refresh');

                        Route::middleware('mobile.auth')->group(function (): void {
                            Route::post('/logout', LogoutController::class)->name('logout');
                            Route::post('/logout-all', LogoutAllDevicesController::class)->name('logout-all');
                            Route::get('/user', CurrentUserController::class)->name('user');
                            Route::patch('/profile', ProfileController::class)->name('profile');
                        });
                    });
            });
    });
