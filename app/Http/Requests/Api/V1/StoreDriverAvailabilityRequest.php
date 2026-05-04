<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDriverAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * PRD §9.3 — POST /drivers/me/availability
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'driver_status' => ['required', 'string', Rule::in(['ONLINE', 'OFFLINE'])],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
