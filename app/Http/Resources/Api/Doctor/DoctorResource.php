<?php

namespace App\Http\Resources\Api\Doctor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'name' => $this->name,
            'specialist' => $this->specialist,
            'exp_years' => $this->exp_years,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'biography' => $this->biography,
        ];
    }

}
