<?php

namespace App\Http\Controllers;

use App\Models\JenisSimpanan;
use App\Models\JkkPrinted;
use App\Models\JkkPrintedType;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class JkkPrintedController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('print jkk', $user);

        $types = JkkPrintedType::all();
        $data['types'] = $types;
        $data['title'] = 'Jkk Printed';
        $data['request'] = $request;
        return view('jkk.printed', $data);
    }

    public function indexAjax(Request $request)
    {
        $user = Auth::user();
        $this->authorize('print jkk', $user);

        $jkkPrinted = JkkPrinted::with('jkkPrintedType', 'jkkPengajuan', 'jkkPenarikan');
        if (isset($request->type_id) && $request->type_id == JKK_PRINTED_TYPE_PENGAJUAN_PINJAMAN)
        {
            $jkkPrinted = $jkkPrinted->has('jkkPengajuan');
        }
        elseif(isset($request->type_id) && $request->type_id == JKK_PRINTED_TYPE_PENARIKAN_SIMPANAN)
        {
            $jkkPrinted = $jkkPrinted->has('jkkPenarikan');
        }

        return DataTables::eloquent($jkkPrinted)
                            ->make(true);
    }

    public function reprint($id)
    {
        $user = Auth::user();
        $this->authorize('print jkk', $user);

        $jkkPrinted = JkkPrinted::findOrFail($id);

        if ($jkkPrinted->isPenarikanSimpanan())
        {
            return $this->reprintPenarikan($jkkPrinted);
        }
        else
        {
            return $this->reprintPengajuanPinjaman($jkkPrinted);
        }
    }

    public function reprintPenarikan(JkkPrinted $jkkPrinted)
    {
        $listPenarikan = $jkkPrinted->jkkPenarikan;

        $data['tgl_print']= Carbon::now();
        $data['listPenarikan'] = $listPenarikan;
        $data['jenisSimpanan'] = JenisSimpanan::all();
        view()->share('data', $data);
        PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
        $pdf = PDF::loadView('penarikan.pdfJKK', $data)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = $jkkPrinted->jkk_number . '-' . $data['tgl_print'] . '.pdf';
        return $pdf->download($filename);
    }

    public function reprintPengajuanPinjaman(JkkPrinted $jkkPrinted)
    {
        $listPengajuan = $jkkPrinted->jkkPengajuan;

        $data['listPengajuan'] = $listPengajuan;
        $data['tgl_print'] = Carbon::now();
        view()->share('data',$data);
        PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
        $pdf = PDF::loadView('pinjaman.printJKK', $data)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = $jkkPrinted->jkk_number.'-'.$data['tgl_print'].'.pdf';
        return $pdf->download($filename);
    }
}
