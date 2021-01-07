<?php

namespace App\Listeners;

use App\Events\Anggota\AnggotaCreated;
use App\Managers\UserManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserListener
{
   public function onAnggotaCreated($event)
   {
       # code...
       UserManager::createUser($event->anggota, $event->password);
   }
}
