<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'email'      => ['nullable', 'email', 'max:255'],
            'start_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Client name is required.',
            'phone.required'      => 'Phone number is required.',
            'email.email'         => 'Please provide a valid email address.',
            'start_date.required' => 'Start date is required.',
            'start_date.date'     => 'Start date must be a valid date.',
        ];
    }
}
