<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AcceptDriverBookingRequest extends FormRequest
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
            'driver_location' => ['nullable', 'array'],
            'driver_location.latitude' => ['required_with:driver_location', 'numeric', 'between:-90,90'],
            'driver_location.longitude' => ['required_with:driver_location', 'numeric', 'between:-180,180'],
            'driver_location.accuracy_meters' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
