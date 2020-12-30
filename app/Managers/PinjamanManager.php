<?php
namespace App\Managers;

use App\Events\Pinjaman\PinjamanCreated;
use App\Models\AsuransiPinjaman;
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
            // $bungaPerbulan = $angsuranPerbulan*$jenisPinjaman->bunga/100;
            $jasaPerbulan = $angsuranPerbulan*$jenisPinjaman->kategoriJenisPinjaman->jasa/100;
            if ($pengajuan->besar_pinjam > 100000000 && $jenisPinjaman->lama_angsuran > 3)
            {
                $jasaPerbulan = $angsuranPerbulan*3/100;
            }
            $asuransiPinjaman = AsuransiPinjaman::where('lama_pinjaman', $jenisPinjaman->lama_angsuran)
                                                ->where('kategori_jenis_pinjaman_id', $jenisPinjaman->kategori_jenis_pinjaman_id)
                                                ->first();
            $asuransi = 0;
            if ($asuransiPinjaman)
            {
                $asuransi = $asuransiPinjaman->besar_asuransi/100;
            }
            $asuransiPerbulan = $angsuranPerbulan*$asuransi;
            $totalAngsuranBulan = $angsuranPerbulan+$jasaPerbulan+$asuransiPerbulan;
           
            $pinjaman = new Pinjaman();
            $kodeAnggota = $pengajuan->kode_anggota;
            $kodePinjaman = str_replace('.','',$jenisPinjaman->kode_jenis_pinjam).'-'.$kodeAnggota.'-'.Carbon::now()->format('dmYHis');
            $pinjaman->kode_pinjam = $kodePinjaman;
            $pinjaman->kode_pengajuan_pinjaman = $pengajuan->kode_pengajuan;
            $pinjaman->kode_anggota = $pengajuan->kode_anggota;
            $pinjaman->kode_jenis_pinjam = $pengajuan->kode_jenis_pinjam;
            $pinjaman->besar_pinjam = $pengajuan->besar_pinjam;
            $pinjaman->besar_angsuran = $totalAngsuranBulan;
            $pinjaman->lama_angsuran = $jenisPinjaman->lama_angsuran;
            $pinjaman->sisa_angsuran = $jenisPinjaman->lama_angsuran;
            $pinjaman->sisa_pinjaman = $pengajuan->besar_pinjam;
            $pinjaman->u_entry = "Administrator";
            $pinjaman->tgl_entri = Carbon::now();
            $pinjaman->tgl_tempo = Carbon::now()->addMonths($jenisPinjaman->lama_angsuran);
            $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
            // dd($pinjaman);
            $pinjaman->save();
            event(new PinjamanCreated($pinjaman));
        }
        catch (\Exception $e)
        {
            \Log::info($e);
        }
    }
}