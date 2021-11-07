<?php

namespace App\Http\Controllers;

use App\Exports\SHUCardExport;
use App\Exports\SHUExport;
use App\Imports\SHUImport;
use App\Models\SHU;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SHUController extends Controller
{
    public function index()
    {
        $this->authorize('import user', Auth::user());
        $shu = SHU::all();

        $data['title'] = 'List SHU';
        $data['shu'] = $shu;
        return view('shu.list-shu', $data);
    }

    public function import()
    {
        $this->authorize('import user', Auth::user());
        $data['title'] = 'Import SHU';
        return view('shu.shu-list-import', $data);
    }

    public function storeImport(Request $request)
    {
        $this->authorize('import user', Auth::user());
        try
        {
            Excel::import(new SHUImport($request->year), $request->file);
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            return redirect()->back()->withError('Gagal import data');
        }
    }

    public function downloadCard($id)
    {
        $shu = SHU::findOrFail($id);
        $data['shu'] = $shu;
        $filename = uniqid().'.pdf';

        view()->share('shu',$shu);
        $pdf = PDF::loadView('shu.shu-card', $shu)->setPaper('a4', 'landscape');

        // download PDF file with download method
        return $pdf->download($filename);

        // return view('shu.shu-card', $data);
    }

    public function exportExcel(Request $request)
    {
        try
        {
            $listSHU = SHU::take(10)->get();
            $data['listSHU'] = $listSHU;
            $name = 'SHU-excel-'.Carbon::now()->toDateTimeString().'.xlsx';
            // return view('shu.excel', $data);
            return Excel::download(new SHUExport($data), $name);
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            return redirect()->back()->withErrors($message);
        }
    }
}
