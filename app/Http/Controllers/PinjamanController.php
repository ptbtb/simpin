<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pinjaman;

use App\Exports\PinjamanExport;

use Auth;
use Carbon\Carbon;
use Excel;
use PDF;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $anggota = $user->anggota;
        if (is_null($anggota))
        {
            return redirect()->back()->withError('Your account has no members');
        }
        
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                ->where('status', 'belum lunas');;
        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "List Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        return view('pinjaman.index',$data);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history pinjaman', $user);

        $anggota = $user->anggota;
        if (is_null($anggota))
        {
            return redirect()->back()->withError('Your account has no members');
        }
        
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                ->where('status', 'lunas');
        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "History Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        return view('pinjaman.history',$data);
    }

    public function show($id)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $anggota = $user->anggota;
        if (is_null($anggota))
        {
            return redirect()->back()->withError('Your account has no members');
        }

        $pinjaman = Pinjaman::with('anggota','listAngsuran')
                            ->where('kode_anggota', $anggota->kode_anggota)
                            ->where('kode_pinjam', $id)
                            ->first();
        
        $data['pinjaman'] = $pinjaman;
        $data['jenisPinjaman'] = $pinjaman->jenisPinjaman;
        return view('pinjaman.detail', $data);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPDF(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $anggota = $user->anggota;
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota);
        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        if ($request->status)
        {
            $listPinjaman = $listPinjaman->where('status', $request->status);
        }

        $listPinjaman = $listPinjaman->get();

        // share data to view
        view()->share('listPinjaman',$listPinjaman);
        $pdf = PDF::loadView('pinjaman.excel', $listPinjaman)->setPaper('a4', 'landscape');
  
        // download PDF file with download method
        $filename = 'export_pinjaman_'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }

    public function createPDF1(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_'.Carbon::now()->format('d M Y').'.pdf';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::DOMPDF);
    }
}