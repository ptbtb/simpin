<?php

namespace App\Listeners;

use App\Events\Pinjaman\PinjamanCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Managers\AngsuranManager;

class AngsuranListener
{
    public function onPinjamanCreated($event)
    {
        AngsuranManager::generateAngsuran($event->pinjaman);
    }
}
