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

class Migration2Controller extends Controller
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

            $jurnals = JurnalTemp::whereMonth('tgl_posting', '=', $bulan)->where('is_success', 0)
            ->orderBy('tgl_posting','asc')
            ->orderBy('kd_bukti','desc')
            ->get()
            ->unique('unik_bukti');
           //dd($jurnals);die;
            $jenisSimpanan = JenisSimpanan::pluck('kode_jenis_simpan')->toArray();
            $jenisPinjaman = JenisPinjaman::pluck('kode_jenis_pinjam')->toArray();
            

            foreach ($jurnals as $key => $jurnal)
            {

                $transactions = JurnalTemp::where('unik_bukti', $jurnal->unik_bukti)->whereMonth('tgl_posting', '=', $bulan)->where('is_success', 0)->get();
                if($transactions->count()>0){
                
                $nextSerialNumber = MigrationManager::getSerialNumber($transactions[0]->tgl_posting->format('d-m-Y'));
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
                    $ceksimpanan=in_array($newCoa, $jenisSimpanan);
                    $cekpinjaman=in_array($newCoa, $jenisPinjaman);
                    if ($ceksimpanan) {
                     $status=self::transaksisimpanan($kredit);
                 }else   
                 if ($cekpinjaman)  {

                    $cekpelunasan = JurnalTemp::wherein('code',[70102016,70102017,70102002,70102014,70102015])->where('unik_bukti',$kredit->unik_bukti)->get();
                    //dd($cekpelunasan);die;
                    //$topup= $uraian->wherein('code',[70102016,70102017]);
                    if ($cekpelunasan->count()>0){
                     $status=self::transaksipelunasandipercepat($kredit); 
                 }else{
                    $status=self::transaksiangsuran($kredit);    
                }
            } else{
                echo "Buffer";
            }  

            $kredits=JurnalTemp::find($kredit->id);
            if ($status[0]){
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
                $kredits->is_success=1;
                $kredits->serial_number=$nextSerialNumber;
                $kredits->save();
            }else{
                $kredits->is_success=2;
                $kredits->keterangan_gagal=$status[1];
                $kredits->save();
            }
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
            $ceksimpanan=in_array($newCoa, $jenisSimpanan);
            $cekpinjaman=in_array($newCoa, $jenisPinjaman);
            if ($ceksimpanan) {
             if ($debet->code=='411.01.000' ||$debet->code=='411.02.000'){
                $status=self::transaksikeluaranggota($debet);
            }else{
                $status=self::transaksipenarikan($debet);
            }

        }else   
        if ($cekpinjaman)  {


            $perlengkapan=JurnalTemp::wherein('code',[40404000,70102004,70102011])
            ->where('unik_bukti',$debet->unik_bukti)->pluck('jumlah','code')->toArray();
            $debet->perlengkapan=$perlengkapan;
            
            $status=self::transaksipinjaman($debet);  
               

        } else{
            echo "Buffer";
        }  


        $debets=JurnalTemp::find($debet->id);
        if ($status[0]){
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

            $debets->is_success=1;
            $debets->serial_number=$nextSerialNumber;
            $debets->save();
        }else{
            $debets->is_success=2;
            $debets->keterangan_gagal=$status[1];
            $debets->save();
        }

    }





}
}

echo('DONE');

} catch (\Exception $e) {
    dd($e);
}
}


