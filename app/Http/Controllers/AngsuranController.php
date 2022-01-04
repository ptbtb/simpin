<?php

namespace App\Http\Controllers;

use App\Imports\AngsuranImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Database\Eloquent\ModelNotfoundException;

class AngsuranController extends Controller
{
    public function importAngsuran()
    {
        try
        {
            $data['title'] = 'Import Angsuran';
            return view('pinjaman.angsuran.import', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function storeImportAngsuran(Request $request)
    {
        try
        {
            DB::transaction(function () use ($request)
            {
                // Excel::import(new AngsuranImport, $request->file); 
                $collection = (new FastExcel)->import($request->file);
                foreach ($collection as $transaksi) {
                    AngsuranImport::generatetransaksi($transaksi);
                }
            });

            return redirect()->back()->withSuccess('Berhasil Import Data');
        }
        catch (\Throwable $e)
        {
            $message = $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError($message);
        }
    }
}
