<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\WaitingList;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentConfirmedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Secertary\Appointement\AppointementSerivce;

class SecretaryAppointmentController extends Controller
{
    protected $service;

    public function __construct(AppointementSerivce $service)
    {
        $this->service = $service;
    }
    public function pendingByDoctor($doctorId)
    {
        $appointments = $this->service->getPendingAppointmentsByDoctor($doctorId);

        return view('secretary.appointments.pending', compact('appointments', 'doctorId'));
    }


    public function sendNotification(Request $request)
    {
        $firebaseToken = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        $SERVER_API_KEY = env('FCM_SERVER_KEY');
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
        return back()->with('success', 'Notification send successfully.');

    }

    public function confirm1(Request $request, $appointmentId)
    {

        \Log::info('CSRF token from input: ' . $request->input('_token'));
        \Log::info('From header: ' . $request->header('X-CSRF-TOKEN'));

        try {
            $appointment = $this->service->confirmAppointment($appointmentId);

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'تم تأكيد الموعد بنجاح'
                ]);
            }
            //  $appointment = $this->service->cancelAppointment($appointmentId);

            return redirect()->back()->with('status', 'تم تأكيد الموعد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إلغاء الموعد: ' . $e->getMessage());
        }
    }

    public function cancel1(Request $request, $appointmentId)
    {
        \Log::info('CSRF token from input: ' . $request->input('_token'));
        \Log::info('From header: ' . $request->header('X-CSRF-TOKEN'));

        try {
            $appointment = $this->service->cancelAppointment($appointmentId);

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'تم إلغاء الموعد بنجاح'
                ]);
            }
            //  $appointment = $this->service->cancelAppointment($appointmentId);

            return redirect()->back()->with('status', 'تم إلغاء الموعد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إلغاء الموعد: ' . $e->getMessage());
        }
    }

    public function todayAppointments()
    {
        $appointments = $this->service->getTodayAppointments();
        /*  return view('secretary.appointments.today', [
             'appointments' => AppointmentResource::collection($appointments)
         ]); */
        return view('secretary.appointments.today', compact('appointments'));

    }
    public function markArrived(Appointment $appointment)
    {
        $today = Carbon::today();

        // تحقق أن الموعد اليوم
        if ($appointment->date != $today->toDateString()) {
            return back()->with('error', 'الموعد ليس اليوم.');
        }

        // تحقق أن الموعد في الوقت المناسب ضمن دوام الطبيب
        $dayName = $today->locale('en')->dayName; // مثل Monday

        $schedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->where('day', $dayName)
            ->first();

        if (!$schedule) {
            return back()->with('error', 'لا يوجد دوام للطبيب اليوم.');
        }

        // تحقق من التوقيت
        if (
            $appointment->start_time < $schedule->start_time ||
            $appointment->end_time > $schedule->end_time
        ) {
            return back()->with('error', 'الموعد خارج وقت دوام الطبيب.');
        }

        // تحقق من الحالة
        if ($appointment->location_type !== 'on_Street' || $appointment->status !== 'confirmed') {
            return back()->with('error', 'المريض ليس في الطريق أو وصل مسبقًا. لا يمكن تعديل الحالة.');
        }
        // تغيير الحالة
        $appointment->update(['location_type' => 'in_Clinic']);
        WaitingList::create([
            'appointment_id' => $appointment->id,
            'check_in_time' => now(),
        ]);
        return back()->with('success', 'تم تأكيد وصول المريض وإضافته لقائمة الانتظار.');
    }

    public function getNextAvailableSlot($doctorId)
    {
        $today = now();

        return Appointment::where('doctor_id', $doctorId)
            ->where('status', 'available')
            ->where(function ($q) use ($today) {
                $q->whereDate('date', '>', $today->toDateString())
                    ->orWhere(function ($q2) use ($today) {
                        $q2->whereDate('date', '=', $today->toDateString())
                            ->whereTime('start_time', '>', $today->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->first();
    }

}

