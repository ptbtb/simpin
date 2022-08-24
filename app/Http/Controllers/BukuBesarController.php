<?php

namespace App\Http\Controllers;

use App\Managers\LabaRugiManager;
use App\Models\Jurnal;
use App\Models\Code;
use App\Models\KodeTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\BukuBesarExport;
use App\Exports\BukuBesarResumeExport;
use Rap2hpoutre\FastExcel\FastExcel;

use Carbon\Carbon;
use Excel;
use DB;
use PDF;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            $jurnalCode = collect();
            $selisih = 0;
            if (!$request->period) {
                $request->period = Carbon::today()->format('Y-m-d');
            }


//            dd($jurnalCode);
            $data['title'] = 'List Buku Besar';
            $data['codes'] = $jurnalCode;
            $data['request'] = $request;
            return view('buku_besar.index', $data);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());

            $jurnalCode = collect();
            $selisih = 0;
            if (!$request->period) {
                $request->period = Carbon::today()->format('Y-m-d');
            }

            if ($request->search) {
                $y = Carbon::createFromFormat('Y-m-d',$request->period)->format('Y');
                if($y=='2020'){
                    $jurnalCode = KodeTransaksi::where('is_parent', 0)
                        ->wherenotin('CODE', [ '606.01.101'])
                        ->orderby('code_type_id','asc')
                        ->orderby('CODE','asc')
                        ->get();
                }else{
                    $jurnalCode = KodeTransaksi::where('is_parent', 0)
                        ->wherenotin('CODE', [ '606.01.101', '607.01.101'])
                        ->orderby('code_type_id','asc')
                        ->orderby('CODE','asc')
                        ->get();
                }


                $result = $jurnalCode->map(function($code,$key)use($request){
                    if($code->CODE=='606.01.000'  ){
                        return [
                            'tipe'=>$code->codeType->name,
                            'CODE'=>$code->CODE,
                            'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                            'saldo'=>-(LabaRugiManager::getShuditahan($request->period) +$code->jurnalAmount($request->period) ),
//

                        ];
                    }
                    return [
                        'tipe'=>$code->codeType->name,
                        'CODE'=>$code->CODE,
                        'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                        'saldo'=>$code->jurnalAmount($request->period),

                    ];
                });

            }

