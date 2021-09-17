<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use App\Models\CodeCategory;
use Illuminate\Http\Request;
use App\Exports\NeracaExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use Rap2hpoutre\FastExcel\FastExcel;

use Carbon\Carbon;
use Excel;
use DB;
use PDF;

class NeracaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
           $groupNeraca = CodeCategory::where('name','like','AKTIVA%')
                            ->orWhere('name','like','KEWAJIBAN%')
                            ->orWhere('name','like','KEKAYAAN%')
                            ->get();

            $codes = Code::where('is_parent', 0)
                            ->where(function ($query) use($groupNeraca) {
                                for ($i = 0; $i < count($groupNeraca); $i++){
                               $query->orWhere('code_category_id',  $groupNeraca[$i]->id );
                                }      
                            })
                            //->whereIn('code_type_id', [CODE_TYPE_ACTIVA, CODE_TYPE_PASSIVA])
                            ->get();

            // aktiva collection
            $aktivalancar = collect();
            $aktivatetap = collect();
            $kewajibanlancar = collect();
            $kewajibanjangkapanjang = collect();
            $kekayaanbersih = collect();

            $groupCodes = $codes->groupBy(function ($item, $key) {
                return substr($item['CODE'], 0, 3);
            });

            // period
            // check if period date has been selected
            if(!$request->period)
            {          
                $request->period = Carbon::today()->format('m-Y');
            }

            // create compare period, sub month from selected period
            $request->compare_period = Carbon::createFromFormat('m-Y', $request->period)->subMonth()->format('m-Y');

            // get start/end period and sub period
            $startPeriod = Carbon::createFromFormat('m-Y', $request->period)->subYear()->endOfYear()->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            $startComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->subYear()->endOfYear()->format('Y-m-d');
            $endComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->endOfMonth()->format('Y-m-d');
            
            foreach ($groupCodes as $key => $groupCode) 
            {
                $saldo = 0;
                $saldoLastMonth = 0;
                foreach ($groupCode as $key1 => $code) 
                {
                    // get code's normal balance 
                    if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                    {
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth += $saldoDebetLastMonth;
                        $saldoLastMonth -= $saldoKreditLastMonth;
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth -= $saldoDebetLastMonth;
                        $saldoLastMonth += $saldoKreditLastMonth;
                    }
                }
                
                if ($key==965 || $key==974 || $key==974){
                    $parentCode = Code::where('CODE', 'like',''.$key . '%')->first();
                }else{
                     $parentCode = Code::where('CODE', $key . '.00.000')->first();
                }
                
                if($parentCode->codeCategory->name=='AKTIVA LANCAR')
                {
                    $aktivalancar->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }
                else if($parentCode->codeCategory->name=='AKTIVA TETAP')
                {
                    $aktivatetap->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }else if($parentCode->codeCategory->name=='KEWAJIBAN LANCAR')
                {
                    $kewajibanlancar->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }else if($parentCode->codeCategory->name=='KEWAJIBAN JANGKA PANJANG')
                {
                    $kewajibanjangkapanjang->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }else if($parentCode->codeCategory->name=='KEKAYAAN BERSIH')
                {
                    $kekayaanbersih->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }
                
            }

            $aktivatetap = $aktivatetap->sortBy('code');
            $aktivalancar = $aktivalancar->sortBy('code');
            $kewajibanlancar = $kewajibanlancar->sortBy('code');
            $kewajibanjangkapanjang = $kewajibanjangkapanjang->sortBy('code');
            $kekayaanbersih = $kekayaanbersih->sortBy('code');
           
            
            $data['title'] = 'Laporan Neraca';
            $data['aktivatetap'] = $aktivatetap;
            $data['aktivalancar'] = $aktivalancar;
            $data['kewajibanlancar'] = $kewajibanlancar;
            $data['kewajibanjangkapanjang'] = $kewajibanjangkapanjang;
            $data['kekayaanbersih'] = $kekayaanbersih;
           
            $data['request'] = $request;
            
            return view('neraca.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function createExcel(Request $request) {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $groupNeraca = CodeCategory::where('name','like','AKTIVA%')
                            ->orWhere('name','like','KEWAJIBAN%')
                            ->orWhere('name','like','KEKAYAAN%')
                            ->get();

            $codes = Code::where('is_parent', 0)
                            ->where(function ($query) use($groupNeraca) {
                                for ($i = 0; $i < count($groupNeraca); $i++){
                               $query->orWhere('code_category_id',  $groupNeraca[$i]->id );
                                }      
                            })
                            //->whereIn('code_type_id', [CODE_TYPE_ACTIVA, CODE_TYPE_PASSIVA])
                            ->get();

            // aktiva collection
            $aktivalancar = collect();
            $aktivatetap = collect();
            $kewajibanlancar = collect();
            $kewajibanjangkapanjang = collect();
            $kekayaanbersih = collect();

            $groupCodes = $codes->groupBy(function ($item, $key) {
                return substr($item['CODE'], 0, 3);
            });

            // period
            // check if period date has been selected
            if(!$request->period)
            {          
                $request->period = Carbon::today()->format('m-Y');
            }

            // create compare period, sub month from selected period
            $request->compare_period = Carbon::createFromFormat('m-Y', $request->period)->subMonth()->format('m-Y');

            // get start/end period and sub period
            $startPeriod = Carbon::createFromFormat('m-Y', $request->period)->subYear()->endOfYear()->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            $startComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->subYear()->endOfYear()->format('Y-m-d');
            $endComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->endOfMonth()->format('Y-m-d');
            
            foreach ($groupCodes as $key => $groupCode) 
            {
                $saldo = 0;
                $saldoLastMonth = 0;
                foreach ($groupCode as $key1 => $code) 
                {
                    // get code's normal balance 
                    if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                    {
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth += $saldoDebetLastMonth;
                        $saldoLastMonth -= $saldoKreditLastMonth;
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth -= $saldoDebetLastMonth;
                        $saldoLastMonth += $saldoKreditLastMonth;
                    }
                }
                
                if ($key==965 || $key==974){
                    $parentCode = Code::where('CODE', 'like',''.$key . '%')->first();
                }else{
                     $parentCode = Code::where('CODE', $key . '.00.000')->first();
                }
                
               
                if($parentCode->codeCategory->name=='AKTIVA LANCAR')
                {
                    $aktivalancar->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }
                else if($parentCode->codeCategory->name=='AKTIVA TETAP')
                {
                    $aktivatetap->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }else if($parentCode->codeCategory->name=='KEWAJIBAN LANCAR')
                {
                    $kewajibanlancar->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }else if($parentCode->codeCategory->name=='KEWAJIBAN JANGKA PANJANG')
                {
                    $kewajibanjangkapanjang->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }else if($parentCode->codeCategory->name=='KEKAYAAN BERSIH')
                {
                    $kekayaanbersih->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }
                
            }

            $aktivatetap = $aktivatetap->sortBy('code');
            $aktivalancar = $aktivalancar->sortBy('code');
            $kewajibanlancar = $kewajibanlancar->sortBy('code');
            $kewajibanjangkapanjang = $kewajibanjangkapanjang->sortBy('code');
            $kekayaanbersih = $kekayaanbersih->sortBy('code');
           
            
            $data['title'] = 'Laporan Neraca';
            $data['aktivatetap'] = $aktivatetap;
            $data['aktivalancar'] = $aktivalancar;
            $data['kewajibanlancar'] = $kewajibanlancar;
            $data['kewajibanjangkapanjang'] = $kewajibanjangkapanjang;
            $data['kekayaanbersih'] = $kekayaanbersih;
           
            

            
            
            $filename = 'export_neraca_excel_' .$request->period. '_'. Carbon::now()->format('d M Y') . '.xlsx';
             return Excel::download(new NeracaExport($data), $filename);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function createPdf($period)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $groupNeraca = [101, 102, 103, 104, 105, 106, 107, 109, 110, 111, 204, 205, 208, 210, 302, 400, 401, 
                            402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 501, 502, 603, 604, 605, 606, 607];

            $codes = Code::where('is_parent', 0)
                            ->where(function ($query) use($groupNeraca) {
                                for ($i = 0; $i < count($groupNeraca); $i++){
                                $query->orWhere('CODE', 'like',  $groupNeraca[$i] .'%');
                                }      
                            })
                            ->whereIn('code_type_id', [CODE_TYPE_ACTIVA, CODE_TYPE_PASSIVA])
                            ->get();

            // aktiva collection
            $aktivas = collect();
            $passivas = collect();

            $groupCodes = $codes->groupBy(function ($item, $key) {
                return substr($item['CODE'], 0, 3);
            });

            // period
            // check if period date has been selected
            if(!$period)
            {          
                $period = Carbon::today()->format('m-Y');
            }

            // create compare period, sub month from selected period
            $compare_period = Carbon::createFromFormat('m-Y', $period)->subMonth()->format('m-Y');

            // get start/end period and sub period
            $startPeriod = Carbon::createFromFormat('m-Y', $period)->startOfMonth()->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $period)->endOfMonth()->format('Y-m-d');

            $startComparePeriod = Carbon::createFromFormat('m-Y', $compare_period)->startOfMonth()->format('Y-m-d');
            $endComparePeriod = Carbon::createFromFormat('m-Y', $compare_period)->endOfMonth()->format('Y-m-d');
            
            foreach ($groupCodes as $key => $groupCode) 
            {
                $saldo = 0;
                $saldoLastMonth = 0;
                foreach ($groupCode as $key1 => $code) 
                {
                    // get code's normal balance 
                    if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                    {
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth += $saldoDebetLastMonth;
                        $saldoLastMonth -= $saldoKreditLastMonth;
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth -= $saldoDebetLastMonth;
                        $saldoLastMonth += $saldoKreditLastMonth;
                    }
                }
                
                $parentCode = Code::where('CODE', $key . '.00.000')->first();

                if($groupCode->first()->code_type_id == CODE_TYPE_ACTIVA)
                {
                    $aktivas->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                        'rekGroup' => substr($key, 0, 1),
                    ]);
                }
                else if($groupCode->first()->code_type_id == CODE_TYPE_PASSIVA)
                {
                    $passivas->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                        'rekGroup' => substr($key, 0, 1),
                    ]);
                }
                
            }

            $aktivas = $aktivas->sortBy('code');
            $passivas = $passivas->sortBy('code');
            
            $data['aktivas'] = $aktivas;
            $data['passivas'] = $passivas;
            $data['period'] = $period;
            
            view()->share('data',$data);
            PDF::setOptions(['margin-left' => 1,'margin-right' => 1, 'margin-top' => 1]);
            $pdf = PDF::loadView('neraca.createPdf', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = 'lap-neraca-'.$period.'-'.Carbon::now()->toDateString().'.pdf';
            return $pdf->download($filename);
            // return view('neraca.createPdf', $data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
}
