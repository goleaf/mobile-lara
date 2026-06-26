<?php

namespace App\Services\MobileSupport;

use App\Services\MobileApi\MobileApiClient;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\AccessTokenService;

final class MobileSupportApiService
{
    public function __construct(
        private readonly MobileApiClient $api,
        private readonly AccessTokenService $accessTokens,
    ) {}

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listTickets(array $query = []): array
    {
        return $this->data($this->api->get('/support/tickets', $query, $this->accessToken()));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createTicket(array $payload): array
    {
        return $this->ticket($this->api->post('/support/tickets', $payload, $this->accessToken()));
    }

    /**
     * @return array<string, mixed>
     */
    public function showTicket(string $ticketId): array
    {
        return $this->ticket($this->api->get("/support/tickets/{$ticketId}", accessToken: $this->accessToken()));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function addMessage(string $ticketId, array $payload): array
    {
        return $this->ticket($this->api->post("/support/tickets/{$ticketId}/messages", $payload, $this->accessToken()));
    }

    private function accessToken(): string
    {
        $accessToken = $this->accessTokens->get();

        if (! is_string($accessToken) || trim($accessToken) === '') {
            throw MobileApiException::missingToken('access');
        }

        return $accessToken;
    }

    /**
     * @param  array<string, mixed>  $envelope
     * @return array<string, mixed>
     */
    private function data(array $envelope): array
    {
        $data = $envelope['data'] ?? null;

        if (! is_array($data)) {
            throw MobileApiException::malformedResponse($envelope);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $envelope
     * @return array<string, mixed>
     */
    private function ticket(array $envelope): array
    {
        $ticket = $envelope['data']['ticket'] ?? null;

        if (! is_array($ticket)) {
            throw MobileApiException::malformedResponse($envelope);
        }

        return $ticket;
    }
}
