<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientProfileRequest extends FormRequest
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
            'gender' => 'required|string|in:male,female',
            'date_birth' => 'required|date',
            'height' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'blood_type' => 'required|string|in:A+,B+,O+,AB+,A-,B-,O-,AB-',
            'smoker' => 'required|boolean',
            'alcohol' => 'required|boolean',
            'matital_status' => 'required|string|in:single,married,widower,divorced',
        ];
    }
}
