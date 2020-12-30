<?php
namespace App\Managers;

use App\Events\Pinjaman\PinjamanCreated;
use App\Models\AsuransiPinjaman;
use App\Models\Pinjaman;
use App\Models\Pengajuan;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PinjamanManager 
{
    static function createPinjaman(Pengajuan $pengajuan)
    {
        try
        {
            $jenisPinjaman = $pengajuan->jenisPinjaman;
            $angsuranPerbulan = round($pengajuan->besar_pinjam/$jenisPinjaman->lama_angsuran,2);
            // $bungaPerbulan = $angsuranPerbulan*$jenisPinjaman->bunga/100;
            $jasaPerbulan = $pengajuan->besar_pinjam*$jenisPinjaman->kategoriJenisPinjaman->jasa/100;
            if ($pengajuan->besar_pinjam > 100000000 && $jenisPinjaman->lama_angsuran > 3 && $jenisPinjaman->isJangkaPendek())
            {
                $jasaPerbulan = $pengajuan->besar_pinjam*3/100;
            }
            $asuransiPinjaman = AsuransiPinjaman::where('lama_pinjaman', $jenisPinjaman->lama_angsuran)
                                                ->where('kategori_jenis_pinjaman_id', $jenisPinjaman->kategori_jenis_pinjaman_id)
                                                ->first();
            $asuransi = 0;
            if ($asuransiPinjaman)
            {
                $asuransi = $asuransiPinjaman->besar_asuransi/100;
            }
            $asuransi = round($pengajuan->besar_pinjam*$asuransi,2);
            $totalAngsuranBulan = $angsuranPerbulan+$jasaPerbulan;
            $provisi = 0;
            if ($jenisPinjaman->isDanaLain())
            {
                $provisi = 0.01;
            }
            $provisi = round($pengajuan->besar_pinjam * $provisi,2);
            $biayaAdministrasi = $jenisPinjaman->kategoriJenisPinjaman->biaya_admin;
            $jasaPerbulan = round($jasaPerbulan,2);
           
            $pinjaman = new Pinjaman();
            $kodeAnggota = $pengajuan->kode_anggota;
            $kodePinjaman = str_replace('.','',$jenisPinjaman->kode_jenis_pinjam).'-'.$kodeAnggota.'-'.Carbon::now()->format('dmYHis');
            $pinjaman->kode_pinjam = $kodePinjaman;
            $pinjaman->kode_pengajuan_pinjaman = $pengajuan->kode_pengajuan;
            $pinjaman->kode_anggota = $pengajuan->kode_anggota;
            $pinjaman->kode_jenis_pinjam = $pengajuan->kode_jenis_pinjam;
            $pinjaman->besar_pinjam = $pengajuan->besar_pinjam;
            $pinjaman->besar_angsuran = $totalAngsuranBulan;
            $pinjaman->besar_angsuran_pokok = $angsuranPerbulan;
            $pinjaman->lama_angsuran = $jenisPinjaman->lama_angsuran;
            $pinjaman->sisa_angsuran = $jenisPinjaman->lama_angsuran;
            $pinjaman->sisa_pinjaman = $pengajuan->besar_pinjam;
            $pinjaman->biaya_jasa = $jasaPerbulan;
            $pinjaman->biaya_asuransi = $asuransi;
            $pinjaman->biaya_provisi = $provisi;
            $pinjaman->biaya_administrasi = $biayaAdministrasi;
            $pinjaman->u_entry = Auth::user()->name;
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