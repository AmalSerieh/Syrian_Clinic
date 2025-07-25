<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');//daily
Schedule::command('medications:update-progress')->hourly();
//تجديث حالة الدواء منهية الصلاحية فقط
//Schedule::command('medications:update-medication-status')->hourly();
//remaider
Schedule::command('appointments:remind')->hourly();
//arrivved
Schedule::command('appointments-get-ready-to-arrive:notify-appointment-status-arrived')->everyMinute();

/* protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        \App\Models\MedicationAlarm::whereDate('med_end_date', '<', now())->delete();
    })->daily();
} */


