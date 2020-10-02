<?php
namespace App\Managers;

use App\Models\Penarikan;

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
}