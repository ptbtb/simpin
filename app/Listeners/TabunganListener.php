<?php

namespace App\Listeners;

use App\Events\Anggota\AnggotaCreated;
use App\Managers\TabunganManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TabunganListener
{
    public function onAnggotaCreated($event)
    {
        TabunganManager::createTabungan($event->anggota);
    }
}
