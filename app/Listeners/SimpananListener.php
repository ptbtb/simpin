<?php

namespace App\Listeners;

use App\Managers\SimpananManager;

class SimpananListener
{
    public function onPenarikanApproved($event)
    {
        SimpananManager::penarikanApproved($event->penarikan);
    }
}
