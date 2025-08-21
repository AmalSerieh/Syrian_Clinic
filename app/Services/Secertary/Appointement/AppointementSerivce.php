<?php

namespace App\Services\Secertary\Appointement;
use App\Notifications\AppointmentConfirmedNotification;
use App\Notifications\AppointmentCancelledNotification;
use App\Repositories\Secertary\AppointmentRepositoryInterface;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class AppointementSerivce
{
    protected $repo;

    public function __construct(AppointmentRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $serviceAccountPath = storage_path('secrets/service-account.json');

    }

    public function getPendingAppointmentsByDoctor($doctorId)
    {
        $this->repo->deleteOldPendingAppointments();
        return $this->repo->getPendingByDoctor($doctorId);
    }

    public function confirmAppointment3($appointmentId)
    {
        // تحديث الحالة وحفظها أولا
        $appointment = $this->repo->updateStatus($appointmentId, 'confirmed');

        $notificationSent = false;
        $notificationError = null;
        $user = $appointment->patient->user ?? null;

        if ($user) {
            try {
                // إرسال إشعار عبر Notification database
                $user->notify(new AppointmentConfirmedNotification($appointment));

                if (!empty($user->fcm_token)) {
                    // محاولة إرسال الإشعار عبر Firebase بداخل try-catch مستقل
                    $notificationSent = $this->sendFirebaseNotification(
                        $user->fcm_token,
                        'تم تأكيد الموعد',
                        'تم تأكيد موعدك بتاريخ ' . $appointment->date
                    );
                    if ($notificationSent) {
                        \Log::info("تم إرسال إشعار FCM للمستخدم ID={$user->id}");
                    } else {
                        \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$user->id}");
                    }
                } else {
                    \Log::info("لا يوجد FCM token للمستخدم ID={$user->id}، تم حفظ الإشعار في قاعدة البيانات فقط");
                }
            } catch (\Exception $e) {
                // التقاط الخطأ وعدم رفعه لمنع rollback تحديث الحالة
                $notificationError = $e->getMessage();
                \Log::error("خطأ في إرسال إشعار FCM: " . $notificationError);
            }
        } else {
            \Log::warning("لا يوجد مريض مرتبط بالموعد ID={$appointment->id}");
        }

        // إعادة تحميل الموديل بعد التحديث
        $appointment->refresh();

        return [
            'appointment' => $appointment,
            'notification_sent' => $notificationSent,
            'notification_error' => $notificationError,
            'has_token' => !empty($user->fcm_token ?? null)
        ];
    }

    public function confirmAppointment($appointmentId)
    {//dd($appointmentId);
        $appointment = $this->repo->updateStatus($appointmentId, 'confirmed');

        $notificationSent = false;

        // إرسال الإشعار للمريض فقط (fcm_token موجود)
        if ($appointment->patient && $appointment->patient->user) {
            $user = $appointment->patient->user;
            $user->notify(new AppointmentConfirmedNotification($appointment));

            // إرسال إشعار Firebase فقط إذا كان هناك token
            if (!empty($user->fcm_token)) {
                $success = $this->sendFirebaseNotification(
                    $appointment->patient->user->fcm_token,
                    'تم تأكيد الموعد',
                    'تم تأكيد موعدك بتاريخ ' . $appointment->date
                );
                if ($success) {
                    $notificationSent = true;
                    \Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                } else {
                    \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                }
            } else {
                \Log::info("لا يوجد FCM token للمستخدم ID={$user->id}، تم حفظ الإشعار في قاعدة البيانات فقط");
            }
        } else {
            \Log::warning("لا يوجد مريض مرتبط بالموعد ID={$appointment->id}");


        }

        $appointment->refresh();
        return [
            'appointment' => $appointment,
            'notification_sent' => $notificationSent,
            'has_token' => !empty($user->fcm_token ?? null)
        ];
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
                ->withData(['type' => 'appointment_update']);

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


    public function cancelAppointment($appointmentId)
    {
        $appointment = $this->repo->updateStatus($appointmentId, 'canceled_by_secretary');
$result = [
        'has_token' => false,
        'notification_sent' => false
    ];
        if ($appointment->patient && $appointment->patient->user) {
            $user = $appointment->patient->user;
           // dd($user->fcm_token);
            $user->notify(new AppointmentCancelledNotification($appointment));
            // إرسال إشعار Firebase فقط إذا كان هناك token
            if (!empty($user->fcm_token)) {
                $result['has_token'] = true;
                $success = $this->sendFirebaseNotification(
                    $appointment->patient->user->fcm_token,
                    'تم إلغاء الموعد',
                    'تم إلغاء موعدك بتاريخ ' . $appointment->date
                );
                if ($success) {
                    $result['notification_sent'] = $success;
                    \Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                } else {
                    \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
                }
            } else {
                \Log::info("لا يوجد FCM token للمستخدم ID={$user->id}، تم حفظ الإشعار في قاعدة البيانات فقط");
            }
        } else {
            \Log::warning("لا يوجد مريض مرتبط بالموعد ID={$appointment->id}");
        }
        return $result;
    }

    public function getTodayAppointments()
    {
        return $this->repo->fetchConfirmedAppointmentsToday();
    }


}
