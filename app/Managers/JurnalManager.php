<?php
namespace App\Managers;

use App\Models\Angsuran;
use App\Models\Jurnal;
use App\Models\Penarikan;
use App\Models\Pinjaman;
use App\Models\JurnalUmum;
use App\Models\Code;
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

    public static function createJurnalPinjaman(Pinjaman $pinjaman)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_kredit = $pinjaman->kode_jenis_pinjam;
        $jurnal->kredit = $pinjaman->besar_pinjam;
        $jurnal->akun_debet = COA_BANK_MANDIRI;
        $jurnal->debet = $pinjaman->besar_pinjam;
        $jurnal->keterangan = 'Pinjaman '. strtolower($pinjaman->jenisPinjaman->nama_pinjaman) .' anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
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

    public static function createJurnalUmum(JurnalUmum $jurnalUmum)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JU;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);

        if ($jurnalUmum->code->normal_balance_id == NORMAL_BALANCE_DEBET) 
        {
            $jurnal->akun_debet = $jurnalUmum->code->CODE;
            $jurnal->debet = $jurnalUmum->nominal;
            $jurnal->akun_kredit = 0;
            $jurnal->kredit = 0;
        }
        else if($jurnalUmum->code->normal_balance_id == NORMAL_BALANCE_KREDIT)
        {
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->akun_kredit = $jurnalUmum->code->CODE;
            $jurnal->kredit = $jurnalUmum->nominal;
        }

        $jurnal->keterangan = 'Jurnal Umum';
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->save();
    }

    public static function updateJurnalUmum(JurnalUmum $jurnalUmum)
    {
        // get jurnal data
        $jurnal = Jurnal::where('id_tipe_jurnal', TIPE_JURNAL_JU);

        // cek updated code is debet/kredit
        $code = Code::find($jurnalUmum->getOriginal()['code_id']);

        // if debet
        if ($code->normal_balance_id == NORMAL_BALANCE_DEBET) 
        {
            $jurnal = $jurnal->where('akun_debet', $code->CODE)->first();
        }
        // if kredit
        else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
        {
            $jurnal = $jurnal->where('akun_kredit', $code->CODE)->first();
        }
        // if jurnal exist
        if($jurnal)
        {
            if ($code->normal_balance_id == NORMAL_BALANCE_DEBET) 
            {
                $jurnal->akun_debet = $jurnalUmum->code->CODE;
                $jurnal->debet = $jurnalUmum->nominal;
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
            }
            else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->akun_kredit = $jurnalUmum->code->CODE;
                $jurnal->kredit = $jurnalUmum->nominal;
            }

            $jurnal->updated_by = Auth::user()->id;
            $jurnal->save();
        }
    }
}