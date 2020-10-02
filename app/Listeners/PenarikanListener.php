<?php

namespace App\Listeners;

use App\Events\Penarikan\PenarikanCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Managers\PenarikanManager;
use App\Managers\TabunganManager;

class PenarikanListener
{
    public function onPenarikanCreated($event)
    {
        TabunganManager::updateSaldo($event->penarikan);
    }
}
