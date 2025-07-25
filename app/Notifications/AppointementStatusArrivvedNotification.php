<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class AppointementStatusArrivvedNotification extends Notification
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
                title: 'تذكير بالموعد ل تهيئة نفسك من أجل الوصول في الوقت المحدد',
                body: '🔔 لديك موعد اليوم الساعة ' . \Carbon\Carbon::parse($this->appointment->start_time)->format('g:i A') . '، الرجاء التوجه للعيادة في الوقت المناسب.',
            ),
            data: [
                'appointment_id' => (string) $this->appointment->id,
                'action' => ' You must be on_Street' . $this->appointment->patient->user->name,

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

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'تذكير بالموعد ل تهيئة نفسك من أجل الوصول في الوقت المحدد',
            'title' => 'تذكير بالموعد ل تهيئة نفسك من أجل الوصول في الوقت المحدد',
            'body' => '🔔 لديك موعد اليوم الساعة ' . \Carbon\Carbon::parse($this->appointment->start_time)->format('g:i A') . '، الرجاء التوجه للعيادة في الوقت المناسب.',
            'appointment_id' => $this->appointment->id,
            'start_time' => $this->appointment->start_time,
            'end_time' => $this->appointment->end_time,
            'date' => $this->appointment->date,
            'action' => ' You must be on_Street ' . $this->appointment->patient->user->name,

        ];
    }

}

