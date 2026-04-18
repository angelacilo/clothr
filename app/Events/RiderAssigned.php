<?php
namespace App\Events;

use App\Models\Delivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $delivery;

    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery->load(['order', 'rider.user']);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('rider.' . $this->delivery->rider_id);
    }

    public function broadcastWith()
    {
        return [
            'delivery' => [
                'id' => $this->delivery->id,
                'order_id' => $this->delivery->order_id,
                'status' => $this->delivery->status,
                'order' => [
                    'id' => $this->delivery->order->id,
                    'customer_name' => $this->delivery->order->user->name ?? 'Guest',
                    'total' => $this->delivery->order->total,
                ],
            ],
            'message' => 'A new delivery has been assigned to you!',
        ];
    }
}
