<?php

namespace App\Listeners;

class BillPayedListener
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
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        if (method_exists($event->bill->billable, 'handlePied')) {
            $event->bill->billable->handlePied($event->bill);
        }
    }
}
