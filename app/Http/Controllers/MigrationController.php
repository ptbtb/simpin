<?php

namespace App\Http\Controllers;

use App\Managers\TabunganManager;
use App\Managers\SimpananManager;
use App\Managers\PenarikanManager;
use App\Managers\PinjamanManager;
use App\Managers\AngsuranManager;

use App\Imports\JurnalImport;

use App\Models\Code;
use App\Models\JurnalTemp;
use App\Models\CodeCategory;
use App\Models\Tabungan;
use App\Models\Simpanan;
use App\Models\JenisSimpanan;
use App\Models\JenisPinjaman;
use App\Models\Penarikan;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\Jurnal;

use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Auth;

class MigrationController extends Controller
{
    public function index()
    {
        // TabunganManager::updateSaldoTahunan();
    }
    public static function migrationJurnalTransaction($bulan)
    {
        try 
        {
            // bulan its 1 = january, 2 = feb, 3 = maret, 1 running for 1 month choosed

            $jurnals = JurnalTemp::whereMonth('tgl_posting', '=', $bulan)->where('is_success', 0)->get()->unique('no_bukti');
           
            $jenisSimpanan = JenisSimpanan::get();
            $jenisPinjaman = JenisPinjaman::get();

            foreach ($jurnals as $key => $jurnal) 
            {
                $transactions = JurnalTemp::where('no_bukti', $jurnal->no_bukti)->whereMonth('tgl_posting', '=', $bulan)->where('is_success', 0)->get();

                // group by uraian3 because 1 kode_bukti has more than 1 transaction
                $groupByUraian = $transactions->groupBy('uraian_3');

                foreach ($groupByUraian as $key => $uraian) 
                {
                    // check if kode_anggota is null
                    $groupByAnggota = $uraian->groupBy('kode_anggota');
                    
                    $transactionSuccess = null;

                    $idTipeJurnal = null;

                    if(count($groupByAnggota) == 1)
                    {
                        // simpanan
                        if(count($uraian->where('normal_balance', 2)->whereIn('code', [40407000, 40401000, 40402000, 40404000, 40408000])) > 0 )
                        {
                            $transaction = $uraian->whereIn('code', [41101000, 41112000, 50201000, 40901000, 40903000])->first();

                            if($transaction)
                            {
                                $newCode = substr($transaction->code,0,3).'.'.substr($transaction->code,3,2).'.'.substr($transaction->code,5,3);

                                // get next serial number
                                $nextSerialNumber = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                                $transactionDebet = $uraian->where('kode_anggota', 0)->where('normal_balance', 1)->first();

                                if(!$transactionDebet)
                                {
                                    $transactionDebet = $uraian->where('normal_balance', 1)->first();
                                }

                                $newCodeDebet = substr($transactionDebet->code,0,3).'.'.substr($transactionDebet->code,3,2).'.'.substr($transactionDebet->code,5,3);

                                $codeDebet = Code::where('CODE', $newCodeDebet)->first();

                                $totalTransaction = $uraian->where('normal_balance', 2)->whereIn('code', [40407000, 40401000, 40402000, 40404000, 40408000])->sum('jumlah');

                                $simpanan = new Simpanan();
                                $simpanan->jenis_simpan = strtoupper($jenisSimpanan->where('kode_jenis_simpan', $newCode)->first()->nama_simpanan);
                                $simpanan->besar_simpanan = $totalTransaction;
                                $simpanan->kode_anggota = $transaction->kode_anggota;
                                $simpanan->u_entry = 'Admin BTB';
                                $simpanan->tgl_entri = Carbon::now();
                                $simpanan->periode = $transaction->tgl_posting;
                                $simpanan->kode_jenis_simpan = $jenisSimpanan->where('kode_jenis_simpan', $newCode)->first()->kode_jenis_simpan;
                                $simpanan->keterangan = $transaction->uraian_3;
                                $simpanan->id_akun_debet = $codeDebet->id;
                                $simpanan->serial_number = $nextSerialNumber;
                                $simpanan->save();

                                echo('NO BUKTI SIMPANAN SUCCESS : ' . $jurnal->no_bukti . "<br>");

                                $transactionSuccess = $simpanan;

                                $idTipeJurnal = TIPE_JURNAL_JKM;

                                foreach($uraian as $jurnalTemp)
                                {
                                    // update status jurnal_temp
                                    $jurnalTemp->is_success = 1;
                                    $jurnalTemp->save();
                                }
                            }
                        }
                        else
                        {
                            // save into jurnal because this transaction is buffer transaction, and change to success
                            foreach($uraian as $jurnalTemp)
                            {
                                // update status jurnal_temp
                                $jurnalTemp->is_success = 1;
                                $jurnalTemp->keterangan_gagal = 'ANGGOTA KOSONG';
                                $jurnalTemp->save();
                            }
                        }
                    }
                    else
                    {
                        $transaction = $uraian->where('kode_anggota', '!=', null)->first();

                        $newCode = substr($transaction->code,0,3).'.'.substr($transaction->code,3,2).'.'.substr($transaction->code,5,3);

                        $totalTransaction = $uraian->where('kode_anggota', '!=', null)->first()->jumlah;

                        // check coa is simpanan or pinjaman
                        // if simpanan
                        if($jenisSimpanan->where('kode_jenis_simpan', $newCode)->first() && $transaction->normal_balance == 2)
                        {
                            // get next serial number
                            $nextSerialNumber = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                            $transactionDebet = $uraian->where('kode_anggota', 0)->where('normal_balance', 1)->first();

                            if(!$transactionDebet)
                            {
                                $transactionDebet = $uraian->where('normal_balance', 1)->first();
                            }

                            $newCodeDebet = substr($transactionDebet->code,0,3).'.'.substr($transactionDebet->code,3,2).'.'.substr($transactionDebet->code,5,3);

                            $codeDebet = Code::where('CODE', $newCodeDebet)->first();

                            $simpanan = new Simpanan();
                            $simpanan->jenis_simpan = strtoupper($jenisSimpanan->where('kode_jenis_simpan', $newCode)->first()->nama_simpanan);
                            $simpanan->besar_simpanan = $totalTransaction;
                            $simpanan->kode_anggota = $transaction->kode_anggota;
                            $simpanan->u_entry = 'Admin BTB';
                            $simpanan->tgl_entri = Carbon::now();
                            $simpanan->periode = $transaction->tgl_posting;
                            $simpanan->kode_jenis_simpan = $jenisSimpanan->where('kode_jenis_simpan', $newCode)->first()->kode_jenis_simpan;
                            $simpanan->keterangan = $transaction->uraian_3;
                            $simpanan->id_akun_debet = $codeDebet->id;
                            $simpanan->serial_number = $nextSerialNumber;
                            $simpanan->save();

                            echo('NO BUKTI SIMPANAN SUCCESS : ' . $jurnal->no_bukti . "<br>");

                            $transactionSuccess = $simpanan;

                            $idTipeJurnal = TIPE_JURNAL_JKM;

                            foreach($uraian as $jurnalTemp)
                            {
                                // update status jurnal_temp
                                $jurnalTemp->is_success = 1;
                                $jurnalTemp->save();
                            }
                        }
                        // if penarikan
                        else if($jenisSimpanan->where('kode_jenis_simpan', $newCode)->first() && $transaction->normal_balance == 1)
                        {
                            $jenisSimpanan = $jenisSimpanan->where('kode_jenis_simpan', $newCode)->first();
                            $anggota = Anggota::with('tabungan')->find($transaction->kode_anggota);
                            
                            if(count($anggota->tabungan) > 0)
                            {
                                $tabungan = $anggota->tabungan->where('kode_trans', $jenisSimpanan->kode_jenis_simpan)->first();
                                
                                if($tabungan)
                                {
                                    // get next serial number
                                    $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                                    $penarikan = new Penarikan();
                                    $penarikan->kode_anggota = $transaction->kode_anggota;
                                    $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                                    $penarikan->id_tabungan = $tabungan->id;
                                    $penarikan->besar_ambil = $totalTransaction;
                                    $penarikan->code_trans = $tabungan->kode_trans;
                                    $penarikan->tgl_ambil = Carbon::now();
                                    $penarikan->u_entry = 'Admin BTB';
                                    $penarikan->created_by = 1;
                                    $penarikan->status_pengambilan = STATUS_PENGAMBILAN_DITERIMA;
                                    $penarikan->serial_number = $nextSerialNumber;
                                    $penarikan->paid_by_cashier = 1;
                                    $penarikan->save();

                                    echo('NO BUKTI PENARIKAN SUCCESS : ' . $jurnal->no_bukti . "<br>");

                                    $transactionSuccess = $penarikan;

                                    foreach($uraian as $jurnalTemp)
                                    {
                                        // update status jurnal_temp
                                        $jurnalTemp->is_success = 1;
                                        $jurnalTemp->save();
                                    }
                                }
                                else
                                {
                                    echo('NO BUKTI PENARIKAN, TABUNGAN KOSONG : ' . $jurnal->no_bukti . "<br>");

                                    foreach($uraian as $jurnalTemp)
                                    {
                                        // update status jurnal_temp
                                        $jurnalTemp->keterangan_gagal = 'NO BUKTI PENARIKAN, TABUNGAN KOSONG';
                                        $jurnalTemp->save();
                                    }
                                }
                            }
                            else
                            {
                                foreach($uraian as $jurnalTemp)
                                {
                                    // update status jurnal_temp
                                    $jurnalTemp->keterangan_gagal = 'NO BUKTI PENARIKAN, TABUNGAN KOSONG';
                                    $jurnalTemp->save();
                                }

                                echo('NO BUKTI PENARIKAN, TABUNGAN KOSONG : ' . $jurnal->no_bukti . "<br>");
                            }

                            $idTipeJurnal = TIPE_JURNAL_JKK;
                            
                        }
                        // pinjaman
                        else if($jenisPinjaman->where('kode_jenis_pinjam', $newCode)->first() && $transaction->normal_balance == 1)
                        {
                            $jenisPinjaman = $jenisPinjaman->where('kode_jenis_pinjam', $newCode)->first();

                            $pinjaman = new Pinjaman();
                            
                            $kodeAnggota = $transaction->kode_anggota;
                            $kodePinjaman = str_replace('.','',$jenisPinjaman->kode_jenis_pinjam).'-'.$kodeAnggota.'-'.Carbon::now()->format('dmYHis');

                            $angsuranPerbulan = round($totalTransaction/$jenisPinjaman->lama_angsuran,2);
                            $jasaPerbulan = $totalTransaction*$jenisPinjaman->jasa;

                            if ($totalTransaction > 100000000 && $jenisPinjaman->lama_angsuran > 3 && $jenisPinjaman->isJangkaPendek())
                            {
                                $jasaPerbulan = $pengajuan->besar_pinjam*0.03;
                            }
                            $jasaPerbulan = round($jasaPerbulan,2);

                            $asuransi = $jenisPinjaman->asuransi;
                            $asuransi = round($totalTransaction*$asuransi,2);

                            $totalAngsuranBulan = $angsuranPerbulan+$jasaPerbulan;

                            $provisi = $jenisPinjaman->provisi;
                            $provisi = round($totalTransaction * $provisi,2);

                            $biayaAdministrasi = $jenisPinjaman->biaya_admin;

                            // get next serial number
                            $nextSerialNumber = PinjamanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                            $pinjaman->kode_pinjam = $kodePinjaman;
                            $pinjaman->kode_pengajuan_pinjaman = 0;
                            $pinjaman->kode_anggota = $kodeAnggota;
                            $pinjaman->kode_jenis_pinjam = $jenisPinjaman->kode_jenis_pinjam;
                            $pinjaman->besar_pinjam = $totalTransaction;
                            $pinjaman->besar_angsuran = $totalAngsuranBulan;
                            $pinjaman->besar_angsuran_pokok = $angsuranPerbulan;
                            $pinjaman->lama_angsuran = $jenisPinjaman->lama_angsuran;
                            $pinjaman->sisa_angsuran = $jenisPinjaman->lama_angsuran;
                            $pinjaman->sisa_pinjaman = $totalTransaction;
                            $pinjaman->biaya_jasa = $jasaPerbulan;
                            $pinjaman->biaya_asuransi = $asuransi;
                            $pinjaman->biaya_provisi = $provisi;
                            $pinjaman->biaya_administrasi = $biayaAdministrasi;
                            $pinjaman->u_entry = 'Admin BTB';
                            $pinjaman->tgl_entri = Carbon::now();
                            $pinjaman->tgl_tempo = Carbon::now()->addMonths($jenisPinjaman->lama_angsuran);
                            $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
                            $pinjaman->serial_number = $nextSerialNumber;
                            $pinjaman->save();

                            echo('NO BUKTI PINJAMAN SUCCESS : ' . $jurnal->no_bukti . "<br>");

                            $transactionSuccess = $pinjaman;

                            $idTipeJurnal = TIPE_JURNAL_JKK;

                            foreach($uraian as $jurnalTemp)
                            {
                                // update status jurnal_temp
                                $jurnalTemp->is_success = 1;
                                $jurnalTemp->save();
                            }
                        }
                        // angsuran
                        else if($jenisPinjaman->where('kode_jenis_pinjam', $newCode)->first() && $transaction->normal_balance == 2)
                        {
                            // get next serial number
                            $nextSerialNumber = AngsuranManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                            $kodeAnggota = $transaction->kode_anggota;

                            $pinjaman = Pinjaman::where('kode_anggota', $kodeAnggota)->where('kode_jenis_pinjam', $newCode)->first();

                            if($pinjaman)
                            {
                                $jatuhTempo = $pinjaman->tgl_entri->addMonths(1)->endOfMonth();

                                // count sisa pinjaman
                                $besarPinjaman = $pinjaman->besar_pinjam;
                                $sisaPinjaman = $besarPinjaman-$transaction->jumlah;

                                $angsuran = new Angsuran();
                                $angsuran->kode_pinjam = $pinjaman->kode_pinjam;
                                $angsuran->angsuran_ke = 1;
                                $angsuran->besar_angsuran = $pinjaman->besar_angsuran_pokok;
                                $angsuran->denda = 0;
                                $angsuran->jasa = $pinjaman->biaya_jasa;
                                $angsuran->kode_anggota = $pinjaman->kode_anggota;
                                $angsuran->sisa_pinjam = $sisaPinjaman;
                                $angsuran->tgl_entri = Carbon::now();
                                $angsuran->jatuh_tempo = $jatuhTempo;
                                $angsuran->u_entry = 'Administrator';
                                $angsuran->serial_number = $nextSerialNumber;
                                $angsuran->besar_pembayaran = $transaction->jumlah + $pinjaman->biaya_jasa;

                                if( ($pinjaman->besar_pinjam - $transaction->jumlah) < 1)
                                {
                                    $angsuran->id_status_angsuran = 2;
                                }
                                else
                                {
                                    $angsuran->id_status_angsuran = 1;
                                }
                                $angsuran->save();

                                // update sisa pinjaman
                                $pinjaman->sisa_pinjaman -= $transaction->jumlah;
                                $pinjaman->save();

                                echo('NO BUKTI PINJAMAN SUCCESS : ' . $jurnal->no_bukti . "<br>");

                                $transactionSuccess = $angsuran;

                                foreach($uraian as $jurnalTemp)
                                {
                                    // update status jurnal_temp
                                    $jurnalTemp->is_success = 1;
                                    $jurnalTemp->save();
                                }
                            }
                            else
                            {
                                foreach($uraian as $jurnalTemp)
                                {
                                    // update status jurnal_temp
                                    $jurnalTemp->keterangan_gagal = 'NO BUKTI ANGSURAN, PINJAMAN KOSONG';
                                    $jurnalTemp->save();
                                }

                                echo('NO BUKTI ANGSURAN, PINJAMAN KOSONG : ' . $jurnal->no_bukti . "<br>");
                            }
                            
                            $idTipeJurnal = TIPE_JURNAL_JKM;
                        }
                    }
                    
                    // save every jurnal to jurnal table
                    foreach ($uraian as $key => $uraianJurnal) 
                    {
                        if($uraianJurnal->is_success == 1)
                        {
                            // save into jurnal table
                            $newJurnal = new Jurnal();
                            $newJurnal->id_tipe_jurnal = $idTipeJurnal;
                            $newJurnal->nomer = Carbon::now()->format('Ymd').(Jurnal::count()+1);

                            // new format for code
                            $newCoa = substr($uraianJurnal->code,0,3).'.'.substr($uraianJurnal->code,3,2).'.'.substr($uraianJurnal->code,5,3);

                            // debet
                            if($uraianJurnal->normal_balance == 1)
                            {
                                $newJurnal->akun_debet = $newCoa;
                                $newJurnal->debet = $uraianJurnal->jumlah;
                                $newJurnal->akun_kredit = 0;
                                $newJurnal->kredit = 0;
                            }
                            // kredit
                            else
                            {
                                $newJurnal->akun_debet = 0;
                                $newJurnal->debet = 0;
                                $newJurnal->akun_kredit = $newCoa;
                                $newJurnal->kredit = $uraianJurnal->jumlah;
                            }

                            $newJurnal->keterangan = $uraianJurnal->uraian_3;
                            
                            if($newJurnal->keterangan == '' || $newJurnal->keterangan == null)
                            {
                                $newJurnal->keterangan = '-';
                            }

                            $newJurnal->created_by = 1;
                            $newJurnal->updated_by = 1;
                            $newJurnal->created_at = $uraianJurnal->tgl_posting;

                            if($transactionSuccess)
                            {
                                // save as polymorphic
                                $transactionSuccess->jurnals()->save($newJurnal);
                            }
                            else
                            {
                                $newJurnal->save();
                            }
                        }
                    }
                }
            }

            echo('DONE');
            
        } catch (\Exception $e) {
            dd($e);
        }
    }

}
