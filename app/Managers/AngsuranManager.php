<?php
namespace App\Managers;

use App\Models\Angsuran;
use App\Models\Pinjaman;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $month = $date->month;

            // get angsuran data on this year
            $lastAngsuran = Angsuran::whereYear('tgl_transaksi', '=', $year)
                                        ->wheremonth('tgl_transaksi', '=', $month)
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

    static function syncAngsuran(Pinjaman $pinjaman)
    {
        try
        {
            if ($pinjaman->listAngsuran->count()){
              foreach ($pinjaman->listAngsuran as $angsuran) {
                // dd($pinjaman->tgl_transaksi);
                $angsuran->jatuh_tempo = Carbon::createFromFormat('Y-m-d',$pinjaman->tgl_transaksi)->addMonths($angsuran->angsuran_ke)->format('Y-m-d');
                $angsuran->save();
              }
            }
        }
        catch (\Exception $e)
        {
            \Log::info($e);
        }
    }

    static function createAngsuran(Pinjaman $pinjaman, $request)
    {
        $angsuran = new Angsuran();
        $angsuran->kode_pinjam = $pinjaman->kode_pinjam;
        // $angsuran->angsuran_ke = $pinjaman->angsuran_sekarang;
        $angsuranmaxKe  = DB::table('t_angsur')
                        ->selectraw("max(angsuran_ke) as maxke ")
                        ->where('kode_pinjam', $pinjaman->kode_pinjam)->get();
        $angsuranKe = $angsuranmaxKe[0]->maxke + 1;
        $angsuran->angsuran_ke = $angsuranKe;
        $angsuran->besar_angsuran = $pinjaman->besar_angsuran_pokok;
        $angsuran->u_entry = Auth::user()->name;
        $angsuran->tgl_entri = Carbon::now();
        // $angsuran->denda = 0;
        $angsuran->jasa = $pinjaman->biaya_jasa;
        $angsuran->kode_anggota = $pinjaman->kode_anggota;
        $angsuran->jatuh_tempo = $pinjaman->tagihan_bulan;
        $angsuran->paid_at = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
        $angsuran->save();

        return $angsuran;
    }
}
