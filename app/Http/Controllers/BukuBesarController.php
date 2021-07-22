<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\BukuBesarExport;
use Rap2hpoutre\FastExcel\FastExcel;

use Carbon\Carbon;
use Excel;
use DB;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $codes = Code::where('is_parent', 0)->get();
            if(!$request->period)
            {          
                $request->period = Carbon::today()->format('Y-m-d');
            }

            // buku besar collection
            $bukuBesars = collect();
            $todays=Carbon::createFromFormat('Y-m-d', $request->period);
            $today=Carbon::createFromFormat('Y-m-d', $request->period)->format('Y-m-d');

            $startOfYear = $todays->subYear()->endOfYear()->format('Y-m-d');
           // / dd($startOfYear);
            
            foreach ($codes as $key => $code) 
            {

                $saldo = 0;
                // get code's normal balance 
                if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                {
                    // if first char of COA is 7 or 8 get jurnal from first date of year until today
                    if(substr($code->CODE, 0, 1) === '7' || substr($code->CODE, 0, 1) === '8')
                    {
                        

                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('created_at', [$startOfYear,$today])->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('created_at',[$startOfYear,$today])->sum('kredit');

                    }
                    else
                    {
                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereDate('created_at', '<=',$today)->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereDate('created_at', '<=',$today)->sum('kredit');

                    }

                    $saldo += $saldoDebet;
                    $saldo -= $saldoKredit;

                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'saldo' => $saldo,
                    ]);

                }
                else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                {
                    // if first char of COA is 7 or 8 get jurnal from first date of year until today
                    if(substr($code->CODE, 0, 1) === '7' || substr($code->CODE, 0, 1) === '8')
                    {
                        

                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('created_at', [$startOfYear,$today])->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startOfYear,$today])->sum('kredit');
                    }
                    else
                    {
                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereDate('created_at', '<=',$today)->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereDate('created_at', '<=',$today)->sum('kredit');
                    }

                    $saldo -= $saldoDebet;
                    $saldo += $saldoKredit;

                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'saldo' => $saldo,
                    ]);
                }
                //dd($bukuBesars);
            }

            $bukuBesars = $bukuBesars->sortBy('code');
            
            $data['title'] = 'List Buku Besar';
            $data['codes'] = $codes;
            $data['bukuBesars'] = $bukuBesars;
            $data['request'] = $request;
            //dd($data['request'] );
            return view('buku_besar.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function indexAjax(Request $request)
    {
        try
        {
            
            $codes = Code::where('is_parent', 0)->get();

            // buku besar collection
            $bukuBesars = collect();
            $todays=Carbon::createFromFormat('Y-m-d', $request->period);
           $today=Carbon::createFromFormat('Y-m-d', $request->period)->format('Y-m-d');
            $startOfYear = $todays->subYear()->endOfYear()->format('Y-m-d');
            foreach ($codes as $key => $code) 
            {
                $saldo = 0;
                // get code's normal balance 
                if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                {
                    // if first char of COA is 7 or 8 get jurnal from first date of year until today
                    if(substr($code->CODE, 0, 1) === '7' || substr($code->CODE, 0, 1) === '8')
                    {
                        

                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('created_at', [$startOfYear,$today])->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startOfYear,$today])->sum('kredit');
                    }
                    else
                    {
                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereDate('created_at', '<=',$today)->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereDate('created_at', '<=',$today)->sum('kredit');
                    }

                    $saldo += $saldoDebet;
                    $saldo -= $saldoKredit;

                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'code_type_id' => $code->code_type_id,
                        'saldo' => $saldo,
                    ]);
                }
                else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                {
                    // if first char of COA is 7 or 8 get jurnal from first date of year until today
                    if(substr($code->CODE, 0, 1) === '7' || substr($code->CODE, 0, 1) === '8')
                    {
                        

                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('created_at', [$startOfYear,$today])->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startOfYear,$today])->sum('kredit');
                    }
                    else
                    {
                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereDate('created_at', '<=',$today)->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereDate('created_at', '<=',$today)->sum('kredit');
                    }

                    $saldo -= $saldoDebet;
                    $saldo += $saldoKredit;

                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'code_type_id' => $code->code_type_id,
                        'saldo' => $saldo,
                    ]);
                }
            }
            
            if ($request->code_type_id)
            {
                $bukuBesars = $bukuBesars->where('code_type_id', $request->code_type_id);
            }

            $bukuBesars = $bukuBesars->sortBy('code');


            return DataTables::collection($bukuBesars)->make(true);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function createExcel(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        $codes = Code::where('is_parent', 0)->get();

        $jurnal = Jurnal::get();

        // buku besar collection
        $bukuBesars = collect();
        if(!$request->period)
            {          
                $request->period = Carbon::today()->format('Y-m-d');
            }

            $todays=Carbon::createFromFormat('Y-m-d', $request->period);
           $today=Carbon::createFromFormat('Y-m-d', $request->period)->format('Y-m-d');
            $startOfYear = $todays->subYear()->endOfYear()->format('Y-m-d');

        foreach ($codes as $key => $code) 
        {
            $saldo = 0;
            // get code's normal balance 
            if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
            {
                $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereDate('created_at', '<=',$today)->sum('debet');
                $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereDate('created_at', '<=',$today)->sum('kredit');

                $saldo += $saldoDebet;
                $saldo -= $saldoKredit;

                $bukuBesars->push([
                    'code' => $code->CODE,
                    'name' => $code->NAMA_TRANSAKSI,
                    'type' => $code->codeType->name,
                    'saldo' => $saldo,
                ]);
            }
            else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {
                $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereDate('created_at', '<=',$today)->sum('debet');
                $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereDate('created_at', '<=',$today)->sum('kredit');

                $saldo -= $saldoDebet;
                $saldo += $saldoKredit;

                $bukuBesars->push([
                    'code' => $code->CODE,
                    'name' => $code->NAMA_TRANSAKSI,
                    'type' => $code->codeType->name,
                    'saldo' => $saldo,
                ]);
            }
        }

        $bukuBesars = $bukuBesars->sortBy('code');
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
         return (new FastExcel($bukuBesars))->download($filename);
        // return Excel::download(new BukuBesarExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
