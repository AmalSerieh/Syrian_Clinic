<?php

namespace App\Http\Resources;

use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PatientResource extends JsonResource
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
            'user_id' => $this->user_id,
            'photo' => $this->photo,
            'photo_url' => $this->photo
                ? asset('storage/' . str_replace('\\', '/', $this->photo))
                : asset('storage/avatars/default.jpg'),


            //  'created_at' => $this->created_at?->toDateTimeString(),
            // 'user'    => new UserResource($this->whenLoaded('user')),//ملاحظة: نستخدم whenLoaded() لضمان تحميل العلاقة فقط إذا كانت محمّلة.
        ];
    }
}
