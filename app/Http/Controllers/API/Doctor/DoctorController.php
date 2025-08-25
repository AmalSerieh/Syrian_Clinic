<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Doctor\DoctorDetailsResource;
use App\Http\Resources\Api\Doctor\DoctorResource;
use App\Models\Doctor;
use App\Models\VisitEvaluation;
use App\Repositories\Api\Doctor\DoctorRepositoryInterface;
use App\Services\Api\Doctor\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DoctorController extends Controller
{
    protected $service;
    protected $doctorRepo;

    public function __construct(DoctorRepositoryInterface $doctorRepo, DoctorService $service)
    {
        $this->service = $service;
        $this->doctorRepo = $doctorRepo;
    }
    public function index(Request $request)
    {
        $lang = $request->getPreferredLanguage(['ar', 'en']) ?? 'ar';
        $doctors = $this->service->listAllDoctors($lang);

        return DoctorResource::collection($doctors);
    }
    public function show(Doctor $doctor)
    {
        // dd($doctor->doctorSchedule);
        $schedule = $doctor->doctorSchedule->map(function ($day) {
            return [
                'day' => $day->day,
                'from' => $day->start_time,
                'to' => $day->end_time,
                'max_patients' => $day->max_patients,
            ];
        });

        $timeSlots = $doctor->doctorSchedule->flatMap(function ($day) {
            $slots = [];
            $start = Carbon::createFromTimeString($day->start_time);
            $end = Carbon::createFromTimeString($day->end_time);

            while ($start < $end) {
                $next = $start->copy()->addHour();
                if ($next > $end)
                    $next = $end;

                $slots[] = [
                    'day' => $day->day,
                    'from' => $start->format('H:i'),
                    'to' => $next->format('H:i'),
                ];

                $start = $next;
            }

            return $slots;
        });
        $rating = VisitEvaluation::where('doctor_id', $doctor)
            /* ->sum('final_evaluate') */ ;
        $rating = VisitEvaluation::where('doctor_id', $doctor->id ?? $doctor)
            ->pluck('final_evaluate');
       
        return response()->json([
            'doctor' => new DoctorResource($doctor),
            'rating' => $rating,
            // 'schedule' => $schedule,
            //'time_slots' => $timeSlots,
        ]);
    }


    public function show1(Request $request, $id)
    {
        $lang = $request->getPreferredLanguage(['ar', 'en']) ?? 'en';

        $doctor = $this->service->getDoctorDetails($id, $lang);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }


        return new DoctorDetailsResource($doctor, $lang);
    }
    public function show2($id, Request $request)
    {
        $lang = $request->header('Accept-Language', 'ar');
        $doctor = $this->doctorRepo->findById($id);
        $data = $this->service->prepareDoctorDetails($doctor, $lang);

        return new DoctorDetailsResource($data);
    }
    public function getDaySlots(Request $request, $doctorId)
    {
        $date = $request->input('date'); // ex: 2025-08-05
        $slots = $this->service->getSlotsForDay($doctorId, $date);

        return response()->json($slots);
    }

}
