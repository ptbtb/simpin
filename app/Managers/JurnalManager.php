<?php
namespace App\Managers;

use App\Models\Angsuran;
use App\Models\Jurnal;
use App\Models\Penarikan;
use App\Models\Pinjaman;
use App\Models\JurnalUmumItem;
use App\Models\JurnalUmum;
use App\Models\SaldoAwal;
use App\Models\Code;
use App\Models\Simpanan;
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
        if($penarikan->akunDebet)
        {
            $jurnal->akun_debet = $penarikan->akunDebet->CODE;
        }
        else
        {
            $jurnal->akun_debet = COA_BANK_MANDIRI;
        }
        $jurnal->debet = $penarikan->besar_ambil;
        $jurnal->keterangan = 'Pengambilan simpanan anggota '. ucwords(strtolower($penarikan->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;

        // save as polymorphic
        $penarikan->jurnals()->save($jurnal);
    }

    public static function createJurnalPinjaman(Pinjaman $pinjaman)
    {
        try
        {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
            $jurnal->akun_kredit = $pinjaman->kode_jenis_pinjam;
            $jurnal->kredit = $pinjaman->besar_pinjam;
            if($pinjaman->akunDebet)
            {
                $jurnal->akun_debet = $pinjaman->akunDebet->CODE;
            }
            else
            {
                $jurnal->akun_debet = COA_BANK_MANDIRI;
            }
            $jurnal->debet = $pinjaman->besar_pinjam;
            $jurnal->keterangan = 'Pinjaman '.strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            
            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);
        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
    }

    public static function createJurnalAngsuran(Angsuran $angsuran)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_debet = $angsuran->pinjaman->kode_jenis_pinjam;
        $jurnal->debet = $angsuran->besar_pembayaran;
        if($angsuran->akunKredit)
        {
            $jurnal->akun_kredit = $angsuran->akunKredit->CODE;
        }
        else
        {
            $jurnal->akun_kredit = COA_BANK_MANDIRI;
        }
        $jurnal->kredit = $angsuran->besar_pembayaran;
        $jurnal->keterangan = 'Pembayaran angsuran ke  '. strtolower($angsuran->angsuran_ke) .' anggota '. ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);
    }

    public static function createJurnalSimpanan(Simpanan $simpanan)
    {
        try
        {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
            $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
            $jurnal->akun_kredit = $simpanan->kode_jenis_simpan;
            $jurnal->kredit = $simpanan->besar_simpanan;
            if($simpanan->akunDebet)
            {
                $jurnal->akun_debet = $simpanan->akunDebet->CODE;
            }
            else
            {
                $jurnal->akun_debet = COA_BANK_MANDIRI;
            }
            $jurnal->debet = $simpanan->besar_simpanan;
            $jurnal->keterangan = 'Simpanan '.strtolower($simpanan->jenis_simpan) . ' anggota '. ucwords(strtolower($simpanan->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            
            // save as polymorphic
            $simpanan->jurnals()->save($jurnal);
        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
    }

    public static function createSaldoAwal(SaldoAwal $saldoAwal)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JSA;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);

        if ($saldoAwal->code->normal_balance_id == NORMAL_BALANCE_DEBET) 
        {
            $jurnal->akun_debet = $saldoAwal->code->CODE;
            $jurnal->debet = $saldoAwal->nominal;
            $jurnal->akun_kredit = 0;
            $jurnal->kredit = 0;
        }
        else if($saldoAwal->code->normal_balance_id == NORMAL_BALANCE_KREDIT)
        {
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->akun_kredit = $saldoAwal->code->CODE;
            $jurnal->kredit = $saldoAwal->nominal;
        }

        $jurnal->keterangan = 'Saldo Awal';
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;

        // save as polymorphic
        $saldoAwal->jurnals()->save($jurnal);
    }

    public static function updateSaldoAwal(SaldoAwal $saldoAwal)
    {
        // get jurnal data
        $jurnal = Jurnal::where('id_tipe_jurnal', TIPE_JURNAL_JSA);

        // cek updated code is debet/kredit
        $code = Code::find($saldoAwal->getOriginal()['code_id']);

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
                $jurnal->akun_debet = $saldoAwal->code->CODE;
                $jurnal->debet = $saldoAwal->nominal;
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
            }
            else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->akun_kredit = $saldoAwal->code->CODE;
                $jurnal->kredit = $saldoAwal->nominal;
            }

            $jurnal->updated_by = Auth::user()->id;

            // save as polymorphic
            $saldoAwal->jurnals()->save($jurnal);
        }
    }

    public static function createJurnalUmum(JurnalUmum $jurnalUmum)
    {
        $jurnalUmumItems = $jurnalUmum->jurnalUmumItems;

        foreach ($jurnalUmumItems as $key => $jurnalUmumItem) 
        {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JU;
            $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);

            if ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_DEBET) 
            {
                $jurnal->akun_debet = $jurnalUmumItem->code->CODE;
                $jurnal->debet = $jurnalUmumItem->nominal;
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
            }
            else if($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->akun_kredit = $jurnalUmumItem->code->CODE;
                $jurnal->kredit = $jurnalUmumItem->nominal;
            }

            $jurnal->keterangan = $jurnalUmum->deskripsi;
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;

            // save as polymorphic
            $jurnalUmum->jurnals()->save($jurnal);
        }
    }

    public static function updateJurnalUmum(JurnalUmum $jurnalUmum)
    {
        // delete old jurnal data
        $jurnalUmum->jurnals()->delete();

        $newJurnalUmum = JurnalUmum::find($jurnalUmum->id);

        $jurnalUmumItems = $newJurnalUmum->jurnalUmumItems;
        
        foreach ($jurnalUmumItems as $key => $jurnalUmumItem) 
        {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JU;
            $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);

            if ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_DEBET) 
            {
                $jurnal->akun_debet = $jurnalUmumItem->code->CODE;
                $jurnal->debet = $jurnalUmumItem->nominal;
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
            }
            else if($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->akun_kredit = $jurnalUmumItem->code->CODE;
                $jurnal->kredit = $jurnalUmumItem->nominal;
            }

            $jurnal->keterangan = $jurnalUmum->deskripsi;
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;

            // save as polymorphic
            $jurnalUmum->jurnals()->save($jurnal);
        }
    }
}