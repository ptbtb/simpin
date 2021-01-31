<?php

namespace App\Listeners;

use App\Managers\PinjamanManager;

class PengajuanTopupListener
{
    public function onPengajuanApproved($event)
    {
        $pengajuanTopup = $event->pengajuan->pengajuanTopup;
        if ($pengajuanTopup->count())
        {
            foreach ($pengajuanTopup as $topup)
            {
                $pinjaman = $topup->pinjaman;
                PinjamanManager::pembayaranPinjamanDipercepat($pinjaman);
            }
        }
    }
}
