<?php

namespace App\Events\Penarikan;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Penarikan;
use App\Models\Tabungan;

class PenarikanCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $penarikan, $tabungan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Penarikan $penarikan, Tabungan $tabungan)
    {
        $this->penarikan = $penarikan;
        $this->tabungan = $tabungan;
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
