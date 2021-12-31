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

use Illuminate\Support\Facades\Log;

class JurnalManager
{
    public static function createJurnalPenarikan(Penarikan $penarikan)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
        $jurnal->nomer = $penarikan->tgl_transaksi->format('Ymd').(Jurnal::count()+1);
        $jurnal->tgl_transaksi = $penarikan->tgl_transaksi;
        $jurnal->akun_debet = $penarikan->code_trans;
        $jurnal->debet = $penarikan->besar_ambil;
        if($penarikan->id_akun_debet)
        {
            $jurnal->akun_kredit = $penarikan->akunDebet->CODE;
        }
        else
        {
            $jurnal->akun_kredit = '102.18.000';
        }
        $jurnal->kredit = $penarikan->besar_ambil;
        $jurnal->keterangan = $penarikan->keterangan;
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;

        // save as polymorphic
        $penarikan->jurnals()->save($jurnal);
    }

    public static function createJurnalPinjaman(Pinjaman $pinjaman)
    {
        try
        {
            // jurnal pinjaman
            // jurnal untuk debet
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
            $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
            $jurnal->akun_debet = $pinjaman->kode_jenis_pinjam;
            $jurnal->debet = $pinjaman->besar_pinjam;
            $jurnal->akun_kredit = 0;
            $jurnal->kredit = 0;
            $jurnal->keterangan = 'Pinjaman '.strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;



            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);


            // jurnal untuk total credit bank
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
            $jurnal->tgl_transaksi = Carbon::parse( $pinjaman->tgl_transaksi);
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            if($pinjaman->akunKredit)
            {
                $jurnal->akun_kredit = $pinjaman->akunKredit->CODE;
            }
            else
            {
                $jurnal->akun_kredit = '102.18.000';
            }
            $jurnal->kredit = $pinjaman->besar_pinjam - $pinjaman->biaya_administrasi - $pinjaman->biaya_provisi - $pinjaman->biaya_asuransi;
            $jurnal->keterangan = 'Pinjaman '.strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;

            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);

            // jurnal untuk total provisi
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
            $jurnal->tgl_transaksi = Carbon::parse( $pinjaman->tgl_transaksi);
            $jurnal->akun_kredit = COA_JASA_PROVISI;
            $jurnal->kredit = $pinjaman->biaya_provisi;
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->keterangan = 'Pinjaman '.strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;

            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);

            // jurnal untuk total ASURANSI
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse( $pinjaman->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
            $jurnal->tgl_transaksi = Carbon::parse( $pinjaman->tgl_transaksi);
            $jurnal->akun_kredit = COA_UTIP_ASURANSI;
            $jurnal->kredit = $pinjaman->biaya_asuransi;
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->keterangan = 'Pinjaman '.strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;

            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);

            // jurnal untuk total ADMINISTRASI
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
            $jurnal->tgl_transaksi = Carbon::parse( $pinjaman->tgl_transaksi);
            $jurnal->akun_kredit = COA_JASA_ADMINISTRASI;
            $jurnal->kredit = $pinjaman->biaya_administrasi;
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
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
public static function createJurnalSaldoPinjaman(Pinjaman $pinjaman)
    {
        try
        {
            // jurnal pinjaman
            // jurnal untuk debet
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JSA;
            $jurnal->nomer = Carbon::createFromFormat('Y-m-d', $pinjaman->tgl_entri)->format('Ymd').(Jurnal::count()+1);
            $jurnal->akun_debet = $pinjaman->kode_jenis_pinjam;
            $jurnal->debet = $pinjaman->besar_pinjam;
            $jurnal->akun_kredit = 0;
            $jurnal->kredit = 0;
            $jurnal->keterangan = 'Pinjaman '.strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->tgl_transaksi =Carbon::now()->subYear()->endOfYear()->format('Ymd');



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
        // debet
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $angsuran->paid_at)->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_debet = $angsuran->pinjaman->kode_jenis_pinjam;

        // make balance
        $jasa = ($angsuran->besar_pembayaran>$angsuran->jasa)?$angsuran->jasa:$angsuran->besar_pembayaran;
        $angsur = ($angsuran->besar_pembayaran-$angsuran->jasa+2>$angsuran->besar_angsuran)?$angsuran->besar_angsuran:($angsuran->besar_pembayaran-$angsuran->jasa);

        //end balancing

        $jurnal->debet = ($angsur>0)?$angsur:0;
        $jurnal->akun_kredit = 0;
        $jurnal->kredit = 0;
        if (is_null($angsuran->keterangan) || $angsuran->keterangan==''){
            $jurnal->keterangan = 'Pembayaran angsuran ke  '. strtolower($angsuran->angsuran_ke) .' anggota '. ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        }else{
             $jurnal->keterangan = $angsuran->keterangan;
        }
       
        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
        $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);

        // kredit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        if($angsuran->akunKredit)
        {
            $jurnal->akun_kredit = $angsuran->akunKredit->CODE;
        }
        else
        {
            $jurnal->akun_kredit = COA_BANK_MANDIRI;
        }
        $jurnal->kredit = $angsuran->besar_pembayaran;
       if (is_null($angsuran->keterangan) || $angsuran->keterangan==''){
            $jurnal->keterangan = 'Pembayaran angsuran ke  '. strtolower($angsuran->angsuran_ke) .' anggota '. ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        }else{
             $jurnal->keterangan = $angsuran->keterangan;
        }
        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
         $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);

        // jurnal untuk JASA
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_kredit = 0;
        $jurnal->kredit = 0;
        // japen
        if($angsuran->pinjaman->kode_jenis_pinjam == '105.01.001')
        {
            $jurnal->akun_debet = '701.02.003';
        }
        // japan and others
        else
        {
            $jurnal->akun_debet = '701.02.001';
        }
        $jurnal->debet = $jasa;
        if (is_null($angsuran->keterangan) || $angsuran->keterangan==''){
            $jurnal->keterangan = 'Pembayaran angsuran ke  '. strtolower($angsuran->angsuran_ke) .' anggota '. ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        }else{
             $jurnal->keterangan = $angsuran->keterangan;
        }
        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
         $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);
    }

    public static function createJurnalSimpanan(Simpanan $simpanan)
    {
        try
        {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
            $jurnal->nomer = Carbon::parse($simpanan->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
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
            $jurnal->keterangan = $simpanan->keterangan;
            $jurnal->created_by = $simpanan->created_by;
            if($jurnal->updated_by){
                $jurnal->updated_by = $simpanan->updated_by;
            }
            
             $jurnal->tgl_transaksi = $simpanan->tgl_transaksi;

            // save as polymorphic
            $simpanan->jurnals()->save($jurnal);
        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
    }
public static function createJurnalSaldoSimpanan(Simpanan $simpanan)
    {
        try
        {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
            $jurnal->nomer = Carbon::createFromFormat('Y-m-d', $simpanan->tgl_entri)->format('Ymd').(Jurnal::count()+1);
            $jurnal->akun_kredit = $simpanan->kode_jenis_simpan;
            $jurnal->kredit = $simpanan->besar_simpanan;
            $jurnal->akun_debet=0;
            $jurnal->debet =0;
            $jurnal->keterangan = $simpanan->keterangan.' '. ucwords(strtolower($simpanan->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
             $jurnal->tgl_transaksi = $simpanan->tgl_transaksi;
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
        $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $saldoAwal->created_at)->format('Ymd').(Jurnal::count()+1);

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
        $jurnal->created_at = Carbon::today()->subYear()->endOfYear()->format('Y-m-d');
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
         $jurnal->tgl_transaksi =Carbon::now()->subYear()->endOfYear()->format('Ymd');


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
        }else{
            self::createSaldoAwal($saldoAwal);
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
             $jurnal->tgl_transaksi =$jurnalUmum->tgl_transaksi;

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
            $jurnal->nomer = Carbon::createFromFormat('Y-m-d', $jurnalUmum->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);

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
            $jurnal->tgl_transaksi =$jurnalUmum->tgl_transaksi;

            // save as polymorphic
            $jurnalUmum->jurnals()->save($jurnal);
        }
    }

    public static function createJurnalPelunasanDipercepat(Pinjaman $pinjaman){

        //kredit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        $jurnal->akun_kredit = $pinjaman->kode_jenis_pinjam;
        $jurnal->kredit = $pinjaman->sisa_pinjaman;
        $jurnal->keterangan = 'Pelunasan dipercepat Pinjaman anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->tgl_transaksi =$pinjaman->tgl_transaksi;

        // save as polymorphic
        $pinjaman->jurnals()->save($jurnal);

        // debit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_kredit = 0;
        $jurnal->kredit = 0;
        if($pinjaman->akunDebet)
        {
            $jurnal->akun_debet = $pinjaman->akunDebet->CODE;
        }
        else
        {
            $jurnal->akun_debet = COA_BANK_MANDIRI;
        }
        $jurnal->debet = $pinjaman->totalBayarPelunasanDipercepat;
        $jurnal->keterangan = 'Pelunasan dipercepat pinjaman   anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->tgl_transaksi =$pinjaman->tgl_transaksi;

        // save as polymorphic
        $pinjaman->jurnals()->save($jurnal);

        // jurnal untuk JASA
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd').(Jurnal::count()+1);
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        // japen
        if($pinjaman->kode_jenis_pinjam == '105.01.001')
        {
            $jurnal->akun_kredit = '701.02.003';
        }
        // japan and others
        else
        {
            $jurnal->akun_kredit = '701.02.001';
        }
        $jurnal->kredit = $pinjaman->jasaPelunasanDipercepat;
        $jurnal->keterangan = 'Pelunasan dipercepat pinjaman   anggota '. ucwords(strtolower($pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->tgl_transaksi =$pinjaman->tgl_transaksi;

        // save as polymorphic
        $pinjaman->jurnals()->save($jurnal);
    }
}
