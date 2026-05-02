<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class OtpVerifyRequest extends FormRequest
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
            'otp_code' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'device_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
