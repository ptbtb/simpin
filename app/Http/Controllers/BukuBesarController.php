<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use App\Models\KodeTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\BukuBesarExport;
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
        try
        {
            $jurnalCode = collect();
            $selisih = 0;
            if(!$request->period)
            {
                $request->period = Carbon::today()->format('Y-m-d');
            }

            if ($request->search)
            {
                $jurnalCode = KodeTransaksi::where('is_parent',0)
                                ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
                                ->get();
            }

            $data['title'] = 'List Buku Besar';
            $data['codes'] = $jurnalCode;
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

    public function createExcel(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        if(!$request->period)
        {
            $request->period = Carbon::today()->format('Y-m-d');
        }

            $jurnalCode = KodeTransaksi::where('is_parent',0)->get();

        $data['title'] = 'List Buku Besar';
        $data['codes'] = $jurnalCode;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPdf(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        if(!$request->period)
        {
            $request->period = Carbon::today()->format('Y-m-d');
        }


            $jurnalCode = KodeTransaksi::where('is_parent',0)->get();


        $data['title'] = 'List Buku Besar';
        $data['codes'] = $jurnalCode;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.pdf';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename);
    }
}
