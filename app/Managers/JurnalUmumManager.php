<?php

namespace App\Services;

use App\Models\JurnalUmum;
use App\Models\JurnalUmumItem;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use DB;
use Auth;

class JurnalUmumManager
{
    /**
     * get serial number on jurnal umum table.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public static function getSerialNumber($date)
    {        
        try
        {
            $nextSerialNumber = 1;

            // get date
            $date = Carbon::createFromFormat('d-m-Y', $date);
            $year = $date->year;

            // get jurnal umum data on this year
            $lastJurnalUmum = JurnalUmum::whereYear('tgl_transaksi', '=', $year)
                                                ->orderBy('serial_number', 'desc')
                                                ->first();
            if($lastJurnalUmum)
            {
                $nextSerialNumber = $lastJurnalUmum->serial_number + 1;
            }

            return $nextSerialNumber;
        }
        catch(\Exception $e)
        {
            \Log::info($e->getMessage());
            return false;
        }
    }

}

?>