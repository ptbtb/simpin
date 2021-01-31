<?php
namespace App\Managers;

use App\Models\Penarikan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SimpananManager 
{
    static function penarikanApproved(Penarikan $penarikan)
    {
        try
        {
            $thisYear = Carbon::now()->year;
            $listSimpanan = Simpanan::where('kode_anggota', $penarikan->kode_anggota)
                                    ->whereYear('tgl_entri', $thisYear)
                                    ->where('kode_jenis_simpan',$penarikan->code_trans)
                                    ->get();
            
            $totalTarik = $penarikan->besar_ambil;
            foreach ($listSimpanan as $simpanan)
            {
                if ($simpanan->besar_simpanan > $totalTarik)
                {
                    $simpanan->besar_simpanan = $simpanan->besar_simpanan - $totalTarik;
                    $simpanan->save();
                    $totalTarik = 0;
                }
                else
                {
                    $totalTarik = $totalTarik - $simpanan->besar_simpanan;
                    $simpanan->besar_simpanan = 0;
                    $simpanan->save();
                }

                if ($totalTarik == 0)
                {
                    break;
                }
            }

            if ($totalTarik > 0)
            {
                $tabungan = $penarikan->tabungan;
                $tabungan->besar_tabungan = $tabungan->besar_tabungan - $totalTarik;
                $tabungan->save();
                $totalTarik = 0;
            }
        }
        catch (\Throwable $e)
        {
            Log::error($e);
        }
    }
}