<?php

use Illuminate\Testing\Fluent\AssertableJson;

test('mobile status endpoint uses the standard success envelope', function (): void {
    $this->getJson('/api/v1/mobile/status')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.service', 'api-admin')
            ->where('data.authority', 'admin_api')
            ->where('data.mobile_api', 'v1')
            ->where('data.status', 'ok')
            ->where('meta.api_version', 'v1')
            ->where('meta.next_contract', 'v1-bootstrap')
            ->has('meta.server_time')
        );
});
