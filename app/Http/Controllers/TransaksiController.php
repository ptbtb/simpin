<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\View\ViewTransaksi;

use App\Exports\TransaksiExport;

use Auth;
use Carbon\Carbon;
use Excel;
use PDF;

class TransaksiController extends Controller
{
    public function listTransaksiAnggota(Request $request)
    {
        $currentUser = Auth::user();
        $this->authorize('view transaksi anggota', $currentUser);
        $anggota = $currentUser->anggota;
        if (is_null($anggota))
        {
            return redirect()->back()->withError('Your account has no members');
        }

        $kode_anggota = $anggota->kode_anggota;
        $listTransaksi = ViewTransaksi::where('kode_anggota', $kode_anggota);
        if ($request->from)
        {
            $listTransaksi = $listTransaksi->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listTransaksi = $listTransaksi->where('tgl_entri','<=', $request->to);
        }
        $listTransaksi = $listTransaksi->get();

        $data['title'] = 'List Transaksi';
        $data['listTransaksi'] = $listTransaksi;
        $data['request'] = $request;
        return view('transaksi.index_anggota', $data);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view transaksi anggota', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_transaksi_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new TransaksiExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPDF(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view transaksi anggota', $user);

        $anggota = $user->anggota;
        $kode_anggota = $anggota->kode_anggota;
        $listTransaksi = ViewTransaksi::where('kode_anggota', $kode_anggota);
        if ($request->from)
        {
            $listTransaksi = $listTransaksi->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listTransaksi = $listTransaksi->where('tgl_entri','<=', $request->to);
        }
        $listTransaksi = $listTransaksi->get();

        // share data to view
        view()->share('listTransaksi',$listTransaksi);
        $pdf = PDF::loadView('transaksi.excel', $listTransaksi)->setPaper('a4', 'landscape');
  
        // download PDF file with download method
        $filename = 'export_transaksi_'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }
}
