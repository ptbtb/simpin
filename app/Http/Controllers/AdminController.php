<?php
namespace App\Http\Controllers;

use App\Exports\KartuSimpananExport;
use App\Exports\LaporanExcelExport;
use App\Exports\SimpananExport;
use App\Imports\SimpananImport;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Jurnal;
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
	public function index(){
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
        $duplicateIds = Simpanan::
                    selectRaw("kode_anggota,kode_jenis_simpan,periode,min(kode_simpan) as kode_simpan ")
                    ->wherein('kode_jenis_simpan',['411.12.000','502.01.000'])
                    ->whereraw("periode>'2021-10-01'")
                    ->groupBy("kode_anggota","kode_jenis_simpan","periode")
                    ->havingRaw('count(periode) > ?', [1])
                    // ->toSql();
                     ->pluck("kode_simpan");
                   
       // dd($duplicateIds);
       
        foreach ($duplicateIds as $id){
            $simpanannya = Simpanan::find($id);
            if ($simpanannya->jurnals->count()>0){
                $simpanannya->jurnals[0]->delete();
            }
            $simpanannya->delete();
             // dd($id);

        }
	}
}