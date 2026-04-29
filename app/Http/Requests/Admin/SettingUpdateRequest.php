<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'default_bidding_multiplier_percent' => ['required', 'integer', 'min:100', 'max:2000'],
            'scrape_interval_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'stripe_payment_enabled' => ['nullable', 'boolean'],
        ];
    }
}
