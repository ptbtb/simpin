<?php

namespace App\Http\Controllers;

use App\Imports\TransaksiUserImport;
use App\Managers\TransaksiUserManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

class GlobalTransaksiController extends Controller
{
     public function importTransaksiUser()
    {
        try
        {
            $data['title'] = 'Import Transaksi User';
            return view('global.transaksiimportuser', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
     public function storeTransaksiUser(Request $request){
     	try
        {
            DB::transaction(function () use ($request)
            {
                // Excel::import(new TransaksiUserImport, $request->file); 
                $collection = (new FastExcel)->import($request->file);
                foreach ($collection as $transaksi) {
                    TransaksiUserImport::generatetransaksi($transaksi);
                }
            });

            return redirect()->back()->withSuccess('Berhasil Import Data');
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan : '.$message);
        }
     }
}
