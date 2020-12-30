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
                $angsuran->save();
            }
        }
        catch (\Exception $e)
        {
            \Log::info($e);
        }
    }
}