<?php

namespace App\Events;

use App\Models\Pay\MultiBill;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillPayedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $bill;

    /**
     * Create a new event instance.
     */
    public function __construct(MultiBill $bill)
    {
        $this->bill = $bill;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('BillPayed'.$this->bill->id);
    }
}
