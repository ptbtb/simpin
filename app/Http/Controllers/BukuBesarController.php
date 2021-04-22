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

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $codes = Code::where('is_parent', 0)->get();

            // buku besar collection
            $bukuBesars = collect();

            foreach ($codes as $key => $code) 
            {

                $saldo = 0;
                // get code's normal balance 
                if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                {
                    $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->sum('debet');
                    $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->sum('kredit');

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
                    $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->sum('debet');
                    $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->sum('kredit');

                    $saldo -= $saldoDebet;
                    $saldo += $saldoKredit;

                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'saldo' => $saldo,
                    ]);
                }
            }

            $bukuBesars = $bukuBesars->sortBy('code');
            
            $data['title'] = 'List Buku Besar';
            $data['codes'] = $codes;
            $data['bukuBesars'] = $bukuBesars;
            $data['request'] = $request;
            return view('buku_besar.index', $data);
        }
        catch (\Throwable $e)
        {
            dd($e);
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

            foreach ($codes as $key => $code) 
            {
                $saldo = 0;
                // get code's normal balance 
                if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                {
                    $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->sum('debet');
                    $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->sum('kredit');

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
                    $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->sum('debet');
                    $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->sum('kredit');

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
            dd($e);
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function createExcel(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);

        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        return Excel::download(new BukuBesarExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
