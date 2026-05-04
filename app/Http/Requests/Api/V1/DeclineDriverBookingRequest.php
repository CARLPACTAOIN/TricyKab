<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class DeclineDriverBookingRequest extends FormRequest
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
            'dispatch_attempt_id' => ['required', 'integer', 'exists:booking_dispatch_attempts,id'],
            'candidate_id' => ['required', 'integer', 'exists:booking_dispatch_candidates,id'],
            'reason_code' => ['required', 'string', 'max:64'],
        ];
    }
}
