<?php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageLostReported implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $reason;

    public function __construct(Order $order, $reason)
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin');
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'reason' => $this->reason,
            'courier' => $this->order->courier_name,
            'message' => "Package for Order #{$this->order->id} reported as LOST by {$this->order->courier_name}",
        ];
    }
}
