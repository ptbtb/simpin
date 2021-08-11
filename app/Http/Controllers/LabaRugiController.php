<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use App\Models\CodeCategory;
use App\Exports\LabaRugiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\BukuBesarExport;

use Carbon\Carbon;
use Excel;
use DB;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            // $groupLabaRugi = ['702.02', '701.02', 101, 102, 103, 104, 105, 106, 107, 109, 110, 111, 201, 202, 203, 204, 205, 208, 210, 301, 302, 303, 304, 401, 
            //                 402, 403, 404, 405, 407, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 891, 791];
        $groupLabaRugi = CodeCategory::where('name','like','PENDAPATAN%')
                            ->orWhere('name','like','BIAYA%')
                            ->orWhere('name','like','HPP%')
                            ->get();

                           
            $codes = Code::where('is_parent', 0)
                            ->where(function ($query) use($groupLabaRugi) {
                                for ($i = 0; $i < count($groupLabaRugi); $i++){
                                $query->orWhere('code_category_id',  $groupLabaRugi[$i]->id );
                                }      
                            }) 
                             //->whereIn('code_category_id',[5,10,3,2,7])
                            ->get();
            // laba rugi collection
            $labaRugis = collect();
            $pendapatan = collect();
            $hpp = collect();
            $biayapegawai = collect();
            $biayaoperasional = collect();
            $biayaperawatan = collect();
            $biayapenyusutan = collect();
            $biayaadminum = collect();
            $biayapenyisihan = collect();
            


            $groupCodes = $codes->groupBy(function ($item, $key) {
                return $item['CODE'];
                // return substr($item['CODE'], 0, 8);
            });

            // period
            // check if period date has been selected
            if(!$request->period)
            {          
                $request->period = Carbon::today()->format('m-Y');
            }
            
            // create until before period month
            $request->lastMonthPeriod = Carbon::createFromFormat('m-Y', $request->period)->subMonth()->format('m-Y');

            // get start/end period (period's month)
            $startPeriod = Carbon::createFromFormat('m-Y', $request->period)->startOfMonth()->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            // get start/end until period's month
            $startUntilPeriod = Carbon::createFromFormat('m-Y', $request->period)->startOfYear()->format('Y-m-d');
            $endUntilPeriod = Carbon::createFromFormat   ('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            // get start/end until before period's month
            $startUntilBeforePeriod = Carbon::createFromFormat('m-Y', $request->lastMonthPeriod)->startOfYear()->format('Y-m-d');
            $endUntilBeforePeriod = Carbon::createFromFormat('m-Y', $request->lastMonthPeriod)->endOfMonth()->format('Y-m-d');
            
            foreach ($groupCodes as $key => $groupCode) 
            {
                $saldo = 0;
                $saldoUntilMonth = 0;
                $saldoUntilBeforeMonth = 0;
                foreach ($groupCode as $key1 => $code) 
                {
                    // get code's normal balance 
                    if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        // until period's month
                        $saldoDebetUntilPeriod = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('debet');
                        $saldoKreditUntilPeriod = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('kredit');

                        $saldoUntilMonth += $saldoDebetUntilPeriod;
                        $saldoUntilMonth -= $saldoKreditUntilPeriod;

                        // until before period's month
                        $saldoDebetUntilBeforeMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('debet');
                        $saldoKreditUntilBeforeMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('kredit');

                        $saldoUntilBeforeMonth += $saldoDebetUntilBeforeMonth;
                        $saldoUntilBeforeMonth -= $saldoKreditUntilBeforeMonth;
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        // until period's month
                        $saldoDebetUntilPeriod = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('debet');
                        $saldoKreditUntilPeriod = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('kredit');

                        $saldoUntilMonth -= $saldoDebetUntilPeriod;
                        $saldoUntilMonth += $saldoKreditUntilPeriod;

                        // until before period's month
                        $saldoDebetUntilBeforeMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('debet');
                        $saldoKreditUntilBeforeMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('kredit');

                        $saldoUntilBeforeMonth -= $saldoDebetUntilBeforeMonth;
                        $saldoUntilBeforeMonth += $saldoKreditUntilBeforeMonth;
                    }
                }

                    $parentCode = Code::where('CODE', $key)->first();
                    if($parentCode->codeCategory->name=='HPP'){
                        $hpp->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='PENDAPATAN'){
                        $pendapatan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PEGAWAI'){
                        $biayapegawai ->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA OPERASIONAL'){
                        $biayaoperasional  ->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PERAWATAN'){
                        $biayaperawatan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PENYUSUTAN'){
                        $biayapenyusutan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA ADMINISTRASI DAN UMUM'){
                        $biayaadminum->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PENYISIHAN'){
                        $penyisihan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }
                    
                
            }
            
            // year data
            $request->year = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y');
            $data['title'] = 'Laporan Laba Rugi';
            $data['labaRugis'] = $labaRugis;
            $data['pendapatan'] = $pendapatan;
            $data['hpp'] = $hpp;
            $data['biayapegawai'] = $biayapegawai;
            $data['biayaoperasional'] = $biayaoperasional;
            $data['biayaperawatan'] = $biayaperawatan;
            $data['biayapenyusutan'] = $biayapenyusutan;
            $data['biayaadminum'] = $biayaadminum;
            $data['biayapenyisihan'] = $biayapenyisihan;
            $data['request'] = $request;
            
            return view('laba_rugi.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function createExcel(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        try
        {
            // $groupLabaRugi = ['702.02', '701.02', 101, 102, 103, 104, 105, 106, 107, 109, 110, 111, 201, 202, 203, 204, 205, 208, 210, 301, 302, 303, 304, 401, 
            //                 402, 403, 404, 405, 407, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 891, 791];
        $groupLabaRugi = CodeCategory::where('name','like','PENDAPATAN%')
                            ->orWhere('name','like','BIAYA%')
                            ->orWhere('name','like','HPP%')
                            ->get();

                           
            $codes = Code::where('is_parent', 0)
                            ->where(function ($query) use($groupLabaRugi) {
                                for ($i = 0; $i < count($groupLabaRugi); $i++){
                                $query->orWhere('code_category_id',  $groupLabaRugi[$i]->id );
                                }      
                            }) 
                             //->whereIn('code_category_id',[5,10,3,2,7])
                            ->get();
            // laba rugi collection
            $labaRugis = collect();
            $pendapatan = collect();
            $hpp = collect();
            $biayapegawai = collect();
            $biayaoperasional = collect();
            $biayaperawatan = collect();
            $biayapenyusutan = collect();
            $biayaadminum = collect();
            $biayapenyisihan = collect();
            


            $groupCodes = $codes->groupBy(function ($item, $key) {
                return $item['CODE'];
                // return substr($item['CODE'], 0, 8);
            });

            // period
            // check if period date has been selected
            if(!$request->period)
            {          
                $request->period = Carbon::today()->format('m-Y');
            }
            
            // create until before period month
            $request->lastMonthPeriod = Carbon::createFromFormat('m-Y', $request->period)->subMonth()->format('m-Y');

            // get start/end period (period's month)
            $startPeriod = Carbon::createFromFormat('m-Y', $request->period)->startOfMonth()->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            // get start/end until period's month
            $startUntilPeriod = Carbon::createFromFormat('m-Y', $request->period)->startOfYear()->format('Y-m-d');
            $endUntilPeriod = Carbon::createFromFormat   ('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            // get start/end until before period's month
            $startUntilBeforePeriod = Carbon::createFromFormat('m-Y', $request->lastMonthPeriod)->startOfYear()->format('Y-m-d');
            $endUntilBeforePeriod = Carbon::createFromFormat('m-Y', $request->lastMonthPeriod)->endOfMonth()->format('Y-m-d');
            
            foreach ($groupCodes as $key => $groupCode) 
            {
                $saldo = 0;
                $saldoUntilMonth = 0;
                $saldoUntilBeforeMonth = 0;
                foreach ($groupCode as $key1 => $code) 
                {
                    // get code's normal balance 
                    if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        // until period's month
                        $saldoDebetUntilPeriod = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('debet');
                        $saldoKreditUntilPeriod = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('kredit');

                        $saldoUntilMonth += $saldoDebetUntilPeriod;
                        $saldoUntilMonth -= $saldoKreditUntilPeriod;

                        // until before period's month
                        $saldoDebetUntilBeforeMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('debet');
                        $saldoKreditUntilBeforeMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('kredit');

                        $saldoUntilBeforeMonth += $saldoDebetUntilBeforeMonth;
                        $saldoUntilBeforeMonth -= $saldoKreditUntilBeforeMonth;
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        // until period's month
                        $saldoDebetUntilPeriod = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('debet');
                        $saldoKreditUntilPeriod = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilPeriod, $endUntilPeriod])->sum('kredit');

                        $saldoUntilMonth -= $saldoDebetUntilPeriod;
                        $saldoUntilMonth += $saldoKreditUntilPeriod;

                        // until before period's month
                        $saldoDebetUntilBeforeMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('debet');
                        $saldoKreditUntilBeforeMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startUntilBeforePeriod, $endUntilBeforePeriod])->sum('kredit');

                        $saldoUntilBeforeMonth -= $saldoDebetUntilBeforeMonth;
                        $saldoUntilBeforeMonth += $saldoKreditUntilBeforeMonth;
                    }
                }

                    $parentCode = Code::where('CODE', $key)->first();
                    if($parentCode->codeCategory->name=='HPP'){
                        $hpp->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='PENDAPATAN'){
                        $pendapatan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PEGAWAI'){
                        $biayapegawai ->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA OPERASIONAL'){
                        $biayaoperasional  ->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PERAWATAN'){
                        $biayaperawatan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PENYUSUTAN'){
                        $biayapenyusutan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA ADMINISTRASI DAN UMUM'){
                        $biayaadminum->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }else
                    if($parentCode->codeCategory->name=='BIAYA PENYISIHAN'){
                        $penyisihan->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoUntilMonth' => $saldoUntilMonth,
                        'saldoUntilBeforeMonth' => $saldoUntilBeforeMonth,
                    ]);
                    }
                    
                
            }
            
            // year data
            $request->year = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y');
            $data['title'] = 'Laporan Laba Rugi';
            $data['labaRugis'] = $labaRugis;
            $data['pendapatan'] = $pendapatan;
            $data['hpp'] = $hpp;
            $data['biayapegawai'] = $biayapegawai;
            $data['biayaoperasional'] = $biayaoperasional;
            $data['biayaperawatan'] = $biayaperawatan;
            $data['biayapenyusutan'] = $biayapenyusutan;
            $data['biayaadminum'] = $biayaadminum;
            $data['biayapenyisihan'] = $biayapenyisihan;
            $data['request'] = $request;
            //dd($labaRugis);
            $filename = 'export_labarugi_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
           return Excel::download(new LabaRugiExport($data), $filename);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
        
    }
}
