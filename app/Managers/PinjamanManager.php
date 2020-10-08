<?php
namespace App\Managers;

use App\Events\Pinjaman\PinjamanCreated;

use App\Models\Pinjaman;
use App\Models\Pengajuan;

use Carbon\Carbon;

class PinjamanManager 
{
    static function createPinjaman(Pengajuan $pengajuan)
    {
        try
        {
            $jenisPinjaman = $pengajuan->jenisPinjaman;
            $angsuranPerbulan = $pengajuan->besar_pinjam/$jenisPinjaman->lama_angsuran;
            $bungaPerbulan = $angsuranPerbulan*$jenisPinjaman->bunga/100;
            $totalAngsuranBulan = $angsuranPerbulan+$bungaPerbulan;
           
            $pinjaman = new Pinjaman();
            $pinjaman->kode_anggota = $pengajuan->kode_anggota;
            $pinjaman->kode_jenis_pinjam = $pengajuan->kode_jenis_pinjam;
            $pinjaman->besar_pinjam = $pengajuan->besar_pinjam;
            $pinjaman->besar_angsuran = $totalAngsuranBulan;
            $pinjaman->lama_angsuran = $jenisPinjaman->lama_angsuran;
            $pinjaman->sisa_angsuran = 0;
            $pinjaman->u_entry = "Administrator";
            $pinjaman->tgl_entri = Carbon::now();
            $pinjaman->tgl_tempo = Carbon::now()->addMonths($jenisPinjaman->lama_angsuran);
            $pinjaman->status = "belum lunas";
            $pinjaman->save();
            event(new PinjamanCreated($pinjaman));
        }
        catch (\Exception $e)
        {
            \Log::info($e);
        }
    }
}