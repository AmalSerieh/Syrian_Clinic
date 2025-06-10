<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientProfileResource extends JsonResource
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
            'gender' => $this->gender,
            'date_birth' => $this->date_birth,
            'height' => $this->height,
            'weight' => $this->weight,
            'blood_type' => $this->blood_type,
            'smoker' => $this->smoker,
            'alcohol' => $this->alcohol,
            'matital_status' => $this->matital_status,
        ];
    }
}
