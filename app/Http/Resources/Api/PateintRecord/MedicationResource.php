<?php

namespace App\Http\Resources\Api\PateintRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,//رقم تسلسلي (ID) لهذا الدواء في قاعدة البيانات.
            'med_name' => $this->med_name,//اسم الدواء الذي أدخله المريض (أو الطبيب).
            'med_type' => $this->translateEnum('med_type', $this->med_type),//chronic أو current
            'start_date' => $this->med_start_date,//تاريخ بدء أخذ الدواء
            'end_date' => $this->med_end_date,//تاريخ الانتهاء (null للدواء الدائم)
            'frequency' => $this->translateEnum('med_frequency', $this->med_frequency),//تكرار الجرعة
            'med_frequency_value' => $this->med_frequency_value,//قيمة رقمية تُستخدم لحساب الكمية الإجمالية: مثلًا weekly = 1/7 ≈ 0.14، تعني أن المريض يأخذ الجرعة بمعدل 0.14 مرة في اليوم.
            'dosage_form' => $this->translateEnum('med_dosage_form', $this->med_dosage_form),//شكل الدواء
            'dose' => $this->med_dose,//كمية الجرعة الواحدة: مثلًا 100 مل، أو 1 حبة... يعتمد على نوع الدواء.
            'quantity_per_dose' => $this->med_quantity_per_dose,//نفس قيمة الجرعة (للتوحيد)، أي كم يأخذ في كل مرة
            'timing' => $this->translateEnum('med_timing', $this->med_timing),//توقيت أخذ الدواء (بعد الغداء مثلًا)
            'med_total_quantity' => intval($this->med_total_quantity),//العدد الكلي للدواء المتوقع استهلاكه خلال المدة المحددة (يُحسب تلقائيًا). مثلًا هنا: أخذ الدواء أسبوعيًا لمدة معينة → النتيجة كانت 4 جرعات.
            'med_prescribed_by_doctor' => $this->med_prescribed_by_doctor,//اسم الطبيب
            'is_active' => $this->is_active,//حالة الدواء: هل الدواء حاليًا نشط ويأخذه المريض (true) أم توقف (false).
           // 'dose_progress_by 100%' => $this->calculateProgressDetailed()['dose_progress_by 100%'],
            'taken_till_now' => $this->med_type === 'chronic'
                ? $this->calculateTakenQuantity()
                : $this->calculateProgressDetailed()['taken_till_now'],

            'progress_info' => $this->calculateProgressDetailed(),
            'progress_percent % ' => $this->med_total_quantity > 0
                ? round(($this->med_taken_quantity / $this->med_total_quantity) * 100, 2)
                : null,

        ];

    }
    private function translateEnum(string $type, ?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return __('patientProfile.' . $type . '.' . $value);
    }
}
