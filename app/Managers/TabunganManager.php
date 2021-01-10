<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\Penarikan;
use App\Models\Tabungan;
use App\Models\JenisSimpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    static function updateSaldoTahunan()
    {
        ini_set('max_execution_time', 300000);
        $anggotas = Anggota::has('tabungan')
                            // ->whereIn('kode_anggota',[9996])
                            // ->take(100)
                            ->get();

        foreach($anggotas as $anggota)
        {                            
            $lastYear = Carbon::now()->year-1;
            $listTabungan = $anggota->tabungan->filter(function ($value) use ($lastYear)
            {
                return $value->batch < $lastYear || $value->batch == null;
            });
            
            if ($listTabungan->count())
            {
                foreach ($listTabungan as $tabungan)
                {
                    $jenisSimpanan = JenisSimpanan::find($tabungan->kode_trans);
                    $simpananTabungan = $anggota->listSimpanan
                                                // ->where('kode_jenis_simpan', $tabungan->kode_trans)
                                                ->filter(function ($simpanan) use ($tabungan, $lastYear)
                                                {
                                                    return $simpanan->kode_jenis_simpan == $tabungan->kode_trans && $simpanan->tgl_entri->year == $lastYear;
                                                })
                                                ->sum('besar_simpanan');

                    $tabungan->besar_tabungan = $tabungan->besar_tabungan + $simpananTabungan;
                    $tabungan->deskripsi = $jenisSimpanan->nama_simpanan.' DES '.$lastYear;
                    $tabungan->batch = $lastYear;
                    $tabungan->save();
                }
            }
        }
        echo 'sukses';
    }
}