public static function transaksisimpanan($simpan){
    echo ('transaksisimpanan');
    $status =true;
    if ($simpan->periode){
        $per = $simpan->periode;
    }else{
       $per = $simpan->tgl_posting;
    }
    $jenisSimpanans = JenisSimpanan::where('kode_jenis_simpan', $simpan->code)->first();
    $cek = Simpanan::where('kode_anggota',$simpan->kode_anggota)
            ->where('periode',$per)
            ->where('kode_jenis_simpan',$simpan->code)
            ->get();
            if($cek->count()>0){
                $oldPer =new Carbon($cek->max('periode')); 
                $newPer = $oldPer->addMonth();
                // dd($newPer);

                $simpanan = new Simpanan();
                $simpanan->jenis_simpan = strtoupper($jenisSimpanans->nama_simpanan);
    $simpanan->besar_simpanan = $simpan->jumlah;
    $simpanan->kode_anggota = $simpan->kode_anggota;
    $simpanan->u_entry = 'Admin BTB';
    $simpanan->tgl_entri = $simpan->tgl_posting;
    $simpanan->periode = $newPer;
    $simpanan->kode_jenis_simpan = $simpan->code;
    $simpanan->keterangan = ($simpan->uraian_3!==null)?$simpan->uraian_3:'';
    $simpanan->save();
    $status=true;
    $msg='';

            }else{

    $simpanan = new Simpanan();
    $simpanan->jenis_simpan = strtoupper($jenisSimpanans->nama_simpanan);
    $simpanan->besar_simpanan = $simpan->jumlah;
    $simpanan->kode_anggota = $simpan->kode_anggota;
    $simpanan->u_entry = 'Admin BTB';
    $simpanan->tgl_entri = $simpan->tgl_posting;
    $simpanan->periode = $per;
    $simpanan->kode_jenis_simpan = $simpan->code;
    $simpanan->keterangan = ($simpan->uraian_3!==null)?$simpan->uraian_3:'';
    $simpanan->save();
    $status=true;
    $msg='';
    }
    return [$status,$msg];

    
}
public static function transaksiangsuran($pinjamans){
    echo ('transaksiangsuran');
    $status =true;
    $pinjaman= Pinjaman::where('kode_jenis_pinjam',$pinjamans->code)
    ->where('kode_anggota',$pinjamans->kode_anggota)
    ->where('id_status_pinjaman',1)
    ->first();
    if($pinjaman){
        $dataAngsuran = Angsuran::where('kode_pinjam',$pinjaman->kode_pinjam)
        ->where('id_status_angsuran',1);
        $angsuran = Angsuran::where('kode_pinjam',$pinjaman->kode_pinjam)
        ->where('angsuran_ke', $dataAngsuran->min('angsuran_ke'))
        ->first();
        
        if ($angsuran)
        {
            $pembayaran = $pinjamans->jumlah+$angsuran->jasa;
            if ($angsuran->besar_pembayaran) {
                $pembayaran = $pembayaran + $angsuran->besar_pembayaran;
            }
            if ($pembayaran >= $angsuran->totalAngsuran-5) {
                $angsuran->besar_pembayaran = $angsuran->totalAngsuran;
                $angsuran->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                $pinjaman->sisa_angsuran = $pinjaman->sisa_angsuran - 1;
                $pinjaman->save();
            } else {
                $angsuran->besar_pembayaran = $pembayaran;
            }


            $pembayaran = $pembayaran - $angsuran->totalAngsuran;
            $angsuran->paid_at = $pinjamans->tgl_posting;
            $angsuran->updated_by = 1;
            $angsuran->id_akun_kredit = null;
            $status=($angsuran->save());

            // create JKM angsuran
            

            if ($pembayaran <= 0) {
                $angsuran->sisa_pinjam = $angsuran->sisa_pinjam - $angsuran->besar_angsuran;
                $angsuran->save();
                $pinjaman->sisa_pinjaman = $angsuran->sisa_pinjam;
                $pinjaman->save();
                foreach ($dataAngsuran as $dataangsur) {
                   $dataangsur->sisa_pinjam=$angsuran->sisa_pinjam;
                   $dataangsur->save();
                }
            }
            if ($pinjaman->sisa_pinjaman <= 0) {
                $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                $pinjaman->save();
            }
        }
    }else{
        return [false,'Pinjaman Tidak Ditemukan'];
    }
    return [$status,''];

}
public static function transaksipelunasandipercepat($pinjamans){
    echo ('transaksipelunasandipercepat');
    $status =true;
    $pinjaman= Pinjaman::where('kode_jenis_pinjam',$pinjamans->code)
    ->where('kode_anggota',$pinjamans->kode_anggota)
    ->where('id_status_pinjaman',1)
    ->first();
    //dd($pinjaman);die;
    if($pinjaman){
        $angsurans = Angsuran::where('kode_pinjam',$pinjaman->kode_pinjam)
        ->where('id_status_angsuran',1)->get();
        foreach ($angsurans as $angsuran) {

            $angsuran->sisa_pinjam=0;
            $angsuran->id_status_angsuran=2;
            $angsuran->updated_by=1;
            $angsuran->updated_at=$pinjamans->tgl_posting;
            $angsuran->save();
        }

        $pinjaman->sisa_pinjaman=0;
        $pinjaman->id_status_pinjaman=2;
        $pinjaman->updated_by=1;
        $pinjaman->updated_at=$pinjamans->tgl_posting;
        $pinjaman->save();

    }else{
        return [false,'Pinjaman Tidak Ditemukan'];
    }

    return [$status,''];
}

