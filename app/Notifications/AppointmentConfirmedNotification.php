<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
//use Illuminate\Notifications\Messages\FcmMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;


class AppointmentConfirmedNotification extends Notification
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
        /*  return (new MailMessage)
             ->line('The introduction to the notification.')
             ->action('Notification Action', url('/'))
             ->line('Thank you for using our application!'); */
        return (new MailMessage)
            ->greeting('Hello Admin')
            ->subject('New Booking Created: ' . $this->appointment['name'])
            ->line('**Appointment Details:**')  // make content strong
            ->line('Name: ' . $this->appointment['name'])
            ->line('Email: ' . $this->appointment['email'])
            ->line('Phone: ' . $this->appointment['phone'])
            // ->line('Category: '. $this->appointment->service->category['title'])
            ->line('Service: ' . $this->appointment->service['title'])
            ->line('Amount: ' . $this->appointment['amount'])
            ->line('Appointment Date : ' . Carbon::parse($this->appointment['booking_date'])->format('d M Y'))
            ->line('Slot Time: ' . $this->appointment['booking_time'])
            ->line('Thank you for using our application !');
    }


     public function toFcm($notifiable)
    {
        return new FcmMessage(
            notification: new FcmNotification(
                title: 'تم تأكيد الموعد',
                body: 'تم تأكيد موعدك مع الطبيب ' . $this->appointment->date,
            ),
            data: [
                'appointment_id' => (string) $this->appointment->id,
                'action' => 'appointment_confirmed',
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


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'تم تأكيد الموعد مع الطبيب ' . $this->appointment->doctor->user->name,
            'title' => 'تم تأكيد الموعد',
            'body' => 'تم تأكيد موعدك بتاريخ ' . $this->appointment->date,
            'appointment_id' => $this->appointment->id,
            'start_time' => $this->appointment->start_time,
            'end_time' => $this->appointment->end_time,
        ];
    }
}
