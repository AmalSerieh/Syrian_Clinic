<?php

namespace App\Http\Controllers\Web\Doctor\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorMaterial;
use App\Models\DoctorSchedule;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Models\WaitingList;
use App\Notifications\AppointmentCancelledNotification;
use App\Notifications\AppointmentPostponedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;


class DoctorAppointmentController extends Controller
{
    //عرض المرضى تبع اليوم
    public function patients()
    {
        $appointments = Appointment::with('patient.user')
            ->where('doctor_id', Auth::user()->doctor->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'confirmed') // فقط المواعيد المؤكدة
            ->orderBy('start_time')
            ->get();
        return view('doctor.appointments.today-patients', compact('appointments'));
    }
    //المرضى يلي في العيادة
    public function patientsInClinic()
    {
        //dd('yes');
        $doctor = Auth::user()->doctor;
        $doctorId = $doctor->id;
        $today = Carbon::today();

        /*  if (!$this->isDoctorAvailableNow($doctor->id)) {
             abort(403, 'الطبيب ليس في وقت دوامه حالياً.');
         } */
        $appointments = Appointment::with('patient.user')
            ->where('doctor_id', Auth::user()->doctor->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'confirmed') // ✅ هذا الشرط ضروري
            ->where('location_type', 'in_Clinic')
            ->orderBy('start_time')
            ->get();
        $waitingPatients = WaitingList::whereHas('appointment', function ($q) use ($doctorId, $today) {
            $q->where('doctor_id', $doctorId)
                ->whereDate('date', $today)
                ->where('location_type', 'in_Clinic'); // تأكد أن المريض في العيادة فعلاً
        })
            ->where('w_status', 'waiting')
            ->orderBy('w_check_in_time') // ترتيب حسب وقت الدخول الحقيقي
            ->get();

        return view('doctor.appointments.clinic_patients', compact('waitingPatients', 'appointments'));
    }

