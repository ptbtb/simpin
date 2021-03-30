<?php

namespace App\Managers;

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

            // get penarikan data on this year
            $lastPenarikan = Penarikan::whereYear('tgl_ambil', '=', $year)
                                                ->orderBy('serial_number', 'desc')
                                                ->first();
            if($lastPenarikan)
            {
                $nextSerialNumber = $lastPenarikan->serial_number + 1;
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