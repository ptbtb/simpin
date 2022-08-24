<?php

namespace App\Http\Controllers;

use App\Managers\LabaRugiManager;
use App\Managers\NeracaManager;
use App\Models\Jurnal;
use App\Models\Code;
use App\Models\CodeCategory;
use App\Models\KodeTransaksi;
use Illuminate\Http\Request;
use App\Exports\NeracaExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\LabaRugiController;

use Rap2hpoutre\FastExcel\FastExcel;

use Carbon\Carbon;
use Excel;
use DB;
use Illuminate\Support\Facades\Cache;
use PDF;

class NeracaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            // period
            // check if period date has been selected
            if (!$request->period) {
                $request->period = Carbon::today()->format('Y-m-d');
            }



            $data['title'] = 'Laporan Neraca';
            $data['request'] = $request;

            return view('neraca.index', $data);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());

            $bulanLalu = Carbon::createFromFormat('Y-m-d',$request->period)->subMonthsNoOverflow()->endOfMonth()->format('Y-m-d');

            if ($request->search) {
                $jurnalCode = KodeTransaksi::where('is_parent',0)
                    ->where('code_type_id',$request->code_type_id)
//                    ->orderby('code_category_id','asc')
                    ->orderby('CODE','asc')
                    ->get();
                $result = $jurnalCode->map(function($code,$key)use($bulanLalu,$request){
                    if($code->CODE=='606.01.000'  ){
                        return [
                            'CODE'=>$code->CODE,
                            'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                            'saldo'=>LabaRugiManager::getShuBerjalan($request->period) +$code->neracaAmount($request->period),
                            'saldoLalu'=>LabaRugiManager::getShuBerjalan($bulanLalu) + $code->neracaAmount($bulanLalu),
                            'code_type_id'=>$code->code_type_id,
                            'Kategori'=>$code->codeCategory->name,

                        ];
                    }
                    if($code->CODE=='607.01.101'){
                        return [
                            'CODE'=>$code->CODE,
                            'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                            'saldo'=>LabaRugiManager::getShuBerjalan($request->period) +$code->neracaAmount($request->period),
                            'saldoLalu'=>LabaRugiManager::getShuBerjalan($bulanLalu) + $code->neracaAmount($bulanLalu),
                            'code_type_id'=>$code->code_type_id,
                            'Kategori'=>$code->codeCategory->name,

                        ];
                    }
                    return [
                        'CODE'=>$code->CODE,
                        'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                        'saldo'=>$code->neracaAmount($request->period),
                        'saldoLalu'=>$code->neracaAmount($bulanLalu),
                        'code_type_id'=>$code->code_type_id,
                        'Kategori'=>$code->codeCategory->name,

                    ];
                });
            }


            return DataTables::of($result)
                ->with('total', function() use ($result) {
                    return $result->sum('saldo');
                })
                ->with('totallalu', function() use ($result) {
                    return $result->sum('saldoLalu');
                })

                ->make(true);
    }

    public function createExcel(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {

            $bulanLalu = Carbon::createFromFormat('Y-m-d',$request->period)->subMonthsNoOverflow()->endOfMonth()->format('Y-m-d');

            $jurnalCode = KodeTransaksi::where('is_parent',0)
                ->wherein('code_type_id',[1,2])
                ->orderby('code_type_id','asc')
                ->orderby('CODE','asc')
                ->get();

            $result = $jurnalCode->map(function($code,$key)use($bulanLalu,$request){
                if($code->CODE=='606.01.000'  ){
                    return [
                        'CODE'=>$code->CODE,
                        'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                        'saldo'=>LabaRugiManager::getShuBerjalan($request->period) +$code->neracaAmount($request->period),
                        'saldoLalu'=>LabaRugiManager::getShuBerjalan($bulanLalu) + $code->neracaAmount($bulanLalu),
                        'code_type_id'=>$code->code_type_id,
                        'Kategori'=>$code->codeType->name,

                    ];
                }
                if($code->CODE=='607.01.101'){
                    return [
                        'CODE'=>$code->CODE,
                        'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                        'saldo'=>LabaRugiManager::getShuBerjalan($request->period) +$code->neracaAmount($request->period),
                        'saldoLalu'=>LabaRugiManager::getShuBerjalan($bulanLalu) + $code->neracaAmount($bulanLalu),
                        'code_type_id'=>$code->code_type_id,
                        'Kategori'=>$code->codeType->name,

                    ];
                }
                return [
                    'CODE'=>$code->CODE,
                    'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                    'saldo'=>$code->neracaAmount($request->period),
                    'saldoLalu'=>$code->neracaAmount($bulanLalu),
                    'code_type_id'=>$code->code_type_id,
                    'Kategori'=>$code->codeType->name,

                ];
            });

            $data['list']=$result;
            $data['title']='Neraca Per '.$request->period;
            $data['request']=$request;



            $filename = 'export_neraca_excel_' .$request->period. '_printed_'. Carbon::now()->format('d M Y his') . '.xlsx';
//            return Excel::download(new NeracaExport($data), $filename);
            return (new FastExcel($result))->download($filename);
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function createPdf($period, Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            $bulanLalu = Carbon::createFromFormat('Y-m-d',$request->period)->subMonthsNoOverflow()->endOfMonth()->format('Y-m-d');

            $jurnalCode = KodeTransaksi::where('is_parent',0)
                ->where('code_type_id',$request->code_type_id)
//                    ->orderby('code_category_id','asc')
                ->orderby('CODE','asc')
                ->get();

            $result = $jurnalCode->map(function($code,$key)use($bulanLalu,$request){
                if($code->CODE=='606.01.000'  ){
                    return [
                        'CODE'=>$code->CODE,
                        'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                        'saldo'=>LabaRugiManager::getShuBerjalan($request->period) +$code->neracaAmount($request->period),
                        'saldoLalu'=>LabaRugiManager::getShuBerjalan($bulanLalu) + $code->neracaAmount($bulanLalu),
                        'code_type_id'=>$code->code_type_id,
                        'Kategori'=>$code->codeType->name,

                    ];
                }
                if($code->CODE=='607.01.101'){
                    return [
                        'CODE'=>$code->CODE,
                        'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                        'saldo'=>LabaRugiManager::getShuBerjalan($request->period) +$code->neracaAmount($request->period),
                        'saldoLalu'=>LabaRugiManager::getShuBerjalan($bulanLalu) + $code->neracaAmount($bulanLalu),
                        'code_type_id'=>$code->code_type_id,
                        'Kategori'=>$code->codeType->name,

                    ];
                }
                return [
                    'CODE'=>$code->CODE,
                    'NAMA_TRANSAKSI'=>$code->NAMA_TRANSAKSI,
                    'saldo'=>$code->neracaAmount($request->period),
                    'saldoLalu'=>$code->neracaAmount($bulanLalu),
                    'code_type_id'=>$code->code_type_id,
                    'Kategori'=>$code->codeType->name,

                ];
            });

            $data['list']=$result;

            view()->share('data', $data);
            PDF::setOptions(['margin-left' => 1,'margin-right' => 1, 'margin-top' => 1]);
            $pdf = PDF::loadView('neraca.createPdf', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = 'lap-neraca-'.$period.'-'.Carbon::now()->toDateString().'.pdf';
            return $pdf->download($filename);
            // return view('neraca.createPdf', $data);
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
}
