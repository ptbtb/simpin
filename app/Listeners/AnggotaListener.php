<?php

namespace App\Listeners;

use App\Events\Penarikan\PenarikanApproved;
use App\Managers\AnggotaManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnggotaListener
{
    public function onPenarikanApproved($event)
    {
        $penarikan = $event->penarikan;
        if ($penarikan->is_exit_anggota)
        {
            $anggota = $penarikan->anggota;
            AnggotaManager::keluarAnggota($anggota);
        }
    }
}
