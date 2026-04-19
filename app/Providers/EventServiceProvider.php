<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Lab completion → auto-bill
        \App\Events\LabTestCompleted::class => [
            \App\Listeners\HospitalBillingListener::class,
        ],
        // Radiology/Imaging completion → auto-bill
        \App\Events\RadiographyCompleted::class => [
            \App\Listeners\HospitalBillingListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
