<?php

namespace App\Listeners;

use App\Events\BillPayedEvent;

class BillPayedHandleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BillPayedEvent $event)
    {
        if (method_exists($event->bill->billable, 'handlePied')) {
            $event->bill->billable->handlePied($event->bill);
        }
    }
}
