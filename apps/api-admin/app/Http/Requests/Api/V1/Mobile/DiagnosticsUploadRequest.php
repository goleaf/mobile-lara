<?php

namespace App\Http\Requests\Api\V1\Mobile;

final class DiagnosticsUploadRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'client_reference' => ['nullable', 'string', 'max:120'],
            'support_ticket_id' => ['nullable', 'string', 'max:120'],
            'snapshot' => ['required', 'array:generated_at,app,user,tenant,features,remote_config,network,sync,failed_sync_actions,device,redactions_applied'],
            'snapshot.generated_at' => ['nullable', 'date'],
            'snapshot.app' => ['required', 'array:app_version,api_base_url,laravel_version,livewire_version,nativephp_mobile_version,nativephp_running,nativephp_app_id,nativephp_start_url'],
            'snapshot.app.app_version' => ['nullable', 'string', 'max:80'],
            'snapshot.app.api_base_url' => ['nullable', 'string', 'max:255'],
            'snapshot.app.laravel_version' => ['nullable', 'string', 'max:80'],
            'snapshot.app.livewire_version' => ['nullable', 'string', 'max:80'],
            'snapshot.app.nativephp_mobile_version' => ['nullable', 'string', 'max:80'],
            'snapshot.app.nativephp_running' => ['nullable', 'boolean'],
            'snapshot.app.nativephp_app_id' => ['nullable', 'string', 'max:120'],
            'snapshot.app.nativephp_start_url' => ['nullable', 'string', 'max:255'],
            'snapshot.user' => ['nullable', 'array:authenticated,id,source'],
            'snapshot.user.authenticated' => ['nullable', 'boolean'],
            'snapshot.user.id' => ['nullable'],
            'snapshot.user.source' => ['nullable', 'string', 'max:80'],
            'snapshot.tenant' => ['required', 'array:tenant_id,status,subscription_state'],
            'snapshot.tenant.tenant_id' => ['required', 'string', 'max:120'],
            'snapshot.tenant.status' => ['nullable', 'string', 'max:80'],
            'snapshot.tenant.subscription_state' => ['nullable', 'string', 'max:80'],
            'snapshot.features' => ['nullable', 'array'],
            'snapshot.remote_config' => ['nullable', 'array'],
            'snapshot.network' => ['nullable', 'array'],
            'snapshot.sync' => ['nullable', 'array'],
            'snapshot.failed_sync_actions' => ['nullable', 'array', 'max:10'],
            'snapshot.failed_sync_actions.*' => ['array:id,action_type,method,endpoint,attempts,last_error,conflict_status,created_at,available_at'],
            'snapshot.failed_sync_actions.*.id' => ['nullable'],
            'snapshot.failed_sync_actions.*.action_type' => ['nullable', 'string', 'max:120'],
            'snapshot.failed_sync_actions.*.method' => ['nullable', 'string', 'max:12'],
            'snapshot.failed_sync_actions.*.endpoint' => ['nullable', 'string', 'max:255'],
            'snapshot.failed_sync_actions.*.attempts' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'snapshot.failed_sync_actions.*.last_error' => ['nullable', 'string', 'max:500'],
            'snapshot.failed_sync_actions.*.conflict_status' => ['nullable', 'string', 'max:80'],
            'snapshot.failed_sync_actions.*.created_at' => ['nullable', 'date'],
            'snapshot.failed_sync_actions.*.available_at' => ['nullable', 'date'],
            'snapshot.device' => ['nullable', 'array'],
            'snapshot.redactions_applied' => ['nullable', 'array', 'max:20'],
            'snapshot.redactions_applied.*' => ['string', 'max:80'],
        ];
    }
}
