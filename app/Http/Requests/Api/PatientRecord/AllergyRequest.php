<?php

namespace App\Http\Requests\Api\PatientRecord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AllergyRequest extends FormRequest
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
            'aller_power' => ['required', 'string', 'in:strong,medium,weak'],
            'aller_name' => ['required', 'string'],
            'aller_type' => ['required', 'string', 'in:animal,pollen,Food,dust,mold,medicine,seasons,other'],
            'aller_cause' => ['nullable', 'string'],
            'aller_treatment' => ['nullable', 'string'],
            'aller_pervention' => ['nullable', 'string'],
            'aller_reasons' => ['nullable', 'string'],
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

            $exists = \App\Models\Allergy::where('patient_record_id', $record->id)
                ->where('aller_name', $this->aller_name)
                ->where('aller_power', $this->aller_power)
                ->where('aller_type', $this->aller_type)
                ->exists();

            if ($exists) {
                $validator->errors()->add('aller_name', 'هذه الحساسية موجودة مسبقًا بنفس التفاصيل.');
            }
        });
    }
    public function messages(): array
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return [
                'aller_power.required' => 'حقل قوة الحساسية مطلوب.',
                'aller_power.in' => 'قوة الحساسية يجب أن يكون قوية أو ضعيفة أو متوسطة',
                'aller_name.required' => 'حقل اسم الحساسية مطلوب.',
                'aller_type.required' => ' نوع الحساسية مطلوبة.',
                'aller_type.in' => 'نوع الحساسية يجب أن يكون حيوان أو خلايا أو الطعام أو دم أو ماء أو مرض أو موسم أو آخر.',
            ];
        }

        // افتراضيًا للإنجليزية
        return [
            'aller_power.required' => 'حقل قوة الحساسية مطلوب.',
            'aller_power.in' => 'The sensitivity strength should be strong, weak or medium.',
            'aller_name.required' => 'The Sensitivity Name field is required.',
            'aller_type.required' => ' Type of allergy required.',
            'aller_type.in' => 'The type of allergy must be animal, cells, food, blood, water, disease, season, or another.',
        ];
    }
}
