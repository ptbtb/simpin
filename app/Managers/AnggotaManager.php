<?php
namespace App\Managers;

use App\Models\Anggota;

class AnggotaManager
{
    static public function keluarAnggota(Anggota $anggota)
    {
        $anggota->status = 'keluar';
        $anggota->save();

        $user = $anggota->user;
        if ($user)
        {
            $user->delete();
        }
    }
}
