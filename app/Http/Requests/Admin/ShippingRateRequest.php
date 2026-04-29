<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'fee_yen' => ['required', 'integer', 'min:0'],
            'country' => ['nullable', 'string', 'max:255'],
            'port' => ['nullable', 'string', 'max:255'],
        ];
    }
}
