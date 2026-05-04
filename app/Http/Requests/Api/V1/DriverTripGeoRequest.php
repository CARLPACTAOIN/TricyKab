<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class DriverTripGeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed|string>|string>
     */
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'started_at_client' => ['sometimes', 'nullable', 'string', 'max:64'],
            'ended_at_client' => ['sometimes', 'nullable', 'string', 'max:64'],
            'manual_reason' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
