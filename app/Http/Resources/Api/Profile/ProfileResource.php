<?php

namespace App\Http\Resources\Api\Profile;

use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        /*  return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => optional($this->patient)->photo
                ? asset('storage/' . $this->patient->photo)
                : null,
        ]; */
        return [
            'message' => trans('message.update_profile'),
            'name' => $this->resource['user']->name,
            'email' => $this->resource['user']->email,
            'phone' => $this->resource['user']->phone,
            'photo' => $this->resource['user']->patient->photo_url ?? null,
            'updated_fields' => $this->resource['changes'] ?? []
        ];
    }

}
