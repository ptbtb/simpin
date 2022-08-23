<?php

namespace App\Managers;

use App\Models\KodeTransaksi;
use Carbon\Carbon;

class NeracaManager
{
    public static function getNeraca($date)
    {
        if(!$date)
        {
            $date = Carbon::today()->format('Y-m-d');
        }

        $jurnalCode = KodeTransaksi::where('is_parent',0)
            ->wherein('code_type_id',[1,2])
            ->orderby('code_type_id','asc')
            ->orderby('CODE','asc')
            ->get();
//        dd($jurnalCode);
        $result = $jurnalCode->map(function($code,$key)use($date){
            return [
                'CODE'=>$code->CODE,
                'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                'saldo'=>$code->jurnalAmount($date),
                'code_type_id'=>$code->code_type_id,
                'Kategori'=>$code->codeCategory->name,

            ];
        });
//        dd($result);

        $data['list'] = $result;
          dd($data);

        return $data;


    }
}
