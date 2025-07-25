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

        $appointments = Appointment::where('status', 'confirmed')
            ->whereDate('date', $now->toDateString())
            ->where('location_type', 'in_Home')
            ->whereNotNull('arrivved_time')
            ->with('patient.user') // تأكد من eager loading للمريض
            ->get()
            ->groupBy('doctor_id'); // تجميع حسب الطبيب

        foreach ($appointments as $doctorAppointments) {
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

                // ⏱ تحقق من وقت الإرسال + التكرار
                if ($now->format('Y-m-d H:i') === $sendTime->format('Y-m-d H:i') && !Cache::has($cacheKey)) {


                    //إرسال إشعار للمريض
                    $appointment->patient->user->notify(new AppointementStatusArrivvedNotification($appointment));
                    // إرسال إشعار Firebase إذا كان هناك token
                    if ($appointment->patient->user->fcm_token) {
                        $success = $this->sendFirebaseNotification(
                            $appointment->patient->user->fcm_token,
                            'تذكير بالموعد - تهيئة للوصول',
                            '🔔 لديك موعد اليوم الساعة ' . $startTime->format('g:i A') . '، الرجاء التوجه للعيادة في الوقت المناسب.'
                        );
                        if ($success) {
                            \Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                        } else {
                            \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                        }

                    } else {
                        Log::warning("لا يوجد FCM token للمستخدم ID={$appointment->patient->user->id}");
                    }
                    $appointment->update(['location_type' => 'on_Street']);
                    // ✅ تخزين أن الإشعار أُرسل بالفعل لمدة 90 دقيقة مثلاً
                    Cache::put($cacheKey, true, now()->addMinutes(90));

                }
            }
        }

    }
    public function sendFirebaseNotification($token, $title, $body)
    {
        try {
            $messaging = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials_file'))
                ->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData([
                    'type' => 'appointment_reminder',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]);

            $messaging->send($message);
            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase Notification Error: ' . $e->getMessage());
            return false;
        }


    }

}

