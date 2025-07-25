<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $listen = [
    \App\Events\AppointmentConfirmed::class => [
        \App\Listeners\SendConfirmedNotification::class,
    ],
    \App\Events\AppointmentCanceled::class => [
        \App\Listeners\SendCanceledNotification::class,
    ],
];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
