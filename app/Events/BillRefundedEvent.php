<?php

namespace App\Events;

use App\Models\Pay\MultiRefundBill;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillRefundedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $refundBill;

    /**
     * Create a new event instance.
     *
     * @param MultiRefundBill $refundBill
     */
    public function __construct(MultiRefundBill $refundBill)
    {
        $this->refundBill = $refundBill;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('BillRefunded' . $this->refundBill->id);
    }
}
