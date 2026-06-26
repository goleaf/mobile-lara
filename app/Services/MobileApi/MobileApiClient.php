<?php

namespace App\Services\MobileApi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class MobileApiClient
{
    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = [], ?string $accessToken = null): array
    {
        return $this->send('get', $path, $query, $accessToken);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function post(string $path, array $payload = [], ?string $accessToken = null): array
    {
        return $this->send('post', $path, $payload, $accessToken);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function patch(string $path, array $payload = [], ?string $accessToken = null): array
    {
        return $this->send('patch', $path, $payload, $accessToken);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, array{contents: string, filename: string, headers?: array<string, string>}>  $files
     * @return array<string, mixed>
     */
    public function patchMultipart(string $path, array $payload = [], array $files = [], ?string $accessToken = null): array
    {
        return $this->send('patch', $path, $payload, $accessToken, $files);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function delete(string $path, array $payload = [], ?string $accessToken = null): array
    {
        return $this->send('delete', $path, $payload, $accessToken);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, array{contents: string, filename: string, headers?: array<string, string>}>  $files
     * @return array<string, mixed>
     */
    private function send(string $method, string $path, array $payload = [], ?string $accessToken = null, array $files = []): array
    {
        $request = $this->request(asJson: $files === []);

        if (is_string($accessToken) && trim($accessToken) !== '') {
            $request = $request->withToken($accessToken);
        }

        foreach ($files as $name => $file) {
            $request = $request->attach(
                $name,
                $file['contents'],
                $file['filename'],
                $file['headers'] ?? [],
            );
        }

        try {
            $response = match ($method) {
                'get' => $request->get($this->path($path), $payload),
                'post' => $request->post($this->path($path), $payload),
                'patch' => $request->patch($this->path($path), $payload),
                'delete' => $request->delete($this->path($path), $payload),
                default => throw MobileApiException::malformedResponse(),
            };
        } catch (ConnectionException $exception) {
            throw MobileApiException::connectionFailed($exception->getMessage());
        }

        return $this->envelope($response);
    }

    private function request(bool $asJson = true): PendingRequest
    {
        $request = Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->timeout(max(1, (int) config('mobile_auth.api.timeout_seconds', 10)))
            ->connectTimeout(max(1, (int) config('mobile_auth.api.connect_timeout_seconds', 3)))
            ->withHeaders([
                'X-Mobile-App-Version' => (string) config('nativephp.version', '1.0.0'),
                'X-Mobile-App-Version-Code' => (string) config('nativephp.version_code', '1'),
            ]);

        return $asJson ? $request->asJson() : $request;
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('mobile_auth.api.base_url', 'https://mobile-lara-api-admin.test/api/v1/mobile'), '/');
    }

    private function path(string $path): string
    {
        return '/'.ltrim($path, '/');
    }

    /**
     * @return array<string, mixed>
     */
    private function envelope(Response $response): array
    {
        $payload = $response->json();

        if (! is_array($payload)) {
            throw MobileApiException::malformedResponse();
        }

        if ($response->failed() || ($payload['success'] ?? null) !== true) {
            throw MobileApiException::fromPayload($payload, $response->status());
        }

        if (! is_array($payload['data'] ?? null)) {
            throw MobileApiException::malformedResponse($payload);
        }

        return $payload;
    }
}
