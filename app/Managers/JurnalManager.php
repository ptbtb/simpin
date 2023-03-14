<?php

namespace App\Managers;

use App\Models\Angsuran;
use App\Models\AngsuranPartial;
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
use DB;
use \App\Models\BukuBesarJurnal;

use Illuminate\Support\Facades\Log;

class JurnalManager
{
    public static function createJurnalPenarikan(Penarikan $penarikan)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
        $jurnal->nomer = $penarikan->tgl_transaksi->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->tgl_transaksi = $penarikan->tgl_transaksi;
        $jurnal->akun_debet = $penarikan->code_trans;
        $jurnal->debet = $penarikan->besar_ambil;
        if ($penarikan->id_akun_debet) {
            $jurnal->akun_kredit = $penarikan->akunDebet->CODE;
        } else {
            $jurnal->akun_kredit = '102.18.000';
        }
        $jurnal->kredit = $penarikan->besar_ambil;
        if ($penarikan->keterangan) {
            $jurnal->keterangan = $penarikan->keterangan;
        } else {
            $jurnal->keterangan = 'Penarikan ' . strtolower($penarikan->jenisSimpanan->nama_simpanan) . ' anggota ' . ucwords(strtolower($penarikan->anggota->nama_anggota));
        }

        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;

        // save as polymorphic
        $jurnal->trans_id = $penarikan->kode_ambil;
        $jurnal->anggota = $penarikan->kode_anggota;
        $penarikan->jurnals()->save($jurnal);
    }

    public static function createJurnalPinjaman(Pinjaman $pinjaman)
    {
        try {
            // jurnal pinjaman
            // jurnal untuk debet
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
            $jurnal->akun_debet = $pinjaman->kode_jenis_pinjam;
            $jurnal->debet = $pinjaman->besar_pinjam;
            $jurnal->akun_kredit = 0;
            $jurnal->kredit = 0;
            $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->trans_id = $pinjaman->kode_pinjam;
            $jurnal->anggota = $pinjaman->kode_anggota;


            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);


            // jurnal untuk total provisi
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::now()->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
            $jurnal->akun_kredit = COA_JASA_PROVISI;
            $jurnal->kredit = $pinjaman->biaya_provisi;
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->trans_id = $pinjaman->kode_pinjam;
            $jurnal->anggota = $pinjaman->kode_anggota;

            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);

            // jurnal untuk total ASURANSI
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
            $jurnal->akun_kredit = COA_UTIP_ASURANSI;
            $jurnal->kredit = $pinjaman->biaya_asuransi;
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->trans_id = $pinjaman->kode_pinjam;
            $jurnal->anggota = $pinjaman->kode_anggota;

            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);

            // jurnal untuk total ADMINISTRASI
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
            $jurnal->akun_kredit = COA_JASA_ADMINISTRASI;
            $jurnal->kredit = $pinjaman->biaya_administrasi;
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->trans_id = $pinjaman->kode_pinjam;
            $jurnal->anggota = $pinjaman->kode_anggota;

            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);

            $simpanan_pagu = 0;
            $jasa_topup = 0;
            $sisa_pinjaman = 0;
            // jurnal untuk topup
            if ($pinjaman->pengajuan->pengajuanTopup->count()) {
                $coa = COA_JASA_TOP_UP_PINJ_JANGKA_PANJANG;
                if ($pinjaman->jenisPinjaman->kategori_jenis_pinjaman_id == KATEGORI_JENIS_PINJAMAN_JANGKA_PENDEK) {
                    $coa = COA_JASA_TOP_UP_PINJ_JANGKA_PENDEK;
                }

                $jurnal = new Jurnal();
                $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
                $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
                $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
                $jurnal->akun_kredit = $coa;
                $jurnal->kredit = $pinjaman->biaya_jasa_topup;
                $jasa_topup = $pinjaman->biaya_jasa_topup;
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
                $jurnal->created_by = Auth::user()->id;
                $jurnal->updated_by = Auth::user()->id;

                // save as polymorphic
                $jurnal->trans_id = $pinjaman->kode_pinjam;
                $jurnal->anggota = $pinjaman->kode_anggota;
                $pinjaman->jurnals()->save($jurnal);


                $pinjamantopup = $pinjaman->pengajuan->pengajuanTopup;
                $pinjamantopup->each(function ($topup)use($pinjaman,&$sisa_pinjaman) {
                    $pinjamandata = $topup->pinjaman;
                    $jurnal = new Jurnal();
                    $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
                    $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
                    $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
                    $jurnal->akun_kredit = $pinjamandata->kode_jenis_pinjam;
                    $jurnal->kredit = $pinjamandata->sisa_pinjaman;
                    $jurnal->akun_debet = 0;
                    $jurnal->debet = 0;
                    $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
                    $jurnal->created_by = Auth::user()->id;
                    $jurnal->updated_by = Auth::user()->id;

                    // save as polymorphic
                    $jurnal->trans_id = $pinjaman->kode_pinjam;
                    $jurnal->anggota = $pinjaman->kode_anggota;
                    $pinjaman->jurnals()->save($jurnal);
                    $sisa_pinjaman +=$pinjamandata->sisa_pinjaman;

                });

            }
            //cek simpanan pagu
            if ($pinjaman->pengajuan->transfer_simpanan_pagu){
                $simpanan_pagu=$pinjaman->pengajuan->transfer_simpanan_pagu;
                $jurnal = new Jurnal();
                $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
                $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
                $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
                $jurnal->akun_kredit = '409.03.000';
                $jurnal->kredit = $simpanan_pagu;
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
                $jurnal->created_by = Auth::user()->id;
                $jurnal->updated_by = Auth::user()->id;

                // save as polymorphic
                $jurnal->trans_id = $pinjaman->kode_pinjam;
                $jurnal->anggota = $pinjaman->kode_anggota;
                $pinjaman->jurnals()->save($jurnal);
            }

            // jurnal untuk total credit bank
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKK;
            $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->tgl_transaksi = Carbon::parse($pinjaman->tgl_transaksi);
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            if ($pinjaman->akunKredit) {
                $jurnal->akun_kredit = $pinjaman->akunKredit->CODE;
            } else {
                $jurnal->akun_kredit = '102.18.000';
            }
            $jurnal->kredit = $pinjaman->besar_pinjam - $pinjaman->biaya_administrasi - $pinjaman->biaya_provisi - $pinjaman->biaya_asuransi - $jasa_topup - $sisa_pinjaman - $simpanan_pagu;
            $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->trans_id = $pinjaman->kode_pinjam;
            $jurnal->anggota = $pinjaman->kode_anggota;

            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    public static function createJurnalSaldoPinjaman(Pinjaman $pinjaman)
    {
        try {
            // jurnal pinjaman
            // jurnal untuk debet
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JSA;
            $jurnal->nomer = $pinjaman->tgl_transaksi->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->akun_debet = $pinjaman->kode_jenis_pinjam;
            $jurnal->debet = $pinjaman->sisa_pinjaman;
            $jurnal->akun_kredit = 0;
            $jurnal->kredit = 0;
            $jurnal->keterangan = 'Pinjaman ' . strtolower($pinjaman->jenisPinjaman->nama_pinjaman) . ' anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->tgl_transaksi = $pinjaman->tgl_mutasi;
            $jurnal->trans_id = $pinjaman->kode_pinjam;
            $jurnal->anggota = $pinjaman->kode_anggota;


            // save as polymorphic
            $pinjaman->jurnals()->save($jurnal);


        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    public static function createJurnalAngsuran(Angsuran $angsuran)
    {
        // kredit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $angsuran->paid_at)->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_kredit = $angsuran->pinjaman->kode_jenis_pinjam;

        // make balance
        $jasa = ($angsuran->besar_pembayaran > $angsuran->jasa) ? $angsuran->jasa : $angsuran->besar_pembayaran;
        $angsur = ($angsuran->besar_pembayaran - $angsuran->jasa + 2 > $angsuran->besar_angsuran) ? $angsuran->besar_angsuran : ($angsuran->besar_pembayaran - $angsuran->jasa);

        //end balancing

        $jurnal->kredit = ($angsur > 0) ? $angsur : 0;
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        if (is_null($angsuran->keterangan) || $angsuran->keterangan == '') {
            $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        } else {
            $jurnal->keterangan = $angsuran->keterangan;
        }

        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
        $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;
        $jurnal->trans_id = $angsuran->kode_angsur;
        $jurnal->anggota = $angsuran->kode_anggota;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);

        // kredit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::now()->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_kredit = 0;
        $jurnal->kredit = 0;
        if ($angsuran->akunKredit) {
            $jurnal->akun_debet = $angsuran->akunKredit->CODE;
        } else {
            $jurnal->akun_debet = COA_BANK_MANDIRI;
        }
        $jurnal->debet = $angsuran->besar_pembayaran;
        if (is_null($angsuran->keterangan) || $angsuran->keterangan == '') {
            $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        } else {
            $jurnal->keterangan = $angsuran->keterangan;
        }
        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
        $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;
        $jurnal->trans_id = $angsuran->kode_angsur;
        $jurnal->anggota = $angsuran->kode_anggota;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);

        // jurnal untuk JASA
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::now()->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        // japen
        switch ($angsuran->pinjaman->kode_jenis_pinjam) {
            case('105.01.001'):
                $jurnal->akun_kredit = '701.02.003';
                break;
            case('106.09.002'):
                $jurnal->akun_kredit = '701.02.005';
                break;
            case('106.09.003'):
                $jurnal->akun_kredit = '701.02.006';
                break;
            case('106.10.001'):
                $jurnal->akun_kredit = '701.02.008';
                break;
            case('106.10.002'):
                $jurnal->akun_kredit = '701.02.008';
                break;
            case('106.10.003'):
                $jurnal->akun_kredit = '701.02.008';
                break;
            case('106.09.001'):
                $jurnal->akun_kredit = '701.02.007';
                break;
            case('106.09.004'):
                $jurnal->akun_kredit = '701.02.012';
                break;

            default:
                $jurnal->akun_kredit = '701.02.001';
        }

        $jurnal->kredit = $jasa;
        if (is_null($angsuran->keterangan) || $angsuran->keterangan == '') {
            $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        } else {
            $jurnal->keterangan = $angsuran->keterangan;
        }
        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
        $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;
        $jurnal->trans_id = $angsuran->kode_angsur;
        $jurnal->anggota = $angsuran->kode_anggota;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);
    }

    public static function createJurnalAngsuranBaru(Angsuran $angsuran, $jasa)
    {
        // kredit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $angsuran->paid_at)->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_kredit = $angsuran->pinjaman->kode_jenis_pinjam;

        // make balance
        // $jasa = ($angsuran->besar_pembayaran>$angsuran->jasa)?$angsuran->jasa:$angsuran->besar_pembayaran;
        // $angsur = ($angsuran->besar_pembayaran-$angsuran->jasa+2>$angsuran->besar_angsuran)?$angsuran->besar_angsuran:($angsuran->besar_pembayaran-$angsuran->jasa);
        $angsur = $angsuran->besar_pembayaran;

        //end balancing

        $jurnal->kredit = ($angsur > 0) ? $angsur : 0;
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        if (is_null($angsuran->keterangan) || $angsuran->keterangan == '') {
            $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        } else {
            $jurnal->keterangan = $angsuran->keterangan;
        }

        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
        $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;
        $jurnal->trans_id = $angsuran->kode_pinjam;
        $jurnal->anggota = $angsuran->kode_anggota;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);

        // kredit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::now()->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_kredit = 0;
        $jurnal->kredit = 0;
        if ($angsuran->akunKredit) {
            $jurnal->akun_debet = $angsuran->akunKredit->CODE;
        } else {
            $jurnal->akun_debet = COA_BANK_MANDIRI;
        }
        // $jurnal->kredit = $angsuran->besar_pembayaran - $angsuran->besar_pembayaran_jasa;
        if ($jasa) {
            $jurnal->debet = $angsur + $jasa;
        } else {
            $jurnal->debet = $angsur;
        }
        if (is_null($angsuran->keterangan) || $angsuran->keterangan == '') {
            $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
        } else {
            $jurnal->keterangan = $angsuran->keterangan;
        }
        $jurnal->created_by = $angsuran->updated_by;
        $jurnal->updated_by = $angsuran->updated_by;
        $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;
        $jurnal->trans_id = $angsuran->kode_pinjam;
        $jurnal->anggota = $angsuran->kode_anggota;

        // save as polymorphic
        $angsuran->jurnals()->save($jurnal);

        if ($jasa) {
            // jurnal untuk JASA
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
            $jurnal->nomer = Carbon::now()->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            // japen
            switch ($angsuran->pinjaman->kode_jenis_pinjam) {
                case('105.01.001'):
                    $jurnal->akun_kredit = '701.02.003';
                    break;
                case('106.09.002'):
                    $jurnal->akun_kredit = '701.02.005';
                    break;
                case('106.09.003'):
                    $jurnal->akun_kredit = '701.02.006';
                    break;
                case('106.10.001'):
                    $jurnal->akun_kredit = '701.02.008';
                    break;
                case('106.10.002'):
                    $jurnal->akun_kredit = '701.02.008';
                    break;
                case('106.10.003'):
                    $jurnal->akun_kredit = '701.02.008';
                    break;
                case('106.09.001'):
                    $jurnal->akun_kredit = '701.02.007';
                    break;
                case('106.09.004'):
                    $jurnal->akun_kredit = '701.02.012';
                    break;

                default:
                    $jurnal->akun_kredit = '701.02.001';
            }
            $jurnal->kredit = $jasa;
            if (is_null($angsuran->keterangan) || $angsuran->keterangan == '') {
                $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angsuran->pinjaman->anggota->nama_anggota));
            } else {
                $jurnal->keterangan = $angsuran->keterangan;
            }
            $jurnal->created_by = $angsuran->updated_by;
            $jurnal->updated_by = $angsuran->updated_by;
            $jurnal->tgl_transaksi = $angsuran->tgl_transaksi;
            $jurnal->trans_id = $angsuran->kode_pinjam;
            $jurnal->anggota = $angsuran->kode_anggota;

            // save as polymorphic
            $angsuran->jurnals()->save($jurnal);
        }
    }

    public static function createJurnalAngsuranPartial(AngsuranPartial $angs)
    {
        // kredit
        DB::beginTransaction();
        try {
            if ($angs->besar_angsuran > 0) {
                $jurnal = new Jurnal();
                $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
                $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $angs->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
                $jurnal->akun_kredit = $angs->angsuran->pinjaman->kode_jenis_pinjam;
                $jurnal->kredit = $angs->besar_angsuran;
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                if (is_null($angs->keterangan) || $angs->keterangan == '') {
                    $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angs->angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angs->angsuran->pinjaman->anggota->nama_anggota));
                } else {
                    $jurnal->keterangan = $angs->angsuran->keterangan;
                }

                $jurnal->created_by = $angs->created_by;
                $jurnal->updated_by = $angs->updated_by;
                $jurnal->tgl_transaksi = $angs->tgl_transaksi;

                // save as polymorphic
                $angs->jurnals()->save($jurnal);
            }


            // debet
            if ($angs->besar_pembayaran > 0) {
                $jurnal = new Jurnal();
                $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
                $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $angs->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
                if ($angs->akunKredit) {
                    $jurnal->akun_debet = $angs->akunKredit->CODE;
                } else {
                    $jurnal->akun_debet = COA_BANK_MANDIRI;
                }
                $jurnal->debet = $angs->besar_pembayaran;
                if (is_null($angs->keterangan) || $angs->keterangan == '') {
                    $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angs->angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angs->angsuran->pinjaman->anggota->nama_anggota));
                } else {
                    $jurnal->keterangan = $angs->angsuran->keterangan;
                }
                $jurnal->created_by = $angs->created_by;
                $jurnal->updated_by = $angs->updated_by;
                $jurnal->tgl_transaksi = $angs->tgl_transaksi;

                // save as polymorphic
                $angs->jurnals()->save($jurnal);
            }


            // jurnal untuk JASA
            if ($angs->jasa > 0) {
                $jurnal = new Jurnal();
                $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
                $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $angs->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                // japen
                if ($angs->angsuran->pinjaman->kode_jenis_pinjam == '105.01.001') {
                    $jurnal->akun_kredit = '701.02.003';
                } // japan and others
                else {
                    $jurnal->akun_kredit = '701.02.001';
                }
                $jurnal->kredit = $angs->jasa;
                if (is_null($angs->keterangan) || $angs->keterangan == '') {
                    $jurnal->keterangan = 'Pembayaran angsuran ke  ' . strtolower($angs->angsuran->angsuran_ke) . ' anggota ' . ucwords(strtolower($angs->angsuran->pinjaman->anggota->nama_anggota));
                } else {
                    $jurnal->keterangan = $angs->angsuran->keterangan;
                }
                $jurnal->created_by = $angs->updated_by;
                $jurnal->updated_by = $angs->updated_by;
                $jurnal->tgl_transaksi = $angs->tgl_transaksi;

                // save as polymorphic
                $angs->jurnals()->save($jurnal);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            throw new \Exception($e->getMessage());

        }
    }

    public static function createJurnalSimpanan(Simpanan $simpanan)
    {
        try {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
            $jurnal->nomer = Carbon::parse($simpanan->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->akun_kredit = $simpanan->kode_jenis_simpan;
            $jurnal->kredit = $simpanan->besar_simpanan;
            if ($simpanan->akunDebet) {
                $jurnal->akun_debet = $simpanan->akunDebet->CODE;
            } else {
                $jurnal->akun_debet = COA_BANK_MANDIRI;
            }
            $jurnal->debet = $simpanan->besar_simpanan;
            $jurnal->keterangan = $simpanan->keterangan;
            $jurnal->created_by = $simpanan->created_by;
            if ($jurnal->updated_by) {
                $jurnal->updated_by = $simpanan->updated_by;
            }

            $jurnal->tgl_transaksi = $simpanan->tgl_transaksi;
            $jurnal->trans_id = $simpanan->kode_simpan;
            $jurnal->anggota = $simpanan->kode_anggota;

            // save as polymorphic
            $simpanan->jurnals()->save($jurnal);
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    public static function createJurnalSaldoSimpanan(Simpanan $simpanan)
    {
        try {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
            $jurnal->nomer = Carbon::createFromFormat('Y-m-d', $simpanan->tgl_entri)->format('Ymd') . (Jurnal::count() + 1);
            $jurnal->akun_kredit = $simpanan->kode_jenis_simpan;
            $jurnal->kredit = $simpanan->besar_simpanan;
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->keterangan = $simpanan->keterangan . ' ' . ucwords(strtolower($simpanan->anggota->nama_anggota));
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->tgl_transaksi = $simpanan->tgl_transaksi;
            $jurnal->trans_id = $simpanan->kode_simpan;
            $jurnal->anggota = $simpanan->kode_anggota;
            // save as polymorphic
            $simpanan->jurnals()->save($jurnal);
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    public static function createSaldoAwal(SaldoAwal $saldoAwal)
    {
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JSA;
        $jurnal->nomer = Carbon::createFromFormat('Y-m-d H:i:s', $saldoAwal->created_at)->format('Ymd') . (Jurnal::count() + 1);

        if ($saldoAwal->code->normal_balance_id == NORMAL_BALANCE_DEBET) {
            $jurnal->akun_debet = $saldoAwal->code->CODE;
            $jurnal->debet = $saldoAwal->nominal;
            $jurnal->akun_kredit = 0;
            $jurnal->kredit = 0;
        } else if ($saldoAwal->code->normal_balance_id == NORMAL_BALANCE_KREDIT) {
            $jurnal->akun_debet = 0;
            $jurnal->debet = 0;
            $jurnal->akun_kredit = $saldoAwal->code->CODE;
            $jurnal->kredit = $saldoAwal->nominal;
        }

        $jurnal->keterangan = 'Saldo Awal';
        $jurnal->created_at = Carbon::today()->subYear()->endOfYear()->format('Y-m-d');
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->tgl_transaksi = Carbon::now()->subYear()->endOfYear()->format('Ymd');


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
        if ($code->normal_balance_id == NORMAL_BALANCE_DEBET) {
            $jurnal = $jurnal->where('akun_debet', $code->CODE)->first();
        } // if kredit
        else if ($code->normal_balance_id == NORMAL_BALANCE_KREDIT) {
            $jurnal = $jurnal->where('akun_kredit', $code->CODE)->first();
        }
        // if jurnal exist
        if ($jurnal) {
            if ($code->normal_balance_id == NORMAL_BALANCE_DEBET) {
                $jurnal->akun_debet = $saldoAwal->code->CODE;
                $jurnal->debet = $saldoAwal->nominal;
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
            } else if ($code->normal_balance_id == NORMAL_BALANCE_KREDIT) {
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->akun_kredit = $saldoAwal->code->CODE;
                $jurnal->kredit = $saldoAwal->nominal;
            }

            $jurnal->updated_by = Auth::user()->id;

            // save as polymorphic
            $saldoAwal->jurnals()->save($jurnal);
        } else {
            self::createSaldoAwal($saldoAwal);
        }
    }

    public static function createJurnalUmum(JurnalUmum $jurnalUmum)
    {
        $jurnalUmumItems = $jurnalUmum->jurnalUmumItems;

        foreach ($jurnalUmumItems as $key => $jurnalUmumItem) {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JU;
            $jurnal->nomer = Carbon::now()->format('Ymd') . (Jurnal::count() + 1);

            if ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_DEBET) {
                $jurnal->akun_debet = $jurnalUmumItem->code->CODE;
                $jurnal->debet = $jurnalUmumItem->nominal;
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
            } else if ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_KREDIT) {
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->akun_kredit = $jurnalUmumItem->code->CODE;
                $jurnal->kredit = $jurnalUmumItem->nominal;
            }

            $jurnal->keterangan = $jurnalUmum->deskripsi;
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->tgl_transaksi = $jurnalUmum->tgl_transaksi;

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

        foreach ($jurnalUmumItems as $key => $jurnalUmumItem) {
            $jurnal = new Jurnal();
            $jurnal->id_tipe_jurnal = TIPE_JURNAL_JU;
            $jurnal->nomer = $jurnalUmum->tgl_transaksi->format('Ymd') . (Jurnal::count() + 1);

            if ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_DEBET) {
                $jurnal->akun_debet = $jurnalUmumItem->code->CODE;
                $jurnal->debet = $jurnalUmumItem->nominal;
                $jurnal->akun_kredit = 0;
                $jurnal->kredit = 0;
            } else if ($jurnalUmumItem->normal_balance_id == NORMAL_BALANCE_KREDIT) {
                $jurnal->akun_debet = 0;
                $jurnal->debet = 0;
                $jurnal->akun_kredit = $jurnalUmumItem->code->CODE;
                $jurnal->kredit = $jurnalUmumItem->nominal;
            }

            $jurnal->keterangan = $jurnalUmum->deskripsi;
            $jurnal->created_by = Auth::user()->id;
            $jurnal->updated_by = Auth::user()->id;
            $jurnal->tgl_transaksi = $jurnalUmum->tgl_transaksi;

            // save as polymorphic
            $jurnalUmum->jurnals()->save($jurnal);
        }
    }

    public static function createJurnalPelunasanDipercepat(Pinjaman $pinjaman)
    {

        //kredit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        $jurnal->akun_kredit = $pinjaman->kode_jenis_pinjam;
        $jurnal->kredit = $pinjaman->sisa_pinjaman;
        $jurnal->keterangan = 'Pelunasan dipercepat Pinjaman anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->tgl_transaksi = $pinjaman->tgl_pelunasan;
        $jurnal->trans_id = $pinjaman->kode_pinjam;
        $jurnal->anggota = $pinjaman->kode_anggota;

        // save as polymorphic
        $pinjaman->jurnals()->save($jurnal);

        // debit
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::parse($pinjaman->tgl_pelunasan)->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_kredit = 0;
        $jurnal->kredit = 0;
        if ($pinjaman->akunDebet) {
            $jurnal->akun_debet = $pinjaman->akunDebet->CODE;
        } else {
            $jurnal->akun_debet = COA_BANK_MANDIRI;
        }
        $jurnal->debet = $pinjaman->totalBayarPelunasanDipercepat;
        $jurnal->keterangan = 'Pelunasan dipercepat pinjaman   anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->tgl_transaksi = $pinjaman->tgl_pelunasan;
        $jurnal->trans_id = $pinjaman->kode_pinjam;
        $jurnal->anggota = $pinjaman->kode_anggota;

        // save as polymorphic
        $pinjaman->jurnals()->save($jurnal);

        // jurnal untuk JASA
        $jurnal = new Jurnal();
        $jurnal->id_tipe_jurnal = TIPE_JURNAL_JKM;
        $jurnal->nomer = Carbon::parse($pinjaman->tgl_transaksi)->format('Ymd') . (Jurnal::count() + 1);
        $jurnal->akun_debet = 0;
        $jurnal->debet = 0;
        // japen
        if ($pinjaman->kode_jenis_pinjam == '105.01.001') {
            $jurnal->akun_kredit = '701.02.003';
        } // japan and others
        else {
            $jurnal->akun_kredit = '701.02.001';
        }
        $jurnal->kredit = $pinjaman->jasaPelunasanDipercepat;
        $jurnal->keterangan = 'Pelunasan dipercepat pinjaman   anggota ' . ucwords(strtolower($pinjaman->anggota->nama_anggota));
        $jurnal->created_by = Auth::user()->id;
        $jurnal->updated_by = Auth::user()->id;
        $jurnal->tgl_transaksi = $pinjaman->tgl_pelunasan;
        $jurnal->trans_id = $pinjaman->kode_pinjam;
        $jurnal->anggota = $pinjaman->kode_anggota;

        // save as polymorphic
        $pinjaman->jurnals()->save($jurnal);
    }

    public static function jurnalTotalDr($from, $to)
    {

        $saldoDr = Jurnal::
        whereBetween('tgl_transaksi', [$from, $to])
//            ->where('trans','D')
            ->sum('debet');

        return $saldoDr;
    }

    public static function jurnalTotalCr($from, $to)
    {
        $saldoCr = Jurnal::
        whereBetween('tgl_transaksi', [$from, $to])
//            ->where('trans','K')
            ->sum('kredit');

        return $saldoCr;
    }


}
