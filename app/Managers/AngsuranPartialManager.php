<?php
namespace App\Managers;

use App\Managers\JurnalManager;
use App\Models\AngsuranPartial;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AngsuranPartialManager 
{
    public static function generate(Angsuran $angsuran,$pembayaran=NULL)
    {      
        DB::beginTransaction();  
        try
        {
            if(!$pembayaran){
                $pembayaran = $angsuran->besar_pembayaran;
            }
            if ($angsuran->angsuranPartial){
                $angs = $angsuran->angsuranPartial;
                $jasalama = $angs->sum('jasa');
                $bsangslama = $angs->sum('besar_angsuran');
                $bayarlama = $angs->sum('besar_pembayaran');
                Log::info('lama ');
                 

            }else{
                $angsp = new AngsuranPartial();
                $jasalama = 0;
                $bsangslama = 0;
                $bayarlama = 0;
                Log::info('baru ');
            }
            $jasabaru = $angsuran->jasa - $jasalama;
            $bsbaru = $angsuran->besar_angsuran - $bsangslama;
            $bayar =  $pembayaran- $bayarlama-$bayarlama;
            $bayarnya =  $bayar;
            // dd($bayarnya);


           if ($jasabaru>0) {
            if($bayar - $jasabaru>=0){
                $jasapay = $jasabaru;
                $bayar = $bayar-$jasapay;
             }else{
                $jasapay = $bayar;
                $bayar=0;
             }
           }else{
            $jasapay=0;
           }
             

             if ($bayar>0){
                if($bayar - $bsbaru>=0){
                $bspay = $bsbaru;
                $bayar = $bayar-$bspay;
             }else{
                $bspay = $bayar;
                $bayar=0;
             }
             }else{
                $bspay =0;
             }

             $pembayaran_final = $jasabaru+$bspay;
               // throw new \Exception('jasa  '.$jasapay.' bayar kas '.$bayarnya.' angsurannya '.$bspay);

             $angsp = new AngsuranPartial();
              $serialNumber = static::getSerialNumber(Carbon::parse($angsuran->tgl_transaksi)->format('d-m-Y'));
             $angsp->kode_angsur = $angsuran->kode_angsur;
             $angsp->kode_anggota = $angsuran->kode_anggota;
             $angsp->jasa = $jasapay;
             $angsp->besar_angsuran = $bspay;
             $angsp->besar_pembayaran = $pembayaran_final;
             $angsp->tgl_transaksi = $angsuran->tgl_transaksi;
             $angsp->keterangan = $angsuran->keterangan;
             $angsp->id_akun_kredit = $angsuran->id_akun_kredit;
             $angsp->serial_number = $serialNumber;

             $angsp->save();
             \Log::info('kesave ');
             JurnalManager::createJurnalAngsuranPartial($angsp);




        DB::commit();
       
    }
    catch(\Exception $e)
    {
        DB::rollback();
        \Log::info($e->getMessage());
        throw new \Exception($e->getMessage());
    }
}

public static function generateFromEdit(Angsuran $angsuran)
    {      
        DB::beginTransaction();  
        try
        {
            // if(!$pembayaran){
                $pembayaran = $angsuran->besar_pembayaran;
            // }
            if ($angsuran->angsuranPartial){
                foreach ($angsuran->angsuranPartial as $angsp){
                    $angsp->jurnals()->delete();
                }
                
                $angsuran->angsuranPartial()->delete();
                
            }
                $angsp = new AngsuranPartial();
                $jasalama = 0;
                $bsangslama = 0;
                $bayarlama = 0;
                Log::info('baru ');
            
            $jasabaru = $angsuran->jasa - $jasalama;
            $bsbaru = $angsuran->besar_angsuran - $bsangslama;
            $bayar =  $pembayaran- $bayarlama-$bayarlama;
            $bayarnya =  $bayar;
            // dd($bayarnya);


           if ($jasabaru>0) {
            if($bayar - $jasabaru>=0){
                $jasapay = $jasabaru;
                $bayar = $bayar-$jasapay;
             }else{
                $jasapay = $bayar;
                $bayar=0;
             }
           }else{
            $jasapay=0;
           }
             

             if ($bayar>0){
                if($bayar - $bsbaru>=0){
                $bspay = $bsbaru;
                $bayar = $bayar-$bspay;
             }else{
                $bspay = $bayar;
                $bayar=0;
             }
             }else{
                $bspay =0;
             }

             $pembayaran_final = $jasabaru+$bspay;
               // throw new \Exception('jasa  '.$jasapay.' bayar kas '.$bayarnya.' angsurannya '.$bspay);

             $angsp = new AngsuranPartial();
              $serialNumber = static::getSerialNumber(Carbon::parse($angsuran->tgl_transaksi)->format('d-m-Y'));
             $angsp->kode_angsur = $angsuran->kode_angsur;
             $angsp->kode_anggota = $angsuran->kode_anggota;
             $angsp->jasa = $jasapay;
             $angsp->besar_angsuran = $bspay;
             $angsp->besar_pembayaran = $pembayaran_final;
             $angsp->tgl_transaksi = $angsuran->tgl_transaksi;
             $angsp->keterangan = $angsuran->keterangan;
             $angsp->id_akun_kredit = $angsuran->id_akun_kredit;
             $angsp->serial_number = $serialNumber;

             $angsp->save();
             \Log::info('kesave ');
             JurnalManager::createJurnalAngsuranPartial($angsp);




        DB::commit();
       
    }
    catch(\Exception $e)
    {
        DB::rollback();
        \Log::info($e->getMessage());
        throw new \Exception($e->getMessage());
    }
}

public static function generatetanpaposting(Angsuran $angsuran,$pembayaran=NULL)
    {      
        DB::beginTransaction();  
        try
        {
            if(!$pembayaran){
                $pembayaran = $angsuran->besar_pembayaran;
            }
            if ($angsuran->angsuranPartial){
                $angs = $angsuran->angsuranPartial;
                $jasalama = $angs->sum('jasa');
                $bsangslama = $angs->sum('besar_angsuran');
                $bayarlama = $angs->sum('besar_pembayaran');
                 

            }else{
                $angsp = new AngsuranPartial();
                $jasalama = 0;
                $bsangslama = 0;
                $bayarlama = 0;
            }
            $jasabaru = $angsuran->jasa - $jasalama;
            $bsbaru = $angsuran->besar_angsuran - $bsangslama;
            $bayar =  $angsuran->besar_pembayaran- $bayarlama;
            $bayarnya =  $bayar;
          


           if ($jasabaru>0) {
            if($bayar - $jasabaru>=0){
                $jasapay = $jasabaru;
                $bayar = $bayar-$jasapay;
             }else{
                $jasapay = $bayar;
                $bayar=0;
             }
           }else{
            $jasapay=0;
           }
             

             if ($bayar>0){
                if($bayar - $bsbaru>=0){
                $bspay = $bsbaru;
                $bayar = $bayar-$bspay;
             }else{
                $bspay = $bayar;
                $bayar=0;
             }
             }else{
                $bspay =0;
             }
               // throw new \Exception('jasa  '.$jasapay.' bayar kas '.$bayarnya.' angsurannya '.$bspay);
             $pembayaran_final = $jasabaru+$bspay;
             $angsp = new AngsuranPartial();
              $serialNumber = static::getSerialNumber(Carbon::parse($angsuran->tgl_transaksi)->format('d-m-Y'));
             $angsp->kode_angsur = $angsuran->kode_angsur;
             $angsp->kode_anggota = $angsuran->kode_anggota;
             $angsp->jasa = $jasapay;
             $angsp->besar_angsuran = $bspay;
             $angsp->besar_pembayaran = $pembayaran_final;
             $angsp->tgl_transaksi = $angsuran->tgl_transaksi;
             $angsp->keterangan = $angsuran->keterangan;
             $angsp->id_akun_kredit = $angsuran->id_akun_kredit;
             $angsp->serial_number = $serialNumber;
             $angsp->created_at = $angsuran->created_at;
             $angsp->created_by = $angsuran->created_by;
             $angsp->updated_at = $angsuran->updated_at;
             $angsp->updated_by = $angsuran->updated_by;

             $angsp->save();

            

        DB::commit();
       
    }
    catch(\Exception $e)
    {
        DB::rollback();
        \Log::info($e->getMessage());
        throw new \Exception($e->getMessage());
    }
}


    /**
     * get serial number on angsuran table.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public static function getSerialNumber($date)
    {        
        try
        {
            $nextSerialNumber = 1;

             $date = Carbon::createFromFormat('d-m-Y', $date);
            $year = $date->year;

            // get angsuran data on this year
            $lastAngsuran = AngsuranPartial::whereYear('tgl_transaksi', '=', $year)
            ->orderBy('serial_number', 'desc')
            ->first();
            if($lastAngsuran)
            {
                $nextSerialNumber = $lastAngsuran->serial_number + 1;
            }

            return $nextSerialNumber;
        }
        catch(\Exception $e)
        {
            \Log::info($e->getMessage());
            return false;
        }
    }
}