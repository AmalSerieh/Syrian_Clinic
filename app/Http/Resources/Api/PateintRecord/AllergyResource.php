<?php

namespace App\Http\Resources\Api\PateintRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllergyResource extends JsonResource
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
            'aller_power' => __('allergy.aller_power.' . $this->aller_power),
            'aller_name' => $this->aller_name,
            'aller_type' => __('allergy.aller_type.' . $this->aller_type),
            'aller_cause' => $this->aller_cause,
            'aller_treatment' => $this->aller_treatment,
            'aller_pervention' => $this->aller_pervention,
            'aller_reasons' => $this->aller_reasons,
        ];
    }
}
