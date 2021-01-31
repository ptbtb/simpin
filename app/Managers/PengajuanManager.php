<?php
namespace App\Managers;

use App\Models\PengajuanTopup;

class PengajuanManager 
{
    public static function createPengajuanTopup($pengajuan, $listTopupPinjaman)
    {
        foreach ($listTopupPinjaman as $pinjaman)
        {
            $topup = new PengajuanTopup();
            $topup->kode_pengajuan = $pengajuan->kode_pengajuan;
            $topup->kode_pinjaman = $pinjaman->kode_pinjam;
            $topup->biaya_pelunasan_dipercepat = $pinjaman->totalBayarPelunasanDipercepat;
            $topup->save();
        }
    }
}