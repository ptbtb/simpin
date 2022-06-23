<?php

namespace App\Listeners;

use App\Events\Pinjaman\PengajuanApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Managers\PinjamanManager;

class PinjamanListener
{
    public function onPengajuanApproved($event)
    {
        PinjamanManager::createPinjaman($event->pengajuan);
    }

    public function onPengajuanPaid($event)
    {
        PinjamanManager::updateTglPinjaman($event->pengajuan);
    }
}
