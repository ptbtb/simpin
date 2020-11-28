<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\Penarikan;
use App\Models\Tabungan;
use Carbon\Carbon;

class TabunganManager 
{
    static function updateSaldo(Penarikan $penarikan)
    {
        $tabungan = $penarikan->tabungan;
        $saldoAwal = $tabungan->besar_tabungan;
        $saldoAkhir = $saldoAwal - $penarikan->besar_ambil;
        $tabungan->besar_tabungan = $saldoAkhir;
        $tabungan->save();
    }

    static function createTabungan(Anggota $anggota)
    {
        $tabungan = new Tabungan();
        $tabungan->kode_tabungan = $anggota->kode_anggota;
        $tabungan->kode_anggota = $anggota->kode_anggota;
        $tabungan->tgl_mulai = Carbon::now();
        $tabungan->besar_tabungan = DEFAULT_BESAR_TABUNGAN;
        $tabungan->save();
    }
}