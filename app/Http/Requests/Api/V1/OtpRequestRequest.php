<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OtpRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'max:32'],
            'role_hint' => ['required', 'string', Rule::in(['PASSENGER', 'DRIVER'])],
            'device_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
