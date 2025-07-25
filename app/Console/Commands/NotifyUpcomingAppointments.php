<?php

namespace App\Console\Commands;

use App\Notifications\UpcomingAppointmentNotification;
use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class NotifyUpcomingAppointments extends Command
{

    protected $signature = 'appointments:remind';
    protected $description = 'Send reminders to patients 24 hours before appointment';


    /**
     * Execute the console command.
     */

    public function handle()
    {
        \Log::info('appointments:remind executed at ' . now());

        $from = Carbon::now()->addDay()->startOfHour();
        $to = (clone $from)->addHour();
        $appointments = Appointment::where('status', 'confirmed')
            ->whereDate('date', '=', Carbon::parse($from)->toDateString())
            ->whereRaw("TIMESTAMP(date, start_time) BETWEEN ? AND ?", [$from, $to])
            ->with(['patient.user']) // تأكد العلاقة patient->user موجودة
            ->get();

        foreach ($appointments as $appointment) {
            if ($appointment->patient && $appointment->patient->user && $appointment->patient->user->fcm_token) {
                Notification::send(
                    $appointment->patient->user,
                    new UpcomingAppointmentNotification($appointment)
                );
            }
        }

        $this->info('تم إرسال إشعارات للمرضى القادمين.' . $appointments->count());
    }

}
