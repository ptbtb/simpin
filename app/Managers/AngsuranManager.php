<?php
namespace App\Managers;

use App\Models\Angsuran;
use App\Models\Pinjaman;

use Carbon\Carbon;

class AngsuranManager 
{
    static function generateAngsuran(Pinjaman $pinjaman)
    {
        try
        {
            $sisaPinjaman = $pinjaman->besar_pinjam;
            for ($i=1; $i <= $pinjaman->lama_angsuran; $i++)
            { 
                // get next serial number
                $nextSerialNumber = self::getSerialNumber(Carbon::now()->format('d-m-Y'));

                $jatuhTempo = $pinjaman->tgl_entri->addMonths($i)->endOfMonth();
                $sisaPinjaman = $sisaPinjaman-$pinjaman->besar_angsuran_pokok;
                $angsuran = new Angsuran();
                $angsuran->kode_pinjam = $pinjaman->kode_pinjam;
                $angsuran->angsuran_ke = $i;
                $angsuran->besar_angsuran = $pinjaman->besar_angsuran_pokok;
                $angsuran->denda = 0;
                $angsuran->jasa = $pinjaman->biaya_jasa;
                $angsuran->kode_anggota = $pinjaman->kode_anggota;
                $angsuran->sisa_pinjam = $sisaPinjaman;
                $angsuran->tgl_entri = Carbon::now();
                $angsuran->jatuh_tempo = $jatuhTempo;
                $angsuran->u_entry = 'Administrator';
                $angsuran->serial_number = $nextSerialNumber;
                $angsuran->save();
            }
        }
        catch (\Exception $e)
        {
            \Log::info($e);
        }
    }

    /**
     * get serial number on angsuran table.
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

            // get angsuran data on this year
            $lastAngsuran = Angsuran::whereYear('tgl_entri', '=', $year)
                                        ->orderBy('serial_number', 'desc')
                                        ->first();
            if($lastAngsuran)
            {
                $nextSerialNumber = $lastAngsuran->serial_number + 1;
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