<?php

namespace App\Http\Resources\Api\Profile;

use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Repositories\Profile\FileStorageInterface;
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
        $photoUrl = null;

        // الحصول على رابط الصورة بشكل صحيح
       // حل التبعية من الحاوية
        $fileStorage = app(FileStorageInterface::class);

        $photoUrl = null;
        $photo = $this->resource['user']->patient->photo ?? null;

        if ($photo) {
            $photoUrl = $fileStorage->getFullUrl($photo);
        }
        
        return [
            'message' => trans('message.update_profile'),
            'name' => $this->resource['user']->name,
            'email' => $this->resource['user']->email,
            'phone' => $this->resource['user']->phone,
            'photo' => $photoUrl,
            'updated_fields' => $this->resource['changes'] ?? []
        ];
    }

}
