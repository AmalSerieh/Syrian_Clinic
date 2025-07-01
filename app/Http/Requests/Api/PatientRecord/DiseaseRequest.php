<?php

namespace App\Http\Requests\Api\PatientRecord;

use Illuminate\Foundation\Http\FormRequest;

class DiseaseRequest extends FormRequest
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
            'd_type' => ['required', 'string', 'in:current,chronic'],
            'd_name' => ['required', 'string'],
            'd_diagnosis_date' => ['required', 'string'],
            'd_doctor' => ['nullable', 'string'],
            'd_advice' => ['nullable', 'string'],
            'd_prohibitions' => ['nullable', 'string'],
        ];

    }
     public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $record = auth()->user()->patient->patient_record;

            if (!$record) {
                $validator->errors()->add('patient_record_id', trans('message.no_record'));
                return;
            }

            $exists = \App\Models\Disease::where('patient_record_id', $record->id)
                ->where('d_type', $this->d_type)
                ->where('d_name', $this->d_name)
                ->where('d_diagnosis_date', $this->d_diagnosis_date)
                ->exists();

            if ($exists) {
                $validator->errors()->add('d_type', 'هذا المرض موجود مسبقًا بنفس التفاصيل.');
            }
        });
    }
    public function messages(): array
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return [
                'd_type.required' => 'حقل نوع المرض مطلوب.',
                'd_type.in' => 'نوع المرض يجب أن يكون دائم أو حالي .',
                'd_name.required' => 'حقل اسم المرض مطلوب.',
                'd_diagnosis_date.required' => ' تاريخ تشخيص المرض مطلوب.',
                'd_diagnosis_date' => 'تاريخ تشخيص المرض يجب أن يكون تاريخ صالح.',
            ];
        }

        // افتراضيًا للإنجليزية
        return [
            'd_type.required' => 'The disease type field is required.',
            'd_type.in' => 'The diesease type should be current or chronic.',
            'd_name.required' => 'The diesease Name field is required.',
            'd_diagnosis_date.required' => ' The date of diesease diagnosis field is required.',
            'd_diagnosis_date' => ' The date of diesease diagnosis must be a valid date.',
        ];
    }
}
