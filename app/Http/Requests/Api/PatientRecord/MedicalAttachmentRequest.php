<?php

namespace App\Http\Requests\Api\PatientRecord;

use Illuminate\Foundation\Http\FormRequest;

class MedicalAttachmentRequest extends FormRequest
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
            'ray_name' => 'required|string',
            'ray_laboratory' => 'required|string',
            'ray_date' => 'required|date',
            'ray_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240'
        ];
    }
    public function messages(): array
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return [
                'ray_name.required' => 'اسم الأشعة مطلوب.',
                'ray_name.string' => 'يجب أن يكون اسم الأشعة نصًا صحيحًا.',

                'ray_laboratory.required' => 'اسم المختبر مطلوب.',
                'ray_laboratory.string' => 'يجب أن يكون اسم المختبر نصًا صحيحًا.',

                'ray_date.required' => 'تاريخ الأشعة مطلوب.',
                'ray_date.date' => 'يجب أن يكون التاريخ بصيغة صحيحة.',

                'ray_image.required' => 'صورة الأشعة مطلوبة.',
                'ray_image.file' => 'يجب رفع ملف صحيح.',
                'ray_image.mimes' => 'يجب أن يكون الملف من نوع: jpg, jpeg, png, webp.',
                'ray_image.max' => 'يجب ألا يتجاوز حجم الملف 10 ميجابايت.',
            ];
        }
        return [
            'ray_name.required' => 'Ray name is required.',
            'ray_name.string' => 'Ray name must be valid text.',

            'ray_laboratory.required' => 'Laboratory name is required.',
            'ray_laboratory.string' => 'Laboratory name must be valid text.',

            'ray_date.required' => 'Ray date is required.',
            'ray_date.date' => 'The date must be in a valid format.',

            'ray_image.required' => 'Ray image is required.',
            'ray_image.file' => 'A valid file must be uploaded.',
            'ray_image.mimes' => 'The file type must be: jpg, jpeg, png, webp.',
            'ray_image.max' => 'The file size must not exceed 10 MB.',
        ];
    }
}
