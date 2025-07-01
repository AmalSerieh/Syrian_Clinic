<?php

namespace App\Http\Resources\Api\PateintRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiseaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'd_type' => __('patientProfile.d_type.' . $this->d_type),
            'd_name' => $this->d_name,
            'd_diagnosis_date' => $this->d_diagnosis_date,
            'd_doctor' => $this->d_doctor,
            'd_advice' => $this->d_advice,
            'd_prohibitions' => $this->d_prohibitions,
        ];
    }
}
