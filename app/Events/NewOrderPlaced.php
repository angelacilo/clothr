<?php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('user');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin');
    }

    public function broadcastWith()
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'customer' => $this->order->user->name ?? 'Guest',
                'total' => $this->order->total,
                'status' => $this->order->status,
                'created_at' => $this->order->created_at->toDateTimeString(),
            ],
            'message' => "A new order (#{$this->order->id}) has been placed!",
        ];
    }
}
