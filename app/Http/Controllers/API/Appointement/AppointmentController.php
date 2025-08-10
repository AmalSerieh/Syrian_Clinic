<?php

namespace App\Http\Controllers\API\Appointement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Appointement\BookingRequest;
use App\Http\Requests\Api\Appointement\SetArrivvedTimeRequest;
use App\Models\Appointment;
use App\Services\Api\Appointement\BookingService;
use App\Services\Api\Doctor\DoctorScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    protected $service, $bookingService;
    public function __construct(DoctorScheduleService $service, BookingService $bookingService)
    {
        $this->service = $service;
        $this->bookingService = $bookingService;
    }
    public function book(BookingRequest $request)
    {
        try {
            $appointment = $this->bookingService->book(
                $request->doctor_id,
                Auth::user()->id,
                $request->date,
                $request->time,
            );
            return response()->json([
                'message' => ' تم الحجز بنجاح و لكن أدخل مدة استغراقك للوصول للعيادة',
                'appointment' => $appointment,
                'doctor_name' => $appointment->doctor->user->name ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
    public function setArrivvedTime(SetArrivvedTimeRequest $request, $appointmentId)
    {
        // جلب الموعد
        $appointment = Appointment::where('id', $appointmentId)
            ->where('status', 'pending')  // فقط الحجوزات التي حالتها pending
            ->first();

        if (!$appointment) {
            return response()->json(['message' => 'الموعد غير موجود'], 404);
        }

        // التحقق أن المريض هو صاحب الموعد
        if ($appointment->patient_id !== Auth::user()->patient->id) {
            return response()->json(['message' => 'غير مصرح بتعديل هذا الموعد'], 403);
        }
        // تحويل الدقائق إلى صيغة "00:46:00"
        $minutes = $request->arrivved_time;
        $timeFormatted = gmdate('H:i:s', $minutes * 60); // تحويل 46 إلى "00:46:00"

        $appointment = Appointment::find($appointmentId);
        $appointment->update(['arrivved_time' => $timeFormatted]);
        //  $appointment->update(['arrived_time' => $request->arrived_time]);

        return response()->json([
            'message' => 'تم الحجز بنجاح',
            'appointment' => $appointment,
            'doctor_name' => $appointment->doctor->user->name ?? null,
        ]);
    }
    public function cancelByPatient(Appointment $appointment)
    {
        // التحقق أنّ هذا المريض صاحب الموعد
        if ($appointment->patient_id !== Auth::user()->patient->id) {
            return response()->json([
                'message' => 'غير مصرح لك بإلغاء هذا الموعد'
            ], 403);
        }

        // الإلغاء: ممكن نحذفه أو نغير الحالة فقط
        // الأفضل نغير الحالة بدل الحذف للحفاظ على السجل:
        $appointment->update(['status' => 'canceled_by_patient']);

        return response()->json([
            'message' => 'تم إلغاء الموعد بنجاح',
            'appointement' => $appointment
        ]);
    }
    public function cancelByDoctor(Appointment $appointment)
    {
        // التحقق أنّ هذا الطبيب صاحب الموعد
        if ($appointment->doctor_id !== Auth::user()->doctor->id) {
            return response()->json([
                'message' => 'غير مصرح لك بإلغاء هذا الموعد'
            ], 403);
        }
    }


    public function getPatientAppointmentsGroupedByStatus1()
    {
        // التحقق من أن المستخدم هو مريض
        if (!Auth::user()->patient) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $patientId = Auth::user()->patient->id;

        // جلب المواعيد وتصنيفها
        $appointments = Appointment::where('patient_id', $patientId)
            ->get()
            ->groupBy('status')
            ->map(function ($group) {
                return $group->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'doctor_name' => $appointment->doctor->user->name,
                        'date' => $appointment->date,
                        'start_time' => $appointment->start_time,
                        'end_time' => $appointment->end_time,
                        'location' => $appointment->location_type,
                    ];
                });
            });

        // تعبئة الحالات المفقودة بمصفوفات فارغة
        $statuses = [
            'pending',
            'confirmed',
            'completed',
            'canceled_by_patient',
            'canceled_by_doctor',
            'canceled_by_secretary',
            'processing'
        ];

        foreach ($statuses as $status) {
            if (!$appointments->has($status)) {
                $appointments[$status] = [];
            }
        }

        return response()->json([
            'appointments' => $appointments
        ]);
    }

    public function getPatientAppointmentsGroupedByStatus7()
    {
        $today = Carbon::today()->toDateString();

        $pending = Appointment::where('status', 'pending')
            ->whereDate('date', '>=', $today)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get();

        $confirmed = Appointment::where('status', 'confirmed')->whereDate('date', '>=', $today)->orderBy('date')->get();
        $canceledByPatient = Appointment::where('status', 'canceled_by_patient')->orderBy('date')->get();
        $completed = Appointment::where('status', 'completed')->orderBy('date')->get();
        $canceledByDoctor = Appointment::where('status', 'canceled_by_doctor')->orderBy('date')->get();
        $canceledBySecretary = Appointment::where('status', 'canceled_by_secretary')->orderBy('date')->get();
        $processing = Appointment::where('status', 'processing')->orderBy('date')->get();

        return response()->json([
            'appointments' => [
                'pending' => $pending,
                'confirmed' => $confirmed,
                'canceled_by_patient' => $canceledByPatient,
                'completed' => $completed,
                'canceled_by_doctor' => $canceledByDoctor,
                'canceled_by_secretary' => $canceledBySecretary,
                'processing' => $processing,
            ]
        ]);
    }

    public function getPatientAppointmentsGroupedByStatus()
    {
        $today = Carbon::today()->toDateString();

        // تحميل علاقة الطبيب مع كل استعلام
        $pending = Appointment::with(['doctor.user'])
            ->where('status', 'pending')
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->get();

        $confirmed = Appointment::with(['doctor.user'])
            ->where('status', 'confirmed')
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->get();

        $canceledByPatient = Appointment::with(['doctor.user'])
            ->where('status', 'canceled_by_patient')
            ->orderBy('date')
            ->get();

        $completed = Appointment::with(['doctor.user'])
            ->where('status', 'completed')
            ->orderBy('date')
            ->get();

        $canceledByDoctor = Appointment::with(['doctor.user'])
            ->where('status', 'canceled_by_doctor')
            ->orderBy('date')
            ->get();

        $canceledBySecretary = Appointment::with(['doctor.user'])
            ->where('status', 'canceled_by_secretary')
            ->orderBy('date')
            ->get();

        $processing = Appointment::with(['doctor.user'])
            ->where('status', 'processing')
            ->orderBy('date')
            ->get();

        return response()->json([
            'appointments' => [
                'pending' => $this->formatAppointments($pending),
                'confirmed' => $this->formatAppointments($confirmed),
                'canceled_by_patient' => $this->formatAppointments($canceledByPatient),
                'completed' => $this->formatAppointments($completed),
                'canceled_by_doctor' => $this->formatAppointments($canceledByDoctor),
                'canceled_by_secretary' => $this->formatAppointments($canceledBySecretary),
                'processing' => $this->formatAppointments($processing),
            ]
        ]);
    }

    // دالة مساعدة لتنسيق بيانات الموعد
    protected function formatAppointments($appointments)
    {
        return $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'doctor_name' => $appointment->doctor->user->name ?? 'غير معروف',
                'doctor_photo' =>asset('storage/' . $appointment->doctor->photo) ?? null,
                'patient_id' => $appointment->patient_id,
                'date' => $appointment->date,
                'day' => $appointment->day,
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'status' => $appointment->status,
                'location_type' => $appointment->location_type,
                'arrivved_time' => $appointment->arrivved_time,
                'created_by' => $appointment->created_by,
                'type_visit' => $appointment->type_visit,
                'created_at' => $appointment->created_at,
                'updated_at' => $appointment->updated_at
            ];
        });
    }


    public function getConfirmedAppointmentsGroupedByDay()
    {
        $today = Carbon::today();
        $endOfMonth = $today->copy()->endOfMonth();

        // جلب جميع المواعيد المؤكدة لهذا الشهر
        $appointments = Appointment::where('status', 'confirmed')
            ->whereBetween('date', [$today->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('date');

        // إنشاء جميع تواريخ الشهر من اليوم وحتى نهاية الشهر
        $dates = [];
        for ($date = $today->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dateStr = $date->toDateString();
            // إذا وجدنا مواعيد لهذا اليوم نضيفها، وإلا نضيف مصفوفة فارغة
            $dates[$dateStr] = $appointments->get($dateStr, []);
        }

        return response()->json([
            'appointments' => $dates
        ]);
    }




    public function getNearestConfirmedAppointmentForCurrentUser()
    {
        $user = Auth::user();
        // نفترض أن لديك علاقة: $user->patient->appointments()
        // أو عندك patient_id في appointments

        $today = Carbon::today()->toDateString();

        $appointment = Appointment::where('status', 'confirmed')
            ->where('patient_id', $user->patient->id)
            ->where('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();

        return response()->json([
            'appointment' => $appointment
        ]);
    }



}
