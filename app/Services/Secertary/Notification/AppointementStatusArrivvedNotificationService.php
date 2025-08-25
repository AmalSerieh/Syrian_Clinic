<?php

namespace App\Services\Secertary\Notification;
use App\Models\Appointment;
use App\Notifications\AppointementStatusArrivvedNotification;
use Carbon\Carbon;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Cache; // تأكد من إضافة هذا
use Illuminate\Support\Facades\Log;   // للتسجيل في حالة الحاجة

class AppointementStatusArrivvedNotificationService
{
       public function sendReminders()
    {
        $now = Carbon::now();
        Log::info("بدء عملية إرسال التذكيرات للمواعيد - الوقت الحالي: " . $now->format('Y-m-d H:i:s'));

        $appointments = Appointment::where('status', 'confirmed')
            ->whereDate('date', $now->toDateString())
            ->where('location_type', 'in_Home')
            ->whereNotNull('arrivved_time')
            ->with('patient.user')
            ->get()
            ->groupBy('doctor_id');

        Log::info("عدد الأطباء الذين لديهم مواعيد: " . count($appointments));

        $totalNotificationsSent = 0;
        $totalFirebaseNotificationsSent = 0;

        foreach ($appointments as $doctorId => $doctorAppointments) {
            Log::info("معالجة مواعيد الطبيب ID: $doctorId - عدد المواعيد: " . count($doctorAppointments));

            // أول ثلاث مواعيد لهذا الطبيب فقط
            $firstThree = $doctorAppointments->take(3);

            foreach ($firstThree as $appointment) {
                // تحقق من وجود المريض وعلاقة المستخدم
                if (!$appointment->patient || !$appointment->patient->user) {
                    Log::warning("مريض غير موجود للموعد ID: {$appointment->id}");
                    continue;
                }

                $startTime = Carbon::parse($appointment->date . ' ' . $appointment->start_time);
                $arrivalDuration = Carbon::parse($appointment->arrivved_time);
                $arrivalMinutes = $arrivalDuration->hour * 60 + $arrivalDuration->minute;
                $sendTime = $startTime->copy()->subMinutes($arrivalMinutes);
                
                // ✅ توليد مفتاح كاش فريد للموعد
                $cacheKey = 'reminder_sent_appointment_' . $appointment->id;

                Log::info("الموعد ID: {$appointment->id} - وقت الإرسال المحدد: " . $sendTime->format('Y-m-d H:i:s'));

                // ⏱ تحقق من وقت الإرسال + التكرار
                if ($now->format('Y-m-d H:i') === $sendTime->format('Y-m-d H:i') && !Cache::has($cacheKey)) {
                    Log::info("إرسال إشعار للموعد ID: {$appointment->id} للمستخدم ID: {$appointment->patient->user->id}");

                    // إرسال إشعار للمريض
                    try {
                        $appointment->patient->user->notify(new AppointementStatusArrivvedNotification($appointment));
                        Log::info("تم إرسال الإشعار المحلي للموعد ID: {$appointment->id}");
                        $totalNotificationsSent++;
                    } catch (\Exception $e) {
                        Log::error("فشل إرسال الإشعار المحلي للموعد ID: {$appointment->id}: " . $e->getMessage());
                    }

                    // إرسال إشعار Firebase إذا كان هناك token
                    if ($appointment->patient->user->fcm_token) {
                        $success = $this->sendFirebaseNotification(
                            $appointment->patient->user->fcm_token,
                            'تذكير بالموعد - تهيئة للوصول 🚶‍♂️',
                            '🔔 لديك موعد اليوم الساعة ' . $startTime->format('g:i A') . '، الرجاء التوجه للعيادة في الوقت المناسب.',
                            $appointment->id
                        );
                        
                        if ($success) {
                            Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                            $totalFirebaseNotificationsSent++;
                        } else {
                            Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                        }
                    } else {
                        Log::warning("لا يوجد FCM token للمستخدم ID={$appointment->patient->user->id}");
                    }

                    $appointment->update(['location_type' => 'on_Street']);
                    
                    // ✅ تخزين أن الإشعار أُرسل بالفعل لمدة 90 دقيقة مثلاً
                    Cache::put($cacheKey, true, now()->addMinutes(90));
                    Log::info("تم تحديث حالة الموعد ID: {$appointment->id} إلى on_Street");
                } else {
                    if (Cache::has($cacheKey)) {
                        Log::info("تم إرسال الإشعار مسبقاً للموعد ID: {$appointment->id} (موجود في الكاش)");
                    } else {
                        Log::info("لم يحن وقت الإرسال بعد للموعد ID: {$appointment->id}");
                    }
                }
            }
        }

        Log::info("انتهت عملية إرسال التذكيرات - تم إرسال {$totalNotificationsSent} إشعارات محلية و {$totalFirebaseNotificationsSent} إشعارات FCM");
    }
    public function sendFirebaseNotification($token, $title, $body)
    {

        if (empty($token)) {
            \Log::warning("محاولة إرسال إشعار بدون FCM Token");
            return false;
        }
        \Log::info("Attempting to send to token: {$token}");
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
                ->withNotification(Notification::create($title, $body))
                ->withData([
                    'type' => 'appointment_reminder',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]);

            $messaging->send($message);
            \Log::info('Firebase notification sent successfully');

            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase Error: ' . $e->getMessage());
            \Log::error('Token used: ' . $token);
            \Log::error('Credentials path: ' . config('services.firebase.credentials_file'));
            return false;
        }


    }
    public function patientsInClinicCountToday($doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->where('location_type', 'in_Clinic')
            ->whereDate('date', Carbon::today())
            ->count();
    }

    /* public function sendReminders()
    {
        $now = Carbon::now();
        $appointments = Appointment::where('status', 'confirmed')
            ->whereDate('date', $now->toDateString())
            ->whereIn('location_type', ['in_Home'])
            ->whereNotNull('arrivved_time')
            ->with('patient.user')
            ->orderBy('start_time') // ترتيب نظري
            ->get()
            ->groupBy('doctor_id');

        foreach ($appointments as $doctorId => $doctorAppointments) {
            $patientsInClinic = $this->patientsInClinicCountToday($doctorId);

            // إرسال الإشعارات فقط إذا أقل من 3 في العيادة
            if ($patientsInClinic >= 3) {
                continue;
            }

            $remainingSlots = 3 - $patientsInClinic;

            // حدد المرضى الذين لم يُرسل لهم إشعار بعد
            $candidates = $doctorAppointments->filter(function ($a) {
                return $a->location_type === 'in_Home';
            });

            // أرسل إشعار لعدد المرضى الذي يحتاجه الطبيب ليكمل الثلاثة
            foreach ($candidates->take($remainingSlots) as $appointment) {
                $startTime = Carbon::parse($appointment->date . ' ' . $appointment->start_time);
                $arrivalDuration = Carbon::parse($appointment->arrivved_time);
                $arrivalMinutes = $arrivalDuration->hour * 60 + $arrivalDuration->minute;
                $sendTime = $startTime->copy()->subMinutes($arrivalMinutes);
                $cacheKey = 'reminder_sent_appointment_' . $appointment->id;

                if ($now->format('Y-m-d H:i') === $sendTime->format('Y-m-d H:i') && !Cache::has($cacheKey)) {
                    // إشعار المريض
                    $appointment->patient->user->notify(new AppointementStatusArrivvedNotification($appointment));

                    if ($appointment->patient->user->fcm_token) {
                        $this->sendFirebaseNotification(
                            $appointment->patient->user->fcm_token,
                            '🚶‍♂️ تذكير بالموعد',
                            'حان وقت التوجه للعيادة.'
                        );
                    }

                    $appointment->update(['location_type' => 'on_Street']);
                    Cache::put($cacheKey, true, now()->addMinutes(90));
                }
            }
        }
    }


    // 🔁 بعد خروجه، نتحقق إن العدد < 3
    public function enterConsultation(Appointment $appointment)
    {
        if ($appointment->location_type !== 'in_Clinic') {
            return back()->with('error', 'المريض غير متواجد في العيادة.');
        }

        $firstInLine = Appointment::where('doctor_id', $appointment->doctor_id)
            ->whereDate('date', Carbon::today())
            ->where('location_type', 'in_Clinic')
            ->where('status', 'confirmed')
            ->orderBy('start_time')
            ->first();

        if (!$firstInLine || $firstInLine->id !== $appointment->id) {
            return back()->with('error', 'ليس هذا دور هذا المريض بعد.');
        }

        $waitingEntry = WaitingList::where('appointment_id', $appointment->id)->first();

        if ($waitingEntry) {
            $waitingEntry->update([
                'status' => 'in_progress',
                'start_time' => now()
            ]);
        }

        $appointment->update([
            'location_type' => 'at_Doctor'
        ]);

        // 🔔 نادِ على المريض التالي (الرابع)
       app(\App\Services\ReminderService::class)->sendReminders();
     // 👈 استدعاء تابع التذكير

        return back()->with('success', 'تم إدخال المريض إلى غرفة المعاينة.');
    }
     */

}

