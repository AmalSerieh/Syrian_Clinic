<?php

namespace App\Http\Requests\Api\PatientRecord;

use Illuminate\Foundation\Http\FormRequest;

class MedicationRequest extends FormRequest
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
            'med_type' => 'required|in:chronic,current',
            'med_name' => 'required|string|max:255',
            'med_start_date' => 'required|date',
            'med_end_date' => 'nullable|date|after_or_equal:med_start_date',
            'med_frequency' => 'required|in:once_daily,twice_daily,three_times_daily,daily,weekly,monthly,yearly',
            'med_dosage_form' => 'required|in:tablet,capsule,pills,syrup,liquid,drops,sprays,patches,injections',
            'med_dose' => 'required|numeric|min:0.1|max:1000',
            'med_timing' => 'nullable|in:before_food,after_food,morinng',
            'med_prescribed_by_doctor' => 'nullable|string|max:255',
        ];
    }
    protected array $allowedDoses = [
        'tablet' => ['0.5', '1', '1.5', '2', '2.5'],
        'capsule' => ['0.5', '1', '1.5', '2', '2.5'],
        'pills' => ['0.5', '1', '1.5', '2', '2.5'],
        'syrup' => ['5', '10', '15', '20', '25', '50', '100', '200'],
        'liquid' => ['5', '10', '15', '20', '25', '50', '100', '200'],
        'drops' => ['5', '10', '15', '20', '25', '50', '100', '200'],
    ];
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $form = $this->input('med_dosage_form');
            $dose = $this->input('med_dose');

            if (isset($this->allowedDoses[$form]) && !in_array((string) $dose, $this->allowedDoses[$form])) {
                $validator->errors()->add('med_dose', 'الجرعة المختارة غير مناسبة لنوع الدواء.');
            }

            if ($this->input('med_type') === 'current' && empty($this->input('med_end_date'))) {
                $validator->errors()->add('med_end_date', 'تاريخ نهاية الدواء مطلوب للدواء المؤقت.');
            }
        });
    }

 public function messages(): array
    {
        return [
            // عربي
            'med_type.required' => __('message.med_type_required'),
            'med_type.in' => __('message.med_type_invalid'),

            'med_name.required' => __('message.med_name_required'),
            'med_name.string' => __('message.med_name_string'),
            'med_name.max' => __('message.med_name_max'),

            'med_start_date.required' => __('message.med_start_date_required'),
            'med_start_date.date' => __('message.med_start_date_date'),

            'med_end_date.date' => __('message.med_end_date_date'),
            'med_end_date.after_or_equal' => __('message.med_end_date_after_or_equal'),

            'med_frequency.required' => __('message.med_frequency_required'),
            'med_frequency.in' => __('message.med_frequency_invalid'),

            'med_dosage_form.required' => __('message.med_dosage_form_required'),
            'med_dosage_form.in' => __('message.med_dosage_form_invalid'),

            'med_dose.required' => __('message.med_dose_required'),
            'med_dose.numeric' => __('message.med_dose_numeric'),
            'med_dose.min' => __('message.med_dose_min'),
            'med_dose.max' => __('message.med_dose_max'),

            'med_timing.in' => __('message.med_timing_invalid'),

            'med_prescribed_by_doctor.string' => __('message.med_prescribed_by_doctor_string'),
            'med_prescribed_by_doctor.max' => __('message.med_prescribed_by_doctor_max'),
        ];
    }
}
