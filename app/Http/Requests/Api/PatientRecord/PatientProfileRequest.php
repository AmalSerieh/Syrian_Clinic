<?php

namespace App\Http\Requests\Api\PatientRecord;

use Illuminate\Foundation\Http\FormRequest;

class PatientProfileRequest extends FormRequest
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
            'date_birth' => 'required|date|before:today',
            'height' => 'required|numeric|min:1',
            'weight' => 'required|numeric|min:1',
            'blood_type' => 'required|string|in:A+,B+,O+,AB+,A-,B-,O-,AB-,Gwada-',
            'smoker' => 'required|boolean',
            'alcohol' => 'required|boolean',
            'drug' => 'required|boolean',
            'matital_status' => 'required|string|in:single,married,widower,divorced',
        ];
    }
    public function messages(): array
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return [
                'gender.required' => 'حقل الجنس مطلوب.',
                'gender.in' => 'الجنس يجب أن يكون ذكر أو أنثى.',
                'date_birth.required' => 'تاريخ الميلاد مطلوب.',
                'date_birth.date' => 'تاريخ الميلاد يجب أن يكون تاريخ صالح.',
                'date_birth.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم.',
                'height.required' => 'الطول مطلوب.',
                'height.numeric' => 'الطول يجب أن يكون رقم.',
                'height.min' => 'الطول يجب أن يكون أكبر من 0.',
                'weight.required' => 'الوزن مطلوب.',
                'weight.numeric' => 'الوزن يجب أن يكون رقم.',
                'weight.min' => 'الوزن يجب أن يكون أكبر من 0.',
                'blood_type.required' => 'فصيلة الدم مطلوبة.',
                'blood_type.in' => 'فصيلة الدم غير صحيحة.',
                'smoker.required' => 'حقل المدخن مطلوب.',
                'smoker.boolean' => 'قيمة المدخن يجب أن تكون true أو false.',
                'alcohol.required' => 'حقل شرب الكحول مطلوب.',
                'alcohol.boolean' => 'قيمة شرب الكحول يجب أن تكون true أو false.',
                'drug.required' => 'حقل تعاطي المخدرات مطلوب.',
                'drug.boolean' => 'قيمة تعاطي المخدرات يجب أن تكون true أو false.',
                'matital_status.required' => 'الحالة الاجتماعية مطلوبة.',
                'matital_status.in' => 'القيمة غير صحيحة للحالة الاجتماعية.',
            ];
        }

        // افتراضيًا للإنجليزية
        return [
            'gender.required' => 'The gender field is required.',
            'gender.in' => 'Gender must be either male or female.',
            'date_birth.required' => 'The date of birth field is required.',
            'date_birth.date' => 'The date of birth must be a valid date.',
            'date_birth.before' => 'The date of birth must be before today.',
            'height.required' => 'The height field is required.',
            'height.numeric' => 'Height must be a number.',
            'height.min' => 'Height must be greater than 0.',
            'weight.required' => 'The weight field is required.',
            'weight.numeric' => 'Weight must be a number.',
            'weight.min' => 'Weight must be greater than 0.',
            'blood_type.required' => 'The blood type field is required.',
            'blood_type.in' => 'Invalid blood type value.',
            'smoker.required' => 'The smoker field is required.',
            'smoker.boolean' => 'Smoker field must be true or false.',
            'alcohol.required' => 'The alcohol field is required.',
            'alcohol.boolean' => 'Alcohol field must be true or false.',
            'drug.required' => 'The drug field is required.',
            'drug.boolean' => 'Drug field must be true or false.',
            'matital_status.required' => 'The marital status field is required.',
            'matital_status.in' => 'Invalid value for marital status.',
        ];
    }

}
