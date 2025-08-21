<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Doctor\DoctorDaySlotResource;
use App\Http\Resources\Api\Doctor\DoctorMonthDayResource;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Services\Api\Doctor\DoctorScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorScheduleController extends Controller
{
    protected $service;
    public function __construct(DoctorScheduleService $service)
    {
        $this->service = $service;
    }
    public function monthDays(Request $request, $doctorId)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|numeric|between:1,12'
        ]);

        try {
            $data = $this->service->getMonthDaysWithStatus($doctorId, $request->year, $request->month);
            return DoctorMonthDayResource::collection(collect($data));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

    }
    public function daySlots(Request $request, $doctorId)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {

            $data = $this->service->getDaySlots($doctorId, $request->date);
            return DoctorDaySlotResource::collection(collect($data));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
        /*
               $data = $this->service->getDaySlots($doctorId, $request->date);
               return DoctorDaySlotResource::collection(collect($data));*/
    }
    public function book(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'time' => 'required',   // مثال: "08:00-09:00"

        ]);

        try {
            $appointment = $this->service->book(
                $validated['doctor_id'],
                Auth::user()->id,
                $validated['date'],
                $validated['time'],
                $validated['arrivved_time']  // تبقى مدة الوصول
            );
            return response()->json([
                'message' => ' تم الحجز بنجاح و لكن أدخل مدة اشتغراقك للوصول للعيادة',
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    public function setArrivvedTime(Request $request, $appointmentId)
    {
        $validated = $request->validate([
            'arrivved_time' => 'required|integer|min:1',  // مدة الوصول بالدقائق
        ]);


        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            return response()->json(['message' => 'الموعد غير موجود'], 404);
        }
        if ($appointment->patient_id !== Auth::user()->patient->id) {
            return response()->json(['message' => 'غير مصرح بتعديل هذا الموعد'], 403);
        }
        $minutes = $request->arrivved_time;
        $timeFormatted = gmdate('H:i:s', $minutes * 60); // تحويل 46 إلى "00:46:00"

        $appointment = Appointment::find($appointmentId);
        $appointment->update(['arrivved_time' => $timeFormatted]);



      //  $appointment->update(['arrived_time' => $request->arrived_time]);

        return response()->json([
            'message' => 'تم الحجز بنجاح',
            'appointment' => $appointment
        ]);
    }



}
