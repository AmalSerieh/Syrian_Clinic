<?php
namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class AppointmentResource extends JsonResource
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
        'date' => $this->date,
        'start_time' => $this->start_time,
        'end_time' => $this->end_time,
        'status' => $this->status,
        'doctor' => new DoctorResource($this->whenLoaded('doctor')),
        'patient' => new PatientResource($this->whenLoaded('patient')),

        ];
    }
}

