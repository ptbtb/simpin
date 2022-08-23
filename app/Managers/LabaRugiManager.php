<?php

namespace App\Managers;

use App\Models\Jurnal;
use App\Models\KodeTransaksi;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use DB;
use Auth;

class LabaRugiManager
{
    public static function getLabaRugi($date)
    {
        if(!$date)
        {
            $date = Carbon::today()->format('Y-m-d');
        }

        $jurnalCode = KodeTransaksi::where('is_parent',0)
                        ->wherein('code_type_id',[3,4])
                        ->get();
        $result = $jurnalCode->map(function($code,$key)use($date){
            return [
                'saldo'=>$code->neracaAmount($date),
                'code_type_id'=>$code->code_type_id,

            ];
        });
//        dd($result);

        $data['list'] = $result;
        $data['pendapatan'] = $result->where('code_type_id',3)->sum('saldo');
        $data['beban'] = $result->where('code_type_id',4)->sum('saldo');


//        dd($data);

        return $data;


    }

    public static function getShuBerjalan($date){
        $shu = static::getLabaRugi($date);
//        dd($shu);
        return $shu['pendapatan']-$shu['beban'];
    }

    public static function getShuBerjalanSaldoAwal(){
        $shu = Jurnal::where('jurnalable_type','App\Models\SaldoAwal')
            ->where('akun_debet','607.01.101')
            ->orWhere('akun_kredit', '607.01.101')
            ->first();
        $saldo = $shu->kredit - $shu->debit;
        return $saldo;
    }

    public static function getShuditahan($date){

            $year = Carbon::createFromFormat('Y-m-d',$date)->format('Y');
            $yearawal='2021';
            $saldoawal=static::getShuBerjalanSaldoAwal();
            $saldoTahunJalan=0;
            $result = 0;
            foreach(range($yearawal, date($year) - 1) as $y) {
                $tgl = Carbon::createFromFormat('Y',$y)->endOfYear()->format('Y-m-d');
//                dd($tgl);
                $saldoTahunJalan += static::getShuBerjalan($tgl);
//                dd($saldoTahunJalan);
            }

            return $saldoawal+$saldoTahunJalan;


    }
}
