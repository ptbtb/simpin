<?php
namespace App\Managers;

use App\Events\Pinjaman\PinjamanCreated;
use App\Managers\JurnalManager;
use App\Models\AsuransiPinjaman;
use App\Models\Pinjaman;
use App\Models\Pengajuan;
use App\Models\SimpinRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PinjamanManager 
{
    static function createPinjaman(Pengajuan $pengajuan)
    {
        try
        {
            $jenisPinjaman = $pengajuan->jenisPinjaman;
            $lamaAngsuran = $pengajuan->tenor;
            if(is_null($lamaAngsuran))
            {
                $lamaAngsuran = $jenisPinjaman->lama_angsuran;
            }
            $angsuranPerbulan = round($pengajuan->besar_pinjam/$lamaAngsuran,2);
            // $bungaPerbulan = $angsuranPerbulan*$jenisPinjaman->bunga/100;
            $jasaPerbulan = $pengajuan->besar_pinjam*$jenisPinjaman->jasa;
            if ($pengajuan->besar_pinjam > 100000000 && $lamaAngsuran > 3 && $jenisPinjaman->isJangkaPendek())
            {
                $jasaPerbulan = $pengajuan->besar_pinjam*0.03;
            }
            $jasaPerbulan = round($jasaPerbulan,2);

            $asuransi = $jenisPinjaman->asuransi;
            $asuransi = round($pengajuan->besar_pinjam * $asuransi, 2);

            $totalAngsuranBulan = $angsuranPerbulan+$jasaPerbulan;

            $provisi = $jenisPinjaman->provisi;
            $provisi = round($pengajuan->besar_pinjam * $provisi,2);
        
            // biaya administrasi
            $biayaAdministrasi = 0;
            $simpinRule = SimpinRule::find(SIMPIN_RULE_ADMINISTRASI);
            if ($pengajuan->besar_pinjam >= $simpinRule->value)
            {
                $biayaAdministrasi = $simpinRule->amount;
            }
            
            // get next serial number
            $nextSerialNumber = self::getSerialNumber(Carbon::now()->format('d-m-Y'));
           
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
            $pinjaman->lama_angsuran = $lamaAngsuran;
            $pinjaman->sisa_angsuran = $lamaAngsuran;
            $pinjaman->sisa_pinjaman = $pengajuan->besar_pinjam;
            $pinjaman->biaya_jasa = $pengajuan->biaya_jasa;
            $pinjaman->biaya_asuransi = $pengajuan->biaya_asuransi;
            $pinjaman->biaya_provisi = $pengajuan->biaya_provisi;
            $pinjaman->biaya_administrasi = $pengajuan->biaya_administrasi;
            $pinjaman->biaya_jasa_topup = $pengajuan->biaya_jasa_topup;
            /* $pinjaman->biaya_jasa = $jasaPerbulan;
            $pinjaman->biaya_asuransi = $asuransi;
            $pinjaman->biaya_provisi = $provisi;
            $pinjaman->biaya_administrasi = $biayaAdministrasi; */
            $pinjaman->u_entry = Auth::user()->name;
            $pinjaman->tgl_entri = Carbon::now();
            $pinjaman->tgl_tempo = Carbon::now()->addMonths($lamaAngsuran);
            $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
            // dd($pinjaman);
            $pinjaman->save();
            event(new PinjamanCreated($pinjaman));
            
            // changed, jurnal setelah pengajuan pinjaman di terima
            // JurnalManager::createJurnalPinjaman($pinjaman);
        }
        catch (\Exception $e)
        {
            \Log::error($e);
        }
    }

    static function pembayaranPinjamanDipercepat(Pinjaman $pinjaman)
    {
        try
        {
            $listAngsuran = $pinjaman->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->sortBy('angsuran_ke')->values();
            foreach ($listAngsuran as $angsuran) {
                $angsuran->besar_pembayaran = $angsuran->totalAngsuran;
                $angsuran->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                $angsuran->paid_at = Carbon::now();
                $angsuran->u_entry = Auth::user()->name;
                $angsuran->save();

                $pinjaman->sisa_angsuran = 0;
                $pinjaman->sisa_pinjaman = 0;
                $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                $pinjaman->save();
            }
        }
        catch (\Throwable $e)
        {
            Log::error($e);
        }
    }

    /**
     * get serial number on pinjaman table.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public static function getSerialNumber($date)
    {        
        try
        {
            $nextSerialNumber = 1;

            // get date
           
            $year = $date->year;

            // get pinjaman data on this year
            $lastPinjaman = Pinjaman::whereYear('tgl_entri', '=', $year)
                                        ->orderBy('serial_number', 'desc')
                                        ->first();
            if($lastPinjaman)
            {
                $nextSerialNumber = $lastPinjaman->serial_number + 1;
            }

            return $nextSerialNumber;
        }
        catch(\Exception $e)
        {
            \Log::info($e->getMessage());
            return false;
        }
    }

    public static function getSerialNumberKredit($date)
    {        
        try
        {
            $nextSerialNumber = 1;

            // get date
            $year = $date->year;

            // get pinjaman data on this year
            $lastPinjaman = Pinjaman::whereYear('tgl_transaksi', '=', $year)
                                        ->orderBy('serial_number_kredit', 'desc')
                                        ->first();
            if($lastPinjaman)
            {
                $nextSerialNumber = $lastPinjaman->serial_number + 1;
            }

            return $nextSerialNumber;
        }
        catch(\Exception $e)
        {
            \Log::info($e->getMessage());
            return false;
        }
    }

    public static function updateTglPinjaman(Pengajuan $pengajuan)
    {
        $pinjaman = $pengajuan->pinjaman;
        $pinjaman->tgl_entri = $pengajuan->tgl_transaksi;
        $pinjaman->tgl_tempo = $pengajuan->tgl_transaksi->addMonths($pengajuan->tenor);
        $pinjaman->save();
    }
}