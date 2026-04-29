<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProxyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scheme' => ['required', 'string', 'in:http,https,socks4,socks5'],
            'host' => ['required', 'string'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'username' => ['nullable', 'string'],
            'password' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'max:2'],
        ];
    }
}
