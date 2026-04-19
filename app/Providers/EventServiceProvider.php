<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\LabTestCompleted::class => [
            \App\Listeners\HospitalBillingListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
