<?php

namespace App\Imports;

use App\Managers\JurnalManager;
use App\Managers\SimpananManager;
use App\Models\Angsuran;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\JenisSimpanan;
use App\Models\JenisPinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use App\Models\Code;


class TransaksiUserImport 
{
	static function generatetransaksi($transaksi)
	{
		try
		{
			if($transaksi['THN']){
			$period_raw=$transaksi['THN'].'-'.sprintf('%02d', $transaksi['BLN']);
        	 //dd($period_raw);die;
        	//simpanan wajib
			$periode=Carbon::createFromFormat('Y-m',$period_raw)->endOfMonth();
			$idakun=Code::where('CODE',$transaksi['COA_PERANTARA'])->first();
			$anggota=Anggota::where('kode_anggota',$transaksi['NO_ANG'])->first();


			if($transaksi['S_WAJIB']>0){
				$check= Simpanan::where('kode_jenis_simpan','411.12.000')
				->where('kode_anggota',$transaksi['NO_ANG'])
				->whereraw("DATE_FORMAT(periode, '%Y-%m')='".$period_raw."'")->first();
				$nama_simpanan=JenisSimpanan::where('kode_jenis_simpan','411.12.000')->first();
				

				if(!$check){
					$simpanan = new Simpanan();
					$simpanan->jenis_simpan = $nama_simpanan->nama_simpanan;
					$simpanan->besar_simpanan = $transaksi['S_WAJIB'];
					$simpanan->kode_anggota = $transaksi['NO_ANG'];
					$simpanan->u_entry = Auth::user()->name;;
					$simpanan->periode = $periode;
					$simpanan->tgl_mulai = null;
					$simpanan->tgl_entri = Carbon::now()->format('Y-m-d');
					$simpanan->kode_jenis_simpan ='411.12.000';
					$simpanan->keterangan = $nama_simpanan->nama_simpanan." ". $anggota->nama_aggota." ".$periode;
					$simpanan->mutasi = 0;
					$simpanan->serial_number = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
					$simpanan->id_akun_debet = ($idakun->id) ? $idakun->id : null;
                 $simpanan->save();
                 JurnalManager::createJurnalSimpanan($simpanan);
				}
			}
			if($transaksi['S_SUKARELA']>0){
				$check2= Simpanan::where('kode_jenis_simpan','502.01.000')
				->where('kode_anggota',$transaksi['NO_ANG'])
				->whereraw("DATE_FORMAT(periode, '%Y-%m')='".$period_raw."'")->first();
				$nama_simpanan2=JenisSimpanan::where('kode_jenis_simpan','502.01.000')->first();
				if(!$check2){
					$simpanan = new Simpanan();
					$simpanan->jenis_simpan = $nama_simpanan2->nama_simpanan;
					$simpanan->besar_simpanan = $transaksi['S_SUKARELA'];
					$simpanan->kode_anggota = $transaksi['NO_ANG'];
					$simpanan->u_entry = Auth::user()->name;;
					$simpanan->periode = $periode;
					$simpanan->tgl_mulai = null;
					$simpanan->tgl_entri = Carbon::now()->format('Y-m-d');
					$simpanan->kode_jenis_simpan ='411.12.000';
					$simpanan->keterangan = $nama_simpanan2->nama_simpanan." ". $anggota->nama_aggota." ".$periode;
					$simpanan->mutasi = 0;
					$simpanan->serial_number = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
					$simpanan->id_akun_debet = ($idakun->id) ? $idakun->id : null;
					
					$simpanan->save();
                 JurnalManager::createJurnalSimpanan($simpanan);
				}
			}
			
			if($transaksi['PINJ1']>0){
				$pinjaman1= Pinjaman::where('kode_jenis_pinjam',$transaksi['REK_PINJ_1'])
				->where('kode_anggota',$transaksi['NO_ANG'])->first();
				if ($pinjaman1){
					$angsuran1= Angsuran::where('kode_pinjam',$pinjaman1['kode_pinjam'])
					->whereraw("DATE_FORMAT(jatuh_tempo, '%Y-%m')='".$period_raw."'")->first();

					if ($angsuran1)
					{
						$serialNumber=Angsuran::getSerialNumber(Carbon::now()->format('d-m-Y'));
						$pembayaran =$transaksi['POKOK_PINJ1']+$transaksi['JS_PINJ1'];
						if ($angsuran1->besar_pembayaran) {
							$pembayaran = $pembayaran + $angsuran1->besar_pembayaran;
						}
						if ($pembayaran >= $angsuran1->totalAngsuran1-5) {
							$angsuran1->besar_pembayaran = $angsuran1->totalAngsuran;
							$angsuran1->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
							$pinjaman1->sisa_angsuran = $pinjaman1->sisa_angsuran - 1;
							$pinjaman1->save();
						} else {
							$angsuran1->besar_pembayaran = $pembayaran;
						}


						$pembayaran = $pembayaran - $angsuran1->totalAngsuran;
						$angsuran1->paid_at =  Carbon::now();
						$angsuran1->updated_by = Auth::user()->id;
						$angsuran1->id_akun_kredit = ($idakun->id) ? $idakun->id : null;
						$angsuran1->serial_number=$serialNumber;
						$angsuran1->save();

            // create JKM angsuran
						JurnalManager::createJurnalAngsuran($angsuran1);

						if ($pembayaran <= 0) {
							$pinjaman1->sisa_pinjaman = $angsuran1->sisaPinjaman;
							$pinjaman1->save();
						}
						if ($pinjaman1->sisa_pinjaman <= 0) {
							$pinjaman1->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
							$pinjaman1->save();
						}
					}
				}
				
			}

			if($transaksi['PINJ2']>0){
				$pinjaman2= Pinjaman::where('kode_jenis_pinjam',$transaksi['REK_PINJ_1'])
				->where('kode_anggota',$transaksi['NO_ANG'])->first();
				if ($pinjaman2){
					$angsuran2= Angsuran::where('kode_pinjam',$pinjaman2['kode_pinjam'])
					->whereraw("DATE_FORMAT(jatuh_tempo, '%Y-%m')='".$period_raw."'")->first();

					if ($angsuran2)
					{
						$serialNumber2=Angsuran::getSerialNumber(Carbon::now()->format('d-m-Y'));
						$pembayaran =$transaksi['POKOK_PINJ1']+$transaksi['JS_PINJ1'];
						if ($angsuran2->besar_pembayaran) {
							$pembayaran = $pembayaran + $angsuran2->besar_pembayaran;
						}
						if ($pembayaran >= $angsuran2->totalAngsuran-5) {
							$angsuran2->besar_pembayaran = $angsuran2->totalAngsuran;
							$angsuran2->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
							$pinjaman2->sisa_angsuran = $pinjaman2->sisa_angsuran - 1;
							$pinjaman2->save();
						} else {
							$angsuran2->besar_pembayaran = $pembayaran;
						}


						$pembayaran = $pembayaran - $angsuran2->totalAngsuran;
						$angsuran2->paid_at =  Carbon::now();
						$angsuran2->updated_by = Auth::user()->id;
						$angsuran2->id_akun_kredit = ($idakun->id) ? $idakun->id : null;
						$angsuran2->serial_number=$serialNumber2;
						$angsuran2->save();

            // create JKM angsuran
						JurnalManager::createJurnalAngsuran($angsuran2);

						if ($pembayaran <= 0) {
							$pinjaman2->sisa_pinjaman = $angsuran2->sisaPinjaman;
							$pinjaman2->save();
						}
						if ($pinjaman2->sisa_pinjaman <= 0) {
							$pinjaman2->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
							$pinjaman2->save();
						}
					}
				}
				
			}
		}
		
	}
	catch (\Exception $e)
	{
		\Log::info($e);
		throw new \Exception($e);
	}
}
}
