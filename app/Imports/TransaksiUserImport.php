<?php

namespace App\Imports;

use App\Managers\JurnalManager;
use App\Managers\AngsuranManager;
use App\Managers\AngsuranPartialManager;
use App\Managers\SimpananManager;
use App\Models\Angsuran;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\JenisSimpanan;
use App\Models\JenisPinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use App\Models\Code;
use Illuminate\Support\Facades\Log;


class TransaksiUserImport
{
    static function generatetransaksi($transaksi)
    {
        try {
            Log::info($transaksi);

            if ($transaksi['THN']) {
                $period_raw = $transaksi['THN'] . '-' . sprintf('%02d', $transaksi['BLN']) . '-01';
                //dd($period_raw);die;
                //simpanan wajib
                $periode = Carbon::createFromFormat('Y-m-d', $period_raw);
                // dd($periode->format('Y-m'));
                $idakun = Code::where('CODE', $transaksi['COA_PERANTARA'])->first();
                $anggota = Anggota::where('kode_anggota', $transaksi['NO_ANG'])->first();
                $tgl_transaksi = Carbon::parse($transaksi['TGL_TRANSAKSI'])->format('Y-m-d');
                Log::info($tgl_transaksi);
                if ($transaksi['S_WAJIB'] > 0) {
                    $check = Simpanan::where('kode_jenis_simpan', '411.12.000')
                        ->where('kode_anggota', $transaksi['NO_ANG'])
                        ->whereraw("DATE_FORMAT(periode, '%Y-%m')='" . $periode->format('Y-m') . "'")->first();
                    $nama_simpanan = JenisSimpanan::where('kode_jenis_simpan', '411.12.000')->first();


                    if (!$check) {
                        $simpanan = new Simpanan();
                        $simpanan->jenis_simpan = $nama_simpanan->nama_simpanan;
                        $simpanan->besar_simpanan = $transaksi['S_WAJIB'];
                        $simpanan->kode_anggota = $transaksi['NO_ANG'];
                        $simpanan->u_entry = Auth::user()->name;;
                        $simpanan->periode = $periode;
                        $simpanan->tgl_mulai = null;
                        $simpanan->tgl_entri = $tgl_transaksi;
                        $simpanan->tgl_transaksi = $tgl_transaksi;
                        $simpanan->kode_jenis_simpan = '411.12.000';
                        $simpanan->keterangan = $transaksi['KETERANGAN'];
                        $simpanan->mutasi = 0;
                        $simpanan->serial_number = SimpananManager::getSerialNumber($transaksi['TGL_TRANSAKSI']->format('d-m-Y'));
                        $simpanan->id_akun_debet = ($idakun->id) ? $idakun->id : null;
                        $simpanan->save();
                        JurnalManager::createJurnalSimpanan($simpanan);
                    }
                }
                if ($transaksi['S_SUKARELA'] > 0) {
                    $check2 = Simpanan::where('kode_jenis_simpan', '502.01.000')
                        ->where('kode_anggota', $transaksi['NO_ANG'])
                        ->whereraw("DATE_FORMAT(periode, '%Y-%m')='" . $periode->format('Y-m') . "'")->first();
                    $nama_simpanan2 = JenisSimpanan::where('kode_jenis_simpan', '502.01.000')->first();
                    if (!$check2) {
                        $simpanan = new Simpanan();
                        $simpanan->jenis_simpan = $nama_simpanan2->nama_simpanan;
                        $simpanan->besar_simpanan = $transaksi['S_SUKARELA'];
                        $simpanan->kode_anggota = $transaksi['NO_ANG'];
                        $simpanan->u_entry = Auth::user()->name;;
                        $simpanan->periode = $periode;
                        $simpanan->tgl_mulai = null;
                        $simpanan->tgl_entri = $tgl_transaksi;
                        $simpanan->tgl_transaksi = $tgl_transaksi;
                        $simpanan->kode_jenis_simpan = '502.01.000';
                        $simpanan->keterangan = $transaksi['KETERANGAN'];
                        $simpanan->mutasi = 0;
                        $simpanan->serial_number = SimpananManager::getSerialNumber($transaksi['TGL_TRANSAKSI']->format('d-m-Y'));
                        $simpanan->id_akun_debet = ($idakun->id) ? $idakun->id : null;

                        $simpanan->save();
                        JurnalManager::createJurnalSimpanan($simpanan);
                    }
                }

                if ($transaksi['PINJ1'] > 0) {

                    if (!empty($transaksi['KODE_PINJAM1'])) {
                        $pinjaman1 = Pinjaman::where('kode_pinjam', $transaksi['KODE_PINJAM1'])
                            ->first();
                        if (!$pinjaman1) {
                            throw new \Exception("Pinjaman " . $transaksi['KODE_PINJAM1'] . " Tidak Ditemukan, silahkan Upload ulang dengan data yang benar");
                        }

                    } else {
                        $pinjaman1 = Pinjaman::where('kode_jenis_pinjam', $transaksi['REK_PINJ_1'])
                            ->where('kode_anggota', $transaksi['NO_ANG'])
                            ->wherenotnull('tgl_transaksi')
                            ->where('id_status_pinjaman', 1)->first();
                        if (!$pinjaman1) {
                            throw new \Exception("Pinjaman " . $transaksi['REK_PINJ_1'] . " anggota " . $transaksi['NO_ANG'] . " Tidak Ditemukan. silahkan Upload ulang dengan data yang benar.");
                        }
                    }

                    if ($pinjaman1) {
                        $angsuranmaxKe = DB::table('t_angsur')
                            ->selectraw("max(angsuran_ke) as maxke ")
                            ->where('kode_pinjam', $transaksi['KODE_PINJAM1'])->get();
                        $angsuranKe = $angsuranmaxKe[0]->maxke + 1;

                        $angsuran1 = new Angsuran();

                        $serialNumber = AngsuranManager::getSerialNumber($transaksi['TGL_TRANSAKSI']->format('d-m-Y'));
                        $pembayaran = $transaksi['POKOK_PINJ1'] + $transaksi['JS_PINJ1'];
                        $angsuran1->kode_pinjam = $pinjaman1->kode_pinjam;
                        $angsuran1->kode_anggota = $pinjaman1->kode_anggota;
                        $angsuran1->angsuran_ke = $angsuranKe;
                        $angsuran1->besar_angsuran = $transaksi['POKOK_PINJ1'];
                        $angsuran1->besar_pembayaran = $pembayaran;
                        $angsuran1->jasa = $transaksi['JS_PINJ1'];
                        $angsuran1->paid_at = $tgl_transaksi;
                        $angsuran1->tgl_entri = Carbon::now();
                        $angsuran1->u_entry = Auth::user()->name;
                        $angsuran1->tgl_transaksi = $tgl_transaksi;
                        $angsuran1->updated_by = Auth::user()->id;
                        $angsuran1->id_akun_kredit = ($idakun->id) ? $idakun->id : null;
                        $angsuran1->serial_number = $serialNumber;
                        $angsuran1->id_status_angsuran = 2;
                        $angsuran1->keterangan = $transaksi['KETERANGAN'];
                        $angsuran1->save();
                        // dd($angsuran1);

                        // JurnalManager::createJurnalAngsuran($angsuran1);

                        if ($pinjaman1->sisa_pinjaman <= 0) {
                            $pinjaman1->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                            $pinjaman1->save();
                        }

                    }


                    JurnalManager::createJurnalAngsuran($angsuran1);

                }
                if ($transaksi['PINJ2'] > 0) {
                    if (!empty($transaksi['KODE_PINJAM2'])) {
                        $pinjaman2 = Pinjaman::where('kode_pinjam', $transaksi['KODE_PINJAM2'])
                            ->first();
                        if (!$pinjaman2) {
                            throw new \Exception("Pinjaman " . $transaksi['KODE_PINJAM2'] . " Tidak Ditemukan, silahkan Upload ulang dengan data yang benar");
                        }

                    } else {
                        $pinjaman2 = Pinjaman::where('kode_jenis_pinjam', $transaksi['REK_PINJ_2'])
                            ->where('kode_anggota', $transaksi['NO_ANG'])
                            ->wherenotnull('tgl_transaksi')
                            ->where('id_status_pinjaman', 1)->first();
                        if (!$pinjaman2) {
                            throw new \Exception("Pinjaman " . $transaksi['REK_PINJ_2'] . " anggota " . $transaksi['NO_ANG'] . " Tidak Ditemukan. silahkan Upload ulang dengan data yang benar.");
                        }
                    }


                    if ($pinjaman2) {
                        $angsuranmaxKe = DB::table('t_angsur')
                            ->selectraw("max(angsuran_ke) as maxke ")
                            ->where('kode_pinjam', $transaksi['KODE_PINJAM2'])->get();
                        $angsuranKe = $angsuranmaxKe[0]->maxke + 1;

                        $angsuran2 = new Angsuran();

                        $serialNumber = AngsuranManager::getSerialNumber($transaksi['TGL_TRANSAKSI']->format('d-m-Y'));
                        $pembayaran = $transaksi['POKOK_PINJ2'] + $transaksi['JS_PINJ2'];
                        $angsuran2->kode_pinjam = $pinjaman2->kode_pinjam;
                        $angsuran2->kode_anggota = $pinjaman2->kode_anggota;
                        $angsuran2->angsuran_ke = $angsuranKe;
                        $angsuran2->besar_angsuran = $transaksi['POKOK_PINJ2'];
                        $angsuran2->besar_pembayaran = $pembayaran;
                        $angsuran2->jasa = $transaksi['JS_PINJ2'];
                        $angsuran2->paid_at = $tgl_transaksi;
                        $angsuran2->tgl_entri = Carbon::now();
                        $angsuran2->u_entry = Auth::user()->name;
                        $angsuran2->tgl_transaksi = $tgl_transaksi;
                        $angsuran2->updated_by = Auth::user()->id;
                        $angsuran2->id_akun_kredit = ($idakun->id) ? $idakun->id : null;
                        $angsuran2->serial_number = $serialNumber;
                        $angsuran2->id_status_angsuran = 2;
                        $angsuran2->keterangan = $transaksi['KETERANGAN'];
                        $angsuran2->save();
                        // dd($angsuran1);

                        // JurnalManager::createJurnalAngsuran($angsuran1);

                        if ($pinjaman1->sisa_pinjaman <= 0) {
                            $pinjaman1->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                            $pinjaman1->save();
                        }
                    }
                    JurnalManager::createJurnalAngsuran($angsuran2);

                }
            }

        } catch (\Exception $e) {
            Log::info($e);
            throw new \Exception($e->getMessage());
        }

    }

    static function storeToTemp($transaksi)
    {
        try {
            dd($transaksi);

        } catch (\Exception $e) {
            Log::info($e);
            throw new \Exception($e->getMessage());
        }
    }

}
