<?php

namespace App\Http\Requests\Api\PatientRecord;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class MedicationAlarmRequest extends FormRequest
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
        $user = Auth::user();
        $patientRecordId = $user->patient->patient_record ? $user->patient->patient_record->id : null;
        return [
            'medication_id' => [
                'required',
                'exists:medications,id',
                Rule::exists('medications', 'id')->where('patient_record_id', $patientRecordId)
            ],
            'alarm_times' => 'required|array',
            'alarm_times.*' => 'required|date_format:H:i',
        ];
    }
    public function messages(): array
    {
        return [
            'medication_id.exists' => 'الدواء غير موجود في سجلك الطبي',
            'alarm_times.required' => 'يرجى تحديد مواعيد المنبهات',
            // ... رسائل الخطأ الأخرى
        ];
    }// تحقق إضافي لقسم الأدوية
    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $user = auth()->user();
        $patientRecord = $user->patient->patient_record;

        if (!$patientRecord) {
            $validator->errors()->add('patient_record', 'يجب أن يكون لديك سجل طبي قبل إضافة منبهات الأدوية');
            return;
        }

        if ($patientRecord->medications()->count() === 0) {
            $validator->errors()->add('medications', 'قسم الأدوية فارغ. يرجى إضافة دواء أولاً');
            return;
        }

        $medicationId = $this->input('medication_id');
        if (!$patientRecord->medications()->find($medicationId)) {
            $validator->errors()->add('medication_id', 'الدواء غير موجود في سجلك الطبي');
        }
    });
}

}