    public function enterConsultation(Appointment $appointment)
    {
        try {
            // تحقق أن المريض في العيادة
            if ($appointment->location_type !== 'in_Clinic') {
                return back()->with('error', 'المريض غير متواجد في العيادة.');
            }
            $doctor = $appointment->doctor;

            // ✅ تحقق أن الطبيب ضمن دوامه
            $todayDay = now()->format('l'); // Sunday, Monday, ...
            $nowTime = now()->format('H:i:s');

            $doctorSchedule = DoctorSchedule::where('doctor_id', $doctor->id)
                //->where('day', $todayDay)
                ->where('start_time', '<=', $nowTime)
                ->where('end_time', '>=', $nowTime)
                ->get();


            /* if ($doctorSchedule->isEmpty()) {
                // dd("الطبيب ليس ضمن دوامه الآن");
                return back()->with('error', 'الطبيب غير متواجد في العيادة الآن (خارج أوقات الدوام).');
            } */


            // تحقق أن هذا المريض هو أول من ينتظر للدخول عند هذا الطبيب اليوم
            $firstInLine = Appointment::where('doctor_id', $appointment->doctor_id)
                ->whereDate('date', Carbon::today())
                ->where('location_type', 'in_Clinic')
                ->where('status', 'confirmed')
                ->orderBy('start_time')
                ->first();
            /*
           if (!$firstInLine || $firstInLine->id !== $appointment->id) {
               return back()->with('error', 'ليس هذا دور هذا المريض بعد.');
           } */


            // تحديث جدول الانتظار: الحالة إلى in_progress، وتسجيل وقت الدخول
            $waitingEntry = WaitingList::where('appointment_id', $appointment->id)->first();

            if ($waitingEntry) {
                $waitingEntry->update([
                    'w_status' => 'in_progress',
                    'w_start_time' => now()// وقت بدء المعاينة
                ]);
            }


            // تحديث الموعد: مكان المريض في العيادة عند الطبيب
            $appointment->update([
                'location_type' => 'at_Doctor'
            ]);
            // 🔔 نادِ على المريض التالي (الرابع)
            app(\App\Services\Secertary\Notification\AppointementStatusArrivvedNotificationService::class)->sendReminders(); // 👈 استدعاء تابع التذكير

            // ✅ إنشاء سجل الزيارة
            $visit = Visit::create([
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'patient_id' => $appointment->patient_id,
                'v_started_at' => Carbon::now(),
                'v_status' => 'active',
                'v_ended_at' => null, // <--- هنا
            ]);


            return back()->with('status', 'تم إدخال المريض إلى غرفة المعاينة.');
        } catch (\Exception $e) {
            \Log::error('فشل في إدخال المريض: ' . $e->getMessage());
            //dd($e->getMessage(), $e->getFile(), $e->getLine());
            return back()->with('error', 'حدث خطأ أثناء إدخال المريض.');
        }

    }
    public function finishVisit(Request $request, $id)
    {
        $visit = Visit::with('appointment')->findOrFail($id);

        $request->validate([
            'v_notes' => 'nullable|string',
            'v_price' => 'required|numeric|min:1',
        ]);

        $hasPrescription = Prescription::where('visit_id', $visit->id)->exists();
        $isFollowUp = $visit->appointment->type === 'followup';

        if (!$isFollowUp && !$hasPrescription) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['يجب إدخال وصفة طبية لهذا الموعد.']], 422);
            }
            return back()->withErrors(['error' => 'يجب إدخال وصفة طبية لهذا الموعد.']);
        }

        DB::beginTransaction();
        try {
            $visit->update([
                'v_notes' => $request->v_notes,
                'v_price' => $request->v_price,
                'v_status' => 'in_payment',
                'v_ended_at' => now(),
            ]);

            $visit->appointment->update([
                'status' => 'completed',
                'location_type' => 'in_Payment'
            ]);

            WaitingList::where('appointment_id', $visit->appointment_id)
                ->update([
                    'w_status' => 'done',
                    'w_end_time' => now(),
                ]);

            DB::commit();

            // ✅ إذا الطلب AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنهاء الزيارة بنجاح، بانتظار الدفع.',
                    'v_price' => $visit->v_price,
                    'totalConsumption' => DoctorMaterial::where('visit_id', $visit->id)
                        ->sum(DB::raw('dm_quantity * dm_price'))
                ]);
            }

            // ✅ إذا الطلب عادي (submit عادي)
            return redirect()->back()
                ->with('status', 'تم إنهاء الزيارة بنجاح، بانتظار الدفع.')
                ->with('v_price', $visit->v_price);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['حدث خطأ: ' . $e->getMessage()]], 500);
            }

            return back()->withErrors(['error' => 'حدث خطأ أثناء إنهاء الزيارة: ' . $e->getMessage()]);
        }
    }


    //✅ 1. إدخال السعر (من الطبيب)

    public function setPrice(Request $request, $id)
    {
        $request->validate([
            'v_price' => 'required|numeric|min:1',
        ]);

        $visit = Visit::findOrFail($id);
        $visit->update([
            'v_price' => $request->v_price,
        ]);

        return back()->with('success', 'تم تحديد سعر الزيارة.');
    }



    //لفحص إن كان الطبيب في وقت دوامه الآن:
    function isDoctorAvailableNow($doctorId)
    {
        $now = Carbon::now();
        $today = $now->format('l'); // Sunday, Monday, etc.
        $currentTime = $now->format('H:i:s');

        return DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day', $today)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->exists();
    }

    public function patientsall()
    {
        // الحصول على مواعيد الدكتور المؤكدة من اليوم فصاعداً
        $appointments = Appointment::with(['patient.user', 'patient'])
            ->where('doctor_id', Auth::user()->doctor->id)
            ->whereDate('date', '>=', Carbon::today())
            ->where('status', 'confirmed')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // تجميع المواعيد حسب المريض وأخذ أحدث موعد
        $patients = collect();

        foreach ($appointments->groupBy('patient_id') as $patientAppointments) {
            $patient = $patientAppointments->first()->patient;
            $latestAppointment = $patientAppointments->sortByDesc(function ($appt) {
                return $appt->date . ' ' . $appt->start_time;
            })->first();

            $patient->latest_appointment = $latestAppointment;
            $patients->push($patient);
        }

        return view('doctor.appointments.patientsall', compact('patients', 'appointments'));
    }

    public function cancel1(Request $request, $appointmentId)
    {
        \Log::info('CSRF token from input: ' . $request->input('_token'));
        \Log::info('From header: ' . $request->header('X-CSRF-TOKEN'));

        try {
            $result = $this->cancelAppointment($appointmentId);

            $message = 'تم إلغاء الموعد بنجاح';
            $notificationDetails = '';

            if (!$result['has_token']) {
                $notificationDetails = 'لم يتم إرسال إشعار للتطبيق لأن المريض ليس لديه جهاز مسجل';
            } elseif (!$result['token_valid']) {
                $notificationDetails = 'لم يتم إرسال إشعار للتطبيق لأن رمز الجهاز غير صالح';
            } elseif (!$result['notification_sent']) {
                $notificationDetails = 'حدث خطأ في إرسال الإشعار للتطبيق';
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'notification_sent' => $result['notification_sent'],
                    'notification_details' => $notificationDetails
                ]);
            }

            return redirect()->back()->with([
                'status' => $message,
                'notification_warning' => $notificationDetails
            ]);

        } catch (\Exception $e) {
            $errorMessage = 'فشل إلغاء الموعد: ' . $e->getMessage();
            \Log::error($errorMessage);

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }
    public function cancelAppointment($appointmentId)
    {
        $appointment = $this->updateStatus($appointmentId, 'canceled_by_doctor');
        $result = [
            'has_token' => false,
            'notification_sent' => false,
            'token_valid' => false
        ];
        if ($appointment->patient && $appointment->patient->user) {
            $user = $appointment->patient->user;
            // dd($user->fcm_token);
            $user->notify(new AppointmentCancelledNotification($appointment));
            // إرسال إشعار Firebase فقط إذا كان هناك token
            if (!empty($user->fcm_token)) {
                $result['has_token'] = true;
                $result['token_valid'] = true;
                $success = $this->sendFirebaseNotification(
                    $appointment->patient->user->fcm_token,
                    'تم إلغاء الموعد' . $appointment->doctor->user->name,
                    'تم إلغاء موعدك بتاريخ ' . $appointment->date
                );
                if ($success) {
                    $result['notification_sent'] = $success;
                    \Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                } else {
                    \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                }
            } else {
                $result['has_token'] = !empty($user->fcm_token);
                $result['token_valid'] = false;
                \Log::info("لا يوجد FCM token للمستخدم ID={$user->id}، تم حفظ الإشعار في قاعدة البيانات فقط");
            }
        } else {
            \Log::warning("لا يوجد مريض مرتبط بالموعد ID={$appointment->id}");
        }
        return $result;
    }
    public function sendFirebaseNotification($token, $title, $body)
    {
        if (empty($token)) {
            \Log::warning("محاولة إرسال إشعار بدون FCM Token");
            return false;
        }

        // التحقق من صحة الـ token
        if (!$this->isValidFcmToken($token)) {
            \Log::warning("FCM token غير صالح: {$token}");
            return false;
        }
        \Log::info("Attempting to send to token: {$token}");
        try {
            $credentialPath = config('services.firebase.credentials_file');
            if (!file_exists($credentialPath)) {
                \Log::error('Firebase credentials file not found');
                return false;
            }


            // التحقق من صحة ملف الاعتماد
            $credentials = json_decode(file_get_contents($credentialPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Invalid JSON in Firebase credentials file');
                return false;
            }

            $messaging = (new Factory)
                ->withServiceAccount($credentialPath)
                ->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData([
                    'type' => 'appointment_update',
                    'appointment_id' => $appointment->id ?? null,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]);
            $messaging->send($message);
            \Log::info('Firebase notification sent successfully');
            return true;
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            // هذا الخطأ يعني أن الـ token غير صالح أو منتهي
            \Log::warning('FCM token not found or invalid: ' . $e->getMessage());

            // يمكنك حذف الـ token من قاعدة البيانات هنا
            $this->removeInvalidFcmToken($token);
            return false;

        } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationFailed $e) {
            \Log::error('Firebase authentication failed: ' . $e->getMessage());
            return false;

        } catch (\Exception $e) {
            \Log::error('Firebase Error: ' . $e->getMessage());
            \Log::error('Token used: ' . $token);
            \Log::error('Credentials path: ' . $credentialPath);
            return false;
        }
    }

    public function updateStatus($appointmentId, $status)
    {
        $appointment = Appointment::find($appointmentId);
        if (!$appointment) {
            \Log::error("الموعد {$appointmentId} غير موجود");
            return null;
        }
        $appointment->status = $status;
        $appointment->save();
        \Log::info("تم تحديث حالة الموعد {$appointmentId} إلى {$status}");
        return $appointment;
    }

    public function removeInvalidFcmToken($invalidToken)
    {
        // ابحث عن جميع المستخدمين الذين لدون هذا الـ token وقم بإزالته
        $users = User::where('fcm_token', $invalidToken)->get();

        foreach ($users as $user) {
            $user->fcm_token = null;
            $user->save();
            \Log::info("تم إزالة FCM token غير الصالح للمستخدم ID: {$user->id}");
        }
    }
    public function isValidFcmToken($token)
    {
        if (empty($token)) {
            return false;
        }

        // التحقق من تنسيق الـ token (يجب أن يكون طوله معقولاً)
        if (strlen($token) < 50 || strlen($token) > 300) {
            \Log::warning("FCM token length invalid: " . strlen($token));
            return false;
        }

        // يمكنك إضافة المزيد من التحققات حسب تنسيق الـ token
        return true;
    }

    public function cancelTodayAppointments(Request $request)
    {
        $today = Carbon::today();

        // جلب المواعيد اليوم المؤكدة من قبل الطبيب
        $appointments = Appointment::where('date', $today)
            ->where('status', 'confirmed')
            ->whereIn('location_type', ['in_Home', 'on_Street', 'in_Clinic'])
            ->get();

        if ($appointments->isEmpty()) {
            return back()->with('error', 'لا توجد مواعيد اليوم يمكن إلغاؤها.');
        }

        foreach ($appointments as $appointment) {
            $appointment->update([
                'status' => 'canceled_by_doctor', // أو canceled_by_secretary حسب السيناريو
            ]);

            // إرسال إشعار للمريض (اختياري)
            $user = $appointment->patient->user;
            if ($user) {
                $user->notify(new AppointmentCancelledNotification($appointment));
                if (!empty($user->fcm_token)) {
                    $result['has_token'] = true;
                    $result['token_valid'] = true;
                    $success = $this->sendFirebaseNotification(
                        $appointment->patient->user->fcm_token,
                        ' تم إلغاء الموعد من قبل الطبيب' . $appointment->doctor->user->name,
                        'تم إلغاء موعدك بتاريخ ' . $appointment->date
                    );
                    if ($success) {
                        $result['notification_sent'] = $success;
                        \Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                    } else {
                        \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                    }
                } else {
                    $result['has_token'] = !empty($user->fcm_token);
                    $result['token_valid'] = false;
                    \Log::info("لا يوجد FCM token للمستخدم ID={$user->id}، تم حفظ الإشعار في قاعدة البيانات فقط");
                }
            }

        }

        return back()->with('status', 'تم إلغاء جميع المواعيد المؤكدة اليوم بنجاح.');
    }
    public function postpone1(Request $request, Appointment $appointment)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1'
        ]);

        $doctor = $appointment->doctor;
        $minutes = $request->minutes;
        $doctorEndTime = Carbon::parse($doctor->end_time);

        $newTime = Carbon::parse($appointment->time)->addMinutes($minutes);

        if ($newTime->gt($doctorEndTime)) {
            return response()->json([
                'message' => 'التأجيل يتجاوز دوام الطبيب'
            ], 400);
        }

        DB::transaction(function () use ($appointment, $minutes, $doctor, $doctorEndTime) {
            // جلب جميع المواعيد لليوم نفسه
            $appointments = Appointment::where('doctor_id', $doctor->id)
                ->where('date', $appointment->date)
                ->orderBy('time')
                ->get();
            // 1️⃣ تأجيل الموعد المحدد
            //$appointment->time = Carbon::parse($appointment->time)->addMinutes($minutes)->format('H:i:s');
            //$appointment->save();

            Notification::send($appointment->patient, new AppointmentPostponedNotification($appointment));

            // إشعار FCM
            $this->sendFirebaseNotification1(
                $appointment->patient->user->fcm_token ?? null,
                'تم تأجيل موعدك',
                'تم تأجيل موعدك مع ' . $appointment->doctor->name . ' إلى ' . $appointment->time,
                [
                    'appointment_id' => $appointment->id,
                    'type' => 'appointment_update'
                ]
            );

            // 2️⃣ المواعيد التالية
            $nextAppointments = Appointment::where('doctor_id', $doctor->id)
                ->where('date', $appointment->date)
                ->where('time', '>', $appointment->time)
                ->orderBy('time')
                ->get();

            foreach ($nextAppointments as $next) {
                $proposedTime = Carbon::parse($next->time)->addMinutes($minutes);

                if ($proposedTime->gt($doctorEndTime)) {
                    continue;
                }

                $next->time = $proposedTime->format('H:i:s');
                $next->save();

                LaravelNotification::send($next->patient, new AppointmentPostponedNotification($next));

                $this->sendFirebaseNotification1(
                    $next->patient->user->fcm_token ?? null,
                    'تم تأجيل موعدك',
                    'تم تأجيل موعدك مع ' . $next->doctor->name . ' إلى ' . $next->time,
                    [
                        'appointment_id' => $next->id,
                        'type' => 'appointment_update'
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'تم تأجيل الموعد والمواعيد التالية بنجاح'
        ]);
    }
    public function postpone(Request $request)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1'
        ]);

        $doctor = auth()->user()->doctor; // الطبيب الحالي
        $minutes = (int) $request->minutes;

        // وقت انتهاء دوام الطبيب
        $doctorEndTime = Carbon::parse($doctor->end_time);

        // 🟢 نجيب كل المواعيد المؤكدة لليوم الحالي
        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', Carbon::today()) // اليوم فقط
            ->where('status', 'confirmed')       // فقط المؤكدة
            ->orderBy('start_time')
            ->get();

        if ($appointments->isEmpty()) {

            return redirect()->back()
                ->with('error', 'لا يوجد مواعيد مؤكدة اليوم ليتم تأجيلها');
        }

        // ✅ تحقق مسبقاً إذا كان أي موعد بعد التأجيل سيتجاوز دوام الطبيب
        foreach ($appointments as $apt) {
            $newTime = Carbon::parse($apt->time)->addMinutes($minutes);
            if ($newTime->gt($doctorEndTime)) {

                return redirect()->back()
                    ->with('status', 'التأجيل يتجاوز دوام الطبيب، لا يمكن تأجيل المواعيد');
            }
        }

        // ⏳ إذا كل شيء تمام → نعمل التأجيل
        DB::transaction(function () use ($appointments, $minutes, $doctor) {
            foreach ($appointments as $apt) {
                $newTime = Carbon::parse($apt->time)->addMinutes($minutes);
                $apt->time = $newTime->format('H:i:s');
                $apt->save();

                // إشعار Laravel Notifications
                Notification::send($apt->patient, new AppointmentPostponedNotification($apt));

                // إشعار عبر Firebase (إذا عندك fcm_token)
                if (!empty($apt->patient->user->fcm_token)) {
                    $this->sendFirebaseNotification1(
                        $apt->patient->user->fcm_token,
                        'تم تأجيل موعدك',
                        'تم تأجيل موعدك مع ' . $doctor->name . ' إلى ' . $apt->time,
                        [
                            'appointment_id' => $apt->id,
                            'type' => 'appointment_update'
                        ]
                    );
                }
            }
        });



        return redirect()->back()
            ->with('status', 'تم تأجيل جميع المواعيد المؤكدة لهذا اليوم بنجاح');
    }


    public function sendFirebaseNotification1($token, $title, $body, array $data = [])
    {
        if (empty($token)) {
            \Log::warning("محاولة إرسال إشعار بدون FCM Token");
            return false;
        }

        try {
            $credentialPath = config('services.firebase.credentials_file');
            if (!file_exists($credentialPath)) {
                \Log::error('Firebase credentials file not found');
                return false;
            }

            $messaging = (new Factory)
                ->withServiceAccount($credentialPath)
                ->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(FirebaseNotification::create($title, $body))
                ->withData(array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]));

            $messaging->send($message);
            \Log::info("Firebase notification sent successfully to token={$token}");
            return true;

        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            \Log::warning("FCM token not found or invalid: {$e->getMessage()}");
            $this->removeInvalidFcmToken($token);
            return false;

        } catch (\Exception $e) {
            \Log::error("Firebase Error: " . $e->getMessage());
            return false;
        }
    }






}
