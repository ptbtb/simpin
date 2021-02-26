<?php
namespace App\Managers;

use App\Models\Angsuran;
use App\Models\Jurnal;
use App\Models\Penarikan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JurnalManager 
{
    public static function createJurnalPenarikan(Penarikan $penarikan)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_kredit = $penarikan->tabungan->kode_trans;
        $jurnal->kredit = $penarikan->besar_ambil;
        $jurnal->akun_debet = COA_BANK_MANDIRI;
        $jurnal->debet = $penarikan->besar_ambil;
        $jurnal->keterangan = 'Pengambilan simpanan anggota '. ucwords(strtolower($penarikan->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->save();
    }
    public static function createJurnalAngsuran(Angsuran $angsuran)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_debet = $angsuran->pinjaman->kode_jenis_pinjam;
        $jurnal->debet = $angsuran->besar_pembayaran;
        $jurnal->akun_kredit = COA_BANK_MANDIRI;
        $jurnal->kredit = $angsuran->besar_pembayaran;
        $jurnal->keterangan = 'Pembayaran angsuran ke  '. strtolower($angsuran->angsuran_ke) .' anggota '. ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->save();
    }
}