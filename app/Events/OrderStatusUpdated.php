<?php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $message;

    public function __construct(Order $order, $message = '')
    {
        $this->order = $order;
        $this->message = $message ?: "Order #{$order->id} status updated to " . ucfirst($order->status);
    }

    public function broadcastOn()
    {
        $channels = [
            new PrivateChannel('admin'),
            new PrivateChannel('user.' . $this->order->user_id),
        ];

        if ($this->order->courier_service) {
            $channels[] = new PrivateChannel('courier.' . $this->order->courier_service);
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'status' => $this->order->status,
                'total' => $this->order->total,
                'tracking_number' => $this->order->tracking_number,
                'courier_name' => $this->order->courier_name,
                'updated_at' => $this->order->updated_at->toDateTimeString(),
            ],
            'message' => $this->message,
        ];
    }
}
