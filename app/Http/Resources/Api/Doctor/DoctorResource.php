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
        // نحاول قراءة اللغة من الهيدر مباشرةً
        $lang = $request->getPreferredLanguage(['ar', 'en']) ?? 'ar';
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'photo' => asset('storage/' . $this->photo),
            'specialization' => $this->doctorProfile?->{"specialist_$lang"} ?? 'غير محدد',
            'biography' => $this->doctorProfile?->biography ?? '',
            'experience_years' => $this->doctorProfile?->exp_years ?? 0,
            'rating' => $this->evaluat->sum('final_evaluate') ?? 0,
        ];
    }

}
