<?php
namespace App\Http\Controllers;

use App\Exports\KartuSimpananExport;
use App\Exports\LaporanExcelExport;
use App\Exports\SimpananExport;
use App\Imports\SimpananImport;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\JenisSimpanan;
use App\Models\Jurnal;
use App\Models\JurnalTemp;
use App\Models\Penarikan;
use App\Models\Simpanan;
use App\Models\Code;
use Illuminate\Http\Request;

use App\Managers\JurnalManager;
use App\Managers\SimpananManager;
use App\Models\Company;
use Auth;
use DB;
use Hash;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Log;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
	public function cleanDoubleSimpanan(){
		// $listAnggota = Anggota::with('listSimpanan')
  //                           ->whereDoesntHave('listSimpanan', function ($query)
  //                           {
  //                               return $query->where('periode', '2021-01-01')
  //                               ->where('kode_jenis_simpan','411.12.000');
  //                           })
  //                           ->get();

        // $simpanan = Simpanan::where('u_entry','<>','Admin BTB')
        // ->where('mutasi',0)
        // ->whereDoesntHave('jurnals')->get();
        // $duplicateIds = Simpanan::
        //             selectRaw("kode_anggota,kode_jenis_simpan,periode,min(kode_simpan) as kode_simpan ")
        //             ->wherein('kode_jenis_simpan',['411.12.000','502.01.000'])
        //             ->whereraw("periode>'2021-10-01'")
        //             ->groupBy("kode_anggota","kode_jenis_simpan","periode")
        //             ->havingRaw('count(periode) > ?', [1])
        //             // ->toSql();
        //              ->pluck("kode_simpan");
                     $duplicateIds = Simpanan::
                    whereraw("date(created_at) ='2021-12-03'")
                    ->where('u_entry','Selly Amalia')
                    ->where('tgl_transaksi','2020-11-30')
                      // ->toSql();
                      ->pluck("kode_simpan");
                   
       
        foreach ($duplicateIds as $id){
            $simpanannya = Simpanan::find($id);
            if ($simpanannya->jurnals->count()>0){
                $simpanannya->jurnals[0]->delete();
            }
            $simpanannya->delete();
             // dd($id);

        }
	}

    public function cleanDoublePenarikan(){
       
        $duplicateIds = Penarikan::
                    selectRaw("kode_anggota,code_trans,besar_ambil,min(kode_ambil) as kode_ambil,tgl_ambil ")
                    ->whereraw("created_at >'2021-12-03'")
                    ->where('status_pengambilan',8)
                    ->groupBy("kode_anggota","code_trans","besar_ambil",'tgl_ambil')
                    ->havingRaw('count(tgl_ambil) > ?', [1])
                     // ->toSql();
                     ->pluck("kode_ambil");
                   
       // dd($duplicateIds);
       
        foreach ($duplicateIds as $id){
            $trans = Penarikan::find($id);
            if ($trans->jurnals->count()>0){
                $trans->jurnals[0]->delete();
            }
            $trans->delete();
             // dd($id);

        }
    }

    public function cleanDoublePeriod(){
       
        // $listAnggota = Anggota::with('listSimpanan')
        //                     ->whereHave('listSimpanan', function ($query)
        //                     {
        //                         return $query->where('periode', '2021-01-01')
        //                         ->havingRaw('count(periode) > ?', [1]);
        //                     })
        //                     ->get();

        $listsimpanan=Simpanan::
                    selectRaw("kode_anggota,kode_jenis_simpan,periode,min(kode_simpan) as kode_simpan ")
                    ->wherein('kode_jenis_simpan',['411.12.000','502.01.000'])
                    ->where('mutasi',0)
                    ->groupBy("kode_anggota","kode_jenis_simpan","periode")
                    ->havingRaw('count(periode) > ?', [1])
                     ->toSql();
                     // ->pluck("kode_simpan");                            

        dd($listsimpanan);

        $simpanan = Simpanan::where('u_entry','<>','Admin BTB')
        ->where('mutasi',0)
        ->whereDoesntHave('jurnals')->get();
    }
    public function cekjreupload(){
       
        

        $trans=JurnalTemp::
                    where('is_success',0) 
                    ->whereDoesntHave('jurnals')
                     // ->toSql();
                      ->get();

       
    }

    public function ceksimpanannojurnal(){
       
        

        $trans=Simpanan::
                    where('mutasi',0) 
                    ->wherenotin('u_entry',['Admin BTB','System']) 
                    ->whereDoesntHave('jurnals')
                     // ->toSql();
                      ->get();                               

        dd($trans);

        
    }
    public function postsimpanannojurnal(){
       
        

        $trans=Simpanan::
                    where('mutasi',0) 
                     ->wherenotin('u_entry',['Admin BTB','System']) 
                    ->whereDoesntHave('jurnals')->limit(500)
                     // ->toSql();
                      ->get();                               

        foreach ($trans as $simpanan){
            if (is_null($simpanan->serial_number)){
                            $simpanan->serial_number = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                            $simpanan->save();
                        }
            JurnalManager::createJurnalSimpanan($simpanan);
        }

        
    }

    public function cekjurnalnotrans(){
       
        

        $trans=Jurnal::
                    whereDoesntHave('jurnalable')
                   -> wherein('jurnalable_type',['App\Models\Penarikan','App\Models\Simpanan','App\Models\Angsuran','App\Models\JurnalTemp'])
                     // ->toSql();
                      ->pluck("id");                               


       foreach ($trans as $id){
            $trans = Jurnal::find($id);
            $trans->delete();
             // dd($id);

        }
    }

    public function cekangsuran(){
       
        

        $trans=Angsuran::
                    where('serial_number','>',0)
                    ->where('updated_by','>',1)
                    ->where('tgl_transaksi','>','20210-09-30')
                    ->whereHas('jurnals')
                    // ->whereDoesntHave('jurnals')
                      // ->toSql();
                      // ->get();
                       ->pluck("kode_angsur");                               

      // dd($trans);
       foreach ($trans as $id){
            $angsurannya = Angsuran::find($id);
            if ($angsurannya->jurnals->count()>0){
                foreach($angsurannya->jurnals as $jrnl ){
                    $jrnl->delete();
                }
                
            }
            // dd($angsurannya);
            JurnalManager::createJurnalAngsuran($angsurannya);
             // dd($id);

        }
    }
}