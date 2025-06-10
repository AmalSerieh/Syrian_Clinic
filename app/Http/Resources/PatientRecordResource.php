<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientRecordResource extends JsonResource
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
            'patient_id' => $this->patient_id,
          //  'created_at' => $this->created_at?->toDateTimeString(),
            //'patient' => new PatientResource($this->whenLoaded('patient')),
        ];
    }
}
