<?php

namespace App\Listeners;

use App\Events\User\UserCreated;
use App\Managers\MailManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmailListener
{
    public function onUserCreated($event)
    {
        MailManager::sendEmailRegistrationCompleted($event->user, $event->password);
    }

    public function onPengajuanCreated($event)
    {
        MailManager::sendEmailApprovalPengajuanPinjaman($event->pengajuan);
    }

    public function onPenarikanCreated($event)
    {
        MailManager::sendEmailApprovalPenarikan($event->penarikan);
    }

    public function onPengajuanUpdated($event)
    {
        MailManager::sendEmailUpdatePengajuanPinjaman($event->pengajuan);
    }

    public function onPenarikanUpdated($event)
    {
        MailManager::sendEmailUpdatePenarikan($event->penarikan);
    }

    public function onPengajuanApproved($event)
    {
        MailManager::sendEmailPengajuanPinjamanApproved($event->pengajuan);
    }

    public function onPenarikanApproved($event)
    {
        MailManager::sendEmailPenarikanApproved($event->penarikan);
    }

    public function onInvoiceCreated($event)
    {
        MailManager::sendEmailInvoiceCreated($event->invoice);
    }
}
