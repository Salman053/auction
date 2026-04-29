<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
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
            'amount_yen' => ['required', 'integer', 'min:1000'],
            'destination_type' => ['nullable', 'string', 'max:50'],
            'destination_meta' => ['nullable', 'array'],
            'memo' => ['nullable', 'string', 'max:255'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // Max 5MB
        ];
    }
}
