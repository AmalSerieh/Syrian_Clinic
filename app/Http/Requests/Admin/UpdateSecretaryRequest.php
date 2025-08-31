<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSecretaryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'digits:10'],
            'password' => ['nullable', 'confirmed', 'min:8'], // اجعلها nullable
            'date_of_appointment' => ['required', 'date'],
            's_type_wage' => ['required', Rule::in(['number', 'percentage'])],
            's_wage' => [
                'required',
                'numeric',
                Rule::when(request('s_type_wage') === 'number', ['min:1', 'max:1000000']), // راتب
                Rule::when(request('s_type_wage') === 'percentage', ['min:5', 'max:100']),  // نسبة مئوية
            ],
        ];
    }
}
