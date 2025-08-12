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
            'cer_place' => 'required|string',
            'cer_name' => 'required|string',
            'cer_images' => 'required|image',
            'cer_date' => 'required|date',
            'exp_place' => 'required|string',
            'exp_years' => 'required|numeric|min:0',
            'biography' => 'required|string',
            'date_birth' => 'required|date',
        ];
        // الصورة مطلوبة فقط عند الإنشاء
        if ($this->isMethod('post')) {
            $rules['cer_images'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['cer_images'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }

        return $rules;
    }
}
