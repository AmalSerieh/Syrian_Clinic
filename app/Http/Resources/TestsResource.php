<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TestsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'test_name' => $this->test_name,
            'test_result' => $this->getResultLink(),
            'test_date' => $this->test_date,
        ];
    }

    protected function getResultLink()
    {
        if ($this->test_result && Storage::disk('public')->exists($this->test_result)) {
            return asset(Storage::url($this->test_result));
        }

        return $this->test_result;
    }

}
