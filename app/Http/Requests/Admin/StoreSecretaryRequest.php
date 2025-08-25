<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreSecretaryRequest extends FormRequest
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],
            'phone' => ['required', 'digits:10', 'numeric'],
            'date_of_appointment' => ['required', 'date'],
            'gender' => 'required|string|in:male,female',
            'type_wage' => ['required', Rule::in(['number', 'percentage'])],
            'wage' => [
                'required',
                'numeric',
                Rule::when(request('type_wage') === 'number', ['min:1', 'max:1000000']), // راتب
                Rule::when(request('type_wage') === 'percentage', ['min:5', 'max:100']),  // نسبة مئوية
            ],

        ];
    }
}
