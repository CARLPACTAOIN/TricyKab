<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class RecordPaymentRequest extends FormRequest
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
            'amount' => ['required'],
            'method' => ['sometimes', 'string', 'max:20'],
            'recorded_by_role' => ['sometimes', 'string', 'max:20'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
