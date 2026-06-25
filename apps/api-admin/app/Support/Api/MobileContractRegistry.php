<?php

namespace App\Support\Api;

final class MobileContractRegistry
{
    /**
     * @return array<string, mixed>
     */
    public static function catalogue(): array
    {
        return [
            'base_path' => '/api/v1/mobile',
            'contract_version' => 'v1',
            'authority' => 'admin_api',
            'envelope' => [
                'success' => ['success', 'data', 'meta'],
                'error' => ['success', 'error', 'meta'],
            ],
            'required_mobile_context' => [
                'accept' => 'application/json',
                'app_version' => 'X-Mobile-App-Version',
                'platform' => 'X-Mobile-Platform',
                'device_id' => 'X-Mobile-Device-Id',
                'tenant' => 'X-Tenant-Id after authentication',
            ],
            'contracts' => self::groups(),
        ];
    }

    /**
     * @return array<int, array{
     *     key: string,
     *     document: string,
     *     status: string,
     *     purpose: string,
     *     routes: array<int, array{
     *         method: string,
     *         path: string,
     *         status: string,
     *         auth: string
     *     }>
     * }>
     */
    public static function groups(): array
    {
        return [
            self::group('foundation', 'v1-foundation.md', 'implemented', 'API reachability and shared envelope proof.', [
                self::route('GET', '/status', 'implemented', 'public'),
                self::route('GET', '/contracts', 'implemented', 'public'),
            ]),
            self::group('auth', 'v1-auth.md', 'documented', 'Mobile account, token, session, and profile authority.', [
                self::route('POST', '/auth/login', 'planned', 'public'),
                self::route('POST', '/auth/register', 'planned', 'public'),
                self::route('POST', '/auth/refresh', 'planned', 'refresh-token'),
                self::route('POST', '/auth/logout', 'planned', 'mobile-token'),
                self::route('POST', '/auth/logout-all', 'planned', 'mobile-token'),
                self::route('GET', '/auth/user', 'planned', 'mobile-token'),
                self::route('PATCH', '/auth/profile', 'planned', 'mobile-token'),
            ]),
            self::group('bootstrap', 'v1-bootstrap.md', 'documented', 'Single mobile operating context after login, app start, tenant switch, and manual refresh.', [
                self::route('GET', '/bootstrap', 'planned', 'mobile-token'),
            ]),
            self::group('tenancy', 'v1-tenancy.md', 'documented', 'Tenant context, tenant switching, invitations, and tenant lifecycle effects.', [
                self::route('GET', '/tenants', 'planned', 'mobile-token'),
                self::route('POST', '/tenants/current', 'planned', 'mobile-token'),
            ]),
            self::group('features', 'v1-features.md', 'documented', 'Resolved global, tenant, user, plan, permission, version, device, cohort, maintenance, and emergency feature outcomes.', [
                self::route('GET', '/features', 'planned', 'mobile-token'),
            ]),
            self::group('remote_config', 'v1-remote-config.md', 'documented', 'Resolved mobile-safe config values, freshness, compatibility, and fallback metadata.', [
                self::route('GET', '/config', 'planned', 'mobile-token'),
            ]),
            self::group('app_version_maintenance', 'v1-app-version-maintenance.md', 'documented', 'Minimum version, optional update, force update, blocked, deprecated, and maintenance decisions.', [
                self::route('GET', '/app-version', 'planned', 'public-with-mobile-context'),
            ]),
            self::group('records', 'v1-records.md', 'documented', 'Tenant-scoped records, categories, tags, notes, attachment metadata, and activity timeline.', [
                self::route('GET', '/records', 'planned', 'mobile-token'),
                self::route('POST', '/records', 'planned', 'mobile-token'),
                self::route('GET', '/records/{record}', 'planned', 'mobile-token'),
                self::route('PATCH', '/records/{record}', 'planned', 'mobile-token'),
                self::route('DELETE', '/records/{record}', 'planned', 'mobile-token'),
            ]),
            self::group('sync', 'v1-sync.md', 'documented', 'Offline queue bootstrap, push, pull, acknowledgement, conflicts, and replay safety.', [
                self::route('GET', '/sync/bootstrap', 'planned', 'mobile-token'),
                self::route('POST', '/sync/push', 'planned', 'mobile-token'),
                self::route('GET', '/sync/pull', 'planned', 'mobile-token'),
                self::route('POST', '/sync/acknowledge', 'planned', 'mobile-token'),
            ]),
            self::group('notifications', 'v1-notifications.md', 'documented', 'Notification preferences, push tokens, inbox, read state, deletes, and deep links.', [
                self::route('GET', '/notifications', 'planned', 'mobile-token'),
                self::route('POST', '/notifications/push-tokens', 'planned', 'mobile-token'),
                self::route('DELETE', '/notifications/push-tokens/{token}', 'planned', 'mobile-token'),
                self::route('PATCH', '/notifications/{notification}/read', 'planned', 'mobile-token'),
            ]),
            self::group('support', 'v1-support.md', 'documented', 'Support tickets, messages, attachments, diagnostics context, assignment, and audit.', [
                self::route('GET', '/support/tickets', 'planned', 'mobile-token'),
                self::route('POST', '/support/tickets', 'planned', 'mobile-token'),
                self::route('GET', '/support/tickets/{ticket}', 'planned', 'mobile-token'),
                self::route('POST', '/support/tickets/{ticket}/messages', 'planned', 'mobile-token'),
            ]),
            self::group('billing', 'v1-billing.md', 'documented', 'Plan, subscription, trial, expired, suspended, quota, invoice placeholder, and upgrade state.', [
                self::route('GET', '/billing/subscription', 'planned', 'mobile-token'),
            ]),
            self::group('reports', 'v1-reports.md', 'documented', 'Tenant/user-safe report summaries and exports controlled by permissions and feature flags.', [
                self::route('GET', '/reports', 'planned', 'mobile-token'),
            ]),
            self::group('diagnostics', 'v1-diagnostics.md', 'documented', 'Privacy-safe diagnostics upload/export context for support and troubleshooting.', [
                self::route('POST', '/diagnostics', 'planned', 'mobile-token'),
            ]),
        ];
    }

    /**
     * @param  array<int, array{method: string, path: string, status: string, auth: string}>  $routes
     * @return array{key: string, document: string, status: string, purpose: string, routes: array<int, array{method: string, path: string, status: string, auth: string}>}
     */
    private static function group(string $key, string $document, string $status, string $purpose, array $routes): array
    {
        return [
            'key' => $key,
            'document' => $document,
            'status' => $status,
            'purpose' => $purpose,
            'routes' => $routes,
        ];
    }

    /**
     * @return array{method: string, path: string, status: string, auth: string}
     */
    private static function route(string $method, string $path, string $status, string $auth): array
    {
        return [
            'method' => $method,
            'path' => $path,
            'status' => $status,
            'auth' => $auth,
        ];
    }
}
