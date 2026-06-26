<?php

namespace App\Http\Requests\Api\V1\Mobile;

use Illuminate\Validation\Rule;

final class PushTokenStoreRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'min:16', 'max:4096'],
            'provider' => ['required', 'string', Rule::in(['apns', 'fcm', 'expo', 'nativephp', 'development'])],
            'platform' => ['required', 'string', Rule::in(['ios', 'android', 'web', 'nativephp', 'unknown'])],
            'device_id' => ['nullable', 'string', 'max:160'],
            'app_version' => ['nullable', 'string', 'max:80'],
            'metadata' => ['nullable', 'array', 'max:20'],
            'metadata.*' => ['nullable', 'string', 'max:160'],
        ];
    }
}
