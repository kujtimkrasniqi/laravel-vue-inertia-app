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
            'name'       => ['required', 'string', 'min:2', 'max:255'],
            'phone'      => ['required', 'string', 'min:7', 'max:20'],
            'email'      => ['nullable', 'string', 'email:rfc,dns', 'max:255'],
            'start_date' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Client name is required.',
            'name.min'             => 'Name must be at least 2 characters.',
            'phone.required'       => 'Phone number is required.',
            'phone.min'            => 'Phone number must be at least 7 digits.',
            'email.email'          => 'Please provide a valid email address.',
            'start_date.required'  => 'Start date is required.',
            'start_date.date'      => 'Start date must be a valid date.',
            'start_date.date_format' => 'Start date must be in YYYY-MM-DD format.',
        ];
    }
}
