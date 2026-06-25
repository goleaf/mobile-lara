<?php

use Illuminate\Testing\Fluent\AssertableJson;

test('mobile contract catalogue is exposed through the standard envelope', function (): void {
    $this->getJson('/api/v1/mobile/contracts')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.base_path', '/api/v1/mobile')
            ->where('data.contract_version', 'v1')
            ->where('data.authority', 'admin_api')
            ->where('meta.api_version', 'v1')
            ->where('meta.next_contract', 'v1-auth')
            ->where('meta.contract_count', 14)
            ->has('meta.server_time')
            ->has('data.envelope.success', 3)
            ->has('data.envelope.error', 3)
            ->has('data.required_mobile_context')
            ->has('data.contracts', 14)
            ->where('data.contracts.0.key', 'foundation')
            ->where('data.contracts.0.status', 'implemented')
            ->where('data.contracts.0.routes.1.path', '/contracts')
            ->where('data.contracts.1.key', 'auth')
            ->where('data.contracts.1.document', 'v1-auth.md')
            ->where('data.contracts.2.key', 'bootstrap')
            ->where('data.contracts.8.key', 'sync')
            ->where('data.contracts.13.key', 'diagnostics')
        );
});

test('every catalogued mobile api contract has a markdown document', function (): void {
    $response = $this->getJson('/api/v1/mobile/contracts')->assertOk();

    collect($response->json('data.contracts'))
        ->pluck('document')
        ->each(function (string $document): void {
            expect(base_path("../../contracts/api/{$document}"))->toBeFile();
        });
});
