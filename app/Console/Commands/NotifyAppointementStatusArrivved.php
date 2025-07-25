<?php

namespace App\Console\Commands;

use App\Services\Secertary\Notification\AppointementStatusArrivvedNotificationService;
use Illuminate\Console\Command;

class NotifyAppointementStatusArrivved extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments-get-ready-to-arrive:notify-appointment-status-arrived';
    protected $description = 'Send arrival reminders based on patient arrival time';


    /**
     * Execute the console command.
     */
    public function handle(AppointementStatusArrivvedNotificationService $service)
    {
        $service->sendReminders();
        $this->info('Reminder notifications checked for appointement-status-arrivved.');
    }
}
