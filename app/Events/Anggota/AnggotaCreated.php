<?php

namespace App\Events\Anggota;

use App\Models\Anggota;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnggotaCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $anggota;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Anggota $anggota)
    {
        $this->anggota = $anggota;
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
