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

    public function confirmAppointment($appointmentId)
    {
        $appointment = $this->repo->updateStatus($appointmentId, 'confirmed');
        // إرسال الإشعار للمريض فقط (fcm_token موجود)
        if ($appointment->patient && $appointment->patient->user->fcm_token) {
            $appointment->patient->user->notify(new AppointmentConfirmedNotification($appointment));
        }
        // إشعار للمريض عبر FCM + notification database
        // $appointment->patient->user->notify(new AppointmentConfirmedNotification($appointment));
        // إرسال إشعار Firebase
        $success=$this->sendFirebaseNotification(
            $appointment->patient->user->fcm_token,
            'تم تأكيد الموعد',
            'تم تأكيد موعدك بتاريخ ' . $appointment->date
        );
        if ($success) {
            \Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
        } else {
            \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
        }
        return $appointment;
    }
    public function sendFirebaseNotification($token, $title, $body)
    {
        try {
            $messaging = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials_file'))
                ->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create($title, $body))
                ->withData(['type' => 'appointment_update']);

            $messaging->send($message);
            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase Notification Error: ' . $e->getMessage());
            return false;
        }


    }

    public function cancelAppointment($appointmentId)
    {
        $appointment = $this->repo->updateStatus($appointmentId, 'canceled_by_secretary');

        if ($appointment->patient && $appointment->patient->user->fcm_token) {
            $appointment->patient->user->notify(new AppointmentCancelledNotification($appointment));
        }
        // إشعار للمريض

        $success = $this->sendFirebaseNotification(
            $appointment->patient->user->fcm_token,
            'تم إلغاء الموعد',
            'تم إلغاء موعدك بتاريخ ' . $appointment->date
        );

        if ($success) {
            \Log::info("تم إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
        } else {
            \Log::warning("فشل إرسال إشعار FCM للمستخدم ID={$appointment->patient->user->id}");
        }
        return $appointment;
    }

    public function getTodayAppointments()
    {
        return $this->repo->fetchConfirmedAppointmentsToday();
    }


}
