<?php

namespace App\Providers;

use App\Events\BillPayedEvent;
use App\Events\BillRefundedEvent;
use App\Listeners\BillPayedListener;
use App\Listeners\BillRefundedHandleListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        BillPayedEvent::class => [
            BillPayedListener::class,
        ],
        BillRefundedEvent::class => [
            BillRefundedHandleListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
