<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $codes = Code::where('is_parent', 0)->get();

            $jurnal = Jurnal::get();

            // buku besar collection
            $bukuBesars = collect();

            foreach ($codes as $key => $code) 
            {
                // get code's normal balance 
                if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                {
                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'saldo' => $jurnal->where('akun_debet', $code->CODE)->sum('debet'),
                    ]);
                }
                else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                {
                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'saldo' => $jurnal->where('akun_kredit', $code->CODE)->sum('kredit'),
                    ]);
                }
            }
            
            $data['title'] = 'List Buku Besar';
            $data['codes'] = $codes;
            $data['bukuBesars'] = $bukuBesars;
            $data['request'] = $request;
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

            $jurnal = Jurnal::get();

            // buku besar collection
            $bukuBesars = collect();

            foreach ($codes as $key => $code) 
            {
                // get code's normal balance 
                if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                {
                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'code_type_id' => $code->code_type_id,
                        'saldo' => $jurnal->where('akun_debet', $code->CODE)->sum('debet'),
                    ]);
                }
                else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                {
                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'code_type_id' => $code->code_type_id,
                        'saldo' => $jurnal->where('akun_kredit', $code->CODE)->sum('kredit'),
                    ]);
                }
            }
            
            if ($request->code_type_id)
            {
                $bukuBesars = $bukuBesars->where('code_type_id', $request->code_type_id);
            }

            return DataTables::collection($bukuBesars)->make(true);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return response()->json(['message' => 'error'], 500);
        }
    }
}