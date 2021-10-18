<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\JurnalTemp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MigrationManager 
{
    

    /**
     * get serial number on simpanan table.
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

            // get simpanan data on this year
            $lastSerial = JurnalTemp::whereYear('tgl_posting', '=', $year)
                                        ->orderBy('serial_number', 'desc')
                                        ->first();
            if($lastSerial)
            {
                $nextSerialNumber = $lastSerial->serial_number + 1;
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