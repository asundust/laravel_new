<?php

namespace App\Providers;

use App\Events\BillPayedEvent;
use App\Events\BillRefundedEvent;
use App\Listeners\BillPayedHandleListener;
use App\Listeners\BillRefundedHandleListener;
use App\Listeners\WeChatUserAuthorizedHandleListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;

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
            BillPayedHandleListener::class,
        ],
        BillRefundedEvent::class => [
            BillRefundedHandleListener::class,
        ],
        WeChatUserAuthorized::class => [
            WeChatUserAuthorizedHandleListener::class,
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