public static function transaksipenarikan($simpanans){
     echo ('transaksipenarikan');
    $status =true;
    if($simpanans->kode_anggota>0){
        $penarikan = new Penarikan();
        $penarikan->kode_anggota = $simpanans->kode_anggota;
        $penarikan->kode_tabungan = $simpanans->kode_anggota;
        $penarikan->id_tabungan = $simpanans->kode_anggota.$simpanans->code;
        $penarikan->besar_ambil = $simpanans->jumlah;
        $penarikan->code_trans = $simpanans->code;
        $penarikan->tgl_ambil = $simpanans->tgl_posting;
        $penarikan->keterangan = ($simpanans->uraian_3!==null)?$simpanans->uraian_3:'';
            //$penarikan->id_akun_debet = null;
        $penarikan->paid_by_cashier = 1;
        $penarikan->u_entry = 1;
        $penarikan->created_by = 1;
        $penarikan->status_pengambilan = 8;
        $penarikan->save();
    }else{
        return [false,'Anggota Kosong'];
    }

    return [$status,''];
}
public static function transaksikeluaranggota($simpanans){
     echo ('transaksikeluaranggota');
    $status =true;
    if($simpanans->kode_anggota>0){
        $penarikan = new Penarikan();
        $penarikan->kode_anggota = $simpanans->kode_anggota;
        $penarikan->kode_tabungan = $simpanans->kode_anggota;
        $penarikan->id_tabungan = $simpanans->kode_anggota.$simpanans->code;
        $penarikan->besar_ambil = $simpanans->jumlah;
        $penarikan->code_trans = $simpanans->code;
        $penarikan->tgl_ambil = $simpanans->tgl_posting;
        $penarikan->keterangan =($simpanans->uraian_3!==null)?$simpanans->uraian_3:'';
            //$penarikan->id_akun_debet = null;
        $penarikan->paid_by_cashier = 1;
        $penarikan->u_entry = 1;
        $penarikan->created_by = 1;
        $penarikan->status_pengambilan = 8;
        $penarikan->save();

        $anggota = Anggota::find($simpanans->kode_anggota);
        if($anggota){
         $anggota->status='keluar';
         $anggota->save();
     }
     $users = User::where('kode_anggota',$simpanans->kode_anggota)->first();
     if ($users){
        $users->deleted_at=Carbon::now();
        $users->is_verified=0;
        $users->save();
    }


}else{
    return [false,'Anggota Kosong'];
}
return [$status,''];
}
public static function transaksitopup($pinjamans){
    $status =true;
    return [$status,''];
}
public static function transaksipinjaman($pinjamans){
    echo ('transaksipinjaman');
    $status =true;
    $jenis = JenisPinjaman::where('kode_jenis_pinjam',$pinjamans->code)->first();
    $lama_angsuran = $jenis->lama_angsuran;
     $kodeAnggota = $pinjamans->kode_anggota;
    $kodePinjaman = str_replace('.', '', $pinjamans->code) . '-' . $kodeAnggota . '-' . $pinjamans->tgl_posting;
    $cekpinjaman=Pinjaman::where('kode_pinjam', $kodePinjaman)
    ->first();
    if($cekpinjaman){
        return [true,'sudah ada'];
    }
    //dd($pinjamans->perlengkapan);die;
    $pinjaman = new Pinjaman();
   
    $pinjaman->kode_pinjam = $kodePinjaman;
    $pinjaman->kode_pengajuan_pinjaman = $kodePinjaman;
    $pinjaman->kode_anggota = $kodeAnggota;
    $pinjaman->kode_jenis_pinjam = $pinjamans->code;
    $pinjaman->besar_pinjam = $pinjamans->jumlah;
    $pinjaman->besar_angsuran_pokok = $pinjaman->besar_pinjam / $lama_angsuran;
    $pinjaman->lama_angsuran = $lama_angsuran;
    $pinjaman->sisa_angsuran = $lama_angsuran;
    $pinjaman->sisa_pinjaman = $pinjaman->besar_pinjam ;
    $pinjaman->biaya_jasa = $jenis->jasa*$pinjaman->besar_pinjam;
    $pinjaman->besar_angsuran =$pinjaman->besar_angsuran_pokok + $pinjaman->biaya_jasa;
    $pinjaman->biaya_asuransi = (array_key_exists(40404000,$pinjamans->perlengkapan))?$pinjamans->perlengkapan[40404000]:0;
    $pinjaman->biaya_provisi = (array_key_exists(70102004,$pinjamans->perlengkapan))?$pinjamans->perlengkapan[70102004]:0;
    $pinjaman->biaya_administrasi = (array_key_exists(70102011,$pinjamans->perlengkapan))?$pinjamans->perlengkapan[70102011]:0;
    $pinjaman->u_entry = 1;
    $pinjaman->tgl_entri = $pinjamans->tgl_posting;
    $pinjaman->tgl_tempo = $pinjamans->tgl_posting->addMonths($lama_angsuran - 1);
    $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
    $pinjaman->keterangan = 'Pinjaman Baru Mutasi Simkop';
    $pinjaman->save();
    for ($i = 0; $i <= $pinjaman->sisa_angsuran - 1; $i++)
    {
        $jatuhTempo = $pinjamans->tgl_posting->addMonths($i)->endOfMonth();
        $sisaPinjaman = $pinjaman->sisa_pinjaman;
        $angsuran = new Angsuran();
        $angsuran->kode_pinjam = $pinjaman->kode_pinjam;
        $angsuran->angsuran_ke = $pinjaman->lama_angsuran - $pinjaman->sisa_angsuran + $i + 1;
        $angsuran->besar_angsuran = $pinjaman->besar_angsuran_pokok;
        $angsuran->denda = 0;
        $angsuran->jasa = $pinjaman->biaya_jasa;
        $angsuran->kode_anggota = $pinjaman->kode_anggota;
        $angsuran->sisa_pinjam = $sisaPinjaman;
        $angsuran->tgl_entri = $pinjaman->tgl_entri;
        $angsuran->jatuh_tempo = $jatuhTempo;
        $angsuran->u_entry = 1;
        $angsuran->save();
    }
    return [$status,''];
}


public static function jenisPinjaman(){
    $pinjamans = JenisPinjaman::selectraw("replace(kode_jenis_pinjam,'.','')as code")->get();
    $codes=[];
    foreach($pinjamans as $pinjaman)
    {
        $newCode = $pinjaman->code;

        $codes[] = $newCode;
    }
    return $codes;
}
public static function jenisSimpanan(){
    $simpanans = JenisSimpanan::selectraw("replace(kode_jenis_simpan,'.','')as code")->get();
    $codes=[];
    foreach($simpanans as $simpanan)
    {
        $newCode = $simpanan->code;

        $codes[] = $newCode;
    }
    return $codes;
}




}
