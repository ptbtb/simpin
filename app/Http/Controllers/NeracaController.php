<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\BukuBesarExport;

use Carbon\Carbon;
use Excel;
use DB;

class NeracaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $codes = Code::where('is_parent', 0)->whereIn('code_type_id', [CODE_TYPE_ACTIVA, CODE_TYPE_PASSIVA])->get();

            // aktiva collection
            $aktivas = collect();
            $passivas = collect();

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
            $startPeriod = Carbon::createFromFormat('m-Y', $request->period)->startOfMonth()->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            $startComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->startOfMonth()->format('Y-m-d');
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
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth += $saldoDebetLastMonth;
                        $saldoLastMonth -= $saldoKreditLastMonth;
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        $saldoDebetLastMonth = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereBetween('created_at', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereBetween('created_at', [$startComparePeriod, $endComparePeriod])->sum('kredit');

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
                    ]);
                }
                else if($groupCode->first()->code_type_id == CODE_TYPE_PASSIVA)
                {
                    $passivas->push([
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoLastMonth' => $saldoLastMonth,
                    ]);
                }
                
            }

            $aktivas = $aktivas->sortBy('code');
            $passivas = $passivas->sortBy('code');
            
            $data['title'] = 'Laporan Neraca';
            $data['aktivas'] = $aktivas;
            $data['passivas'] = $passivas;
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
        $user = Auth::user();
        $this->authorize('view jurnal', $user);

        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        return Excel::download(new BukuBesarExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
