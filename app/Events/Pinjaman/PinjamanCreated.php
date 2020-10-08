<?php

namespace App\Events\Pinjaman;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Pinjaman;

class PinjamanCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pinjaman;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Pinjaman $pinjaman)
    {
        $this->pinjaman = $pinjaman;
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
