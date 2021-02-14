<?php

namespace App\Listeners;

use App\Events\Pinjaman\PinjamanCreated;
use App\Managers\NotificationManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationListener
{
    public function onApprovalPengajuanCreated($event)
    {
        NotificationManager::sendNotificationApprovalPengajuanPinjaman($event->pengajuan);
    }
    
    public function onPengajuanUpdated($event)
    {
        NotificationManager::sendNotificationUpdatePengajuanPinjaman($event->pengajuan);
    }
   
    public function onPengajuanApproved($event)
    {
        NotificationManager::sendNotificationPengajuanApproved($event->pengajuan);
    }

    public function onInvoiceCreated($event)
    {
        NotificationManager::sendNotificationInvoiceCreated($event->invoice);
    }
}
