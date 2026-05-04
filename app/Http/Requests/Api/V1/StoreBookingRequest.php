<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
            'ride_type' => ['required', 'string', Rule::in(['SHARED', 'SPECIAL'])],
            'pickup' => ['required', 'array'],
            'pickup.latitude' => ['required', 'numeric', 'between:-90,90'],
            'pickup.longitude' => ['required', 'numeric', 'between:-180,180'],
            'pickup.address' => ['required', 'string', 'max:500'],
            'pickup.notes' => ['nullable', 'string', 'max:1000'],
            'destination' => ['required', 'array'],
            'destination.latitude' => ['required', 'numeric', 'between:-90,90'],
            'destination.longitude' => ['required', 'numeric', 'between:-180,180'],
            'destination.address' => ['required', 'string', 'max:500'],
        ];
    }
}
