<?php
namespace App\Managers;

use App\Models\Anggota;
use Illuminate\Support\Facades\DB;

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
    static public function batalKeluarAnggota(Anggota $anggota)
    {
        $anggota->status = 'aktif';
        $anggota->save();

        $user = DB::select('select * from users where kode_anggota = '.$anggota->kode_anggota);
        if(count($user))
        {
            DB::table('users')
              ->where('kode_anggota', $anggota->kode_anggota)
              ->update(['deleted_at' => null]);
        }
    }
}
