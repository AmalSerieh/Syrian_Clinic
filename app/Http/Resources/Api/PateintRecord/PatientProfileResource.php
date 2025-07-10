<?php

namespace App\Http\Resources\Api\PateintRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PatientProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // احسب العمر من تاريخ الميلاد
        $birthDate = Carbon::parse($this->date_birth);
        $now = Carbon::now();
        $diff = $birthDate->diff($now);
        // نستخدم الترجمة هنا
        $formatted = "{$diff->y} " . __('patientProfile.years') . ' ' .
            "{$diff->m} " . __('patientProfile.months') . ' ' .
            "{$diff->d} " . __('patientProfile.days');
        $heightInMeters = $this->height / 100;
        $bmi = null;
        if ($heightInMeters > 0) {
            $bmi = round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return [
            'id' => $this->id,
            'name'=>auth()->user()->name,
            'date_birth' => $this->date_birth,
            'age' => [
                'years' => $diff->y,
                'months' => $diff->m,
                'days' => $diff->d,
                'formatted' => $formatted
            ],
            'weight' => $this->weight,
            'height' => $this->height,
            'bmi' => $bmi,
            'gender' => __('patientProfile.gender.' . $this->gender),
            'matital_status' => __('patientProfile.matital_status.' . $this->matital_status),
            'blood_type' => $this->blood_type,
            'can_receive_from' => $this->getAcceptedBloodTypes(),
            'cannot_receive_from' => $this->getRejectedBloodTypes(),
            'addictions' => [
                'smoker' => (bool) $this->smoker,
                'alcohol' => (bool) $this->alcohol,
                'drug' => (bool) $this->drug,
            ]
        ];
    }
}
