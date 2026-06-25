<?php

namespace App\Http\Requests\Api\V1\Mobile;

final class RefreshTokenRequest extends MobileApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
        ];
    }
}
