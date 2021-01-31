<?php

namespace App\Events\Penarikan;

use App\Models\Penarikan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PenarikanUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $penarikan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Penarikan $penarikan)
    {
        $this->penarikan = $penarikan;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
