<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\Penarikan;
use App\Models\Tabungan;
use App\Models\JenisSimpanan;
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
        $jenisSimpanan = JenisSimpanan::required()->get();
        foreach ($jenisSimpanan as $simpanan)
        {
            $id = $anggota->kode_anggota.str_replace('.','',$simpanan->kode_jenis_simpan);
            $tabungan = new Tabungan();
            $tabungan->id = $id;
            $tabungan->kode_tabungan = $anggota->kode_anggota;
            $tabungan->kode_anggota = $anggota->kode_anggota;
            $tabungan->tgl_mulai = Carbon::now();
            $tabungan->besar_tabungan = DEFAULT_BESAR_TABUNGAN;
            $tabungan->kode_trans = $simpanan->kode_jenis_simpan;
            $tabungan->save();
        }
    }
}