<?php

namespace App\Managers;

use App\Models\JenisSimpanan;
use App\Models\Jurnal;
use App\Models\Penarikan;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use DB;
use Auth;

class PenarikanManager
{
    /**
     * get serial number on penarikan table.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public static function getSerialNumber($date)
    {
        try {
            $nextSerialNumber = 1;

            // get date
            $date = Carbon::createFromFormat('d-m-Y', $date);
            $year = $date->year;
            $month = $date->month;

            // get penarikan data on this year
            $lastPenarikan = Penarikan::whereYear('tgl_transaksi', '=', $year)
                ->wheremonth('tgl_transaksi', '=', $month)
                ->orderBy('serial_number', 'desc')
                ->first();
            if ($lastPenarikan) {
                $nextSerialNumber = $lastPenarikan->serial_number + 1;
            }

            return $nextSerialNumber;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }
    }

    static public function getListPenarikanSaldoAwal($id,$year=null){
        $result = [];
        $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc')->pluck('kode_jenis_simpan');
        if($year){
            $tgl= Carbon::createFromFormat('Y',$year)->startOfYear()->format('Y-m-d');
            return Jurnal::where('anggota',$id)
                ->wherein('akun_debet',$jenisSimpanan)
                ->where('tgl_transaksi','<',$tgl)
                ->groupBy('akun_debet')
                ->selectRaw("sum(debet) as debet,akun_debet")
                ;
//                ->wherein('id_tipe_jurnal',[4]);
        }
        return Jurnal::where('anggota',$id)
            ->wherein('akun_debet',$jenisSimpanan)
            ->wherein('id_tipe_jurnal',[4]);

    }
    static public function getListPenarikan($id,$from,$to){
        $result = [];
        $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc')->pluck('kode_jenis_simpan');

        return Jurnal::where('anggota',$id)
            ->wherein('akun_debet',$jenisSimpanan)
            ->wherenotin('id_tipe_jurnal',[4])
            ->whereBetween('tgl_transaksi',[$from,$to])
            ;


    }

    static public function getTotalPenarikan($id=null,$kode=null,$tgl=null){
        $result = [];
        $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc')->pluck('kode_jenis_simpan');
        $tar= Jurnal::wherein('akun_debet',$jenisSimpanan);
        if($id){
            $tar=  $tar->where('anggota',$id);
        }
        if($kode){
            $tar=  $tar->where('akun_debet',$kode);
        }
        if($tgl){
            $tar=  $tar->where('tgl_transaksi','<=',$tgl);
        }

        return $tar->sum('debet');

    }

}

?>
