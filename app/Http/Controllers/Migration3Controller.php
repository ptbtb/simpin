<?php

namespace App\Http\Controllers;

use App\Managers\TabunganManager;
use App\Managers\SimpananManager;
use App\Managers\PenarikanManager;
use App\Managers\PinjamanManager;
use App\Managers\AngsuranManager;
use App\Managers\MigrationManager;

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
use App\Models\User;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use App\Models\Jurnal;

use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Auth;

class Migration3Controller extends Controller
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

            $jurnals = JurnalTemp::whereMonth('tgl_posting', '=', $bulan)->where('sync2', 0)
            ->orderBy('tgl_posting','asc')
            ->orderBy('kd_bukti','desc')
            ->get()
            ->unique('unik_bukti');
           //dd($jurnals);die;
            $jenisSimpanan = JenisSimpanan::pluck('kode_jenis_simpan')->toArray();
            $jenisPinjaman = JenisPinjaman::pluck('kode_jenis_pinjam')->toArray();
            

            foreach ($jurnals as $key => $jurnal)
            {

                $transactions = JurnalTemp::where('unik_bukti', $jurnal->unik_bukti)->whereMonth('tgl_posting', '=', $bulan)->where('sync2', 0)->get();
                if($transactions->count()>0){
                
               $nextnomor = $transactions[0]->tgl_posting->format('Ymd').(Jurnal::count()+1);
                }
                

                // group by uraian3 because 1 kode_bukti has more than 1 transaction
                $groupByUraian = $transactions->groupBy('unik_bukti');

                foreach ($groupByUraian as $key => $uraian)
                {
                    // $topup= $uraian->wherein('code',[70102002,70102014,70102015]);
                    // if($topup){
                    //     echo 'ada';die;
                    // }else{
                    //     echo 'ada';die;
                    // }

                 $kredits = $uraian->wherein('normal_balance',2);
                 $debets = $uraian->wherein('normal_balance',1);

// transaksi kredit

                 foreach ($kredits as $kredit) {
                    $status=[true,''];
                    $newCoa = substr($kredit->code,0,3).'.'.substr($kredit->code,3,2).'.'.substr($kredit->code,5,3);
                    $kredit->code=$newCoa;
                    if($kredit->kd_bukti=='JKM'){
                        $idTipeJurnal = TIPE_JURNAL_JKM;
                        
                    }elseif($kredit->kd_bukti=='JKK'){
                        $idTipeJurnal = TIPE_JURNAL_JKK;
                        
                    }elseif($kredit->kd_bukti=='JR'){
                        $idTipeJurnal = TIPE_JURNAL_JU;
                        
                    }

// cek jenis transaksi
                   
                echo "Buffer";
            

            $kredits=JurnalTemp::find($kredit->id);
            
                $newJurnal = new Jurnal();
                $newJurnal->id_tipe_jurnal = $idTipeJurnal;
                // $newJurnal->nomer =$kredit->no_bukti;
                $newJurnal->nomer =$nextnomor;

                                        // new format for code


                                        // debet

                $newJurnal->akun_debet = 0;
                $newJurnal->debet = 0;
                $newJurnal->akun_kredit = $newCoa;
                $newJurnal->kredit = $kredit->jumlah;

                $newJurnal->keterangan = $kredit->uraian_1.'|'.$kredit->uraian_2.'|'.$kredit->uraian_3;
                $newJurnal->created_by = 1;
                $newJurnal->updated_by = 1;
                $newJurnal->created_at = $kredit->tgl_posting;
                $newJurnal->tgl_transaksi = $kredit->tgl_posting;
                $kredit->jurnals()->save($newJurnal); 
                $kredits->sync2=1;
                $kredits->save();
            
        }

// transaksi DEBIT

        foreach ($debets as $debet) {
            $status=[true,''];
            $newCoa = substr($debet->code,0,3).'.'.substr($debet->code,3,2).'.'.substr($debet->code,5,3);
            $debet->code = $newCoa;
            if($debet->kd_bukti=='JKM'){
                $idTipeJurnal = TIPE_JURNAL_JKM;

            }elseif($debet->kd_bukti=='JKK'){
                $idTipeJurnal = TIPE_JURNAL_JKK;

            }elseif($debet->kd_bukti=='JR'){
                $idTipeJurnal = TIPE_JURNAL_JU;

            }
            
            echo "Buffer";
        


        $debets=JurnalTemp::find($debet->id);
             $newJurnal = new Jurnal();
            $newJurnal->id_tipe_jurnal = $idTipeJurnal;
            // $newJurnal->nomer = $debet->no_bukti;
            $newJurnal->nomer = $nextnomor;
                                        // debet

            $newJurnal->akun_debet = $newCoa;
            $newJurnal->debet = $debet->jumlah;
            $newJurnal->akun_kredit = 0;
            $newJurnal->kredit = 0;

            $newJurnal->keterangan = $debets->uraian_1.'|'.$debets->uraian_2.'|'.$debets->uraian_3;
            $newJurnal->created_by = 1;
            $newJurnal->updated_by = 1;
            $newJurnal->created_at = $debet->tgl_posting;
            $newJurnal->tgl_transaksi = $debet->tgl_posting;
            $debet->jurnals()->save($newJurnal);
            $debets->sync2=1;
            $debets->save();

           

    }





}
}

echo('DONE');

} catch (\Exception $e) {
    dd($e);
}
}

}
