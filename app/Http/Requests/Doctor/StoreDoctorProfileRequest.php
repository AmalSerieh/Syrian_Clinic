<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorProfileRequest extends FormRequest
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
            'specialist' => 'required|string|max:255',
            'cer_place' => 'nullable|string',
            'cer_name' => 'nullable|string',
            'cer_images' => 'nullable|image',
            'cer_date' => 'nullable|date',
            'exp_place' => 'nullable|string',
            'exp_years' => 'required|numeric',
            'biography' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'date_birth' => 'required|date',
        ];
    }
}