//            dd($jurnalCode);
            return DataTables::of($result)
                ->with('diffamount', function() use ($result) {
                    return $result->sum('saldo');
                })
                ->make(true);

    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        if (!$request->period) {
            $request->period = Carbon::today()->format('Y-m-d');
        }
        $y = Carbon::createFromFormat('Y-m-d',$request->period)->format('Y');
        if($y=='2020'){
            $jurnalCode = KodeTransaksi::where('is_parent', 0)
                ->wherenotin('CODE', [ '606.01.101'])
                ->orderby('code_type_id','asc')
                ->orderby('CODE','asc')
                ->get();
        }else{
            $jurnalCode = KodeTransaksi::where('is_parent', 0)
                ->wherenotin('CODE', [ '606.01.101', '607.01.101'])
                ->orderby('code_type_id','asc')
                ->orderby('CODE','asc')
                ->get();
        }


        $result = $jurnalCode->map(function($code,$key)use($request){
            if($code->CODE=='606.01.000'  ){
                return [
                    'tipe'=>$code->codeType->name,
                    'CODE'=>$code->CODE,
                    'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                    'saldo'=>-(LabaRugiManager::getShuditahan($request->period) +$code->jurnalAmount($request->period) ),
//

                ];
            }
            return [
                'tipe'=>$code->codeType->name,
                'CODE'=>$code->CODE,
                'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                'saldo'=>$code->jurnalAmount($request->period),

            ];
        });

        $data['title'] = 'List Buku Besar';
        $data['codes'] = $result;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPdf(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        if (!$request->period) {
            $request->period = Carbon::today()->format('Y-m-d');
        }
        $y = Carbon::createFromFormat('Y-m-d',$request->period)->format('Y');
        if($y=='2020'){
            $jurnalCode = KodeTransaksi::where('is_parent', 0)
                ->wherenotin('CODE', [ '606.01.101'])
                ->orderby('code_type_id','asc')
                ->orderby('CODE','asc')
                ->get();
        }else{
            $jurnalCode = KodeTransaksi::where('is_parent', 0)
                ->wherenotin('CODE', [ '606.01.101', '607.01.101'])
                ->orderby('code_type_id','asc')
                ->orderby('CODE','asc')
                ->get();
        }
        $result = $jurnalCode->map(function($code,$key)use($request){
            if($code->CODE=='606.01.000'  ){
                return [
                    'tipe'=>$code->codeType->name,
                    'CODE'=>$code->CODE,
                    'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                    'saldo'=>-(LabaRugiManager::getShuditahan($request->period) +$code->jurnalAmount($request->period) ),
//

                ];
            }
            return [
                'tipe'=>$code->codeType->name,
                'CODE'=>$code->CODE,
                'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                'saldo'=>$code->jurnalAmount($request->period),

            ];
        });


        $data['title'] = 'List Buku Besar';
        $data['codes'] = $result;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.pdf';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename);
    }

    public function resume(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            $jurnalCode = collect();
            $selisih = 0;
            if (!$request->from) {
                $request->from = Carbon::today()->startOfMonth()->format('Y-m-d');
            }
            if (!$request->to) {
                $request->to = Carbon::today()->format('Y-m-d');
            }

//            if ($request->search)
//            {
//                $jurnalCode = KodeTransaksi::where('is_parent',0)
//                    ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
//                    ->where('CODE','411.12.000' )
//                    ->get();
//                $tglawal =  Carbon::createFromFormat('Y-m-d',$request->from)->subDays(1)->format('Y-m-d');
////                dd($tglawal);
//                foreach ($jurnalCode as $key=>$jk){
//                    $jurnalCode[$key]->awaldr=$jk->jurnalAmount($tglawal)['dr'];
//                    $jurnalCode[$key]->awalcr=$jk->jurnalAmount($tglawal)['cr'];
//                    $jurnalCode[$key]->trxdr=$jk->jurnalAmountTransaksi($request->from,$request->to)['dr'];
//                    $jurnalCode[$key]->trxcr=$jk->jurnalAmountTransaksi($request->from,$request->to)['cr'];
//                    $jurnalCode[$key]->akhirdr=$jk->jurnalAmount($request->to)['dr'];
//                    $jurnalCode[$key]->akhircr=$jk->jurnalAmount($request->to)['cr'];
//                }
////                dd($jurnalCode);
//            }

            $data['title'] = 'Resume Buku Besar';
            $data['codes'] = $jurnalCode;
            $data['request'] = $request;
            return view('buku_besar.resume', $data);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function resumeajax(Request $request)
    {

            $this->authorize('view jurnal', Auth::user());
//            ini_set('memory_limit', '-1');
//            dd($request);
            $selisih = 0;
            if (!$request->from) {
                $request->from = Carbon::today()->startOfMonth()->format('Y-m-d');
            }
            if (!$request->to) {
                $request->to = Carbon::today()->format('Y-m-d');
            }

            if ($request->search) {


                $tglawal = Carbon::createFromFormat('Y-m-d', $request->from)->subDays(1)->format('Y-m-d');
                $y = Carbon::createFromFormat('Y-m-d',$tglawal)->format('Y');
                if($y=='2020'){
                    $jurnalCode = KodeTransaksi::where('is_parent', 0)
                        ->wherenotin('CODE', [ '606.01.101'])
                        ->orderby('code_type_id','asc')
                        ->orderby('CODE','asc')
                        ->get();
                }else{
                    $jurnalCode = KodeTransaksi::where('is_parent', 0)
                        ->wherenotin('CODE', [ '606.01.101', '607.01.101'])
                        ->orderby('code_type_id','asc')
                        ->orderby('CODE','asc')
                        ->get();
                }
                //
                $result = $jurnalCode->map(function($code,$key)use($tglawal,$request){
                    if($code->CODE=='606.01.000'  ){
                        return [
                            'CODE'=>$code->CODE,
                            'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                            'awal'=>number_format(-(LabaRugiManager::getShuditahan($tglawal)+$code->jurnalAmount($tglawal)),0,',','.'),
                            'trxdr'=>0,
                            'trxcr'=>0,
                            'akhir'=>number_format(-(LabaRugiManager::getShuditahan($request->to)+$code->jurnalAmount($request->to)),0,',','.'),
//

                        ];
                    }
                    return [
                        'CODE'=>$code->CODE,
                        'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                        'awal'=>number_format($code->jurnalAmount($tglawal),0,',','.'),
                        'trxdr'=>number_format($code->saldoDr($request->from, $request->to),0,',','.'),
                        'trxcr'=>number_format($code->saldoCr($request->from, $request->to),0,',','.'),
                        'akhir'=>number_format($code->jurnalAmount($request->to),0,',','.'),
                    ];
                });
//                dd($result);
            }

            return DataTables::of($result)
                ->make(true);

    }

    public function resumeexcel(Request $request)
    {
        $jurnalCode = collect();
        $selisih = 0;
        if (!$request->from) {
            $request->from = Carbon::today()->startOfMonth()->format('Y-m-d');
        }
        if (!$request->to) {
            $request->to = Carbon::today()->format('Y-m-d');
        }



        $tglawal = Carbon::createFromFormat('Y-m-d', $request->from)->subDays(1)->format('Y-m-d');
        $y = Carbon::createFromFormat('Y-m-d',$tglawal)->format('Y');
        if($y=='2020'){
            $jurnalCode = KodeTransaksi::where('is_parent', 0)
                ->wherenotin('CODE', [ '606.01.101'])
                ->orderby('code_type_id','asc')
                ->orderby('CODE','asc')
                ->get();
        }else{
            $jurnalCode = KodeTransaksi::where('is_parent', 0)
                ->wherenotin('CODE', [ '606.01.101', '607.01.101'])
                ->orderby('code_type_id','asc')
                ->orderby('CODE','asc')
                ->get();
        }
        $result = $jurnalCode->map(function($code,$key)use($tglawal,$request){
            if($code->CODE=='606.01.000'  ){
                return [
                    'CODE'=>$code->CODE,
                    'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                    'awal'=>-(LabaRugiManager::getShuditahan($tglawal)+$code->jurnalAmount($tglawal)),
                    'trxdr'=>0,
                    'trxcr'=>0,
                    'akhir'=>-(LabaRugiManager::getShuditahan($request->to)+$code->jurnalAmount($request->to)),
//

                ];
            }
            return [
                'tipe'=>$code->codeType->name,
                'CODE'=>$code->CODE,
                'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                'awal'=>$code->jurnalAmount($tglawal),
                'trxdr'=>$code->saldoDr($request->from, $request->to),
                'trxcr'=>$code->saldoCr($request->from, $request->to),
                'akhir'=>$code->jurnalAmount($request->to),
            ];
        });
//                dd($request);


        $data['title'] = 'List Buku Besar';
        $data['codes'] = $result;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarResumeExport($data), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
