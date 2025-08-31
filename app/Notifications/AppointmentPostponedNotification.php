<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AppointmentPostponedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    public $appointment;

    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'database',
            FcmChannel::class,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }


    public function toFcm($notifiable)
    {
        return new FcmMessage(
            notification: new FcmNotification(
                title: 'تم تأجيل الموعد',
                body: 'تم تأجيل موعدك بتاريخ ' . $this->appointment->date . 'تم تأجيل الموعد مع الطبيب ' . $this->appointment->doctor->user->name,

            ),
            data: [
                'appointment_id' => (string) $this->appointment->id,
                'action' => 'appointment_cancelled',
            ],
            custom: [
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                        'sound' => 'default',
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
            ]
        );
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'تم تأجيل الموعد مع الطبيب ' . $this->appointment->doctor->user->name,
            'title' => 'تم تأجيل الموعد',
            'body' => 'تم تأجيل موعدك بتاريخ ' . $this->appointment->date,
            'appointment_id' => $this->appointment->id,
            'start_time' => $this->appointment->start_time,
            'end_time' => $this->appointment->end_time,

        ];
    }

}
