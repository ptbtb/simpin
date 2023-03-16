<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\Jurnal;
use App\Models\Penarikan;
use App\Models\Tabungan;
use App\Models\JenisSimpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TabunganManager
{
    static function updateSaldo(Penarikan $penarikan, Tabungan $tabungan)
    {
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

    static public function getSaldoTabungan($id,$tgl){
        $result = [];
        $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc')->pluck('kode_jenis_simpan');

        foreach ($jenisSimpanan as $key=> $val){
//            dd($key);
            $saldo = static::getSimpanan($id,$val,$tgl) -  static::getTarikan($id,$val,$tgl);
            $result[$key]=collect();
            $result[$key]->kode_trans=$val;
            $result[$key]->besar_tabungan=$saldo;
            $result[$key]->nama_simpanan=JenisSimpanan::where('kode_jenis_simpan',$val)->first()->nama_simpanan;

        }
       return $result;


    }
    static public function getSimpanan($id,$kode,$tgl){

        $simpanan = Jurnal::where('anggota',$id)
            ->where('akun_kredit',$kode)
            ->get();
        return $simpanan->sum('kredit');

    }
    static public function getTarikan($id,$kode,$tgl){

        $simpanan = Jurnal::where('anggota',$id)
            ->where('akun_debet',$kode)
            ->get();
        return $simpanan->sum('debet');

    }
}
