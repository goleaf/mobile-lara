<?php

namespace App\Http\Requests\Api\V1\Mobile;

use App\Support\Api\MobileApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class MobileApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            MobileApiResponse::error(
                code: 'validation_failed',
                message: 'The submitted mobile request is invalid.',
                category: 'validation',
                nextAction: 'correct_input',
                status: 422,
                meta: [
                    'validation_errors' => $validator->errors()->toArray(),
                ],
            ),
        );
    }
}
