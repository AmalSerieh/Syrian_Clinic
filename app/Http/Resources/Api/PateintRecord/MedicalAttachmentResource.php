<?php

namespace App\Http\Resources\Api\PateintRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalAttachmentResource extends JsonResource
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
            'ray_name' => $this->ray_name,
            'ray_laboratory' => $this->ray_laboratory,
            'ray_date' => $this->ray_date,
            'ray_image_url' => asset('storage/' . $this->ray_image),
            'file_type' => pathinfo($this->ray_image, PATHINFO_EXTENSION) === 'pdf' ? 'document' : 'image'
        ];
    }
}
