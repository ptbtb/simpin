<?php

namespace App\Http\Controllers;

use App\Models\JkkPrinted;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDF;

class PengajuanController extends Controller
{
    public function indexJkk()
    {
        try
        {
            $listPengajuanPinjaman = Pengajuan::needPrintJkk()
                                            ->menungguPembayaran()
                                            ->get();

            $data['title'] = "Print JKK";
            $data['listPengajuanPinjaman'] = $listPengajuanPinjaman;
            return view('pinjaman.indexPrintJKK',$data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
    public function printJkk(Request $request)
    {
        try
        {
            $listPengajuan = Pengajuan::whereIn('kode_pengajuan', $request->kode_pengajuan)
                                        ->get();

            foreach ($listPengajuan as $pengajuan)
            {
                $pengajuan->no_jkk = $request->no_jkk;
                $pengajuan->status_jkk = 1;
                $pengajuan->save();
            }

            // create jkkprinted
            $jkkPrinted = JkkPrinted::where('jkk_number',  $request->no_jkk)->first();
            if (is_null($jkkPrinted))
            {
                $jkkPrinted = new JkkPrinted();
                $jkkPrinted->jkk_number = $request->no_jkk;
                $jkkPrinted->jkk_printed_type_id = JKK_PRINTED_TYPE_PENGAJUAN_PINJAMAN;
                $jkkPrinted->printed_at = Carbon::createFromFormat('Y-m-d', $request->tgl_print);
                $jkkPrinted->printed_by = Auth::user()->id;
                $jkkPrinted->save();
            }

            $data['listPengajuan'] = $listPengajuan;
            $data['tgl_print']=Carbon::createFromFormat('Y-m-d', $request->tgl_print);
            view()->share('data',$data);
            PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
            $pdf = PDF::loadView('pinjaman.printJKK', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = $request->no_jkk.'-'.$data['tgl_print'].'.pdf';
            return $pdf->download($filename);

            // return view('pinjaman.printJKK', $data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
}
